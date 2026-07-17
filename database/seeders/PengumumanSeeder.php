<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengumuman;
use Carbon\Carbon;

class PengumumanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pengumuman::create([
            'judul' => 'Kerja Bakti Akbar & Penataan Lingkungan RW 013',
            'isi' => "Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\nKepada Yth. Seluruh Warga RT 001 - RT 008 / RW 013,\n\nDalam rangka menjaga kebersihan, kelancaran saluran air, dan keasrian lingkungan, Pengurus RW 013 mengundang seluruh warga untuk hadir dalam Kerja Bakti Akbar pada:\n\n📅 Hari/Tanggal: Minggu, 26 Juli 2026\n🕖 Waktu: Pukul 06.30 WIB - Selesai\n📍 Titik Kumpul: Gedung Serbaguna Utama RW 013\n\nAgenda Utama:\n1. Pembersihan saluran air dan selokan utama RW 013\n2. Pemangkasan dahan pohon rawan tumbang\n3. Penataan Taman Warga dan Pos Ronda\n\nMohon membawa peralatan kerja bakti masing-masing. Konsumsi dan sarapan bersama disediakan oleh Pengurus RW 013.\n\nTerima kasih atas partisipasi aktif warga,\nPengurus RW 013",
            'kategori' => 'Penting',
            'tanggal_aktif' => Carbon::now(),
        ]);

        Pengumuman::create([
            'judul' => 'Jadwal Pelayanan Posyandu & Cek Kesehatan RW 013',
            'isi' => "Kepada Yth. Seluruh Warga RW 013,\n\nInformasi jadwal pelayanan Posyandu Balita, Ibu Hamil, dan Lansia se-RW 013 bulan Juli 2026:\n\n• Posyandu Sejahtera (RT 001): Sabtu Pertama (08.00 - 11.00 WIB)\n• Posyandu Kasih Ibu (RT 003): Sabtu Kedua (08.00 - 11.00 WIB)\n• Posyandu Tunas Bangsa (RT 005): Sabtu Ketiga (08.00 - 11.00 WIB)\n• Posyandu Harapan Bunda (RT 007): Sabtu Keempat (08.00 - 11.00 WIB)\n\nFasilitas GRATIS: Imunisasi lengkap, penimbangan balita, pemberian Vitamin A, serta cek gula darah & kolesterol lansia.\n\nSalam sehat,\nTim Kesehatan RW 013",
            'kategori' => 'Informasi',
            'tanggal_aktif' => Carbon::now(),
        ]);

        Pengumuman::create([
            'judul' => 'Himbauan Keamanan Ronda Malam & Siskamling RW 013',
            'isi' => "Himbauan Keamanan Lingkungan RW 013:\n\nMenindaklanjuti koordinasi keamanan wilayah, diharapkan seluruh jadwal ronda malam di RT 001 s/d RT 008 dapat dilaksanakan sesuai petunjuk berikut:\n\n1. Petugas ronda wajib melapor ke Pos Sekretariat Utama RW 013 pada pukul 23.00 WIB.\n2. Tamu yang berkunjung di atas pukul 22.00 WIB wajib melapor 1x24 jam.\n3. Mengaktifkan portal keamanan RW mulai pukul 23.00 - 05.00 WIB.\n\nHormat kami,\nSeksi Keamanan RW 013",
            'kategori' => 'Penting',
            'tanggal_aktif' => Carbon::parse('2026-07-10'),
        ]);

        Pengumuman::create([
            'judul' => 'Jadwal Pembayaran Iuran & Kebersihan Warga RW 013',
            'isi' => "Kepada Yth. Semua Warga RT 001 - RT 008 / RW 013,\n\nDengan hormat kami informasikan jadwal dan rincian pembayaran iuran bulanan warga RW 013:\n\n• Iuran Kebersihan & Sampah RW: Rp 50.000,00\n• Iuran Keamanan & Siskamling: Rp 50.000,00\n• Kas Pengembangan RW: Rp 50.000,00\nTotal: Rp 150.000,00 / bulan\n\nPembayaran dapat dilakukan secara online melalui aplikasi Warkas Machi atau menghubungi Bendahara RT/RW.\n\nTerima kasih,\nBendahara RW 013",
            'kategori' => 'Informasi',
            'tanggal_aktif' => Carbon::parse('2026-07-01'),
        ]);

        Pengumuman::create([
            'judul' => 'Persiapan Peringatan HUT RI ke-81 & Lomba Antar-RT',
            'isi' => "Pengumuman Kepada Warga RW 013:\n\nDalam rangka menyambut HUT Kemerdekaan RI ke-81, Panitia Karang Taruna RW 013 akan mengelar rangkaian perlombaan antar-RT:\n\n• Lomba Kebersihan & Keasrian Lingkungan RT\n• Turnamen Bulutangkis & Catur Warga\n• Panggung Pentas Seni Anak & Lansia\n\nPendaftaran peserta lomba dapat dilakukan melalui ketua RT masing-masing mulai tanggal 1 Agustus 2026.\n\nPanitia HUT RI RW 013",
            'kategori' => 'Informasi',
            'tanggal_aktif' => Carbon::parse('2026-06-25'),
        ]);
    }
}
