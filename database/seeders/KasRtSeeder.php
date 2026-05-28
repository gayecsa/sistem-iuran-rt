<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KasRt;
use App\Models\User;
use Carbon\Carbon;

class KasRtSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kosongkan tabel kas_rt terlebih dahulu agar data lama terhapus bersih
        KasRt::truncate();

        // 2. Ambil semua data user yang berstatus 'warga'
        $warga = User::where('role', 'warga')->get();

        // Mapping nama bulan Indonesia agar aman dari bug locale sistem
        $list_bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei'
        ];

        // 3. Looping PEMASUKAN: Setiap warga akan dibuatkan riwayat iuran dari Januari-April/Mei
        foreach ($warga as $w) {
            // Kita acak batas bulan bayarnya (ada yang baru bayar sampai Maret, April, atau sudah lunas sampai Mei)
            $bulan_terakhir_bayar = rand(3, 5); 

            for ($bulan = 1; $bulan <= $bulan_terakhir_bayar; $bulan++) {
                KasRt::create([
                    'nama_warga' => $w->name,
                    'no_hp' => $w->phone,
                    'pemasukan' => 100000, // Iuran Rp 100.000 per bulan
                    'pengeluaran' => 0,
                    'kategori' => 'Iuran Bulanan',
                    'keterangan' => 'Iuran Kas Bulan ' . $list_bulan[$bulan],
                    // Tanggal transaksi disesuaikan dengan tahun 2026, bulannya dinamis, tanggalnya diacak 1-28
                    'tanggal_transaksi' => Carbon::create(2026, $bulan, rand(1, 28)),
                    'dibuat_oleh' => 'Sistem Seeder',
                ]);
            }
        }

        // 4. Looping PENGELUARAN: Disebar juga dari bulan Januari sampai Mei agar balance laporan seimbang
        $daftar_pengeluaran = [
            ['kategori' => 'Operasional', 'jumlah' => 200000, 'ket' => 'Membeli alat kebersihan & trash bag', 'bulan' => 1],
            ['kategori' => 'Sosial', 'jumlah' => 500000, 'ket' => 'Menjenguk warga sakit di RS', 'bulan' => 2],
            ['kategori' => 'Lainnya', 'jumlah' => 150000, 'ket' => 'Fotokopi berkas & konsumsi rapat RT', 'bulan' => 3],
            ['kategori' => 'Operasional', 'jumlah' => 350000, 'ket' => 'Perbaikan pompa air warga', 'bulan' => 4],
            ['kategori' => 'Sosial', 'jumlah' => 300000, 'ket' => 'Bantuan konsumsi kerja bakti', 'bulan' => 5],
        ];

        foreach ($daftar_pengeluaran as $peng) {
            KasRt::create([
                'nama_warga' => null, // Tetap null untuk pengeluaran umum
                'no_hp' => null,
                'pemasukan' => 0,
                'pengeluaran' => $peng['jumlah'],
                'kategori' => $peng['kategori'],
                'keterangan' => $peng['ket'],
                'tanggal_transaksi' => Carbon::create(2026, $peng['bulan'], rand(15, 28)),
                'dibuat_oleh' => 'Admin RT 001',
            ]);
        }
    }
}