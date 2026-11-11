<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\KartuKeluarga;
use App\Models\WargaStatusHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DataKependudukanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Menu: Data Keluarga (Seluruh KK di RW 013 / RT Masing-masing)
     */
    public function dataKeluarga(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isRw() || $user->isRt() || $user->isBendahara(), 403, 'Akses khusus pengurus.');

        $query = KartuKeluarga::with(['kepalaKeluarga', 'anggota'])
            ->withCount('anggota');

        if ($user->isRt()) {
            $rtNum = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);
            $query->where(function ($q) use ($rtNum) {
                $q->where('rt', $rtNum)->orWhere('rt', intval($rtNum));
            });
        } elseif ($request->filled('rt') && $request->rt !== 'all') {
            $filterRt = str_pad(intval($request->rt), 3, '0', STR_PAD_LEFT);
            $query->where(function ($q) use ($filterRt) {
                $q->where('rt', $filterRt)->orWhere('rt', intval($filterRt));
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                  ->orWhere('nama_kepala_keluarga', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $keluargas = $query->orderBy('rt')->orderBy('nomor_rumah')->paginate(30);
        $keluargas->appends($request->all());

        return view('kependudukan.keluarga', compact('keluargas', 'user'));
    }

    /**
     * Menu: Verifikasi Warga (Pusat Pemeriksaan & Validasi Status Kependudukan Berjenjang)
     */
    public function verifikasiWarga(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isRw() || $user->isRt() || $user->isBendahara(), 403, 'Akses khusus pengurus.');

        $query = User::whereIn('role', ['warga', 'rt'])->with('kartuKeluarga');

        if ($user->isRt()) {
            $rtNum = $user->clean_rt_number;
            $query->where(function ($q) use ($rtNum) {
                $q->where('rt_number', $rtNum)
                  ->orWhere('rt_number', str_pad($rtNum, 3, '0', STR_PAD_LEFT))
                  ->orWhere('address', 'like', "%RT 00{$rtNum}%");
            });
        } elseif ($request->filled('rt') && $request->rt !== 'all') {
            $filterRt = intval($request->rt);
            $query->where(function ($q) use ($filterRt) {
                $q->where('rt_number', $filterRt)
                  ->orWhere('rt_number', str_pad($filterRt, 3, '0', STR_PAD_LEFT))
                  ->orWhere('address', 'like', "%RT 00{$filterRt}%");
            });
        }

        // Filter status verifikasi jika dipilih
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status_verifikasi', $request->status);
        } else {
            // Default: Tampilkan yang perlu ditindaklanjuti lebih dahulu
            $query->orderByRaw("
                CASE 
                    WHEN status_verifikasi = 'Menunggu Verifikasi RT' THEN 1
                    WHEN status_verifikasi = 'Menunggu Verifikasi RW' THEN 2
                    WHEN status_verifikasi = 'Belum Diverifikasi' THEN 3
                    WHEN status_verifikasi = 'Perlu Perbaikan' THEN 4
                    WHEN status_verifikasi = 'Terverifikasi RT' THEN 5
                    ELSE 6
                END
            ");
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%");
            });
        }

        $wargaList = $query->paginate(25);
        $wargaList->appends($request->all());

        return view('kependudukan.verifikasi', compact('wargaList', 'user'));
    }

    /**
     * Menu: Riwayat Perubahan Data (Audit Log Terpadu Kependudukan)
     */
    public function riwayatPerubahan(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isRw() || $user->isRt() || $user->isBendahara(), 403, 'Akses khusus pengurus.');

        $query = WargaStatusHistory::with(['user', 'kartuKeluarga', 'petugas']);

        if ($user->isRt()) {
            $rtNum = $user->clean_rt_number;
            $query->whereHas('user', function ($q) use ($rtNum) {
                $q->where('rt_number', $rtNum)
                  ->orWhere('rt_number', str_pad($rtNum, 3, '0', STR_PAD_LEFT));
            });
        }

        if ($request->filled('tipe') && $request->tipe !== 'all') {
            $query->where('tipe_perubahan', $request->tipe);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('petugas_nama', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        $histories = $query->orderBy('id', 'desc')->paginate(30);
        $histories->appends($request->all());

        return view('kependudukan.riwayat', compact('histories', 'user'));
    }

    /**
     * Menu: Laporan Kependudukan & Demografi Realistis RW 013
     */
    public function laporanKependudukan(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isRw() || $user->isRt() || $user->isBendahara(), 403, 'Akses khusus pengurus.');

        $wargaQuery = User::whereIn('role', ['warga', 'rt']);
        if ($user->isRt()) {
            $rtNum = $user->clean_rt_number;
            $wargaQuery->where(function ($q) use ($rtNum) {
                $q->where('rt_number', $rtNum)->orWhere('rt_number', str_pad($rtNum, 3, '0', STR_PAD_LEFT));
            });
        }

        $totalWarga = (clone $wargaQuery)->count();
        $totalLaki = (clone $wargaQuery)->where('gender', 'Laki-laki')->count();
        $totalPerempuan = (clone $wargaQuery)->where('gender', 'Perempuan')->count();

        // Kelompok Usia
        $now = Carbon::now();
        $usiaBalita = (clone $wargaQuery)->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(5))->count();
        $usiaAnak = (clone $wargaQuery)->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(12))->whereDate('tanggal_lahir', '<', $now->copy()->subYears(5))->count();
        $usiaRemaja = (clone $wargaQuery)->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(19))->whereDate('tanggal_lahir', '<', $now->copy()->subYears(12))->count();
        $usiaDewasa = (clone $wargaQuery)->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(55))->whereDate('tanggal_lahir', '<', $now->copy()->subYears(19))->count();
        $usiaLansia = (clone $wargaQuery)->whereDate('tanggal_lahir', '<', $now->copy()->subYears(55))->count();

        // Status Domisili
        $domisiliStats = (clone $wargaQuery)->select('status_domisili', DB::raw('count(*) as total'))
            ->groupBy('status_domisili')
            ->pluck('total', 'status_domisili');

        // Status Rumah
        $rumahStats = (clone $wargaQuery)->select('status_rumah', DB::raw('count(*) as total'))
            ->groupBy('status_rumah')
            ->pluck('total', 'status_rumah');

        // Rekap per RT (RT 001 - RT 008)
        $rekapPerRt = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtPad = str_pad($i, 3, '0', STR_PAD_LEFT);
            $qRt = User::whereIn('role', ['warga', 'rt'])->where(function ($q) use ($i, $rtPad) {
                $q->where('rt_number', $i)
                  ->orWhere('rt_number', $rtPad)
                  ->orWhere('rt_number', 'RT ' . $rtPad)
                  ->orWhere('address', 'like', "%RT 00{$i}%")
                  ->orWhere('address', 'like', "%RT {$i}%");
            });

            $rekapPerRt[] = [
                'rt' => 'RT ' . $rtPad,
                'total_warga' => (clone $qRt)->count(),
                'total_kk' => (clone $qRt)->whereNotNull('no_kk')->distinct('no_kk')->count('no_kk'),
                'laki' => (clone $qRt)->where('gender', 'Laki-laki')->count(),
                'perempuan' => (clone $qRt)->where('gender', 'Perempuan')->count(),
                'terverifikasi' => (clone $qRt)->whereIn('status_verifikasi', ['Terverifikasi RT', 'Terverifikasi RW'])->count(),
            ];
        }

        return view('kependudukan.laporan', compact(
            'totalWarga', 'totalLaki', 'totalPerempuan',
            'usiaBalita', 'usiaAnak', 'usiaRemaja', 'usiaDewasa', 'usiaLansia',
            'domisiliStats', 'rumahStats', 'rekapPerRt', 'user'
        ));
    }
}
