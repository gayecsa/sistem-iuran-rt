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
        // 1. Upgrade tabel UMKM dengan status verifikasi, keaktifan, dan kisaran harga
        Schema::table('umkm', function (Blueprint $table) {
            if (!Schema::hasColumn('umkm', 'status_verifikasi')) {
                $table->string('status_verifikasi', 30)->default('diverifikasi')->after('status_usaha');
            }
            if (!Schema::hasColumn('umkm', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status_verifikasi');
            }
            if (!Schema::hasColumn('umkm', 'harga_mulai')) {
                $table->unsignedInteger('harga_mulai')->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('umkm', 'harga_sampai')) {
                $table->unsignedInteger('harga_sampai')->default(0)->after('harga_mulai');
            }
            if (!Schema::hasColumn('umkm', 'catatan_verifikasi')) {
                $table->text('catatan_verifikasi')->nullable()->after('harga_sampai');
            }
        });

        // 2. Buat tabel produk / menu UMKM
        if (!Schema::hasTable('umkm_produks')) {
            Schema::create('umkm_produks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('umkm_id');
                $table->string('nama_produk');
                $table->string('kategori_produk', 100)->nullable();
                $table->unsignedInteger('harga')->default(0);
                $table->text('deskripsi')->nullable();
                $table->string('foto')->nullable();
                $table->boolean('is_tersedia')->default(true);
                $table->boolean('is_best_seller')->default(false);
                $table->timestamps();

                $table->foreign('umkm_id')->references('id')->on('umkm')->onDelete('cascade');
                $table->index(['umkm_id', 'is_tersedia']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkm_produks');
        Schema::table('umkm', function (Blueprint $table) {
            $table->dropColumn([
                'status_verifikasi',
                'is_active',
                'harga_mulai',
                'harga_sampai',
                'catatan_verifikasi'
            ]);
        });
    }
};
