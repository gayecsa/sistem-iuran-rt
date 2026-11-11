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
        if (!Schema::hasTable('pengaduans')) {
            Schema::create('pengaduans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index(); // Warga pelapor
                $table->string('pelapor_nama')->nullable(); // Snapshot nama
                $table->string('pelapor_phone')->nullable();
                $table->string('kategori');
                $table->string('rt_number', 10)->default('001');
                $table->string('rw_number', 10)->default('013');
                $table->string('lokasi'); // Alamat realistis Bandung
                $table->text('deskripsi');
                $table->string('status')->default('Diproses'); // Diproses, Perlu Tindak Lanjut, Selesai
                $table->string('foto')->nullable();
                $table->text('catatan_tindak_lanjut')->nullable();
                $table->unsignedBigInteger('petugas_id')->nullable()->index(); // Petugas / Ketua RT yang menangani
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('petugas_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};
