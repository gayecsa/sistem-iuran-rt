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
        Schema::table('umkm', function (Blueprint $table) {
            if (!Schema::hasColumn('umkm', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id')->index();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('umkm', 'kategori')) {
                $table->string('kategori')->default('Kuliner Khas Bandung')->after('jenis_usaha');
            }
            if (!Schema::hasColumn('umkm', 'rt_number')) {
                $table->string('rt_number', 10)->default('001')->after('alamat');
            }
            if (!Schema::hasColumn('umkm', 'rw_number')) {
                $table->string('rw_number', 10)->default('013')->after('rt_number');
            }
            if (!Schema::hasColumn('umkm', 'kelurahan')) {
                $table->string('kelurahan')->default('Sukamiskin')->after('rw_number');
            }
            if (!Schema::hasColumn('umkm', 'kecamatan')) {
                $table->string('kecamatan')->default('Arcamanik')->after('kelurahan');
            }
            if (!Schema::hasColumn('umkm', 'kota')) {
                $table->string('kota')->default('Kota Bandung')->after('kecamatan');
            }
            if (!Schema::hasColumn('umkm', 'status_usaha')) {
                $table->string('status_usaha')->default('Buka')->after('foto');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('umkm', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id', 'kategori', 'rt_number', 'rw_number',
                'kelurahan', 'kecamatan', 'kota', 'status_usaha'
            ]);
        });
    }
};
