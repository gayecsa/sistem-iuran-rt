<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PosyanduKegiatan;
use App\Models\User;

$units = PosyanduKegiatan::all();
echo "--- POSYANDU KEGIATAN ROWS ---\n";
foreach ($units as $u) {
    echo "ID: {$u->id} | RT: {$u->rt_number} | Nama: {$u->nama_posyandu} | Lokasi: {$u->lokasi} | Jadwal: {$u->jadwal} | Kader: {$u->kader_utama_id} ({$u->kader_name})\n";
}

echo "\n--- USER ROLES ---\n";
$roles = User::select('role', \Illuminate\Support\Facades\DB::raw('count(*) as count'))->groupBy('role')->get();
foreach ($roles as $r) {
    echo "Role: {$r->role} => {$r->count}\n";
}

echo "\n--- CITIZEN AGE BREAKDOWN (POTENTIAL PARTICIPANTS) ---\n";
$totalWarga = User::where('role', 'warga')->count();
$bayiBalita = User::where('role', 'warga')->whereNotNull('tanggal_lahir')->where('tanggal_lahir', '>=', now()->subYears(5))->count();
$lansia = User::where('role', 'warga')->whereNotNull('tanggal_lahir')->where('tanggal_lahir', '<=', now()->subYears(60))->count();
$wanitaProduktif = User::where('role', 'warga')->where('gender', 'Perempuan')->whereNotNull('tanggal_lahir')->whereBetween('tanggal_lahir', [now()->subYears(49), now()->subYears(18)])->count();

echo "Total Warga: $totalWarga\n";
echo "Balita (0-5 th): $bayiBalita\n";
echo "Lansia (60+ th): $lansia\n";
echo "Wanita Usia Subur (18-49 th): $wanitaProduktif\n";
