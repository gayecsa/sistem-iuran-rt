<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pelapor_nama',
        'pelapor_phone',
        'kategori',
        'rt_number',
        'rw_number',
        'lokasi',
        'deskripsi',
        'status',
        'foto',
        'catatan_tindak_lanjut',
        'petugas_id',
        'latitude',
        'longitude',
    ];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->pelapor();
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function getPelaporNameAttribute(): string
    {
        if ($this->pelapor) {
            return $this->pelapor->name;
        }
        return $this->pelapor_nama ?: 'Warga Anonim';
    }

    public function getPelaporAvatarUrlAttribute(): string
    {
        if ($this->pelapor) {
            return $this->pelapor->avatar_url;
        }
        return asset('images/avatar-default.svg');
    }
}
