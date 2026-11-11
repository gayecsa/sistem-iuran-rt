<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Pembayaran;
use App\Models\KasRt;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'rt_number',
        'rw_number',
        'house_number',
        'phone',
        'address',
        'status_rumah',
        'is_active',
        'profile_photo',
        'nik',
        'no_kk',
        'gender',
        'tanggal_lahir',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function kasRt()
    {
        return $this->hasMany(KasRt::class, 'dibuat_oleh', 'name');
    }

    public function perkembanganBalita()
    {
        return $this->hasMany(PerkembanganBalita::class, 'user_id');
    }

    // ======================================================================
    // HELPER & SCOPE HIERARKI RW & RT WARKAS MACHI
    // ======================================================================

    public function isRw(): bool
    {
        return in_array(strtolower($this->role ?? ''), ['admin', 'rw', 'super_admin']);
    }

    public function isRt(): bool
    {
        return strtolower($this->role ?? '') === 'rt';
    }

    public function isWarga(): bool
    {
        return strtolower($this->role ?? '') === 'warga';
    }

    public function isBendahara(): bool
    {
        return strtolower($this->role ?? '') === 'bendahara';
    }

    public function getCleanRtNumberAttribute(): int
    {
        $rt = intval($this->rt_number);
        if ($rt === 0 && preg_match('/RT\s*(\d+)/i', $this->address ?? '', $m)) {
            $rt = intval($m[1]);
        }
        return $rt > 0 ? $rt : 1;
    }

    public function getCleanRtLabelAttribute(): string
    {
        return 'RT ' . str_pad($this->clean_rt_number, 3, '0', STR_PAD_LEFT);
    }

    public function getRoleBadgeLabelAttribute(): string
    {
        if ($this->isRw()) {
            return 'Ketua RW 013 / Admin';
        }
        if ($this->isRt()) {
            return 'Ketua ' . $this->clean_rt_label . ' (RW 013)';
        }
        if ($this->isBendahara()) {
            return 'Bendahara RW 013';
        }
        return 'Warga ' . $this->clean_rt_label;
    }
}