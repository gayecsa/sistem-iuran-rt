<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\UmkmController; // <-- Tambahan Import UmkmController
use App\Http\Controllers\CctvController;
use App\Http\Controllers\AkunRtController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\LetterMasterController;
use App\Http\Controllers\ArsipKkController;
use App\Http\Controllers\DataKependudukanController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\BankSampahController;
use App\Http\Controllers\PosyanduController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\RoleSwitchController;
use App\Http\Controllers\SuperAdminController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showCover'])->name('cover');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Verifikasi Publik Keaslian Surat (Tanpa Login / Scan QR Code)
Route::get('/surat/verifikasi/{code}', [SuratController::class, 'verifikasi'])->name('surat.verifikasi');

// Katalog Publik Layanan Surat (Masyarakat Umum / Guest)
Route::get('/layanan-surat', [SuratController::class, 'publicCatalog'])->name('surat.public_catalog');

// Auth routes (Semua yang sudah login bisa akses ini)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Fitur Switch Role Super Admin (Impersonation Tanpa Logout)
    Route::post('/switch-role', [RoleSwitchController::class, 'switchRole'])->name('switch.role');
    Route::post('/switch-role/revert', [RoleSwitchController::class, 'revert'])->name('switch.role.revert');
    Route::post('/switch-role/mode', [RoleSwitchController::class, 'updateMode'])->name('switch.role.mode');
    Route::get('/switch-role/warga-options', [RoleSwitchController::class, 'getWargaOptions'])->name('switch.role.warga_options');

    // Dashboard Khusus Super Admin (Control Center)
    Route::get('/dashboard/super-admin', [SuperAdminController::class, 'index'])
        ->name('dashboard.super-admin')
        ->middleware('role:super_admin');

    // Modul Khusus Super Admin (Manajemen Pengguna & Master Keuangan)
    Route::middleware('role:super_admin')->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users.index');
        Route::put('/users/{id}', [SuperAdminController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{id}/reset-password', [SuperAdminController::class, 'resetUserPassword'])->name('users.reset-password');
        
        Route::get('/master-keuangan', [SuperAdminController::class, 'masterKeuangan'])->name('master-keuangan.index');
        Route::put('/master-keuangan/rekening/{id}', [SuperAdminController::class, 'updateRekening'])->name('master-keuangan.rekening.update');
        Route::put('/master-keuangan/tarif/{id}', [SuperAdminController::class, 'updateTarif'])->name('master-keuangan.tarif.update');

        // Modul 3: Log Aktivitas & Audit Trail Sistem
        Route::get('/audit-log', [SuperAdminController::class, 'auditLog'])->name('audit-log.index');
        Route::get('/audit-log/export', [SuperAdminController::class, 'exportAuditLog'])->name('audit-log.export');
    });

    // Tombol Back multi-langkah
    Route::get('/back', function () {
        $stack = session(\App\Http\Middleware\NavigationHistory::KEY, []);
        array_pop($stack); // buang halaman saat ini
        $target = end($stack) ?: route('dashboard');
        session([\App\Http\Middleware\NavigationHistory::KEY => $stack]);
        return redirect()->to($target);
    })->name('nav.back');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.edit');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/biodata', [AuthController::class, 'updateBiodata'])->name('profile.biodata.update');
    Route::post('/profile/email', [AuthController::class, 'updateEmail'])->name('profile.email.update');
    Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/notifications', [AuthController::class, 'updateNotifications'])->name('profile.notifications.update');
    Route::post('/profile/privacy', [AuthController::class, 'updatePrivacy'])->name('profile.privacy.update');
    Route::post('/profile/upload-foto', [AuthController::class, 'uploadFoto'])->name('profile.upload-foto');
    Route::post('/profile/make-admin', [AuthController::class, 'makeAdmin'])->name('profile.makeAdmin');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/rw', [DashboardController::class, 'dashboardRw'])->name('dashboard.rw')->middleware('role:rw,admin,bendahara');
    Route::get('/dashboard/rt', [DashboardController::class, 'dashboardRt'])->name('dashboard.rt')->middleware('role:rt,rw,admin');
    Route::get('/dashboard/warga', [DashboardController::class, 'dashboardWarga'])->name('dashboard.warga');

    // Keuangan detail route
    Route::get('/keuangan/detail', [KeuanganController::class, 'detail'])->name('keuangan.detail');
    
    // Pengumuman routes
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    
    // Pengumuman Manajemen (Khusus Pengurus RW, RT, Admin, Super Admin)
    Route::middleware('role:rw,rt,admin,super_admin')->group(function () {
        Route::get('/pengumuman/create', [PengumumanController::class, 'create'])->name('pengumuman.create');
        Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
        Route::get('/pengumuman/{id}/edit', [PengumumanController::class, 'edit'])->name('pengumuman.edit');
        Route::put('/pengumuman/{id}', [PengumumanController::class, 'update'])->name('pengumuman.update');
        Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');
    });

    Route::get('/pengumuman/{id}', [PengumumanController::class, 'show'])->name('pengumuman.show');
    Route::get('/pengumuman/{id}/detail', [PengumumanController::class, 'detail'])->name('pengumuman.detail');

    // --- POSYANDU TERPADU RW 013 (ROLE-AWARE & OPERASIONAL) ---
    Route::get('/posyandu', [PosyanduController::class, 'index'])->name('posyandu');
    Route::get('/posyandu/peserta/{id}', [PosyanduController::class, 'showPeserta'])->name('posyandu.peserta.show');
    Route::post('/posyandu/daftar-keluarga', [PosyanduController::class, 'daftarKeluarga'])->name('posyandu.daftar_keluarga');
    Route::get('/posyandu/laporan/{id}', [PosyanduController::class, 'showLaporan'])->name('posyandu.laporan.show');
    Route::get('/posyandu/laporan/{id}/cetak', [PosyanduController::class, 'cetakLaporan'])->name('posyandu.laporan.cetak');
    
    // Operasional Posyandu RT / RW
    Route::middleware('role:rt,rw,admin,super_admin')->group(function () {
        Route::post('/posyandu/peserta', [PosyanduController::class, 'storePeserta'])->name('posyandu.peserta.store');
        Route::post('/posyandu/peserta/{id}/pengukuran', [PosyanduController::class, 'storePengukuran'])->name('posyandu.pengukuran.store');
        Route::post('/posyandu/jadwal', [PosyanduController::class, 'storeJadwal'])->name('posyandu.jadwal.store');
        Route::post('/posyandu/jadwal/{id}/batal', [PosyanduController::class, 'batalJadwal'])->name('posyandu.jadwal.batal');
        Route::post('/posyandu/jadwal/{id}/kehadiran', [PosyanduController::class, 'catatKehadiran'])->name('posyandu.jadwal.kehadiran');
        Route::post('/posyandu/jadwal/{id}/laporan', [PosyanduController::class, 'storeLaporan'])->name('posyandu.jadwal.laporan');
        Route::post('/posyandu/kader', [PosyanduController::class, 'storeKader'])->name('posyandu.kader.store');
        Route::get('/posyandu/api/search-warga', [PosyanduController::class, 'searchWarga'])->name('posyandu.api.search_warga');
    });

    // Monitoring & Evaluasi Khusus RW / Admin
    Route::middleware('role:rw,admin,super_admin')->group(function () {
        Route::post('/posyandu/laporan/{id}/evaluasi', [PosyanduController::class, 'evaluasiLaporan'])->name('posyandu.laporan.evaluasi');
        Route::post('/posyandu/monitoring/pengingat/{rt}', [PosyanduController::class, 'kirimPengingat'])->name('posyandu.monitoring.pengingat');
    });

    // Route legacy / kompatibilitas: Otomatis dialihkan ke route posyandu utama (tanpa kebocoran privasi)
    Route::get('/posyandu/jadwal', fn() => redirect()->route('posyandu'))->name('posyandu.jadwal'); 
    Route::get('/posyandu/lokasi', fn() => redirect()->route('posyandu'))->name('posyandu.lokasi');
    Route::get('/posyandu/detail-balita', fn() => redirect()->route('posyandu'))->name('posyandu.detail_balita');
    Route::get('/posyandu/detail-balita/{id}', fn() => redirect()->route('posyandu'));
    Route::get('/posyandu/detail-imunisasi', fn() => redirect()->route('posyandu'))->name('posyandu.detail_imunisasi');
    Route::get('/posyandu/detail-ibu-hamil', fn() => redirect()->route('posyandu'))->name('posyandu.detail_ibu_hamil');
    Route::get('/posyandu/detail-edukasi', fn() => redirect()->route('posyandu'))->name('posyandu.detail_edukasi');
    // -----------------------------------------------------------

    // --- UMKM MARKETPLACE LOKAL RW 013 ---
    Route::get('/umkm', [UmkmController::class, 'index'])->name('umkm.index');
    Route::get('/umkm/saya', [UmkmController::class, 'saya'])->name('umkm.saya');
    Route::get('/umkm/moderasi', [UmkmController::class, 'moderasi'])->name('umkm.moderasi');
    Route::post('/umkm', [UmkmController::class, 'store'])->name('umkm.store');
    Route::get('/umkm/{id}', [UmkmController::class, 'show'])->name('umkm.show');
    Route::get('/umkm/{id}/edit', [UmkmController::class, 'edit'])->name('umkm.edit');
    Route::put('/umkm/{id}', [UmkmController::class, 'update'])->name('umkm.update');
    Route::get('/umkm/{id}/menu', [UmkmController::class, 'kelolaMenu'])->name('umkm.menu');
    Route::post('/umkm/{id}/moderasi', [UmkmController::class, 'postModerasi'])->name('umkm.moderasi.post');
    Route::post('/umkm/{id}/hubungkan-pemilik', [UmkmController::class, 'hubungkanPemilik'])->name('umkm.hubungkan');
    Route::post('/umkm/{id}/produk', [UmkmController::class, 'storeProduk'])->name('umkm.produk.store');
    Route::put('/umkm/produk/{id}', [UmkmController::class, 'updateProduk'])->name('umkm.produk.update');
    Route::delete('/umkm/produk/{id}', [UmkmController::class, 'deleteProduk'])->name('umkm.produk.destroy');
    Route::delete('/umkm/{id}', [UmkmController::class, 'destroy'])->name('umkm.destroy');
    // -------------------------------------

    // --- PETA WILAYAH & WISATA ROUTES ---
    Route::get('/peta', [DashboardController::class, 'peta'])->name('peta.index');
    Route::get('/wisata', [DashboardController::class, 'wisata'])->name('wisata.index');
    // ------------------------------------
    
    // --- BANK SAMPAH TERPADU RW 013 ---
    Route::get('/bank-sampah', [BankSampahController::class, 'index'])->name('bank-sampah');
    Route::post('/bank-sampah/setor', [BankSampahController::class, 'storePengajuan'])->name('bank-sampah.setor');
    Route::get('/bank-sampah/kelola', [BankSampahController::class, 'kelola'])->name('bank-sampah.kelola');
    Route::get('/bank-sampah/riwayat', [BankSampahController::class, 'riwayatSaya'])->name('bank-sampah.riwayat');
    Route::get('/bank-sampah/tarif', [BankSampahController::class, 'kelolaTarif'])->name('bank-sampah.tarif');
    Route::post('/bank-sampah/tarif/{id}', [BankSampahController::class, 'updateTarif'])->name('bank-sampah.tarif.update');
    Route::post('/bank-sampah/unit/{id}/petugas', [BankSampahController::class, 'updatePetugas'])->name('bank-sampah.unit.petugas');
    Route::get('/bank-sampah/laporan', [BankSampahController::class, 'laporan'])->name('bank-sampah.laporan');
    Route::get('/bank-sampah/transaksi/{id}', [BankSampahController::class, 'showTransaksi'])->name('bank-sampah.show');
    Route::get('/bank-sampah/transaksi/{id}/cetak', [BankSampahController::class, 'cetakSlip'])->name('bank-sampah.cetak');
    Route::post('/bank-sampah/transaksi/{id}/batal', [BankSampahController::class, 'batalTransaksi'])->name('bank-sampah.batal');
    Route::post('/bank-sampah/transaksi/{id}/terima', [BankSampahController::class, 'terimaTransaksi'])->name('bank-sampah.terima');
    Route::post('/bank-sampah/transaksi/{id}/timbang', [BankSampahController::class, 'timbangTransaksi'])->name('bank-sampah.timbang');
    Route::post('/bank-sampah/transaksi/{id}/konfirmasi', [BankSampahController::class, 'konfirmasiSelesai'])->name('bank-sampah.konfirmasi');
    Route::post('/bank-sampah/transaksi/{id}/tolak', [BankSampahController::class, 'tolakTransaksi'])->name('bank-sampah.tolak');

    // --- SISTEM LAYANAN WARGA LAINNYA ---
    Route::get('/ronda', [DashboardController::class, 'ronda'])->name('ronda.index');
    Route::post('/ronda/lapor-keamanan', [DashboardController::class, 'storeLaporKeamanan'])->name('ronda.lapor-keamanan');
    Route::post('/ronda/peringatan/{id}/konfirmasi-rt', [DashboardController::class, 'konfirmasiPeringatanRt'])->name('ronda.peringatan.konfirmasi-rt');
    Route::post('/ronda/peringatan/{id}/umumkan-kondusif', [DashboardController::class, 'umumkanPeringatanKondusif'])->name('ronda.peringatan.umumkan-kondusif');
    Route::post('/ronda/cctv-request', [DashboardController::class, 'storeCctvRequest'])->name('ronda.cctv-request.store');
    Route::post('/ronda/cctv-request/{id}/approve-rt', [DashboardController::class, 'approveCctvRequestRt'])->name('ronda.cctv-request.approve-rt');
    Route::post('/ronda/cctv-request/{id}/approve-rw', [DashboardController::class, 'approveCctvRequestRw'])->name('ronda.cctv-request.approve-rw');
    Route::post('/ronda/cctv-request/{id}/reject', [DashboardController::class, 'rejectCctvRequest'])->name('ronda.cctv-request.reject');
    Route::post('/ronda/portal/set-mode', [DashboardController::class, 'setPortalMode'])->name('ronda.portal.set-mode');
    Route::post('/ronda/portal/manual-register', [DashboardController::class, 'manualRegisterPortal'])->name('ronda.portal.manual-register');
    Route::post('/ronda/portal/unlink', [DashboardController::class, 'unlinkPortalRfid'])->name('ronda.portal.unlink');
    Route::get('/pengaduan', [DashboardController::class, 'pengaduan'])->name('pengaduan.index');
    Route::post('/pengaduan', [DashboardController::class, 'storePengaduan'])->name('pengaduan.store');
    Route::patch('/pengaduan/{id}/status', [DashboardController::class, 'updateStatusPengaduan'])->name('pengaduan.status');

    // --- MANAJEMEN RT/RW ---
    Route::get('/data-rt', [DashboardController::class, 'dataRt'])->name('data-rt.index');
    Route::get('/monitoring-rt', [DashboardController::class, 'monitoringRt'])->name('monitoring-rt.index');
    Route::get('/monitoring-rt/{rt}', [DashboardController::class, 'detailMonitoringRt'])->name('monitoring-rt.show');
    Route::post('/monitoring-rt/{rt}/evaluasi', [DashboardController::class, 'saveEvaluasiRt'])->name('monitoring-rt.evaluasi');
    Route::get('/laporan-rt', [DashboardController::class, 'laporanRt'])->name('laporan-rt.index');
    
    // --- FITUR AGENDA & RAPAT RW (SISTEM NAVIGASI 3 TAB & MONITORING RT) ---
    Route::get('/agenda-rw', [AgendaController::class, 'index'])->name('agenda-rw.index');
    Route::post('/agenda-rw', [AgendaController::class, 'store'])->name('agenda-rw.store');
    Route::get('/agenda-rw/{id}', [AgendaController::class, 'show'])->name('agenda-rw.show');
    Route::post('/agenda-rw/{id}/status', [AgendaController::class, 'updateStatus'])->name('agenda-rw.status');
    Route::delete('/agenda-rw/{id}', [AgendaController::class, 'destroy'])->name('agenda-rw.destroy');

    // --- MANAJEMEN AKUN KETUA RT (KHUSUS RW & ADMIN) ---
    Route::middleware('role:rw,admin,super_admin')->group(function () {
        Route::get('/manajemen-akun-rt', [AkunRtController::class, 'index'])->name('rt.akun.index');
        Route::post('/manajemen-akun-rt', [AkunRtController::class, 'store'])->name('rt.akun.store');
        Route::put('/manajemen-akun-rt/{id}', [AkunRtController::class, 'update'])->name('rt.akun.update');
        Route::patch('/manajemen-akun-rt/{id}/toggle-active', [AkunRtController::class, 'toggleActive'])->name('rt.akun.toggle');
        Route::post('/manajemen-akun-rt/{id}/reset-password', [AkunRtController::class, 'resetPassword'])->name('rt.akun.reset_password');
        Route::post('/manajemen-akun-rt/generate-initial', [AkunRtController::class, 'generateInitial'])->name('rt.akun.generate');

        // Verifikasi & Persetujuan Akhir Surat oleh RW 013
        Route::get('/rw/surat', [SuratController::class, 'rwIndex'])->name('rw.surat.index');
        Route::post('/rw/surat/{id}/approve', [SuratController::class, 'rwApprove'])->name('rw.surat.approve');
        Route::post('/rw/surat/{id}/reject', [SuratController::class, 'rwReject'])->name('rw.surat.reject');
        Route::post('/rw/surat/{id}/revisi', [SuratController::class, 'rwRevisi'])->name('rw.surat.revisi');

        // Master Katalog Jenis, Template, dan Konfigurasi Alur Surat RW 013
        Route::get('/rw/surat-master', [LetterMasterController::class, 'index'])->name('rw.surat_master.index');
        Route::post('/rw/surat-master', [LetterMasterController::class, 'store'])->name('rw.surat_master.store');
        Route::get('/rw/surat-master/{id}/edit', [LetterMasterController::class, 'edit'])->name('rw.surat_master.edit');
        Route::put('/rw/surat-master/{id}', [LetterMasterController::class, 'update'])->name('rw.surat_master.update');
        Route::put('/rw/surat-master/{id}/alur', [LetterMasterController::class, 'updateAlur'])->name('rw.surat_master.update_alur');
        Route::patch('/rw/surat-master/{id}/toggle', [LetterMasterController::class, 'toggleActive'])->name('rw.surat_master.toggle');

        // Monitoring Keuangan RW 013 (Sentral, Terpadu & Read-Only)
        Route::get('/rw/monitoring-keuangan', [KeuanganController::class, 'monitoringRw'])->name('rw.keuangan.monitoring');

        // Audit & Evaluasi RW 013
        Route::get('/rw/audit', [AuditController::class, 'index'])->name('rw.audit.index');
        Route::post('/rw/audit/kas-rw/store', [AuditController::class, 'storeTransaksiRw'])->name('rw.audit.kas_rw.store');
        Route::post('/rw/audit/kas-rw/{id}/update', [AuditController::class, 'updateTransaksiRw'])->name('rw.audit.kas_rw.update');
        Route::post('/rw/audit/rekap-rt/{rt}/verifikasi', [AuditController::class, 'verifikasiLaporanRt'])->name('rw.audit.rekap_rt.verifikasi');
        Route::post('/rw/audit/temuan', [AuditController::class, 'storeTemuan'])->name('rw.audit.temuan.store');
        Route::post('/rw/audit/temuan/{id}/status', [AuditController::class, 'updateStatusTemuan'])->name('rw.audit.temuan.status');
        Route::post('/rw/audit/evaluasi-rt', [AuditController::class, 'storeEvaluasiRt'])->name('rw.audit.evaluasi.store');
        Route::get('/rw/audit/cetak-laporan', [AuditController::class, 'cetakLaporanTahunan'])->name('rw.audit.cetak');
    });

    // Tanggapan/Klarifikasi Temuan Audit oleh RT
    Route::post('/rw/audit/temuan/{id}/tanggapi', [AuditController::class, 'tanggapiTemuan'])->name('rw.audit.temuan.tanggapi');

    // --- LAYANAN PENERBITAN SURAT RESMI (BERJENJANG RT -> RW 013) ---
    Route::get('/surat', [SuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/create', [SuratController::class, 'create'])->name('surat.create');
    Route::post('/surat', [SuratController::class, 'store'])->name('surat.store');
    Route::get('/surat/{id}', [SuratController::class, 'show'])->name('surat.show');
    Route::get('/surat/{id}/cetak', [SuratController::class, 'cetak'])->name('surat.cetak');
    // -----------------------------------------------------------------

    // --- SISTEM KEUANGAN TERPADU WARKAS MACHI RW 013 (MULTI-KAS & MULTI-ROLE) ---
    // Dashboard Keuangan Berjenjang (RW / RT / Warga)
    Route::get('/keuangan', [KeuanganController::class, 'dashboard'])->name('keuangan.index');
    Route::get('/keuangan/dashboard', [KeuanganController::class, 'dashboard'])->name('keuangan.dashboard');
    Route::get('/keuangan/saya', [KeuanganController::class, 'dashboardWarga'])->name('warga.keuangan.index');

    // Modul Mandiri Keuangan Warga (Keuangan Saya) - Kompatibilitas Redirect ke Tab
    Route::get('/keuangan/saya/tagihan', [KeuanganController::class, 'wargaTagihan'])->name('warga.keuangan.tagihan');
    Route::get('/keuangan/saya/bayar', [KeuanganController::class, 'wargaBayar'])->name('warga.keuangan.bayar');
    Route::get('/keuangan/saya/riwayat', [KeuanganController::class, 'wargaRiwayat'])->name('warga.keuangan.riwayat');
    Route::get('/keuangan/saya/kuitansi', [KeuanganController::class, 'wargaKuitansi'])->name('warga.keuangan.kuitansi');

    // Buku Kas (Kas RW, Kas RT 001-008, Kas Sampah, Dana Kegiatan)
    Route::get('/keuangan/buku-kas', [KeuanganController::class, 'bukuKasIndex'])->name('keuangan.buku_kas.index');
    Route::get('/keuangan/buku-kas/{id}', [KeuanganController::class, 'bukuKasDetail'])->name('keuangan.buku_kas.show');

    // Jurnal Transaksi Keuangan & Catat Transaksi
    Route::get('/keuangan/transaksi', [KeuanganController::class, 'transaksiIndex'])->name('keuangan.transaksi.index');
    Route::get('/keuangan/transaksi/create', [KeuanganController::class, 'transaksiCreate'])->name('keuangan.transaksi.create');
    Route::post('/keuangan/transaksi', [KeuanganController::class, 'transaksiStore'])->name('keuangan.transaksi.store');
    Route::post('/keuangan/transaksi/{id}/batal', [KeuanganController::class, 'transaksiCancel'])->name('keuangan.transaksi.cancel');

    // Laporan Keuangan Terpadu & Cetak PDF / Kop Resmi
    Route::get('/keuangan/laporan', [KeuanganController::class, 'laporanIndex'])->name('keuangan.laporan.index');
    Route::get('/keuangan/laporan/cetak', [KeuanganController::class, 'laporanCetak'])->name('keuangan.laporan.cetak');

    // Pengaturan Tarif Iuran Bulanan KK (Khusus RW / Admin)
    Route::get('/keuangan/pengaturan-iuran', [KeuanganController::class, 'pengaturanIuran'])->name('keuangan.pengaturan');
    Route::post('/keuangan/pengaturan-iuran/{id}', [KeuanganController::class, 'updateTarif'])->name('keuangan.pengaturan.update');
    Route::post('/keuangan/rt/lapor', [KeuanganController::class, 'kirimLaporanRt'])->name('keuangan.rt.lapor');

    // Modul Tagihan Iuran Keluarga
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::get('/tagihan/cetak', [TagihanController::class, 'cetakLaporan'])->name('tagihan.cetak');
    Route::post('/tagihan/generate', [TagihanController::class, 'generateBulanan'])->name('tagihan.generate');
    Route::get('/tagihan/{id}', [TagihanController::class, 'show'])->name('tagihan.show');
    Route::post('/tagihan/{id}/pay-digital', [TagihanController::class, 'payDigital'])->name('tagihan.pay_digital');
    Route::post('/tagihan/{id}/bayar', [TagihanController::class, 'konfirmasiPembayaran'])->name('tagihan.bayar');
    Route::post('/tagihan/{id}/verifikasi', [TagihanController::class, 'verifikasiPembayaran'])->name('tagihan.verifikasi');
    Route::post('/tagihan/{id}/bayar-sebagian', [TagihanController::class, 'prosesPembayaran'])->name('tagihan.bayar_sebagian');
    Route::post('/tagihan/{id}/bebaskan', [TagihanController::class, 'bebaskanTagihan'])->name('tagihan.bebaskan');
    Route::get('/tagihan/{id}/kuitansi', [TagihanController::class, 'kuitansi'])->name('tagihan.kuitansi');
    Route::post('/tagihan/setor-tunai-rw', [TagihanController::class, 'setorTunaiKeRw'])->name('tagihan.setor_tunai_rw');
    Route::post('/tagihan/keringanan', [TagihanController::class, 'keringananStore'])->name('tagihan.keringanan.store');
    Route::post('/tagihan/keringanan/{id}/batal', [TagihanController::class, 'keringananBatal'])->name('tagihan.keringanan.batal');
    Route::post('/webhook/payment-gateway', [TagihanController::class, 'handleWebhook'])->name('payment.webhook');

    // Backward-Compatibility Aliases
    Route::get('/kas-rw', [KeuanganController::class, 'dashboard'])->name('kas-rw.index');
    Route::get('/laporan/keuangan', [KeuanganController::class, 'laporanIndex'])->name('laporan.keuangan');
    Route::resource('iuran', IuranController::class);
    Route::resource('pembayaran', PembayaranController::class);
    Route::post('/pembayaran/{id}/verifikasi', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    
    // --- KOORDINASI RT & PELAPORAN KE RW ---
    Route::middleware('role:rt,rw,admin')->group(function () {
        // Layanan Verifikasi Surat Tingkat RT
        Route::get('/rt/surat', [SuratController::class, 'rtIndex'])->name('rt.surat.index');
        Route::post('/rt/surat/{id}/approve', [SuratController::class, 'rtApprove'])->name('rt.surat.approve');
        Route::post('/rt/surat/{id}/reject', [SuratController::class, 'rtReject'])->name('rt.surat.reject');
        Route::post('/rt/surat/{id}/revisi', [SuratController::class, 'rtRevisi'])->name('rt.surat.revisi');

        Route::get('/rt/keluarga', [DashboardController::class, 'rtKeluarga'])->name('rt.keluarga');
        Route::get('/rt/pendatang', [DashboardController::class, 'rtPendatang'])->name('rt.pendatang');
        Route::get('/rt/mutasi', [DashboardController::class, 'rtMutasi'])->name('rt.mutasi');
        Route::get('/rt/laporan', [DashboardController::class, 'rtLaporan'])->name('rt.laporan');
        Route::get('/rt/laporan/kirim', [DashboardController::class, 'rtLaporanCreate'])->name('rt.laporan.create');
        Route::post('/rt/laporan/kirim', [DashboardController::class, 'rtLaporanStore'])->name('rt.laporan.store');
        Route::get('/rt/evaluasi', [DashboardController::class, 'rtEvaluasi'])->name('rt.evaluasi');
        Route::get('/rt/arsip', [DashboardController::class, 'rtArsip'])->name('rt.arsip');
        Route::get('/rt/profil', [DashboardController::class, 'rtProfil'])->name('rt.profil');
    });

    // Warga & Kas routes (Pengurus RW, Bendahara, dan Ketua RT sesuai hak akses)
    Route::middleware('role:admin,bendahara,rt,rw,super_admin')->group(function () {
        Route::post('/posyandu/detail-balita/{id}/perkembangan', fn() => redirect()->route('posyandu'))->name('posyandu.store_perkembangan');
        
        // ======================================================================
        // MODUL TUNGGAL: DATA KEPENDUDUKAN RW 013 (WARGA, KELUARGA & DOKUMEN KK)
        // ======================================================================
        Route::get('/kependudukan', [DataKependudukanController::class, 'index'])->name('kependudukan.index');
        Route::get('/kependudukan/keluarga/{id}', [DataKependudukanController::class, 'detailKeluarga'])->name('kependudukan.keluarga.detail');
        Route::post('/kependudukan/keluarga/{id}/anggota', [DataKependudukanController::class, 'tambahAnggotaKeluarga'])->name('kependudukan.keluarga.tambah_anggota');
        Route::post('/kependudukan/keluarga/{id}/pindah', [DataKependudukanController::class, 'pindahKeluarga'])->name('kependudukan.keluarga.pindah');
        Route::post('/kependudukan/keluarga/{id}/ganti-kepala', [DataKependudukanController::class, 'gantiKepalaKeluarga'])->name('kependudukan.keluarga.ganti_kepala');
        Route::post('/kependudukan/keluarga/{id}/upload-dokumen', [DataKependudukanController::class, 'uploadDokumenKk'])->name('kependudukan.keluarga.upload');
        Route::post('/kependudukan/keluarga/{id}/verifikasi-dokumen', [DataKependudukanController::class, 'verifikasiDokumenKk'])->name('kependudukan.keluarga.verifikasi');
        Route::get('/kependudukan/keluarga/{id}/cetak', [DataKependudukanController::class, 'cetakKk'])->name('kependudukan.keluarga.cetak');
        Route::get('/kependudukan/api/warga/{id}', [DataKependudukanController::class, 'apiWargaDetail'])->name('kependudukan.api.warga');
        Route::get('/kependudukan/verifikasi', [DataKependudukanController::class, 'verifikasiWarga'])->name('kependudukan.verifikasi');
        Route::get('/kependudukan/riwayat', [DataKependudukanController::class, 'riwayatPerubahan'])->name('kependudukan.riwayat');
        Route::get('/kependudukan/laporan', [DataKependudukanController::class, 'laporanKependudukan'])->name('kependudukan.laporan');
        Route::post('/kependudukan/mutasi', [DataKependudukanController::class, 'storeMutasi'])->name('kependudukan.mutasi.store');

        // Backward-compatibility Aliases (Menghindari 404 & Mengarahkan ke Tab Terpadu)
        Route::get('/warga', function () {
            return redirect()->route('kependudukan.index', ['tab' => 'individu']);
        })->name('warga.index');

        Route::get('/kependudukan/keluarga', function () {
            return redirect()->route('kependudukan.index', ['tab' => 'keluarga']);
        })->name('kependudukan.keluarga');

        Route::get('/arsip-kk', function () {
            return redirect()->route('kependudukan.index', ['tab' => 'dokumen']);
        })->name('arsip-kk.index');

        Route::get('/arsip-kk/{id}', function ($id) {
            return redirect()->route('kependudukan.keluarga.detail', $id);
        })->name('arsip-kk.show');

        Route::get('/arsip-kk/{id}/cetak', [DataKependudukanController::class, 'cetakKk'])->name('arsip-kk.cetak');
        Route::get('/arsip-kk/{id}/stream', [DataKependudukanController::class, 'cetakKk'])->name('arsip-kk.stream');
        Route::post('/arsip-kk/{id}/upload', [DataKependudukanController::class, 'uploadDokumenKk'])->name('arsip-kk.upload');
        Route::post('/arsip-kk/{id}/verifikasi', [DataKependudukanController::class, 'verifikasiDokumenKk'])->name('arsip-kk.verifikasi');

        // FITUR WARGA, DOKUMEN & VERIFIKASI BERJENJANG (CRUD Tetap Berfungsi Penuh)
        Route::get('/warga/keluarga/{no_kk}', [WargaController::class, 'getKeluargaDetail'])->name('warga.keluarga_detail');
        Route::post('/warga/keluarga/{no_kk}/anggota', [WargaController::class, 'storeAnggotaKeluarga'])->name('warga.store_anggota');
        Route::post('/warga/{warga}/verifikasi', [WargaController::class, 'verifikasi'])->name('warga.verifikasi');
        Route::post('/warga/{warga}/reset-password', [WargaController::class, 'resetPassword'])->name('warga.reset_password');
        Route::get('/warga/{id}/dokumen/{type}', [WargaController::class, 'downloadDokumen'])->name('warga.dokumen.download');
        Route::resource('warga', WargaController::class, ['except' => ['index']]);
        Route::patch('/warga/{warga}/toggle-active', [WargaController::class, 'toggleActive'])->name('warga.toggleActive');
        Route::resource('kas-rt', KasController::class);
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/keuangan', [LaporanController::class, 'laporanKeuangan'])->name('laporan.keuangan');
        Route::get('/laporan/perwarga', [LaporanController::class, 'laporanPerWarga'])->name('laporan.perwarga');
    });

    // --- CCTV (kelola khusus admin) ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/cctv', [CctvController::class, 'index'])->name('cctv.index');
        Route::post('/cctv', [CctvController::class, 'store'])->name('cctv.store');
        Route::put('/cctv/{cctv}', [CctvController::class, 'update'])->name('cctv.update');
        Route::delete('/cctv/{cctv}', [CctvController::class, 'destroy'])->name('cctv.destroy');
    });
});

// --- API KHUSUS IOT ESP32 GERBANG & RFID RW 013 (BEBAS CSRF, DIPROTEKSI API KEY) ---
Route::prefix('api/iot/portal')->group(function () {
    Route::post('/tap', [\App\Http\Controllers\Api\PortalIotController::class, 'tap'])->name('api.iot.portal.tap');
    Route::get('/live-status', [\App\Http\Controllers\Api\PortalIotController::class, 'liveStatus'])->name('api.iot.portal.live_status');
});