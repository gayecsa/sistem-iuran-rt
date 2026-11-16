<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PosyanduJadwal extends Model
{
    use HasFactory;

    protected $table = 'posyandu_jadwals';

    protected $fillable = [
        'posyandu_unit_id',
        'rt_number',
        'judul_kegiatan',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'jenis_layanan_list',
        'mitra_nakes',
        'status',
        'alasan_batal',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jenis_layanan_list' => 'array',
    ];

    public function unit()
    {
        return $this->belongsTo(PosyanduKegiatan::class, 'posyandu_unit_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function kehadirans()
    {
        return $this->hasMany(PosyanduKehadiran::class, 'jadwal_id');
    }

    public function pengukurans()
    {
        return $this->hasMany(PosyanduPengukuran::class, 'jadwal_id');
    }

    public function laporan()
    {
        return $this->hasOne(PosyanduLaporan::class, 'jadwal_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'akan_datang' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'sedang_berlangsung' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle animate-pulse',
            'selesai' => 'bg-success-subtle text-success border border-success-subtle',
            'ditunda' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'dibatalkan' => 'bg-danger-subtle text-danger border border-danger-subtle',
            default => 'bg-light text-dark',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'akan_datang' => 'Akan Datang',
            'sedang_berlangsung' => 'Sedang Berlangsung',
            'selesai' => 'Selesai Dilaksanakan',
            'ditunda' => 'Ditunda',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function getTanggalFormattedAttribute(): string
    {
        return Carbon::parse($this->tanggal)->translatedFormat('l, d F Y');
    }
}
