<?php

namespace Database\Seeders;

use App\Models\Umkm;
use Illuminate\Database\Seeder;

class UmkmSeeder extends Seeder
{
    public function run()
    {
        $umkmData = [
            [
                'nama_umkm' => 'Nasi Kuning & Lontong Sayur Bu Siti',
                'jenis_usaha' => 'Makanan Tradisional',
                'nama_pemilik' => 'Siti Aminah',
                'no_hp' => '082123456789',
                'alamat' => 'Jl. Gandaria No. 1 (RT 001 / RW 013)',
                'latitude' => '-6.229800',
                'longitude' => '106.816500',
                'deskripsi' => "DAFTAR MENU SPESIAL:\nNasi Kuning Komplit Telur & Rendang Rp 22.000\nNasi Kuning Ayam Suwir Kremes Rp 18.000\nLontong Sayur Telur Balado Rp 15.000\nNasi Uduk Komplit Rp 16.000\nMINUMAN:\nEs Teh Manis Jumbo Rp 5.000\nEs Jeruk Peras Rp 7.000\nTeh Hangat Manis Rp 4.000",
                'jam_buka' => '06:00',
                'jam_tutup' => '14:00'
            ],
            [
                'nama_umkm' => 'Bakso & Mie Ayam Barokah Mas Budi',
                'jenis_usaha' => 'Makanan Utama',
                'nama_pemilik' => 'Budi Prasetyo',
                'no_hp' => '081567890123',
                'alamat' => 'Jl. Kenanga No. 5 (RT 002 / RW 013)',
                'latitude' => '-6.230200',
                'longitude' => '106.817300',
                'deskripsi' => "PAKET BAKSO & MIE AYAM:\nBakso Urat Spesial Jumbo Rp 20.000\nBakso Telur Daging Sapi Rp 18.000\nMie Ayam Bakso Urat Rp 18.000\nMie Ayam Jamur Spesial Rp 15.000\nMINUMAN:\nEs Teller Barokah Rp 12.000\nEs Teh Manis Rp 4.000\nEs Jeruk Murni Rp 6.000",
                'jam_buka' => '10:00',
                'jam_tutup' => '21:00'
            ],
            [
                'nama_umkm' => 'Kopi Machi & Camilan Kekinian',
                'jenis_usaha' => 'Minuman & Cafe',
                'nama_pemilik' => 'Hendra Syahputra',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Anggrek No. 12 (RT 003 / RW 013)',
                'latitude' => '-6.231300',
                'longitude' => '106.818200',
                'deskripsi' => "MENU KOPI & DRINKS:\nKopi Susu Gula Aren Machi Rp 15.000\nAmericano Cold Brew Rp 12.000\nMatcha Latte Creamy Rp 18.000\nChocolate Ice Creamy Rp 16.000\nCAMILAN SNACK:\nRoti Bakar Cokelat Keju Rp 14.000\nFrench Fries Crispy Rp 12.000",
                'jam_buka' => '08:00',
                'jam_tutup' => '22:00'
            ],
            [
                'nama_umkm' => 'Lalapan & Seafood Bu Mira',
                'jenis_usaha' => 'Warung Makan',
                'nama_pemilik' => 'Mira Rahmawati',
                'no_hp' => '083456789012',
                'alamat' => 'Jl. Mawar No. 15 (RT 004 / RW 013)',
                'latitude' => '-6.232100',
                'longitude' => '106.819100',
                'deskripsi' => "PAKET LALAPAN SEAFOOD:\nPaket Ayam Bakar Madu + Nasi Rp 24.000\nPaket Lele Goreng Kremes + Nasi Rp 18.000\nUdang Goreng Tepung Crispy Rp 28.000\nCumi Bakar Bumbu Pedas Rp 30.000\nSoto Ayam Lamongan Rp 16.000\nMINUMAN:\nEs Jeruk Peras Segar Rp 6.000\nEs Kelapa Muda Murni Rp 10.000",
                'jam_buka' => '10:00',
                'jam_tutup' => '22:00'
            ],
            [
                'nama_umkm' => 'Jus Segar & Smoothie Toko Sehat',
                'jenis_usaha' => 'Minuman Sehat',
                'nama_pemilik' => 'Eka Pertiwi',
                'no_hp' => '082678901234',
                'alamat' => 'Jl. Melati No. 25 (RT 005 / RW 013)',
                'latitude' => '-6.232900',
                'longitude' => '106.819900',
                'deskripsi' => "MENU JUS BUAH SEGAR:\nJus Alpukat Kocok Dancow Rp 15.000\nJus Buah Naga Murni Rp 12.000\nJus Mangga Harum Manis Rp 12.000\nSmoothie Bowl Tropical Fruit Rp 22.000\nJus Sirsak Segar Rp 12.000",
                'jam_buka' => '08:00',
                'jam_tutup' => '20:00'
            ],
            [
                'nama_umkm' => 'Toko Roti & Kue Sinar Bakery',
                'jenis_usaha' => 'Kue & Roti',
                'nama_pemilik' => 'Ibu Rini Handayani',
                'no_hp' => '082345678901',
                'alamat' => 'Jl. Flamboyan No. 8 (RT 006 / RW 013)',
                'latitude' => '-6.233600',
                'longitude' => '106.820700',
                'deskripsi' => "VARIAN ROTI & KUE:\nRoti Sobek Cokelat Keju Rp 16.000\nCroissant Butter Premium Rp 14.000\nKue Risoles Ragout Ayam (5 pcs) Rp 15.000\nDonat Gula Halus (6 pcs) Rp 18.000\nKue Bolu Kukus Panda Rp 25.000",
                'jam_buka' => '06:30',
                'jam_tutup' => '19:30'
            ],
            [
                'nama_umkm' => 'Soto Ayam & Bebek Goreng Nyonya Lis',
                'jenis_usaha' => 'Makanan Tradisional',
                'nama_pemilik' => 'Lisdawati',
                'no_hp' => '083789012345',
                'alamat' => 'Jl. Dahlia No. 30 (RT 007 / RW 013)',
                'latitude' => '-6.234300',
                'longitude' => '106.821500',
                'deskripsi' => "MENU KULINER TRADISIONAL:\nSoto Ayam Kudus Nasi Pisah Rp 20.000\nBebek Goreng Bumbu Hitam + Nasi Rp 32.000\nAyam Kampung Bakar Solo Rp 28.000\nKerupuk Kaleng Rp 2.000\nTeh Manis Hangat Rp 4.000",
                'jam_buka' => '07:00',
                'jam_tutup' => '17:00'
            ],
            [
                'nama_umkm' => 'Laundry Express Kilat RW 013',
                'jenis_usaha' => 'Jasa Laundry',
                'nama_pemilik' => 'Joni Wibowo',
                'no_hp' => '081890123456',
                'alamat' => 'Jl. Cempaka No. 35 (RT 008 / RW 013)',
                'latitude' => '-6.235100',
                'longitude' => '106.822300',
                'deskripsi' => "LAYANAN LAUNDRY & CUCI:\nCuci Setrika Reguler (Kg) Rp 7.000\nCuci Setrika Express 6 Jam (Kg) Rp 12.000\nCuci Selimut / Bedcover Jumbo Rp 25.000\nCuci Sepatu Sneaker Rp 30.000",
                'jam_buka' => '07:00',
                'jam_tutup' => '21:00'
            ]
        ];

        foreach ($umkmData as $umkm) {
            Umkm::updateOrCreate(
                ['nama_umkm' => $umkm['nama_umkm']],
                $umkm
            );
        }
    }
}
