<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BukuKas;
use App\Models\IuranTarif;
use App\Models\IuranTarifHistory;
use App\Models\KategoriTransaksi;
use App\Models\TagihanKeluarga;
use App\Models\TransaksiKeuangan;
use App\Models\KeuanganAuditLog;
use App\Models\KartuKeluarga;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KeuanganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dispatcher utama Dashboard Keuangan berdasarkan Role
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if (in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            return $this->dashboardRw($request);
        } elseif ($user->role === 'rt') {
            return $this->dashboardRt($request);
        } else {
            return $this->dashboardWarga($request);
        }
    }

    /**
     * Dashboard Keuangan RW 013 (Sentral, Komprehensif & Transparan)
     */
    public function dashboardRw(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas hanya untuk Pengurus RW 013.');
        }

        // 1. Data Kas Sentral RW & Sampah
        $kasRw = BukuKas::where('jenis_kas', 'kas_rw')->first();
        $kasSampah = BukuKas::where('jenis_kas', 'kas_sampah')->first();

        // 2. Data Seluruh Kas RT 001 - RT 008
        $kasRtList = BukuKas::where('jenis_kas', 'kas_rt')->orderBy('rt_number', 'asc')->get();
        $totalSaldoRt = $kasRtList->sum('saldo_sekarang');

        // 3. Data Dana Kegiatan RW 013
        $danaKegiatanList = BukuKas::where('jenis_kas', 'dana_kegiatan')->where('is_active', true)->get();
        $totalSaldoKegiatan = $danaKegiatanList->sum('saldo_sekarang');

        // Total Kekayaan Likuid Kas Seluruh RW 013
        $totalLikuidRw = ($kasRw->saldo_sekarang ?? 0) + ($kasSampah->saldo_sekarang ?? 0) + $totalSaldoRt + $totalSaldoKegiatan;

        // 4. Statistik Tagihan Bulan Berjalan
        $currentPeriode = $request->input('periode', Carbon::now()->format('Y-m'));
        $tagihans = TagihanKeluarga::where('periode', $currentPeriode)->get();

        $totalTagihanBulanIni = $tagihans->sum('total_tagihan');
        $totalPenerimaanBulanIni = $tagihans->sum('total_dibayar');
        $sisaPiutangBulanIni = $tagihans->whereIn('status', ['Belum Dibayar', 'Terlambat', 'Dibayar Sebagian'])->sum('sisa_tagihan');

        $totalKkTerdaftar = KartuKeluarga::count();
        $totalKkLunas = $tagihans->where('status', 'Lunas')->count();
        $totalKkMenunggu = $tagihans->where('status', 'Menunggu Konfirmasi')->count();
        $totalKkBelum = $tagihans->whereIn('status', ['Belum Dibayar', 'Terlambat'])->count();
        $totalKkDibebaskan = $tagihans->where('status', 'Dibebaskan')->count();

        $persenKolektibilitas = $totalKkTerdaftar > 0 ? round(($totalKkLunas / $totalKkTerdaftar) * 100, 1) : 0;

        // Pemasukan & Pengeluaran Riil Periode Ini
        $periodeDate = Carbon::parse($currentPeriode . '-01');
        $totalPemasukanPeriode = TransaksiKeuangan::where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
            ->whereYear('tanggal_transaksi', $periodeDate->year)
            ->whereMonth('tanggal_transaksi', $periodeDate->month)
            ->sum('nominal');

        $totalPengeluaranPeriode = TransaksiKeuangan::where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
            ->whereYear('tanggal_transaksi', $periodeDate->year)
            ->whereMonth('tanggal_transaksi', $periodeDate->month)
            ->sum('nominal');

        // Rekap Masuk/Keluar per Akun Kas Utama
        $kasRwMasuk = TransaksiKeuangan::where('buku_kas_id', $kasRw->id ?? 1)->where('status', 'selesai')->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])->sum('nominal');
        $kasRwKeluar = TransaksiKeuangan::where('buku_kas_id', $kasRw->id ?? 1)->where('status', 'selesai')->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])->sum('nominal');
        
        $kasSampahMasuk = TransaksiKeuangan::where('buku_kas_id', $kasSampah->id ?? 2)->where('status', 'selesai')->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])->sum('nominal');
        $kasSampahKeluar = TransaksiKeuangan::where('buku_kas_id', $kasSampah->id ?? 2)->where('status', 'selesai')->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])->sum('nominal');

        // 5. Performa Kolektibilitas Per RT (RT 001 s/d RT 008)
        $performaRt = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT);
            $kkRt = KartuKeluarga::where('rt', $rtStr)->orWhere('rt', (string)$i)->count();
            $tagihanRt = $tagihans->filter(function($t) use ($rtStr, $i) {
                return $t->rt_number == $rtStr || $t->rt_number == $i;
            });
            $lunasRt = $tagihanRt->where('status', 'Lunas')->count();
            $terkumpulRt = $tagihanRt->sum('total_dibayar');
            $targetRt = $tagihanRt->sum('total_tagihan');
            $persenRt = $kkRt > 0 ? round(($lunasRt / $kkRt) * 100, 1) : 0;

            $performaRt[] = [
                'rt' => 'RT ' . $rtStr,
                'rt_raw' => $rtStr,
                'total_kk' => $kkRt,
                'lunas_kk' => $lunasRt,
                'target_nominal' => $targetRt,
                'terkumpul_nominal' => $terkumpulRt,
                'persen' => $persenRt,
            ];
        }

        // 6. Grafik Cashflow 6 Bulan Terakhir & Pertumbuhan Saldo
        $labelsChart = [];
        $dataPemasukanChart = [];
        $dataPengeluaranChart = [];
        $dataSaldoChart = [];
        $runningCumulativeSaldo = $totalLikuidRw;

        for ($m = 5; $m >= 0; $m--) {
            $date = Carbon::now()->subMonths($m);
            $labelsChart[] = $date->translatedFormat('M Y');

            $in = TransaksiKeuangan::where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
                ->whereYear('tanggal_transaksi', $date->year)
                ->whereMonth('tanggal_transaksi', $date->month)
                ->sum('nominal');

            $out = TransaksiKeuangan::where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
                ->whereYear('tanggal_transaksi', $date->year)
                ->whereMonth('tanggal_transaksi', $date->month)
                ->sum('nominal');

            $dataPemasukanChart[] = (float) $in;
            $dataPengeluaranChart[] = (float) $out;
            $dataSaldoChart[] = max(0, $totalLikuidRw - (($m) * 800000) + ($in - $out));
        }

        // Ringkasan Kategori Pemasukan & Pengeluaran
        $sumberPemasukan = TransaksiKeuangan::where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
            ->select('kategori', DB::raw('SUM(nominal) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $kategoriPengeluaran = TransaksiKeuangan::where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
            ->select('kategori', DB::raw('SUM(nominal) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 7. Transaksi Terakhir dengan Filter Pencarian
        $trxQuery = TransaksiKeuangan::with(['bukuKas', 'petugas']);

        if ($request->filled('search')) {
            $s = $request->search;
            $trxQuery->where(function($q) use ($s) {
                $q->where('nomor_transaksi', 'like', "%{$s}%")
                  ->orWhere('penyetor_penerima', 'like', "%{$s}%")
                  ->orWhere('kategori', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%");
            });
        }
        if ($request->filled('kategori')) {
            $trxQuery->where('kategori', $request->kategori);
        }
        if ($request->filled('jenis_transaksi')) {
            $trxQuery->where('jenis_transaksi', $request->jenis_transaksi);
        }
        if ($request->filled('buku_kas_id')) {
            $trxQuery->where('buku_kas_id', $request->buku_kas_id);
        }

        $recentTransactions = $trxQuery->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $kategoriList = TransaksiKeuangan::distinct()->pluck('kategori')->filter();
        $allBukuKas = BukuKas::where('is_active', true)->get();

        // 8. Log Audit Keuangan Terkini
        $recentAudits = KeuanganAuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('keuangan.dashboard_rw', compact(
            'kasRw',
            'kasSampah',
            'kasRwMasuk',
            'kasRwKeluar',
            'kasSampahMasuk',
            'kasSampahKeluar',
            'kasRtList',
            'totalSaldoRt',
            'danaKegiatanList',
            'totalSaldoKegiatan',
            'totalLikuidRw',
            'currentPeriode',
            'tagihans',
            'totalTagihanBulanIni',
            'totalPenerimaanBulanIni',
            'sisaPiutangBulanIni',
            'totalPemasukanPeriode',
            'totalPengeluaranPeriode',
            'totalKkTerdaftar',
            'totalKkLunas',
            'totalKkMenunggu',
            'totalKkBelum',
            'totalKkDibebaskan',
            'persenKolektibilitas',
            'performaRt',
            'labelsChart',
            'dataPemasukanChart',
            'dataPengeluaranChart',
            'dataSaldoChart',
            'sumberPemasukan',
            'kategoriPengeluaran',
            'recentTransactions',
            'kategoriList',
            'allBukuKas',
            'recentAudits'
        ));
    }

    /**
     * Dashboard Keuangan Pengurus RT (Terisolasi strictly hanya RT mereka)
     */
    public function dashboardRt(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'rt' && !in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Pengurus RT.');
        }

        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        // Ambil Buku Kas milik RT ini
        $bukuKasRt = BukuKas::where('jenis_kas', 'kas_rt')
            ->where(function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
            })->first();

        if (!$bukuKasRt) {
            $bukuKasRt = BukuKas::firstOrCreate(
                ['kode_kas' => 'KAS-RT-' . $rtStr],
                [
                    'nama_kas' => 'Buku Kas RT ' . $rtStr . ' RW 013',
                    'jenis_kas' => 'kas_rt',
                    'rt_number' => $rtStr,
                    'saldo_awal' => 0,
                    'saldo_sekarang' => 0,
                    'penanggung_jawab_id' => $user->id,
                    'nama_penanggung_jawab' => $user->name,
                    'deskripsi' => 'Kas Operasional RT ' . $rtStr,
                    'is_active' => true,
                ]
            );
        }

        // Ambil info Kas Sampah
        $kasSampah = BukuKas::where('jenis_kas', 'kas_sampah')->first();

        // Tagihan KK di RT ini
        $currentPeriode = $request->input('periode', Carbon::now()->format('Y-m'));
        $tagihansRt = TagihanKeluarga::with(['kartuKeluarga', 'kepalaKeluarga'])
            ->where('periode', $currentPeriode)
            ->where(function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
            })->get();

        $totalKkRt = KartuKeluarga::where('rt', $rtStr)->orWhere('rt', ltrim($rtStr, '0'))->count();
        $totalLunas = $tagihansRt->where('status', 'Lunas')->count();
        $totalMenunggu = $tagihansRt->where('status', 'Menunggu Konfirmasi')->count();
        $totalBelum = $tagihansRt->whereIn('status', ['Belum Dibayar', 'Terlambat'])->count();
        $totalDibebaskan = $tagihansRt->where('status', 'Dibebaskan')->count();

        $nominalTerkumpulRt = $tagihansRt->sum('nominal_rt');
        $nominalBelumRt = $tagihansRt->whereIn('status', ['Belum Dibayar', 'Terlambat'])->sum('nominal_rt');
        $persenKolektibilitas = $totalKkRt > 0 ? round(($totalLunas / $totalKkRt) * 100, 1) : 0;

        // Transaksi Buku Kas RT ini saja
        $transaksis = TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        // Pemasukan & Pengeluaran Bulan Ini
        $pemasukanBulanIni = TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
            ->whereMonth('tanggal_transaksi', Carbon::now()->month)
            ->whereYear('tanggal_transaksi', Carbon::now()->year)
            ->sum('nominal');

        $pengeluaranBulanIni = TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
            ->whereMonth('tanggal_transaksi', Carbon::now()->month)
            ->whereYear('tanggal_transaksi', Carbon::now()->year)
            ->sum('nominal');

        return view('keuangan.dashboard_rt', compact(
            'bukuKasRt',
            'kasSampah',
            'rtStr',
            'currentPeriode',
            'tagihansRt',
            'totalKkRt',
            'totalLunas',
            'totalMenunggu',
            'totalBelum',
            'totalDibebaskan',
            'nominalTerkumpulRt',
            'nominalBelumRt',
            'persenKolektibilitas',
            'transaksis',
            'pemasukanBulanIni',
            'pengeluaranBulanIni'
        ));
    }

    /**
     * Dashboard Keuangan Warga (Hanya KK milik sendiri)
     */
    public function dashboardWarga(Request $request)
    {
        $user = Auth::user();

        // Cari KK warga
        $kartuKeluarga = null;
        if ($user->kartu_keluarga_id) {
            $kartuKeluarga = KartuKeluarga::find($user->kartu_keluarga_id);
        }
        if (!$kartuKeluarga && $user->no_kk) {
            $kartuKeluarga = KartuKeluarga::where('no_kk', $user->no_kk)->first();
        }

        if (!$kartuKeluarga) {
            return view('keuangan.dashboard_warga', [
                'kartuKeluarga' => null,
                'tagihanBulanIni' => null,
                'riwayatTagihans' => collect([]),
                'totalIuranTerkumpul' => 0,
                'kasRw' => BukuKas::where('jenis_kas', 'kas_rw')->first(),
            ]);
        }

        $currentPeriode = Carbon::now()->format('Y-m');
        $tagihanBulanIni = TagihanKeluarga::where('kartu_keluarga_id', $kartuKeluarga->id)
            ->where('periode', $currentPeriode)
            ->first();

        $riwayatTagihans = TagihanKeluarga::where('kartu_keluarga_id', $kartuKeluarga->id)
            ->orderBy('periode', 'desc')
            ->get();

        $totalIuranTerkumpul = $riwayatTagihans->where('status', 'Lunas')->sum('total_dibayar');

        // Rekening tujuan pembayaran RW 013
        $kasRw = BukuKas::where('jenis_kas', 'kas_rw')->first();

        return view('keuangan.dashboard_warga', compact(
            'kartuKeluarga',
            'tagihanBulanIni',
            'riwayatTagihans',
            'totalIuranTerkumpul',
            'kasRw'
        ));
    }

    /**
     * Daftar Buku Kas Terpadu
     */
    public function bukuKasIndex(Request $request)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        $query = BukuKas::query();

        if (!$isRw) {
            // Role RT hanya bisa melihat Kas RT mereka dan Kas Sampah
            $query->where(function($q) use ($rtStr) {
                $q->where(function($sub) use ($rtStr) {
                    $sub->where('jenis_kas', 'kas_rt')
                        ->where(function($rtq) use ($rtStr) {
                            $rtq->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
                        });
                })->orWhere('jenis_kas', 'kas_sampah');
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_kas', $request->jenis);
        }

        $bukuKas = $query->orderBy('jenis_kas', 'asc')->orderBy('kode_kas', 'asc')->get();

        return view('keuangan.buku_kas.index', compact('bukuKas', 'isRw', 'rtStr'));
    }

    /**
     * Detail Buku Kas / Rekening Koran & Mutasi
     */
    public function bukuKasDetail(Request $request, $id)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        $bukuKas = BukuKas::findOrFail($id);

        // Security check
        if (!$isRw && $bukuKas->jenis_kas === 'kas_rt') {
            if ($bukuKas->clean_rt != $rtStr && $bukuKas->rt_number != ltrim($rtStr, '0')) {
                abort(403, 'Anda tidak memiliki hak akses melihat buku kas RT lain.');
            }
        }
        if (!$isRw && in_array($bukuKas->jenis_kas, ['kas_rw', 'dana_kegiatan'])) {
            abort(403, 'Akses kas ini khusus Pengurus RW 013.');
        }

        // Filter mutasi
        $query = TransaksiKeuangan::where('buku_kas_id', $bukuKas->id);

        if ($request->filled('jenis')) {
            $query->where('jenis_transaksi', $request->jenis);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nomor_transaksi', 'like', "%{$s}%")
                  ->orWhere('kategori', 'like', "%{$s}%")
                  ->orWhere('penyetor_penerima', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%");
            });
        }

        $transaksis = $query->orderBy('tanggal_transaksi', 'desc')->orderBy('id', 'desc')->paginate(20);

        // Statistik Buku Kas
        $totalMasuk = TransaksiKeuangan::where('buku_kas_id', $bukuKas->id)
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
            ->sum('nominal');

        $totalKeluar = TransaksiKeuangan::where('buku_kas_id', $bukuKas->id)
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
            ->sum('nominal');

        $kategoriList = KategoriTransaksi::where('jenis_kas', $bukuKas->jenis_kas)->get();
        $targetBukuTransfer = BukuKas::where('id', '!=', $bukuKas->id)->where('is_active', true)->get();

        return view('keuangan.buku_kas.show', compact('bukuKas', 'transaksis', 'totalMasuk', 'totalKeluar', 'kategoriList', 'targetBukuTransfer', 'isRw'));
    }

    /**
     * Jurnal Transaksi Keuangan Seluruhnya
     */
    public function transaksiIndex(Request $request)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        $query = TransaksiKeuangan::with(['bukuKas', 'petugas']);

        if (!$isRw) {
            $query->whereHas('bukuKas', function($q) use ($rtStr) {
                $q->where(function($sub) use ($rtStr) {
                    $sub->where('jenis_kas', 'kas_rt')
                        ->where(function($rtq) use ($rtStr) {
                            $rtq->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
                        });
                })->orWhere('jenis_kas', 'kas_sampah');
            });
        }

        if ($request->filled('buku_kas_id')) {
            $query->where('buku_kas_id', $request->buku_kas_id);
        }
        if ($request->filled('jenis_transaksi')) {
            $query->where('jenis_transaksi', $request->jenis_transaksi);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nomor_transaksi', 'like', "%{$s}%")
                  ->orWhere('penyetor_penerima', 'like', "%{$s}%")
                  ->orWhere('kategori', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%");
            });
        }

        $transaksis = $query->orderBy('tanggal_transaksi', 'desc')->orderBy('id', 'desc')->paginate(20);

        if ($isRw) {
            $availableBuku = BukuKas::where('is_active', true)->get();
        } else {
            $availableBuku = BukuKas::where('is_active', true)
                ->where(function($q) use ($rtStr) {
                    $q->where('rt_number', $rtStr)->orWhere('jenis_kas', 'kas_sampah');
                })->get();
        }

        return view('keuangan.transaksi.index', compact('transaksis', 'availableBuku', 'isRw'));
    }

    /**
     * Form Catat Transaksi Baru
     */
    public function transaksiCreate(Request $request)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        if ($isRw) {
            $bukuKasList = BukuKas::where('is_active', true)->get();
        } else {
            $bukuKasList = BukuKas::where('is_active', true)
                ->where(function($q) use ($rtStr) {
                    $q->where('rt_number', $rtStr)->orWhere('jenis_kas', 'kas_sampah');
                })->get();
        }

        $kategoris = KategoriTransaksi::all();

        return view('keuangan.transaksi.create', compact('bukuKasList', 'kategoris', 'isRw'));
    }

    /**
     * Simpan Transaksi Keuangan Baru
     */
    public function transaksiStore(Request $request)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        $request->validate([
            'buku_kas_id' => 'required|exists:buku_kas,id',
            'jenis_transaksi' => 'required|in:pemasukan,pengeluaran,transfer_keluar',
            'tanggal_transaksi' => 'required|date',
            'kategori' => 'required|string|max:100',
            'nominal' => 'required|numeric|min:1000',
            'metode_pembayaran' => 'required|in:tunai,transfer_bank,qris',
            'penyetor_penerima' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string',
            'bukti_transaksi' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
            'target_buku_kas_id' => 'nullable|required_if:jenis_transaksi,transfer_keluar|exists:buku_kas,id',
        ]);

        $bukuKas = BukuKas::findOrFail($request->buku_kas_id);

        if (!$isRw && $bukuKas->jenis_kas === 'kas_rt' && $bukuKas->clean_rt != $rtStr) {
            abort(403, 'Anda tidak berwenang mencatat transaksi pada kas RT lain.');
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');
        }

        DB::beginTransaction();
        try {
            $date = Carbon::parse($request->tanggal_transaksi);
            $prefix = 'TRX-' . $date->format('Ym') . '-';
            $latestTrx = TransaksiKeuangan::where('nomor_transaksi', 'like', $prefix . '%')->count();
            $nomorTrx = $prefix . str_pad($latestTrx + 1, 4, '0', STR_PAD_LEFT);

            if ($request->jenis_transaksi === 'transfer_keluar') {
                $targetBuku = BukuKas::findOrFail($request->target_buku_kas_id);
                if ($targetBuku->id == $bukuKas->id) {
                    return back()->withErrors(['target_buku_kas_id' => 'Kas tujuan tidak boleh sama dengan kas sumber!'])->withInput();
                }

                $transferRef = 'TRF-' . $date->format('Ymd') . '-' . uniqid();

                $trxKeluar = TransaksiKeuangan::create([
                    'nomor_transaksi' => $nomorTrx . '-OUT',
                    'buku_kas_id' => $bukuKas->id,
                    'tanggal_transaksi' => $request->tanggal_transaksi,
                    'jenis_transaksi' => 'transfer_keluar',
                    'kategori' => 'Transfer Antar Kas (Keluar)',
                    'nominal' => $request->nominal,
                    'saldo_sebelumnya' => $bukuKas->saldo_sekarang,
                    'saldo_setelahnya' => $bukuKas->saldo_sekarang - $request->nominal,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'penyetor_penerima' => 'Tujuan: ' . $targetBuku->nama_kas,
                    'keterangan' => $request->keterangan ?: 'Transfer dana kas ke ' . $targetBuku->nama_kas,
                    'petugas_id' => $user->id,
                    'nama_petugas' => $user->name,
                    'bukti_transaksi_path' => $buktiPath,
                    'transfer_ref_id' => $transferRef,
                    'status' => 'selesai',
                ]);
                $bukuKas->recalculateSaldo();

                $trxMasuk = TransaksiKeuangan::create([
                    'nomor_transaksi' => $nomorTrx . '-IN',
                    'buku_kas_id' => $targetBuku->id,
                    'tanggal_transaksi' => $request->tanggal_transaksi,
                    'jenis_transaksi' => 'transfer_masuk',
                    'kategori' => 'Transfer Antar Kas (Masuk)',
                    'nominal' => $request->nominal,
                    'saldo_sebelumnya' => $targetBuku->saldo_sekarang,
                    'saldo_setelahnya' => $targetBuku->saldo_sekarang + $request->nominal,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'penyetor_penerima' => 'Sumber: ' . $bukuKas->nama_kas,
                    'keterangan' => $request->keterangan ?: 'Penerimaan transfer dana dari ' . $bukuKas->nama_kas,
                    'petugas_id' => $user->id,
                    'nama_petugas' => $user->name,
                    'bukti_transaksi_path' => $buktiPath,
                    'transfer_ref_id' => $transferRef,
                    'status' => 'selesai',
                ]);
                $targetBuku->recalculateSaldo();

                KeuanganAuditLog::create([
                    'user_id' => $user->id,
                    'transaksi_id' => $trxKeluar->id,
                    'action' => 'transfer_antar_kas',
                    'buku_kas_kode' => $bukuKas->kode_kas,
                    'deskripsi' => "Transfer antar kas Rp " . number_format($request->nominal, 0, ',', '.') . " dari [{$bukuKas->nama_kas}] ke [{$targetBuku->nama_kas}].",
                    'new_values' => ['nominal' => $request->nominal, 'sumber' => $bukuKas->kode_kas, 'tujuan' => $targetBuku->kode_kas],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                DB::commit();
                return redirect()->route('keuangan.buku_kas.show', $bukuKas->id)->with('success', 'Transfer antar kas berhasil dicatat!');
            }

            $saldoSebelumnya = $bukuKas->saldo_sekarang;
            $saldoSetelahnya = ($request->jenis_transaksi === 'pemasukan') 
                ? ($saldoSebelumnya + $request->nominal) 
                : ($saldoSebelumnya - $request->nominal);

            $transaksi = TransaksiKeuangan::create([
                'nomor_transaksi' => $nomorTrx,
                'buku_kas_id' => $bukuKas->id,
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'jenis_transaksi' => $request->jenis_transaksi,
                'kategori' => $request->kategori,
                'nominal' => $request->nominal,
                'saldo_sebelumnya' => $saldoSebelumnya,
                'saldo_setelahnya' => $saldoSetelahnya,
                'metode_pembayaran' => $request->metode_pembayaran,
                'penyetor_penerima' => $request->penyetor_penerima,
                'keterangan' => $request->keterangan,
                'petugas_id' => $user->id,
                'nama_petugas' => $user->name,
                'bukti_transaksi_path' => $buktiPath,
                'status' => 'selesai',
            ]);

            $bukuKas->recalculateSaldo();

            KeuanganAuditLog::create([
                'user_id' => $user->id,
                'transaksi_id' => $transaksi->id,
                'action' => 'create_transaksi',
                'buku_kas_kode' => $bukuKas->kode_kas,
                'deskripsi' => "Mencatat {$request->jenis_transaksi} Rp " . number_format($request->nominal, 0, ',', '.') . " pada [{$bukuKas->nama_kas}] kategori {$request->kategori}.",
                'new_values' => $transaksi->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return redirect()->route('keuangan.buku_kas.show', $bukuKas->id)->with('success', 'Transaksi keuangan berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mencatat transaksi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Batalkan Transaksi (Soft Cancellation dengan Audit Trail & Balance Reversal)
     */
    public function transaksiCancel(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin', 'rt'])) {
            abort(403, 'Akses dibatasi.');
        }

        $request->validate([
            'alasan_batal' => 'required|string|min:5|max:255',
        ]);

        $trx = TransaksiKeuangan::with('bukuKas')->findOrFail($id);
        if ($trx->status === 'dibatalkan') {
            return back()->with('error', 'Transaksi ini sudah pernah dibatalkan sebelumnya.');
        }

        DB::beginTransaction();
        try {
            $bukuKas = $trx->bukuKas;

            if ($trx->transfer_ref_id) {
                $pasangan = TransaksiKeuangan::where('transfer_ref_id', $trx->transfer_ref_id)
                    ->where('id', '!=', $trx->id)
                    ->first();
                if ($pasangan) {
                    $pasangan->update([
                        'status' => 'dibatalkan',
                        'dibatalkan_oleh_id' => $user->id,
                        'alasan_batal' => 'Dibatalkan berpasangan: ' . $request->alasan_batal,
                        'dibatalkan_pada' => Carbon::now(),
                    ]);
                    $pasangan->bukuKas->recalculateSaldo();
                }
            }

            $trx->update([
                'status' => 'dibatalkan',
                'dibatalkan_oleh_id' => $user->id,
                'alasan_batal' => $request->alasan_batal,
                'dibatalkan_pada' => Carbon::now(),
            ]);

            $bukuKas->recalculateSaldo();

            KeuanganAuditLog::create([
                'user_id' => $user->id,
                'transaksi_id' => $trx->id,
                'action' => 'cancel_transaksi',
                'buku_kas_kode' => $bukuKas->kode_kas,
                'deskripsi' => "Membatalkan transaksi [{$trx->nomor_transaksi}] nominal Rp " . number_format($trx->nominal, 0, ',', '.') . " dengan alasan: {$request->alasan_batal}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return back()->with('success', 'Transaksi berhasil dibatalkan dan saldo kas otomatis disesuaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Keuangan Formal Terpadu
     */
    public function laporanIndex(Request $request)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        $filterTipe = $request->input('tipe_laporan', 'bulanan');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $bukuKasId = $request->input('buku_kas_id');

        if (!$startDate || !$endDate) {
            if ($filterTipe === 'harian') {
                $startDate = Carbon::today()->toDateString();
                $endDate = Carbon::today()->toDateString();
            } elseif ($filterTipe === 'mingguan') {
                $startDate = Carbon::now()->startOfWeek()->toDateString();
                $endDate = Carbon::now()->endOfWeek()->toDateString();
            } elseif ($filterTipe === 'tahunan') {
                $startDate = Carbon::now()->startOfYear()->toDateString();
                $endDate = Carbon::now()->endOfYear()->toDateString();
            } else {
                $startDate = Carbon::now()->startOfMonth()->toDateString();
                $endDate = Carbon::now()->endOfMonth()->toDateString();
            }
        }

        $query = TransaksiKeuangan::with('bukuKas')
            ->where('status', 'selesai')
            ->whereDate('tanggal_transaksi', '>=', $startDate)
            ->whereDate('tanggal_transaksi', '<=', $endDate);

        if (!$isRw) {
            $query->whereHas('bukuKas', function($q) use ($rtStr) {
                $q->where(function($sub) use ($rtStr) {
                    $sub->where('jenis_kas', 'kas_rt')
                        ->where(function($rtq) use ($rtStr) {
                            $rtq->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
                        });
                })->orWhere('jenis_kas', 'kas_sampah');
            });
        }

        if ($bukuKasId) {
            $query->where('buku_kas_id', $bukuKasId);
        }

        $transaksis = $query->orderBy('tanggal_transaksi', 'asc')->get();

        $totalPemasukan = $transaksis->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])->sum('nominal');
        $totalPengeluaran = $transaksis->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])->sum('nominal');
        $surplusDefisit = $totalPemasukan - $totalPengeluaran;

        $breakdownKategori = $transaksis->groupBy('kategori')->map(function($items) {
            return [
                'kategori' => $items->first()->kategori,
                'jenis' => $items->first()->jenis_transaksi,
                'total' => $items->sum('nominal'),
                'count' => $items->count(),
            ];
        })->sortByDesc('total');

        if ($isRw) {
            $bukuKasList = BukuKas::where('is_active', true)->get();
        } else {
            $bukuKasList = BukuKas::where('is_active', true)
                ->where(function($q) use ($rtStr) {
                    $q->where('rt_number', $rtStr)->orWhere('jenis_kas', 'kas_sampah');
                })->get();
        }

        return view('keuangan.laporan.index', compact(
            'transaksis',
            'totalPemasukan',
            'totalPengeluaran',
            'surplusDefisit',
            'breakdownKategori',
            'bukuKasList',
            'filterTipe',
            'startDate',
            'endDate',
            'bukuKasId',
            'isRw'
        ));
    }

    /**
     * Cetak Laporan Keuangan Resmi Kop RW 013 (Print Ready View)
     */
    public function laporanCetak(Request $request)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $bukuKasId = $request->input('buku_kas_id');

        $query = TransaksiKeuangan::with('bukuKas')
            ->where('status', 'selesai')
            ->whereDate('tanggal_transaksi', '>=', $startDate)
            ->whereDate('tanggal_transaksi', '<=', $endDate);

        if (!$isRw) {
            $query->whereHas('bukuKas', function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('jenis_kas', 'kas_sampah');
            });
        }

        if ($bukuKasId) {
            $query->where('buku_kas_id', $bukuKasId);
        }

        $transaksis = $query->orderBy('tanggal_transaksi', 'asc')->get();
        $totalPemasukan = $transaksis->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])->sum('nominal');
        $totalPengeluaran = $transaksis->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])->sum('nominal');
        $surplusDefisit = $totalPemasukan - $totalPengeluaran;

        $targetBuku = $bukuKasId ? BukuKas::find($bukuKasId) : null;

        return view('keuangan.laporan.cetak', compact(
            'transaksis',
            'totalPemasukan',
            'totalPengeluaran',
            'surplusDefisit',
            'startDate',
            'endDate',
            'targetBuku',
            'isRw'
        ));
    }

    /**
     * Pengaturan Tarif Iuran Bulanan
     */
    public function pengaturanIuran(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses khusus Ketua RW dan Administrator.');
        }

        $tarifs = IuranTarif::with('histories.diubahOleh')->get();
        $totalTarifKk = $tarifs->sum('nominal');

        return view('keuangan.pengaturan', compact('tarifs', 'totalTarifKk'));
    }

    /**
     * Update Tarif Iuran & Catat Riwayat Musyawarah
     */
    public function updateTarif(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses dibatasi.');
        }

        $request->validate([
            'nominal' => 'required|numeric|min:0',
            'alasan_perubahan' => 'required|string|min:5|max:500',
        ]);

        $tarif = IuranTarif::findOrFail($id);
        $nominalLama = $tarif->nominal;

        if ($nominalLama == $request->nominal) {
            return back()->with('info', 'Nominal tarif tidak berubah.');
        }

        DB::beginTransaction();
        try {
            IuranTarifHistory::create([
                'iuran_tarif_id' => $tarif->id,
                'nominal_lama' => $nominalLama,
                'nominal_baru' => $request->nominal,
                'tanggal_perubahan' => Carbon::now()->toDateString(),
                'diubah_oleh_id' => $user->id,
                'alasan_perubahan' => $request->alasan_perubahan,
            ]);

            $tarif->update([
                'nominal' => $request->nominal,
                'keterangan_musyawarah' => $request->alasan_perubahan,
            ]);

            KeuanganAuditLog::create([
                'user_id' => $user->id,
                'action' => 'update_tarif',
                'deskripsi' => "Mengubah tarif [{$tarif->nama_tarif}] dari Rp " . number_format($nominalLama, 0, ',', '.') . " menjadi Rp " . number_format($request->nominal, 0, ',', '.') . ". Alasan: {$request->alasan_perubahan}",
                'new_values' => ['nominal_baru' => $request->nominal],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return back()->with('success', 'Tarif iuran berhasil diperbarui dan disimpan dalam riwayat musyawarah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui tarif: ' . $e->getMessage());
        }
    }
}
