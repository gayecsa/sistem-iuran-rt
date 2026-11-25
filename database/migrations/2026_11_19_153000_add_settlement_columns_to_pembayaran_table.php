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
        if (Schema::hasTable('pembayaran_iurans')) {
            Schema::table('pembayaran_iurans', function (Blueprint $table) {
                if (!Schema::hasColumn('pembayaran_iurans', 'gateway_provider')) {
                    $table->string('gateway_provider', 50)->default('manual')->after('metode_pembayaran');
                }
                if (!Schema::hasColumn('pembayaran_iurans', 'gateway_reference')) {
                    $table->string('gateway_reference', 100)->nullable()->after('gateway_provider');
                }
                if (!Schema::hasColumn('pembayaran_iurans', 'settlement_status')) {
                    $table->enum('settlement_status', ['pending', 'dalam_proses', 'settled', 'failed'])->default('settled')->after('status');
                }
            });
        }

        if (Schema::hasTable('pembayaran_alokasis')) {
            Schema::table('pembayaran_alokasis', function (Blueprint $table) {
                if (!Schema::hasColumn('pembayaran_alokasis', 'settlement_status')) {
                    $table->enum('settlement_status', ['pending', 'dalam_proses', 'settled', 'failed'])->default('settled')->after('status_alokasi');
                }
                if (!Schema::hasColumn('pembayaran_alokasis', 'settlement_reference')) {
                    $table->string('settlement_reference', 100)->nullable()->after('settlement_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pembayaran_alokasis')) {
            Schema::table('pembayaran_alokasis', function (Blueprint $table) {
                $cols = ['settlement_status', 'settlement_reference'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('pembayaran_alokasis', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }

        if (Schema::hasTable('pembayaran_iurans')) {
            Schema::table('pembayaran_iurans', function (Blueprint $table) {
                $cols = ['gateway_provider', 'gateway_reference', 'settlement_status'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('pembayaran_iurans', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }
    }
};
