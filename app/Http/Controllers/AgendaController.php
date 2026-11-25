<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Database\Seeders\AgendaSeeder;

class AgendaController extends Controller
{
    /**
     * Helper untuk mengambil collection data Agenda baik dari database maupun storage fallback
     */
    protected function getAgendasQuery()
    {
        // Jalankan auto-seed jika tabel belum memiliki data
        try {
            if (Schema::hasTable('agendas')) {
                if (Agenda::count() === 0) {
                    $seeder = new AgendaSeeder();
                    $seeder->run();
                }
                return Agenda::query();
            }
        } catch (\Throwable $e) {
            // Abaikan error koneksi database di lingkungan sandbox
        }

        // Fallback jika belum migrate database
        return $this->getFallbackAgendas();
    }

    /**
     * Fallback collection jika tabel MySQL belum ter-migrate
     */
    protected function getFallbackAgendas()
    {
        $filePath = storage_path('app/agenda_data.json');
        if (!file_exists($filePath)) {
            $seeder = new AgendaSeeder();
            $seeder->run();
        }

        $raw = file_get_contents($filePath);
        $items = json_decode($raw, true) ?: [];

        $collection = collect();
        foreach ($items as $idx => $row) {
            $model = new Agenda();
            $model->id = $row['id'] ?? ($idx + 1);
            $model->fill($row);
            $model->exists = true;
            $collection->push($model);
        }

        return $collection;
    }

