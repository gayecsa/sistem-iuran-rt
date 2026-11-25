<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cctv extends Model
{
    protected $fillable = [
        'nama',
        'lokasi',
        'rt',
        'latitude',
        'longitude',
        'stream_url',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function accessRequests()
    {
        return $this->hasMany(CctvAccessRequest::class, 'cctv_id');
    }

    public function getCleanRtAttribute(): string
    {
        $num = intval(preg_replace('/[^0-9]/', '', (string)($this->rt ?? '1')));
        return str_pad($num ?: 1, 3, '0', STR_PAD_LEFT);
    }
}
