<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditKegiatan extends Model
{
    use HasFactory;

    protected $table = 'audit_kegiatans';

    protected $fillable = [
        'kode_kegiatan',
        'nama_kegiatan',
        'kategori_kegiatan',
        'rt_number',
        'penanggung_jawab',
        'buku_kas_id',
        'anggaran_awal',
        'realisasi_anggaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'tahun',
        'status',
        'status_audit',
        'dokumentasi_path',
        'lpj_dokumen_path',
        'catatan_kegiatan',
    ];

    protected $casts = [
        'anggaran_awal' => 'decimal:2',
        'realisasi_anggaran' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tahun' => 'integer',
    ];

    public function bukuKas()
    {
        return $this->belongsTo(BukuKas::class, 'buku_kas_id');
    }

    public function temuans()
    {
        return $this->hasMany(AuditTemuan::class, 'kegiatan_id');
    }

    public function getCleanRtAttribute()
    {
        if (!$this->rt_number) return 'RW';
        return 'RT ' . str_pad(preg_replace('/[^0-9]/', '', $this->rt_number), 3, '0', STR_PAD_LEFT);
    }

    public function getPersentaseRealisasiAttribute()
    {
        if ($this->anggaran_awal <= 0) return 0;
        return min(100, round(($this->realisasi_anggaran / $this->anggaran_awal) * 100));
    }

    public function getSisaAnggaranAttribute()
    {
        return max(0, $this->anggaran_awal - $this->realisasi_anggaran);
    }

    public function getKategoriLabelAttribute()
    {
        return match($this->kategori_kegiatan) {
            'pembangunan_fasilitas' => 'Pembangunan & Fasilitas',
            'kebersihan_lingkungan' => 'Kebersihan Lingkungan',
            'kegiatan_sosial' => 'Kegiatan Sosial & Warga',
            'pengadaan_barang' => 'Pengadaan Inventaris',
            'pemberdayaan_masyarakat' => 'Pemberdayaan Masyarakat',
            default => 'Kegiatan Umum / Lainnya',
        };
    }
}
