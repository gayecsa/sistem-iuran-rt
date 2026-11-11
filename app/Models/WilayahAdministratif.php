<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahAdministratif extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'parent_id',
        'kode',
        'nama',
        'keterangan',
        'kode_pos',
        'pusat_pemerintahan',
        'latitude',
        'longitude',
    ];

    public function parent()
    {
        return $this->belongsTo(WilayahAdministratif::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(WilayahAdministratif::class, 'parent_id');
    }

    public function subWilayah()
    {
        return $this->children()->with('subWilayah');
    }
}
