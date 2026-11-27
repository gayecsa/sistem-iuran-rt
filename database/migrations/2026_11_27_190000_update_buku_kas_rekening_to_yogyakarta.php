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
        if (Schema::hasTable('buku_kas')) {
            // Update atas_nama and deskripsi in buku_kas table
            DB::statement("
                UPDATE buku_kas 
                SET atas_nama = REPLACE(
                    REPLACE(atas_nama, 'SUKAMISKIN', 'SURYATMAJAN'),
                    'BANDUNG', 'YOGYAKARTA'
                )
                WHERE atas_nama LIKE '%SUKAMISKIN%' OR atas_nama LIKE '%BANDUNG%'
            ");

            DB::statement("
                UPDATE buku_kas 
                SET deskripsi = REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(deskripsi, 'Sukamiskin', 'Suryatmajan'),
                            'Arcamanik', 'Danurejan'
                        ),
                        'Kota Bandung', 'Kota Yogyakarta'
                    ),
                    'Bandung', 'Yogyakarta'
                )
                WHERE deskripsi LIKE '%Sukamiskin%' 
                   OR deskripsi LIKE '%Arcamanik%' 
                   OR deskripsi LIKE '%Bandung%'
            ");

            DB::statement("
                UPDATE buku_kas 
                SET nama_kas = REPLACE(
                    REPLACE(nama_kas, 'Sukamiskin', 'Suryatmajan'),
                    'Bandung', 'Yogyakarta'
                )
                WHERE nama_kas LIKE '%Sukamiskin%' OR nama_kas LIKE '%Bandung%'
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert not required
    }
};
