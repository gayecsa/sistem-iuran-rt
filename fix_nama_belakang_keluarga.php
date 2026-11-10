<?php
// Mengganti nama belakang anggota keluarga agar tidak sama dengan kepala keluarga.
// Jalankan: php fix_nama_belakang_keluarga.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Carbon\Carbon;

$lastNames = ['Pradipta', 'Maheswara', 'Bratadikara', 'Wijaya', 'Dirgantara', 'Adhitama', 'Wiratama', 'Rajendra',
    'Baskara', 'Niscala', 'Pangestu', 'Hadikusuma', 'Mahardika', 'Pramana', 'Setiawan', 'Nugroha', 'Gunawan',
    'Hidayat', 'Santoso', 'Anindita', 'Kusumawati', 'Prameswari', 'Ramadhan', 'Saputra', 'Lestari', 'Permana',
    'Aditama', 'Wardhana', 'Nirmala', 'Puspita', 'Kurniawan', 'Firmansyah', 'Utami', 'Safitri', 'Hakim'];

$changed = 0;
$families = User::whereNotNull('no_kk')->where('no_kk', '!=', '')->get()->groupBy('no_kk');

foreach ($families as $noKk => $members) {
    $members = $members->sortBy('tanggal_lahir')->values();
    $age = fn ($m) => $m->tanggal_lahir ? Carbon::parse($m->tanggal_lahir)->age : 0;
    $head = $members->first(fn ($m) => $m->gender === 'Laki-laki' && $age($m) >= 20 && $age($m) < 60)
        ?? $members->first(fn ($m) => $m->gender === 'Laki-laki' && $age($m) >= 20)
        ?? $members->first();

    $headParts = explode(' ', trim($head->name));
    if (count($headParts) < 2) continue;
    $headLast = end($headParts);

    $used = [$headLast];
    foreach ($members as $m) {
        if ($m->id === $head->id) continue;
        $parts = explode(' ', trim($m->name));
        if (count($parts) < 2 || end($parts) !== $headLast) continue;

        $pool = array_values(array_diff($lastNames, $used));
        $new = $pool[array_rand($pool)];
        $used[] = $new;

        array_pop($parts);
        $m->name = implode(' ', $parts) . ' ' . $new;
        $m->save();
        $changed++;
    }
}

echo "Selesai. $changed nama anggota keluarga diperbarui.\n";
