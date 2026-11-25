<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ActivityLog;

class RoleSwitchController extends Controller
{
    /**
     * Memeriksa apakah user saat ini adalah Super Admin asli
     * atau sedang dalam sesi impersonasi oleh Super Admin asli.
     */
    protected function getAuthorizedSuperAdmin(): ?User
    {
        if (!Auth::check()) {
            return null;
        }

        $currentUser = Auth::user();

        // Jika user saat ini langsung adalah Super Admin
        if ($currentUser->role === 'super_admin') {
            return $currentUser;
        }

        // Jika sedang dalam sesi switch role / impersonasi
        if (session()->has('impersonator_id')) {
            $impersonator = User::find(session('impersonator_id'));
            if ($impersonator && $impersonator->role === 'super_admin') {
                return $impersonator;
            }
        }

        return null;
    }

    /**
     * Melakukan switch role ke target role / user tanpa logout.
     */
    public function switchRole(Request $request)
    {
        $superAdmin = $this->getAuthorizedSuperAdmin();
        if (!$superAdmin) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk menggunakan fitur Switch Role.');
        }

        $request->validate([
            'target_role' => 'required|in:rw,rt,warga,super_admin',
            'rt_number' => 'nullable|string',
            'target_user_id' => 'nullable|integer',
            'mode' => 'nullable|in:view_only,edit',
        ]);

        $targetRole = $request->input('target_role');
        $mode = $request->input('mode', 'view_only');

        // Jika target adalah kembali ke super_admin
        if ($targetRole === 'super_admin') {
            return $this->revert($request);
        }

        $targetUser = null;
        $targetRoute = 'dashboard';

        if ($targetRole === 'rw') {
            // Cari akun RW / Admin utama di sistem
            $targetUser = User::whereIn('role', ['admin', 'rw'])
                ->where('email', '!=', $superAdmin->email)
                ->first();

            if (!$targetUser) {
                return back()->with('error', 'Akun Ketua RW / Admin tidak ditemukan di database.');
            }
            $targetRoute = 'dashboard.rw';

        } elseif ($targetRole === 'rt') {
            $rtNumber = $request->input('rt_number');
            $cleanRt = str_pad(preg_replace('/[^0-9]/', '', (string)$rtNumber), 3, '0', STR_PAD_LEFT);

            $targetUser = User::where('role', 'rt')
                ->where(function($q) use ($cleanRt) {
                    $q->where('rt_number', $cleanRt)->orWhere('rt_number', ltrim($cleanRt, '0'));
                })
                ->first();

            // Jika tidak ditemukan by rt_number, coba by target_user_id
            if (!$targetUser && $request->filled('target_user_id')) {
                $targetUser = User::where('role', 'rt')->where('id', $request->input('target_user_id'))->first();
            }

            if (!$targetUser) {
                return back()->with('error', 'Akun Ketua RT ' . $cleanRt . ' tidak ditemukan di database.');
            }
            $targetRoute = 'dashboard.rt';

        } elseif ($targetRole === 'warga') {
            if ($request->filled('target_user_id')) {
                $targetUser = User::where('role', 'warga')->where('id', $request->input('target_user_id'))->first();
            }

            // Jika tidak ada user id tapi ada rt_number, ambil warga pertama di RT tersebut
            if (!$targetUser && $request->filled('rt_number')) {
                $cleanRt = str_pad(preg_replace('/[^0-9]/', '', (string)$request->input('rt_number')), 3, '0', STR_PAD_LEFT);
                $targetUser = User::where('role', 'warga')
                    ->where(function($q) use ($cleanRt) {
                        $q->where('rt_number', $cleanRt)->orWhere('rt_number', ltrim($cleanRt, '0'));
                    })
                    ->first();
            }

            if (!$targetUser) {
                return back()->with('error', 'Data Warga yang dipilih tidak ditemukan di database.');
            }
            $targetRoute = 'dashboard.warga';
        }

        // Simpan ID Super Admin asli & mode simulasi di session sebelum berpindah akun
        session([
            'impersonator_id' => $superAdmin->id,
            'impersonator_name' => $superAdmin->name,
            'impersonator_email' => $superAdmin->email,
            'is_impersonating' => true,
            'impersonation_mode' => $mode,
        ]);

        // Login sebagai target user tanpa password
        Auth::login($targetUser);

