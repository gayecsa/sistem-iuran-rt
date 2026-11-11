<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampahTarifHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id',
        'harga_lama',
        'harga_baru',
        'diubah_oleh_id',
        'alasan_perubahan',
    ];

    protected $casts = [
        'harga_lama' => 'decimal:2',
        'harga_baru' => 'decimal:2',
    ];

    public function kategori()
    {
        return $this->belongsTo(BankSampahKategori::class, 'kategori_id');
    }

    public function diubahOleh()
    {
        return $this->belongsTo(User::class, 'diubah_oleh_id');
    }
}
