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
        if (!Schema::hasTable('warga_mutasis')) {
            Schema::create('warga_mutasis', function (Blueprint $table) {
                $table->id();
                $table->string('kode_mutasi', 30)->unique()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('kartu_keluarga_id')->nullable()->index();
                $table->enum('jenis_mutasi', ['mutasi_masuk', 'mutasi_keluar', 'pindah_antar_rt', 'perubahan_alamat'])->index();
                $table->string('nama_warga');
                $table->string('nik', 20)->index();
                $table->string('nomor_kk', 20)->nullable()->index();
                $table->string('gender', 15)->nullable();
                $table->string('no_telepon', 25)->nullable();
                $table->string('rt_asal', 10)->nullable()->index();
                $table->string('rt_tujuan', 10)->nullable()->index();
                $table->text('alamat_asal')->nullable();
                $table->text('alamat_tujuan')->nullable();
                $table->string('asal_kota_kab')->nullable();
                $table->string('asal_kecamatan')->nullable();
                $table->string('asal_kelurahan')->nullable();
                $table->date('tanggal_mutasi')->index();
                $table->text('alasan_mutasi');
                $table->string('dokumen_pendukung_path')->nullable();
                $table->enum('status', ['draft', 'menunggu_verifikasi', 'sedang_diperiksa', 'disetujui', 'ditolak', 'dibatalkan'])->default('menunggu_verifikasi')->index();
                $table->text('catatan_pengurus')->nullable();
                $table->text('alasan_penolakan')->nullable();
                $table->unsignedBigInteger('pengaju_id')->index();
                $table->unsignedBigInteger('pemeriksa_id')->nullable()->index();
                $table->timestamp('tanggal_verifikasi')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('kartu_keluarga_id')->references('id')->on('kartu_keluargas')->onDelete('set null');
                $table->foreign('pengaju_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('pemeriksa_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warga_mutasis');
    }
};
