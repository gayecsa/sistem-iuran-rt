<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\PosyanduPeserta;
use App\Models\PosyanduJadwal;
use App\Models\PosyanduKehadiran;
use App\Models\PosyanduPengukuran;
use App\Models\PosyanduLaporan;
use App\Models\PosyanduKader;
use App\Http\Controllers\PosyanduController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

View::share('errors', new \Illuminate\Support\ViewErrorBag);

echo "========================================================\n";
echo "TEST SUITE: PEMBAHARUAN TOTAL FITUR POSYANDU RW 013\n";
echo "========================================================\n\n";

$pass = 0;
$fail = 0;

function assertTest($cond, $msg) {
    global $pass, $fail;
    if ($cond) {
        echo " [PASS] " . $msg . "\n";
        $pass++;
    } else {
        echo " [FAIL] " . $msg . "\n";
        $fail++;
    }
}

$controller = new PosyanduController();

// 1. Identifikasi Aktor Uji
$adminRw = User::whereIn('role', ['rw', 'admin', 'super_admin'])->first();
$ketuaRt1 = User::where('role', 'rt')->where(function($q){ $q->where('rt_number', '001')->orWhere('rt_number', '1'); })->first();
$ketuaRt2 = User::where('role', 'rt')->where(function($q){ $q->where('rt_number', '002')->orWhere('rt_number', '2'); })->first();
$wargaRt1 = User::where('role', 'warga')->where(function($q){ $q->where('rt_number', '001')->orWhere('rt_number', '1'); })->first();
$wargaRt2 = User::where('role', 'warga')->where(function($q){ $q->where('rt_number', '002')->orWhere('rt_number', '2'); })->first();

assertTest($adminRw !== null, "Admin/RW ditemukan: {$adminRw->name} (Role: {$adminRw->role})");
assertTest($ketuaRt1 !== null, "Ketua RT 001 ditemukan: {$ketuaRt1->name}");
assertTest($ketuaRt2 !== null, "Ketua RT 002 ditemukan: {$ketuaRt2->name}");
assertTest($wargaRt1 !== null, "Warga RT 001 ditemukan: {$wargaRt1->name}");
assertTest($wargaRt2 !== null, "Warga RT 002 ditemukan: {$wargaRt2->name}");

// 2. UJI DASHBOARD MONITORING RW
Auth::login($adminRw);
$reqRw = Request::create('/posyandu', 'GET');
$respRw = $controller->index($reqRw);
$htmlRw = $respRw->render();

assertTest(str_contains($htmlRw, 'Pusat Pemantauan Posyandu RW 013'), "Dashboard RW berhasil render judul monitoring");
assertTest(str_contains($htmlRw, 'Matriks Monitoring Posyandu RT 001 – RT 008'), "Dashboard RW menampilkan matriks 8 RT");
assertTest(str_contains($htmlRw, 'RW Memantau & Mengevaluasi'), "Prinsip RW memantau ditegaskan pada tampilan");
assertTest(!str_contains($htmlRw, 'name="berat_badan"'), "RW tidak memiliki input form operasional timbangan");

// 3. UJI DASHBOARD OPERASIONAL RT 001
Auth::login($ketuaRt1);
$reqRt1 = Request::create('/posyandu', 'GET');
$respRt1 = $controller->index($reqRt1);
$htmlRt1 = $respRt1->render();

assertTest(str_contains($htmlRt1, 'OPERASIONAL POSYANDU RT 001'), "Dashboard RT 001 terisolasi di wilayah RT 001");
assertTest(str_contains($htmlRt1, 'Pendaftaran Peserta Posyandu RT 001'), "Tombol/Modal pendaftaran peserta tersedia untuk RT 001");
assertTest(str_contains($htmlRt1, 'Buat Jadwal Posyandu RT 001'), "Tombol/Modal buat jadwal tersedia untuk RT 001");

// 4. UJI RESTRIKSI RT 001 TIDAK BISA AKSES PESERTA RT 002
$pesertaRt2 = PosyanduPeserta::where('rt_number', '002')->first();
if ($pesertaRt2) {
    try {
        $controller->showPeserta($pesertaRt2->id);
        assertTest(false, "RT 001 seharusnya DIBLOKIR saat mengakses profil peserta RT 002");
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        assertTest($e->getStatusCode() === 403, "RT 001 DIBLOKIR (HTTP 403) saat mengakses profil peserta RT 002: {$e->getMessage()}");
    }
}

// 5. UJI PENCATATAN PENGUKURAN OLEH RT 001
$pesertaRt1 = PosyanduPeserta::where('rt_number', '001')->first();
assertTest($pesertaRt1 !== null, "Peserta RT 001 ditemukan: {$pesertaRt1->user->name} ({$pesertaRt1->kategori_label})");

