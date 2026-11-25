<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\KartuKeluarga;
use App\Models\User;

class BalanceRtFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds to balance all 8 RTs to exactly 10 KK per RT (Total 80 KK).
     */
    public function run(): void
    {
        $newFamilies = [
            // ==================== RT 002 (6 KK Baru: KK 5 s.d. 10) ====================
            [
                'rt' => '2',
                'rt_number' => '002',
                'no_kk' => '3273102401260001',
                'alamat' => 'Jl. Arcamanik Endah No. 21, RT 002 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'A2-21',
                'status_verifikasi_dukcapil' => 'Terverifikasi',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Bambang Gunawan', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1984-05-14', 'pekerjaan' => 'Karyawan Swasta'],
                    ['name' => 'Sri Wahyuni', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1987-08-20', 'pekerjaan' => 'Ibu Rumah Tangga'],
                    ['name' => 'Dimas Pratama Gunawan', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '2010-03-12', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                    ['name' => 'Alifia Putri Gunawan', 'gender' => 'Perempuan', 'hub' => 'Anak', 'tgl_lahir' => '2023-08-10', 'pekerjaan' => 'Belum Bekerja'], // Balita (~3 thn)
                ],
            ],
            [
                'rt' => '2',
                'rt_number' => '002',
                'no_kk' => '3273102401260002',
                'alamat' => 'Jl. Arcamanik Endah No. 24, RT 002 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'A2-24',
                'status_verifikasi_dukcapil' => 'Menunggu Verifikasi',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Rachmat Hidayat', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1988-11-05', 'pekerjaan' => 'Wiraswasta'],
                    ['name' => 'Dewi Sartika', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1990-04-18', 'pekerjaan' => 'Guru'],
                    ['name' => 'Rizky Hidayat', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '2016-09-22', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                    ['name' => 'Kenzo Hidayat', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '2024-05-15', 'pekerjaan' => 'Belum Bekerja'], // Balita (~2 thn)
                ],
            ],
            [
                'rt' => '2',
                'rt_number' => '002',
                'no_kk' => '3273102401260003',
                'alamat' => 'Jl. Arcamanik Endah No. 28, RT 002 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'A2-28',
                'status_verifikasi_dukcapil' => 'Belum Ada Dokumen',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Hadi Sutrisno', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1960-02-17', 'pekerjaan' => 'Pensiunan'], // Lansia (66 thn)
                    ['name' => 'Sumarni Sutrisno', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1963-06-25', 'pekerjaan' => 'Ibu Rumah Tangga'], // Lansia (63 thn)
                    ['name' => 'Agus Sutrisno', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '1992-01-30', 'pekerjaan' => 'Karyawan Swasta'],
                    ['name' => 'Lestari Utami', 'gender' => 'Perempuan', 'hub' => 'Menantu', 'tgl_lahir' => '1995-10-14', 'pekerjaan' => 'Tenaga Medis'],
                ],
            ],
            [
                'rt' => '2',
                'rt_number' => '002',
                'no_kk' => '3273102401260004',
                'alamat' => 'Jl. Arcamanik Endah No. 32, RT 002 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'A2-32',
                'status_verifikasi_dukcapil' => 'Terverifikasi',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Eko Prasetyo', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1991-07-22', 'pekerjaan' => 'Arsitek'],
                    ['name' => 'Nadia Anggraini', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1993-12-09', 'pekerjaan' => 'Desainer Grafis'],
                    ['name' => 'Aditya Prasetyo', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '2019-04-16', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                ],
            ],
            [
                'rt' => '2',
                'rt_number' => '002',
                'no_kk' => '3273102401260005',
                'alamat' => 'Jl. Arcamanik Endah No. 35, RT 002 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'A2-35',
                'status_verifikasi_dukcapil' => 'Menunggu Verifikasi',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Indra Kusuma', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1981-09-03', 'pekerjaan' => 'PNS'],
                    ['name' => 'Nuraini Kusuma', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1984-03-19', 'pekerjaan' => 'PNS'],
                    ['name' => 'Fajar Kusuma', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '2008-08-11', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                    ['name' => 'Salma Kusuma', 'gender' => 'Perempuan', 'hub' => 'Anak', 'tgl_lahir' => '2013-05-27', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                ],
            ],
            [
                'rt' => '2',
                'rt_number' => '002',
                'no_kk' => '3273102401260006',
                'alamat' => 'Jl. Arcamanik Endah No. 38, RT 002 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'A2-38',
                'status_verifikasi_dukcapil' => 'Belum Ada Dokumen',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Budi Santoso', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1986-06-15', 'pekerjaan' => 'Karyawan BUMN'],
                    ['name' => 'Rina Maryani', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1989-11-28', 'pekerjaan' => 'Apoteker'],
                    ['name' => 'Ahmad Zaki Santoso', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '2014-02-04', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                    ['name' => 'Aqila Humaira Santoso', 'gender' => 'Perempuan', 'hub' => 'Anak', 'tgl_lahir' => '2025-07-19', 'pekerjaan' => 'Belum Bekerja'], // Balita (~1 thn)
                ],
            ],

            // ==================== RT 005 (3 KK Baru: KK 8 s.d. 10) ====================
            [
                'rt' => '5',
                'rt_number' => '005',
                'no_kk' => '3273102401260007',
                'alamat' => 'Jl. Cisaranten Kulon No. 41, RT 005 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'C5-41',
                'status_verifikasi_dukcapil' => 'Terverifikasi',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Dedi Supriyadi', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1978-04-10', 'pekerjaan' => 'Dosen'],
                    ['name' => 'Eni Rohaeni', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1981-08-25', 'pekerjaan' => 'Guru'],
                    ['name' => 'Wildan Supriyadi', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '2006-01-18', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                    ['name' => 'Tiara Supriyadi', 'gender' => 'Perempuan', 'hub' => 'Anak', 'tgl_lahir' => '2011-09-08', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                ],
            ],
            [
                'rt' => '5',
                'rt_number' => '005',
                'no_kk' => '3273102401260008',
                'alamat' => 'Jl. Cisaranten Kulon No. 45, RT 005 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'C5-45',
                'status_verifikasi_dukcapil' => 'Menunggu Verifikasi',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Suryadi Wibowo', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1958-10-12', 'pekerjaan' => 'Pensiunan'], // Lansia (68 thn)
                    ['name' => 'Endang Sulistyo', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1962-03-05', 'pekerjaan' => 'Ibu Rumah Tangga'], // Lansia (64 thn)
                    ['name' => 'Bagas Wibowo', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '1994-07-20', 'pekerjaan' => 'Karyawan Swasta'],
                ],
            ],
            [
                'rt' => '5',
                'rt_number' => '005',
                'no_kk' => '3273102401260009',
                'alamat' => 'Jl. Cisaranten Kulon No. 48, RT 005 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'C5-48',
                'status_verifikasi_dukcapil' => 'Belum Ada Dokumen',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Gilang Ramadhan', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1993-02-14', 'pekerjaan' => 'Programmer'],
                    ['name' => 'Putri Ayu Wandira', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1996-05-22', 'pekerjaan' => 'Penulis'],
                    ['name' => 'Muhammad Rayyan', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '2024-09-01', 'pekerjaan' => 'Belum Bekerja'], // Balita (~2 thn)
                ],
            ],

            // ==================== RT 006 (1 KK Baru: KK 10) ====================
            [
                'rt' => '6',
                'rt_number' => '006',
                'no_kk' => '3273102401260010',
                'alamat' => 'Jl. Golf Timur No. 52, RT 006 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'G6-52',
                'status_verifikasi_dukcapil' => 'Terverifikasi',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Herman Wijaya', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1982-12-03', 'pekerjaan' => 'Wiraswasta'],
                    ['name' => 'Siti Kalsum', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1985-07-17', 'pekerjaan' => 'Pedagang'],
                    ['name' => 'Vicky Wijaya', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '2009-10-10', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                    ['name' => 'Nabila Wijaya', 'gender' => 'Perempuan', 'hub' => 'Anak', 'tgl_lahir' => '2015-06-24', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                ],
            ],

            // ==================== RT 007 (2 KK Baru: KK 9 s.d. 10) ====================
            [
                'rt' => '7',
                'rt_number' => '007',
                'no_kk' => '3273102401260011',
                'alamat' => 'Jl. Arcamanik Timur No. 58, RT 007 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'E7-58',
                'status_verifikasi_dukcapil' => 'Menunggu Verifikasi',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Agus Firmansyah', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1983-03-29', 'pekerjaan' => 'Teknisi'],
                    ['name' => 'Fitri Handayani', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1986-11-12', 'pekerjaan' => 'Ibu Rumah Tangga'],
                    ['name' => 'Aldo Firmansyah', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '2012-08-15', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                    ['name' => 'Zahra Firmansyah', 'gender' => 'Perempuan', 'hub' => 'Anak', 'tgl_lahir' => '2022-10-05', 'pekerjaan' => 'Belum Bekerja'], // Balita (~4 thn)
                ],
            ],
            [
                'rt' => '7',
                'rt_number' => '007',
                'no_kk' => '3273102401260012',
                'alamat' => 'Jl. Arcamanik Timur No. 62, RT 007 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293',
                'nomor_rumah' => 'E7-62',
                'status_verifikasi_dukcapil' => 'Belum Ada Dokumen',
                'status_dokumen' => 'Aktif',
                'members' => [
                    ['name' => 'Sukirno', 'gender' => 'Laki-laki', 'hub' => 'Kepala Keluarga', 'tgl_lahir' => '1959-05-18', 'pekerjaan' => 'Pensiunan'], // Lansia (67 thn)
                    ['name' => 'Wartini Sukirno', 'gender' => 'Perempuan', 'hub' => 'Istri', 'tgl_lahir' => '1961-09-24', 'pekerjaan' => 'Ibu Rumah Tangga'], // Lansia (65 thn)
                    ['name' => 'Danang Sukirno', 'gender' => 'Laki-laki', 'hub' => 'Anak', 'tgl_lahir' => '1991-01-11', 'pekerjaan' => 'Karyawan Swasta'],
                    ['name' => 'Kevin Sukirno', 'gender' => 'Laki-laki', 'hub' => 'Cucu', 'tgl_lahir' => '2018-06-19', 'pekerjaan' => 'Pelajar/Mahasiswa'],
                ],
            ],
        ];

        $defaultPassword = Hash::make('password');
        $nikCounter = 3273102401260100;
        $emailCounter = 1;

        foreach ($newFamilies as $f) {
            // Cek apakah KK sudah ada
            $existingKk = KartuKeluarga::where('no_kk', $f['no_kk'])->first();
            if ($existingKk) {
                continue;
            }

            // 1. Buat KK terlebih dahulu tanpa kepala_keluarga_id
            $kk = KartuKeluarga::create([
                'no_kk' => $f['no_kk'],
                'nama_kepala_keluarga' => $f['members'][0]['name'],
                'nik_kepala_keluarga' => (string)($nikCounter + 1),
                'alamat' => $f['alamat'],
                'rt' => $f['rt'],
                'rw' => '013',
                'nomor_rumah' => $f['nomor_rumah'],
                'kelurahan' => 'Sukamiskin',
                'kecamatan' => 'Arcamanik',
                'kota' => 'Kota Bandung',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '40293',
                'status_rumah' => 'Milik sendiri',
                'status_verifikasi_dukcapil' => $f['status_verifikasi_dukcapil'],
                'status_dokumen' => $f['status_dokumen'],
                'tgl_diterbitkan_dukcapil' => '2021-04-15',
                'sumber_dokumen' => 'Salinan Resmi Dukcapil',
                'versi_dokumen' => 1,
                'catatan_verifikasi_dukcapil' => $f['status_verifikasi_dukcapil'] === 'Terverifikasi' 
                    ? 'Berkas KK fisik telah dicocokkan dengan data registrasi wilayah.' 
                    : null,
                'metode_verifikasi' => $f['status_verifikasi_dukcapil'] === 'Terverifikasi' 
                    ? 'Pemeriksaan Berkas Fisik Asli' 
                    : null,
            ]);

            $kepalaKeluargaId = null;

            // 2. Buat seluruh anggota keluarga
            foreach ($f['members'] as $mIndex => $m) {
                $nikCounter++;
                $cleanRt = $f['rt_number'];
                $userEmail = 'warga.rt' . intval($cleanRt) . '.kk' . $kk->id . '.' . ($mIndex + 1) . '@sukamiskin.rw013.id';

                $user = User::create([
                    'name' => $m['name'],
                    'email' => $userEmail,
                    'password' => $defaultPassword,
                    'role' => 'warga',
                    'rt_number' => $cleanRt,
                    'rw_number' => '013',
                    'house_number' => $f['nomor_rumah'],
                    'phone' => '08' . rand(1111111111, 9999999999),
                    'no_kk' => $f['no_kk'],
                    'kartu_keluarga_id' => $kk->id,
                    'hubungan_keluarga' => $m['hub'],
                    'status_domisili' => 'Warga Tetap',
                    'status_verifikasi' => 'Terverifikasi RT',
                    'nik' => (string)$nikCounter,
                    'gender' => $m['gender'],
                    'tempat_lahir' => 'Bandung',
                    'agama' => 'Islam',
                    'status_perkawinan' => in_array($m['hub'], ['Kepala Keluarga', 'Istri']) ? 'Kawin' : 'Belum Kawin',
                    'pekerjaan' => $m['pekerjaan'],
                    'kewarganegaraan' => 'WNI',
                    'pendidikan_terakhir' => in_array($m['hub'], ['Kepala Keluarga', 'Istri']) ? 'S1' : 'SMA',
                    'tanggal_lahir' => $m['tgl_lahir'],
                    'address' => $f['alamat'],
                    'status_rumah' => 'milik_sendiri',
                    'is_active' => true,
                ]);

                if ($mIndex === 0) {
                    $kepalaKeluargaId = $user->id;
                    $kk->update([
                        'kepala_keluarga_id' => $user->id,
                        'nik_kepala_keluarga' => $user->nik,
                    ]);
                }
            }
        }
    }
}
