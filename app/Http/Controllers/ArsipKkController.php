<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KartuKeluarga;
use App\Models\User;
use App\Models\KkVerifikasiHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArsipKkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Daftar Arsip KK Resmi Dukcapil (Dashboard RW & RT)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isRw() || $user->isRt() || $user->isBendahara(), 403, 'Akses khusus pengurus wilayah.');

        $query = KartuKeluarga::with(['kepalaKeluarga', 'anggota'])
            ->withCount('anggota');

        // Batasi RT jika login sebagai Ketua RT
        if ($user->isRt()) {
            $rtNum = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);
            $query->where(function ($q) use ($rtNum) {
                $q->where('rt', $rtNum)
                  ->orWhere('rt', intval($rtNum))
                  ->orWhere('rt', 'RT ' . $rtNum);
            });
        } elseif ($request->filled('rt') && $request->rt !== 'all') {
            $filterRt = str_pad(intval($request->rt), 3, '0', STR_PAD_LEFT);
            $query->where(function ($q) use ($filterRt) {
                $q->where('rt', $filterRt)
                  ->orWhere('rt', intval($filterRt))
                  ->orWhere('rt', 'RT ' . $filterRt);
            });
        }

        // Filter Status Verifikasi Dukcapil
        if ($request->filled('status_verifikasi') && $request->status_verifikasi !== 'all') {
            $query->where('status_verifikasi_dukcapil', $request->status_verifikasi);
        }

        // Filter Status Dokumen (Aktif / Nonaktif / Perlu Pembaruan)
        if ($request->filled('status_dokumen') && $request->status_dokumen !== 'all') {
            $query->where('status_dokumen', $request->status_dokumen);
        }

        // Pencarian (No KK atau Nama Kepala Keluarga)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                  ->orWhere('nama_kepala_keluarga', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $kartuKeluargas = $query->orderBy('rt', 'asc')->orderBy('nomor_rumah', 'asc')->paginate(25);
        $kartuKeluargas->appends($request->all());

        // Statistik Ringkas Arsip KK Resmi
        $baseStatQuery = KartuKeluarga::query();
        if ($user->isRt()) {
            $rtNum = str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT);
            $baseStatQuery->where(function ($q) use ($rtNum) {
                $q->where('rt', $rtNum)->orWhere('rt', intval($rtNum));
            });
        }

        $stats = [
            'total_kk' => (clone $baseStatQuery)->count(),
            'terverifikasi' => (clone $baseStatQuery)->where('status_verifikasi_dukcapil', 'Terverifikasi')->count(),
            'menunggu' => (clone $baseStatQuery)->where('status_verifikasi_dukcapil', 'Menunggu Verifikasi')->count(),
            'belum_ada' => (clone $baseStatQuery)->where('status_verifikasi_dukcapil', 'Belum Ada Dokumen')->count(),
            'tidak_valid' => (clone $baseStatQuery)->whereIn('status_verifikasi_dukcapil', ['Tidak Valid', 'Perlu Pembaruan', 'Dicabut atau Diganti'])->count(),
        ];

        return view('arsip_kk.index', compact('kartuKeluargas', 'stats', 'user'));
    }

    /**
     * Detail Arsip Kartu Keluarga & Riwayat Verifikasi
     */
    public function show($id)
    {
        $user = auth()->user();
        $kk = KartuKeluarga::with(['anggota', 'kepalaKeluarga', 'verifikator', 'verifikasiHistories.petugas'])->findOrFail($id);

        // Otorisasi
        $this->authorizeViewKk($user, $kk);

        return view('arsip_kk.show', compact('kk', 'user'));
    }

    /**
     * Upload Salinan Dokumen PDF Resmi dari Dukcapil (Private Storage)
     */
    public function uploadDokumen(Request $request, $id)
    {
        $user = auth()->user();
        abort_unless($user->isRw() || $user->isRt(), 403, 'Akses khusus pengurus.');

        $kk = KartuKeluarga::findOrFail($id);
        if ($user->isRt() && intval($user->clean_rt_number) !== intval($kk->rt)) {
            abort(403, 'Anda hanya dapat mengunggah dokumen KK warga wilayah RT Anda sendiri.');
        }

        $request->validate([
            'dokumen_kk' => 'required|file|mimes:pdf|max:10240', // Maks 10MB PDF
            'tgl_diterbitkan' => 'nullable|date|before_or_equal:today',
            'catatan' => 'nullable|string|max:500',
        ], [
            'dokumen_kk.mimes' => 'Salinan KK elektronik resmi harus berupa berkas PDF autentik yang diterbitkan Dukcapil.',
            'dokumen_kk.max' => 'Ukuran berkas PDF tidak boleh melebihi 10MB.',
            'tgl_diterbitkan.before_or_equal' => 'Tanggal penerbitan dokumen tidak boleh di masa depan.',
        ]);

        $file = $request->file('dokumen_kk');
        
        // Hapus file lama jika ada
        if ($kk->dokumen_kk_path && Storage::disk('local')->exists($kk->dokumen_kk_path)) {
            Storage::disk('local')->delete($kk->dokumen_kk_path);
        }

        $path = $file->store('private/dokumen_kk', 'local');

        $oldStatus = $kk->status_verifikasi_dukcapil;
        // Status otomatis menjadi 'Menunggu Verifikasi' (JANGAN otomatis Terverifikasi)
        $newStatus = 'Menunggu Verifikasi';

        $kk->update([
            'dokumen_kk_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'tgl_diterbitkan_dukcapil' => $request->tgl_diterbitkan ?: $kk->tgl_diterbitkan_dukcapil,
            'versi_dokumen' => $kk->versi_dokumen + 1,
            'status_verifikasi_dukcapil' => $newStatus,
            'status_dokumen' => 'Aktif',
            'catatan_verifikasi_dukcapil' => 'Dokumen salinan PDF resmi telah diunggah. Menunggu pemeriksaan legalitas oleh Sekretariat RW 013.',
        ]);

        // Catat Audit Verifikasi
        KkVerifikasiHistory::create([
            'kartu_keluarga_id' => $kk->id,
            'status_lama' => $oldStatus,
            'status_baru' => $newStatus,
            'metode_verifikasi' => 'Upload Dokumen PDF Salinan Resmi Dukcapil',
            'petugas_id' => $user->id,
            'petugas_nama' => $user->name,
            'petugas_role' => $user->role,
            'catatan' => $request->catatan ?: "Pengunggahan versi {$kk->versi_dokumen} salinan KK resmi ({$file->getClientOriginalName()}).",
            'created_at' => now(),
        ]);

        return back()->with('success', 'Dokumen PDF salinan resmi Dukcapil berhasil diunggah! Status sekarang "Menunggu Verifikasi".');
    }

    /**
     * Proses Verifikasi Resmi Dokumen Dukcapil oleh RW / Super Admin
     */
    public function verifyDukcapil(Request $request, $id)
    {
        $user = auth()->user();
        abort_unless($user->isRw(), 403, 'Pemeriksaan dan penetapan status legalitas dokumen KK resmi merupakan kewenangan Ketua RW 013.');

        $kk = KartuKeluarga::findOrFail($id);

        $request->validate([
            'status_verifikasi_dukcapil' => 'required|in:Terverifikasi,Menunggu Verifikasi,Tidak Valid,Perlu Pembaruan,Dicabut atau Diganti',
            'metode_verifikasi' => 'required|string|max:150',
            'catatan' => 'required|string|max:500',
        ]);

        $oldStatus = $kk->status_verifikasi_dukcapil;
        $newStatus = $request->status_verifikasi_dukcapil;

        $kk->update([
            'status_verifikasi_dukcapil' => $newStatus,
            'metode_verifikasi' => $request->metode_verifikasi,
            'catatan_verifikasi_dukcapil' => $request->catatan,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        // Rekam Audit
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

        return back()->with('success', "Status verifikasi dokumen KK resmi berhasil diperbarui menjadi: {$newStatus}");
    }

    /**
     * Stream / Preview Dokumen PDF Asli Dukcapil
     * SYARAT KETAT: Dokumen HARUS berstatus 'Terverifikasi' untuk dapat dicetak / diunduh secara resmi!
     */
    public function streamPdf($id)
    {
        $user = auth()->user();
        $kk = KartuKeluarga::findOrFail($id);

        $this->authorizeViewKk($user, $kk);

        // PEMBATASAN CETAK & DOWNLOAD DUKCAPIL:
        // Jika belum terverifikasi, cetak dan unduh dinonaktifkan secara ketat
        if (!$kk->isVerifiedDukcapil()) {
            abort(403, 'Akses Dibatalkan: Dokumen KK No. ' . $kk->masked_no_kk . ' berstatus "' . $kk->status_verifikasi_dukcapil . '". Dokumen salinan hanya dapat dibuka dan dicetak apabila telah berstatus "Terverifikasi" resmi.');
        }

        if (!$kk->dokumen_kk_path || !Storage::disk('local')->exists($kk->dokumen_kk_path)) {
            abort(404, 'Berkas fisik PDF KK resmi tidak ditemukan di penyimpanan server.');
        }

        $fullPath = Storage::disk('local')->path($kk->dokumen_kk_path);
        $fileName = 'Salinan_KK_Resmi_' . $kk->no_kk . '.pdf';

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, private'
        ]);
    }

    /**
     * Cetak Salinan Dokumen KK Resmi
     */
    public function cetak($id)
    {
        return $this->streamPdf($id);
    }

    /**
     * Helper Otorisasi Akses KK
     */
    protected function authorizeViewKk(User $user, KartuKeluarga $kk)
    {
        if ($user->isRw() || $user->isBendahara()) {
            return true;
        }

        if ($user->isRt()) {
            if (intval($user->clean_rt_number) === intval($kk->rt)) {
                return true;
            }
            abort(403, "Akses ditolak: Anda hanya dapat mengakses arsip keluarga di wilayah {$user->clean_rt_label}.");
        }

        // Warga biasa hanya bisa melihat KK miliknya sendiri
        if ($user->kartu_keluarga_id === $kk->id || $user->no_kk === $kk->no_kk) {
            return true;
        }

        abort(403, 'Akses ditolak: Anda tidak memiliki wewenang mengakses data Kartu Keluarga ini.');
    }
}
