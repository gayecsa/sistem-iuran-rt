<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_kk', 16)->nullable()->after('phone');
            $table->string('nik', 16)->nullable()->after('no_kk');
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable()->after('nik');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_kk', 'nik', 'gender']);
        });
    }
};