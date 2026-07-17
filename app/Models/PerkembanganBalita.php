<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerkembanganBalita extends Model
{
    use HasFactory;

    protected $table = 'perkembangan_balitas';

    protected $fillable = [
        'user_id',
        'tanggal_pemeriksaan',
        'berat_badan',
        'tinggi_badan',
        'status_gizi',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
