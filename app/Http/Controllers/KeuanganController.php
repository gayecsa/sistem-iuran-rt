<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BukuKas;
use App\Models\IuranTarif;
use App\Models\IuranTarifHistory;
use App\Models\KategoriTransaksi;
use App\Models\TagihanKeluarga;
use App\Models\TagihanItem;
use App\Models\PembayaranIuran;
use App\Models\PembayaranAlokasi;
use App\Models\IuranKeringanan;
use App\Models\TransaksiKeuangan;
use App\Models\KeuanganAuditLog;
use App\Models\ActivityLog;
use App\Models\KartuKeluarga;
use App\Models\AuditEvaluasiRt;
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
     * Prinsip: SEPARATE FINANCIAL ACCOUNT, CENTRALIZED MONITORING
     * Kas RW adalah dana milik RW; Kas RT milik masing-masing RT.
     */
    public function dashboardRw(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas hanya untuk Pengurus RW 013.');
        }

        $currentPeriode = $request->input('periode', Carbon::now()->format('Y-m'));
        $periodeDate = Carbon::parse($currentPeriode . '-01');

        // 1. Data Kas Induk RW 013 (Entitas Tunggal RW)
        $kasRw = BukuKas::where('jenis_kas', 'kas_rw')->first();
        if (!$kasRw) {
            $kasRw = BukuKas::firstOrCreate(
                ['jenis_kas' => 'kas_rw'],
                ['kode_kas' => 'KAS-RW-013', 'nama_kas' => 'Kas Induk RW 013 Sukamiskin', 'saldo_sekarang' => 0, 'is_active' => true]
            );
        }
        $saldoKasRw = floatval($kasRw->saldo_sekarang ?? 0);

        // 2. Data Kas Kebersihan & Sampah
        $kasSampah = BukuKas::where('jenis_kas', 'kas_sampah')->first();
        $saldoKasSampah = floatval($kasSampah->saldo_sekarang ?? 0);

        // 3. Data Kas Keamanan & Pos Ronda
        $kasKeamanan = BukuKas::where('kode_kas', 'KEG-RONDA')
            ->orWhere('nama_kas', 'like', '%Keamanan%')
            ->orWhere('nama_kas', 'like', '%Ronda%')
            ->first();
        $saldoKasKeamanan = floatval($kasKeamanan->saldo_sekarang ?? 0);

        // 4. Data Dana Kegiatan Lingkungan & Sosial (di luar Keamanan)
        $kegiatanLainQuery = BukuKas::where('jenis_kas', 'dana_kegiatan')->where('is_active', true);
        if ($kasKeamanan) {
            $kegiatanLainQuery->where('id', '!=', $kasKeamanan->id);
        }
        $danaKegiatanList = $kegiatanLainQuery->get();
        $saldoKasKegiatan = floatval($danaKegiatanList->sum('saldo_sekarang'));

        // 5. Data Seluruh Kas RT 001 - RT 008 (MONITORING SAJA - Milik Masing-masing RT)
        $kasRtList = BukuKas::where('jenis_kas', 'kas_rt')->orderBy('rt_number', 'asc')->get();
        $totalSaldoRt = floatval($kasRtList->sum('saldo_sekarang'));

        // 6. Pemasukan & Pengeluaran Khusus Kas Induk RW (Murni Transaksi Pemasukan & Pengeluaran, Exclude Transfer Internal)
        $pemasukanRwBulanIni = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
            ->where('status', 'selesai')
            ->where('jenis_transaksi', 'pemasukan')
            ->whereYear('tanggal_transaksi', $periodeDate->year)
            ->whereMonth('tanggal_transaksi', $periodeDate->month)
            ->sum('nominal');

        $pengeluaranRwBulanIni = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
            ->where('status', 'selesai')
            ->where('jenis_transaksi', 'pengeluaran')
            ->whereYear('tanggal_transaksi', $periodeDate->year)
            ->whereMonth('tanggal_transaksi', $periodeDate->month)
            ->sum('nominal');

        // Saldo Awal & Akhir Khusus Kas Induk RW 013 (Formula: Saldo Awal + Pemasukan - Pengeluaran = Saldo Akhir)
        $saldoAwalRw = max(0, $saldoKasRw - $pemasukanRwBulanIni + $pengeluaranRwBulanIni);
        $saldoAkhirRw = $saldoKasRw;

        // Total akumulasi sepanjang tahun/historis Kas RW
        $kasRwMasuk = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
            ->where('status', 'selesai')
            ->where('jenis_transaksi', 'pemasukan')
            ->sum('nominal');

        $kasRwKeluar = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
            ->where('status', 'selesai')
            ->where('jenis_transaksi', 'pengeluaran')
            ->sum('nominal');

        // 7. Statistik Tagihan & Piutang Iuran Khusus RW Bulan Ini
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

        // Piutang Iuran Khusus Porsi Kas RW
        $targetIuranRw = $tagihans->sum('nominal_rw');
        $terkumpulIuranRw = $tagihans->where('status', 'Lunas')->sum('nominal_rw')
            + $tagihans->where('status', 'Dibayar Sebagian')->sum(function($t) {
                return min($t->nominal_rw, max(0, $t->total_dibayar - $t->nominal_rt));
            });
        $piutangIuranRw = max(0, $targetIuranRw - $terkumpulIuranRw);

        // 8. Monitoring Detail RT 001 sampai RT 008 (Section 3: Terpisah, Saldo Awal, Pemasukan, Pengeluaran, Saldo Akhir, Iuran, Tunggakan, Status Lapor)
        $monitoringRtList = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT);
            $bukuKasRt = BukuKas::where('jenis_kas', 'kas_rt')
                ->where(function($q) use ($rtStr, $i) {
                    $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$i);
                })->first();

            $saldoAkhirRt = floatval($bukuKasRt->saldo_sekarang ?? 0);

            // Pemasukan & Pengeluaran terverifikasi bulan ini
            $inRt = $bukuKasRt ? TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
                ->where('status', 'selesai')
                ->where('jenis_transaksi', 'pemasukan')
                ->whereYear('tanggal_transaksi', $periodeDate->year)
                ->whereMonth('tanggal_transaksi', $periodeDate->month)
                ->sum('nominal') : 0;

            $outRt = $bukuKasRt ? TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
                ->where('status', 'selesai')
                ->where('jenis_transaksi', 'pengeluaran')
                ->whereYear('tanggal_transaksi', $periodeDate->year)
                ->whereMonth('tanggal_transaksi', $periodeDate->month)
                ->sum('nominal') : 0;

            // Rumus Saldo: Saldo Awal = Saldo Akhir - Pemasukan + Pengeluaran
            $saldoAwalRt = max(0, $saldoAkhirRt - $inRt + $outRt);

            // Iuran warga RT setempat
            $tagihanRt = $tagihans->filter(function($t) use ($rtStr, $i) {
                return $t->rt_number == $rtStr || $t->rt_number == $i;
            });
            $targetRt = $tagihanRt->sum('nominal_rt');
            $terkumpulRt = $tagihanRt->where('status', 'Lunas')->sum('nominal_rt') 
                + $tagihanRt->where('status', 'Dibayar Sebagian')->sum(function($t) {
                    return min($t->nominal_rt, $t->total_dibayar);
                });
            $tunggakanRt = max(0, $targetRt - $terkumpulRt);

            // Status Laporan Keuangan dari AuditEvaluasiRt
            $evalRt = AuditEvaluasiRt::where('tahun', $periodeDate->year)
                ->where(function($q) use ($rtStr, $i) {
                    $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$i);
                })->first();

            $statusLaporan = $evalRt && $evalRt->status_laporan ? $evalRt->status_laporan : 'Belum Lapor';

            // User PIC RT
            $picRt = User::where('role', 'rt')
                ->where(function($q) use ($rtStr, $i) {
                    $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$i);
                })->first();

            $monitoringRtList[] = [
                'rt_num' => $i,
                'rt_label' => 'RT ' . $rtStr,
                'rt_number' => $rtStr,
                'buku_kas_id' => $bukuKasRt->id ?? null,
                'pic_name' => $picRt->name ?? ('Ketua RT ' . $rtStr),
                'saldo_awal' => $saldoAwalRt,
                'pemasukan' => $inRt,
                'pengeluaran' => $outRt,
                'saldo_akhir' => $saldoAkhirRt,
                'iuran_terkumpul' => $terkumpulRt,
                'tunggakan' => $tunggakanRt,
                'status_laporan' => $statusLaporan,
                'evaluasi' => $evalRt,
            ];
        }

        // Backward compatibility
        $performaRt = $monitoringRtList;

        // 8b. Monitoring Keuangan Unit (Kas Sampah, Keamanan, Posyandu, Kegiatan Sosial)
        $unitBukuKas = BukuKas::where('pemilik_dana', 'unit')->where('is_active', true)->orderBy('id', 'asc')->get();
        $monitoringUnitList = [];
        foreach ($unitBukuKas as $uBk) {
            $inU = TransaksiKeuangan::where('buku_kas_id', $uBk->id)
                ->where('status', 'selesai')
                ->where('jenis_transaksi', 'pemasukan')
                ->whereYear('tanggal_transaksi', $periodeDate->year)
                ->whereMonth('tanggal_transaksi', $periodeDate->month)
                ->sum('nominal');

            $outU = TransaksiKeuangan::where('buku_kas_id', $uBk->id)
                ->where('status', 'selesai')
                ->where('jenis_transaksi', 'pengeluaran')
                ->whereYear('tanggal_transaksi', $periodeDate->year)
                ->whereMonth('tanggal_transaksi', $periodeDate->month)
                ->sum('nominal');

            $saldoAkhirU = floatval($uBk->saldo_sekarang ?? 0);
            $saldoAwalU = max(0, $saldoAkhirU - $inU + $outU);

            $monitoringUnitList[] = [
                'id' => $uBk->id,
                'kode_kas' => $uBk->kode_kas,
                'nama_kas' => $uBk->nama_kas,
                'jenis_kas' => $uBk->jenis_kas,
                'saldo_awal' => $saldoAwalU,
                'pemasukan' => $inU,
                'pengeluaran' => $outU,
                'saldo_akhir' => $saldoAkhirU,
                'penanggung_jawab' => $uBk->nama_penanggung_jawab ?: ($uBk->penanggungJawab->name ?? '-'),
                'rekening' => $uBk->masked_rekening,
            ];
        }
        $totalSaldoUnit = floatval(BukuKas::where('pemilik_dana', 'unit')->sum('saldo_sekarang'));
        $totalDanaTerpantau = $saldoKasRw + $totalSaldoRt + $totalSaldoUnit;
        
        // Saldo Konsolidasi Wilayah (Kas Induk RW + Total Kas 8 RT + Kas Pengelolaan Sampah RW)
        $saldoKonsolidasi = $saldoKasRw + $totalSaldoRt + $saldoKasSampah;

        // 9. Arsitektur Split Settlement & Rekonsiliasi (Section 5)
        $alokasisPeriode = PembayaranAlokasi::whereHas('pembayaranIuran', function($q) use ($periodeDate) {
            $q->whereYear('tanggal_bayar', $periodeDate->year)
              ->whereMonth('tanggal_bayar', $periodeDate->month);
        })->with(['bukuKas', 'tagihanItem'])->get();

        $rekapAlokasiSplit = [
            'kas_rt' => [
                'nama' => 'Iuran Operasional 8 RT',
                'target_kas' => 'Buku Kas RT Masing-masing (8 Akun Otonom)',
                'nominal' => $alokasisPeriode->filter(fn($a) => $a->bukuKas && $a->bukuKas->jenis_kas === 'kas_rt')->sum('nominal_alokasi'),
                'status_settlement' => 'Terkredit ke Kas RT / Tunai di Pengurus',
                'rekonsiliasi' => 'Sesuai Tagihan',
            ],
            'kas_rw' => [
                'nama' => 'Iuran Operasional Kas RW 013',
                'target_kas' => 'Kas Induk RW 013 (Bank Mandiri)',
                'nominal' => $alokasisPeriode->filter(fn($a) => $a->bukuKas && $a->bukuKas->jenis_kas === 'kas_rw')->sum('nominal_alokasi'),
                'status_settlement' => 'Terkredit Langsung / Settlement Titipan Tunai',
                'rekonsiliasi' => 'Sesuai Tagihan',
            ],
            'kas_sampah' => [
                'nama' => 'Retribusi Kebersihan & Pengangkutan Sampah',
                'target_kas' => 'Kas Pengelolaan Sampah (Bank BJB)',
                'nominal' => $alokasisPeriode->filter(fn($a) => $a->bukuKas && $a->bukuKas->jenis_kas === 'kas_sampah')->sum('nominal_alokasi'),
                'status_settlement' => 'Terkredit Langsung / Settlement Titipan Tunai',
                'rekonsiliasi' => 'Sesuai Tagihan',
            ],
            'keamanan' => [
                'nama' => 'Iuran Keamanan & Satpam',
                'target_kas' => 'Dana Pos Ronda & Keamanan',
                'nominal' => $alokasisPeriode->filter(fn($a) => $a->bukuKas && ($a->bukuKas->kode_kas === 'KEG-RONDA' || str_contains(strtolower($a->nama_komponen), 'keamanan')))->sum('nominal_alokasi'),
                'status_settlement' => 'Terkredit Langsung / Settlement Titipan Tunai',
                'rekonsiliasi' => 'Sesuai Tagihan',
            ],
            'kegiatan' => [
                'nama' => 'Iuran Kegiatan Lingkungan & Sosial',
                'target_kas' => 'Dana Peringatan & Kegiatan Lingkungan',
                'nominal' => $alokasisPeriode->filter(fn($a) => $a->bukuKas && str_contains(strtolower($a->nama_komponen), 'kegiatan'))->sum('nominal_alokasi'),
                'status_settlement' => 'Terkredit Langsung / Settlement Titipan Tunai',
                'rekonsiliasi' => 'Sesuai Tagihan',
            ],
        ];

        // Titipan Tunai RT ke RW yang menunggu setor
        $titipanRtList = PembayaranAlokasi::where('status_alokasi', 'titipan_rt')
            ->with(['tagihanItem.tagihanKeluarga.kartuKeluarga', 'bukuKas', 'pembayaranIuran'])
            ->get();
        $totalTitipanMenunggu = $titipanRtList->sum('nominal_alokasi');

        // 10. Grafik Cashflow 6 Bulan Terakhir KHUSUS KAS INDUK RW
        $labelsChart = [];
        $dataPemasukanChart = [];
        $dataPengeluaranChart = [];
        $dataSaldoChart = [];

        for ($m = 5; $m >= 0; $m--) {
            $date = Carbon::now()->subMonths($m);
            $labelsChart[] = $date->translatedFormat('M Y');

            $in = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
                ->where('status', 'selesai')
                ->where('jenis_transaksi', 'pemasukan')
                ->whereYear('tanggal_transaksi', $date->year)
                ->whereMonth('tanggal_transaksi', $date->month)
                ->sum('nominal');

            $out = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
                ->where('status', 'selesai')
                ->where('jenis_transaksi', 'pengeluaran')
                ->whereYear('tanggal_transaksi', $date->year)
                ->whereMonth('tanggal_transaksi', $date->month)
                ->sum('nominal');

            // Hitung saldo kumulatif riil kas RW hingga akhir bulan tersebut
            $endOfMonth = $date->copy()->endOfMonth();
            $inCumulative = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
                ->where('status', 'selesai')
                ->where('jenis_transaksi', 'pemasukan')
                ->where('tanggal_transaksi', '<=', $endOfMonth)
                ->sum('nominal');
            $outCumulative = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
                ->where('status', 'selesai')
                ->where('jenis_transaksi', 'pengeluaran')
                ->where('tanggal_transaksi', '<=', $endOfMonth)
                ->sum('nominal');
            $saldoBulan = floatval($kasRw->saldo_awal ?? 0) + $inCumulative - $outCumulative;

            $dataPemasukanChart[] = (float) $in;
            $dataPengeluaranChart[] = (float) $out;
            $dataSaldoChart[] = max(0, $saldoBulan);
        }

        // Ringkasan Kategori Pemasukan & Pengeluaran Khusus Kas RW
        $sumberPemasukan = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
            ->where('status', 'selesai')
            ->where('jenis_transaksi', 'pemasukan')
            ->select('kategori', DB::raw('SUM(nominal) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $kategoriPengeluaran = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
            ->where('status', 'selesai')
            ->where('jenis_transaksi', 'pengeluaran')
            ->select('kategori', DB::raw('SUM(nominal) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 11. Transaksi dengan Filter Pencarian
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
        if ($request->filled('pemilik_dana')) {
            $trxQuery->where('pemilik_dana', $request->pemilik_dana);
        }
        if ($request->filled('filter_periode') && $request->filter_periode !== 'all') {
            $pDate = Carbon::parse($request->filter_periode . '-01');
            $trxQuery->whereYear('tanggal_transaksi', $pDate->year)
                     ->whereMonth('tanggal_transaksi', $pDate->month);
        }

        $recentTransactions = $trxQuery->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $kategoriList = TransaksiKeuangan::distinct()->pluck('kategori')->filter();
        $allBukuKas = BukuKas::where('is_active', true)->get();

        // 12. Log Audit Keuangan Terkini
        $recentAudits = KeuanganAuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('keuangan.dashboard_rw', compact(
            'kasRw',
            'saldoKasRw',
            'saldoAwalRw',
            'saldoAkhirRw',
            'kasSampah',
            'saldoKasSampah',
            'kasKeamanan',
            'saldoKasKeamanan',
            'saldoKasKegiatan',
            'totalSaldoRt',
            'monitoringUnitList',
            'totalSaldoUnit',
            'totalDanaTerpantau',
            'saldoKonsolidasi',
            'kasRwMasuk',
            'kasRwKeluar',
            'pemasukanRwBulanIni',
            'pengeluaranRwBulanIni',
            'piutangIuranRw',
            'currentPeriode',
            'tagihans',
            'totalTagihanBulanIni',
            'totalPenerimaanBulanIni',
            'sisaPiutangBulanIni',
            'totalKkTerdaftar',
            'totalKkLunas',
            'totalKkMenunggu',
            'totalKkBelum',
            'totalKkDibebaskan',
            'persenKolektibilitas',
            'monitoringRtList',
            'performaRt',
            'rekapAlokasiSplit',
            'titipanRtList',
            'totalTitipanMenunggu',
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
        $activeTab = $request->input('tab', 'ringkasan');
        if (!in_array($activeTab, ['ringkasan', 'buku_kas', 'kas_sampah', 'tagihan', 'transaksi', 'laporan'])) {
            $activeTab = 'ringkasan';
        }

        // 1. Ambil Buku Kas milik RT ini secara otonom
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

        // 2. Rekening Pendukung Lingkungan RT (Kas Sampah & Kas Keamanan/Ronda)
        $kasSampah = BukuKas::where('jenis_kas', 'kas_sampah')->first();
        if ($kasSampah) {
            $kasSampah->recalculateSaldo();
        }

        $kasKeamanan = BukuKas::where('kode_kas', 'KEG-RONDA')
            ->orWhere('nama_kas', 'like', '%Keamanan%')
            ->orWhere('nama_kas', 'like', '%Ronda%')
            ->first();
        if ($kasKeamanan) {
            $kasKeamanan->recalculateSaldo();
        }

        // Saldo Kas Otonom RT (Murni Buku Kas Milik RT Sendiri)
        $totalSaldoOtonomRt = floatval($bukuKasRt ? $bukuKasRt->saldo_sekarang : 0);

        // Total Saldo Keseluruhan Lingkungan RT (Buku Kas RT + Kas Sampah + Kas Keamanan/Ronda - Exclude Kas RW)
        $totalSaldoKeseluruhanRt = $totalSaldoOtonomRt 
            + floatval($kasSampah ? $kasSampah->saldo_sekarang : 0) 
            + floatval($kasKeamanan ? $kasKeamanan->saldo_sekarang : 0);

        // 3. Periode Filter
        $currentPeriode = $request->input('periode', Carbon::now()->format('Y-m'));
        $periodeCarbon = Carbon::parse($currentPeriode . '-01');
        $currentMonth = $periodeCarbon->month;
        $currentYear = $periodeCarbon->year;

        // 4. Data Tagihan Iuran KK di RT ini (Relasi Lengkap Items Rincian)
        $tagihanQuery = TagihanKeluarga::with(['kartuKeluarga', 'kepalaKeluarga', 'items.bukuKas', 'pembayarans'])
            ->where('periode', $currentPeriode)
            ->where(function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
            });

        // Filter status tagihan jika ada
        if ($request->filled('status_tagihan')) {
            $tagihanQuery->where('status', $request->status_tagihan);
        }
        if ($request->filled('search_tagihan')) {
            $s = $request->search_tagihan;
            $tagihanQuery->where(function($q) use ($s) {
                $q->where('nomor_tagihan', 'like', "%{$s}%")
                  ->orWhereHas('kartuKeluarga', function($kq) use ($s) {
                      $kq->where('nama_kepala_keluarga', 'like', "%{$s}%")
                         ->orWhere('no_kk', 'like', "%{$s}%")
                         ->orWhere('alamat', 'like', "%{$s}%")
                         ->orWhere('nomor_rumah', 'like', "%{$s}%");
                  });
            });
        }

        $tagihansRt = (clone $tagihanQuery)->get();

        // Statistik Tagihan RT (Hanya Komponen Wewenang RT: nominal_rt)
        $allTagihanPeriode = TagihanKeluarga::where('periode', $currentPeriode)
            ->where(function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
            })->get();

        $totalKkRt = KartuKeluarga::where('rt', $rtStr)->orWhere('rt', ltrim($rtStr, '0'))->count();
        $totalLunas = $allTagihanPeriode->where('status', 'Lunas')->count();
        $totalMenunggu = $allTagihanPeriode->where('status', 'Menunggu Konfirmasi')->count();
        $totalBelum = $allTagihanPeriode->whereIn('status', ['Belum Dibayar', 'Terlambat'])->count();
        $totalDibebaskan = $allTagihanPeriode->where('status', 'Dibebaskan')->count();

        // Nominal Iuran RT Murni (Strict Separation - Exclude Kas RW)
        $nominalTargetRt = $allTagihanPeriode->sum('nominal_rt');
        $nominalTerkumpulRt = $allTagihanPeriode->where('status', 'Lunas')->sum('nominal_rt');
        $nominalBelumRt = $allTagihanPeriode->whereIn('status', ['Belum Dibayar', 'Terlambat'])->sum('nominal_rt');
        $persenKolektibilitas = $totalKkRt > 0 ? round(($totalLunas / $totalKkRt) * 100, 1) : 0;

        // Kontribusi Iuran Sampah Warga RT Setempat (Retribusi Kebersihan Lingkungan)
        $nominalTargetSampahRt = $allTagihanPeriode->sum('nominal_sampah');
        $nominalTerkumpulSampahRt = $allTagihanPeriode->where('status', 'Lunas')->sum('nominal_sampah');

        // 5. Pemasukan & Pengeluaran Bulan Ini (Kas RT Murni)
        $pemasukanBulanIni = TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
            ->whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->sum('nominal');

        $pengeluaranBulanIni = TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
            ->whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->sum('nominal');

        // Recent Mutasi untuk Tab Ringkasan
        $transaksisRingkasan = TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Full Mutasi untuk Tab Buku Kas RT dengan Filter Lengkap
        $bukuKasQuery = TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id);
        if ($request->filled('buku_kas_jenis')) {
            $bukuKasQuery->where('jenis_transaksi', $request->buku_kas_jenis);
        }
        if ($request->filled('buku_kas_start')) {
            $bukuKasQuery->whereDate('tanggal_transaksi', '>=', $request->buku_kas_start);
        }
        if ($request->filled('buku_kas_end')) {
            $bukuKasQuery->whereDate('tanggal_transaksi', '<=', $request->buku_kas_end);
        }
        if ($request->filled('buku_kas_search')) {
            $s = $request->buku_kas_search;
            $bukuKasQuery->where(function($q) use ($s) {
                $q->where('nomor_transaksi', 'like', "%{$s}%")
                  ->orWhere('kategori', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%")
                  ->orWhere('penyetor_penerima', 'like', "%{$s}%");
            });
        }
        $transaksisBukuKas = $bukuKasQuery->orderBy('tanggal_transaksi', 'desc')->orderBy('id', 'desc')->get();

        // 6. Mutasi Kas Sampah (Tab Kas Sampah Terpisah)
        $transaksisKasSampah = collect([]);
        if ($kasSampah) {
            $sampahQuery = TransaksiKeuangan::where('buku_kas_id', $kasSampah->id);
            if ($request->filled('sampah_search')) {
                $s = $request->sampah_search;
                $sampahQuery->where(function($q) use ($s) {
                    $q->where('nomor_transaksi', 'like', "%{$s}%")
                      ->orWhere('kategori', 'like', "%{$s}%")
                      ->orWhere('keterangan', 'like', "%{$s}%");
                });
            }
            $transaksisKasSampah = $sampahQuery->orderBy('tanggal_transaksi', 'desc')->orderBy('id', 'desc')->limit(30)->get();
        }

        // 7. Jurnal Transaksi RT & Catat Transaksi
        $jurnalQuery = TransaksiKeuangan::with(['bukuKas', 'petugas'])
            ->where('buku_kas_id', $bukuKasRt->id);
        if ($request->filled('jurnal_jenis')) {
            $jurnalQuery->where('jenis_transaksi', $request->jurnal_jenis);
        }
        $jurnalTransaksis = $jurnalQuery->orderBy('tanggal_transaksi', 'desc')->orderBy('id', 'desc')->paginate(15, ['*'], 'jurnal_page');

        // Rekening pilihan untuk form Catat Transaksi RT: Kas RT (default) dan Kas Sampah
        $bukuKasPilihan = collect([$bukuKasRt]);
        if ($kasSampah) {
            $bukuKasPilihan->push($kasSampah);
        }

        // Kategori Transaksi RT Standar Warkas Machi
        $kategoriPemasukanRt = [
            'Iuran Kas RT Warga',
            'Sumbangan / Donasi Sukarela Warga',
            'Bantuan Operasional dari RW 013',
            'Hasil Pengelolaan Lingkungan RT',
            'Pemasukan Lain-lain RT'
        ];
        $kategoriPengeluaranRt = [
            'Kerja Bakti & Kebersihan Lingkungan',
            'Pembelian Perlengkapan & ATK RT',
            'Konsumsi Rapat Warga RT',
            'Pemeliharaan Fasilitas & Pos RT',
            'Bantuan Sosial / Takziyah Warga',
            'Operasional Keamanan RT',
            'Pengeluaran Lain-lain RT'
        ];

        // 8. Laporan Keuangan Bulanan RT (Tab Laporan)
        $rekapBulananRt = [];
        for ($m = 1; $m <= 12; $m++) {
            $inMonth = TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
                ->whereYear('tanggal_transaksi', $currentYear)
                ->whereMonth('tanggal_transaksi', $m)
                ->sum('nominal');

            $outMonth = TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
                ->whereYear('tanggal_transaksi', $currentYear)
                ->whereMonth('tanggal_transaksi', $m)
                ->sum('nominal');

            $rekapBulananRt[] = [
                'bulan_angka' => $m,
                'nama_bulan' => Carbon::createFromDate($currentYear, $m, 1)->translatedFormat('F'),
                'pemasukan' => $inMonth,
                'pengeluaran' => $outMonth,
                'selisih' => $inMonth - $outMonth,
            ];
        }

        // Status LPJ / Evaluasi RT ke RW
        $evalRt = AuditEvaluasiRt::where('tahun', $currentYear)
            ->where(function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
            })->first();

        // 9. Titipan Tunai RT ke RW/Unit (Iuran warga bayar tunai ke RT yang menjadi hak RW/Sampah/Kegiatan)
        $titipanPending = \App\Models\PembayaranAlokasi::where('status_alokasi', 'titipan_rt')
            ->whereHas('tagihanKeluarga', function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
            });
        $titipanPendingNominal = (clone $titipanPending)->sum('nominal_alokasi');
        $titipanPendingCount = (clone $titipanPending)->count();

        // Audit Logs untuk RT ini
        $auditLogsRt = KeuanganAuditLog::where(function($q) use ($bukuKasRt, $user) {
                $q->where('user_id', $user->id)
                  ->orWhere('deskripsi', 'like', "%RT {$bukuKasRt->clean_rt}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        $daftarPeriode = TagihanKeluarga::where(function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
            })->select('periode')->distinct()->orderBy('periode', 'desc')->pluck('periode');

        return view('keuangan.dashboard_rt', compact(
            'bukuKasRt',
            'kasSampah',
            'kasKeamanan',
            'totalSaldoOtonomRt',
            'totalSaldoKeseluruhanRt',
            'nominalTargetSampahRt',
            'nominalTerkumpulSampahRt',
            'rtStr',
            'activeTab',
            'currentPeriode',
            'currentMonth',
            'currentYear',
            'pemasukanBulanIni',
            'pengeluaranBulanIni',
            'tagihansRt',
            'totalKkRt',
            'totalLunas',
            'totalMenunggu',
            'totalBelum',
            'totalDibebaskan',
            'nominalTargetRt',
            'nominalTerkumpulRt',
            'nominalBelumRt',
            'persenKolektibilitas',
            'transaksisRingkasan',
            'transaksisBukuKas',
            'transaksisKasSampah',
            'jurnalTransaksis',
            'bukuKasPilihan',
            'kategoriPemasukanRt',
            'kategoriPengeluaranRt',
            'rekapBulananRt',
            'evalRt',
            'auditLogsRt',
            'titipanPendingNominal',
            'titipanPendingCount',
            'daftarPeriode'
        ));
    }

    /**
     * Kirim Laporan Keuangan RT ke Ketua RW
     */
    public function kirimLaporanRt(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'rt' && !in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Pengurus RT.');
        }

        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);
        $tahun = Carbon::now()->year;

        $eval = AuditEvaluasiRt::firstOrCreate(
            [
                'rt_number' => $rtStr,
                'tahun' => $tahun,
            ],
            [
                'periode' => 'Tahunan',
                'skor_kepatuhan' => 88,
                'status_pertanggungjawaban' => 'baik',
                'catatan_evaluasi' => 'Pencatatan kas dan administrasi berjalan tertib.',
            ]
        );

        $eval->status_laporan = 'Terkirim';
        $eval->status_verifikasi_rw = 'Menunggu Pemeriksaan';
        $eval->laporan_dikunci = true;
        $eval->save();

        KeuanganAuditLog::create([
            'user_id' => $user->id,
            'action' => 'kirim_laporan_rt',
            'deskripsi' => "Ketua RT {$rtStr} telah mengirimkan Laporan Keuangan RT kepada Ketua RW untuk ditinjau dan diverifikasi.",
            'new_values' => ['status_laporan' => 'Terkirim', 'status_verifikasi_rw' => 'Menunggu Pemeriksaan'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Laporan Keuangan RT {$rtStr} berhasil dikirimkan kepada Ketua RW!");
    }

    /**
     * Helper: Cari data Kartu Keluarga milik user warga yang aktif
     */
    private function getWargaKartuKeluarga($user)
    {
        $kartuKeluarga = null;
        if ($user->kartu_keluarga_id) {
            $kartuKeluarga = KartuKeluarga::find($user->kartu_keluarga_id);
        }
        if (!$kartuKeluarga && $user->no_kk) {
            $kartuKeluarga = KartuKeluarga::where('no_kk', $user->no_kk)->first();
        }
        if (!$kartuKeluarga && $user->nik) {
            $kartuKeluarga = KartuKeluarga::whereHas('anggota', function($q) use ($user) {
                $q->where('nik', $user->nik);
            })->first();
        }
        return $kartuKeluarga;
    }

    /**
     * Dashboard Keuangan Warga Terpadu (Single Page dengan Internal Tabs: Tagihan, Bayar, Riwayat)
     */
    public function dashboardWarga(Request $request)
    {
        $user = Auth::user();
        $kartuKeluarga = $this->getWargaKartuKeluarga($user);
        
        $activeTab = $request->query('tab', 'tagihan');
        if (!in_array($activeTab, ['tagihan', 'bayar', 'riwayat'])) {
            $activeTab = 'tagihan';
        }

        if (!$kartuKeluarga) {
            return view('keuangan.warga.index', [
                'kartuKeluarga' => null,
                'activeTab' => $activeTab,
                'tagihanBulanIni' => null,
                'totalIuranTerkumpul' => 0,
                'unpaidTagihans' => collect([]),
                'selectedTagihan' => null,
                'pendingTagihans' => collect([]),
                'pembayarans' => collect([]),
                'tagihans' => collect([]),
                'totalLunas' => 0,
                'totalTertunggak' => 0,
                'kasRw' => BukuKas::where('jenis_kas', 'kas_rw')->first(),
                'kasRt' => null,
            ]);
        }

        // --- Data Tab 1: Tagihan Aktif ---
        $currentPeriode = Carbon::now()->format('Y-m');
        $tagihanBulanIni = TagihanKeluarga::with(['items.bukuKas', 'pembayarans.alokasis'])
            ->where('kartu_keluarga_id', $kartuKeluarga->id)
            ->where('periode', $currentPeriode)
            ->first();

        if (!$tagihanBulanIni) {
            $tagihanBulanIni = TagihanKeluarga::with(['items.bukuKas', 'pembayarans.alokasis'])
                ->where('kartu_keluarga_id', $kartuKeluarga->id)
                ->whereIn('status', ['Belum Dibayar', 'Terlambat', 'Dibayar Sebagian', 'Menunggu Konfirmasi'])
                ->orderBy('periode', 'desc')
                ->first();
        }
        if (!$tagihanBulanIni) {
            $tagihanBulanIni = TagihanKeluarga::with(['items.bukuKas', 'pembayarans.alokasis'])
                ->where('kartu_keluarga_id', $kartuKeluarga->id)
                ->orderBy('periode', 'desc')
                ->first();
        }

        $totalIuranTerkumpul = TagihanKeluarga::where('kartu_keluarga_id', $kartuKeluarga->id)
            ->where('status', 'Lunas')
            ->sum('total_dibayar');

        // --- Data Tab 2: Bayar & Konfirmasi ---
        $unpaidTagihans = TagihanKeluarga::with(['items.bukuKas'])
            ->where('kartu_keluarga_id', $kartuKeluarga->id)
            ->whereIn('status', ['Belum Dibayar', 'Terlambat', 'Dibayar Sebagian'])
            ->orderBy('periode', 'desc')
            ->get();

        $selectedTagihanId = $request->query('tagihan_id');
        $selectedTagihan = null;
        if ($selectedTagihanId) {
            $selectedTagihan = $unpaidTagihans->firstWhere('id', $selectedTagihanId);
        }
        if (!$selectedTagihan && $unpaidTagihans->count() > 0) {
            $selectedTagihan = $unpaidTagihans->first();
        }

        $pendingTagihans = TagihanKeluarga::with(['items.bukuKas'])
            ->where('kartu_keluarga_id', $kartuKeluarga->id)
            ->where('status', 'Menunggu Konfirmasi')
            ->orderBy('updated_at', 'desc')
            ->get();

        $kasRw = BukuKas::where('jenis_kas', 'kas_rw')->first();
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $kartuKeluarga->rt_number ?: $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);
        $kasRt = BukuKas::where('jenis_kas', 'kas_rt')
            ->where(function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
            })->first();

        // --- Data Tab 3: Riwayat & Kuitansi ---
        $pembayarans = PembayaranIuran::with(['tagihanKeluarga', 'penerima', 'alokasis.bukuKas'])
            ->where('kartu_keluarga_id', $kartuKeluarga->id)
            ->orderBy('tanggal_bayar', 'desc')
            ->paginate(15)
            ->withQueryString();

        $tagihans = TagihanKeluarga::with(['items.bukuKas', 'pembayarans.penerima', 'diverifikasiOleh'])
            ->where('kartu_keluarga_id', $kartuKeluarga->id)
            ->orderBy('periode', 'desc')
            ->get();

        $totalLunas = $tagihans->where('status', 'Lunas')->sum('total_dibayar');
        $totalTertunggak = $tagihans->whereIn('status', ['Belum Dibayar', 'Terlambat', 'Dibayar Sebagian'])->sum('sisa_tagihan');

        return view('keuangan.warga.index', compact(
            'kartuKeluarga',
            'activeTab',
            'tagihanBulanIni',
            'totalIuranTerkumpul',
            'unpaidTagihans',
            'selectedTagihan',
            'pendingTagihans',
            'pembayarans',
            'tagihans',
            'totalLunas',
            'totalTertunggak',
            'kasRw',
            'kasRt'
        ));
    }

    /**
     * Backward compatibility aliases -> redirect ke tab yang sesuai
     */
    public function wargaTagihan(Request $request)
    {
        return redirect()->route('keuangan.dashboard', array_merge(['tab' => 'tagihan'], $request->query()));
    }

    public function wargaBayar(Request $request)
    {
        return redirect()->route('keuangan.dashboard', array_merge(['tab' => 'bayar'], $request->query()));
    }

    public function wargaRiwayat(Request $request)
    {
        return redirect()->route('keuangan.dashboard', array_merge(['tab' => 'riwayat'], $request->query()));
    }

    public function wargaKuitansi(Request $request)
    {
        return redirect()->route('keuangan.dashboard', array_merge(['tab' => 'riwayat'], $request->query()));
    }

    /**
     * Daftar Buku Kas Terpadu
     */
    public function bukuKasIndex(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'rt') {
            $tab = ($request->get('jenis') === 'kas_sampah') ? 'kas_sampah' : 'buku_kas';
            return redirect()->route('keuangan.dashboard', array_merge(['tab' => $tab], $request->query()));
        }

        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        // Base query seluruh rekening yang dapat diakses pengguna
        $baseQuery = BukuKas::query();

        if (!$isRw) {
            // Role RT hanya bisa melihat Kas RT mereka dan Kas Sampah
            $baseQuery->where(function($q) use ($rtStr) {
                $q->where(function($sub) use ($rtStr) {
                    $sub->where('jenis_kas', 'kas_rt')
                        ->where(function($rtq) use ($rtStr) {
                            $rtq->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
                        });
                })->orWhere('jenis_kas', 'kas_sampah');
            });
        }

        $allBukuKas = $baseQuery->orderBy('jenis_kas', 'asc')->orderBy('kode_kas', 'asc')->get();
        $visibleIds = $allBukuKas->pluck('id')->toArray();

        // Rekap Jumlah per Kategori Kas untuk tab navigasi
        $kategoriCounts = [
            'all' => $allBukuKas->count(),
            'kas_rw' => $allBukuKas->where('jenis_kas', 'kas_rw')->count(),
            'kas_rt' => $allBukuKas->where('jenis_kas', 'kas_rt')->count(),
            'kas_sampah' => $allBukuKas->where('jenis_kas', 'kas_sampah')->count(),
            'dana_kegiatan' => $allBukuKas->where('jenis_kas', 'dana_kegiatan')->count(),
        ];

        // Filter Periode untuk Statistik Pemasukan & Pengeluaran
        $periode = $request->get('periode', 'bulan_ini');
        $now = Carbon::now();
        $startDate = null;
        $endDate = null;
        $periodeLabel = '';

        if ($periode === 'bulan_lalu') {
            $startDate = $now->copy()->subMonth()->startOfMonth()->toDateString();
            $endDate = $now->copy()->subMonth()->endOfMonth()->toDateString();
            $periodeLabel = $now->copy()->subMonth()->translatedFormat('F Y');
        } elseif ($periode === 'tahun_ini') {
            $startDate = $now->copy()->startOfYear()->toDateString();
            $endDate = $now->copy()->endOfYear()->toDateString();
            $periodeLabel = 'Tahun ' . $now->year;
        } elseif ($periode === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $periodeLabel = Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
        } else {
            // Default: bulan_ini
            $periode = 'bulan_ini';
            $startDate = $now->copy()->startOfMonth()->toDateString();
            $endDate = $now->copy()->endOfMonth()->toDateString();
            $periodeLabel = $now->translatedFormat('F Y');
        }

        // 4 Kartu Statistik Keuangan Utama (Data Riil Database)
        // 1. Total Saldo Seluruh Kas & Pemisahan Kepemilikan Dana
        $totalSaldo = (float) $allBukuKas->sum('saldo_sekarang');
        $saldoKasRw = (float) ($allBukuKas->where('jenis_kas', 'kas_rw')->first()->saldo_sekarang ?? 0);
        $totalSaldoRt = (float) $allBukuKas->where('jenis_kas', 'kas_rt')->sum('saldo_sekarang');
        $totalSaldoUnit = (float) $allBukuKas->where('pemilik_dana', 'unit')->sum('saldo_sekarang');
        $totalDanaTerpantau = $totalSaldo;

        // 2. Total Pemasukan dalam periode terpilih
        $totalPemasukan = (float) TransaksiKeuangan::whereIn('buku_kas_id', $visibleIds)
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('nominal');

        // 3. Total Pengeluaran dalam periode terpilih
        $totalPengeluaran = (float) TransaksiKeuangan::whereIn('buku_kas_id', $visibleIds)
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('nominal');

        // 4. Jumlah Rekening Aktif
        $totalRekeningAktif = $allBukuKas->where('is_active', true)->count();
        $totalRekening = $allBukuKas->count();

        // Rekening yang ditampilkan berdasarkan filter kategori
        $selectedJenis = $request->get('jenis', 'all');
        $bukuKas = $allBukuKas;
        if ($selectedJenis && $selectedJenis !== 'all') {
            $bukuKas = $allBukuKas->where('jenis_kas', $selectedJenis);
        }

        return view('keuangan.buku_kas.index', compact(
            'bukuKas',
            'allBukuKas',
            'isRw',
            'rtStr',
            'user',
            'saldoKasRw',
            'totalSaldoRt',
            'totalSaldoUnit',
            'totalDanaTerpantau',
            'saldoKonsolidasi',
            'totalSaldo',
            'totalPemasukan',
            'totalPengeluaran',
            'totalRekeningAktif',
            'totalRekening',
            'kategoriCounts',
            'periode',
            'periodeLabel',
            'startDate',
            'endDate',
            'selectedJenis'
        ));
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

        // Hak kelola rekening transaksi aktif (Pemisahan kewenangan operasional vs monitoring)
        $canManageThisKas = false;
        if ($isRw && in_array($bukuKas->jenis_kas, ['kas_rw', 'kas_sampah', 'dana_kegiatan'])) {
            $canManageThisKas = true;
        } elseif (!$isRw && $bukuKas->jenis_kas === 'kas_rt' && ($bukuKas->clean_rt == $rtStr || $bukuKas->rt_number == ltrim($rtStr, '0'))) {
            $canManageThisKas = true;
        }

        $kategoriList = KategoriTransaksi::where('jenis_kas', $bukuKas->jenis_kas)->get();
        $targetBukuTransfer = BukuKas::where('id', '!=', $bukuKas->id)->where('is_active', true)->get();

        return view('keuangan.buku_kas.show', compact('bukuKas', 'transaksis', 'totalMasuk', 'totalKeluar', 'kategoriList', 'targetBukuTransfer', 'isRw', 'canManageThisKas'));
    }

    /**
     * Jurnal Transaksi Keuangan Seluruhnya
     */
    public function transaksiIndex(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'rt') {
            return redirect()->route('keuangan.dashboard', array_merge(['tab' => 'transaksi'], $request->query()));
        }

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
        if ($user->role === 'rt') {
            return redirect()->route('keuangan.dashboard', array_merge(['tab' => 'transaksi', 'modal' => 'catat'], $request->query()));
        }

        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        if ($isRw) {
            // Pengurus RW mengelola Kas Induk RW, Kas Sampah, dan Dana Kegiatan (Kas RT murni dikelola masing-masing RT)
            $bukuKasList = BukuKas::where('is_active', true)->whereIn('jenis_kas', ['kas_rw', 'kas_sampah', 'dana_kegiatan'])->get();
        } else {
            $bukuKasList = BukuKas::where('is_active', true)
                ->where(function($q) use ($rtStr) {
                    $q->where(function($sub) use ($rtStr) {
                        $sub->where('jenis_kas', 'kas_rt')
                            ->where(function($rtq) use ($rtStr) {
                                $rtq->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
                            });
                    })->orWhere('jenis_kas', 'kas_sampah');
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
            'referensi_pembayaran' => 'nullable|string|max:100',
            'penyetor_penerima' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string',
            'bukti_transaksi' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
            'target_buku_kas_id' => 'nullable|required_if:jenis_transaksi,transfer_keluar|exists:buku_kas,id',
        ]);

        $bukuKas = BukuKas::findOrFail($request->buku_kas_id);

        // Keamanan Hak Akses Keuangan Berjenjang
        if ($isRw && $bukuKas->jenis_kas === 'kas_rt') {
            abort(403, 'Pengurus RW hanya memiliki hak monitoring pada Kas RT dan tidak berwenang mencatat transaksi pada kas operasional RT.');
        }

        if (!$isRw && $bukuKas->jenis_kas === 'kas_rt') {
            if ($bukuKas->clean_rt != $rtStr && $bukuKas->rt_number != ltrim($rtStr, '0')) {
                abort(403, 'Anda tidak berwenang mencatat transaksi pada kas RT lain.');
            }
        }

        if (!$isRw && in_array($bukuKas->jenis_kas, ['kas_rw', 'dana_kegiatan'])) {
            abort(403, 'Akses pencatatan kas ini khusus Pengurus RW 013.');
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
                    'pemilik_dana' => $bukuKas->pemilik_dana,
                    'tanggal_transaksi' => $request->tanggal_transaksi,
                    'jenis_transaksi' => 'transfer_keluar',
                    'kategori' => 'Transfer Antar Kas (Keluar)',
                    'nominal' => $request->nominal,
                    'saldo_sebelumnya' => $bukuKas->saldo_sekarang,
                    'saldo_setelahnya' => $bukuKas->saldo_sekarang - $request->nominal,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'referensi_pembayaran' => $transferRef,
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
                    'pemilik_dana' => $targetBuku->pemilik_dana,
                    'tanggal_transaksi' => $request->tanggal_transaksi,
                    'jenis_transaksi' => 'transfer_masuk',
                    'kategori' => 'Transfer Antar Kas (Masuk)',
                    'nominal' => $request->nominal,
                    'saldo_sebelumnya' => $targetBuku->saldo_sekarang,
                    'saldo_setelahnya' => $targetBuku->saldo_sekarang + $request->nominal,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'referensi_pembayaran' => $transferRef,
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

                ActivityLog::record(
                    'keuangan',
                    'keuangan.transfer_created',
                    "Petugas {$user->name} mencatat transfer kas Rp " . number_format($request->nominal, 0, ',', '.') . " dari [{$bukuKas->nama_kas}] ke [{$targetBuku->nama_kas}].",
                    [
                        'sumber_kode' => $bukuKas->kode_kas,
                        'tujuan_kode' => $targetBuku->kode_kas,
                        'nominal' => (float)$request->nominal,
                    ],
                    $user->id
                );

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
                'pemilik_dana' => $bukuKas->pemilik_dana,
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'jenis_transaksi' => $request->jenis_transaksi,
                'kategori' => $request->kategori,
                'nominal' => $request->nominal,
                'saldo_sebelumnya' => $saldoSebelumnya,
                'saldo_setelahnya' => $saldoSetelahnya,
                'metode_pembayaran' => $request->metode_pembayaran,
                'referensi_pembayaran' => $request->referensi_pembayaran,
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

            ActivityLog::record(
                'keuangan',
                'keuangan.transaksi_created',
                "Petugas {$user->name} mencatat {$request->jenis_transaksi} Rp " . number_format($request->nominal, 0, ',', '.') . " pada [{$bukuKas->nama_kas}].",
                [
                    'transaksi_id' => $transaksi->id,
                    'nomor_transaksi' => $transaksi->nomor_transaksi,
                    'buku_kas_id' => $bukuKas->id,
                    'kode_kas' => $bukuKas->kode_kas,
                    'nominal' => (float)$request->nominal,
                    'jenis_transaksi' => $request->jenis_transaksi,
                ],
                $user->id
            );

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
        if ($user->role === 'rt') {
            return redirect()->route('keuangan.dashboard', array_merge(['tab' => 'laporan'], $request->query()));
        }

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

    /**
     * MONITORING KEUANGAN RW 013 (Sentral, Read-Only, Komprehensif)
     * Rute: /rw/monitoring-keuangan (rw.keuangan.monitoring)
     */
    public function monitoringRw(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas hanya untuk Ketua RW / Administrator RW 013.');
        }

        $selectedTahun = (int) $request->input('tahun', Carbon::now()->year);
        $selectedBulan = (int) $request->input('bulan', Carbon::now()->month);
        $periodeStr = sprintf('%04d-%02d', $selectedTahun, $selectedBulan);

        // --- 1. RINGKASAN KEUANGAN ---
        $kasRw = BukuKas::where('jenis_kas', 'kas_rw')->first();
        $kasSampah = BukuKas::where('jenis_kas', 'kas_sampah')->first();
        $kasRtList = BukuKas::where('jenis_kas', 'kas_rt')->orderBy('rt_number', 'asc')->get();
        $danaKegiatanList = BukuKas::where('jenis_kas', 'dana_kegiatan')->where('is_active', true)->get();

        $saldoKasRw = $kasRw->saldo_sekarang ?? 0;
        $saldoKasSampah = $kasSampah->saldo_sekarang ?? 0;
        $saldoKasRtTotal = $kasRtList->sum('saldo_sekarang');
        $saldoDanaKegiatanTotal = $danaKegiatanList->sum('saldo_sekarang');
        $totalSaldoSeluruhKas = $saldoKasRw + $saldoKasSampah + $saldoKasRtTotal + $saldoDanaKegiatanTotal;

        // Arus Kas Bulan Terpilih (Seluruh Kas)
        $pemasukanBulanIni = TransaksiKeuangan::where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
            ->whereYear('tanggal_transaksi', $selectedTahun)
            ->whereMonth('tanggal_transaksi', $selectedBulan)
            ->sum('nominal');

        $pengeluaranBulanIni = TransaksiKeuangan::where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
            ->whereYear('tanggal_transaksi', $selectedTahun)
            ->whereMonth('tanggal_transaksi', $selectedBulan)
            ->sum('nominal');

        $jumlahTransaksiBulanIni = TransaksiKeuangan::where('status', 'selesai')
            ->whereYear('tanggal_transaksi', $selectedTahun)
            ->whereMonth('tanggal_transaksi', $selectedBulan)
            ->count();

        // --- 2. MONITORING KAS RT (RT 001 - RT 008) ---
        $monitoringKasRt = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT);
            $bukuKasRt = $kasRtList->first(function($item) use ($i, $rtStr) {
                return $item->rt_number == $i || $item->rt_number == $rtStr;
            });

            $picRt = User::where('role', 'rt')->where(function($q) use ($i, $rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$i)->orWhere('rt_number', $i);
            })->first();

            $saldoRt = $bukuKasRt->saldo_sekarang ?? 0;

            $masukRt = $bukuKasRt ? TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
                ->whereYear('tanggal_transaksi', $selectedTahun)
                ->whereMonth('tanggal_transaksi', $selectedBulan)
                ->sum('nominal') : 0;

            $keluarRt = $bukuKasRt ? TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
                ->whereYear('tanggal_transaksi', $selectedTahun)
                ->whereMonth('tanggal_transaksi', $selectedBulan)
                ->sum('nominal') : 0;

            $txTerakhir = $bukuKasRt ? TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
                ->orderBy('tanggal_transaksi', 'desc')
                ->orderBy('id', 'desc')
                ->first() : null;

            // Kondisi keuangan: Aman (> 2jt), Waspada (500rb - 2jt), Perlu Perhatian (< 500rb)
            if ($saldoRt >= 2000000) {
                $kondisi = ['label' => 'Aman & Sehat', 'badge' => 'bg-success-subtle text-success border-success-subtle', 'color' => '#10B981'];
            } elseif ($saldoRt >= 500000) {
                $kondisi = ['label' => 'Cukup / Waspada', 'badge' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle', 'color' => '#F59E0B'];
            } else {
                $kondisi = ['label' => 'Perlu Perhatian / Minim', 'badge' => 'bg-danger-subtle text-danger border-danger-subtle', 'color' => '#EF4444'];
            }

            // Kepatuhan pencatatan: Cek apakah ada pencatatan bulan ini
            $countBulanIni = $bukuKasRt ? TransaksiKeuangan::where('buku_kas_id', $bukuKasRt->id)
                ->whereYear('tanggal_transaksi', $selectedTahun)
                ->whereMonth('tanggal_transaksi', $selectedBulan)
                ->count() : 0;

            $kepatuhan = $countBulanIni >= 3 ? 'Sangat Tertib' : ($countBulanIni >= 1 ? 'Cukup Tertib' : 'Belum Ada Entri');

            $monitoringKasRt[] = [
                'rt_num' => $i,
                'rt_name' => 'RT ' . $rtStr,
                'buku_kas' => $bukuKasRt,
                'pic_name' => $picRt->name ?? 'Ketua RT ' . $rtStr,
                'pic_phone' => $picRt->phone ?? '-',
                'saldo' => $saldoRt,
                'pemasukan' => $masukRt,
                'pengeluaran' => $keluarRt,
                'tx_terakhir' => $txTerakhir,
                'kondisi' => $kondisi,
                'kepatuhan' => $kepatuhan,
                'count_tx' => $countBulanIni,
            ];
        }

        // --- 3. MONITORING IURAN WARGA ---
        $tagihans = TagihanKeluarga::where('periode', $periodeStr)->get();
        $totalTagihanIuran = $tagihans->sum('total_tagihan');
        $totalIuranTerbayar = $tagihans->sum('total_dibayar');
        $totalIuranTunggakan = $tagihans->whereIn('status', ['Belum Dibayar', 'Terlambat', 'Dibayar Sebagian'])->sum('sisa_tagihan');
        $totalKkTerdaftar = KartuKeluarga::count();
        $totalKkLunas = $tagihans->where('status', 'Lunas')->count();
        $persenIuran = $totalTagihanIuran > 0 ? round(($totalIuranTerbayar / $totalTagihanIuran) * 100, 1) : 0;

        $rekapIuranRt = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT);
            $kkRt = KartuKeluarga::where('rt', $rtStr)->orWhere('rt', (string)$i)->count();
            $tagihanPerRt = $tagihans->filter(function($t) use ($rtStr, $i) {
                return $t->rt_number == $rtStr || $t->rt_number == $i;
            });
            $targetRt = $tagihanPerRt->sum('total_tagihan');
            $bayarRt = $tagihanPerRt->sum('total_dibayar');
            $sisaRt = $tagihanPerRt->sum('sisa_tagihan');
            $lunasCount = $tagihanPerRt->where('status', 'Lunas')->count();
            $persenBayarRt = $targetRt > 0 ? round(($bayarRt / $targetRt) * 100, 1) : 0;

            $rekapIuranRt[] = [
                'rt_name' => 'RT ' . $rtStr,
                'total_kk' => $kkRt,
                'lunas_kk' => $lunasCount,
                'target' => $targetRt,
                'terbayar' => $bayarRt,
                'tunggakan' => $sisaRt,
                'persentase' => $persenBayarRt,
            ];
        }

        // --- 4. MONITORING BANK SAMPAH ---
        $bankSampahUnitList = \App\Models\BankSampah::orderBy('rt_number', 'asc')->get();
        $totalKgBankSampahBulanIni = \App\Models\BankSampahTransaksi::where('status', 'selesai')
            ->whereYear('tanggal_pengajuan', $selectedTahun)
            ->whereMonth('tanggal_pengajuan', $selectedBulan)
            ->sum('total_berat_kg');
        if ($totalKgBankSampahBulanIni == 0) {
            $totalKgBankSampahBulanIni = \App\Models\BankSampah::sum('total_sampah_terkumpul_kg');
        }

        $nilaiTransaksiSampahBulanIni = \App\Models\BankSampahTransaksi::where('status', 'selesai')
            ->whereYear('tanggal_pengajuan', $selectedTahun)
            ->whereMonth('tanggal_pengajuan', $selectedBulan)
            ->sum('total_rupiah');

        $rekapBankSampahRt = [];
        foreach ($bankSampahUnitList as $bsUnit) {
            $rekapBankSampahRt[] = [
                'nama_unit' => $bsUnit->nama_unit,
                'rt_name' => 'RT ' . str_pad($bsUnit->rt_number, 3, '0', STR_PAD_LEFT),
                'petugas' => $bsUnit->petugas_name,
                'total_kg' => $bsUnit->total_sampah_terkumpul_kg,
                'lokasi' => $bsUnit->lokasi_drop_point,
            ];
        }

        $setoranSampahTerbaru = \App\Models\BankSampahTransaksi::with(['user', 'bankSampah'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // --- 5. MONITORING DANA KEGIATAN ---
        $monitoringDanaKegiatan = [];
        foreach ($danaKegiatanList as $dk) {
            $realisasi = TransaksiKeuangan::where('buku_kas_id', $dk->id)
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
                ->sum('nominal');

            $anggaranRencana = $dk->target_saldo > 0 ? $dk->target_saldo : ($dk->saldo_sekarang + $realisasi);
            if ($anggaranRencana <= 0) $anggaranRencana = $realisasi + $dk->saldo_sekarang;
            if ($anggaranRencana <= 0) $anggaranRencana = 5000000;

            $sisaAnggaran = $dk->saldo_sekarang;
            $persenSerap = $anggaranRencana > 0 ? round(($realisasi / $anggaranRencana) * 100, 1) : 0;
            $picKegiatan = $dk->pengelola->name ?? 'Pengurus RW 013';
            $statusKegiatan = $dk->status_badge['label'] ?? ($dk->is_active ? 'Sedang Berjalan' : 'Selesai');

            $monitoringDanaKegiatan[] = [
                'id' => $dk->id,
                'nama_pos' => $dk->nama_kas,
                'nomor_rekening' => $dk->nomor_rekening,
                'anggaran_awal' => $anggaranRencana,
                'realisasi' => $realisasi,
                'sisa_anggaran' => $sisaAnggaran,
                'persen_serap' => $persenSerap,
                'status' => $statusKegiatan,
                'pic' => $picKegiatan,
            ];
        }

        // --- 6. AKTIVITAS KEUANGAN TERBARU (FILTERABLE, READ-ONLY) ---
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
        if ($request->filled('jenis_kas_id')) {
            $trxQuery->where('buku_kas_id', $request->jenis_kas_id);
        }
        if ($request->filled('jenis_transaksi')) {
            $trxQuery->where('jenis_transaksi', $request->jenis_transaksi);
        }
        if ($request->filled('rt_filter')) {
            $rtFilt = $request->rt_filter;
            $kasRtIds = BukuKas::where('rt_number', $rtFilt)->pluck('id');
            $trxQuery->whereIn('buku_kas_id', $kasRtIds);
        }

        if ($request->filled('filter_periode') && $request->filter_periode !== 'semua') {
            $pDate = Carbon::parse($request->filter_periode . '-01');
            $trxQuery->whereYear('tanggal_transaksi', $pDate->year)
                     ->whereMonth('tanggal_transaksi', $pDate->month);
        }

        $recentTransactions = $trxQuery->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $allBukuKas = BukuKas::where('is_active', true)->orderBy('nama_kas', 'asc')->get();

        // Sistem Navigasi Tab Horizontal Monitoring Keuangan RW
        $activeTab = $request->input('tab', 'kas_rt');
        if (!in_array($activeTab, ['kas_rt', 'bank_sampah', 'dana_kegiatan', 'aktivitas'])) {
            $activeTab = 'kas_rt';
        }

        // Indikator perhatian Ketua RW untuk badge tab
        $rtPerluPerhatianCount = collect($monitoringKasRt)->filter(function($item) {
            return ($item['saldo'] < 500000) || ($item['count_tx'] == 0);
        })->count();

        $kegiatanPerluAtensiCount = collect($monitoringDanaKegiatan)->filter(function($item) {
            return $item['persen_serap'] >= 90 || $item['sisa_anggaran'] <= 500000;
        })->count();

        return view('keuangan.monitoring_rw', compact(
            'selectedTahun',
            'selectedBulan',
            'periodeStr',
            'kasRw',
            'kasSampah',
            'saldoKasRw',
            'saldoKasSampah',
            'saldoKasRtTotal',
            'saldoDanaKegiatanTotal',
            'totalSaldoSeluruhKas',
            'pemasukanBulanIni',
            'pengeluaranBulanIni',
            'jumlahTransaksiBulanIni',
            'monitoringKasRt',
            'totalTagihanIuran',
            'totalIuranTerbayar',
            'totalIuranTunggakan',
            'totalKkTerdaftar',
            'totalKkLunas',
            'persenIuran',
            'rekapIuranRt',
            'totalKgBankSampahBulanIni',
            'nilaiTransaksiSampahBulanIni',
            'rekapBankSampahRt',
            'setoranSampahTerbaru',
            'monitoringDanaKegiatan',
            'recentTransactions',
            'allBukuKas',
            'activeTab',
            'rtPerluPerhatianCount',
            'kegiatanPerluAtensiCount'
        ));
    }
}
