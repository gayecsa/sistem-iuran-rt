<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmAuditLog extends Model
{
    use HasFactory;

    protected $table = 'umkm_audit_logs';

    protected $fillable = [
        'umkm_id',
        'user_id',
        'user_name',
        'role_saat_itu',
        'action',
        'keterangan',
        'data_lama',
        'data_baru',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat aktivitas audit secara aman dan immutable
     */
    public static function record(int $umkmId, string $action, ?string $keterangan = null, ?array $dataLama = null, ?array $dataBaru = null): self
    {
        $user = auth()->user();
        return self::create([
            'umkm_id' => $umkmId,
            'user_id' => $user?->id,
            'user_name' => $user?->name ?: 'Sistem Warkas Machi',
            'role_saat_itu' => $user?->role ?: 'system',
            'action' => $action,
            'keterangan' => $keterangan,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
        ]);
    }
}