    /**
     * Halaman Utama Agenda & Rapat RW (Sistem Navigasi 3 Tab)
     * Rute: GET /agenda-rw (agenda-rw.index)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeTab = $request->input('tab', 'mendatang');
        if (!in_array($activeTab, ['mendatang', 'monitoring_rt', 'riwayat'])) {
            $activeTab = 'mendatang';
        }

        // Filter Parameters
        $kategori = $request->input('kategori');
        $penyelenggara = $request->input('penyelenggara');
        $rtFilter = $request->input('rt');
        $statusKegiatan = $request->input('status');
        $statusLaporan = $request->input('status_laporan');
        $search = $request->input('search');
        $periode = $request->input('periode');

        // Query Base
        $queryResult = $this->getAgendasQuery();

        if ($queryResult instanceof \Illuminate\Database\Eloquent\Builder) {
            $allAgendas = $queryResult->get();
        } else {
            $allAgendas = $queryResult;
        }

        // --- 1. METRIK RINGKASAN AGENDA RW 013 ---
        $totalUpcoming = $allAgendas->whereIn('status_kegiatan', ['akan_datang', 'sedang_berlangsung'])->count();
        $totalMonitoringRt = $allAgendas->where('penyelenggara', 'rt')->count();
        $totalRiwayat = $allAgendas->whereIn('status_kegiatan', ['selesai', 'dibatalkan', 'tidak_terlaksana'])->count();
        $totalLaporanLengkap = $allAgendas->where('status_laporan', 'laporan_lengkap')->count();

        // --- 2. TAB 1: AGENDA MENDATANG ---
        $agendaMendatang = $allAgendas->filter(function ($item) use ($kategori, $penyelenggara, $search) {
            // Hanya agenda akan datang atau sedang berlangsung
            if (!in_array($item->status_kegiatan, ['akan_datang', 'sedang_berlangsung'])) {
                return false;
            }
            // Filter Kategori
            if (!empty($kategori) && $item->kategori !== $kategori) {
                return false;
            }
            // Filter Penyelenggara (rw / rt_001..008)
            if (!empty($penyelenggara)) {
                if ($penyelenggara === 'rw' && $item->penyelenggara !== 'rw') {
                    return false;
                }
                if ($penyelenggara !== 'rw') {
                    $cleanRtPeny = str_pad(preg_replace('/[^0-9]/', '', $penyelenggara), 3, '0', STR_PAD_LEFT);
                    if ($item->penyelenggara !== 'rt' || $item->clean_rt !== 'RT ' . $cleanRtPeny) {
                        return false;
                    }
                }
            }
            // Search keyword
            if (!empty($search)) {
                $needle = strtolower($search);
                $haystack = strtolower($item->judul . ' ' . $item->lokasi . ' ' . $item->peserta . ' ' . $item->deskripsi);
                if (!str_contains($haystack, $needle)) {
                    return false;
                }
            }
            return true;
        })->sortBy('tanggal')->values();

        // --- 3. TAB 2: MONITORING AGENDA RT (RT 001 - RT 008) ---
        $monitoringRtList = $allAgendas->filter(function ($item) use ($rtFilter, $kategori, $statusKegiatan, $statusLaporan, $search) {
            // Hanya kegiatan yang diselenggarakan oleh RT
            if ($item->penyelenggara !== 'rt') {
                return false;
            }
            // Filter RT tertentu
            if (!empty($rtFilter)) {
                $cleanFilter = str_pad(preg_replace('/[^0-9]/', '', $rtFilter), 3, '0', STR_PAD_LEFT);
                if ($item->clean_rt !== 'RT ' . $cleanFilter) {
                    return false;
                }
            }
            // Filter Kategori
            if (!empty($kategori) && $item->kategori !== $kategori) {
                return false;
            }
            // Filter Status Pelaksanaan
            if (!empty($statusKegiatan) && $item->status_kegiatan !== $statusKegiatan) {
                return false;
            }
            // Filter Status Laporan Kegiatan
            if (!empty($statusLaporan) && $item->status_laporan !== $statusLaporan) {
                return false;
            }
            // Search keyword
            if (!empty($search)) {
                $needle = strtolower($search);
                $haystack = strtolower($item->judul . ' ' . $item->lokasi . ' ' . $item->penanggung_jawab . ' ' . $item->deskripsi);
                if (!str_contains($haystack, $needle)) {
                    return false;
                }
            }
            return true;
        })->sortByDesc('tanggal')->values();

        // --- 4. TAB 3: RIWAYAT AGENDA (SELESAI / DIBATALKAN / TIDAK TERLAKSANA) ---
        $riwayatList = $allAgendas->filter(function ($item) use ($kategori, $penyelenggara, $statusKegiatan, $search, $periode) {
            // Hanya agenda masa lalu / selesai / batal
            if (!in_array($item->status_kegiatan, ['selesai', 'dibatalkan', 'tidak_terlaksana'])) {
                return false;
            }
            // Filter Kategori
            if (!empty($kategori) && $item->kategori !== $kategori) {
                return false;
            }
            // Filter Penyelenggara
            if (!empty($penyelenggara)) {
                if ($penyelenggara === 'rw' && $item->penyelenggara !== 'rw') {
                    return false;
                }
                if ($penyelenggara !== 'rw') {
                    $cleanRtPeny = str_pad(preg_replace('/[^0-9]/', '', $penyelenggara), 3, '0', STR_PAD_LEFT);
                    if ($item->penyelenggara !== 'rt' || $item->clean_rt !== 'RT ' . $cleanRtPeny) {
                        return false;
                    }
                }
            }
            // Filter Status Akhir
            if (!empty($statusKegiatan) && $item->status_kegiatan !== $statusKegiatan) {
                return false;
            }
            // Filter Periode / Tahun
            if (!empty($periode)) {
                $itemYear = Carbon::parse($item->tanggal)->format('Y');
                if ($itemYear !== $periode) {
                    return false;
                }
            }
            // Search keyword
            if (!empty($search)) {
                $needle = strtolower($search);
                $haystack = strtolower($item->judul . ' ' . $item->lokasi . ' ' . $item->ringkasan_hasil . ' ' . $item->deskripsi);
                if (!str_contains($haystack, $needle)) {
                    return false;
                }
            }
            return true;
        })->sortByDesc('tanggal')->values();

        // Data Kategori untuk Filter Dropdown
        $kategoriList = [
            'rapat_koordinasi' => 'Rapat Koordinasi',
            'kegiatan_sosial' => 'Kegiatan Sosial',
            'kegiatan_warga' => 'Kegiatan Warga',
            'kesehatan' => 'Kesehatan',
            'kebersihan' => 'Kebersihan',
            'keamanan' => 'Keamanan',
            'lainnya' => 'Lainnya',
        ];

        return view('agenda.index', compact(
            'user',
            'activeTab',
            'totalUpcoming',
            'totalMonitoringRt',
            'totalRiwayat',
            'totalLaporanLengkap',
            'agendaMendatang',
            'monitoringRtList',
            'riwayatList',
            'kategoriList',
            'kategori',
            'penyelenggara',
            'rtFilter',
            'statusKegiatan',
            'statusLaporan',
            'search',
            'periode'
        ));
    }

    /**
     * API Detail Agenda untuk Modal Interaktif (11 Poin Informasi)
     * Rute: GET /agenda-rw/{id} (agenda-rw.show)
     */
    public function show($id)
    {
        $agenda = null;
        try {
            if (Schema::hasTable('agendas')) {
                $agenda = Agenda::find($id);
            }
        } catch (\Throwable $e) {}

        if (!$agenda) {
            $fallback = $this->getFallbackAgendas();
            $agenda = $fallback->firstWhere('id', (int)$id) ?: $fallback->firstWhere('id', (string)$id);
        }

        if (!$agenda) {
            return response()->json(['success' => false, 'message' => 'Agenda tidak ditemukan.'], 404);
        }

        $user = Auth::user();
        $isRw = in_array($user->role, ['rw', 'admin', 'super_admin']);
        $canEdit = ($agenda->penyelenggara === 'rw' && $isRw) || ($agenda->penyelenggara === 'rt' && $user->isRt() && $user->clean_rt_number == $agenda->rt_number);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $agenda->id,
                'judul' => $agenda->judul,
                'deskripsi' => $agenda->deskripsi,
                'tanggal' => $agenda->tanggal ? Carbon::parse($agenda->tanggal)->translatedFormat('l, d F Y') : '-',
                'tanggal_raw' => $agenda->tanggal ? Carbon::parse($agenda->tanggal)->format('Y-m-d') : '',
                'waktu' => $agenda->waktu,
                'lokasi' => $agenda->lokasi,
                'kategori' => $agenda->kategori,
                'kategori_label' => $agenda->kategori_label,
                'kategori_icon' => $agenda->kategori_icon,
                'penyelenggara' => $agenda->penyelenggara,
                'penyelenggara_label' => $agenda->penyelenggara_label,
                'rt_number' => $agenda->clean_rt,
                'peserta' => $agenda->peserta ?: 'Warga Terkait',
                'penanggung_jawab' => $agenda->penanggung_jawab,
                'prioritas' => $agenda->prioritas,
                'prioritas_badge' => $agenda->prioritas_badge,
                'status_kegiatan' => $agenda->status_kegiatan,
                'status_badge' => $agenda->status_badge,
                'status_laporan' => $agenda->status_laporan,
                'status_laporan_badge' => $agenda->status_laporan_badge,
                'ringkasan_hasil' => $agenda->ringkasan_hasil,
                'dokumentasi_path' => $agenda->dokumentasi_path,
                'laporan_file_path' => $agenda->laporan_file_path,
                'is_public' => $agenda->is_public,
                'can_edit' => $canEdit,
                'is_rt_agenda' => ($agenda->penyelenggara === 'rt'),
            ]
        ]);
    }

    /**
     * Tambah Agenda RW Baru (Khusus Ketua RW / Admin)
     * Rute: POST /agenda-rw (agenda-rw.store)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Akses terbatas: Hanya Pengurus RW 013 yang dapat membuat agenda rapat tingkat RW.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string',
            'tanggal' => 'required|date',
            'waktu' => 'required|string|max:50',
            'lokasi' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:150',
            'peserta' => 'nullable|string|max:255',
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat',
            'deskripsi' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ]);

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'lokasi' => $request->lokasi,
            'kategori' => $request->kategori,
            'penyelenggara' => 'rw',
            'rt_number' => null,
            'penanggung_jawab' => $request->penanggung_jawab,
            'peserta' => $request->peserta ?: 'Pengurus RT 001 - RT 008 & Warga Terkait',
            'prioritas' => $request->prioritas,
            'status_kegiatan' => 'akan_datang',
            'status_laporan' => 'belum_ada_laporan',
            'is_public' => $request->boolean('is_public', false),
            'created_by' => $user->id,
        ];

        // Simpan ke database jika ada
        try {
            if (Schema::hasTable('agendas')) {
                Agenda::create($data);
            }
        } catch (\Throwable $e) {}

        // Sinkronkan ke JSON storage fallback
        $this->saveFallbackAgenda($data);

        return redirect()->route('agenda-rw.index', ['tab' => 'mendatang'])
            ->with('success', "Agenda RW '{$request->judul}' berhasil diterbitkan ke jadwal kegiatan.");
    }

    /**
     * Perbarui Status & Notulen Agenda Milik RW
     * Rute: POST /agenda-rw/{id}/status (agenda-rw.status)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();

        // Cari agenda
        $agenda = null;
        try {
            if (Schema::hasTable('agendas')) {
                $agenda = Agenda::find($id);
            }
        } catch (\Throwable $e) {}

        if (!$agenda) {
            $fallback = $this->getFallbackAgendas();
            $agenda = $fallback->firstWhere('id', (int)$id) ?: $fallback->firstWhere('id', (string)$id);
        }

        if (!$agenda) {
            return back()->with('error', 'Agenda tidak ditemukan.');
        }

        // PENEGAKAN PRINSIP HAK AKSES:
        // RW TIDAK BOLEH MENGUBAH AGENDA MILIK RT!
        if ($agenda->penyelenggara === 'rt' && !in_array($user->role, ['super_admin'])) {
            abort(403, 'Akses Ditolak: Pengurus RW hanya memiliki hak akses monitoring untuk agenda milik RT.');
        }

        $request->validate([
            'status_kegiatan' => 'required|in:akan_datang,sedang_berlangsung,selesai,dibatalkan,tidak_terlaksana',
            'ringkasan_hasil' => 'nullable|string',
        ]);

        $statusLaporan = $agenda->status_laporan;
        if (!empty($request->ringkasan_hasil) && $request->status_kegiatan === 'selesai') {
            $statusLaporan = 'laporan_lengkap';
        }

        try {
            if (Schema::hasTable('agendas') && $agenda instanceof Agenda && $agenda->exists) {
                $agenda->update([
                    'status_kegiatan' => $request->status_kegiatan,
                    'status_laporan' => $statusLaporan,
                    'ringkasan_hasil' => $request->ringkasan_hasil ?: $agenda->ringkasan_hasil,
                    'tanggal_laporan' => $request->status_kegiatan === 'selesai' ? Carbon::now() : $agenda->tanggal_laporan,
                ]);
            }
        } catch (\Throwable $e) {}

        // Update JSON storage fallback
        $this->updateFallbackAgenda($id, [
            'status_kegiatan' => $request->status_kegiatan,
            'status_laporan' => $statusLaporan,
            'ringkasan_hasil' => $request->ringkasan_hasil ?: $agenda->ringkasan_hasil,
        ]);

        return back()->with('success', "Status agenda '{$agenda->judul}' berhasil diperbarui menjadi [{$request->status_kegiatan}].");
    }

    /**
     * Hapus Agenda Milik RW
     * Rute: DELETE /agenda-rw/{id} (agenda-rw.destroy)
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $agenda = null;
        try {
            if (Schema::hasTable('agendas')) {
                $agenda = Agenda::find($id);
            }
        } catch (\Throwable $e) {}

        if (!$agenda) {
            $fallback = $this->getFallbackAgendas();
            $agenda = $fallback->firstWhere('id', (int)$id) ?: $fallback->firstWhere('id', (string)$id);
        }

        if (!$agenda) {
            return back()->with('error', 'Agenda tidak ditemukan.');
        }

        // PENEGAKAN PRINSIP HAK AKSES:
        // RW TIDAK BOLEH MENGHAPUS AGENDA MILIK RT!
        if ($agenda->penyelenggara === 'rt') {
            abort(403, 'Akses Ditolak: Pengurus RW tidak diizinkan menghapus agenda kegiatan milik RT.');
        }

        if (!in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            abort(403, 'Hanya Pengurus RW yang berwenang menghapus agenda ini.');
        }

        try {
            if (Schema::hasTable('agendas') && $agenda instanceof Agenda && $agenda->exists) {
                $agenda->delete();
            }
        } catch (\Throwable $e) {}

        $this->deleteFallbackAgenda($id);

        return back()->with('success', "Agenda '{$agenda->judul}' berhasil dihapus.");
    }

    // --- HELPER STORAGE SYNC ---

    protected function saveFallbackAgenda($data)
    {
        $filePath = storage_path('app/agenda_data.json');
        $items = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
        $data['id'] = count($items) + 1;
        $items[] = $data;
        file_put_contents($filePath, json_encode($items, JSON_PRETTY_PRINT));
    }

    protected function updateFallbackAgenda($id, $updates)
    {
        $filePath = storage_path('app/agenda_data.json');
        if (!file_exists($filePath)) return;
        $items = json_decode(file_get_contents($filePath), true) ?: [];
        foreach ($items as &$item) {
            if (($item['id'] ?? 0) == $id) {
                foreach ($updates as $k => $v) {
                    $item[$k] = $v;
                }
                break;
            }
        }
        file_put_contents($filePath, json_encode($items, JSON_PRETTY_PRINT));
    }

    protected function deleteFallbackAgenda($id)
    {
        $filePath = storage_path('app/agenda_data.json');
        if (!file_exists($filePath)) return;
        $items = json_decode(file_get_contents($filePath), true) ?: [];
        $items = array_values(array_filter($items, fn($it) => ($it['id'] ?? 0) != $id));
        file_put_contents($filePath, json_encode($items, JSON_PRETTY_PRINT));
    }
}
