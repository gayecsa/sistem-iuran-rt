<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE iuran_tarifs MODIFY COLUMN jenis_kas VARCHAR(50) NOT NULL DEFAULT 'kas_rw'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE iuran_tarifs MODIFY COLUMN jenis_kas ENUM('kas_rt','kas_rw','kas_sampah') NOT NULL DEFAULT 'kas_rw'");
    }
};
