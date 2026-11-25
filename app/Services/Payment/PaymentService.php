<?php

namespace App\Services\Payment;

use App\Models\BukuKas;
use App\Models\KeuanganAuditLog;
use App\Models\PembayaranAlokasi;
use App\Models\PembayaranIuran;
use App\Models\TagihanItem;
use App\Models\TagihanKeluarga;
use App\Models\TransaksiKeuangan;
use App\Services\Payment\Adapters\MockPaymentGatewayAdapter;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    protected PaymentGatewayInterface $gateway;

    public function __construct(?PaymentGatewayInterface $gateway = null)
    {
        $this->gateway = $gateway ?? new MockPaymentGatewayAdapter(true);
    }

    public function setGateway(PaymentGatewayInterface $gateway): self
    {
        $this->gateway = $gateway;
        return $this;
    }

    public function getGateway(): PaymentGatewayInterface
    {
        return $this->gateway;
    }

    /**
     * Membuat intent/payload pembayaran digital melalui gateway aktif
     */
    public function createDigitalPaymentIntent(TagihanKeluarga $tagihan, ?float $nominal = null): array
    {
        $nominalBayar = $nominal ?? (float)$tagihan->sisa_tagihan;
        if ($nominalBayar <= 0) {
            throw new \InvalidArgumentException('Nominal tagihan sudah lunas atau tidak valid.');
        }

        return $this->gateway->createPaymentPayload($tagihan, $nominalBayar, [
            'nomor_kk' => $tagihan->kartuKeluarga->nomor_kk ?? null,
            'kepala_keluarga' => $tagihan->kartuKeluarga->kepala_keluarga ?? null,
            'rt' => $tagihan->rt_number,
            'periode' => $tagihan->periode,
        ]);
    }

    /**
     * Memproses penerimaan pembayaran dan melakukan alokasi otomatis ke kas masing-masing
     * (One Billing, Multiple Automatic Fund Allocations) dengan DB Transaction & Idempotency.
     */
    public function processPaymentAndAllocate(
        TagihanKeluarga $tagihan,
        float $nominalBayar,
        string $metodePembayaran = 'tunai', // 'tunai', 'transfer_bank', 'qris', 'online_gateway'
        string $kanalPembayaran = 'langsung_rekening', // 'langsung_rekening', 'tunai_melalui_rt'
        ?string $idempotencyKey = null,
        array $extraAttributes = []
    ): PembayaranIuran {
        if ($nominalBayar <= 0) {
            throw new \InvalidArgumentException('Nominal pembayaran harus lebih besar dari 0.');
        }

        // 1. Idempotency Check: Pastikan transaksi dengan idempotency key yang sama tidak dieksekusi ganda
        $effectiveIdempotencyKey = $idempotencyKey ?: ($extraAttributes['gateway_reference'] ?? ('PAY-' . Str::uuid()));
        $existingPayment = PembayaranIuran::where('idempotency_key', $effectiveIdempotencyKey)->first();
        if ($existingPayment) {
            Log::info("Idempotent hit: Pembayaran {$effectiveIdempotencyKey} sudah pernah diproses.");
            return $existingPayment;
        }

        return DB::transaction(function () use ($tagihan, $nominalBayar, $metodePembayaran, $kanalPembayaran, $effectiveIdempotencyKey, $extraAttributes) {
            // Lock record tagihan untuk menghindari race condition
            $lockedTagihan = TagihanKeluarga::where('id', $tagihan->id)->lockForUpdate()->first();
            $tglBayar = $extraAttributes['tanggal_bayar'] ?? Carbon::now();
            $provider = $extraAttributes['gateway_provider'] ?? $this->gateway->getProviderName();
            $gatewayRef = $extraAttributes['gateway_reference'] ?? null;
            $supportsSplit = $this->gateway->supportsSplitSettlement();

            // Status settlement: jika split settlement didukung provider, 'settled'. Jika belum, 'dalam_proses'.
            $settlementStatus = $extraAttributes['settlement_status'] ?? ($supportsSplit ? 'settled' : 'dalam_proses');

            // 2. Buat Record Pembayaran Induk
            $nomorPembayaran = 'PAY/' . date('Ym') . '/' . str_pad($lockedTagihan->id, 5, '0', STR_PAD_LEFT) . '/' . strtoupper(Str::random(4));
            
            // Tentukan status setoran ke RW untuk pembayaran tunai ke RT
            $statusSetoranRw = ($kanalPembayaran === 'tunai_melalui_rt') ? 'menunggu_setor_ke_rw' : 'tidak_perlu';

            $pembayaran = PembayaranIuran::create([
                'nomor_pembayaran' => $nomorPembayaran,
                'tagihan_keluarga_id' => $lockedTagihan->id,
                'kartu_keluarga_id' => $lockedTagihan->kartu_keluarga_id,
                'tanggal_bayar' => $tglBayar,
                'nominal_bayar' => $nominalBayar,
                'metode_pembayaran' => $metodePembayaran,
                'gateway_provider' => $provider,
                'gateway_reference' => $gatewayRef,
                'kanal_pembayaran' => $kanalPembayaran,
                'status_setoran_rw' => $statusSetoranRw,
                'bukti_pembayaran_path' => $extraAttributes['bukti_pembayaran_path'] ?? null,
                'catatan' => $extraAttributes['catatan'] ?? 'Pembayaran iuran gabungan ' . $lockedTagihan->periode,
                'diterima_oleh_id' => $extraAttributes['diterima_oleh_id'] ?? auth()->id(),
                'nama_penerima' => $extraAttributes['nama_penerima'] ?? (auth()->check() ? auth()->user()->name : 'System Gateway'),
                'idempotency_key' => $effectiveIdempotencyKey,
                'status' => 'berhasil',
                'settlement_status' => $settlementStatus,
            ]);

            // 3. Distribusikan Dana ke Komponen Tagihan (Alokasi Otomatis)
            $sisaDanaAlokasi = $nominalBayar;
            $items = TagihanItem::where('tagihan_keluarga_id', $lockedTagihan->id)
                ->where('sisa_item', '>', 0)
                ->orderBy('id', 'asc')
                ->get();

            foreach ($items as $item) {
                if ($sisaDanaAlokasi <= 0) {
                    break;
                }

                $alokasiNominal = min($sisaDanaAlokasi, (float)$item->sisa_item);
                $item->nominal_terbayar += $alokasiNominal;
                $item->sisa_item = max(0, (float)$item->nominal_akhir - (float)$item->nominal_terbayar);
                $item->status_pembayaran = ($item->sisa_item <= 0) ? 'lunas' : 'dibayar_sebagian';
                $item->save();

                $sisaDanaAlokasi -= $alokasiNominal;

                // Tentukan Rekening Kas Tujuan & Status Alokasi
                $bukuKas = $item->bukuKas;
                $statusAlokasi = 'masuk_kas';
                $pemilikDana = strtolower(trim($item->pemilik_dana ?: 'rw'));

                // Logika Penitipan: Jika warga membayar tunai ke Ketua/Bendahara RT,
                // dana yang menjadi hak RW atau Unit Lingkungan dicatat sebagai titipan_rt
                if ($kanalPembayaran === 'tunai_melalui_rt' && in_array($pemilikDana, ['rw', 'sampah', 'unit', 'kegiatan'])) {
                    $statusAlokasi = 'titipan_rt';
                }

                $trxId = null;

                // Jika dana langsung masuk ke kas (bukan titipan dan sudah settled), bukukan transaksi keuangan ke Buku Kas terkait
                if ($statusAlokasi === 'masuk_kas' && $bukuKas && $settlementStatus === 'settled') {
                    $trx = TransaksiKeuangan::create([
                        'buku_kas_id' => $bukuKas->id,
                        'tagihan_keluarga_id' => $lockedTagihan->id,
                        'nomor_transaksi' => 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                        'tanggal_transaksi' => $tglBayar,
                        'jenis_transaksi' => 'pemasukan',
                        'kategori' => 'Alokasi Iuran: ' . $item->nama_iuran,
                        'pemilik_dana' => strtolower($item->pemilik_dana ?: 'rw'),
                        'nominal' => $alokasiNominal,
                        'saldo_sebelumnya' => $bukuKas->saldo_sekarang,
                        'saldo_setelahnya' => $bukuKas->saldo_sekarang + $alokasiNominal,
                        'metode_pembayaran' => $metodePembayaran,
                        'penyetor_penerima' => $lockedTagihan->kartuKeluarga->nama_kepala_keluarga ?? 'Warga RT ' . $lockedTagihan->clean_rt,
                        'keterangan' => "Alokasi {$item->nama_iuran} - " . ($lockedTagihan->kartuKeluarga->nama_kepala_keluarga ?? 'Warga') . " [{$lockedTagihan->nomor_tagihan}]",
                        'status' => 'selesai',
                        'petugas_id' => auth()->id() ?? 1,
                        'nama_petugas' => auth()->check() ? auth()->user()->name : 'System Gateway',
                    ]);

                    $bukuKas->recalculateSaldo();
                    $trxId = $trx->id;
                }

                // Catat Detail Alokasi Resmi per Komponen
                PembayaranAlokasi::create([
                    'tagihan_keluarga_id' => $lockedTagihan->id,
                    'pembayaran_iuran_id' => $pembayaran->id,
                    'tagihan_item_id' => $item->id,
                    'buku_kas_id' => $bukuKas ? $bukuKas->id : null,
                    'transaksi_keuangan_id' => $trxId,
                    'nama_komponen' => $item->nama_iuran,
                    'nominal_alokasi' => $alokasiNominal,
                    'pemilik_dana' => $item->pemilik_dana ?: 'RW',
                    'status_alokasi' => $statusAlokasi,
                    'settlement_status' => $settlementStatus,
                    'settlement_reference' => $gatewayRef,
                ]);
            }

            // 4. Update Tagihan Induk
            $lockedTagihan->total_dibayar += $nominalBayar;
            $lockedTagihan->sisa_tagihan = max(0, (float)$lockedTagihan->total_tagihan - (float)$lockedTagihan->total_dibayar);
            $lockedTagihan->metode_pembayaran_terakhir = match ($metodePembayaran) {
                'tunai' => 'Tunai',
                'qris' => 'QRIS',
                'transfer_bank' => 'Transfer Bank',
                default => 'Digital Gateway',
            };

            // Status lunas HANYA jika seluruh sisa tagihan = 0 dan tidak ada item tertinggal
            if ($lockedTagihan->sisa_tagihan <= 0) {
                $lockedTagihan->status = 'Lunas';
                $lockedTagihan->tanggal_lunas = $tglBayar;
            } else {
                $lockedTagihan->status = 'Dibayar Sebagian';
            }
            $lockedTagihan->save();

            // 5. Audit Trail
            KeuanganAuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'tagihan_id' => $lockedTagihan->id,
                'action' => 'pembayaran_iuran',
                'deskripsi' => "Pembayaran Tagihan #{$lockedTagihan->nomor_tagihan} sebesar Rp " . number_format($nominalBayar, 0, ',', '.') . " berhasil diproses ({$metodePembayaran}) dan dialokasikan otomatis.",
                'old_values' => null,
                'new_values' => [
                    'pembayaran_id' => $pembayaran->id,
                    'tagihan_id' => $lockedTagihan->id,
                    'nominal_bayar' => $nominalBayar,
                    'sisa_tagihan' => $lockedTagihan->sisa_tagihan,
                    'settlement_status' => $settlementStatus,
                ],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'System',
            ]);

            return $pembayaran;
        });
    }

    /**
     * Memproses Webhook dari Payment Gateway Provider
     */
    public function handleWebhook(array $payload, ?string $signature = null): array
    {
        if (!$this->gateway->validateWebhook($payload, $signature)) {
            Log::warning('Payment Gateway Webhook Validation Failed', ['payload' => $payload]);
            return ['success' => false, 'message' => 'Invalid webhook signature / payload'];
        }

        $parsed = $this->gateway->parseWebhookPayload($payload);
        if ($parsed['status'] !== 'paid') {
            Log::info("Webhook status is not paid: {$parsed['status']}");
            return ['success' => true, 'message' => "Payment status is {$parsed['status']}, no allocation needed."];
        }

        $nomorTagihan = $parsed['nomor_tagihan'];
        $tagihan = TagihanKeluarga::where('nomor_tagihan', $nomorTagihan)->first();

        if (!$tagihan) {
            Log::error("Tagihan not found for webhook order: {$nomorTagihan}");
            return ['success' => false, 'message' => 'Tagihan not found'];
        }

        $pembayaran = $this->processPaymentAndAllocate(
            $tagihan,
            $parsed['nominal'],
            $parsed['payment_type'],
            'langsung_rekening',
            'WEBHOOK-' . $parsed['gateway_reference'],
            [
                'gateway_provider' => $this->gateway->getProviderName(),
                'gateway_reference' => $parsed['gateway_reference'],
                'settlement_status' => $parsed['settlement_status'],
                'catatan' => 'Pembayaran digital via Webhook ' . $this->gateway->getProviderName(),
            ]
        );

        return [
            'success' => true,
            'message' => 'Payment successfully recorded and allocated',
            'pembayaran_id' => $pembayaran->id,
            'nomor_pembayaran' => $pembayaran->nomor_pembayaran,
        ];
    }
}
