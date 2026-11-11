<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KartuKeluarga extends Model
{
    use HasFactory;

    protected $table = 'kartu_keluargas';

    protected $fillable = [
        'no_kk',
        'kepala_keluarga_id',
        'nama_kepala_keluarga',
        'nik_kepala_keluarga',
        'alamat',
        'rt',
        'rw',
        'blok',
        'gang_jalan',
        'nomor_rumah',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'status_rumah',
        'dokumen_kk_path',
        'original_filename',
        'file_size',
        'mime_type',
        'tgl_diterbitkan_dukcapil',
        'sumber_dokumen',
        'versi_dokumen',
        'status_verifikasi_dukcapil',
        'status_dokumen',
        'catatan_verifikasi_dukcapil',
        'metode_verifikasi',
        'verified_by',
        'verified_at',
        'created_by',
    ];

    protected $casts = [
        'tgl_diterbitkan_dukcapil' => 'date',
        'verified_at' => 'datetime',
        'file_size' => 'integer',
        'versi_dokumen' => 'integer',
    ];

    /**
     * Anggota keluarga yang terdaftar dalam KK ini
     */
    public function anggota()
    {
        return $this->hasMany(User::class, 'kartu_keluarga_id')->orderByRaw("
            CASE 
                WHEN hubungan_keluarga = 'Kepala Keluarga' THEN 1
                WHEN hubungan_keluarga = 'Istri' THEN 2
                WHEN hubungan_keluarga = 'Suami' THEN 3
                WHEN hubungan_keluarga = 'Anak' THEN 4
                WHEN hubungan_keluarga = 'Orang Tua' THEN 5
                ELSE 6
            END, tanggal_lahir ASC
        ");
    }

    /**
     * Kepala Keluarga (User)
     */
    public function kepalaKeluarga()
    {
        return $this->belongsTo(User::class, 'kepala_keluarga_id');
    }

    /**
     * Petugas yang memverifikasi dokumen Dukcapil
     */
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Riwayat verifikasi KK
     */
    public function verifikasiHistories()
    {
        return $this->hasMany(KkVerifikasiHistory::class, 'kartu_keluarga_id')->latest();
    }

    /**
     * Masked No. KK untuk tampilan publik/tabel (327318******0001)
     */
    public function getMaskedNoKkAttribute(): string
    {
        $kk = trim($this->no_kk ?? '');
        if (strlen($kk) < 8) {
            return $kk;
        }
        return substr($kk, 0, 6) . '******' . substr($kk, -4);
    }

    /**
     * Format RT dengan padding 3 digit (RT 001)
     */
    public function getCleanRtLabelAttribute(): string
    {
        $rt = intval($this->rt);
        return 'RT ' . str_pad($rt > 0 ? $rt : 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Periksa apakah dokumen KK sudah Terverifikasi dan sah dicetak
     */
    public function isVerifiedDukcapil(): bool
    {
        return $this->status_verifikasi_dukcapil === 'Terverifikasi';
    }

    /**
     * Apakah file PDF asli tersedia di storage
     */
    public function hasDokumenPdf(): bool
    {
        return !empty($this->dokumen_kk_path) && file_exists(storage_path('app/' . $this->dokumen_kk_path));
    }

    /**
     * Jumlah anggota keluarga
     */
    public function getTotalAnggotaAttribute(): int
    {
        return $this->anggota()->count();
    }

    /**
     * Tagihan iuran keluarga
     */
    public function tagihan()
    {
        return $this->hasMany(TagihanKeluarga::class, 'kartu_keluarga_id')->orderBy('periode', 'desc');
    }
}
