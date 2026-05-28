<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kas_rt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->nullable()->constrained('pembayaran')->onDelete('set null');
            $table->decimal('pemasukan', 10, 2)->default(0);
            $table->decimal('pengeluaran', 10, 2)->default(0);
            $table->text('keterangan');
            $table->string('kategori');
            $table->date('tanggal_transaksi');
            $table->string('dibuat_oleh');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kas_rt');
    }
};