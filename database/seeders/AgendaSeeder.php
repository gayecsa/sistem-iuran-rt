<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agenda;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = null;
        try {
            $rwUser = User::where('role', 'rw')->first() ?: User::where('role', 'admin')->first();
            $adminId = $rwUser->id ?? null;
        } catch (\Throwable $e) {}

        $items = [
            // --- 1. AGENDA MENDATANG / AKTIF RW ---
            [
                'judul' => 'Rapat Pleno Koordinasi Pengurus RT & RW 013',
                'deskripsi' => 'Rapat evaluasi laporan bulanan RT, monitoring kas akhir tahun, dan persiapan musim hujan serentak seluruh wilayah RW 013.',
                'tanggal' => Carbon::now()->addDays(3)->toDateString(),
                'waktu' => '19.30 - 22.00 WIB',
                'lokasi' => 'Ruang Rapat Utama Sekretariat RW 013',
                'kategori' => 'rapat_koordinasi',
                'penyelenggara' => 'rw',
                'rt_number' => null,
                'penanggung_jawab' => 'Ketua RW 013',
                'peserta' => 'Ketua RW, Sekretaris, Bendahara, & Seluruh Ketua RT 001 - RT 008',
                'prioritas' => 'tinggi',
                'status_kegiatan' => 'akan_datang',
                'status_laporan' => 'belum_ada_laporan',
                'ringkasan_hasil' => null,
                'is_public' => false,
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Kerja Bakti Serentak & Fogging DBD RW 013',
                'deskripsi' => 'Pembersihan saluran drainase, pemilahan barang bekas sarang nyamuk, dan pengasapan/fogging serentak 8 RT untuk antisipasi musim penghujan.',
                'tanggal' => Carbon::now()->addDays(5)->toDateString(),
                'waktu' => '06.30 - 10.00 WIB',
                'lokasi' => 'Seluruh Wilayah RT 001 sampai RT 008',
                'kategori' => 'kebersihan',
                'penyelenggara' => 'rw',
                'rt_number' => null,
                'penanggung_jawab' => 'Seksi Kebersihan RW 013',
                'peserta' => 'Warga RT 001 - RT 008, Pengurus RW, Tim DLHK',
                'prioritas' => 'tinggi',
                'status_kegiatan' => 'akan_datang',
                'status_laporan' => 'belum_ada_laporan',
                'ringkasan_hasil' => null,
                'is_public' => true,
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Posyandu Terpadu Balita & Skrining Lansia Sehat',
                'deskripsi' => 'Penimbangan balita, pemantauan status stunting, vaksinasi imunisasi dasar balita, dan cek kesehatan tensi/gula darah untuk lansia.',
                'tanggal' => Carbon::now()->addDays(12)->toDateString(),
                'waktu' => '08.00 - 11.30 WIB',
                'lokasi' => 'Balai Warga Terpadu RW 013',
                'kategori' => 'kesehatan',
                'penyelenggara' => 'rw',
                'rt_number' => null,
                'penanggung_jawab' => 'Ketua Kader Posyandu RW 013',
                'peserta' => 'Kader Posyandu, Nakes Puskesmas Arcamanik, Ibu Balita & Lansia',
                'prioritas' => 'sedang',
                'status_kegiatan' => 'akan_datang',
                'status_laporan' => 'belum_ada_laporan',
                'ringkasan_hasil' => null,
                'is_public' => true,
                'created_by' => $adminId,
            ],

            // --- 2. AGENDA MONITORING RT (BERASAL DARI RT 001 S/D RT 008) ---
            [
                'judul' => 'Musyawarah Warga: Perbaikan Gorong-gorong Gang Cempaka',
                'deskripsi' => 'Musyawarah RT membahas pembongkaran sedimentasi saluran air dan penutup gorong-gorong di sepanjang Gang Cempaka.',
                'tanggal' => Carbon::now()->addDays(4)->toDateString(),
                'waktu' => '19.30 - 21.00 WIB',
                'lokasi' => 'Pos Ronda RT 001',
                'kategori' => 'kegiatan_warga',
                'penyelenggara' => 'rt',
                'rt_number' => '001',
                'penanggung_jawab' => 'Ketua RT 001',
                'peserta' => 'Warga Blok Cempaka RT 001',
                'prioritas' => 'sedang',
                'status_kegiatan' => 'akan_datang',
                'status_laporan' => 'belum_ada_laporan',
                'ringkasan_hasil' => null,
                'is_public' => false,
                'created_by' => null,
            ],
            [
                'judul' => 'Pelatihan Pembuatan Kompos & Biopori Ramah Lingkungan',
                'deskripsi' => 'Edukasi dan praktik pembuatan lubang biopori serta pengolahan sampah daun menjadi kompos organik.',
                'tanggal' => Carbon::now()->toDateString(),
                'waktu' => '09.00 - 12.00 WIB',
                'lokasi' => 'Taman Komunal RT 003',
                'kategori' => 'kegiatan_sosial',
                'penyelenggara' => 'rt',
                'rt_number' => '003',
                'penanggung_jawab' => 'Ketua RT 003 & Unit Bank Sampah',
                'peserta' => 'Warga RT 003 & Penggiat Lingkungan',
                'prioritas' => 'sedang',
                'status_kegiatan' => 'sedang_berlangsung',
                'status_laporan' => 'menunggu_laporan',
                'ringkasan_hasil' => 'Kegiatan sedang berjalan, dihadiri 24 perwakilan kepala keluarga.',
                'is_public' => true,
                'created_by' => null,
            ],
            [
                'judul' => 'Patroli Siskamling & Evaluasi Portal Keamanan Malam',
                'deskripsi' => 'Sosialisasi pemberlakuan jam tutup portal malam pukul 23.00 WIB serta penjadwalan giliran ronda malam warga.',
                'tanggal' => Carbon::now()->addDays(2)->toDateString(),
                'waktu' => '21.00 - 23.30 WIB',
                'lokasi' => 'Pos Satpam Utama RT 005',
                'kategori' => 'keamanan',
                'penyelenggara' => 'rt',
                'rt_number' => '005',
                'penanggung_jawab' => 'Ketua RT 005 & Koordinator Linmas',
                'peserta' => 'Petugas Jaga, Tim Linmas, & Perwakilan Blok',
                'prioritas' => 'tinggi',
                'status_kegiatan' => 'akan_datang',
                'status_laporan' => 'belum_ada_laporan',
                'ringkasan_hasil' => null,
                'is_public' => false,
                'created_by' => null,
            ],
            [
                'judul' => 'Pemeriksaan Kesehatan Lansia & Senam Jantung Sehat RT 007',
                'deskripsi' => 'Kegiatan rutin senam sehat lansia di lapangan RT 007 bekerjasama dengan kader posbindu.',
                'tanggal' => Carbon::now()->addDays(6)->toDateString(),
                'waktu' => '06.30 - 08.30 WIB',
                'lokasi' => 'Lapangan Olahraga RT 007',
                'kategori' => 'kesehatan',
                'penyelenggara' => 'rt',
                'rt_number' => '007',
                'penanggung_jawab' => 'Ketua RT 007',
                'peserta' => 'Warga Lansia & Pra-Lansia RT 007',
                'prioritas' => 'sedang',
                'status_kegiatan' => 'akan_datang',
                'status_laporan' => 'belum_ada_laporan',
                'ringkasan_hasil' => null,
                'is_public' => true,
                'created_by' => null,
            ],

            // --- 3. RIWAYAT AGENDA (SELESAI / DIBATALKAN / TIDAK TERLAKSANA) ---
            [
                'judul' => 'Sosialisasi Pemilahan Sampah Organik Rumah Tangga RT 002',
                'deskripsi' => 'Sosialisasi gerakan pilah sampah dari dapur dan pemanfaatan ember tumpuk di lingkungan RT 002.',
                'tanggal' => Carbon::now()->subDays(6)->toDateString(),
                'waktu' => '09.00 - 11.30 WIB',
                'lokasi' => 'Balai RT 002',
                'kategori' => 'kebersihan',
                'penyelenggara' => 'rt',
                'rt_number' => '002',
                'penanggung_jawab' => 'Ketua RT 002',
                'peserta' => '35 Ibu Rumah Tangga RT 002',
                'prioritas' => 'sedang',
                'status_kegiatan' => 'selesai',
                'status_laporan' => 'laporan_lengkap',
                'ringkasan_hasil' => 'Telah didistribusikan 35 ember pilah sampah organik. Warga menyepakati penyetoran sampah dapur tiap hari Selasa dan Kamis.',
                'is_public' => true,
                'created_by' => null,
            ],
            [
                'judul' => 'Penyuluhan Mitigasi Bencana & Pemeriksaan APAR Lingkungan RW',
                'deskripsi' => 'Edukasi kesiapsiagaan kebakaran pemukiman padat dan simulasi pemadaman api menggunakan APAR dan karung goni basah.',
                'tanggal' => Carbon::now()->subDays(9)->toDateString(),
                'waktu' => '13.30 - 16.00 WIB',
                'lokasi' => 'Halaman Kantor RW 013',
                'kategori' => 'keamanan',
                'penyelenggara' => 'rw',
                'rt_number' => null,
                'penanggung_jawab' => 'Seksi Kamtibmas RW 013 & Damkar Kota Bandung',
                'peserta' => 'Pengurus RT 001 - RT 008 & Satgas Bencana Warga',
                'prioritas' => 'tinggi',
                'status_kegiatan' => 'selesai',
                'status_laporan' => 'laporan_lengkap',
                'ringkasan_hasil' => 'Simulasi berjalan lancar. Sebanyak 12 tabung APAR di lingkungan RW dan pos ronda RT telah diuji dan diisi ulang siap pakai.',
                'is_public' => true,
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Penertiban Parkir Liar & Rekayasa Arus Jalan Melati RT 004',
                'deskripsi' => 'Musyawarah penetapan larangan parkir kendaraan roda empat di badan jalan satu arah Jalan Melati.',
                'tanggal' => Carbon::now()->subDays(12)->toDateString(),
                'waktu' => '20.00 - 22.00 WIB',
                'lokasi' => 'Pos Ronda RT 004',
                'kategori' => 'kegiatan_warga',
                'penyelenggara' => 'rt',
                'rt_number' => '004',
                'penanggung_jawab' => 'Ketua RT 004',
                'peserta' => 'Pemilik Kendaraan & Warga Jl. Melati',
                'prioritas' => 'sedang',
                'status_kegiatan' => 'selesai',
                'status_laporan' => 'laporan_dikirim',
                'ringkasan_hasil' => 'Disepakati pemasangan rambu dilarang parkir di sisi timur, dan kantong parkir bersama dialihkan ke lahan kosong sewaan.',
                'is_public' => false,
                'created_by' => null,
            ],
            [
                'judul' => 'Kerja Bakti Pengerukan Sedimen Saluran Utama RT 008',
                'deskripsi' => 'Rencana pengerukan saluran drainase perbatasan wilayah Arcamanik.',
                'tanggal' => Carbon::now()->subDays(15)->toDateString(),
                'waktu' => '07.00 - 10.30 WIB',
                'lokasi' => 'Saluran Utama RT 008',
                'kategori' => 'kebersihan',
                'penyelenggara' => 'rt',
                'rt_number' => '008',
                'penanggung_jawab' => 'Ketua RT 008',
                'peserta' => 'Warga RT 008',
                'prioritas' => 'tinggi',
                'status_kegiatan' => 'dibatalkan',
                'status_laporan' => 'laporan_lengkap',
                'ringkasan_hasil' => 'Kegiatan dibatalkan karena curah hujan deras dan air sungai meluap tinggi. Ditunda dan dialihkan ke jadwal kerja bakti serentak RW.',
                'is_public' => false,
                'created_by' => null,
            ],
            [
                'judul' => 'Inspeksi & Sanitasi Tempat Penampungan Air RT 006',
                'deskripsi' => 'Pemeriksaan jentik nyamuk berkala (Jumantik) mandiri ke rumah-rumah warga.',
                'tanggal' => Carbon::now()->subDays(20)->toDateString(),
                'waktu' => '08.00 - 11.00 WIB',
                'lokasi' => 'Wilayah Pemukiman RT 006',
                'kategori' => 'kesehatan',
                'penyelenggara' => 'rt',
                'rt_number' => '006',
                'penanggung_jawab' => 'Ketua RT 006',
                'peserta' => 'Kader Jumantik RT 006',
                'prioritas' => 'rendah',
                'status_kegiatan' => 'tidak_terlaksana',
                'status_laporan' => 'menunggu_laporan',
                'ringkasan_hasil' => 'Tidak terlaksana karena kader berhalangan serentak. Dijadwalkan ulang oleh Ketua RT 006.',
                'is_public' => false,
                'created_by' => null,
            ],
        ];

        try {
            if (Schema::hasTable('agendas')) {
                foreach ($items as $data) {
                    Agenda::updateOrCreate(
                        [
                            'judul' => $data['judul'],
                            'tanggal' => $data['tanggal']
                        ],
                        $data
                    );
                }
            }
        } catch (\Throwable $e) {}

        // Simpan juga ke storage/app/agenda_data.json sebagai fallback sinkronisasi
        $filePath = storage_path('app/agenda_data.json');
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        file_put_contents($filePath, json_encode($items, JSON_PRETTY_PRINT));
    }
}
