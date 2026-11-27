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
        // Ubah kolom status_rumah pada tabel users menjadi VARCHAR(50) agar serasi dengan kartu_keluargas
        // dan dapat menampung opsi: 'Milik Sendiri', 'Kontrak', 'Kos', 'Menumpang', 'Lainnya'
        // tanpa memicu error SQLSTATE[01000]: Data truncated for column 'status_rumah'
        DB::statement("ALTER TABLE users MODIFY COLUMN status_rumah VARCHAR(50) NOT NULL DEFAULT 'Milik Sendiri'");

        // Normalisasi data lama dari enum slug ('milik_sendiri', 'kontrak', 'sewa') ke format standar Title Case
        DB::table('users')->where('status_rumah', 'milik_sendiri')->update(['status_rumah' => 'Milik Sendiri']);
        DB::table('users')->where('status_rumah', 'kontrak')->update(['status_rumah' => 'Kontrak']);
        DB::table('users')->where('status_rumah', 'sewa')->update(['status_rumah' => 'Kontrak']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('status_rumah', 'Milik Sendiri')->update(['status_rumah' => 'milik_sendiri']);
        DB::table('users')->where('status_rumah', 'Kontrak')->update(['status_rumah' => 'kontrak']);
        DB::table('users')->whereNotIn('status_rumah', ['milik_sendiri', 'kontrak', 'sewa'])->update(['status_rumah' => 'milik_sendiri']);

        DB::statement("ALTER TABLE users MODIFY COLUMN status_rumah ENUM('milik_sendiri', 'kontrak', 'sewa') NOT NULL DEFAULT 'milik_sendiri'");
    }
};
