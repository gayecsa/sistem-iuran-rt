<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori',
        'kode',
        'nama',
        'singkatan',
        'deskripsi',
        'tujuan',
        'persyaratan',
        'form_fields',
        'alur_persetujuan',
        'biaya',
        'estimasi_hari',
        'kode_klasifikasi',
        'template_body',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'persyaratan' => 'array',
        'form_fields' => 'array',
        'is_active' => 'boolean',
    ];

    const KATEGORI_KEPENDUDUKAN = 'kependudukan';
    const KATEGORI_SOSIAL_KELUARGA = 'sosial_keluarga';
    const KATEGORI_USAHA_EKONOMI = 'usaha_ekonomi';
    const KATEGORI_LINGKUNGAN_KEAMANAN = 'lingkungan_keamanan';
    const KATEGORI_INTERNAL = 'internal';

    const ALUR_RT_THEN_RW = 'rt_then_rw';
    const ALUR_RT_ONLY = 'rt_only';
    const ALUR_PENGANTAR_KELURAHAN = 'pengantar_kelurahan';
    const ALUR_INTERNAL_RW = 'internal_rw';

    public function requests()
    {
        return $this->hasMany(LetterRequest::class, 'letter_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }

    public function requiresRwApproval(): bool
    {
        return in_array($this->alur_persetujuan, [self::ALUR_RT_THEN_RW, self::ALUR_PENGANTAR_KELURAHAN, self::ALUR_INTERNAL_RW]);
    }

    public function isRtOnly(): bool
    {
        return $this->alur_persetujuan === self::ALUR_RT_ONLY;
    }

    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            self::KATEGORI_KEPENDUDUKAN => 'Administrasi Kependudukan',
            self::KATEGORI_SOSIAL_KELUARGA => 'Sosial dan Keluarga',
            self::KATEGORI_USAHA_EKONOMI => 'Usaha, Pekerjaan & Ekonomi',
            self::KATEGORI_LINGKUNGAN_KEAMANAN => 'Lingkungan & Keamanan',
            self::KATEGORI_INTERNAL => 'Administrasi Internal',
            default => 'Layanan Umum',
        };
    }

    public function getAlurLabelAttribute(): string
    {
        return match ($this->alur_persetujuan) {
            self::ALUR_RT_THEN_RW => 'Persetujuan Berjenjang (RT & RW)',
            self::ALUR_RT_ONLY => 'Persetujuan RT Saja',
            self::ALUR_PENGANTAR_KELURAHAN => 'Verifikasi RT & Pengantar RW ke Kelurahan',
            self::ALUR_INTERNAL_RW => 'Administrasi Internal RW',
            default => 'Persetujuan RT & RW',
        };
    }

    public function getAlurBadgeColorAttribute(): string
    {
        return match ($this->alur_persetujuan) {
            self::ALUR_RT_THEN_RW => 'primary',
            self::ALUR_RT_ONLY => 'success',
            self::ALUR_PENGANTAR_KELURAHAN => 'info',
            self::ALUR_INTERNAL_RW => 'warning',
            default => 'secondary',
        };
    }

    public function getIconAttribute(): string
    {
        return match ($this->kode) {
            'domisili' => 'home',
            'pengantar_kependudukan' => 'id-card',
            'pindah_domisili' => 'truck',
            'pendatang_baru' => 'user-plus',
            'sktm' => 'heart-handshake',
            'anggota_keluarga' => 'users-round',
            'kematian' => 'user-x',
            'belum_menikah' => 'heart',
            'sku' => 'store',
            'pekerjaan' => 'briefcase',
            'beasiswa' => 'graduation-cap',
            'rekomendasi_bansos' => 'gift',
            'izin_kegiatan' => 'calendar-check',
            'kejadian_lingkungan' => 'shield-alert',
            'rekomendasi_warga' => 'award',
            'keanggotaan_pengurus' => 'badge-check',
            'tamu_penghuni' => 'user-check',
            default => 'file-text',
        };
    }
}
