<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KkVerifikasiHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'kk_verifikasi_histories';

    protected $fillable = [
        'kartu_keluarga_id',
        'status_lama',
        'status_baru',
        'metode_verifikasi',
        'petugas_id',
        'petugas_nama',
        'petugas_role',
        'catatan',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
