<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditKegiatan;
use App\Models\AuditTemuan;
use App\Models\AuditEvaluasiRt;
use App\Models\BukuKas;
use App\Models\TransaksiKeuangan;
use App\Models\KeuanganAuditLog;
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
        $activeTab = $request->input('tab', 'overview');

        // --- 1. DASHBOARD METRICS ---
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

        // Distribusi Status Kepatuhan RT
        $evaluasiRtList = AuditEvaluasiRt::with('evaluator')
            ->where('tahun', $tahun)
            ->orderBy('rt_number', 'asc')
            ->get();

        // Pastikan seluruh RT 001 - RT 008 terdaftar di scorecard
        $scorecardRt = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT);
            $eval = $evaluasiRtList->firstWhere('rt_number', $rtStr);
            if (!$eval) {
                $eval = $evaluasiRtList->firstWhere('rt_number', (string)$i);
            }

            $bukuKas = BukuKas::where('jenis_kas', 'kas_rt')
                ->where(function($q) use ($i, $rtStr) {
                    $q->where('rt_number', $i)->orWhere('rt_number', $rtStr);
                })->first();

            $picRt = User::where('role', 'rt')->where(function($q) use ($i, $rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$i)->orWhere('rt_number', $i);
            })->first();

            $scorecardRt[] = [
                'rt_num' => $i,
                'rt_name' => 'RT ' . $rtStr,
                'evaluasi' => $eval,
                'pic_name' => $picRt->name ?? 'Ketua RT ' . $rtStr,
                'saldo_kas' => $bukuKas->saldo_sekarang ?? 0,
                'skor' => $eval->skor_kepatuhan ?? 85,
                'status_label' => $eval->status_badge['label'] ?? 'Baik & Memadai',
                'status_badge' => $eval->status_badge['bg'] ?? 'bg-blue-100 text-blue-800 border-blue-300',
                'catatan' => $eval->catatan_evaluasi ?? 'Pencatatan kas dan administrasi berjalan tertib.',
                'rekomendasi' => $eval->rekomendasi_rw ?? 'Pertahankan konsistensi pelaporan bulanan.',
                'deadline' => $eval->tenggat_perbaikan ?? null,
            ];
        }

        // --- 2. AUDIT KEUANGAN ---
        // Deteksi Transaksi Tanpa Bukti Pendukung
        $transaksiTanpaBuktiQuery = TransaksiKeuangan::with('bukuKas')
            ->where('status', 'selesai')
            ->whereYear('tanggal_transaksi', $tahun)
            ->where(function($q) {
                $q->whereNull('bukti_transaksi_path')->orWhere('bukti_transaksi_path', '');
            });
        $countTanpaBukti = $transaksiTanpaBuktiQuery->count();
        $transaksiTanpaBukti = $transaksiTanpaBuktiQuery->orderBy('tanggal_transaksi', 'desc')->limit(6)->get();

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

        // Riwayat Log Aktivitas Petugas Keuangan
        $logAktivitasAudit = KeuanganAuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        // Perbandingan Realisasi vs Anggaran Akun Kas Utama
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

        // --- 3. AUDIT KEGIATAN & PROYEK ---
        $kegiatanQuery = AuditKegiatan::with(['bukuKas', 'temuans'])->where('tahun', $tahun);
        if ($filterRt !== 'all') {
            $rtStr = str_pad($filterRt, 3, '0', STR_PAD_LEFT);
            $kegiatanQuery->where(function($q) use ($filterRt, $rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)$filterRt);
            });
        }
        $daftarKegiatan = $kegiatanQuery->orderBy('created_at', 'desc')->get();

        // Summary Status Audit Kegiatan
        $lpjDisetujui = $daftarKegiatan->where('status_audit', 'sesuai')->count();
        $lpjPerluRevisi = $daftarKegiatan->where('status_audit', 'perlu_perhatian')->count();
        $lpjBelumDiperiksa = $daftarKegiatan->where('status_audit', 'belum_diaudit')->count();
        $lpjDitolak = $daftarKegiatan->where('status_audit', 'temuan_audit')->count();

        // --- 4. TEMUAN AUDIT (AUDIT FINDINGS) ---
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

        return view('audit.index', compact(
            'tahun',
            'filterRt',
            'filterKategori',
            'filterStatus',
            'activeTab',
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
            'status' => 'required|in:perlu_diperiksa,dalam_pemeriksaan,menunggu_klarifikasi,perlu_tindak_lanjut,selesai',
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

            $scorecardRt[] = [
                'rt_num' => $i,
                'rt_name' => 'RT ' . $rtStr,
                'evaluasi' => $eval,
                'pic_name' => $picRt->name ?? 'Pengurus RT ' . $rtStr,
                'skor' => $eval->skor_kepatuhan ?? 85,
                'status_label' => $eval ? $eval->status_badge['label'] : 'Baik & Memadai',
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
