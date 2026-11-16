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
        // 1. Peserta Posyandu Terhubung dengan Data Warga
        if (!Schema::hasTable('posyandu_pesertas')) {
            Schema::create('posyandu_pesertas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('kartu_keluarga_id')->nullable()->index();
                $table->string('rt_number', 10)->default('001')->index();
                $table->enum('kategori', ['bayi', 'balita', 'anak', 'ibu_hamil', 'ibu_menyusui', 'lansia'])->default('balita');
                $table->boolean('status_aktif')->default(true);
                $table->string('golongan_darah', 5)->nullable();
                $table->text('riwayat_alergi')->nullable();
                $table->text('catatan_khusus')->nullable();
                $table->date('tanggal_bergabung')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('kartu_keluarga_id')->references('id')->on('kartu_keluargas')->onDelete('set null');
            });
        }

        // 2. Jadwal & Agenda Posyandu RT
        if (!Schema::hasTable('posyandu_jadwals')) {
            Schema::create('posyandu_jadwals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('posyandu_unit_id')->nullable()->index();
                $table->string('rt_number', 10)->default('001')->index();
                $table->string('judul_kegiatan');
                $table->date('tanggal')->index();
                $table->string('jam_mulai', 10)->default('08:00');
                $table->string('jam_selesai', 10)->default('11:00');
                $table->string('lokasi');
                $table->json('jenis_layanan_list')->nullable();
                $table->string('mitra_nakes')->default('UPTD Puskesmas Arcamanik Kota Bandung');
                $table->enum('status', ['akan_datang', 'sedang_berlangsung', 'selesai', 'ditunda', 'dibatalkan'])->default('akan_datang')->index();
                $table->text('alasan_batal')->nullable();
                $table->text('keterangan')->nullable();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();

                $table->foreign('posyandu_unit_id')->references('id')->on('posyandu_kegiatans')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 3. Kehadiran Peserta per Jadwal
        if (!Schema::hasTable('posyandu_kehadirans')) {
            Schema::create('posyandu_kehadirans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('jadwal_id')->index();
                $table->unsignedBigInteger('peserta_id')->index();
                $table->string('rt_number', 10)->default('001')->index();
                $table->enum('status_kehadiran', ['hadir', 'tidak_hadir', 'izin'])->default('hadir');
                $table->string('jam_datang', 10)->nullable();
                $table->unsignedBigInteger('petugas_id')->nullable()->index();
                $table->text('catatan_kunjungan')->nullable();
                $table->timestamps();

                $table->foreign('jadwal_id')->references('id')->on('posyandu_jadwals')->onDelete('cascade');
                $table->foreign('peserta_id')->references('id')->on('posyandu_pesertas')->onDelete('cascade');
                $table->foreign('petugas_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 4. Pencatatan Pengukuran Medis/Fisik
        if (!Schema::hasTable('posyandu_pengukurans')) {
            Schema::create('posyandu_pengukurans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('peserta_id')->index();
                $table->unsignedBigInteger('jadwal_id')->nullable()->index();
                $table->string('rt_number', 10)->default('001')->index();
                $table->date('tanggal_pemeriksaan')->index();
                $table->integer('usia_bulan')->nullable();
                $table->integer('usia_tahun')->nullable();
                $table->decimal('berat_badan', 5, 2)->nullable(); // kg
                $table->decimal('tinggi_badan', 5, 2)->nullable(); // cm
                $table->decimal('lingkar_kepala', 4, 1)->nullable(); // cm
                $table->integer('usia_kehamilan_minggu')->nullable(); // ibu hamil
                $table->integer('tekanan_darah_sistol')->nullable();
                $table->integer('tekanan_darah_diastol')->nullable();
                $table->text('keluhan')->nullable();
                $table->string('status_pertumbuhan')->nullable(); // interpretasi umum nakes
                $table->text('tindakan_layanan')->nullable();
                $table->text('catatan_petugas')->nullable();
                $table->text('anjuran_rujukan')->nullable();
                $table->unsignedBigInteger('petugas_pencatat_id')->nullable()->index();
                $table->timestamps();

                $table->foreign('peserta_id')->references('id')->on('posyandu_pesertas')->onDelete('cascade');
                $table->foreign('jadwal_id')->references('id')->on('posyandu_jadwals')->onDelete('set null');
                $table->foreign('petugas_pencatat_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 5. Data Kader Posyandu RT
        if (!Schema::hasTable('posyandu_kaders')) {
            Schema::create('posyandu_kaders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('rt_number', 10)->default('001')->index();
                $table->string('nama_kader');
                $table->string('no_telepon', 25)->nullable();
                $table->string('jabatan')->default('Kader Pelaksana');
                $table->boolean('is_aktif')->default(true);
                $table->string('foto_path')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 6. Laporan Kegiatan Posyandu RT & Monitoring RW
        if (!Schema::hasTable('posyandu_laporans')) {
            Schema::create('posyandu_laporans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('jadwal_id')->unique()->index();
                $table->string('rt_number', 10)->default('001')->index();
                $table->string('nomor_laporan', 50)->unique();
                $table->date('tanggal_laporan');
                $table->integer('total_hadir')->default(0);
                $table->integer('total_bayi_balita')->default(0);
                $table->integer('total_ibu_hamil')->default(0);
                $table->integer('total_lansia')->default(0);
                $table->text('ringkasan_layanan');
                $table->text('kendala')->nullable();
                $table->text('catatan_evaluasi_rt')->nullable();
                $table->string('foto_dokumentasi_path')->nullable();
                $table->unsignedBigInteger('penanggung_jawab_id')->index();
                $table->enum('status_laporan', ['draft', 'dikirim_ke_rw', 'disetujui_rw', 'dievaluasi_rw'])->default('dikirim_ke_rw')->index();
                $table->text('catatan_rw')->nullable();
                $table->timestamp('tanggal_evaluasi_rw')->nullable();
                $table->unsignedBigInteger('evaluator_rw_id')->nullable()->index();
                $table->timestamps();

                $table->foreign('jadwal_id')->references('id')->on('posyandu_jadwals')->onDelete('cascade');
                $table->foreign('penanggung_jawab_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('evaluator_rw_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posyandu_laporans');
        Schema::dropIfExists('posyandu_kaders');
        Schema::dropIfExists('posyandu_pengukurans');
        Schema::dropIfExists('posyandu_kehadirans');
        Schema::dropIfExists('posyandu_jadwals');
        Schema::dropIfExists('posyandu_pesertas');
    }
};
