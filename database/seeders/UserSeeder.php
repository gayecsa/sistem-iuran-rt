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

        for ($i = 1; $i <= 100; $i++) {
            $houseNumber = str_pad($i + 2, 3, '0', STR_PAD_LEFT);
            User::updateOrCreate([
                'email' => "warga{$i}@rt001.com"
            ], [
                'name' => $faker->name,
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'rt_number' => '001',
                'house_number' => $houseNumber,
                'phone' => '08' . $faker->numerify('##########'),
                'address' => $faker->streetAddress . ' RT 001',
                'status_rumah' => $statusOptions[array_rand($statusOptions)],
            ]);
        }
    }
}