<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\KartuKeluarga;
use App\Models\PosyanduKegiatan;
use App\Models\PosyanduKader;
use App\Models\PosyanduPeserta;
use App\Models\PosyanduJadwal;
use App\Models\PosyanduKehadiran;
use App\Models\PosyanduPengukuran;
use App\Models\PosyanduLaporan;
use App\Models\ActivityLog;

class PosyanduController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Helper to get clean 3-digit RT string (e.g. "001")
     */
    protected function getCleanRt($rt): string
    {
        $num = preg_replace('/[^0-9]/', '', (string)$rt);
        return str_pad($num ?: '1', 3, '0', STR_PAD_LEFT);
    }

    /**
     * Halaman Utama Posyandu Terpadu RW 013 (Role-Aware Dispatcher)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. ROLE RW / ADMIN: Monitoring Menyeluruh Seluruh RW 013
        if ($user->isRw()) {
            return $this->indexRw($request, $user);
        }

        // 2. ROLE RT: Ruang Operasional Posyandu Wilayah RT Sendiri
        if ($user->isRt()) {
            return $this->indexRt($request, $user);
        }

        // 3. ROLE WARGA: Akses Informasi, Jadwal & Riwayat Anggota Keluarga
        return $this->indexWarga($request, $user);
    }

    /**
     * Dashboard Monitoring RW (Aggregated & Non-Tampering)
     */
    protected function indexRw(Request $request, $user)
    {
        $filterRt = $request->query('rt');
        if ($filterRt) {
            $filterRt = $this->getCleanRt($filterRt);
        }

        // Statistik Agregat RW 013
        $totalUnits = PosyanduKegiatan::count() ?: 8;
        $totalPeserta = PosyanduPeserta::where('status_aktif', true)->count();
        $totalBalita = PosyanduPeserta::where('status_aktif', true)->whereIn('kategori', ['bayi', 'balita', 'anak'])->count();
        $totalIbuHamil = PosyanduPeserta::where('status_aktif', true)->whereIn('kategori', ['ibu_hamil', 'ibu_menyusui'])->count();
        $totalLansia = PosyanduPeserta::where('status_aktif', true)->where('kategori', 'lansia')->count();

        $totalKegiatanSelesai = PosyanduJadwal::where('status', 'selesai')->count();
        $totalKegiatanAkanDatang = PosyanduJadwal::where('status', 'akan_datang')->count();

        $totalLaporanTerkirim = PosyanduLaporan::whereIn('status_laporan', ['dikirim_ke_rw', 'disetujui_rw', 'dievaluasi_rw'])->count();
        $persentaseLaporan = $totalKegiatanSelesai > 0 ? round(($totalLaporanTerkirim / $totalKegiatanSelesai) * 100) : 100;

        // Data Monitoring RT 001 s.d. RT 008
        $monitoringRt = [];
        $rtBelumLapor = [];

        for ($i = 1; $i <= 8; $i++) {
            $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT);
            $pesertaCount = PosyanduPeserta::where('rt_number', $rtStr)->where('status_aktif', true)->count();
            
            $lastEvent = PosyanduJadwal::where('rt_number', $rtStr)
                ->where('status', 'selesai')
                ->orderBy('tanggal', 'desc')
                ->first();

            $nextEvent = PosyanduJadwal::where('rt_number', $rtStr)
                ->where('status', 'akan_datang')
                ->orderBy('tanggal', 'asc')
                ->first();

            $lastAttendanceCount = 0;
            $reportStatus = 'Belum Ada Kegiatan';
            $reportModel = null;

            if ($lastEvent) {
                $lastAttendanceCount = PosyanduKehadiran::where('jadwal_id', $lastEvent->id)
                    ->where('status_kehadiran', 'hadir')
                    ->count();

                $report = PosyanduLaporan::where('jadwal_id', $lastEvent->id)->first();
                if ($report) {
                    $reportStatus = $report->status_label;
                    $reportModel = $report;
                } else {
                    $reportStatus = 'Belum Kirim Laporan';
                    $rtBelumLapor[] = "RT {$rtStr}";
                }
            }

            $kaderCount = PosyanduKader::where('rt_number', $rtStr)->where('is_aktif', true)->count();

            $monitoringRt[] = [
                'rt_number' => $rtStr,
                'peserta_count' => $pesertaCount,
                'last_event' => $lastEvent,
                'next_event' => $nextEvent,
                'last_attendance_count' => $lastAttendanceCount,
                'report_status' => $reportStatus,
                'report' => $reportModel,
                'kader_count' => $kaderCount,
            ];
        }

        // Laporan Kegiatan Terbaru Masuk ke RW
        $recentLaporans = PosyanduLaporan::with(['jadwal.unit', 'penanggungJawab'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('posyandu.index_rw', compact(
            'user',
            'totalUnits',
            'totalPeserta',
            'totalBalita',
            'totalIbuHamil',
            'totalLansia',
            'totalKegiatanSelesai',
            'totalKegiatanAkanDatang',
            'persentaseLaporan',
            'monitoringRt',
            'rtBelumLapor',
            'recentLaporans',
            'filterRt'
        ));
    }

    /**
     * Dashboard Operasional RT (RT Level Executor)
     */
    protected function indexRt(Request $request, $user)
    {
        $rtStr = $this->getCleanRt($user->rt_number);

        // Unit Posyandu RT
        $posyanduUnit = PosyanduKegiatan::where('rt_number', $rtStr)
            ->orWhere('rt_number', (string)intval($rtStr))
            ->first();

        // Statistik Peserta RT
        $pesertaQuery = PosyanduPeserta::where('rt_number', $rtStr)->where('status_aktif', true);
        $totalPeserta = (clone $pesertaQuery)->count();
        $totalBalita = (clone $pesertaQuery)->whereIn('kategori', ['bayi', 'balita', 'anak'])->count();
        $totalIbuHamil = (clone $pesertaQuery)->whereIn('kategori', ['ibu_hamil', 'ibu_menyusui'])->count();
        $totalLansia = (clone $pesertaQuery)->where('kategori', 'lansia')->count();

        // Kegiatan Terakhir & Kehadiran
        $lastEvent = PosyanduJadwal::where('rt_number', $rtStr)
            ->where('status', 'selesai')
            ->orderBy('tanggal', 'desc')
            ->first();

        $lastAttendanceCount = 0;
        $laporanTerakhir = null;
        if ($lastEvent) {
            $lastAttendanceCount = PosyanduKehadiran::where('jadwal_id', $lastEvent->id)
                ->where('status_kehadiran', 'hadir')
                ->count();
            $laporanTerakhir = PosyanduLaporan::where('jadwal_id', $lastEvent->id)->first();
        }

        // Jadwal Berikutnya
        $nextEvent = PosyanduJadwal::where('rt_number', $rtStr)
            ->where('status', 'akan_datang')
            ->orderBy('tanggal', 'asc')
            ->first();

        // Daftar Peserta RT (Paginated & Filterable)
        $kategoriFilter = $request->query('kategori');
        $search = $request->query('search');

        $pesertaListQuery = PosyanduPeserta::with(['user', 'kartuKeluarga', 'pengukurans' => function($q) {
            $q->orderBy('tanggal_pemeriksaan', 'desc')->limit(1);
        }])
        ->where('rt_number', $rtStr)
        ->where('status_aktif', true);

        if ($kategoriFilter) {
            $pesertaListQuery->where('kategori', $kategoriFilter);
        }

        if ($search) {
            $pesertaListQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $pesertaList = $pesertaListQuery->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Daftar Jadwal RT
        $jadwalList = PosyanduJadwal::where('rt_number', $rtStr)
            ->orderBy('tanggal', 'desc')
            ->limit(10)
            ->get();

        // Daftar Kader RT
        $kaderList = PosyanduKader::where('rt_number', $rtStr)->orderBy('is_aktif', 'desc')->get();

        // Notifikasi Tindak Lanjut Peserta
        $tindakLanjutList = PosyanduPengukuran::with('peserta.user')
            ->where('rt_number', $rtStr)
            ->whereNotNull('anjuran_rujukan')
            ->where('anjuran_rujukan', '!=', '')
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->limit(6)
            ->get();

        // Calon Warga RT untuk Dropdown Tambah Peserta
        $registeredRtUserIds = PosyanduPeserta::where('rt_number', $rtStr)
            ->where('status_aktif', true)
            ->pluck('user_id')
            ->toArray();

        $wargaBelumTerdaftar = User::where('role', 'warga')
            ->where(function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', (string)intval($rtStr));
            })
            ->whereNotIn('id', $registeredRtUserIds)
            ->orderBy('name')
            ->limit(100)
            ->get();

        // Smart Auto-Detection: Warga RT Belum Terdaftar (0-5 th Balita & >= 60 th Lansia)
        $autoDetectedRtWarga = [];
        foreach ($wargaBelumTerdaftar as $w) {
            if (!$w->tanggal_lahir) continue;
            $dob = Carbon::parse($w->tanggal_lahir);
            $diffMonths = (int) $dob->diffInMonths(Carbon::now());
            $diffYears = (int) $dob->age;

            if ($diffMonths < 60) {
                $cat = $diffMonths < 12 ? 'bayi' : 'balita';
                $autoDetectedRtWarga[] = [
                    'user' => $w,
                    'kategori' => $cat,
                    'kategori_label' => $diffMonths < 12 ? 'Bayi (0-11 Bln)' : 'Balita (1-5 Thn)',
                    'usia_text' => "{$diffMonths} Bulan",
                    'badge_class' => 'bg-info-subtle text-info border border-info-subtle',
                    'icon' => 'baby',
                ];
            } elseif ($diffYears >= 60) {
                $autoDetectedRtWarga[] = [
                    'user' => $w,
                    'kategori' => 'lansia',
                    'kategori_label' => 'Lansia (60+ Thn)',
                    'usia_text' => "{$diffYears} Tahun",
                    'badge_class' => 'bg-amber-subtle text-warning-emphasis border border-warning-subtle',
                    'icon' => 'smile',
                ];
            }
        }

        // Cek pengingat pelaporan resmi dari RW dalam 7 hari terakhir
        $rwReminder = ActivityLog::where('category', 'posyandu')
            ->where('action', 'posyandu.reminder_sent')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get()
            ->first(function($log) use ($rtStr) {
                return isset($log->metadata['target_rt']) && (string)$log->metadata['target_rt'] === (string)$rtStr;
            });

        return view('posyandu.index_rt', compact(
            'user',
            'rtStr',
            'posyanduUnit',
            'totalPeserta',
            'totalBalita',
            'totalIbuHamil',
            'totalLansia',
            'lastEvent',
            'lastAttendanceCount',
            'laporanTerakhir',
            'nextEvent',
            'pesertaList',
            'jadwalList',
            'kaderList',
            'tindakLanjutList',
            'wargaBelumTerdaftar',
            'autoDetectedRtWarga',
            'rwReminder'
        ));
    }

    /**
     * Dashboard Posyandu Warga (Family-Scoped & Health Privacy)
     */
    protected function indexWarga(Request $request, $user)
    {
        $rtStr = $this->getCleanRt($user->rt_number);

        // Unit Posyandu RT Warga
        $posyanduUnit = PosyanduKegiatan::where('rt_number', $rtStr)
            ->orWhere('rt_number', (string)intval($rtStr))
            ->first();

        // Jadwal Terdekat di RT Warga
        $nextEvent = PosyanduJadwal::where('rt_number', $rtStr)
            ->where('status', 'akan_datang')
            ->orderBy('tanggal', 'asc')
            ->first();

        // Jadwal Riwayat di RT Warga
        $pastEvents = PosyanduJadwal::where('rt_number', $rtStr)
            ->where('status', 'selesai')
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        // Anggota Keluarga yang Terdaftar Posyandu
        $familyUserIds = [$user->id];
        if ($user->kartu_keluarga_id) {
            $familyMemberIds = User::where('kartu_keluarga_id', $user->kartu_keluarga_id)->pluck('id')->toArray();
            $familyUserIds = array_unique(array_merge($familyUserIds, $familyMemberIds));
        }

        $myFamilyPeserta = PosyanduPeserta::with(['user', 'pengukurans' => function ($q) {
            $q->orderBy('tanggal_pemeriksaan', 'desc');
        }])
        ->whereIn('user_id', $familyUserIds)
        ->where('status_aktif', true)
        ->get();

        // Anggota Keluarga yang Belum Terdaftar (untuk opsi Daftarkan Keluarga)
        $unregisteredFamilyMembers = User::whereIn('id', $familyUserIds)
            ->whereNotIn('id', PosyanduPeserta::whereIn('user_id', $familyUserIds)->where('status_aktif', true)->pluck('user_id'))
            ->get();

        // Smart Auto-Detection: Balita (0-5 th / < 60 bulan) & Lansia (>= 60 th) dalam KK
        $autoDetectedFamily = [];
        foreach ($unregisteredFamilyMembers as $member) {
            if (!$member->tanggal_lahir) continue;
            $dob = Carbon::parse($member->tanggal_lahir);
            $diffMonths = (int) $dob->diffInMonths(Carbon::now());
            $diffYears = (int) $dob->age;

            if ($diffMonths < 60) {
                $category = $diffMonths < 12 ? 'bayi' : 'balita';
                $autoDetectedFamily[] = [
                    'user' => $member,
                    'kategori' => $category,
                    'kategori_label' => $diffMonths < 12 ? 'Bayi (0-11 Bln)' : 'Balita (1-5 Thn)',
                    'usia_text' => "{$diffMonths} Bulan",
                    'badge_class' => 'bg-info-subtle text-info border border-info-subtle',
                    'icon' => 'baby',
                ];
            } elseif ($diffYears >= 60) {
                $autoDetectedFamily[] = [
                    'user' => $member,
                    'kategori' => 'lansia',
                    'kategori_label' => 'Lansia (60+ Thn)',
                    'usia_text' => "{$diffYears} Tahun",
                    'badge_class' => 'bg-amber-subtle text-warning-emphasis border border-warning-subtle',
                    'icon' => 'smile',
                ];
            }
        }

        // Riwayat Pengukuran Anggota Keluarga
        $pesertaIds = $myFamilyPeserta->pluck('id')->toArray();
        $riwayatPengukuran = PosyanduPengukuran::with(['peserta.user', 'jadwal'])
            ->whereIn('peserta_id', $pesertaIds)
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->limit(15)
            ->get();

        // Kader Pengurus RT
        $kaders = PosyanduKader::where('rt_number', $rtStr)->where('is_aktif', true)->get();

        return view('posyandu.index_warga', compact(
            'user',
            'rtStr',
            'posyanduUnit',
            'nextEvent',
            'pastEvents',
            'myFamilyPeserta',
            'unregisteredFamilyMembers',
            'autoDetectedFamily',
            'riwayatPengukuran',
            'kaders'
        ));
    }

    /**
     * RT: Simpan Pendaftaran Peserta Baru dari Database Warga
     */
    public function storePeserta(Request $request)
    {
        $user = Auth::user();
        if (!$user->isRt() && !$user->isRw()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus RT/RW.');
        }

        $rtStr = $this->getCleanRt($user->rt_number);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'kategori' => 'required|in:bayi,balita,anak,ibu_hamil,ibu_menyusui,lansia',
            'golongan_darah' => 'nullable|string|max:5',
            'riwayat_alergi' => 'nullable|string|max:500',
            'catatan_khusus' => 'nullable|string|max:500',
        ]);

        $calonPeserta = User::findOrFail($validated['user_id']);

        // Validasi: Warga harus berasal dari RT yang sama
        $wargaRt = $this->getCleanRt($calonPeserta->rt_number);
        if ($user->isRt() && $wargaRt !== $rtStr) {
            abort(403, "Warga ini tercatat di RT {$wargaRt}. Anda hanya dapat mengelola peserta di RT {$rtStr}.");
        }

        // Cek duplikasi peserta aktif
        $exists = PosyanduPeserta::where('user_id', $calonPeserta->id)
            ->where('status_aktif', true)
            ->first();

        if ($exists) {
            return redirect()->back()->with('warning', 'Warga ini sudah terdaftar aktif sebagai peserta Posyandu.');
        }

        $peserta = PosyanduPeserta::create([
            'user_id' => $calonPeserta->id,
            'kartu_keluarga_id' => $calonPeserta->kartu_keluarga_id,
            'rt_number' => $wargaRt,
            'kategori' => $validated['kategori'],
            'status_aktif' => true,
            'golongan_darah' => $validated['golongan_darah'] ?? null,
            'riwayat_alergi' => $validated['riwayat_alergi'] ?? null,
            'catatan_khusus' => $validated['catatan_khusus'] ?? null,
            'tanggal_bergabung' => Carbon::now()->toDateString(),
        ]);

        ActivityLog::record(
            'posyandu',
            'posyandu.peserta_added',
            "Mendaftarkan peserta posyandu: {$calonPeserta->name} (RT {$wargaRt})",
            [
                'peserta_id' => $peserta->id,
                'user_id' => $calonPeserta->id,
                'kategori' => $peserta->kategori,
                'rt_number' => $wargaRt,
                'registered_by' => $user->name,
            ]
        );

        return redirect()->back()->with('success', "Peserta {$calonPeserta->name} berhasil didaftarkan ke Posyandu RT {$wargaRt}.");
    }

    /**
     * Lihat Profil Detail & Riwayat Peserta (Privacy-Protected)
     */
    public function showPeserta($id)
    {
        $user = Auth::user();
        $peserta = PosyanduPeserta::with(['user', 'kartuKeluarga', 'pengukurans.petugasPencatat', 'kehadirans.jadwal'])->findOrFail($id);

        // Otorisasi: Warga hanya boleh melihat keluarganya sendiri
        if ($user->isWarga()) {
            $isFamily = ($user->id === $peserta->user_id) || 
                        ($user->kartu_keluarga_id && $user->kartu_keluarga_id === $peserta->kartu_keluarga_id);
            if (!$isFamily) {
                abort(403, 'Anda tidak memiliki hak untuk melihat catatan kesehatan warga lain.');
            }
        } elseif ($user->isRt()) {
            $rtStr = $this->getCleanRt($user->rt_number);
            if ($peserta->rt_number !== $rtStr) {
                abort(403, 'Anda hanya dapat mengakses profil peserta dari RT Anda sendiri.');
            }
        }

        // Jadwal aktif di RT peserta untuk form input pengukuran
        $jadwalAktif = PosyanduJadwal::where('rt_number', $peserta->rt_number)
            ->whereIn('status', ['akan_datang', 'sedang_berlangsung'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('posyandu.show_peserta', compact('user', 'peserta', 'jadwalAktif'));
    }

    /**
     * RT: Buat Jadwal Kegiatan Posyandu Baru
     */
    public function storeJadwal(Request $request)
    {
        $user = Auth::user();
        if (!$user->isRt() && !$user->isRw()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus RT/RW.');
        }

        $rtStr = $this->getCleanRt($user->rt_number);

        $validated = $request->validate([
            'judul_kegiatan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|string',
            'jam_selesai' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'jenis_layanan_list' => 'required|array|min:1',
            'mitra_nakes' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $unit = PosyanduKegiatan::where('rt_number', $rtStr)->first();

        $jadwal = PosyanduJadwal::create([
            'posyandu_unit_id' => $unit ? $unit->id : null,
            'rt_number' => $rtStr,
            'judul_kegiatan' => $validated['judul_kegiatan'],
            'tanggal' => $validated['tanggal'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'lokasi' => $validated['lokasi'],
            'jenis_layanan_list' => $validated['jenis_layanan_list'],
            'mitra_nakes' => $validated['mitra_nakes'] ?? 'UPTD Puskesmas Arcamanik Kota Bandung',
            'status' => 'akan_datang',
            'keterangan' => $validated['keterangan'] ?? null,
            'created_by' => $user->id,
        ]);

        ActivityLog::record(
            'posyandu',
            'posyandu.schedule_created',
            "Membuat jadwal posyandu RT {$rtStr}: {$jadwal->judul_kegiatan} ({$jadwal->tanggal})",
            [
                'jadwal_id' => $jadwal->id,
                'rt_number' => $rtStr,
                'created_by' => $user->name,
            ]
        );

        return redirect()->back()->with('success', 'Jadwal kegiatan Posyandu berhasil dibuat.');
    }

    /**
     * RT: Batalkan / Tunda Jadwal dengan Alasan Tercatat
     */
    public function batalJadwal(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isRt() && !$user->isRw()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus RT/RW.');
        }

        $jadwal = PosyanduJadwal::findOrFail($id);

        if ($user->isRt() && $this->getCleanRt($user->rt_number) !== $jadwal->rt_number) {
            abort(403, 'Anda tidak berhak mengubah jadwal RT lain.');
        }

        $validated = $request->validate([
            'status' => 'required|in:ditunda,dibatalkan,selesai',
            'alasan_batal' => 'nullable|string|max:500',
        ]);

        $jadwal->update([
            'status' => $validated['status'],
            'alasan_batal' => $validated['alasan_batal'] ?? null,
        ]);

        return redirect()->back()->with('success', "Status jadwal berhasil diperbarui menjadi {$jadwal->status_label}.");
    }

    /**
     * RT: Catat Kehadiran Peserta pada Kegiatan
     */
    public function catatKehadiran(Request $request, $jadwalId)
    {
        $user = Auth::user();
        if (!$user->isRt() && !$user->isRw()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus RT/RW.');
        }

        $jadwal = PosyanduJadwal::findOrFail($jadwalId);

        if ($user->isRt() && $this->getCleanRt($user->rt_number) !== $jadwal->rt_number) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'peserta_id' => 'required|exists:posyandu_pesertas,id',
            'status_kehadiran' => 'required|in:hadir,tidak_hadir,izin',
            'jam_datang' => 'nullable|string',
            'catatan_kunjungan' => 'nullable|string|max:500',
        ]);

        PosyanduKehadiran::updateOrCreate(
            [
                'jadwal_id' => $jadwal->id,
                'peserta_id' => $validated['peserta_id'],
            ],
            [
                'rt_number' => $jadwal->rt_number,
                'status_kehadiran' => $validated['status_kehadiran'],
                'jam_datang' => $validated['jam_datang'] ?? Carbon::now()->format('H:i'),
                'petugas_id' => $user->id,
                'catatan_kunjungan' => $validated['catatan_kunjungan'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Catatan kehadiran peserta berhasil disimpan.');
    }

    /**
     * RT: Simpan Hasil Pengukuran Medis/Fisik
     */
    public function storePengukuran(Request $request, $pesertaId)
    {
        $user = Auth::user();
        if (!$user->isRt() && !$user->isRw()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus RT/RW.');
        }

        $peserta = PosyanduPeserta::findOrFail($pesertaId);

        if ($user->isRt() && $this->getCleanRt($user->rt_number) !== $peserta->rt_number) {
            abort(403, 'Anda hanya berhak menginput pengukuran untuk RT Anda sendiri.');
        }

        $validated = $request->validate([
            'jadwal_id' => 'nullable|exists:posyandu_jadwals,id',
            'tanggal_pemeriksaan' => 'required|date',
            'berat_badan' => 'nullable|numeric|min:0.5|max:200',
            'tinggi_badan' => 'nullable|numeric|min:20|max:250',
            'lingkar_kepala' => 'nullable|numeric|min:20|max:80',
            'usia_kehamilan_minggu' => 'nullable|integer|min:1|max:44',
            'tekanan_darah_sistol' => 'nullable|integer|min:50|max:250',
            'tekanan_darah_diastol' => 'nullable|integer|min:30|max:150',
            'keluhan' => 'nullable|string|max:500',
            'status_pertumbuhan' => 'nullable|string|max:255',
            'tindakan_layanan' => 'nullable|string|max:500',
            'catatan_petugas' => 'nullable|string|max:500',
            'anjuran_rujukan' => 'nullable|string|max:500',
        ]);

        $pengukuran = PosyanduPengukuran::create([
            'peserta_id' => $peserta->id,
            'jadwal_id' => $validated['jadwal_id'] ?? null,
            'rt_number' => $peserta->rt_number,
            'tanggal_pemeriksaan' => $validated['tanggal_pemeriksaan'],
            'berat_badan' => $validated['berat_badan'] ?? null,
            'tinggi_badan' => $validated['tinggi_badan'] ?? null,
            'lingkar_kepala' => $validated['lingkar_kepala'] ?? null,
            'usia_kehamilan_minggu' => $validated['usia_kehamilan_minggu'] ?? null,
            'tekanan_darah_sistol' => $validated['tekanan_darah_sistol'] ?? null,
            'tekanan_darah_diastol' => $validated['tekanan_darah_diastol'] ?? null,
            'keluhan' => $validated['keluhan'] ?? null,
            'status_pertumbuhan' => $validated['status_pertumbuhan'] ?? 'Pengukuran Terverifikasi (Kader & Nakes Puskesmas)',
            'tindakan_layanan' => $validated['tindakan_layanan'] ?? null,
            'catatan_petugas' => $validated['catatan_petugas'] ?? null,
            'anjuran_rujukan' => $validated['anjuran_rujukan'] ?? null,
            'petugas_pencatat_id' => $user->id,
        ]);

        ActivityLog::record(
            'posyandu',
            'posyandu.measurement_recorded',
            "Mencatat pengukuran fisik peserta {$peserta->user->name} (RT {$peserta->rt_number})",
            [
                'peserta_id' => $peserta->id,
                'rt_number' => $peserta->rt_number,
                'berat_badan' => $validated['berat_badan'] ?? null,
                'tinggi_badan' => $validated['tinggi_badan'] ?? null,
                'petugas' => $user->name,
            ]
        );

        return redirect()->back()->with('success', "Pengukuran untuk {$peserta->user->name} berhasil dicatat.");
    }

    /**
     * RT: Simpan / Kirim Laporan Kegiatan Posyandu
     */
    public function storeLaporan(Request $request, $jadwalId)
    {
        $user = Auth::user();
        if (!$user->isRt() && !$user->isRw()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus RT/RW.');
        }

        $jadwal = PosyanduJadwal::findOrFail($jadwalId);

        if ($user->isRt() && $this->getCleanRt($user->rt_number) !== $jadwal->rt_number) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'ringkasan_layanan' => 'required|string|min:10',
            'kendala' => 'nullable|string|max:1000',
            'catatan_evaluasi_rt' => 'nullable|string|max:1000',
            'foto_dokumentasi' => 'nullable|image|max:3072',
        ]);

        // Hitung agregat kehadiran & kategori
        $kehadirans = PosyanduKehadiran::where('jadwal_id', $jadwal->id)->where('status_kehadiran', 'hadir')->get();
        $totalHadir = $kehadirans->count();

        $pesertaIds = $kehadirans->pluck('peserta_id')->toArray();
        $pesertas = PosyanduPeserta::whereIn('id', $pesertaIds)->get();

        $totalBalita = $pesertas->whereIn('kategori', ['bayi', 'balita', 'anak'])->count();
        $totalIbuHamil = $pesertas->whereIn('kategori', ['ibu_hamil', 'ibu_menyusui'])->count();
        $totalLansia = $pesertas->where('kategori', 'lansia')->count();

        $fotoPath = null;
        if ($request->hasFile('foto_dokumentasi')) {
            $fotoPath = $request->file('foto_dokumentasi')->store('posyandu/dokumentasi', 'public');
        }

        $nomorLaporan = 'LAP-POS-' . $jadwal->rt_number . '-' . Carbon::now()->format('Ym') . '-' . sprintf('%02d', rand(1, 99));

        $laporan = PosyanduLaporan::updateOrCreate(
            ['jadwal_id' => $jadwal->id],
            [
                'rt_number' => $jadwal->rt_number,
                'nomor_laporan' => $nomorLaporan,
                'tanggal_laporan' => Carbon::now()->toDateString(),
                'total_hadir' => $totalHadir,
                'total_bayi_balita' => $totalBalita,
                'total_ibu_hamil' => $totalIbuHamil,
                'total_lansia' => $totalLansia,
                'ringkasan_layanan' => $validated['ringkasan_layanan'],
                'kendala' => $validated['kendala'] ?? null,
                'catatan_evaluasi_rt' => $validated['catatan_evaluasi_rt'] ?? null,
                'foto_dokumentasi_path' => $fotoPath,
                'penanggung_jawab_id' => $user->id,
                'status_laporan' => 'dikirim_ke_rw',
            ]
        );

        // Update status jadwal menjadi selesai
        $jadwal->update(['status' => 'selesai']);

        ActivityLog::record(
            'posyandu',
            'posyandu.report_submitted',
            "Mengirimkan laporan kegiatan posyandu RT {$jadwal->rt_number}: {$jadwal->judul_kegiatan}",
            [
                'jadwal_id' => $jadwal->id,
                'rt_number' => $jadwal->rt_number,
                'nomor_laporan' => $nomorLaporan,
                'total_hadir' => $totalHadir,
                'penanggung_jawab' => $user->name,
            ]
        );

        return redirect()->back()->with('success', "Laporan kegiatan Posyandu berhasil disimpan dan dikirimkan ke RW 013.");
    }

    /**
     * Lihat Detail Laporan Kegiatan (RW & RT)
     */
    public function showLaporan($id)
    {
        $user = Auth::user();
        $laporan = PosyanduLaporan::with(['jadwal.unit', 'penanggungJawab', 'evaluatorRw'])->findOrFail($id);

        if ($user->isRt() && $this->getCleanRt($user->rt_number) !== $laporan->rt_number) {
            abort(403, 'Anda hanya dapat melihat laporan RT Anda sendiri.');
        }

        return view('posyandu.show_laporan', compact('user', 'laporan'));
    }

    /**
     * Cetak Resmi Laporan Kegiatan Posyandu RT (Kop Surat Resmi RW 013)
     */
    public function cetakLaporan($id)
    {
        $user = Auth::user();
        $laporan = PosyanduLaporan::with(['jadwal.unit', 'penanggungJawab', 'evaluatorRw'])->findOrFail($id);

        if ($user->isRt() && $this->getCleanRt($user->rt_number) !== $laporan->rt_number) {
            abort(403, 'Akses ditolak.');
        }

        return view('posyandu.cetak_laporan', compact('user', 'laporan'));
    }

    /**
     * RW/Admin: Berikan Catatan Evaluasi & Persetujuan Laporan Kegiatan RT
     */
    public function evaluasiLaporan(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isRw()) {
            abort(403, 'Hanya pengurus RW yang berhak memberikan evaluasi laporan.');
        }

        $laporan = PosyanduLaporan::findOrFail($id);

        $validated = $request->validate([
            'status_laporan' => 'required|in:disetujui_rw,dievaluasi_rw',
            'catatan_rw' => 'required|string|min:5|max:1000',
        ]);

        $laporan->update([
            'status_laporan' => $validated['status_laporan'],
            'catatan_rw' => $validated['catatan_rw'],
            'tanggal_evaluasi_rw' => Carbon::now(),
            'evaluator_rw_id' => $user->id,
        ]);

        ActivityLog::record(
            'posyandu',
            'posyandu.report_evaluated',
            "Mengevaluasi laporan kegiatan posyandu RT {$laporan->rt_number} ({$validated['status_laporan']})",
            [
                'laporan_id' => $laporan->id,
                'rt_number' => $laporan->rt_number,
                'status' => $validated['status_laporan'],
                'evaluator' => $user->name,
            ]
        );

        return redirect()->back()->with('success', 'Catatan evaluasi RW berhasil disimpan.');
    }

    /**
     * RW/Admin: Kirim Pengingat Laporan ke RT
     */
    public function kirimPengingat(Request $request, $rtNumber)
    {
        $user = Auth::user();
        if (!$user->isRw()) {
            abort(403, 'Hanya pengurus RW yang berhak mengirimkan pengingat laporan.');
        }

        $cleanRt = $this->getCleanRt($rtNumber);
        
        ActivityLog::record(
            'posyandu',
            'posyandu.reminder_sent',
            "Pengurus RW mengirimkan pengingat pelaporan kegiatan Posyandu ke Ketua RT {$cleanRt}",
            [
                'target_rt' => $cleanRt,
                'sender' => $user->name,
                'sent_at' => Carbon::now()->toDateTimeString(),
            ]
        );

        return redirect()->back()->with('success', "Notifikasi pengingat pengiriman laporan Posyandu berhasil diteruskan ke Pengurus RT {$cleanRt}.");
    }

    /**
     * RT: Tambah Kader Posyandu Baru
     */
    public function storeKader(Request $request)
    {
        $user = Auth::user();
        if (!$user->isRt() && !$user->isRw()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk pengurus RT/RW.');
        }

        $rtStr = $this->getCleanRt($user->rt_number);

        $validated = $request->validate([
            'nama_kader' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:25',
            'jabatan' => 'required|string|max:100',
            'catatan' => 'nullable|string|max:255',
        ]);

        PosyanduKader::create([
            'rt_number' => $rtStr,
            'nama_kader' => $validated['nama_kader'],
            'no_telepon' => $validated['no_telepon'] ?? null,
            'jabatan' => $validated['jabatan'],
            'is_aktif' => true,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Data kader Posyandu berhasil ditambahkan.');
    }

    /**
     * Warga: Pengajuan Pendaftaran Anggota Keluarga ke Posyandu RT
     */
    public function daftarKeluarga(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'kategori' => 'required|in:bayi,balita,anak,ibu_hamil,ibu_menyusui,lansia',
            'catatan_khusus' => 'nullable|string|max:500',
        ]);

        $calon = User::findOrFail($validated['user_id']);

        // Verifikasi hubungan keluarga
        $isFamily = ($user->id === $calon->id) || 
                    ($user->kartu_keluarga_id && $user->kartu_keluarga_id === $calon->kartu_keluarga_id);

        if (!$isFamily) {
            return redirect()->back()->with('error', 'Anda hanya dapat mendaftarkan anggota keluarga Anda sendiri.');
        }

        $rtStr = $this->getCleanRt($calon->rt_number ?: $user->rt_number);

        $peserta = PosyanduPeserta::firstOrCreate(
            ['user_id' => $calon->id],
            [
                'kartu_keluarga_id' => $calon->kartu_keluarga_id,
                'rt_number' => $rtStr,
                'kategori' => $validated['kategori'],
                'status_aktif' => true,
                'catatan_khusus' => $validated['catatan_khusus'] ?? null,
                'tanggal_bergabung' => Carbon::now()->toDateString(),
            ]
        );

        ActivityLog::record(
            'posyandu',
            'posyandu.family_registered',
            "Warga mendaftarkan anggota keluarga ke Posyandu: {$calon->name} (RT {$rtStr})",
            [
                'peserta_id' => $peserta->id,
                'calon_id' => $calon->id,
                'kategori' => $validated['kategori'],
                'rt_number' => $rtStr,
                'registered_by' => $user->name,
            ]
        );

        return redirect()->back()->with('success', "Anggota keluarga {$calon->name} berhasil didaftarkan ke Posyandu RT {$rtStr}.");
    }

    /**
     * API: Pencarian Warga RT untuk Pemilihan Peserta Cepat
     */
    public function searchWarga(Request $request)
    {
        $user = Auth::user();
        $query = $request->query('q', '');
        $rtStr = $this->getCleanRt($user->rt_number);

        $wargas = User::where('role', 'warga')
            ->where(function ($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)
                  ->orWhere('rt_number', (string)intval($rtStr));
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('nik', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'nik', 'gender', 'tanggal_lahir']);

        return response()->json($wargas);
    }
}
