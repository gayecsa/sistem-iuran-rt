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
        // 1. Tabel Kartu Keluarga Resmi
        if (!Schema::hasTable('kartu_keluargas')) {
            Schema::create('kartu_keluargas', function (Blueprint $table) {
                $table->id();
                $table->string('no_kk', 20)->unique()->index();
                $table->unsignedBigInteger('kepala_keluarga_id')->nullable()->index();
                $table->string('nama_kepala_keluarga', 150);
                $table->string('nik_kepala_keluarga', 20)->nullable()->index();
                $table->text('alamat')->nullable();
                $table->string('rt', 10)->default('001')->index();
                $table->string('rw', 10)->default('013')->index();
                $table->string('blok', 20)->nullable();
                $table->string('gang_jalan', 100)->nullable();
                $table->string('nomor_rumah', 20)->nullable();
                $table->string('kelurahan', 100)->default('Sukamiskin');
                $table->string('kecamatan', 100)->default('Arcamanik');
                $table->string('kota', 100)->default('Kota Bandung');
                $table->string('provinsi', 100)->default('Jawa Barat');
                $table->string('kode_pos', 10)->default('40293');
                $table->string('status_rumah', 50)->default('Milik Sendiri'); // Milik Sendiri, Kontrak, Kos, Menumpang, Lainnya
                
                // Dokumen KK Resmi Dukcapil (Private Storage)
                $table->string('dokumen_kk_path', 255)->nullable();
                $table->string('original_filename', 255)->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->string('mime_type', 50)->nullable();
                $table->date('tgl_diterbitkan_dukcapil')->nullable();
                $table->string('sumber_dokumen', 100)->default('Salinan Resmi Dukcapil');
                $table->integer('versi_dokumen')->default(1);
                
                // Status Verifikasi Dukcapil & Status Dokumen
                // 'Belum Ada Dokumen', 'Menunggu Verifikasi', 'Terverifikasi', 'Tidak Valid', 'Perlu Pembaruan', 'Dicabut atau Diganti'
                $table->string('status_verifikasi_dukcapil', 50)->default('Belum Ada Dokumen')->index();
                // 'Aktif', 'Perlu Pembaruan', 'Nonaktif'
                $table->string('status_dokumen', 50)->default('Aktif')->index();
                $table->text('catatan_verifikasi_dukcapil')->nullable();
                $table->string('metode_verifikasi', 100)->nullable(); // e.g. "Pemeriksaan Fisik & Legalitas Dokumen Asli Dukcapil", "Verifikasi Portal Resmi Dukcapil"
                $table->unsignedBigInteger('verified_by')->nullable()->index();
                $table->timestamp('verified_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();
            });
        }

        // 2. Tambah Kolom Kependudukan Lanjutan pada Tabel Users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'kartu_keluarga_id')) {
                $table->unsignedBigInteger('kartu_keluarga_id')->nullable()->index()->after('no_kk');
            }
            if (!Schema::hasColumn('users', 'hubungan_keluarga')) {
                $table->string('hubungan_keluarga', 50)->default('Kepala Keluarga')->after('kartu_keluarga_id');
            }
            if (!Schema::hasColumn('users', 'status_domisili')) {
                $table->string('status_domisili', 50)->default('Warga Tetap')->index()->after('hubungan_keluarga');
            }
            if (!Schema::hasColumn('users', 'status_verifikasi')) {
                $table->string('status_verifikasi', 50)->default('Terverifikasi RT')->index()->after('status_domisili');
            }
            if (!Schema::hasColumn('users', 'kewarganegaraan')) {
                $table->string('kewarganegaraan', 20)->default('WNI')->after('pekerjaan');
            }
            if (!Schema::hasColumn('users', 'blok_rumah')) {
                $table->string('blok_rumah', 20)->nullable()->after('house_number');
            }
            if (!Schema::hasColumn('users', 'gang_jalan')) {
                $table->string('gang_jalan', 100)->nullable()->after('blok_rumah');
            }
            if (!Schema::hasColumn('users', 'dokumen_ktp_path')) {
                $table->string('dokumen_ktp_path', 255)->nullable()->after('status_rumah');
            }
            if (!Schema::hasColumn('users', 'dokumen_pendukung_path')) {
                $table->string('dokumen_pendukung_path', 255)->nullable()->after('dokumen_ktp_path');
            }
        });

        // 3. Tabel Riwayat Audit Perubahan Status Warga & Verifikasi
        if (!Schema::hasTable('warga_status_histories')) {
            Schema::create('warga_status_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('kartu_keluarga_id')->nullable()->index();
                $table->string('tipe_perubahan', 50); // status_verifikasi, status_domisili, hubungan_keluarga, dokumen_ktp, dokumen_kk, akun
                $table->string('status_lama', 100)->nullable();
                $table->string('status_baru', 100);
                $table->unsignedBigInteger('petugas_id')->nullable();
                $table->string('petugas_nama', 150)->nullable();
                $table->string('petugas_role', 50)->nullable();
                $table->text('catatan')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 4. Tabel Riwayat Verifikasi Arsip KK Dukcapil
        if (!Schema::hasTable('kk_verifikasi_histories')) {
            Schema::create('kk_verifikasi_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('kartu_keluarga_id')->index();
                $table->string('status_lama', 100)->nullable();
                $table->string('status_baru', 100);
                $table->string('metode_verifikasi', 100)->nullable();
                $table->unsignedBigInteger('petugas_id')->nullable();
                $table->string('petugas_nama', 150)->nullable();
                $table->string('petugas_role', 50)->nullable();
                $table->text('catatan')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kk_verifikasi_histories');
        Schema::dropIfExists('warga_status_histories');
        
        Schema::table('users', function (Blueprint $table) {
            $cols = [
                'kartu_keluarga_id', 'hubungan_keluarga', 'status_domisili', 
                'status_verifikasi', 'kewarganegaraan', 'blok_rumah', 
                'gang_jalan', 'dokumen_ktp_path', 'dokumen_pendukung_path'
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('users', $c)) {
                    $table->dropColumn($c);
                }
            }
        });

        Schema::dropIfExists('kartu_keluargas');
    }
};
