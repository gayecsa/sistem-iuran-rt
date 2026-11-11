<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampahTransaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'bank_sampah_id',
        'rt_number',
        'petugas_id',
        'status',
        'metode_penyerahan',
        'tanggal_pengajuan',
        'tanggal_dijadwalkan',
        'tanggal_penimbangan',
        'tanggal_selesai',
        'catatan_warga',
        'catatan_petugas',
        'alasan_penolakan',
        'total_berat_kg',
        'total_rupiah',
        'is_simulasi',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_dijadwalkan' => 'datetime',
        'tanggal_penimbangan' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'total_berat_kg' => 'decimal:2',
        'total_rupiah' => 'decimal:2',
        'is_simulasi' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function bankSampah()
    {
        return $this->belongsTo(BankSampah::class, 'bank_sampah_id');
    }

    public function items()
    {
        return $this->hasMany(BankSampahTransaksiItem::class, 'transaksi_id');
    }

    public function ledger()
    {
        return $this->hasOne(BankSampahLedger::class, 'transaksi_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'menunggu' => 'Menunggu Verifikasi',
            'dijadwalkan' => 'Dijadwalkan',
            'sedang_ditimbang' => 'Sedang Ditimbang',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'selesai' => 'Selesai & Masuk Tabungan',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan Warga',
            default => ucfirst(str_replace('_', ' ', $this->status))
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'menunggu' => [
                'bg' => 'bg-amber-100 text-amber-800 border-amber-300',
                'dot' => 'bg-amber-500',
                'icon' => 'clock'
            ],
            'dijadwalkan' => [
                'bg' => 'bg-sky-100 text-sky-800 border-sky-300',
                'dot' => 'bg-sky-500',
                'icon' => 'calendar'
            ],
            'sedang_ditimbang' => [
                'bg' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                'dot' => 'bg-indigo-500',
                'icon' => 'scale'
            ],
            'menunggu_konfirmasi' => [
                'bg' => 'bg-orange-100 text-orange-800 border-orange-300',
                'dot' => 'bg-orange-500',
                'icon' => 'alert-circle'
            ],
            'selesai' => [
                'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'dot' => 'bg-emerald-500',
                'icon' => 'check-circle'
            ],
            'ditolak' => [
                'bg' => 'bg-rose-100 text-rose-800 border-rose-300',
                'dot' => 'bg-rose-500',
                'icon' => 'x-circle'
            ],
            'dibatalkan' => [
                'bg' => 'bg-slate-100 text-slate-700 border-slate-300',
                'dot' => 'bg-slate-400',
                'icon' => 'slash'
            ],
            default => [
                'bg' => 'bg-slate-100 text-slate-700 border-slate-300',
                'dot' => 'bg-slate-400',
                'icon' => 'help-circle'
            ]
        };
    }

    public function getTotalRupiahFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_rupiah, 0, ',', '.');
    }

    public function getTotalBeratFormattedAttribute(): string
    {
        return number_format($this->total_berat_kg, 2, ',', '.') . ' kg';
    }

    public static function generateKodeTransaksi(): string
    {
        $prefix = 'BS-' . date('Ym') . '-';
        $last = self::where('kode_transaksi', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last->kode_transaksi, strlen($prefix));
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }
}
