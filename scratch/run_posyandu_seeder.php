<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

require_once __DIR__ . '/../database/seeders/PosyanduSystemSeeder.php';

$seeder = new Database\Seeders\PosyanduSystemSeeder();
$seeder->run();

echo "SEEDED SUCCESSFULLY!\n";
echo "Total Peserta: " . App\Models\PosyanduPeserta::count() . "\n";
echo "Total Jadwal: " . App\Models\PosyanduJadwal::count() . "\n";
echo "Total Kehadiran: " . App\Models\PosyanduKehadiran::count() . "\n";
echo "Total Pengukuran: " . App\Models\PosyanduPengukuran::count() . "\n";
echo "Total Laporan: " . App\Models\PosyanduLaporan::count() . "\n";
echo "Total Kader: " . App\Models\PosyanduKader::count() . "\n";
