<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasRt extends Model
{
    use HasFactory;
    protected $table = 'kas_rt';
    protected $fillable = [
        'pembayaran_id', 'pemasukan', 'pengeluaran', 
        'keterangan', 'kategori', 'tanggal_transaksi', 'dibuat_oleh'
    ];
}