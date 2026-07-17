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
                'nama_umkm' => 'Nasi Kuning Ibu Siti',
                'jenis_usaha' => 'Makanan Tradisional',
                'nama_pemilik' => 'Siti Aminah',
                'no_hp' => '082123456789',
                'alamat' => 'Jl. Sancaka No. 1',
                'latitude' => '-6.2748',
                'longitude' => '106.8154',
                'deskripsi' => 'Nasi kuning dengan lauk pauk lengkap, harga terjangkau',
                'jam_buka' => '06:00',
                'jam_tutup' => '15:00'
            ],
            [
                'nama_umkm' => 'Warung Kopi Pak Hendra',
                'jenis_usaha' => 'Minuman & Makanan Ringan',
                'nama_pemilik' => 'Hendra',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Taksaka No. 5',
                'latitude' => '-6.2750',
                'longitude' => '106.8155',
                'deskripsi' => 'Kopi enak dengan berbagai pilihan menu pagi',
                'jam_buka' => '05:00',
                'jam_tutup' => '12:00'
            ],
            [
                'nama_umkm' => 'Toko Roti Sinar Bakery',
                'jenis_usaha' => 'Kue & Roti',
                'nama_pemilik' => 'Ibu Rini',
                'no_hp' => '082345678901',
                'alamat' => 'Jl. Sancaka No. 8',
                'latitude' => '-6.2751',
                'longitude' => '106.8153',
                'deskripsi' => 'Roti segar setiap hari, berbagai varian kue',
                'jam_buka' => '06:00',
                'jam_tutup' => '18:00'
            ],
            [
                'nama_umkm' => 'Lalapan & Seafood Bu Mira',
                'jenis_usaha' => 'Warung Makan',
                'nama_pemilik' => 'Mira',
                'no_hp' => '083456789012',
                'alamat' => 'Jl. Taksaka No. 15',
                'latitude' => '-6.2752',
                'longitude' => '106.8156',
                'deskripsi' => 'Spesialis lalapan dan seafood dengan porsi besar',
                'jam_buka' => '10:00',
                'jam_tutup' => '21:00'
            ],
            [
                'nama_umkm' => 'Mie Ayam Om Budi',
                'jenis_usaha' => 'Makanan Jalan',
                'nama_pemilik' => 'Budi',
                'no_hp' => '081567890123',
                'alamat' => 'Jl. Sancaka No. 20',
                'latitude' => '-6.2749',
                'longitude' => '106.8154',
                'deskripsi' => 'Mie ayam jamur dan bakso dengan kuah nikmat',
                'jam_buka' => '10:00',
                'jam_tutup' => '20:00'
            ],
            [
                'nama_umkm' => 'Jus Segar Toko Sehat',
                'jenis_usaha' => 'Minuman Sehat',
                'nama_pemilik' => 'Eka',
                'no_hp' => '082678901234',
                'alamat' => 'Jl. Taksaka No. 25',
                'latitude' => '-6.2753',
                'longitude' => '106.8157',
                'deskripsi' => 'Jus buah segar dan smoothie bowl yang sehat',
                'jam_buka' => '08:00',
                'jam_tutup' => '19:00'
            ],
            [
                'nama_umkm' => 'Soto Ayam Nyonya Lis',
                'jenis_usaha' => 'Makanan Tradisional',
                'nama_pemilik' => 'Lis',
                'no_hp' => '083789012345',
                'alamat' => 'Jl. Sancaka No. 30',
                'latitude' => '-6.2747',
                'longitude' => '106.8152',
                'deskripsi' => 'Soto ayam kental dengan resep turun temurun',
                'jam_buka' => '07:00',
                'jam_tutup' => '16:00'
            ],
            [
                'nama_umkm' => 'Satay & Grilled Meat Joni',
                'jenis_usaha' => 'Warung Makan',
                'nama_pemilik' => 'Joni',
                'no_hp' => '081890123456',
                'alamat' => 'Jl. Taksaka No. 35',
                'latitude' => '-6.2754',
                'longitude' => '106.8158',
                'deskripsi' => 'Sate ayam dan daging bakar premium dengan bumbu rahasia',
                'jam_buka' => '15:00',
                'jam_tutup' => '23:00'
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
