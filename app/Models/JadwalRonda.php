<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalRonda extends Model
{
    use HasFactory;

    protected $fillable = [
        'hari',
        'rt_list',
        'primary_rt',
        'coordinator_id_1',
        'coordinator_id_2',
        'koordinator_fallback',
        'anggota_label',
        'pos_ronda',
        'area_patroli',
        'jam_mulai',
        'jam_selesai',
        'status',
        'latitude',
        'longitude',
    ];

    public function coordinator1()
    {
        return $this->belongsTo(User::class, 'coordinator_id_1');
    }

    public function coordinator2()
    {
        return $this->belongsTo(User::class, 'coordinator_id_2');
    }

    /**
     * Nama koordinator terformat secara dinamis.
     * Jika tidak ada warga yang di-assign, return fallback atau 'Belum ditentukan'.
     */
    public function getKoordinatorTextAttribute(): string
    {
        $names = [];
        if ($this->coordinator1) {
            $names[] = $this->coordinator1->name;
        }
        if ($this->coordinator2) {
            $names[] = $this->coordinator2->name;
        }

        if (count($names) > 0) {
            return implode(' & ', $names);
        }

        return $this->koordinator_fallback ?: 'Belum ditentukan';
    }

    /**
     * Avatar URL koordinator utama.
     */
    public function getKoordinatorAvatarUrlAttribute(): string
    {
        if ($this->coordinator1) {
            return $this->coordinator1->avatar_url;
        }
        return asset('images/avatar-default.svg');
    }
}
