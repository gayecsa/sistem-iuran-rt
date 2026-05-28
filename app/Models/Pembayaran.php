<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    protected $table = 'pembayaran';
    protected $fillable = [
        'user_id', 'iuran_id', 'kode_pembayaran', 'jumlah_bayar',
        'tanggal_bayar', 'tanggal_jatuh_tempo', 'status', 
        'bukti_pembayaran', 'keterangan', 'metode_pembayaran'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function iuran()
    {
        return $this->belongsTo(Iuran::class);
    }
}