<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IotDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_code',
        'name',
        'mode',
        'target_user_id',
        'last_seen_at',
        'last_uid',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->greaterThan(now()->subMinutes(2));
    }

    public static function getDefault(): self
    {
        return self::firstOrCreate(
            ['device_code' => 'GATE-01'],
            [
                'name' => 'Portal & Reader Utama RW 013',
                'mode' => 'portal',
            ]
        );
    }
}

