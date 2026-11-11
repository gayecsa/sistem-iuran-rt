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
        Schema::table('umkm', function (Blueprint $table) {
            if (!Schema::hasColumn('umkm', 'alasan_moderasi')) {
                $table->text('alasan_moderasi')->nullable()->after('catatan_verifikasi');
            }
            if (!Schema::hasColumn('umkm', 'moderated_by')) {
                $table->unsignedBigInteger('moderated_by')->nullable()->after('alasan_moderasi');
                $table->foreign('moderated_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('umkm', 'moderated_at')) {
                $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            }
        });

        if (!Schema::hasTable('umkm_audit_logs')) {
            Schema::create('umkm_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('umkm_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('user_name')->nullable();
                $table->string('role_saat_itu', 50)->nullable();
                $table->string('action', 80)->index();
                $table->text('keterangan')->nullable();
                $table->json('data_lama')->nullable();
                $table->json('data_baru')->nullable();
                $table->timestamps();

                $table->foreign('umkm_id')->references('id')->on('umkm')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkm_audit_logs');
        Schema::table('umkm', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropColumn(['alasan_moderasi', 'moderated_by', 'moderated_at']);
        });
    }
};
