<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::updateOrCreate([
            'email' => 'admin@rt001.com'
        ], [
            'name' => 'Admin RT 001',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'rt_number' => '001',
            'house_number' => '001',
            'phone' => '081234567890',
            'address' => 'Jl. Sancaka No. 13',
            'status_rumah' => 'milik_sendiri'
        ]);
        
        // Bendahara
        User::updateOrCreate([
            'email' => 'bendahara@rt001.com'
        ], [
            'name' => 'Bendahara RT 001',
            'password' => Hash::make('password123'),
            'role' => 'bendahara',
            'rt_number' => '001',
            'house_number' => '002',
            'phone' => '081234567891',
            'address' => 'Jl. Taksaka No. 2',
            'status_rumah' => 'milik_sendiri'
        ]);
        
        $faker = Faker::create('id_ID');
        $statusOptions = ['milik_sendiri', 'kontrak', 'sewa'];

        // Hapus semua warga lama agar data bisa direfresh sepenuhnya
        User::where('role', 'warga')->delete();

        for ($i = 1; $i <= 250; $i++) {
            $houseNumber = str_pad($i + 2, 3, '0', STR_PAD_LEFT);
            $gender = $i % 2 === 0 ? 'Laki-laki' : 'Perempuan';
            $rwNumber = str_pad(rand(1, 5), 2, '0', STR_PAD_LEFT);
            $rtNumber = '001';
            $email = "warga{$i}@rt001.com";

            User::create([
                'name' => $faker->name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'rt_number' => $rtNumber,
                'rw_number' => $rwNumber,
                'house_number' => $houseNumber,
                'phone' => '0812' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'address' => 'Jl. ' . $faker->streetName . ' No. ' . $houseNumber . ' RT ' . intval($rtNumber) . ' / RW ' . intval($rwNumber),
                'status_rumah' => $statusOptions[array_rand($statusOptions)],
                'nik' => '32750' . str_pad($i, 11, '0', STR_PAD_LEFT),
                'no_kk' => '32751' . str_pad($i, 11, '0', STR_PAD_LEFT),
                'gender' => $gender,
            ]);
        }
    }
}