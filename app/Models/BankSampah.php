<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_unit',
        'rt_number',
        'lokasi',
        'jadwal',
        'petugas_id',
        'kategori_diterima',
        'total_terkumpul_kg',
        'is_active',
        'keterangan_simulasi',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_terkumpul_kg' => 'integer',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function transaksis()
    {
        return $this->hasMany(BankSampahTransaksi::class, 'bank_sampah_id');
    }

    public function getPetugasNameAttribute(): string
    {
        if ($this->petugas) {
            return $this->petugas->name;
        }
        return 'Petugas Lingkungan RW 013';
    }

    public function getPetugasAvatarUrlAttribute(): string
    {
        if ($this->petugas) {
            return $this->petugas->avatar_url;
        }
        return asset('images/avatar-default.svg');
    }

    /**
     * Total berat sampah aktual dari transaksi berstatus SELESAI
     */
    public function getTotalAktualKgAttribute(): float
    {
        return (float) $this->transaksis()->where('status', 'selesai')->sum('total_berat_kg');
    }

    /**
     * Total nilai rupiah aktual dari transaksi berstatus SELESAI
     */
    public function getTotalAktualRupiahAttribute(): float
    {
        return (float) $this->transaksis()->where('status', 'selesai')->sum('total_rupiah');
    }

    /**
     * Jumlah transaksi selesai
     */
    public function getTotalTransaksiSelesaiCountAttribute(): int
    {
        return $this->transaksis()->where('status', 'selesai')->count();
    }
}
