<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'bendahara', 'warga'])->default('warga');
            $table->string('rt_number')->default('001');
            $table->string('house_number')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('status_rumah', ['milik_sendiri', 'kontrak', 'sewa'])->default('milik_sendiri');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'rt_number', 'house_number', 'phone', 'address', 'status_rumah']);
        });
    }
};