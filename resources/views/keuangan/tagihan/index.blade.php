@extends('layouts.app')

@section('title', 'Manajemen Tagihan Iuran Warga')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- ======================================================================
         1. HEADER HALAMAN
         ====================================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                    <i data-lucide="layers" style="width: 13px; height: 13px; vertical-align: middle;"></i> One Billing System
                </span>
                <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                    Periode Aktif: {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                </span>
                @if($isRt)
                    <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                        Wilayah RT {{ $rtStr }}
                    </span>
                @endif
            </div>
            <h3 class="fw-bold text-dark mb-1">Manajemen Tagihan Iuran Warga</h3>
            <p class="text-muted small mb-0">
                Satu tagihan gabungan terpadu per KK dengan alokasi otomatis ke Kas RT, Kas RW, Retribusi Sampah & Kegiatan Lingkungan.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Tombol Ekspor Data --}}
            <a href="{{ route('tagihan.index', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                <i data-lucide="download" style="width: 14px; height: 14px;"></i> Ekspor CSV
            </a>

            {{-- Tombol Cetak Laporan --}}
            <a href="{{ route('tagihan.cetak', ['periode' => $periode, 'rt' => $rtFilter]) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak Laporan
            </a>

            {{-- Tombol Buat Tagihan (Khusus RW / Pengurus) --}}
            @if($isRw)
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGenerateTagihan">
                    <i data-lucide="plus-circle" style="width: 14px; height: 14px;"></i> Buat Tagihan Baru
                </button>
            @endif
        </div>
    </div>

    {{-- Notifikasi Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4" role="alert" style="background-color: #ECFDF5; color: #065F46; border-left: 4px solid #10B981 !important;">
            <i data-lucide="check-circle-2" style="width: 22px; height: 22px; color: #10B981;" class="flex-shrink-0"></i>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4" role="alert" style="background-color: #FEF2F2; color: #991B1B; border-left: 4px solid #EF4444 !important;">
            <i data-lucide="alert-triangle" style="width: 22px; height: 22px; color: #EF4444;" class="flex-shrink-0"></i>
            <div>
                <strong>Perhatian:</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4" role="alert" style="background-color: #EFF6FF; color: #1E40AF; border-left: 4px solid #3B82F6 !important;">
            <i data-lucide="info" style="width: 22px; height: 22px; color: #3B82F6;" class="flex-shrink-0"></i>
            <div>
                <strong>Informasi:</strong> {{ session('info') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ======================================================================
         2. KARTU RINGKASAN KEUANGAN (5 STATISTIC CARDS)
         ====================================================================== --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Tagihan --}}
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card h-100 border-0 rounded-4 shadow-sm p-3" style="border-top: 3px solid #1456A0 !important; background: #FFFFFF;">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem;">Total Tagihan</span>
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="background: #EFF6FF; color: #1456A0; width: 34px; height: 34px;">
                        <i data-lucide="file-text" style="width: 17px; height: 17px;"></i>
                    </div>
                </div>
                <div class="fs-4 fw-black text-dark mt-2 mb-1">
                    Rp {{ number_format($totalTagihanNominal, 0, ',', '.') }}
                </div>
                <div class="text-muted small" style="font-size: 0.75rem;">
                    Seluruh kewajiban KK terfilter
                </div>
            </div>
        </div>

        {{-- Card 2: Sudah Dibayar --}}
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card h-100 border-0 rounded-4 shadow-sm p-3" style="border-top: 3px solid #16865D !important; background: #FFFFFF;">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="text-success small fw-bold text-uppercase" style="font-size: 0.72rem;">Sudah Dibayar</span>
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="background: #ECFDF5; color: #16865D; width: 34px; height: 34px;">
                        <i data-lucide="check-circle-2" style="width: 17px; height: 17px;"></i>
                    </div>
                </div>
                <div class="fs-4 fw-black text-success mt-2 mb-1">
                    Rp {{ number_format($totalTerkumpulNominal, 0, ',', '.') }}
                </div>
                <div class="text-muted small" style="font-size: 0.75rem;">
                    Teralokasi otomatis ke kas tujuan
                </div>
            </div>
        </div>

        {{-- Card 3: Belum Dibayar --}}
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card h-100 border-0 rounded-4 shadow-sm p-3" style="border-top: 3px solid #DC2626 !important; background: #FFFFFF;">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="text-danger small fw-bold text-uppercase" style="font-size: 0.72rem;">Belum Dibayar</span>
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="background: #FEF2F2; color: #DC2626; width: 34px; height: 34px;">
                        <i data-lucide="alert-circle" style="width: 17px; height: 17px;"></i>
                    </div>
                </div>
                <div class="fs-4 fw-black text-danger mt-2 mb-1">
                    Rp {{ number_format($totalBelumDibayarNominal, 0, ',', '.') }}
                </div>
                <div class="text-muted small" style="font-size: 0.75rem;">
                    Sisa piutang iuran warga
                </div>
            </div>
        </div>

        {{-- Card 4: Menunggu Verifikasi --}}
        <div class="col-xl col-md-6 col-sm-6">
            <div class="card h-100 border-0 rounded-4 shadow-sm p-3" style="border-top: 3px solid #D97706 !important; background: #FFFFFF;">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="text-warning small fw-bold text-uppercase" style="font-size: 0.72rem; color: #B45309 !important;">Menunggu Verifikasi</span>
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="background: #FFFBEB; color: #D97706; width: 34px; height: 34px;">
                        <i data-lucide="clock" style="width: 17px; height: 17px;"></i>
                    </div>
                </div>
                <div class="fs-4 fw-black text-dark mt-2 mb-1">
                    Rp {{ number_format($totalMenungguNominal, 0, ',', '.') }}
                </div>
                <div class="text-muted small" style="font-size: 0.75rem;">
                    <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill">{{ $countMenunggu }} tagihan</span> perlu verifikasi
                </div>
            </div>
        </div>

        {{-- Card 5: Persentase Kolektivitas --}}
        <div class="col-xl col-md-6 col-sm-12">
            <div class="card h-100 border-0 rounded-4 shadow-sm p-3" style="border-top: 3px solid #2563EB !important; background: #FFFFFF;">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="text-primary small fw-bold text-uppercase" style="font-size: 0.72rem;">Kolektivitas</span>
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="background: #EFF6FF; color: #2563EB; width: 34px; height: 34px;">
                        <i data-lucide="pie-chart" style="width: 17px; height: 17px;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mt-2 mb-1">
                    <span class="fs-4 fw-black text-primary">{{ $kolektivitasPersen }}%</span>
                    <span class="text-muted small" style="font-size: 0.75rem;">terkumpul</span>
                </div>
                <div class="progress" style="height: 6px; border-radius: 9999px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $kolektivitasPersen) }}%;" aria-valuenow="{{ $kolektivitasPersen }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         3. RINCIAN KOMPOSISI TAGIHAN (DYNAMIC TARIFF BREAKDOWN & SPLIT BAR)
         ====================================================================== --}}
    <div class="card border-0 rounded-4 shadow-sm p-4 mb-4" style="background: #FFFFFF;">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i data-lucide="sliders" class="text-primary" style="width: 20px; height: 20px;"></i>
                    Komposisi 1 Tagihan Gabungan (One Billing Snapshot)
                </h5>
                <p class="text-muted small mb-0">
                    Konfigurasi tarif resmi yang berlaku di sistem. Setiap pembayaran warga secara otomatis dipecah dan dialokasikan ke rekening kas tujuan tanpa tercampur.
                </p>
            </div>
            <div class="text-end">
                <span class="text-muted small d-block">Standar Kewajiban per KK:</span>
                <span class="fs-5 fw-black text-primary">Rp {{ number_format($komposisiTarif['total_per_kk'], 0, ',', '.') }}</span>
                <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.7rem;">Flat / KK</span>
            </div>
        </div>

        {{-- Visual Split Bar Porsi Alokasi Dana --}}
        @php
            $totalPorsi = max(1, $komposisiTarif['total_per_kk']);
            $pctRt = round(($komposisiTarif['rt'] / $totalPorsi) * 100);
            $pctRw = round(($komposisiTarif['rw'] / $totalPorsi) * 100);
            $pctSampah = round(($komposisiTarif['sampah'] / $totalPorsi) * 100);
            $pctKegiatan = round(($komposisiTarif['kegiatan'] / $totalPorsi) * 100);
        @endphp
        <div class="progress mb-3" style="height: 12px; border-radius: 9999px; overflow: hidden; background: #F1F5F9;">
            <div class="progress-bar" role="progressbar" style="width: {{ $pctRt }}%; background-color: #1456A0;" title="Kas RT: {{ $pctRt }}%"></div>
            <div class="progress-bar" role="progressbar" style="width: {{ $pctRw }}%; background-color: #16865D;" title="Kas RW: {{ $pctRw }}%"></div>
            <div class="progress-bar" role="progressbar" style="width: {{ $pctSampah }}%; background-color: #D97706;" title="Kas Sampah: {{ $pctSampah }}%"></div>
            <div class="progress-bar" role="progressbar" style="width: {{ $pctKegiatan }}%; background-color: #7C3AED;" title="Kegiatan/Keamanan: {{ $pctKegiatan }}%"></div>
        </div>

        {{-- 4 Kotak Komponen Alokasi --}}
        <div class="row g-3">
            {{-- Komponen Kas RT --}}
            <div class="col-md-3 col-sm-6">
                <div class="p-3 rounded-4 border bg-light h-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge px-2 py-0.5 rounded-pill fw-bold" style="background: #EFF6FF; color: #1456A0; border: 1px solid #BFDBFE; font-size: 0.72rem;">
                            Rekening Kas RT
                        </span>
                        <span class="small fw-bold text-muted">{{ $pctRt }}%</span>
                    </div>
                    <div class="fs-5 fw-bold text-dark mt-1">Rp {{ number_format($komposisiTarif['rt'], 0, ',', '.') }}</div>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Operasional masing-masing RT</small>
                </div>
            </div>

            {{-- Komponen Kas RW --}}
            <div class="col-md-3 col-sm-6">
                <div class="p-3 rounded-4 border bg-light h-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge px-2 py-0.5 rounded-pill fw-bold" style="background: #ECFDF5; color: #16865D; border: 1px solid #A7F3D0; font-size: 0.72rem;">
                            Rekening Kas RW
                        </span>
                        <span class="small fw-bold text-muted">{{ $pctRw }}%</span>
                    </div>
                    <div class="fs-5 fw-bold text-dark mt-1">Rp {{ number_format($komposisiTarif['rw'], 0, ',', '.') }}</div>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Operasional & koordinasi wilayah RW 013</small>
                </div>
            </div>

            {{-- Komponen Kas Sampah --}}
            <div class="col-md-3 col-sm-6">
                <div class="p-3 rounded-4 border bg-light h-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge px-2 py-0.5 rounded-pill fw-bold" style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; font-size: 0.72rem;">
                            Kas Sampah
                        </span>
                        <span class="small fw-bold text-muted">{{ $pctSampah }}%</span>
                    </div>
                    <div class="fs-5 fw-bold text-dark mt-1">Rp {{ number_format($komposisiTarif['sampah'], 0, ',', '.') }}</div>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Petugas kebersihan & armada sampah</small>
                </div>
            </div>

            {{-- Komponen Kas Kegiatan / Tambahan --}}
            <div class="col-md-3 col-sm-6">
                <div class="p-3 rounded-4 border bg-light h-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge px-2 py-0.5 rounded-pill fw-bold" style="background: #F5F3FF; color: #6D28D9; border: 1px solid #DDD6FE; font-size: 0.72rem;">
                            Kegiatan & Keamanan
                        </span>
                        <span class="small fw-bold text-muted">{{ $pctKegiatan }}%</span>
                    </div>
                    <div class="fs-5 fw-bold text-dark mt-1">Rp {{ number_format($komposisiTarif['kegiatan'], 0, ',', '.') }}</div>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Ronda malam & kegiatan warga</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         4. TOOLBAR FILTER & PENCARIAN
         ====================================================================== --}}
    <div class="card border-0 rounded-4 shadow-sm p-3 mb-4" style="background: #FFFFFF;">
        <form method="GET" action="{{ route('tagihan.index') }}" class="row g-2 align-items-center">
            {{-- Filter Periode --}}
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Periode Tagihan</label>
                <select name="periode" class="form-select form-select-sm rounded-3">
                    <option value="">Semua Periode</option>
                    @foreach($daftarPeriode as $p)
                        <option value="{{ $p }}" {{ $periode === $p ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($p . '-01')->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter RT (Hanya aktif untuk RW/Admin) --}}
            @if($isRw)
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Wilayah RT</label>
                    <select name="rt" class="form-select form-select-sm rounded-3">
                        <option value="">Semua RT (001 - 008)</option>
                        @for($i = 1; $i <= 8; $i++)
                            @php $rtVal = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $rtVal }}" {{ $rtFilter === $rtVal ? 'selected' : '' }}>
                                RT {{ $rtVal }}
                            </option>
                        @endfor
                    </select>
                </div>
            @endif

            {{-- Filter Status Pembayaran --}}
            <div class="col-md-2 col-sm-6">
                <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Status Pembayaran</label>
                <select name="status" class="form-select form-select-sm rounded-3">
                    <option value="">Semua Status</option>
                    <option value="Lunas" {{ $status === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="Belum Dibayar" {{ $status === 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="Dibayar Sebagian" {{ $status === 'Dibayar Sebagian' ? 'selected' : '' }}>Dibayar Sebagian</option>
                    <option value="Menunggu Konfirmasi" {{ $status === 'Menunggu Konfirmasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Terlambat" {{ $status === 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>

            {{-- Input Pencarian --}}
            <div class="{{ $isRw ? 'col-md-3' : 'col-md-5' }} col-sm-6">
                <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Cari Nama KK / Nomor KK</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i data-lucide="search" style="width: 14px; height: 14px;"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm border-start-0 rounded-end-3" placeholder="Contoh: Geovano Al / 3273...">
                </div>
            </div>

            {{-- Tombol Filter & Reset --}}
            <div class="col-md-2 col-sm-12 d-flex gap-2 align-self-end mt-2 mt-md-0">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold flex-grow-1">
                    <i data-lucide="filter" style="width: 14px; height: 14px;"></i> Filter
                </button>
                <a href="{{ route('tagihan.index') }}" class="btn btn-sm btn-light border rounded-pill px-2" title="Reset Filter">
                    <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- ======================================================================
         5. TABEL DAFTAR TAGIHAN WARGA
         ====================================================================== --}}
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden" style="background: #FFFFFF;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 text-secondary small fw-bold text-center" style="width: 50px;">No</th>
                        <th class="text-secondary small fw-bold">Kepala Keluarga</th>
                        <th class="text-secondary small fw-bold">Nomor KK</th>
                        <th class="text-secondary small fw-bold text-center" style="width: 80px;">Wilayah</th>
                        <th class="text-secondary small fw-bold text-end" style="width: 120px;">Total Tagihan</th>
                        <th class="text-secondary small fw-bold text-end" style="width: 120px;">Sudah Dibayar</th>
                        <th class="text-secondary small fw-bold text-end" style="width: 120px;">Sisa Tagihan</th>
                        <th class="text-secondary small fw-bold text-center" style="width: 140px;">Status</th>
                        <th class="text-secondary small fw-bold text-center" style="width: 110px;">Tgl Bayar</th>
                        <th class="text-secondary small fw-bold text-center pe-3" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihans as $idx => $tagihan)
                        @php
                            $namaKk = $tagihan->kartuKeluarga->nama_kepala_keluarga ?? ($tagihan->kepalaKeluarga->name ?? 'Warga');
                            $noKk = $tagihan->kartuKeluarga->no_kk ?? '-';
                        @endphp
                        <tr>
                            <td class="ps-3 text-center text-muted small">
                                {{ $tagihans->firstItem() + $idx }}
                            </td>
                            <td>
                                <strong class="text-dark d-block" style="font-size: 0.85rem;">{{ $namaKk }}</strong>
                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                    <small class="text-muted font-monospace" style="font-size: 0.72rem;">#{{ $tagihan->nomor_tagihan }}</small>
                                    @if($tagihan->kartuKeluarga && $tagihan->kartuKeluarga->nomor_rumah)
                                        <span class="text-muted small" style="font-size: 0.7rem;">&bull; No. {{ $tagihan->kartuKeluarga->nomor_rumah }}</span>
                                    @endif
                                </div>
                                {{-- Visualisasi Rincian Komponen Alokasi --}}
                                <div class="d-flex flex-wrap gap-1 mt-1.5">
                                    @if($tagihan->items && $tagihan->items->count() > 0)
                                        @foreach($tagihan->items as $it)
                                            @php
                                                $isRwItem = ($it->pemilik_dana === 'RW' || ($it->bukuKas && $it->bukuKas->jenis_kas === 'kas_rw'));
                                                $badgeBg = $it->status_pembayaran === 'lunas' ? '#ECFDF5' : '#F8FAFC';
                                                $badgeBorder = $it->status_pembayaran === 'lunas' ? '#A7F3D0' : '#E2E8F0';
                                                $badgeColor = $it->status_pembayaran === 'lunas' ? '#065F46' : '#64748B';
                                            @endphp
                                            <span class="badge" style="background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }}; color: {{ $badgeColor }}; font-size: 0.68rem; font-weight: 600; padding: 2px 6px;">
                                                {{ $it->nama_komponen }}: Rp {{ number_format($it->nominal_subtotal, 0, ',', '.') }}
                                                @if($isRt && $isRwItem)
                                                    <span class="badge bg-warning text-dark ms-0.5" style="font-size: 0.6rem; padding: 1px 3px;">Titipan RW</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="badge" style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; font-size: 0.68rem; font-weight: 600; padding: 2px 6px;">
                                            Kas RT: Rp {{ number_format($tagihan->nominal_rt, 0, ',', '.') }}
                                        </span>
                                        <span class="badge" style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; font-size: 0.68rem; font-weight: 600; padding: 2px 6px;">
                                            Kas RW: Rp {{ number_format($tagihan->nominal_rw, 0, ',', '.') }}
                                            @if($isRt) <span class="badge bg-warning text-dark ms-0.5" style="font-size: 0.6rem; padding: 1px 3px;">Titipan RW</span> @endif
                                        </span>
                                        <span class="badge" style="background: #FFFBEB; border: 1px solid #FDE68A; color: #B45309; font-size: 0.68rem; font-weight: 600; padding: 2px 6px;">
                                            Sampah: Rp {{ number_format($tagihan->nominal_sampah, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="font-monospace text-secondary" style="font-size: 0.8rem;">{{ $noKk }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge px-2 py-1 rounded-pill" style="background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; font-size: 0.72rem;">
                                    RT {{ $tagihan->clean_rt }}
                                </span>
                            </td>
                            <td class="text-end">
                                <strong class="text-dark">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end">
                                @if($tagihan->sisa_tagihan > 0)
                                    <span class="fw-bold text-danger">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted small">Rp 0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($tagihan->status === 'Lunas')
                                    <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #DCFCE7; color: #166534; border: 1px solid #86EFAC;">
                                        <i data-lucide="check" style="width: 12px; height: 12px; vertical-align: middle;"></i> Lunas
                                    </span>
                                @elseif($tagihan->status === 'Dibayar Sebagian')
                                    <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD;">
                                        Dibayar Sebagian
                                    </span>
                                @elseif($tagihan->status === 'Menunggu Konfirmasi')
                                    <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($tagihan->status === 'Terlambat')
                                    <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #FFE4E6; color: #9F1239; border: 1px solid #FDA4AF;">
                                        Terlambat
                                    </span>
                                @else
                                    <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="text-center small text-muted">
                                {{ $tagihan->tanggal_lunas ? $tagihan->tanggal_lunas->format('d/m/Y') : ($tagihan->pembayarans->first() ? $tagihan->pembayarans->first()->tanggal_bayar->format('d/m/Y') : '-') }}
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-inline-flex gap-1">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('tagihan.show', $tagihan->id) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1" title="Lihat Detail & Rincian Alokasi" style="font-size: 0.75rem;">
                                        Detail
                                    </a>

                                    {{-- Tombol Kuitansi (Jika Lunas) --}}
                                    @if($tagihan->status === 'Lunas')
                                        <a href="{{ route('tagihan.kuitansi', $tagihan->id) }}" target="_blank" class="btn btn-xs btn-outline-success rounded-pill px-2 py-1" title="Cetak Kuitansi Resmi" style="font-size: 0.75rem;">
                                            <i data-lucide="printer" style="width: 12px; height: 12px;"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i data-lucide="inbox" style="width: 36px; height: 36px;" class="mb-2 text-secondary opacity-50 d-block mx-auto"></i>
                                Tidak ditemukan data tagihan iuran yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($tagihans->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-muted small">
                    Menampilkan {{ $tagihans->firstItem() }} - {{ $tagihans->lastItem() }} dari total {{ $tagihans->total() }} tagihan
                </span>
                <div>
                    {{ $tagihans->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ======================================================================
     MODAL: GENERATE TAGIHAN BULANAN OTOMATIS (KHUSUS RW / ADMIN)
     ====================================================================== --}}
@if($isRw)
<div class="modal fade" id="modalGenerateTagihan" tabindex="-1" aria-labelledby="modalGenerateTagihanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalGenerateTagihanLabel">
                    <i data-lucide="sparkles" class="text-warning"></i> Generate Tagihan Bulanan KK
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tagihan.generate') }}" method="POST">
                @csrf
                <div class="modal-body space-y-3">
                    <p class="text-muted small mb-3">
                        Sistem akan memeriksa seluruh Kartu Keluarga (KK) terdaftar di RW 013 dan secara idempoten membuat lembar tagihan gabungan bagi yang belum memiliki di periode ini.
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Periode Tagihan *</label>
                        <input type="month" name="periode" value="{{ \Carbon\Carbon::now()->format('Y-m') }}" required class="form-control rounded-3">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Tanggal Jatuh Tempo *</label>
                        <input type="date" name="jatuh_tempo" value="{{ \Carbon\Carbon::now()->addDays(20)->toDateString() }}" required class="form-control rounded-3">
                    </div>

                    <div class="p-3 rounded-3 border bg-light small">
                        <span class="fw-bold d-block text-dark mb-1">Rincian Komponen Sesuai Konfigurasi Aktif:</span>
                        @foreach($komposisiTarif['items'] as $itemTarif)
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.78rem;">
                                <span>• {{ $itemTarif['nama'] }} ({{ strtoupper($itemTarif['jenis_kas']) }}):</span>
                                <strong class="text-dark">Rp {{ number_format($itemTarif['nominal'], 0, ',', '.') }}</strong>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between text-dark fw-bold border-top pt-1 mt-1">
                            <span>Total Standar per KK:</span>
                            <span class="text-primary">Rp {{ number_format($komposisiTarif['total_per_kk'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                        <i data-lucide="check" style="width: 14px; height: 14px;"></i> Generate Tagihan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
