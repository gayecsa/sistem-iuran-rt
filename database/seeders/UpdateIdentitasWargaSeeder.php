<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateIdentitasWargaSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua data user yang rolenya 'warga'
        $warga = User::where('role', 'warga')->get();

        foreach ($warga as $index => $w) {
            // Generate 16 digit No KK & NIK buatan agar terlihat realistis
            $no_kk = '321601' . str_pad($index + 1, 10, '0', STR_PAD_LEFT);
            $nik = '321601' . rand(10, 28) . '0590' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            
            // Selang-seling gender antara Laki-laki dan Perempuan
            $gender = $index % 2 === 0 ? 'Laki-laki' : 'Perempuan';

            // Update datanya ke database
            $w->update([
                'no_kk' => $no_kk,
                'nik' => $nik,
                'gender' => $gender
            ]);
        }
    }
}