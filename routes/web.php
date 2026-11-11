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

        // Verifikasi & Persetujuan Akhir Surat oleh RW 013
        Route::get('/rw/surat', [SuratController::class, 'rwIndex'])->name('rw.surat.index');
        Route::post('/rw/surat/{id}/approve', [SuratController::class, 'rwApprove'])->name('rw.surat.approve');
        Route::post('/rw/surat/{id}/reject', [SuratController::class, 'rwReject'])->name('rw.surat.reject');
        Route::post('/rw/surat/{id}/revisi', [SuratController::class, 'rwRevisi'])->name('rw.surat.revisi');

        // Master Katalog Jenis, Template, dan Konfigurasi Alur Surat RW 013
        Route::get('/rw/surat-master', [LetterMasterController::class, 'index'])->name('rw.surat_master.index');
        Route::get('/rw/surat-master/{id}/edit', [LetterMasterController::class, 'edit'])->name('rw.surat_master.edit');
        Route::put('/rw/surat-master/{id}', [LetterMasterController::class, 'update'])->name('rw.surat_master.update');
        Route::patch('/rw/surat-master/{id}/toggle', [LetterMasterController::class, 'toggleActive'])->name('rw.surat_master.toggle');
    });

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

    // Modul Tagihan Iuran Keluarga
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::post('/tagihan/generate', [TagihanController::class, 'generateBulanan'])->name('tagihan.generate');
    Route::get('/tagihan/{id}', [TagihanController::class, 'show'])->name('tagihan.show');
    Route::post('/tagihan/{id}/bayar', [TagihanController::class, 'konfirmasiPembayaran'])->name('tagihan.bayar');
    Route::post('/tagihan/{id}/verifikasi', [TagihanController::class, 'verifikasiPembayaran'])->name('tagihan.verifikasi');
    Route::post('/tagihan/{id}/bebaskan', [TagihanController::class, 'bebaskanTagihan'])->name('tagihan.bebaskan');
    Route::get('/tagihan/{id}/kuitansi', [TagihanController::class, 'kuitansi'])->name('tagihan.kuitansi');

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
        Route::post('/posyandu/detail-balita/{id}/perkembangan', [DashboardController::class, 'storePerkembanganBalita'])->name('posyandu.store_perkembangan');
        
        // MODUL DATA KEPENDUDUKAN & KELUARGA TERPADU RW 013
        Route::get('/kependudukan/keluarga', [DataKependudukanController::class, 'dataKeluarga'])->name('kependudukan.keluarga');
        Route::get('/kependudukan/verifikasi', [DataKependudukanController::class, 'verifikasiWarga'])->name('kependudukan.verifikasi');
        Route::get('/kependudukan/riwayat', [DataKependudukanController::class, 'riwayatPerubahan'])->name('kependudukan.riwayat');
        Route::get('/kependudukan/laporan', [DataKependudukanController::class, 'laporanKependudukan'])->name('kependudukan.laporan');

        // ARSIP KK RESMI DUKCAPIL
        Route::get('/arsip-kk', [ArsipKkController::class, 'index'])->name('arsip-kk.index');
        Route::get('/arsip-kk/{id}', [ArsipKkController::class, 'show'])->name('arsip-kk.show');
        Route::post('/arsip-kk/{id}/upload', [ArsipKkController::class, 'uploadDokumen'])->name('arsip-kk.upload');
        Route::post('/arsip-kk/{id}/verifikasi', [ArsipKkController::class, 'verifyDukcapil'])->name('arsip-kk.verifikasi');
        Route::get('/arsip-kk/{id}/stream', [ArsipKkController::class, 'streamPdf'])->name('arsip-kk.stream');
        Route::get('/arsip-kk/{id}/cetak', [ArsipKkController::class, 'cetak'])->name('arsip-kk.cetak');

        // FITUR WARGA, DOKUMEN & VERIFIKASI BERJENJANG
        Route::get('/warga/keluarga/{no_kk}', [WargaController::class, 'getKeluargaDetail'])->name('warga.keluarga_detail');
        Route::post('/warga/keluarga/{no_kk}/anggota', [WargaController::class, 'storeAnggotaKeluarga'])->name('warga.store_anggota');
        Route::post('/warga/{warga}/verifikasi', [WargaController::class, 'verifikasi'])->name('warga.verifikasi');
        Route::post('/warga/{warga}/reset-password', [WargaController::class, 'resetPassword'])->name('warga.reset_password');
        Route::get('/warga/{id}/dokumen/{type}', [WargaController::class, 'downloadDokumen'])->name('warga.dokumen.download');
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