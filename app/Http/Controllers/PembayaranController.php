<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Iuran;
use App\Models\User;
use App\Models\KasRt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if($user->role == 'admin' || $user->role == 'bendahara') {
            $pembayaran = Pembayaran::with(['user', 'iuran'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $pembayaran = Pembayaran::with('iuran')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }
        
        return view('pembayaran.index', compact('pembayaran'));
    }
    
    public function create()
    {
        $users = User::where('role', 'warga')->get();
        $iuran = Iuran::where('status', 'aktif')->get();
        return view('pembayaran.create', compact('users', 'iuran'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'iuran_id' => 'required|exists:iuran,id',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string',
        ]);
        
        $iuran = Iuran::find($request->iuran_id);
        
        $pembayaran = Pembayaran::create([
            'user_id' => $request->user_id,
            'iuran_id' => $request->iuran_id,
            'kode_pembayaran' => 'INV/' . date('Ymd') . '/' . Str::random(6),
            'jumlah_bayar' => $request->jumlah_bayar,
            'tanggal_bayar' => Carbon::now(),
            'tanggal_jatuh_tempo' => $iuran->tanggal_mulai->addMonth(),
            'status' => 'lunas',
            'metode_pembayaran' => $request->metode_pembayaran,
            'keterangan' => $request->keterangan,
        ]);
        
        // Tambahkan ke kas RT
        KasRt::create([
            'pembayaran_id' => $pembayaran->id,
            'pemasukan' => $request->jumlah_bayar,
            'pengeluaran' => 0,
            'keterangan' => 'Pembayaran iuran ' . $iuran->nama_iuran . ' oleh ' . User::find($request->user_id)->name,
            'kategori' => 'iuran',
            'tanggal_transaksi' => Carbon::now(),
            'dibuat_oleh' => Auth::user()->name,
        ]);
        
        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil dicatat!');
    }
    
    public function verifikasi($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->update(['status' => 'lunas']);
        
        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi!');
    }
    
    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->delete();
        
        return redirect()->route('pembayaran.index')
            ->with('success', 'Data pembayaran dihapus!');
    }
}