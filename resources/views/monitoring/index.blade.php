@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Halaman Monitoring RT Terpadu RW 013 -->
    <div class="wm-card mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #123766 0%, #164070 100%); color: #fff; border: none; border-radius: 16px;">
        <div class="p-4 p-md-5 position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div style="max-width: 780px;">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-light text-primary fw-bold px-3 py-1 rounded-pill" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;"></i> PUSAT PEMANTAUAN TERPADU
                        </span>
                        <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill opacity-90" style="font-size: 11px;">
                            RW 013 Suryatmajan Danurejan
                        </span>
                        <span class="badge bg-emerald-500 text-white fw-bold px-3 py-1 rounded-pill" style="background: #10B981 !important; font-size: 11px;">
                            RT 001 s/d RT 008
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Monitoring Terpadu Rukun Tetangga (RT 001–008)</h2>
                    <p class="mb-0 text-white text-opacity-75 lead fs-6">
                        Pusat pemantauan tunggal untuk operasional, verifikasi laporan bulanan, transparansi kas lingkungan, dan evaluasi kinerja 8 RT. Ketua RW bertindak sebagai pengawas dan koordinator.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('dashboard.rw') }}" class="btn btn-light rounded-pill px-3 py-2 fw-bold small text-primary shadow-sm">
                        <i data-lucide="layout-dashboard" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Ringkasan RW
                    </a>
                    <a href="{{ route('rw.audit.index') }}" class="btn btn-outline-light rounded-pill px-3 py-2 fw-bold small shadow-sm">
                        <i data-lucide="clipboard-check" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Audit & Evaluasi RW
                    </a>
                </div>
            </div>
        </div>
        <div class="position-absolute rounded-circle" style="width: 320px; height: 320px; background: rgba(255,255,255,0.04); top: -80px; right: -60px; pointer-events: none;"></div>
    </div>

    <!-- Alert Notifikasi Session Flash -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i data-lucide="check" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="text-success d-block">Berhasil!</strong>
                <span class="small text-dark">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- A. RINGKASAN MONITORING (5 KARTU KPI CEPAT) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Total Wilayah</span>
                <h3 class="fw-bold mb-0 text-dark">{{ $totalRt }} <span class="fs-6 fw-normal text-muted">RT</span></h3>
                <small class="text-muted" style="font-size: 11px;">RT 001 - RT 008</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Laporan Lengkap</span>
                <h3 class="fw-bold mb-0 text-success">{{ $rtLengkapCount }} <span class="fs-6 fw-normal text-muted">RT</span></h3>
                <small class="text-success" style="font-size: 11px;"><i data-lucide="check" style="width: 12px; height: 12px; vertical-align: middle;"></i> Terverifikasi</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Perlu Diperiksa</span>
                <h3 class="fw-bold mb-0 text-warning-emphasis">{{ $rtPerluPeriksaCount }} <span class="fs-6 fw-normal text-muted">RT</span></h3>
                <small class="text-warning-emphasis" style="font-size: 11px;">Menunggu Telaah</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Perlu Tindak Lanjut</span>
                <h3 class="fw-bold mb-0 text-danger">{{ $rtTindakLanjutCount }} <span class="fs-6 fw-normal text-muted">RT</span></h3>
                <small class="text-danger" style="font-size: 11px;">Ada Catatan Khusus</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Belum Lapor</span>
                <h3 class="fw-bold mb-0 text-secondary">{{ $rtBelumLaporCount }} <span class="fs-6 fw-normal text-muted">RT</span></h3>
                <small class="text-muted" style="font-size: 11px;">Menunggu Kiriman</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Total Saldo 8 RT</span>
                <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($totalSaldo8Rt / 1000000, 1, ',', '.') }}<span class="fs-6 fw-normal text-muted">jt</span></h4>
                <small class="text-muted" style="font-size: 11px;">Kas Terkelola</small>
            </div>
        </div>
    </div>

    <!-- Navigasi Subfitur / Tab Monitoring RT Terpadu -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-2">
        <ul class="nav nav-pills nav-fill gap-2" role="tablist">
            <li class="nav-item">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'overview' ? 'active' : '' }}" href="{{ route('monitoring-rt.index', ['tab' => 'overview', 'rt' => $filterRt, 'periode' => $filterPeriode]) }}">
                    <i data-lucide="layout-grid" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Ikhtisar & Daftar RT
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'laporan' ? 'active' : '' }}" href="{{ route('monitoring-rt.index', ['tab' => 'laporan', 'rt' => $filterRt, 'periode' => $filterPeriode]) }}">
                    <i data-lucide="file-text" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Laporan Bulanan RT
                    @if($rtPerluPeriksaCount > 0)
                        <span class="badge rounded-pill bg-warning text-dark ms-1">{{ $rtPerluPeriksaCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'keuangan' ? 'active' : '' }}" href="{{ route('monitoring-rt.index', ['tab' => 'keuangan', 'rt' => $filterRt, 'periode' => $filterPeriode]) }}">
                    <i data-lucide="wallet" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Monitoring Keuangan RT
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'evaluasi' ? 'active' : '' }}" href="{{ route('monitoring-rt.index', ['tab' => 'evaluasi', 'rt' => $filterRt, 'periode' => $filterPeriode]) }}">
                    <i data-lucide="award" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Evaluasi Kinerja RT
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'riwayat' ? 'active' : '' }}" href="{{ route('monitoring-rt.index', ['tab' => 'riwayat', 'rt' => $filterRt, 'periode' => $filterPeriode]) }}">
                    <i data-lucide="history" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Riwayat & Tindak Lanjut
                </a>
            </li>
        </ul>
    </div>

    <!-- Filter Global: Wilayah RT & Periode -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-3">
        <form method="GET" action="{{ route('monitoring-rt.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-bold text-muted mb-1">Filter Wilayah RT</label>
                <select name="rt" class="form-select form-select-sm rounded-3">
                    <option value="all" {{ $filterRt == 'all' ? 'selected' : '' }}>Seluruh Wilayah (RT 001 - RT 008)</option>
                    @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ $filterRt == $i ? 'selected' : '' }}>RT 00{{ $i }} - RW 013</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small fw-bold text-muted mb-1">Periode Laporan</label>
                <select name="periode" class="form-select form-select-sm rounded-3">
                    <option value="November 2026" {{ $filterPeriode == 'November 2026' ? 'selected' : '' }}>November 2026 (Bulan Berjalan)</option>
                    <option value="Oktober 2026" {{ $filterPeriode == 'Oktober 2026' ? 'selected' : '' }}>Oktober 2026</option>
                    <option value="September 2026" {{ $filterPeriode == 'September 2026' ? 'selected' : '' }}>September 2026</option>
                    <option value="Triwulan 3 2026" {{ $filterPeriode == 'Triwulan 3 2026' ? 'selected' : '' }}>Triwulan 3 (Juli - September 2026)</option>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex align-items-end gap-2 pt-md-3">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                    <i data-lucide="filter" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Terapkan
                </button>
                <a href="{{ route('monitoring-rt.index', ['tab' => $activeTab]) }}" class="btn btn-sm btn-light border rounded-pill px-3 text-muted">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: IKHTISAR & DAFTAR MONITORING RT 001–008                           -->
    <!-- ========================================================================= -->
    @if($activeTab == 'overview')
        <div class="row g-4 mb-4">
            @foreach($rtMonitoringList ?? [] as $rt)
                <div class="col-xl-6 col-12">
                    <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <!-- Header Kartu RT -->
                            <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="badge bg-primary text-white p-3 rounded-4 fw-bold fs-4 shadow-sm" style="width: 52px; height: 52px; display: inline-flex; align-items: center; justify-content: center;">
                                        {{ $rt['nomor'] }}
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0">{{ $rt['nama'] }} RW 013</h5>
                                        <small class="text-muted">Ketua RT: <strong class="text-dark">{{ $rt['ketua_rt'] }}</strong> ({{ $rt['kontak'] }})</small>
                                    </div>
                                </div>
                                <div>
                                    @if($rt['status_laporan'] === 'Lengkap')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small fw-bold">
                                            <i data-lucide="check-circle" style="width: 13px; height: 13px; vertical-align: middle;"></i> Lengkap
                                        </span>
                                    @elseif($rt['status_laporan'] === 'Perlu diperiksa')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill small fw-bold">
                                            <i data-lucide="alert-circle" style="width: 13px; height: 13px; vertical-align: middle;"></i> Perlu Diperiksa
                                        </span>
                                    @elseif($rt['status_laporan'] === 'Belum dilaporkan')
                                        <span class="badge bg-secondary-subtle text-secondary border px-3 py-2 rounded-pill small fw-semibold">
                                            <i data-lucide="clock" style="width: 13px; height: 13px; vertical-align: middle;"></i> Belum Lapor
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill small fw-bold">
                                            <i data-lucide="alert-triangle" style="width: 13px; height: 13px; vertical-align: middle;"></i> Perlu Tindak Lanjut
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- 4 Pilar Status Operasional -->
                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <div class="p-2 rounded-3 bg-light border small">
                                        <span class="text-muted d-block fw-bold" style="font-size: 11px;">1. ADMINISTRASI KEPENDUDUKAN</span>
                                        <strong class="text-dark">{{ $rt['jumlah_kk'] }} KK &bull; {{ $rt['jumlah_warga'] }} Jiwa</strong>
                                        <small class="text-muted d-block" style="font-size: 11px;">{{ $rt['administrasi'] }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-2 rounded-3 bg-light border small">
                                        <span class="text-muted d-block fw-bold" style="font-size: 11px;">2. KEUANGAN & KAS RT</span>
                                        <strong class="text-success">Rp {{ number_format($rt['saldo_kas_real'] ?? 0, 0, ',', '.') }}</strong>
                                        <small class="text-muted d-block" style="font-size: 11px;">Tx terakhir: {{ $rt['tx_terakhir'] ?? '-' }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-2 rounded-3 bg-light border small">
                                        <span class="text-muted d-block fw-bold" style="font-size: 11px;">3. KEGIATAN SOSIAL MASYARAKAT</span>
                                        <strong class="text-dark">{{ $rt['sosial'] }}</strong>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-2 rounded-3 bg-light border small">
                                        <span class="text-muted d-block fw-bold" style="font-size: 11px;">4. KEBERSIHAN & KEAMANAN</span>
                                        <strong class="text-dark">{{ $rt['kebersihan_keamanan'] }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan Evaluasi & Rekomendasi Terakhir -->
                            <div class="p-3 rounded-3 bg-light border mb-3">
                                <div class="mb-2">
                                    <strong class="text-dark small d-block mb-1">
                                        <i data-lucide="clipboard-edit" class="text-primary me-1" style="width: 14px; height: 14px; vertical-align: middle;"></i> Catatan Evaluasi RW:
                                    </strong>
                                    <p class="text-muted small mb-0">{{ $rt['evaluasi_rw'] }}</p>
                                </div>
                                <div class="pt-2 border-top">
                                    <strong class="text-dark small d-block mb-1">
                                        <i data-lucide="lightbulb" class="text-warning me-1" style="width: 14px; height: 14px; vertical-align: middle;"></i> Rekomendasi Perbaikan:
                                    </strong>
                                    <p class="text-secondary small mb-0">{{ $rt['rekomendasi'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Aksi: Buka Evaluasi Langsung & Shortcut Subfitur -->
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top flex-wrap gap-2">
                            <span class="small text-muted" style="font-size: 11px;">
                                <i data-lucide="calendar" style="width: 12px; height: 12px; vertical-align: middle;" class="me-1"></i> Update: {{ $rt['terakhir_update'] }}
                            </span>
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('monitoring-rt.index', ['tab' => 'laporan', 'rt' => $rt['rt']]) }}" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-muted" style="font-size: 11px;" title="Lihat Laporan Bulanan">
                                    <i data-lucide="file-text" style="width: 12px; height: 12px; vertical-align: middle;"></i> Laporan
                                </a>
                                <a href="{{ route('monitoring-rt.index', ['tab' => 'keuangan', 'rt' => $rt['rt']]) }}" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-muted" style="font-size: 11px;" title="Lihat Kas RT">
                                    <i data-lucide="wallet" style="width: 12px; height: 12px; vertical-align: middle;"></i> Kas
                                </a>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold small" data-bs-toggle="modal" data-bs-target="#modalEvaluasiRT{{ $rt['rt'] }}">
                                    <i data-lucide="edit-3" style="width: 13px; height: 13px; vertical-align: middle;" class="me-1"></i> Evaluasi RT
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL INTERAKTIF EVALUASI RT (DIJAMIN MEMBUKA LANGSUNG UNTUK RT INI) -->
                <div class="modal fade" id="modalEvaluasiRT{{ $rt['rt'] }}" tabindex="-1" aria-labelledby="modalEvaluasiRTLabel{{ $rt['rt'] }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <form action="{{ route('monitoring-rt.evaluasi', $rt['rt']) }}" method="POST">
                                @csrf
                                <input type="hidden" name="tab" value="{{ $activeTab }}">
                                <input type="hidden" name="filter_rt" value="{{ $filterRt }}">
                                <div class="modal-header border-bottom">
                                    <div>
                                        <h5 class="modal-title fw-bold text-dark" id="modalEvaluasiRTLabel{{ $rt['rt'] }}">
                                            Evaluasi & Pembinaan {{ $rt['nama'] }} RW 013
                                        </h5>
                                        <small class="text-muted">Ketua RT: {{ $rt['ketua_rt'] }} &bull; Periode: {{ $filterPeriode }}</small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark mb-1">Status Pemeriksaan Laporan</label>
                                            <select name="status_laporan" class="form-select rounded-3" required>
                                                <option value="Lengkap" {{ $rt['status_laporan'] === 'Lengkap' ? 'selected' : '' }}>Lengkap (Disetujui Resmi)</option>
                                                <option value="Perlu diperiksa" {{ $rt['status_laporan'] === 'Perlu diperiksa' ? 'selected' : '' }}>Perlu diperiksa (Menunggu Telaah)</option>
                                                <option value="Perlu tindak lanjut" {{ $rt['status_laporan'] === 'Perlu tindak lanjut' ? 'selected' : '' }}>Perlu tindak lanjut (Ada Catatan/Klarifikasi)</option>
                                                <option value="Belum dilaporkan" {{ $rt['status_laporan'] === 'Belum dilaporkan' ? 'selected' : '' }}>Belum dilaporkan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark mb-1">Skor Kepatuhan RT (0 - 100)</label>
                                            <input type="number" name="skor" class="form-control rounded-3" min="0" max="100" value="{{ $rt['skor_kinerja'] ?? 85 }}" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark mb-1">Catatan Evaluasi Ketua RW</label>
                                        <textarea name="evaluasi_rw" rows="3" class="form-control rounded-3" placeholder="Masukkan evaluasi kedisiplinan, administrasi, dan sosial..." required>{{ $rt['evaluasi_rw'] }}</textarea>
                                        <small class="text-muted">Catatan ini akan tersimpan pada arsip evaluasi resmi dan riwayat tindak lanjut RT.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark mb-1">Rekomendasi Perbaikan & Arahan RW</label>
                                        <textarea name="rekomendasi" rows="3" class="form-control rounded-3" placeholder="Langkah konkrit yang wajib ditindaklanjuti Ketua RT..." required>{{ $rt['rekomendasi'] }}</textarea>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-dark mb-1">Tenggat Waktu Tindak Lanjut (Opsional)</label>
                                        <input type="date" name="tenggat_perbaikan" class="form-control rounded-3">
                                    </div>
                                </div>
                                <div class="modal-footer border-top pt-2">
                                    <button type="button" class="btn btn-secondary rounded-pill btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">
                                        <i data-lucide="save" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Simpan Evaluasi RT
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 2: LAPORAN BULANAN RT (INTEGRASI PENUH ARSIP LAPORAN MASUK)           -->
    <!-- ========================================================================= -->
    @if($activeTab == 'laporan')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i data-lucide="file-check" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Laporan Pertanggungjawaban Bulanan RT 001 - RT 008
                    </h5>
                    <p class="mb-0 text-muted small">
                        Pemeriksaan berkas laporan bulanan mencakup administrasi warga, keuangan kas, kegiatan sosial, dan keamanan wilayah.
                    </p>
                </div>
                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill small">
                    Periode: {{ $filterPeriode }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold text-uppercase">Wilayah RT</th>
                            <th class="text-secondary small fw-bold text-uppercase">Ketua RT</th>
                            <th class="text-secondary small fw-bold text-uppercase">Status Pemeriksaan</th>
                            <th class="text-secondary small fw-bold text-uppercase">Kelengkapan Administrasi</th>
                            <th class="text-secondary small fw-bold text-uppercase">Kas Dilaporkan</th>
                            <th class="text-secondary small fw-bold text-uppercase">Terakhir Diperbarui</th>
                            <th class="text-end pe-3 text-secondary small fw-bold text-uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanList as $lap)
                            <tr>
                                <td class="ps-3">
                                    <strong class="text-dark d-block">{{ $lap['rt_label'] ?? ('RT ' . str_pad($lap['rt_number'], 3, '0', STR_PAD_LEFT)) }}</strong>
                                    <small class="text-muted">RW 013</small>
                                </td>
                                <td>
                                    <span class="text-dark fw-semibold small d-block">{{ $lap['ketua_rt'] }}</span>
                                    <small class="text-muted">{{ $lap['kontak'] }}</small>
                                </td>
                                <td>
                                    @if(($lap['status'] ?? '') === 'Lengkap')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small fw-bold">
                                            Lengkap (Disetujui)
                                        </span>
                                    @elseif(($lap['status'] ?? '') === 'Perlu diperiksa')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill small fw-bold">
                                            Perlu Diperiksa
                                        </span>
                                    @elseif(($lap['status'] ?? '') === 'Belum dilaporkan')
                                        <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 rounded-pill small">
                                            Belum Dilaporkan
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill small fw-bold">
                                            Perlu Perbaikan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-dark fw-semibold d-block">
                                        {{ is_array($lap['administrasi']) ? ($lap['administrasi']['jumlah_kk'] . ' KK, ' . $lap['administrasi']['jumlah_warga'] . ' Warga') : $lap['administrasi'] }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="small text-success">
                                        {{ is_array($lap['keuangan']) ? ('Rp ' . number_format($lap['keuangan']['saldo_akhir'] ?? 0, 0, ',', '.')) : $lap['keuangan'] }}
                                    </strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $lap['updated_at'] ?? 'Baru saja' }}</small>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 small fw-semibold" data-bs-toggle="modal" data-bs-target="#modalDetailLaporan{{ $lap['id'] }}">
                                        Buka Berkas
                                    </button>

                                    <!-- MODAL DETAIL BERKAS LAPORAN BULANAN -->
                                    <div class="modal fade text-start" id="modalDetailLaporan{{ $lap['id'] }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <div class="modal-header border-bottom">
                                                    <div>
                                                        <h6 class="modal-title fw-bold text-dark">
                                                            Berkas Laporan Bulanan {{ $lap['rt_label'] }} (Periode {{ $lap['periode'] }})
                                                        </h6>
                                                        <small class="text-muted">Ketua RT: {{ $lap['ketua_rt'] }}</small>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3 border">
                                                                <h6 class="fw-bold text-dark mb-1">1. Rekapitulasi Warga</h6>
                                                                <span class="small text-muted d-block">
                                                                    {{ is_array($lap['administrasi']) ? ($lap['administrasi']['catatan'] ?? 'Data lengkap') : $lap['administrasi'] }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3 border">
                                                                <h6 class="fw-bold text-dark mb-1">2. Kas Lingkungan</h6>
                                                                <span class="small text-muted d-block">
                                                                    {{ is_array($lap['keuangan']) ? ('Saldo Akhir: Rp ' . number_format($lap['keuangan']['saldo_akhir'] ?? 0, 0, ',', '.') . ' (Kolektibilitas: ' . ($lap['keuangan']['iuran_terkumpul_persen'] ?? 0) . '%)') : $lap['keuangan'] }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="small fw-bold text-dark mb-1 d-block">Usulan / Pengajuan dari RT ke RW:</label>
                                                        <p class="p-3 bg-light-subtle border rounded-3 small text-dark mb-0">{{ $lap['usulan_rt'] ?? 'Nihil / Tidak ada usulan khusus.' }}</p>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="small fw-bold text-dark mb-1 d-block">Catatan Evaluasi RW:</label>
                                                        <p class="p-3 bg-light border rounded-3 small text-dark mb-0">{{ $lap['evaluasi_rw'] ?? 'Belum ada catatan evaluasi.' }}</p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top pt-2">
                                                    <button type="button" class="btn btn-secondary rounded-pill btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="button" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalEvaluasiRT{{ $lap['rt_number'] }}">
                                                        Beri Catatan / Evaluasi RT
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada berkas laporan bulanan yang sesuai filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 3: MONITORING KEUANGAN RT (INTEGRASI PEMANTAUAN KAS 8 RT)             -->
    <!-- ========================================================================= -->
    @if($activeTab == 'keuangan')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i data-lucide="wallet" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Monitoring Kas 8 RT (Murni Read-Only Pengawas)
                    </h5>
                    <p class="mb-0 text-muted small">
                        Pemantauan posisi saldo, pemasukan bulanan, dan kepatuhan entri kas RT 001–008. Ketua RW tidak berwenang menambah/mengubah saldo kas RT.
                    </p>
                </div>
                <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-2 rounded-pill small fw-bold" style="background: #E8F5E9; color: #16865D;">
                    <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Hak Akses Pengawas
                </span>
            </div>

            <!-- Tabel Kas 8 RT -->
            <div class="table-responsive mb-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold">Wilayah RT</th>
                            <th class="text-secondary small fw-bold">Nama Akun Buku Kas</th>
                            <th class="text-secondary small fw-bold">Saldo Terkini</th>
                            <th class="text-secondary small fw-bold">Pemasukan Bulan Ini</th>
                            <th class="text-secondary small fw-bold">Pengeluaran Bulan Ini</th>
                            <th class="text-secondary small fw-bold">Kondisi Keuangan</th>
                            <th class="text-end pe-3 text-secondary small fw-bold">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kasRtList as $kas)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: #E8F5E9; color: #16865D; border: 1px solid #C8E6C9;">
                                        RT 00{{ $kas->rt_number }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark d-block small">{{ $kas->nama_kas }}</strong>
                                    <small class="text-muted">Rek: {{ $kas->nomor_rekening }}</small>
                                </td>
                                <td>
                                    <strong class="fs-6 text-dark">Rp {{ number_format($kas->saldo_sekarang, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <span class="text-success small fw-semibold">
                                        + Rp {{ number_format($kas->transaksis()->where('status', 'selesai')->whereIn('jenis_transaksi', ['pemasukan', 'transfer_masuk'])->whereMonth('tanggal_transaksi', Carbon\Carbon::now()->month)->sum('nominal'), 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-danger small fw-semibold">
                                        - Rp {{ number_format($kas->transaksis()->where('status', 'selesai')->whereIn('jenis_transaksi', ['pengeluaran', 'transfer_keluar'])->whereMonth('tanggal_transaksi', Carbon\Carbon::now()->month)->sum('nominal'), 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @if($kas->saldo_sekarang >= 2000000)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small fw-bold">Aman & Tertib</span>
                                    @elseif($kas->saldo_sekarang >= 500000)
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill small fw-bold">Waspada</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill small fw-bold">Perlu Perhatian</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 small fw-semibold" data-bs-toggle="modal" data-bs-target="#modalKlarifikasiKas{{ $kas->rt_number }}">
                                        Minta Klarifikasi
                                    </button>

                                    <!-- MODAL MINTA KLARIFIKASI KAS RT -->
                                    <div class="modal fade text-start" id="modalKlarifikasiKas{{ $kas->rt_number }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <form action="{{ route('monitoring-rt.evaluasi', $kas->rt_number) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="tab" value="keuangan">
                                                    <input type="hidden" name="status_laporan" value="Perlu tindak lanjut">
                                                    <div class="modal-header border-bottom">
                                                        <h6 class="modal-title fw-bold text-dark">Minta Klarifikasi Kas RT 00{{ $kas->rt_number }}</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-3">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark mb-1">Uraian Masalah / Hal yang Perlu Diklarifikasi</label>
                                                            <textarea name="evaluasi_rw" class="form-control rounded-3" rows="3" placeholder="Contoh: Ditemukan selisih pencatatan atau kuitansi pengeluaran yang belum diunggah..." required></textarea>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label small fw-bold text-dark mb-1">Instruksi Tindak Lanjut untuk Ketua RT</label>
                                                            <textarea name="rekomendasi" class="form-control rounded-3" rows="2" placeholder="Mohon kirimkan salinan kuitansi fisik..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top pt-2">
                                                        <button type="button" class="btn btn-secondary rounded-pill btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning rounded-pill btn-sm px-4 fw-bold">Kirim Catatan Klarifikasi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Rekap Mutasi Transaksi Terbaru Kas RT -->
            <h6 class="fw-bold text-dark mb-2 border-bottom pb-2">
                <i data-lucide="history" class="me-1 text-primary" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                Log Transaksi Kas RT Terkini
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-2 small text-secondary fw-bold">Tanggal & No. TX</th>
                            <th class="small text-secondary fw-bold">Akun Kas RT</th>
                            <th class="small text-secondary fw-bold">Jenis / Kategori</th>
                            <th class="small text-secondary fw-bold">Pihak / Uraian</th>
                            <th class="small text-secondary fw-bold text-end pe-2">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $rtx)
                            <tr>
                                <td class="ps-2">
                                    <strong class="text-dark small d-block">{{ $rtx->nomor_transaksi }}</strong>
                                    <small class="text-muted">{{ $rtx->tanggal_transaksi->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border small">{{ $rtx->bukuKas->nama_kas ?? '-' }}</span>
                                </td>
                                <td>
                                    @if(in_array($rtx->jenis_transaksi, ['pemasukan', 'transfer_masuk']))
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0 small">Masuk</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0 small">Keluar</span>
                                    @endif
                                    <small class="text-muted d-block mt-1">{{ $rtx->kategori }}</small>
                                </td>
                                <td>
                                    <span class="small text-dark d-block">{{ $rtx->penyetor_penerima ?? '-' }}</span>
                                    <small class="text-muted">{{ Str::limit($rtx->keterangan, 35) }}</small>
                                </td>
                                <td class="text-end pe-2">
                                    @if(in_array($rtx->jenis_transaksi, ['pemasukan', 'transfer_masuk']))
                                        <strong class="text-success small">+ Rp {{ number_format($rtx->nominal, 0, ',', '.') }}</strong>
                                    @else
                                        <strong class="text-danger small">- Rp {{ number_format($rtx->nominal, 0, ',', '.') }}</strong>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted small py-3">Belum ada transaksi kas RT yang tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 4: EVALUASI KINERJA RT (SCORECARD & EVALUASI RESMI)                   -->
    <!-- ========================================================================= -->
    @if($activeTab == 'evaluasi')
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                <i data-lucide="award" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                                Scorecard & Riwayat Evaluasi 8 RT (RW 013)
                            </h5>
                            <p class="mb-0 text-muted small">Rekapitulasi penetapan skor kepatuhan, catatan perbaikan, dan arahan resmi Ketua RW.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-secondary small fw-bold">Wilayah RT</th>
                                    <th class="text-secondary small fw-bold text-center">Skor Kinerja</th>
                                    <th class="text-secondary small fw-bold text-center">Status</th>
                                    <th class="text-secondary small fw-bold">Catatan Evaluasi Ketua RW</th>
                                    <th class="text-secondary small fw-bold">Rekomendasi Tindak Lanjut</th>
                                    <th class="text-end pe-3 text-secondary small fw-bold">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rtMonitoringList as $evalItem)
                                    <tr>
                                        <td class="ps-3">
                                            <strong class="text-dark d-block">{{ $evalItem['nama'] }}</strong>
                                            <small class="text-muted">Ketua: {{ $evalItem['ketua_rt'] }}</small>
                                        </td>
                                        <td class="text-center">
                                            <strong class="fs-5 text-dark">{{ $evalItem['skor_kinerja'] ?? 85 }}</strong><span class="small text-muted">/100</span>
                                        </td>
                                        <td class="text-center">
                                            @if($evalItem['status_laporan'] === 'Lengkap')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small fw-bold">Lengkap</span>
                                            @elseif($evalItem['status_laporan'] === 'Perlu diperiksa')
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill small fw-bold">Perlu Diperiksa</span>
                                            @elseif($evalItem['status_laporan'] === 'Belum dilaporkan')
                                                <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 rounded-pill small">Belum Lapor</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill small fw-bold">Perlu Tindak Lanjut</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-dark d-block">{{ $evalItem['evaluasi_rw'] }}</small>
                                        </td>
                                        <td>
                                            <small class="text-secondary d-block">{{ $evalItem['rekomendasi'] }}</small>
                                        </td>
                                        <td class="text-end pe-3">
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 small fw-bold" data-bs-toggle="modal" data-bs-target="#modalEvaluasiRT{{ $evalItem['rt'] }}">
                                                Beri Evaluasi
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 5: RIWAYAT & TINDAK LANJUT                                            -->
    <!-- ========================================================================= -->
    @if($activeTab == 'riwayat')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-2 text-dark border-bottom pb-2">
                <i data-lucide="history" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                Log Riwayat Laporan & Tindak Lanjut Koordinasi RW 013
            </h5>
            <p class="text-muted small mb-4">Rekam jejak seluruh penyerahan berkas bulanan, catatan evaluasi Ketua RW, dan tanggapan dari 8 RT.</p>

            <div class="d-flex flex-column gap-3">
                @forelse($timelineHistory as $hist)
                    <div class="p-3 border rounded-3 bg-light-subtle d-flex align-items-start justify-content-between gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary text-white p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; min-width: 38px;">
                                <i data-lucide="activity" style="width: 18px; height: 18px;"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-light text-dark border fw-bold">{{ $hist['rt_label'] }}</span>
                                    <strong class="text-dark small">{{ $hist['aksi'] }}</strong>
                                </div>
                                <small class="text-muted">Aktor: <strong class="text-secondary">{{ $hist['aktor'] }}</strong> &bull; {{ $hist['tanggal'] }}</small>
                            </div>
                        </div>
                        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">
                            {{ $hist['status'] }}
                        </span>
                    </div>
                @empty
                    <div class="text-center text-muted py-4 small">Belum ada riwayat aktivitas tercatat.</div>
                @endforelse
            </div>
        </div>
    @endif
</div>
@endsection
