<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('activity_logs', 'impersonator_id')) {
                    $table->foreignId('impersonator_id')
                        ->nullable()
                        ->after('user_id')
                        ->constrained('users')
                        ->onDelete('set null');
                }

                if (!Schema::hasColumn('activity_logs', 'category')) {
                    $table->string('category', 50)->default('sistem')->after('impersonator_id')->index();
                }

                if (!Schema::hasColumn('activity_logs', 'action')) {
                    $table->string('action', 100)->nullable()->after('category')->index();
                }

                if (!Schema::hasColumn('activity_logs', 'metadata')) {
                    $table->json('metadata')->nullable()->after('description');
                }
            });

            // Backfill existing data if needed
            if (Schema::hasColumn('activity_logs', 'activity') && Schema::hasColumn('activity_logs', 'action')) {
                DB::statement("UPDATE activity_logs SET `action` = `activity` WHERE `action` IS NULL");
            }
        } else {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('impersonator_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('category', 50)->default('sistem')->index();
                $table->string('action', 100)->index();
                $table->string('activity', 100)->nullable();
                $table->text('description')->nullable();
                $table->json('metadata')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index(['category', 'created_at']);
                $table->index(['action', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (Schema::hasColumn('activity_logs', 'impersonator_id')) {
                    $table->dropForeign(['impersonator_id']);
                    $table->dropColumn('impersonator_id');
                }
                if (Schema::hasColumn('activity_logs', 'category')) {
                    $table->dropColumn('category');
                }
                if (Schema::hasColumn('activity_logs', 'action')) {
                    $table->dropColumn('action');
                }
                if (Schema::hasColumn('activity_logs', 'metadata')) {
                    $table->dropColumn('metadata');
                }
            });
        }
    }
};
