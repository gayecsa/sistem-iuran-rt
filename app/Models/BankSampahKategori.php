<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampahKategori extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori',
        'slug',
        'satuan',
        'harga_per_satuan',
        'deskripsi',
        'petunjuk_pemilahan',
        'badge_color',
        'icon',
        'is_aktif',
        'urutan',
    ];

    protected $casts = [
        'harga_per_satuan' => 'decimal:2',
        'is_aktif' => 'boolean',
        'urutan' => 'integer',
    ];

    public function tarifHistories()
    {
        return $this->hasMany(BankSampahTarifHistory::class, 'kategori_id')->orderBy('created_at', 'desc');
    }

    public function transaksiItems()
    {
        return $this->hasMany(BankSampahTransaksiItem::class, 'kategori_id');
    }

    public function getHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_per_satuan, 0, ',', '.') . ' / ' . $this->satuan;
    }
}
