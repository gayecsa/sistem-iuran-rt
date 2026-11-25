<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('agendas')) {
            Schema::create('agendas', function (Blueprint $table) {
                $table->id();
                $table->string('judul', 255);
                $table->text('deskripsi')->nullable();
                $table->date('tanggal');
                $table->string('waktu', 50)->default('19:30 - 21:30 WIB');
                $table->string('lokasi', 255);
                $table->string('kategori', 50); // rapat_koordinasi, kegiatan_sosial, kegiatan_warga, kesehatan, kebersihan, keamanan, lainnya
                $table->enum('penyelenggara', ['rw', 'rt'])->default('rw');
                $table->string('rt_number', 10)->nullable(); // null jika RW, '001' s/d '008' jika RT
                $table->string('penanggung_jawab', 150);
                $table->string('peserta', 255)->nullable();
                $table->enum('prioritas', ['rendah', 'sedang', 'tinggi', 'darurat'])->default('sedang');
                $table->enum('status_kegiatan', ['akan_datang', 'sedang_berlangsung', 'selesai', 'dibatalkan', 'tidak_terlaksana'])->default('akan_datang');
                $table->enum('status_laporan', ['belum_ada_laporan', 'menunggu_laporan', 'laporan_dikirim', 'laporan_lengkap'])->default('belum_ada_laporan');
                $table->text('ringkasan_hasil')->nullable(); // notulen / hasil rapat / kesimpulan
                $table->string('dokumentasi_path', 255)->nullable();
                $table->string('laporan_file_path', 255)->nullable();
                $table->dateTime('tanggal_laporan')->nullable();
                $table->boolean('is_public')->default(false);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
