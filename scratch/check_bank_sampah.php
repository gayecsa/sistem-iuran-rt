<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BankSampah;
use App\Models\BankSampahKategori;
use App\Models\BankSampahTransaksi;

$units = BankSampah::with('petugas')->orderBy('rt_number')->get();
echo "TOTAL UNITS: " . $units->count() . "\n";
foreach ($units as $u) {
    echo sprintf(
        "RT %s | %s | Petugas: %s (ID %s) | Lokasi: %s | Terkumpul DB: %s kg | Aktual Selesai: %s kg\n",
        $u->rt_number,
        $u->nama_unit,
        $u->petugas ? $u->petugas->name : 'NONE',
        $u->petugas_id,
        $u->lokasi,
        $u->total_terkumpul_kg,
        $u->total_aktual_kg
    );
}

echo "\nTOTAL TRANSAKSI: " . BankSampahTransaksi::count() . "\n";
foreach (BankSampahTransaksi::with('items')->take(5)->get() as $t) {
    echo sprintf("TX #%s | RT %s | Status: %s | Total: %s kg / Rp %s\n", $t->kode_transaksi, $t->rt_number, $t->status, $t->total_berat_kg, $t->total_rupiah);
}
