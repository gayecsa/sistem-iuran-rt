<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            WargaTransaksiSeeder::class, // <-- Kita tambahkan ini di sini biar otomatis ikut diproses
            IuranSeeder::class,
            PengumumanSeeder::class,
        ]);
    }
}