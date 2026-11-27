<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BukuKas;
use App\Models\IuranTarif;
use App\Models\KartuKeluarga;
use App\Models\TagihanKeluarga;
use App\Models\TagihanItem;
use App\Models\PembayaranIuran;
use App\Models\PembayaranAlokasi;
use App\Models\TransaksiKeuangan;
use Carbon\Carbon;

class RealisticFiveMonthFinanceSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // 1. STANDARISASI MASTER TARIF ONE BILLING (iuran_tarifs) -> RP 50.000 / KK
        // =========================================================================
        $tarifs = [
            'TARIF-RT' => [
                'nama_tarif' => 'Iuran Kas Operasional RT',
                'nominal' => 20000,
                'jenis_kas' => 'kas_rt',
                'buku_kas_id' => null,
                'metode_perhitungan' => 'per_kk',
                'periode' => 'bulanan',
                'jenis_iuran' => 'wajib',
                'tingkat_pengelola' => 'rt',
                'urutan_prioritas' => 1,
                'is_active' => true,
            ],
            'TARIF-RW' => [
                'nama_tarif' => 'Iuran Kas Induk RW 013',
                'nominal' => 10000,
                'jenis_kas' => 'kas_rw',
                'buku_kas_id' => 1, // KAS-RW-013
                'metode_perhitungan' => 'per_kk',
                'periode' => 'bulanan',
                'jenis_iuran' => 'wajib',
                'tingkat_pengelola' => 'rw',
                'urutan_prioritas' => 2,
                'is_active' => true,
            ],
            'TARIF-SAMPAH' => [
                'nama_tarif' => 'Iuran Pengelolaan Sampah & Kebersihan',
                'nominal' => 15000,
                'jenis_kas' => 'kas_sampah',
                'buku_kas_id' => 2, // KAS-SAMPAH
                'metode_perhitungan' => 'per_kk',
                'periode' => 'bulanan',
                'jenis_iuran' => 'wajib',
                'tingkat_pengelola' => 'rw',
                'urutan_prioritas' => 3,
                'is_active' => true,
            ],
            'TARIF-KEAMANAN' => [
                'nama_tarif' => 'Iuran Keamanan & Pos Ronda',
                'nominal' => 5000,
                'jenis_kas' => 'dana_kegiatan',
                'buku_kas_id' => 11, // KEG-RONDA
                'metode_perhitungan' => 'per_kk',
                'periode' => 'bulanan',
                'jenis_iuran' => 'wajib',
                'tingkat_pengelola' => 'rw',
                'urutan_prioritas' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($tarifs as $kode => $data) {
            IuranTarif::updateOrCreate(['kode_tarif' => $kode], $data);
        }

        // Nonaktifkan tarif insidental / alternatif agar tidak menduplikasi tagihan rutin
        IuranTarif::whereNotIn('kode_tarif', array_keys($tarifs))->update(['is_active' => false]);

        // =========================================================================
        // 2. BERSIHKAN TABEL TRANSAKSI KEUANGAN & TAGIHAN (PRESERVE USER, KK, DLL)
        // =========================================================================
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PembayaranAlokasi::truncate();
        PembayaranIuran::truncate();
        TransaksiKeuangan::truncate();
        TagihanItem::truncate();
        TagihanKeluarga::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Reset saldo awal & saldo sekarang pada seluruh 18 Buku Kas ke 0
        BukuKas::query()->update([
            'saldo_awal' => 0,
            'saldo_sekarang' => 0,
        ]);

        // Mapping Buku Kas RT (RT 001 - RT 008)
        $bukuKasRt = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtCode = str_pad($i, 3, '0', STR_PAD_LEFT);
            $bk = BukuKas::where('kode_kas', 'KAS-RT-' . $rtCode)->first();
            if ($bk) {
                $bukuKasRt[$i] = $bk->id;
            }
        }

        // =========================================================================
        // 3. GENERATE TAGIHAN & PEMBAYARAN 5 BULAN (JULI 2026 - NOVEMBER 2026)
        // =========================================================================
        $allKk = KartuKeluarga::with('kepalaKeluarga')->get();
        $periodeList = [
            '2026-07' => ['lunas_ratio' => 0.91], // ~91% Lunas
            '2026-08' => ['lunas_ratio' => 0.93], // ~93% Lunas
            '2026-09' => ['lunas_ratio' => 0.88], // ~88% Lunas
            '2026-10' => ['lunas_ratio' => 0.90], // ~90% Lunas
            '2026-11' => ['lunas_ratio' => 0.68], // ~68% Lunas (Bulan Berjalan)
        ];

        $paymentMethods = ['transfer_bank', 'tunai', 'qris'];

        foreach ($periodeList as $periode => $meta) {
            $yearMonth = explode('-', $periode);
            $year = intval($yearMonth[0]);
            $month = intval($yearMonth[1]);

            $kkIndex = 0;
            foreach ($allKk as $kk) {
                $kkIndex++;
                $cleanRtNum = intval(preg_replace('/[^0-9]/', '', $kk->rt)) ?: 1;
                $rtPadded = str_pad($cleanRtNum, 3, '0', STR_PAD_LEFT);
                $rtKasId = $bukuKasRt[$cleanRtNum] ?? 3;

                // Tentukan status pembayaran keluarga untuk periode ini
                $isLunas = false;
                $status = 'Belum Dibayar';

                if ($periode !== '2026-11') {
                    // Juli - Oktober: ~88% - 93% lunas
                    // Gunakan modulo deterministik agar riil dan konsisten
                    $isLunas = ($kkIndex % 10 !== ($month % 5));
                    $status = $isLunas ? 'Lunas' : 'Belum Dibayar';
                } else {
                    // November 2026: ~68% Lunas, ~9% Menunggu Konfirmasi, ~23% Belum Dibayar
                    if ($kkIndex % 3 !== 0) {
                        $isLunas = true;
                        $status = 'Lunas';
                    } elseif ($kkIndex % 7 === 0) {
                        $status = 'Menunggu Konfirmasi';
                    } else {
                        $status = 'Belum Dibayar';
                    }
                }

                $tglTerbit = Carbon::create($year, $month, 1);
                $tglJatuhTempo = Carbon::create($year, $month, 20);
                $dayLunas = 2 + ($kkIndex % 15); // Tanggal 2 s/d 17
                $tglLunas = $isLunas ? Carbon::create($year, $month, $dayLunas, 9 + ($kkIndex % 8), rand(10, 50)) : null;

                $method = $paymentMethods[$kkIndex % count($paymentMethods)];

                // 1. Buat Header Tagihan Keluarga
                $tagihan = TagihanKeluarga::create([
                    'nomor_tagihan' => 'TAG-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($kk->id, 4, '0', STR_PAD_LEFT),
                    'kartu_keluarga_id' => $kk->id,
                    'kepala_keluarga_id' => $kk->kepala_keluarga_id,
                    'rt_number' => $rtPadded,
                    'periode' => $periode,
                    'nominal_rt' => 20000,
                    'nominal_rw' => 10000,
                    'nominal_sampah' => 15000,
                    'total_tagihan' => 50000,
                    'total_dibayar' => $isLunas ? 50000 : 0,
                    'sisa_tagihan' => $isLunas ? 0 : 50000,
                    'status' => $status,
                    'tanggal_terbit' => $tglTerbit,
                    'jatuh_tempo' => $tglJatuhTempo,
                    'tanggal_lunas' => $tglLunas,
                    'metode_pembayaran_terakhir' => $isLunas ? $method : ($status === 'Menunggu Konfirmasi' ? 'transfer_bank' : null),
                    'catatan_warga' => $status === 'Menunggu Konfirmasi' ? 'Sudah transfer m-Banking, mohon diverifikasi pengurus.' : null,
                ]);

                // 2. Buat 4 Rincian Item Tagihan One Billing
                $itemsConfig = [
                    [
                        'kode' => 'TARIF-RT',
                        'tarif_id' => 1,
                        'nama' => 'Iuran Kas Operasional RT',
                        'nominal' => 20000,
                        'pemilik' => 'rt',
                        'buku_kas_id' => $rtKasId,
                        'kategori_trx' => 'Iuran Kas RT',
                    ],
                    [
                        'kode' => 'TARIF-RW',
                        'tarif_id' => 2,
                        'nama' => 'Iuran Kas Induk RW 013',
                        'nominal' => 10000,
                        'pemilik' => 'rw',
                        'buku_kas_id' => 1, // KAS-RW-013
                        'kategori_trx' => 'Iuran Kas RW',
                    ],
                    [
                        'kode' => 'TARIF-SAMPAH',
                        'tarif_id' => 3,
                        'nama' => 'Iuran Pengelolaan Sampah & Kebersihan',
                        'nominal' => 15000,
                        'pemilik' => 'rw',
                        'buku_kas_id' => 2, // KAS-SAMPAH
                        'kategori_trx' => 'Iuran Sampah & Kebersihan',
                    ],
                    [
                        'kode' => 'TARIF-KEAMANAN',
                        'tarif_id' => 5,
                        'nama' => 'Iuran Keamanan & Pos Ronda',
                        'nominal' => 5000,
                        'pemilik' => 'kegiatan',
                        'buku_kas_id' => 11, // KEG-RONDA
                        'kategori_trx' => 'Iuran Keamanan & Ronda',
                    ],
                ];

                $createdItems = [];
                foreach ($itemsConfig as $cfg) {
                    $item = TagihanItem::create([
                        'tagihan_keluarga_id' => $tagihan->id,
                        'iuran_tarif_id' => $cfg['tarif_id'],
                        'nama_iuran' => $cfg['nama'],
                        'kode_iuran' => $cfg['kode'],
                        'metode_perhitungan' => 'per_kk',
                        'tarif_satuan' => $cfg['nominal'],
                        'kuantitas' => 1,
                        'nominal_subtotal' => $cfg['nominal'],
                        'nominal_diskon' => 0,
                        'nominal_akhir' => $cfg['nominal'],
                        'nominal_terbayar' => $isLunas ? $cfg['nominal'] : 0,
                        'sisa_item' => $isLunas ? 0 : $cfg['nominal'],
                        'pemilik_dana' => $cfg['pemilik'],
                        'buku_kas_id' => $cfg['buku_kas_id'],
                        'status_pembayaran' => $isLunas ? 'lunas' : 'belum_dibayar',
                        'keterangan_snapshot' => 'One Billing RW 013 Periode ' . $periode,
                    ]);
                    $createdItems[] = ['item' => $item, 'config' => $cfg];
                }

                // 3. Jika LUNAS: Buat Bukti Pembayaran, Alokasi Kas, dan Transaksi Keuangan Kredit (Pemasukan)
                if ($isLunas) {
                    $pembayaran = PembayaranIuran::create([
                        'nomor_pembayaran' => 'PAY-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($tagihan->id, 5, '0', STR_PAD_LEFT),
                        'tagihan_keluarga_id' => $tagihan->id,
                        'kartu_keluarga_id' => $kk->id,
                        'tanggal_bayar' => $tglLunas,
                        'nominal_bayar' => 50000,
                        'metode_pembayaran' => $method,
                        'kanal_pembayaran' => $method === 'tunai' ? 'tunai_melalui_rt' : 'langsung_rekening',
                        'status' => 'berhasil',
                        'status_setoran_rw' => 'sudah_disetor_ke_rw',
                        'disetor_ke_rw_pada' => $tglLunas,
                        'catatan' => 'Pembayaran lunas iuran One Billing periode ' . $periode,
                        'diterima_oleh_id' => 1,
                        'nama_penerima' => 'Bendahara Penerima RW 013',
                        'idempotency_key' => 'IDEM-' . $tagihan->id . '-' . $kk->id,
                    ]);

                    $kepalaNama = $kk->nama_kepala_keluarga ?: ('KK ' . $kk->nomor_kk);

                    foreach ($createdItems as $idx => $ci) {
                        $itemModel = $ci['item'];
                        $cfg = $ci['config'];

                        // Transaksi Keuangan Riil Masuk ke Rekening Kas Masing-Masing
                        $trx = TransaksiKeuangan::create([
                            'nomor_transaksi' => 'TRX-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . $itemModel->id . '-' . rand(100, 999),
                            'buku_kas_id' => $cfg['buku_kas_id'],
                            'pemilik_dana' => $cfg['pemilik'],
                            'tagihan_keluarga_id' => $tagihan->id,
                            'tanggal_transaksi' => $tglLunas->toDateString(),
                            'jenis_transaksi' => 'pemasukan',
                            'kategori' => $cfg['kategori_trx'],
                            'sumber_dana' => 'Iuran ' . $kepalaNama . ' (RT ' . $rtPadded . ')',
                            'tujuan_penggunaan' => 'Pemasukan Kas Rutin ' . $cfg['nama'],
                            'nominal' => $cfg['nominal'],
                            'saldo_sebelumnya' => 0,
                            'saldo_setelahnya' => 0,
                            'metode_pembayaran' => $method,
                            'penyetor_penerima' => $kepalaNama,
                            'keterangan' => 'Penerimaan ' . $cfg['nama'] . ' Periode ' . $periode . ' (' . $kepalaNama . ')',
                            'status' => 'selesai',
                        ]);

                        // Alokasi Pembayaran
                        PembayaranAlokasi::create([
                            'tagihan_keluarga_id' => $tagihan->id,
                            'pembayaran_iuran_id' => $pembayaran->id,
                            'tagihan_item_id' => $itemModel->id,
                            'buku_kas_id' => $cfg['buku_kas_id'],
                            'transaksi_keuangan_id' => $trx->id,
                            'nama_komponen' => $cfg['nama'],
                            'nominal_alokasi' => $cfg['nominal'],
                            'pemilik_dana' => $cfg['pemilik'],
                            'status_alokasi' => 'masuk_kas',
                        ]);
                    }
                }
            }
        }

        // =========================================================================
        // 4. GENERATE PENGELUARAN OPERASIONAL BULANAN RIIL (JULI - NOVEMBER 2026)
        // =========================================================================
        $months = [
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
        ];

        foreach ($months as $mNum => $mName) {
            $paddedM = str_pad($mNum, 2, '0', STR_PAD_LEFT);

            // A. Kas Induk RW 013 (Buku Kas ID: 1)
            // ~Rp 330.000 / bulan
            TransaksiKeuangan::create([
                'nomor_transaksi' => 'TRX-OUT-RW-2026' . $paddedM . '-01',
                'buku_kas_id' => 1,
                'pemilik_dana' => 'rw',
                'tanggal_transaksi' => "2026-{$paddedM}-12",
                'jenis_transaksi' => 'pengeluaran',
                'kategori' => 'Operasional Balai RW',
                'sumber_dana' => 'Kas Induk RW 013',
                'tujuan_penggunaan' => 'Pembayaran Listrik & WiFi Balai Warga RW 013',
                'nominal' => 175000,
                'saldo_sebelumnya' => 0,
                'saldo_setelahnya' => 0,
                'metode_pembayaran' => 'transfer_bank',
                'penyetor_penerima' => 'PLN & Provider Internet',
                'keterangan' => "Tagihan Listrik & Internet Balai RW Bulan {$mName} 2026",
                'status' => 'selesai',
            ]);

            TransaksiKeuangan::create([
                'nomor_transaksi' => 'TRX-OUT-RW-2026' . $paddedM . '-02',
                'buku_kas_id' => 1,
                'pemilik_dana' => 'rw',
                'tanggal_transaksi' => "2026-{$paddedM}-18",
                'jenis_transaksi' => 'pengeluaran',
                'kategori' => 'Kesekretariatan & ATK',
                'sumber_dana' => 'Kas Induk RW 013',
                'tujuan_penggunaan' => 'Pembelian Kertas, Tinta Printer & ATK Layanan Surat',
                'nominal' => 75000,
                'saldo_sebelumnya' => 0,
                'saldo_setelahnya' => 0,
                'metode_pembayaran' => 'tunai',
                'penyetor_penerima' => 'Toko Buku & ATK Suryatmajan',
                'keterangan' => "Belanja ATK Sekretariat RW Bulan {$mName} 2026",
                'status' => 'selesai',
            ]);

            TransaksiKeuangan::create([
                'nomor_transaksi' => 'TRX-OUT-RW-2026' . $paddedM . '-03',
                'buku_kas_id' => 1,
                'pemilik_dana' => 'rw',
                'tanggal_transaksi' => "2026-{$paddedM}-25",
                'jenis_transaksi' => 'pengeluaran',
                'kategori' => 'Konsumsi & Rapat',
                'sumber_dana' => 'Kas Induk RW 013',
                'tujuan_penggunaan' => 'Konsumsi Rapat Koordinasi Rutin Pengurus RW & Ketua RT 001-008',
                'nominal' => 80000,
                'saldo_sebelumnya' => 0,
                'saldo_setelahnya' => 0,
                'metode_pembayaran' => 'tunai',
                'penyetor_penerima' => 'Dapur Warga RW 013',
                'keterangan' => "Snack & Minum Rapat Bulanan RW {$mName} 2026",
                'status' => 'selesai',
            ]);

            // B. Kas Sampah & Kebersihan (Buku Kas ID: 2)
            // ~Rp 670.000 / bulan
            TransaksiKeuangan::create([
                'nomor_transaksi' => 'TRX-OUT-SMP-2026' . $paddedM . '-01',
                'buku_kas_id' => 2,
                'pemilik_dana' => 'rw',
                'tanggal_transaksi' => "2026-{$paddedM}-26",
                'jenis_transaksi' => 'pengeluaran',
                'kategori' => 'Honor Petugas',
                'sumber_dana' => 'Kas Pengelolaan Sampah & Kebersihan',
                'tujuan_penggunaan' => 'Honor Petugas Kebersihan & Pengangkut Sampah RW 013 (2 Orang)',
                'nominal' => 550000,
                'saldo_sebelumnya' => 0,
                'saldo_setelahnya' => 0,
                'metode_pembayaran' => 'tunai',
                'penyetor_penerima' => 'Pak Joko & Pak Asep (Petugas)',
                'keterangan' => "Honor Angkut Sampah Rutin Bulan {$mName} 2026",
                'status' => 'selesai',
            ]);

            TransaksiKeuangan::create([
                'nomor_transaksi' => 'TRX-OUT-SMP-2026' . $paddedM . '-02',
                'buku_kas_id' => 2,
                'pemilik_dana' => 'rw',
                'tanggal_transaksi' => "2026-{$paddedM}-14",
                'jenis_transaksi' => 'pengeluaran',
                'kategori' => 'Alat & Pemeliharaan',
                'sumber_dana' => 'Kas Pengelolaan Sampah & Kebersihan',
                'tujuan_penggunaan' => 'Beli Karung Sampah Jumbo, Sapu Lidi & Service Roda Gerobak',
                'nominal' => 120000,
                'saldo_sebelumnya' => 0,
                'saldo_setelahnya' => 0,
                'metode_pembayaran' => 'tunai',
                'penyetor_penerima' => 'TB Mandiri Kebersihan',
                'keterangan' => "Peralatan & Pemeliharaan Armada Sampah {$mName} 2026",
                'status' => 'selesai',
            ]);

            // C. Kas Operasional Ronda & Keamanan (Buku Kas ID: 11)
            // ~Rp 125.000 / bulan
            TransaksiKeuangan::create([
                'nomor_transaksi' => 'TRX-OUT-RND-2026' . $paddedM . '-01',
                'buku_kas_id' => 11,
                'pemilik_dana' => 'kegiatan',
                'tanggal_transaksi' => "2026-{$paddedM}-08",
                'jenis_transaksi' => 'pengeluaran',
                'kategori' => 'Konsumsi Pos Ronda',
                'sumber_dana' => 'Dana Operasional Pos Ronda & Keamanan',
                'tujuan_penggunaan' => 'Logistik Kopi, Gula, Teh & Snack Ronda Malam Warga',
                'nominal' => 80000,
                'saldo_sebelumnya' => 0,
                'saldo_setelahnya' => 0,
                'metode_pembayaran' => 'tunai',
                'penyetor_penerima' => 'Warung Pos Kamling',
                'keterangan' => "Belanja Kopi & Konsumsi Ronda Malam {$mName} 2026",
                'status' => 'selesai',
            ]);

            TransaksiKeuangan::create([
                'nomor_transaksi' => 'TRX-OUT-RND-2026' . $paddedM . '-02',
                'buku_kas_id' => 11,
                'pemilik_dana' => 'kegiatan',
                'tanggal_transaksi' => "2026-{$paddedM}-21",
                'jenis_transaksi' => 'pengeluaran',
                'kategori' => 'Perlengkapan Kamtibmas',
                'sumber_dana' => 'Dana Operasional Pos Ronda & Keamanan',
                'tujuan_penggunaan' => 'Baterai Senter Cas, Jas Hujan & Minyak Pelumas Kentongan',
                'nominal' => 45000,
                'saldo_sebelumnya' => 0,
                'saldo_setelahnya' => 0,
                'metode_pembayaran' => 'tunai',
                'penyetor_penerima' => 'Toko Listrik Suryatmajan',
                'keterangan' => "Perlengkapan Jaga Malam Bulan {$mName} 2026",
                'status' => 'selesai',
            ]);

            // D. Kas Masing-Masing RT (RT 001 - RT 008, Buku Kas ID: 3 s/d 10)
            // Pengeluaran Kerja Bakti & Operasional Ringkas: Rp 50.000 / RT / bulan
            for ($r = 1; $r <= 8; $r++) {
                $rtCode = str_pad($r, 3, '0', STR_PAD_LEFT);
                $rtBukuId = $bukuKasRt[$r] ?? (2 + $r);

                TransaksiKeuangan::create([
                    'nomor_transaksi' => 'TRX-OUT-RT' . $rtCode . '-2026' . $paddedM . '-01',
                    'buku_kas_id' => $rtBukuId,
                    'pemilik_dana' => 'rt',
                    'tanggal_transaksi' => "2026-{$paddedM}-19",
                    'jenis_transaksi' => 'pengeluaran',
                    'kategori' => 'Kerja Bakti & ATK RT',
                    'sumber_dana' => 'Buku Kas RT ' . $rtCode . ' RW 013',
                    'tujuan_penggunaan' => "Konsumsi Kerja Bakti Lingkungan & ATK Surat RT {$rtCode}",
                    'nominal' => 50000,
                    'saldo_sebelumnya' => 0,
                    'saldo_setelahnya' => 0,
                    'metode_pembayaran' => 'tunai',
                    'penyetor_penerima' => "Ketua RT {$rtCode}",
                    'keterangan' => "Pengeluaran Operasional Rutin RT {$rtCode} Bulan {$mName} 2026",
                    'status' => 'selesai',
                ]);
            }

            // E. Program Posyandu Balita & Lansia (Buku Kas ID: 12)
            // Tiap bulan: Pemasukan Bantuan PMT (Rp 180.000), Pengeluaran PMT (Rp 140.000)
            TransaksiKeuangan::create([
                'nomor_transaksi' => 'TRX-IN-POS-2026' . $paddedM . '-01',
                'buku_kas_id' => 12,
                'pemilik_dana' => 'kegiatan',
                'tanggal_transaksi' => "2026-{$paddedM}-04",
                'jenis_transaksi' => 'pemasukan',
                'kategori' => 'Bantuan & Donasi PMT',
                'sumber_dana' => 'Bantuan Operasional Posyandu & Donasi Peduli Gizi',
                'tujuan_penggunaan' => 'Subsidi Menu PMT Sehat Balita & Lansia Posyandu Cempaka',
                'nominal' => 180000,
                'saldo_sebelumnya' => 0,
                'saldo_setelahnya' => 0,
                'metode_pembayaran' => 'tunai',
                'penyetor_penerima' => 'Kader Posyandu Cempaka RW 013',
                'keterangan' => "Penerimaan Donasi & Bantuan PMT Posyandu Bulan {$mName} 2026",
                'status' => 'selesai',
            ]);

            TransaksiKeuangan::create([
                'nomor_transaksi' => 'TRX-OUT-POS-2026' . $paddedM . '-01',
                'buku_kas_id' => 12,
                'pemilik_dana' => 'kegiatan',
                'tanggal_transaksi' => "2026-{$paddedM}-15",
                'jenis_transaksi' => 'pengeluaran',
                'kategori' => 'Penyaluran PMT',
                'sumber_dana' => 'Dana Posyandu Balita & Lansia Cempaka',
                'tujuan_penggunaan' => 'Pembelian Susu, Bubur Kacang Hijau, Buah & Vitamin Balita/Lansia',
                'nominal' => 140000,
                'saldo_sebelumnya' => 0,
                'saldo_setelahnya' => 0,
                'metode_pembayaran' => 'tunai',
                'penyetor_penerima' => 'Dapur PMT Kader Posyandu',
                'keterangan' => "Belanja Bahan PMT Posyandu Bulan {$mName} 2026",
                'status' => 'selesai',
            ]);
        }

        // =========================================================================
        // 5. TRANSAKSI PROGRAM INSIDENTAL SPESIFIK
        // =========================================================================

        // A. Peringatan HUT RI ke-81 (Buku Kas ID: 13, KEG-17AGS) - Agustus 2026
        TransaksiKeuangan::create([
            'nomor_transaksi' => 'TRX-IN-17A-202608-01',
            'buku_kas_id' => 13,
            'pemilik_dana' => 'kegiatan',
            'tanggal_transaksi' => '2026-08-05',
            'jenis_transaksi' => 'pemasukan',
            'kategori' => 'Donasi & Sponsor 17-an',
            'sumber_dana' => 'Donasi Sukarela Warga RW 013 & Sponsorship Lokal',
            'tujuan_penggunaan' => 'Dana Kegiatan Peringatan HUT RI ke-81 Tingkat RW 013',
            'nominal' => 2500000,
            'saldo_sebelumnya' => 0,
            'saldo_setelahnya' => 0,
            'metode_pembayaran' => 'transfer_bank',
            'penyetor_penerima' => 'Panitia HUT RI RW 013',
            'keterangan' => 'Penerimaan Donasi & Sponsorship HUT RI ke-81 RW 013',
            'status' => 'selesai',
        ]);

        TransaksiKeuangan::create([
            'nomor_transaksi' => 'TRX-OUT-17A-202608-01',
            'buku_kas_id' => 13,
            'pemilik_dana' => 'kegiatan',
            'tanggal_transaksi' => '2026-08-14',
            'jenis_transaksi' => 'pengeluaran',
            'kategori' => 'Hadiah & Perlombaan',
            'sumber_dana' => 'Dana Peringatan HUT RI ke-81 RW 013',
            'tujuan_penggunaan' => 'Belanja Hadiah Perlombaan Anak-anak & Ibu-ibu 17 Agustus',
            'nominal' => 1150000,
            'saldo_sebelumnya' => 0,
            'saldo_setelahnya' => 0,
            'metode_pembayaran' => 'tunai',
            'penyetor_penerima' => 'Seksi Perlombaan Karang Taruna',
            'keterangan' => 'Belanja Tropi & Bingkisan Lomba 17 Agustus',
            'status' => 'selesai',
        ]);

        TransaksiKeuangan::create([
            'nomor_transaksi' => 'TRX-OUT-17A-202608-02',
            'buku_kas_id' => 13,
            'pemilik_dana' => 'kegiatan',
            'tanggal_transaksi' => '2026-08-17',
            'jenis_transaksi' => 'pengeluaran',
            'kategori' => 'Panggung & Sound System',
            'sumber_dana' => 'Dana Peringatan HUT RI ke-81 RW 013',
            'tujuan_penggunaan' => 'Sewa Tenda, Panggung & Sound System Malam Puncak Syukuran Kemerdekaan',
            'nominal' => 1000000,
            'saldo_sebelumnya' => 0,
            'saldo_setelahnya' => 0,
            'metode_pembayaran' => 'tunai',
            'penyetor_penerima' => 'Rental Tenda Suryatmajan',
            'keterangan' => 'Sewa Panggung & Sound System Peringatan HUT RI',
            'status' => 'selesai',
        ]);

        // B. Dana Sosial & Santunan Duka Cita (Buku Kas ID: 15, KEG-SOSIAL) - September 2026
        TransaksiKeuangan::create([
            'nomor_transaksi' => 'TRX-IN-SOS-202609-01',
            'buku_kas_id' => 15,
            'pemilik_dana' => 'kegiatan',
            'tanggal_transaksi' => '2026-09-10',
            'jenis_transaksi' => 'pemasukan',
            'kategori' => 'Donasi Sosial',
            'sumber_dana' => 'Donasi Spontanitas Warga Peduli Sesama',
            'tujuan_penggunaan' => 'Penggalangan Dana Solidaritas Warga Sakit & Duka Cita',
            'nominal' => 500000,
            'saldo_sebelumnya' => 0,
            'saldo_setelahnya' => 0,
            'metode_pembayaran' => 'tunai',
            'penyetor_penerima' => 'Seksi Sosial RW 013',
            'keterangan' => 'Donasi Kas Sosial RW 013',
            'status' => 'selesai',
        ]);

        TransaksiKeuangan::create([
            'nomor_transaksi' => 'TRX-OUT-SOS-202609-01',
            'buku_kas_id' => 15,
            'pemilik_dana' => 'kegiatan',
            'tanggal_transaksi' => '2026-09-14',
            'jenis_transaksi' => 'pengeluaran',
            'kategori' => 'Santunan Warga',
            'sumber_dana' => 'Dana Sosial & Santunan Duka Cita',
            'tujuan_penggunaan' => 'Penyaluran Santunan Warga Sakit Rawat Inap & Duka Cita Keluarga Warga',
            'nominal' => 400000,
            'saldo_sebelumnya' => 0,
            'saldo_setelahnya' => 0,
            'metode_pembayaran' => 'tunai',
            'penyetor_penerima' => 'Keluarga Penerima Manfaat',
            'keterangan' => 'Penyaluran Santunan Sosial RW 013',
            'status' => 'selesai',
        ]);

        // Rekening program non-aktif / belum ada transaksi tetap Rp 0:
        // - Buku Kas ID 14 (KEG-KEAGAMAAN): Rp 0
        // - Buku Kas ID 16 (KEG-OLAHRAGA): Rp 0
        // - Buku Kas ID 17 (KEG-UMKM): Rp 0
        // - Buku Kas ID 18 (KEG-DARURAT): Rp 0

        // =========================================================================
        // 6. HITUNG ULANG & REKONSILIASI 18 REKENING BUKU KAS SECARA OTOMATIS
        // =========================================================================
        $allBukuKas = BukuKas::all();
        foreach ($allBukuKas as $bk) {
            $bk->recalculateSaldo();
        }
    }
}
