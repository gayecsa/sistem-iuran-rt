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
        if (!Schema::hasTable('wilayah_administratifs')) {
            Schema::create('wilayah_administratifs', function (Blueprint $table) {
                $table->id();
                $table->string('level'); // provinsi, kota_kab, kecamatan, kelurahan, rw, rt
                $table->unsignedBigInteger('parent_id')->nullable()->index();
                $table->string('kode', 30)->nullable()->index(); // e.g. 32.73.23.1001
                $table->string('nama');
                $table->string('keterangan')->nullable();
                $table->string('kode_pos', 10)->nullable();
                $table->string('pusat_pemerintahan')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->timestamps();

                $table->foreign('parent_id')
                    ->references('id')
                    ->on('wilayah_administratifs')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayah_administratifs');
    }
};
