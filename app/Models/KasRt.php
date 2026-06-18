<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasRt extends Model
{
    use HasFactory;

    protected $table = 'kas_rt';

    // Perbaikan: Menambahkan nama_warga, no_hp, dan bukti_pembayaran agar diizinkan masuk ke database
    protected $fillable = [
        'pembayaran_id', 
        'nama_warga', 
        'no_hp', 
        'bukti_pembayaran',
        'pemasukan', 
        'pengeluaran', 
        'keterangan', 
        'kategori', 
        'tanggal_transaksi', 
        'dibuat_oleh'
    ];
}