<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\KasRt;
use Carbon\Carbon;
use Faker\Factory as Faker;

class WargaTransaksiSeeder extends Seeder
{
    public function run()
    {
        // 1. Pastikan jumlah akun warga di tabel users sudah genap minimal 250 orang
        $existingWargaCount = User::where('role', 'warga')->count();
        $needed = 250 - $existingWargaCount;

        $faker = Faker::create('id_ID');

        if ($needed > 0) {
            for ($i = 1; $i <= $needed; $i++) {
                $nomorUrut = $existingWargaCount + $i;
                $rwNumber = str_pad(rand(1, 5), 3, '0', STR_PAD_LEFT);
                $rtNumber = '001';

                User::create([
                    'name' => 'Warga Sampel ' . $nomorUrut,
                    'email' => 'wargasampel' . $nomorUrut . '@gmail.com',
                    'password' => bcrypt('password123'),
                    'role' => 'warga',
                    'nik' => '32750' . str_pad($nomorUrut, 11, '0', STR_PAD_LEFT),
                    'no_kk' => '32751' . str_pad($nomorUrut, 11, '0', STR_PAD_LEFT),
                    'gender' => $nomorUrut % 2 == 0 ? 'Laki-laki' : 'Perempuan',
                    'rt_number' => $rtNumber,
                    'rw_number' => $rwNumber,
                    'house_number' => str_pad($nomorUrut, 3, '0', STR_PAD_LEFT),
                    'phone' => '0812' . str_pad($nomorUrut, 8, '0', STR_PAD_LEFT),
                    'address' => 'Jl. ' . $faker->streetName . ' No. ' . $nomorUrut . ' RW ' . intval($rwNumber),
                    'status_rumah' => 'milik_sendiri'
                ]);
            }
        }

        // Ambil 250 warga untuk diproses transaksinya
        $wargas = User::where('role', 'warga')->limit(250)->get();

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
                    'dibuat_oleh' => 'Sistem Seeder',
                ]);

                // 2. Transaksi Kebersihan (Rp 35.000)
                KasRt::create([
                    'tanggal_transaksi' => $tanggalBayar,
                    'keterangan' => 'Kebersihan ' . $warga->name . ' - Bulan ' . $bulan,
                    'kategori' => 'Kebersihan',
                    'pemasukan' => 35000, 
                    'pengeluaran' => 0,
                    'dibuat_oleh' => 'Sistem Seeder',
                ]);

                // 3. Transaksi Tabungan (Rp 20.000)
                KasRt::create([
                    'tanggal_transaksi' => $tanggalBayar,
                    'keterangan' => 'Tabungan ' . $warga->name . ' - Bulan ' . $bulan,
                    'kategori' => 'Tabungan',
                    'pemasukan' => 20000, 
                    'pengeluaran' => 0,
                    'dibuat_oleh' => 'Sistem Seeder',
                ]);
            }
        }
    }
}