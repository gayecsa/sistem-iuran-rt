<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agendas';

    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal',
        'waktu',
        'lokasi',
        'kategori',
        'penyelenggara',
        'rt_number',
        'penanggung_jawab',
        'peserta',
        'prioritas',
        'status_kegiatan',
        'status_laporan',
        'ringkasan_hasil',
        'dokumentasi_path',
        'laporan_file_path',
        'tanggal_laporan',
        'is_public',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_laporan' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCleanRtAttribute()
    {
        if ($this->penyelenggara === 'rw' || empty($this->rt_number)) {
            return 'RW 013';
        }
        return 'RT ' . str_pad(preg_replace('/[^0-9]/', '', $this->rt_number), 3, '0', STR_PAD_LEFT);
    }

    public function getPenyelenggaraLabelAttribute()
    {
        if ($this->penyelenggara === 'rw') {
            return 'Pengurus RW 013';
        }
        return 'Pengurus ' . $this->clean_rt;
    }

    public function getFormattedTanggalAttribute()
    {
        if (!$this->tanggal) return '-';
        return Carbon::parse($this->tanggal)->translatedFormat('l, d F Y');
    }

    public function getTanggalShortAttribute()
    {
        if (!$this->tanggal) return '-';
        return Carbon::parse($this->tanggal)->translatedFormat('d M Y');
    }

    public function getStatusBadgeAttribute()
    {
        $status = strtolower(trim((string)($this->status_kegiatan ?? $this->status ?? '')));

        return match($status) {
            'akan_datang' => [
                'bg' => 'wm-badge-status-akan_datang',
                'style' => 'background-color: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD;',
                'label' => 'Akan Datang',
                'color' => '#1E40AF',
            ],
            'sedang_berlangsung' => [
                'bg' => 'wm-badge-status-sedang_berlangsung',
                'style' => 'background-color: #FEF3C7; color: #B45309; border: 1px solid #FCD34D;',
                'label' => 'Sedang Berlangsung',
                'color' => '#B45309',
            ],
            'selesai' => [
                'bg' => 'wm-badge-status-selesai',
                'style' => 'background-color: #DCFCE7; color: #15803D; border: 1px solid #86EFAC;',
                'label' => 'Selesai',
                'color' => '#15803D',
            ],
            'dibatalkan' => [
                'bg' => 'wm-badge-status-dibatalkan',
                'style' => 'background-color: #FEE2E2; color: #B91C1C; border: 1px solid #FCA5A5;',
                'label' => 'Dibatalkan',
                'color' => '#B91C1C',
            ],
            'tidak_terlaksana' => [
                'bg' => 'wm-badge-status-tidak_terlaksana',
                'style' => 'background-color: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;',
                'label' => 'Tidak Terlaksana',
                'color' => '#475569',
            ],
            default => [
                'bg' => 'wm-badge-status-fallback',
                'style' => 'background-color: #F1F5F9; color: #64748B; border: 1px solid #CBD5E1;',
                'label' => !empty($status) ? ucwords(str_replace('_', ' ', $status)) : 'Status Belum Ditentukan',
                'color' => '#64748B',
            ],
        };
    }

    public function getPrioritasBadgeAttribute()
    {
        $prioritas = strtolower(trim((string)($this->prioritas ?? '')));

        return match($prioritas) {
            'darurat', 'mendesak' => [
                'bg' => 'wm-badge-prioritas-darurat',
                'style' => 'background-color: #DC2626; color: #FFFFFF; border: 1px solid #B91C1C;',
                'label' => 'Mendesak / Darurat',
                'icon' => 'alert-triangle',
                'color' => '#FFFFFF',
            ],
            'tinggi' => [
                'bg' => 'wm-badge-prioritas-tinggi',
                'style' => 'background-color: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;',
                'label' => 'Prioritas Tinggi',
                'icon' => 'alert-circle',
                'color' => '#991B1B',
            ],
            'sedang' => [
                'bg' => 'wm-badge-prioritas-sedang',
                'style' => 'background-color: #FEF3C7; color: #92400E; border: 1px solid #FCD34D;',
                'label' => 'Prioritas Sedang',
                'icon' => 'clock',
                'color' => '#92400E',
            ],
            'rendah' => [
                'bg' => 'wm-badge-prioritas-rendah',
                'style' => 'background-color: #DCFCE7; color: #166534; border: 1px solid #86EFAC;',
                'label' => 'Prioritas Rendah',
                'icon' => 'minus-circle',
                'color' => '#166534',
            ],
            default => [
                'bg' => 'wm-badge-prioritas-sedang',
                'style' => 'background-color: #FEF3C7; color: #92400E; border: 1px solid #FCD34D;',
                'label' => !empty($prioritas) ? 'Prioritas ' . ucfirst($prioritas) : 'Prioritas Sedang',
                'icon' => 'clock',
                'color' => '#92400E',
            ],
        };
    }

    public function getKategoriLabelAttribute()
    {
        return match($this->kategori) {
            'rapat_koordinasi' => 'Rapat Koordinasi',
            'kegiatan_sosial' => 'Kegiatan Sosial',
            'kegiatan_warga' => 'Kegiatan Warga',
            'kesehatan' => 'Kesehatan',
            'kebersihan' => 'Kebersihan',
            'keamanan' => 'Keamanan',
            default => ucfirst(str_replace('_', ' ', $this->kategori ?: 'Lainnya')),
        };
    }

    public function getKategoriIconAttribute()
    {
        return match($this->kategori) {
            'rapat_koordinasi' => 'users',
            'kegiatan_sosial' => 'heart',
            'kegiatan_warga' => 'flag',
            'kesehatan' => 'heart-pulse',
            'kebersihan' => 'trash-2',
            'keamanan' => 'shield-check',
            default => 'calendar',
        };
    }

    public function getStatusLaporanBadgeAttribute()
    {
        $statusLap = strtolower(trim((string)($this->status_laporan ?? '')));

        return match($statusLap) {
            'laporan_lengkap' => [
                'bg' => 'wm-badge-laporan-lengkap',
                'style' => 'background-color: #DCFCE7; color: #166534; border: 1px solid #86EFAC;',
                'label' => 'Laporan Lengkap',
                'icon' => 'check-circle-2',
                'color' => '#166534',
            ],
            'laporan_dikirim' => [
                'bg' => 'wm-badge-laporan-dikirim',
                'style' => 'background-color: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD;',
                'label' => 'Laporan Dikirim',
                'icon' => 'send',
                'color' => '#0369A1',
            ],
            'menunggu_laporan' => [
                'bg' => 'wm-badge-laporan-menunggu',
                'style' => 'background-color: #FEF3C7; color: #92400E; border: 1px solid #FCD34D;',
                'label' => 'Menunggu Laporan',
                'icon' => 'clock',
                'color' => '#92400E',
            ],
            default => [
                'bg' => 'wm-badge-laporan-belum',
                'style' => 'background-color: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;',
                'label' => 'Belum Ada Laporan',
                'icon' => 'file-x',
                'color' => '#475569',
            ],
        };
    }
}
