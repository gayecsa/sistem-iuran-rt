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
        'blok_rumah',
        'gang_jalan',
        'phone',
        'address',
        'status_rumah',
        'is_active',
        'profile_photo',
        'foto_profil',
        'nik',
        'no_kk',
        'kartu_keluarga_id',
        'hubungan_keluarga',
        'status_domisili',
        'status_verifikasi',
        'gender',
        'tempat_lahir',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'pendidikan_terakhir',
        'kewarganegaraan',
        'tanggal_lahir',
        'dokumen_ktp_path',
        'dokumen_pendukung_path',
        'last_login_at',
        'rfid_uid',
        'notification_preferences',
        'privacy_settings',
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
        'tanggal_lahir' => 'date',
        'notification_preferences' => 'array',
        'privacy_settings' => 'array',
    ];

    public function getNotificationPreference(string $key, bool $default = true): bool
    {
        $prefs = $this->notification_preferences ?? [];
        return isset($prefs[$key]) ? (bool)$prefs[$key] : $default;
    }

    public function getPrivacySetting(string $key, $default = null)
    {
        $settings = $this->privacy_settings ?? [];
        return $settings[$key] ?? $default;
    }

    public function isMaskNikKk(): bool
    {
        return (bool)$this->getPrivacySetting('mask_nik_kk', true);
    }

    public function getPhoneVisibility(): string
    {
        return (string)$this->getPrivacySetting('phone_visibility', 'internal');
    }

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(WargaStatusHistory::class, 'user_id')->latest();
    }

    public function familyMembers()
    {
        if (!$this->kartu_keluarga_id && !$this->no_kk) {
            return collect([$this]);
        }
        if ($this->kartu_keluarga_id) {
            return User::where('kartu_keluarga_id', $this->kartu_keluarga_id)->get();
        }
        return User::where('no_kk', $this->no_kk)->get();
    }

    public function getMaskedNikAttribute(): string
    {
        $nik = trim($this->nik ?? '');
        if (strlen($nik) < 8) {
            return $nik ?: '-';
        }
        return substr($nik, 0, 6) . '******' . substr($nik, -4);
    }

    public function getMaskedNoKkAttribute(): string
    {
        $kk = trim($this->no_kk ?? '');
        if (strlen($kk) < 8) {
            return $kk ?: '-';
        }
        return substr($kk, 0, 6) . '******' . substr($kk, -4);
    }

    public function isVerified(): bool
    {
        return in_array($this->status_verifikasi, ['Terverifikasi RT', 'Terverifikasi RW']);
    }

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

    public function portalLogs()
    {
        return $this->hasMany(PortalGateLog::class, 'user_id');
    }

    public function getMaskedRfidUidAttribute(): string
    {
        $uid = trim($this->rfid_uid ?? '');
        if (!$uid) return '-';
        $len = strlen($uid);
        if ($len <= 4) return '***' . $uid;
        if ($len >= 12) {
            // e-KTP 7-byte (14 hex chars): 04A1******D4E5
            return substr($uid, 0, 4) . '******' . substr($uid, -4);
        }
        // RFID 4-byte (8 hex chars): A1****D4
        return substr($uid, 0, 2) . '****' . substr($uid, -2);
    }

    // ======================================================================
    // HELPER & SCOPE HIERARKI RW & RT HAVEN
    // ======================================================================

    public function isSuperAdmin(): bool
    {
        return strtolower($this->role ?? '') === 'super_admin';
    }

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
        if ($this->isSuperAdmin()) {
            return 'Super Admin Haven';
        }
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

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->name ?? ''));
        $initials = '';
        foreach ($words as $w) {
            if (!empty($w)) {
                $initials .= mb_strtoupper(mb_substr($w, 0, 1));
            }
            if (mb_strlen($initials) >= 2) {
                break;
            }
        }
        return $initials ?: 'U';
    }

    public function getInitialsSvgAttribute(): string
    {
        $in = htmlspecialchars($this->initials, ENT_QUOTES, 'UTF-8');
        return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'><rect width='100%25' height='100%25' rx='50' fill='%231456A0'/><text x='50%25' y='55%25' dominant-baseline='middle' text-anchor='middle' fill='%23FFFFFF' font-family='system-ui, -apple-system, sans-serif' font-weight='800' font-size='38'>{$in}</text></svg>";
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->foto_profil && file_exists(public_path('storage/' . $this->foto_profil))) {
            return asset('storage/' . $this->foto_profil);
        }
        if ($this->profile_photo && file_exists(public_path('storage/' . $this->profile_photo))) {
            return asset('storage/' . $this->profile_photo);
        }
        return $this->initials_svg;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->tanggal_lahir ? \Carbon\Carbon::parse($this->tanggal_lahir)->age : null;
    }

    public function umkms()
    {
        return $this->hasMany(Umkm::class, 'user_id');
    }

    public function pengaduans()
    {
        return $this->hasMany(Pengaduan::class, 'user_id');
    }

    public function jadwalRondas()
    {
        return $this->hasMany(JadwalRonda::class, 'coordinator_id_1');
    }

    public function letterRequests()
    {
        return $this->hasMany(LetterRequest::class, 'user_id');
    }

    public function rtApprovedLetters()
    {
        return $this->hasMany(LetterRequest::class, 'rt_approved_by');
    }

    public function rwApprovedLetters()
    {
        return $this->hasMany(LetterRequest::class, 'rw_approved_by');
    }

    // ======================================================================
    // BANK SAMPAH TERPADU
    // ======================================================================

    public function bankSampahSaldo()
    {
        return $this->hasOne(BankSampahSaldo::class, 'user_id');
    }

    public function bankSampahTransaksis()
    {
        return $this->hasMany(BankSampahTransaksi::class, 'user_id');
    }

    public function bankSampahLedgers()
    {
        return $this->hasMany(BankSampahLedger::class, 'user_id');
    }

    public function isPetugasBankSampah($rtNumber = null): bool
    {
        // Admin RW memiliki hak pengawasan menyeluruh
        if ($this->isRw()) {
            return true;
        }

        // Cek jika warga terdaftar sebagai petugas aktif pada unit Bank Sampah
        $query = BankSampah::where('petugas_id', $this->id);
        if ($rtNumber) {
            $cleanRt = str_pad(preg_replace('/[^0-9]/', '', (string)$rtNumber), 3, '0', STR_PAD_LEFT);
            $query->where('rt_number', $cleanRt);
        }
        if ($query->exists()) {
            return true;
        }

        // Ketua RT adalah pembina / petugas default di wilayah RT-nya
        if ($this->isRt()) {
            if ($rtNumber) {
                $cleanRt = str_pad(preg_replace('/[^0-9]/', '', (string)$rtNumber), 3, '0', STR_PAD_LEFT);
                $userRt = str_pad(preg_replace('/[^0-9]/', '', (string)$this->rt_number), 3, '0', STR_PAD_LEFT);
                return $userRt === $cleanRt;
            }
            return true;
        }

        return false;
    }

    /**
     * Kirim notifikasi pengaturan ulang kata sandi dalam Bahasa Indonesia.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}