<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('iuran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_iuran');
            $table->text('deskripsi');
            $table->enum('jenis_iuran', ['wajib', 'sukarela', 'kegiatan']);
            $table->decimal('nominal', 10, 2);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('periode', ['bulanan', 'tahunan', 'sekali'])->default('bulanan');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('iuran');
    }
};