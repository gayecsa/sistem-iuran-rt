<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            WargaTransaksiSeeder::class,
            IuranSeeder::class,
            PengumumanSeeder::class,
            KasRtSeeder::class,
            UmkmSeeder::class,
        ]);
    }
}