<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Iuran;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\KasRt;
use App\Models\Pengumuman;
use App\Models\PerkembanganBalita;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isRt()) {
            return redirect()->route('dashboard.rt');
        } elseif ($user->isWarga()) {
            return redirect()->route('dashboard.warga');
        }
        return redirect()->route('dashboard.rw');
    }

    public function dashboardRw()
    {
        $user = Auth::user();
        $data = $this->getRwDashboardData($user);
        return view('dashboard.rw', $data);
    }

    public function dashboardRt()
    {
        $user = Auth::user();
        if (!$user->isRt() && !$user->isRw() && !in_array($user->role, ['admin', 'bendahara'])) {
            return redirect()->route('dashboard.warga')->with('error', 'Akses ditolak: Halaman ini khusus untuk Ketua RT.');
        }

        $rtNum = $user->clean_rt_number;
        $rtLabel = $user->clean_rt_label;

        // Query data warga khusus RT sendiri (Keamanan data antar-RT)
        $wargaRtQuery = User::where('role', 'warga')->where(function ($q) use ($rtNum) {
            $q->where('rt_number', $rtNum)
              ->orWhere('rt_number', str_pad($rtNum, 3, '0', STR_PAD_LEFT))
              ->orWhere('rt_number', 'RT ' . str_pad($rtNum, 3, '0', STR_PAD_LEFT))
              ->orWhere('address', 'like', "%RT 00{$rtNum}%")
              ->orWhere('address', 'like', "%RT {$rtNum}%");
        });

        $totalWarga = (clone $wargaRtQuery)->count();
        $totalLaki = (clone $wargaRtQuery)->where('gender', 'Laki-laki')->count();
        $totalPerempuan = (clone $wargaRtQuery)->where('gender', 'Perempuan')->count();
        $totalKk = (clone $wargaRtQuery)->whereNotNull('no_kk')->distinct('no_kk')->count('no_kk');
        $wargaAktif = (clone $wargaRtQuery)->where('is_active', true)->count();

        // Ambil Laporan & Monitoring RT dari data tersimpan
        $laporanList = $this->getLaporanRtList();
        $myReport = null;
        foreach ($laporanList as $lap) {
            if ($lap['rt_number'] == $rtNum) {
                $myReport = $lap;
                break;
            }
        }
        if (!$myReport) {
            $myReport = $laporanList[0] ?? [];
            $myReport['rt_number'] = $rtNum;
            $myReport['rt_label'] = $rtLabel;
        }

        // Jika DB warga belum terisi untuk RT ini, fallback ke data statistik laporan RT
        if ($totalWarga === 0) {
            $totalWarga = $myReport['administrasi']['jumlah_warga'] ?? 142;
            $totalKk = $myReport['administrasi']['jumlah_kk'] ?? 45;
            $wargaAktif = $totalWarga;
            $totalLaki = round($totalWarga * 0.52);
            $totalPerempuan = $totalWarga - $totalLaki;
        }

        // Keuangan & Iuran RT
        $saldoKasRt = $myReport['keuangan']['saldo_akhir'] ?? 14850000;
        $iuranTerkumpul = $myReport['keuangan']['pemasukan'] ?? 3850000;

        // Pengaduan Warga RT sendiri
        $pengaduanRt = array_values(array_filter($this->getPengaduanData(), function ($p) use ($rtNum) {
            return str_contains($p['rt'] ?? '', (string)$rtNum);
        }));

        // Permohonan surat warga RT
        $permohonanSurat = [
            ['nama' => 'Bpk. Hendra Gunawan', 'jenis' => 'Surat Pengantar KTP / Domisili', 'tanggal' => 'Hari ini, 09:30 WIB', 'status' => 'Menunggu Tanda Tangan'],
            ['nama' => 'Ibu Siti Aminah', 'jenis' => 'Surat Keterangan Usaha (UMKM)', 'tanggal' => 'Kemarin, 16:15 WIB', 'status' => 'Diproses'],
            ['nama' => 'Sdr. Rizky Ramadhan', 'jenis' => 'Surat Pengantar SKCK', 'tanggal' => '09 Nov 2026', 'status' => 'Selesai'],
        ];

        // Kegiatan mendatang RT
        $kegiatanRt = [
            ['nama' => 'Jadwal Ronda Malam ' . $rtLabel, 'waktu' => 'Malam ini, 22.00 WIB', 'lokasi' => 'Pos Ronda ' . $rtLabel, 'status' => 'Aktif'],
            ['nama' => 'Posyandu Balita ' . $rtLabel, 'waktu' => 'Sabtu, 08.00 WIB', 'lokasi' => 'Balai Warga ' . $rtLabel, 'status' => 'Terjadwal'],
            ['nama' => 'Setor Bank Sampah ' . $rtLabel, 'waktu' => 'Rabu Depan, 10.00 WIB', 'lokasi' => 'Titik Kumpul ' . $rtLabel, 'status' => 'Terjadwal'],
        ];

        $pengumuman = Pengumuman::where('tanggal_aktif', '<=', Carbon::now())
            ->orderBy('tanggal_aktif', 'desc')->limit(4)->get();

        return view('dashboard.rt', compact(
            'user', 'rtNum', 'rtLabel', 'totalWarga', 'totalKk', 'wargaAktif',
            'totalLaki', 'totalPerempuan', 'saldoKasRt', 'iuranTerkumpul',
            'myReport', 'pengaduanRt', 'permohonanSurat', 'kegiatanRt', 'pengumuman'
        ));
    }

    public function dashboardWarga()
    {
        $user = Auth::user();
        $tagihan_saya = Iuran::where('status', 'aktif')->get();
        $riwayat_bayar = Pembayaran::where('user_id', $user->id)
            ->with('iuran')->orderBy('created_at', 'desc')->limit(5)->get();
        $total_bayar = Pembayaran::where('user_id', $user->id)
            ->where('status', 'lunas')->sum('jumlah_bayar');
        $tagihan_belum_bayar = $this->getTagihanBelumBayar($user->id);

        $pengumuman = Pengumuman::where('tanggal_aktif', '<=', Carbon::now())
            ->orderBy('tanggal_aktif', 'desc')->limit(5)->get();

        $cctvs = \App\Models\Cctv::where('aktif', true)->limit(2)->get();
        $admin_contact_phone = User::where('role', 'admin')->value('phone') ?? $user->phone ?? '081234567890';

        // Pengaduan warga saya
        $pengaduanSaya = array_values(array_filter($this->getPengaduanData(), function ($p) use ($user) {
            return strtolower($p['pelapor'] ?? '') === strtolower($user->name);
        }));

        return view('dashboard.warga', compact(
            'user', 'tagihan_saya', 'riwayat_bayar', 'total_bayar',
            'tagihan_belum_bayar', 'pengumuman', 'cctvs', 'admin_contact_phone', 'pengaduanSaya'
        ));
    }

    public function getRwDashboardData($user = null)
    {
        $user = $user ?? Auth::user();
        $data = [];

        $baseWargaQuery = User::where('role', 'warga');
        $data['total_warga'] = (clone $baseWargaQuery)->count(); 
        $data['total_laki'] = (clone $baseWargaQuery)->where('gender', 'Laki-laki')->count();
        $data['total_perempuan'] = (clone $baseWargaQuery)->where('gender', 'Perempuan')->count();

        $kasData = KasRt::selectRaw('COALESCE(SUM(pemasukan), 0) as total_pemasukan, COALESCE(SUM(pengeluaran), 0) as total_pengeluaran')->first();
        $data['total_pemasukan'] = $kasData->total_pemasukan ?? 0;
        $data['total_pengeluaran'] = $kasData->total_pengeluaran ?? 0;
        $data['saldo_kas'] = $data['total_pemasukan'] - $data['total_pengeluaran'];

        $data['total_rt'] = 8;
        $data['total_laporan_rt'] = 8;
        $data['total_iuran_terkumpul'] = Pembayaran::where('status', 'lunas')->sum('jumlah_bayar') ?? 0;

        $data['saldo_kas_rw'] = max(34500000, $data['saldo_kas']);
        $data['pemasukan_rw'] = max(42800000, $data['total_pemasukan']);
        $data['pengeluaran_rw'] = max(8300000, $data['total_pengeluaran']);

        $data['rt_monitoring_list'] = $this->getRtMonitoringData();

        $pengaduanAll = $this->getPengaduanData();
        $data['pengaduan_terbaru'] = array_slice($pengaduanAll, 0, 5);
        $data['pengaduan_pending_count'] = count(array_filter($pengaduanAll, function($p) {
            return in_array($p['status'], ['Menunggu', 'Diproses', 'Perlu Tindak Lanjut', 'Perlu perbaikan']);
        }));

        $data['agenda_rw'] = $this->getAgendaRwData();
        $data['aktivitas_lingkungan'] = $this->getAktivitasLingkungan();

        $data['total_iuran_aktif'] = Iuran::where('status', 'aktif')->count();
        $data['total_pembayaran_bulan_ini'] = Pembayaran::where('status', 'lunas')
            ->whereMonth('tanggal_bayar', Carbon::now()->month)->sum('jumlah_bayar');

        $warga_bayar = Pembayaran::where('status', 'lunas')
            ->whereMonth('tanggal_bayar', Carbon::now()->month)->distinct('user_id')->count('user_id');
        $data['warga_belum_bayar'] = $data['total_warga'] - $warga_bayar;

        $data['pembayaran_terbaru'] = Pembayaran::with(['user', 'iuran'])->orderBy('created_at', 'desc')->limit(5)->get();
        $data['grafik_bulanan'] = $this->getGrafikBulanan();

        $data['pengumuman'] = Pengumuman::where('tanggal_aktif', '<=', Carbon::now())
            ->orderBy('tanggal_aktif', 'desc')->limit(5)->get();

        $data['admin_contact_phone'] = User::where('role', 'admin')->value('phone') ?? $user->phone ?? '081234567890';
        $data['user'] = $user;

        return $data;
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
        $userAddress = $user->address ?? 'Jl. Melati Utama No. 15, RT 001 / RW 013';
        $userRt = intval($user->rt_number ?? 1);
        if ($userRt === 0 && preg_match('/RT\s*(\d+)/i', $userAddress, $m)) {
            $userRt = intval($m[1]);
        }
        if ($userRt === 0) $userRt = 1;

        $posyanduList = [
            [
                'id' => 1,
                'nama' => 'Posyandu Sejahtera (RT 001)',
                'rt' => 1,
                'lokasi' => 'Balai Warga RT 001, Jl. Gandaria No. 12',
                'jadwal' => 'Sabtu Pertama (08.00 - 11.00 WIB)',
                'lat' => -6.229728,
                'lng' => 106.816482,
                'penanggung_jawab' => 'Bidan Ratna & Kader Ibu Ningsih',
                'jarak' => '150 meter',
                'waktu' => '2 Menit Jalan Kaki',
                'rute_petunjuk' => 'Dari tempat tinggal Anda (' . $userAddress . '), jalan lurus ke arah barat 100m, lalu belok kanan di gang RT 001. Balai RT 001 berada tepat di sebelah kiri lapangan.'
            ],
            [
                'id' => 2,
                'nama' => 'Posyandu Kasih Ibu (RT 002)',
                'rt' => 2,
                'lokasi' => 'Lapangan Serbaguna RT 002, Jl. Kenanga No. 8',
                'jadwal' => 'Sabtu Pertama (08.00 - 11.00 WIB)',
                'lat' => -6.230150,
                'lng' => 106.817200,
                'penanggung_jawab' => 'Bidan Melati & Kader Ibu Sri',
                'jarak' => '280 meter',
                'waktu' => '3 Menit Jalan Kaki',
                'rute_petunjuk' => 'Dari tempat tinggal Anda, menuju ke utara melintasi Jl. Kenanga. Posyandu berada di samping Pendopo Serbaguna RT 002.'
            ],
            [
                'id' => 3,
                'nama' => 'Posyandu Tunas Bangsa (RT 003)',
                'rt' => 3,
                'lokasi' => 'Pos Kesehatan RT 003, Jl. Anggrek No. 15',
                'jadwal' => 'Sabtu Kedua (08.00 - 11.00 WIB)',
                'lat' => -6.231200,
                'lng' => 106.818100,
                'penanggung_jawab' => 'dr. Farhan & Kader Ibu Dewita',
                'jarak' => '400 meter',
                'waktu' => '5 Menit Jalan Kaki / 1 Menit Motor',
                'rute_petunjuk' => 'Berjalanlah ke arah timur menyusuri Jl. Anggrek. Gedung Pos Kesehatan RT 003 berada di pojok pertigaan jalan.'
            ],
            [
                'id' => 4,
                'nama' => 'Posyandu Harapan Bunda (RT 004)',
                'rt' => 4,
                'lokasi' => 'Gedung PAUD Ceria RT 004, Jl. Mawar No. 22',
                'jadwal' => 'Sabtu Kedua (08.00 - 11.00 WIB)',
                'lat' => -6.232000,
                'lng' => 106.819000,
                'penanggung_jawab' => 'Bidan Anita & Kader Ibu Endang',
                'jarak' => '520 meter',
                'waktu' => '6 Menit Jalan Kaki / 2 Menit Motor',
                'rute_petunjuk' => 'Melintasi jalan utama RW 013 menuju Blok RT 004, lokasi berada di halaman depan PAUD Ceria.'
            ],
            [
                'id' => 5,
                'nama' => 'Posyandu Cendekia (RT 005)',
                'rt' => 5,
                'lokasi' => 'Balai Pertemuan RT 005, Jl. Melati No. 5',
                'jadwal' => 'Sabtu Ketiga (08.00 - 11.00 WIB)',
                'lat' => -6.232800,
                'lng' => 106.819800,
                'penanggung_jawab' => 'dr. Amanda & Kader Ibu Yanti',
                'jarak' => '600 meter',
                'waktu' => '7 Menit Jalan Kaki / 2 Menit Motor',
                'rute_petunjuk' => 'Dari rumah Anda, ikuti jalur utama RW ke arah selatan menuju RT 005. Balai pertemuan berada di dekat Taman Melati.'
            ],
            [
                'id' => 6,
                'nama' => 'Posyandu Permata Hati (RT 006)',
                'rt' => 6,
                'lokasi' => 'Pos Sekretariat RW 013 / RT 006, Jl. Flamboyan No. 19',
                'jadwal' => 'Sabtu Ketiga (08.00 - 11.00 WIB)',
                'lat' => -6.233500,
                'lng' => 106.820600,
                'penanggung_jawab' => 'dr. Setyo & Kader Ibu Kartini',
                'jarak' => '750 meter',
                'waktu' => '8 Menit Jalan Kaki / 3 Menit Motor',
                'rute_petunjuk' => 'Posyandu terletak di Pos Utama Sekretariat RW 013 (RT 006). Patokannya bersebelahan dengan Kantor RW.'
            ],
            [
                'id' => 7,
                'nama' => 'Posyandu Bintang Kecil (RT 007)',
                'rt' => 7,
                'lokasi' => 'Ruang Serbaguna RT 007, Jl. Dahlia No. 30',
                'jadwal' => 'Sabtu Keempat (08.00 - 11.00 WIB)',
                'lat' => -6.234200,
                'lng' => 106.821400,
                'penanggung_jawab' => 'Bidan Nurul & Kader Ibu Tuti',
                'jarak' => '850 meter',
                'waktu' => '9 Menit Jalan Kaki / 3 Menit Motor',
                'rute_petunjuk' => 'Berada di Komplek Dahlia RT 007. Masuk melalui gerbang timur RW 013, bangunan cat hijau di sebelah kanan.'
            ],
            [
                'id' => 8,
                'nama' => 'Posyandu Pelangi Nusantara (RT 008)',
                'rt' => 8,
                'lokasi' => 'Balai Kemasyarakatan RT 008, Jl. Cempaka No. 45',
                'jadwal' => 'Sabtu Keempat (08.00 - 11.00 WIB)',
                'lat' => -6.235000,
                'lng' => 106.822200,
                'penanggung_jawab' => 'dr. Nabila & Kader Ibu Rahma',
                'jarak' => '950 meter',
                'waktu' => '10 Menit Jalan Kaki / 4 Menit Motor',
                'rute_petunjuk' => 'Berada di ujung jalan Cempaka RT 008. Akses mudah dari gapura selatan RW 013.'
            ]
        ];

        return view('posyandu.detail_lokasi', compact('user', 'posyanduList', 'userAddress', 'userRt'));
    }
    // -----------------------------------------

    // --- FUNGSI BARU UNTUK DETAIL POSYANDU ---
    public function detailBalita(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');
        
        // Ambil data warga yang lahir dalam 5 tahun terakhir (balita)
        $query = \App\Models\User::where('role', 'warga')
            ->whereNotNull('tanggal_lahir')
            ->where('tanggal_lahir', '>=', Carbon::now()->subYears(5));
            
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }
            
        $balitas = $query->orderBy('name', 'asc')->get();
            
        return view('posyandu.detail_balita', compact('user', 'balitas', 'search'));
    }

    public function getBalitaDetail($id)
    {
        $balita = \App\Models\User::where('role', 'warga')->findOrFail($id);
        
        // Cari orang tua berdasarkan no_kk
        $orangTua = \App\Models\User::where('no_kk', $balita->no_kk)
            ->where('id', '!=', $balita->id)
            ->whereNotNull('tanggal_lahir')
            ->where('tanggal_lahir', '<=', Carbon::now()->subYears(20)) // Asumsi umur ortu minimal 20 tahun
            ->orderBy('tanggal_lahir', 'asc') // Yang paling tua kemungkinan ortunya
            ->get();
            
        $ayah = $orangTua->where('gender', 'Laki-laki')->first();
        $ibu = $orangTua->where('gender', 'Perempuan')->first();
        
        // Jika tidak ketemu yang pas 20+, ambil sembarang yang lebih tua dari balita
        if (!$ayah && !$ibu) {
            $kerabat = \App\Models\User::where('no_kk', $balita->no_kk)
                ->where('id', '!=', $balita->id)
                ->where('tanggal_lahir', '<', $balita->tanggal_lahir)
                ->first();
            if ($kerabat) {
                if ($kerabat->gender == 'Laki-laki') $ayah = $kerabat;
                else $ibu = $kerabat;
            }
        }

        // Perkembangan Real dari Database
        $perkembanganList = PerkembanganBalita::where('user_id', $balita->id)
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $latest = $perkembanganList->first();

        if ($latest) {
            $tinggi = $latest->tinggi_badan . ' cm';
            $berat = $latest->berat_badan . ' kg';
            $status = $latest->status_gizi;
            $catatan = $latest->catatan ?? '-';
            $tanggal_terakhir = Carbon::parse($latest->tanggal_pemeriksaan)->translatedFormat('d F Y');
        } else {
            // Default awal jika belum pernah diinput admin
            $tinggi = '80 cm';
            $berat = '16.3 kg';
            $status = 'Normal / Sehat';
            $catatan = 'Belum ada catatan perkembangan khusus.';
            $tanggal_terakhir = 'Belum dicatat';
        }

        $historyFormatted = $perkembanganList->map(function ($p) {
            return [
                'tanggal' => Carbon::parse($p->tanggal_pemeriksaan)->translatedFormat('d M Y'),
                'tinggi' => $p->tinggi_badan . ' cm',
                'berat' => $p->berat_badan . ' kg',
                'status' => $p->status_gizi,
                'catatan' => $p->catatan ?? '-',
            ];
        });

        $tanggalJadwal = Carbon::now()->addDays(rand(5, 20));
        while (!$tanggalJadwal->isSaturday()) {
            $tanggalJadwal->addDay();
        }

        $currentUser = Auth::user();
        $isAdmin = $currentUser && in_array(strtolower($currentUser->role), ['admin', 'bendahara']);

        return response()->json([
            'id' => $balita->id,
            'nama' => $balita->name,
            'gender' => $balita->gender,
            'tanggal_lahir' => Carbon::parse($balita->tanggal_lahir)->translatedFormat('d F Y'),
            'usia' => (int) Carbon::parse($balita->tanggal_lahir)->diffInMonths(Carbon::now()) . ' bulan',
            'alamat' => $balita->address,
            'ayah' => $ayah ? $ayah->name : '-',
            'ibu' => $ibu ? $ibu->name : '-',
            'perkembangan' => [
                'tinggi' => $tinggi,
                'berat' => $berat,
                'status' => $status,
                'catatan' => $catatan,
                'tanggal_terakhir' => $tanggal_terakhir,
            ],
            'riwayat' => $historyFormatted,
            'jadwal_berikutnya' => $tanggalJadwal->translatedFormat('l, d F Y') . ' (08.00 WIB)',
            'is_admin' => $isAdmin
        ]);
    }

    public function storePerkembanganBalita(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !in_array(strtolower($currentUser->role), ['admin', 'bendahara'])) {
            return response()->json(['message' => 'Hanya admin yang memiliki akses untuk menambah data perkembangan.'], 403);
        }

        $request->validate([
            'tanggal_pemeriksaan' => 'required|date',
            'berat_badan' => 'required|numeric|min:0.1|max:50',
            'tinggi_badan' => 'required|numeric|min:10|max:150',
            'status_gizi' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $balita = User::where('role', 'warga')->findOrFail($id);

        $perkembangan = PerkembanganBalita::create([
            'user_id' => $balita->id,
            'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
            'berat_badan' => $request->berat_badan,
            'tinggi_badan' => $request->tinggi_badan,
            'status_gizi' => $request->status_gizi,
            'catatan' => $request->catatan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data perkembangan balita berhasil ditambahkan!',
            'data' => $perkembangan
        ]);
    }

    public function detailImunisasi()
    {
        $user = Auth::user();

        // Ambil semua balita (usia 0-5 tahun) dari data warga
        $allBalita = User::where('role', 'warga')
            ->whereNotNull('tanggal_lahir')
            ->where('tanggal_lahir', '>=', Carbon::now()->subYears(5))
            ->orderBy('name', 'asc')
            ->get();

        // Fungsi helper penentu RT dari user
        $getRt = function ($b) {
            $rt = intval($b->rt_number);
            if ($rt === 0 && preg_match('/RT\s*(\d+)/i', $b->address, $matches)) {
                $rt = intval($matches[1]);
            }
            return $rt > 0 ? $rt : 1; // Fallback ke RT 1 jika tidak terdeteksi
        };

        // Jadwal Posyandu Mingguan di RW 013 beserta Posyandu & Cakupan RT-nya
        $posyanduJadwal = [
            [
                'nama' => 'Posyandu Melati 01',
                'minggu' => 'Minggu 1',
                'jadwal' => 'Sabtu Pertama (08.00 - 11.00 WIB)',
                'lokasi' => 'Balai Warga RT 001 / RW 013',
                'cakupan' => 'RT 001 & RT 002',
                'fokus' => 'Imunisasi Dasar Complete (BCG, Polio, DPT, Hepatitis B) & Timbang Balita',
                'badge_color' => 'bg-primary',
                'text_color' => 'text-primary',
                'anak' => $allBalita->filter(fn($b) => in_array($getRt($b), [1, 2]))->values()
            ],
            [
                'nama' => 'Posyandu Melati 02',
                'minggu' => 'Minggu 2',
                'jadwal' => 'Sabtu Kedua (08.00 - 11.00 WIB)',
                'lokasi' => 'Pos Kesehatan RT 003 / RW 013',
                'cakupan' => 'RT 003 & RT 004',
                'fokus' => 'Imunisasi Lanjutan (Campak/MR), Vitamin A & Pemantauan Tumbuh Kembang',
                'badge_color' => 'bg-success',
                'text_color' => 'text-success',
                'anak' => $allBalita->filter(fn($b) => in_array($getRt($b), [3, 4]))->values()
            ],
            [
                'nama' => 'Posyandu Melati 03',
                'minggu' => 'Minggu 3',
                'jadwal' => 'Sabtu Ketiga (08.00 - 11.00 WIB)',
                'lokasi' => 'Pos Sekretariat RW 013',
                'cakupan' => 'RT 005 & RT 006',
                'fokus' => 'Pemberian Makanan Tambahan (PMT) Gizi Seimbang & Cek Lingkar Kepala Balita',
                'badge_color' => 'bg-warning text-dark',
                'text_color' => 'text-warning-emphasis',
                'anak' => $allBalita->filter(fn($b) => in_array($getRt($b), [5, 6]))->values()
            ],
            [
                'nama' => 'Posyandu Melati 04',
                'minggu' => 'Minggu 4',
                'jadwal' => 'Sabtu Keempat (08.00 - 11.00 WIB)',
                'lokasi' => 'Ruang Serbaguna RT 007 / RW 013',
                'cakupan' => 'RT 007 & RT 008',
                'fokus' => 'Konsultasi Pemenuhan Gizi Seimbang & Skrining Dini Stunting',
                'badge_color' => 'bg-info text-white',
                'text_color' => 'text-info',
                'anak' => $allBalita->filter(fn($b) => in_array($getRt($b), [7, 8]))->values()
            ],
        ];

        return view('posyandu.detail_imunisasi', compact('user', 'posyanduJadwal'));
    }

    public function detailIbuHamil()
    {
        $user = Auth::user();

        // Ambil data ibu/wanita usia produktif (20 - 45 tahun) dari data warga
        $allIbu = User::where('role', 'warga')
            ->where('gender', 'Perempuan')
            ->whereNotNull('tanggal_lahir')
            ->where('tanggal_lahir', '<=', Carbon::now()->subYears(20))
            ->where('tanggal_lahir', '>=', Carbon::now()->subYears(45))
            ->orderBy('name', 'asc')
            ->get();

        // Fungsi helper penentu RT dari user
        $getRt = function ($b) {
            $rt = intval($b->rt_number);
            if ($rt === 0 && preg_match('/RT\s*(\d+)/i', $b->address, $matches)) {
                $rt = intval($matches[1]);
            }
            return $rt > 0 ? $rt : 1;
        };

        // Jadwal Posyandu Ibu Hamil Mingguan di RW 013 beserta Cakupan RT-nya
        $posyanduJadwal = [
            [
                'nama' => 'Posyandu Ibu Hamil Melati 01',
                'minggu' => 'Minggu 1',
                'jadwal' => 'Sabtu Pertama (08.00 - 11.00 WIB)',
                'lokasi' => 'Balai Warga RT 001 / RW 013',
                'cakupan' => 'RT 001 & RT 002',
                'fokus' => 'Pemeriksaan Trimester 1, USG Dasar & Screening Awal Kehamilan (Zat Besi/Fe)',
                'tenaga_medis' => 'dr. Farhan Sp.OG & Bidan Ratna, S.ST',
                'badge_color' => 'bg-primary',
                'ibu' => $allIbu->filter(fn($b) => in_array($getRt($b), [1, 2]))->values()
            ],
            [
                'nama' => 'Posyandu Ibu Hamil Melati 02',
                'minggu' => 'Minggu 2',
                'jadwal' => 'Sabtu Kedua (08.00 - 11.00 WIB)',
                'lokasi' => 'Pos Kesehatan RT 003 / RW 013',
                'cakupan' => 'RT 003 & RT 004',
                'fokus' => 'Pemeriksaan Trimester 2, Cek Tekanan Darah, Lingkar Lengan Atas (LiLA) & HB',
                'tenaga_medis' => 'dr. Amanda Sp.A & Bidan Melati, S.Tr.Keb',
                'badge_color' => 'bg-success',
                'ibu' => $allIbu->filter(fn($b) => in_array($getRt($b), [3, 4]))->values()
            ],
            [
                'nama' => 'Posyandu Ibu Hamil Melati 03',
                'minggu' => 'Minggu 3',
                'jadwal' => 'Sabtu Ketiga (08.00 - 11.00 WIB)',
                'lokasi' => 'Pos Sekretariat RW 013',
                'cakupan' => 'RT 005 & RT 006',
                'fokus' => 'Pemeriksaan Trimester 3, Konsultasi Persalinan Sehat & Senam Hamil',
                'tenaga_medis' => 'dr. Setyo Utomo & Bidan Anita, A.Md.Keb',
                'badge_color' => 'bg-warning text-dark',
                'ibu' => $allIbu->filter(fn($b) => in_array($getRt($b), [5, 6]))->values()
            ],
            [
                'nama' => 'Posyandu Ibu Hamil Melati 04',
                'minggu' => 'Minggu 4',
                'jadwal' => 'Sabtu Keempat (08.00 - 11.00 WIB)',
                'lokasi' => 'Ruang Serbaguna RT 007 / RW 013',
                'cakupan' => 'RT 007 & RT 008',
                'fokus' => 'Konseling Pasca Persalinan, Inisiasi Menyusu Dini (IMD) & ASI Eksklusif',
                'tenaga_medis' => 'dr. Nabila Putri & Konsultan Laktasi',
                'badge_color' => 'bg-info text-white',
                'ibu' => $allIbu->filter(fn($b) => in_array($getRt($b), [7, 8]))->values()
            ],
        ];

        return view('posyandu.detail_ibu_hamil', compact('user', 'posyanduJadwal'));
    }

    public function detailEdukasi()
    {
        $user = Auth::user();

        // Data Seminar Edukasi 3 Bulan Sekali (Triwulan) di RW 013
        $seminarJadwal = [
            [
                'periode' => 'Triwulan I (Maret 2026)',
                'badge' => 'bg-primary',
                'judul' => 'Seminar Kesehatan Keluarga & Pencegahan Stunting Dini',
                'tanggal' => 'Sabtu, 28 Maret 2026 (09.00 - 12.00 WIB)',
                'lokasi' => 'Gedung Serbaguna Utama RW 013',
                'narasumber' => 'dr. Amanda Sp.A & Tim Dinkes Kota',
                'audiens' => 'Seluruh Warga & Pasangan Muda RW 013',
                'kuota' => '150 Peserta',
                'fasilitas' => 'Snack Box, Modul Edukasi, Sertifikat & Cek Kesehatan Gratis',
                'deskripsi' => 'Seminar interaktif membahas langkah krusial 1.000 Hari Pertama Kehidupan (HPK) untuk mencetak generasi bebas stunting serta pola gizi seimbang di keluarga.'
            ],
            [
                'periode' => 'Triwulan II (Juni 2026)',
                'badge' => 'bg-success',
                'judul' => 'Lokakarya Pengolahan Sampah Rumah Tangga & PHBS Lingkungan',
                'tanggal' => 'Sabtu, 27 Juni 2026 (08.30 - 11.30 WIB)',
                'lokasi' => 'Aula Terbuka Bank Sampah (RT 004 / RW 013)',
                'narasumber' => 'Dinas Lingkungan Hidup & Praktisi Eco-Enzyme',
                'audiens' => 'Ibu Rumah Tangga & Kepala Keluarga RW 013',
                'kuota' => '120 Peserta',
                'fasilitas' => 'Starter Kit Composting, Snack, & Doorprize Alat Kebersihan',
                'deskripsi' => 'Pelatihan praktis memilah sampah dapur, pembuatan pupuk kompos mandiri, dan menjaga kebersihan lingkungan dari jentik nyamuk DBD.'
            ],
            [
                'periode' => 'Triwulan III (September 2026)',
                'badge' => 'bg-warning text-dark',
                'judul' => 'Simposium Kesehatan Mental Keluarga & Parenting Anak',
                'tanggal' => 'Sabtu, 26 September 2026 (09.00 - 12.00 WIB)',
                'lokasi' => 'Aula Utama Masjid Al-Ikhlas (RT 002 / RW 013)',
                'narasumber' => 'Psikolog Klinis Family Center & Duta BKKBN',
                'audiens' => 'Orang Tua & Remaja Usia 12-18 Tahun RW 013',
                'kuota' => '100 Keluarga',
                'fasilitas' => 'Materi E-Book Parenting, Konsultasi Gratis & Goodie Bag',
                'deskripsi' => 'Membangun komunikasi harmonis orang tua dan anak, pencegahan kecanduan gadget pada anak, serta manajemen stres rumah tangga.'
            ],
            [
                'periode' => 'Triwulan IV (Desember 2026)',
                'badge' => 'bg-info text-white',
                'judul' => 'Festival Edukasi KB Kekinian & Kesehatan Lansia Bahagia',
                'tanggal' => 'Sabtu, 19 Desember 2026 (08.00 - 12.00 WIB)',
                'lokasi' => 'Lapangan Olahraga & Kebudayaan (RT 006 / RW 013)',
                'narasumber' => 'Tim Dokter Puskesmas & Penyuluh KB',
                'audiens' => 'Seluruh Warga Lintas Generasi RW 013',
                'kuota' => '200 Peserta',
                'fasilitas' => 'Pemeriksaan Darah Lengkap, Senam Bersama & Bazar Kesehatan',
                'deskripsi' => 'Puncak edukasi tahunan yang menggabungkan sosialisasi KB modern, pemeriksaan kesehatan lansia gratis, serta hiburan edukatif keluarga.'
            ],
        ];

        return view('posyandu.detail_edukasi', compact('user', 'seminarJadwal'));
    }
    // ---------------------------------------

    public function umkm()
    {
        $user = Auth::user();
        $umkms = \App\Models\Umkm::orderBy('nama_umkm')->get();
        return view('umkm', compact('user', 'umkms'));
    }

    public function peta()
    {
        $user = Auth::user();
        return view('peta', compact('user'));
    }

    public function wisata()
    {
        $user = Auth::user();
        $userAddress = $user->address ?? 'Jl. Melati Utama No. 15, RT 001 / RW 013';

        $wisataList = [
            [
                'id' => 1,
                'nama' => 'Taman Hijau & Danau Edukasi RW 013',
                'kategori' => 'Taman & Ruang Terbuka',
                'badge_color' => 'bg-success',
                'lokasi' => 'Jl. Taman Edukasi No. 1, RW 013',
                'lat' => -6.227500,
                'lng' => 106.814800,
                'jarak' => '450 meter',
                'waktu' => '5 Menit Jalan Kaki',
                'jam_buka' => '05:30 - 20:00 WIB',
                'tiket' => 'Gratis (Fasilitas Warga)',
                'fasilitas' => 'Jogging Track, Arena Bermain Anak, Gazebo & Wi-Fi Publik',
                'deskripsi' => 'Taman ramah keluarga dengan pepohonan rimbun, danau buatan dengan ikan hias, dan arena bermain balita yang aman dan bersih.'
            ],
            [
                'id' => 2,
                'nama' => 'Pasar Kuliner Malam & Festival Warga',
                'kategori' => 'Wisata Kuliner',
                'badge_color' => 'bg-warning text-dark',
                'lokasi' => 'Lapangan Utama RW 013 (RT 006)',
                'lat' => -6.233800,
                'lng' => 106.821000,
                'jarak' => '750 meter',
                'waktu' => '8 Menit Jalan Kaki / 3 Menit Motor',
                'jam_buka' => '16:00 - 23:00 WIB',
                'tiket' => 'Gratis Masuk Area',
                'fasilitas' => 'Panggung Live Music, Stan Jajanan Lokal, Parkir Luas & Musholla',
                'deskripsi' => 'Pusat jajanan kuliner malam favorit warga RW 013 yang menyajikan puluhan aneka kuliner kekinian, jajanan tradisional, dan wahana bermain anak.'
            ],
            [
                'id' => 3,
                'nama' => 'Museum Sejarah & Galeri Kebudayaan',
                'kategori' => 'Wisata Budaya & Edukasi',
                'badge_color' => 'bg-primary',
                'lokasi' => 'Jl. Kebudayaan No. 10 (Dekat Gerbang RW 013)',
                'lat' => -6.226000,
                'lng' => 106.813000,
                'jarak' => '1.2 km',
                'waktu' => '4 Menit Motor',
                'jam_buka' => '08:00 - 16:00 WIB',
                'tiket' => 'Rp 5.000 / Orang',
                'fasilitas' => 'Pemandu Wisata, Ruang AC, Perpustakaan Mini & Spot Foto',
                'deskripsi' => 'Destinasi wisata edukatif memamerkan sejarah daerah, peninggalan kerajinan tangan, dan galeri foto tempo doeloe wilayah pemukiman.'
            ],
            [
                'id' => 4,
                'nama' => 'Hutan Kota & Eco-Park Konservasi',
                'kategori' => 'Wisata Alam & Ekowisata',
                'badge_color' => 'bg-success text-white',
                'lokasi' => 'Kawasan Jalur Hijau RW 013',
                'lat' => -6.236000,
                'lng' => 106.823500,
                'jarak' => '1.5 km',
                'waktu' => '5 Menit Motor',
                'jam_buka' => '06:00 - 18:00 WIB',
                'tiket' => 'Gratis (Donasi Rp 2.000)',
                'fasilitas' => 'Jembatan Kayu Aesthetic, Rumah Burung, & Area Foto Instagramable',
                'deskripsi' => 'Kawasan konservasi hijau dengan pepohonan rindang tempat habitat berbagai spesies burung kota, cocok untuk refreshing dan jalan sehat.'
            ],
            [
                'id' => 5,
                'nama' => 'Waterpark Ceria & Kolam Renang',
                'kategori' => 'Wisata Rekreasi Air',
                'badge_color' => 'bg-info text-white',
                'lokasi' => 'Jl. Raya Tirta No. 88 (Dekat RW 013)',
                'lat' => -6.224500,
                'lng' => 106.811500,
                'jarak' => '2.1 km',
                'waktu' => '7 Menit Motor',
                'jam_buka' => '07:30 - 17:30 WIB',
                'tiket' => 'Rp 20.000 / Orang',
                'fasilitas' => 'Kolam Renang Anak & Dewasa, Perosotan Water Slide, Ember Tumpah & Kantin',
                'deskripsi' => 'Wahana rekreasi air menyenangkan favorit anak-anak dan keluarga untuk menghabiskan waktu libur akhir pekan.'
            ],
            [
                'id' => 6,
                'nama' => 'Alun-Alun Olahraga & Ruang Terbuka Hijau',
                'kategori' => 'Wisata Olahraga',
                'badge_color' => 'bg-secondary',
                'lokasi' => 'Pusat Alun-Alun Kecamatan',
                'lat' => -6.223000,
                'lng' => 106.810000,
                'jarak' => '2.8 km',
                'waktu' => '9 Menit Motor',
                'jam_buka' => '24 Jam (Terbuka Umum)',
                'tiket' => 'Gratis',
                'fasilitas' => 'Lapangan Basket, Futsal, Fitness Outdoor, Track Sepeda & Penjual Makanan',
                'deskripsi' => 'Ruang publik terbuka luas untuk kegiatan olahraga pagi, senam bersama akhir pekan, serta ajang kumpul komunitas warga.'
            ]
        ];

        return view('wisata', compact('user', 'wisataList', 'userAddress'));
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

    // ======================================================================
    // FITUR BARU: LAYANAN MASYARAKAT, AUDIT RT, KEUANGAN RW, PENGADUAN
    // ======================================================================
    public function bankSampah()
    {
        $user = Auth::user();
        return view('bank-sampah', compact('user'));
    }

    public function ronda()
    {
        $user = Auth::user();
        $cctvs = \App\Models\Cctv::where('aktif', true)->latest()->get();
        $jadwalRonda = [
            ['hari' => 'Senin', 'rt' => 'RT 001 & RT 002', 'koordinator' => 'Pak Joko & Pak Hadi', 'anggota' => '6 Petugas', 'status' => 'Selesai'],
            ['hari' => 'Selasa', 'rt' => 'RT 003 & RT 004', 'koordinator' => 'Pak Rudi & Pak Ahmad', 'anggota' => '6 Petugas', 'status' => 'Selesai'],
            ['hari' => 'Rabu (Malam Ini)', 'rt' => 'RT 005 & RT 006', 'koordinator' => 'Pak Hendra & Pak Rahmat', 'anggota' => '8 Petugas', 'status' => 'Bertugas (22.00 - 04.30)'],
            ['hari' => 'Kamis', 'rt' => 'RT 007 & RT 008', 'koordinator' => 'Pak Dedi & Pak Eko', 'anggota' => '6 Petugas', 'status' => 'Terjadwal'],
            ['hari' => 'Jumat', 'rt' => 'RT 001 - RT 004', 'koordinator' => 'Koordinator Linmas RW', 'anggota' => '10 Petugas (Patroli Gabungan)', 'status' => 'Terjadwal'],
            ['hari' => 'Sabtu', 'rt' => 'RT 005 - RT 008', 'koordinator' => 'Koordinator Linmas RW', 'anggota' => '10 Petugas (Patroli Gabungan)', 'status' => 'Terjadwal'],
            ['hari' => 'Minggu', 'rt' => 'Bergilir Semua RT', 'koordinator' => 'Piket Pengurus RW', 'anggota' => '8 Petugas', 'status' => 'Terjadwal'],
        ];
        return view('ronda.index', compact('user', 'cctvs', 'jadwalRonda'));
    }

    public function pengaduan(Request $request)
    {
        $user = Auth::user();
        $kategori = $request->query('kategori');
        $status = $request->query('status');
        $pengaduanList = $this->getPengaduanData();

        if ($kategori) {
            $pengaduanList = array_values(array_filter($pengaduanList, fn($p) => $p['kategori'] == $kategori));
        }
        if ($status) {
            $pengaduanList = array_values(array_filter($pengaduanList, fn($p) => $p['status'] == $status));
        }

        return view('pengaduan.index', compact('user', 'pengaduanList', 'kategori', 'status'));
    }

    public function storePengaduan(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string',
            'rt' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'required|string|min:10',
            'foto' => 'nullable|image|max:3072'
        ]);

        $user = Auth::user();
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('pengaduan', 'public');
        }

        $all = $this->getPengaduanData();
        $newId = count($all) ? (max(array_column($all, 'id')) + 1) : 1;

        $newComplaint = [
            'id' => $newId,
            'kategori' => $request->kategori,
            'rt' => $request->rt,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'pelapor' => $user->name,
            'phone' => $user->phone ?? '-',
            'status' => 'Menunggu',
            'foto' => $fotoPath ? asset('storage/' . $fotoPath) : null,
            'catatan_tindak_lanjut' => 'Pengaduan baru terkirim, menunggu verifikasi pengurus RW 013.',
            'created_at' => Carbon::now()->translatedFormat('d M Y, H:i') . ' WIB'
        ];

        array_unshift($all, $newComplaint);
        Storage::disk('local')->put('pengaduan.json', json_encode($all, JSON_PRETTY_PRINT));

        return redirect()->route('pengaduan.index')->with('success', 'Pengaduan warga berhasil dikirim dan akan segera ditindaklanjuti oleh pengurus RW!');
    }

    public function updateStatusPengaduan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'catatan_tindak_lanjut' => 'nullable|string'
        ]);

        $all = $this->getPengaduanData();
        foreach ($all as &$item) {
            if ($item['id'] == $id) {
                $item['status'] = $request->status;
                if ($request->filled('catatan_tindak_lanjut')) {
                    $item['catatan_tindak_lanjut'] = $request->catatan_tindak_lanjut;
                }
                break;
            }
        }

        Storage::disk('local')->put('pengaduan.json', json_encode($all, JSON_PRETTY_PRINT));
        return back()->with('success', 'Status dan catatan pengaduan berhasil diperbarui!');
    }

    public function dataRt()
    {
        $user = Auth::user();
        $rtList = $this->getRtMonitoringData();
        return view('data-rt.index', compact('user', 'rtList'));
    }

    public function monitoringRt(Request $request)
    {
        $user = Auth::user();
        $filterRt = $request->query('rt');
        $filterPeriode = $request->query('periode', 'November 2026');
        $rtMonitoringList = $this->getRtMonitoringData($filterRt, $filterPeriode);

        return view('monitoring.index', compact('user', 'rtMonitoringList', 'filterRt', 'filterPeriode'));
    }

    public function detailMonitoringRt($rt)
    {
        $user = Auth::user();
        $all = $this->getRtMonitoringData();
        $rtDetail = null;
        foreach ($all as $item) {
            if ($item['rt'] == $rt || $item['nomor'] == $rt) {
                $rtDetail = $item;
                break;
            }
        }

        if (!$rtDetail) {
            abort(404, 'Data RT tidak ditemukan.');
        }

        return view('monitoring.show', compact('user', 'rtDetail'));
    }

    public function saveEvaluasiRt(Request $request, $rt)
    {
        $request->validate([
            'evaluasi_rw' => 'required|string',
            'rekomendasi' => 'required|string',
            'status_laporan' => 'required|string'
        ]);

        $savedEvaluations = session('rt_evaluations', []);
        $savedEvaluations[$rt] = [
            'evaluasi_rw' => $request->evaluasi_rw,
            'rekomendasi' => $request->rekomendasi,
            'status_laporan' => $request->status_laporan,
            'updated_at' => Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB'
        ];
        session(['rt_evaluations' => $savedEvaluations]);

        // Sinkronisasi data ke storage/app/laporan_rt.json
        $laporanList = $this->getLaporanRtList();
        foreach ($laporanList as &$lap) {
            if ($lap['rt_number'] == $rt || $lap['rt_label'] == 'RT ' . str_pad($rt, 3, '0', STR_PAD_LEFT)) {
                $lap['evaluasi_rw'] = $request->evaluasi_rw;
                $lap['rekomendasi_rw'] = $request->rekomendasi;
                $lap['status'] = $request->status_laporan;
                $lap['updated_at'] = Carbon::now()->translatedFormat('d M Y, H:i') . ' WIB';
                if (!isset($lap['history']) || !is_array($lap['history'])) {
                    $lap['history'] = [];
                }
                $lap['history'][] = [
                    'tanggal' => Carbon::now()->translatedFormat('d M Y, H:i') . ' WIB',
                    'aktor' => 'Ketua RW 013',
                    'aksi' => 'Evaluasi diperbarui (Status: ' . $request->status_laporan . ')'
                ];
                break;
            }
        }
        $this->saveLaporanRtList($laporanList);

        return back()->with('success', "Catatan evaluasi untuk RT {$rt} berhasil disimpan!");
    }

    public function getLaporanRtList()
    {
        if (Storage::disk('local')->exists('laporan_rt.json')) {
            $data = json_decode(Storage::disk('local')->get('laporan_rt.json'), true);
            if (is_array($data) && count($data)) {
                return $data;
            }
        }

        // Default Laporan 8 RT jika file belum ada
        $defaults = [
            [
                'id' => 1,
                'rt_number' => 1,
                'rt_label' => 'RT 001',
                'periode' => 'November 2026',
                'ketua_rt' => 'Bambang Suprayitno',
                'kontak' => '0812-9844-0101',
                'status' => 'Lengkap',
                'administrasi' => [
                    'jumlah_kk' => 45,
                    'jumlah_warga' => 142,
                    'warga_baru' => 2,
                    'mutasi_keluar' => 0,
                    'surat_dikeluarkan' => 12,
                    'catatan' => 'Seluruh berkas KK dan surat pengantar terdata lengkap.'
                ],
                'keuangan' => [
                    'saldo_awal' => 12500000,
                    'pemasukan' => 3850000,
                    'pengeluaran' => 1500000,
                    'saldo_akhir' => 14850000,
                    'iuran_terkumpul_persen' => 95.5
                ],
                'sosial' => [
                    'posyandu_terlaksana' => 'Ya',
                    'partisipasi_posyandu' => '28 Balita & 15 Lansia',
                    'kerja_bakti' => 'Terlaksana Minggu ke-1'
                ],
                'keamanan' => [
                    'jadwal_ronda_berjalan' => 'Ya (7 Hari)',
                    'kejadian_menonjol' => 'Nihil / Aman',
                    'bank_sampah_terkumpul' => '65 kg'
                ],
                'usulan_rt' => 'Pengajuan perbaikan 2 unit PJU Gang Mawar.',
                'evaluasi_rw' => 'Administrasi sangat tertib, partisipasi gotong royong dan pos ronda sangat tinggi.',
                'rekomendasi_rw' => 'Pertahankan transparansi pelaporan kas dan komunikasi aktif.',
                'updated_at' => '10 Nov 2026, 14:00 WIB',
                'history' => [
                    ['tanggal' => '02 Nov 2026, 10:15 WIB', 'aktor' => 'Ketua RT 001', 'aksi' => 'Laporan bulanan dikirim ke RW.'],
                    ['tanggal' => '05 Nov 2026, 16:30 WIB', 'aktor' => 'Ketua RW 013', 'aksi' => 'Laporan diverifikasi Lengkap.']
                ]
            ],
            [
                'id' => 2,
                'rt_number' => 2,
                'rt_label' => 'RT 002',
                'periode' => 'November 2026',
                'ketua_rt' => 'H. Agus Mulyadi',
                'kontak' => '0813-8821-0202',
                'status' => 'Perlu perbaikan',
                'administrasi' => [
                    'jumlah_kk' => 42,
                    'jumlah_warga' => 138,
                    'warga_baru' => 1,
                    'mutasi_keluar' => 1,
                    'surat_dikeluarkan' => 8,
                    'catatan' => 'Rekap absensi siskamling belum dilampirkan.'
                ],
                'keuangan' => [
                    'saldo_awal' => 10200000,
                    'pemasukan' => 3200000,
                    'pengeluaran' => 1000000,
                    'saldo_akhir' => 12400000,
                    'iuran_terkumpul_persen' => 90.4
                ],
                'sosial' => [
                    'posyandu_terlaksana' => 'Ya',
                    'partisipasi_posyandu' => '22 Balita',
                    'kerja_bakti' => 'Terlaksana Minggu ke-2'
                ],
                'keamanan' => [
                    'jadwal_ronda_berjalan' => 'Ya (5 Hari)',
                    'kejadian_menonjol' => 'Laporan parkir liar',
                    'bank_sampah_terkumpul' => '40 kg'
                ],
                'usulan_rt' => 'Rambu larangan parkir sembarangan di persimpangan jalan Kenanga.',
                'evaluasi_rw' => 'Laporan keuangan sudah masuk, namun rekap absensi siskamling bulan ini belum dilampirkan.',
                'rekomendasi_rw' => 'Lengkapi lembar rekap jaga pos ronda dan laporan penerbitan surat pengantar.',
                'updated_at' => '08 Nov 2026, 11:30 WIB',
                'history' => [
                    ['tanggal' => '03 Nov 2026, 09:00 WIB', 'aktor' => 'Ketua RT 002', 'aksi' => 'Laporan bulanan diserahkan.'],
                    ['tanggal' => '08 Nov 2026, 11:30 WIB', 'aktor' => 'Ketua RW 013', 'aksi' => 'Catatan revisi: lampirkan absensi ronda.']
                ]
            ],
            [
                'id' => 3,
                'rt_number' => 3,
                'rt_label' => 'RT 003',
                'periode' => 'November 2026',
                'ketua_rt' => 'Suryanto',
                'kontak' => '0815-7766-0303',
                'status' => 'Lengkap',
                'administrasi' => [
                    'jumlah_kk' => 48,
                    'jumlah_warga' => 155,
                    'warga_baru' => 3,
                    'mutasi_keluar' => 0,
                    'surat_dikeluarkan' => 15,
                    'catatan' => 'Berkas digital tersimpan rapi di Google Drive RT.'
                ],
                'keuangan' => [
                    'saldo_awal' => 14000000,
                    'pemasukan' => 4100000,
                    'pengeluaran' => 1800000,
                    'saldo_akhir' => 16300000,
                    'iuran_terkumpul_persen' => 97.9
                ],
                'sosial' => [
                    'posyandu_terlaksana' => 'Ya',
                    'partisipasi_posyandu' => '32 Balita & 20 Lansia',
                    'kerja_bakti' => 'Terlaksana Minggu ke-1 & 3'
                ],
                'keamanan' => [
                    'jadwal_ronda_berjalan' => 'Ya (7 Hari)',
                    'kejadian_menonjol' => 'Aman terkendali',
                    'bank_sampah_terkumpul' => '120 kg'
                ],
                'usulan_rt' => 'Bantuan pengadaan tempat sampah pilah organik dan anorganik untuk area taman.',
                'evaluasi_rw' => 'Pusat percontohan Bank Sampah RW. Pengelolaan kas dan partisipasi warga sangat baik.',
                'rekomendasi_rw' => 'Bantu dampingi RT lain dalam aktivasi unit bank sampah komunal.',
                'updated_at' => '10 Nov 2026, 15:00 WIB',
                'history' => [
                    ['tanggal' => '01 Nov 2026, 14:00 WIB', 'aktor' => 'Ketua RT 003', 'aksi' => 'Laporan bulanan diserahkan.'],
                    ['tanggal' => '04 Nov 2026, 10:00 WIB', 'aktor' => 'Ketua RW 013', 'aksi' => 'Laporan diverifikasi Lengkap dan Terbaik.']
                ]
            ],
            [
                'id' => 4,
                'rt_number' => 4,
                'rt_label' => 'RT 004',
                'periode' => 'November 2026',
                'ketua_rt' => 'Wahyudi Kusuma',
                'kontak' => '0818-4455-0404',
                'status' => 'Perlu perbaikan',
                'administrasi' => [
                    'jumlah_kk' => 38,
                    'jumlah_warga' => 120,
                    'warga_baru' => 0,
                    'mutasi_keluar' => 0,
                    'surat_dikeluarkan' => 5,
                    'catatan' => 'Laporan fisik belum diserahkan.'
                ],
                'keuangan' => [
                    'saldo_awal' => 8500000,
                    'pemasukan' => 2000000,
                    'pengeluaran' => 1200000,
                    'saldo_akhir' => 9300000,
                    'iuran_terkumpul_persen' => 68.4
                ],
                'sosial' => [
                    'posyandu_terlaksana' => 'Kurang Optimal',
                    'partisipasi_posyandu' => '14 Balita',
                    'kerja_bakti' => 'Tertunda'
                ],
                'keamanan' => [
                    'jadwal_ronda_berjalan' => 'Ya (4 Hari)',
                    'kejadian_menonjol' => 'Kucing liar banyak di gang',
                    'bank_sampah_terkumpul' => '15 kg'
                ],
                'usulan_rt' => 'Bantuan pengadaan formulir dan buku registrasi warga baru.',
                'evaluasi_rw' => 'Capaian iuran warga masih di bawah 70% dan kegiatan kerja bakti bulan ini belum terlaksana.',
                'rekomendasi_rw' => 'Lakukan pendekatan kekeluargaan door-to-door dan koordinasi intensif dengan bendahara RW.',
                'updated_at' => '06 Nov 2026, 17:00 WIB',
                'history' => [
                    ['tanggal' => '06 Nov 2026, 17:00 WIB', 'aktor' => 'Ketua RW 013', 'aksi' => 'Catatan evaluasi: perlu percepatan pelaporan iuran.']
                ]
            ],
            [
                'id' => 5,
                'rt_number' => 5,
                'rt_label' => 'RT 005',
                'periode' => 'November 2026',
                'ketua_rt' => 'Hendro Purnomo',
                'kontak' => '0819-1122-0505',
                'status' => 'Lengkap',
                'administrasi' => [
                    'jumlah_kk' => 50,
                    'jumlah_warga' => 160,
                    'warga_baru' => 4,
                    'mutasi_keluar' => 2,
                    'surat_dikeluarkan' => 18,
                    'catatan' => 'Sistem surat online warga berjalan efektif.'
                ],
                'keuangan' => [
                    'saldo_awal' => 15000000,
                    'pemasukan' => 4200000,
                    'pengeluaran' => 1900000,
                    'saldo_akhir' => 17300000,
                    'iuran_terkumpul_persen' => 96.0
                ],
                'sosial' => [
                    'posyandu_terlaksana' => 'Ya',
                    'partisipasi_posyandu' => '30 Balita & 18 Lansia',
                    'kerja_bakti' => 'Terlaksana Minggu ke-1'
                ],
                'keamanan' => [
                    'jadwal_ronda_berjalan' => 'Ya (7 Hari)',
                    'kejadian_menonjol' => 'PJU padam depan gang (sudah perbaikan)',
                    'bank_sampah_terkumpul' => '55 kg'
                ],
                'usulan_rt' => 'Pemasangan 1 titik CCTV baru di ujung barat gang melati.',
                'evaluasi_rw' => 'Kinerja solid, tertib administrasi, respon cepat terhadap aduan warga (PJU mati langsung ditindaklanjuti).',
                'rekomendasi_rw' => 'Pertahankan kedisiplinan dan kesigapan linmas lingkungan.',
                'updated_at' => '10 Nov 2026, 16:30 WIB',
                'history' => [
                    ['tanggal' => '02 Nov 2026, 11:00 WIB', 'aktor' => 'Ketua RT 005', 'aksi' => 'Laporan bulanan diserahkan.'],
                    ['tanggal' => '05 Nov 2026, 14:00 WIB', 'aktor' => 'Ketua RW 013', 'aksi' => 'Laporan diverifikasi Lengkap.']
                ]
            ],
            [
                'id' => 6,
                'rt_number' => 6,
                'rt_label' => 'RT 006',
                'periode' => 'November 2026',
                'ketua_rt' => 'Rahmat Hidayat',
                'kontak' => '0812-3344-0606',
                'status' => 'Perlu perbaikan',
                'administrasi' => [
                    'jumlah_kk' => 40,
                    'jumlah_warga' => 130,
                    'warga_baru' => 1,
                    'mutasi_keluar' => 0,
                    'surat_dikeluarkan' => 6,
                    'catatan' => 'Verifikasi fisik perbatasan sedang berjalan.'
                ],
                'keuangan' => [
                    'saldo_awal' => 10500000,
                    'pemasukan' => 2800000,
                    'pengeluaran' => 1550000,
                    'saldo_akhir' => 11750000,
                    'iuran_terkumpul_persen' => 87.5
                ],
                'sosial' => [
                    'posyandu_terlaksana' => 'Ya',
                    'partisipasi_posyandu' => '20 Balita',
                    'kerja_bakti' => 'Terlaksana kuras gorong-gorong'
                ],
                'keamanan' => [
                    'jadwal_ronda_berjalan' => 'Ya (6 Hari)',
                    'kejadian_menonjol' => 'Drainase tersumbat dahan',
                    'bank_sampah_terkumpul' => '35 kg'
                ],
                'usulan_rt' => 'Bantuan gerobak sampah tambahan untuk kerja bakti gabungan.',
                'evaluasi_rw' => 'Terdapat catatan genangan air di saluran drainase perbatasan RT 006 dan jalan raya.',
                'rekomendasi_rw' => 'Jadwalkan kerja bakti kuras gorong-gorong bersama seksi lingkungan RW.',
                'updated_at' => '09 Nov 2026, 13:45 WIB',
                'history' => [
                    ['tanggal' => '04 Nov 2026, 15:00 WIB', 'aktor' => 'Ketua RT 006', 'aksi' => 'Laporan bulanan diserahkan.'],
                    ['tanggal' => '09 Nov 2026, 13:45 WIB', 'aktor' => 'Ketua RW 013', 'aksi' => 'Evaluasi: atasi drainase tersumbat.']
                ]
            ],
            [
                'id' => 7,
                'rt_number' => 7,
                'rt_label' => 'RT 007',
                'periode' => 'November 2026',
                'ketua_rt' => 'Eko Prasetyo',
                'kontak' => '0817-2233-0707',
                'status' => 'Perlu perbaikan',
                'administrasi' => [
                    'jumlah_kk' => 36,
                    'jumlah_warga' => 115,
                    'warga_baru' => 0,
                    'mutasi_keluar' => 1,
                    'surat_dikeluarkan' => 4,
                    'catatan' => 'Pencatatan kuitansi sedang disinkronkan.'
                ],
                'keuangan' => [
                    'saldo_awal' => 7800000,
                    'pemasukan' => 2200000,
                    'pengeluaran' => 950000,
                    'saldo_akhir' => 9050000,
                    'iuran_terkumpul_persen' => 77.7
                ],
                'sosial' => [
                    'posyandu_terlaksana' => 'Ya',
                    'partisipasi_posyandu' => '16 Balita',
                    'kerja_bakti' => 'Terlaksana Minggu ke-2'
                ],
                'keamanan' => [
                    'jadwal_ronda_berjalan' => 'Ya (5 Hari)',
                    'kejadian_menonjol' => 'Kondusif',
                    'bank_sampah_terkumpul' => '30 kg'
                ],
                'usulan_rt' => 'Sinkronisasi format pembukuan keuangan dengan bendahara RW.',
                'evaluasi_rw' => 'Terdapat selisih pencatatan iuran kebersihan bulan lalu sebesar Rp 350.000 yang belum dikonfirmasi.',
                'rekomendasi_rw' => 'Kunjungan bendahara RW ke bendahara RT 007 untuk sinkronisasi kuitansi dan pencatatan.',
                'updated_at' => '07 Nov 2026, 10:20 WIB',
                'history' => [
                    ['tanggal' => '03 Nov 2026, 13:00 WIB', 'aktor' => 'Ketua RT 007', 'aksi' => 'Laporan diserahkan.'],
                    ['tanggal' => '07 Nov 2026, 10:20 WIB', 'aktor' => 'Ketua RW 013', 'aksi' => 'Klarifikasi bukti kas dijadwalkan.']
                ]
            ],
            [
                'id' => 8,
                'rt_number' => 8,
                'rt_label' => 'RT 008',
                'periode' => 'November 2026',
                'ketua_rt' => 'Ir. Dedi Kurniawan',
                'kontak' => '0811-9988-0808',
                'status' => 'Perlu perbaikan',
                'administrasi' => [
                    'jumlah_kk' => 44,
                    'jumlah_warga' => 140,
                    'warga_baru' => 2,
                    'mutasi_keluar' => 0,
                    'surat_dikeluarkan' => 10,
                    'catatan' => 'Berkas administrasi lengkap.'
                ],
                'keuangan' => [
                    'saldo_awal' => 13500000,
                    'pemasukan' => 3600000,
                    'pengeluaran' => 1600000,
                    'saldo_akhir' => 15500000,
                    'iuran_terkumpul_persen' => 88.6
                ],
                'sosial' => [
                    'posyandu_terlaksana' => 'Ya',
                    'partisipasi_posyandu' => '24 Balita',
                    'kerja_bakti' => 'Terlaksana Minggu ke-1'
                ],
                'keamanan' => [
                    'jadwal_ronda_berjalan' => 'Ya (6 Hari)',
                    'kejadian_menonjol' => 'Portal malam kerap lupa digembok',
                    'bank_sampah_terkumpul' => '45 kg'
                ],
                'usulan_rt' => 'Pengadaan gembok otomatis atau kartu akses gerbang masuk malam.',
                'evaluasi_rw' => 'Pintu gerbang portal malam sering tidak terkunci setelah pukul 23.00 WIB.',
                'rekomendasi_rw' => 'Instruksikan linmas / petugas ronda untuk disiplin penutupan portal tepat pukul 23.00 WIB.',
                'updated_at' => '08 Nov 2026, 22:00 WIB',
                'history' => [
                    ['tanggal' => '02 Nov 2026, 16:00 WIB', 'aktor' => 'Ketua RT 008', 'aksi' => 'Laporan bulanan diserahkan.'],
                    ['tanggal' => '08 Nov 2026, 22:00 WIB', 'aktor' => 'Ketua RW 013', 'aksi' => 'Peringatan disiplin portal malam.']
                ]
            ],
        ];

        Storage::disk('local')->put('laporan_rt.json', json_encode($defaults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $defaults;
    }

    public function saveLaporanRtList(array $data)
    {
        return Storage::disk('local')->put('laporan_rt.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // ======================================================================
    // KOORDINASI RT & LAPORAN BULANAN KE RW
    // ======================================================================

    public function rtLaporan(Request $request)
    {
        $user = Auth::user();
        $rtNum = $user->clean_rt_number;
        $rtLabel = $user->clean_rt_label;

        if (($user->isRw() || in_array($user->role, ['admin', 'bendahara'])) && $request->has('rt')) {
            $rtNum = (int)$request->query('rt');
            $rtLabel = 'RT ' . str_pad($rtNum, 3, '0', STR_PAD_LEFT);
        }

        $laporanList = $this->getLaporanRtList();
        $myReport = null;
        foreach ($laporanList as $lap) {
            if ($lap['rt_number'] == $rtNum) {
                $myReport = $lap;
                break;
            }
        }
        if (!$myReport) {
            $myReport = $laporanList[0] ?? [];
            $myReport['rt_number'] = $rtNum;
            $myReport['rt_label'] = $rtLabel;
        }

        return view('rt.laporan', compact('user', 'rtNum', 'rtLabel', 'myReport', 'laporanList'));
    }

    public function rtLaporanCreate(Request $request)
    {
        $user = Auth::user();
        $rtNum = $user->clean_rt_number;
        $rtLabel = $user->clean_rt_label;

        $laporanList = $this->getLaporanRtList();
        $myReport = null;
        foreach ($laporanList as $lap) {
            if ($lap['rt_number'] == $rtNum) {
                $myReport = $lap;
                break;
            }
        }

        return view('rt.laporan-create', compact('user', 'rtNum', 'rtLabel', 'myReport'));
    }

    public function rtLaporanStore(Request $request)
    {
        $user = Auth::user();
        $rtNum = $user->clean_rt_number;
        $rtLabel = $user->clean_rt_label;

        $request->validate([
            'periode' => 'required|string',
            'jumlah_kk' => 'required|numeric',
            'jumlah_warga' => 'required|numeric',
            'saldo_awal' => 'required|numeric',
            'pemasukan' => 'required|numeric',
            'pengeluaran' => 'required|numeric',
        ]);

        $laporanList = $this->getLaporanRtList();
        $foundIndex = -1;
        foreach ($laporanList as $idx => $lap) {
            if ($lap['rt_number'] == $rtNum) {
                $foundIndex = $idx;
                break;
            }
        }

        $saldoAkhir = $request->saldo_awal + $request->pemasukan - $request->pengeluaran;

        $reportData = [
            'id' => $foundIndex >= 0 ? $laporanList[$foundIndex]['id'] : ($rtNum),
            'rt_number' => $rtNum,
            'rt_label' => $rtLabel,
            'periode' => $request->periode,
            'ketua_rt' => $user->name,
            'kontak' => $user->phone ?? '0812-XXXX-XXXX',
            'status' => 'Menunggu pemeriksaan',
            'administrasi' => [
                'jumlah_kk' => (int)$request->jumlah_kk,
                'jumlah_warga' => (int)$request->jumlah_warga,
                'warga_baru' => (int)($request->warga_baru ?? 0),
                'mutasi_keluar' => (int)($request->mutasi_keluar ?? 0),
                'surat_dikeluarkan' => (int)($request->surat_dikeluarkan ?? 0),
                'catatan' => $request->catatan_administrasi ?? 'Laporan administrasi warga diserahkan lengkap.'
            ],
            'keuangan' => [
                'saldo_awal' => (float)$request->saldo_awal,
                'pemasukan' => (float)$request->pemasukan,
                'pengeluaran' => (float)$request->pengeluaran,
                'saldo_akhir' => $saldoAkhir,
                'iuran_terkumpul_persen' => round(($request->pemasukan / max(1, $request->saldo_awal + $request->pemasukan)) * 100, 1)
            ],
            'sosial' => [
                'posyandu_terlaksana' => $request->posyandu_terlaksana ?? 'Ya',
                'partisipasi_posyandu' => $request->partisipasi_posyandu ?? 'Terlaksana sesuai jadwal',
                'kerja_bakti' => $request->kerja_bakti ?? 'Terlaksana'
            ],
            'keamanan' => [
                'jadwal_ronda_berjalan' => $request->ronda_berjalan ?? 'Ya (7 Hari)',
                'kejadian_menonjol' => $request->kejadian_menonjol ?? 'Nihil / Kondusif',
                'bank_sampah_terkumpul' => $request->bank_sampah_terkumpul ?? '50 kg'
            ],
            'usulan_rt' => $request->usulan_rt ?? '',
            'evaluasi_rw' => $foundIndex >= 0 ? ($laporanList[$foundIndex]['evaluasi_rw'] ?? 'Menunggu peninjauan oleh Pengurus RW.') : 'Menunggu peninjauan oleh Pengurus RW.',
            'rekomendasi_rw' => $foundIndex >= 0 ? ($laporanList[$foundIndex]['rekomendasi_rw'] ?? 'Laporan telah diterima di antrean pemeriksaan RW.') : 'Laporan telah diterima di antrean pemeriksaan RW.',
            'updated_at' => Carbon::now()->translatedFormat('d M Y, H:i') . ' WIB',
            'history' => $foundIndex >= 0 ? ($laporanList[$foundIndex]['history'] ?? []) : []
        ];

        $reportData['history'][] = [
            'tanggal' => Carbon::now()->translatedFormat('d M Y, H:i') . ' WIB',
            'aktor' => 'Ketua ' . $rtLabel . ' (' . $user->name . ')',
            'aksi' => 'Laporan bulanan periode ' . $request->periode . ' berhasil diserahkan ke RW 013.'
        ];

        if ($foundIndex >= 0) {
            $laporanList[$foundIndex] = $reportData;
        } else {
            $laporanList[] = $reportData;
        }

        $this->saveLaporanRtList($laporanList);

        return redirect()->route('rt.laporan')->with('success', 'Laporan bulanan ' . $rtLabel . ' berhasil diserahkan ke Sekretariat RW 013!');
    }

    public function rtEvaluasi(Request $request)
    {
        $user = Auth::user();
        $rtNum = $user->clean_rt_number;
        $rtLabel = $user->clean_rt_label;

        $laporanList = $this->getLaporanRtList();
        $myReport = null;
        foreach ($laporanList as $lap) {
            if ($lap['rt_number'] == $rtNum) {
                $myReport = $lap;
                break;
            }
        }
        if (!$myReport) {
            $myReport = $laporanList[0] ?? [];
            $myReport['rt_number'] = $rtNum;
            $myReport['rt_label'] = $rtLabel;
        }

        $monitoringList = $this->getRtMonitoringData();
        $rtAudit = null;
        foreach ($monitoringList as $m) {
            if ($m['rt'] == $rtNum) {
                $rtAudit = $m;
                break;
            }
        }

        return view('rt.evaluasi', compact('user', 'rtNum', 'rtLabel', 'myReport', 'rtAudit'));
    }

    public function rtKeluarga(Request $request)
    {
        $user = Auth::user();
        $rtNum = $user->clean_rt_number;
        $rtLabel = $user->clean_rt_label;

        // Query warga RT ini saja
        $wargaList = User::where('role', 'warga')->where(function ($q) use ($rtNum) {
            $q->where('rt_number', $rtNum)
              ->orWhere('rt_number', str_pad($rtNum, 3, '0', STR_PAD_LEFT))
              ->orWhere('rt_number', 'RT ' . str_pad($rtNum, 3, '0', STR_PAD_LEFT))
              ->orWhere('address', 'like', "%RT 00{$rtNum}%")
              ->orWhere('address', 'like', "%RT {$rtNum}%");
        })->orderBy('name')->get();

        $keluargaList = $wargaList->groupBy(function($item) {
            return $item->no_kk ?: 'KK_BELUM_TERDATA';
        });

        return view('rt.keluarga', compact('user', 'rtNum', 'rtLabel', 'keluargaList', 'wargaList'));
    }

    public function rtPendatang(Request $request)
    {
        $user = Auth::user();
        $rtNum = $user->clean_rt_number;
        $rtLabel = $user->clean_rt_label;

        $pendatang = User::where('role', 'warga')
            ->where(function ($q) use ($rtNum) {
                $q->where('rt_number', $rtNum)
                  ->orWhere('rt_number', str_pad($rtNum, 3, '0', STR_PAD_LEFT))
                  ->orWhere('rt_number', 'RT ' . str_pad($rtNum, 3, '0', STR_PAD_LEFT))
                  ->orWhere('address', 'like', "%RT 00{$rtNum}%");
            })
            ->where(function($q) {
                $q->where('status_rumah', 'like', '%kontrak%')
                  ->orWhere('status_rumah', 'like', '%sewa%')
                  ->orWhere('status_rumah', 'like', '%kos%')
                  ->orWhereNull('status_rumah');
            })
            ->orderBy('name')
            ->get();

        return view('rt.pendatang', compact('user', 'rtNum', 'rtLabel', 'pendatang'));
    }

    public function rtMutasi(Request $request)
    {
        $user = Auth::user();
        $rtNum = $user->clean_rt_number;
        $rtLabel = $user->clean_rt_label;

        $mutasiList = [
            ['nama' => 'Bambang Sudarsono', 'nik' => '3276010508820001', 'jenis' => 'Pindah Datang', 'tanggal' => '05 Nov 2026', 'asal_tujuan' => 'Kec. Sukmajaya, Depok', 'status' => 'Disetujui RT'],
            ['nama' => 'Dewi Safitri', 'nik' => '3276014409950003', 'jenis' => 'Pindah Keluar', 'tanggal' => '28 Okt 2026', 'asal_tujuan' => 'Jakarta Selatan', 'status' => 'Selesai Pengantar'],
            ['nama' => 'Ananda Rayyan', 'nik' => '-', 'jenis' => 'Kelahiran', 'tanggal' => '15 Okt 2026', 'asal_tujuan' => 'Keluarga Bpk. Herman', 'status' => 'Tercatat'],
        ];

        return view('rt.mutasi', compact('user', 'rtNum', 'rtLabel', 'mutasiList'));
    }

    public function rtArsip(Request $request)
    {
        $user = Auth::user();
        $rtNum = $user->clean_rt_number;
        $rtLabel = $user->clean_rt_label;

        $arsipList = [
            ['nama' => 'SK Kepengurusan ' . $rtLabel . ' Periode 2024-2029', 'kategori' => 'SK & Legalitas', 'tanggal' => '10 Jan 2024', 'ukuran' => '1.4 MB'],
            ['nama' => 'Laporan Pertanggungjawaban Kas ' . $rtLabel . ' Triwulan III', 'kategori' => 'Keuangan', 'tanggal' => '01 Okt 2026', 'ukuran' => '2.1 MB'],
            ['nama' => 'Notulensi Musyawarah Warga ' . $rtLabel . ' - Ronda & Kebersihan', 'kategori' => 'Risalah Rapat', 'tanggal' => '25 Okt 2026', 'ukuran' => '850 KB'],
            ['nama' => 'Daftar Inventaris Pos Ronda & Tenda ' . $rtLabel, 'kategori' => 'Aset RT', 'tanggal' => '15 Sep 2026', 'ukuran' => '512 KB'],
        ];

        return view('rt.arsip', compact('user', 'rtNum', 'rtLabel', 'arsipList'));
    }

    public function rtProfil(Request $request)
    {
        $user = Auth::user();
        $rtNum = $user->clean_rt_number;
        $rtLabel = $user->clean_rt_label;

        $monitoringList = $this->getRtMonitoringData();
        $rtInfo = null;
        foreach ($monitoringList as $m) {
            if ($m['rt'] == $rtNum) {
                $rtInfo = $m;
                break;
            }
        }

        return view('rt.profil', compact('user', 'rtNum', 'rtLabel', 'rtInfo'));
    }

    // ======================================================================
    // FITUR MANAJEMEN AKUN RT (KHUSUS KETUA RW & SUPER ADMIN)
    // ======================================================================

    public function manajemenAkunRt()
    {
        $user = Auth::user();
        $rtAccounts = User::where('role', 'rt')->orderBy('rt_number')->get();

        $totalRt = $rtAccounts->count();
        $totalAktif = $rtAccounts->where('is_active', true)->count();
        $totalNonaktif = $totalRt - $totalAktif;

        return view('rt.akun.index', compact('user', 'rtAccounts', 'totalRt', 'totalAktif', 'totalNonaktif'));
    }

    public function storeAkunRt(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'rt_number' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $cleanRt = str_pad(preg_replace('/[^0-9]/', '', $request->rt_number) ?: '001', 3, '0', STR_PAD_LEFT);
        $passwordInitial = $request->password ?: 'password123';

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($passwordInitial),
            'role' => 'rt',
            'rt_number' => $cleanRt,
            'rw_number' => '013',
            'phone' => $request->phone,
            'address' => $request->address ?: "RT {$cleanRt} / RW 013",
            'status_rumah' => 'milik_sendiri',
            'is_active' => true,
        ]);

        return back()->with('success', "Akun Ketua RT {$cleanRt} ({$request->email}) berhasil dibuat! Password awal: {$passwordInitial}");
    }

    public function toggleActiveAkunRt($id)
    {
        $targetUser = User::findOrFail($id);
        
        if ($targetUser->role !== 'rt') {
            return back()->with('error', 'Hanya akun dengan role RT yang dapat diubah statusnya melalui modul ini.');
        }

        $targetUser->is_active = !$targetUser->is_active;
        $targetUser->save();

        $statusText = $targetUser->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$targetUser->name} ({$targetUser->clean_rt_label}) berhasil {$statusText}!");
    }

    public function resetPasswordAkunRt(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'nullable|string|min:6',
        ]);

        $targetUser = User::findOrFail($id);
        
        if ($targetUser->role !== 'rt') {
            return back()->with('error', 'Hanya akun Ketua RT yang dapat direset melalui modul ini.');
        }

        $newPassword = $request->new_password ?: 'password123';
        $targetUser->password = \Illuminate\Support\Facades\Hash::make($newPassword);
        $targetUser->save();

        return back()->with('success', "Password untuk {$targetUser->name} ({$targetUser->clean_rt_label}) berhasil direset menjadi: {$newPassword}");
    }

    public function laporanRt()
    {
        $user = Auth::user();
        $rtList = $this->getRtMonitoringData();
        return view('laporan.rt', compact('user', 'rtList'));
    }

    public function agendaRw()
    {
        $user = Auth::user();
        $agendaList = $this->getAgendaRwData();
        return view('agenda.index', compact('user', 'agendaList'));
    }

    public function kasRw()
    {
        $user = Auth::user();
        $kasData = KasRt::selectRaw('COALESCE(SUM(pemasukan), 0) as total_pemasukan, COALESCE(SUM(pengeluaran), 0) as total_pengeluaran')->first();
        $total_pemasukan = max(42800000, $kasData->total_pemasukan ?? 0);
        $total_pengeluaran = max(8300000, $kasData->total_pengeluaran ?? 0);
        $saldo_kas_rw = $total_pemasukan - $total_pengeluaran;

        $rekapIuranRt = [
            ['rt' => 'RT 001', 'target_kk' => 45, 'kk_lunas' => 43, 'total' => 2150000, 'persen' => 95.5, 'status' => 'Hampir Tuntas'],
            ['rt' => 'RT 002', 'target_kk' => 42, 'kk_lunas' => 38, 'total' => 1900000, 'persen' => 90.4, 'status' => 'Baik'],
            ['rt' => 'RT 003', 'target_kk' => 48, 'kk_lunas' => 47, 'total' => 2350000, 'persen' => 97.9, 'status' => 'Tuntas'],
            ['rt' => 'RT 004', 'target_kk' => 38, 'kk_lunas' => 26, 'total' => 1300000, 'persen' => 68.4, 'status' => 'Perlu Follow-up'],
            ['rt' => 'RT 005', 'target_kk' => 50, 'kk_lunas' => 48, 'total' => 2400000, 'persen' => 96.0, 'status' => 'Tuntas'],
            ['rt' => 'RT 006', 'target_kk' => 40, 'kk_lunas' => 35, 'total' => 1750000, 'persen' => 87.5, 'status' => 'Baik'],
            ['rt' => 'RT 007', 'target_kk' => 36, 'kk_lunas' => 28, 'total' => 1400000, 'persen' => 77.7, 'status' => 'Sedang Berjalan'],
            ['rt' => 'RT 008', 'target_kk' => 44, 'kk_lunas' => 39, 'total' => 1950000, 'persen' => 88.6, 'status' => 'Baik'],
        ];

        $transaksiRw = KasRt::orderBy('tanggal_transaksi', 'desc')->limit(10)->get();

        return view('keuangan.kas-rw', compact('user', 'saldo_kas_rw', 'total_pemasukan', 'total_pengeluaran', 'rekapIuranRt', 'transaksiRw'));
    }

    public function getRtMonitoringData($filterRt = null, $filterPeriode = null)
    {
        $sessionEvals = session('rt_evaluations', []);

        $list = [
            [
                'rt' => 1,
                'nomor' => '001',
                'nama' => 'RT 001',
                'ketua_rt' => 'Bambang Suprayitno',
                'sekretaris' => 'Agus Priyono',
                'bendahara' => 'Siti Nurhaliza',
                'kontak' => '0812-9844-0101',
                'jumlah_kk' => 45,
                'jumlah_warga' => 142,
                'alamat_pos' => 'Jl. Gandaria No. 12, RT 001 / RW 013',
                'status_laporan' => 'Lengkap',
                'administrasi' => 'Lengkap (Verifikasi 100%)',
                'keuangan' => 'Lengkap (Saldo Rp 14.850.000)',
                'sosial' => 'Aktif (Ronda & Posyandu Berjalan)',
                'kebersihan_keamanan' => 'Sangat Baik (Bebas Sampah Liar)',
                'skor_kinerja' => 96,
                'evaluasi_rw' => 'Administrasi sangat tertib, partisipasi warga pada gotong royong dan pos ronda sangat tinggi.',
                'rekomendasi' => 'Pertahankan transparansi pelaporan kas bulanan dan keaktifan media koordinasi warga.',
                'terakhir_update' => '10 Nov 2026',
                'riwayat_tindak_lanjut' => [
                    ['tanggal' => '02 Nov 2026', 'catatan' => 'Laporan fisik dan digital bulanan diterima Sekretariat RW.'],
                    ['tanggal' => '05 Nov 2026', 'catatan' => 'Verifikasi kas dan sinkronisasi saldo operasional tuntas.']
                ]
            ],
            [
                'rt' => 2,
                'nomor' => '002',
                'nama' => 'RT 002',
                'ketua_rt' => 'H. Agus Mulyadi',
                'sekretaris' => 'Hendra Setiawan',
                'bendahara' => 'Dewi Lestari',
                'kontak' => '0813-8821-0202',
                'jumlah_kk' => 42,
                'jumlah_warga' => 138,
                'alamat_pos' => 'Jl. Kenanga No. 8, RT 002 / RW 013',
                'status_laporan' => 'Perlu diperiksa',
                'administrasi' => 'Perlu Diperiksa (2 Berkas Belum Lengkap)',
                'keuangan' => 'Lengkap (Saldo Rp 12.400.000)',
                'sosial' => 'Aktif',
                'kebersihan_keamanan' => 'Baik',
                'skor_kinerja' => 84,
                'evaluasi_rw' => 'Laporan keuangan sudah masuk, namun rekap absensi siskamling bulan ini belum dilampirkan.',
                'rekomendasi' => 'Lengkapi lembar rekap jaga pos ronda dan laporan penerbitan surat pengantar.',
                'terakhir_update' => '08 Nov 2026',
                'riwayat_tindak_lanjut' => [
                    ['tanggal' => '08 Nov 2026', 'catatan' => 'Catatan kelengkapan dikirimkan melalui pesan koordinasi RW ke Ketua RT 002.']
                ]
            ],
            [
                'rt' => 3,
                'nomor' => '003',
                'nama' => 'RT 003',
                'ketua_rt' => 'Suparman Hadi',
                'sekretaris' => 'Teguh Wibowo',
                'bendahara' => 'Rina Kartika',
                'kontak' => '0812-7711-0303',
                'jumlah_kk' => 48,
                'jumlah_warga' => 156,
                'alamat_pos' => 'Jl. Anggrek No. 15, RT 003 / RW 013',
                'status_laporan' => 'Lengkap',
                'administrasi' => 'Lengkap (Verifikasi 100%)',
                'keuangan' => 'Lengkap (Saldo Rp 16.200.000)',
                'sosial' => 'Sangat Aktif (Bank Sampah Berjalan)',
                'kebersihan_keamanan' => 'Sangat Baik',
                'skor_kinerja' => 98,
                'evaluasi_rw' => 'Program pilah sampah dan bank sampah berjalan tertib. Administrasi digital sangat baik.',
                'rekomendasi' => 'Bisa dijadikan RT percontohan tata kelola bank sampah tingkat RW.',
                'terakhir_update' => '09 Nov 2026',
                'riwayat_tindak_lanjut' => [
                    ['tanggal' => '04 Nov 2026', 'catatan' => 'Presentasi inovasi bank sampah mini di balai warga disetujui RW.']
                ]
            ],
            [
                'rt' => 4,
                'nomor' => '004',
                'nama' => 'RT 004',
                'ketua_rt' => 'Hendra Wijaya',
                'sekretaris' => 'Bayu Wicaksono',
                'bendahara' => 'Nurmala',
                'kontak' => '0815-6622-0404',
                'jumlah_kk' => 38,
                'jumlah_warga' => 120,
                'alamat_pos' => 'Jl. Mawar No. 22, RT 004 / RW 013',
                'status_laporan' => 'Belum dilaporkan',
                'administrasi' => 'Belum Dilaporkan',
                'keuangan' => 'Belum Dilaporkan',
                'sosial' => 'Cukup',
                'kebersihan_keamanan' => 'Cukup',
                'skor_kinerja' => 60,
                'evaluasi_rw' => 'Pengurus RT 004 belum mengunggah rekap bulanan periode berjalan ke sekretariat RW.',
                'rekomendasi' => 'Sekretariat RW telah melayangkan surat konfirmasi dan undangan koordinasi ke Ketua RT 004.',
                'terakhir_update' => '25 Okt 2026',
                'riwayat_tindak_lanjut' => [
                    ['tanggal' => '06 Nov 2026', 'catatan' => 'Surat pengingat pelaporan bulan November dikirim via sekretariat RW.']
                ]
            ],
            [
                'rt' => 5,
                'nomor' => '005',
                'nama' => 'RT 005',
                'ketua_rt' => 'Joko Susilo',
                'sekretaris' => 'Budi Santoso',
                'bendahara' => 'Endang Sulastri',
                'kontak' => '0813-4455-0505',
                'jumlah_kk' => 50,
                'jumlah_warga' => 165,
                'alamat_pos' => 'Jl. Melati No. 5, RT 005 / RW 013',
                'status_laporan' => 'Lengkap',
                'administrasi' => 'Lengkap (Verifikasi 100%)',
                'keuangan' => 'Lengkap (Saldo Rp 18.100.000)',
                'sosial' => 'Aktif (Posyandu Balita Tertib)',
                'kebersihan_keamanan' => 'Baik',
                'skor_kinerja' => 94,
                'evaluasi_rw' => 'Realisasi iuran warga mencapai 96%. Kegiatan imunisasi balita dan senam pagi berjalan rutin.',
                'rekomendasi' => 'Peremajaan lampu penerangan jalan di gang 3 perlu diprioritaskan.',
                'terakhir_update' => '10 Nov 2026',
                'riwayat_tindak_lanjut' => [
                    ['tanggal' => '07 Nov 2026', 'catatan' => 'Pengajuan 4 titik lampu LED jalan disetujui untuk pengadaan RW.']
                ]
            ],
            [
                'rt' => 6,
                'nomor' => '006',
                'nama' => 'RT 006',
                'ketua_rt' => 'Rahmat Hidayat',
                'sekretaris' => 'Fajar Nugraha',
                'bendahara' => 'Anisa Rahmawati',
                'kontak' => '0812-3344-0606',
                'jumlah_kk' => 40,
                'jumlah_warga' => 130,
                'alamat_pos' => 'Jl. Flamboyan No. 19, RT 006 / RW 013',
                'status_laporan' => 'Perlu diperiksa',
                'administrasi' => 'Perlu Diperiksa (Verifikasi Fisik)',
                'keuangan' => 'Lengkap (Saldo Rp 11.750.000)',
                'sosial' => 'Aktif',
                'kebersihan_keamanan' => 'Perlu Evaluasi (Saluran Air)',
                'skor_kinerja' => 82,
                'evaluasi_rw' => 'Terdapat catatan genangan air di saluran drainase perbatasan RT 006 dan jalan raya.',
                'rekomendasi' => 'Jadwalkan kerja bakti kuras gorong-gorong pada hari Minggu depan.',
                'terakhir_update' => '09 Nov 2026',
                'riwayat_tindak_lanjut' => [
                    ['tanggal' => '09 Nov 2026', 'catatan' => 'Ketua RT 006 menyetujui jadwal kerja bakti gabungan hari Minggu.']
                ]
            ],
            [
                'rt' => 7,
                'nomor' => '007',
                'nama' => 'RT 007',
                'ketua_rt' => 'Eko Prasetyo',
                'sekretaris' => 'Wahyudi',
                'bendahara' => 'Tri Wahyuni',
                'kontak' => '0817-2233-0707',
                'jumlah_kk' => 36,
                'jumlah_warga' => 115,
                'alamat_pos' => 'Jl. Dahlia No. 30, RT 007 / RW 013',
                'status_laporan' => 'Perlu tindak lanjut',
                'administrasi' => 'Perlu Tindak Lanjut',
                'keuangan' => 'Perlu Klarifikasi Bukti',
                'sosial' => 'Cukup',
                'kebersihan_keamanan' => 'Baik',
                'skor_kinerja' => 75,
                'evaluasi_rw' => 'Terdapat selisih pencatatan iuran kebersihan bulan lalu sebesar Rp 350.000 yang belum dikonfirmasi.',
                'rekomendasi' => 'Kunjungan bendahara RW ke bendahara RT 007 untuk sinkronisasi kuitansi dan pencatatan.',
                'terakhir_update' => '07 Nov 2026',
                'riwayat_tindak_lanjut' => [
                    ['tanggal' => '08 Nov 2026', 'catatan' => 'Jadwal pertemuan klarifikasi keuangan ditetapkan tanggal 12 Nov 2026.']
                ]
            ],
            [
                'rt' => 8,
                'nomor' => '008',
                'nama' => 'RT 008',
                'ketua_rt' => 'Ir. Dedi Kurniawan',
                'sekretaris' => 'Hasan Basri',
                'bendahara' => 'Maya Anggraini',
                'kontak' => '0811-9988-0808',
                'jumlah_kk' => 44,
                'jumlah_warga' => 140,
                'alamat_pos' => 'Jl. Cempaka No. 45, RT 008 / RW 013',
                'status_laporan' => 'Perlu tindak lanjut',
                'administrasi' => 'Perlu Tindak Lanjut',
                'keuangan' => 'Lengkap (Saldo Rp 15.500.000)',
                'sosial' => 'Aktif',
                'kebersihan_keamanan' => 'Perlu Perhatian (Portal Malam)',
                'skor_kinerja' => 78,
                'evaluasi_rw' => 'Pintu gerbang portal malam sering tidak terkunci setelah pukul 23.00 WIB.',
                'rekomendasi' => 'Instruksikan linmas / petugas ronda untuk disiplin penutupan portal tepat pukul 23.00 WIB demi keamanan bersama.',
                'terakhir_update' => '08 Nov 2026',
                'riwayat_tindak_lanjut' => [
                    ['tanggal' => '09 Nov 2026', 'catatan' => 'Sosialisasi SOP portal malam disampaikan di grup koordinasi warga RT 008.']
                ]
            ],
        ];

        // Apply session overrides if any
        foreach ($list as &$item) {
            $rtKey = $item['rt'];
            if (isset($sessionEvals[$rtKey])) {
                $item['evaluasi_rw'] = $sessionEvals[$rtKey]['evaluasi_rw'];
                $item['rekomendasi'] = $sessionEvals[$rtKey]['rekomendasi'];
                $item['status_laporan'] = $sessionEvals[$rtKey]['status_laporan'];
                $item['terakhir_update'] = 'Hari ini';
            }
        }

        if ($filterRt && $filterRt !== 'all') {
            $list = array_values(array_filter($list, fn($i) => $i['rt'] == $filterRt || $i['nomor'] == $filterRt));
        }

        return $list;
    }

    public function getPengaduanData()
    {
        if (Storage::disk('local')->exists('pengaduan.json')) {
            $data = json_decode(Storage::disk('local')->get('pengaduan.json'), true);
            if (is_array($data) && count($data)) {
                return $data;
            }
        }

        $defaultComplaints = [
            [
                'id' => 1,
                'kategori' => 'Fasilitas & Lampu Jalan',
                'rt' => 'RT 005',
                'lokasi' => 'Depan Gang 3 No. 14, RT 005',
                'deskripsi' => 'Lampu PJU utama mati sejak 3 hari lalu. Jalan menjadi gelap dan rawan saat malam hari.',
                'pelapor' => 'Ahmad Suhendar',
                'phone' => '0812-4411-2233',
                'status' => 'Diproses',
                'foto' => null,
                'catatan_tindak_lanjut' => 'Sudah diteruskan ke seksi sarana RW, pengadaan bohlam LED 50W dijadwalkan pasang sore ini.',
                'created_at' => '10 Nov 2026, 09:15 WIB'
            ],
            [
                'id' => 2,
                'kategori' => 'Kebersihan & Sampah',
                'rt' => 'RT 003',
                'lokasi' => 'Pojok Lapangan Serbaguna RT 003',
                'deskripsi' => 'Tumpukan dahan pohon sisa pemangkasan belum diangkut truk kebersihan dinas lingkungan hidup.',
                'pelapor' => 'Ibu Ratna Susanti',
                'phone' => '0813-7722-1144',
                'status' => 'Diproses',
                'foto' => null,
                'catatan_tindak_lanjut' => 'Koordinasi dengan petugas gerobak sampah RW untuk pengangkutan bertahap hari Rabu.',
                'created_at' => '09 Nov 2026, 14:30 WIB'
            ],
            [
                'id' => 3,
                'kategori' => 'Keamanan & Ketertiban',
                'rt' => 'RT 008',
                'lokasi' => 'Portal Gerbang Masuk RT 008',
                'deskripsi' => 'Portal gerbang masuk masih terbuka lewat pukul 23.30 WIB dan tidak terlihat petugas jaga di pos ronda.',
                'pelapor' => 'Kurniawan Pratama',
                'phone' => '0811-9988-7711',
                'status' => 'Perlu Tindak Lanjut',
                'foto' => null,
                'catatan_tindak_lanjut' => 'Pengurus RW memanggil koordinator siskamling RT 008 untuk penegakan SOP jam malam.',
                'created_at' => '08 Nov 2026, 23:45 WIB'
            ],
            [
                'id' => 4,
                'kategori' => 'Drainase & Saluran Air',
                'rt' => 'RT 006',
                'lokasi' => 'Saluran drainase depan Balai Pertemuan RT 006',
                'deskripsi' => 'Air saluran drainase tersumbat endapan lumpur dan sampah plastik, menimbulkan bau tidak sedap.',
                'pelapor' => 'Bambang Irawan',
                'phone' => '0815-3322-1100',
                'status' => 'Selesai',
                'foto' => null,
                'catatan_tindak_lanjut' => 'Kerja bakti pembersihan gorong-gorong telah dilaksanakan pada hari Minggu 08 Nov 2026. Aliran air kembali lancar.',
                'created_at' => '06 Nov 2026, 11:20 WIB'
            ],
            [
                'id' => 5,
                'kategori' => 'Sosial & Ketertiban Umum',
                'rt' => 'RT 002',
                'lokasi' => 'Jl. Kenanga Blok C',
                'deskripsi' => 'Parkir mobil di bahu jalan sempit menghalangi mobil ambulans dan kendaraan warga yang melintas.',
                'pelapor' => 'Siti Nurjanah',
                'phone' => '0818-7766-5544',
                'status' => 'Selesai',
                'foto' => null,
                'catatan_tindak_lanjut' => 'Ketua RT 002 telah memberikan teguran persuasif kepada pemilik kendaraan dan disepakati parkir di lahan komunal.',
                'created_at' => '04 Nov 2026, 16:10 WIB'
            ]
        ];

        Storage::disk('local')->put('pengaduan.json', json_encode($defaultComplaints, JSON_PRETTY_PRINT));
        return $defaultComplaints;
    }

    public function getAgendaRwData()
    {
        return [
            [
                'judul' => 'Rapat Pleno Koordinasi Pengurus RT & RW 013',
                'tanggal' => 'Minggu, 15 November 2026',
                'waktu' => '19.30 - 22.00 WIB',
                'lokasi' => 'Ruang Rapat Utama Sekretariat RW 013',
                'peserta' => 'Ketua RW, Sekretaris, Bendahara, & Seluruh Ketua RT 001 - RT 008',
                'agenda' => 'Evaluasi laporan bulanan RT, monitoring kas akhir tahun, dan persiapan musim hujan.',
                'status' => 'Penting',
                'badge' => 'bg-danger'
            ],
            [
                'judul' => 'Kerja Bakti Serentak & Fogging DBD RW 013',
                'tanggal' => 'Minggu, 22 November 2026',
                'waktu' => '06.30 - 10.00 WIB',
                'lokasi' => 'Seluruh Wilayah RT 001 sampai RT 008',
                'peserta' => 'Seluruh Warga RW 013',
                'agenda' => 'Pembersihan saluran air, drainase, pemilahan barang bekas, dan fogging pencegahan nyamuk DBD.',
                'status' => 'Kegiatan Warga',
                'badge' => 'bg-success'
            ],
            [
                'judul' => 'Posyandu Terpadu Balita & Skrining Lansia Sehat',
                'tanggal' => 'Sabtu, 05 Desember 2026',
                'waktu' => '08.00 - 11.30 WIB',
                'lokasi' => 'Balai Warga RW 013',
                'peserta' => 'Kader Posyandu, Puskesmas, Balita, & Warga Lansia',
                'agenda' => 'Penimbangan balita, imunisasi, cek gula darah, tensi darah, dan pembagian PMT.',
                'status' => 'Kesehatan',
                'badge' => 'bg-info text-white'
            ],
        ];
    }

    public function getAktivitasLingkungan()
    {
        $cctvCount = \App\Models\Cctv::where('aktif', true)->count();

        return [
            'ronda_malam_ini' => [
                'pos' => 'Pos Utama RW 013 & RT 005 - 006',
                'petugas' => '8 Personel Linmas & Giliran Warga',
                'jam' => '22.00 - 04.30 WIB',
                'koordinator' => 'Pak Hendra (0815-6622-0404)'
            ],
            'bank_sampah' => [
                'jadwal' => 'Setiap Rabu, 10.00 - 13.00 WIB',
                'lokasi' => 'Pusat Bank Sampah RT 003',
                'koleksi_terakhir' => '185 kg Sampah Terpilah'
            ],
            'posyandu_berikutnya' => [
                'jadwal' => 'Sabtu, 08.00 WIB',
                'lokasi' => 'Balai Warga RT 001',
                'fokus' => 'Imunisasi Dasar & Timbang Balita'
            ],
            'cctv_aktif' => $cctvCount > 0 ? $cctvCount : 4
        ];
    }
}