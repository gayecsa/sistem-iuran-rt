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
        Schema::table('pengaduans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaduans', 'rt_checked_by')) {
                $table->unsignedBigInteger('rt_checked_by')->nullable()->after('catatan_tindak_lanjut');
                $table->foreign('rt_checked_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('pengaduans', 'rt_checked_at')) {
                $table->timestamp('rt_checked_at')->nullable()->after('rt_checked_by');
            }
            if (!Schema::hasColumn('pengaduans', 'rt_check_note')) {
                $table->text('rt_check_note')->nullable()->after('rt_checked_at');
            }
            if (!Schema::hasColumn('pengaduans', 'rw_resolved_by')) {
                $table->unsignedBigInteger('rw_resolved_by')->nullable()->after('rt_check_note');
                $table->foreign('rw_resolved_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('pengaduans', 'rw_resolved_at')) {
                $table->timestamp('rw_resolved_at')->nullable()->after('rw_resolved_by');
            }
            if (!Schema::hasColumn('pengaduans', 'rw_resolve_note')) {
                $table->text('rw_resolve_note')->nullable()->after('rw_resolved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduans', function (Blueprint $table) {
            if (Schema::hasColumn('pengaduans', 'rt_checked_by')) {
                $table->dropForeign(['rt_checked_by']);
                $table->dropColumn('rt_checked_by');
            }
            if (Schema::hasColumn('pengaduans', 'rt_checked_at')) {
                $table->dropColumn('rt_checked_at');
            }
            if (Schema::hasColumn('pengaduans', 'rt_check_note')) {
                $table->dropColumn('rt_check_note');
            }
            if (Schema::hasColumn('pengaduans', 'rw_resolved_by')) {
                $table->dropForeign(['rw_resolved_by']);
                $table->dropColumn('rw_resolved_by');
            }
            if (Schema::hasColumn('pengaduans', 'rw_resolved_at')) {
                $table->dropColumn('rw_resolved_at');
            }
            if (Schema::hasColumn('pengaduans', 'rw_resolve_note')) {
                $table->dropColumn('rw_resolve_note');
            }
        });
    }
};
