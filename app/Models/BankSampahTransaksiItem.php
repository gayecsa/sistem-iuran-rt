<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampahTransaksiItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_id',
        'kategori_id',
        'berat_satuan',
        'satuan',
        'harga_per_satuan',
        'subtotal',
        'catatan',
    ];

    protected $casts = [
        'berat_satuan' => 'decimal:2',
        'harga_per_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function transaksi()
    {
        return $this->belongsTo(BankSampahTransaksi::class, 'transaksi_id');
    }

    public function kategori()
    {
        return $this->belongsTo(BankSampahKategori::class, 'kategori_id');
    }

    public function getSubtotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
