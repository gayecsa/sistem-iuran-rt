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
        // 1. Buat Tabel Master Jenis & Template Surat (letter_types)
        if (!Schema::hasTable('letter_types')) {
            Schema::create('letter_types', function (Blueprint $table) {
                $table->id();
                $table->string('kategori', 50)->index(); // kependudukan, sosial_keluarga, usaha_ekonomi, lingkungan_keamanan, internal
                $table->string('kode', 60)->unique(); // domisili, sktm, sku, etc.
                $table->string('nama', 150);
                $table->string('singkatan', 30)->nullable();
                $table->text('deskripsi')->nullable();
                $table->string('tujuan')->nullable();
                $table->json('persyaratan')->nullable(); // Array string persyaratan berkas
                $table->json('form_fields')->nullable(); // Schema dinamis field form khusus
                $table->string('alur_persetujuan', 40)->default('rt_then_rw')->index(); // rt_then_rw, rt_only, pengantar_kelurahan, internal_rw
                $table->string('biaya', 50)->default('Gratis (Rp 0)');
                $table->string('estimasi_hari', 50)->default('1 - 2 Hari Kerja');
                $table->string('kode_klasifikasi', 20)->default('470');
                $table->longText('template_body')->nullable(); // Template kalimat resmi surat
                $table->boolean('is_active')->default(true)->index();
                $table->integer('urutan')->default(1);
                $table->timestamps();
            });
        }

        // 2. Tambah foreign / relasi di tabel letter_requests
        if (Schema::hasTable('letter_requests')) {
            Schema::table('letter_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('letter_requests', 'letter_type_id')) {
                    $table->unsignedBigInteger('letter_type_id')->nullable()->after('jenis_surat')->index();
                }
                if (!Schema::hasColumn('letter_requests', 'alur_persetujuan')) {
                    $table->string('alur_persetujuan', 40)->default('rt_then_rw')->after('letter_type_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('letter_requests')) {
            Schema::table('letter_requests', function (Blueprint $table) {
                if (Schema::hasColumn('letter_requests', 'letter_type_id')) {
                    $table->dropColumn('letter_type_id');
                }
                if (Schema::hasColumn('letter_requests', 'alur_persetujuan')) {
                    $table->dropColumn('alur_persetujuan');
                }
            });
        }

        Schema::dropIfExists('letter_types');
    }
};
