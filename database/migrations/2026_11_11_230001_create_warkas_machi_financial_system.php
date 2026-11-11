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
        // 1. Master Buku Kas (Kas RT 001-008, Kas RW, Kas Sampah, Dana Kegiatan)
        if (!Schema::hasTable('buku_kas')) {
            Schema::create('buku_kas', function (Blueprint $table) {
                $table->id();
                $table->string('kode_kas', 50)->unique(); // KAS-RW-013, KAS-RT-001 s/d KAS-RT-008, KAS-SAMPAH, KEG-17AGS, dll
                $table->string('nama_kas', 150);
                $table->enum('jenis_kas', ['kas_rt', 'kas_rw', 'kas_sampah', 'dana_kegiatan']);
                $table->string('rt_number', 10)->nullable(); // 001 - 008, null jika Kas RW / Publik
                $table->decimal('target_anggaran', 15, 2)->default(0); // Khusus dana kegiatan
                $table->decimal('saldo_awal', 15, 2)->default(0);
                $table->decimal('saldo_sekarang', 15, 2)->default(0);
                $table->foreignId('penanggung_jawab_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('nama_penanggung_jawab', 150)->nullable();
                $table->string('no_rekening', 50)->nullable();
                $table->string('nama_bank', 50)->nullable();
                $table->string('atas_nama', 150)->nullable();
                $table->text('deskripsi')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Kategori Transaksi per Jenis Kas
        if (!Schema::hasTable('kategori_transaksis')) {
            Schema::create('kategori_transaksis', function (Blueprint $table) {
                $table->id();
                $table->string('jenis_kas', 50); // kas_rt, kas_rw, kas_sampah, dana_kegiatan
                $table->enum('tipe', ['pemasukan', 'pengeluaran']);
                $table->string('nama_kategori', 100);
                $table->boolean('is_default')->default(true);
                $table->timestamps();
            });
        }

        // 3. Konfigurasi Tarif Iuran
        if (!Schema::hasTable('iuran_tarifs')) {
            Schema::create('iuran_tarifs', function (Blueprint $table) {
                $table->id();
                $table->string('kode_tarif', 50)->unique();
                $table->string('nama_tarif', 100);
                $table->enum('jenis_kas', ['kas_rt', 'kas_rw', 'kas_sampah']);
                $table->decimal('nominal', 15, 2);
                $table->string('periode', 50)->default('bulanan');
                $table->date('berlaku_mulai');
                $table->text('keterangan_musyawarah')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Riwayat Perubahan Tarif Iuran
        if (!Schema::hasTable('iuran_tarif_histories')) {
            Schema::create('iuran_tarif_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('iuran_tarif_id')->constrained('iuran_tarifs')->cascadeOnDelete();
                $table->decimal('nominal_lama', 15, 2);
                $table->decimal('nominal_baru', 15, 2);
                $table->date('tanggal_perubahan');
                $table->foreignId('diubah_oleh_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('alasan_perubahan')->nullable();
                $table->timestamps();
            });
        }

        // 5. Tagihan Per Kartu Keluarga (KK)
        if (!Schema::hasTable('tagihan_keluargas')) {
            Schema::create('tagihan_keluargas', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_tagihan', 50)->unique();
                $table->foreignId('kartu_keluarga_id')->constrained('kartu_keluargas')->cascadeOnDelete();
                $table->foreignId('kepala_keluarga_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('rt_number', 10);
                $table->string('periode', 20); // format YYYY-MM contoh 2026-11
                $table->decimal('nominal_rt', 15, 2)->default(20000);
                $table->decimal('nominal_rw', 15, 2)->default(10000);
                $table->decimal('nominal_sampah', 15, 2)->default(15000);
                $table->decimal('total_tagihan', 15, 2);
                $table->decimal('total_dibayar', 15, 2)->default(0);
                $table->decimal('sisa_tagihan', 15, 2);
                $table->enum('status', [
                    'Belum Dibayar',
                    'Menunggu Konfirmasi',
                    'Dibayar Sebagian',
                    'Lunas',
                    'Terlambat',
                    'Dibebaskan',
                    'Dibatalkan'
                ])->default('Belum Dibayar');
                $table->date('tanggal_terbit');
                $table->date('jatuh_tempo');
                $table->dateTime('tanggal_lunas')->nullable();
                $table->string('metode_pembayaran_terakhir', 50)->nullable();
                $table->string('bukti_pembayaran_path', 255)->nullable();
                $table->text('catatan_warga')->nullable();
                $table->text('catatan_petugas')->nullable();
                $table->text('alasan_pembebasan')->nullable();
                $table->foreignId('diverifikasi_oleh_id')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('diverifikasi_pada')->nullable();
                $table->timestamps();

                // Unique per KK dan Periode untuk mencegah tagihan ganda (idempotency)
                $table->unique(['kartu_keluarga_id', 'periode'], 'unique_tagihan_kk_periode');
                $table->index(['rt_number', 'periode']);
                $table->index('status');
            });
        }

        // 6. Transaksi Keuangan Multi-Kas & Transfer Antar Kas
        if (!Schema::hasTable('transaksi_keuangans')) {
            Schema::create('transaksi_keuangans', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_transaksi', 60)->unique();
                $table->foreignId('buku_kas_id')->constrained('buku_kas')->cascadeOnDelete();
                $table->foreignId('tagihan_keluarga_id')->nullable()->constrained('tagihan_keluargas')->nullOnDelete();
                $table->date('tanggal_transaksi');
                $table->enum('jenis_transaksi', ['pemasukan', 'pengeluaran', 'transfer_masuk', 'transfer_keluar']);
                $table->string('kategori', 100);
                $table->decimal('nominal', 15, 2);
                $table->decimal('saldo_sebelumnya', 15, 2)->default(0);
                $table->decimal('saldo_setelahnya', 15, 2)->default(0);
                $table->enum('metode_pembayaran', ['tunai', 'transfer_bank', 'qris'])->default('tunai');
                $table->string('penyetor_penerima', 150)->nullable();
                $table->text('keterangan')->nullable();
                $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('nama_petugas', 150)->nullable();
                $table->string('bukti_transaksi_path', 255)->nullable();
                $table->string('transfer_ref_id', 60)->nullable()->index(); // ID transaksi pasangan untuk transfer kas
                $table->enum('status', ['selesai', 'dibatalkan'])->default('selesai');
                $table->foreignId('dibatalkan_oleh_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('alasan_batal')->nullable();
                $table->dateTime('dibatalkan_pada')->nullable();
                $table->timestamps();

                $table->index(['buku_kas_id', 'tanggal_transaksi']);
                $table->index('jenis_transaksi');
            });
        }

        // 7. Audit Log Keuangan RW 013
        if (!Schema::hasTable('keuangan_audit_logs')) {
            Schema::create('keuangan_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('transaksi_id')->nullable()->constrained('transaksi_keuangans')->nullOnDelete();
                $table->foreignId('tagihan_id')->nullable()->constrained('tagihan_keluargas')->nullOnDelete();
                $table->string('action', 50); // create_transaksi, cancel_transaksi, transfer_antar_kas, verifikasi_pembayaran, dll
                $table->string('buku_kas_kode', 50)->nullable();
                $table->text('deskripsi');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->timestamps();

                $table->index(['action', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangan_audit_logs');
        Schema::dropIfExists('transaksi_keuangans');
        Schema::dropIfExists('tagihan_keluargas');
        Schema::dropIfExists('iuran_tarif_histories');
        Schema::dropIfExists('iuran_tarifs');
        Schema::dropIfExists('kategori_transaksis');
        Schema::dropIfExists('buku_kas');
    }
};
