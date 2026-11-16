<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosyanduKader extends Model
{
    use HasFactory;

    protected $table = 'posyandu_kaders';

    protected $fillable = [
        'user_id',
        'rt_number',
        'nama_kader',
        'no_telepon',
        'jabatan',
        'is_aktif',
        'foto_path',
        'catatan',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto_path) {
            return asset('storage/' . $this->foto_path);
        }
        if ($this->user && $this->user->avatar_url) {
            return $this->user->avatar_url;
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_kader) . '&background=E8F5E9&color=1B5E20';
    }
}
