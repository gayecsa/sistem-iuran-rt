<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\KeuanganController; // Tambahan untuk halaman detail grafik
use App\Models\User; // Tambahkan ini di atas agar Route bisa membaca Model User

// Guest routes
Route::middleware('guest')->group(function () {
    // FIX: URL '/' sekarang bisa menerima GET (tampilan) dan POST (proses login)
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/', [AuthController::class, 'login']); 
    
    // Tetap sediakan URL /login jika user mengetik manual di browser
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login']);
    
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
    
    // INI ROUTE BARU UNTUK KLIK CARD SALDO IURAN 
    Route::get('/detail-keuangan', [KeuanganController::class, 'detail'])->name('keuangan.detail');
    
    // Pindah ke sini: Agar semua warga yang login bisa upload & ganti foto profil
    Route::post('/profile/upload-foto', [DashboardController::class, 'uploadFoto'])->name('profile.upload-foto');
    
    // Iuran routes
    Route::resource('iuran', IuranController::class);
    
    // Pembayaran routes
    Route::resource('pembayaran', PembayaranController::class);
    Route::post('/pembayaran/{id}/verifikasi', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    
    // Warga routes (Khusus admin & bendahara saja)
    Route::middleware('role:admin,bendahara')->group(function () {
        Route::resource('warga', WargaController::class);
        Route::patch('/warga/{warga}/toggle-active', [WargaController::class, 'toggleActive'])->name('warga.toggleActive');
        Route::get('/kas-rt/create', [KasController::class, 'create'])->name('kas-rt.create');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/keuangan', [LaporanController::class, 'laporanKeuangan'])->name('laporan.keuangan');
        Route::get('/laporan/perwarga', [LaporanController::class, 'laporanPerWarga'])->name('laporan.perwarga');
        
        // --- TEMPELAN LANGKAH 2 (Suku Otomatis Inject No HP Warga ke Kolom Phone) ---
        Route::get('/inject-no-hp', function() {
            // Ambil semua user dengan role warga, kecuali Helga Darius
            $warga = User::where('role', 'warga')
                         ->where('name', '!=', 'Helga Darius')
                         ->get();

            $counter = 0;
            foreach ($warga as $index => $w) {
                // Membuat format nomor HP unik berurutan: 081234567001, 081234567002, dst.
                $no_hp_otomatis = '081234567' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                
                // PERBAIKAN: Menggunakan properti 'phone' sesuai dengan kolom database di Model User
                $w->update([
                    'phone' => $no_hp_otomatis
                ]);
                $counter++;
            }

            return "Berhasil memperbarui " . $counter . " warga! Nomor HP otomatis telah terisi pada kolom phone (Kecuali Helga Darius).";
        })->name('warga.inject-hp');
        // ----------------------------------------------------------------------------
    });

    // Jalur Kas RT tambahan
    Route::post('/kas-rt/store', [KasController::class, 'store'])->name('kas-rt.store');
    Route::get('/kas-rt', [KasController::class, 'sindex'])->name('kas-rt.index');

    // Route untuk mengambil data NIK & Alamat secara real-time berdasarkan nama
    Route::get('/get-warga-by-nama', [KasController::class, 'getWargaByNama'])->name('warga.get-by-nama');
    Route::get('/informasi-terbaru', [App\Http\Controllers\PengumumanController::class, 'index']);
});