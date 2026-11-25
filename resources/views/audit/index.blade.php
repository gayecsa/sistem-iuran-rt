@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Audit & Evaluasi RW 013 -->
    <div class="wm-card mb-4 overflow-hidden position-relative sa-hero-card" style="background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%) !important; color: #fff !important; border: none !important; border-radius: 16px;">
        <div class="p-4 p-md-5 position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div style="max-width: 820px;">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-primary text-white fw-bold px-3 py-1 rounded-pill" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;"></i> KEUANGAN & AUDIT EVALUASI RW
                        </span>
                        <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill opacity-90" style="font-size: 11px;">
                            RW 013 Sukamiskin Arcamanik
                        </span>
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill" style="font-size: 11px;">
                            Tahun Anggaran {{ $tahun }}
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Manajemen & Monitoring Keuangan RW 013</h2>
                    <p class="mb-0 text-white text-opacity-75 lead fs-6">
                        Pengelolaan kas mandiri RW, monitoring pelaporan keuangan 8 RT secara terpusat, pengawasan kolektibilitas iuran warga, serta audit akuntabilitas lingkungan terpadu.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('rw.audit.cetak', ['tahun' => $tahun]) }}" target="_blank" class="btn btn-warning rounded-pill px-3 py-2 fw-bold small text-dark shadow-sm">
                        <i data-lucide="printer" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Cetak Laporan (LHA)
                    </a>
                </div>
            </div>
        </div>
        <div class="position-absolute rounded-circle" style="width: 320px; height: 320px; background: rgba(255,255,255,0.03); top: -80px; right: -60px; pointer-events: none;"></div>
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
    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="rounded-circle bg-danger text-white p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="text-danger d-block">Perhatian!</strong>
                <ul class="mb-0 small ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Navigasi 6 Tab Utama Sistem Keuangan RW 013 -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-2">
        <ul class="nav nav-pills nav-fill gap-2" id="auditTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'ringkasan' ? 'active' : '' }}" href="{{ route('rw.audit.index', ['tab' => 'ringkasan', 'tahun' => $tahun]) }}">
                    <i data-lucide="layout-dashboard" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> 1. Ringkasan Keuangan
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'kas_rw' ? 'active' : '' }}" href="{{ route('rw.audit.index', ['tab' => 'kas_rw', 'tahun' => $tahun]) }}">
                    <i data-lucide="wallet" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> 2. Kas RW
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'rekap_rt' ? 'active' : '' }}" href="{{ route('rw.audit.index', ['tab' => 'rekap_rt', 'tahun' => $tahun]) }}">
                    <i data-lucide="building-2" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> 3. Rekap Kas RT
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'monitoring_iuran' ? 'active' : '' }}" href="{{ route('rw.audit.index', ['tab' => 'monitoring_iuran', 'tahun' => $tahun]) }}">
                    <i data-lucide="pie-chart" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> 4. Monitoring Iuran
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'laporan' ? 'active' : '' }}" href="{{ route('rw.audit.index', ['tab' => 'laporan', 'tahun' => $tahun]) }}">
                    <i data-lucide="file-text" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> 5. Laporan Keuangan
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'riwayat' ? 'active' : '' }}" href="{{ route('rw.audit.index', ['tab' => 'riwayat', 'tahun' => $tahun]) }}">
                    <i data-lucide="history" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> 6. Riwayat Transaksi
                </a>
            </li>
        </ul>

        <!-- Sub-navigasi untuk Audit Khusus (Backward-Compatible) -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2 pt-2 border-top px-2">
            <span class="small text-muted"><i data-lucide="shield-alert" style="width: 14px; height: 14px; vertical-align: middle;"></i> Modul Audit & Evaluasi Kepatuhan:</span>
            <div class="d-flex gap-2">
                <a href="{{ route('rw.audit.index', ['tab' => 'temuan', 'tahun' => $tahun]) }}" class="btn btn-sm {{ $activeTab == 'temuan' ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3 py-1 fw-bold audit-subnav-btn audit-btn-temuan" style="font-size: 11px;">
                    <i data-lucide="alert-circle" style="width: 13px; height: 13px; vertical-align: middle;"></i> Temuan Audit ({{ $totalTemuan }})
                </a>
                <a href="{{ route('rw.audit.index', ['tab' => 'evaluasi', 'tahun' => $tahun]) }}" class="btn btn-sm {{ $activeTab == 'evaluasi' ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3 py-1 fw-bold audit-subnav-btn audit-btn-evaluasi" style="font-size: 11px;">
                    <i data-lucide="award" style="width: 13px; height: 13px; vertical-align: middle;"></i> Evaluasi & Rekomendasi RT
                </a>
                <a href="{{ route('rw.audit.index', ['tab' => 'kegiatan', 'tahun' => $tahun]) }}" class="btn btn-sm {{ $activeTab == 'kegiatan' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 py-1 fw-bold audit-subnav-btn audit-btn-kegiatan" style="font-size: 11px;">
                    <i data-lucide="briefcase" style="width: 13px; height: 13px; vertical-align: middle;"></i> Audit Kegiatan LPJ
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- TAB 1: RINGKASAN KEUANGAN RW                             -->
    <!-- ========================================================= -->
    @if($activeTab == 'ringkasan')
        <!-- 6 Kartu Informasi Kas RW & Status -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl-4">
                <div class="wm-card p-4 h-100 bg-white border rounded-4 shadow-sm position-relative overflow-hidden">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Saldo Kas RW 013</span>
                            <h3 class="fw-bold mb-0 text-primary">Rp {{ number_format($saldoKasRw, 0, ',', '.') }}</h3>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle">
                            <i data-lucide="wallet" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                    <small class="text-success fw-bold"><i data-lucide="shield-check" style="width: 13px; height: 13px; vertical-align: middle;"></i> Kas Operasional Murni RW 013</small>
                </div>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                    <span class="text-muted small fw-semibold d-block mb-1">Pemasukan RW Bulan Ini</span>
                    <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($pemasukanKasRwBulanIni, 0, ',', '.') }}</h5>
                    <small class="text-muted" style="font-size: 11px;">{{ Carbon\Carbon::now()->translatedFormat('F Y') }}</small>
                </div>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                    <span class="text-muted small fw-semibold d-block mb-1">Pengeluaran RW Bulan Ini</span>
                    <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($pengeluaranKasRwBulanIni, 0, ',', '.') }}</h5>
                    <small class="text-muted" style="font-size: 11px;">{{ Carbon\Carbon::now()->translatedFormat('F Y') }}</small>
                </div>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                    <span class="text-muted small fw-semibold d-block mb-1">Iuran RW Terkumpul</span>
                    <h5 class="fw-bold mb-0 text-primary">Rp {{ number_format($iuranRwTerkumpul, 0, ',', '.') }}</h5>
                    <small class="text-success" style="font-size: 11px;">Alokasi Bersih RW</small>
                </div>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                    <span class="text-muted small fw-semibold d-block mb-1">Iuran RW Belum Bayar</span>
                    <h5 class="fw-bold mb-0 text-warning-emphasis">Rp {{ number_format($iuranRwBelumBayar, 0, ',', '.') }}</h5>
                    <small class="text-danger" style="font-size: 11px;">Tunggakan Warga</small>
                </div>
            </div>
        </div>

        <!-- SEPARATE FINANCIAL ACCOUNT, CENTRALIZED MONITORING -->
        <!-- Banner Monitoring Terpisah Total Kas 8 RT (Tidak Masuk Saldo RW) -->
        <div class="alert border-2 rounded-4 p-3 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3 audit-monitoring-banner" 
             style="background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%); border-color: #93C5FD;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary text-white p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                    <i data-lucide="building" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary text-white fw-bold px-2.5 py-0.5 rounded-pill" style="font-size: 10px; letter-spacing: 0.5px;">
                            MONITORING TERPUSAT KAS 8 RT
                        </span>
                        <span class="badge bg-white text-dark fw-bold px-2 py-0.5 rounded-pill border" style="font-size: 10px;">
                            {{ $rtSudahLapor }} RT Lapor &bull; {{ $rtBelumLapor }} Belum Lapor
                        </span>
                    </div>
                    <h5 class="fw-bold mb-0 text-dark">
                        Total Akumulasi Saldo 8 RT: <span class="text-primary fw-black">Rp {{ number_format($totalKas8Rt, 0, ',', '.') }}</span>
                    </h5>
                    <small class="text-secondary d-block" style="font-size: 12px;">
                        <strong>Prinsip Isolasi Kas:</strong> Seluruh saldo kas RT merupakan dana otonom milik masing-masing RT (RT 001 - RT 008) dan <em>bukan</em> bagian dari saldo Kas RW.
                    </small>
                </div>
            </div>
            <div>
                <a href="{{ route('rw.audit.index', ['tab' => 'rekap_rt', 'tahun' => $tahun]) }}" class="btn btn-primary rounded-pill px-3 py-2 fw-bold small shadow-sm">
                    <i data-lucide="eye" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Periksa Rekap 8 RT
                </a>
            </div>
        </div>

        <!-- 4 GRAFIK UTAMA KEUANGAN (Chart.js) -->
        <div class="row g-4 mb-4">
            <!-- Grafik 1: Pemasukan & Pengeluaran Bulanan Kas RW -->
            <div class="col-12 col-xl-6">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Arus Kas RW Bulanan (Pemasukan vs Pengeluaran)</h6>
                            <small class="text-muted">Tren 6 bulan terakhir Kas RW 013</small>
                        </div>
                        <span class="badge bg-light text-dark border">Kas RW</span>
                    </div>
                    <div style="height: 280px; position: relative;">
                        <canvas id="chartKasRwCashflow"></canvas>
                    </div>
                </div>
            </div>

            <!-- Grafik 2: Perkembangan Saldo Kas RW -->
            <div class="col-12 col-xl-6">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Perkembangan Saldo Kas RW</h6>
                            <small class="text-muted">Pertumbuhan likuiditas kas operasional RW</small>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">Tren Saldo</span>
                    </div>
                    <div style="height: 280px; position: relative;">
                        <canvas id="chartKasRwSaldo"></canvas>
                    </div>
                </div>
            </div>

            <!-- Grafik 3: Persentase Pembayaran Iuran RW -->
            <div class="col-12 col-xl-4">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Kolektibilitas Iuran Warga</h6>
                            <small class="text-muted">Status partisipasi pembayaran warga</small>
                        </div>
                        <span class="badge bg-light text-dark border">Partisipasi</span>
                    </div>
                    <div style="height: 250px; position: relative;" class="d-flex align-items-center justify-content-center">
                        <canvas id="chartIuranStatus"></canvas>
                    </div>
                </div>
            </div>

            <!-- Grafik 4: Perbandingan Kondisi Keuangan Antar-RT -->
            <div class="col-12 col-xl-8">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Perbandingan Kondisi Keuangan Antar-RT (RT 001 - RT 008)</h6>
                            <small class="text-muted">Komparasi saldo dan akumulasi penerimaan kas masing-masing rukun tetangga</small>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success">Monitoring 8 RT</span>
                    </div>
                    <div style="height: 250px; position: relative;">
                        <canvas id="chartPerbandinganRt"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- TAB 2: KAS RW (Pencatatan & Pengelolaan Transaksi Kas RW)  -->
    <!-- ========================================================= -->
    @if($activeTab == 'kas_rw')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i data-lucide="wallet" class="me-2 text-primary" style="width: 22px; height: 22px; vertical-align: middle;"></i>
                        Buku Kas Utama RW 013
                    </h5>
                    <p class="mb-0 text-muted small">
                        Pencatatan penerimaan dan pengeluaran dana operasional RW 013. Saldo terhitung otomatis: Saldo akhir = Saldo awal + Pemasukan - Pengeluaran.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-success rounded-pill px-3 py-2 fw-bold small shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPemasukanRw">
                        <i data-lucide="plus-circle" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Tambah Pemasukan
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill px-3 py-2 fw-bold small shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPengeluaranRw">
                        <i data-lucide="minus-circle" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Tambah Pengeluaran
                    </button>
                </div>
            </div>

            <!-- Filter Kas RW -->
            <form method="GET" action="{{ route('rw.audit.index') }}" class="row g-2 mb-4 align-items-center">
                <input type="hidden" name="tab" value="kas_rw">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i data-lucide="search" style="width: 14px; height: 14px;"></i></span>
                        <input type="text" name="search_kas_rw" value="{{ request('search_kas_rw') }}" class="form-control border-start-0" placeholder="Cari transaksi, penyetor, nomor...">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="kategori_kas_rw" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriKasRwList as $kat)
                            <option value="{{ $kat }}" {{ request('kategori_kas_rw') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="jenis_kas_rw" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="pemasukan" {{ request('jenis_kas_rw') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="pengeluaran" {{ request('jenis_kas_rw') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 text-end">
                    <a href="{{ route('rw.audit.index', ['tab' => 'kas_rw', 'tahun' => $tahun]) }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <!-- Tabel Transaksi Kas RW -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold">No. Transaksi / Tanggal</th>
                            <th class="text-secondary small fw-bold">Jenis & Kategori</th>
                            <th class="text-secondary small fw-bold">Sumber / Tujuan</th>
                            <th class="text-secondary small fw-bold text-end">Nominal</th>
                            <th class="text-secondary small fw-bold text-center">Bukti</th>
                            <th class="text-secondary small fw-bold">Petugas</th>
                            <th class="text-secondary small fw-bold text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiKasRw as $trx)
                            <tr>
                                <td class="ps-3">
                                    <strong class="font-monospace small text-primary d-block">{{ $trx->nomor_transaksi }}</strong>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $trx->jenis_transaksi === 'pemasukan' ? 'bg-success text-white' : 'bg-danger text-white' }} px-2 py-1 rounded-pill small">
                                        {{ ucfirst($trx->jenis_transaksi) }}
                                    </span>
                                    <span class="d-block small text-dark mt-1 fw-bold">{{ $trx->kategori }}</span>
                                </td>
                                <td>
                                    <span class="small text-dark fw-semibold d-block">
                                        {{ $trx->sumber_dana ?: ($trx->tujuan_penggunaan ?: ($trx->penyetor_penerima ?: '-')) }}
                                    </span>
                                    @if($trx->keterangan)
                                        <small class="text-muted d-block" style="font-size: 11px;">{{ Str::limit($trx->keterangan, 40) }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $trx->jenis_transaksi === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                        {{ $trx->jenis_transaksi === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td class="text-center">
                                    @if($trx->bukti_transaksi_path)
                                        <a href="{{ asset('storage/' . $trx->bukti_transaksi_path) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none">
                                            <i data-lucide="paperclip" style="width: 12px; height: 12px; vertical-align: middle;"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-dark d-block fw-semibold">{{ $trx->nama_petugas ?: ($trx->petugas->name ?? 'Pengurus RW') }}</small>
                                    @if($trx->alasan_edit)
                                        <span class="badge bg-warning text-dark border" style="font-size: 9px;" title="Diedit: {{ $trx->alasan_edit }}">
                                            <i data-lucide="edit-2" style="width: 10px; height: 10px;"></i> Diedit
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDetailTrx{{ $trx->id }}" title="Detail Transaksi">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditTrx{{ $trx->id }}" title="Edit Transaksi">
                                            <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL DETAIL TRANSAKSI -->
                            <div class="modal fade" id="modalDetailTrx{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-bottom p-3">
                                            <h6 class="modal-title fw-bold">Detail Transaksi {{ $trx->nomor_transaksi }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3 small">
                                                <div class="col-6">
                                                    <span class="text-muted d-block">Tanggal:</span>
                                                    <strong>{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d F Y') }}</strong>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted d-block">Jenis:</span>
                                                    <span class="badge {{ $trx->jenis_transaksi === 'pemasukan' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ ucfirst($trx->jenis_transaksi) }}
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted d-block">Kategori:</span>
                                                    <strong>{{ $trx->kategori }}</strong>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted d-block">Nominal:</span>
                                                    <strong class="fs-6 {{ $trx->jenis_transaksi === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                                        Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                                    </strong>
                                                </div>
                                                <div class="col-12">
                                                    <span class="text-muted d-block">{{ $trx->jenis_transaksi === 'pemasukan' ? 'Sumber Dana:' : 'Tujuan Penggunaan:' }}</span>
                                                    <strong>{{ $trx->sumber_dana ?: ($trx->tujuan_penggunaan ?: ($trx->penyetor_penerima ?: '-')) }}</strong>
                                                </div>
                                                <div class="col-12">
                                                    <span class="text-muted d-block">Keterangan:</span>
                                                    <p class="mb-0 bg-light p-2 rounded-2">{{ $trx->keterangan ?: 'Tidak ada keterangan tambahan.' }}</p>
                                                </div>
                                                <div class="col-12">
                                                    <span class="text-muted d-block">Petugas Pencatat:</span>
                                                    <strong>{{ $trx->nama_petugas ?: ($trx->petugas->name ?? 'Pengurus RW') }}</strong>
                                                </div>
                                                @if($trx->alasan_edit)
                                                    <div class="col-12 bg-warning bg-opacity-10 p-3 rounded-3 border border-warning">
                                                        <strong class="text-warning-emphasis d-block mb-1"><i data-lucide="alert-triangle" style="width: 14px; height: 14px; vertical-align: middle;"></i> Riwayat Perubahan (Audit Log):</strong>
                                                        <span class="small d-block"><strong>Alasan:</strong> {{ $trx->alasan_edit }}</span>
                                                        <span class="small text-muted d-block">Diedit oleh: {{ $trx->editedBy->name ?? 'Admin' }} pada {{ $trx->edited_at ? \Carbon\Carbon::parse($trx->edited_at)->translatedFormat('d M Y H:i') : '-' }}</span>
                                                    </div>
                                                @endif
                                                @if($trx->bukti_transaksi_path)
                                                    <div class="col-12 text-center pt-2">
                                                        <a href="{{ asset('storage/' . $trx->bukti_transaksi_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                            <i data-lucide="external-link" style="width: 14px; height: 14px; vertical-align: middle;"></i> Buka Lampiran Bukti
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL EDIT TRANSAKSI (AUDIT TRAIL REQUIRED) -->
                            <div class="modal fade" id="modalEditTrx{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <form method="POST" action="{{ route('rw.audit.kas_rw.update', $trx->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header border-bottom p-3">
                                                <h6 class="modal-title fw-bold">Edit Transaksi {{ $trx->nomor_transaksi }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="alert alert-warning border-0 p-2 rounded-3 small mb-3">
                                                    <i data-lucide="info" style="width: 14px; height: 14px; vertical-align: middle;"></i>
                                                    Setiap perubahan transaksi akan tercatat dalam <strong>Audit Trail</strong> lengkap dengan identitas dan alasan perubahan.
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Alasan Perubahan (Wajib Diisi)*</label>
                                                    <textarea name="alasan_edit" class="form-control form-control-sm" rows="2" placeholder="Contoh: Koreksi nominal salah input dari nota faktur..." required>{{ old('alasan_edit', $trx->alasan_edit) }}</textarea>
                                                </div>

                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Tanggal Transaksi*</label>
                                                        <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" value="{{ $trx->tanggal_transaksi }}" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Nominal (Rp)*</label>
                                                        <input type="number" name="nominal" class="form-control form-control-sm" value="{{ (int)$trx->nominal }}" min="1000" required>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Kategori*</label>
                                                    <input type="text" name="kategori" class="form-control form-control-sm" value="{{ $trx->kategori }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">{{ $trx->jenis_transaksi === 'pemasukan' ? 'Sumber Dana' : 'Tujuan Penggunaan' }}</label>
                                                    <input type="text" name="{{ $trx->jenis_transaksi === 'pemasukan' ? 'sumber_dana' : 'tujuan_penggunaan' }}" 
                                                           class="form-control form-control-sm" 
                                                           value="{{ $trx->jenis_transaksi === 'pemasukan' ? $trx->sumber_dana : $trx->tujuan_penggunaan }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Metode Pembayaran*</label>
                                                    <select name="metode_pembayaran" class="form-select form-select-sm" required>
                                                        <option value="transfer_bank" {{ $trx->metode_pembayaran == 'transfer_bank' ? 'selected' : '' }}>Transfer Bank</option>
                                                        <option value="tunai" {{ $trx->metode_pembayaran == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                                        <option value="qris" {{ $trx->metode_pembayaran == 'qris' ? 'selected' : '' }}>QRIS</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Keterangan</label>
                                                    <textarea name="keterangan" class="form-control form-control-sm" rows="2">{{ $trx->keterangan }}</textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Unggah Bukti Baru (Opsional)</label>
                                                    <input type="file" name="bukti_transaksi" class="form-control form-control-sm" accept="image/*,.pdf">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top p-3">
                                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i data-lucide="inbox" style="width: 32px; height: 32px;" class="mx-auto mb-2 text-secondary opacity-50"></i>
                                    <p class="mb-0">Belum ada transaksi kas RW yang sesuai dengan filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Kas RW -->
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                <small class="text-muted">Menampilkan {{ $transaksiKasRw->count() }} transaksi</small>
                <div>{{ $transaksiKasRw->links() }}</div>
            </div>
        </div>

        <!-- MODAL TAMBAH PEMASUKAN RW -->
        <div class="modal fade" id="modalTambahPemasukanRw" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <form method="POST" action="{{ route('rw.audit.kas_rw.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="jenis_transaksi" value="pemasukan">
                        <div class="modal-header bg-success text-white p-3 rounded-top-4">
                            <h6 class="modal-title fw-bold"><i data-lucide="plus-circle" style="width: 16px; height: 16px; vertical-align: middle;"></i> Catat Pemasukan Kas RW</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Kategori Pemasukan*</label>
                                <select name="kategori" class="form-select form-select-sm" required>
                                    <option value="Iuran operasional RW">Iuran operasional RW</option>
                                    <option value="Donasi">Donasi</option>
                                    <option value="Bantuan">Bantuan Pemerintah / Swasta</option>
                                    <option value="Sumbangan">Sumbangan Sukarela</option>
                                    <option value="Pendapatan kegiatan">Pendapatan kegiatan</option>
                                    <option value="Pemasukan lainnya">Pemasukan lainnya</option>
                                </select>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Tanggal Transaksi*</label>
                                    <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Nominal (Rp)*</label>
                                    <input type="number" name="nominal" class="form-control form-control-sm" placeholder="Contoh: 500000" min="1000" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Sumber Dana</label>
                                <input type="text" name="sumber_dana" class="form-control form-control-sm" placeholder="Contoh: Kas Iuran RW / PT Telkom Donasi CSR / Warga Dermawan">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Metode Pembayaran*</label>
                                <select name="metode_pembayaran" class="form-select form-select-sm" required>
                                    <option value="transfer_bank">Transfer Bank</option>
                                    <option value="tunai">Tunai</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Keterangan / Deskripsi</label>
                                <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Catatan transaksi..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Bukti Transaksi (Kuitansi/Nota/Bukti Transfer)</label>
                                <input type="file" name="bukti_transaksi" class="form-control form-control-sm" accept="image/*,.pdf">
                            </div>
                        </div>
                        <div class="modal-footer border-top p-3">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-success fw-bold px-3">Simpan Pemasukan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH PENGELUARAN RW -->
        <div class="modal fade" id="modalTambahPengeluaranRw" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <form method="POST" action="{{ route('rw.audit.kas_rw.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="jenis_transaksi" value="pengeluaran">
                        <div class="modal-header bg-danger text-white p-3 rounded-top-4">
                            <h6 class="modal-title fw-bold"><i data-lucide="minus-circle" style="width: 16px; height: 16px; vertical-align: middle;"></i> Catat Pengeluaran Kas RW</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Kategori Pengeluaran*</label>
                                <select name="kategori" class="form-select form-select-sm" required>
                                    <option value="Operasional sekretariat RW">Operasional sekretariat RW</option>
                                    <option value="Kegiatan sosial RW">Kegiatan sosial RW</option>
                                    <option value="Kegiatan masyarakat">Kegiatan masyarakat</option>
                                    <option value="Administrasi">Administrasi & ATK</option>
                                    <option value="Perlengkapan">Perlengkapan Lingkungan</option>
                                    <option value="Keamanan tingkat RW">Keamanan tingkat RW (Honor Satpam/CCTV)</option>
                                    <option value="Pengeluaran lainnya">Pengeluaran lainnya</option>
                                </select>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Tanggal Transaksi*</label>
                                    <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Nominal (Rp)*</label>
                                    <input type="number" name="nominal" class="form-control form-control-sm" placeholder="Contoh: 250000" min="1000" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Tujuan Penggunaan</label>
                                <input type="text" name="tujuan_penggunaan" class="form-control form-control-sm" placeholder="Contoh: Pembayaran Honor Jaga Portal Malam / Pembelian Kertas & Tinta">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Metode Pembayaran*</label>
                                <select name="metode_pembayaran" class="form-select form-select-sm" required>
                                    <option value="transfer_bank">Transfer Bank</option>
                                    <option value="tunai">Tunai</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Keterangan / Deskripsi</label>
                                <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Catatan pengeluaran..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Bukti Transaksi (Kuitansi/Nota/Faktur)</label>
                                <input type="file" name="bukti_transaksi" class="form-control form-control-sm" accept="image/*,.pdf">
                            </div>
                        </div>
                        <div class="modal-footer border-top p-3">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-danger fw-bold px-3">Simpan Pengeluaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- TAB 3: REKAP KAS RT (Monitoring Sentral 8 RT Otonom)       -->
    <!-- ========================================================= -->
    @if($activeTab == 'rekap_rt')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i data-lucide="building-2" class="me-2 text-primary" style="width: 22px; height: 22px; vertical-align: middle;"></i>
                        Rekapitulasi Kas & Pertanggungjawaban 8 RT
                    </h5>
                    <p class="mb-0 text-muted small">
                        Pusat monitoring dan verifikasi laporan keuangan RT 001 s/d RT 008. Ketua RW berwenang memeriksa dan memverifikasi, tanpa memodifikasi transaksi RT.
                    </p>
                </div>
                <div class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                    <i data-lucide="shield" style="width: 14px; height: 14px; vertical-align: middle;" class="text-success me-1"></i>
                    Hak Akses: Monitoring & Verifikasi (Read-Only Transaksi RT)
                </div>
            </div>

            <!-- Tabel Daftar 8 RT -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold">Wilayah RT</th>
                            <th class="text-secondary small fw-bold">Ketua RT</th>
                            <th class="text-secondary small fw-bold text-end">Saldo Kas Dilaporkan</th>
                            <th class="text-secondary small fw-bold text-end">Pemasukan Thn Ini</th>
                            <th class="text-secondary small fw-bold text-end">Pengeluaran Thn Ini</th>
                            <th class="text-secondary small fw-bold text-center">Status Laporan</th>
                            <th class="text-secondary small fw-bold text-center">Verifikasi RW</th>
                            <th class="text-secondary small fw-bold text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapKasRt as $rtItem)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge px-3 py-1 rounded-pill fw-bold" style="background: #E8F5E9; color: #16865D; border: 1px solid #C8E6C9;">
                                        {{ $rtItem['rt_label'] }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark d-block small">{{ $rtItem['pic_name'] }}</strong>
                                    <small class="text-muted">{{ $rtItem['tx_count'] }} transaksi tercatat</small>
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary fs-6">Rp {{ number_format($rtItem['saldo'], 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <span class="text-success small fw-semibold">+Rp {{ number_format($rtItem['total_masuk'], 0, ',', '.') }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-danger small fw-semibold">-Rp {{ number_format($rtItem['total_keluar'], 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="wm-badge {{ $rtItem['status_laporan_badge']['bg'] }}" style="{{ $rtItem['status_laporan_badge']['style'] }}">
                                        {{ $rtItem['status_laporan_badge']['label'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="wm-badge {{ $rtItem['status_verifikasi_badge']['bg'] }}" style="{{ $rtItem['status_verifikasi_badge']['style'] }}">
                                        {{ $rtItem['status_verifikasi_badge']['label'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAuditRt{{ $rtItem['rt_num'] }}">
                                        <i data-lucide="search" style="width: 13px; height: 13px; vertical-align: middle;"></i> Periksa & Verifikasi
                                    </button>
                                </td>
                            </tr>

                            <!-- MODAL DETAIL & VERIFIKASI RT -->
                            <div class="modal fade" id="modalAuditRt{{ $rtItem['rt_num'] }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-bottom p-3">
                                            <h6 class="modal-title fw-bold">Laporan & Ledger Keuangan {{ $rtItem['rt_label'] }} RW 013</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <!-- Ringkasan Kas RT -->
                                            <div class="row g-3 mb-4">
                                                <div class="col-4">
                                                    <div class="p-3 rounded-3 bg-light text-center border">
                                                        <span class="text-muted small d-block">Saldo Kas RT</span>
                                                        <strong class="text-primary fs-6">Rp {{ number_format($rtItem['saldo'], 0, ',', '.') }}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="p-3 rounded-3 bg-light text-center border">
                                                        <span class="text-muted small d-block">Total Penerimaan</span>
                                                        <strong class="text-success fs-6">Rp {{ number_format($rtItem['total_masuk'], 0, ',', '.') }}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="p-3 rounded-3 bg-light text-center border">
                                                        <span class="text-muted small d-block">Total Pengeluaran</span>
                                                        <strong class="text-danger fs-6">Rp {{ number_format($rtItem['total_keluar'], 0, ',', '.') }}</strong>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Transaksi Terakhir RT (Read Only) -->
                                            <h6 class="fw-bold mb-2 small text-uppercase text-secondary">8 Transaksi Terakhir Kas {{ $rtItem['rt_label'] }}</h6>
                                            <div class="table-responsive border rounded-3 mb-4">
                                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 12px;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Kategori</th>
                                                            <th>Keterangan</th>
                                                            <th class="text-end">Nominal</th>
                                                            <th class="text-center">Bukti</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($rtItem['transaksis'] as $rtTx)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($rtTx->tanggal_transaksi)->format('d/m/Y') }}</td>
                                                                <td><span class="badge {{ $rtTx->jenis_transaksi === 'pemasukan' ? 'bg-success' : 'bg-danger' }}">{{ $rtTx->kategori }}</span></td>
                                                                <td>{{ Str::limit($rtTx->keterangan ?: '-', 30) }}</td>
                                                                <td class="text-end fw-bold {{ $rtTx->jenis_transaksi === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                                                    {{ $rtTx->jenis_transaksi === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($rtTx->nominal, 0, ',', '.') }}
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($rtTx->bukti_transaksi_path)
                                                                        <a href="{{ asset('storage/' . $rtTx->bukti_transaksi_path) }}" target="_blank" class="text-primary"><i data-lucide="paperclip" style="width: 12px; height: 12px;"></i></a>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center py-2 text-muted">Belum ada transaksi di periode ini.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Form Verifikasi RW -->
                                            <div class="p-3 rounded-4 bg-light border">
                                                <h6 class="fw-bold mb-2 text-dark"><i data-lucide="check-square" style="width: 16px; height: 16px; vertical-align: middle;"></i> Verifikasi & Catatan Ketua RW</h6>
                                                <form method="POST" action="{{ route('rw.audit.rekap_rt.verifikasi', $rtItem['rt_number']) }}">
                                                    @csrf
                                                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                                                    
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">Status Verifikasi*</label>
                                                            <select name="status_verifikasi" class="form-select form-select-sm" required>
                                                                <option value="Terverifikasi" {{ $rtItem['status_verifikasi'] === 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi (Disetujui & Dikunci)</option>
                                                                <option value="Perlu Klarifikasi" {{ $rtItem['status_verifikasi'] === 'Perlu Klarifikasi' ? 'selected' : '' }}>Perlu Klarifikasi (Minta Penjelasan RT)</option>
                                                                <option value="Menunggu Pemeriksaan" {{ $rtItem['status_verifikasi'] === 'Menunggu Pemeriksaan' ? 'selected' : '' }}>Menunggu Pemeriksaan</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">Terakhir Diverifikasi</label>
                                                            <input type="text" class="form-control form-control-sm bg-white" readonly value="{{ $rtItem['diverifikasi_pada'] ? \Carbon\Carbon::parse($rtItem['diverifikasi_pada'])->translatedFormat('d M Y H:i') : 'Belum Diverifikasi' }}">
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Catatan Evaluasi / Rekomendasi RW</label>
                                                        <textarea name="catatan_verifikasi" class="form-control form-control-sm" rows="2" placeholder="Catatan hasil verifikasi atau hal yang perlu diklarifikasi...">{{ $rtItem['catatan_verifikasi'] }}</textarea>
                                                    </div>

                                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                                                        <i data-lucide="save" style="width: 14px; height: 14px; vertical-align: middle;"></i> Simpan Verifikasi Laporan
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- TAB 4: MONITORING IURAN (Tarif, Target, Tunggakan, Kolektibilitas) -->
    <!-- ========================================================= -->
    @if($activeTab == 'monitoring_iuran')
        <!-- KPI Monitoring Iuran -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                    <span class="text-muted small fw-semibold d-block mb-1">Target Pembayaran</span>
                    <h5 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalTargetIuran, 0, ',', '.') }}</h5>
                    <small class="text-muted" style="font-size: 11px;">Tahun {{ $tahun }}</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                    <span class="text-muted small fw-semibold d-block mb-1">Penerimaan Masuk</span>
                    <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($totalTerkumpulIuran, 0, ',', '.') }}</h5>
                    <small class="text-success" style="font-size: 11px;">Iuran Warga Terkumpul</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                    <span class="text-muted small fw-semibold d-block mb-1">Total Tunggakan</span>
                    <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalTunggakanIuran, 0, ',', '.') }}</h5>
                    <small class="text-danger" style="font-size: 11px;">Belum Terbayarkan</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                    <span class="text-muted small fw-semibold d-block mb-1">Kolektibilitas Lingkungan</span>
                    <h5 class="fw-bold mb-0 text-primary">{{ $persenKolektibilitasTotal }}%</h5>
                    <small class="text-muted" style="font-size: 11px;">Rata-rata Kepatuhan</small>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Tabel 1: Konfigurasi Tarif Iuran Warga (One Billing Multi Allocation) -->
            <div class="col-12 col-xl-5">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i data-lucide="tag" class="me-1 text-primary" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                            Konfigurasi Komponen Tagihan & Alokasi
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2 small fw-bold">Komponen</th>
                                    <th class="small fw-bold">Metode Hitung</th>
                                    <th class="small fw-bold">Kas Tujuan</th>
                                    <th class="small fw-bold text-end">Tarif Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tarifIuranList as $tarif)
                                    <tr>
                                        <td class="ps-2">
                                            <strong class="text-dark d-block small">{{ $tarif->nama_tarif }}</strong>
                                            <small class="font-monospace text-muted">{{ $tarif->kode_tarif }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border rounded-pill" style="font-size: 10px;">
                                                {{ $tarif->metode_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary rounded-pill" style="font-size: 10px;">
                                                {{ $tarif->bukuKas->nama_kas ?? strtoupper(str_replace('_', ' ', $tarif->jenis_kas)) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-dark small">
                                            Rp {{ number_format($tarif->nominal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-3" style="font-size: 11px;">
                        *Sistem One Billing menggabungkan seluruh komponen aktif di atas ke dalam 1 invoice KK dengan alokasi otomatis.
                    </small>
                </div>
            </div>

            <!-- Tabel 2: Rekap Kolektibilitas Per RT -->
            <div class="col-12 col-xl-7">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i data-lucide="bar-chart-2" class="me-1 text-primary" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                            Rekapitulasi Kolektibilitas 8 RT (Tahun {{ $tahun }})
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2 small fw-bold">RT</th>
                                    <th class="small fw-bold text-end">Target</th>
                                    <th class="small fw-bold text-end">Terkumpul</th>
                                    <th class="small fw-bold text-end">Tunggakan</th>
                                    <th class="small fw-bold text-center">Kepatuhan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapIuranRt as $iuranRt)
                                    <tr>
                                        <td class="ps-2">
                                            <strong class="text-dark">{{ $iuranRt['rt_label'] }}</strong>
                                            <small class="text-muted d-block">{{ $iuranRt['total_kk'] }} KK</small>
                                        </td>
                                        <td class="text-end small">Rp {{ number_format($iuranRt['target'], 0, ',', '.') }}</td>
                                        <td class="text-end small text-success fw-bold">Rp {{ number_format($iuranRt['terkumpul'], 0, ',', '.') }}</td>
                                        <td class="text-end small text-danger">Rp {{ number_format($iuranRt['tunggakan'], 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $iuranRt['persen'] >= 80 ? 'bg-success' : ($iuranRt['persen'] >= 60 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill">
                                                {{ $iuranRt['persen'] }}%
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
    @endif

    <!-- ========================================================= -->
    <!-- TAB 5: LAPORAN KEUANGAN (Bulanan, Tahunan, Cetak PDF)       -->
    <!-- ========================================================= -->
    @if($activeTab == 'laporan')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i data-lucide="file-text" class="me-2 text-primary" style="width: 22px; height: 22px; vertical-align: middle;"></i>
                        Laporan Pertanggungjawaban Keuangan RW 013 (Tahun {{ $tahun }})
                    </h5>
                    <p class="mb-0 text-muted small">
                        Rekapitulasi arus kas bulanan Kas RW 013 serta kompilasi laporan realisasi anggaran wilayah.
                    </p>
                </div>
                <div>
                    <a href="{{ route('rw.audit.cetak', ['tahun' => $tahun]) }}" target="_blank" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                        <i data-lucide="printer" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Cetak Laporan Resmi (PDF)
                    </a>
                </div>
            </div>

            <!-- Tabel Arus Kas Bulanan Kas RW -->
            <h6 class="fw-bold text-dark mb-3">1. Arus Kas Bulanan Kas RW 013</h6>
            <div class="table-responsive mb-5 border rounded-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 small fw-bold">Bulan</th>
                            <th class="small fw-bold text-end">Pemasukan</th>
                            <th class="small fw-bold text-end">Pengeluaran</th>
                            <th class="small fw-bold text-end pe-3">Surplus / Defisit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sumIn = 0; $sumOut = 0;
                        @endphp
                        @foreach($laporanBulananKasRw as $lap)
                            @php
                                $sumIn += $lap['pemasukan'];
                                $sumOut += $lap['pengeluaran'];
                            @endphp
                            <tr>
                                <td class="ps-3 fw-bold text-dark">{{ $lap['bulan_name'] }}</td>
                                <td class="text-end text-success font-monospace">Rp {{ number_format($lap['pemasukan'], 0, ',', '.') }}</td>
                                <td class="text-end text-danger font-monospace">Rp {{ number_format($lap['pengeluaran'], 0, ',', '.') }}</td>
                                <td class="text-end pe-3 fw-bold {{ $lap['surplus_defisit'] >= 0 ? 'text-success' : 'text-danger' }} font-monospace">
                                    {{ $lap['surplus_defisit'] >= 0 ? '+' : '' }}Rp {{ number_format($lap['surplus_defisit'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="table-light fw-bold">
                            <td class="ps-3 text-uppercase">Total Akumulasi</td>
                            <td class="text-end text-success font-monospace">Rp {{ number_format($sumIn, 0, ',', '.') }}</td>
                            <td class="text-end text-danger font-monospace">Rp {{ number_format($sumOut, 0, ',', '.') }}</td>
                            <td class="text-end pe-3 font-monospace {{ ($sumIn - $sumOut) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($sumIn - $sumOut) >= 0 ? '+' : '' }}Rp {{ number_format($sumIn - $sumOut, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tabel Kompilasi Realisasi Kas 8 RT -->
            <h6 class="fw-bold text-dark mb-3">2. Kompilasi Realisasi Kas 8 RT (Buku Kas Mandiri)</h6>
            <div class="table-responsive border rounded-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 small fw-bold">Entitas Kas</th>
                            <th class="small fw-bold">Ketua RT</th>
                            <th class="small fw-bold text-end">Pemasukan</th>
                            <th class="small fw-bold text-end">Pengeluaran</th>
                            <th class="small fw-bold text-end pe-3">Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapKasRt as $rtL)
                            <tr>
                                <td class="ps-3 fw-bold text-dark">{{ $rtL['rt_label'] }}</td>
                                <td>{{ $rtL['pic_name'] }}</td>
                                <td class="text-end text-success small font-monospace">Rp {{ number_format($rtL['total_masuk'], 0, ',', '.') }}</td>
                                <td class="text-end text-danger small font-monospace">Rp {{ number_format($rtL['total_keluar'], 0, ',', '.') }}</td>
                                <td class="text-end pe-3 fw-bold text-primary font-monospace">Rp {{ number_format($rtL['saldo'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- TAB 6: RIWAYAT TRANSAKSI (AUDIT TRAIL & PERUBAHAN)        -->
    <!-- ========================================================= -->
    @if($activeTab == 'riwayat')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i data-lucide="history" class="me-2 text-primary" style="width: 22px; height: 22px; vertical-align: middle;"></i>
                        Audit Trail & Log Riwayat Perubahan Keuangan
                    </h5>
                    <p class="mb-0 text-muted small">
                        Rekam jejak digital seluruh aktivitas pencatatan, pengubahan transaksi, koreksi, dan verifikasi laporan keuangan.
                    </p>
                </div>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
                    <i data-lucide="lock" style="width: 12px; height: 12px; vertical-align: middle;" class="text-primary me-1"></i> Log Permanen (Append-Only)
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 small fw-bold">Waktu</th>
                            <th class="small fw-bold">Pengguna</th>
                            <th class="small fw-bold">Aksi / Event</th>
                            <th class="small fw-bold">Deskripsi & Rincian</th>
                            <th class="small fw-bold text-end pe-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditLogs as $log)
                            <tr>
                                <td class="ps-3 font-monospace small text-muted">
                                    {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    <strong class="text-dark small d-block">{{ $log->user->name ?? 'Sistem' }}</strong>
                                    <span class="badge bg-light text-secondary border" style="font-size: 10px;">{{ strtoupper($log->user->role ?? 'System') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary small">
                                        {{ str_replace('_', ' ', strtoupper($log->action)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-dark d-block">{{ $log->deskripsi }}</span>
                                    @if(!empty($log->old_values))
                                        <details class="mt-1 small text-muted">
                                            <summary class="cursor-pointer text-primary" style="font-size: 11px;">Rincian Data Sebelum/Sesudah</summary>
                                            <div class="p-2 bg-light rounded-2 mt-1 font-monospace" style="font-size: 11px;">
                                                <strong>Sebelum:</strong> {{ json_encode($log->old_values) }}<br>
                                                <strong>Sesudah:</strong> {{ json_encode($log->new_values) }}
                                            </div>
                                        </details>
                                    @endif
                                </td>
                                <td class="text-end pe-3 font-monospace small text-muted">
                                    {{ $log->ip_address ?: '127.0.0.1' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i data-lucide="check-circle" style="width: 24px; height: 24px;" class="mx-auto mb-1 text-success opacity-50"></i>
                                    <p class="mb-0">Belum ada aktivitas audit log.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                <small class="text-muted">Menampilkan {{ $auditLogs->count() }} log aktivitas</small>
                <div>{{ $auditLogs->links() }}</div>
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- PRESERVED TAB: TEMUAN AUDIT                               -->
    <!-- ========================================================= -->
    @if($activeTab == 'temuan')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i data-lucide="alert-circle" class="me-2 text-danger" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Register Temuan Audit (Audit Findings)
                    </h5>
                    <p class="mb-0 text-muted small">
                        Daftar temuan hasil pemeriksaan Ketua RW dan Auditor resmi serta status tindak lanjut klarifikasi RT.
                    </p>
                </div>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahTemuan">
                    <i data-lucide="plus" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Terbitkan Temuan Baru
                </button>
            </div>

            <!-- Tabel Daftar Temuan Audit -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold">Nomor & Judul Temuan</th>
                            <th class="text-secondary small fw-bold">Kategori</th>
                            <th class="text-secondary small fw-bold text-center">Prioritas</th>
                            <th class="text-secondary small fw-bold text-center">Wilayah Terkait</th>
                            <th class="text-secondary small fw-bold text-center">Tenggat Waktu</th>
                            <th class="text-secondary small fw-bold text-center">Status</th>
                            <th class="text-secondary small fw-bold text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarTemuan as $temuan)
                            <tr>
                                <td class="ps-3">
                                    <strong class="font-monospace small text-primary d-block">{{ $temuan->kode_temuan }}</strong>
                                    <strong class="text-dark small d-block">{{ $temuan->judul_temuan }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px;">
                                        {{ ucwords(str_replace('_', ' ', $temuan->kategori_masalah)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="wm-badge {{ $temuan->prioritas_badge['bg'] }}" style="{{ $temuan->prioritas_badge['style'] }}">
                                        {{ $temuan->prioritas_badge['label'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge px-2 py-1 rounded-pill" style="background: #E8F5E9; color: #16865D; border: 1px solid #C8E6C9;">
                                        RT {{ $temuan->rt_number }}
                                    </span>
                                </td>
                                <td class="text-center small text-muted">
                                    {{ $temuan->tenggat_tindak_lanjut ? \Carbon\Carbon::parse($temuan->tenggat_tindak_lanjut)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="wm-badge {{ $temuan->status_badge['bg'] }}" style="{{ $temuan->status_badge['style'] }}">
                                        {{ $temuan->status_badge['label'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalStatusTemuan{{ $temuan->id }}">
                                        Kelola & Status
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i data-lucide="check-circle" style="width: 32px; height: 32px;" class="mx-auto mb-2 text-success opacity-50"></i>
                                    <p class="mb-0">Tidak ada temuan audit terbuka. Seluruh pertanggungjawaban dalam kondisi tertib.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- PRESERVED TAB: EVALUASI & REKOMENDASI RT                  -->
    <!-- ========================================================= -->
    @if($activeTab == 'evaluasi')
        <div class="row g-4 mb-4">
            <!-- Form Input Skor & Evaluasi RT -->
            <div class="col-12 col-xl-5">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i data-lucide="edit-3" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Form Penetapan Skor & Evaluasi RT
                    </h5>
                    <form method="POST" action="{{ route('rw.audit.evaluasi.store') }}">
                        @csrf
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <input type="hidden" name="periode" value="Tahunan">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">Pilih Wilayah RT</label>
                            <select name="rt_number" class="form-select rounded-3" required>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}">RT {{ str_pad($i, 3, '0', STR_PAD_LEFT) }} - RW 013</option>
                                @endfor
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-dark mb-1">Skor Kepatuhan (0-100)</label>
                                <input type="number" step="0.5" name="skor_kepatuhan" class="form-control rounded-3" placeholder="Contoh: 88.5" min="0" max="100" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-dark mb-1">Status Kepatuhan</label>
                                <select name="status_pertanggungjawaban" class="form-select rounded-3" required>
                                    <option value="sangat_baik">Sangat Baik</option>
                                    <option value="baik" selected>Baik</option>
                                    <option value="cukup">Cukup</option>
                                    <option value="perlu_pembinaan">Perlu Pembinaan</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">Catatan Evaluasi Ketua RW</label>
                            <textarea name="catatan_evaluasi" class="form-control rounded-3" rows="3" placeholder="Pencatatan kas dan administrasi berjalan tertib..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">Rekomendasi Perbaikan</label>
                            <textarea name="rekomendasi_rw" class="form-control rounded-3" rows="2" placeholder="Pertahankan konsistensi pelaporan bulanan..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark mb-1">Tenggat Perbaikan (Jika ada)</label>
                            <input type="date" name="tenggat_perbaikan" class="form-control rounded-3">
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2">
                            <i data-lucide="save" style="width: 16px; height: 16px; vertical-align: middle;" class="me-1"></i> Simpan Evaluasi RT
                        </button>
                    </form>
                </div>
            </div>

            <!-- Daftar Evaluasi Seluruh RT -->
            <div class="col-12 col-xl-7">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i data-lucide="award" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                            Daftar Nilai & Rekomendasi 8 RT (Tahun {{ $tahun }})
                        </h5>
                        <a href="{{ route('rw.audit.cetak', ['tahun' => $tahun]) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold">
                            <i data-lucide="printer" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Cetak LHA (PDF)
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-secondary small fw-bold">Wilayah RT</th>
                                    <th class="text-secondary small fw-bold text-center">Skor</th>
                                    <th class="text-secondary small fw-bold text-center">Status</th>
                                    <th class="text-secondary small fw-bold">Catatan & Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scorecardRt as $itemSc)
                                    <tr>
                                        <td class="ps-3">
                                            <strong class="text-dark d-block">{{ $itemSc['rt_name'] ?? $itemSc['rt_label'] }}</strong>
                                            <small class="text-muted">{{ $itemSc['pic_name'] }}</small>
                                        </td>
                                        <td class="text-center">
                                            <strong class="fs-6 text-dark">{{ $itemSc['skor'] }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="wm-badge {{ $itemSc['status_badge'] }}" style="{{ $itemSc['status_style'] ?? '' }}">
                                                {{ $itemSc['status_label'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-dark d-block"><strong>Catatan:</strong> {{ $itemSc['catatan'] ?? ($itemSc['catatan_verifikasi'] ?: 'Pencatatan kas dan administrasi berjalan tertib.') }}</small>
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

    <!-- ========================================================= -->
    <!-- PRESERVED TAB: AUDIT KEGIATAN & PROYEK                    -->
    <!-- ========================================================= -->
    @if($activeTab == 'kegiatan')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                <i data-lucide="briefcase" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                Audit Kegiatan & Pertanggungjawaban LPJ Proyek Lingkungan
            </h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 small fw-bold">Nama Kegiatan</th>
                            <th class="small fw-bold">Wilayah RT</th>
                            <th class="small fw-bold text-end">Anggaran Awal</th>
                            <th class="small fw-bold text-end">Realisasi</th>
                            <th class="small fw-bold text-center">Status LPJ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarKegiatan as $kg)
                            <tr>
                                <td class="ps-3">
                                    <strong class="text-dark d-block small">{{ $kg->nama_kegiatan }}</strong>
                                    <small class="text-muted">{{ $kg->tanggal_kegiatan ? \Carbon\Carbon::parse($kg->tanggal_kegiatan)->translatedFormat('d M Y') : '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge px-2 py-1 rounded-pill" style="background: #E8F5E9; color: #16865D;">RT {{ $kg->rt_number }}</span>
                                </td>
                                <td class="text-end small">Rp {{ number_format($kg->anggaran_awal, 0, ',', '.') }}</td>
                                <td class="text-end small fw-bold text-primary">Rp {{ number_format($kg->realisasi_anggaran, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $kg->status_audit === 'sesuai' ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill small">
                                        {{ ucwords(str_replace('_', ' ', $kg->status_audit)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada kegiatan proyek yang diaudit di tahun {{ $tahun }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- ========================================================= -->
<!-- JAVASCRIPT & CHART.JS INITIALIZATION                      -->
<!-- ========================================================= -->
@if($activeTab == 'ringkasan')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Chart Pemasukan vs Pengeluaran Bulanan Kas RW
    const ctxCashflow = document.getElementById('chartKasRwCashflow');
    if (ctxCashflow) {
        new Chart(ctxCashflow, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartBulanLabels) !!},
                datasets: [
                    {
                        label: 'Pemasukan Kas RW',
                        data: {!! json_encode($chartKasRwMasuk) !!},
                        backgroundColor: '#16865D',
                        borderRadius: 6,
                    },
                    {
                        label: 'Pengeluaran Kas RW',
                        data: {!! json_encode($chartKasRwKeluar) !!},
                        backgroundColor: '#DC2626',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + Number(context.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            },
                            font: { size: 10 }
                        }
                    },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    // 2. Chart Saldo Kas RW
    const ctxSaldo = document.getElementById('chartKasRwSaldo');
    if (ctxSaldo) {
        new Chart(ctxSaldo, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartBulanLabels) !!},
                datasets: [{
                    label: 'Saldo Kas RW (Rp)',
                    data: {!! json_encode($chartKasRwSaldo) !!},
                    borderColor: '#1456A0',
                    backgroundColor: 'rgba(20, 86, 160, 0.1)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#1456A0',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Saldo: Rp ' + Number(context.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            },
                            font: { size: 10 }
                        }
                    },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    // 3. Chart Status Pembayaran Iuran RW
    const ctxIuran = document.getElementById('chartIuranStatus');
    if (ctxIuran) {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark' || document.body.classList.contains('dark-mode');
        new Chart(ctxIuran, {
            type: 'doughnut',
            data: {
                labels: ['Lunas', 'Belum Bayar / Terlambat', 'Menunggu Konfirmasi'],
                datasets: [{
                    data: [{{ $countIuranLunas }}, {{ $countIuranBelum }}, {{ $countIuranMenunggu }}],
                    backgroundColor: ['#16865D', '#DC2626', '#F59E0B'],
                    borderWidth: 2,
                    borderColor: isDark ? '#131F35' : '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                },
                cutout: '68%'
            }
        });
    }

    // 4. Chart Perbandingan Saldo & Kondisi Antar-RT
    const ctxPerbandingan = document.getElementById('chartPerbandinganRt');
    if (ctxPerbandingan) {
        new Chart(ctxPerbandingan, {
            type: 'bar',
            data: {
                labels: {!! json_encode($rtChartLabels) !!},
                datasets: [
                    {
                        label: 'Saldo Kas RT',
                        data: {!! json_encode($rtChartSaldo) !!},
                        backgroundColor: '#1456A0',
                        borderRadius: 6,
                    },
                    {
                        label: 'Akumulasi Pemasukan',
                        data: {!! json_encode($rtChartInflow) !!},
                        backgroundColor: '#10B981',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + Number(context.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            },
                            font: { size: 10 }
                        }
                    },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });
    }
});
</script>
@endif

@endsection
