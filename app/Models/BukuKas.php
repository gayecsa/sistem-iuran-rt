<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuKas extends Model
{
    use HasFactory;

    protected $table = 'buku_kas';

    protected $fillable = [
        'kode_kas',
        'nama_kas',
        'jenis_kas',
        'rt_number',
        'target_anggaran',
        'saldo_awal',
        'saldo_sekarang',
        'penanggung_jawab_id',
        'nama_penanggung_jawab',
        'no_rekening',
        'nama_bank',
        'atas_nama',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'target_anggaran' => 'decimal:2',
        'saldo_awal' => 'decimal:2',
        'saldo_sekarang' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id');
    }

    public function transaksis()
    {
        return $this->hasMany(TransaksiKeuangan::class, 'buku_kas_id');
    }

    /**
     * Hitung ulang saldo berdasarkan: Saldo Awal + Total Pemasukan - Total Pengeluaran
     */
    public function recalculateSaldo()
    {
        $pemasukan = $this->transaksis()
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
            ->sum('nominal');

        $pengeluaran = $this->transaksis()
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
            ->sum('nominal');

        $saldoSekarang = (float) $this->saldo_awal + (float) $pemasukan - (float) $pengeluaran;
        $this->update(['saldo_sekarang' => $saldoSekarang]);

        return $saldoSekarang;
    }

    public function getCleanRtAttribute()
    {
        if (!$this->rt_number) return null;
        return str_pad(preg_replace('/[^0-9]/', '', $this->rt_number), 3, '0', STR_PAD_LEFT);
    }
}
