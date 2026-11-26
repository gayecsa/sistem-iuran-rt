<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iot_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_code', 50)->unique();
            $table->string('name', 100)->default('Portal & Reader Utama RW 013');
            $table->enum('mode', ['portal', 'ronda', 'bank_sampah', 'register'])->default('portal')->index();
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->string('last_uid', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_devices');
    }
};

