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
        return match ($this->category) {
            'auth' => 'bg-info-subtle text-info border border-info border-opacity-25',
            'super_admin' => 'bg-purple-subtle text-purple border border-primary border-opacity-25',
            'keuangan' => 'bg-success-subtle text-success border border-success border-opacity-25',
            'keamanan' => 'bg-danger-subtle text-danger border border-danger border-opacity-25',
            'profil' => 'bg-primary-subtle text-primary border border-primary border-opacity-25',
            default => 'bg-secondary-subtle text-secondary border border-secondary border-opacity-25',
        };
    }

    /**
     * Label representasi kategori dalam Bahasa Indonesia
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'auth' => 'Otentikasi & Akun',
            'super_admin' => 'Super Admin',
            'keuangan' => 'Keuangan & Kas',
            'keamanan' => 'Keamanan & CCTV',
            'profil' => 'Profil & Akun',
            default => 'Sistem & Operasional',
        };
    }
}
