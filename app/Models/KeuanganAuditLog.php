<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeuanganAuditLog extends Model
{
    use HasFactory;

    protected $table = 'keuangan_audit_logs';

    protected $fillable = [
        'user_id',
        'transaksi_id',
        'tagihan_id',
        'action',
        'buku_kas_kode',
        'deskripsi',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(TransaksiKeuangan::class, 'transaksi_id');
    }

    public function tagihan()
    {
        return $this->belongsTo(TagihanKeluarga::class, 'tagihan_id');
    }
}
