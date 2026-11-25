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
        // 1. Tambah Kolom Metode Perhitungan & Kas Tujuan pada iuran_tarifs
        if (Schema::hasTable('iuran_tarifs')) {
            Schema::table('iuran_tarifs', function (Blueprint $table) {
                if (!Schema::hasColumn('iuran_tarifs', 'metode_perhitungan')) {
                    $table->enum('metode_perhitungan', ['per_kk', 'per_individu', 'per_rumah', 'nominal_tetap'])->default('per_kk')->after('nominal');
                }
                if (!Schema::hasColumn('iuran_tarifs', 'buku_kas_id')) {
                    $table->foreignId('buku_kas_id')->nullable()->after('jenis_kas')->constrained('buku_kas')->nullOnDelete();
                }
                if (!Schema::hasColumn('iuran_tarifs', 'rt_target')) {
                    $table->string('rt_target', 10)->default('all')->after('buku_kas_id');
                }
                if (!Schema::hasColumn('iuran_tarifs', 'jenis_iuran')) {
                    $table->enum('jenis_iuran', ['wajib', 'opsional', 'sukarela'])->default('wajib')->after('rt_target');
                }
                if (!Schema::hasColumn('iuran_tarifs', 'tingkat_pengelola')) {
                    $table->enum('tingkat_pengelola', ['rw', 'rt'])->default('rw')->after('jenis_iuran');
                }
                if (!Schema::hasColumn('iuran_tarifs', 'urutan_prioritas')) {
                    $table->integer('urutan_prioritas')->default(1)->after('tingkat_pengelola');
                }
            });
        }

        // 2. Tabel Tagihan Items (Rincian Komponen Invoice Induk)
        if (!Schema::hasTable('tagihan_items')) {
            Schema::create('tagihan_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tagihan_keluarga_id')->constrained('tagihan_keluargas')->cascadeOnDelete();
                $table->foreignId('iuran_tarif_id')->nullable()->constrained('iuran_tarifs')->nullOnDelete();
                $table->string('nama_iuran', 150);
                $table->string('kode_iuran', 50)->nullable();
                $table->enum('metode_perhitungan', ['per_kk', 'per_individu', 'per_rumah', 'nominal_tetap'])->default('per_kk');
                $table->decimal('tarif_satuan', 15, 2);
                $table->integer('kuantitas')->default(1);
                $table->decimal('nominal_subtotal', 15, 2);
                $table->decimal('nominal_diskon', 15, 2)->default(0);
                $table->decimal('nominal_akhir', 15, 2);
                $table->decimal('nominal_terbayar', 15, 2)->default(0);
                $table->decimal('sisa_item', 15, 2)->default(0);
                $table->string('pemilik_dana', 50)->default('RW'); // RW, RT, Unit Kebersihan, Keamanan, Kegiatan
                $table->foreignId('buku_kas_id')->nullable()->constrained('buku_kas')->nullOnDelete();
                $table->enum('status_pembayaran', ['belum_dibayar', 'dibayar_sebagian', 'lunas'])->default('belum_dibayar');
                $table->string('keterangan_snapshot', 255)->nullable();
                $table->timestamps();

                $table->index(['tagihan_keluarga_id', 'status_pembayaran']);
            });
        }

        // 3. Tabel Pembayaran Iurans (Payment Reference Induk)
        if (!Schema::hasTable('pembayaran_iurans')) {
            Schema::create('pembayaran_iurans', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_pembayaran', 50)->unique();
                $table->foreignId('tagihan_keluarga_id')->constrained('tagihan_keluargas')->cascadeOnDelete();
                $table->foreignId('kartu_keluarga_id')->constrained('kartu_keluargas')->cascadeOnDelete();
                $table->dateTime('tanggal_bayar');
                $table->decimal('nominal_bayar', 15, 2);
                $table->string('metode_pembayaran', 50); // tunai, transfer_bank, qris
                $table->enum('kanal_pembayaran', ['langsung_rekening', 'tunai_melalui_rt'])->default('langsung_rekening');
                $table->enum('status_setoran_rw', ['tidak_perlu', 'menunggu_setor_ke_rw', 'sudah_disetor_ke_rw'])->default('tidak_perlu');
                $table->dateTime('disetor_ke_rw_pada')->nullable();
                $table->string('bukti_pembayaran_path', 255)->nullable();
                $table->text('catatan')->nullable();
                $table->foreignId('diterima_oleh_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('nama_penerima', 100)->nullable();
                $table->string('idempotency_key', 64)->nullable()->unique();
                $table->enum('status', ['berhasil', 'dibatalkan'])->default('berhasil');
                $table->text('alasan_batal')->nullable();
                $table->foreignId('dibatalkan_oleh_id')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('dibatalkan_pada')->nullable();
                $table->timestamps();

                $table->index(['tagihan_keluarga_id', 'status']);
                $table->index('tanggal_bayar');
            });
        }

        // 4. Tabel Pembayaran Alokasis (Payment Allocation Breakdown per Cash Account)
        if (!Schema::hasTable('pembayaran_alokasis')) {
            Schema::create('pembayaran_alokasis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pembayaran_iuran_id')->constrained('pembayaran_iurans')->cascadeOnDelete();
                $table->foreignId('tagihan_item_id')->constrained('tagihan_items')->cascadeOnDelete();
                $table->foreignId('buku_kas_id')->constrained('buku_kas')->cascadeOnDelete();
                $table->foreignId('transaksi_keuangan_id')->nullable()->constrained('transaksi_keuangans')->nullOnDelete();
                $table->string('nama_komponen', 150);
                $table->decimal('nominal_alokasi', 15, 2);
                $table->string('pemilik_dana', 50);
                $table->enum('status_alokasi', ['masuk_kas', 'titipan_rt', 'disetor_ke_rw'])->default('masuk_kas');
                $table->timestamps();

                $table->index(['pembayaran_iuran_id', 'buku_kas_id']);
            });
        }

        // 5. Tabel Iuran Keringanans (Bebas Iuran, Diskon, Penundaan)
        if (!Schema::hasTable('iuran_keringanans')) {
            Schema::create('iuran_keringanans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kartu_keluarga_id')->constrained('kartu_keluargas')->cascadeOnDelete();
                $table->foreignId('iuran_tarif_id')->nullable()->constrained('iuran_tarifs')->nullOnDelete();
                $table->enum('jenis_keringanan', ['bebas_iuran', 'diskon_nominal', 'diskon_persen', 'penundaan']);
                $table->decimal('nilai_keringanan', 15, 2)->default(0);
                $table->text('alasan');
                $table->string('periode_mulai', 20); // YYYY-MM
                $table->string('periode_selesai', 20)->nullable(); // YYYY-MM
                $table->string('dokumen_pendukung_path', 255)->nullable();
                $table->foreignId('disetujui_oleh_id')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
                $table->timestamps();

                $table->index(['kartu_keluarga_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iuran_keringanans');
        Schema::dropIfExists('pembayaran_alokasis');
        Schema::dropIfExists('pembayaran_iurans');
        Schema::dropIfExists('tagihan_items');

        if (Schema::hasTable('iuran_tarifs')) {
            Schema::table('iuran_tarifs', function (Blueprint $table) {
                $cols = ['metode_perhitungan', 'buku_kas_id', 'rt_target', 'jenis_iuran', 'tingkat_pengelola', 'urutan_prioritas'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('iuran_tarifs', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }
    }
};
