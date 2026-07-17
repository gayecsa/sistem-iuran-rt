<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    use HasFactory;

    protected $table = 'umkm';

    protected $fillable = [
        'nama_umkm',
        'jenis_usaha',
        'nama_pemilik',
        'no_hp',
        'alamat',
        'latitude',
        'longitude',
        'deskripsi',
        'jam_buka',
        'jam_tutup',
        'foto'
    ];
}
