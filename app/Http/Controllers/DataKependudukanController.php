<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\KartuKeluarga;
use App\Models\KkVerifikasiHistory;
use App\Models\WargaStatusHistory;
use App\Models\WargaMutasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DataKependudukanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * PUSAT DATA KEPENDUDUKAN RW 013 (Unified Module with 3 Tabs)
     * Tab A: Data Individu (Warga)
     * Tab B: Data Keluarga (Kartu Keluarga)
     * Tab C: Dokumen KK Resmi
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Warga biasa diarahkan ke kartu keluarga / biodata miliknya sendiri
        if ($user->isWarga()) {
            if ($user->kartu_keluarga_id) {
                return redirect()->route('kependudukan.keluarga.detail', $user->kartu_keluarga_id);
            }
            return redirect()->route('warga.show', $user->id);
        }

        abort_unless($user->isRw() || $user->isRt() || $user->isBendahara(), 403, 'Akses khusus pengurus wilayah.');

        $activeTab = $request->input('tab', 'individu');
        if (!in_array($activeTab, ['individu', 'keluarga', 'dokumen', 'pendatang', 'mutasi'])) {
            $activeTab = 'individu';
        }

        // ======================================================================
        // 1. DASHBOARD RINGKASAN KEPENDUDUKAN (METRIK AKTUAL DATABASE)
        // ======================================================================
        $wargaBase = User::whereIn('role', ['warga', 'rt']);
        $kkBase = KartuKeluarga::query();

        // Query Dasar Pendatang (Kontrak / Sewa / Kos / Domisili Non-Tetap)
        $pendatangBase = User::whereIn('role', ['warga', 'rt'])->where(function ($q) {
            $q->whereIn('status_rumah', ['kontrak', 'sewa', 'kos', 'Kontrak', 'Sewa', 'Kos', 'Menumpang'])
              ->orWhereIn('status_domisili', ['Warga Sementara', 'Pendatang Baru', 'Akan Pindah']);
        });

        // Query Dasar Mutasi
        $mutasiBase = WargaMutasi::query();

        if ($user->isRt()) {
            $cleanRt = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);
            $rtInt = intval($cleanRt);
            $wargaBase->where(function ($q) use ($cleanRt, $user) {
                $q->where('rt_number', $cleanRt)
                  ->orWhere('rt_number', intval($cleanRt))
                  ->orWhere('address', 'like', "%RT 00{$user->clean_rt_number}%");
            });
            $kkBase->where(function ($q) use ($cleanRt) {
                $q->where('rt', $cleanRt)
                  ->orWhere('rt', intval($cleanRt))
                  ->orWhere('rt', 'RT ' . $cleanRt);
            });
            $pendatangBase->where(function ($q) use ($cleanRt, $rtInt, $user) {
                $q->where('rt_number', $cleanRt)
                  ->orWhere('rt_number', $rtInt)
                  ->orWhere('address', 'like', "%RT 00{$user->clean_rt_number}%");
            });
            $mutasiBase->where(function ($q) use ($cleanRt, $rtInt) {
                $q->where('rt_asal', $cleanRt)
                  ->orWhere('rt_asal', $rtInt)
                  ->orWhere('rt_asal', 'RT ' . $cleanRt)
                  ->orWhere('rt_tujuan', $cleanRt)
                  ->orWhere('rt_tujuan', $rtInt)
                  ->orWhere('rt_tujuan', 'RT ' . $cleanRt);
            });
        }

        $totalWarga = (clone $wargaBase)->count();
        $totalKk = (clone $kkBase)->count();
        $totalLaki = (clone $wargaBase)->where('gender', 'Laki-laki')->count();
        $totalPerempuan = (clone $wargaBase)->where('gender', 'Perempuan')->count();
        $totalPendatang = (clone $pendatangBase)->count();
        $totalMutasi = (clone $mutasiBase)->count();

        $kkTerverifikasi = (clone $kkBase)->where('status_verifikasi_dukcapil', 'Terverifikasi')->count();
        $kkMenunggu = (clone $kkBase)->where('status_verifikasi_dukcapil', 'Menunggu Verifikasi')->count();
        $kkPerluPembaruan = (clone $kkBase)->where(function ($q) {
            $q->whereNotIn('status_verifikasi_dukcapil', ['Terverifikasi', 'Menunggu Verifikasi'])
              ->orWhereNull('status_verifikasi_dukcapil');
        })->count();

        // Distribusi Warga per RT (RT 001 - RT 008)
        $distribusiRt = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtPad = str_pad($i, 3, '0', STR_PAD_LEFT);
            $cWarga = User::whereIn('role', ['warga', 'rt'])->where(function ($q) use ($i, $rtPad) {
                $q->where('rt_number', $i)
                  ->orWhere('rt_number', $rtPad)
                  ->orWhere('rt_number', 'RT ' . $rtPad)
                  ->orWhere('address', 'like', "%RT 00{$i}%")
                  ->orWhere('address', 'like', "%RT {$i}%");
            })->count();

            $cKk = KartuKeluarga::where(function ($q) use ($i, $rtPad) {
                $q->where('rt', $i)
                  ->orWhere('rt', $rtPad)
                  ->orWhere('rt', 'RT ' . $rtPad);
            })->count();

            $distribusiRt[] = [
                'rt' => $rtPad,
                'warga' => $cWarga,
                'kk' => $cKk,
            ];
        }

        // ======================================================================
        // 2. QUERY TAB SPESIFIK DENGAN FILTER & SEARCH
        // ======================================================================
        $wargas = null;
        $keluargas = null;
        $dokumens = null;
        $pendatangs = null;
        $mutasis = null;

        if ($activeTab === 'individu') {
            $allowedRoles = ($user->isRw() || $user->isSuperAdmin())
                ? ['warga', 'rt', 'rw', 'admin', 'bendahara', 'super_admin']
                : ['warga', 'rt'];
            $queryWarga = User::whereIn('role', $allowedRoles)->with('kartuKeluarga');

            if ($user->isRt()) {
                $cleanRt = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);
                $queryWarga->where(function ($q) use ($cleanRt) {
                    $q->where('rt_number', $cleanRt)
                      ->orWhere('rt_number', intval($cleanRt));
                });
            } elseif ($request->filled('rt') && $request->rt !== 'all') {
                $filterRt = str_pad(intval($request->rt), 3, '0', STR_PAD_LEFT);
                $queryWarga->where(function ($q) use ($filterRt) {
                    $q->where('rt_number', $filterRt)
                      ->orWhere('rt_number', intval($filterRt));
                });
            }

            if ($request->filled('gender') && $request->gender !== 'all') {
                $queryWarga->where('gender', $request->gender);
            }

            if ($request->filled('status_warga') && $request->status_warga !== 'all') {
                $queryWarga->where('status_domisili', $request->status_warga);
            }

            if ($request->filled('status_verifikasi') && $request->status_verifikasi !== 'all') {
                $queryWarga->where('status_verifikasi', $request->status_verifikasi);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $queryWarga->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                      ->orWhere('nik', 'like', "%{$s}%")
                      ->orWhere('no_kk', 'like', "%{$s}%")
                      ->orWhere('address', 'like', "%{$s}%")
                      ->orWhere('phone', 'like', "%{$s}%");
                });
            }

            $wargas = $queryWarga->orderBy('rt_number')->orderBy('name')->paginate(25)->withQueryString();
        } elseif ($activeTab === 'keluarga') {
            $queryKk = KartuKeluarga::with(['kepalaKeluarga', 'anggota'])->withCount('anggota');

            if ($user->isRt()) {
                $cleanRt = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);
                $queryKk->where(function ($q) use ($cleanRt) {
                    $q->where('rt', $cleanRt)->orWhere('rt', intval($cleanRt));
                });
            } elseif ($request->filled('rt') && $request->rt !== 'all') {
                $filterRt = str_pad(intval($request->rt), 3, '0', STR_PAD_LEFT);
                $queryKk->where(function ($q) use ($filterRt) {
                    $q->where('rt', $filterRt)->orWhere('rt', intval($filterRt));
                });
            }

            if ($request->filled('status_verifikasi') && $request->status_verifikasi !== 'all') {
                $queryKk->where('status_verifikasi_dukcapil', $request->status_verifikasi);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $queryKk->where(function ($q) use ($s) {
                    $q->where('no_kk', 'like', "%{$s}%")
                      ->orWhere('nama_kepala_keluarga', 'like', "%{$s}%")
                      ->orWhere('nik_kepala_keluarga', 'like', "%{$s}%")
                      ->orWhere('alamat', 'like', "%{$s}%");
                });
            }

            $keluargas = $queryKk->orderBy('rt')->orderBy('nomor_rumah')->paginate(25)->withQueryString();
        } elseif ($activeTab === 'dokumen') {
            // Tab Dokumen KK
            $queryDokumen = KartuKeluarga::with(['kepalaKeluarga', 'verifikator'])->withCount('anggota');

            if ($user->isRt()) {
                $cleanRt = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);
                $queryDokumen->where(function ($q) use ($cleanRt) {
                    $q->where('rt', $cleanRt)->orWhere('rt', intval($cleanRt));
                });
            } elseif ($request->filled('rt') && $request->rt !== 'all') {
                $filterRt = str_pad(intval($request->rt), 3, '0', STR_PAD_LEFT);
                $queryDokumen->where(function ($q) use ($filterRt) {
                    $q->where('rt', $filterRt)->orWhere('rt', intval($filterRt));
                });
            }

            if ($request->filled('status_verifikasi') && $request->status_verifikasi !== 'all') {
                $queryDokumen->where('status_verifikasi_dukcapil', $request->status_verifikasi);
            }

            if ($request->filled('status_dokumen') && $request->status_dokumen !== 'all') {
                $queryDokumen->where('status_dokumen', $request->status_dokumen);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $queryDokumen->where(function ($q) use ($s) {
                    $q->where('no_kk', 'like', "%{$s}%")
                      ->orWhere('nama_kepala_keluarga', 'like', "%{$s}%")
                      ->orWhere('alamat', 'like', "%{$s}%");
                });
            }

            $dokumens = $queryDokumen->orderBy('rt')->orderBy('nomor_rumah')->paginate(25)->withQueryString();
        } elseif ($activeTab === 'pendatang') {
            // Tab D: Data Warga Pendatang / Kontrak
            $queryPendatang = User::whereIn('role', ['warga', 'rt'])->with('kartuKeluarga')
                ->where(function ($q) {
                    $q->whereIn('status_rumah', ['kontrak', 'sewa', 'kos', 'Kontrak', 'Sewa', 'Kos', 'Menumpang'])
                      ->orWhereIn('status_domisili', ['Warga Sementara', 'Pendatang Baru', 'Akan Pindah']);
                });

            if ($user->isRt()) {
                $cleanRt = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);
                $queryPendatang->where(function ($q) use ($cleanRt, $user) {
                    $q->where('rt_number', $cleanRt)
                      ->orWhere('rt_number', intval($cleanRt))
                      ->orWhere('address', 'like', "%RT 00{$user->clean_rt_number}%");
                });
            } elseif ($request->filled('rt') && $request->rt !== 'all') {
                $filterRt = str_pad(intval($request->rt), 3, '0', STR_PAD_LEFT);
                $queryPendatang->where(function ($q) use ($filterRt) {
                    $q->where('rt_number', $filterRt)
                      ->orWhere('rt_number', intval($filterRt));
                });
            }

            if ($request->filled('status_rumah') && $request->status_rumah !== 'all') {
                $queryPendatang->where('status_rumah', $request->status_rumah);
            }

            if ($request->filled('status_domisili') && $request->status_domisili !== 'all') {
                $queryPendatang->where('status_domisili', $request->status_domisili);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $queryPendatang->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                      ->orWhere('nik', 'like', "%{$s}%")
                      ->orWhere('no_kk', 'like', "%{$s}%")
                      ->orWhere('address', 'like', "%{$s}%")
                      ->orWhere('phone', 'like', "%{$s}%");
                });
            }

            $pendatangs = $queryPendatang->orderBy('rt_number')->orderBy('name')->paginate(25)->withQueryString();
        } elseif ($activeTab === 'mutasi') {
            // Tab E: Data Mutasi Warga
            $queryMutasi = WargaMutasi::with(['user', 'kartuKeluarga', 'pengaju', 'pemeriksa']);

            if ($user->isRt()) {
                $cleanRt = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);
                $rtInt = intval($cleanRt);
                $queryMutasi->where(function ($q) use ($cleanRt, $rtInt) {
                    $q->where('rt_asal', $cleanRt)
                      ->orWhere('rt_asal', $rtInt)
                      ->orWhere('rt_asal', 'RT ' . $cleanRt)
                      ->orWhere('rt_tujuan', $cleanRt)
                      ->orWhere('rt_tujuan', $rtInt)
                      ->orWhere('rt_tujuan', 'RT ' . $cleanRt);
                });
            } elseif ($request->filled('rt') && $request->rt !== 'all') {
                $filterRt = str_pad(intval($request->rt), 3, '0', STR_PAD_LEFT);
                $filterInt = intval($filterRt);
                $queryMutasi->where(function ($q) use ($filterRt, $filterInt) {
                    $q->where('rt_asal', $filterRt)
                      ->orWhere('rt_asal', $filterInt)
                      ->orWhere('rt_tujuan', $filterRt)
                      ->orWhere('rt_tujuan', $filterInt);
                });
            }

            if ($request->filled('jenis_mutasi') && $request->jenis_mutasi !== 'all') {
                $queryMutasi->where('jenis_mutasi', $request->jenis_mutasi);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $queryMutasi->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $queryMutasi->where(function ($q) use ($s) {
                    $q->where('nama_warga', 'like', "%{$s}%")
                      ->orWhere('nik', 'like', "%{$s}%")
                      ->orWhere('nomor_kk', 'like', "%{$s}%")
                      ->orWhere('kode_mutasi', 'like', "%{$s}%");
                });
            }

            $mutasis = $queryMutasi->orderBy('tanggal_mutasi', 'desc')->paginate(25)->withQueryString();
        }

        return view('kependudukan.index', compact(
            'user',
            'activeTab',
            'totalWarga',
            'totalKk',
            'totalLaki',
            'totalPerempuan',
            'totalPendatang',
            'totalMutasi',
            'kkTerverifikasi',
            'kkMenunggu',
            'kkPerluPembaruan',
            'distribusiRt',
            'wargas',
            'keluargas',
            'dokumens',
            'pendatangs',
            'mutasis'
        ));
    }

    /**
     * Simpan Catatan Mutasi Warga Baru
     */
    public function storeMutasi(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isRw() || $user->isRt() || $user->isBendahara(), 403, 'Akses terbatas untuk pengurus.');

        $validated = $request->validate([
            'nama_warga' => 'required|string|max:255',
            'nik' => 'nullable|string|max:25',
            'nomor_kk' => 'nullable|string|max:25',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'no_telepon' => 'nullable|string|max:25',
            'jenis_mutasi' => 'required|in:mutasi_masuk,mutasi_keluar,pindah_antar_rt,perubahan_alamat,kelahiran,kematian',
            'tanggal_mutasi' => 'required|date',
            'rt_asal' => 'nullable|string|max:10',
            'rt_tujuan' => 'nullable|string|max:10',
            'alamat_asal' => 'nullable|string|max:500',
            'alamat_tujuan' => 'nullable|string|max:500',
            'alasan_mutasi' => 'nullable|string|max:500',
        ]);

        $count = WargaMutasi::count() + 1;
        $kodeMutasi = 'MUT-' . date('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $rtAsal = $validated['rt_asal'] ?? ($user->isRt() ? str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT) : null);
        $rtTujuan = $validated['rt_tujuan'] ?? ($user->isRt() ? str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT) : null);

        WargaMutasi::create([
            'kode_mutasi' => $kodeMutasi,
            'jenis_mutasi' => $validated['jenis_mutasi'],
            'nama_warga' => $validated['nama_warga'],
            'nik' => $validated['nik'],
            'nomor_kk' => $validated['nomor_kk'],
            'gender' => $validated['gender'] ?? 'Laki-laki',
            'no_telepon' => $validated['no_telepon'],
            'rt_asal' => $rtAsal ? str_pad(preg_replace('/[^0-9]/', '', $rtAsal), 3, '0', STR_PAD_LEFT) : null,
            'rt_tujuan' => $rtTujuan ? str_pad(preg_replace('/[^0-9]/', '', $rtTujuan), 3, '0', STR_PAD_LEFT) : null,
            'alamat_asal' => $validated['alamat_asal'],
            'alamat_tujuan' => $validated['alamat_tujuan'],
            'tanggal_mutasi' => $validated['tanggal_mutasi'],
            'alasan_mutasi' => $validated['alasan_mutasi'] ?? 'Pencatatan mutasi kependudukan',
            'status' => 'disetujui',
            'pengaju_id' => $user->id,
            'pemeriksa_id' => $user->id,
            'tanggal_verifikasi' => now(),
        ]);

        return redirect()->route('kependudukan.index', ['tab' => 'mutasi'])
            ->with('success', "Catatan mutasi {$validated['nama_warga']} ({$kodeMutasi}) berhasil disimpan.");
    }

    /**
     * DETAIL KELUARGA TERPADU (Single Integrated Family View)
     * Menampilkan informasi KK, identitas kepala keluarga, daftar seluruh anggota,
     * dokumen KK digital, status verifikasi Dukcapil, dan riwayat audit.
     */
    public function detailKeluarga($id)
    {
        $user = auth()->user();
        $kk = KartuKeluarga::with([
            'kepalaKeluarga',
            'anggota',
            'verifikator',
            'verifikasiHistories.petugas',
        ])->findOrFail($id);

        $this->authorizeAksesKk($user, $kk);

        // Ambil daftar KK lain di RT yang sama untuk fitur "Pindah Keluarga"
        $otherKks = KartuKeluarga::where('id', '!=', $kk->id)
            ->where('rt', $kk->rt)
            ->orderBy('nama_kepala_keluarga')
            ->get();

        // Ambil warga tanpa KK di RT ini (untuk quick link anggota yang sudah terdaftar)
        $wargaTanpaKk = User::whereIn('role', ['warga'])
            ->where(function ($q) use ($kk) {
                $q->whereNull('kartu_keluarga_id')
                  ->orWhere('kartu_keluarga_id', 0);
            })
            ->where(function ($q) use ($kk) {
                $q->where('rt_number', $kk->rt)
                  ->orWhere('rt_number', intval($kk->rt));
            })
            ->orderBy('name')
            ->get();

        // Riwayat mutasi status anggota keluarga di KK ini
        $wargaHistories = WargaStatusHistory::with(['user', 'petugas'])
            ->where('kartu_keluarga_id', $kk->id)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        return view('kependudukan.detail_keluarga', compact(
            'user',
            'kk',
            'otherKks',
            'wargaTanpaKk',
            'wargaHistories'
        ));
    }

    /**
     * FITUR TERPADU: Tambah Anggota ke Keluarga
     */
    public function tambahAnggotaKeluarga(Request $request, $id)
    {
        $user = auth()->user();
        $kk = KartuKeluarga::findOrFail($id);

        $this->authorizeKelolaKk($user, $kk);

        $validated = $request->validate([
            'mode' => 'required|in:pilih_warga,input_baru',
            'existing_user_id' => 'nullable|required_if:mode,pilih_warga|exists:users,id',
            'name' => 'nullable|required_if:mode,input_baru|string|max:255',
            'nik' => 'nullable|required_if:mode,input_baru|string|size:16|unique:users,nik',
            'gender' => 'nullable|required_if:mode,input_baru|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|required_if:mode,input_baru|date',
            'hubungan_keluarga' => 'required|string|max:50',
            'status_perkawinan' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'agama' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($validated, $kk, $user) {
            if ($validated['mode'] === 'pilih_warga') {
                $member = User::findOrFail($validated['existing_user_id']);
                $oldKkId = $member->kartu_keluarga_id;

                $member->update([
                    'kartu_keluarga_id' => $kk->id,
                    'no_kk' => $kk->no_kk,
                    'hubungan_keluarga' => $validated['hubungan_keluarga'],
                    'address' => $kk->alamat,
                    'rt_number' => $kk->rt,
                ]);

                WargaStatusHistory::create([
                    'user_id' => $member->id,
                    'kartu_keluarga_id' => $kk->id,
                    'tipe_perubahan' => 'Penambahan Anggota',
                    'status_lama' => 'KK Lama #' . ($oldKkId ?: 'None'),
                    'status_baru' => 'KK #' . $kk->no_kk . ' (' . $validated['hubungan_keluarga'] . ')',
                    'petugas_id' => $user->id,
                    'petugas_nama' => $user->name,
                    'petugas_role' => $user->role,
                    'catatan' => "Warga {$member->name} ditambahkan ke Kartu Keluarga {$kk->nama_kepala_keluarga} sebagai {$validated['hubungan_keluarga']}.",
                    'created_at' => now(),
                ]);
            } else {
                // Buat warga baru di database
                $cleanRt = str_pad(intval($kk->rt), 3, '0', STR_PAD_LEFT);
                $newMember = User::create([
                    'name' => $validated['name'],
                    'nik' => $validated['nik'],
                    'email' => strtolower(str_replace(' ', '', $validated['name'])) . rand(100, 999) . '@haven.local',
                    'password' => bcrypt('password123'),
                    'role' => 'warga',
                    'gender' => $validated['gender'],
                    'tempat_lahir' => $validated['tempat_lahir'] ?? 'Bandung',
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'kartu_keluarga_id' => $kk->id,
                    'no_kk' => $kk->no_kk,
                    'hubungan_keluarga' => $validated['hubungan_keluarga'],
                    'status_perkawinan' => $validated['status_perkawinan'] ?? 'Belum Kawin',
                    'pekerjaan' => $validated['pekerjaan'] ?? 'Pelajar/Mahasiswa',
                    'agama' => $validated['agama'] ?? 'Islam',
                    'phone' => $validated['phone'],
                    'address' => $kk->alamat,
                    'rt_number' => $cleanRt,
                    'rw_number' => '013',
                    'status_domisili' => 'Warga Tetap',
                    'status_verifikasi' => 'Terverifikasi RT',
                    'is_active' => true,
                ]);

                WargaStatusHistory::create([
                    'user_id' => $newMember->id,
                    'kartu_keluarga_id' => $kk->id,
                    'tipe_perubahan' => 'Pendaftaran Warga Baru',
                    'status_lama' => 'Belum Terdaftar',
                    'status_baru' => 'Warga Terdaftar',
                    'petugas_id' => $user->id,
                    'petugas_nama' => $user->name,
                    'petugas_role' => $user->role,
                    'catatan' => "Warga baru {$newMember->name} (NIK: {$newMember->nik}) didaftarkan ke KK {$kk->no_kk}.",
                    'created_at' => now(),
                ]);
            }
        });

        return back()->with('success', 'Anggota keluarga baru berhasil ditambahkan ke Kartu Keluarga.');
    }

    /**
     * FITUR TERPADU: Pindah Anggota Keluarga ke KK Lain
     */
    public function pindahKeluarga(Request $request, $id)
    {
        $user = auth()->user();
        $sourceKk = KartuKeluarga::findOrFail($id);

        $this->authorizeKelolaKk($user, $sourceKk);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'target_kk_id' => 'required|exists:kartu_keluargas,id|different:id',
            'hubungan_keluarga_baru' => 'required|string|max:50',
            'alasan_pindah' => 'required|string|max:255',
        ]);

        $member = User::where('kartu_keluarga_id', $sourceKk->id)->findOrFail($validated['user_id']);
        $targetKk = KartuKeluarga::findOrFail($validated['target_kk_id']);

        // Jika warga adalah Kepala Keluarga, harus ada anggota lain atau ganti kepala dulu
        if ($member->hubungan_keluarga === 'Kepala Keluarga' && $sourceKk->anggota()->count() > 1) {
            return back()->with('error', 'Warga ini merupakan Kepala Keluarga saat ini. Silakan tentukan Kepala Keluarga pengganti terlebih dahulu sebelum memindahkannya ke KK lain.');
        }

        DB::transaction(function () use ($member, $sourceKk, $targetKk, $validated, $user) {
            $oldHubungan = $member->hubungan_keluarga;

            $member->update([
                'kartu_keluarga_id' => $targetKk->id,
                'no_kk' => $targetKk->no_kk,
                'hubungan_keluarga' => $validated['hubungan_keluarga_baru'],
                'address' => $targetKk->alamat,
                'rt_number' => $targetKk->rt,
            ]);

            WargaStatusHistory::create([
                'user_id' => $member->id,
                'kartu_keluarga_id' => $targetKk->id,
                'tipe_perubahan' => 'Pindah Kartu Keluarga',
                'status_lama' => "KK #{$sourceKk->no_kk} ({$oldHubungan})",
                'status_baru' => "KK #{$targetKk->no_kk} ({$validated['hubungan_keluarga_baru']})",
                'petugas_id' => $user->id,
                'petugas_nama' => $user->name,
                'petugas_role' => $user->role,
                'catatan' => "Kepindahan dari KK {$sourceKk->nama_kepala_keluarga} ke KK {$targetKk->nama_kepala_keluarga}. Alasan: {$validated['alasan_pindah']}",
                'created_at' => now(),
            ]);
        });

        return back()->with('success', "Warga {$member->name} berhasil dipindahkan ke Kartu Keluarga {$targetKk->nama_kepala_keluarga}.");
    }

    /**
     * FITUR TERPADU: Perubahan Kepala Keluarga dalam KK
     */
    public function gantiKepalaKeluarga(Request $request, $id)
    {
        $user = auth()->user();
        $kk = KartuKeluarga::findOrFail($id);

        $this->authorizeKelolaKk($user, $kk);

        $validated = $request->validate([
            'new_kepala_id' => 'required|exists:users,id',
            'old_kepala_hubungan' => 'required|string|max:50', // e.g. 'Istri', 'Orang Tua', 'Lainnya'
            'alasan_pergantian' => 'required|string|max:255',
        ]);

        $newKepala = User::where('kartu_keluarga_id', $kk->id)->findOrFail($validated['new_kepala_id']);
        $oldKepala = $kk->kepalaKeluarga;

        // Validasi usia minimal 17 tahun untuk Kepala Keluarga
        if ($newKepala->tanggal_lahir && Carbon::parse($newKepala->tanggal_lahir)->age < 17) {
            return back()->with('error', 'Calon Kepala Keluarga harus berusia minimal 17 tahun atau sudah menikah.');
        }

        DB::transaction(function () use ($kk, $newKepala, $oldKepala, $validated, $user) {
            // Update Kepala Keluarga lama jika ada
            if ($oldKepala && $oldKepala->id !== $newKepala->id) {
                $oldKepala->update([
                    'hubungan_keluarga' => $validated['old_kepala_hubungan'],
                ]);
            }

            // Update Kepala Keluarga baru
            $newKepala->update([
                'hubungan_keluarga' => 'Kepala Keluarga',
            ]);

            // Update KK
            $kk->update([
                'kepala_keluarga_id' => $newKepala->id,
                'nama_kepala_keluarga' => $newKepala->name,
                'nik_kepala_keluarga' => $newKepala->nik,
            ]);

            // Catat Log Riwayat KK
            KkVerifikasiHistory::create([
                'kartu_keluarga_id' => $kk->id,
                'status_lama' => 'Kepala: ' . ($oldKepala ? $oldKepala->name : 'None'),
                'status_baru' => 'Kepala Baru: ' . $newKepala->name,
                'metode_verifikasi' => 'Penetapan Pengurus RT/RW',
                'petugas_id' => $user->id,
                'petugas_nama' => $user->name,
                'petugas_role' => $user->role,
                'catatan' => "Pergantian Kepala Keluarga menjadi {$newKepala->name}. Alasan: {$validated['alasan_pergantian']}",
                'created_at' => now(),
            ]);
        });

        return back()->with('success', "Kepala Keluarga resmi diperbarui menjadi {$newKepala->name}.");
    }

    /**
     * FITUR TERPADU: Unggah / Pembaruan Dokumen KK Digital
     */
    public function uploadDokumenKk(Request $request, $id)
    {
        $user = auth()->user();
        $kk = KartuKeluarga::findOrFail($id);

        $this->authorizeKelolaKk($user, $kk);

        $request->validate([
            'dokumen_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tgl_diterbitkan_dukcapil' => 'nullable|date',
            'sumber_dokumen' => 'nullable|string|max:100',
        ]);

        $file = $request->file('dokumen_kk');
        $fileName = 'KK_' . $kk->no_kk . '_v' . ($kk->versi_dokumen + 1) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('dokumen_kk', $fileName, 'local');

        $oldStatus = $kk->status_verifikasi_dukcapil;

        $kk->update([
            'dokumen_kk_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'tgl_diterbitkan_dukcapil' => $request->tgl_diterbitkan_dukcapil ?: $kk->tgl_diterbitkan_dukcapil,
            'sumber_dokumen' => $request->sumber_dokumen ?: ($user->isRw() ? 'Pengurus RW 013' : 'Pengurus RT ' . $user->clean_rt_number),
            'versi_dokumen' => $kk->versi_dokumen + 1,
            'status_verifikasi_dukcapil' => 'Menunggu Verifikasi',
            'status_dokumen' => 'Aktif',
            'catatan_verifikasi_dukcapil' => 'Dokumen baru diunggah oleh ' . $user->name . '. Menunggu verifikasi keabsahan.',
        ]);

        KkVerifikasiHistory::create([
            'kartu_keluarga_id' => $kk->id,
            'status_lama' => $oldStatus,
            'status_baru' => 'Menunggu Verifikasi (Unggah Dokumen)',
            'metode_verifikasi' => 'Unggah Berkas Digital',
            'petugas_id' => $user->id,
            'petugas_nama' => $user->name,
            'petugas_role' => $user->role,
            'catatan' => 'Unggah dokumen baru: ' . $file->getClientOriginalName() . ' (' . round($file->getSize() / 1024) . ' KB)',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Dokumen KK digital berhasil diunggah. Status dokumen saat ini: Menunggu Verifikasi.');
    }

    /**
     * FITUR TERPADU: Verifikasi Dokumen KK oleh Pengurus RT/RW
     */
    public function verifikasiDokumenKk(Request $request, $id)
    {
        $user = auth()->user();
        abort_unless($user->isRw() || $user->isRt() || $user->isBendahara(), 403, 'Akses khusus pengurus.');

        $kk = KartuKeluarga::findOrFail($id);
        $this->authorizeKelolaKk($user, $kk);

        $request->validate([
            'status_verifikasi' => 'required|in:Terverifikasi,Menunggu Verifikasi,Perlu Pembaruan,Ditolak',
            'metode_verifikasi' => 'required|string|max:100',
            'catatan' => 'required|string|max:500',
        ]);

        $oldStatus = $kk->status_verifikasi_dukcapil;
        $newStatus = $request->status_verifikasi;

        $kk->update([
            'status_verifikasi_dukcapil' => $newStatus,
            'metode_verifikasi' => $request->metode_verifikasi,
            'catatan_verifikasi_dukcapil' => $request->catatan,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        KkVerifikasiHistory::create([
            'kartu_keluarga_id' => $kk->id,
            'status_lama' => $oldStatus,
            'status_baru' => $newStatus,
            'metode_verifikasi' => $request->metode_verifikasi,
            'petugas_id' => $user->id,
            'petugas_nama' => $user->name,
            'petugas_role' => $user->role,
            'catatan' => $request->catatan,
            'created_at' => now(),
        ]);

        return back()->with('success', "Status verifikasi dokumen KK berhasil diperbarui menjadi: {$newStatus}");
    }

    /**
     * FITUR TERPADU: Cetak Salinan KK Resmi
     * SYARAT: Dokumen KK HARUS berstatus 'Terverifikasi'
     */
    public function cetakKk($id)
    {
        $user = auth()->user();
        $kk = KartuKeluarga::with(['kepalaKeluarga', 'anggota', 'verifikator'])->findOrFail($id);

        $this->authorizeAksesKk($user, $kk);

        // VALIDASI KETAT SYARAT VERIFIKASI:
        if (!$kk->isVerifiedDukcapil()) {
            abort(403, "Dokumen KK No. {$kk->masked_no_kk} belum dapat dicetak karena status verifikasi saat ini adalah '{$kk->status_verifikasi_dukcapil}'. Dokumen resmi hanya dapat dicetak setelah diverifikasi sah.");
        }

        // Jika berkas PDF asli tersedia, kirim file
        if ($kk->dokumen_kk_path && Storage::disk('local')->exists($kk->dokumen_kk_path)) {
            $fullPath = Storage::disk('local')->path($kk->dokumen_kk_path);
            $fileName = 'Salinan_KK_' . $kk->no_kk . '.pdf';

            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, private'
            ]);
        }

        // Tampilan cetak resmi standar Warkas Machi RW 013
        return view('kependudukan.cetak_kk', compact('kk', 'user'));
    }

    /**
     * API: Detail Cepat Biodata Warga (Untuk Modal Quickview Anggota Keluarga)
     */
    public function apiWargaDetail($id)
    {
        $user = auth()->user();
        $warga = User::with('kartuKeluarga')->findOrFail($id);

        // Validasi hak akses RT / Warga
        if ($user->isRt() && intval($user->clean_rt_number) !== intval($warga->rt_number)) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'id' => $warga->id,
            'name' => $warga->name,
            'nik' => $warga->masked_nik,
            'full_nik' => ($user->isRw() || $user->isRt()) ? $warga->nik : $warga->masked_nik,
            'no_kk' => $warga->masked_no_kk,
            'hubungan_keluarga' => $warga->hubungan_keluarga ?: 'Anggota Keluarga',
            'gender' => $warga->gender,
            'tempat_lahir' => $warga->tempat_lahir,
            'tanggal_lahir' => $warga->tanggal_lahir ? $warga->tanggal_lahir->format('d/m/Y') : '-',
            'age' => $warga->age,
            'agama' => $warga->agama,
            'status_perkawinan' => $warga->status_perkawinan,
            'pekerjaan' => $warga->pekerjaan,
            'pendidikan_terakhir' => $warga->pendidikan_terakhir,
            'phone' => $warga->phone ?: '-',
            'address' => $warga->address,
            'rt' => 'RT ' . str_pad($warga->rt_number, 3, '0', STR_PAD_LEFT),
            'status_domisili' => $warga->status_domisili,
            'status_verifikasi' => $warga->status_verifikasi,
            'avatar_url' => $warga->avatar_url,
            'detail_url' => route('warga.show', $warga->id),
            'edit_url' => route('warga.edit', $warga->id),
        ]);
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

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status_verifikasi', $request->status);
        } else {
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

        $selectedRt = $request->input('rt', 'all');
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        // Base Queries
        $wargaQuery = User::whereIn('role', ['warga', 'rt']);
        $kkQuery = KartuKeluarga::query();

        // Scope jika Ketua RT
        if ($user->isRt()) {
            $rtNum = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);
            $selectedRt = intval($rtNum);
            $wargaQuery->where(function ($q) use ($rtNum) {
                $q->where('rt_number', $rtNum)->orWhere('rt_number', intval($rtNum));
            });
            $kkQuery->where(function ($q) use ($rtNum) {
                $q->where('rt', $rtNum)->orWhere('rt', intval($rtNum));
            });
        } elseif ($selectedRt !== 'all' && $selectedRt !== null && $selectedRt !== '') {
            $rtPad = str_pad(intval($selectedRt), 3, '0', STR_PAD_LEFT);
            $wargaQuery->where(function ($q) use ($rtPad) {
                $q->where('rt_number', $rtPad)->orWhere('rt_number', intval($rtPad));
            });
            $kkQuery->where(function ($q) use ($rtPad) {
                $q->where('rt', $rtPad)->orWhere('rt', intval($rtPad));
            });
        }

        // 1. Metrik Utama Warga & KK
        $totalWarga = (clone $wargaQuery)->count();
        $totalKk = (clone $kkQuery)->count();
        $totalLaki = (clone $wargaQuery)->where('gender', 'Laki-laki')->count();
        $totalPerempuan = (clone $wargaQuery)->where('gender', 'Perempuan')->count();

        // 2. Status Verifikasi KK
        $kkTerverifikasi = (clone $kkQuery)->where('status_verifikasi_dukcapil', 'Terverifikasi')->count();
        $kkMenunggu = (clone $kkQuery)->where('status_verifikasi_dukcapil', 'Menunggu Verifikasi')->count();
        $kkBelumTerverifikasi = (clone $kkQuery)->where(function ($q) {
            $q->where('status_verifikasi_dukcapil', '!=', 'Terverifikasi')
              ->orWhereNull('status_verifikasi_dukcapil');
        })->count();

        // 3. Kelompok Usia
        $now = Carbon::now();
        $usiaBalita = (clone $wargaQuery)->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(5))->count();
        $usiaAnak = (clone $wargaQuery)->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(12))->whereDate('tanggal_lahir', '<', $now->copy()->subYears(5))->count();
        $usiaRemaja = (clone $wargaQuery)->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(19))->whereDate('tanggal_lahir', '<', $now->copy()->subYears(12))->count();
        $usiaDewasa = (clone $wargaQuery)->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(55))->whereDate('tanggal_lahir', '<', $now->copy()->subYears(19))->count();
        $usiaLansia = (clone $wargaQuery)->whereDate('tanggal_lahir', '<', $now->copy()->subYears(55))->count();

        // 4. Status Domisili & Warga Masuk / Keluar
        $domisiliStats = (clone $wargaQuery)->select('status_domisili', DB::raw('count(*) as total'))
            ->groupBy('status_domisili')
            ->pluck('total', 'status_domisili');

        $wargaTetapCount = (clone $wargaQuery)->where('status_domisili', 'Warga Tetap')->count();
        $wargaSementaraCount = (clone $wargaQuery)->where('status_domisili', 'Warga Sementara')->count();
        $wargaPendatangCount = (clone $wargaQuery)->where('status_domisili', 'Pendatang')->count();
        $wargaMutasiCount = (clone $wargaQuery)->whereIn('status_domisili', ['Pindah Domisili', 'Mutasi Keluar', 'Pindah'])->count();

        // 5. Status Rumah
        $rumahStats = (clone $wargaQuery)->select('status_rumah', DB::raw('count(*) as total'))
            ->groupBy('status_rumah')
            ->pluck('total', 'status_rumah');

        // 6. Rekapitulasi per RT 001 sampai RT 008
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

            $qKkRt = KartuKeluarga::where(function ($q) use ($i, $rtPad) {
                $q->where('rt', $i)
                  ->orWhere('rt', $rtPad)
                  ->orWhere('rt', 'RT ' . $rtPad);
            });

            $rekapPerRt[] = [
                'rt' => 'RT ' . $rtPad,
                'rt_raw' => $i,
                'total_warga' => (clone $qRt)->count(),
                'total_kk' => (clone $qKkRt)->count(),
                'laki' => (clone $qRt)->where('gender', 'Laki-laki')->count(),
                'perempuan' => (clone $qRt)->where('gender', 'Perempuan')->count(),
                'terverifikasi' => (clone $qRt)->whereIn('status_verifikasi', ['Terverifikasi RT', 'Terverifikasi RW'])->count(),
                'kk_terverifikasi' => (clone $qKkRt)->where('status_verifikasi_dukcapil', 'Terverifikasi')->count(),
                'kk_belum' => (clone $qKkRt)->where('status_verifikasi_dukcapil', '!=', 'Terverifikasi')->count(),
            ];
        }

        // 7. Rekap Perubahan Data Warga Terbaru (Audit Log)
        $auditQuery = WargaStatusHistory::with(['user', 'petugas'])->orderBy('created_at', 'desc');
        if ($user->isRt()) {
            $rtNum = $user->clean_rt_number;
            $auditQuery->whereHas('user', function ($q) use ($rtNum) {
                $q->where('rt_number', $rtNum)
                  ->orWhere('rt_number', str_pad($rtNum, 3, '0', STR_PAD_LEFT));
            });
        } elseif ($selectedRt !== 'all' && $selectedRt !== null && $selectedRt !== '') {
            $rtPad = str_pad(intval($selectedRt), 3, '0', STR_PAD_LEFT);
            $auditQuery->whereHas('user', function ($q) use ($rtPad) {
                $q->where('rt_number', $rtPad)
                  ->orWhere('rt_number', intval($rtPad));
            });
        }
        $rekapPerubahan = $auditQuery->limit(10)->get();

        $isPrint = $request->has('print');

        return view('kependudukan.laporan', compact(
            'totalWarga', 'totalKk', 'totalLaki', 'totalPerempuan',
            'kkTerverifikasi', 'kkMenunggu', 'kkBelumTerverifikasi',
            'usiaBalita', 'usiaAnak', 'usiaRemaja', 'usiaDewasa', 'usiaLansia',
            'domisiliStats', 'rumahStats',
            'wargaTetapCount', 'wargaSementaraCount', 'wargaPendatangCount', 'wargaMutasiCount',
            'rekapPerRt', 'rekapPerubahan',
            'selectedRt', 'bulan', 'tahun', 'isPrint', 'user'
        ));
    }

    /**
     * Otorisasi Hak Akses Melihat KK
     */
    protected function authorizeAksesKk(User $user, KartuKeluarga $kk)
    {
        if ($user->isRw() || $user->isBendahara()) {
            return true;
        }

        if ($user->isRt()) {
            if (intval($user->clean_rt_number) === intval($kk->rt)) {
                return true;
            }
            abort(403, "Akses Ditolak: Anda hanya memiliki kewenangan mengakses data keluarga di wilayah RT " . str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT) . ".");
        }

        if ($user->kartu_keluarga_id === $kk->id || $user->no_kk === $kk->no_kk) {
            return true;
        }

        abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang mengakses data Kartu Keluarga ini.');
    }

    /**
     * Otorisasi Hak Akses Mengelola / Mengubah KK
     */
    protected function authorizeKelolaKk(User $user, KartuKeluarga $kk)
    {
        if ($user->isRw()) {
            return true;
        }

        if ($user->isRt()) {
            if (intval($user->clean_rt_number) === intval($kk->rt)) {
                return true;
            }
            abort(403, "Akses Ditolak: Ketua RT hanya dapat mengelola data Kartu Keluarga di wilayah RT-nya sendiri.");
        }

        abort(403, 'Akses Ditolak: Pengubahan data kependudukan resmi hanya dapat diproses oleh Pengurus RT/RW.');
    }
}
