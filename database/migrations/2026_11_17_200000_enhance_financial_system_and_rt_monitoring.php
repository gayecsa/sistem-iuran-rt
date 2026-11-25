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
        // 1. Perkaya transaksi_keuangans dengan sumber dana, tujuan penggunaan, dan audit edit
        Schema::table('transaksi_keuangans', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_keuangans', 'sumber_dana')) {
                $table->string('sumber_dana', 150)->nullable()->after('kategori');
            }
            if (!Schema::hasColumn('transaksi_keuangans', 'tujuan_penggunaan')) {
                $table->string('tujuan_penggunaan', 150)->nullable()->after('sumber_dana');
            }
            if (!Schema::hasColumn('transaksi_keuangans', 'alasan_edit')) {
                $table->text('alasan_edit')->nullable()->after('alasan_batal');
            }
            if (!Schema::hasColumn('transaksi_keuangans', 'edited_by_id')) {
                $table->foreignId('edited_by_id')->nullable()->after('alasan_edit')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('transaksi_keuangans', 'edited_at')) {
                $table->dateTime('edited_at')->nullable()->after('edited_by_id');
            }
        });

        // 2. Perkaya audit_evaluasi_rts untuk status verifikasi laporan keuangan RT oleh Ketua RW
        Schema::table('audit_evaluasi_rts', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_evaluasi_rts', 'status_laporan')) {
                $table->string('status_laporan', 50)->default('Terkirim')->after('status_tindak_lanjut');
            }
            if (!Schema::hasColumn('audit_evaluasi_rts', 'status_verifikasi_rw')) {
                $table->string('status_verifikasi_rw', 50)->default('Menunggu Pemeriksaan')->after('status_laporan');
            }
            if (!Schema::hasColumn('audit_evaluasi_rts', 'diverifikasi_rw_pada')) {
                $table->dateTime('diverifikasi_rw_pada')->nullable()->after('status_verifikasi_rw');
            }
            if (!Schema::hasColumn('audit_evaluasi_rts', 'catatan_verifikasi_rw')) {
                $table->text('catatan_verifikasi_rw')->nullable()->after('diverifikasi_rw_pada');
            }
            if (!Schema::hasColumn('audit_evaluasi_rts', 'laporan_dikunci')) {
                $table->boolean('laporan_dikunci')->default(false)->after('catatan_verifikasi_rw');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_keuangans', function (Blueprint $table) {
            $table->dropForeign(['edited_by_id']);
            $table->dropColumn([
                'sumber_dana',
                'tujuan_penggunaan',
                'alasan_edit',
                'edited_by_id',
                'edited_at',
            ]);
        });

        Schema::table('audit_evaluasi_rts', function (Blueprint $table) {
            $table->dropColumn([
                'status_laporan',
                'status_verifikasi_rw',
                'diverifikasi_rw_pada',
                'catatan_verifikasi_rw',
                'laporan_dikunci',
            ]);
        });
    }
};
