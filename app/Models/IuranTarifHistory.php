<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IuranTarifHistory extends Model
{
    use HasFactory;

    protected $table = 'iuran_tarif_histories';

    protected $fillable = [
        'iuran_tarif_id',
        'nominal_lama',
        'nominal_baru',
        'tanggal_perubahan',
        'diubah_oleh_id',
        'alasan_perubahan',
    ];

    protected $casts = [
        'nominal_lama' => 'decimal:2',
        'nominal_baru' => 'decimal:2',
        'tanggal_perubahan' => 'date',
    ];

    public function tarif()
    {
        return $this->belongsTo(IuranTarif::class, 'iuran_tarif_id');
    }

    public function diubahOleh()
    {
        return $this->belongsTo(User::class, 'diubah_oleh_id');
    }
}
