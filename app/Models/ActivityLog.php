<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'impersonator_id',
        'target_user_id',
        'category',
        'action',
        'activity',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Pelaku aktif aktivitas
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Super Admin yang sedang melakukan impersonasi/simulasi (jika ada)
     */
    public function impersonator()
    {
        return $this->belongsTo(User::class, 'impersonator_id');
    }

    /**
     * Pengguna target (relasi legacy kompatibilitas)
     */
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Helper statis utama untuk merekam aktivitas sistem secara fail-safe.
     * Tidak akan pernah memicu error fatal ke alur transaksi utama bila terjadi kegagalan pencatatan.
     *
     * @param string $category   auth|super_admin|keuangan|keamanan|profil|sistem
     * @param string $action     Kode aksi (misal: 'auth.login', 'super_admin.switch_role', dll.)
     * @param string $description Deskripsi singkat bahasa Indonesia
     * @param array  $metadata    Konteks detail tambahan
     * @param int|null $userId    ID User opsional (default: Auth::id())
     * @return static|null
     */
    public static function record(
        string $category,
        string $action,
        string $description,
        array $metadata = [],
        ?int $userId = null
    ): ?self {
        try {
            $actorId = $userId;
            if (!$actorId && Auth::check()) {
                $actorId = Auth::id();
            }

            $impersonatorId = null;
            try {
                if (app()->bound('session')) {
                    $impersonatorId = session('impersonator_id');
                }
            } catch (\Throwable $e) {
                $impersonatorId = null;
            }

            $ipAddress = null;
            $userAgent = null;
            try {
                if (app()->bound('request')) {
                    $req = request();
                    $ipAddress = $req?->ip();
                    $userAgent = $req?->userAgent();
                }
            } catch (\Throwable $e) {
                $ipAddress = '127.0.0.1';
            }

            return self::create([
                'user_id' => $actorId,
                'impersonator_id' => $impersonatorId,
                'category' => strtolower($category),
                'action' => $action,
                'activity' => $action, // Kompatibilitas dengan kolom lama
                'description' => $description,
                'metadata' => !empty($metadata) ? $metadata : null,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        } catch (\Throwable $e) {
            // Fail-safe: catat ke error log Laravel tanpa memecahkan eksekusi user
            Log::warning('ActivityLog::record failed: ' . $e->getMessage(), [
                'category' => $category,
                'action' => $action,
                'description' => $description,
            ]);
            return null;
        }
    }

    /**
     * Helper legacy log untuk kompatibilitas kode lama
     */
    public static function log($activity, $description, $targetUserId = null, $userId = null)
    {
        return self::record(
            'sistem',
            (string)$activity,
            (string)$description,
            $targetUserId ? ['target_user_id' => $targetUserId] : [],
            $userId
        );
    }

    /**
     * Badge CSS class untuk kategori aktivitas
     */
    public function getCategoryBadgeClassAttribute(): string
    {
        $cat = strtolower(str_replace('-', '_', $this->category ?? ''));
        if (empty($cat) || $cat === 'null') {
            $act = strtolower($this->action ?? $this->activity ?? '');
            if (str_starts_with($act, 'super_admin') || str_starts_with($act, 'super-admin')) $cat = 'super_admin';
            elseif (str_starts_with($act, 'auth')) $cat = 'auth';
            elseif (str_starts_with($act, 'keuangan') || str_starts_with($act, 'kas')) $cat = 'keuangan';
            elseif (str_starts_with($act, 'keamanan') || str_starts_with($act, 'ronda') || str_starts_with($act, 'portal')) $cat = 'keamanan';
            elseif (str_starts_with($act, 'profil') || str_starts_with($act, 'profile')) $cat = 'profil';
        }

        return match ($cat) {
            'auth' => 'badge-cat-auth',
            'super_admin' => 'badge-cat-super-admin',
            'keuangan' => 'badge-cat-keuangan',
            'keamanan' => 'badge-cat-keamanan',
            'profil' => 'badge-cat-profil',
            default => 'badge-cat-sistem',
        };
    }

    /**
     * Label representasi kategori dalam Bahasa Indonesia
     */
    public function getCategoryLabelAttribute(): string
    {
        $cat = strtolower(str_replace('-', '_', $this->category ?? ''));
        if (empty($cat) || $cat === 'null') {
            $act = strtolower($this->action ?? $this->activity ?? '');
            if (str_starts_with($act, 'super_admin') || str_starts_with($act, 'super-admin')) return 'Super Admin';
            if (str_starts_with($act, 'auth')) return 'Otentikasi & Akun';
            if (str_starts_with($act, 'keuangan') || str_starts_with($act, 'kas')) return 'Keuangan & Kas';
            if (str_starts_with($act, 'keamanan') || str_starts_with($act, 'ronda') || str_starts_with($act, 'portal')) return 'Keamanan & CCTV';
            if (str_starts_with($act, 'profil') || str_starts_with($act, 'profile')) return 'Profil & Akun';
            return 'Sistem & Operasional';
        }

        return match ($cat) {
            'auth' => 'Otentikasi & Akun',
            'super_admin' => 'Super Admin',
            'keuangan' => 'Keuangan & Kas',
            'keamanan' => 'Keamanan & CCTV',
            'profil' => 'Profil & Akun',
            default => 'Sistem & Operasional',
        };
    }
}
