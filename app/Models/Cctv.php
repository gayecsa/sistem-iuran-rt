<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cctv extends Model
{
    protected $fillable = ['nama', 'lokasi', 'stream_url', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];
}
