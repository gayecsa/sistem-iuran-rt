<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\KasRt;
use Carbon\Carbon;

class WargaTransaksiSeeder extends Seeder
{
    public function run()
    {
        // 1. Pastikan jumlah akun warga di tabel users sudah genap minimal 100 orang
        $existingWargaCount = User::where('role', 'warga')->count();
        $needed = 100 - $existingWargaCount;

        if ($needed > 0) {
            for ($i = 1; $i <= $needed; $i++) {
                User::create([
                    'name' => 'Warga Sampel ' . ($existingWargaCount + $i),
                    'email' => 'wargasampel' . ($existingWargaCount + $i) . '@gmail.com',
                    'password' => bcrypt('password123'),
                    'role' => 'warga',
                    'rt' => '001'
                ]);
            }
        }

        // Ambil 100 warga untuk diproses transaksinya
        $wargas = User::where('role', 'warga')->limit(100)->get();

        foreach ($wargas as $index => $warga) {
            
            // A. SEMUA 100 warga melakukan transaksi Kas Bulanan (Rp 100.000)
            KasRt::create([
                'tanggal_transaksi' => Carbon::now()->format('Y-m-d'), // FIX: Mengisi kolom tanggal_transaksi
                'keterangan' => $warga->name,
                'kategori' => 'Iuran Bulanan',
                'pemasukan' => 100000, 
                'pengeluaran' => 0,
            ]);

            // B. 50 dari 100 warga melakukan transaksi Uang Sampah (Rp 45.000)
            if ($index < 50) {
                KasRt::create([
                    'tanggal_transaksi' => Carbon::now()->format('Y-m-d'), // FIX: Mengisi kolom tanggal_transaksi
                    'keterangan' => $warga->name,
                    'kategori' => 'Uang Sampah',
                    'pemasukan' => 45000, 
                    'pengeluaran' => 0,
                ]);
            }

            // C. 25 dari 100 warga melakukan transaksi Jenguk Orang Sakit (Rp 25.000)
            if ($index < 25) {
                KasRt::create([
                    'tanggal_transaksi' => Carbon::now()->format('Y-m-d'), // FIX: Mengisi kolom tanggal_transaksi
                    'keterangan' => $warga->name,
                    'kategori' => 'Jenguk Orang Sakit',
                    'pemasukan' => 25000, 
                    'pengeluaran' => 0,
                ]);
            }
        }
    }
}