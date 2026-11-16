<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditEvaluasiRt extends Model
{
    use HasFactory;

    protected $table = 'audit_evaluasi_rts';

    protected $fillable = [
        'rt_number',
        'tahun',
        'periode',
        'skor_kepatuhan',
        'status_pertanggungjawaban',
        'catatan_evaluasi',
        'rekomendasi_rw',
        'tenggat_perbaikan',
        'status_tindak_lanjut',
        'evaluator_id',
        'tanggal_evaluasi',
    ];

    protected $casts = [
        'tanggal_evaluasi' => 'date',
        'tenggat_perbaikan' => 'date',
        'tahun' => 'integer',
        'skor_kepatuhan' => 'integer',
    ];

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function getCleanRtAttribute()
    {
        return 'RT ' . str_pad(preg_replace('/[^0-9]/', '', $this->rt_number), 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status_pertanggungjawaban) {
            'sangat_baik' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300', 'label' => 'Sangat Baik (Akuntabel)'],
            'baik' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-300', 'label' => 'Baik & Memadai'],
            'cukup' => ['bg' => 'bg-teal-100 text-teal-800 border-teal-300', 'label' => 'Cukup'],
            'perlu_pembinaan', 'perlu_perhatian' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-300', 'label' => 'Perlu Pembinaan'],
            default => ['bg' => 'bg-rose-100 text-rose-800 border-rose-300', 'label' => 'Belum Lengkap / Tertunda'],
        };
    }
}
