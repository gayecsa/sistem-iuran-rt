<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

$faker = Faker::create('id_ID');

// Delete existing warga to restructure
User::where('role', 'warga')->delete();

// We need 250+ unique first names
$baseMaleNames = ['Elvano', 'Arshakal', 'Raffasya', 'Atharrazka', 'Xabier', 'Kael', 'Kenzo', 'Alvaro', 'Zayyan', 'Keenan', 'Bumi', 'Sagara', 'Langit', 'Dirgantara', 'Gavin', 'Kenzie', 'Devan', 'Abrisam', 'Abqary', 'Arkatama', 'Gibran', 'Zayn', 'Raffi', 'Rasya', 'Bastian', 'Arya', 'Fathan', 'Aditya', 'Rizky', 'Bima'];
$baseFemaleNames = ['Nafeeza', 'Shanaya', 'Eleasha', 'Freya', 'Ameena', 'Zeina', 'Kanaya', 'Mishal', 'Almaira', 'Inara', 'Zahwa', 'Alesha', 'Queensha', 'Ayana', 'Mikaela', 'Senja', 'Keisha', 'Ayla', 'Nadhifa', 'Safira', 'Aurelia', 'Kiara', 'Nabila', 'Syifa', 'Zahra', 'Aira', 'Kayla', 'Clarissa', 'Nadhira', 'Kirana'];

$suffixes = ['', 'Syah', 'Putra', 'Adhi', 'Dharma', 'Mahendra', 'Kusuma', 'Dewi', 'Putri', 'Sari', 'Ayu', 'Lestari', 'Nisa', 'Aulia', 'Wijaya', 'Pratama'];

$uniqueFirstNamesMale = [];
$uniqueFirstNamesFemale = [];

// Generate combinations until we have enough
foreach ($baseMaleNames as $name) {
    foreach (['', 'Jr', 'El', 'Al', 'Ar', 'Ibnu', 'Raka'] as $prefix) {
        $n = trim($prefix . ' ' . $name);
        if (!in_array($n, $uniqueFirstNamesMale)) $uniqueFirstNamesMale[] = $n;
    }
}
foreach ($baseFemaleNames as $name) {
    foreach (['', 'Siti', 'Nur', 'Cut', 'Nyai', 'Dian', 'Rara'] as $prefix) {
        $n = trim($prefix . ' ' . $name);
        if (!in_array($n, $uniqueFirstNamesFemale)) $uniqueFirstNamesFemale[] = $n;
    }
}

shuffle($uniqueFirstNamesMale);
shuffle($uniqueFirstNamesFemale);

$lastNames = ['Pradipta', 'Maheswara', 'Bratadikara', 'Wijaya', 'Dirgantara', 'Adhitama', 'Wiratama', 'Rajendra', 'Baskara', 'Niscala', 'Sastrowardoyo', 'Pangestu', 'Hadikusuma', 'Mahardika', 'Pramana', 'Setiawan', 'Nugroha', 'Gunawan', 'Hidayat', 'Santoso'];

$totalFamilies = 65; 
$usedEmails = [];
$userCount = 0;

for ($i = 1; $i <= $totalFamilies; $i++) {
    $familySize = rand(4, 6); // Minimal 4 members per family
    $noKk = '32751' . str_pad($i, 11, '0', STR_PAD_LEFT);
    $houseNumber = str_pad($i + 2, 3, '0', STR_PAD_LEFT);
    $rwNumber = '013';
    $rtNumber = str_pad(rand(1, 8), 3, '0', STR_PAD_LEFT);
    $address = 'Jl. ' . $faker->streetName . ' No. ' . $houseNumber . ' RT ' . intval($rtNumber) . ' / RW ' . intval($rwNumber);
    $statusRumah = ['milik_sendiri', 'kontrak', 'sewa'][array_rand(['milik_sendiri', 'kontrak', 'sewa'])];
    $lastName = $lastNames[array_rand($lastNames)]; // Family last name
    
    // Member 1: Ayah
    $ayahAge = rand(30, 55);
    $ayahName = array_pop($uniqueFirstNamesMale) . ' ' . $lastName;
    createUser($ayahName, 'Laki-laki', $ayahAge, clone $faker, $noKk, $address, $statusRumah, $rtNumber, $rwNumber, $houseNumber, $userCount, $usedEmails);
    
    // Member 2: Ibu
    $ibuAge = rand(28, 50);
    $ibuName = array_pop($uniqueFirstNamesFemale) . ' ' . $lastName;
    createUser($ibuName, 'Perempuan', $ibuAge, clone $faker, $noKk, $address, $statusRumah, $rtNumber, $rwNumber, $houseNumber, $userCount, $usedEmails);
    
    // Members 3 to N: Anak / Kakek Nenek
    for ($j = 3; $j <= $familySize; $j++) {
        $isMale = (rand(1, 100) > 50);
        $firstName = $isMale ? array_pop($uniqueFirstNamesMale) : array_pop($uniqueFirstNamesFemale);
        $memberName = $firstName . ' ' . $lastName;
        
        // Ensure at least some balita
        if ($j === 3 && rand(1, 100) <= 40) { // 40% chance the 3rd member is a balita
            $ageInDays = rand(10, 365 * 5); // 0 to 5 years old
            $birthDate = Carbon::now()->subDays($ageInDays)->format('Y-m-d');
        } else if ($j === 4 && rand(1, 100) <= 20) { // Older relative (Kakek/Nenek)
            $ageInYears = rand(60, 80);
            $birthDate = Carbon::now()->subYears($ageInYears)->subDays(rand(1, 365))->format('Y-m-d');
        } else {
            // Older children or teenagers
            $ageInYears = rand(6, 25);
            $birthDate = Carbon::now()->subYears($ageInYears)->subDays(rand(1, 365))->format('Y-m-d');
        }
        
        createUser($memberName, $isMale ? 'Laki-laki' : 'Perempuan', null, clone $faker, $noKk, $address, $statusRumah, $rtNumber, $rwNumber, $houseNumber, $userCount, $usedEmails, $birthDate);
    }
}

echo "Berhasil membuat $totalFamilies keluarga dengan total $userCount warga (Semua nama depan unik, minimal 4 orang per keluarga).\n";

function createUser($name, $gender, $ageYears, $faker, $noKk, $address, $statusRumah, $rtNumber, $rwNumber, $houseNumber, &$userCount, &$usedEmails, $exactBirthDate = null) {
    $userCount++;
    
    $email = "warga{$userCount}@rt001.com";
    while (in_array($email, $usedEmails)) {
        $email = "warga" . rand(1000, 9999) . "@rt001.com";
    }
    $usedEmails[] = $email;
    
    if ($exactBirthDate) {
        $tanggal_lahir = $exactBirthDate;
    } else {
        $tanggal_lahir = Carbon::now()->subYears($ageYears)->subDays(rand(1, 365))->format('Y-m-d');
    }
    
    User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make('password123'),  
        'role' => 'warga',
        'rt_number' => $rtNumber,
        'rw_number' => $rwNumber,
        'house_number' => $houseNumber,
        'phone' => '0812' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT),
        'address' => $address,
        'status_rumah' => $statusRumah,
        'nik' => '32750' . str_pad($userCount, 11, '0', STR_PAD_LEFT),
        'no_kk' => $noKk,
        'gender' => $gender,
        'tanggal_lahir' => $tanggal_lahir
    ]);
}
