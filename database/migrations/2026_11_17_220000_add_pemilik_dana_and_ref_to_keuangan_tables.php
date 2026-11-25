<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan pemilik_dana ke tabel buku_kas
        if (!Schema::hasColumn('buku_kas', 'pemilik_dana')) {
            Schema::table('buku_kas', function (Blueprint $table) {
                $table->enum('pemilik_dana', ['rw', 'rt', 'unit'])->default('rw')->after('jenis_kas');
            });
        }

        // 2. Tambahkan pemilik_dana dan referensi_pembayaran ke transaksi_keuangans
        Schema::table('transaksi_keuangans', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_keuangans', 'pemilik_dana')) {
                $table->string('pemilik_dana', 50)->default('rw')->after('buku_kas_id');
            }
            if (!Schema::hasColumn('transaksi_keuangans', 'referensi_pembayaran')) {
                $table->string('referensi_pembayaran', 100)->nullable()->after('metode_pembayaran');
            }
        });

        // 3. Backfill pemilik_dana pada buku_kas berdasarkan jenis_kas
        DB::table('buku_kas')->where('jenis_kas', 'kas_rw')->update(['pemilik_dana' => 'rw']);
        DB::table('buku_kas')->where('jenis_kas', 'kas_rt')->update(['pemilik_dana' => 'rt']);
        DB::table('buku_kas')->whereIn('jenis_kas', ['kas_sampah', 'dana_kegiatan', 'kas_sosial', 'kas_keamanan'])->update(['pemilik_dana' => 'unit']);

        // 4. Backfill pemilik_dana pada transaksi_keuangans mengikuti buku_kas terkait
        $allBukuKas = DB::table('buku_kas')->select('id', 'pemilik_dana')->get();
        foreach ($allBukuKas as $bk) {
            DB::table('transaksi_keuangans')
                ->where('buku_kas_id', $bk->id)
                ->update(['pemilik_dana' => $bk->pemilik_dana]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('transaksi_keuangans', 'referensi_pembayaran')) {
            Schema::table('transaksi_keuangans', function (Blueprint $table) {
                $table->dropColumn(['referensi_pembayaran']);
            });
        }

        if (Schema::hasColumn('transaksi_keuangans', 'pemilik_dana')) {
            Schema::table('transaksi_keuangans', function (Blueprint $table) {
                $table->dropColumn(['pemilik_dana']);
            });
        }

        if (Schema::hasColumn('buku_kas', 'pemilik_dana')) {
            Schema::table('buku_kas', function (Blueprint $table) {
                $table->dropColumn(['pemilik_dana']);
            });
        }
    }
};
