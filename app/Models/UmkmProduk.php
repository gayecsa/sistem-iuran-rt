<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmProduk extends Model
{
    use HasFactory;

    protected $table = 'umkm_produks';

    protected $fillable = [
        'umkm_id',
        'nama_produk',
        'kategori_produk',
        'harga',
        'deskripsi',
        'foto',
        'is_tersedia',
        'is_best_seller',
    ];

    protected $casts = [
        'harga' => 'integer',
        'is_tersedia' => 'boolean',
        'is_best_seller' => 'boolean',
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    /**
     * Format harga Rupiah
     */
    public function getHargaRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * URL Foto Produk yang aman dengan fallback placeholder
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
                return $this->foto;
            }
            return asset('storage/' . $this->foto);
        }
        return '';
    }
}
