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

    public function getPrioritasBadgeAttribute()
    {
        return match($this->tingkat_prioritas) {
            'kritis' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-300', 'label' => 'Kritis'],
            'tinggi' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-300', 'label' => 'Tinggi'],
            'sedang' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-300', 'label' => 'Sedang'],
            default => ['bg' => 'bg-slate-100 text-slate-800 border-slate-300', 'label' => 'Rendah'],
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'selesai' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300', 'label' => 'Selesai'],
            'perlu_tindak_lanjut' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-300', 'label' => 'Perlu Tindak Lanjut'],
            'menunggu_klarifikasi' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-300', 'label' => 'Menunggu Klarifikasi'],
            'dalam_pemeriksaan' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-300', 'label' => 'Dalam Pemeriksaan'],
            default => ['bg' => 'bg-slate-100 text-slate-800 border-slate-300', 'label' => 'Perlu Diperiksa'],
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
