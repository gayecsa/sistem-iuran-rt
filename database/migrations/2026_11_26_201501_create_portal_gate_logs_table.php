<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_gate_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('rfid_uid', 50)->index();
            $table->string('device_id', 50)->default('GATE-01')->index();
            $table->enum('mode', ['portal', 'ronda', 'bank_sampah', 'register'])->default('portal')->index();
            $table->enum('status', ['granted', 'denied'])->default('denied')->index();
            $table->string('reason', 150)->nullable();
            $table->timestamp('tapped_at')->useCurrent()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_gate_logs');
    }
};

