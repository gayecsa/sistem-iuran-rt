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
        'rt_checked_by',
        'rt_checked_at',
        'rt_check_note',
        'rw_resolved_by',
        'rw_resolved_at',
        'rw_resolve_note',
    ];

    protected $casts = [
        'rt_checked_at' => 'datetime',
        'rw_resolved_at' => 'datetime',
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

    public function rtChecker()
    {
        return $this->belongsTo(User::class, 'rt_checked_by');
    }

    public function rwResolver()
    {
        return $this->belongsTo(User::class, 'rw_resolved_by');
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

    public function getUrgensiAttribute(): string
    {
        if (preg_match('/\[Tingkat Urgensi:\s*([^\]]+)\]/', $this->deskripsi ?? '', $matches)) {
            return trim($matches[1]);
        }
        return 'Normal';
    }

    public function getJenisKejadianAttribute(): string
    {
        if (preg_match('/\[Jenis Kejadian:\s*([^\]]+)\]/', $this->deskripsi ?? '', $matches)) {
            return trim($matches[1]);
        }
        return $this->kategori ?: 'Keamanan';
    }

    public function getKronologiCleanAttribute(): string
    {
        $clean = preg_replace('/\[Jenis Kejadian:[^\]]+\]\s*\[Tingkat Urgensi:[^\]]+\]\s*Kronologi Kejadian:\s*/', '', $this->deskripsi ?? '');
        return trim($clean) ?: ($this->deskripsi ?? '');
    }
}
