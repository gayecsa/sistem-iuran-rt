<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\WargaMutasi;
use App\Models\WargaStatusHistory;
use Carbon\Carbon;

$admin = User::whereIn('role', ['admin', 'rw'])->first();
$rt1User = User::where('role', 'rt')->where(function($q){ $q->where('rt_number', '001')->orWhere('rt_number', '1'); })->first();
$wargas = User::where('role', 'warga')->take(10)->get();

if ($wargas->count() >= 4) {
    // 1. Mutasi Masuk
    WargaMutasi::firstOrCreate(
        ['kode_mutasi' => 'MUT-202611-0001'],
        [
            'user_id' => $wargas[0]->id,
            'kartu_keluarga_id' => $wargas[0]->kartu_keluarga_id,
            'jenis_mutasi' => 'mutasi_masuk',
            'nama_warga' => $wargas[0]->name,
            'nik' => $wargas[0]->nik ?? '3273010101900001',
            'nomor_kk' => $wargas[0]->kartuKeluarga->nomor_kk ?? '3273010101900001',
            'gender' => $wargas[0]->gender,
            'rt_tujuan' => '001',
            'alamat_asal' => 'Jl. Kebon Sirih No. 45, Jakarta Pusat',
            'alamat_tujuan' => 'Jl. Pacuan Kuda No. 12, RT 001/RW 013',
            'asal_kota_kab' => 'Kota Jakarta Pusat',
            'asal_kecamatan' => 'Menteng',
            'asal_kelurahan' => 'Kebon Sirih',
            'tanggal_mutasi' => Carbon::now()->subDays(10)->toDateString(),
            'alasan_mutasi' => 'Pindah kerja ke kantor cabang Bandung dan membeli rumah tinggal di RW 013.',
            'status' => 'disetujui',
            'catatan_pengurus' => 'Surat Pengantar Pindah Domisili dari kelurahan asal lengkap.',
            'pengaju_id' => $rt1User ? $rt1User->id : $admin->id,
            'pemeriksa_id' => $admin->id,
            'tanggal_verifikasi' => Carbon::now()->subDays(9),
        ]
    );

    // 2. Pindah Antar-RT
    WargaMutasi::firstOrCreate(
        ['kode_mutasi' => 'MUT-202611-0002'],
        [
            'user_id' => $wargas[1]->id,
            'kartu_keluarga_id' => $wargas[1]->kartu_keluarga_id,
            'jenis_mutasi' => 'pindah_antar_rt',
            'nama_warga' => $wargas[1]->name,
            'nik' => $wargas[1]->nik ?? '3273010101900002',
            'nomor_kk' => $wargas[1]->kartuKeluarga->nomor_kk ?? '3273010101900002',
            'gender' => $wargas[1]->gender,
            'rt_asal' => '001',
            'rt_tujuan' => '003',
            'alamat_asal' => 'Jl. Pacuan Kuda No. 5, RT 001/RW 013',
            'alamat_tujuan' => 'Jl. Golf Raya No. 18, RT 003/RW 013',
            'tanggal_mutasi' => Carbon::now()->subDays(5)->toDateString(),
            'alasan_mutasi' => 'Pindah ke rumah keluarga yang lebih luas di RT 003.',
            'status' => 'disetujui',
            'catatan_pengurus' => 'Disetujui Ketua RT 001 dan Ketua RT 003.',
            'pengaju_id' => $rt1User ? $rt1User->id : $admin->id,
            'pemeriksa_id' => $admin->id,
            'tanggal_verifikasi' => Carbon::now()->subDays(4),
        ]
    );

    // 3. Mutasi Keluar (Menunggu Verifikasi)
    WargaMutasi::firstOrCreate(
        ['kode_mutasi' => 'MUT-202611-0003'],
        [
            'user_id' => $wargas[2]->id,
            'kartu_keluarga_id' => $wargas[2]->kartu_keluarga_id,
            'jenis_mutasi' => 'mutasi_keluar',
            'nama_warga' => $wargas[2]->name,
            'nik' => $wargas[2]->nik ?? '3273010101900003',
            'nomor_kk' => $wargas[2]->kartuKeluarga->nomor_kk ?? '3273010101900003',
            'gender' => $wargas[2]->gender,
            'rt_asal' => '002',
            'alamat_asal' => 'Jl. Arcamanik Endah No. 10, RT 002/RW 013',
            'alamat_tujuan' => 'Komplek Griya Indah Blok C2, Sukaraja, Sukabumi',
            'asal_kota_kab' => 'Kabupaten Sukabumi',
            'tanggal_mutasi' => Carbon::now()->subDays(2)->toDateString(),
            'alasan_mutasi' => 'Pindah domisili mengikuti penempatan tugas suami.',
            'status' => 'menunggu_verifikasi',
            'catatan_pengurus' => 'Menunggu verifikasi kelengkapan berkas SKPWNI.',
            'pengaju_id' => $rt1User ? $rt1User->id : $admin->id,
        ]
    );

    // 4. Perubahan Alamat dalam RT (Menunggu Verifikasi)
    WargaMutasi::firstOrCreate(
        ['kode_mutasi' => 'MUT-202611-0004'],
        [
            'user_id' => $wargas[3]->id,
            'kartu_keluarga_id' => $wargas[3]->kartu_keluarga_id,
            'jenis_mutasi' => 'perubahan_alamat',
            'nama_warga' => $wargas[3]->name,
            'nik' => $wargas[3]->nik ?? '3273010101900004',
            'nomor_kk' => $wargas[3]->kartuKeluarga->nomor_kk ?? '3273010101900004',
            'gender' => $wargas[3]->gender,
            'rt_asal' => '001',
            'rt_tujuan' => '001',
            'alamat_asal' => 'Jl. Pacuan Kuda No. 8, RT 001',
            'alamat_tujuan' => 'Jl. Pacuan Kuda No. 22A, RT 001',
            'tanggal_mutasi' => Carbon::now()->subDay()->toDateString(),
            'alasan_mutasi' => 'Pindah sewa rumah di kavling sebelah.',
            'status' => 'menunggu_verifikasi',
            'catatan_pengurus' => 'Pembaruan nomor rumah di RT 001.',
            'pengaju_id' => $rt1User ? $rt1User->id : $admin->id,
        ]
    );

    echo "SEEDED MUTASI DATA SUCCESSFULLY! Total Mutasi: " . WargaMutasi::count() . PHP_EOL;
}
