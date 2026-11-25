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
        'status_laporan',
        'status_verifikasi_rw',
        'diverifikasi_rw_pada',
        'catatan_verifikasi_rw',
        'laporan_dikunci',
    ];

    protected $casts = [
        'tanggal_evaluasi' => 'date',
        'tenggat_perbaikan' => 'date',
        'diverifikasi_rw_pada' => 'datetime',
        'tahun' => 'integer',
        'skor_kepatuhan' => 'integer',
        'laporan_dikunci' => 'boolean',
    ];

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function getCleanRtAttribute()
    {
        return 'RT ' . str_pad(preg_replace('/[^0-9]/', '', $this->rt_number), 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): array
    {
        $status = $this->status_pertanggungjawaban;

        // Jika status kosong namun memiliki skor, petakan berdasarkan standar nilai evaluasi
        if (empty($status) && isset($this->skor_kepatuhan)) {
            $skor = (float) $this->skor_kepatuhan;
            if ($skor >= 92) {
                $status = 'sangat_baik';
            } elseif ($skor >= 85) {
                $status = 'baik';
            } elseif ($skor >= 75) {
                $status = 'cukup';
            } else {
                $status = 'perlu_pembinaan';
            }
        }

        return match(strtolower(trim((string)$status))) {
            'sangat_baik', 'sangat baik', 'sangat_baik (akuntabel)' => [
                'bg' => 'wm-badge-eval-sangat-baik',
                'label' => 'Sangat Baik',
                'style' => 'background-color: #065F46; color: #FFFFFF; border: 1px solid #047857;',
            ],
            'baik', 'baik & memadai' => [
                'bg' => 'wm-badge-eval-baik',
                'label' => 'Baik',
                'style' => 'background-color: #DCFCE7; color: #166534; border: 1px solid #86EFAC;',
            ],
            'cukup' => [
                'bg' => 'wm-badge-eval-cukup',
                'label' => 'Cukup',
                'style' => 'background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;',
            ],
            'perlu_pembinaan', 'perlu pembinaan', 'perlu_perhatian', 'perlu perhatian' => [
                'bg' => 'wm-badge-eval-pembinaan',
                'label' => 'Perlu Pembinaan',
                'style' => 'background-color: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;',
            ],
            default => [
                'bg' => 'wm-badge-netral',
                'label' => !empty($status) ? ucwords(str_replace('_', ' ', $status)) : 'Belum Ditentukan',
                'style' => 'background-color: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;',
            ],
        };
    }

    public function getStatusLaporanBadgeAttribute(): array
    {
        $status = $this->status_laporan ?: 'Belum Lapor';
        return match(strtolower(trim($status))) {
            'terkirim' => [
                'label' => 'Terkirim',
                'bg' => 'wm-badge-eval-baik',
                'style' => 'background-color: #DCFCE7; color: #166534; border: 1px solid #86EFAC;',
            ],
            default => [
                'label' => 'Belum Lapor',
                'bg' => 'wm-badge-eval-cukup',
                'style' => 'background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;',
            ]
        };
    }

    public function getStatusVerifikasiBadgeAttribute(): array
    {
        $status = $this->status_verifikasi_rw ?: 'Menunggu Pemeriksaan';
        return match(strtolower(trim($status))) {
            'terverifikasi' => [
                'label' => 'Terverifikasi',
                'bg' => 'wm-badge-info',
                'style' => 'background-color: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD;',
            ],
            'perlu klarifikasi', 'perlu_klarifikasi' => [
                'label' => 'Perlu Klarifikasi',
                'bg' => 'wm-badge-eval-pembinaan',
                'style' => 'background-color: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;',
            ],
            default => [
                'label' => 'Menunggu Pemeriksaan',
                'bg' => 'wm-badge-eval-cukup',
                'style' => 'background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;',
            ]
        };
    }
}
