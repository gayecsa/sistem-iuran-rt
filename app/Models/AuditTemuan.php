<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTemuan extends Model
{
    use HasFactory;

    protected $table = 'audit_temuans';

    protected $fillable = [
        'kode_temuan',
        'judul_temuan',
        'kategori_masalah',
        'rt_number',
        'kegiatan_id',
        'transaksi_id',
        'buku_kas_id',
        'tanggal_temuan',
        'tahun',
        'deskripsi_masalah',
        'bukti_pendukung_path',
        'tingkat_prioritas',
        'status',
        'tenggat_tindak_lanjut',
        'catatan_evaluasi_rw',
        'tanggapan_rt',
        'tanggal_tanggapan',
        'auditor_id',
        'diselesaikan_pada',
    ];

    protected $casts = [
        'tanggal_temuan' => 'date',
        'tenggat_tindak_lanjut' => 'date',
        'tanggal_tanggapan' => 'datetime',
        'diselesaikan_pada' => 'datetime',
        'tahun' => 'integer',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(AuditKegiatan::class, 'kegiatan_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(TransaksiKeuangan::class, 'transaksi_id');
    }

    public function bukuKas()
    {
        return $this->belongsTo(BukuKas::class, 'buku_kas_id');
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function getCleanRtAttribute()
    {
        if (!$this->rt_number) return 'RW 013';
        return 'RT ' . str_pad(preg_replace('/[^0-9]/', '', $this->rt_number), 3, '0', STR_PAD_LEFT);
    }

    public function getPrioritasBadgeAttribute(): array
    {
        return match(strtolower(trim((string)$this->tingkat_prioritas))) {
            'kritis' => [
                'bg' => 'wm-badge-prioritas-kritis',
                'label' => 'Kritis',
                'style' => 'background-color: #FEE2E2; color: #991B1B; border: 1px solid #F87171;',
            ],
            'tinggi' => [
                'bg' => 'wm-badge-prioritas-tinggi',
                'label' => 'Tinggi',
                'style' => 'background-color: #FFEDD5; color: #9A3412; border: 1px solid #FDBA74;',
            ],
            'sedang' => [
                'bg' => 'wm-badge-prioritas-sedang',
                'label' => 'Sedang',
                'style' => 'background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;',
            ],
            'rendah' => [
                'bg' => 'wm-badge-prioritas-rendah',
                'label' => 'Rendah',
                'style' => 'background-color: #E2E8F0; color: #334155; border: 1px solid #CBD5E1;',
            ],
            default => [
                'bg' => 'wm-badge-netral',
                'label' => !empty($this->tingkat_prioritas) ? ucfirst($this->tingkat_prioritas) : 'Belum Ditentukan',
                'style' => 'background-color: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;',
            ],
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match(strtolower(trim((string)$this->status))) {
            'selesai' => [
                'bg' => 'wm-badge-status-selesai',
                'label' => 'Selesai',
                'style' => 'background-color: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;',
            ],
            'menunggu_verifikasi', 'menunggu_klarifikasi', 'menunggu klarifikasi' => [
                'bg' => 'wm-badge-status-verifikasi',
                'label' => 'Menunggu Verifikasi',
                'style' => 'background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;',
            ],
            'dalam_proses', 'dalam_pemeriksaan', 'dalam pemeriksaan' => [
                'bg' => 'wm-badge-status-proses',
                'label' => 'Dalam Proses',
                'style' => 'background-color: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD;',
            ],
            'terbuka', 'perlu_tindak_lanjut', 'perlu_diperiksa', 'perlu tindak lanjut', 'perlu diperiksa' => [
                'bg' => 'wm-badge-status-terbuka',
                'label' => 'Terbuka',
                'style' => 'background-color: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;',
            ],
            default => [
                'bg' => 'wm-badge-netral',
                'label' => !empty($this->status) ? ucwords(str_replace('_', ' ', $this->status)) : 'Belum Ditentukan',
                'style' => 'background-color: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;',
            ],
        };
    }

    public function getKategoriLabelAttribute()
    {
        return match($this->kategori_masalah) {
            'transaksi_tanpa_bukti' => 'Transaksi Tanpa Bukti / Nota',
            'selisih_anggaran' => 'Selisih / Deviasi Anggaran',
            'kegiatan_terlambat' => 'Keterlambatan Pelaksanaan Proyek',
            'prosedur_pelaporan' => 'Pelanggaran Prosedur Pelaporan',
            'keuangan' => 'Ketidaksesuaian Pembukuan Kas',
            'administrasi' => 'Administrasi & Arsip',
            default => 'Temuan Lainnya',
        };
    }
}
