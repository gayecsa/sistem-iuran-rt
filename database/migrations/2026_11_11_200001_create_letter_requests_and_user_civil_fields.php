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
        // 1. Tambahkan kolom biodata kependudukan lengkap pada tabel users jika belum ada
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'agama')) {
                $table->string('agama')->default('Islam')->after('tempat_lahir');
            }
            if (!Schema::hasColumn('users', 'status_perkawinan')) {
                $table->string('status_perkawinan')->default('Kawin')->after('agama');
            }
            if (!Schema::hasColumn('users', 'pekerjaan')) {
                $table->string('pekerjaan')->default('Karyawan Swasta')->after('status_perkawinan');
            }
            if (!Schema::hasColumn('users', 'pendidikan_terakhir')) {
                $table->string('pendidikan_terakhir')->default('S1')->after('pekerjaan');
            }
        });

        // 2. Buat tabel permohonan surat (letter_requests)
        if (!Schema::hasTable('letter_requests')) {
            Schema::create('letter_requests', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_registrasi', 50)->unique(); // e.g. REG-SKD-202611-001
                $table->string('nomor_surat', 100)->nullable()->unique(); // e.g. 470/013/RT.001/SKD/XI/2026
                $table->unsignedBigInteger('user_id')->index(); // Pemohon
                $table->string('rt_number', 10)->default('001')->index();
                $table->string('rw_number', 10)->default('013')->index();
                $table->string('jenis_surat', 50)->index(); // domisili, sktm, usaha, pengantar
                $table->string('judul_surat');

                // Snapshot Biodata Pemohon (Terisi otomatis dari database)
                $table->string('nama_lengkap');
                $table->string('nik', 25)->nullable();
                $table->string('no_kk', 25)->nullable();
                $table->string('tempat_lahir')->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->string('jenis_kelamin', 20)->nullable();
                $table->string('agama', 30)->nullable();
                $table->string('status_perkawinan', 30)->nullable();
                $table->string('pekerjaan', 100)->nullable();
                $table->string('pendidikan_terakhir', 50)->nullable();
                $table->string('phone', 25)->nullable();
                $table->string('email', 100)->nullable();

                // Snapshot Alamat Pemohon (Bandung Raya Sukamiskin Arcamanik)
                $table->string('alamat_jalan');
                $table->string('no_rumah', 20)->nullable();
                $table->string('rt', 10);
                $table->string('rw', 10)->default('013');
                $table->string('kelurahan')->default('Sukamiskin');
                $table->string('kecamatan')->default('Arcamanik');
                $table->string('kota')->default('Kota Bandung');
                $table->string('provinsi')->default('Jawa Barat');
                $table->string('kode_pos', 10)->default('40293');

                // Detail Permohonan
                $table->text('keperluan'); // Tujuan / keperluan pembuatan surat
                $table->json('detail_khusus')->nullable(); // Data spesifik per jenis surat
                $table->text('keterangan_tambahan')->nullable();
                $table->json('dokumen_lampiran')->nullable(); // Array file dokumen pendukung

                // Status Alur Persetujuan Berjenjang RT -> RW
                // Draft, Menunggu Verifikasi RT, Perlu Perbaikan RT, Ditolak RT,
                // Menunggu Persetujuan RW, Perlu Perbaikan RW, Ditolak RW, Siap Dicetak, Selesai
                $table->string('status', 50)->default('Menunggu Verifikasi RT')->index();

                // Verifikasi & Tanda Tangan RT
                $table->unsignedBigInteger('rt_approved_by')->nullable()->index();
                $table->string('rt_approved_name')->nullable();
                $table->timestamp('rt_approved_at')->nullable();
                $table->text('rt_notes')->nullable();
                $table->string('rt_signature_token', 64)->nullable();

                // Verifikasi & Tanda Tangan RW
                $table->unsignedBigInteger('rw_approved_by')->nullable()->index();
                $table->string('rw_approved_name')->nullable();
                $table->timestamp('rw_approved_at')->nullable();
                $table->text('rw_notes')->nullable();
                $table->string('rw_signature_token', 64)->nullable();

                // Catatan Penolakan / Revisi
                $table->text('rejection_reason')->nullable();
                $table->text('revision_notes')->nullable();

                // QR Code Verifikasi Keabsahan Publik
                $table->string('qr_verification_code', 64)->unique();
                $table->timestamp('printed_at')->nullable();

                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('rt_approved_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('rw_approved_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 3. Buat tabel riwayat status surat (letter_status_histories)
        if (!Schema::hasTable('letter_status_histories')) {
            Schema::create('letter_status_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('letter_request_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index(); // Aktor
                $table->string('actor_name')->nullable();
                $table->string('actor_role')->nullable();
                $table->string('status_sebelumnya', 50)->nullable();
                $table->string('status_baru', 50);
                $table->text('catatan')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->foreign('letter_request_id')
                    ->references('id')
                    ->on('letter_requests')
                    ->onDelete('cascade');

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_status_histories');
        Schema::dropIfExists('letter_requests');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tempat_lahir', 'agama', 'status_perkawinan', 'pekerjaan', 'pendidikan_terakhir']);
        });
    }
};
