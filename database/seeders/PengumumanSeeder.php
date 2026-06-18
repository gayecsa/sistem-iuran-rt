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
            'judul' => 'Rapat Pengurus RT Bulan Juni',
            'isi' => 'Assalamu\'alaikum Warga RT 001,

Kami mengumumkan bahwa akan ada rapat rutin pengurus RT pada:

📅 Tanggal: Minggu, 16 Juni 2026
🕖 Waktu: Pukul 15.00 WIB
📍 Lokasi: Rumah Ketua RT (Jalan Gandaria No. 15)

Agenda Rapat:
• Laporan keuangan RT bulan Mei
• Pembahasan program RT semester II
• Evaluasi program kerja bakti
• Tanya jawab dan aspirasi warga

Diharapkan kehadiran semua pengurus RT. Jika ada yang berhalangan, silakan konfirmasi ke nomor di bawah.

Terima kasih,
Pengurus RT 001',
            'kategori' => 'Penting',
            'tanggal_aktif' => Carbon::now(),
        ]);

        Pengumuman::create([
            'judul' => 'Jadwal Pembayaran Iuran RT Juni 2026',
            'isi' => 'Kepada Yth. Semua Warga RT 001,

Dengan hormat kami informasikan jadwal pembayaran iuran RT untuk bulan Juni 2026:

📋 Rincian Pembayaran:
• Iuran Rutin: Rp 150.000,00
• Iuran Perbaikan Jalan: Rp 100.000,00
• Iuran Kebersihan: Rp 50.000,00
Total: Rp 300.000,00

📅 Jadwal Pembayaran:
• Tanggal 1-15 Juni: Pembayaran normal
• Tanggal 16-30 Juni: Denda Rp 10.000,00 per hari
• Setelah 30 Juni: Akan ada tindakan lanjutan

💳 Cara Pembayaran:
1. Transfer ke rekening Kas RT
   BCA: 12345678901 (Bendahara RT)
2. Bayar langsung ke Bendahara RT
3. Bayar ke Koordinator Blok masing-masing

📝 Catatan:
- Cantumkan nama dan nomor rumah saat transfer
- Jangan lupa menyimpan bukti pembayaran
- Jika ada pertanyaan, hubungi Bendahara RT

Terima kasih atas perhatian dan partisipasi Anda.
Salam hormat,
Bendahara RT 001',
            'kategori' => 'Informasi',
            'tanggal_aktif' => Carbon::now(),
        ]);

        Pengumuman::create([
            'judul' => 'Program Kerja Bakti Lingkungan Mei',
            'isi' => 'Alhamdulillah, kegiatan kerja bakti lingkungan RT 001 bulan Mei telah berhasil dilaksanakan dengan baik.

📊 Hasil Pelaksanaan:
✅ Pembersihan saluran air: Selesai
✅ Perbaikan jalan utama RT: Selesai
✅ Penanaman pohon: 25 pohon tertanam
✅ Pengecatan batas RT: Selesai

👥 Partisipasi:
Total peserta: 47 orang (Alhamdulillah tingkat partisipasi tinggi)
Waktu pelaksanaan: 6 jam kerja

📸 Dokumentasi:
Semua foto kegiatan telah kami uploadkan di papan informasi RT.

🙏 Terima Kasih kepada:
• Semua warga yang telah berpartisipasi
• Ibu-ibu PKK atas penyediaan minuman
• Tim inti yang telah mengkoordinir

Semoga lingkungan RT kita tetap indah dan bersih.

Salam,
Pengurus RT 001',
            'kategori' => 'Selesai',
            'tanggal_aktif' => Carbon::parse('2026-05-28'),
        ]);

        Pengumuman::create([
            'judul' => 'Himbauan Keamanan dan Ketertiban',
            'isi' => 'Yth. Semua Warga RT 001,

Berdasarkan laporan dari koordinator blok dan observasi pengurus RT, kami mengumumkan beberapa himbauan penting:

🚨 Himbauan Keamanan:
1. Ada laporan pencurian di area RT. Mohon tingkatkan kewaspadaan
2. Pastikan pintu dan jendela terkunci dengan baik
3. Matikan lampu ketika tidak digunakan
4. Laporkan aktivitas mencurigakan ke pengurus RT atau kepolisian

🚴 Himbauan Tertib Lalu Lintas:
1. Jangan parkir sembarangan di jalan umum
2. Kurangi kecepatan di area pemukiman
3. Parkir kendaraan di area yang sudah ditentukan

🌳 Himbauan Lingkungan:
1. Jangan membuang sampah sembarangan
2. Tata tertib penempatan TPS (Tempat Pembuangan Sampah)
3. Pantau aliran air agar tidak tersumbat

Kami berharap semua warga dapat mematuhi himbauan ini demi kenyamanan dan keamanan bersama.

Hormat,
Pengurus RT 001',
            'kategori' => 'Penting',
            'tanggal_aktif' => Carbon::parse('2026-06-05'),
        ]);

        Pengumuman::create([
            'judul' => 'Pengumuman Liburan Raya Idul Fitri 2026',
            'isi' => 'Assalamu\'alaikum Warahmatullahi Wabarakatuh,

Dengan bahagia kami umumkan menjelang perayaan Idul Fitri 1447 H, pengurus RT 001 akan mengadakan kegiatan silaturrahmi.

📅 Program Idul Fitri 2026:
• Sholat Idul Fitri: 05.30 WIB di lapangan RT
• Arisan RT: Setiap keluarga diharapkan berpartisipasi
• Syukuran bersama: Makan-makan bersama di balai RT
• Takziyah ke keluarga yang ditinggal

🎁 Persiapan:
• Iuran takziyah: Rp 50.000,00 per keluarga
• Jika ada yang kurang mampu, silakan hubungi sekretaris RT
• Target pengumpulan: sebelum 10 Juni 2026

📝 Jadwal Kegiatan:
• 15 Juni (Hari H): Pagi - Sholat Idul Fitri
• 15 Juni (Sore): Arisan dan Syukuran

Semoga ibadah kita diterima oleh Allah SWT dan Idul Fitri menjadi momentum untuk memperkuat silaturrahmi.

Wassalamu\'alaikum Warahmatullahi Wabarakatuh,
Pengurus RT 001',
            'kategori' => 'Informasi',
            'tanggal_aktif' => Carbon::parse('2026-06-08'),
        ]);
    }
}
