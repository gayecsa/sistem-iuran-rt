<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Http\Controllers\BankSampahController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

View::share('errors', new \Illuminate\Support\ViewErrorBag);

$controller = new BankSampahController();

// 1. Test as Admin RW
$admin = User::whereIn('role', ['admin', 'rw', 'super_admin'])->first();
Auth::login($admin);
$resp = $controller->index(Request::create('/bank-sampah', 'GET'));
$html = $resp->render();

echo "Render as Admin RW: OK (" . strlen($html) . " bytes)\n";

// Cek urutan komponen dalam HTML:
$posBanner = strpos($html, 'Program Bank Sampah Terpadu RW 013');
$posRingkasan = strpos($html, 'Jadwal Setor Sampah');
$posAlur = strpos($html, 'Alur Kerja Setoran Bank Sampah RW 013');
$posTitik = strpos($html, 'Titik Pengumpulan & Petugas Bank Sampah 8 RT');
$posKategori = strpos($html, 'Daftar Kategori Sampah & Tarif Resmi RW 013');

echo "Posisi Banner: $posBanner\n";
echo "Posisi Ringkasan: $posRingkasan\n";
echo "Posisi Alur Kerja: $posAlur\n";
echo "Posisi Titik Pengumpulan: $posTitik\n";
echo "Posisi Kategori & Tarif: $posKategori\n";

if ($posBanner < $posRingkasan && $posRingkasan < $posAlur && $posAlur < $posTitik && $posTitik < $posKategori) {
    echo "URUTAN TEPAT SESUAI SPESIFIKASI: Banner -> Ringkasan -> Alur Kerja -> Titik Pengumpulan -> Kategori & Tarif!\n";
} else {
    echo "PERINGATAN: Urutan tidak sesuai!\n";
}

// 2. Test as Warga
$warga = User::where('role', 'warga')->first();
Auth::login($warga);
$respW = $controller->index(Request::create('/bank-sampah', 'GET'));
$htmlW = $respW->render();
echo "Render as Warga: OK (" . strlen($htmlW) . " bytes)\n";
