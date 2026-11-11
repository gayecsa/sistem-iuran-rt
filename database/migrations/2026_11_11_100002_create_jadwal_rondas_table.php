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
        if (!Schema::hasTable('jadwal_rondas')) {
            Schema::create('jadwal_rondas', function (Blueprint $table) {
                $table->id();
                $table->string('hari'); // Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu
                $table->string('rt_list'); // e.g. "RT 001 & RT 002"
                $table->string('primary_rt', 10)->default('001');
                $table->unsignedBigInteger('coordinator_id_1')->nullable()->index();
                $table->unsignedBigInteger('coordinator_id_2')->nullable()->index();
                $table->string('koordinator_fallback')->nullable(); // Jika belum ada warga yang ditunjuk
                $table->string('anggota_label')->default('6 Petugas');
                $table->string('pos_ronda');
                $table->string('area_patroli');
                $table->time('jam_mulai')->default('22:00:00');
                $table->time('jam_selesai')->default('04:30:00');
                $table->enum('status', ['Bertugas', 'Terjadwal', 'Selesai'])->default('Terjadwal');
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->timestamps();

                $table->foreign('coordinator_id_1')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');

                $table->foreign('coordinator_id_2')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_rondas');
    }
};
