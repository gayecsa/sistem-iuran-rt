<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
   Schema::create('pengumuman', function (Blueprint $table) {
        $table->id();
        $table->string('judul');
        $table->text('isi');
        $table->string('kategori'); // contoh: 'Penting', 'Informasi', 'Selesai'
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
};