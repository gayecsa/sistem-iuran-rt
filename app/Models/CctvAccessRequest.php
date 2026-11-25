<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CctvAccessRequest extends Model
{
    protected $fillable = [
        'user_id',
        'cctv_id',
        'alasan',
        'status_rt',
        'approved_rt_by',
        'approved_rt_at',
        'status_rw',
        'approved_rw_by',
        'approved_rw_at',
        'catatan_pengurus',
        'expires_at',
    ];

    protected $casts = [
        'approved_rt_at' => 'datetime',
        'approved_rw_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cctv()
    {
        return $this->belongsTo(Cctv::class, 'cctv_id');
    }

    public function approvedRtBy()
    {
        return $this->belongsTo(User::class, 'approved_rt_by');
    }

    public function approvedRwBy()
    {
        return $this->belongsTo(User::class, 'approved_rw_by');
    }

    public function isFullyApproved(): bool
    {
        return $this->status_rt === 'approved' &&
               $this->status_rw === 'approved' &&
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->status_rt === 'rejected' || $this->status_rw === 'rejected') {
            return 'Ditolak';
        }

        if ($this->status_rt === 'approved' && $this->status_rw === 'approved') {
            if ($this->expires_at && $this->expires_at->isPast()) {
                return 'Akses Kedaluwarsa';
            }
            return 'Akses Disetujui (Aktif)';
        }

        if ($this->status_rt === 'approved' && $this->status_rw === 'pending') {
            return 'Disetujui RT — Menunggu Izin RW';
        }

        return 'Menunggu Izin RT';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->status_rt === 'rejected' || $this->status_rw === 'rejected') {
            return 'bg-danger text-white';
        }

        if ($this->status_rt === 'approved' && $this->status_rw === 'approved') {
            if ($this->expires_at && $this->expires_at->isPast()) {
                return 'bg-secondary text-white';
            }
            return 'bg-success text-white';
        }

        if ($this->status_rt === 'approved' && $this->status_rw === 'pending') {
            return 'bg-warning text-dark';
        }

        return 'bg-info text-dark';
    }
}
