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
                $nomorUrut = $existingWargaCount + $i;
                
                User::create([
                    'name' => 'Warga Sampel ' . $nomorUrut,
                    'email' => 'wargasampel' . $nomorUrut . '@gmail.com',
                    'password' => bcrypt('password123'),
                    'role' => 'warga',
                    // TAMBAHAN DATA DUMMY YANG HILANG:
                    'nik' => '327501' . str_pad($nomorUrut, 10, '0', STR_PAD_LEFT), // Generates 16 digit NIK
                    'no_kk' => '327502' . str_pad($nomorUrut, 10, '0', STR_PAD_LEFT), // Generates 16 digit KK
                    'jenis_kelamin' => $nomorUrut % 2 == 0 ? 'Laki-laki' : 'Perempuan', // Selang-seling (Sesuaikan dengan enum database-mu: L/P atau Laki-laki/Perempuan)
                    'rt_number' => '001', // Disamakan dengan struktur UserSeeder kamu kemarin
                    'house_number' => str_pad($nomorUrut, 3, '0', STR_PAD_LEFT),
                    'phone' => '081234567' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT),
                    'address' => 'Jl. Gandaria No. ' . $nomorUrut,
                    'status_rumah' => 'milik_sendiri'
                ]);
            }
        }

        // Ambil 100 warga untuk diproses transaksinya
        $wargas = User::where('role', 'warga')->limit(100)->get();

        foreach ($wargas as $warga) {
            
            // Looping dari bulan 1 (Januari) sampai 5 (Mei) untuk tahun 2026
            for ($bulan = 1; $bulan <= 5; $bulan++) {
                
                // Bikin tanggal bayar acak antara tanggal 1 sampai 5 di setiap bulannya
                $tanggalBayar = Carbon::create(2026, $bulan, rand(1, 5))->format('Y-m-d');

                // 1. Transaksi Kas (Rp 100.000)
                KasRt::create([
                    'tanggal_transaksi' => $tanggalBayar,
                    'keterangan' => 'Kas ' . $warga->name . ' - Bulan ' . $bulan,
                    'kategori' => 'Kas',
                    'pemasukan' => 100000, 
                    'pengeluaran' => 0,
                ]);

                // 2. Transaksi Kebersihan (Rp 35.000)
                KasRt::create([
                    'tanggal_transaksi' => $tanggalBayar,
                    'keterangan' => 'Kebersihan ' . $warga->name . ' - Bulan ' . $bulan,
                    'kategori' => 'Kebersihan',
                    'pemasukan' => 35000, 
                    'pengeluaran' => 0,
                ]);

                // 3. Transaksi Tabungan (Rp 20.000)
                KasRt::create([
                    'tanggal_transaksi' => $tanggalBayar,
                    'keterangan' => 'Tabungan ' . $warga->name . ' - Bulan ' . $bulan,
                    'kategori' => 'Tabungan',
                    'pemasukan' => 20000, 
                    'pengeluaran' => 0,
                ]);
            }
        }
    }
}