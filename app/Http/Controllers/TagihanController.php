<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanKeluarga;
use App\Models\TagihanItem;
use App\Models\PembayaranIuran;
use App\Models\PembayaranAlokasi;
use App\Models\IuranKeringanan;
use App\Models\KartuKeluarga;
use App\Models\BukuKas;
use App\Models\IuranTarif;
use App\Models\TransaksiKeuangan;
use App\Models\KeuanganAuditLog;
use App\Models\User;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TagihanController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('auth')->except(['handleWebhook']);
        $this->paymentService = $paymentService;
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

        $query = TagihanKeluarga::with(['kartuKeluarga.anggota', 'kepalaKeluarga', 'items.bukuKas', 'pembayarans']);

        if (!$isRw && !$isRt) {
            // Warga biasa: redirect ke dashboard tagihan warga terpadu
            return redirect()->route('keuangan.dashboard', array_merge(['tab' => 'tagihan'], $request->query()));
        } elseif ($isRt) {
            // Pengurus RT: redirect ke tab tagihan di Manajemen Keuangan RT terpadu
            return redirect()->route('keuangan.dashboard', array_merge(['tab' => 'tagihan'], $request->query()));
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

        // Ekspor CSV jika diminta
        if ($request->input('export') === 'csv') {
            return $this->exportCsv($query->get(), $periode);
        }

        $tagihans = $query->orderBy('status', 'asc')->orderBy('rt_number', 'asc')->paginate(20)->withQueryString();

        // Statistik Ringkasan Keuangan Sesuai Permintaan
        $statQuery = clone $query;
        if ($isRt) {
            // Khusus Akun RT: hanya menghitung kewajiban & hak iuran RT (nominal_rt), BUKAN Kas Induk RW!
            $totalTagihanNominal = (float)(clone $statQuery)->sum('nominal_rt');
            $totalTerkumpulNominal = (float)(clone $statQuery)->where('status', 'Lunas')->sum('nominal_rt');
            $totalBelumDibayarNominal = (float)(clone $statQuery)->whereIn('status', ['Belum Dibayar', 'Terlambat'])->sum('nominal_rt');
            $totalMenungguNominal = (float)(clone $statQuery)->where('status', 'Menunggu Konfirmasi')->sum('nominal_rt');
        } else {
            $totalTagihanNominal = (float)(clone $statQuery)->sum('total_tagihan');
            $totalTerkumpulNominal = (float)(clone $statQuery)->sum('total_dibayar');
            $totalBelumDibayarNominal = (float)(clone $statQuery)->where('status', '!=', 'Lunas')->sum('sisa_tagihan');
            $totalMenungguNominal = (float)(clone $statQuery)->where('status', 'Menunggu Konfirmasi')->sum('sisa_tagihan');
        }
        $countMenunggu = (int)(clone $statQuery)->where('status', 'Menunggu Konfirmasi')->count();
        $kolektivitasPersen = $totalTagihanNominal > 0 ? round(($totalTerkumpulNominal / $totalTagihanNominal) * 100, 1) : 0;

        // Komposisi Dinamis Tagihan Iuran Sesuai Konfigurasi Database (Bukan Hardcoded)
        $activeTarifs = IuranTarif::where('is_active', true)->orderBy('urutan_prioritas', 'asc')->get();
        $komposisiTarif = [
            'rt' => 0,
            'rw' => 0,
            'sampah' => 0,
            'kegiatan' => 0,
            'total_per_kk' => 0,
            'items' => [],
        ];

        foreach ($activeTarifs as $t) {
            $nom = (float)$t->nominal;
            $komposisiTarif['total_per_kk'] += $nom;
            $targetKas = strtolower($t->jenis_kas ?: '');
            
            if ($t->kode_tarif === 'TARIF-RT' || $targetKas === 'kas_rt') {
                $komposisiTarif['rt'] += $nom;
            } elseif ($t->kode_tarif === 'TARIF-RW' || $targetKas === 'kas_rw') {
                $komposisiTarif['rw'] += $nom;
            } elseif (str_contains($targetKas, 'sampah') || str_contains($targetKas, 'kebersihan')) {
                $komposisiTarif['sampah'] += $nom;
            } else {
                $komposisiTarif['kegiatan'] += $nom;
            }

            $komposisiTarif['items'][] = [
                'id' => $t->id,
                'kode' => $t->kode_tarif,
                'nama' => $t->nama_tarif,
                'nominal' => $nom,
                'jenis_kas' => $t->jenis_kas,
                'tingkat' => $t->tingkat_pengelola ?? 'rw',
                'metode' => $t->metode_perhitungan ?? 'per_kk',
            ];
        }

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
            'totalBelumDibayarNominal',
            'totalMenungguNominal',
            'countMenunggu',
            'kolektivitasPersen',
            'komposisiTarif',
            'daftarPeriode'
        ));
    }

    /**
     * Ekspor Data Tagihan ke File CSV
     */
    protected function exportCsv($tagihans, $periode): StreamedResponse
    {
        $filename = 'laporan_tagihan_iuran_' . ($periode ?: date('Y-m')) . '.csv';

        return response()->streamDownload(function () use ($tagihans) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fputs($handle, "\xEF\xBB\xBF");

            // Header kolom CSV
            fputcsv($handle, [
                'No',
                'Nomor Tagihan',
                'Nomor KK',
                'Nama Kepala Keluarga',
                'Wilayah RT',
                'Periode',
                'Porsi Kas RT (Rp)',
                'Porsi Kas RW (Rp)',
                'Porsi Sampah (Rp)',
                'Total Tagihan (Rp)',
                'Sudah Dibayar (Rp)',
                'Sisa Tagihan (Rp)',
                'Status Pembayaran',
                'Tanggal Pembayaran Terakhir',
                'Metode Pembayaran',
            ]);

            $no = 1;
            foreach ($tagihans as $t) {
                fputcsv($handle, [
                    $no++,
                    $t->nomor_tagihan,
                    "'" . ($t->kartuKeluarga->no_kk ?? '-'),
                    $t->kartuKeluarga->nama_kepala_keluarga ?? ($t->kepalaKeluarga->name ?? 'Warga'),
                    'RT ' . $t->clean_rt,
                    $t->periode,
                    $t->nominal_rt,
                    $t->nominal_rw,
                    $t->nominal_sampah,
                    $t->total_tagihan,
                    $t->total_dibayar,
                    $t->sisa_tagihan,
                    $t->status,
                    $t->tanggal_lunas ? $t->tanggal_lunas->format('d/m/Y H:i') : '-',
                    $t->metode_pembayaran_terakhir ?: '-',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Cetak Laporan Tagihan & Realisasi Iuran Warga (PDF / Print View)
     */
    public function cetakLaporan(Request $request)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $isRt = ($user->role === 'rt');
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $rtFilter = $request->input('rt', $isRt ? $rtStr : null);

        $query = TagihanKeluarga::with(['kartuKeluarga', 'kepalaKeluarga', 'items.bukuKas', 'pembayarans']);

        if ($isRt) {
            $query->where(function($q) use ($rtStr) {
                $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
            });
        } elseif ($rtFilter) {
            $query->where(function($q) use ($rtFilter) {
                $q->where('rt_number', $rtFilter)->orWhere('rt_number', ltrim($rtFilter, '0'));
            });
        }

        if ($periode) {
            $query->where('periode', $periode);
        }

        $tagihans = $query->orderBy('rt_number', 'asc')->orderBy('status', 'asc')->get();

        $totalTagihan = $tagihans->sum('total_tagihan');
        $totalBayar = $tagihans->sum('total_dibayar');
        $totalSisa = $tagihans->sum('sisa_tagihan');
        $pctKolektibilitas = $totalTagihan > 0 ? round(($totalBayar / $totalTagihan) * 100, 1) : 0;

        return view('keuangan.tagihan.cetak', compact(
            'tagihans',
            'periode',
            'rtFilter',
            'totalTagihan',
            'totalBayar',
            'totalSisa',
            'pctKolektibilitas',
            'user'
        ));
    }

    /**
     * Inisiasi Pembayaran Digital (QRIS / Split Settlement Intent)
     */
    public function payDigital(Request $request, $id)
    {
        $tagihan = TagihanKeluarga::with(['kartuKeluarga', 'items.bukuKas'])->findOrFail($id);
        
        try {
            $intent = $this->paymentService->createDigitalPaymentIntent($tagihan);
            return response()->json([
                'success' => true,
                'data' => $intent,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Webhook Handler dari Payment Gateway Provider
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('X-Callback-Signature') ?? $request->header('X-Signature');

        $result = $this->paymentService->handleWebhook($payload, $signature);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Detail Lembar Tagihan (One Billing Breakdown)
     */
    public function show($id)
    {
        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $isRt = ($user->role === 'rt');
        $rtStr = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        $tagihan = TagihanKeluarga::with([
            'kartuKeluarga.anggota',
            'kepalaKeluarga',
            'diverifikasiOleh',
            'items.bukuKas',
            'items.alokasis',
            'pembayarans.alokasis.bukuKas',
            'pembayarans.penerima'
        ])->findOrFail($id);

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
        $supportsSplit = $this->paymentService->getGateway()->supportsSplitSettlement();

        // Validasi Alokasi Lengkap: Jangan tampilkan status Lunas jika ada komponen yang belum berhasil dialokasikan
        $allComponentsAllocated = true;
        foreach ($tagihan->items as $item) {
            $totalItemAlokasi = $item->alokasis->sum('nominal_alokasi');
            if ($item->nominal_terbayar > 0 && $totalItemAlokasi < $item->nominal_terbayar) {
                $allComponentsAllocated = false;
                break;
            }
        }

        return view('keuangan.tagihan.show', compact('tagihan', 'isRw', 'isRt', 'kasRw', 'supportsSplit', 'allComponentsAllocated'));
    }

    /**
     * Generate Tagihan Bulanan Otomatis (One Billing & Multi Calculation Snapshot)
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

        // Ambil seluruh tarif aktif terurut prioritas
        $activeTarifs = IuranTarif::where('is_active', true)->orderBy('urutan_prioritas', 'asc')->get();
        if ($activeTarifs->isEmpty()) {
            return back()->with('error', 'Tidak ada tarif iuran aktif di sistem.');
        }

        $allKk = KartuKeluarga::with('anggota')->get();
        $generatedCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($allKk as $kk) {
                // Idempotensi per KK per Periode
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

                // Buat tagihan induk terlebih dahulu
                $tagihan = TagihanKeluarga::create([
                    'nomor_tagihan' => $nomorTagihan,
                    'kartu_keluarga_id' => $kk->id,
                    'kepala_keluarga_id' => $kk->kepala_keluarga_id,
                    'rt_number' => $rtStr,
                    'periode' => $periode,
                    'nominal_rt' => 0,
                    'nominal_rw' => 0,
                    'nominal_sampah' => 0,
                    'total_tagihan' => 0,
                    'total_dibayar' => 0,
                    'sisa_tagihan' => 0,
                    'status' => 'Belum Dibayar',
                    'tanggal_terbit' => $tglTerbit,
                    'jatuh_tempo' => $jatuhTempo,
                ]);

                // Hitung jumlah anggota keluarga aktif (minimal 1)
                $jumlahAnggota = $kk->anggota ? $kk->anggota->count() : 1;
                if ($jumlahAnggota < 1) {
                    $jumlahAnggota = 1;
                }

                // Ambil keringanan aktif untuk KK ini
                $keringanans = IuranKeringanan::where('kartu_keluarga_id', $kk->id)
                    ->where('status', 'aktif')
                    ->where(function($q) use ($periode) {
                        $q->whereNull('periode_mulai')->orWhere('periode_mulai', '<=', $periode);
                    })
                    ->where(function($q) use ($periode) {
                        $q->whereNull('periode_selesai')->orWhere('periode_selesai', '>=', $periode);
                    })
                    ->get();

                $totalTagihanKK = 0;
                $nomRt = 0;
                $nomRw = 0;
                $nomSampah = 0;

                foreach ($activeTarifs as $tarif) {
                    // Tentukan Buku Kas tujuan
                    $targetKasId = $tarif->buku_kas_id;
                    if (!$targetKasId) {
                        if ($tarif->kode_tarif === 'TARIF-RT' || $tarif->jenis_kas === 'kas_rt') {
                            $kasRtObj = BukuKas::where('jenis_kas', 'kas_rt')
                                ->where(function($q) use ($rtStr) {
                                    $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
                                })->first();
                            $targetKasId = $kasRtObj ? $kasRtObj->id : null;
                        } elseif ($tarif->kode_tarif === 'TARIF-RW' || $tarif->jenis_kas === 'kas_rw') {
                            $kasRwObj = BukuKas::where('jenis_kas', 'kas_rw')->first();
                            $targetKasId = $kasRwObj ? $kasRwObj->id : null;
                        } elseif ($tarif->kode_tarif === 'TARIF-KEBERSIHAN' || $tarif->kode_tarif === 'TARIF-SAMPAH' || $tarif->jenis_kas === 'kas_sampah') {
                            $kasSampahObj = BukuKas::where('jenis_kas', 'kas_sampah')->first();
                            $targetKasId = $kasSampahObj ? $kasSampahObj->id : null;
                        }
                    }

                    // 4 Metode Perhitungan Kombinasi
                    $kuantitas = 1;
                    $ketFormula = '';

                    switch ($tarif->metode_perhitungan) {
                        case 'per_individu':
                            $kuantitas = $jumlahAnggota;
                            $ketFormula = "{$jumlahAnggota} orang x Rp " . number_format($tarif->nominal, 0, ',', '.');
                            break;
                        case 'per_rumah':
                            $kuantitas = 1;
                            $ketFormula = "1 Rumah x Rp " . number_format($tarif->nominal, 0, ',', '.');
                            break;
                        case 'nominal_tetap':
                            $kuantitas = 1;
                            $ketFormula = "Nominal Tetap Bulanan";
                            break;
                        case 'per_kk':
                        default:
                            $kuantitas = 1;
                            $ketFormula = "1 KK x Rp " . number_format($tarif->nominal, 0, ',', '.');
                            break;
                    }

                    $subtotal = $tarif->nominal * $kuantitas;

                    // Cek Potongan Keringanan
                    $potongan = 0;
                    $relief = $keringanans->first(function($k) use ($tarif) {
                        return empty($k->iuran_tarif_id) || $k->iuran_tarif_id == $tarif->id;
                    });

                    if ($relief) {
                        $nilaiKeringanan = floatval($relief->nilai_keringanan ?? $relief->nilai_potongan ?? 0);
                        if ($relief->jenis_keringanan === 'bebas_iuran') {
                            $potongan = $subtotal;
                            $ketFormula .= " (Bebas Iuran: {$relief->alasan})";
                        } elseif ($relief->jenis_keringanan === 'diskon_persen') {
                            $potongan = round(($nilaiKeringanan / 100) * $subtotal);
                            $ketFormula .= " (Diskon {$nilaiKeringanan}%)";
                        } elseif ($relief->jenis_keringanan === 'diskon_nominal') {
                            $potongan = min($subtotal, $nilaiKeringanan);
                            $ketFormula .= " (Potongan Rp " . number_format($potongan, 0, ',', '.') . ")";
                        }
                    }

                    $totalAkhir = max(0, $subtotal - $potongan);

                    // Pemilik dana
                    $pemilikDana = 'RW';
                    if ($tarif->kode_tarif === 'TARIF-RT' || $tarif->jenis_kas === 'kas_rt') {
                        $pemilikDana = 'RT';
                    } elseif ($tarif->kode_tarif === 'TARIF-KEBERSIHAN' || $tarif->jenis_kas === 'kas_sampah') {
                        $pemilikDana = 'Unit Kebersihan';
                    } elseif ($tarif->kode_tarif === 'TARIF-KEAMANAN') {
                        $pemilikDana = 'Unit Keamanan';
                    } elseif ($tarif->kode_tarif === 'TARIF-KEGIATAN') {
                        $pemilikDana = 'Unit Kegiatan';
                    }

                    TagihanItem::create([
                        'tagihan_keluarga_id' => $tagihan->id,
                        'iuran_tarif_id' => $tarif->id,
                        'nama_iuran' => $tarif->nama_tarif,
                        'kode_iuran' => $tarif->kode_tarif,
                        'metode_perhitungan' => $tarif->metode_perhitungan ?: 'per_kk',
                        'tarif_satuan' => $tarif->nominal,
                        'kuantitas' => $kuantitas,
                        'nominal_subtotal' => $subtotal,
                        'nominal_diskon' => $potongan,
                        'nominal_akhir' => $totalAkhir,
                        'nominal_terbayar' => 0,
                        'sisa_item' => $totalAkhir,
                        'pemilik_dana' => $pemilikDana,
                        'buku_kas_id' => $targetKasId,
                        'status_pembayaran' => ($totalAkhir == 0) ? 'lunas' : 'belum_dibayar',
                        'keterangan_snapshot' => $ketFormula,
                    ]);

                    $totalTagihanKK += $totalAkhir;

                    // Pemetaan backward-compatibility kolom lama
                    if ($tarif->kode_tarif === 'TARIF-RT' || $tarif->jenis_kas === 'kas_rt') {
                        $nomRt += $totalAkhir;
                    } elseif ($tarif->kode_tarif === 'TARIF-RW' || $tarif->jenis_kas === 'kas_rw') {
                        $nomRw += $totalAkhir;
                    } elseif ($tarif->kode_tarif === 'TARIF-KEBERSIHAN' || $tarif->kode_tarif === 'TARIF-SAMPAH' || $tarif->jenis_kas === 'kas_sampah') {
                        $nomSampah += $totalAkhir;
                    }
                }

                $tagihan->update([
                    'nominal_rt' => $nomRt,
                    'nominal_rw' => $nomRw,
                    'nominal_sampah' => $nomSampah,
                    'total_tagihan' => $totalTagihanKK,
                    'sisa_tagihan' => $totalTagihanKK,
                    'status' => ($totalTagihanKK == 0) ? 'Lunas' : 'Belum Dibayar',
                ]);

                $generatedCount++;
            }

            // Catat audit log
            KeuanganAuditLog::create([
                'user_id' => $user->id,
                'action' => 'generate_tagihan',
                'deskripsi' => "Generate tagihan terpadu periode {$periode}: Berhasil membuat {$generatedCount} tagihan KK, {$skippedCount} dilewati (sudah ada).",
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
     * Engine Alokasi Otomatis & Pembayaran Parsial / Penuh
     * (One Billing, Multiple Automatic Fund Allocations)
     */
    public function prosesPembayaran(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin', 'rt'])) {
            abort(403, 'Akses dibatasi.');
        }

        $request->validate([
            'nominal_bayar' => 'required|numeric|min:1',
            'kanal_pembayaran' => 'required|string',
            'nomor_referensi' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:255',
            'idempotency_key' => 'nullable|string|max:100',
        ]);

        // Cek Idempotency Key
        if ($request->filled('idempotency_key')) {
            $existing = PembayaranIuran::where('idempotency_key', $request->idempotency_key)->first();
            if ($existing) {
                return back()->with('info', "Transaksi pembayaran dengan ID ref {$request->idempotency_key} sudah pernah diproses.");
            }
        }

        DB::beginTransaction();
        try {
            $tagihan = TagihanKeluarga::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($tagihan->status === 'Lunas' || $tagihan->sisa_tagihan <= 0) {
                DB::rollBack();
                return back()->with('info', 'Tagihan ini sudah berstatus Lunas.');
            }

            $nominalBayar = min(floatval($request->nominal_bayar), floatval($tagihan->sisa_tagihan));
            $inputKanal = $request->kanal_pembayaran;
            $isTunaiRt = ($inputKanal === 'tunai_melalui_rt' || ($inputKanal === 'Tunai' && $user->role === 'rt'));
            $kanalEnum = $isTunaiRt ? 'tunai_melalui_rt' : 'langsung_rekening';
            $metodeStr = ($inputKanal === 'tunai_melalui_rt' || $inputKanal === 'tunai_rw' || $inputKanal === 'Tunai') ? 'tunai' : (($inputKanal === 'QRIS' || $inputKanal === 'qris') ? 'qris' : 'transfer_bank');

            $tglBayar = Carbon::now();
            $dateStr = $tglBayar->format('Ym');
            $rtStr = $tagihan->clean_rt;
            $kk = $tagihan->kartuKeluarga;
            $penyetor = $kk ? ($kk->nama_kepala_keluarga . ' (' . $kk->clean_rt_label . ')') : 'Warga RT ' . $rtStr;

            $countPay = PembayaranIuran::whereDate('tanggal_bayar', $tglBayar->toDateString())->count() + 1;
            $nomorPembayaran = 'PAY-' . $tglBayar->format('Ymd') . '-' . str_pad($countPay, 4, '0', STR_PAD_LEFT);

            // Jika dibayar tunai ke RT, porsi RW/unit berstatus menunggu setor ke RW
            $statusSetoranRw = $isTunaiRt ? 'menunggu_setor_ke_rw' : 'tidak_perlu';

            $pembayaran = PembayaranIuran::create([
                'nomor_pembayaran' => $nomorPembayaran,
                'tagihan_keluarga_id' => $tagihan->id,
                'kartu_keluarga_id' => $tagihan->kartu_keluarga_id,
                'tanggal_bayar' => $tglBayar,
                'nominal_bayar' => $nominalBayar,
                'metode_pembayaran' => $metodeStr,
                'kanal_pembayaran' => $kanalEnum,
                'status_setoran_rw' => $statusSetoranRw,
                'diterima_oleh_id' => $user->id,
                'nama_penerima' => $user->name,
                'idempotency_key' => $request->idempotency_key,
                'catatan' => $request->catatan,
                'status' => 'berhasil',
            ]);

            // Alokasi berurutan ke Tagihan Items berdasarkan urutan prioritas
            $items = $tagihan->items()->where('sisa_item', '>', 0)->orderBy('id', 'asc')->get();
            $sisaDanaAlokasi = $nominalBayar;

            foreach ($items as $item) {
                if ($sisaDanaAlokasi <= 0) {
                    break;
                }

                $alokasiNominal = min($sisaDanaAlokasi, floatval($item->sisa_item));
                $item->nominal_terbayar += $alokasiNominal;
                $item->sisa_item = max(0, $item->nominal_akhir - $item->nominal_terbayar);
                $item->status_pembayaran = ($item->sisa_item <= 0) ? 'lunas' : 'dibayar_sebagian';
                $item->save();

                $sisaDanaAlokasi -= $alokasiNominal;

                // Tentukan Buku Kas Tujuan
                $bukuKas = BukuKas::find($item->buku_kas_id);
                if (!$bukuKas && ($item->kode_iuran === 'TARIF-RT' || str_contains(strtolower($item->nama_iuran), 'rt'))) {
                    $bukuKas = BukuKas::where('jenis_kas', 'kas_rt')
                        ->where(function($q) use ($rtStr) {
                            $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
                        })->first();
                }

                $trxId = null;
                $statusAlokasi = 'masuk_kas';
                $isKasRt = $bukuKas && ($bukuKas->jenis_kas === 'kas_rt');

                // TITIPAN TUNAI RT:
                // Jika pembayaran tunai melalui RT dan tujuannya BUKAN Kas RT (misal RW, Kebersihan, Keamanan, Kegiatan),
                // maka dana ini berstatus 'titipan_rt' dan BELUM dicatatkan sebagai pemasukan kas manapun.
                if ($isTunaiRt && !$isKasRt) {
                    $statusAlokasi = 'titipan_rt';
                } else {
                    // Kredit langsung ke Buku Kas tujuan yang bersangkutan
                    if ($bukuKas) {
                        $prefix = ($bukuKas->jenis_kas === 'kas_rt') ? "RT{$rtStr}" : strtoupper(str_replace('kas_', '', $bukuKas->jenis_kas));
                        $prefix = substr(preg_replace('/[^A-Za-z0-9]/', '', $prefix), 0, 6);
                        
                        $count = TransaksiKeuangan::where('nomor_transaksi', 'like', "TRX-{$dateStr}-{$prefix}-%")->count() + 1;
                        do {
                            $noTrx = "TRX-{$dateStr}-{$prefix}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
                            $count++;
                        } while (TransaksiKeuangan::where('nomor_transaksi', $noTrx)->exists());

                        $trx = TransaksiKeuangan::create([
                            'nomor_transaksi' => $noTrx,
                            'buku_kas_id' => $bukuKas->id,
                            'tagihan_keluarga_id' => $tagihan->id,
                            'tanggal_transaksi' => $tglBayar->toDateString(),
                            'jenis_transaksi' => 'pemasukan',
                            'kategori' => 'Alokasi Iuran: ' . $item->nama_iuran,
                            'nominal' => $alokasiNominal,
                            'saldo_sebelumnya' => $bukuKas->saldo_sekarang,
                            'saldo_setelahnya' => $bukuKas->saldo_sekarang + $alokasiNominal,
                            'metode_pembayaran' => $metodeStr,
                            'penyetor_penerima' => $penyetor,
                            'keterangan' => "Alokasi {$item->nama_iuran} [{$tagihan->nomor_tagihan}]",
                            'petugas_id' => $user->id,
                            'nama_petugas' => $user->name,
                            'status' => 'selesai',
                        ]);
                        $bukuKas->recalculateSaldo();
                        $trxId = $trx->id;
                    }
                }

                // Catat detail alokasi per item
                PembayaranAlokasi::create([
                    'tagihan_keluarga_id' => $tagihan->id,
                    'pembayaran_iuran_id' => $pembayaran->id,
                    'tagihan_item_id' => $item->id,
                    'buku_kas_id' => $bukuKas ? $bukuKas->id : null,
                    'transaksi_keuangan_id' => $trxId,
                    'nama_komponen' => $item->nama_iuran,
                    'nominal_alokasi' => $alokasiNominal,
                    'pemilik_dana' => $item->pemilik_dana ?: 'RW',
                    'status_alokasi' => $statusAlokasi,
                ]);
            }

            // Update Tagihan Induk
            $tagihan->total_dibayar += $nominalBayar;
            $tagihan->sisa_tagihan = max(0, $tagihan->total_tagihan - $tagihan->total_dibayar);
            $tagihan->metode_pembayaran_terakhir = ($metodeStr === 'tunai') ? 'Tunai' : (($metodeStr === 'qris') ? 'QRIS' : 'Transfer Bank');

            if ($tagihan->sisa_tagihan <= 0) {
                $tagihan->status = 'Lunas';
                $tagihan->tanggal_lunas = $tglBayar;
            } else {
                $tagihan->status = 'Dibayar Sebagian';
            }

            $tagihan->diverifikasi_oleh_id = $user->id;
            $tagihan->diverifikasi_pada = $tglBayar;
            $tagihan->save();

            // Catat audit log
            KeuanganAuditLog::create([
                'user_id' => $user->id,
                'tagihan_id' => $tagihan->id,
                'action' => 'proses_pembayaran',
                'deskripsi' => "Pembayaran {$tagihan->status} diterima sebesar Rp " . number_format($nominalBayar, 0, ',', '.') . " untuk tagihan [{$tagihan->nomor_tagihan}]. Kanal: {$kanalEnum}.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            $statusMsg = ($tagihan->status === 'Lunas') ? 'LUNAS' : 'SEBAGIAN (Sisa Rp ' . number_format($tagihan->sisa_tagihan, 0, ',', '.') . ')';
            return back()->with('success', "Pembayaran Rp " . number_format($nominalBayar, 0, ',', '.') . " berhasil diproses dan dialokasikan otomatis! Status tagihan kini {$statusMsg}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Verifikasi Pembayaran oleh Pengurus RT/RW (Mendukung Backward Compatibility Form Lama)
     */
    public function verifikasiPembayaran(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin', 'rt'])) {
            abort(403, 'Akses dibatasi.');
        }

        $tagihan = TagihanKeluarga::findOrFail($id);

        $metode = $request->input('metode_verifikasi', 'Tunai');
        $nominal = $request->input('nominal_bayar', $tagihan->sisa_tagihan);

        // Map metode verifikasi ke kanal pembayaran
        $kanal = 'tunai_melalui_rt';
        if ($user->role !== 'rt' && $metode === 'Tunai') {
            $kanal = 'tunai_rw';
        } elseif ($metode === 'Transfer Bank') {
            $kanal = 'transfer_bank';
        } elseif ($metode === 'QRIS') {
            $kanal = 'qris';
        }

        $request->merge([
            'nominal_bayar' => $nominal,
            'kanal_pembayaran' => $kanal,
            'catatan' => $request->input('catatan_petugas', 'Verifikasi sah oleh ' . $user->name),
        ]);

        return $this->prosesPembayaran($request, $id);
    }

    /**
     * Setor Titipan Tunai RT ke Kas RW & Unit Terkait
     */
    public function setorTunaiKeRw(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin', 'rt'])) {
            abort(403, 'Akses dibatasi.');
        }

        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $rtStr = $isRw 
            ? str_pad(preg_replace('/[^0-9]/', '', $request->input('rt_number', '1')), 3, '0', STR_PAD_LEFT)
            : str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $query = PembayaranAlokasi::where('status_alokasi', 'titipan_rt')
                ->whereHas('tagihanItem.tagihanKeluarga', function($q) use ($rtStr) {
                    $q->where('rt_number', $rtStr)->orWhere('rt_number', ltrim($rtStr, '0'));
                });

            if ($request->filled('alokasi_ids') && is_array($request->alokasi_ids)) {
                $query->whereIn('id', $request->alokasi_ids);
            }

            $titipans = $query->with(['bukuKas', 'tagihanItem.tagihanKeluarga', 'pembayaranIuran'])->get();

            if ($titipans->isEmpty()) {
                DB::rollBack();
                return back()->with('info', "Tidak ada dana titipan tunai RT {$rtStr} yang perlu disetorkan.");
            }

            $totalDisetor = 0;
            $tglSetor = Carbon::now();
            $dateStr = $tglSetor->format('Ym');

            foreach ($titipans as $alokasi) {
                $bukuKas = $alokasi->bukuKas;
                if (!$bukuKas) {
                    // Fallback Kas RW
                    $bukuKas = BukuKas::where('jenis_kas', 'kas_rw')->first();
                }

                $prefix = strtoupper(str_replace('kas_', '', $bukuKas->jenis_kas));
                $prefix = substr(preg_replace('/[^A-Za-z0-9]/', '', $prefix), 0, 6);

                $count = TransaksiKeuangan::where('nomor_transaksi', 'like', "TRX-{$dateStr}-{$prefix}-%")->count() + 1;
                do {
                    $noTrx = "TRX-{$dateStr}-{$prefix}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
                    $count++;
                } while (TransaksiKeuangan::where('nomor_transaksi', $noTrx)->exists());

                $tagihanObj = $alokasi->tagihanItem ? $alokasi->tagihanItem->tagihanKeluarga : null;

                $trx = TransaksiKeuangan::create([
                    'nomor_transaksi' => $noTrx,
                    'buku_kas_id' => $bukuKas->id,
                    'tagihan_keluarga_id' => $tagihanObj ? $tagihanObj->id : null,
                    'tanggal_transaksi' => $tglSetor->toDateString(),
                    'jenis_transaksi' => 'pemasukan',
                    'kategori' => 'Setoran Tunai dari RT ' . $rtStr,
                    'nominal' => $alokasi->nominal_alokasi,
                    'saldo_sebelumnya' => $bukuKas->saldo_sekarang,
                    'saldo_setelahnya' => $bukuKas->saldo_sekarang + $alokasi->nominal_alokasi,
                    'metode_pembayaran' => 'tunai',
                    'penyetor_penerima' => "Pengurus RT {$rtStr}",
                    'keterangan' => "Setoran tunai dari RT {$rtStr} untuk {$alokasi->nama_komponen} [" . ($tagihanObj ? $tagihanObj->nomor_tagihan : '') . "]",
                    'petugas_id' => $user->id,
                    'nama_petugas' => $user->name,
                    'status' => 'selesai',
                ]);

                $bukuKas->recalculateSaldo();

                // Update status alokasi menjadi disetor_ke_rw
                $alokasi->update([
                    'status_alokasi' => 'disetor_ke_rw',
                    'transaksi_keuangan_id' => $trx->id,
                ]);

                // Update status induk pembayaran jika semua alokasi sudah disetor
                if ($alokasi->pembayaranIuran) {
                    $hasPending = PembayaranAlokasi::where('pembayaran_iuran_id', $alokasi->pembayaran_iuran_id)
                        ->where('status_alokasi', 'titipan_rt')
                        ->exists();
                    if (!$hasPending) {
                        $alokasi->pembayaranIuran->update([
                            'status_setoran_rw' => 'sudah_disetor_ke_rw',
                            'disetor_ke_rw_pada' => $tglSetor,
                        ]);
                    }
                }

                $totalDisetor += $alokasi->nominal_alokasi;
            }

            // Catat Audit Log
            KeuanganAuditLog::create([
                'user_id' => $user->id,
                'action' => 'setor_titipan_rt',
                'deskripsi' => "Setoran tunai dana titipan RT {$rtStr} berhasil dibukukan ke Kas RW/Unit terkait sebesar Rp " . number_format($totalDisetor, 0, ',', '.') . " ({$titipans->count()} alokasi).",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return back()->with('success', "Setoran tunai RT {$rtStr} sebesar Rp " . number_format($totalDisetor, 0, ',', '.') . " berhasil diterima dan dibukukan ke kas tujuan tanpa double-counting!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses setoran RT: ' . $e->getMessage());
        }
    }

    /**
     * Daftarkan Keringanan / Diskon / Pembebasan Iuran KK
     */
    public function keringananStore(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Hanya Pengurus RW yang berwenang memberikan keringanan.');
        }

        $request->validate([
            'kartu_keluarga_id' => 'required|exists:kartu_keluargas,id',
            'iuran_tarif_id' => 'nullable|exists:iuran_tarifs,id',
            'jenis_keringanan' => 'required|in:diskon_nominal,diskon_persen,bebas_iuran,penundaan',
            'nilai_potongan' => 'required|numeric|min:0',
            'periode_mulai' => 'nullable|date_format:Y-m',
            'periode_selesai' => 'nullable|date_format:Y-m',
            'alasan' => 'required|string|max:255',
        ]);

        $keringanan = IuranKeringanan::create([
            'kartu_keluarga_id' => $request->kartu_keluarga_id,
            'iuran_tarif_id' => $request->iuran_tarif_id,
            'jenis_keringanan' => $request->jenis_keringanan,
            'nilai_keringanan' => $request->nilai_potongan,
            'periode_mulai' => $request->periode_mulai ?: Carbon::now()->format('Y-m'),
            'periode_selesai' => $request->periode_selesai,
            'alasan' => $request->alasan,
            'disetujui_oleh_id' => $user->id,
            'status' => 'aktif',
        ]);

        return back()->with('success', 'Keringanan iuran berhasil dicatat dan akan berlaku pada penerbitan tagihan terkait.');
    }

    /**
     * Batalkan Status Keringanan Iuran
     */
    public function keringananBatal(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Hanya Pengurus RW yang berwenang.');
        }

        $keringanan = IuranKeringanan::findOrFail($id);
        $keringanan->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Keringanan iuran berhasil dibatalkan.');
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

        // Bebaskan juga item-itemnya
        $tagihan->items()->update([
            'nominal_diskon' => DB::raw('nominal_subtotal'),
            'nominal_akhir' => 0,
            'sisa_item' => 0,
            'status_pembayaran' => 'lunas',
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

        $tagihan = TagihanKeluarga::with([
            'kartuKeluarga.anggota',
            'kepalaKeluarga',
            'diverifikasiOleh',
            'items.bukuKas',
            'pembayarans.alokasis.bukuKas',
            'pembayarans.penerima'
        ])->findOrFail($id);

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
