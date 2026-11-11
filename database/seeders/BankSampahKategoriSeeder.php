<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BankSampahKategori;
use App\Models\BankSampahTarifHistory;
use App\Models\User;

class BankSampahKategoriSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereIn('role', ['admin', 'rw', 'super_admin'])->first();
        $adminId = $admin ? $admin->id : null;

        $kategoris = [
            [
                'nama_kategori' => 'Plastik PET',
                'slug' => 'plastik-pet',
                'satuan' => 'kg',
                'harga_per_satuan' => 3500,
                'deskripsi' => 'Botol air mineral bening, botol jus, botol soda kemasan sekali pakai.',
                'petunjuk_pemilahan' => 'Lepaskan label plastik dan tutup botol, bilas hingga bersih, lalu kempeskan botol.',
                'badge_color' => 'emerald',
                'icon' => 'recycle',
                'urutan' => 1,
            ],
            [
                'nama_kategori' => 'Plastik Campuran',
                'slug' => 'plastik-campuran',
                'satuan' => 'kg',
                'harga_per_satuan' => 1500,
                'deskripsi' => 'Kantong kresek, botol sabun, jerigen bekas, wadah makanan plastik tebal.',
                'petunjuk_pemilahan' => 'Pastikan bebas dari sisa cairan berminyak atau bahan kimia berbahaya.',
                'badge_color' => 'teal',
                'icon' => 'package',
                'urutan' => 2,
            ],
            [
                'nama_kategori' => 'Kertas',
                'slug' => 'kertas',
                'satuan' => 'kg',
                'harga_per_satuan' => 2500,
                'deskripsi' => 'Kertas HVS, buku tulis bekas, lembar fotokopi, koran, dan dokumen arsip.',
                'petunjuk_pemilahan' => 'Pastikan kering dan tidak terkena air, satukan dalam ikatan rapi.',
                'badge_color' => 'blue',
                'icon' => 'file-text',
                'urutan' => 3,
            ],
            [
                'nama_kategori' => 'Kardus',
                'slug' => 'kardus',
                'satuan' => 'kg',
                'harga_per_satuan' => 2200,
                'deskripsi' => 'Kardus box gelombang cokelat, karton kemasan barang elektronik/makanan.',
                'petunjuk_pemilahan' => 'Bongkar dan lipat pipih kardus, ikat kuat dengan tali rafia.',
                'badge_color' => 'amber',
                'icon' => 'box',
                'urutan' => 4,
            ],
            [
                'nama_kategori' => 'Botol Kaca',
                'slug' => 'botol-kaca',
                'satuan' => 'kg',
                'harga_per_satuan' => 800,
                'deskripsi' => 'Botol kecap, botol sirup, toples selai kaca, botol minuman kaca bening & gelap.',
                'petunjuk_pemilahan' => 'Cuci bersih, keringkan, dan pastikan tidak retak atau pecah berhamburan.',
                'badge_color' => 'cyan',
                'icon' => 'glass-water',
                'urutan' => 5,
            ],
            [
                'nama_kategori' => 'Besi',
                'slug' => 'besi',
                'satuan' => 'kg',
                'harga_per_satuan' => 4500,
                'deskripsi' => 'Besi padu, pipa besi, plat seng, paku, rongsokan pagar atau teralis.',
                'petunjuk_pemilahan' => 'Pisahkan dari semen atau plastik yang menempel.',
                'badge_color' => 'slate',
                'icon' => 'hammer',
                'urutan' => 6,
            ],
            [
                'nama_kategori' => 'Aluminium',
                'slug' => 'aluminium',
                'satuan' => 'kg',
                'harga_per_satuan' => 14000,
                'deskripsi' => 'Kaleng minuman bersoda, kusen aluminium, wajan bekas aluminium.',
                'petunjuk_pemilahan' => 'Kempeskan kaleng minuman, bersihkan dari endapan gula atau kotoran.',
                'badge_color' => 'indigo',
                'icon' => 'disc',
                'urutan' => 7,
            ],
            [
                'nama_kategori' => 'Logam Lainnya',
                'slug' => 'logam-lainnya',
                'satuan' => 'kg',
                'harga_per_satuan' => 25000,
                'deskripsi' => 'Kabel tembaga, kuningan keran air, timah, perunggu rongsok.',
                'petunjuk_pemilahan' => 'Kupas kulit kabel tembaga jika memungkinkan untuk mendapatkan nilai optimal.',
                'badge_color' => 'violet',
                'icon' => 'cpu',
                'urutan' => 8,
            ],
            [
                'nama_kategori' => 'Minyak Jelantah',
                'slug' => 'minyak-jelantah',
                'satuan' => 'liter',
                'harga_per_satuan' => 6500,
                'deskripsi' => 'Minyak goreng bekas pakai rumah tangga yang telah disaring dari remah makanan.',
                'petunjuk_pemilahan' => 'Saring remahan kotoran, tuangkan ke dalam jerigen atau botol plastik bertutup rapat.',
                'badge_color' => 'orange',
                'icon' => 'droplet',
                'urutan' => 9,
            ],
        ];

        foreach ($kategoris as $k) {
            $kat = BankSampahKategori::updateOrCreate(
                ['slug' => $k['slug']],
                $k
            );

            // Buat history tarif awal jika belum ada
            if ($kat->tarifHistories()->count() === 0) {
                BankSampahTarifHistory::create([
                    'kategori_id' => $kat->id,
                    'harga_lama' => 0,
                    'harga_baru' => $kat->harga_per_satuan,
                    'diubah_oleh_id' => $adminId,
                    'alasan_perubahan' => 'Penetapan tarif dasar resmi Bank Sampah Terpadu RW 013 Sukamiskin Arcamanik.',
                ]);
            }
        }
    }
}
