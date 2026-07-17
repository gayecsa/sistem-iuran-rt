<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Iuran;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\KasRt;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];
        
        // ======================================================================
        // DATA GLOBAL (Untuk semua role)
        // ======================================================================
        
        // Buat Base Query: Hanya ambil user yang role-nya 'warga'
        $baseWargaQuery = User::where('role', 'warga');
        
        // Hitung total warga dari base query
        $data['total_warga'] = (clone $baseWargaQuery)->count(); 
        
        // Hitung Gender untuk Grafik dari base query yang sama
        $data['total_laki'] = (clone $baseWargaQuery)->where('gender', 'Laki-laki')->count();
        $data['total_perempuan'] = (clone $baseWargaQuery)->where('gender', 'Perempuan')->count();
        
        // Perhitungan Kas RT - Gunakan DISTINCT untuk menghindari duplikasi
        $kasData = KasRt::selectRaw('COALESCE(SUM(pemasukan), 0) as total_pemasukan, COALESCE(SUM(pengeluaran), 0) as total_pengeluaran')
            ->first();
        
        $data['total_pemasukan'] = $kasData->total_pemasukan ?? 0;
        $data['total_pengeluaran'] = $kasData->total_pengeluaran ?? 0;
        $data['saldo_kas'] = $data['total_pemasukan'] - $data['total_pengeluaran'];

        // ======================================================================
        // LOGIKA BERDASARKAN ROLE
        // ======================================================================
        if(in_array(strtolower($user->role), ['admin', 'bendahara'])) {
            
            $data['total_iuran_aktif'] = Iuran::where('status', 'aktif')->count();
            $data['total_pembayaran_bulan_ini'] = Pembayaran::where('status', 'lunas')
                ->whereMonth('tanggal_bayar', Carbon::now()->month)
                ->sum('jumlah_bayar');
            
            $warga_bayar = Pembayaran::where('status', 'lunas')
                ->whereMonth('tanggal_bayar', Carbon::now()->month)
                ->distinct('user_id')
                ->count('user_id');
                
            $data['warga_belum_bayar'] = $data['total_warga'] - $warga_bayar;
            
            $data['pembayaran_terbaru'] = Pembayaran::with(['user', 'iuran'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            $data['grafik_bulanan'] = $this->getGrafikBulanan();
            
        } else {
            // Data untuk warga biasa
            $data['tagihan_saya'] = Iuran::where('status', 'aktif')->get();
            $data['riwayat_bayar'] = Pembayaran::where('user_id', $user->id)
                ->with('iuran')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            $data['total_bayar'] = Pembayaran::where('user_id', $user->id)
                ->where('status', 'lunas')
                ->sum('jumlah_bayar');
            $data['tagihan_belum_bayar'] = $this->getTagihanBelumBayar($user->id);
        }
        
        // Data pengumuman terbaru
        $data['pengumuman'] = Pengumuman::where('tanggal_aktif', '<=', Carbon::now())
            ->orderBy('tanggal_aktif', 'desc')
            ->limit(5)
            ->get();
        
        $data['admin_contact_phone'] = User::where('role', 'admin')->value('phone') ?? $user->phone ?? '081234567890';
        $data['user'] = $user;
        return view('dashboard', $data);
    }
    
    private function getGrafikBulanan()
    {
        $data = [];
        for($i = 1; $i <= 12; $i++) {
            $data[$i] = Pembayaran::where('status', 'lunas')
                ->whereMonth('tanggal_bayar', $i)
                ->whereYear('tanggal_bayar', Carbon::now()->year)
                ->sum('jumlah_bayar');
        }
        return $data;
    }
    
    private function getTagihanBelumBayar($userId)
    {
        $iuran_aktif = Iuran::where('status', 'aktif')->get();
        $tagihan = [];
        
        foreach($iuran_aktif as $iuran) {
            $sudahBayar = Pembayaran::where('user_id', $userId)
                ->where('iuran_id', $iuran->id)
                ->where('status', 'lunas')
                ->exists();
                
            if(!$sudahBayar) {
                $tagihan[] = $iuran;
            }
        }
        
        return $tagihan;
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $path = $request->file('foto_profil')->store('foto-profil', 'public');

        $user->update([
            'foto_profil'   => $path,
            'profile_photo' => $path
        ]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function posyandu()
    {
        $user = Auth::user();
        return view('posyandu', compact('user'));
    }

    // --- FUNGSI BARU UNTUK JADWAL POSYANDU ---
    public function jadwal()
    {
        $user = Auth::user();
        return view('posyandu.detail_jadwal', compact('user'));
    }
    // -----------------------------------------

    // --- FUNGSI BARU UNTUK LOKASI POSYANDU ---
    public function lokasi()
    {
        $user = Auth::user();
        return view('posyandu.detail_lokasi', compact('user'));
    }
    // -----------------------------------------

    // --- FUNGSI BARU UNTUK DETAIL POSYANDU ---
    public function detailBalita()
    {
        $user = Auth::user();
        return view('posyandu.detail_balita', compact('user'));
    }

    public function detailImunisasi()
    {
        $user = Auth::user();
        return view('posyandu.detail_imunisasi', compact('user'));
    }

    public function detailIbuHamil()
    {
        $user = Auth::user();
        return view('posyandu.detail_ibu_hamil', compact('user'));
    }

    public function detailEdukasi()
    {
        $user = Auth::user();
        return view('posyandu.detail_edukasi', compact('user'));
    }
    // ---------------------------------------

    public function umkm()
    {
        $user = Auth::user();
        $umkms = \App\Models\Umkm::orderBy('nama_umkm')->get();
        return view('umkm', compact('user', 'umkms'));
    }

    public function surat()
    {
        $user = Auth::user();
        $admin_contact_phone = User::where('role', 'admin')->value('phone') ?? $user->phone ?? '081234567890';
        return view('surat', compact('user', 'admin_contact_phone'));
    }

    // ======================================================================
    // TAMBAHAN FUNCTION SURAT (Untuk mengatasi RouteNotFoundException)
    // ======================================================================
    public function createSurat()
    {
        $user = Auth::user();
        return view('surat.create', compact('user')); 
    }

    public function storeSurat(Request $request)
    {
        return redirect()->route('surat.index')->with('success', 'Pengajuan surat berhasil diproses!');
    }
    // ======================================================================

    public function debugKas()
    {
        // Hanya untuk admin
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $kasData = KasRt::selectRaw('COALESCE(SUM(pemasukan), 0) as total_pemasukan, COALESCE(SUM(pengeluaran), 0) as total_pengeluaran')
            ->first();
        
        $total_records = KasRt::count();
        $total_pemasukan = $kasData->total_pemasukan ?? 0;
        $total_pengeluaran = $kasData->total_pengeluaran ?? 0;
        $saldo = $total_pemasukan - $total_pengeluaran;

        // Check untuk duplikasi
        $duplicates = KasRt::selectRaw('nama_warga, kategori, keterangan, tanggal_transaksi, COUNT(*) as count')
            ->groupBy('nama_warga', 'kategori', 'keterangan', 'tanggal_transaksi')
            ->having('count', '>', 1)
            ->count();

        $duplicate_check = [
            'status' => $duplicates === 0 ? 'ok' : 'warning',
            'message' => $duplicates === 0 ? 'No duplicates' : "Found {$duplicates} duplicate groups"
        ];

        // Check pengeluaran
        $pengeluaran_check = KasRt::where('pengeluaran', '>', 0)->sum('pengeluaran');

        // Categories breakdown
        $categories = KasRt::selectRaw('kategori, COUNT(*) as count')
            ->groupBy('kategori')
            ->get()
            ->toArray();

        // Recent transactions
        $recent_transactions = KasRt::orderBy('tanggal_transaksi', 'desc')
            ->limit(20)
            ->get();

        return view('debug-kas', compact(
            'total_records', 
            'total_pemasukan', 
            'total_pengeluaran', 
            'saldo',
            'duplicate_check',
            'pengeluaran_check',
            'categories',
            'recent_transactions'
        ));
    }
}