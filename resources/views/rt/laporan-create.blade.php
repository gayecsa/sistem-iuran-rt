@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i data-lucide="file-plus" class="text-primary me-2" style="width: 26px; height: 26px;"></i>
                Formulir Laporan Bulanan {{ $rtLabel }} ke RW 013
            </h4>
            <p class="text-muted small mb-0">
                Kirim data perkembangan administrasi kependudukan, keuangan, dan kegiatan bulanan RT untuk diverifikasi oleh Pengurus RW 013.
            </p>
        </div>
        <div>
            <a href="{{ route('rt.laporan') }}" class="wm-btn-outline">
                <i data-lucide="arrow-left" style="width: 17px; height: 17px;"></i> Kembali ke Laporan
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i data-lucide="alert-circle" style="width: 18px; height: 18px;" class="me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('rt.laporan.store') }}">
        @csrf

        {{-- 1. IDENTITAS LAPORAN & PERIODE --}}
        <div class="wm-card mb-4" style="border-left: 5px solid var(--wm-primary);">
            <div class="wm-card-header">
                <h5 class="wm-card-title mb-0">
                    <i data-lucide="calendar" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                    1. Periode & Penanggung Jawab Laporan
                </h5>
            </div>
            <div class="wm-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Wilayah RT</label>
                        <input type="text" class="form-control bg-light" value="{{ $rtLabel }} - RW 013" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ketua RT / Pelapor</label>
                        <input type="text" class="form-control bg-light" value="{{ $user->name }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Periode Bulan & Tahun <span class="text-danger">*</span></label>
                        <input type="text" name="periode" class="form-control" value="{{ old('periode', $myReport['periode'] ?? 'November 2026') }}" required placeholder="Contoh: November 2026">
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. ADMINISTRASI & KEPENDUDUKAN --}}
        <div class="wm-card mb-4" style="border-left: 5px solid var(--wm-green);">
            <div class="wm-card-header">
                <h5 class="wm-card-title mb-0">
                    <i data-lucide="users" class="text-success me-2" style="width: 20px; height: 20px;"></i>
                    2. Data Administrasi Warga
                </h5>
            </div>
            <div class="wm-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Jumlah KK Terdata <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_kk" class="form-control" value="{{ old('jumlah_kk', $myReport['administrasi']['jumlah_kk'] ?? 45) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Total Warga (Jiwa) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_warga" class="form-control" value="{{ old('jumlah_warga', $myReport['administrasi']['jumlah_warga'] ?? 142) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Warga Baru (Datang)</label>
                        <input type="number" name="warga_baru" class="form-control" value="{{ old('warga_baru', $myReport['administrasi']['warga_baru'] ?? 0) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Mutasi Keluar</label>
                        <input type="number" name="mutasi_keluar" class="form-control" value="{{ old('mutasi_keluar', $myReport['administrasi']['mutasi_keluar'] ?? 0) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Surat Diterbitkan</label>
                        <input type="number" name="surat_dikeluarkan" class="form-control" value="{{ old('surat_dikeluarkan', $myReport['administrasi']['surat_dikeluarkan'] ?? 10) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan Kependudukan RT</label>
                        <textarea name="catatan_administrasi" class="form-control" rows="2" placeholder="Catatan berkas warga, mutasi, atau domisili...">{{ old('catatan_administrasi', $myReport['administrasi']['catatan'] ?? 'Seluruh data berkas KK dan administrasi surat terdata lengkap.') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. KEUANGAN & KAS RT --}}
        <div class="wm-card mb-4" style="border-left: 5px solid #D97706;">
            <div class="wm-card-header">
                <h5 class="wm-card-title mb-0">
                    <i data-lucide="wallet" class="text-warning me-2" style="width: 20px; height: 20px;"></i>
                    3. Laporan Keuangan & Kas RT
                </h5>
            </div>
            <div class="wm-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Saldo Awal (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="saldo_awal" id="saldo_awal" class="form-control" value="{{ old('saldo_awal', $myReport['keuangan']['saldo_awal'] ?? 12500000) }}" required oninput="calcSaldo()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pemasukan / Iuran (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="pemasukan" id="pemasukan" class="form-control text-success fw-bold" value="{{ old('pemasukan', $myReport['keuangan']['pemasukan'] ?? 3850000) }}" required oninput="calcSaldo()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pengeluaran Operasional (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="pengeluaran" id="pengeluaran" class="form-control text-danger fw-bold" value="{{ old('pengeluaran', $myReport['keuangan']['pengeluaran'] ?? 1500000) }}" required oninput="calcSaldo()">
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded bg-light border d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Estimasi Saldo Akhir Kas:</span>
                                <h5 class="mb-0 fw-bold text-primary" id="preview_saldo_akhir">Rp 14.850.000</h5>
                            </div>
                            <small class="text-muted">Dihitung otomatis: Saldo Awal + Pemasukan - Pengeluaran</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. KEGIATAN SOSIAL, KESEHATAN, & KEAMANAN --}}
        <div class="wm-card mb-4" style="border-left: 5px solid #2878D0;">
            <div class="wm-card-header">
                <h5 class="wm-card-title mb-0">
                    <i data-lucide="shield-check" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                    4. Sosial, Posyandu, & Siskamling
                </h5>
            </div>
            <div class="wm-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pelaksanaan Posyandu</label>
                        <select name="posyandu_terlaksana" class="form-select">
                            <option value="Ya" {{ (old('posyandu_terlaksana', $myReport['sosial']['posyandu_terlaksana'] ?? 'Ya') === 'Ya') ? 'selected' : '' }}>Terlaksana Sesuai Jadwal</option>
                            <option value="Tertunda" {{ (old('posyandu_terlaksana', $myReport['sosial']['posyandu_terlaksana'] ?? '') === 'Tertunda') ? 'selected' : '' }}>Tertunda</option>
                            <option value="Tidak Terlaksana" {{ (old('posyandu_terlaksana', $myReport['sosial']['posyandu_terlaksana'] ?? '') === 'Tidak Terlaksana') ? 'selected' : '' }}>Tidak Terlaksana</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Partisipasi Posyandu</label>
                        <input type="text" name="partisipasi_posyandu" class="form-control" value="{{ old('partisipasi_posyandu', $myReport['sosial']['partisipasi_posyandu'] ?? '28 Balita & 15 Lansia') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kegiatan Kerja Bakti</label>
                        <input type="text" name="kerja_bakti" class="form-control" value="{{ old('kerja_bakti', $myReport['sosial']['kerja_bakti'] ?? 'Terlaksana Minggu ke-1') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Jadwal Ronda Berjalan</label>
                        <select name="ronda_berjalan" class="form-select">
                            <option value="Ya (7 Hari)" {{ str_contains(old('ronda_berjalan', $myReport['keamanan']['jadwal_ronda_berjalan'] ?? 'Ya (7 Hari)'), 'Ya') ? 'selected' : '' }}>Ya, Aktif Setiap Malam (7 Hari)</option>
                            <option value="5 Hari" {{ str_contains(old('ronda_berjalan', $myReport['keamanan']['jadwal_ronda_berjalan'] ?? ''), '5') ? 'selected' : '' }}>Sebagian (5 Hari / Pekan)</option>
                            <option value="Perlu Ditingkatkan" {{ str_contains(old('ronda_berjalan', $myReport['keamanan']['jadwal_ronda_berjalan'] ?? ''), 'Perlu') ? 'selected' : '' }}>Perlu Ditingkatkan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Bank Sampah Terkumpul</label>
                        <input type="text" name="bank_sampah_terkumpul" class="form-control" value="{{ old('bank_sampah_terkumpul', $myReport['keamanan']['bank_sampah_terkumpul'] ?? '65 kg') }}" placeholder="Contoh: 65 kg">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kejadian Keamanan Menonjol</label>
                        <input type="text" name="kejadian_menonjol" class="form-control" value="{{ old('kejadian_menonjol', $myReport['keamanan']['kejadian_menonjol'] ?? 'Nihil / Kondusif') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Aspirasi & Usulan RT ke Pengurus RW 013</label>
                        <textarea name="usulan_rt" class="form-control" rows="2" placeholder="Tuliskan usulan sarana prasarana, bantuan PJU, drainase, atau koordinasi rapat RW...">{{ old('usulan_rt', $myReport['usulan_rt'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- BUTTON ACTIONS --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('rt.laporan') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="wm-btn-primary px-4">
                <i data-lucide="send" style="width: 17px; height: 17px;"></i> Serahkan Laporan ke RW 013
            </button>
        </div>

    </form>

</div>

<script>
    function calcSaldo() {
        const awal = parseFloat(document.getElementById('saldo_awal').value) || 0;
        const masuk = parseFloat(document.getElementById('pemasukan').value) || 0;
        const keluar = parseFloat(document.getElementById('pengeluaran').value) || 0;
        const akhir = awal + masuk - keluar;
        document.getElementById('preview_saldo_akhir').innerText = 'Rp ' + akhir.toLocaleString('id-ID');
    }
    document.addEventListener('DOMContentLoaded', calcSaldo);
</script>
@endsection
