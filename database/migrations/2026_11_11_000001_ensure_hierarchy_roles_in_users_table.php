<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom role menjadi string varchar(50) agar mendukung role: admin, rw, rt, warga, bendahara, super_admin
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'warga'");
        } catch (\Throwable $e) {
            // Fallback Laravel Schema jika DB raw gagal
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 50)->default('warga')->change();
            });
        }
    }

    public function down(): void
    {
        // Tidak perlu revert ke enum terbatas untuk menjaga kompatibilitas data
    }
};
