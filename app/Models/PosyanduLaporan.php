<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PosyanduLaporan extends Model
{
    use HasFactory;

    protected $table = 'posyandu_laporans';

    protected $fillable = [
        'jadwal_id',
        'rt_number',
        'nomor_laporan',
        'tanggal_laporan',
        'total_hadir',
        'total_bayi_balita',
        'total_ibu_hamil',
        'total_lansia',
        'ringkasan_layanan',
        'kendala',
        'catatan_evaluasi_rt',
        'foto_dokumentasi_path',
        'penanggung_jawab_id',
        'status_laporan',
        'catatan_rw',
        'tanggal_evaluasi_rw',
        'evaluator_rw_id',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        'tanggal_evaluasi_rw' => 'datetime',
    ];

    public function jadwal()
    {
        return $this->belongsTo(PosyanduJadwal::class, 'jadwal_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id');
    }

    public function evaluatorRw()
    {
        return $this->belongsTo(User::class, 'evaluator_rw_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status_laporan) {
            'draft' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'dikirim_ke_rw' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            'disetujui_rw' => 'bg-success-subtle text-success border border-success-subtle',
            'dievaluasi_rw' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            default => 'bg-light text-dark',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_laporan) {
            'draft' => 'Draft RT',
            'dikirim_ke_rw' => 'Menunggu Verifikasi RW',
            'disetujui_rw' => 'Disetujui RW (Selesai)',
            'dievaluasi_rw' => 'Ada Catatan Evaluasi RW',
            default => ucfirst(str_replace('_', ' ', $this->status_laporan)),
        };
    }

    public function getTanggalLaporanFormattedAttribute(): string
    {
        return Carbon::parse($this->tanggal_laporan)->translatedFormat('d F Y');
    }
}
