<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranIuran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_iurans';

    protected $fillable = [
        'nomor_pembayaran',
        'tagihan_keluarga_id',
        'kartu_keluarga_id',
        'tanggal_bayar',
        'nominal_bayar',
        'metode_pembayaran',
        'kanal_pembayaran',
        'status_setoran_rw',
        'disetor_ke_rw_pada',
        'bukti_pembayaran_path',
        'catatan',
        'diterima_oleh_id',
        'nama_penerima',
        'idempotency_key',
        'status',
        'alasan_batal',
        'dibatalkan_oleh_id',
        'dibatalkan_pada',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'disetor_ke_rw_pada' => 'datetime',
        'dibatalkan_pada' => 'datetime',
        'nominal_bayar' => 'decimal:2',
    ];

    public function tagihanKeluarga()
    {
        return $this->belongsTo(TagihanKeluarga::class, 'tagihan_keluarga_id');
    }

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'diterima_oleh_id');
    }

    public function dibatalkanOleh()
    {
        return $this->belongsTo(User::class, 'dibatalkan_oleh_id');
    }

    public function alokasis()
    {
        return $this->hasMany(PembayaranAlokasi::class, 'pembayaran_iuran_id');
    }
}
