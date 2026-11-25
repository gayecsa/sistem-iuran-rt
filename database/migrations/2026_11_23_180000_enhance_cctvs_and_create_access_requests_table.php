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
        Schema::table('cctvs', function (Blueprint $table) {
            if (!Schema::hasColumn('cctvs', 'rt')) {
                $table->string('rt', 10)->nullable()->after('lokasi');
            }
            if (!Schema::hasColumn('cctvs', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('rt');
            }
            if (!Schema::hasColumn('cctvs', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });

        // Buat tabel permintaan izin akses CCTV
        if (!Schema::hasTable('cctv_access_requests')) {
            Schema::create('cctv_access_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('cctv_id')->constrained('cctvs')->onDelete('cascade');
                $table->text('alasan');
                $table->string('status_rt', 20)->default('pending'); // pending, approved, rejected
                $table->foreignId('approved_rt_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_rt_at')->nullable();
                $table->string('status_rw', 20)->default('pending'); // pending, approved, rejected
                $table->foreignId('approved_rw_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_rw_at')->nullable();
                $table->text('catatan_pengurus')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // Lengkapi data awal wilayah RT dan koordinat untuk 4 titik CCTV RW 013 Sukamiskin Arcamanik
        DB::table('cctvs')->where('id', 1)->update([
            'rt' => '001',
            'latitude' => -6.914200,
            'longitude' => 107.677500,
        ]);

        DB::table('cctvs')->where('id', 2)->update([
            'rt' => '002',
            'latitude' => -6.915100,
            'longitude' => 107.678800,
        ]);

        DB::table('cctvs')->where('id', 3)->update([
            'rt' => '003',
            'latitude' => -6.916300,
            'longitude' => 107.680200,
        ]);

        DB::table('cctvs')->where('id', 4)->update([
            'rt' => '005',
            'latitude' => -6.917200,
            'longitude' => 107.681500,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cctv_access_requests');

        Schema::table('cctvs', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('cctvs', 'rt')) $cols[] = 'rt';
            if (Schema::hasColumn('cctvs', 'latitude')) $cols[] = 'latitude';
            if (Schema::hasColumn('cctvs', 'longitude')) $cols[] = 'longitude';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
