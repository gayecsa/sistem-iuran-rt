<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LetterRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_registrasi',
        'nomor_surat',
        'user_id',
        'rt_number',
        'rw_number',
        'jenis_surat',
        'letter_type_id',
        'alur_persetujuan',
        'judul_surat',
        'nama_lengkap',
        'nik',
        'no_kk',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'pendidikan_terakhir',
        'phone',
        'email',
        'alamat_jalan',
        'no_rumah',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'keperluan',
        'detail_khusus',
        'keterangan_tambahan',
        'dokumen_lampiran',
        'status',
        'rt_approved_by',
        'rt_approved_name',
        'rt_approved_at',
        'rt_notes',
        'rt_signature_token',
        'rw_approved_by',
        'rw_approved_name',
        'rw_approved_at',
        'rw_notes',
        'rw_signature_token',
        'rejection_reason',
        'revision_notes',
        'qr_verification_code',
        'printed_at',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'detail_khusus' => 'array',
        'dokumen_lampiran' => 'array',
        'rt_approved_at' => 'datetime',
        'rw_approved_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    // Status Constants
    public const STATUS_DRAFT = 'Draft';
    public const STATUS_WAITING_RT = 'Menunggu Verifikasi RT';
    public const STATUS_REVISION_RT = 'Perlu Perbaikan RT';
    public const STATUS_REJECTED_RT = 'Ditolak RT';
    public const STATUS_WAITING_RW = 'Menunggu Persetujuan RW';
    public const STATUS_REVISION_RW = 'Perlu Perbaikan RW';
    public const STATUS_REJECTED_RW = 'Ditolak RW';
    public const STATUS_APPROVED = 'Siap Dicetak';
    public const STATUS_COMPLETED = 'Selesai';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    public function rtApprover()
    {
        return $this->belongsTo(User::class, 'rt_approved_by');
    }

    public function rwApprover()
    {
        return $this->belongsTo(User::class, 'rw_approved_by');
    }

    public function histories()
    {
        return $this->hasMany(LetterStatusHistory::class, 'letter_request_id')->latest();
    }

    /**
     * Syarat cetak: Menyesuaikan konfigurasi alur persetujuan surat
     * - Untuk alur RT saja: cukup persetujuan Ketua RT
     * - Untuk alur RT & RW / Pengantar: wajib persetujuan Ketua RT DAN Ketua RW serta nomor surat resmi
     */
    public function canPrint(): bool
    {
        if (!in_array($this->status, [self::STATUS_APPROVED, self::STATUS_COMPLETED])) {
            return false;
        }

        if ($this->alur_persetujuan === 'rt_only') {
            return !is_null($this->rt_approved_at);
        }

        return !is_null($this->rt_approved_at)
            && !is_null($this->rw_approved_at)
            && !empty($this->nomor_surat);
    }

    /**
     * Helper log transisi status
     */
    public function logStatusTransition(string $newStatus, ?string $catatan = null, ?User $actor = null): void
    {
        $actor = $actor ?: auth()->user();
        $oldStatus = $this->status;

        $this->update(['status' => $newStatus]);

        $this->histories()->create([
            'user_id' => $actor?->id,
            'actor_name' => $actor?->name ?? 'Sistem Warkas Machi',
            'actor_role' => $actor ? $actor->role_badge_label : 'Sistem Otomatis',
            'status_sebelumnya' => $oldStatus,
            'status_baru' => $newStatus,
            'catatan' => $catatan,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Generator Nomor Registrasi Unik
     */
    public static function generateNomorRegistrasi(string $jenis): string
    {
        $prefix = match(strtolower($jenis)) {
            'domisili' => 'SKD',
            'sktm', 'tidak-mampu' => 'SKTM',
            'usaha' => 'SKU',
            default => 'SP',
        };
        $dateCode = Carbon::now()->format('Ym');
        $random = strtoupper(Str::random(4));
        $count = self::whereDate('created_at', Carbon::today())->count() + 1;
        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "REG-{$prefix}-{$dateCode}-{$sequence}-{$random}";
    }

    /**
     * Generator Nomor Surat Resmi Standar Kelurahan Sukamiskin Arcamanik
     */
    public function generateOfficialNumber(): string
    {
        $rtFormatted = 'RT.' . str_pad($this->rt_number, 3, '0', STR_PAD_LEFT);
        
        $klasifikasi = '470';
        $code = 'SP';
        if ($this->letterType) {
            $klasifikasi = $this->letterType->kode_klasifikasi ?: '470';
            $code = $this->letterType->singkatan ?: strtoupper(substr($this->jenis_surat, 0, 4));
        } else {
            $code = match(strtolower($this->jenis_surat)) {
                'domisili' => 'SKD',
                'sktm', 'tidak-mampu' => 'SKTM',
                'usaha' => 'SKU',
                default => 'SP',
            };
        }

        $romanMonth = $this->getRomanMonth(Carbon::now()->month);
        $year = Carbon::now()->year;
        $order = str_pad($this->id, 3, '0', STR_PAD_LEFT);

        return "{$klasifikasi}/{$order}/{$rtFormatted}-RW.013/{$code}/{$romanMonth}/{$year}";
    }

    protected function getRomanMonth(int $month): string
    {
        $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        return $map[$month] ?? 'XI';
    }

    /**
     * UI Status Badge styling
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            self::STATUS_DRAFT => ['bg' => 'bg-secondary', 'text' => 'text-white', 'icon' => 'file-text', 'label' => 'Draft'],
            self::STATUS_WAITING_RT => ['bg' => 'bg-warning', 'text' => 'text-dark', 'icon' => 'clock', 'label' => 'Menunggu Verifikasi RT'],
            self::STATUS_REVISION_RT => ['bg' => 'bg-danger text-white', 'text' => 'text-white', 'icon' => 'alert-circle', 'label' => 'Perlu Perbaikan RT'],
            self::STATUS_REJECTED_RT => ['bg' => 'bg-danger', 'text' => 'text-white', 'icon' => 'x-circle', 'label' => 'Ditolak RT'],
            self::STATUS_WAITING_RW => ['bg' => 'bg-info', 'text' => 'text-white', 'icon' => 'clock', 'label' => 'Menunggu Persetujuan RW'],
            self::STATUS_REVISION_RW => ['bg' => 'bg-warning text-dark', 'text' => 'text-dark', 'icon' => 'alert-triangle', 'label' => 'Perlu Perbaikan RW'],
            self::STATUS_REJECTED_RW => ['bg' => 'bg-danger', 'text' => 'text-white', 'icon' => 'x-circle', 'label' => 'Ditolak RW'],
            self::STATUS_APPROVED => ['bg' => 'bg-success', 'text' => 'text-white', 'icon' => 'check-circle-2', 'label' => 'Siap Dicetak'],
            self::STATUS_COMPLETED => ['bg' => 'bg-primary', 'text' => 'text-white', 'icon' => 'printer', 'label' => 'Selesai Dicetak'],
            default => ['bg' => 'bg-secondary', 'text' => 'text-white', 'icon' => 'help-circle', 'label' => $this->status],
        };
    }
}
