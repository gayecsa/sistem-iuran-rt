<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanItem extends Model
{
    use HasFactory;

    protected $table = 'tagihan_items';

    protected $fillable = [
        'tagihan_keluarga_id',
        'iuran_tarif_id',
        'nama_iuran',
        'kode_iuran',
        'metode_perhitungan',
        'tarif_satuan',
        'kuantitas',
        'nominal_subtotal',
        'nominal_diskon',
        'nominal_akhir',
        'nominal_terbayar',
        'sisa_item',
        'pemilik_dana',
        'buku_kas_id',
        'status_pembayaran',
        'keterangan_snapshot',
    ];

    protected $casts = [
        'tarif_satuan' => 'decimal:2',
        'kuantitas' => 'integer',
        'nominal_subtotal' => 'decimal:2',
        'nominal_diskon' => 'decimal:2',
        'nominal_akhir' => 'decimal:2',
        'nominal_terbayar' => 'decimal:2',
        'sisa_item' => 'decimal:2',
    ];

    public function tagihanKeluarga()
    {
        return $this->belongsTo(TagihanKeluarga::class, 'tagihan_keluarga_id');
    }

    public function iuranTarif()
    {
        return $this->belongsTo(IuranTarif::class, 'iuran_tarif_id');
    }

    public function bukuKas()
    {
        return $this->belongsTo(BukuKas::class, 'buku_kas_id');
    }

    public function alokasis()
    {
        return $this->hasMany(PembayaranAlokasi::class, 'tagihan_item_id');
    }

    // Accessors & Mutators for compatibility
    public function getNamaItemAttribute()
    {
        return $this->nama_iuran;
    }

    public function setNamaItemAttribute($value)
    {
        $this->attributes['nama_iuran'] = $value;
    }

    public function getKodeItemAttribute()
    {
        return $this->kode_iuran;
    }

    public function setKodeItemAttribute($value)
    {
        $this->attributes['kode_iuran'] = $value;
    }

    public function getSubtotalAttribute()
    {
        return $this->nominal_subtotal;
    }

    public function setSubtotalAttribute($value)
    {
        $this->attributes['nominal_subtotal'] = $value;
    }

    public function getPotonganKeringananAttribute()
    {
        return $this->nominal_diskon;
    }

    public function setPotonganKeringananAttribute($value)
    {
        $this->attributes['nominal_diskon'] = $value;
    }

    public function getTotalAkhirAttribute()
    {
        return $this->nominal_akhir;
    }

    public function setTotalAkhirAttribute($value)
    {
        $this->attributes['nominal_akhir'] = $value;
    }

    public function getDibayarAttribute()
    {
        return $this->nominal_terbayar;
    }

    public function setDibayarAttribute($value)
    {
        $this->attributes['nominal_terbayar'] = $value;
    }

    public function getStatusItemAttribute()
    {
        return $this->status_pembayaran;
    }

    public function setStatusItemAttribute($value)
    {
        $this->attributes['status_pembayaran'] = $value;
    }

    public function getSatuanKuantitasAttribute()
    {
        switch ($this->metode_perhitungan) {
            case 'per_individu': return 'orang';
            case 'per_rumah': return 'unit';
            case 'nominal_tetap': return 'paket';
            case 'per_kk': default: return 'KK';
        }
    }

    public function getMetodeLabelAttribute()
    {
        switch ($this->metode_perhitungan) {
            case 'per_individu': return 'Per Individu';
            case 'per_rumah': return 'Per Rumah/Unit';
            case 'nominal_tetap': return 'Nominal Tetap';
            case 'per_kk': default: return 'Per KK (Flat)';
        }
    }
}
