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
        // 1. Kategori & Tarif Sampah Terpadu
        if (!Schema::hasTable('bank_sampah_kategoris')) {
            Schema::create('bank_sampah_kategoris', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kategori'); // e.g. Plastik PET, Kardus
                $table->string('slug')->unique();
                $table->string('satuan', 20)->default('kg'); // kg, liter, pcs
                $table->decimal('harga_per_satuan', 12, 2)->default(0);
                $table->text('deskripsi')->nullable();
                $table->text('petunjuk_pemilahan')->nullable();
                $table->string('badge_color', 50)->default('emerald');
                $table->string('icon', 50)->default('recycle');
                $table->boolean('is_aktif')->default(true);
                $table->integer('urutan')->default(0);
                $table->timestamps();
            });
        }

        // 2. Riwayat Perubahan Tarif Harga Sampah (Audit Trail)
        if (!Schema::hasTable('bank_sampah_tarif_histories')) {
            Schema::create('bank_sampah_tarif_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('kategori_id')->index();
                $table->decimal('harga_lama', 12, 2);
                $table->decimal('harga_baru', 12, 2);
                $table->unsignedBigInteger('diubah_oleh_id')->nullable()->index();
                $table->string('alasan_perubahan');
                $table->timestamps();

                $table->foreign('kategori_id')->references('id')->on('bank_sampah_kategoris')->onDelete('cascade');
                $table->foreign('diubah_oleh_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 3. Transaksi Pengajuan & Penimbangan Sampah
        if (!Schema::hasTable('bank_sampah_transaksis')) {
            Schema::create('bank_sampah_transaksis', function (Blueprint $table) {
                $table->id();
                $table->string('kode_transaksi', 40)->unique();
                $table->unsignedBigInteger('user_id')->index(); // Warga yang menyetor
                $table->unsignedBigInteger('bank_sampah_id')->nullable()->index(); // Unit Bank Sampah RT
                $table->string('rt_number', 10)->default('001')->index();
                $table->unsignedBigInteger('petugas_id')->nullable()->index(); // Petugas penimbang
                $table->enum('status', [
                    'menunggu',             // Baru diajukan warga
                    'dijadwalkan',          // Disetujui petugas, ada tanggal serah terima
                    'sedang_ditimbang',      // Sedang proses timbang di drop point
                    'menunggu_konfirmasi',  // Hasil timbang diinput, menunggu verifikasi akhir
                    'selesai',              // Selesai & saldo masuk ke tabungan
                    'ditolak',              // Ditolak petugas dengan alasan
                    'dibatalkan'            // Dibatalkan oleh warga
                ])->default('menunggu')->index();
                $table->enum('metode_penyerahan', ['drop_point', 'jemput_ke_rumah'])->default('drop_point');
                $table->dateTime('tanggal_pengajuan');
                $table->dateTime('tanggal_dijadwalkan')->nullable();
                $table->dateTime('tanggal_penimbangan')->nullable();
                $table->dateTime('tanggal_selesai')->nullable();
                $table->text('catatan_warga')->nullable();
                $table->text('catatan_petugas')->nullable();
                $table->text('alasan_penolakan')->nullable();
                $table->decimal('total_berat_kg', 10, 2)->default(0);
                $table->decimal('total_rupiah', 15, 2)->default(0);
                $table->boolean('is_simulasi')->default(false); // Membedakan data simulasi dengan transaksi riil
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('bank_sampah_id')->references('id')->on('bank_sampahs')->onDelete('set null');
                $table->foreign('petugas_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 4. Detail Item Jenis Sampah per Transaksi
        if (!Schema::hasTable('bank_sampah_transaksi_items')) {
            Schema::create('bank_sampah_transaksi_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('transaksi_id')->index();
                $table->unsignedBigInteger('kategori_id')->index();
                $table->decimal('berat_satuan', 10, 2)->default(0);
                $table->string('satuan', 20)->default('kg');
                $table->decimal('harga_per_satuan', 12, 2)->default(0);
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->string('catatan')->nullable();
                $table->timestamps();

                $table->foreign('transaksi_id')->references('id')->on('bank_sampah_transaksis')->onDelete('cascade');
                $table->foreign('kategori_id')->references('id')->on('bank_sampah_kategoris')->onDelete('cascade');
            });
        }

        // 5. Rekening / Saldo Tabungan Sampah Warga
        if (!Schema::hasTable('bank_sampah_saldos')) {
            Schema::create('bank_sampah_saldos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->decimal('total_kg_terkumpul', 12, 2)->default(0);
                $table->decimal('saldo_aktif', 15, 2)->default(0);
                $table->decimal('total_penarikan', 15, 2)->default(0);
                $table->decimal('total_dihasilkan', 15, 2)->default(0);
                $table->dateTime('transaksi_terakhir_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 6. Buku Kas / Ledger Tabungan Sampah Warga
        if (!Schema::hasTable('bank_sampah_ledgers')) {
            Schema::create('bank_sampah_ledgers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('transaksi_id')->nullable()->index();
                $table->enum('tipe', ['setoran', 'penarikan', 'koreksi'])->default('setoran');
                $table->decimal('debit', 15, 2)->default(0);   // Dana masuk ke tabungan
                $table->decimal('kredit', 15, 2)->default(0);  // Dana keluar / ditarik
                $table->decimal('saldo_sebelum', 15, 2)->default(0);
                $table->decimal('saldo_sesudah', 15, 2)->default(0);
                $table->string('keterangan');
                $table->unsignedBigInteger('petugas_id')->nullable()->index();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('transaksi_id')->references('id')->on('bank_sampah_transaksis')->onDelete('set null');
                $table->foreign('petugas_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 7. Tambahkan kolom status simulasi & catatan pada bank_sampahs jika belum ada
        Schema::table('bank_sampahs', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_sampahs', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('total_terkumpul_kg');
            }
            if (!Schema::hasColumn('bank_sampahs', 'keterangan_simulasi')) {
                $table->string('keterangan_simulasi')->default('Data Simulasi Awal Periode Demo')->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_sampah_ledgers');
        Schema::dropIfExists('bank_sampah_saldos');
        Schema::dropIfExists('bank_sampah_transaksi_items');
        Schema::dropIfExists('bank_sampah_transaksis');
        Schema::dropIfExists('bank_sampah_tarif_histories');
        Schema::dropIfExists('bank_sampah_kategoris');

        Schema::table('bank_sampahs', function (Blueprint $table) {
            if (Schema::hasColumn('bank_sampahs', 'keterangan_simulasi')) {
                $table->dropColumn('keterangan_simulasi');
            }
            if (Schema::hasColumn('bank_sampahs', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
