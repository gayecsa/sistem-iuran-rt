<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iuran extends Model
{
    use HasFactory;
    protected $table = 'iuran';
    protected $fillable = [
        'nama_iuran', 'deskripsi', 'jenis_iuran', 'nominal', 
        'tanggal_mulai', 'tanggal_selesai', 'periode', 'status'
    ];
    
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}