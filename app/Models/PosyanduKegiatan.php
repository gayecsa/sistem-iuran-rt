<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosyanduKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_posyandu',
        'rt_number',
        'lokasi',
        'jadwal',
        'kader_utama_id',
        'mitra_kesehatan',
        'tenaga_medis',
        'latitude',
        'longitude',
        'layanan',
    ];

    public function kaderUtama()
    {
        return $this->belongsTo(User::class, 'kader_utama_id');
    }

    public function getKaderNameAttribute(): string
    {
        if ($this->kaderUtama) {
            return $this->kaderUtama->name;
        }
        return 'Kader Kesehatan RW 013';
    }

    public function getKaderAvatarUrlAttribute(): string
    {
        if ($this->kaderUtama) {
            return $this->kaderUtama->avatar_url;
        }
        return asset('images/avatar-default.svg');
    }
}
