<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AkunRtController extends Controller
{
    /**
     * Tampilkan halaman utama Manajemen Akun RT (RW 013).
     * Hanya dapat diakses oleh RW / Super Admin melalui middleware.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');
        $filterRt = $request->query('rt');
        $filterStatus = $request->query('status');

        // Query akun RT di bawah RW 013
        $query = User::where('role', 'rt')->where(function ($q) {
            $q->where('rw_number', '013')
              ->orWhere('rw_number', '13')
              ->orWhere('rw_number', 'RW 013')
              ->orWhereNull('rw_number');
        });

        // Pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter RT
        if ($filterRt && $filterRt !== 'all') {
            $cleanFilter = str_pad(preg_replace('/[^0-9]/', '', $filterRt), 3, '0', STR_PAD_LEFT);
            $query->where(function ($q) use ($cleanFilter) {
                $q->where('rt_number', $cleanFilter)
                  ->orWhere('rt_number', (string)(int)$cleanFilter)
                  ->orWhere('rt_number', "RT {$cleanFilter}");
            });
        }

        // Filter Status
        if ($filterStatus && $filterStatus !== 'all') {
            if ($filterStatus === 'aktif') {
                $query->where('is_active', true);
            } elseif ($filterStatus === 'nonaktif') {
                $query->where('is_active', false);
            }
        }

        $rtAccounts = $query->orderBy('rt_number')->get();

        // Rekapitulasi status 8 RT
        $allRtNumbers = ['001', '002', '003', '004', '005', '006', '007', '008'];
        $existingRtNumbers = User::where('role', 'rt')->pluck('rt_number')->map(function ($num) {
            return str_pad(preg_replace('/[^0-9]/', '', $num), 3, '0', STR_PAD_LEFT);
        })->unique()->toArray();

        $totalWilayahRt = 8;
        $totalAktif = User::where('role', 'rt')->where('is_active', true)->count();
        $totalNonaktif = User::where('role', 'rt')->where('is_active', false)->count();
        
        $missingRt = array_diff($allRtNumbers, $existingRtNumbers);
        $totalBelumAdaAkun = count($missingRt);

        // Riwayat aktivitas terbaru
        $recentActivities = ActivityLog::with(['user', 'targetUser'])
            ->whereIn('activity', ['CREATE_AKUN_RT', 'UPDATE_AKUN_RT', 'TOGGLE_STATUS_RT', 'RESET_PASSWORD_RT', 'GENERATE_AKUN_RT'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('rt.akun.index', compact(
            'user',
            'rtAccounts',
            'totalWilayahRt',
            'totalAktif',
            'totalNonaktif',
            'totalBelumAdaAkun',
            'missingRt',
            'search',
            'filterRt',
            'filterStatus',
            'recentActivities'
        ));
    }

    /**
     * Tambah Akun RT baru (hanya untuk wilayah RT 001 - RT 008 di RW 013).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'rt_number' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'house_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap Ketua RT wajib diisi.',
            'email.required' => 'Email login wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'rt_number.required' => 'Nomor RT wajib dipilih.',
            'password.required' => 'Password awal wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $cleanRt = str_pad(preg_replace('/[^0-9]/', '', $request->rt_number) ?: '001', 3, '0', STR_PAD_LEFT);

        // Cegah pembuatan nomor RT di luar 001 - 008
        if (!in_array($cleanRt, ['001', '002', '003', '004', '005', '006', '007', '008'])) {
            return back()->with('error', 'Nomor RT tidak valid. Wilayah RW 013 hanya membawahi RT 001 sampai RT 008.')->withInput();
        }

        // Cegah duplikasi akun aktif untuk RT yang sama
        $duplicate = User::where('role', 'rt')->where(function ($q) use ($cleanRt) {
            $q->where('rt_number', $cleanRt)
              ->orWhere('rt_number', (string)(int)$cleanRt)
              ->orWhere('rt_number', "RT {$cleanRt}");
        })->first();

        if ($duplicate) {
            return back()->with('error', "Akun untuk RT {$cleanRt} sudah terdaftar atas nama {$duplicate->name} ({$duplicate->email}). Silakan gunakan fitur edit jika ingin memperbarui pengurus.")->withInput();
        }

        DB::beginTransaction();
        try {
            $newRtUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'rt',
                'rt_number' => $cleanRt,
                'rw_number' => '013',
                'phone' => $request->phone,
                'address' => $request->address ?: "RT {$cleanRt} / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293",
                'house_number' => $request->house_number,
                'status_rumah' => 'milik_sendiri',
                'is_active' => true,
            ]);

            ActivityLog::log(
                'CREATE_AKUN_RT',
                "Ketua RW (" . Auth::user()->name . ") membuat akun baru Ketua RT {$cleanRt} atas nama {$newRtUser->name} ({$newRtUser->email})",
                $newRtUser->id
            );

            DB::commit();
            return back()->with('success', "Akun Ketua RT {$cleanRt} ({$newRtUser->name}) berhasil dibuat dan siap digunakan!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan akun: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update profil / identitas akun RT (Nama, Email, No. HP, Alamat).
     * Nomor RT dan RW dilindungi agar tidak terjadi pergeseran hak akses ilegal.
     */
    public function update(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);

        if ($targetUser->role !== 'rt') {
            return back()->with('error', 'Hanya akun Ketua RT yang dapat dikelola melalui modul ini.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$id}",
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'house_number' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama lengkap Ketua RT wajib diisi.',
            'email.required' => 'Email login wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        DB::beginTransaction();
        try {
            $oldName = $targetUser->name;
            $oldEmail = $targetUser->email;

            $targetUser->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'house_number' => $request->house_number,
                // rt_number & rw_number sengaja dikunci demi keamanan otorisasi kependudukan
            ]);

            ActivityLog::log(
                'UPDATE_AKUN_RT',
                "Ketua RW (" . Auth::user()->name . ") memperbarui profil akun Ketua {$targetUser->clean_rt_label} ({$oldName} -> {$targetUser->name})",
                $targetUser->id
            );

            DB::commit();
            return back()->with('success', "Data profil akun Ketua {$targetUser->clean_rt_label} ({$targetUser->name}) berhasil diperbarui!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui akun: ' . $e->getMessage());
        }
    }

    /**
     * Aktifkan atau nonaktifkan akun Ketua RT.
     * Tidak menghapus akun, data warga, maupun laporan RT.
     */
    public function toggleActive($id)
    {
        $targetUser = User::findOrFail($id);

        if ($targetUser->role !== 'rt') {
            return back()->with('error', 'Aksi ini hanya berlaku untuk akun Ketua RT.');
        }

        DB::beginTransaction();
        try {
            $targetUser->is_active = !$targetUser->is_active;
            $targetUser->save();

            $statusText = $targetUser->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

            ActivityLog::log(
                'TOGGLE_STATUS_RT',
                "Ketua RW (" . Auth::user()->name . ") mengubah status akun Ketua {$targetUser->clean_rt_label} ({$targetUser->name}) menjadi: " . ($targetUser->is_active ? 'AKTIF' : 'NONAKTIF'),
                $targetUser->id
            );

            DB::commit();
            return back()->with('success', "Akun Ketua {$targetUser->clean_rt_label} ({$targetUser->name}) berhasil {$statusText}!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengubah status akun: ' . $e->getMessage());
        }
    }

    /**
     * Reset password akun Ketua RT oleh Ketua RW atau Super Admin.
     * Menggunakan konfirmasi password, hashing bcrypt, dan pencatatan audit log tanpa menyimpan plain password.
     */
    public function resetPassword(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);

        if ($targetUser->role !== 'rt') {
            return back()->with('error', 'Hanya akun Ketua RT yang dapat direset melalui modul ini.');
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        DB::beginTransaction();
        try {
            $targetUser->password = Hash::make($request->password);
            $targetUser->save();

            ActivityLog::log(
                'RESET_PASSWORD_RT',
                "Ketua RW (" . Auth::user()->name . ") berhasil melakukan reset password untuk akun Ketua {$targetUser->clean_rt_label} ({$targetUser->name})",
                $targetUser->id
            );

            DB::commit();
            return back()->with('success', "Password untuk akun Ketua {$targetUser->clean_rt_label} ({$targetUser->name}) berhasil diperbarui secara aman!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mereset password: ' . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi & siapkan akun RT 001 - RT 008 jika ada wilayah yang belum memiliki akun.
     * Bersifat aman dan idempotent (tidak menghapus/mengubah akun yang sudah ada).
     */
    public function generateInitial()
    {
        $seeder = new \Database\Seeders\AkunRtSeeder();
        
        DB::beginTransaction();
        try {
            $seeder->run();

            ActivityLog::log(
                'GENERATE_AKUN_RT',
                "Ketua RW (" . Auth::user()->name . ") menjalankan sinkronisasi awal akun RT 001 s/d RT 008 wilayah RW 013",
                Auth::id()
            );

            DB::commit();
            return back()->with('success', 'Sinkronisasi akun RT 001 s/d RT 008 selesai! Seluruh wilayah RT yang belum memiliki akun telah dibuatkan kredensial awal.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menjalankan sinkronisasi: ' . $e->getMessage());
        }
    }
}
