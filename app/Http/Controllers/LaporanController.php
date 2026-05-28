<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\KasRt;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,bendahara']);
    }
    
    public function index()
    {
        return view('laporan.index');
    }
    
    public function laporanKeuangan(Request $request)
    {
        // Menyamakan format input bulan jika dikirim dalam bentuk teks (misal: "May" dari form)
        $bulanInput = $request->bulan;
        if ($bulanInput && !is_numeric($bulanInput)) {
            try {
                $bulan = Carbon::parse($bulanInput)->month;
            } catch (\Exception $e) {
                $bulan = Carbon::now()->month;
            }
        } else {
            $bulan = $bulanInput ?? Carbon::now()->month;
        }
        
        $tahun = $request->tahun ?? Carbon::now()->year;
        
        // 1. Ambil pemasukan dari Iuran Warga Resmi (Tabel Pembayaran)
        $pemasukan_iuran = Pembayaran::where('status', 'lunas')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->sum('jumlah_bayar');

        // 2. Ambil pemasukan dari Kas RT Umum (Tabel KasRt kolom pemasukan)
        $pemasukan_kas_umum = KasRt::whereMonth('tanggal_transaksi', $bulan)
            ->whereYear('tanggal_transaksi', $tahun)
            ->sum('pemasukan');

        // SINKRONISASI: Total Pemasukan adalah gabungan keduanya
        $pemasukan = $pemasukan_iuran + $pemasukan_kas_umum;
            
        // Ambil pengeluaran dari Kas RT
        $pengeluaran = KasRt::whereMonth('tanggal_transaksi', $bulan)
            ->whereYear('tanggal_transaksi', $tahun)
            ->sum('pengeluaran');
            
        // 3. Ambil detail untuk list tabel di bawah
        // Kita ambil data dari Pembayaran Iuran Warga
        $detail_pembayaran = Pembayaran::with(['user', 'iuran'])
            ->where('status', 'lunas')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->get();
            
        // PERBAIKAN: Hapus filter 'pemasukan > 0' agar transaksi Pengeluaran (seperti Rp 0 di pemasukan) tetap ikut terbaca masuk ke tabel laporan keuangan
        $detail_kas_umum = KasRt::whereMonth('tanggal_transaksi', $bulan)
            ->whereYear('tanggal_transaksi', $tahun)
            ->get();
            
        // Mengirimkan variabel ke view
        return view('laporan.keuangan', compact(
            'pemasukan', 
            'pengeluaran', 
            'detail_pembayaran', 
            'detail_kas_umum', 
            'bulan', 
            'tahun'
        ));
    }
    
    public function laporanPerWarga(Request $request)
    {
        $warga = User::where('role', 'warga')->get(); //
        $data_warga = []; //
        
        foreach($warga as $w) { //
            $total_bayar = Pembayaran::where('user_id', $w->id) //
                ->where('status', 'lunas') //
                ->sum('jumlah_bayar'); //
                
            $data_warga[] = [ //
                'nama' => $w->name, //
                'no_rumah' => $w->house_number, //
                'total_bayar' => $total_bayar, //
                'status' => $total_bayar > 0 ? 'Aktif' : 'Belum Bayar' //
            ];
        }
        
        return view('laporan.perwarga', compact('data_warga')); //
    }
}