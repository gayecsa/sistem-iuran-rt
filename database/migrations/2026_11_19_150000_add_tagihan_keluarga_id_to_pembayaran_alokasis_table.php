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
        if (Schema::hasTable('pembayaran_alokasis')) {
            Schema::table('pembayaran_alokasis', function (Blueprint $table) {
                if (!Schema::hasColumn('pembayaran_alokasis', 'tagihan_keluarga_id')) {
                    $table->foreignId('tagihan_keluarga_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('tagihan_keluargas')
                        ->cascadeOnDelete();
                    
                    $table->index(['tagihan_keluarga_id', 'status_alokasi']);
                }
            });

            // Backfill data existing di pembayaran_alokasis dari tagihan_items atau pembayaran_iurans
            DB::statement("
                UPDATE pembayaran_alokasis pa
                INNER JOIN tagihan_items ti ON pa.tagihan_item_id = ti.id
                SET pa.tagihan_keluarga_id = ti.tagihan_keluarga_id
                WHERE pa.tagihan_keluarga_id IS NULL
            ");

            DB::statement("
                UPDATE pembayaran_alokasis pa
                INNER JOIN pembayaran_iurans pi ON pa.pembayaran_iuran_id = pi.id
                SET pa.tagihan_keluarga_id = pi.tagihan_keluarga_id
                WHERE pa.tagihan_keluarga_id IS NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pembayaran_alokasis')) {
            Schema::table('pembayaran_alokasis', function (Blueprint $table) {
                if (Schema::hasColumn('pembayaran_alokasis', 'tagihan_keluarga_id')) {
                    $table->dropForeign(['tagihan_keluarga_id']);
                    $table->dropColumn('tagihan_keluarga_id');
                }
            });
        }
    }
};
