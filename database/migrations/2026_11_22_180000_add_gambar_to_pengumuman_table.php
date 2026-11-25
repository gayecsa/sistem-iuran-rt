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
        if (Schema::hasTable('pengumuman') && !Schema::hasColumn('pengumuman', 'gambar')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->string('gambar')->nullable()->after('kategori');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pengumuman') && Schema::hasColumn('pengumuman', 'gambar')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $table->dropColumn('gambar');
            });
        }
    }
};
