<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IuranTarif extends Model
{
    use HasFactory;

    protected $table = 'iuran_tarifs';

    protected $fillable = [
        'kode_tarif',
        'nama_tarif',
        'jenis_kas',
        'buku_kas_id',
        'nominal',
        'metode_perhitungan',
        'periode',
        'rt_target',
        'jenis_iuran',
        'tingkat_pengelola',
        'urutan_prioritas',
        'berlaku_mulai',
        'keterangan_musyawarah',
        'is_active',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'berlaku_mulai' => 'date',
        'is_active' => 'boolean',
        'urutan_prioritas' => 'integer',
    ];

    public function bukuKas()
    {
        return $this->belongsTo(BukuKas::class, 'buku_kas_id');
    }

    public function histories()
    {
        return $this->hasMany(IuranTarifHistory::class, 'iuran_tarif_id')->orderBy('created_at', 'desc');
    }
}
