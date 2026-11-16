<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WargaMutasi extends Model
{
    use HasFactory;

    protected $table = 'warga_mutasis';

    protected $fillable = [
        'kode_mutasi',
        'user_id',
        'kartu_keluarga_id',
        'jenis_mutasi',
        'nama_warga',
        'nik',
        'nomor_kk',
        'gender',
        'no_telepon',
        'rt_asal',
        'rt_tujuan',
        'alamat_asal',
        'alamat_tujuan',
        'asal_kota_kab',
        'asal_kecamatan',
        'asal_kelurahan',
        'tanggal_mutasi',
        'alasan_mutasi',
        'dokumen_pendukung_path',
        'status',
        'catatan_pengurus',
        'alasan_penolakan',
        'pengaju_id',
        'pemeriksa_id',
        'tanggal_verifikasi',
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
        'tanggal_verifikasi' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'pengaju_id');
    }

    public function pemeriksa()
    {
        return $this->belongsTo(User::class, 'pemeriksa_id');
    }

    public function getJenisMutasiLabelAttribute(): string
    {
        return match ($this->jenis_mutasi) {
            'mutasi_masuk' => 'Mutasi Masuk (Pendatang Baru)',
            'mutasi_keluar' => 'Mutasi Keluar (Pindah ke Luar RW)',
            'pindah_antar_rt' => 'Pindah Antar-RT (Internal RW 013)',
            'perubahan_alamat' => 'Perubahan Alamat (RT Sama)',
            default => ucfirst(str_replace('_', ' ', $this->jenis_mutasi)),
        };
    }

    public function getJenisMutasiBadgeAttribute(): string
    {
        return match ($this->jenis_mutasi) {
            'mutasi_masuk' => 'bg-success-subtle text-success border border-success-subtle',
            'mutasi_keluar' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'pindah_antar_rt' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'perubahan_alamat' => 'bg-info-subtle text-info border border-info-subtle',
            default => 'bg-secondary-subtle text-secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft Pengajuan',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'sedang_diperiksa' => 'Sedang Diperiksa',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'menunggu_verifikasi' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'sedang_diperiksa' => 'bg-info-subtle text-info border border-info-subtle',
            'disetujui' => 'bg-success-subtle text-success border border-success-subtle',
            'ditolak' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'dibatalkan' => 'bg-dark-subtle text-dark border border-dark-subtle',
            default => 'bg-light text-dark',
        };
    }

    public function getTanggalMutasiFormattedAttribute(): string
    {
        return Carbon::parse($this->tanggal_mutasi)->translatedFormat('d F Y');
    }
}
