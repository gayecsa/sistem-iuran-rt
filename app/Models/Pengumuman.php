<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';
    protected $fillable = ['judul', 'isi', 'kategori', 'gambar', 'tanggal_aktif'];

    protected $casts = [
        'tanggal_aktif' => 'datetime',
    ];

    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            if (str_starts_with($this->gambar, 'http')) {
                return $this->gambar;
            }
            if (Storage::disk('public')->exists($this->gambar)) {
                return asset('storage/' . $this->gambar);
            }
            if (file_exists(public_path('storage/' . $this->gambar))) {
                return asset('storage/' . $this->gambar);
            }
        }
        return null;
    }

    public function getBadgeClassAttribute()
    {
        return match(strtolower($this->kategori)) {
            'penting' => 'bg-rose-100 text-rose-800 border-rose-300',
            'posyandu', 'kesehatan', 'lingkungan' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'kegiatan', 'umkm', 'selesai' => 'bg-amber-100 text-amber-900 border-amber-300',
            'keamanan' => 'bg-purple-100 text-purple-800 border-purple-300',
            default => 'bg-blue-100 text-[#1456A0] border-blue-300',
        };
    }

    public function getFallbackGradientAttribute()
    {
        return match(strtolower($this->kategori)) {
            'penting' => 'from-[#9B1C1C] via-[#DC2626] to-[#F87171]',
            'posyandu', 'kesehatan', 'lingkungan' => 'from-[#0E5A3E] via-[#16865D] to-[#34D399]',
            'kegiatan', 'umkm', 'selesai' => 'from-[#92400E] via-[#D97706] to-[#FBBF24]',
            'keamanan' => 'from-[#4C1D95] via-[#6D28D9] to-[#A78BFA]',
            default => 'from-[#0F3966] via-[#1456A0] to-[#2878D0]',
        };
    }

    public function getFallbackIconAttribute()
    {
        return match(strtolower($this->kategori)) {
            'penting' => 'alert-triangle',
            'posyandu', 'kesehatan', 'lingkungan' => 'heart-pulse',
            'kegiatan', 'umkm', 'selesai' => 'calendar-days',
            'keamanan' => 'shield-alert',
            default => 'megaphone',
        };
    }
}
