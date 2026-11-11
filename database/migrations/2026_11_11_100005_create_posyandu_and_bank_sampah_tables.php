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
        if (!Schema::hasTable('posyandu_kegiatans')) {
            Schema::create('posyandu_kegiatans', function (Blueprint $table) {
                $table->id();
                $table->string('nama_posyandu'); // e.g. Posyandu Melati 13 Unit RT 001
                $table->string('rt_number', 10)->default('001');
                $table->string('lokasi');
                $table->string('jadwal');
                $table->unsignedBigInteger('kader_utama_id')->nullable()->index(); // Kader warga
                $table->string('mitra_kesehatan')->default('UPTD Puskesmas Arcamanik Kota Bandung');
                $table->string('tenaga_medis')->default('dr. Amanda Sp.A & Bidan Puskesmas');
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->string('layanan')->default('Penimbangan Balita, Imunisasi Dasar, PMT, Skrining Lansia');
                $table->timestamps();

                $table->foreign('kader_utama_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('bank_sampahs')) {
            Schema::create('bank_sampahs', function (Blueprint $table) {
                $table->id();
                $table->string('nama_unit'); // e.g. Bank Sampah Berkah Bersih RT 001
                $table->string('rt_number', 10)->default('001');
                $table->string('lokasi');
                $table->string('jadwal')->default('Setiap Hari Rabu (10.00 - 13.00 WIB)');
                $table->unsignedBigInteger('petugas_id')->nullable()->index(); // Petugas warga
                $table->string('kategori_diterima')->default('Plastik, Kardus, Kertas Arsip, Logam, Minyak Jelantah');
                $table->integer('total_terkumpul_kg')->default(0);
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->timestamps();

                $table->foreign('petugas_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_sampahs');
        Schema::dropIfExists('posyandu_kegiatans');
    }
};
