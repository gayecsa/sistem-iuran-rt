<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\KartuKeluarga;
use App\Models\BukuKas;
use App\Models\TransaksiKeuangan;
use App\Models\IuranTarif;
use App\Models\ActivityLog;

class SuperAdminController extends Controller
{
    /**
     * Dashboard Control Center Super Admin WarkasMachi.
     * Hanya dapat diakses oleh pengguna dengan role super_admin asli.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Akses ditolak: Halaman Control Center hanya dapat diakses oleh Super Admin.');
        }

        // 1. Statistik Pengguna & Kependudukan (100% Data DB)
        $userStats = [
            'super_admin' => User::where('role', 'super_admin')->count(),
            'rw' => User::whereIn('role', ['admin', 'rw', 'bendahara'])->where('email', '!=', 'annisasfzahra@gmail.com')->count(),
            'rt' => User::where('role', 'rt')->count(),
            'warga' => User::where('role', 'warga')->count(),
            'total_user' => User::count(),
        ];

        $totalKk = KartuKeluarga::count();
        if ($totalKk === 0) {
            $totalKk = User::whereNotNull('no_kk')->distinct('no_kk')->count('no_kk');
        }

        // 2. Audit & Rekonsiliasi Real-Time Seluruh Rekening Buku Kas
        $allBukuKas = BukuKas::orderBy('id', 'asc')->get();
        $reconciledCount = 0;
        $discrepancyCount = 0;
        $bukuKasLedger = [];

        foreach ($allBukuKas as $kas) {
            $calculatedSaldo = $kas->recalculateSaldo();
            $currentSaldo = floatval($kas->saldo_sekarang);
            $diff = round(abs($currentSaldo - $calculatedSaldo), 2);
            $isKlop = ($diff == 0);

            if ($isKlop) {
                $reconciledCount++;
            } else {
                $discrepancyCount++;
            }

            $pemasukan = floatval($kas->transaksis()
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
                ->sum('nominal'));

            $pengeluaran = floatval($kas->transaksis()
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
                ->sum('nominal'));

            $bukuKasLedger[] = [
                'id' => $kas->id,
                'kode_kas' => $kas->kode_kas,
                'nama_kas' => $kas->clean_nama_kas,
                'jenis_kas' => $kas->jenis_kas,
                'pemilik_dana' => $kas->pemilik_dana,
                'rt_number' => $kas->rt_number,
                'clean_rt' => $kas->clean_rt,
                'nama_bank' => $kas->nama_bank ?: 'Bank',
                'no_rekening' => $kas->masked_rekening,
                'saldo_awal' => floatval($kas->saldo_awal),
                'total_pemasukan' => $pemasukan,
                'total_pengeluaran' => $pengeluaran,
                'saldo_sekarang' => $currentSaldo,
                'selisih' => $diff,
                'is_klop' => $isKlop,
            ];
        }

        // 3. Saldo Konsolidasi Wilayah RW 013 (Kas Induk + 8 RT + Kas Sampah)
        $kasRw = BukuKas::where('jenis_kas', 'kas_rw')->first();
        $saldoKasRw = floatval($kasRw ? $kasRw->saldo_sekarang : 0);

        $totalKasRt = floatval(BukuKas::where('jenis_kas', 'kas_rt')->sum('saldo_sekarang'));

        $kasSampah = BukuKas::where('jenis_kas', 'kas_sampah')->first();
        $saldoKasSampah = floatval($kasSampah ? $kasSampah->saldo_sekarang : 0);

        $totalKonsolidasiWilayah = $saldoKasRw + $totalKasRt + $saldoKasSampah;

        // 4. Data untuk Panel Akses Cepat Switch Role
        $rwUser = User::whereIn('role', ['admin', 'rw'])
            ->where('email', '!=', 'annisasfzahra@gmail.com')
            ->first();

        $rtUsers = User::where('role', 'rt')->orderBy('rt_number', 'asc')->get();

        // 5. Data 5 Aktivitas Sistem Terbaru untuk Widget Dashboard
        $recentLogs = ActivityLog::with(['user', 'impersonator'])->latest()->limit(5)->get();

        return view('super_admin.dashboard', compact(
            'user',
            'userStats',
            'totalKk',
            'reconciledCount',
            'discrepancyCount',
            'bukuKasLedger',
            'saldoKasRw',
            'totalKasRt',
            'saldoKasSampah',
            'totalKonsolidasiWilayah',
            'rwUser',
            'rtUsers',
            'recentLogs'
        ));
    }

    /**
     * Modul 1: Manajemen Pengguna & Role Super Admin.
     */
    public function users(Request $request)
    {
        $currentUser = Auth::user();

        // 1. Ringkasan Pengguna Per Role
        $userStats = [
            'super_admin' => User::where('role', 'super_admin')->count(),
            'rw' => User::whereIn('role', ['admin', 'rw', 'bendahara'])->where('email', '!=', 'annisasfzahra@gmail.com')->count(),
            'rt' => User::where('role', 'rt')->count(),
            'warga' => User::where('role', 'warga')->count(),
            'total_user' => User::count(),
        ];

        // 2. Query Builder dengan Filter & Pencarian
        $query = User::query();

        // Filter Role
        if ($request->filled('role') && $request->role !== 'all') {
            $roleFilter = $request->role;
            if ($roleFilter === 'rw') {
                $query->whereIn('role', ['admin', 'rw', 'bendahara']);
            } else {
                $query->where('role', $roleFilter);
            }
        }

        // Filter Wilayah RT
        if ($request->filled('rt') && $request->rt !== 'all') {
            $rtClean = str_pad(preg_replace('/[^0-9]/', '', (string)$request->rt), 3, '0', STR_PAD_LEFT);
            $query->where(function ($q) use ($rtClean) {
                $q->where('rt_number', $rtClean)
                  ->orWhere('rt_number', ltrim($rtClean, '0'));
            });
        }

        // Search Bar (nama, email, no_kk, nik, phone)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Ordering: Super Admin -> RW -> RT -> Warga, lalu urut RT & Nama
        $users = $query->orderByRaw("
            CASE 
                WHEN role = 'super_admin' THEN 1 
                WHEN role IN ('admin', 'rw', 'bendahara') THEN 2 
                WHEN role = 'rt' THEN 3 
                ELSE 4 
            END
        ")
        ->orderBy('rt_number', 'asc')
        ->orderBy('name', 'asc')
        ->paginate(15)
        ->withQueryString();

        $rtList = ['001', '002', '003', '004', '005', '006', '007', '008'];

        return view('super_admin.users', compact('currentUser', 'userStats', 'users', 'rtList'));
    }

    /**
     * Memperbarui informasi profil, role, atau penugasan RT pengguna.
     */
    public function updateUser(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        $currentAuthId = Auth::id();

        // Proteksi Diri: Larang Super Admin mengubah role akunnya sendiri agar tidak terkunci
        if ($targetUser->id === $currentAuthId && $request->role !== 'super_admin') {
            return back()->with('error', 'Aksi ditolak: Anda tidak dapat mengubah role akun Super Admin Anda sendiri untuk mencegah hilangnya hak akses sistem.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $targetUser->id,
            'role' => 'required|in:super_admin,admin,rw,rt,warga,bendahara',
            'rt_number' => 'nullable|string',
            'phone' => 'nullable|string|max:25',
            'is_active' => 'nullable|boolean',
        ]);

        // Normalisasi format nomor RT (3 digit angka)
        $cleanRt = '000';
        if (in_array($request->role, ['rt', 'warga']) && $request->filled('rt_number')) {
            $digits = preg_replace('/[^0-9]/', '', (string)$request->rt_number);
            $cleanRt = str_pad($digits ?: '1', 3, '0', STR_PAD_LEFT);
        } elseif ($request->filled('rt_number')) {
            $digits = preg_replace('/[^0-9]/', '', (string)$request->rt_number);
            $cleanRt = str_pad($digits ?: '0', 3, '0', STR_PAD_LEFT);
        }

        $targetUser->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'rt_number' => $cleanRt,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        ActivityLog::record(
            'super_admin',
            'user.updated',
            "Super Admin memperbarui data pengguna {$targetUser->name} ({$targetUser->email}).",
            [
                'target_user_id' => $targetUser->id,
                'role' => $targetUser->role,
                'rt_number' => $targetUser->rt_number,
                'is_active' => $targetUser->is_active,
            ]
        );

        return back()->with('success', "Data pengguna {$targetUser->name} ({$targetUser->email}) berhasil diperbarui.");
    }

    /**
     * Reset password darurat oleh Super Admin.
     */
    public function resetUserPassword(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $targetUser->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::record(
            'super_admin',
            'user.password_reset',
            "Super Admin mereset password pengguna {$targetUser->name} ({$targetUser->email}).",
            [
                'target_user_id' => $targetUser->id,
            ]
        );

        return back()->with('success', "Password untuk akun {$targetUser->name} ({$targetUser->email}) berhasil direset.");
    }

    /**
     * Modul 2: Master Rekening Buku Kas & Tarif Iuran (One Billing).
     */
    public function masterKeuangan(Request $request)
    {
        $activeTab = $request->query('tab', 'rekening');
        if (!in_array($activeTab, ['rekening', 'tarif'])) {
            $activeTab = 'rekening';
        }

        // Tab 1: 18 Rekening Buku Kas
        $allBukuKas = BukuKas::orderBy('id', 'asc')->get();
        foreach ($allBukuKas as $kas) {
            $kas->recalculateSaldo();
        }

        // Tab 2: Master Tarif & Komponen Iuran
        $allTarifs = IuranTarif::with('bukuKas')
            ->orderBy('urutan_prioritas', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('super_admin.master_keuangan', compact('activeTab', 'allBukuKas', 'allTarifs'));
    }

    /**
     * Memperbarui identitas & metadata rekening buku kas (saldo diisolasi/dilarang diubah).
     */
    public function updateRekening(Request $request, $id)
    {
        $kas = BukuKas::findOrFail($id);

        $request->validate([
            'nama_kas' => 'required|string|max:255',
            'nama_bank' => 'nullable|string|max:100',
            'no_rekening' => 'nullable|string|max:100',
            'atas_nama' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // HANYA update metadata identitas rekening — DILARANG mengubah saldo_awal maupun saldo_sekarang!
        $kas->update([
            'nama_kas' => $request->nama_kas,
            'nama_bank' => $request->nama_bank ?: $kas->nama_bank,
            'no_rekening' => $request->no_rekening,
            'atas_nama' => $request->atas_nama,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $kas->is_active,
        ]);

        // Pastikan saldo tetap tersinkron dengan ledger riil
        $kas->recalculateSaldo();

        ActivityLog::record(
            'keuangan',
            'master.rekening_updated',
            "Super Admin memperbarui metadata rekening kas {$kas->kode_kas} ({$kas->clean_nama_kas}).",
            [
                'buku_kas_id' => $kas->id,
                'kode_kas' => $kas->kode_kas,
                'nama_bank' => $kas->nama_bank,
                'no_rekening' => $kas->no_rekening,
                'is_active' => (bool)$kas->is_active,
            ]
        );

        return redirect()->route('super-admin.master-keuangan.index', ['tab' => 'rekening'])
            ->with('success', "Identitas rekening {$kas->kode_kas} ({$kas->clean_nama_kas}) berhasil diperbarui tanpa mengganggu saldo ledger.");
    }

    /**
     * Memperbarui nominal tarif atau status aktif/nonaktif komponen iuran One Billing.
     */
    public function updateTarif(Request $request, $id)
    {
        $tarif = IuranTarif::findOrFail($id);

        $request->validate([
            'nominal' => 'required|numeric|min:0',
            'is_active' => 'required|in:0,1',
            'nama_tarif' => 'nullable|string|max:255',
            'keterangan_musyawarah' => 'nullable|string',
        ]);

        $oldNominal = $tarif->nominal;

        $tarif->update([
            'nominal' => $request->nominal,
            'is_active' => (bool)$request->is_active,
            'nama_tarif' => $request->filled('nama_tarif') ? $request->nama_tarif : $tarif->nama_tarif,
            'keterangan_musyawarah' => $request->keterangan_musyawarah,
        ]);

        ActivityLog::record(
            'keuangan',
            'master.tarif_updated',
            "Super Admin memperbarui master tarif {$tarif->nama_tarif} ({$tarif->kode_tarif}) menjadi Rp " . number_format($tarif->nominal, 0, ',', '.') . ".",
            [
                'tarif_id' => $tarif->id,
                'kode_tarif' => $tarif->kode_tarif,
                'old_nominal' => (float)$oldNominal,
                'new_nominal' => (float)$tarif->nominal,
                'is_active' => (bool)$tarif->is_active,
            ]
        );

        return redirect()->route('super-admin.master-keuangan.index', ['tab' => 'tarif'])
            ->with('success', "Tarif {$tarif->nama_tarif} ({$tarif->kode_tarif}) berhasil diperbarui.");
    }

    /**
     * Modul 3: Log Aktivitas & Audit Trail Sistem Super Admin.
     */
    public function auditLog(Request $request)
    {
        $query = ActivityLog::with(['user', 'impersonator'])->latest();

        // 1. Filter Kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // 2. Filter Rentang Tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 3. Pencarian Teks
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Statistik Cepat (Data Real DB)
        $totalLogs = ActivityLog::count();
        $todayLogs = ActivityLog::whereDate('created_at', today())->count();
        $simulasiLogs = ActivityLog::where(function($q) {
            $q->whereNotNull('impersonator_id')->orWhere('category', 'super_admin');
        })->count();
        $securityMasterLogs = ActivityLog::whereIn('category', ['keamanan', 'keuangan'])->count();

        $logs = $query->paginate(20)->withQueryString();

        return view('super_admin.audit_log', compact(
            'logs',
            'totalLogs',
            'todayLogs',
            'simulasiLogs',
            'securityMasterLogs'
        ));
    }

    /**
     * Export Log Aktivitas ke file CSV bersih.
     */
    public function exportAuditLog(Request $request)
    {
        $query = ActivityLog::with(['user', 'impersonator'])->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->get();
        $fileName = 'audit_logs_warkasmachi_' . now()->format('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            // Tulis UTF-8 BOM agar Excel dapat membuka karakter dengan benar
            fputs($file, "\xEF\xBB\xBF");

            // Header baris CSV
            fputcsv($file, [
                'ID',
                'Waktu (WIB)',
                'Kategori',
                'Kode Aksi',
                'Pelaku (Nama)',
                'Email Pelaku',
                'Role Pelaku',
                'Simulasi (Via Super Admin)',
                'Deskripsi Aktivitas',
                'Alamat IP',
                'User Agent',
                'Metadata Detail',
            ]);

            foreach ($logs as $log) {
                $impersonatorInfo = $log->impersonator 
                    ? "{$log->impersonator->name} (ID: {$log->impersonator->id})"
                    : ($log->impersonator_id ? "Super Admin ID #{$log->impersonator_id}" : 'Langsung (Non-Simulasi)');

                fputcsv($file, [
                    $log->id,
                    $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '-',
                    strtoupper($log->category),
                    $log->action,
                    $log->user ? $log->user->name : 'Sistem / Anonim',
                    $log->user ? $log->user->email : '-',
                    $log->user ? strtoupper($log->user->role) : '-',
                    $impersonatorInfo,
                    $log->description,
                    $log->ip_address ?: '-',
                    $log->user_agent ?: '-',
                    $log->metadata ? json_encode($log->metadata, JSON_UNESCAPED_UNICODE) : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
