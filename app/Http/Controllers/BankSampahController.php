<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\BankSampah;
use App\Models\BankSampahKategori;
use App\Models\BankSampahTarifHistory;
use App\Models\BankSampahTransaksi;
use App\Models\BankSampahTransaksiItem;
use App\Models\BankSampahSaldo;
use App\Models\BankSampahLedger;
use App\Models\User;

class BankSampahController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman Utama Portal Bank Sampah Terpadu RW 013
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Daftar Unit Bank Sampah 8 RT
        $bankSampahList = BankSampah::with('petugas')->orderBy('rt_number')->get();

        // 2. Kategori & Tarif Aktif
        $kategoriList = BankSampahKategori::where('is_aktif', true)->orderBy('urutan')->get();

        // 3. Saldo Tabungan Pribadi Pengguna
        $mySaldo = BankSampahSaldo::firstOrCreate(
            ['user_id' => $user->id],
            [
                'total_kg_terkumpul' => 0,
                'saldo_aktif' => 0,
                'total_penarikan' => 0,
                'total_dihasilkan' => 0
            ]
        );

        // 4. Statistik Keseluruhan (HANYA DARI TRANSAKSI SELESAI AKTUAL)
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'total_kg_hari_ini' => (float) BankSampahTransaksi::where('status', 'selesai')->whereDate('tanggal_selesai', $today)->sum('total_berat_kg'),
            'total_kg_bulan_ini' => (float) BankSampahTransaksi::where('status', 'selesai')->where('tanggal_selesai', '>=', $thisMonth)->sum('total_berat_kg'),
            'total_kg_total' => (float) BankSampahTransaksi::where('status', 'selesai')->sum('total_berat_kg'),
            'total_rupiah_total' => (float) BankSampahTransaksi::where('status', 'selesai')->sum('total_rupiah'),
            'total_warga_aktif' => (int) BankSampahTransaksi::where('status', 'selesai')->distinct('user_id')->count('user_id'),
            'total_tabungan_beredar' => (float) BankSampahSaldo::sum('saldo_aktif'),
        ];

        // 5. Rekap Kg per Kategori Sampah (Aktual)
        $rekapKategori = BankSampahTransaksiItem::join('bank_sampah_transaksis', 'bank_sampah_transaksi_items.transaksi_id', '=', 'bank_sampah_transaksis.id')
            ->join('bank_sampah_kategoris', 'bank_sampah_transaksi_items.kategori_id', '=', 'bank_sampah_kategoris.id')
            ->where('bank_sampah_transaksis.status', 'selesai')
            ->select(
                'bank_sampah_kategoris.nama_kategori',
                'bank_sampah_kategoris.badge_color',
                DB::raw('SUM(bank_sampah_transaksi_items.berat_satuan) as total_berat'),
                DB::raw('SUM(bank_sampah_transaksi_items.subtotal) as total_subtotal')
            )
            ->groupBy('bank_sampah_kategoris.id', 'bank_sampah_kategoris.nama_kategori', 'bank_sampah_kategoris.badge_color')
            ->get();

        // 6. Rekap Tonase per RT (Aktual)
        $rekapRt = BankSampahTransaksi::where('status', 'selesai')
            ->select('rt_number', DB::raw('SUM(total_berat_kg) as total_kg'), DB::raw('COUNT(id) as total_tx'))
            ->groupBy('rt_number')
            ->orderBy('rt_number')
            ->pluck('total_kg', 'rt_number')
            ->toArray();

        // 7. Transaksi Terbaru Berdasarkan Hak Akses
        $queryTx = BankSampahTransaksi::with(['user', 'petugas', 'bankSampah', 'items.kategori'])
            ->orderBy('created_at', 'desc');

        if ($user->isRw()) {
            // RW melihat seluruhnya
            $recentTransactions = (clone $queryTx)->limit(10)->get();
            $pendingCount = BankSampahTransaksi::where('status', 'menunggu')->count();
        } elseif ($user->isPetugasBankSampah()) {
            // Petugas RT melihat RT-nya
            $cleanRt = str_pad(preg_replace('/[^0-9]/', '', (string)$user->rt_number), 3, '0', STR_PAD_LEFT);
            $recentTransactions = (clone $queryTx)->where('rt_number', $cleanRt)->limit(10)->get();
            $pendingCount = BankSampahTransaksi::where('status', 'menunggu')->where('rt_number', $cleanRt)->count();
        } else {
            // Warga biasa melihat riwayatnya sendiri
            $recentTransactions = (clone $queryTx)->where('user_id', $user->id)->limit(8)->get();
            $pendingCount = BankSampahTransaksi::where('user_id', $user->id)->where('status', 'menunggu')->count();
        }

        // Hitung total simulasi awal (sebagai baseline visualisasi sesuai permintaan user)
        $totalSimulasiAwal = $bankSampahList->sum('total_terkumpul_kg');

        return view('bank-sampah.index', compact(
            'user',
            'bankSampahList',
            'kategoriList',
            'mySaldo',
            'stats',
            'rekapKategori',
            'rekapRt',
            'recentTransactions',
            'pendingCount',
            'totalSimulasiAwal'
        ));
    }

    /**
     * Warga Mengajukan Setoran Sampah Baru
     */
    public function storePengajuan(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'bank_sampah_id' => 'required|exists:bank_sampahs,id',
            'metode_penyerahan' => 'required|in:drop_point,jemput_ke_rumah',
            'catatan_warga' => 'nullable|string|max:500',
            'estimasi_deskripsi' => 'nullable|string|max:255',
        ]);

        // Pencegahan Double Submit (Idempotency check dalam 15 detik terakhir)
        $recentDuplicate = BankSampahTransaksi::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subSeconds(15))
            ->where('status', 'menunggu')
            ->first();

        if ($recentDuplicate) {
            return redirect()->route('bank-sampah.show', $recentDuplicate->id)
                ->with('warning', 'Pengajuan Anda sedang diproses dan telah terdaftar sebelumnya.');
        }

        $bankSampah = BankSampah::findOrFail($validated['bank_sampah_id']);

        $transaksi = BankSampahTransaksi::create([
            'kode_transaksi' => BankSampahTransaksi::generateKodeTransaksi(),
            'user_id' => $user->id,
            'bank_sampah_id' => $bankSampah->id,
            'rt_number' => $bankSampah->rt_number,
            'status' => 'menunggu',
            'metode_penyerahan' => $validated['metode_penyerahan'],
            'tanggal_pengajuan' => Carbon::now(),
            'catatan_warga' => $validated['catatan_warga'] . ($validated['estimasi_deskripsi'] ? " (Estimasi: {$validated['estimasi_deskripsi']})" : ''),
            'total_berat_kg' => 0,
            'total_rupiah' => 0,
            'is_simulasi' => false,
        ]);

        return redirect()->route('bank-sampah.show', $transaksi->id)
            ->with('success', 'Pengajuan setoran sampah berhasil dibuat dengan kode ' . $transaksi->kode_transaksi . '. Petugas Bank Sampah RT ' . $transaksi->rt_number . ' akan segera menindaklanjuti.');
    }

    /**
     * Detail Transaksi & Resi / Bukti Digital
     */
    public function showTransaksi($id)
    {
        $user = Auth::user();
        $transaksi = BankSampahTransaksi::with(['user', 'petugas', 'bankSampah', 'items.kategori', 'ledger'])->findOrFail($id);

        $this->authorize('viewTransaksi', $transaksi);

        $kategoris = BankSampahKategori::where('is_aktif', true)->orderBy('urutan')->get();

        return view('bank-sampah.show', compact('user', 'transaksi', 'kategoris'));
    }

    /**
     * Warga Membatalkan Pengajuan (Hanya jika masih 'menunggu')
     */
    public function batalTransaksi(Request $request, $id)
    {
        $user = Auth::user();
        $transaksi = BankSampahTransaksi::findOrFail($id);

        $this->authorize('cancelTransaksi', $transaksi);

        $transaksi->update([
            'status' => 'dibatalkan',
            'catatan_warga' => $transaksi->catatan_warga . ' [Dibatalkan oleh Warga pada ' . Carbon::now()->format('d/m/Y H:i') . ']',
        ]);

        return redirect()->route('bank-sampah.show', $transaksi->id)
            ->with('info', 'Pengajuan setoran sampah telah dibatalkan.');
    }

    /**
     * Dashboard Pengelolaan Setoran (Khusus Petugas Bank Sampah RT, Ketua RT, dan Admin RW)
     */
    public function kelola(Request $request)
    {
        $user = Auth::user();

        if (!$user->isRw() && !$user->isPetugasBankSampah()) {
            abort(403, 'Akses terbatas untuk Petugas Bank Sampah RT, Ketua RT, dan Pengurus RW.');
        }

        $query = BankSampahTransaksi::with(['user', 'petugas', 'bankSampah', 'items.kategori'])
            ->orderBy('created_at', 'desc');

        // Scoping RT
        if ($user->isRw()) {
            if ($request->filled('rt')) {
                $query->where('rt_number', str_pad($request->rt, 3, '0', STR_PAD_LEFT));
            }
        } else {
            $userRt = str_pad(preg_replace('/[^0-9]/', '', (string)$user->rt_number), 3, '0', STR_PAD_LEFT);
            $query->where('rt_number', $userRt);
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Pencarian Nama / Kode
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('kode_transaksi', 'like', "%{$q}%")
                    ->orWhereHas('user', function($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%");
                    });
            });
        }

        $transaksis = $query->paginate(15)->withQueryString();

        // Counter Tab Status
        $baseCounter = BankSampahTransaksi::query();
        if (!$user->isRw()) {
            $userRt = str_pad(preg_replace('/[^0-9]/', '', (string)$user->rt_number), 3, '0', STR_PAD_LEFT);
            $baseCounter->where('rt_number', $userRt);
        }

        $counts = [
            'semua' => (clone $baseCounter)->count(),
            'menunggu' => (clone $baseCounter)->where('status', 'menunggu')->count(),
            'dijadwalkan' => (clone $baseCounter)->where('status', 'dijadwalkan')->count(),
            'sedang_ditimbang' => (clone $baseCounter)->where('status', 'sedang_ditimbang')->count(),
            'menunggu_konfirmasi' => (clone $baseCounter)->where('status', 'menunggu_konfirmasi')->count(),
            'selesai' => (clone $baseCounter)->where('status', 'selesai')->count(),
            'ditolak' => (clone $baseCounter)->where('status', 'ditolak')->count(),
        ];

        return view('bank-sampah.kelola', compact('user', 'transaksis', 'counts'));
    }

    /**
     * Petugas Menerima & Menjadwalkan Pengajuan Warga
     */
    public function terimaTransaksi(Request $request, $id)
    {
        $user = Auth::user();
        $transaksi = BankSampahTransaksi::findOrFail($id);

        $this->authorize('manageTransaksi', $transaksi);

        if (!in_array($transaksi->status, ['menunggu', 'dijadwalkan'])) {
            return back()->with('error', 'Status transaksi saat ini tidak dapat dijadwalkan ulang.');
        }

        $validated = $request->validate([
            'tanggal_dijadwalkan' => 'nullable|date',
            'catatan_petugas' => 'nullable|string|max:500',
            'langsung_timbang' => 'nullable|boolean',
        ]);

        $newStatus = !empty($validated['langsung_timbang']) ? 'sedang_ditimbang' : 'dijadwalkan';

        $transaksi->update([
            'status' => $newStatus,
            'petugas_id' => $user->id,
            'tanggal_dijadwalkan' => $validated['tanggal_dijadwalkan'] ? Carbon::parse($validated['tanggal_dijadwalkan']) : Carbon::now(),
            'catatan_petugas' => $validated['catatan_petugas'] ?? 'Pengajuan telah diterima oleh petugas Bank Sampah.',
        ]);

        return redirect()->route('bank-sampah.show', $transaksi->id)
            ->with('success', 'Pengajuan berhasil diproses menjadi status: ' . $transaksi->status_label);
    }

    /**
     * Petugas Menginput Hasil Penimbangan Aktual Berdasarkan Jenis Sampah
     */
    public function timbangTransaksi(Request $request, $id)
    {
        $user = Auth::user();
        $transaksi = BankSampahTransaksi::findOrFail($id);

        $this->authorize('manageTransaksi', $transaksi);

        if (in_array($transaksi->status, ['selesai', 'ditolak', 'dibatalkan'])) {
            return back()->with('error', 'Transaksi yang telah selesai, ditolak, atau dibatalkan tidak dapat ditimbang ulang.');
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.kategori_id' => 'required|exists:bank_sampah_kategoris,id',
            'items.*.berat' => 'required|numeric|min:0.01|max:5000',
            'catatan_petugas' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($transaksi, $validated, $user) {
            // Hapus item lama jika ada input penimbangan ulang
            $transaksi->items()->delete();

            $totalKg = 0;
            $totalRp = 0;

            foreach ($validated['items'] as $item) {
                $kategori = BankSampahKategori::findOrFail($item['kategori_id']);
                $berat = (float) $item['berat'];
                $subtotal = $berat * (float) $kategori->harga_per_satuan;

                $totalKg += $berat;
                $totalRp += $subtotal;

                BankSampahTransaksiItem::create([
                    'transaksi_id' => $transaksi->id,
                    'kategori_id' => $kategori->id,
                    'berat_satuan' => $berat,
                    'satuan' => $kategori->satuan,
                    'harga_per_satuan' => $kategori->harga_per_satuan,
                    'subtotal' => $subtotal,
                ]);
            }

            $transaksi->update([
                'petugas_id' => $user->id,
                'status' => 'menunggu_konfirmasi',
                'tanggal_penimbangan' => Carbon::now(),
                'total_berat_kg' => $totalKg,
                'total_rupiah' => $totalRp,
                'catatan_petugas' => $validated['catatan_petugas'] ?? $transaksi->catatan_petugas,
            ]);
        });

        return redirect()->route('bank-sampah.show', $transaksi->id)
            ->with('success', 'Hasil penimbangan berhasil dicatat (Total: ' . $transaksi->total_berat_formatted . ', Nilai: ' . $transaksi->total_rupiah_formatted . '). Silakan lakukan konfirmasi final untuk memasukkan saldo ke tabungan warga.');
    }

    /**
     * Petugas Mengonfirmasi Transaksi (Selesai -> Saldo Masuk ke Rekening Tabungan Warga)
     */
    public function konfirmasiSelesai(Request $request, $id)
    {
        $user = Auth::user();

        return DB::transaction(function () use ($id, $user) {
            $transaksi = BankSampahTransaksi::with('items')->lockForUpdate()->findOrFail($id);

            $this->authorize('manageTransaksi', $transaksi);

            if ($transaksi->status === 'selesai') {
                return redirect()->route('bank-sampah.show', $transaksi->id)
                    ->with('info', 'Transaksi ini sudah selesai sebelumnya.');
            }

            if ($transaksi->items->count() === 0 || $transaksi->total_berat_kg <= 0) {
                return back()->with('error', 'Transaksi belum memiliki hasil penimbangan sampah yang valid.');
            }

            // 1. Update status transaksi
            $transaksi->update([
                'status' => 'selesai',
                'petugas_id' => $user->id,
                'tanggal_selesai' => Carbon::now(),
            ]);

            // 2. Update Saldo Tabungan Warga
            $saldoWarga = BankSampahSaldo::firstOrCreate(
                ['user_id' => $transaksi->user_id],
                [
                    'total_kg_terkumpul' => 0,
                    'saldo_aktif' => 0,
                    'total_penarikan' => 0,
                    'total_dihasilkan' => 0
                ]
            );

            $saldoSebelum = $saldoWarga->saldo_aktif;
            $saldoSesudah = $saldoSebelum + $transaksi->total_rupiah;

            $saldoWarga->saldo_aktif = $saldoSesudah;
            $saldoWarga->total_kg_terkumpul += $transaksi->total_berat_kg;
            $saldoWarga->total_dihasilkan += $transaksi->total_rupiah;
            $saldoWarga->transaksi_terakhir_at = Carbon::now();
            $saldoWarga->save();

            // 3. Catat di Ledger Tabungan Warga
            BankSampahLedger::create([
                'user_id' => $transaksi->user_id,
                'transaksi_id' => $transaksi->id,
                'tipe' => 'setoran',
                'debit' => $transaksi->total_rupiah,
                'kredit' => 0,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'keterangan' => 'Setoran Sampah Terpadu #' . $transaksi->kode_transaksi . ' (' . $transaksi->total_berat_formatted . ')',
                'petugas_id' => $user->id,
            ]);

            return redirect()->route('bank-sampah.show', $transaksi->id)
                ->with('success', 'Transaksi berhasil diselesaikan! Saldo sebesar ' . $transaksi->total_rupiah_formatted . ' telah otomatis ditambahkan ke rekening tabungan sampah warga.');
        });
    }

    /**
     * Petugas Menolak Transaksi
     */
    public function tolakTransaksi(Request $request, $id)
    {
        $user = Auth::user();
        $transaksi = BankSampahTransaksi::findOrFail($id);

        $this->authorize('manageTransaksi', $transaksi);

        if ($transaksi->status === 'selesai') {
            return back()->with('error', 'Transaksi yang telah selesai tidak dapat ditolak.');
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        $transaksi->update([
            'status' => 'ditolak',
            'petugas_id' => $user->id,
            'alasan_penolakan' => $validated['alasan_penolakan'],
        ]);

        return redirect()->route('bank-sampah.show', $transaksi->id)
            ->with('warning', 'Pengajuan setoran telah ditolak dengan alasan: ' . $validated['alasan_penolakan']);
    }

    /**
     * Halaman Riwayat Tabungan Pribadi Warga (Buku Rekening Sampah Digital)
     */
    public function riwayatSaya(Request $request)
    {
        $user = Auth::user();

        $saldo = BankSampahSaldo::firstOrCreate(
            ['user_id' => $user->id],
            [
                'total_kg_terkumpul' => 0,
                'saldo_aktif' => 0,
                'total_penarikan' => 0,
                'total_dihasilkan' => 0
            ]
        );

        $ledgers = BankSampahLedger::with(['transaksi', 'petugas'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $transaksis = BankSampahTransaksi::with(['items.kategori', 'petugas', 'bankSampah'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bank-sampah.riwayat', compact('user', 'saldo', 'ledgers', 'transaksis'));
    }

    /**
     * Kelola Master Kategori & Tarif Harga Sampah (Khusus Admin RW)
     */
    public function kelolaTarif()
    {
        $user = Auth::user();

        if (!$user->isRw()) {
            abort(403, 'Akses terbatas untuk Pengurus RW.');
        }

        $kategoris = BankSampahKategori::with(['tarifHistories.diubahOleh'])->orderBy('urutan')->get();

        return view('bank-sampah.tarif', compact('user', 'kategoris'));
    }

    /**
     * Update Tarif Harga per Kg / Liter oleh Admin RW dengan Audit History
     */
    public function updateTarif(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isRw()) {
            abort(403, 'Akses terbatas untuk Pengurus RW.');
        }

        $kategori = BankSampahKategori::findOrFail($id);

        $validated = $request->validate([
            'harga_per_satuan' => 'required|numeric|min:0',
            'alasan_perubahan' => 'required|string|max:255',
            'is_aktif' => 'nullable|boolean',
        ]);

        $hargaLama = $kategori->harga_per_satuan;
        $hargaBaru = (float) $validated['harga_per_satuan'];

        DB::transaction(function () use ($kategori, $hargaLama, $hargaBaru, $validated, $user) {
            $kategori->update([
                'harga_per_satuan' => $hargaBaru,
                'is_aktif' => $validated['is_aktif'] ?? $kategori->is_aktif,
            ]);

            // Simpan riwayat perubahan harga
            BankSampahTarifHistory::create([
                'kategori_id' => $kategori->id,
                'harga_lama' => $hargaLama,
                'harga_baru' => $hargaBaru,
                'diubah_oleh_id' => $user->id,
                'alasan_perubahan' => $validated['alasan_perubahan'],
            ]);
        });

        return back()->with('success', 'Tarif kategori ' . $kategori->nama_kategori . ' berhasil diperbarui menjadi Rp ' . number_format($hargaBaru, 0, ',', '.') . ' / ' . $kategori->satuan . '. Riwayat perubahan tercatat dalam sistem audit.');
    }

    /**
     * Admin RW Menugaskan / Mengubah Petugas Unit Bank Sampah per RT
     */
    public function updatePetugas(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isRw()) {
            abort(403, 'Akses terbatas untuk Pengurus RW.');
        }

        $unit = BankSampah::findOrFail($id);

        $validated = $request->validate([
            'petugas_id' => 'required|exists:users,id',
            'lokasi' => 'required|string|max:255',
            'jadwal' => 'required|string|max:255',
        ]);

        $petugas = User::findOrFail($validated['petugas_id']);

        $unit->update([
            'petugas_id' => $petugas->id,
            'lokasi' => $validated['lokasi'],
            'jadwal' => $validated['jadwal'],
        ]);

        return back()->with('success', 'Petugas pengelola unit Bank Sampah RT ' . $unit->rt_number . ' berhasil diperbarui menjadi: ' . $petugas->name);
    }

    /**
     * Laporan Bulanan & Rekap Cetak Bank Sampah RW 013
     */
    public function laporan(Request $request)
    {
        $user = Auth::user();

        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $rt = $request->input('rt', null);

        $query = BankSampahTransaksi::with(['user', 'petugas', 'items.kategori', 'bankSampah'])
            ->where('status', 'selesai')
            ->whereMonth('tanggal_selesai', $bulan)
            ->whereYear('tanggal_selesai', $tahun);

        if ($rt) {
            $query->where('rt_number', str_pad($rt, 3, '0', STR_PAD_LEFT));
        }

        $transaksis = $query->orderBy('tanggal_selesai', 'asc')->get();

        $totalKg = $transaksis->sum('total_berat_kg');
        $totalRupiah = $transaksis->sum('total_rupiah');
        $totalWarga = $transaksis->pluck('user_id')->unique()->count();

        // Rekap per Kategori untuk Laporan
        $rekapKategori = BankSampahTransaksiItem::join('bank_sampah_transaksis', 'bank_sampah_transaksi_items.transaksi_id', '=', 'bank_sampah_transaksis.id')
            ->join('bank_sampah_kategoris', 'bank_sampah_transaksi_items.kategori_id', '=', 'bank_sampah_kategoris.id')
            ->where('bank_sampah_transaksis.status', 'selesai')
            ->whereMonth('bank_sampah_transaksis.tanggal_selesai', $bulan)
            ->whereYear('bank_sampah_transaksis.tanggal_selesai', $tahun)
            ->when($rt, function($q) use ($rt) {
                return $q->where('bank_sampah_transaksis.rt_number', str_pad($rt, 3, '0', STR_PAD_LEFT));
            })
            ->select(
                'bank_sampah_kategoris.nama_kategori',
                'bank_sampah_kategoris.satuan',
                DB::raw('SUM(bank_sampah_transaksi_items.berat_satuan) as total_berat'),
                DB::raw('SUM(bank_sampah_transaksi_items.subtotal) as total_subtotal')
            )
            ->groupBy('bank_sampah_kategoris.id', 'bank_sampah_kategoris.nama_kategori', 'bank_sampah_kategoris.satuan')
            ->get();

        // Rekap per RT untuk Laporan
        $rekapPerRt = BankSampahTransaksi::where('status', 'selesai')
            ->whereMonth('tanggal_selesai', $bulan)
            ->whereYear('tanggal_selesai', $tahun)
            ->select('rt_number', DB::raw('SUM(total_berat_kg) as total_kg'), DB::raw('SUM(total_rupiah) as total_rp'), DB::raw('COUNT(id) as total_transaksi'))
            ->groupBy('rt_number')
            ->orderBy('rt_number')
            ->get();

        $isPrint = $request->has('print');

        return view('bank-sampah.laporan', compact(
            'user',
            'bulan',
            'tahun',
            'rt',
            'transaksis',
            'totalKg',
            'totalRupiah',
            'totalWarga',
            'rekapKategori',
            'rekapPerRt',
            'isPrint'
        ));
    }

    /**
     * Cetak Resi / Slip Bukti Setoran Sampah Digital
     */
    public function cetakSlip($id)
    {
        $user = Auth::user();
        $transaksi = BankSampahTransaksi::with(['user', 'petugas', 'bankSampah', 'items.kategori'])->findOrFail($id);

        $this->authorize('viewTransaksi', $transaksi);

        return view('bank-sampah.cetak_slip', compact('user', 'transaksi'));
    }
}
