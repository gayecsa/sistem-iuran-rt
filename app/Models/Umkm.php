<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Umkm extends Model
{
    use HasFactory;

    protected $table = 'umkm';

    protected $fillable = [
        'user_id',
        'nama_umkm',
        'jenis_usaha',
        'kategori',
        'nama_pemilik',
        'no_hp',
        'alamat',
        'rt_number',
        'rw_number',
        'kelurahan',
        'kecamatan',
        'kota',
        'latitude',
        'longitude',
        'deskripsi',
        'jam_buka',
        'jam_tutup',
        'foto',
        'status_usaha',
        'status_verifikasi',
        'is_active',
        'harga_mulai',
        'harga_sampai',
        'catatan_verifikasi',
        'alasan_moderasi',
        'moderated_by',
        'moderated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'harga_mulai' => 'integer',
        'harga_sampai' => 'integer',
        'moderated_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->owner();
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function produks()
    {
        return $this->hasMany(UmkmProduk::class, 'umkm_id')->orderBy('is_best_seller', 'desc');
    }

    public function auditLogs()
    {
        return $this->hasMany(UmkmAuditLog::class, 'umkm_id')->orderBy('created_at', 'desc');
    }

    /**
     * Status Kepemilikan (Terhubung vs Belum Terhubung)
     */
    public function getStatusKepemilikanAttribute(): string
    {
        return $this->user_id ? 'terhubung' : 'belum_terhubung';
    }

    /**
     * Badge Status Verifikasi & Moderasi
     */
    public function getStatusVerifikasiBadgeAttribute(): array
    {
        return match ($this->status_verifikasi) {
            'diverifikasi' => [
                'label' => 'Terverifikasi',
                'bg' => '#DCFCE7',
                'text' => '#15803D',
                'border' => '#86EFAC',
                'icon' => 'check-circle-2',
            ],
            'menunggu' => [
                'label' => 'Menunggu Verifikasi',
                'bg' => '#FEF3C7',
                'text' => '#B45309',
                'border' => '#FCD34D',
                'icon' => 'clock',
            ],
            'perlu_perbaikan' => [
                'label' => 'Perlu Perbaikan',
                'bg' => '#FFEDD5',
                'text' => '#C2410C',
                'border' => '#FDBA74',
                'icon' => 'alert-triangle',
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'bg' => '#FEE2E2',
                'text' => '#B91C1C',
                'border' => '#FCA5A5',
                'icon' => 'x-circle',
            ],
            'dinonaktifkan' => [
                'label' => 'Dinonaktifkan',
                'bg' => '#F1F5F9',
                'text' => '#475569',
                'border' => '#CBD5E1',
                'icon' => 'pause-circle',
            ],
            default => [
                'label' => 'Belum Terhubung',
                'bg' => '#F8FAFC',
                'text' => '#64748B',
                'border' => '#E2E8F0',
                'icon' => 'help-circle',
            ],
        };
    }

    /**
     * Nama pemilik dinamis: jika terhubung dengan user, ambil dari user->name
     */
    public function getOwnerNameAttribute(): string
    {
        if ($this->owner) {
            return $this->owner->name;
        }
        return $this->nama_pemilik ?: 'Warga RW 013';
    }

    /**
     * Foto pemilik dinamis
     */
    public function getOwnerAvatarUrlAttribute(): string
    {
        if ($this->owner) {
            return $this->owner->avatar_url;
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->owner_name) . '&background=1456A0&color=FFFFFF&bold=true';
    }

    /**
     * URL Foto UMKM dengan proteksi link dan fallback SVG/Unsplash
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
                return $this->foto;
            }
            return asset('storage/' . $this->foto);
        }
        return $this->category_placeholder_image;
    }

    /**
     * Placeholder image default berdasarkan kategori usaha
     */
    public function getCategoryPlaceholderImageAttribute(): string
    {
        $kat = strtolower($this->kategori . ' ' . $this->jenis_usaha);
        if (str_contains($kat, 'kopi') || str_contains($kat, 'minuman') || str_contains($kat, 'cafe')) {
            return 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=600&auto=format&fit=crop&q=70';
        } elseif (str_contains($kat, 'roti') || str_contains($kat, 'kue') || str_contains($kat, 'bakery') || str_contains($kat, 'pastry')) {
            return 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&auto=format&fit=crop&q=70';
        } elseif (str_contains($kat, 'sembako') || str_contains($kat, 'warung') || str_contains($kat, 'sayur')) {
            return 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?w=600&auto=format&fit=crop&q=70';
        } elseif (str_contains($kat, 'laundry') || str_contains($kat, 'jasa')) {
            return 'https://images.unsplash.com/photo-1545173168-9f1947eebb7f?w=600&auto=format&fit=crop&q=70';
        } elseif (str_contains($kat, 'konveksi') || str_contains($kat, 'sablon') || str_contains($kat, 'fashion')) {
            return 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&auto=format&fit=crop&q=70';
        }
        return 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&auto=format&fit=crop&q=70';
    }

    /**
     * Cek apakah usaha sedang buka sekarang berdasarkan jam operasional
     */
    public function getIsBukaSekarangAttribute(): bool
    {
        if (strtolower($this->status_usaha) === 'tutup') {
            return false;
        }

        if (!$this->jam_buka || !$this->jam_tutup) {
            return true;
        }

        try {
            $now = Carbon::now('Asia/Jakarta');
            $open = Carbon::createFromTimeString($this->jam_buka, 'Asia/Jakarta');
            $close = Carbon::createFromTimeString($this->jam_tutup, 'Asia/Jakarta');

            if ($close->lessThan($open)) {
                // Misal buka 18:00 tutup 02:00 malam
                return $now->greaterThanOrEqualTo($open) || $now->lessThanOrEqualTo($close);
            }

            return $now->between($open, $close);
        } catch (\Throwable $e) {
            return strtolower($this->status_usaha) === 'buka';
        }
    }

    /**
     * Label RT Bersih (e.g. RT 001)
     */
    public function getCleanRtLabelAttribute(): string
    {
        $num = preg_replace('/[^0-9]/', '', $this->rt_number ?: '1');
        return 'RT ' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Kisaran harga (otomatis dihitung dari produk atau harga default)
     */
    public function getKisaranHargaFormattedAttribute(): string
    {
        if ($this->harga_mulai > 0 && $this->harga_sampai > 0) {
            return 'Rp ' . number_format($this->harga_mulai, 0, ',', '.') . ' - Rp ' . number_format($this->harga_sampai, 0, ',', '.');
        } elseif ($this->harga_mulai > 0) {
            return 'Mulai Rp ' . number_format($this->harga_mulai, 0, ',', '.');
        }

        // Cek jika ada produk terdaftar
        if ($this->relationLoaded('produks') && $this->produks->count() > 0) {
            $min = $this->produks->min('harga');
            $max = $this->produks->max('harga');
            if ($min && $max && $min != $max) {
                return 'Rp ' . number_format($min, 0, ',', '.') . ' - Rp ' . number_format($max, 0, ',', '.');
            } elseif ($min) {
                return 'Mulai Rp ' . number_format($min, 0, ',', '.');
            }
        }

        return 'Harga Terjangkau';
    }

    /**
     * Link WhatsApp Langsung
     */
    public function getWhatsappUrlAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->no_hp ?: '');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        $text = urlencode("Halo {$this->nama_umkm}, saya warga RW 013 Sukamiskin ingin memesan / menanyakan produk Anda.");
        return "https://wa.me/{$phone}?text={$text}";
    }

    /**
     * Link Google Maps Langsung
     */
    public function getGoogleMapsUrlAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps/dir/?api=1&destination={$this->latitude},{$this->longitude}&travelmode=driving";
        }
        $query = urlencode("{$this->nama_umkm}, {$this->alamat}, Sukamiskin, Arcamanik, Bandung");
        return "https://www.google.com/maps/search/?api=1&query={$query}";
    }

    /**
     * Styling badge kategori
     */
    public function getKategoriBadgeAttribute(): array
    {
        $kat = strtolower($this->kategori . ' ' . $this->jenis_usaha);
        if (str_contains($kat, 'kopi') || str_contains($kat, 'minuman') || str_contains($kat, 'cafe')) {
            return ['bg' => '#E0F2FE', 'text' => '#0369A1', 'icon' => 'coffee', 'label' => 'Minuman'];
        } elseif (str_contains($kat, 'kue') || str_contains($kat, 'roti') || str_contains($kat, 'bakery') || str_contains($kat, 'pastry')) {
            return ['bg' => '#FCE7F3', 'text' => '#BE185D', 'icon' => 'cake', 'label' => 'Kue & Roti'];
        } elseif (str_contains($kat, 'sembako') || str_contains($kat, 'warung')) {
            return ['bg' => '#DCFCE7', 'text' => '#15803D', 'icon' => 'shopping-cart', 'label' => 'Warung & Sembako'];
        } elseif (str_contains($kat, 'camilan') || str_contains($kat, 'gorengan') || str_contains($kat, 'ringan')) {
            return ['bg' => '#FEF3C7', 'text' => '#B45309', 'icon' => 'cookie', 'label' => 'Makanan Ringan'];
        } elseif (str_contains($kat, 'jasa') || str_contains($kat, 'laundry')) {
            return ['bg' => '#EDE9FE', 'text' => '#6D28D9', 'icon' => 'sparkles', 'label' => 'Jasa'];
        } elseif (str_contains($kat, 'konveksi') || str_contains($kat, 'sablon') || str_contains($kat, 'fashion')) {
            return ['bg' => '#FEE2E2', 'text' => '#B91C1C', 'icon' => 'shirt', 'label' => 'Fashion'];
        }
        return ['bg' => '#FFEDD5', 'text' => '#C2410C', 'icon' => 'utensils', 'label' => 'Makanan'];
    }
}
