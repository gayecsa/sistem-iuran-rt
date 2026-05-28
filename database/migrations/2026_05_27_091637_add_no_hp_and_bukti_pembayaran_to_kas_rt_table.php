<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Blok 1: Hapus kolom lama HANYA JIKA kolom tersebut memang ada
        Schema::table('kas_rt', function (Blueprint $table) {
            if (Schema::hasColumn('kas_rt', 'nik')) {
                $table->dropColumn('nik');
            }
            if (Schema::hasColumn('kas_rt', 'alamat_rumah')) {
                $table->dropColumn('alamat_rumah');
            }
        });

        // Blok 2: Tambahkan kolom baru tanpa 'after' agar tidak memicu error "Column not found"
        Schema::table('kas_rt', function (Blueprint $table) {
            if (!Schema::hasColumn('kas_rt', 'no_hp')) {
                $table->string('no_hp', 20)->nullable();
            }
            if (!Schema::hasColumn('kas_rt', 'bukti_pembayaran')) {
                $table->string('bukti_pembayaran', 255)->nullable();
            }
            
            // ANTISIPASI: Pastikan kolom 'nama_warga' juga ada di tabel ini, karena view index/create kamu membutuhkannya!
            if (!Schema::hasColumn('kas_rt', 'nama_warga')) {
                $table->string('nama_warga', 100)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('kas_rt', function (Blueprint $table) {
            // Rollback yang aman, hapus kolom baru jika ada
            if (Schema::hasColumn('kas_rt', 'no_hp')) {
                $table->dropColumn('no_hp');
            }
            if (Schema::hasColumn('kas_rt', 'bukti_pembayaran')) {
                $table->dropColumn('bukti_pembayaran');
            }
            if (Schema::hasColumn('kas_rt', 'nama_warga')) {
                $table->dropColumn('nama_warga');
            }

            // Kembalikan kolom lama secara aman jika belum ada
            if (!Schema::hasColumn('kas_rt', 'nik')) {
                $table->string('nik', 50)->nullable();
            }
            if (!Schema::hasColumn('kas_rt', 'alamat_rumah')) {
                $table->string('alamat_rumah', 255)->nullable();
            }
        });
    }
};