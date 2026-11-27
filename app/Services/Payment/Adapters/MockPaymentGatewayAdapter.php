<?php

namespace App\Services\Payment\Adapters;

use App\Models\TagihanKeluarga;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Str;

class MockPaymentGatewayAdapter implements PaymentGatewayInterface
{
    protected bool $splitSettlementEnabled;

    public function __construct(bool $splitSettlementEnabled = true)
    {
        $this->splitSettlementEnabled = $splitSettlementEnabled;
    }

    public function getProviderName(): string
    {
        return 'mock_gateway';
    }

    public function supportsSplitSettlement(): bool
    {
        return $this->splitSettlementEnabled;
    }

    public function createPaymentPayload(TagihanKeluarga $tagihan, float $nominal, array $metadata = []): array
    {
        $reference = 'MOCK-' . strtoupper(Str::random(12));
        $qrisCode = '00020101021226670016ID.WARKAS.MACHI0118' . $tagihan->nomor_tagihan . '520458125303360540' . number_format($nominal, 0, '', '') . '5802ID5913WARKAS MACHI6007Yogyakarta62070703A016304';

        // Hitung simulasi alokasi split settlement
        $splitPlan = [];
        foreach ($tagihan->items as $item) {
            $splitPlan[] = [
                'nama' => $item->nama_iuran,
                'pemilik' => $item->pemilik_dana,
                'buku_kas_id' => $item->buku_kas_id,
                'nominal' => (float)$item->nominal_akhir,
                'settlement_target' => $this->supportsSplitSettlement() ? 'direct_transfer' : 'holding_pool',
            ];
        }

        return [
            'success' => true,
            'gateway_reference' => $reference,
            'checkout_url' => url("/pembayaran/mock-checkout/{$reference}"),
            'qris_string' => $qrisCode,
            'nominal' => $nominal,
            'split_settlement_supported' => $this->supportsSplitSettlement(),
            'split_plan' => $splitPlan,
            'expired_at' => now()->addHours(24)->toIso8601String(),
        ];
    }

    public function validateWebhook(array $payload, ?string $signature = null): bool
    {
        // Untuk mock, webhook dianggap valid jika memiliki transaction_id dan status
        return !empty($payload['transaction_id']) && !empty($payload['status']);
    }

    public function parseWebhookPayload(array $payload): array
    {
        $statusStr = strtolower($payload['status'] ?? '');
        $status = match ($statusStr) {
            'settlement', 'capture', 'success', 'paid' => 'paid',
            'pending' => 'pending',
            'expired' => 'expired',
            default => 'failed',
        };

        // Jika provider mendukung split settlement, dana langsung 'settled',
        // jika belum, dicatat sebagai 'dalam_proses' settlement
        $settlementStatus = ($status === 'paid')
            ? ($this->supportsSplitSettlement() ? 'settled' : 'dalam_proses')
            : 'pending';

        return [
            'gateway_reference' => $payload['transaction_id'] ?? null,
            'nomor_tagihan' => $payload['order_id'] ?? null,
            'nominal' => (float)($payload['gross_amount'] ?? 0),
            'status' => $status,
            'settlement_status' => $settlementStatus,
            'payment_type' => $payload['payment_type'] ?? 'qris',
            'paid_at' => now(),
            'raw_payload' => $payload,
        ];
    }

    public function checkStatus(string $gatewayReference): array
    {
        return [
            'gateway_reference' => $gatewayReference,
            'status' => 'paid',
            'settlement_status' => $this->supportsSplitSettlement() ? 'settled' : 'dalam_proses',
            'checked_at' => now()->toIso8601String(),
        ];
    }
}
