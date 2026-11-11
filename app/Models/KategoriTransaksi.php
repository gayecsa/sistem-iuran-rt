<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriTransaksi extends Model
{
    use HasFactory;

    protected $table = 'kategori_transaksis';

    protected $fillable = [
        'jenis_kas',
        'tipe',
        'nama_kategori',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
