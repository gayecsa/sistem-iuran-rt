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

// Auth routes (Semua yang sudah login bisa akses ini)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
    Route::post('/profile/make-admin', [AuthController::class, 'makeAdmin'])->name('profile.makeAdmin');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/rw', [DashboardController::class, 'dashboardRw'])->name('dashboard.rw')->middleware('role:rw,admin,bendahara');
    Route::get('/dashboard/rt', [DashboardController::class, 'dashboardRt'])->name('dashboard.rt')->middleware('role:rt,rw,admin');
    Route::get('/dashboard/warga', [DashboardController::class, 'dashboardWarga'])->name('dashboard.warga');
    
    // Pindah ke sini: Agar semua warga yang login bisa upload & ganti foto profil
    Route::post('/profile/upload-foto', [DashboardController::class, 'uploadFoto'])->name('profile.upload-foto');

    // Keuangan detail route
    Route::get('/keuangan/detail', [KeuanganController::class, 'detail'])->name('keuangan.detail');
    
    // Pengumuman routes
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::get('/pengumuman/{id}', [PengumumanController::class, 'show'])->name('pengumuman.show');
    Route::get('/pengumuman/{id}/detail', [PengumumanController::class, 'detail'])->name('pengumuman.detail');

    // --- POSYANDU ROUTES ---
    Route::get('/posyandu', [DashboardController::class, 'posyandu'])->name('posyandu');
    Route::get('/posyandu/jadwal', [DashboardController::class, 'jadwal'])->name('posyandu.jadwal'); 
    Route::get('/posyandu/lokasi', [DashboardController::class, 'lokasi'])->name('posyandu.lokasi');
    Route::get('/posyandu/detail-balita', [DashboardController::class, 'detailBalita'])->name('posyandu.detail_balita');
    Route::get('/posyandu/detail-balita/{id}', [DashboardController::class, 'getBalitaDetail']);
    Route::get('/posyandu/detail-imunisasi', [DashboardController::class, 'detailImunisasi'])->name('posyandu.detail_imunisasi');
    Route::get('/posyandu/detail-ibu-hamil', [DashboardController::class, 'detailIbuHamil'])->name('posyandu.detail_ibu_hamil');
    Route::get('/posyandu/detail-edukasi', [DashboardController::class, 'detailEdukasi'])->name('posyandu.detail_edukasi');
    // -----------------------

    // --- UMKM ROUTES ---
    Route::get('/umkm', [DashboardController::class, 'umkm'])->name('umkm.index');
    Route::get('/umkm/{id}/edit', [UmkmController::class, 'edit'])->name('umkm.edit');     // <-- RUTE BARU EDIT UMKM
    Route::put('/umkm/{id}', [UmkmController::class, 'update'])->name('umkm.update');      // <-- RUTE BARU UPDATE UMKM
    // -------------------

    // --- PETA WILAYAH & WISATA ROUTES ---
    Route::get('/peta', [DashboardController::class, 'peta'])->name('peta.index');
    Route::get('/wisata', [DashboardController::class, 'wisata'])->name('wisata.index');
    // ------------------------------------
    
    // --- LAYANAN MASYARAKAT & SISTEM MANAJEMEN RW 013 ---
    Route::get('/bank-sampah', [DashboardController::class, 'bankSampah'])->name('bank-sampah');
    Route::get('/ronda', [DashboardController::class, 'ronda'])->name('ronda.index');
    Route::get('/pengaduan', [DashboardController::class, 'pengaduan'])->name('pengaduan.index');
    Route::post('/pengaduan', [DashboardController::class, 'storePengaduan'])->name('pengaduan.store');
    Route::patch('/pengaduan/{id}/status', [DashboardController::class, 'updateStatusPengaduan'])->name('pengaduan.status');

    // --- MANAJEMEN RT/RW ---
    Route::get('/data-rt', [DashboardController::class, 'dataRt'])->name('data-rt.index');
    Route::get('/monitoring-rt', [DashboardController::class, 'monitoringRt'])->name('monitoring-rt.index');
    Route::get('/monitoring-rt/{rt}', [DashboardController::class, 'detailMonitoringRt'])->name('monitoring-rt.show');
    Route::post('/monitoring-rt/{rt}/evaluasi', [DashboardController::class, 'saveEvaluasiRt'])->name('monitoring-rt.evaluasi');
    Route::get('/laporan-rt', [DashboardController::class, 'laporanRt'])->name('laporan-rt.index');
    Route::get('/agenda-rw', [DashboardController::class, 'agendaRw'])->name('agenda-rw.index');

    // --- MANAJEMEN AKUN KETUA RT (KHUSUS RW & ADMIN) ---
    Route::middleware('role:rw,admin,super_admin')->group(function () {
        Route::get('/manajemen-akun-rt', [AkunRtController::class, 'index'])->name('rt.akun.index');
        Route::post('/manajemen-akun-rt', [AkunRtController::class, 'store'])->name('rt.akun.store');
        Route::put('/manajemen-akun-rt/{id}', [AkunRtController::class, 'update'])->name('rt.akun.update');
        Route::patch('/manajemen-akun-rt/{id}/toggle-active', [AkunRtController::class, 'toggleActive'])->name('rt.akun.toggle');
        Route::post('/manajemen-akun-rt/{id}/reset-password', [AkunRtController::class, 'resetPassword'])->name('rt.akun.reset_password');
        Route::post('/manajemen-akun-rt/generate-initial', [AkunRtController::class, 'generateInitial'])->name('rt.akun.generate');
    });

    // --- KEUANGAN RW ---
    Route::get('/kas-rw', [DashboardController::class, 'kasRw'])->name('kas-rw.index');
    // ----------------------------------------------------

    // --- PERBAIKAN ROUTE SURAT ---
    Route::get('/surat', [DashboardController::class, 'surat'])->name('surat.index');
    Route::get('/surat/create', [DashboardController::class, 'createSurat'])->name('surat.create');
    Route::post('/surat', [DashboardController::class, 'storeSurat'])->name('surat.store');
    // -----------------------------
    
    // Debug route - Hanya untuk development
    if (config('app.debug')) {
        Route::get('/debug/kas', [DashboardController::class, 'debugKas'])->name('debug.kas');
    }
    
    // Iuran routes
    Route::resource('iuran', IuranController::class);
    
    // Pembayaran routes
    Route::resource('pembayaran', PembayaranController::class);
    Route::post('/pembayaran/{id}/verifikasi', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    
    // --- KOORDINASI RT & PELAPORAN KE RW ---
    Route::middleware('role:rt,rw,admin')->group(function () {
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
        Route::post('/posyandu/detail-balita/{id}/perkembangan', [DashboardController::class, 'storePerkembanganBalita'])->name('posyandu.store_perkembangan');
        Route::get('/warga/keluarga/{no_kk}', [WargaController::class, 'getKeluargaDetail'])->name('warga.keluarga_detail');
        Route::post('/warga/keluarga/{no_kk}/anggota', [WargaController::class, 'storeAnggotaKeluarga'])->name('warga.store_anggota');
        Route::resource('warga', WargaController::class);
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