<?php

namespace App\Services\Payment\Contracts;

use App\Models\TagihanKeluarga;

interface PaymentGatewayInterface
{
    /**
     * Nama unik identifier provider payment gateway
     */
    public function getProviderName(): string;

    /**
     * Mengindikasikan apakah payment gateway mendukung split settlement langsung ke sub-rekening/kas
     */
    public function supportsSplitSettlement(): bool;

    /**
     * Membuat payment intent / payload checkout digital (QRIS, VA, E-Wallet)
     */
    public function createPaymentPayload(TagihanKeluarga $tagihan, float $nominal, array $metadata = []): array;

    /**
     * Memvalidasi keabsahan payload webhook / signature dari gateway provider
     */
    public function validateWebhook(array $payload, ?string $signature = null): bool;

    /**
     * Mengekstrak informasi pembayaran menjadi format standar internal:
     * - nomor_pembayaran
     * - external_id / gateway_reference
     * - nominal
     * - status ('paid', 'pending', 'expired', 'failed')
     * - settlement_status ('settled', 'dalam_proses')
     * - method
     */
    public function parseWebhookPayload(array $payload): array;

    /**
     * Memeriksa status transaksi pembayaran secara on-demand ke server gateway
     */
    public function checkStatus(string $gatewayReference): array;
}
