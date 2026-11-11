<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanKeluarga;
use App\Models\KartuKeluarga;
use App\Models\BukuKas;
use App\Models\IuranTarif;
use App\Models\TransaksiKeuangan;
use App\Models\KeuanganAuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TagihanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Daftar Tagihan Iuran Keluarga
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $isRt = ($user->role === 'rt');
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $status = $request->input('status');
        $rtFilter = $request->input('rt');
        $search = $request->input('search');

        $query = TagihanKeluarga::with(['kartuKeluarga.anggota', 'kepalaKeluarga']);

        if (!$isRw && !$isRt) {
            // Warga biasa: hanya KK sendiri
            $kkId = $user->kartu_keluarga_id;
            if (!$kkId && $user->no_kk) {
                $kk = KartuKeluarga::where('no_kk', $user->no_kk)->first();
                $kkId = $kk ? $kk->id : -1;
            }
            $query->where('kartu_keluarga_id', $kkId);
        } elseif ($isRt) {
            // RT: hanya RT miliknya
            $query->where(function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
            });
        } elseif ($isRw && $rtFilter) {
            $query->where(function($q) use ($rtFilter) {
                $q->where('rt_number', $rtFilter)->orWhere('rt_number', ltrim($rtFilter, '0'));
            });
        }

        if ($request->filled('periode')) {
            $query->where('periode', $periode);
        }
        if ($request->filled('status')) {
            $query->where('status', $status);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_tagihan', 'like', "%{$search}%")
                  ->orWhereHas('kartuKeluarga', function($kq) use ($search) {
                      $kq->where('nama_kepala_keluarga', 'like', "%{$search}%")
                         ->orWhere('no_kk', 'like', "%{$search}%")
                         ->orWhere('alamat', 'like', "%{$search}%");
                  });
            });
        }

        $tagihans = $query->orderBy('status', 'asc')->orderBy('rt_number', 'asc')->paginate(25);

        // Statistik ringkas filter
        $statQuery = clone $query;
        $totalTagihanNominal = $statQuery->sum('total_tagihan');
        $totalTerkumpulNominal = $statQuery->sum('total_dibayar');
        $totalSisaNominal = $statQuery->sum('sisa_tagihan');

        $daftarPeriode = TagihanKeluarga::select('periode')->distinct()->orderBy('periode', 'desc')->pluck('periode');

        return view('keuangan.tagihan.index', compact(
            'tagihans',
            'periode',
            'status',
            'rtFilter',
            'search',
            'isRw',
            'isRt',
            'rtStr',
            'totalTagihanNominal',
            'totalTerkumpulNominal',
            'totalSisaNominal',
            'daftarPeriode'
        ));
    }

    /**
     * Detail Lembar Tagihan
     */
    public function show($id)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $isRt = ($user->role === 'rt');
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        $tagihan = TagihanKeluarga::with(['kartuKeluarga.anggota', 'kepalaKeluarga', 'diverifikasiOleh', 'transaksis.bukuKas'])->findOrFail($id);

        // Proteksi hak akses
        if (!$isRw && !$isRt) {
            $myKkId = $user->kartu_keluarga_id;
            if (!$myKkId && $user->no_kk) {
                $kk = KartuKeluarga::where('no_kk', $user->no_kk)->first();
                $myKkId = $kk ? $kk->id : null;
            }
            if ($tagihan->kartu_keluarga_id != $myKkId) {
                abort(403, 'Anda tidak memiliki hak akses melihat tagihan keluarga lain.');
            }
        } elseif ($isRt) {
            if ($tagihan->clean_rt != $rtStr && $tagihan->rt_number != ltrim($rtStr, '0')) {
                abort(403, 'Anda tidak memiliki hak akses melihat tagihan RT lain.');
            }
        }

        $kasRw = BukuKas::where('jenis_kas', 'kas_rw')->first();

        return view('keuangan.tagihan.show', compact('tagihan', 'isRw', 'isRt', 'kasRw'));
    }

    /**
     * Generate Tagihan Bulanan Otomatis untuk Seluruh KK Aktif
     */
    public function generateBulanan(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Hanya Pengurus RW yang berwenang men-generate tagihan.');
        }

        $request->validate([
            'periode' => 'required|date_format:Y-m',
            'jatuh_tempo' => 'required|date',
        ]);

        $periode = $request->periode;
        $jatuhTempo = $request->jatuh_tempo;
        $tglTerbit = Carbon::parse($periode . '-01')->toDateString();

        // Ambil tarif aktif
        $tarifRt = IuranTarif::where('kode_tarif', 'TARIF-RT')->first();
        $tarifRw = IuranTarif::where('kode_tarif', 'TARIF-RW')->first();
        $tarifSampah = IuranTarif::where('kode_tarif', 'TARIF-SAMPAH')->first();

        $nomRt = $tarifRt ? $tarifRt->nominal : 20000;
        $nomRw = $tarifRw ? $tarifRw->nominal : 10000;
        $nomSampah = $tarifSampah ? $tarifSampah->nominal : 15000;
        $totalTagihan = $nomRt + $nomRw + $nomSampah;

        $allKk = KartuKeluarga::all();
        $generatedCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($allKk as $kk) {
                // Cek apakah tagihan untuk KK ini di periode ini sudah ada (Idempotent)
                $exists = TagihanKeluarga::where('kartu_keluarga_id', $kk->id)
                    ->where('periode', $periode)
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                $rtStr = str_pad(intval($kk->rt ?: 1), 3, '0', STR_PAD_LEFT);
                $countTagihan = TagihanKeluarga::where('periode', $periode)->count() + 1;
                $nomorTagihan = 'BILL-' . str_replace('-', '', $periode) . '-' . str_pad($countTagihan, 4, '0', STR_PAD_LEFT);

                TagihanKeluarga::create([
                    'nomor_tagihan' => $nomorTagihan,
                    'kartu_keluarga_id' => $kk->id,
                    'kepala_keluarga_id' => $kk->kepala_keluarga_id,
                    'rt_number' => $rtStr,
                    'periode' => $periode,
                    'nominal_rt' => $nomRt,
                    'nominal_rw' => $nomRw,
                    'nominal_sampah' => $nomSampah,
                    'total_tagihan' => $totalTagihan,
                    'total_dibayar' => 0,
                    'sisa_tagihan' => $totalTagihan,
                    'status' => 'Belum Dibayar',
                    'tanggal_terbit' => $tglTerbit,
                    'jatuh_tempo' => $jatuhTempo,
                ]);

                $generatedCount++;
            }

            // Catat audit log
            KeuanganAuditLog::create([
                'user_id' => $user->id,
                'action' => 'generate_tagihan',
                'deskripsi' => "Generate tagihan periode {$periode}: Berhasil membuat {$generatedCount} tagihan KK baru, {$skippedCount} dilewati (sudah ada).",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return back()->with('success', "Generate tagihan periode {$periode} selesai! {$generatedCount} tagihan dibuat, {$skippedCount} sudah ada.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi Pembayaran oleh Warga (Upload Bukti Transfer / Bayar Tunai)
     */
    public function konfirmasiPembayaran(Request $request, $id)
    {
        $user = Auth::user();
        $request->validate([
            'metode_pembayaran' => 'required|in:Transfer Bank,Tunai,QRIS',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
            'catatan_warga' => 'nullable|string|max:255',
        ]);

        $tagihan = TagihanKeluarga::findOrFail($id);

        $buktiPath = $tagihan->bukti_pembayaran_path;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        }

        $tagihan->update([
            'status' => 'Menunggu Konfirmasi',
            'metode_pembayaran_terakhir' => $request->metode_pembayaran,
            'bukti_pembayaran_path' => $buktiPath,
            'catatan_warga' => $request->catatan_warga ?: 'Konfirmasi pembayaran diajukan oleh ' . $user->name,
        ]);

        KeuanganAuditLog::create([
            'user_id' => $user->id,
            'tagihan_id' => $tagihan->id,
            'action' => 'submit_pembayaran',
            'deskripsi' => "Warga {$user->name} mengajukan konfirmasi pembayaran tagihan {$tagihan->nomor_tagihan} ({$request->metode_pembayaran}).",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Konfirmasi pembayaran berhasil dikirim! Menunggu verifikasi Bendahara / Pengurus RT/RW.');
    }

    /**
     * Verifikasi Pembayaran oleh Pengurus RT/RW (Menjadikan Lunas & Mencatat ke 3 Buku Kas)
     */
    public function verifikasiPembayaran(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin', 'rt'])) {
            abort(403, 'Akses dibatasi.');
        }

        $request->validate([
            'metode_verifikasi' => 'required|in:Tunai,Transfer Bank,QRIS',
            'catatan_petugas' => 'nullable|string|max:255',
        ]);

        $tagihan = TagihanKeluarga::with('kartuKeluarga')->findOrFail($id);
        if ($tagihan->status === 'Lunas') {
            return back()->with('info', 'Tagihan ini sudah berstatus Lunas.');
        }

        DB::beginTransaction();
        try {
            $rtStr = $tagihan->clean_rt;
            $kk = $tagihan->kartuKeluarga;
            $tglLunas = Carbon::now();

            // Ambil 3 Buku Kas terkait:
            // 1. Kas RT
            $bukuKasRt = BukuKas::where('jenis_kas', 'kas_rt')
                ->where(function($q) use ($rtStr) {
                    $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
                })->first();

            // 2. Kas RW
            $kasRw = BukuKas::where('jenis_kas', 'kas_rw')->first();

            // 3. Kas Sampah
            $kasSampah = BukuKas::where('jenis_kas', 'kas_sampah')->first();

            $dateStr = $tglLunas->format('Ym');
            $penyetor = $kk ? ($kk->nama_kepala_keluarga . ' (' . $kk->clean_rt_label . ')') : 'Warga RT ' . $rtStr;
            $metode = ($request->metode_verifikasi === 'Transfer Bank') ? 'transfer_bank' : (($request->metode_verifikasi === 'QRIS') ? 'qris' : 'tunai');

            // Catat Pemasukan Kas RT jika nominal_rt > 0
            if ($bukuKasRt && $tagihan->nominal_rt > 0) {
                $count = TransaksiKeuangan::where('nomor_transaksi', 'like', "TRX-{$dateStr}-RT{$rtStr}-%")->count() + 1;
                $noTrx = "TRX-{$dateStr}-RT{$rtStr}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

                TransaksiKeuangan::create([
                    'nomor_transaksi' => $noTrx,
                    'buku_kas_id' => $bukuKasRt->id,
                    'tagihan_keluarga_id' => $tagihan->id,
                    'tanggal_transaksi' => $tglLunas->toDateString(),
                    'jenis_transaksi' => 'pemasukan',
                    'kategori' => 'Iuran Warga RT Bulanan',
                    'nominal' => $tagihan->nominal_rt,
                    'saldo_sebelumnya' => $bukuKasRt->saldo_sekarang,
                    'saldo_setelahnya' => $bukuKasRt->saldo_sekarang + $tagihan->nominal_rt,
                    'metode_pembayaran' => $metode,
                    'penyetor_penerima' => $penyetor,
                    'keterangan' => "Iuran RT periode {$tagihan->periode} [{$tagihan->nomor_tagihan}]",
                    'petugas_id' => $user->id,
                    'nama_petugas' => $user->name,
                    'status' => 'selesai',
                ]);
                $bukuKasRt->recalculateSaldo();
            }

            // Catat Pemasukan Kas RW jika nominal_rw > 0
            if ($kasRw && $tagihan->nominal_rw > 0) {
                $count = TransaksiKeuangan::where('nomor_transaksi', 'like', "TRX-{$dateStr}-RW-%")->count() + 1;
                $noTrx = "TRX-{$dateStr}-RW-" . str_pad($count, 4, '0', STR_PAD_LEFT);

                TransaksiKeuangan::create([
                    'nomor_transaksi' => $noTrx,
                    'buku_kas_id' => $kasRw->id,
                    'tagihan_keluarga_id' => $tagihan->id,
                    'tanggal_transaksi' => $tglLunas->toDateString(),
                    'jenis_transaksi' => 'pemasukan',
                    'kategori' => 'Alokasi Iuran RW dari KK',
                    'nominal' => $tagihan->nominal_rw,
                    'saldo_sebelumnya' => $kasRw->saldo_sekarang,
                    'saldo_setelahnya' => $kasRw->saldo_sekarang + $tagihan->nominal_rw,
                    'metode_pembayaran' => $metode,
                    'penyetor_penerima' => $penyetor,
                    'keterangan' => "Iuran RW periode {$tagihan->periode} [{$tagihan->nomor_tagihan}]",
                    'petugas_id' => $user->id,
                    'nama_petugas' => $user->name,
                    'status' => 'selesai',
                ]);
                $kasRw->recalculateSaldo();
            }

            // Catat Pemasukan Kas Sampah jika nominal_sampah > 0
            if ($kasSampah && $tagihan->nominal_sampah > 0) {
                $count = TransaksiKeuangan::where('nomor_transaksi', 'like', "TRX-{$dateStr}-SMPH-%")->count() + 1;
                $noTrx = "TRX-{$dateStr}-SMPH-" . str_pad($count, 4, '0', STR_PAD_LEFT);

                TransaksiKeuangan::create([
                    'nomor_transaksi' => $noTrx,
                    'buku_kas_id' => $kasSampah->id,
                    'tagihan_keluarga_id' => $tagihan->id,
                    'tanggal_transaksi' => $tglLunas->toDateString(),
                    'jenis_transaksi' => 'pemasukan',
                    'kategori' => 'Retribusi Kebersihan & Sampah KK',
                    'nominal' => $tagihan->nominal_sampah,
                    'saldo_sebelumnya' => $kasSampah->saldo_sekarang,
                    'saldo_setelahnya' => $kasSampah->saldo_sekarang + $tagihan->nominal_sampah,
                    'metode_pembayaran' => $metode,
                    'penyetor_penerima' => $penyetor,
                    'keterangan' => "Retribusi sampah periode {$tagihan->periode} [{$tagihan->nomor_tagihan}]",
                    'petugas_id' => $user->id,
                    'nama_petugas' => $user->name,
                    'status' => 'selesai',
                ]);
                $kasSampah->recalculateSaldo();
            }

            // Update Tagihan jadi Lunas
            $tagihan->update([
                'status' => 'Lunas',
                'total_dibayar' => $tagihan->total_tagihan,
                'sisa_tagihan' => 0,
                'tanggal_lunas' => $tglLunas,
                'metode_pembayaran_terakhir' => $request->metode_verifikasi,
                'catatan_petugas' => $request->catatan_petugas ?: 'Diverifikasi sah oleh ' . $user->name,
                'diverifikasi_oleh_id' => $user->id,
                'diverifikasi_pada' => $tglLunas,
            ]);

            // Catat Audit Log
            KeuanganAuditLog::create([
                'user_id' => $user->id,
                'tagihan_id' => $tagihan->id,
                'action' => 'verifikasi_pembayaran',
                'deskripsi' => "Verifikasi pembayaran LUNAS tagihan [{$tagihan->nomor_tagihan}] senilai Rp " . number_format($tagihan->total_tagihan, 0, ',', '.') . " ({$request->metode_verifikasi})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return back()->with('success', 'Pembayaran tagihan berhasil diverifikasi dan disetujui LUNAS! Dana otomatis dialokasikan ke Kas RT, RW, dan Sampah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Bebaskan Tagihan untuk Warga Miskin / Dhuafa / SKTM
     */
    public function bebaskanTagihan(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin', 'rt'])) {
            abort(403, 'Akses dibatasi.');
        }

        $request->validate([
            'alasan_pembebasan' => 'required|string|min:5|max:255',
        ]);

        $tagihan = TagihanKeluarga::findOrFail($id);

        $tagihan->update([
            'status' => 'Dibebaskan',
            'total_dibayar' => 0,
            'sisa_tagihan' => 0,
            'alasan_pembebasan' => $request->alasan_pembebasan,
            'catatan_petugas' => 'Dibebaskan dari kewajiban iuran oleh ' . $user->name,
            'diverifikasi_oleh_id' => $user->id,
            'diverifikasi_pada' => Carbon::now(),
        ]);

        KeuanganAuditLog::create([
            'user_id' => $user->id,
            'tagihan_id' => $tagihan->id,
            'action' => 'exempt_tagihan',
            'deskripsi' => "Pembebasan iuran keluarga [{$tagihan->nomor_tagihan}]. Alasan: {$request->alasan_pembebasan}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Tagihan keluarga berhasil dibebaskan!');
    }

    /**
     * Cetak Kuitansi / Tanda Bukti Sah Pembayaran
     */
    public function kuitansi($id)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $isRt = ($user->role === 'rt');

        $tagihan = TagihanKeluarga::with(['kartuKeluarga.anggota', 'kepalaKeluarga', 'diverifikasiOleh'])->findOrFail($id);

        // Security check
        if (!$isRw && !$isRt) {
            $myKkId = $user->kartu_keluarga_id;
            if (!$myKkId && $user->no_kk) {
                $kk = KartuKeluarga::where('no_kk', $user->no_kk)->first();
                $myKkId = $kk ? $kk->id : null;
            }
            if ($tagihan->kartu_keluarga_id != $myKkId) {
                abort(403, 'Anda tidak berwenang melihat kuitansi tagihan warga lain.');
            }
        }

        return view('keuangan.tagihan.kuitansi', compact('tagihan'));
    }
}
