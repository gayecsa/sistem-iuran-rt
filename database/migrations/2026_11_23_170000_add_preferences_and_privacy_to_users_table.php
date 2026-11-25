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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'privacy_settings')) {
                $table->json('privacy_settings')->nullable()->after('notification_preferences');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('users', 'notification_preferences')) {
                $cols[] = 'notification_preferences';
            }
            if (Schema::hasColumn('users', 'privacy_settings')) {
                $cols[] = 'privacy_settings';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
