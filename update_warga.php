<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;

$faker = Faker::create('id_ID');

// Aesthetic/modern Indonesian names (first names + last names) tren 2024-2025
$firstNamesLaki = ['Elvano', 'Arshakal', 'Raffasya', 'Atharrazka', 'Xabier', 'Kael', 'Kenzo', 'Alvaro', 'Zayyan', 'Keenan', 'Bumi', 'Sagara', 'Langit', 'Dirgantara', 'Gavin', 'Kenzie', 'Devan', 'Abrisam', 'Abqary', 'Arkatama'];
$firstNamesPerempuan = ['Nafeeza', 'Shanaya', 'Eleasha', 'Freya', 'Ameena', 'Zeina', 'Kanaya', 'Mishal', 'Almaira', 'Inara', 'Zahwa', 'Alesha', 'Queensha', 'Ayana', 'Mikaela', 'Senja', 'Keisha', 'Ayla', 'Nadhifa', 'Safira'];
$lastNames = ['Pradipta', 'Maheswara', 'Bratadikara', 'Wijaya', 'Dirgantara', 'Adhitama', 'Wiratama', 'Rajendra', 'Baskara', 'Niscala', 'Sastrowardoyo', 'Pangestu', 'Hadikusuma', 'Mahardika', 'Pramana'];

$wargas = User::where('role', 'warga')->get();

foreach ($wargas as $warga) {
    $isMale = $warga->gender === 'Laki-laki';
    
    // Pick random names
    if ($isMale) {
        $firstName = $firstNamesLaki[array_rand($firstNamesLaki)];
    } else {
        $firstName = $firstNamesPerempuan[array_rand($firstNamesPerempuan)];
    }
    $lastName = $lastNames[array_rand($lastNames)];
    
    $aestheticName = $firstName . ' ' . $lastName;
    
    // Generate random birth date. 
    // Let's make some of them toddlers/babies (0-5 years old) to ensure data shows up in Posyandu.
    // 20% chance to be a toddler
    if (rand(1, 100) <= 20) {
        $tanggal_lahir = Carbon::now()->subDays(rand(10, 365 * 5))->format('Y-m-d');
    } else {
        // Adults (18-60 years old)
        $tanggal_lahir = Carbon::now()->subYears(rand(18, 60))->subDays(rand(1, 365))->format('Y-m-d');
    }
    
    $warga->name = $aestheticName;
    $warga->tanggal_lahir = $tanggal_lahir;
    $warga->save();
}

echo "Berhasil memperbarui " . $wargas->count() . " warga dengan nama aesthetic dan tanggal lahir.\n";
