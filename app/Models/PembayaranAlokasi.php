<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranAlokasi extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_alokasis';

    protected $fillable = [
        'tagihan_keluarga_id',
        'pembayaran_iuran_id',
        'tagihan_item_id',
        'buku_kas_id',
        'transaksi_keuangan_id',
        'nama_komponen',
        'nominal_alokasi',
        'pemilik_dana',
        'status_alokasi',
    ];

    protected $casts = [
        'nominal_alokasi' => 'decimal:2',
    ];

    public function tagihanKeluarga()
    {
        return $this->belongsTo(TagihanKeluarga::class, 'tagihan_keluarga_id');
    }

    public function pembayaranIuran()
    {
        return $this->belongsTo(PembayaranIuran::class, 'pembayaran_iuran_id');
    }

    public function tagihanItem()
    {
        return $this->belongsTo(TagihanItem::class, 'tagihan_item_id');
    }

    public function bukuKas()
    {
        return $this->belongsTo(BukuKas::class, 'buku_kas_id');
    }

    public function transaksiKeuangan()
    {
        return $this->belongsTo(TransaksiKeuangan::class, 'transaksi_keuangan_id');
    }
}
