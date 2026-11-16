@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Monitoring Keuangan Terpadu RW 013 -->
    <div class="wm-card mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0D233A 0%, #164070 100%); color: #fff; border: none; border-radius: 16px;">
        <div class="p-4 p-md-5 position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div style="max-width: 760px;">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-light text-primary fw-bold px-3 py-1 rounded-pill" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;"></i> PENGAWASAN & MONITORING SENTRAL
                        </span>
                        <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill opacity-90" style="font-size: 11px;">
                            RW 013 Sukamiskin Arcamanik
                        </span>
                        <span class="badge bg-success text-white fw-bold px-3 py-1 rounded-pill" style="font-size: 11px;">
                            <i data-lucide="eye" style="width: 12px; height: 12px; vertical-align: middle;"></i> Mode Read-Only Pengawas
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Monitoring Keuangan Terpadu RW 013</h2>
                    <p class="mb-0 text-white text-opacity-75 lead fs-6">
                        Pusat pemantauan seluruh kas lingkungan (Kas RW, Kas RT 001–008, Kas Sampah, dan Dana Kegiatan). Ketua RW bertindak sebagai pengawas akuntabilitas, transparansi, dan evaluasi berkala.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('dashboard.rw') }}" class="btn btn-light rounded-pill px-3 py-2 fw-bold small text-primary shadow-sm">
                        <i data-lucide="layout-dashboard" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Dashboard RW
                    </a>
                    <a href="{{ route('rw.audit.index') }}" class="btn btn-outline-light rounded-pill px-3 py-2 fw-bold small shadow-sm">
                        <i data-lucide="clipboard-check" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Audit & Evaluasi
                    </a>
                </div>
            </div>
        </div>
        <!-- Decorative Glow -->
        <div class="position-absolute rounded-circle" style="width: 320px; height: 320px; background: rgba(255,255,255,0.04); top: -80px; right: -60px; pointer-events: none;"></div>
    </div>

    <!-- Filter Periode Bulan & Tahun -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-3">
        <form method="GET" action="{{ route('rw.keuangan.monitoring') }}" class="row g-3 align-items-center" id="formPeriodeFilter">
            <input type="hidden" name="tab" id="filterPeriodeTabInput" value="{{ $activeTab }}">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Tahun Evaluasi</label>
                <select name="tahun" class="form-select rounded-3">
                    @for($y = Carbon\Carbon::now()->year + 1; $y >= Carbon\Carbon::now()->year - 2; $y--)
                        <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Bulan Monitoring</label>
                <select name="bulan" class="form-select rounded-3">
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $monthName = Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F');
                        @endphp
                        <option value="{{ $m }}" {{ $selectedBulan == $m ? 'selected' : '' }}>{{ $monthName }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex align-items-end gap-2 pt-md-4">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold small">
                    <i data-lucide="filter" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Perbarui Data
                </button>
                <a href="{{ route('rw.keuangan.monitoring', ['tab' => $activeTab]) }}" class="btn btn-light rounded-pill px-3 fw-bold small border text-muted">
                    Reset
                </a>
            </div>
            <div class="col-12 col-md-2 text-md-end text-muted small pt-md-4">
                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">
                    Periode: {{ Carbon\Carbon::create($selectedTahun, $selectedBulan, 1)->translatedFormat('F Y') }}
                </span>
            </div>
        </form>
    </div>

    <!-- 1. RINGKASAN KEUANGAN KESELURUHAN (5 SALDO UTAMA & ARUS KAS) -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Likuid RW 013 -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="wm-card p-4 h-100 bg-white border rounded-4 shadow-sm position-relative overflow-hidden" style="border-left: 5px solid #10B981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase tracking-wide">Total Saldo Seluruh Kas RW 013</span>
                    <div class="p-2 rounded-3 bg-emerald-50 text-success" style="background: #E8F5E9;">
                        <i data-lucide="vault" style="width: 22px; height: 22px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-2 text-dark">Rp {{ number_format($totalSaldoSeluruhKas, 0, ',', '.') }}</h3>
                <div class="small text-muted">
                    Akumulasi likuid aktif pada 18 rekening kas resmi RW 013
                </div>
            </div>
        </div>

        <!-- Card 2: Saldo Kas RW -->
        <div class="col-12 col-md-6 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Saldo Kas RW</span>
                    <div class="p-2 rounded-3 text-primary" style="background: #E3F2FD;">
                        <i data-lucide="landmark" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1 text-primary">Rp {{ number_format($saldoKasRw, 0, ',', '.') }}</h5>
                <span class="small text-muted">Kas sentral operasional</span>
            </div>
        </div>

        <!-- Card 3: Saldo Kas RT 001 - RT 008 -->
        <div class="col-12 col-md-6 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Kas 8 RT</span>
                    <div class="p-2 rounded-3 text-success" style="background: #E8F5E9;">
                        <i data-lucide="users" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1 text-success">Rp {{ number_format($saldoKasRtTotal, 0, ',', '.') }}</h5>
                <span class="small text-muted">Saldo 8 wilayah RT</span>
            </div>
        </div>

        <!-- Card 4: Saldo Kas Sampah -->
        <div class="col-12 col-md-6 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Kas Bank Sampah</span>
                    <div class="p-2 rounded-3 text-emerald-800" style="background: #E0F2F1;">
                        <i data-lucide="recycle" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1 text-success">Rp {{ number_format($saldoKasSampah, 0, ',', '.') }}</h5>
                <span class="small text-muted">Hasil pilah anorganik</span>
            </div>
        </div>

        <!-- Card 5: Saldo Dana Kegiatan -->
        <div class="col-12 col-md-6 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Dana Kegiatan</span>
                    <div class="p-2 rounded-3" style="background: #EDE7F6; color: #5E35B1;">
                        <i data-lucide="calendar-check" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1" style="color: #5E35B1;">Rp {{ number_format($saldoDanaKegiatanTotal, 0, ',', '.') }}</h5>
                <span class="small text-muted">8 pos program kerja</span>
            </div>
        </div>
    </div>

    <!-- Metrik Arus Kas Bulan Berjalan -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="wm-card p-3 bg-white border rounded-4 shadow-sm d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 text-success d-flex align-items-center justify-content-center" style="background: #E8F5E9; width: 48px; height: 48px;">
                    <i data-lucide="arrow-down-left" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <span class="small text-muted d-block">Pemasukan Bulan Ini (Seluruh Kas)</span>
                    <h5 class="fw-bold text-success mb-0">+ Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="wm-card p-3 bg-white border rounded-4 shadow-sm d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 text-danger d-flex align-items-center justify-content-center" style="background: #FEE2E2; width: 48px; height: 48px;">
                    <i data-lucide="arrow-up-right" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <span class="small text-muted d-block">Pengeluaran Bulan Ini (Seluruh Kas)</span>
                    <h5 class="fw-bold text-danger mb-0">- Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="wm-card p-3 bg-white border rounded-4 shadow-sm d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 text-primary d-flex align-items-center justify-content-center" style="background: #E3F2FD; width: 48px; height: 48px;">
                    <i data-lucide="receipt" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <span class="small text-muted d-block">Jumlah Transaksi Terverifikasi</span>
                    <h5 class="fw-bold text-primary mb-0">{{ $jumlahTransaksiBulanIni }} Transaksi Selesai</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SISTEM NAVIGASI TAB HORIZONTAL MONITORING KEUANGAN RW 013
         ========================================================================= -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-2">
        <ul class="nav nav-pills flex-nowrap overflow-x-auto gap-2 p-1" id="monitoringTabs" role="tablist" style="scrollbar-width: thin;">
            <!-- Tab A: Monitoring Kas RT -->
            <li class="nav-item flex-shrink-0" role="presentation">
                <button class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'kas_rt' ? 'active' : '' }}" 
                        id="tab-btn-kas-rt" data-bs-toggle="tab" data-bs-target="#tab-pane-kas-rt" type="button" role="tab" aria-controls="tab-pane-kas-rt" aria-selected="{{ $activeTab == 'kas_rt' ? 'true' : 'false' }}">
                    <i data-lucide="building" style="width: 17px; height: 17px;"></i>
                    <span>A. Monitoring Kas RT</span>
                    @if($rtPerluPerhatianCount > 0)
                        <span class="badge rounded-pill bg-danger text-white ms-1" style="font-size: 11px;">{{ $rtPerluPerhatianCount }} Perhatian</span>
                    @else
                        <span class="badge rounded-pill bg-light text-secondary border ms-1" style="font-size: 11px;">8 RT</span>
                    @endif
                </button>
            </li>

            <!-- Tab B: Monitoring Bank Sampah -->
            <li class="nav-item flex-shrink-0" role="presentation">
                <button class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'bank_sampah' ? 'active' : '' }}" 
                        id="tab-btn-bank-sampah" data-bs-toggle="tab" data-bs-target="#tab-pane-bank-sampah" type="button" role="tab" aria-controls="tab-pane-bank-sampah" aria-selected="{{ $activeTab == 'bank_sampah' ? 'true' : 'false' }}">
                    <i data-lucide="recycle" style="width: 17px; height: 17px;"></i>
                    <span>B. Bank Sampah</span>
                    <span class="badge rounded-pill bg-light text-secondary border ms-1" style="font-size: 11px;">8 Unit Drop</span>
                </button>
            </li>

            <!-- Tab C: Monitoring Dana Kegiatan -->
            <li class="nav-item flex-shrink-0" role="presentation">
                <button class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'dana_kegiatan' ? 'active' : '' }}" 
                        id="tab-btn-dana-kegiatan" data-bs-toggle="tab" data-bs-target="#tab-pane-dana-kegiatan" type="button" role="tab" aria-controls="tab-pane-dana-kegiatan" aria-selected="{{ $activeTab == 'dana_kegiatan' ? 'true' : 'false' }}">
                    <i data-lucide="calendar-check" style="width: 17px; height: 17px;"></i>
                    <span>C. Dana Kegiatan</span>
                    @if($kegiatanPerluAtensiCount > 0)
                        <span class="badge rounded-pill bg-warning text-dark ms-1" style="font-size: 11px;">{{ $kegiatanPerluAtensiCount }} Alert</span>
                    @else
                        <span class="badge rounded-pill bg-light text-secondary border ms-1" style="font-size: 11px;">{{ count($monitoringDanaKegiatan) }} Pos</span>
                    @endif
                </button>
            </li>

            <!-- Tab D: Aktivitas Keuangan Terbaru -->
            <li class="nav-item flex-shrink-0" role="presentation">
                <button class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'aktivitas' ? 'active' : '' }}" 
                        id="tab-btn-aktivitas" data-bs-toggle="tab" data-bs-target="#tab-pane-aktivitas" type="button" role="tab" aria-controls="tab-pane-aktivitas" aria-selected="{{ $activeTab == 'aktivitas' ? 'true' : 'false' }}">
                    <i data-lucide="history" style="width: 17px; height: 17px;"></i>
                    <span>D. Aktivitas Terbaru</span>
                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 11px;">{{ $recentTransactions->total() ?? count($recentTransactions) }} Tx</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- =========================================================================
         AREA KONTEN TAB MONITORING KEUANGAN (HANYA TAB AKTIF YANG TAMPIL)
         ========================================================================= -->
    <div class="tab-content" id="monitoringTabsContent">

        <!-- =====================================================================
             TAB A: MONITORING KAS RT (RT 001 - RT 008)
             ===================================================================== -->
        <div class="tab-pane fade {{ $activeTab == 'kas_rt' ? 'show active' : '' }}" id="tab-pane-kas-rt" role="tabpanel" aria-labelledby="tab-btn-kas-rt">
            
            @php
                $rtTertinggi = collect($monitoringKasRt)->sortByDesc('saldo')->first();
                $avgSaldoRt = count($monitoringKasRt) > 0 ? round(collect($monitoringKasRt)->avg('saldo')) : 0;
            @endphp

            <!-- Kartu Analisis Cepat Kas RT -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="p-3 bg-white border rounded-4 shadow-sm h-100 d-flex align-items-center gap-3">
                        <div class="p-3 rounded-circle text-primary" style="background: #E3F2FD;">
                            <i data-lucide="bar-chart-3" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <span class="small text-muted d-block">Rata-rata Saldo per RT</span>
                            <h5 class="fw-bold text-dark mb-0">Rp {{ number_format($avgSaldoRt, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 bg-white border rounded-4 shadow-sm h-100 d-flex align-items-center gap-3">
                        <div class="p-3 rounded-circle text-success" style="background: #E8F5E9;">
                            <i data-lucide="trending-up" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <span class="small text-muted d-block">Saldo Tertinggi</span>
                            <h5 class="fw-bold text-success mb-0">{{ $rtTertinggi['rt_name'] ?? '-' }}: Rp {{ number_format($rtTertinggi['saldo'] ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 bg-white border rounded-4 shadow-sm h-100 d-flex align-items-center gap-3">
                        <div class="p-3 rounded-circle {{ $rtPerluPerhatianCount > 0 ? 'text-danger' : 'text-success' }}" style="background: {{ $rtPerluPerhatianCount > 0 ? '#FEE2E2' : '#E8F5E9' }};">
                            <i data-lucide="{{ $rtPerluPerhatianCount > 0 ? 'alert-triangle' : 'check-circle' }}" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <span class="small text-muted d-block">Status Perhatian Pengawas</span>
                            <h5 class="fw-bold {{ $rtPerluPerhatianCount > 0 ? 'text-danger' : 'text-success' }} mb-0">
                                {{ $rtPerluPerhatianCount > 0 ? $rtPerluPerhatianCount . ' RT Perlu Perhatian' : 'Seluruh RT Sehat & Aktif' }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Monitoring Kas RT (RT 001 - RT 008) -->
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i data-lucide="building" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                            Monitoring Kas RT (RT 001 s/d RT 008)
                        </h5>
                        <p class="mb-0 text-muted small">
                            Evaluasi saldo kas, arus kas masuk/keluar, dan kepatuhan pencatatan keuangan dari 8 Ketua RT di lingkungan RW 013.
                        </p>
                    </div>
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">
                        <i data-lucide="check-circle" style="width: 14px; height: 14px; vertical-align: middle;" class="text-success me-1"></i> Data Terkini Sinkron Otomatis
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-secondary small fw-bold text-uppercase">Wilayah RT</th>
                                <th class="text-secondary small fw-bold text-uppercase">Penanggung Jawab / Ketua RT</th>
                                <th class="text-secondary small fw-bold text-uppercase">Saldo Terkini</th>
                                <th class="text-secondary small fw-bold text-uppercase">Pemasukan Bulan Ini</th>
                                <th class="text-secondary small fw-bold text-uppercase">Pengeluaran Bulan Ini</th>
                                <th class="text-secondary small fw-bold text-uppercase">Transaksi Terakhir</th>
                                <th class="text-secondary small fw-bold text-uppercase">Kondisi Keuangan</th>
                                <th class="text-secondary small fw-bold text-uppercase">Kepatuhan Entri</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monitoringKasRt as $row)
                                <tr>
                                    <td class="ps-3">
                                        <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: #E8F5E9; color: #16865D; border: 1px solid #C8E6C9;">
                                            {{ $row['rt_name'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block">{{ $row['pic_name'] }}</strong>
                                        <small class="text-muted"><i data-lucide="phone" style="width: 12px; height: 12px; vertical-align: middle;"></i> {{ $row['pic_phone'] }}</small>
                                    </td>
                                    <td>
                                        <strong class="text-dark fs-6">Rp {{ number_format($row['saldo'], 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-success fw-semibold">+ Rp {{ number_format($row['pemasukan'], 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <span class="text-danger fw-semibold">- Rp {{ number_format($row['pengeluaran'], 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        @if($row['tx_terakhir'])
                                            <span class="text-dark d-block small fw-semibold">{{ $row['tx_terakhir']->tanggal_transaksi->format('d/m/Y') }}</span>
                                            <small class="text-muted">{{ Str::limit($row['tx_terakhir']->kategori, 20) }}</small>
                                        @else
                                            <span class="text-muted small">Belum ada transaksi</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $row['kondisi']['badge'] }} border px-3 py-1 rounded-pill small fw-bold">
                                            {{ $row['kondisi']['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($row['count_tx'] >= 3)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                                                <i data-lucide="check" style="width: 12px; height: 12px; vertical-align: middle;"></i> {{ $row['kepatuhan'] }} ({{ $row['count_tx'] }} tx)
                                            </span>
                                        @elseif($row['count_tx'] >= 1)
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill small">
                                                {{ $row['kepatuhan'] }} ({{ $row['count_tx'] }} tx)
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill small">
                                                {{ $row['kepatuhan'] }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Monitoring Iuran Warga Terpadu (Penopang Kas RT) -->
            <div class="row g-4 mb-2">
                <div class="col-12 col-xl-4">
                    <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                            <i data-lucide="receipt" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                            Ringkasan Iuran Warga
                        </h5>
                        <p class="text-muted small mb-3">Tingkat kolektibilitas dan penerimaan iuran bulanan warga RW 013 periode {{ Carbon\Carbon::create($selectedTahun, $selectedBulan, 1)->translatedFormat('F Y') }}.</p>

                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Target Penerimaan:</span>
                                <strong class="text-dark">Rp {{ number_format($totalTagihanIuran, 0, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Realisasi Terbayar:</span>
                                <strong class="text-success">Rp {{ number_format($totalIuranTerbayar, 0, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Tunggakan / Sisa:</span>
                                <strong class="text-danger">Rp {{ number_format($totalIuranTunggakan, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="fw-bold text-dark">Kolektibilitas Pembayaran:</span>
                                <span class="fw-bold text-primary">{{ $persenIuran }}%</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $persenIuran) }}%;" aria-valuenow="{{ $persenIuran }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="p-2 border rounded-3 bg-light-subtle small text-muted">
                            <i data-lucide="users" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i>
                            <strong>{{ $totalKkLunas }}</strong> dari <strong>{{ $totalKkTerdaftar }}</strong> KK telah melunasi kewajiban iuran.
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-8">
                    <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i data-lucide="bar-chart-2" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                                Rekapitulasi Iuran 8 RT (Bulan Ini)
                            </h5>
                            <span class="small text-muted">Periode: {{ $periodeStr }}</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-2 text-secondary small fw-bold">Wilayah RT</th>
                                        <th class="text-secondary small fw-bold text-center">KK Terdaftar</th>
                                        <th class="text-secondary small fw-bold text-center">KK Lunas</th>
                                        <th class="text-secondary small fw-bold">Target</th>
                                        <th class="text-secondary small fw-bold">Terbayar</th>
                                        <th class="text-secondary small fw-bold">Tunggakan</th>
                                        <th class="text-secondary small fw-bold text-center">% Capaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekapIuranRt as $rtItem)
                                        <tr>
                                            <td class="ps-2">
                                                <span class="badge bg-light text-dark border fw-bold">{{ $rtItem['rt_name'] }}</span>
                                            </td>
                                            <td class="text-center small">{{ $rtItem['total_kk'] }} KK</td>
                                            <td class="text-center small">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    {{ $rtItem['lunas_kk'] }}
                                                </span>
                                            </td>
                                            <td class="small">Rp {{ number_format($rtItem['target'], 0, ',', '.') }}</td>
                                            <td class="small fw-bold text-success">Rp {{ number_format($rtItem['terbayar'], 0, ',', '.') }}</td>
                                            <td class="small text-danger">Rp {{ number_format($rtItem['tunggakan'], 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $rtItem['persentase'] >= 80 ? 'bg-success' : ($rtItem['persentase'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-2 py-1 small">
                                                    {{ $rtItem['persentase'] }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- =====================================================================
             TAB B: MONITORING BANK SAMPAH TERPADU RW 013
             ===================================================================== -->
        <div class="tab-pane fade {{ $activeTab == 'bank_sampah' ? 'show active' : '' }}" id="tab-pane-bank-sampah" role="tabpanel" aria-labelledby="tab-btn-bank-sampah">
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i data-lucide="recycle" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                            Monitoring Program Bank Sampah Terpadu RW 013
                        </h5>
                        <p class="mb-0 text-muted small">
                            Pemantauan volume sampah anorganik terpilah, total nilai tabungan warga, dan operasional drop point 8 RT.
                        </p>
                    </div>
                    <a href="{{ route('bank-sampah') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">
                        <i data-lucide="external-link" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Buka Portal Bank Sampah
                    </a>
                </div>

                <!-- Kartu Metrik Bank Sampah -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="p-3 border rounded-3 bg-light-subtle d-flex align-items-center justify-content-between">
                            <div>
                                <span class="small text-muted d-block">Saldo Kas Bank Sampah</span>
                                <h5 class="fw-bold text-success mb-0">Rp {{ number_format($saldoKasSampah, 0, ',', '.') }}</h5>
                            </div>
                            <div class="p-2 rounded-3 bg-emerald-50 text-success">
                                <i data-lucide="wallet" style="width: 22px; height: 22px;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="p-3 border rounded-3 bg-light-subtle d-flex align-items-center justify-content-between">
                            <div>
                                <span class="small text-muted d-block">Sampah Terkumpul Bulan Ini</span>
                                <h5 class="fw-bold text-dark mb-0">{{ number_format($totalKgBankSampahBulanIni, 1, ',', '.') }} kg</h5>
                            </div>
                            <div class="p-2 rounded-3 bg-sky-50 text-primary">
                                <i data-lucide="scale" style="width: 22px; height: 22px;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="p-3 border rounded-3 bg-light-subtle d-flex align-items-center justify-content-between">
                            <div>
                                <span class="small text-muted d-block">Nilai Tabungan Tercipta</span>
                                <h5 class="fw-bold text-dark mb-0">Rp {{ number_format($nilaiTransaksiSampahBulanIni, 0, ',', '.') }}</h5>
                            </div>
                            <div class="p-2 rounded-3 bg-amber-50 text-warning">
                                <i data-lucide="coins" style="width: 22px; height: 22px;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi & Catatan Pengawas RW -->
                <div class="p-3 rounded-3 mb-4 border d-flex align-items-start gap-3" style="background: #F0FDF4; border-color: #DCFCE7 !important;">
                    <i data-lucide="info" class="text-success mt-1" style="width: 20px; height: 20px; min-width: 20px;"></i>
                    <div class="small text-dark">
                        <strong>Catatan Pengawasan RW:</strong> Seluruh tabungan nasabah warga tercatat terpusat pada sistem buku kas bank sampah. Pastikan penimbangan rutin setiap Sabtu/Minggu di masing-masing RT didampingi oleh petugas unit agar rekapitulasi kg dan saldo tabungan warga tetap akurat.
                    </div>
                </div>

                <!-- Dua Kolom: Drop Point 8 RT & Aktivitas Setoran Terbaru -->
                <div class="row g-4">
                    <div class="col-12 col-lg-7">
                        <h6 class="fw-bold text-dark mb-2">Drop Point & Petugas Bank Sampah 8 RT</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-2 text-secondary small fw-bold">Unit / RT</th>
                                        <th class="text-secondary small fw-bold">Petugas Pengelola</th>
                                        <th class="text-secondary small fw-bold">Drop Point</th>
                                        <th class="text-secondary small fw-bold text-end pe-2">Total Terkumpul</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekapBankSampahRt as $bsItem)
                                        <tr>
                                            <td class="ps-2">
                                                <strong class="text-dark d-block small">{{ $bsItem['nama_unit'] }}</strong>
                                                <span class="badge bg-light text-secondary border" style="font-size: 10px;">{{ $bsItem['rt_name'] }}</span>
                                            </td>
                                            <td class="small text-dark">{{ $bsItem['petugas'] }}</td>
                                            <td class="small text-muted">{{ Str::limit($bsItem['lokasi'], 25) }}</td>
                                            <td class="small fw-bold text-success text-end pe-2">{{ number_format($bsItem['total_kg'], 1) }} kg</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <h6 class="fw-bold text-dark mb-2">Aktivitas Setoran Sampah Terbaru</h6>
                        <div class="d-flex flex-column gap-2">
                            @forelse($setoranSampahTerbaru as $txSampah)
                                <div class="p-2 border rounded-3 bg-light-subtle d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-dark small d-block">{{ $txSampah->user->name ?? 'Warga' }}</strong>
                                        <small class="text-muted">{{ $txSampah->created_at->format('d/m/Y H:i') }} • {{ $txSampah->total_berat_formatted }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold text-success small d-block">{{ $txSampah->total_rupiah_formatted }}</span>
                                        <span class="badge {{ $txSampah->status_badge['bg'] }} px-2 py-0" style="font-size: 10px;">{{ $txSampah->status_label }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted small py-4">Belum ada transaksi setoran terbaru.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =====================================================================
             TAB C: MONITORING DANA KEGIATAN RW 013
             ===================================================================== -->
        <div class="tab-pane fade {{ $activeTab == 'dana_kegiatan' ? 'show active' : '' }}" id="tab-pane-dana-kegiatan" role="tabpanel" aria-labelledby="tab-btn-dana-kegiatan">
            
            @php
                $totalPaguKegiatan = collect($monitoringDanaKegiatan)->sum('anggaran_awal');
                $totalRealisasiKeg = collect($monitoringDanaKegiatan)->sum('realisasi');
                $totalSisaKeg = collect($monitoringDanaKegiatan)->sum('sisa_anggaran');
                $avgSerap = $totalPaguKegiatan > 0 ? round(($totalRealisasiKeg / $totalPaguKegiatan) * 100, 1) : 0;
            @endphp

            <!-- Kartu Metrik Ringkasan Dana Kegiatan -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-3">
                    <div class="p-3 bg-white border rounded-4 shadow-sm h-100">
                        <span class="small text-muted d-block mb-1">Total Pagu Anggaran Rencana</span>
                        <h5 class="fw-bold text-dark mb-0">Rp {{ number_format($totalPaguKegiatan, 0, ',', '.') }}</h5>
                        <small class="text-muted">{{ count($monitoringDanaKegiatan) }} Pos Kegiatan Terdaftar</small>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 bg-white border rounded-4 shadow-sm h-100">
                        <span class="small text-muted d-block mb-1">Total Realisasi Terpakai</span>
                        <h5 class="fw-bold text-danger mb-0">- Rp {{ number_format($totalRealisasiKeg, 0, ',', '.') }}</h5>
                        <small class="text-muted">{{ $avgSerap }}% dari total pagu</small>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 bg-white border rounded-4 shadow-sm h-100">
                        <span class="small text-muted d-block mb-1">Sisa Anggaran Tersedia</span>
                        <h5 class="fw-bold text-success mb-0">Rp {{ number_format($totalSisaKeg, 0, ',', '.') }}</h5>
                        <small class="text-muted">Likuid siap realisasi</small>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 bg-white border rounded-4 shadow-sm h-100">
                        <span class="small text-muted d-block mb-1">Kondisi Serapan Anggaran</span>
                        <h5 class="fw-bold {{ $kegiatanPerluAtensiCount > 0 ? 'text-warning-emphasis' : 'text-primary' }} mb-0">
                            {{ $kegiatanPerluAtensiCount > 0 ? $kegiatanPerluAtensiCount . ' Pos Perlu Atensi' : 'Penyerapan Terkendali' }}
                        </h5>
                        <small class="text-muted">{{ $avgSerap }}% rata-rata serapan</small>
                    </div>
                </div>
            </div>

            @if($kegiatanPerluAtensiCount > 0)
                <div class="p-3 rounded-3 mb-4 border d-flex align-items-start gap-3" style="background: #FFFBEB; border-color: #FDE68A !important;">
                    <i data-lucide="alert-triangle" class="text-warning mt-1" style="width: 20px; height: 20px; min-width: 20px;"></i>
                    <div class="small text-dark">
                        <strong>Perhatian Pengawas:</strong> Terdapat {{ $kegiatanPerluAtensiCount }} pos kegiatan dengan tingkat penyerapan dana di atas 90% atau sisa pagu di bawah Rp 500.000. Pengurus pelaksana wajib menyampaikan Laporan Pertanggungjawaban (LPJ) sebelum mengajukan permohonan dana tambahan.
                    </div>
                </div>
            @endif

            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i data-lucide="calendar-check" class="me-2" style="width: 20px; height: 20px; vertical-align: middle; color: #5E35B1;"></i>
                            Monitoring Pos Dana Kegiatan RW 013
                        </h5>
                        <p class="mb-0 text-muted small">
                            Alokasi anggaran, realisasi penyerapan dana, dan sisa pagu anggaran pada seluruh program kerja kepengurusan RW 013.
                        </p>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill small">
                        Total Pos: {{ count($monitoringDanaKegiatan) }} Rekening Kegiatan
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-secondary small fw-bold text-uppercase">Nama Pos Kegiatan</th>
                                <th class="text-secondary small fw-bold text-uppercase">Penanggung Jawab (PIC)</th>
                                <th class="text-secondary small fw-bold text-uppercase">Anggaran Rencana</th>
                                <th class="text-secondary small fw-bold text-uppercase">Realisasi Terpakai</th>
                                <th class="text-secondary small fw-bold text-uppercase">Sisa Anggaran</th>
                                <th class="text-secondary small fw-bold text-uppercase text-center">Penyerapan</th>
                                <th class="text-secondary small fw-bold text-uppercase text-end pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monitoringDanaKegiatan as $pos)
                                <tr>
                                    <td class="ps-3">
                                        <strong class="text-dark d-block">{{ $pos['nama_pos'] }}</strong>
                                        <small class="text-muted">Rek: {{ $pos['nomor_rekening'] }}</small>
                                    </td>
                                    <td>
                                        <span class="small text-dark fw-semibold">{{ $pos['pic'] }}</span>
                                    </td>
                                    <td>
                                        <span class="small fw-semibold text-dark">Rp {{ number_format($pos['anggaran_awal'], 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <span class="small fw-bold text-danger">- Rp {{ number_format($pos['realisasi'], 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <strong class="small fw-bold text-success">Rp {{ number_format($pos['sisa_anggaran'], 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-center" style="min-width: 140px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px;">
                                                <div class="progress-bar {{ $pos['persen_serap'] > 90 ? 'bg-danger' : ($pos['persen_serap'] > 50 ? 'bg-primary' : 'bg-success') }}" style="width: {{ min(100, $pos['persen_serap']) }}%;"></div>
                                            </div>
                                            <span class="small fw-bold text-dark">{{ $pos['persen_serap'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="badge {{ $pos['persen_serap'] > 90 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-light text-secondary border' }} px-3 py-1 rounded-pill small fw-semibold">
                                            {{ $pos['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada akun dana kegiatan terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- =====================================================================
             TAB D: AKTIVITAS KEUANGAN TERBARU (READ-ONLY MONITORING)
             ===================================================================== -->
        <div class="tab-pane fade {{ $activeTab == 'aktivitas' ? 'show active' : '' }}" id="tab-pane-aktivitas" role="tabpanel" aria-labelledby="tab-btn-aktivitas">
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i data-lucide="history" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                            Aktivitas Keuangan Terbaru (Seluruh Kas RW 013)
                        </h5>
                        <p class="mb-0 text-muted small">
                            Log transaksi harian dari seluruh kas sentral RW 013, kas 8 RT, kas sampah, dan pos dana kegiatan. (Mode Read-Only Pengawas).
                        </p>
                    </div>
                    <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-2 rounded-pill small fw-bold" style="background: #E8F5E9; color: #16865D;">
                        <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Audit Ledger Terkunci
                    </span>
                </div>

                <!-- Filter Interaktif Aktivitas -->
                <form method="GET" action="{{ route('rw.keuangan.monitoring') }}" class="row g-2 mb-3">
                    <input type="hidden" name="tab" value="aktivitas">
                    <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
                    <input type="hidden" name="bulan" value="{{ $selectedBulan }}">
                    
                    <div class="col-12 col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Cari nomor tx / pihak / uraian..." value="{{ request('search') }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <select name="jenis_kas_id" class="form-select form-select-sm rounded-3">
                            <option value="">Semua Akun Kas</option>
                            @foreach($allBukuKas as $bk)
                                <option value="{{ $bk->id }}" {{ request('jenis_kas_id') == $bk->id ? 'selected' : '' }}>{{ $bk->nama_kas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <select name="rt_filter" class="form-select form-select-sm rounded-3">
                            <option value="">Semua Wilayah RT</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ request('rt_filter') == $i ? 'selected' : '' }}>RT 00{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <select name="jenis_transaksi" class="form-select form-select-sm rounded-3">
                            <option value="">Semua Jenis</option>
                            <option value="pemasukan" {{ request('jenis_transaksi') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="pengeluaran" {{ request('jenis_transaksi') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold flex-grow-1">
                            <i data-lucide="search" style="width: 14px; height: 14px; vertical-align: middle;"></i> Filter
                        </button>
                        <a href="{{ route('rw.keuangan.monitoring', ['tab' => 'aktivitas', 'tahun' => $selectedTahun, 'bulan' => $selectedBulan]) }}" class="btn btn-sm btn-light border rounded-pill px-3 text-muted">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-secondary small fw-bold">Tanggal & No. TX</th>
                                <th class="text-secondary small fw-bold">Akun Kas</th>
                                <th class="text-secondary small fw-bold">Jenis / Kategori</th>
                                <th class="text-secondary small fw-bold">Pihak / Keterangan</th>
                                <th class="text-secondary small fw-bold">Nominal</th>
                                <th class="text-secondary small fw-bold">Petugas Pencatat</th>
                                <th class="text-secondary small fw-bold">Status</th>
                                <th class="text-end pe-3 text-secondary small fw-bold">Detail Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $tx)
                                <tr>
                                    <td class="ps-3">
                                        <strong class="text-dark d-block small">{{ $tx->nomor_transaksi }}</strong>
                                        <small class="text-muted">{{ $tx->tanggal_transaksi->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border small">{{ $tx->bukuKas->nama_kas ?? 'Kas RW' }}</span>
                                    </td>
                                    <td>
                                        @if(in_array($tx->jenis_transaksi, ['pemasukan', 'transfer_masuk']))
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                                                <i data-lucide="arrow-down-left" style="width: 11px; height: 11px; vertical-align: middle;"></i> Masuk
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill small">
                                                <i data-lucide="arrow-up-right" style="width: 11px; height: 11px; vertical-align: middle;"></i> Keluar
                                            </span>
                                        @endif
                                        <small class="d-block text-muted mt-1">{{ $tx->kategori }}</small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-semibold small d-block">{{ $tx->penyetor_penerima ?? '-' }}</span>
                                        <small class="text-muted">{{ Str::limit($tx->keterangan, 35) }}</small>
                                    </td>
                                    <td>
                                        @if(in_array($tx->jenis_transaksi, ['pemasukan', 'transfer_masuk']))
                                            <strong class="text-success small">+ Rp {{ number_format($tx->nominal, 0, ',', '.') }}</strong>
                                        @else
                                            <strong class="text-danger small">- Rp {{ number_format($tx->nominal, 0, ',', '.') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-dark d-block">{{ $tx->petugas->name ?? 'Sistem' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $tx->status_badge['badge'] ?? 'bg-success-subtle text-success border-success-subtle' }} px-2 py-1 rounded-pill small">
                                            {{ $tx->status_badge['label'] ?? ucfirst($tx->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 small" data-bs-toggle="modal" data-bs-target="#modalTxDetail{{ $tx->id }}">
                                            <i data-lucide="file-text" style="width: 13px; height: 13px; vertical-align: middle;"></i> Bukti
                                        </button>

                                        <!-- MODAL DETAIL / BUKTI TRANSAKSI (READ-ONLY) -->
                                        <div class="modal fade text-start" id="modalTxDetail{{ $tx->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <div class="modal-header border-bottom pb-3">
                                                        <div>
                                                            <h6 class="modal-title fw-bold text-dark">Detail Transaksi {{ $tx->nomor_transaksi }}</h6>
                                                            <small class="text-muted">Buku Kas: {{ $tx->bukuKas->nama_kas ?? 'Kas RW' }}</small>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="p-3 bg-light rounded-3 mb-3 border">
                                                            <div class="d-flex justify-content-between mb-1 small">
                                                                <span class="text-muted">Nominal Transaksi:</span>
                                                                <strong class="{{ in_array($tx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? 'text-success' : 'text-danger' }}">
                                                                    Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                                                                </strong>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1 small">
                                                                <span class="text-muted">Tanggal Transaksi:</span>
                                                                <span class="text-dark">{{ $tx->tanggal_transaksi->format('d F Y') }}</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1 small">
                                                                <span class="text-muted">Penyetor / Penerima:</span>
                                                                <span class="text-dark">{{ $tx->penyetor_penerima ?? '-' }}</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between small">
                                                                <span class="text-muted">Metode Pembayaran:</span>
                                                                <span class="text-dark text-uppercase">{{ $tx->metode_pembayaran ?? 'Tunai' }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="small text-muted fw-bold d-block mb-1">Uraian / Keterangan:</label>
                                                            <p class="small text-dark p-2 border rounded-3 bg-white mb-0">{{ $tx->keterangan ?? 'Tidak ada keterangan tambahan.' }}</p>
                                                        </div>

                                                        <div>
                                                            <label class="small text-muted fw-bold d-block mb-1">Bukti Nota / Kuitansi:</label>
                                                            @if($tx->bukti_transaksi_path)
                                                                <div class="text-center p-2 border rounded-3 bg-light">
                                                                    <img src="{{ asset('storage/' . $tx->bukti_transaksi_path) }}" alt="Bukti Transaksi" class="img-fluid rounded" style="max-height: 250px;">
                                                                </div>
                                                            @else
                                                                <div class="p-3 text-center border rounded-3 bg-light text-muted small">
                                                                    <i data-lucide="image-off" class="d-block mx-auto mb-1" style="width: 24px; height: 24px;"></i>
                                                                    Tidak ada lampiran bukti nota digital.
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top pt-2">
                                                        <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Tidak ada data transaksi yang sesuai filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $recentTransactions->appends(['tab' => 'aktivitas'])->links() }}
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Scoped Styles for Horizontal Tab Navigation -->
<style>
    #monitoringTabs {
        -webkit-overflow-scrolling: touch;
    }
    #monitoringTabs::-webkit-scrollbar {
        height: 4px;
    }
    #monitoringTabs::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    #monitoringTabs .nav-link {
        color: #475569;
        background-color: transparent;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    #monitoringTabs .nav-link:hover {
        color: #0f172a;
        background-color: #f1f5f9;
    }
    #monitoringTabs .nav-link.active {
        color: #ffffff !important;
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    #monitoringTabs .nav-link.active .badge {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.4) !important;
    }
    .tab-pane {
        animation: fadeIn 0.2s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- Tab Switching & Persistence Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Init Lucide Icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const tabButtons = document.querySelectorAll('#monitoringTabs button[data-bs-toggle="tab"]');
        const filterPeriodeTabInput = document.getElementById('filterPeriodeTabInput');

        // Check URL parameter on load
        const urlParams = new URLSearchParams(window.location.search);
        const urlTab = urlParams.get('tab');
        if (urlTab) {
            const targetBtn = document.querySelector(`#tab-btn-${urlTab.replace('_', '-')}`);
            if (targetBtn && typeof bootstrap !== 'undefined') {
                const tabInstance = bootstrap.Tab.getOrCreateInstance(targetBtn);
                tabInstance.show();
            }
        }

        // Listen for tab switch
        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function (e) {
                const targetId = e.target.getAttribute('data-bs-target');
                let tabName = 'kas_rt';
                if (targetId === '#tab-pane-bank-sampah') {
                    tabName = 'bank_sampah';
                } else if (targetId === '#tab-pane-dana-kegiatan') {
                    tabName = 'dana_kegiatan';
                } else if (targetId === '#tab-pane-aktivitas') {
                    tabName = 'aktivitas';
                }

                // Update URL query string without reloading page
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('tab', tabName);
                window.history.replaceState({}, '', currentUrl);

                // Update hidden input in filter form
                if (filterPeriodeTabInput) {
                    filterPeriodeTabInput.value = tabName;
                }

                // Refresh icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        });
    });
</script>
@endsection
