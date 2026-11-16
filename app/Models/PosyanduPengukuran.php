<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PosyanduPengukuran extends Model
{
    use HasFactory;

    protected $table = 'posyandu_pengukurans';

    protected $fillable = [
        'peserta_id',
        'jadwal_id',
        'rt_number',
        'tanggal_pemeriksaan',
        'usia_bulan',
        'usia_tahun',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'usia_kehamilan_minggu',
        'tekanan_darah_sistol',
        'tekanan_darah_diastol',
        'keluhan',
        'status_pertumbuhan',
        'tindakan_layanan',
        'catatan_petugas',
        'anjuran_rujukan',
        'petugas_pencatat_id',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'berat_badan' => 'float',
        'tinggi_badan' => 'float',
        'lingkar_kepala' => 'float',
    ];

    public function peserta()
    {
        return $this->belongsTo(PosyanduPeserta::class, 'peserta_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(PosyanduJadwal::class, 'jadwal_id');
    }

    public function petugasPencatat()
    {
        return $this->belongsTo(User::class, 'petugas_pencatat_id');
    }

    public function getTekananDarahAttribute(): ?string
    {
        if ($this->tekanan_darah_sistol && $this->tekanan_darah_diastol) {
            return "{$this->tekanan_darah_sistol}/{$this->tekanan_darah_diastol} mmHg";
        }
        return null;
    }

    public function getTanggalFormattedAttribute(): string
    {
        return Carbon::parse($this->tanggal_pemeriksaan)->translatedFormat('d M Y');
    }
}
