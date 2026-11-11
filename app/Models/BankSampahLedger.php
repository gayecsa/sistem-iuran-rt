<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSampahLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaksi_id',
        'tipe',
        'debit',
        'kredit',
        'saldo_sebelum',
        'saldo_sesudah',
        'keterangan',
        'petugas_id',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(BankSampahTransaksi::class, 'transaksi_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
