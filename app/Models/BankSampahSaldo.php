<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampahSaldo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_kg_terkumpul',
        'saldo_aktif',
        'total_penarikan',
        'total_dihasilkan',
        'transaksi_terakhir_at',
    ];

    protected $casts = [
        'total_kg_terkumpul' => 'decimal:2',
        'saldo_aktif' => 'decimal:2',
        'total_penarikan' => 'decimal:2',
        'total_dihasilkan' => 'decimal:2',
        'transaksi_terakhir_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSaldoAktifFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->saldo_aktif, 0, ',', '.');
    }

    public function getTotalDihasilkanFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_dihasilkan, 0, ',', '.');
    }
}
