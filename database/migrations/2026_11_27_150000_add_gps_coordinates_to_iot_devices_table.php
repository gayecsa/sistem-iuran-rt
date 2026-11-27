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
        Schema::table('iot_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('iot_devices', 'gate_latitude')) {
                $table->decimal('gate_latitude', 10, 7)->nullable()->after('last_uid');
            }
            if (!Schema::hasColumn('iot_devices', 'gate_longitude')) {
                $table->decimal('gate_longitude', 10, 7)->nullable()->after('gate_latitude');
            }
            if (!Schema::hasColumn('iot_devices', 'gate_radius_meters')) {
                $table->unsignedInteger('gate_radius_meters')->default(15)->after('gate_longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iot_devices', function (Blueprint $table) {
            $table->dropColumn(['gate_latitude', 'gate_longitude', 'gate_radius_meters']);
        });
    }
};
