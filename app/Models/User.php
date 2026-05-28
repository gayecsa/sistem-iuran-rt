<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\KasRt;
use App\Models\Pembayaran;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'rt_number',
        'house_number',
        'phone',
        'address',
        'status_rumah',
        'is_active',
        'profile_photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function kasRt()
    {
        return $this->hasManyThrough(KasRt::class, Pembayaran::class, 'user_id', 'pembayaran_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isBendahara()
    {
        return $this->role === 'bendahara';
    }

    public function isWarga()
    {
        return $this->role === 'warga';
    }
}
