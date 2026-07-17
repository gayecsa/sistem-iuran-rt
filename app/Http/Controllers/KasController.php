<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KasRt;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class KasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,bendahara']);
    }
    
    public function index()
    {
        $kas = KasRt::orderBy('tanggal_transaksi', 'desc')->paginate(15);
        
        // Gunakan selectRaw untuk perhitungan yang lebih akurat
        $kasData = KasRt::selectRaw('COALESCE(SUM(pemasukan), 0) as total_pemasukan, COALESCE(SUM(pengeluaran), 0) as total_pengeluaran')
            ->first();
        
        $saldo = ($kasData->total_pemasukan ?? 0) - ($kasData->total_pengeluaran ?? 0);
        $total_pemasukan = $kasData->total_pemasukan ?? 0;
        $total_pengeluaran = $kasData->total_pengeluaran ?? 0;
        
        return view('kas.index', compact('kas', 'saldo', 'total_pemasukan', 'total_pengeluaran'));
    }
    
    public function create()
    {
        // Mengambil data warga untuk pilihan dropdown (opsional)
        $pembayaran = Pembayaran::all(); 
        return view('kas.create', compact('pembayaran'));
    }
    
    public function store(Request $request)
    {
        // Validasi input disesuaikan dengan No HP (Teks) dan Bukti Pembayaran (File Gambar)
        $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'tanggal_transaksi' => 'required|date',
            'pembayaran_id' => 'nullable|integer',
            'nama_warga' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:15',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // File gambar maks 2MB
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);
        
        // Memisahkan nominal input ke kolom database yang tepat
        $pemasukan = $request->tipe === 'pemasukan' ? $request->jumlah : 0;
        $pengeluaran = $request->tipe === 'pengeluaran' ? $request->jumlah : 0;
        
        // Logika upload file gambar bukti transfer
        $nama_file_bukti = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $nama_file_bukti = 'bukti_' . time() . '.' . $file->getClientOriginalExtension();
            // Menyimpan file fisik ke folder storage/app/public/bukti_transfer
            $file->storeAs('public/bukti_transfer', $nama_file_bukti);
        }
        
        // Simpan data ke database kas menggunakan nama kolom baru hasil migrasi
        KasRt::create([
            'pembayaran_id' => $request->pembayaran_id,
            'nama_warga' => $request->nama_warga,
            'no_hp' => $request->no_hp,
            'bukti_pembayaran' => $nama_file_bukti, // Menyimpan nama filenya saja ke database
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'keterangan' => $request->keterangan,
            'kategori' => $request->kategori,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'dibuat_oleh' => Auth::user()->name,
        ]);
        
        // Tetap menggunakan route redirect kas-rt.index agar kembali ke halaman utama kas
        return redirect()->route('kas-rt.index')->with('success', 'Transaksi kas berhasil disimpan!');
    }
}