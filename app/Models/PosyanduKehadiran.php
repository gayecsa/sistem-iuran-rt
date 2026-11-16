<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosyanduKehadiran extends Model
{
    use HasFactory;

    protected $table = 'posyandu_kehadirans';

    protected $fillable = [
        'jadwal_id',
        'peserta_id',
        'rt_number',
        'status_kehadiran',
        'jam_datang',
        'petugas_id',
        'catatan_kunjungan',
    ];

    public function jadwal()
    {
        return $this->belongsTo(PosyanduJadwal::class, 'jadwal_id');
    }

    public function peserta()
    {
        return $this->belongsTo(PosyanduPeserta::class, 'peserta_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
