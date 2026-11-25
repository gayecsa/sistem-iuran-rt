<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IuranKeringanan extends Model
{
    use HasFactory;

    protected $table = 'iuran_keringanans';

    protected $fillable = [
        'kartu_keluarga_id',
        'iuran_tarif_id',
        'jenis_keringanan',
        'nilai_keringanan',
        'alasan',
        'periode_mulai',
        'periode_selesai',
        'dokumen_pendukung_path',
        'disetujui_oleh_id',
        'status',
    ];

    protected $casts = [
        'nilai_keringanan' => 'decimal:2',
    ];

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function iuranTarif()
    {
        return $this->belongsTo(IuranTarif::class, 'iuran_tarif_id');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh_id');
    }
}
