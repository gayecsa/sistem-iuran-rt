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
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.edit');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/make-admin', [AuthController::class, 'makeAdmin'])->name('profile.makeAdmin');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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
    
    // Warga routes (Khusus admin & bendahara saja)
    Route::middleware('role:admin,bendahara')->group(function () {
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
});