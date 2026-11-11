<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKeuangan extends Model
{
    use HasFactory;

    protected $table = 'transaksi_keuangans';

    protected $fillable = [
        'nomor_transaksi',
        'buku_kas_id',
        'tagihan_keluarga_id',
        'tanggal_transaksi',
        'jenis_transaksi',
        'kategori',
        'nominal',
        'saldo_sebelumnya',
        'saldo_setelahnya',
        'metode_pembayaran',
        'penyetor_penerima',
        'keterangan',
        'petugas_id',
        'nama_petugas',
        'bukti_transaksi_path',
        'transfer_ref_id',
        'status',
        'dibatalkan_oleh_id',
        'alasan_batal',
        'dibatalkan_pada',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'saldo_sebelumnya' => 'decimal:2',
        'saldo_setelahnya' => 'decimal:2',
        'tanggal_transaksi' => 'date',
        'dibatalkan_pada' => 'datetime',
    ];

    public function bukuKas()
    {
        return $this->belongsTo(BukuKas::class, 'buku_kas_id');
    }

    public function tagihanKeluarga()
    {
        return $this->belongsTo(TagihanKeluarga::class, 'tagihan_keluarga_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function dibatalkanOleh()
    {
        return $this->belongsTo(User::class, 'dibatalkan_oleh_id');
    }

    public function getJenisBadgeAttribute()
    {
        return match($this->jenis_transaksi) {
            'pemasukan' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'transfer_masuk' => 'bg-teal-100 text-teal-800 border-teal-300',
            'pengeluaran' => 'bg-rose-100 text-rose-800 border-rose-300',
            'transfer_keluar' => 'bg-amber-100 text-amber-800 border-amber-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
