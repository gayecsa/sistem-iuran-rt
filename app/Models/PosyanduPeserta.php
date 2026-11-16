<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PosyanduPeserta extends Model
{
    use HasFactory;

    protected $table = 'posyandu_pesertas';

    protected $fillable = [
        'user_id',
        'kartu_keluarga_id',
        'rt_number',
        'kategori',
        'status_aktif',
        'golongan_darah',
        'riwayat_alergi',
        'catatan_khusus',
        'tanggal_bergabung',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'tanggal_bergabung' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function pengukurans()
    {
        return $this->hasMany(PosyanduPengukuran::class, 'peserta_id')->orderBy('tanggal_pemeriksaan', 'desc');
    }

    public function kehadirans()
    {
        return $this->hasMany(PosyanduKehadiran::class, 'peserta_id')->orderBy('created_at', 'desc');
    }

    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'bayi' => 'Bayi (0-11 Bulan)',
            'balita' => 'Balita (1-5 Tahun)',
            'anak' => 'Anak Pra-Sekolah',
            'ibu_hamil' => 'Ibu Hamil',
            'ibu_menyusui' => 'Ibu Menyusui',
            'lansia' => 'Lansia (60+ Tahun)',
            default => ucfirst(str_replace('_', ' ', $this->kategori)),
        };
    }

    public function getKategoriBadgeAttribute(): string
    {
        return match ($this->kategori) {
            'bayi' => 'bg-info-subtle text-info border border-info-subtle',
            'balita' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'anak' => 'bg-cyan-subtle text-cyan border border-cyan-subtle',
            'ibu_hamil' => 'bg-pink-subtle text-danger border border-pink-subtle',
            'ibu_menyusui' => 'bg-purple-subtle text-purple border border-purple-subtle',
            'lansia' => 'bg-amber-subtle text-warning-emphasis border border-warning-subtle',
            default => 'bg-light text-dark',
        };
    }

    public function getUsiaTextAttribute(): string
    {
        if (!$this->user || !$this->user->tanggal_lahir) {
            return '-';
        }
        $dob = Carbon::parse($this->user->tanggal_lahir);
        $diffYears = $dob->age;
        if ($diffYears < 2) {
            $diffMonths = $dob->diffInMonths(Carbon::now());
            return "{$diffMonths} Bulan";
        }
        return "{$diffYears} Tahun";
    }

    public function getLatestPengukuranAttribute()
    {
        return $this->pengukurans()->first();
    }
}
