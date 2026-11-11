<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LetterRequest;
use App\Models\LetterStatusHistory;
use App\Models\LetterType;
use App\Models\User;
use App\Models\WilayahAdministratif;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SuratController extends Controller
{
    /**
     * Dashboard Layanan Surat & Daftar Riwayat Pengajuan (Warga / Pengurus)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Statistik Pengajuan Surat Real-time dari Database
        $stats = [
            'total' => 0,
            'menunggu_rt' => 0,
            'menunggu_rw' => 0,
            'disetujui' => 0,
            'ditolak' => 0,
        ];

        $query = LetterRequest::with(['user', 'rtApprover', 'rwApprover', 'letterType'])->latest();

        if ($user->isWarga()) {
            $query->where('user_id', $user->id);
            $stats['total'] = LetterRequest::where('user_id', $user->id)->count();
            $stats['menunggu_rt'] = LetterRequest::where('user_id', $user->id)->where('status', LetterRequest::STATUS_WAITING_RT)->count();
            $stats['menunggu_rw'] = LetterRequest::where('user_id', $user->id)->where('status', LetterRequest::STATUS_WAITING_RW)->count();
            $stats['disetujui'] = LetterRequest::where('user_id', $user->id)->whereIn('status', [LetterRequest::STATUS_APPROVED, LetterRequest::STATUS_COMPLETED])->count();
            $stats['ditolak'] = LetterRequest::where('user_id', $user->id)->whereIn('status', [LetterRequest::STATUS_REJECTED_RT, LetterRequest::STATUS_REJECTED_RW])->count();
        } elseif ($user->isRt()) {
            $cleanRt = $user->clean_rt_number;
            $query->where('rt_number', $cleanRt);
            $stats['total'] = LetterRequest::where('rt_number', $cleanRt)->count();
            $stats['menunggu_rt'] = LetterRequest::where('rt_number', $cleanRt)->where('status', LetterRequest::STATUS_WAITING_RT)->count();
            $stats['menunggu_rw'] = LetterRequest::where('rt_number', $cleanRt)->where('status', LetterRequest::STATUS_WAITING_RW)->count();
            $stats['disetujui'] = LetterRequest::where('rt_number', $cleanRt)->whereIn('status', [LetterRequest::STATUS_APPROVED, LetterRequest::STATUS_COMPLETED])->count();
            $stats['ditolak'] = LetterRequest::where('rt_number', $cleanRt)->whereIn('status', [LetterRequest::STATUS_REJECTED_RT, LetterRequest::STATUS_REJECTED_RW])->count();
        } else {
            $stats['total'] = LetterRequest::count();
            $stats['menunggu_rt'] = LetterRequest::where('status', LetterRequest::STATUS_WAITING_RT)->count();
            $stats['menunggu_rw'] = LetterRequest::where('status', LetterRequest::STATUS_WAITING_RW)->count();
            $stats['disetujui'] = LetterRequest::whereIn('status', [LetterRequest::STATUS_APPROVED, LetterRequest::STATUS_COMPLETED])->count();
            $stats['ditolak'] = LetterRequest::whereIn('status', [LetterRequest::STATUS_REJECTED_RT, LetterRequest::STATUS_REJECTED_RW])->count();
        }

        // Filter riwayat pengajuan surat
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_surat', $request->jenis);
        }

        $myLetters = $query->paginate(10)->withQueryString();

        // 2. Katalog 18 Layanan Surat Resmi Terpadu (Aktif)
        $kategoriFilter = $request->query('kategori', 'semua');
        $searchKatalog = $request->query('search_katalog');

        $katalogQuery = LetterType::where('is_active', true)->orderBy('urutan');
        if ($kategoriFilter && $kategoriFilter !== 'semua') {
            $katalogQuery->where('kategori', $kategoriFilter);
        }
        if ($searchKatalog) {
            $katalogQuery->where(function($q) use ($searchKatalog) {
                $q->where('nama', 'like', "%{$searchKatalog}%")
                  ->orWhere('singkatan', 'like', "%{$searchKatalog}%")
                  ->orWhere('deskripsi', 'like', "%{$searchKatalog}%")
                  ->orWhere('tujuan', 'like', "%{$searchKatalog}%");
            });
        }
        $katalogSurat = $katalogQuery->get();

        // Data Kategori & Jumlah
        $categories = [
            'semua' => ['label' => 'Semua Kategori', 'count' => LetterType::where('is_active', true)->count()],
            LetterType::KATEGORI_KEPENDUDUKAN => ['label' => 'Kependudukan', 'count' => LetterType::where('is_active', true)->where('kategori', LetterType::KATEGORI_KEPENDUDUKAN)->count()],
            LetterType::KATEGORI_SOSIAL_KELUARGA => ['label' => 'Sosial & Keluarga', 'count' => LetterType::where('is_active', true)->where('kategori', LetterType::KATEGORI_SOSIAL_KELUARGA)->count()],
            LetterType::KATEGORI_USAHA_EKONOMI => ['label' => 'Usaha & Ekonomi', 'count' => LetterType::where('is_active', true)->where('kategori', LetterType::KATEGORI_USAHA_EKONOMI)->count()],
            LetterType::KATEGORI_LINGKUNGAN_KEAMANAN => ['label' => 'Lingkungan & Keamanan', 'count' => LetterType::where('is_active', true)->where('kategori', LetterType::KATEGORI_LINGKUNGAN_KEAMANAN)->count()],
            LetterType::KATEGORI_INTERNAL => ['label' => 'Administrasi Internal', 'count' => LetterType::where('is_active', true)->where('kategori', LetterType::KATEGORI_INTERNAL)->count()],
        ];

        return view('surat.index', compact('user', 'stats', 'myLetters', 'katalogSurat', 'categories', 'kategoriFilter', 'searchKatalog'));
    }

    /**
     * Katalog Layanan Publik untuk Tamu / Masyarakat Umum (Tanpa Login)
     */
    public function publicCatalog(Request $request)
    {
        $kategoriFilter = $request->query('kategori', 'semua');
        $search = $request->query('search');

        $katalogQuery = LetterType::where('is_active', true)->orderBy('urutan');
        if ($kategoriFilter && $kategoriFilter !== 'semua') {
            $katalogQuery->where('kategori', $kategoriFilter);
        }
        if ($search) {
            $katalogQuery->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('singkatan', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%");
            });
        }
        $katalogSurat = $katalogQuery->get();

        $categories = [
            'semua' => ['label' => 'Semua Layanan', 'count' => LetterType::where('is_active', true)->count()],
            LetterType::KATEGORI_KEPENDUDUKAN => ['label' => 'Kependudukan', 'count' => LetterType::where('is_active', true)->where('kategori', LetterType::KATEGORI_KEPENDUDUKAN)->count()],
            LetterType::KATEGORI_SOSIAL_KELUARGA => ['label' => 'Sosial & Keluarga', 'count' => LetterType::where('is_active', true)->where('kategori', LetterType::KATEGORI_SOSIAL_KELUARGA)->count()],
            LetterType::KATEGORI_USAHA_EKONOMI => ['label' => 'Usaha & Ekonomi', 'count' => LetterType::where('is_active', true)->where('kategori', LetterType::KATEGORI_USAHA_EKONOMI)->count()],
            LetterType::KATEGORI_LINGKUNGAN_KEAMANAN => ['label' => 'Lingkungan & Keamanan', 'count' => LetterType::where('is_active', true)->where('kategori', LetterType::KATEGORI_LINGKUNGAN_KEAMANAN)->count()],
            LetterType::KATEGORI_INTERNAL => ['label' => 'Administrasi Internal', 'count' => LetterType::where('is_active', true)->where('kategori', LetterType::KATEGORI_INTERNAL)->count()],
        ];

        return view('surat.public_catalog', compact('katalogSurat', 'categories', 'kategoriFilter', 'search'));
    }

    /**
     * Tampilan Formulir Pengajuan Surat Multi-Step Dinamis
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $jenis = strtolower($request->query('jenis', 'domisili'));

        // Cari master jenis surat dari database
        $letterType = LetterType::where('kode', $jenis)->first();

        if (!$letterType) {
            $letterType = LetterType::where('is_active', true)->orderBy('urutan')->first();
        }

        // Jika surat dinonaktifkan oleh RW
        if ($letterType && !$letterType->is_active) {
            return redirect()->route('surat.index')
                ->with('error', "Mohon maaf, layanan \"{$letterType->nama}\" saat ini sedang dinonaktifkan sementara oleh Pengurus RW 013.");
        }

        $allTypes = LetterType::where('is_active', true)->orderBy('urutan')->get();

        // Alamat default dari database kependudukan
        $cleanRt = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);
        $cleanRw = '013';

        return view('surat.create', compact('user', 'letterType', 'allTypes', 'cleanRt', 'cleanRw'));
    }

    /**
     * Simpan Pengajuan Surat Baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi data umum
        $request->validate([
            'jenis_surat' => 'required|string',
            'keperluan' => 'required|string|min:5|max:1000',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'dokumen_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $letterType = LetterType::where('kode', $request->jenis_surat)->firstOrFail();

        if (!$letterType->is_active) {
            return back()->withInput()->with('error', "Layanan \"{$letterType->nama}\" saat ini sedang dinonaktifkan oleh Pengurus RW.");
        }

        $cleanRt = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);

        // Upload Dokumen Pendukung ke storage privat
        $lampiran = [];
        if ($request->hasFile('dokumen_ktp')) {
            $pathKtp = $request->file('dokumen_ktp')->store('surat_dokumen', 'public');
            $lampiran[] = [
                'nama' => 'Kartu Tanda Penduduk (KTP)',
                'file' => $pathKtp,
                'ukuran' => round($request->file('dokumen_ktp')->getSize() / 1024, 1) . ' KB'
            ];
        }
        if ($request->hasFile('dokumen_kk')) {
            $pathKk = $request->file('dokumen_kk')->store('surat_dokumen', 'public');
            $lampiran[] = [
                'nama' => 'Kartu Keluarga (KK)',
                'file' => $pathKk,
                'ukuran' => round($request->file('dokumen_kk')->getSize() / 1024, 1) . ' KB'
            ];
        }
        if ($request->hasFile('dokumen_pendukung')) {
            $pathDoc = $request->file('dokumen_pendukung')->store('surat_dokumen', 'public');
            $lampiran[] = [
                'nama' => 'Dokumen Pendukung Tambahan',
                'file' => $pathDoc,
                'ukuran' => round($request->file('dokumen_pendukung')->getSize() / 1024, 1) . ' KB'
            ];
        }

        // Tangkap dinamis seluruh field form khusus yang dikonfigurasi pada jenis surat
        $detailKhusus = [];
        if (!empty($letterType->form_fields)) {
            foreach ($letterType->form_fields as $field) {
                $fName = $field['name'];
                if ($request->filled($fName)) {
                    $detailKhusus[$fName] = $request->input($fName);
                }
            }
        }

        $isDraft = $request->input('action') === 'draft';
        
        // Tentukan alur status awal berdasarkan konfigurasi surat
        if ($isDraft) {
            $statusAwal = LetterRequest::STATUS_DRAFT;
        } elseif ($letterType->alur_persetujuan === LetterType::ALUR_INTERNAL_RW) {
            $statusAwal = LetterRequest::STATUS_WAITING_RW;
        } else {
            $statusAwal = LetterRequest::STATUS_WAITING_RT;
        }

        $nomorRegistrasi = LetterRequest::generateNomorRegistrasi($letterType->singkatan ?: $letterType->kode);
        $qrCode = 'VK-' . Carbon::now()->format('Ym') . '-' . ($letterType->singkatan ?: 'SP') . '-' . strtoupper(Str::random(6));

        DB::beginTransaction();
        try {
            $letter = LetterRequest::create([
                'nomor_registrasi' => $nomorRegistrasi,
                'user_id' => $user->id,
                'letter_type_id' => $letterType->id,
                'alur_persetujuan' => $letterType->alur_persetujuan,
                'rt_number' => $cleanRt,
                'rw_number' => '013',
                'jenis_surat' => $letterType->kode,
                'judul_surat' => $letterType->nama,

                // Snapshot Biodata Pemohon
                'nama_lengkap' => $request->nama_lengkap ?: $user->name,
                'nik' => $request->nik ?: ($user->nik ?? '3273' . str_pad($user->id, 12, '0', STR_PAD_LEFT)),
                'no_kk' => $request->no_kk ?: ($user->no_kk ?? '3273' . str_pad($user->id, 12, '1', STR_PAD_LEFT)),
                'tempat_lahir' => $request->tempat_lahir ?: ($user->tempat_lahir ?? 'Bandung'),
                'tanggal_lahir' => $request->tanggal_lahir ?: ($user->tanggal_lahir ? $user->tanggal_lahir->toDateString() : '1990-01-01'),
                'jenis_kelamin' => $request->jenis_kelamin ?: ($user->jenis_kelamin ?? 'Laki-laki'),
                'agama' => $request->agama ?: ($user->agama ?? 'Islam'),
                'status_perkawinan' => $request->status_perkawinan ?: ($user->status_perkawinan ?? 'Kawin'),
                'pekerjaan' => $request->pekerjaan ?: ($user->pekerjaan ?? 'Karyawan Swasta'),
                'pendidikan_terakhir' => $request->pendidikan_terakhir ?: ($user->pendidikan_terakhir ?? 'Sarjana (S1)'),
                'phone' => $request->phone ?: ($user->phone ?? '-'),
                'email' => $request->email ?: $user->email,

                // Snapshot Alamat
                'alamat_jalan' => $request->alamat_jalan ?: ($user->alamat ?? "Jl. Lingkungan RT {$cleanRt}"),
                'no_rumah' => $request->no_rumah ?: ($user->no_rumah ?? '01'),
                'rt' => $cleanRt,
                'rw' => '013',
                'kelurahan' => 'Sukamiskin',
                'kecamatan' => 'Arcamanik',
                'kota' => 'Kota Bandung',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '40293',

                'keperluan' => $request->keperluan,
                'detail_khusus' => $detailKhusus,
                'keterangan_tambahan' => $request->keterangan_tambahan,
                'dokumen_lampiran' => $lampiran,

                'status' => $statusAwal,
                'qr_verification_code' => $qrCode,
            ]);

            // Catat riwayat audit awal
            $letter->histories()->create([
                'user_id' => $user->id,
                'actor_name' => $user->name,
                'actor_role' => $user->role_badge_label,
                'status_sebelumnya' => null,
                'status_baru' => $statusAwal,
                'catatan' => $isDraft ? 'Permohonan disimpan sebagai draft oleh warga.' : "Permohonan {$letterType->nama} berhasil diajukan online ke antrean verifikasi.",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('surat.show', $letter->id)
                ->with('success', 'Permohonan surat berhasil diajukan! Nomor registrasi: ' . $nomorRegistrasi . '. Pantau perkembangan status verifikasi berjenjang Anda di bawah ini.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses permohonan surat: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Detail Status & Tracking Timeline Permohonan Surat
     */
    public function show($id)
    {
        $letter = LetterRequest::with(['user', 'rtApprover', 'rwApprover', 'letterType', 'histories'])->findOrFail($id);
        $user = Auth::user();

        // Otorisasi Akses
        if ($user->isWarga() && $letter->user_id !== $user->id) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang melihat permohonan surat warga lain.');
        }
        if ($user->isRt() && intval($letter->rt_number) !== $user->clean_rt_number) {
            abort(403, "Akses ditolak: Anda hanya berwenang memeriksa permohonan surat untuk warga di {$user->clean_rt_label}.");
        }

        return view('surat.show', compact('letter', 'user'));
    }

    /**
     * Halaman Inbox Permohonan Masuk Khusus RT
     */
    public function rtIndex(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isRt() || $user->isRw(), 403, 'Akses khusus Ketua RT / Pengurus.');

        $cleanRt = $user->clean_rt_number;
        $query = LetterRequest::with(['user', 'letterType'])->where('rt_number', $cleanRt)->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_surat', $request->jenis);
        }

        $letters = $query->paginate(15)->withQueryString();

        $counts = [
            'pending' => LetterRequest::where('rt_number', $cleanRt)->where('status', LetterRequest::STATUS_WAITING_RT)->count(),
            'approved' => LetterRequest::where('rt_number', $cleanRt)->whereIn('status', [LetterRequest::STATUS_WAITING_RW, LetterRequest::STATUS_APPROVED, LetterRequest::STATUS_COMPLETED])->count(),
            'revision' => LetterRequest::where('rt_number', $cleanRt)->where('status', LetterRequest::STATUS_REVISION_RT)->count(),
            'rejected' => LetterRequest::where('rt_number', $cleanRt)->where('status', LetterRequest::STATUS_REJECTED_RT)->count(),
        ];

        return view('surat.rt_index', compact('user', 'letters', 'counts', 'cleanRt'));
    }

    /**
     * RT Menyetujui Surat
     */
    public function rtApprove(Request $request, $id)
    {
        $user = Auth::user();
        abort_unless($user->isRt() || $user->isRw(), 403, 'Akses ditolak.');

        $letter = LetterRequest::with('letterType')->findOrFail($id);

        if ($user->isRt() && intval($letter->rt_number) !== $user->clean_rt_number) {
            abort(403, 'Akses ditolak: Anda hanya berwenang menyetujui surat warga RT Anda.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $signatureToken = hash('sha256', "RT-{$user->id}-{$letter->id}-" . Carbon::now()->timestamp);

        // Cek alur persetujuan: Apakah cukup RT saja atau perlu diteruskan ke RW?
        $isRtOnly = $letter->alur_persetujuan === LetterType::ALUR_RT_ONLY || ($letter->letterType && $letter->letterType->isRtOnly());
        $newStatus = $isRtOnly ? LetterRequest::STATUS_APPROVED : LetterRequest::STATUS_WAITING_RW;
        $officialNumber = $isRtOnly ? ($letter->nomor_surat ?: $letter->generateOfficialNumber()) : $letter->nomor_surat;

        DB::beginTransaction();
        try {
            $letter->update([
                'status' => $newStatus,
                'nomor_surat' => $officialNumber,
                'rt_approved_by' => $user->id,
                'rt_approved_name' => $user->name,
                'rt_approved_at' => Carbon::now(),
                'rt_notes' => $request->notes ?: ($isRtOnly ? 'Disetujui dan disahkan oleh Ketua ' . $user->clean_rt_label . '. Dokumen resmi siap dicetak.' : 'Telah diverifikasi dan disetujui oleh Ketua ' . $user->clean_rt_label . '. Berkas valid dan diteruskan ke Ketua RW 013.'),
                'rt_signature_token' => $signatureToken,
                'rejection_reason' => null,
                'revision_notes' => null,
            ]);

            $letter->histories()->create([
                'user_id' => $user->id,
                'actor_name' => $user->name,
                'actor_role' => 'Ketua ' . $user->clean_rt_label,
                'status_sebelumnya' => LetterRequest::STATUS_WAITING_RT,
                'status_baru' => $newStatus,
                'catatan' => $isRtOnly ? 'Disetujui penuh oleh Ketua RT. Dokumen terbit dan siap dicetak.' : 'Diverifikasi oleh Ketua RT dan diteruskan ke Ketua RW 013.',
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            $pesan = $isRtOnly ? "Permohonan #{$letter->nomor_registrasi} berhasil disetujui dan disahkan oleh RT. Dokumen siap dicetak!" : "Permohonan #{$letter->nomor_registrasi} berhasil diverifikasi dan diteruskan ke Ketua RW 013.";

            return redirect()->route('rt.surat.index')->with('success', $pesan);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses persetujuan RT: ' . $e->getMessage());
        }
    }

    /**
     * RT Menolak Permohonan Surat
     */
    public function rtReject(Request $request, $id)
    {
        $user = Auth::user();
        abort_unless($user->isRt() || $user->isRw(), 403, 'Akses ditolak.');

        $request->validate([
            'reason' => 'required|string|min:5|max:1000',
        ]);

        $letter = LetterRequest::findOrFail($id);

        if ($user->isRt() && intval($letter->rt_number) !== $user->clean_rt_number) {
            abort(403, 'Akses ditolak.');
        }

        DB::beginTransaction();
        try {
            $letter->update([
                'status' => LetterRequest::STATUS_REJECTED_RT,
                'rejection_reason' => $request->reason,
            ]);

            $letter->histories()->create([
                'user_id' => $user->id,
                'actor_name' => $user->name,
                'actor_role' => 'Ketua ' . $user->clean_rt_label,
                'status_sebelumnya' => $letter->status,
                'status_baru' => LetterRequest::STATUS_REJECTED_RT,
                'catatan' => 'Permohonan ditolak oleh Ketua RT. Alasan: ' . $request->reason,
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return back()->with('success', 'Permohonan berhasil ditolak dengan alasan yang tercatat dalam sistem.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses penolakan: ' . $e->getMessage());
        }
    }

    /**
     * RT Meminta Perbaikan / Revisi Berkas
     */
    public function rtRevisi(Request $request, $id)
    {
        $user = Auth::user();
        abort_unless($user->isRt() || $user->isRw(), 403, 'Akses ditolak.');

        $request->validate([
            'notes' => 'required|string|min:5|max:1000',
        ]);

        $letter = LetterRequest::findOrFail($id);

        if ($user->isRt() && intval($letter->rt_number) !== $user->clean_rt_number) {
            abort(403, 'Akses ditolak.');
        }

        DB::beginTransaction();
        try {
            $letter->update([
                'status' => LetterRequest::STATUS_REVISION_RT,
                'revision_notes' => $request->notes,
            ]);

            $letter->histories()->create([
                'user_id' => $user->id,
                'actor_name' => $user->name,
                'actor_role' => 'Ketua ' . $user->clean_rt_label,
                'status_sebelumnya' => $letter->status,
                'status_baru' => LetterRequest::STATUS_REVISION_RT,
                'catatan' => 'Ketua RT meminta perbaikan data/dokumen: ' . $request->notes,
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return back()->with('success', 'Permintaan perbaikan berhasil dikirimkan ke pemohon.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses permintaan perbaikan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Inbox Persetujuan Masuk Khusus RW
     */
    public function rwIndex(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isRw(), 403, 'Akses khusus Ketua RW / Administrator.');

        $query = LetterRequest::with(['user', 'rtApprover', 'letterType'])->latest();

        if ($request->filled('rt')) {
            $query->where('rt_number', $request->rt);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_surat', $request->jenis);
        }

        $letters = $query->paginate(15)->withQueryString();

        $counts = [
            'waiting_rw' => LetterRequest::where('status', LetterRequest::STATUS_WAITING_RW)->count(),
            'approved' => LetterRequest::whereIn('status', [LetterRequest::STATUS_APPROVED, LetterRequest::STATUS_COMPLETED])->count(),
            'rejected' => LetterRequest::whereIn('status', [LetterRequest::STATUS_REJECTED_RW, LetterRequest::STATUS_REJECTED_RT])->count(),
            'total' => LetterRequest::count(),
        ];

        return view('surat.rw_index', compact('user', 'letters', 'counts'));
    }

    /**
     * RW Menyetujui Surat (Penerbitan Nomor Surat Resmi & Siap Dicetak)
     */
    public function rwApprove(Request $request, $id)
    {
        $user = Auth::user();
        abort_unless($user->isRw(), 403, 'Akses khusus Ketua RW / Administrator.');

        $letter = LetterRequest::with('letterType')->findOrFail($id);

        // Jika alur mensyaratkan verifikasi RT terlebih dahulu:
        if ($letter->alur_persetujuan !== LetterType::ALUR_INTERNAL_RW && is_null($letter->rt_approved_at)) {
            abort(400, 'Pelanggaran Alur: Ketua RT wilayah terkait belum memverifikasi permohonan ini.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $officialNumber = $letter->nomor_surat ?: $letter->generateOfficialNumber();
        $signatureToken = hash('sha256', "RW-{$user->id}-{$letter->id}-" . Carbon::now()->timestamp);

        DB::beginTransaction();
        try {
            $letter->update([
                'status' => LetterRequest::STATUS_APPROVED,
                'nomor_surat' => $officialNumber,
                'rw_approved_by' => $user->id,
                'rw_approved_name' => $user->name,
                'rw_approved_at' => Carbon::now(),
                'rw_notes' => $request->notes ?: 'Disetujui dan disahkan oleh Ketua RW 013 Kota Bandung. Nomor surat resmi telah diterbitkan dan sah untuk dicetak.',
                'rw_signature_token' => $signatureToken,
                'rejection_reason' => null,
                'revision_notes' => null,
            ]);

            $letter->histories()->create([
                'user_id' => $user->id,
                'actor_name' => $user->name,
                'actor_role' => 'Ketua RW 013',
                'status_sebelumnya' => LetterRequest::STATUS_WAITING_RW,
                'status_baru' => LetterRequest::STATUS_APPROVED,
                'catatan' => "Pengesahan surat selesai. Nomor Surat Resmi: {$officialNumber}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('rw.surat.index')
                ->with('success', "Surat berhasil disahkan! Nomor resmi diterbitkan: {$officialNumber}. Dokumen kini sah dicetak oleh warga.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses persetujuan RW: ' . $e->getMessage());
        }
    }

    /**
     * RW Menolak Permohonan Surat
     */
    public function rwReject(Request $request, $id)
    {
        $user = Auth::user();
        abort_unless($user->isRw(), 403, 'Akses khusus Ketua RW / Administrator.');

        $request->validate([
            'reason' => 'required|string|min:5|max:1000',
        ]);

        $letter = LetterRequest::findOrFail($id);

        DB::beginTransaction();
        try {
            $letter->update([
                'status' => LetterRequest::STATUS_REJECTED_RW,
                'rejection_reason' => $request->reason,
            ]);

            $letter->histories()->create([
                'user_id' => $user->id,
                'actor_name' => $user->name,
                'actor_role' => 'Ketua RW 013',
                'status_sebelumnya' => $letter->status,
                'status_baru' => LetterRequest::STATUS_REJECTED_RW,
                'catatan' => 'Permohonan ditolak oleh Ketua RW 013. Alasan: ' . $request->reason,
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return back()->with('success', 'Permohonan ditolak oleh RW dan alasan telah tersimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses penolakan: ' . $e->getMessage());
        }
    }

    /**
     * RW Meminta Revisi Berkas
     */
    public function rwRevisi(Request $request, $id)
    {
        $user = Auth::user();
        abort_unless($user->isRw(), 403, 'Akses khusus Ketua RW / Administrator.');

        $request->validate([
            'notes' => 'required|string|min:5|max:1000',
        ]);

        $letter = LetterRequest::findOrFail($id);

        DB::beginTransaction();
        try {
            $letter->update([
                'status' => LetterRequest::STATUS_REVISION_RW,
                'revision_notes' => $request->notes,
            ]);

            $letter->histories()->create([
                'user_id' => $user->id,
                'actor_name' => $user->name,
                'actor_role' => 'Ketua RW 013',
                'status_sebelumnya' => $letter->status,
                'status_baru' => LetterRequest::STATUS_REVISION_RW,
                'catatan' => 'Ketua RW 013 meminta perbaikan berkas: ' . $request->notes,
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return back()->with('success', 'Permintaan revisi dari RW telah diteruskan ke pemohon.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses revisi: ' . $e->getMessage());
        }
    }

    /**
     * Tampilan Cetak Surat Resmi Format Standar Pemerintahan A4
     */
    public function cetak($id)
    {
        $letter = LetterRequest::with(['user', 'rtApprover', 'rwApprover', 'letterType'])->findOrFail($id);
        $user = Auth::user();

        // 1. Otorisasi Akses
        if ($user->isWarga() && $letter->user_id !== $user->id) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang mencetak surat milik warga lain.');
        }
        if ($user->isRt() && intval($letter->rt_number) !== $user->clean_rt_number) {
            abort(403, 'Akses ditolak.');
        }

        // 2. Keamanan Cetak Berdasarkan Alur Persetujuan
        if (!$letter->canPrint()) {
            abort(403, 'Akses ditolak: Dokumen ini belum sah untuk dicetak. Diperlukan persetujuan lengkap sesuai alur kewenangan.');
        }

        if (is_null($letter->printed_at)) {
            $letter->update(['printed_at' => Carbon::now()]);
        }

        $qrUrl = route('surat.verifikasi', $letter->qr_verification_code);

        $rtName = $letter->rt_approved_name ?? ($letter->rtApprover?->name ?? 'Ketua RT ' . $letter->rt);
        $rwName = $letter->rw_approved_name ?? ($letter->rwApprover?->name ?? 'Gayeca');

        // Resolve template body
        $templateText = '';
        if ($letter->letterType && !empty($letter->letterType->template_body)) {
            $templateText = $letter->letterType->template_body;
            $replacements = [
                '{rt}' => $letter->rt,
                '{rw}' => $letter->rw,
                '{nama}' => $letter->nama_lengkap,
                '{nik}' => $letter->nik,
                '{keperluan}' => $letter->keperluan,
            ];
            if (!empty($letter->detail_khusus)) {
                foreach ($letter->detail_khusus as $k => $v) {
                    $replacements['{' . $k . '}'] = is_array($v) ? implode(', ', $v) : $v;
                }
            }
            $templateText = str_replace(array_keys($replacements), array_values($replacements), $templateText);
        }

        return view('surat.cetak', compact('letter', 'user', 'qrUrl', 'rtName', 'rwName', 'templateText'));
    }

    /**
     * Halaman Publik Verifikasi Keabsahan Surat via Scan QR Code
     */
    public function verifikasi($code)
    {
        $letter = LetterRequest::with('letterType')->where('qr_verification_code', $code)->first();

        return view('surat.verifikasi', compact('letter', 'code'));
    }
}
