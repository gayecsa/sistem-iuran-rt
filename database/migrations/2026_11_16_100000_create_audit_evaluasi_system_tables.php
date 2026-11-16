<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Kegiatan dan Proyek RT & RW yang Diaudit
        Schema::create('audit_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kegiatan', 50)->unique();
            $table->string('nama_kegiatan', 255);
            $table->string('kategori_kegiatan', 100); // pembangunan_fasilitas, kebersihan_lingkungan, kegiatan_sosial, pengadaan_barang, pemberdayaan_masyarakat, lainnya
            $table->string('rt_number', 10)->nullable(); // null jika proyek RW komunal
            $table->string('penanggung_jawab', 150);
            $table->foreignId('buku_kas_id')->nullable()->constrained('buku_kas')->nullOnDelete();
            $table->decimal('anggaran_awal', 15, 2)->default(0);
            $table->decimal('realisasi_anggaran', 15, 2)->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->integer('tahun')->default(2026);
            $table->enum('status', ['direncanakan', 'berjalan', 'selesai', 'tertunda', 'evaluasi'])->default('berjalan');
            $table->enum('status_audit', ['sesuai', 'perlu_perhatian', 'temuan_audit', 'belum_diaudit'])->default('belum_diaudit');
            $table->string('dokumentasi_path', 255)->nullable();
            $table->string('lpj_dokumen_path', 255)->nullable();
            $table->text('catatan_kegiatan')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Catatan Temuan Audit Resmi RW 013
        Schema::create('audit_temuans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_temuan', 50)->unique();
            $table->string('judul_temuan', 255);
            $table->string('kategori_masalah', 100); // administrasi, keuangan, transaksi_tanpa_bukti, selisih_anggaran, kegiatan_terlambat, prosedur_pelaporan, lainnya
            $table->string('rt_number', 10)->nullable();
            $table->foreignId('kegiatan_id')->nullable()->constrained('audit_kegiatans')->nullOnDelete();
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi_keuangans')->nullOnDelete();
            $table->foreignId('buku_kas_id')->nullable()->constrained('buku_kas')->nullOnDelete();
            $table->date('tanggal_temuan');
            $table->integer('tahun')->default(2026);
            $table->text('deskripsi_masalah');
            $table->string('bukti_pendukung_path', 255)->nullable();
            $table->enum('tingkat_prioritas', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('sedang');
            $table->enum('status', ['perlu_diperiksa', 'dalam_pemeriksaan', 'menunggu_klarifikasi', 'perlu_tindak_lanjut', 'selesai'])->default('perlu_diperiksa');
            $table->date('tenggat_tindak_lanjut')->nullable();
            $table->text('catatan_evaluasi_rw')->nullable();
            $table->text('tanggapan_rt')->nullable();
            $table->dateTime('tanggal_tanggapan')->nullable();
            $table->foreignId('auditor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('diselesaikan_pada')->nullable();
            $table->timestamps();
        });

        // 3. Tabel Evaluasi & Status Pertanggungjawaban RT
        Schema::create('audit_evaluasi_rts', function (Blueprint $table) {
            $table->id();
            $table->string('rt_number', 10);
            $table->integer('tahun')->default(2026);
            $table->string('periode', 100)->default('Tahunan 2026');
            $table->integer('skor_kepatuhan')->default(85);
            $table->enum('status_pertanggungjawaban', ['sangat_baik', 'baik', 'cukup', 'perlu_perhatian', 'belum_lengkap'])->default('baik');
            $table->text('catatan_evaluasi')->nullable();
            $table->text('rekomendasi_rw')->nullable();
            $table->date('tenggat_perbaikan')->nullable();
            $table->enum('status_tindak_lanjut', ['selesai', 'dalam_proses', 'menunggu_tanggapan'])->default('dalam_proses');
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_evaluasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_evaluasi_rts');
        Schema::dropIfExists('audit_temuans');
        Schema::dropIfExists('audit_kegiatans');
    }
};
