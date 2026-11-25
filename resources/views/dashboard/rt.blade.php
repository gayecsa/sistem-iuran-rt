@extends('layouts.app')

@section('title', 'Dashboard RT')

@push('styles')
<style>
    .rt-kpi-card {
        border-radius: 16px !important;
        background: #FFFFFF !important;
        border: 1px solid #E2E8F0 !important;
        padding: 20px 22px !important;
        box-shadow: 0 2px 10px rgba(23, 43, 77, 0.05) !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }
    .rt-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(23, 43, 77, 0.08) !important;
    }
    .rt-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3.5px;
    }
    .rt-kpi-card.accent-primary::before { background: #1456A0; }
    .rt-kpi-card.accent-green::before { background: #16865D; }
    .rt-kpi-card.accent-amber::before { background: #D97706; }
    .rt-kpi-card.accent-blue::before { background: #2878D0; }

    /* Dark Mode Overrides for RT KPI Cards */
    [data-theme="dark"] .rt-kpi-card,
    body.dark-mode .rt-kpi-card {
        background: #131F35 !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
    }
    [data-theme="dark"] .rt-kpi-card .text-muted,
    body.dark-mode .rt-kpi-card .text-muted {
        color: #94A3B8 !important;
    }
    [data-theme="dark"] .rt-kpi-card [style*="border-top"],
    body.dark-mode .rt-kpi-card [style*="border-top"] {
        border-top-color: #24344F !important;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">

    {{-- ======================================================================
         BAGIAN ATAS: SAPAAN PERSONAL, IDENTITAS RT, STATUS & QUICK ACTIONS
         ====================================================================== --}}
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #FFFFFF 0%, #F4FBF7 100%); border-left: 5px solid var(--wm-green);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=16865D&color=FFFFFF' }}"
                     alt="Avatar" class="rounded-circle shadow-sm border" style="width: 58px; height: 58px; object-fit: cover;">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0 fw-bold text-dark">Halo, {{ $user->name }}</h4>
                        <span class="badge" style="background: var(--wm-green); color: #fff; font-size: 0.8rem; border-radius: 999px; padding: 6px 12px;">
                            <i data-lucide="shield" style="width:13px;height:13px;display:inline-block;vertical-align:middle;margin-right:2px;"></i>
                            Ketua {{ $rtLabel }} / RW 013
                        </span>
                        @php
                            $lapStatus = $myReport['status'] ?? 'Menunggu pemeriksaan';
                            $badgeColor = match($lapStatus) {
                                'Lengkap' => 'bg-success',
                                'Perlu perbaikan' => 'bg-warning text-dark',
                                'Menunggu pemeriksaan' => 'bg-info text-white',
                                default => 'bg-secondary text-white'
                            };
                        @endphp
                        <span class="badge {{ $badgeColor }}" style="border-radius: 999px; padding: 6px 12px; font-size:0.75rem;">
                            Status Laporan ke RW: {{ $lapStatus }}
                        </span>
                    </div>
                    <p class="text-muted mb-0 small mt-1">
                        <i data-lucide="map-pin" style="width: 14px; height: 14px; vertical-align: middle; color: var(--wm-green);"></i>
                        Pusat Pengelolaan Administrasi, Pelayanan Warga & Keuangan Lingkungan {{ $rtLabel }}
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('warga.index') }}" class="wm-btn-primary">
                    <i data-lucide="users" style="width: 17px; height: 17px;"></i> Warga {{ $rtLabel }}
                </a>
                <a href="{{ route('rt.laporan.create') }}" class="wm-btn-success">
                    <i data-lucide="send" style="width: 17px; height: 17px;"></i> Kirim Laporan ke RW
                </a>
                <a href="{{ route('kas-rt.index') }}" class="wm-btn-outline">
                    <i data-lucide="wallet" style="width: 17px; height: 17px;"></i> Kas RT
                </a>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         RINGKASAN KEUANGAN RT & LINGKUNGAN (100% DATA DINAMIS DATABASE)
         ====================================================================== --}}
    <div class="wm-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i data-lucide="wallet-cards" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Ringkasan Keuangan RT {{ $rtStr }} & Kas Lingkungan
                </h5>
                <p class="text-muted small mb-0">Pencatatan kas otonom milik RT {{ $rtStr }}. Kas Sampah & Ronda dipantau transparan dari pembukuan lingkungan.</p>
            </div>
            <a href="{{ route('keuangan.dashboard') }}" class="wm-btn-success" style="padding: 7px 16px; font-size: 0.85rem;">
                <i data-lucide="wallet" style="width: 16px; height: 16px;"></i> Kelola Keuangan RT &rarr;
            </a>
        </div>

        <!-- 5 Mini-Kartu Keuangan Terpisah -->
        <div class="row g-3">
            <!-- 1. Pemasukan RT Bulan Ini -->
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="p-3 rounded-4 border bg-light text-center h-100">
                    <span class="text-muted small fw-bold text-uppercase d-block" style="font-size: 0.72rem;">Pemasukan RT (Bulan Ini)</span>
                    <h5 class="fw-bold text-success mt-2 mb-1">Rp {{ number_format($pemasukanRtBulanIni ?? 0, 0, ',', '.') }}</h5>
                    <small class="text-muted" style="font-size: 0.7rem;">Iuran Warga RT {{ $rtStr }}</small>
                </div>
            </div>
            <!-- 2. Pengeluaran RT Bulan Ini -->
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="p-3 rounded-4 border bg-light text-center h-100">
                    <span class="text-muted small fw-bold text-uppercase d-block" style="font-size: 0.72rem;">Pengeluaran RT (Bulan Ini)</span>
                    <h5 class="fw-bold text-danger mt-2 mb-1">Rp {{ number_format($pengeluaranRtBulanIni ?? 0, 0, ',', '.') }}</h5>
                    <small class="text-muted" style="font-size: 0.7rem;">Operasional Lingkungan</small>
                </div>
            </div>
            <!-- 3. Saldo Kas RT -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="p-3 rounded-4 border h-100 text-center" style="background: #E3F5EC; border-color: rgba(22, 134, 93, 0.4) !important;">
                    <span class="text-success small fw-bold text-uppercase d-block" style="font-size: 0.72rem;">Saldo Kas RT {{ $rtStr }}</span>
                    <h4 class="fw-bold text-success mt-2 mb-1">Rp {{ number_format($totalSaldoOtonomRt ?? 0, 0, ',', '.') }}</h4>
                    <span class="badge bg-success text-white px-2 py-1" style="font-size: 0.7rem;">Kas Otonom RT</span>
                </div>
            </div>
            <!-- 4. Kas Sampah RW -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="p-3 rounded-4 border h-100 text-center" style="background: #EFF6FF; border-color: rgba(59, 130, 246, 0.3) !important;">
                    <span class="text-primary small fw-bold text-uppercase d-block" style="font-size: 0.72rem;">Kas Sampah RW</span>
                    <h4 class="fw-bold text-primary mt-2 mb-1">Rp {{ number_format($saldoKasSampah ?? 0, 0, ',', '.') }}</h4>
                    <span class="badge bg-primary text-white px-2 py-1" style="font-size: 0.7rem;">Rekening Bersama RW</span>
                </div>
            </div>
            <!-- 5. Kas Keamanan / Ronda -->
            <div class="col-lg-2 col-md-6 col-sm-12">
                <div class="p-3 rounded-4 border bg-light text-center h-100">
                    <span class="text-muted small fw-bold text-uppercase d-block" style="font-size: 0.72rem;">Kas Keamanan / Ronda</span>
                    <h5 class="fw-bold text-dark mt-2 mb-1">Rp {{ number_format($saldoKasKeamanan ?? 0, 0, ',', '.') }}</h5>
                    <span class="badge bg-secondary text-white px-2 py-0.5" style="font-size: 0.68rem;">Dana Pos Ronda</span>
                </div>
            </div>
        </div>

        <!-- Banner Total Saldo Lingkungan RT -->
        <div class="mt-3">
            <div class="p-2.5 px-3 rounded-3 border bg-white small text-muted d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="shield-check" class="text-success flex-shrink-0" style="width: 18px; height: 18px;"></i>
                    <span style="font-size: 0.8rem;">
                        <strong>Total Saldo Lingkungan RT:</strong> <strong class="text-dark">Rp {{ number_format($totalSaldoKeseluruhanRt ?? 0, 0, ',', '.') }}</strong> (Kas RT {{ $rtStr }} + Kas Sampah + Kas Keamanan)
                    </span>
                </div>
                <span class="badge bg-light text-success border border-success px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                    100% Sinkron dengan Pembukuan RT
                </span>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         ALERT / BANNER EVALUASI DARI KETUA RW 013
         ====================================================================== --}}
    @if(!empty($myReport['evaluasi_rw']))
        <div class="wm-card mb-4" style="background: #FFFBEB; border: 1px solid #FDE68A;">
            <div class="d-flex align-items-start gap-3">
                <div class="p-2 rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; flex-shrink: 0;">
                    <i data-lucide="message-square-quote" style="width: 22px; height: 22px;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h6 class="fw-bold mb-1 text-dark">
                            <i data-lucide="shield-check" style="width: 16px; height: 16px; color: var(--wm-primary);"></i>
                            Catatan Evaluasi dari Pengurus RW 013 untuk {{ $rtLabel }}
                        </h6>
                        <span class="small text-muted">{{ $myReport['updated_at'] ?? 'Bulan Berjalan' }}</span>
                    </div>
                    <p class="mb-2 text-secondary small" style="line-height: 1.5;">
                        "{{ $myReport['evaluasi_rw'] }}"
                    </p>
                    @if(!empty($myReport['rekomendasi_rw']))
                        <div class="p-2 rounded bg-white border small text-dark mb-2 rt-evaluasi-rekomendasi-box">
                            <strong class="rt-evaluasi-rekomendasi-label"><i data-lucide="arrow-right-circle" style="width:14px;height:14px;color:var(--wm-green);"></i> Rekomendasi RW:</strong>
                            <span class="rt-evaluasi-rekomendasi-text">{{ $myReport['rekomendasi_rw'] }}</span>
                        </div>
                    @endif
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <a href="{{ route('rt.evaluasi') }}" class="rt-btn-evaluasi-riwayat">
                            <i data-lucide="clipboard-list" style="width:14px;height:14px;"></i> Riwayat Evaluasi Lengkap
                        </a>
                        <a href="{{ route('rt.laporan.create') }}" class="btn btn-sm btn-primary" style="border-radius: 10px; padding: 7px 14px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease;">
                            <i data-lucide="file-edit" style="width:14px;height:14px;"></i> Tindak Lanjut / Perbarui Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ======================================================================
         STATISTIK UTAMA KHUSUS RT (4 KARTU METRIK SIMETRIS & PENUH MELEBAR)
         ====================================================================== --}}
    <div class="row g-3 mb-4">
        <!-- 1. Total Warga RT -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rt-kpi-card accent-primary">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.4px;">Warga {{ $rtLabel }}</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: #EAF3FF; color: #1456A0;">
                            <i data-lucide="users" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-primary mb-1" style="font-size: 1.65rem;">{{ number_format($totalWarga, 0, ',', '.') }}</h3>
                    <small class="text-muted d-block" style="font-size: 0.76rem;">Total Penduduk Terdaftar</small>
                </div>
                <div class="pt-2 mt-3 d-flex justify-content-between align-items-center" style="border-top: 1px solid #F1F5F9; font-size: 0.75rem;">
                    <span class="text-muted fw-semibold">
                        <span class="text-primary">{{ $totalLaki }} L</span> &bull; 
                        <span class="text-danger">{{ $totalPerempuan }} P</span>
                    </span>
                    <a href="{{ route('warga.index') }}" class="text-primary fw-bold text-decoration-none">Detail &rarr;</a>
                </div>
            </div>
        </div>

        <!-- 2. Kepala Keluarga (KK) RT -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rt-kpi-card accent-green">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.4px;">Kepala Keluarga</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: #E3F5EC; color: #16865D;">
                            <i data-lucide="home" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-success mb-1" style="font-size: 1.65rem;">{{ $totalKk }}</h3>
                    <small class="text-muted d-block" style="font-size: 0.76rem;">KK Terdaftar di RT {{ $rtStr }}</small>
                </div>
                <div class="pt-2 mt-3 d-flex justify-content-between align-items-center" style="border-top: 1px solid #F1F5F9; font-size: 0.75rem;">
                    <span class="text-muted fw-semibold">Kepala Keluarga Sah</span>
                    <a href="{{ route('warga.index', ['tab' => 'kk']) }}" class="text-success fw-bold text-decoration-none">Lihat KK &rarr;</a>
                </div>
            </div>
        </div>

        <!-- 3. Warga Aktif RT -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rt-kpi-card accent-amber">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.4px;">Warga Aktif</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: #FEF3C7; color: #D97706;">
                            <i data-lucide="user-check" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-warning mb-1" style="font-size: 1.65rem; color: #D97706 !important;">{{ $wargaAktif }}</h3>
                    <small class="text-muted d-block" style="font-size: 0.76rem;">Status Domisili Sah</small>
                </div>
                <div class="pt-2 mt-3 d-flex justify-content-between align-items-center" style="border-top: 1px solid #F1F5F9; font-size: 0.75rem;">
                    <span class="text-muted fw-semibold">Tinggal Menetap</span>
                    <a href="{{ route('warga.index', ['status' => 'aktif']) }}" class="fw-bold text-decoration-none" style="color: #D97706 !important;">Rincian &rarr;</a>
                </div>
            </div>
        </div>

        <!-- 4. Total Saldo Lingkungan RT -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="rt-kpi-card accent-blue">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.4px;">Total Saldo {{ $rtLabel }}</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: #EAF3FF; color: #2878D0;">
                            <i data-lucide="wallet" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1" style="font-size: 1.45rem; color: #2878D0 !important;">Rp {{ number_format($totalSaldoKeseluruhanRt, 0, ',', '.') }}</h3>
                    <small class="text-muted d-block" style="font-size: 0.76rem;">Kas RT + Sampah + Keamanan</small>
                </div>
                <div class="pt-2 mt-3 d-flex justify-content-between align-items-center" style="border-top: 1px solid #F1F5F9; font-size: 0.75rem;">
                    <span class="text-muted fw-semibold">Buku Kas RT {{ $rtStr }}</span>
                    <a href="{{ route('keuangan.dashboard') }}" class="fw-bold text-decoration-none" style="color: #2878D0 !important;">Kelola &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         KONTEN UTAMA: 2 KOLOM
         ====================================================================== --}}
    <div class="row g-4">
        
        <!-- KOLOM KIRI (PELAYANAN WARGA, SURAT, & PENGADUAN) -->
        <div class="col-lg-7">
            
            <!-- 1. Permohonan Surat Pengantar Warga RT -->
            <div class="wm-card mb-4">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="wm-card-title mb-0">
                            <i data-lucide="file-signature" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                            Permohonan Surat Pengantar Warga
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Daftar warga yang mengajukan surat pengantar domisili, KTP, SKCK, dan usaha.</p>
                    </div>
                    <a href="{{ route('surat.index') }}" class="wm-btn-outline" style="padding: 6px 12px; font-size: 0.8rem;">
                        Kelola Semua
                    </a>
                </div>
                <div class="wm-card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr style="font-size: 0.8rem; text-transform: uppercase;">
                                    <th>Nama Pemohon</th>
                                    <th>Jenis Surat</th>
                                    <th>Waktu Pengajuan</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permohonanSurat as $surat)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $surat['nama'] }}</div>
                                            <small class="text-muted">Warga {{ $rtLabel }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $surat['jenis'] }}</span>
                                        </td>
                                        <td class="small text-muted">{{ $surat['tanggal'] }}</td>
                                        <td>
                                            @if($surat['status'] === 'Selesai')
                                                <span class="badge bg-success">Selesai</span>
                                            @elseif($surat['status'] === 'Diproses')
                                                <span class="badge bg-warning text-dark">Diproses</span>
                                            @else
                                                <span class="badge bg-info text-white">Menunggu TTD</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('surat.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:0.75rem;">
                                                <i data-lucide="check-circle" style="width:13px;height:13px;"></i> Proses
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            Tidak ada permohonan surat yang sedang menunggu.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. Pengaduan Warga RT Sendiri -->
            <div class="wm-card mb-4">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="wm-card-title mb-0">
                            <i data-lucide="message-square-warning" class="text-danger me-2" style="width: 20px; height: 20px;"></i>
                            Pengaduan & Aspirasi Warga {{ $rtLabel }}
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Laporan masalah kebersihan, penerangan jalan, saluran air, dan keamanan di RT.</p>
                    </div>
                    <a href="{{ route('pengaduan.index') }}" class="wm-btn-outline" style="padding: 6px 12px; font-size: 0.8rem;">
                        Semua Aduan
                    </a>
                </div>
                <div class="wm-card-body">
                    @forelse($pengaduanRt as $aduan)
                        <div class="p-3 mb-3 rounded border" style="background: #FAFAFA;">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <span class="badge bg-light text-dark border me-1">{{ $aduan['kategori'] ?? 'Layanan' }}</span>
                                    <span class="fw-semibold text-dark">{{ $aduan['lokasi'] ?? $rtLabel }}</span>
                                </div>
                                <span class="badge {{ ($aduan['status'] ?? '') === 'Selesai' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $aduan['status'] ?? 'Menunggu' }}
                                </span>
                            </div>
                            <p class="small text-secondary mb-2">{{ $aduan['deskripsi'] ?? '-' }}</p>
                            <div class="d-flex justify-content-between align-items-center small text-muted pt-2 border-top">
                                <span><i data-lucide="user" style="width:12px;height:12px;"></i> {{ $aduan['pelapor'] ?? 'Warga' }}</span>
                                <span><i data-lucide="clock" style="width:12px;height:12px;"></i> {{ $aduan['created_at'] ?? 'Hari ini' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i data-lucide="check-circle-2" style="width: 32px; height: 32px; color: var(--wm-green); margin-bottom: 8px;"></i>
                            <p class="small mb-0">Lingkungan kondusif. Belum ada keluhan warga baru di wilayah {{ $rtLabel }}.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 3. Pintasan Manajemen Lingkungan RT -->
            <div class="row g-3">
                <div class="col-sm-4">
                    <a href="{{ route('rt.keluarga') }}" class="wm-card text-decoration-none text-dark p-3 d-flex align-items-center gap-3 h-100 hover-shadow transition">
                        <div class="p-2 rounded bg-primary text-white">
                            <i data-lucide="home" style="width:20px;height:20px;"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">Data Keluarga (KK)</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $totalKk }} KK Terdaftar</div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-4">
                    <a href="{{ route('rt.pendatang') }}" class="wm-card text-decoration-none text-dark p-3 d-flex align-items-center gap-3 h-100 hover-shadow transition">
                        <div class="p-2 rounded bg-success text-white">
                            <i data-lucide="user-check" style="width:20px;height:20px;"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">Data Pendatang</div>
                            <div class="text-muted" style="font-size:0.75rem;">Sewa / Kontrak</div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-4">
                    <a href="{{ route('rt.mutasi') }}" class="wm-card text-decoration-none text-dark p-3 d-flex align-items-center gap-3 h-100 hover-shadow transition">
                        <div class="p-2 rounded bg-warning text-dark">
                            <i data-lucide="arrow-left-right" style="width:20px;height:20px;"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">Mutasi Warga</div>
                            <div class="text-muted" style="font-size:0.75rem;">Pindah & Lahir</div>
                        </div>
                    </a>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN (KEGIATAN RT, STATUS LAPORAN, PENGUMUMAN RW) -->
        <div class="col-lg-5">
            
            <!-- 1. Status Laporan Bulanan ke RW -->
            <div class="wm-card mb-4" style="border-top: 4px solid var(--wm-primary);">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="send" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                        Laporan Bulanan ke RW 013
                    </h5>
                    <span class="badge {{ $badgeColor }}">{{ $lapStatus }}</span>
                </div>
                <div class="wm-card-body">
                    <div class="p-3 rounded mb-3 rt-laporan-summary-box" style="background: #F4F7FB;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted rt-laporan-summary-label">Periode Laporan:</span>
                            <span class="fw-bold text-dark rt-laporan-summary-val">{{ $myReport['periode'] ?? 'November 2026' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted rt-laporan-summary-label">Capaian Iuran RT:</span>
                            <span class="fw-bold text-success rt-laporan-summary-val-success">{{ $myReport['keuangan']['iuran_terkumpul_persen'] ?? 90 }}%</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted rt-laporan-summary-label">Terakhir Diperbarui:</span>
                            <span class="small text-dark rt-laporan-summary-val">{{ $myReport['updated_at'] ?? 'Hari ini' }}</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('rt.laporan') }}" class="wm-btn-primary text-center">
                            <i data-lucide="eye" style="width: 16px; height: 16px;"></i> Lihat Rincian Laporan
                        </a>
                        <a href="{{ route('rt.laporan.create') }}" class="wm-btn-outline text-center">
                            <i data-lucide="file-plus" style="width: 16px; height: 16px;"></i> Buat / Perbarui Laporan RT
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Jadwal Kegiatan RT & Ronda -->
            <div class="wm-card mb-4">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="calendar" class="text-success me-2" style="width: 20px; height: 20px;"></i>
                        Agenda & Kegiatan {{ $rtLabel }}
                    </h5>
                    <a href="{{ route('ronda.index') }}" class="wm-btn-outline" style="padding: 5px 10px; font-size: 0.75rem;">
                        Pos Ronda
                    </a>
                </div>
                <div class="wm-card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($kegiatanRt as $keg)
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="fw-semibold text-dark">{{ $keg['nama'] }}</div>
                                    <span class="badge bg-light text-dark border">{{ $keg['status'] }}</span>
                                </div>
                                <div class="small text-muted mb-1">
                                    <i data-lucide="clock" style="width:12px;height:12px;"></i> {{ $keg['waktu'] }}
                                </div>
                                <div class="small text-muted">
                                    <i data-lucide="map-pin" style="width:12px;height:12px;color:var(--wm-green);"></i> {{ $keg['lokasi'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 3. Pengumuman Resmi dari RW 013 -->
            <div class="wm-card mb-4">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="megaphone" class="text-warning me-2" style="width: 20px; height: 20px;"></i>
                        Pengumuman RW 013
                    </h5>
                    <a href="{{ route('pengumuman.index') }}" class="wm-btn-outline" style="padding: 5px 10px; font-size: 0.75rem;">
                        Semua
                    </a>
                </div>
                <div class="wm-card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pengumuman as $p)
                            <a href="{{ route('pengumuman.show', $p->id) }}" class="list-group-item list-group-item-action p-3">
                                <div class="fw-semibold text-dark small mb-1">{{ $p->judul }}</div>
                                <div class="small text-muted d-flex justify-content-between">
                                    <span><i data-lucide="calendar" style="width:12px;height:12px;"></i> {{ Carbon\Carbon::parse($p->tanggal_aktif)->translatedFormat('d M Y') }}</span>
                                    <span class="badge bg-light text-primary border">Resmi RW</span>
                                </div>
                            </a>
                        @empty
                            <div class="p-3 text-center text-muted small">
                                Belum ada pengumuman terbaru dari RW 013.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