        $modeLabel = $mode === 'edit' ? 'Mode Edit (Akses Penuh)' : 'Mode Lihat Saja (Read-Only)';

        // Audit Trail: Catat aksi switch role oleh Super Admin
        ActivityLog::record(
            'super_admin',
            'super_admin.switch_role',
            "Super Admin {$superAdmin->name} melakukan simulasi peran sebagai {$targetUser->name} ({$targetUser->role_badge_label}) dalam {$modeLabel}.",
            [
                'super_admin_id' => $superAdmin->id,
                'target_user_id' => $targetUser->id,
                'target_role' => $targetUser->role,
                'mode' => $mode,
            ],
            $targetUser->id
        );

        return redirect()->route($targetRoute)->with('info', 'Mode Switch Role Aktif: Anda sekarang berperan sebagai ' . $targetUser->name . ' (' . $targetUser->role_badge_label . ') dalam ' . $modeLabel . '.');
    }

    /**
     * Mengembalikan sesi ke akun Super Admin asli.
     */
    public function revert(Request $request)
    {
        $superAdmin = $this->getAuthorizedSuperAdmin();
        if (!$superAdmin) {
            abort(403, 'Akses ditolak: Tidak ada sesi Super Admin yang valid.');
        }

        $previousUser = Auth::user();

        // Audit Trail: Catat revert sebelum session impersonation dihapus
        ActivityLog::record(
            'super_admin',
            'super_admin.revert',
            "Super Admin {$superAdmin->name} mengakhiri simulasi peran dan kembali ke Control Center.",
            [
                'previous_user_id' => $previousUser?->id,
                'previous_user_name' => $previousUser?->name,
                'previous_role' => $previousUser?->role,
            ],
            $superAdmin->id
        );

        // Hapus penanda impersonasi & mode dari session
        session()->forget(['impersonator_id', 'impersonator_name', 'impersonator_email', 'is_impersonating', 'impersonation_mode']);

        // Login kembali sebagai Super Admin asli
        Auth::login($superAdmin);

        return redirect()->route('dashboard.super-admin')->with('success', 'Kembali ke Control Center Super Admin berhasil.');
    }

    /**
     * Mengubah mode simulasi (Lihat Saja vs Mode Edit) secara instan.
     */
    public function updateMode(Request $request)
    {
        $superAdmin = $this->getAuthorizedSuperAdmin();
        if (!$superAdmin) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk mengubah mode simulasi.');
        }

        $request->validate([
            'mode' => 'required|in:view_only,edit',
        ]);

        $newMode = $request->input('mode');
        session(['impersonation_mode' => $newMode]);

        $modeLabel = $newMode === 'edit' ? 'Mode Edit (Akses Penuh)' : 'Mode Lihat Saja (Read-Only)';

        $currentUser = Auth::user();
        ActivityLog::record(
            'super_admin',
            'super_admin.switch_mode',
            "Super Admin {$superAdmin->name} mengubah mode simulasi menjadi {$modeLabel}.",
            [
                'mode' => $newMode,
                'current_simulated_user_id' => $currentUser?->id,
            ],
            $currentUser?->id
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'mode' => $newMode,
                'message' => "Mode simulasi berhasil diubah ke {$modeLabel}.",
            ]);
        }

        return back()->with('success', "Mode simulasi berhasil diubah ke {$modeLabel}.");
    }

    /**
     * Endpoint API JSON untuk mengambil daftar warga berdasarkan RT secara dinamis.
     */
    public function getWargaOptions(Request $request)
    {
        $superAdmin = $this->getAuthorizedSuperAdmin();
        if (!$superAdmin) {
            abort(403, 'Unauthorized');
        }

        $rtNumber = $request->input('rt_number');
        $cleanRt = str_pad(preg_replace('/[^0-9]/', '', (string)$rtNumber), 3, '0', STR_PAD_LEFT);

        $wargas = User::where('role', 'warga')
            ->where(function($q) use ($cleanRt) {
                $q->where('rt_number', $cleanRt)->orWhere('rt_number', ltrim($cleanRt, '0'));
            })
            ->select('id', 'name', 'email', 'rt_number', 'house_number', 'phone')
            ->orderBy('name', 'asc')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => 'success',
            'rt' => $cleanRt,
            'count' => $wargas->count(),
            'data' => $wargas,
        ]);
    }
}
