<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanKeluarga extends Model
{
    use HasFactory;

    protected $table = 'tagihan_keluargas';

    protected $fillable = [
        'nomor_tagihan',
        'kartu_keluarga_id',
        'kepala_keluarga_id',
        'rt_number',
        'periode',
        'nominal_rt',
        'nominal_rw',
        'nominal_sampah',
        'total_tagihan',
        'total_dibayar',
        'sisa_tagihan',
        'status',
        'tanggal_terbit',
        'jatuh_tempo',
        'tanggal_lunas',
        'metode_pembayaran_terakhir',
        'bukti_pembayaran_path',
        'catatan_warga',
        'catatan_petugas',
        'alasan_pembebasan',
        'diverifikasi_oleh_id',
        'diverifikasi_pada',
    ];

    protected $casts = [
        'nominal_rt' => 'decimal:2',
        'nominal_rw' => 'decimal:2',
        'nominal_sampah' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
        'total_dibayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'tanggal_terbit' => 'date',
        'jatuh_tempo' => 'date',
        'tanggal_lunas' => 'datetime',
        'diverifikasi_pada' => 'datetime',
    ];

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function kepalaKeluarga()
    {
        return $this->belongsTo(User::class, 'kepala_keluarga_id');
    }

    public function diverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh_id');
    }

    public function transaksis()
    {
        return $this->hasMany(TransaksiKeuangan::class, 'tagihan_keluarga_id');
    }

    public function getCleanRtAttribute()
    {
        return str_pad(preg_replace('/[^0-9]/', '', $this->rt_number), 3, '0', STR_PAD_LEFT);
    }

    public function getBadgeClassAttribute()
    {
        return match($this->status) {
            'Lunas' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'Menunggu Konfirmasi' => 'bg-amber-100 text-amber-800 border-amber-300',
            'Dibayar Sebagian' => 'bg-blue-100 text-blue-800 border-blue-300',
            'Terlambat' => 'bg-rose-100 text-rose-800 border-rose-300',
            'Dibebaskan' => 'bg-purple-100 text-purple-800 border-purple-300',
            'Dibatalkan' => 'bg-slate-200 text-slate-700 border-slate-300',
            default => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }
}
