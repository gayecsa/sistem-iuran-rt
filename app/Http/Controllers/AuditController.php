<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditKegiatan;
use App\Models\AuditTemuan;
use App\Models\AuditEvaluasiRt;
use App\Models\BukuKas;
use App\Models\TransaksiKeuangan;
use App\Models\KeuanganAuditLog;
use App\Models\TagihanKeluarga;
use App\Models\IuranTarif;
use App\Models\KartuKeluarga;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard Utama Audit & Evaluasi RW 013
     * Rute: /rw/audit (rw.audit.index)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas hanya untuk Pengurus RW 013 & Tim Auditor Resmi.');
        }

        $tahun = (int) $request->input('tahun', Carbon::now()->year);
        $filterRt = $request->input('rt', 'all');
        $filterKategori = $request->input('kategori', 'all');
        $filterStatus = $request->input('status', 'all');
        
        $tabInput = $request->input('tab', 'ringkasan');
        if ($tabInput === 'overview') {
            $activeTab = 'ringkasan';
        } else {
            $activeTab = in_array($tabInput, ['ringkasan', 'kas_rw', 'rekap_rt', 'monitoring_iuran', 'laporan', 'riwayat', 'temuan', 'evaluasi', 'kegiatan'])
                ? $tabInput 
                : 'ringkasan';
        }

        // ==========================================
        // 1. DATA INTI KAS RW (Strictly RW's Funds)
        // ==========================================
        $kasRw = BukuKas::where('jenis_kas', 'kas_rw')->first() ?: BukuKas::first();
        $saldoKasRw = $kasRw ? (float)$kasRw->saldo_sekarang : 0;

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $pemasukanKasRwBulanIni = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
            ->whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->sum('nominal');

        $pengeluaranKasRwBulanIni = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
            ->whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->sum('nominal');

        $iuranRwTerkumpul = TagihanKeluarga::where('status', 'Lunas')->sum('nominal_rw');
        $iuranRwBelumBayar = TagihanKeluarga::whereIn('status', ['Belum Dibayar', 'Terlambat', 'Dibayar Sebagian'])->sum('nominal_rw');

        $rtSudahLapor = AuditEvaluasiRt::where('tahun', $tahun)->where('status_laporan', 'Terkirim')->count();
        $rtBelumLapor = max(0, 8 - $rtSudahLapor);

        // SEPARATE MONITORING ONLY: Total Kas 8 RT (Bukan milik RW)
        $totalKas8Rt = BukuKas::where('jenis_kas', 'kas_rt')->sum('saldo_sekarang');

        // ==========================================
        // 2. GRAFIK KEUANGAN (Chart.js)
        // ==========================================
        // A. Pemasukan & Pengeluaran 6 Bulan Terakhir Kas RW
        $chartBulanLabels = [];
        $chartKasRwMasuk = [];
        $chartKasRwKeluar = [];
        $chartKasRwSaldo = [];
        $runningBalance = $saldoKasRw;

        for ($m = 5; $m >= 0; $m--) {
            $d = Carbon::now()->subMonths($m);
            $chartBulanLabels[] = $d->translatedFormat('M Y');

            $in = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
                ->whereYear('tanggal_transaksi', $d->year)
                ->whereMonth('tanggal_transaksi', $d->month)
                ->sum('nominal');

            $out = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
                ->whereYear('tanggal_transaksi', $d->year)
                ->whereMonth('tanggal_transaksi', $d->month)
                ->sum('nominal');

            $chartKasRwMasuk[] = (float)$in;
            $chartKasRwKeluar[] = (float)$out;
            $chartKasRwSaldo[] = max(0, $saldoKasRw - ($m * 500000) + ($in - $out));
        }

        // B. Persentase Pembayaran Iuran
        $countIuranLunas = TagihanKeluarga::where('status', 'Lunas')->count();
        $countIuranBelum = TagihanKeluarga::whereIn('status', ['Belum Dibayar', 'Terlambat'])->count();
        $countIuranMenunggu = TagihanKeluarga::where('status', 'Menunggu Konfirmasi')->count();

        // C. Perbandingan Keuangan RT 001 - RT 008
        $rtChartLabels = [];
        $rtChartSaldo = [];
        $rtChartInflow = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT);
            $rtChartLabels[] = 'RT ' . $rtStr;
            $b = BukuKas::where('jenis_kas', 'kas_rt')->where(function($q) use ($rtStr, $i) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$i);
            })->first();
            $rtChartSaldo[] = $b ? (float)$b->saldo_sekarang : 0;

            $inRt = $b ? TransaksiKeuangan::where('buku_kas_id', $b->id)->where('status', 'selesai')->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])->whereYear('tanggal_transaksi', $tahun)->sum('nominal') : 0;
            $rtChartInflow[] = (float)$inRt;
        }

        // ==========================================
        // 3. TAB KAS RW (Hanya Transaksi Kas RW)
        // ==========================================
        $kasRwQuery = TransaksiKeuangan::with(['petugas', 'editedBy'])
            ->where('buku_kas_id', $kasRw->id);

        if ($request->filled('search_kas_rw')) {
            $s = $request->search_kas_rw;
            $kasRwQuery->where(function($q) use ($s) {
                $q->where('nomor_transaksi', 'like', "%{$s}%")
                  ->orWhere('penyetor_penerima', 'like', "%{$s}%")
                  ->orWhere('kategori', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%")
                  ->orWhere('sumber_dana', 'like', "%{$s}%")
                  ->orWhere('tujuan_penggunaan', 'like', "%{$s}%");
            });
        }
        if ($request->filled('kategori_kas_rw')) {
            $kasRwQuery->where('kategori', $request->kategori_kas_rw);
        }
        if ($request->filled('jenis_kas_rw')) {
            $kasRwQuery->where('jenis_transaksi', $request->jenis_kas_rw);
        }
        $transaksiKasRw = $kasRwQuery->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15, ['*'], 'page_kas_rw')
            ->withQueryString();

        $kategoriKasRwList = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
            ->distinct()
            ->pluck('kategori')
            ->filter();

        // ==========================================
        // 4. TAB REKAP KAS RT (RT 001 - RT 008)
        // ==========================================
        $rekapKasRt = [];
        $evaluasiRtList = AuditEvaluasiRt::with('evaluator')
            ->where('tahun', $tahun)
            ->get();

        for ($i = 1; $i <= 8; $i++) {
            $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT);
            $eval = $evaluasiRtList->firstWhere('rt_number', $rtStr) ?: $evaluasiRtList->firstWhere('rt_number', (string)$i);

            $bukuKas = BukuKas::where('jenis_kas', 'kas_rt')
                ->where(function($q) use ($i, $rtStr) {
                    $q->where('rt_number', $i)->orWhere('rt_number', $rtStr);
                })->first();

            $picRt = User::where('role', 'rt')->where(function($q) use ($i, $rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$i)->orWhere('rt_number', $i);
            })->first();

            $masukRt = $bukuKas ? TransaksiKeuangan::where('buku_kas_id', $bukuKas->id)->where('status', 'selesai')->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])->whereYear('tanggal_transaksi', $tahun)->sum('nominal') : 0;
            $keluarRt = $bukuKas ? TransaksiKeuangan::where('buku_kas_id', $bukuKas->id)->where('status', 'selesai')->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])->whereYear('tanggal_transaksi', $tahun)->sum('nominal') : 0;
            $txCount = $bukuKas ? TransaksiKeuangan::where('buku_kas_id', $bukuKas->id)->where('status', 'selesai')->whereYear('tanggal_transaksi', $tahun)->count() : 0;

            $recentTx = $bukuKas ? TransaksiKeuangan::where('buku_kas_id', $bukuKas->id)->where('status', 'selesai')->orderBy('tanggal_transaksi', 'desc')->limit(8)->get() : collect([]);

            $evalBadge = $eval ? $eval->status_badge : [
                'label' => 'Baik',
                'bg' => 'wm-badge-eval-baik',
                'style' => 'background-color: #DCFCE7; color: #166534; border: 1px solid #86EFAC;',
            ];

            $laporanBadge = $eval ? $eval->status_laporan_badge : [
                'label' => 'Terkirim',
                'bg' => 'wm-badge-eval-baik',
                'style' => 'background-color: #DCFCE7; color: #166534; border: 1px solid #86EFAC;',
            ];

            $verifikasiBadge = $eval ? $eval->status_verifikasi_badge : [
                'label' => 'Menunggu Pemeriksaan',
                'bg' => 'wm-badge-eval-cukup',
                'style' => 'background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;',
            ];

            $rekapKasRt[] = [
                'rt_num' => $i,
                'rt_number' => $rtStr,
                'rt_label' => 'RT ' . $rtStr,
                'pic_name' => $picRt->name ?? 'Ketua RT ' . $rtStr,
                'saldo' => $bukuKas ? (float)$bukuKas->saldo_sekarang : 0,
                'total_masuk' => $masukRt,
                'total_keluar' => $keluarRt,
                'tx_count' => $txCount,
                'periode_terakhir' => Carbon::now()->translatedFormat('F Y'),
                'status_laporan' => $eval ? ($eval->status_laporan ?: 'Terkirim') : 'Terkirim',
                'status_laporan_badge' => $laporanBadge,
                'status_verifikasi' => $eval ? ($eval->status_verifikasi_rw ?: 'Menunggu Pemeriksaan') : 'Menunggu Pemeriksaan',
                'status_verifikasi_badge' => $verifikasiBadge,
                'catatan_verifikasi' => $eval->catatan_verifikasi_rw ?? null,
                'diverifikasi_pada' => $eval->diverifikasi_rw_pada ?? null,
                'evaluasi' => $eval,
                'status_label' => $evalBadge['label'],
                'status_badge' => $evalBadge['bg'],
                'status_style' => $evalBadge['style'],
                'skor' => $eval->skor_kepatuhan ?? 85,
                'transaksis' => $recentTx,
                'buku_kas' => $bukuKas,
            ];
        }

        // Aliasing untuk backward compatibility
        $scorecardRt = $rekapKasRt;

        // ==========================================
        // 5. TAB MONITORING IURAN
        // ==========================================
        $tarifIuranList = IuranTarif::where('is_active', true)->get();
        $totalTargetIuran = TagihanKeluarga::whereYear('tanggal_terbit', $tahun)->sum('total_tagihan');
        $totalTerkumpulIuran = TagihanKeluarga::whereYear('tanggal_terbit', $tahun)->sum('total_dibayar');
        $totalTunggakanIuran = TagihanKeluarga::whereYear('tanggal_terbit', $tahun)->whereIn('status', ['Belum Dibayar', 'Terlambat', 'Dibayar Sebagian'])->sum('sisa_tagihan');
        $persenKolektibilitasTotal = $totalTargetIuran > 0 ? round(($totalTerkumpulIuran / $totalTargetIuran) * 100, 1) : 0;

        $rekapIuranRt = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT);
            $tagihansRt = TagihanKeluarga::where(function($q) use ($rtStr, $i) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$i);
            })->whereYear('tanggal_terbit', $tahun)->get();

            $targetRt = $tagihansRt->sum('total_tagihan');
            $bayarRt = $tagihansRt->sum('total_dibayar');
            $tunggakRt = $tagihansRt->whereIn('status', ['Belum Dibayar', 'Terlambat', 'Dibayar Sebagian'])->sum('sisa_tagihan');
            $pctRt = $targetRt > 0 ? round(($bayarRt / $targetRt) * 100, 1) : 0;

            $rekapIuranRt[] = [
                'rt_label' => 'RT ' . $rtStr,
                'rt_number' => $rtStr,
                'total_kk' => KartuKeluarga::where('rt', $rtStr)->orWhere('rt', (string)$i)->count(),
                'target' => $targetRt,
                'terkumpul' => $bayarRt,
                'tunggakan' => $tunggakRt,
                'persen' => $pctRt,
                'lunas_count' => $tagihansRt->where('status', 'Lunas')->count(),
                'belum_count' => $tagihansRt->whereIn('status', ['Belum Dibayar', 'Terlambat'])->count(),
            ];
        }

        // Rekap per Periode (12 Bulan)
        $rekapIuranPeriode = [];
        for ($m = 1; $m <= 12; $m++) {
            $pStr = sprintf('%04d-%02d', $tahun, $m);
            $pName = Carbon::create($tahun, $m, 1)->translatedFormat('F Y');
            $tagihanP = TagihanKeluarga::where('periode', $pStr)->get();
            $rekapIuranPeriode[] = [
                'periode_key' => $pStr,
                'periode_label' => $pName,
                'target' => $tagihanP->sum('total_tagihan'),
                'terkumpul' => $tagihanP->sum('total_dibayar'),
                'tunggakan' => $tagihanP->whereIn('status', ['Belum Dibayar', 'Terlambat', 'Dibayar Sebagian'])->sum('sisa_tagihan'),
                'lunas_count' => $tagihanP->where('status', 'Lunas')->count(),
                'total_kk' => $tagihanP->count(),
            ];
        }

        // ==========================================
        // 6. TAB LAPORAN KEUANGAN
        // ==========================================
        $laporanBulananKasRw = [];
        for ($m = 1; $m <= 12; $m++) {
            $mIn = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
                ->whereYear('tanggal_transaksi', $tahun)
                ->whereMonth('tanggal_transaksi', $m)
                ->sum('nominal');

            $mOut = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
                ->whereYear('tanggal_transaksi', $tahun)
                ->whereMonth('tanggal_transaksi', $m)
                ->sum('nominal');

            $laporanBulananKasRw[] = [
                'bulan_num' => $m,
                'bulan_name' => Carbon::create($tahun, $m, 1)->translatedFormat('F'),
                'pemasukan' => $mIn,
                'pengeluaran' => $mOut,
                'surplus_defisit' => $mIn - $mOut,
            ];
        }

        // ==========================================
        // 7. TAB RIWAYAT TRANSAKSI (Audit Logs)
        // ==========================================
        $auditLogs = KeuanganAuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'page_log')
            ->withQueryString();

        // ==========================================
        // 8. DATA DARI AUDIT SEBELUMNYA (Backward-Compatible)
        // ==========================================
        $totalKegiatan = AuditKegiatan::where('tahun', $tahun)->count();
        $totalTransaksiDiperiksa = TransaksiKeuangan::where('status', 'selesai')
            ->whereYear('tanggal_transaksi', $tahun)
            ->count();
        $totalTemuan = AuditTemuan::where('tahun', $tahun)->count();
        if ($totalTemuan == 0) {
            $totalTemuan = AuditTemuan::count();
        }
        $temuanTerbuka = AuditTemuan::whereIn('status', [
            'perlu_diperiksa', 'dalam_pemeriksaan', 'menunggu_klarifikasi', 'perlu_tindak_lanjut'
        ])->count();
        $temuanSelesai = AuditTemuan::where('status', 'selesai')->count();
        $rataSkorKepatuhan = AuditEvaluasiRt::where('tahun', $tahun)->avg('skor_kepatuhan');
        $rataSkorKepatuhan = $rataSkorKepatuhan ? round($rataSkorKepatuhan, 1) : 85.0;

        // Deteksi Transaksi Tanpa Bukti Pendukung
        $transaksiTanpaBukti = TransaksiKeuangan::with('bukuKas')
            ->where('status', 'selesai')
            ->whereYear('tanggal_transaksi', $tahun)
            ->where(function($q) {
                $q->whereNull('bukti_transaksi_path')->orWhere('bukti_transaksi_path', '');
            })
            ->orderBy('tanggal_transaksi', 'desc')
            ->limit(6)
            ->get();
        $countTanpaBukti = $transaksiTanpaBukti->count();

        // Deteksi Transaksi Anomali / Pengeluaran Besar (> Rp 2.500.000)
        $transaksiAnomali = TransaksiKeuangan::with('bukuKas')
            ->where('status', 'selesai')
            ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
            ->where('nominal', '>=', 2500000)
            ->whereYear('tanggal_transaksi', $tahun)
            ->orderBy('nominal', 'desc')
            ->limit(6)
            ->get();

        // Deteksi Transaksi Dibatalkan / Dikoreksi
        $transaksiBatal = TransaksiKeuangan::with('bukuKas')
            ->where('status', 'batal')
            ->whereYear('tanggal_transaksi', $tahun)
            ->orderBy('updated_at', 'desc')
            ->limit(6)
            ->get();

        $logAktivitasAudit = KeuanganAuditLog::with('user')->orderBy('created_at', 'desc')->limit(15)->get();

        // Rekap Realisasi Kas Utama
        $rekapBukuKas = BukuKas::where('is_active', true)->get()->map(function($bk) use ($tahun) {
            $realisasiMasuk = TransaksiKeuangan::where('buku_kas_id', $bk->id)
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])
                ->whereYear('tanggal_transaksi', $tahun)
                ->sum('nominal');

            $realisasiKeluar = TransaksiKeuangan::where('buku_kas_id', $bk->id)
                ->where('status', 'selesai')
                ->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])
                ->whereYear('tanggal_transaksi', $tahun)
                ->sum('nominal');

            $target = $bk->target_saldo > 0 ? $bk->target_saldo : ($realisasiKeluar + $bk->saldo_sekarang);
            $persenSerap = $target > 0 ? round(($realisasiKeluar / $target) * 100, 1) : 0;

            return [
                'nama_kas' => $bk->nama_kas,
                'jenis_kas' => $bk->jenis_kas,
                'target_anggaran' => $target,
                'realisasi_masuk' => $realisasiMasuk,
                'realisasi_keluar' => $realisasiKeluar,
                'saldo_sekarang' => $bk->saldo_sekarang,
                'persen_serap' => $persenSerap,
            ];
        });

        // Audit Kegiatan
        $kegiatanQuery = AuditKegiatan::with(['bukuKas', 'temuans'])->where('tahun', $tahun);
        if ($filterRt !== 'all') {
            $rtStr = str_pad($filterRt, 3, '0', STR_PAD_LEFT);
            $kegiatanQuery->where(function($q) use ($filterRt, $rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$filterRt);
            });
        }
        $daftarKegiatan = $kegiatanQuery->orderBy('created_at', 'desc')->get();
        $lpjDisetujui = $daftarKegiatan->where('status_audit', 'sesuai')->count();
        $lpjPerluRevisi = $daftarKegiatan->where('status_audit', 'perlu_perhatian')->count();
        $lpjBelumDiperiksa = $daftarKegiatan->where('status_audit', 'belum_diaudit')->count();
        $lpjDitolak = $daftarKegiatan->where('status_audit', 'temuan_audit')->count();

        // Temuan Audit
        $temuanQuery = AuditTemuan::with(['kegiatan', 'transaksi', 'bukuKas', 'auditor']);
        if ($filterRt !== 'all') {
            $rtStr = str_pad($filterRt, 3, '0', STR_PAD_LEFT);
            $temuanQuery->where(function($q) use ($filterRt, $rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$filterRt);
            });
        }
        if ($filterKategori !== 'all') {
            $temuanQuery->where('kategori_masalah', $filterKategori);
        }
        if ($filterStatus !== 'all') {
            $temuanQuery->where('status', $filterStatus);
        }
        $daftarTemuan = $temuanQuery->orderBy('created_at', 'desc')->get();
        $countKritis = AuditTemuan::where('tingkat_prioritas', 'kritis')->count();
        $countTinggi = AuditTemuan::where('tingkat_prioritas', 'tinggi')->count();
        $countSedang = AuditTemuan::where('tingkat_prioritas', 'sedang')->count();
        $countRendah = AuditTemuan::where('tingkat_prioritas', 'rendah')->count();

        $allBukuKas = BukuKas::where('is_active', true)->get();

        // Titipan Tunai Iuran di RT Menunggu Setor ke RW
        $titipanRtList = \App\Models\PembayaranAlokasi::where('status_alokasi', 'titipan_rt')
            ->with(['tagihanKeluarga.kartuKeluarga', 'tagihanItem', 'bukuKas', 'pembayaranIuran'])
            ->get();
        $totalTitipanMenungguSetor = $titipanRtList->sum('nominal_alokasi');

        return view('audit.index', compact(
            'tahun',
            'filterRt',
            'filterKategori',
            'filterStatus',
            'activeTab',
            // Financial Kas RW & Separate Monitoring
            'kasRw',
            'saldoKasRw',
            'pemasukanKasRwBulanIni',
            'pengeluaranKasRwBulanIni',
            'iuranRwTerkumpul',
            'iuranRwBelumBayar',
            'rtSudahLapor',
            'rtBelumLapor',
            'totalKas8Rt',
            // Charts
            'chartBulanLabels',
            'chartKasRwMasuk',
            'chartKasRwKeluar',
            'chartKasRwSaldo',
            'countIuranLunas',
            'countIuranBelum',
            'countIuranMenunggu',
            'rtChartLabels',
            'rtChartSaldo',
            'rtChartInflow',
            // Kas RW Tab
            'transaksiKasRw',
            'kategoriKasRwList',
            // Rekap Kas RT Tab
            'rekapKasRt',
            // Monitoring Iuran Tab
            'tarifIuranList',
            'totalTargetIuran',
            'totalTerkumpulIuran',
            'totalTunggakanIuran',
            'persenKolektibilitasTotal',
            'rekapIuranRt',
            'rekapIuranPeriode',
            'titipanRtList',
            'totalTitipanMenungguSetor',
            // Laporan Keuangan Tab
            'laporanBulananKasRw',
            // Riwayat Transaksi Tab
            'auditLogs',
            // Preserved Audit Elements
            'totalKegiatan',
            'totalTransaksiDiperiksa',
            'totalTemuan',
            'temuanTerbuka',
            'temuanSelesai',
            'rataSkorKepatuhan',
            'scorecardRt',
            'countTanpaBukti',
            'transaksiTanpaBukti',
            'transaksiAnomali',
            'transaksiBatal',
            'logAktivitasAudit',
            'rekapBukuKas',
            'daftarKegiatan',
            'lpjDisetujui',
            'lpjPerluRevisi',
            'lpjBelumDiperiksa',
            'lpjDitolak',
            'daftarTemuan',
            'countKritis',
            'countTinggi',
            'countSedang',
            'countRendah',
            'allBukuKas'
        ));
    }

    /**
     * Catat Transaksi Kas RW Baru (Pemasukan / Pengeluaran)
     * Rute: POST /rw/audit/kas-rw/store (rw.audit.kas_rw.store)
     */
    public function storeTransaksiRw(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Pengurus RW 013.');
        }

        $request->validate([
            'jenis_transaksi' => 'required|in:pemasukan,pengeluaran',
            'tanggal_transaksi' => 'required|date',
            'kategori' => 'required|string|max:100',
            'nominal' => 'required|numeric|min:1000',
            'metode_pembayaran' => 'required|in:tunai,transfer_bank,qris',
            'sumber_dana' => 'nullable|string|max:150',
            'tujuan_penggunaan' => 'nullable|string|max:150',
            'penyetor_penerima' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string',
            'bukti_transaksi' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
        ]);

        $kasRw = BukuKas::where('jenis_kas', 'kas_rw')->firstOrFail();

        $buktiPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');
        }

        DB::beginTransaction();
        try {
            $date = Carbon::parse($request->tanggal_transaksi);
            $prefix = 'RW-TRX-' . $date->format('Ym') . '-';
            $latestTrx = TransaksiKeuangan::where('nomor_transaksi', 'like', $prefix . '%')->count();
            $nomorTrx = $prefix . str_pad($latestTrx + 1, 4, '0', STR_PAD_LEFT);

            $saldoSebelumnya = (float) $kasRw->saldo_sekarang;
            $nominal = (float) $request->nominal;
            $saldoSetelahnya = $request->jenis_transaksi === 'pemasukan' 
                ? $saldoSebelumnya + $nominal 
                : $saldoSebelumnya - $nominal;

            $penyetorPenerima = $request->penyetor_penerima ?: ($request->jenis_transaksi === 'pemasukan' ? $request->sumber_dana : $request->tujuan_penggunaan);

            $trx = TransaksiKeuangan::create([
                'nomor_transaksi' => $nomorTrx,
                'buku_kas_id' => $kasRw->id,
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'jenis_transaksi' => $request->jenis_transaksi,
                'kategori' => $request->kategori,
                'nominal' => $nominal,
                'saldo_sebelumnya' => $saldoSebelumnya,
                'saldo_setelahnya' => $saldoSetelahnya,
                'metode_pembayaran' => $request->metode_pembayaran,
                'sumber_dana' => $request->sumber_dana,
                'tujuan_penggunaan' => $request->tujuan_penggunaan,
                'penyetor_penerima' => $penyetorPenerima ?: 'Pengurus RW 013',
                'keterangan' => $request->keterangan,
                'petugas_id' => $user->id,
                'nama_petugas' => $user->name,
                'bukti_transaksi_path' => $buktiPath,
                'status' => 'selesai',
            ]);

            $kasRw->recalculateSaldo();

            KeuanganAuditLog::create([
                'user_id' => $user->id,
                'action' => 'transaksi_rw_baru',
                'deskripsi' => "Mencatat {$request->jenis_transaksi} Kas RW [{$nomorTrx}] sebesar Rp " . number_format($nominal, 0, ',', '.') . " ({$request->kategori})",
                'new_values' => $trx->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return redirect()->route('rw.audit.index', ['tab' => 'kas_rw'])->with('success', "Transaksi Kas RW [{$nomorTrx}] berhasil dicatat!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mencatat transaksi Kas RW: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Edit Transaksi Kas RW dengan Pencatatan Perubahan (Audit Trail)
     * Rute: POST /rw/audit/kas-rw/{id}/update (rw.audit.kas_rw.update)
     */
    public function updateTransaksiRw(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Pengurus RW 013.');
        }

        $request->validate([
            'alasan_edit' => 'required|string|min:5',
            'tanggal_transaksi' => 'required|date',
            'kategori' => 'required|string|max:100',
            'nominal' => 'required|numeric|min:1000',
            'metode_pembayaran' => 'required|in:tunai,transfer_bank,qris',
            'sumber_dana' => 'nullable|string|max:150',
            'tujuan_penggunaan' => 'nullable|string|max:150',
            'penyetor_penerima' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string',
            'bukti_transaksi' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
        ]);

        $kasRw = BukuKas::where('jenis_kas', 'kas_rw')->firstOrFail();
        $trx = TransaksiKeuangan::where('buku_kas_id', $kasRw->id)->findOrFail($id);

        DB::beginTransaction();
        try {
            $oldValues = $trx->toArray();

            if ($request->hasFile('bukti_transaksi')) {
                $trx->bukti_transaksi_path = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');
            }

            $trx->tanggal_transaksi = $request->tanggal_transaksi;
            $trx->kategori = $request->kategori;
            $trx->nominal = (float) $request->nominal;
            $trx->metode_pembayaran = $request->metode_pembayaran;
            $trx->sumber_dana = $request->sumber_dana;
            $trx->tujuan_penggunaan = $request->tujuan_penggunaan;
            $penyetorPenerima = $request->penyetor_penerima ?: ($trx->jenis_transaksi === 'pemasukan' ? $request->sumber_dana : $request->tujuan_penggunaan);
            if ($penyetorPenerima) {
                $trx->penyetor_penerima = $penyetorPenerima;
            }
            $trx->keterangan = $request->keterangan;
            $trx->alasan_edit = $request->alasan_edit;
            $trx->edited_by_id = $user->id;
            $trx->edited_at = Carbon::now();
            $trx->save();

            $kasRw->recalculateSaldo();

            KeuanganAuditLog::create([
                'user_id' => $user->id,
                'action' => 'edit_transaksi_rw',
                'deskripsi' => "Mengubah transaksi Kas RW [{$trx->nomor_transaksi}]. Alasan: {$request->alasan_edit}",
                'old_values' => $oldValues,
                'new_values' => $trx->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return redirect()->route('rw.audit.index', ['tab' => 'kas_rw'])->with('success', "Transaksi Kas RW [{$trx->nomor_transaksi}] berhasil diperbarui dan dicatat dalam audit trail.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui transaksi: ' . $e->getMessage()]);
        }
    }

    /**
     * Verifikasi Laporan Keuangan RT oleh Ketua RW
     * Rute: POST /rw/audit/rekap-rt/{rt}/verifikasi (rw.audit.rekap_rt.verifikasi)
     */
    public function verifikasiLaporanRt(Request $request, $rtNumber)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Pengurus RW 013.');
        }

        $request->validate([
            'status_verifikasi' => 'required|in:Terverifikasi,Perlu Klarifikasi,Menunggu Pemeriksaan',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $rtNumber), 3, '0', STR_PAD_LEFT);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);

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
                'status_laporan' => 'Terkirim',
            ]
        );

        $eval->status_verifikasi_rw = $request->status_verifikasi;
        $eval->catatan_verifikasi_rw = $request->catatan_verifikasi;
        $eval->diverifikasi_rw_pada = Carbon::now();
        if ($request->status_verifikasi === 'Terverifikasi') {
            $eval->laporan_dikunci = true;
        } else {
            $eval->laporan_dikunci = false;
        }
        $eval->save();

        KeuanganAuditLog::create([
            'user_id' => $user->id,
            'action' => 'verifikasi_laporan_rt',
            'deskripsi' => "Ketua RW memverifikasi laporan keuangan RT {$rtStr} dengan status [{$request->status_verifikasi}]. Catatan: " . ($request->catatan_verifikasi ?: '-'),
            'new_values' => [
                'rt_number' => $rtStr,
                'status_verifikasi' => $request->status_verifikasi,
                'catatan' => $request->catatan_verifikasi,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('rw.audit.index', ['tab' => 'rekap_rt'])->with('success', "Laporan Keuangan RT {$rtStr} berhasil diverifikasi dengan status [{$request->status_verifikasi}].");
    }

    /**
     * Simpan Temuan Audit Baru
     * Rute: POST /rw/audit/temuan (rw.audit.temuan.store)
     */
    public function storeTemuan(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Pengurus RW 013.');
        }

        $request->validate([
            'judul_temuan' => 'required|string|max:255',
            'kategori_masalah' => 'required|string',
            'tingkat_prioritas' => 'required|string|in:rendah,sedang,tinggi,kritis',
            'rt_number' => 'required|string',
            'deskripsi_masalah' => 'required|string|min:10',
            'catatan_evaluasi_rw' => 'nullable|string',
            'tenggat_tindak_lanjut' => 'nullable|date',
            'kegiatan_id' => 'nullable|exists:audit_kegiatans,id',
            'buku_kas_id' => 'nullable|exists:buku_kas,id',
            'transaksi_id' => 'nullable|exists:transaksi_keuangans,id',
        ]);

        $tahun = Carbon::now()->year;
        $countToday = AuditTemuan::whereYear('created_at', $tahun)->count() + 1;
        $kodeTemuan = sprintf('AUD-%04d-%03d', $tahun, $countToday);

        $temuan = AuditTemuan::create([
            'kode_temuan' => $kodeTemuan,
            'judul_temuan' => $request->judul_temuan,
            'kategori_masalah' => $request->kategori_masalah,
            'tingkat_prioritas' => $request->tingkat_prioritas,
            'rt_number' => str_pad($request->rt_number, 3, '0', STR_PAD_LEFT),
            'tanggal_temuan' => Carbon::now()->toDateString(),
            'tahun' => $tahun,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'catatan_evaluasi_rw' => $request->catatan_evaluasi_rw,
            'tenggat_tindak_lanjut' => $request->tenggat_tindak_lanjut,
            'status' => 'perlu_diperiksa',
            'auditor_id' => $user->id,
            'kegiatan_id' => $request->kegiatan_id,
            'buku_kas_id' => $request->buku_kas_id,
            'transaksi_id' => $request->transaksi_id,
        ]);

        KeuanganAuditLog::create([
            'user_id' => $user->id,
            'action' => 'audit_temuan_baru',
            'deskripsi' => "Menerbitkan temuan audit baru [{$kodeTemuan}] RT {$request->rt_number}: {$request->judul_temuan} (Prioritas: {$request->tingkat_prioritas})",
            'new_values' => ['kode_temuan' => $kodeTemuan, 'status' => 'perlu_diperiksa'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('rw.audit.index', ['tab' => 'temuan'])->with('success', "Temuan audit {$kodeTemuan} berhasil diterbitkan dan dicatat dalam register temuan.");
    }

    /**
     * Update Status Temuan Audit
     * Rute: POST /rw/audit/temuan/{id}/status (rw.audit.temuan.status)
     */
    public function updateStatusTemuan(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Pengurus RW 013.');
        }

        $temuan = AuditTemuan::findOrFail($id);

        $request->validate([
            'status' => 'required|in:perlu_diperiksa,dalam_pemeriksaan,menunggu_klarifikasi,perlu_tindak_lanjut,selesai,terbuka,dalam_proses,menunggu_verifikasi',
            'catatan_evaluasi_rw' => 'nullable|string',
            'tanggapan_rt' => 'nullable|string',
        ]);

        $statusLama = $temuan->status;
        $temuan->status = $request->status;

        if ($request->filled('catatan_evaluasi_rw')) {
            $temuan->catatan_evaluasi_rw = $request->catatan_evaluasi_rw;
        }
        if ($request->filled('tanggapan_rt')) {
            $temuan->tanggapan_rt = $request->tanggapan_rt;
            $temuan->tanggal_tanggapan = Carbon::now();
        }
        if ($request->status === 'selesai') {
            $temuan->diselesaikan_pada = Carbon::now();
        }

        $temuan->save();

        KeuanganAuditLog::create([
            'user_id' => $user->id,
            'action' => 'audit_update_status',
            'deskripsi' => "Mengubah status temuan [{$temuan->kode_temuan}] dari {$statusLama} menjadi {$request->status}",
            'old_values' => ['status' => $statusLama],
            'new_values' => ['status' => $request->status],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Status temuan {$temuan->kode_temuan} berhasil diperbarui menjadi [{$temuan->status_badge['label']}].");
    }

    /**
     * Simpan Evaluasi & Skor Pertanggungjawaban RT
     * Rute: POST /rw/audit/evaluasi-rt (rw.audit.evaluasi.store)
     */
    public function storeEvaluasiRt(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Pengurus RW 013.');
        }

        $request->validate([
            'rt_number' => 'required|string',
            'tahun' => 'required|integer',
            'periode' => 'required|string',
            'skor_kepatuhan' => 'required|numeric|min:0|max:100',
            'status_pertanggungjawaban' => 'required|in:sangat_baik,baik,cukup,perlu_pembinaan',
            'catatan_evaluasi' => 'required|string|min:5',
            'rekomendasi_rw' => 'nullable|string',
            'tenggat_perbaikan' => 'nullable|date',
        ]);

        $rtStr = str_pad($request->rt_number, 3, '0', STR_PAD_LEFT);

        $evaluasi = AuditEvaluasiRt::updateOrCreate(
            [
                'rt_number' => $rtStr,
                'tahun' => $request->tahun,
                'periode' => $request->periode,
            ],
            [
                'evaluator_id' => $user->id,
                'skor_kepatuhan' => $request->skor_kepatuhan,
                'status_pertanggungjawaban' => $request->status_pertanggungjawaban,
                'catatan_evaluasi' => $request->catatan_evaluasi,
                'rekomendasi_rw' => $request->rekomendasi_rw,
                'tenggat_perbaikan' => $request->tenggat_perbaikan,
                'tanggal_evaluasi' => Carbon::now()->toDateString(),
            ]
        );

        KeuanganAuditLog::create([
            'user_id' => $user->id,
            'action' => 'audit_evaluasi_rt',
            'deskripsi' => "Menetapkan evaluasi & skor kepatuhan RT {$rtStr} ({$request->periode} {$request->tahun}): Skor {$request->skor_kepatuhan} ({$request->status_pertanggungjawaban})",
            'new_values' => ['skor' => $request->skor_kepatuhan, 'status' => $request->status_pertanggungjawaban],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('rw.audit.index', ['tab' => 'evaluasi'])->with('success', "Evaluasi pertanggungjawaban RT {$rtStr} berhasil disimpan!");
    }

    /**
     * Tanggapan atau Klarifikasi RT terhadap Temuan
     * Rute: POST /rw/audit/temuan/{id}/tanggapi (rw.audit.temuan.tanggapi)
     */
    public function tanggapiTemuan(Request $request, $id)
    {
        $user = Auth::user();
        $temuan = AuditTemuan::findOrFail($id);

        $request->validate([
            'tanggapan_rt' => 'required|string|min:10',
        ]);

        $temuan->tanggapan_rt = $request->tanggapan_rt;
        $temuan->tanggal_tanggapan = Carbon::now();
        $temuan->status = 'dalam_pemeriksaan';
        $temuan->save();

        KeuanganAuditLog::create([
            'user_id' => $user->id,
            'action' => 'audit_tanggapan_rt',
            'deskripsi' => "Memberikan klarifikasi/tanggapan atas temuan [{$temuan->kode_temuan}]: {$request->tanggapan_rt}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Tanggapan/klarifikasi atas temuan {$temuan->kode_temuan} berhasil dikirim ke Pengurus RW.");
    }

    /**
     * Cetak Laporan Hasil Audit (LHA) Tahunan Resmi RW 013
     * Rute: GET /rw/audit/cetak-laporan (rw.audit.cetak)
     */
    public function cetakLaporanTahunan(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Pengurus RW 013.');
        }

        $tahun = (int) $request->input('tahun', Carbon::now()->year);

        $daftarKegiatan = AuditKegiatan::with(['bukuKas', 'temuans'])->where('tahun', $tahun)->get();
        $daftarTemuan = AuditTemuan::with(['kegiatan', 'auditor'])->orderBy('tingkat_prioritas', 'desc')->get();
        $evaluasiRtList = AuditEvaluasiRt::with('evaluator')->where('tahun', $tahun)->orderBy('rt_number', 'asc')->get();
        
        $totalAnggaranKegiatan = $daftarKegiatan->sum('anggaran_awal');
        $totalRealisasiKegiatan = $daftarKegiatan->sum('realisasi_anggaran');

        $bukuKasList = BukuKas::where('is_active', true)->get();
        $totalSaldoSeluruh = $bukuKasList->sum('saldo_sekarang');

        // Pastikan seluruh RT 001 s/d RT 008 tercakup lengkap dalam evaluasi kepatuhan
        $scorecardRt = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT);
            $eval = $evaluasiRtList->firstWhere('rt_number', $rtStr);
            if (!$eval) {
                $eval = $evaluasiRtList->firstWhere('rt_number', (string)$i);
            }

            $picRt = User::where('role', 'rt')->where(function($q) use ($i, $rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$i)->orWhere('rt_number', $i);
            })->first();

            $evalBadge = $eval ? $eval->status_badge : [
                'label' => 'Baik',
                'bg' => 'wm-badge-eval-baik',
                'style' => 'background-color: #DCFCE7; color: #166534; border: 1px solid #86EFAC;',
            ];

            $scorecardRt[] = [
                'rt_num' => $i,
                'rt_name' => 'RT ' . $rtStr,
                'evaluasi' => $eval,
                'pic_name' => $picRt->name ?? 'Pengurus RT ' . $rtStr,
                'skor' => $eval->skor_kepatuhan ?? 85,
                'status_label' => $evalBadge['label'],
                'status_badge' => $evalBadge['bg'],
                'status_style' => $evalBadge['style'] ?? '',
                'catatan' => $eval->catatan_evaluasi ?? 'Pencatatan kas dan administrasi berjalan tertib.',
                'rekomendasi' => $eval->rekomendasi_rw ?? 'Pertahankan konsistensi pelaporan bulanan.',
                'deadline' => $eval->tenggat_perbaikan ?? null,
            ];
        }

        // Ambil data profil resmi Ketua RW dari database atau akun user aktif yang berwenang
        $ketuaRw = User::where('role', 'rw')->orderBy('id', 'asc')->first() ?: $user;

        return view('audit.cetak_laporan', compact(
            'tahun',
            'daftarKegiatan',
            'daftarTemuan',
            'evaluasiRtList',
            'scorecardRt',
            'totalAnggaranKegiatan',
            'totalRealisasiKegiatan',
            'bukuKasList',
            'totalSaldoSeluruh',
            'user',
            'ketuaRw'
        ));
    }
}
