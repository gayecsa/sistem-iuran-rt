<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Iuran;
use Carbon\Carbon;

class IuranSeeder extends Seeder
{
    public function run()
    {
        Iuran::create([
            'nama_iuran' => 'Iuran Wajib Bulanan',
            'deskripsi' => 'Iuran rutin untuk kebersihan dan keamanan lingkungan',
            'jenis_iuran' => 'wajib',
            'nominal' => 50000,
            'tanggal_mulai' => Carbon::now()->startOfMonth(),
            'periode' => 'bulanan',
            'status' => 'aktif'
        ]);
        
        Iuran::create([
            'nama_iuran' => 'Dana Sosial',
            'deskripsi' => 'Iuran untuk kegiatan sosial dan bantuan',
            'jenis_iuran' => 'sukarela',
            'nominal' => 25000,
            'tanggal_mulai' => Carbon::now(),
            'periode' => 'sekali',
            'status' => 'aktif'
        ]);
        
        Iuran::create([
            'nama_iuran' => 'Iuran Kebersihan',
            'deskripsi' => 'Iuran untuk petugas kebersihan',
            'jenis_iuran' => 'wajib',
            'nominal' => 30000,
            'tanggal_mulai' => Carbon::now(),
            'periode' => 'bulanan',
            'status' => 'aktif'
        ]);
    }
}