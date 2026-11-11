<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Umkm;
use App\Models\UmkmProduk;
use App\Models\UmkmAuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UmkmController extends Controller
{
    /**
     * Tampilan Utama Katalog Marketplace Publik UMKM RW 013
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Umkm::with(['owner', 'produks']);

        // Scope Publik:
        // UMKM yang tampil di katalog umum wajib berstatus 'diverifikasi' dan 'is_active' = true
        // Pemilik dapat melihat usahanya sendiri meskipun masih dalam status 'menunggu' atau 'perlu_perbaikan'
        $query->where(function ($q) use ($user) {
            $q->where(function ($q2) {
                $q2->where('status_verifikasi', 'diverifikasi')
                   ->where('is_active', true);
            });

            if ($user) {
                $q->orWhere('user_id', $user->id);
            }
        });

        // 1. Pencarian Realistis (Nama Usaha, Produk, Kategori, Pemilik, Alamat)
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('nama_umkm', 'like', "%{$search}%")
                  ->orWhere('jenis_usaha', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('nama_pemilik', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('produks', function ($pq) use ($search) {
                      $pq->where('nama_produk', 'like', "%{$search}%")
                         ->orWhere('deskripsi', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter Kategori
        if ($request->filled('kategori') && $request->kategori !== 'Semua UMKM' && $request->kategori !== 'Semua') {
            $kat = $request->kategori;
            $query->where(function ($q) use ($kat) {
                $q->where('kategori', $kat)
                  ->orWhere('jenis_usaha', 'like', "%{$kat}%");
            });
        }

        // 3. Filter RT (RT 001 - RT 008)
        if ($request->filled('rt') && $request->rt !== 'Semua') {
            $rtNum = str_pad(preg_replace('/[^0-9]/', '', $request->rt), 3, '0', STR_PAD_LEFT);
            $query->where(function ($q) use ($rtNum) {
                $q->where('rt_number', $rtNum)
                  ->orWhere('rt_number', ltrim($rtNum, '0'));
            });
        }

        // 4. Filter Status Usaha (Sedang Buka)
        if ($request->filled('status') && $request->status === 'buka') {
            $query->where('status_usaha', '!=', 'Tutup');
        }

        // 5. Pengurutan (Sorting)
        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'nama_asc':
                $query->orderBy('nama_umkm', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('nama_umkm', 'desc');
                break;
            case 'harga_asc':
                $query->orderBy('harga_mulai', 'asc');
                break;
            case 'harga_desc':
                $query->orderBy('harga_sampai', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
                break;
        }

        $umkms = $query->paginate(12)->withQueryString();

        // Statistik Katalog
        $totalUmkm = Umkm::where('status_verifikasi', 'diverifikasi')->where('is_active', true)->count();
        $totalProduk = UmkmProduk::count();
        
        // Count usaha milik user yang sedang login
        $myUmkmCount = $user ? Umkm::where('user_id', $user->id)->count() : 0;

        // Count pending verifikasi untuk pengurus
        $pendingCount = 0;
        if ($user && in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            $pendingCount = Umkm::where('status_verifikasi', 'menunggu')->count();
        } elseif ($user && $user->role === 'rt') {
            $cleanRt = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);
            $pendingCount = Umkm::where('status_verifikasi', 'menunggu')->where('rt_number', $cleanRt)->count();
        }

        // Daftar Kategori Standard
        $kategoriList = [
            'Semua UMKM',
            'Makanan',
            'Minuman',
            'Kue & Roti',
            'Makanan Ringan',
            'Warung & Sembako',
            'Toko',
            'Fashion',
            'Jasa',
            'Kerajinan',
            'Usaha Rumahan'
        ];

        // Jika request AJAX (debounced search / chip filter)
        if ($request->ajax()) {
            return response()->json([
                'html' => view('umkm.partials.grid', compact('umkms'))->render(),
                'pagination' => view('umkm.partials.pagination', compact('umkms'))->render(),
                'total' => $umkms->total(),
            ]);
        }

        return view('umkm', compact(
            'user',
            'umkms',
            'totalUmkm',
            'totalProduk',
            'pendingCount',
            'myUmkmCount',
            'kategoriList'
        ));
    }

    /**
     * Halaman Khusus: KELOLA UMKM SAYA (Business Owner Dashboard)
     * Hanya menampilkan usaha yang dimiliki secara sah oleh akun pengguna yang sedang login.
     */
    public function saya(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Ambil HANYA usaha yang terhubung dengan user_id pengguna login
        $myUmkms = Umkm::with(['produks', 'auditLogs'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalUsaha = $myUmkms->count();
        $totalMenu = $myUmkms->sum(fn($u) => $u->produks->count());
        $terverifikasiCount = $myUmkms->where('status_verifikasi', 'diverifikasi')->count();
        $menungguCount = $myUmkms->where('status_verifikasi', 'menunggu')->count();
        $perluPerbaikanCount = $myUmkms->where('status_verifikasi', 'perlu_perbaikan')->count();

        // Riwayat aktivitas dari usaha-usaha milik user ini
        $umkmIds = $myUmkms->pluck('id')->toArray();
        $recentActivities = UmkmAuditLog::whereIn('umkm_id', $umkmIds)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('umkm.saya', compact(
            'user',
            'myUmkms',
            'totalUsaha',
            'totalMenu',
            'terverifikasiCount',
            'menungguCount',
            'perluPerbaikanCount',
            'recentActivities'
        ));
    }

    /**
     * Halaman Khusus: PENGAWASAN & VERIFIKASI UMKM (Admin RT & Admin RW)
     * Pengurus memiliki kewenangan moderasi & verifikasi, BUKAN mengubah konten/menu usaha warga!
     */
    public function moderasi(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin', 'rt'])) {
            abort(403, 'Akses terbatas untuk Pengurus RT dan RW.');
        }

        $query = Umkm::with(['owner', 'produks', 'moderator', 'auditLogs']);

        // Scope Wilayah:
        // Admin RT HANYA melihat UMKM di RT masing-masing
        // Admin RW melihat seluruh RT 001 s/d RT 008
        if ($user->role === 'rt') {
            $cleanRt = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);
            $query->where(function($q) use ($cleanRt) {
                $q->where('rt_number', $cleanRt)->orWhere('rt_number', ltrim($cleanRt, '0'));
            });
        } elseif ($request->filled('rt') && $request->rt !== 'Semua') {
            $rtNum = str_pad(preg_replace('/[^0-9]/', '', $request->rt), 3, '0', STR_PAD_LEFT);
            $query->where('rt_number', $rtNum);
        }

        // Filter Status Verifikasi
        if ($request->filled('status_verifikasi') && $request->status_verifikasi !== 'Semua') {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        // Filter Status Kepemilikan (Terhubung / Belum Terhubung)
        if ($request->filled('status_kepemilikan') && $request->status_kepemilikan !== 'Semua') {
            if ($request->status_kepemilikan === 'belum_terhubung') {
                $query->whereNull('user_id');
            } elseif ($request->status_kepemilikan === 'terhubung') {
                $query->whereNotNull('user_id');
            }
        }

        // Filter Status Publikasi (Aktif / Nonaktif)
        if ($request->filled('status_publikasi') && $request->status_publikasi !== 'Semua') {
            $query->where('is_active', $request->status_publikasi === 'aktif');
        }

        // Search kata kunci
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('nama_umkm', 'like', "%{$search}%")
                  ->orWhere('nama_pemilik', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $umkms = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Audit Logs Terkini untuk Pengawasan
        $auditQuery = UmkmAuditLog::with(['umkm', 'user'])->orderBy('created_at', 'desc');
        if ($user->role === 'rt') {
            $cleanRt = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);
            $auditQuery->whereHas('umkm', function($q) use ($cleanRt) {
                $q->where('rt_number', $cleanRt);
            });
        }
        $recentAudits = $auditQuery->limit(15)->get();

        // Daftar User Warga untuk form penautan kepemilikan (bagi yang belum terhubung)
        $wargaUsers = User::orderBy('name', 'asc')->get(['id', 'name', 'role', 'rt_number', 'phone']);

        return view('umkm.moderasi', compact(
            'user',
            'umkms',
            'recentAudits',
            'wargaUsers'
        ));
    }

    /**
     * Tampilkan detail data UMKM beserta produk lengkap (JSON untuk modal & API)
     */
    public function show($id)
    {
        $umkm = Umkm::with(['owner', 'produks', 'auditLogs'])->findOrFail($id);
        $user = Auth::user();

        $this->authorize('view', $umkm);

        $isOwner = $user && ($user->id === $umkm->user_id);
        $isPengurus = $user && in_array($user->role, ['rw', 'admin', 'super_admin', 'rt']);

        return response()->json([
            'id' => $umkm->id,
            'nama_umkm' => $umkm->nama_umkm,
            'kategori' => $umkm->kategori,
            'jenis_usaha' => $umkm->jenis_usaha,
            'nama_pemilik' => $umkm->owner_name,
            'owner_avatar' => $umkm->owner_avatar_url,
            'no_hp' => $umkm->no_hp,
            'alamat' => $umkm->alamat,
            'rt_number' => $umkm->clean_rt_label,
            'rw_number' => $umkm->rw_number,
            'kelurahan' => $umkm->kelurahan,
            'kecamatan' => $umkm->kecamatan,
            'kota' => $umkm->kota,
            'latitude' => $umkm->latitude ?: -6.916000,
            'longitude' => $umkm->longitude ?: 107.678000,
            'deskripsi' => $umkm->deskripsi,
            'jam_buka' => $umkm->jam_buka,
            'jam_tutup' => $umkm->jam_tutup,
            'is_buka_sekarang' => $umkm->is_buka_sekarang,
            'status_usaha' => $umkm->status_usaha,
            'status_verifikasi' => $umkm->status_verifikasi,
            'status_kepemilikan' => $umkm->status_kepemilikan,
            'is_active' => $umkm->is_active,
            'catatan_verifikasi' => $umkm->catatan_verifikasi,
            'alasan_moderasi' => $umkm->alasan_moderasi,
            'foto_url' => $umkm->foto_url,
            'kisaran_harga' => $umkm->kisaran_harga_formatted,
            'whatsapp_url' => $umkm->whatsapp_url,
            'gmaps_url' => $umkm->google_maps_url,
            'kategori_badge' => $umkm->kategori_badge,
            'is_owner' => $isOwner,
            'is_pengurus' => $isPengurus,
            'produks' => $umkm->produks->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nama_produk' => $p->nama_produk,
                    'kategori_produk' => $p->kategori_produk,
                    'harga' => $p->harga,
                    'harga_rupiah' => $p->harga_rupiah,
                    'deskripsi' => $p->deskripsi,
                    'foto_url' => $p->foto_url,
                    'is_tersedia' => $p->is_tersedia,
                    'is_best_seller' => $p->is_best_seller,
                ];
            }),
        ]);
    }

    /**
     * Pendaftaran UMKM Baru oleh Pemilik Usaha
     * Keamanan Backend: user_id SELALU diambil dari pengguna yang sedang login (Auth::id()),
     * BUKAN dari input form (mencegah manipulasi/privilege escalation).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu untuk mendaftarkan usaha.');
        }

        $this->authorize('create', Umkm::class);

        $request->validate([
            'nama_umkm' => 'required|string|max:255',
            'jenis_usaha' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'nama_pemilik' => 'required|string|max:255',
            'no_hp' => 'required|string|max:25',
            'alamat' => 'required|string',
            'rt_number' => 'required|string|max:10',
            'deskripsi' => 'nullable|string',
            'jam_buka' => 'nullable|string|max:10',
            'jam_tutup' => 'nullable|string|max:10',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('umkm_photos', 'public');
        }

        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $request->rt_number), 3, '0', STR_PAD_LEFT);

        // Alur Verifikasi: Setiap pendaftaran baru wajib melalui status 'menunggu'
        // untuk diverifikasi oleh Pengurus RT/RW demi validitas kependudukan wilayah.
        $umkm = Umkm::create([
            'user_id' => $user->id, // KEAMANAN: Strict kepemilikan terikat pada akun login
            'nama_umkm' => $request->nama_umkm,
            'jenis_usaha' => $request->jenis_usaha,
            'kategori' => $request->kategori,
            'nama_pemilik' => $request->nama_pemilik,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'rt_number' => $rtStr,
            'rw_number' => '013',
            'kelurahan' => 'Sukamiskin',
            'kecamatan' => 'Arcamanik',
            'kota' => 'Kota Bandung',
            'latitude' => $request->latitude ?: -6.916000,
            'longitude' => $request->longitude ?: 107.678000,
            'deskripsi' => $request->deskripsi,
            'jam_buka' => $request->jam_buka ?: '08:00',
            'jam_tutup' => $request->jam_tutup ?: '21:00',
            'foto' => $fotoPath,
            'status_usaha' => 'Buka',
            'status_verifikasi' => 'menunggu',
            'is_active' => true,
            'harga_mulai' => $request->harga_mulai ?: 0,
            'harga_sampai' => $request->harga_sampai ?: 0,
        ]);

        // Audit Log
        UmkmAuditLog::record(
            $umkm->id,
            'tambah_umkm',
            "Pendaftaran usaha baru '{$umkm->nama_umkm}' oleh {$user->name} ({$user->role}). Status: Menunggu Verifikasi.",
            null,
            $umkm->toArray()
        );

        return redirect()->route('umkm.saya')->with('success', 'Pendaftaran usaha berhasil diajukan! Usaha Anda sedang menunggu verifikasi Pengurus RT/RW.');
    }

    /**
     * Form Edit UMKM (Khusus Pemilik Usaha)
     */
    public function edit($id)
    {
        $umkm = Umkm::with('produks')->findOrFail($id);

        // KEAMANAN BACKEND: Validasi ketat menggunakan Laravel Policy
        // Jika bukan pemilik usaha, lemparkan HTTP 403 Forbidden!
        // Admin RT/RW sekalipun TIDAK BISA mengedit konten usaha warga!
        $this->authorize('update', $umkm);

        return view('umkm.edit', compact('umkm'));
    }

    /**
     * Update Data UMKM (Khusus Pemilik Usaha)
     */
    public function update(Request $request, $id)
    {
        $umkm = Umkm::findOrFail($id);

        // KEAMANAN BACKEND: Otorisasi kepemilikan ketat
        $this->authorize('update', $umkm);

        $request->validate([
            'nama_umkm' => 'required|string|max:255',
            'jenis_usaha' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:25',
            'alamat' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'status_usaha' => 'nullable|string|in:Buka,Tutup',
            'jam_buka' => 'nullable|string|max:10',
            'jam_tutup' => 'nullable|string|max:10',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $dataLama = $umkm->toArray();

        $data = $request->only([
            'nama_umkm', 'jenis_usaha', 'kategori', 'no_hp',
            'alamat', 'deskripsi', 'status_usaha', 'jam_buka', 'jam_tutup',
            'harga_mulai', 'harga_sampai'
        ]);

        if ($request->hasFile('foto')) {
            if ($umkm->foto && !str_starts_with($umkm->foto, 'http') && Storage::disk('public')->exists($umkm->foto)) {
                Storage::disk('public')->delete($umkm->foto);
            }
            $data['foto'] = $request->file('foto')->store('umkm_photos', 'public');
        }

        // Jika sebelumnya berstatus 'perlu_perbaikan', ubah kembali ke 'menunggu' untuk ditinjau ulang
        if ($umkm->status_verifikasi === 'perlu_perbaikan') {
            $data['status_verifikasi'] = 'menunggu';
        }

        $umkm->update($data);

        // Audit Log
        UmkmAuditLog::record(
            $umkm->id,
            'edit_umkm',
            "Pemilik memperbarui informasi profil usaha '{$umkm->nama_umkm}'.",
            $dataLama,
            $umkm->toArray()
        );

        return redirect()->route('umkm.saya')->with('success', "Informasi usaha {$umkm->nama_umkm} berhasil diperbarui!");
    }

    /**
     * Halaman Khusus Kelola Menu & Produk (Khusus Pemilik Usaha)
     */
    public function kelolaMenu($id)
    {
        $umkm = Umkm::with('produks')->findOrFail($id);

        // KEAMANAN BACKEND: Policy check kepemilikan
        $this->authorize('manageProduk', $umkm);

        return view('umkm.menu', compact('umkm'));
    }

    /**
     * Tambah Produk Baru ke UMKM (Khusus Pemilik Usaha)
     */
    public function storeProduk(Request $request, $umkmId)
    {
        $umkm = Umkm::findOrFail($umkmId);

        // KEAMANAN BACKEND: Strict check via Policy
        $this->authorize('manageProduk', $umkm);

        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'kategori_produk' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('umkm_products', 'public');
        }

        $produk = UmkmProduk::create([
            'umkm_id' => $umkm->id,
            'nama_produk' => $request->nama_produk,
            'kategori_produk' => $request->kategori_produk ?: $umkm->kategori,
            'harga' => (int)$request->harga,
            'deskripsi' => $request->deskripsi,
            'foto' => $fotoPath,
            'is_tersedia' => $request->boolean('is_tersedia', true),
            'is_best_seller' => $request->boolean('is_best_seller', false),
        ]);

        // Perbarui rentang harga UMKM
        $min = $umkm->produks()->min('harga');
        $max = $umkm->produks()->max('harga');
        $umkm->update([
            'harga_mulai' => $min ?: 0,
            'harga_sampai' => $max ?: 0,
        ]);

        // Audit Log
        UmkmAuditLog::record(
            $umkm->id,
            'tambah_menu',
            "Pemilik menambahkan menu baru '{$produk->nama_produk}' seharga {$produk->harga_rupiah}.",
            null,
            $produk->toArray()
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan!',
                'produk' => $produk
            ]);
        }

        return back()->with('success', 'Menu / Produk berhasil ditambahkan!');
    }

    /**
     * Update Produk (Khusus Pemilik Usaha)
     */
    public function updateProduk(Request $request, $id)
    {
        $produk = UmkmProduk::with('umkm')->findOrFail($id);
        $umkm = $produk->umkm;

        // KEAMANAN BACKEND: Strict check via Policy
        $this->authorize('manageProduk', $umkm);

        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'kategori_produk' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $dataLama = $produk->toArray();

        $data = [
            'nama_produk' => $request->nama_produk,
            'kategori_produk' => $request->kategori_produk ?: $produk->kategori_produk,
            'harga' => (int)$request->harga,
            'deskripsi' => $request->deskripsi,
            'is_tersedia' => $request->boolean('is_tersedia', true),
            'is_best_seller' => $request->boolean('is_best_seller', false),
        ];

        if ($request->hasFile('foto')) {
            if ($produk->foto && !str_starts_with($produk->foto, 'http') && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }
            $data['foto'] = $request->file('foto')->store('umkm_products', 'public');
        }

        $produk->update($data);

        // Update rentang harga
        $min = $umkm->produks()->min('harga');
        $max = $umkm->produks()->max('harga');
        $umkm->update([
            'harga_mulai' => $min ?: 0,
            'harga_sampai' => $max ?: 0,
        ]);

        // Audit Log
        $action = ($dataLama['harga'] != $produk->harga) ? 'ubah_harga' : 'edit_menu';
        UmkmAuditLog::record(
            $umkm->id,
            $action,
            "Pemilik memperbarui menu '{$produk->nama_produk}' (Harga: {$produk->harga_rupiah}, Tersedia: " . ($produk->is_tersedia ? 'Ya' : 'Tidak') . ").",
            $dataLama,
            $produk->toArray()
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil diperbarui!',
                'produk' => $produk
            ]);
        }

        return back()->with('success', "Menu {$produk->nama_produk} berhasil diperbarui!");
    }

    /**
     * Hapus Produk UMKM (Khusus Pemilik Usaha)
     */
    public function deleteProduk($id)
    {
        $produk = UmkmProduk::with('umkm')->findOrFail($id);
        $umkm = $produk->umkm;

        // KEAMANAN BACKEND: Strict check via Policy
        $this->authorize('manageProduk', $umkm);

        $nama = $produk->nama_produk;
        $dataLama = $produk->toArray();

        if ($produk->foto && !str_starts_with($produk->foto, 'http') && Storage::disk('public')->exists($produk->foto)) {
            Storage::disk('public')->delete($produk->foto);
        }

        $produk->delete();

        // Update rentang harga
        $min = $umkm->produks()->min('harga');
        $max = $umkm->produks()->max('harga');
        $umkm->update([
            'harga_mulai' => $min ?: 0,
            'harga_sampai' => $max ?: 0,
        ]);

        // Audit Log
        UmkmAuditLog::record(
            $umkm->id,
            'hapus_menu',
            "Pemilik menghapus menu '{$nama}'.",
            $dataLama,
            null
        );

        return back()->with('success', "Menu {$nama} berhasil dihapus.");
    }

    /**
     * Moderasi Verifikasi & Status Usaha (Khusus Pengurus RT & RW)
     * Hanya mengatur status verifikasi/publikasi dengan alasan yang jelas, BUKAN mengubah konten!
     */
    public function postModerasi(Request $request, $id)
    {
        $umkm = Umkm::findOrFail($id);
        $user = Auth::user();

        // Policy check hak moderasi
        $this->authorize('moderate', $umkm);

        $request->validate([
            'action' => 'required|string|in:diverifikasi,perlu_perbaikan,ditolak,dinonaktifkan,aktifkan_kembali',
            'catatan' => 'required|string|min:4',
        ]);

        $action = $request->input('action');
        $catatan = trim($request->input('catatan'));
        $dataLama = $umkm->toArray();

        if ($action === 'diverifikasi') {
            $umkm->status_verifikasi = 'diverifikasi';
            $umkm->catatan_verifikasi = $catatan;
            $umkm->is_active = true;
            $umkm->moderated_by = $user->id;
            $umkm->moderated_at = Carbon::now();
            $logAction = 'setujui_verifikasi';
            $logMsg = "Pengurus ({$user->name} [{$user->role}]) menyetujui verifikasi UMKM '{$umkm->nama_umkm}'. Catatan: {$catatan}";
        } elseif ($action === 'perlu_perbaikan') {
            $umkm->status_verifikasi = 'perlu_perbaikan';
            $umkm->catatan_verifikasi = $catatan;
            $umkm->moderated_by = $user->id;
            $umkm->moderated_at = Carbon::now();
            $logAction = 'minta_perbaikan';
            $logMsg = "Pengurus ({$user->name} [{$user->role}]) meminta perbaikan data untuk UMKM '{$umkm->nama_umkm}'. Catatan revisi: {$catatan}";
        } elseif ($action === 'ditolak') {
            $umkm->status_verifikasi = 'ditolak';
            $umkm->catatan_verifikasi = $catatan;
            $umkm->is_active = false;
            $umkm->moderated_by = $user->id;
            $umkm->moderated_at = Carbon::now();
            $logAction = 'tolak_verifikasi';
            $logMsg = "Pengurus ({$user->name} [{$user->role}]) menolak pengajuan verifikasi UMKM '{$umkm->nama_umkm}'. Alasan: {$catatan}";
        } elseif ($action === 'dinonaktifkan') {
            $umkm->status_verifikasi = 'dinonaktifkan';
            $umkm->is_active = false;
            $umkm->alasan_moderasi = $catatan;
            $umkm->moderated_by = $user->id;
            $umkm->moderated_at = Carbon::now();
            $logAction = 'nonaktifkan';
            $logMsg = "Pengurus ({$user->name} [{$user->role}]) menonaktifkan sementara publikasi UMKM '{$umkm->nama_umkm}'. Alasan moderasi: {$catatan}";
        } elseif ($action === 'aktifkan_kembali') {
            $umkm->status_verifikasi = 'diverifikasi';
            $umkm->is_active = true;
            $umkm->alasan_moderasi = null;
            $umkm->moderated_by = $user->id;
            $umkm->moderated_at = Carbon::now();
            $logAction = 'aktifkan_kembali';
            $logMsg = "Pengurus ({$user->name} [{$user->role}]) mengaktifkan kembali publikasi UMKM '{$umkm->nama_umkm}'. Keterangan: {$catatan}";
        }

        $umkm->save();

        // Audit Log
        UmkmAuditLog::record(
            $umkm->id,
            $logAction,
            $logMsg,
            $dataLama,
            $umkm->toArray()
        );

        return back()->with('success', "Status moderasi UMKM {$umkm->nama_umkm} berhasil diperbarui.");
    }

    /**
     * Menghubungkan Akun Pemilik untuk UMKM yang berstatus 'Belum Terhubung' (Khusus Admin RW)
     */
    public function hubungkanPemilik(Request $request, $id)
    {
        $umkm = Umkm::findOrFail($id);
        $user = Auth::user();

        $this->authorize('linkOwner', $umkm);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'alasan' => 'required|string|min:4',
        ]);

        $targetUser = User::findOrFail($request->user_id);
        $dataLama = $umkm->toArray();

        $umkm->user_id = $targetUser->id;
        $umkm->nama_pemilik = $targetUser->name;
        $umkm->save();

        // Audit Log
        UmkmAuditLog::record(
            $umkm->id,
            'hubungkan_pemilik',
            "Admin ({$user->name}) menetapkan kepemilikan UMKM '{$umkm->nama_umkm}' kepada akun {$targetUser->name} (ID: {$targetUser->id}). Alasan/Dasar penetapan: " . $request->alasan,
            $dataLama,
            $umkm->toArray()
        );

        return back()->with('success', "Kepemilikan UMKM {$umkm->nama_umkm} berhasil dihubungkan ke akun {$targetUser->name}.");
    }

    /**
     * Hapus Usaha UMKM (Khusus Pemilik Usaha)
     */
    public function destroy($id)
    {
        $umkm = Umkm::findOrFail($id);

        $this->authorize('delete', $umkm);

        $nama = $umkm->nama_umkm;
        $dataLama = $umkm->toArray();

        // Catat sebelum dihapus
        UmkmAuditLog::record(
            $umkm->id,
            'hapus_umkm',
            "Pemilik menghapus usaha '{$nama}'.",
            $dataLama,
            null
        );

        $umkm->delete();

        return redirect()->route('umkm.saya')->with('success', "Usaha {$nama} berhasil dihapus dari sistem.");
    }
}
