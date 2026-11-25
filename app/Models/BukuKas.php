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
        'pemilik_dana',
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

    public function getCleanNamaKasAttribute()
    {
        return str_replace(['Buku Kas Kas', 'Kas Kas'], ['Buku Kas', 'Kas'], $this->nama_kas);
    }

    /**
     * Tampilan Rekening Bank yang Disamarkan Sebagian (Keamanan & Privasi)
     * Contoh: Bank BCA •••• 4921
     */
    public function getMaskedRekeningAttribute()
    {
        if (!$this->no_rekening) {
            return 'Kas Tunai (Fisik/Brankas)';
        }

        $bank = $this->nama_bank ?: 'Bank';
        $cleanDigits = preg_replace('/[^0-9]/', '', $this->no_rekening);
        
        if (strlen($cleanDigits) >= 4) {
            $lastFour = substr($cleanDigits, -4);
            return "{$bank} •••• {$lastFour}";
        }

        return "{$bank} " . substr($this->no_rekening, 0, 3) . '••••';
    }

    /**
     * Label Kategori Kas yang Rapi & Informatif
     */
    public function getJenisLabelAttribute()
    {
        return match($this->jenis_kas) {
            'kas_rw' => 'Kas Induk RW 013',
            'kas_rt' => 'Kas Operasional RT ' . ($this->clean_rt ?: '001'),
            'kas_sampah' => 'Kas Sampah & Kebersihan',
            'dana_kegiatan' => 'Dana Kegiatan Khusus',
            default => strtoupper(str_replace('_', ' ', $this->jenis_kas)),
        };
    }

    /**
     * Ikon Lucide yang Sesuai dengan Jenis Rekening
     */
    public function getJenisIconAttribute()
    {
        return match($this->jenis_kas) {
            'kas_rw' => 'landmark',
            'kas_rt' => 'home',
            'kas_sampah' => 'recycle',
            'dana_kegiatan' => 'sparkles',
            default => 'wallet',
        };
    }
}
