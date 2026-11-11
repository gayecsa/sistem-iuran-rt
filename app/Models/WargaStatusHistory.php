<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WargaStatusHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'warga_status_histories';

    protected $fillable = [
        'user_id',
        'kartu_keluarga_id',
        'tipe_perubahan',
        'status_lama',
        'status_baru',
        'petugas_id',
        'petugas_nama',
        'petugas_role',
        'catatan',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }
}