$reqUkur = Request::create("/posyandu/peserta/{$pesertaRt1->id}/pengukuran", 'POST', [
    'tanggal_pemeriksaan' => date('Y-m-d'),
    'berat_badan' => 12.5,
    'tinggi_badan' => 88.0,
    'lingkar_kepala' => 47.0,
    'tindakan_layanan' => 'Pemberian PMT Biskuit Balita & Vitamin A',
    'catatan_petugas' => 'Pertumbuhan terpantau baik, nafsu makan aktif',
    'anjuran_rujukan' => null,
]);
$respUkur = $controller->storePengukuran($reqUkur, $pesertaRt1->id);
assertTest($respUkur->isRedirect(), "RT 001 berhasil menginput pengukuran peserta RT 001");

$pengukuranBaru = PosyanduPengukuran::where('peserta_id', $pesertaRt1->id)->orderBy('id', 'desc')->first();
assertTest($pengukuranBaru !== null && $pengukuranBaru->berat_badan == 12.5, "Data pengukuran tersimpan di DB: BB {$pengukuranBaru->berat_badan} kg, TB {$pengukuranBaru->tinggi_badan} cm");

// 6. UJI WARGA HANYA LIHAT KELUARGA SENDIRI
Auth::login($wargaRt1);
$reqWarga = Request::create('/posyandu', 'GET');
$respWarga = $controller->index($reqWarga);
$htmlWarga = $respWarga->render();

assertTest(str_contains($htmlWarga, 'Posyandu Keluarga RT 001'), "Portal Warga menampilkan posyandu sesuai RT-nya");
assertTest(str_contains($htmlWarga, 'Daftar 10 Layanan Terpadu Posyandu Melati 13'), "Warga dapat melihat 10 layanan posyandu resmi");

// Coba akses peserta RT lain oleh warga (harus 403)
if ($pesertaRt2) {
    try {
        $controller->showPeserta($pesertaRt2->id);
        assertTest(false, "Warga seharusnya DIBLOKIR saat mengakses data peserta keluarga lain");
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        assertTest($e->getStatusCode() === 403, "Warga DIBLOKIR (HTTP 403) mengakses data peserta keluarga lain: {$e->getMessage()}");
    }
}

// 7. UJI PEMBUATAN & EVALUASI LAPORAN
Auth::login($ketuaRt1);
$jadwalRt1 = PosyanduJadwal::where('rt_number', '001')->where('status', 'selesai')->first();
if ($jadwalRt1) {
    $reqLap = Request::create("/posyandu/jadwal/{$jadwalRt1->id}/laporan", 'POST', [
        'ringkasan_layanan' => 'Pelaksanaan penimbangan balita dan skrining tensi darah lansia berjalan lancar dengan pendampingan Bidan Puskesmas Arcamanik.',
        'kendala' => 'Antrean awal sempat padat di meja pendaftaran.',
        'catatan_evaluasi_rt' => 'Akan disiapkan 2 meja pendaftaran untuk agenda berikutnya.',
    ]);
    $respLap = $controller->storeLaporan($reqLap, $jadwalRt1->id);
    assertTest($respLap->isRedirect(), "RT 001 berhasil menyusun dan mengirim laporan kegiatan ke RW");

    $laporanRt1 = PosyanduLaporan::where('jadwal_id', $jadwalRt1->id)->first();
    assertTest($laporanRt1 !== null, "Laporan kegiatan tersimpan di DB (Nomor: {$laporanRt1->nomor_laporan})");

    // Login sebagai RW untuk evaluasi laporan
    Auth::login($adminRw);
    $reqEval = Request::create("/posyandu/laporan/{$laporanRt1->id}/evaluasi", 'POST', [
        'status_laporan' => 'disetujui_rw',
        'catatan_rw' => 'Laporan lengkap dan akuntabel. Partisipasi warga RT 001 sangat baik.',
    ]);
    $respEval = $controller->evaluasiLaporan($reqEval, $laporanRt1->id);
    assertTest($respEval->isRedirect(), "RW 013 berhasil mengevaluasi dan menyetujui laporan RT 001");

    $laporanRt1->refresh();
    assertTest($laporanRt1->status_laporan === 'disetujui_rw', "Status laporan berhasil terupdate menjadi 'disetujui_rw'");

    // Uji Cetak Laporan Resmi
    $reqCetak = Request::create("/posyandu/laporan/{$laporanRt1->id}/cetak", 'GET');
    $respCetak = $controller->cetakLaporan($laporanRt1->id);
    $htmlCetak = $respCetak->render();
    assertTest(str_contains($htmlCetak, 'LAPORAN KEGIATAN OPERASIONAL POSYANDU'), "Dokumen cetak resmi memuat judul laporan resmi");
    assertTest(str_contains($htmlCetak, 'KELURAHAN SUKAMISKIN KECAMATAN ARCAMANIK'), "Dokumen cetak memuat Kop Surat Resmi RW 013");
    assertTest(str_contains($htmlCetak, 'Ketua RW 013 Sukamiskin'), "Dokumen cetak memuat tanda tangan Ketua RW");
}

echo "\n========================================================\n";
echo "HASIL PENGUJIAN: $pass LULUS, $fail GAGAL\n";
echo "========================================================\n";
