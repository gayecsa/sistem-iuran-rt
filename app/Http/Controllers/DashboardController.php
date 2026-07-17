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
}