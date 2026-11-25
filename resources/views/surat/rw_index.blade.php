@extends('layouts.app')

@section('title', 'Manajemen Layanan Surat RW 013')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" style="max-width: 1650px;">

    {{-- Breadcrumb & Header Title --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i data-lucide="home" class="me-1" style="width:14px;height:14px;"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Manajemen Layanan Surat</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2 flex-wrap">
                <span>Manajemen Layanan Surat RW 013</span>
                <span class="badge bg-primary text-white rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                    Pusat Administrasi & Pengesahan Terpadu
                </span>
            </h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Kelola permohonan surat warga, konfigurasi 18 jenis layanan surat, template dokumen resmi, alur persetujuan, dan pratinjau tampilan warga dalam satu kendali terpusat.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small d-flex align-items-center gap-1.5 shadow-sm">
                <i data-lucide="shield-check" class="text-success" style="width:15px;height:15px;"></i>
                <span>Kewenangan Pengurus RW 013</span>
            </span>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-3 mb-4">
            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;flex-shrink:0;">
                <i data-lucide="check" style="width:20px;height:20px;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold text-success">Berhasil!</div>
                <div class="text-secondary small">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-3 mb-4">
            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;flex-shrink:0;">
                <i data-lucide="alert-triangle" style="width:20px;height:20px;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold text-danger">Perhatian</div>
                <div class="text-secondary small">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 4 KARTU STATISTIK RINGKASAN PERSURATAN RW --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #0284C7 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Menunggu Tindakan RW</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(2, 132, 199, 0.12); color: #0284C7;">
                        <i data-lucide="file-clock" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-info mb-0">{{ $statusCounts['waiting_rw'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Lolos verifikasi RT • Perlu TTD RW</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #16865D !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Layanan Surat Aktif</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(22, 134, 93, 0.12); color: #16865D;">
                        <i data-lucide="layers" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ $masterStats['active'] }} <span class="text-muted fs-6 fw-normal">/ {{ $masterStats['total'] }}</span></h3>
                <span class="text-muted" style="font-size: 0.75rem;">Dapat diajukan warga RW 013</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #1456A0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Surat Terbit Sah</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(20, 86, 160, 0.12); color: #1456A0;">
                        <i data-lucide="check-circle-2" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-primary mb-0">{{ $statusCounts['approved'] + $statusCounts['completed'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Bersetempel & Ber-QR Code Resmi</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #F59E0B !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Alur Mandiri RT</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); color: #D97706;">
                        <i data-lucide="shield-check" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-warning mb-0">{{ $alurStats['rt_only'] }} Layanan</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Cukup TTD Ketua RT</span>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         SISTEM NAVIGASI 5 TAB UTAMA (MANAJEMEN LAYANAN SURAT RW)
         ========================================================================= --}}
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-2">
        <ul class="nav nav-pills flex-nowrap overflow-x-auto gap-2 p-1" id="suratTabs" role="tablist" style="scrollbar-width: thin;">
            
            {{-- TAB A: PERMOHONAN SURAT --}}
            <li class="nav-item flex-shrink-0" role="presentation">
                <a class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'permohonan' ? 'active' : '' }}" 
                   href="{{ route('rw.surat.index', ['tab' => 'permohonan']) }}" role="tab">
                    <i data-lucide="inbox" style="width: 17px; height: 17px;"></i>
                    <span>A. Permohonan Surat</span>
                    @if($statusCounts['waiting_rw'] > 0)
                        <span class="badge rounded-pill bg-danger text-white ms-1" style="font-size: 11px;">{{ $statusCounts['waiting_rw'] }} Menunggu</span>
                    @else
                        <span class="badge rounded-pill bg-light text-secondary border ms-1" style="font-size: 11px;">{{ $statusCounts['total'] }}</span>
                    @endif
                </a>
            </li>

            {{-- TAB B: DAFTAR LAYANAN SURAT --}}
            <li class="nav-item flex-shrink-0" role="presentation">
                <a class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'daftar_layanan' ? 'active' : '' }}" 
                   href="{{ route('rw.surat.index', ['tab' => 'daftar_layanan']) }}" role="tab">
                    <i data-lucide="layers" style="width: 17px; height: 17px;"></i>
                    <span>B. Daftar Layanan Surat</span>
                    <span class="badge rounded-pill bg-light text-secondary border ms-1" style="font-size: 11px;">18 Layanan</span>
                </a>
            </li>

            {{-- TAB C: TEMPLATE & PERSYARATAN --}}
            <li class="nav-item flex-shrink-0" role="presentation">
                <a class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'template' ? 'active' : '' }}" 
                   href="{{ route('rw.surat.index', ['tab' => 'template']) }}" role="tab">
                    <i data-lucide="file-edit" style="width: 17px; height: 17px;"></i>
                    <span>C. Template & Persyaratan</span>
                </a>
            </li>

            {{-- TAB D: ALUR PERSETUJUAN --}}
            <li class="nav-item flex-shrink-0" role="presentation">
                <a class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'alur' ? 'active' : '' }}" 
                   href="{{ route('rw.surat.index', ['tab' => 'alur']) }}" role="tab">
                    <i data-lucide="git-merge" style="width: 17px; height: 17px;"></i>
                    <span>D. Alur Persetujuan</span>
                </a>
            </li>

            {{-- TAB E: PRATINJAU LAYANAN WARGA --}}
            <li class="nav-item flex-shrink-0" role="presentation">
                <a class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'pratinjau' ? 'active' : '' }}" 
                   href="{{ route('rw.surat.index', ['tab' => 'pratinjau']) }}" role="tab">
                    <i data-lucide="eye" style="width: 17px; height: 17px;"></i>
                    <span>E. Pratinjau Layanan Warga</span>
                    <span class="badge rounded-pill bg-light text-secondary border ms-1" style="font-size: 11px;">Mode Pratinjau</span>
                </a>
            </li>

        </ul>
    </div>

    {{-- =========================================================================
         TAB A — PERMOHONAN SURAT (INBOX & VERIFIKASI KEWENANGAN RW)
         ========================================================================= --}}
    @if($activeTab == 'permohonan')
        {{-- Banner Kewenangan Pengurus RW --}}
        <div class="p-3 rounded-4 mb-4 border d-flex align-items-start gap-3 bg-white shadow-sm">
            <div class="p-2.5 rounded-circle text-primary" style="background: #E3F2FD;">
                <i data-lucide="shield-alert" style="width: 22px; height: 22px;"></i>
            </div>
            <div class="small text-secondary">
                <strong class="text-dark d-block mb-1">Batasan Kewenangan Pemeriksaan RW 013:</strong>
                Pengurus RW berwenang menyetujui dokumen yang telah lolos verifikasi dari Ketua RT asal, menerbitkan <strong>Nomor Surat Resmi RW</strong>, serta membubuhkan pengesahan tanda tangan elektronik resmi. Permohonan berstatus mandiri RT (RT Only) tidak memerlukan persetujuan ulang RW.
            </div>
        </div>

        {{-- Status Filter Quick Pills --}}
        <div class="d-flex align-items-center gap-2 mb-3 overflow-x-auto pb-1" style="scrollbar-width: none;">
            <a href="{{ route('rw.surat.index', ['tab' => 'permohonan']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ !request('status') ? 'btn-primary' : 'btn-light border text-secondary' }}">
                Semua Permohonan ({{ $statusCounts['total'] }})
            </a>
            <a href="{{ route('rw.surat.index', ['tab' => 'permohonan', 'status' => 'menunggu_rw']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ request('status') == 'menunggu_rw' ? 'btn-info text-white' : 'btn-light border text-secondary' }}">
                Menunggu Tindakan RW ({{ $statusCounts['waiting_rw'] }})
            </a>
            <a href="{{ route('rw.surat.index', ['tab' => 'permohonan', 'status' => 'proses']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ request('status') == 'proses' ? 'btn-warning text-dark' : 'btn-light border text-secondary' }}">
                Sedang Diproses RT ({{ $statusCounts['processing'] }})
            </a>
            <a href="{{ route('rw.surat.index', ['tab' => 'permohonan', 'status' => 'perbaikan']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ request('status') == 'perbaikan' ? 'btn-secondary text-white' : 'btn-light border text-secondary' }}">
                Perlu Perbaikan ({{ $statusCounts['revision'] }})
            </a>
            <a href="{{ route('rw.surat.index', ['tab' => 'permohonan', 'status' => 'disetujui']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ request('status') == 'disetujui' ? 'btn-success text-white' : 'btn-light border text-secondary' }}">
                Disetujui / Siap Cetak ({{ $statusCounts['approved'] }})
            </a>
            <a href="{{ route('rw.surat.index', ['tab' => 'permohonan', 'status' => 'ditolak']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ request('status') == 'ditolak' ? 'btn-danger text-white' : 'btn-light border text-secondary' }}">
                Ditolak ({{ $statusCounts['rejected'] }})
            </a>
            <a href="{{ route('rw.surat.index', ['tab' => 'permohonan', 'status' => 'selesai']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ request('status') == 'selesai' ? 'btn-dark text-white' : 'btn-light border text-secondary' }}">
                Selesai ({{ $statusCounts['completed'] }})
            </a>
        </div>

        {{-- Filter Box --}}
        <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-3">
            <form method="GET" action="{{ route('rw.surat.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="permohonan">
                
                <div class="col-12 col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm rounded-3" 
                           placeholder="Cari pemohon / NIK / No. Registrasi / No. Surat..." 
                           value="{{ request('search') }}">
                </div>

                <div class="col-6 col-md-2">
                    <select name="rt" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Wilayah RT (8 RT)</option>
                        @for($i = 1; $i <= 8; $i++)
                            @php $rtVal = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $rtVal }}" {{ request('rt') == $rtVal ? 'selected' : '' }}>RT 00{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="jenis" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Jenis Surat (18 Layanan)</option>
                        @foreach($allTypes as $t)
                            <option value="{{ $t->kode }}" {{ request('jenis') == $t->kode ? 'selected' : '' }}>{{ $t->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <input type="date" name="tgl_mulai" class="form-control form-control-sm rounded-3" title="Tanggal Mulai" value="{{ request('tgl_mulai') }}">
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold flex-grow-1 shadow-sm">
                        <i data-lucide="search" style="width: 14px; height: 14px; vertical-align: middle;"></i> Saring
                    </button>
                    <a href="{{ route('rw.surat.index', ['tab' => 'permohonan']) }}" class="btn btn-sm btn-light border rounded-pill px-3 text-muted">Reset</a>
                </div>
            </form>
        </div>

        {{-- Tabel Permohonan Masuk RW --}}
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i data-lucide="inbox" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Daftar Permohonan Surat Masuk RW 013
                    </h5>
                    <p class="mb-0 text-muted small">
                        Daftar berkas warga yang diajukan dari wilayah RT 001 s/d RT 008 Kelurahan Sukamiskin Arcamanik.
                    </p>
                </div>
            </div>

            @if($letters->isEmpty())
                <div class="text-center py-5">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                        <i data-lucide="file-check" class="text-muted" style="width:32px;height:32px;"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Tidak Ada Permohonan Surat</h6>
                    <p class="text-muted small mb-0">Belum ada permohonan surat yang sesuai dengan kriteria filter saat ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-secondary small fw-bold text-uppercase">No. Registrasi & Tanggal</th>
                                <th class="text-secondary small fw-bold text-uppercase">Pemohon & RT</th>
                                <th class="text-secondary small fw-bold text-uppercase">Jenis Surat & Keperluan</th>
                                <th class="text-secondary small fw-bold text-uppercase">Verifikasi RT</th>
                                <th class="text-secondary small fw-bold text-uppercase">Persetujuan RW</th>
                                <th class="text-secondary small fw-bold text-uppercase text-center">Status Akhir</th>
                                <th class="text-end pe-3 text-secondary small fw-bold text-uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($letters as $item)
                                <tr>
                                    {{-- No Registrasi & Tanggal --}}
                                    <td class="ps-3">
                                        <div class="fw-bold font-monospace text-dark" style="font-size: 0.85rem;">{{ $item->nomor_registrasi }}</div>
                                        @if($item->nomor_surat)
                                            <div class="text-success small fw-semibold font-monospace" style="font-size: 0.74rem;">
                                                <i data-lucide="file-check" style="width:11px;height:11px;"></i> {{ $item->nomor_surat }}
                                            </div>
                                        @else
                                            <div class="text-muted small" style="font-size: 0.74rem;">
                                                <i data-lucide="calendar" style="width:11px;height:11px;" class="me-1"></i>
                                                {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Pemohon & RT --}}
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->nama_lengkap }}</div>
                                        <div class="text-muted small" style="font-size: 0.74rem;">
                                            <span class="badge bg-light text-secondary border">RT {{ $item->rt }}</span>
                                            NIK: {{ substr($item->nik, 0, 8) }}****
                                        </div>
                                    </td>

                                    {{-- Jenis & Keperluan --}}
                                    <td>
                                        <div class="fw-semibold text-primary">{{ $item->judul_surat }}</div>
                                        <div class="text-dark small text-truncate" style="max-width: 240px;" title="{{ $item->keperluan }}">
                                            {{ $item->keperluan }}
                                        </div>
                                    </td>

                                    {{-- Verifikasi RT --}}
                                    <td>
                                        @if($item->rt_approved_at)
                                            <div class="text-success small fw-semibold">
                                                <i data-lucide="check-circle" style="width:13px;height:13px;"></i> Diverifikasi RT
                                            </div>
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                {{ $item->rt_approved_name }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                {{ $item->rt_approved_at->translatedFormat('d/m/Y H:i') }}
                                            </div>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.72rem;">
                                                Belum Verifikasi RT
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Persetujuan RW --}}
                                    <td>
                                        @if($item->rw_approved_at)
                                            <div class="text-success small fw-semibold">
                                                <i data-lucide="check-check" style="width:13px;height:13px;"></i> Disahkan RW
                                            </div>
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                {{ $item->rw_approved_name }}
                                            </div>
                                        @elseif($item->status === 'Menunggu Persetujuan RW')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.72rem;">
                                                Menunggu Tindakan RW
                                            </span>
                                        @else
                                            <span class="text-muted small" style="font-size: 0.72rem;">-</span>
                                        @endif
                                    </td>

                                    {{-- Status Akhir --}}
                                    <td class="text-center">
                                        @php
                                            $stClass = 'bg-secondary-subtle text-secondary';
                                            if ($item->status === 'Menunggu Persetujuan RW') $stClass = 'bg-info-subtle text-info border border-info-subtle';
                                            elseif (in_array($item->status, ['Siap Dicetak', 'Selesai'])) $stClass = 'bg-success-subtle text-success border border-success-subtle';
                                            elseif (str_contains($item->status, 'Ditolak')) $stClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                            elseif ($item->status === 'Menunggu Verifikasi RT') $stClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                                            elseif (str_contains($item->status, 'Perlu Perbaikan')) $stClass = 'bg-secondary-subtle text-secondary border';
                                        @endphp
                                        <span class="badge {{ $stClass }} px-2 py-1 rounded-pill" style="font-size: 0.75rem; font-weight: 600;">
                                            {{ $item->status }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="pe-3 text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('surat.show', $item->id) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1" style="font-size: 0.78rem;" title="Lihat Rincian Berkas">
                                                <i data-lucide="eye" style="width:14px;height:14px;"></i>
                                            </a>

                                            @if($item->status === 'Menunggu Persetujuan RW')
                                                {{-- Tombol Sahkan RW --}}
                                                <form action="{{ route('rw.surat.approve', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menyahkan dokumen ini dan menerbitkan Nomor Surat Resmi RW 013?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-2 py-1 d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.78rem; background: #1456A0; border-color: #1456A0;">
                                                        <i data-lucide="file-check" style="width:14px;height:14px;"></i>
                                                        <span>Sahkan & TTD</span>
                                                    </button>
                                                </form>

                                                {{-- Tombol Revisi --}}
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded-3 px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalItemRevisi-{{ $item->id }}" title="Minta Revisi">
                                                    <i data-lucide="edit-3" style="width:14px;height:14px;"></i>
                                                </button>

                                                {{-- Tombol Tolak --}}
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalItemTolak-{{ $item->id }}" title="Tolak">
                                                    <i data-lucide="x" style="width:14px;height:14px;"></i>
                                                </button>
                                            @elseif($item->canPrint())
                                                <a href="{{ route('surat.cetak', $item->id) }}" target="_blank" class="btn btn-sm btn-success rounded-3 px-2 py-1 d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.78rem;">
                                                    <i data-lucide="printer" style="width:14px;height:14px;"></i>
                                                    <span>Cetak</span>
                                                </a>
                                            @endif
                                        </div>

                                        {{-- MODAL REVISI PERMOHONAN --}}
                                        <div class="modal fade text-start" id="modalItemRevisi-{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content rounded-4 border-0">
                                                    <form action="{{ route('rw.surat.revisi', $item->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Permintaan Revisi Pengesahan RW</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-2 text-muted small">Pemohon: <strong>{{ $item->nama_lengkap }}</strong> (RT {{ $item->rt }})</div>
                                                            <label class="form-label small fw-semibold">Catatan Perbaikan <span class="text-danger">*</span></label>
                                                            <textarea name="notes" class="form-control" rows="3" required placeholder="Tuliskan catatan perbaikan berkas untuk warga/RT..."></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-warning">Kirim Catatan Revisi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- MODAL TOLAK PERMOHONAN --}}
                                        <div class="modal fade text-start" id="modalItemTolak-{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content rounded-4 border-0">
                                                    <form action="{{ route('rw.surat.reject', $item->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold text-danger">Tolak Permohonan Surat</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-2 text-muted small">Pemohon: <strong>{{ $item->nama_lengkap }}</strong></div>
                                                            <label class="form-label small fw-semibold">Alasan Penolakan Resmi RW 013 <span class="text-danger">*</span></label>
                                                            <textarea name="reason" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Tolak Permohonan</button>
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

                @if($letters->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $letters->links() }}
                    </div>
                @endif
            @endif
        </div>
    @endif

    {{-- =========================================================================
         TAB B — DAFTAR LAYANAN SURAT (18 JENIS LAYANAN MASTER & TOGGLE)
         ========================================================================= --}}
    @if($activeTab == 'daftar_layanan')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="layers" class="text-primary" style="width: 20px; height: 20px;"></i>
                        <span>Katalog 18 Jenis Layanan Surat Terdaftar</span>
                    </h5>
                    <p class="mb-0 text-muted small">
                        Kelola data master jenis surat, persyaratan berkas, estimasi penyelesaian, dan status aktif/nonaktif di Haven.
                    </p>
                </div>
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-pill px-4 shadow-sm fw-bold" 
                        data-bs-toggle="modal" data-bs-target="#modalTambahLayanan">
                    <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
                    <span>Tambah Layanan Baru</span>
                </button>
            </div>

            {{-- Filter Kategori & Search Layanan --}}
            <div class="row g-2 align-items-center mb-4">
                <div class="col-12 col-md-5">
                    <form method="GET" action="{{ route('rw.surat.index') }}" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="daftar_layanan">
                        <input type="text" name="search_layanan" class="form-control form-control-sm rounded-3" 
                               placeholder="Cari nama layanan, kode, atau singkatan..." 
                               value="{{ request('search_layanan') }}">
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3">Cari</button>
                    </form>
                </div>
                <div class="col-12 col-md-7 d-flex justify-content-md-end gap-1 flex-wrap">
                    @php
                        $kategoriList = [
                            'semua' => 'Semua',
                            'kependudukan' => 'Kependudukan',
                            'sosial_keluarga' => 'Sosial',
                            'usaha_ekonomi' => 'Usaha',
                            'lingkungan_keamanan' => 'Lingkungan',
                            'internal' => 'Internal'
                        ];
                    @endphp
                    @foreach($kategoriList as $kKey => $kLabel)
                        <a href="{{ route('rw.surat.index', ['tab' => 'daftar_layanan', 'kategori_layanan' => $kKey]) }}" 
                           class="btn btn-sm rounded-pill px-3 py-1 {{ (request('kategori_layanan', 'semua') == $kKey) ? 'btn-primary' : 'btn-light border text-secondary' }}">
                            {{ $kLabel }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Tabel Master Layanan Surat --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold text-uppercase">No & Kode</th>
                            <th class="text-secondary small fw-bold text-uppercase">Nama Dokumen & Kategori</th>
                            <th class="text-secondary small fw-bold text-uppercase">Persyaratan</th>
                            <th class="text-secondary small fw-bold text-uppercase">Estimasi & Biaya</th>
                            <th class="text-secondary small fw-bold text-uppercase">Alur Persetujuan</th>
                            <th class="text-center text-secondary small fw-bold text-uppercase">Status</th>
                            <th class="text-end pe-3 text-secondary small fw-bold text-uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($masterTypes as $idx => $t)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">#{{ $t->urutan }}</div>
                                    <span class="badge bg-light text-secondary border font-monospace">{{ $t->singkatan }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $t->nama }}</div>
                                    <small class="text-muted d-block">{{ $t->kategori_label }}</small>
                                </td>
                                <td>
                                    @php $reqCount = is_array($t->persyaratan) ? count($t->persyaratan) : 0; @endphp
                                    <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1">
                                        {{ $reqCount }} Berkas Syarat
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $t->estimasi_hari }}</div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 11px;">
                                        {{ $t->biaya }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $t->alur_badge_color }}-subtle text-{{ $t->alur_badge_color }} border border-{{ $t->alur_badge_color }}-subtle rounded-pill px-2.5 py-1 small">
                                        {{ $t->alur_label }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($t->is_active)
                                        <span class="badge bg-success text-white rounded-pill px-2.5 py-1 small">Aktif</span>
                                    @else
                                        <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        {{-- Toggle Aktif / Nonaktif --}}
                                        <form action="{{ route('rw.surat_master.toggle', $t->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $t->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} rounded-3 px-2 py-1" 
                                                    title="{{ $t->is_active ? 'Nonaktifkan Layanan' : 'Aktifkan Layanan' }}">
                                                <i data-lucide="{{ $t->is_active ? 'slash' : 'check' }}" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>

                                        {{-- Edit Konfigurasi Lengkap --}}
                                        <a href="{{ route('rw.surat_master.edit', $t->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1" title="Edit Lengkap">
                                            <i data-lucide="edit-2" style="width: 14px; height: 14px;"></i>
                                        </a>

                                        {{-- Edit Template Shortcut --}}
                                        <a href="{{ route('rw.surat.index', ['tab' => 'template', 'template_id' => $t->id]) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1" title="Lihat & Edit Template">
                                            <i data-lucide="file-text" style="width: 14px; height: 14px;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada layanan yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODAL TAMBAH JENIS LAYANAN BARU --}}
        <div class="modal fade" id="modalTambahLayanan" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content rounded-4 border-0">
                    <form action="{{ route('rw.surat_master.store') }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                <i data-lucide="plus-circle" class="text-primary" style="width: 20px; height: 20px;"></i>
                                <span>Tambah Jenis Layanan Surat Baru</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12 col-md-8">
                                    <label class="form-label small fw-semibold text-dark">Nama Dokumen Surat <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: Surat Rekomendasi Kegiatan" required>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label small fw-semibold text-dark">Singkatan / Kode <span class="text-danger">*</span></label>
                                    <input type="text" name="singkatan" class="form-control rounded-3 font-monospace" placeholder="Contoh: SRK" required>
                                </div>
                                <div class="col-6 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Kategori Administrasi <span class="text-danger">*</span></label>
                                    <select name="kategori" class="form-select rounded-3" required>
                                        <option value="kependudukan">Administrasi Kependudukan</option>
                                        <option value="sosial_keluarga">Sosial dan Keluarga</option>
                                        <option value="usaha_ekonomi">Usaha, Pekerjaan & Ekonomi</option>
                                        <option value="lingkungan_keamanan">Lingkungan & Keamanan</option>
                                        <option value="internal">Administrasi Internal</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Alur Persetujuan <span class="text-danger">*</span></label>
                                    <select name="alur_persetujuan" class="form-select rounded-3" required>
                                        <option value="rt_then_rw">Persetujuan Berjenjang (RT & RW)</option>
                                        <option value="rt_only">Persetujuan RT Saja</option>
                                        <option value="pengantar_kelurahan">Verifikasi RT & Pengantar Kelurahan</option>
                                        <option value="internal_rw">Administrasi Internal RW</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-dark">Deskripsi Layanan <span class="text-danger">*</span></label>
                                    <textarea name="deskripsi" class="form-control rounded-3" rows="2" placeholder="Jelaskan tujuan dan fungsi surat ini..." required></textarea>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label small fw-semibold text-dark">Estimasi Waktu <span class="text-danger">*</span></label>
                                    <input type="text" name="estimasi_hari" class="form-control rounded-3" value="1 - 2 Hari Kerja" required>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label small fw-semibold text-dark">Biaya Pelayanan <span class="text-danger">*</span></label>
                                    <input type="text" name="biaya" class="form-control rounded-3" value="Gratis (Rp 0)" required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label small fw-semibold text-dark">Kode Klasifikasi <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_klasifikasi" class="form-control rounded-3 font-monospace" value="470" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-dark">Persyaratan Berkas (Tuliskan satu per baris)</label>
                                    <textarea name="persyaratan_text" class="form-control rounded-3" rows="3" placeholder="Surat Pengantar RT&#10;KTP Pemohon&#10;Kartu Keluarga"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Layanan Baru</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================================
         TAB C — TEMPLATE & PERSYARATAN
         ========================================================================= --}}
    @if($activeTab == 'template')
        <div class="row g-4 mb-4">
            {{-- KOLOM KIRI: SELECTOR & FORM EDIT --}}
            <div class="col-12 col-lg-7">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                <i data-lucide="file-edit" class="text-primary" style="width: 20px; height: 20px;"></i>
                                <span>Editor Template Kalimat Resmi</span>
                            </h5>
                            <p class="mb-0 text-muted small">
                                Sesuaikan format kalimat resmi dan persyaratan berkas untuk jenis surat terpilih.
                            </p>
                        </div>
                    </div>

                    {{-- Selector Dropdown --}}
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <label class="form-label small fw-bold text-dark mb-1">Pilih Jenis Surat yang Dikelola:</label>
                        <form method="GET" action="{{ route('rw.surat.index') }}">
                            <input type="hidden" name="tab" value="template">
                            <select name="template_id" class="form-select rounded-3" onchange="this.form.submit()">
                                @foreach($allTypes as $t)
                                    <option value="{{ $t->id }}" {{ $selectedTemplate?->id == $t->id ? 'selected' : '' }}>
                                        #{{ $t->urutan }} - {{ $t->nama }} ({{ $t->singkatan }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    @if($selectedTemplate)
                        <form action="{{ route('rw.surat_master.update', $selectedTemplate->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="redirect_tab" value="template">

                            <div class="row g-3">
                                <div class="col-12 col-md-8">
                                    <label class="form-label small fw-semibold text-dark">Nama Dokumen Surat</label>
                                    <input type="text" name="nama" class="form-control rounded-3" value="{{ old('nama', $selectedTemplate->nama) }}" required>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label small fw-semibold text-dark">Singkatan</label>
                                    <input type="text" name="singkatan" class="form-control rounded-3 font-monospace" value="{{ old('singkatan', $selectedTemplate->singkatan) }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-dark">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control rounded-3" rows="2" required>{{ old('deskripsi', $selectedTemplate->deskripsi) }}</textarea>
                                </div>
                                <div class="col-6 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Alur Persetujuan</label>
                                    <select name="alur_persetujuan" class="form-select rounded-3">
                                        <option value="rt_then_rw" {{ $selectedTemplate->alur_persetujuan == 'rt_then_rw' ? 'selected' : '' }}>Persetujuan Berjenjang (RT & RW)</option>
                                        <option value="rt_only" {{ $selectedTemplate->alur_persetujuan == 'rt_only' ? 'selected' : '' }}>Persetujuan RT Saja</option>
                                        <option value="pengantar_kelurahan" {{ $selectedTemplate->alur_persetujuan == 'pengantar_kelurahan' ? 'selected' : '' }}>Verifikasi RT & Pengantar Kelurahan</option>
                                        <option value="internal_rw" {{ $selectedTemplate->alur_persetujuan == 'internal_rw' ? 'selected' : '' }}>Administrasi Internal RW</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Kode Klasifikasi</label>
                                    <input type="text" name="kode_klasifikasi" class="form-control rounded-3 font-monospace" value="{{ old('kode_klasifikasi', $selectedTemplate->kode_klasifikasi) }}" required>
                                </div>
                                <div class="col-6 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Estimasi Waktu</label>
                                    <input type="text" name="estimasi_hari" class="form-control rounded-3" value="{{ old('estimasi_hari', $selectedTemplate->estimasi_hari) }}" required>
                                </div>
                                <div class="col-6 col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Biaya Pelayanan</label>
                                    <input type="text" name="biaya" class="form-control rounded-3" value="{{ old('biaya', $selectedTemplate->biaya) }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-dark">Template Kalimat Resmi Surat <span class="text-danger">*</span></label>
                                    <textarea name="template_body" class="form-control rounded-3 font-monospace" rows="5" required>{{ old('template_body', $selectedTemplate->template_body) }}</textarea>
                                    <div class="form-text small text-muted">Gunakan placeholder seperti [KEPERLUAN], [NAMA], [NIK], dll.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-dark">Persyaratan Berkas (Satu per baris)</label>
                                    <textarea name="persyaratan_text" class="form-control rounded-3" rows="3">@if(is_array($selectedTemplate->persyaratan)){{ implode("\n", $selectedTemplate->persyaratan) }}@endif</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="swActiveTpl" {{ $selectedTemplate->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold text-dark" for="swActiveTpl">Status Layanan Aktif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                    <i data-lucide="save" style="width: 15px; height: 15px; vertical-align: middle;"></i> Simpan Perubahan Template
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            {{-- KOLOM KANAN: KAMUS PLACEHOLDERS & PRATINJAU DOKUMEN --}}
            <div class="col-12 col-lg-5">
                {{-- Kamus Placeholder --}}
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
                    <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                        <i data-lucide="code-2" class="text-primary" style="width: 18px; height: 18px;"></i>
                        <span>Kamus Variabel & Placeholder</span>
                    </h6>
                    <p class="text-muted small mb-3">
                        Variabel berikut otomatis diganti dengan data identitas pemohon saat surat dicetak:
                    </p>
                    <div class="d-flex flex-wrap gap-1.5 small font-monospace">
                        <span class="badge bg-light text-dark border p-2">[NOMOR_SURAT]</span>
                        <span class="badge bg-light text-dark border p-2">[NAMA]</span>
                        <span class="badge bg-light text-dark border p-2">[NIK]</span>
                        <span class="badge bg-light text-dark border p-2">[NO_KK]</span>
                        <span class="badge bg-light text-dark border p-2">[TEMPAT_LAHIR]</span>
                        <span class="badge bg-light text-dark border p-2">[TANGGAL_LAHIR]</span>
                        <span class="badge bg-light text-dark border p-2">[JENIS_KELAMIN]</span>
                        <span class="badge bg-light text-dark border p-2">[AGAMA]</span>
                        <span class="badge bg-light text-dark border p-2">[PEKERJAAN]</span>
                        <span class="badge bg-light text-dark border p-2">[ALAMAT]</span>
                        <span class="badge bg-light text-dark border p-2">[RT]</span>
                        <span class="badge bg-light text-dark border p-2">[RW]</span>
                        <span class="badge bg-light text-dark border p-2">[KEPERLUAN]</span>
                        <span class="badge bg-light text-dark border p-2">[TANGGAL_SURAT]</span>
                    </div>
                </div>

                {{-- Simulasi Dokumen Cetak Resmi --}}
                @if($selectedTemplate)
                    <div class="wm-card bg-white border rounded-4 shadow-sm p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i data-lucide="printer" class="text-success" style="width: 18px; height: 18px;"></i>
                                <span>Simulasi Tampilan Kop Dokumen</span>
                            </h6>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small">Pratinjau Otomatis</span>
                        </div>

                        <div class="p-3 bg-light rounded-3 border" style="font-size: 0.8rem; font-family: 'Times New Roman', serif;">
                            <div class="text-center border-bottom pb-2 mb-2">
                                <div class="fw-bold" style="font-size: 0.95rem;">RUKUN WARGA 013</div>
                                <div class="fw-bold" style="font-size: 0.9rem;">KELURAHAN SUKAMISKIN KECAMATAN ARCAMANIK</div>
                                <div style="font-size: 0.75rem;">KOTA BANDUNG - JAWA BARAT 40293</div>
                            </div>
                            <div class="text-center fw-bold mb-2 text-decoration-underline" style="font-size: 0.9rem;">
                                {{ strtoupper($selectedTemplate->nama) }}
                            </div>
                            <div class="text-center text-muted mb-3" style="font-size: 0.75rem;">
                                Nomor: {{ $selectedTemplate->kode_klasifikasi }}/RW.013/{{ date('Y') }}
                            </div>
                            <p class="mb-2" style="line-height: 1.5;">
                                Yang bertanda tangan di bawah ini Ketua Rukun Warga (RW) 013 Kelurahan Sukamiskin Kecamatan Arcamanik Kota Bandung, menerangkan bahwa:
                            </p>
                            <div class="ps-3 mb-2">
                                <div>Nama : <strong>Ahmad Dahlan</strong></div>
                                <div>NIK : <strong>3273010101900001</strong></div>
                                <div>Alamat : Jl. Cisaranten Kulon RT 003 / RW 013</div>
                            </div>
                            <p class="mb-3" style="line-height: 1.5;">
                                {{ Str::limit($selectedTemplate->template_body, 140) }}
                            </p>
                            <div class="d-flex justify-content-between mt-3 pt-2 text-center" style="font-size: 0.75rem;">
                                <div>Mengetahui,<br>Ketua RT Terkait<br><br><span class="fw-bold">( .................... )</span></div>
                                <div>Bandung, {{ date('d F Y') }}<br>Ketua RW 013<br><br><span class="fw-bold">( Bambang Sutrisno )</span></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- =========================================================================
         TAB D — ALUR PERSETUJUAN (VISUAL WORKFLOW & KONFIGURASI)
         ========================================================================= --}}
    @if($activeTab == 'alur')
        {{-- Visual Workflow Diagrams --}}
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-2 text-dark d-flex align-items-center gap-2">
                <i data-lucide="git-merge" class="text-primary" style="width: 20px; height: 20px;"></i>
                <span>Visualisasi Alur Kerja Pelayanan Surat</span>
            </h5>
            <p class="text-muted small mb-4">
                Pahami perbedaan proses pengesahan untuk setiap kategori alur persetujuan yang diterapkan di lingkungan RW 013.
            </p>

            <div class="row g-3">
                {{-- Alur 1: Berjenjang (RT & RW) --}}
                <div class="col-12 col-md-6">
                    <div class="p-3 rounded-4 border bg-light h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-primary small d-flex align-items-center gap-1.5">
                                <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                                Persetujuan Berjenjang (RT & RW)
                            </span>
                            <span class="badge bg-primary text-white rounded-pill px-2.5">{{ $alurStats['rt_then_rw'] }} Layanan</span>
                        </div>
                        <p class="small text-secondary mb-3">Wajib verifikasi berurutan dari Ketua RT setempat, kemudian diverifikasi dan disahkan oleh Ketua RW 013.</p>
                        <div class="d-flex align-items-center gap-1 text-center small fw-semibold flex-wrap">
                            <span class="badge bg-white text-dark border px-2.5 py-1.5 rounded-3">1. Warga Ajukan</span>
                            <i data-lucide="arrow-right" class="text-muted" style="width: 14px; height: 14px;"></i>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5 rounded-3">2. Verifikasi RT</span>
                            <i data-lucide="arrow-right" class="text-muted" style="width: 14px; height: 14px;"></i>
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1.5 rounded-3">3. Pengesahan RW</span>
                            <i data-lucide="arrow-right" class="text-muted" style="width: 14px; height: 14px;"></i>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-3">4. Terbit Sah</span>
                        </div>
                    </div>
                </div>

                {{-- Alur 2: Mandiri RT Saja --}}
                <div class="col-12 col-md-6">
                    <div class="p-3 rounded-4 border bg-light h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-success small d-flex align-items-center gap-1.5">
                                <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i>
                                Persetujuan RT Saja (Mandiri RT)
                            </span>
                            <span class="badge bg-success text-white rounded-pill px-2.5">{{ $alurStats['rt_only'] }} Layanan</span>
                        </div>
                        <p class="small text-secondary mb-3">Kewenangan penuh di tangan Ketua RT setempat. Dokumen langsung terbit tanpa antrean pemeriksaan di tingkat RW.</p>
                        <div class="d-flex align-items-center gap-1 text-center small fw-semibold flex-wrap">
                            <span class="badge bg-white text-dark border px-2.5 py-1.5 rounded-3">1. Warga Ajukan</span>
                            <i data-lucide="arrow-right" class="text-muted" style="width: 14px; height: 14px;"></i>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-3">2. Verifikasi & TTD RT</span>
                            <i data-lucide="arrow-right" class="text-muted" style="width: 14px; height: 14px;"></i>
                            <span class="badge bg-success text-white px-2.5 py-1.5 rounded-3">3. Dokumen Terbit</span>
                        </div>
                    </div>
                </div>

                {{-- Alur 3: Pengantar Kelurahan --}}
                <div class="col-12 col-md-6">
                    <div class="p-3 rounded-4 border bg-light h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-info small d-flex align-items-center gap-1.5">
                                <i data-lucide="send" style="width: 16px; height: 16px;"></i>
                                Pengantar Kelurahan Sukamiskin
                            </span>
                            <span class="badge bg-info text-white rounded-pill px-2.5">{{ $alurStats['pengantar_kelurahan'] }} Layanan</span>
                        </div>
                        <p class="small text-secondary mb-3">Surat pengantar resmi lingkungan RT & RW yang menjadi berkas wajib untuk pengurusan administrasi di Kantor Kelurahan.</p>
                        <div class="d-flex align-items-center gap-1 text-center small fw-semibold flex-wrap">
                            <span class="badge bg-white text-dark border px-2.5 py-1.5 rounded-3">Warga</span>
                            <i data-lucide="arrow-right" class="text-muted" style="width: 14px; height: 14px;"></i>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5 rounded-3">RT</span>
                            <i data-lucide="arrow-right" class="text-muted" style="width: 14px; height: 14px;"></i>
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1.5 rounded-3">RW 013</span>
                            <i data-lucide="arrow-right" class="text-muted" style="width: 14px; height: 14px;"></i>
                            <span class="badge bg-primary text-white px-2.5 py-1.5 rounded-3">Kantor Kelurahan</span>
                        </div>
                    </div>
                </div>

                {{-- Alur 4: Internal RW --}}
                <div class="col-12 col-md-6">
                    <div class="p-3 rounded-4 border bg-light h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-warning-emphasis small d-flex align-items-center gap-1.5">
                                <i data-lucide="building-2" style="width: 16px; height: 16px;"></i>
                                Administrasi Internal RW
                            </span>
                            <span class="badge bg-warning text-dark rounded-pill px-2.5">{{ $alurStats['internal_rw'] }} Layanan</span>
                        </div>
                        <p class="small text-secondary mb-3">Dokumen keputusan, surat tugas, atau rekomendasi kader lingkungan yang diterbitkan langsung oleh Pengurus RW 013.</p>
                        <div class="d-flex align-items-center gap-1 text-center small fw-semibold flex-wrap">
                            <span class="badge bg-white text-dark border px-2.5 py-1.5 rounded-3">Pemohon / Kader</span>
                            <i data-lucide="arrow-right" class="text-muted" style="width: 14px; height: 14px;"></i>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5 rounded-3">Pengurus RW</span>
                            <i data-lucide="arrow-right" class="text-muted" style="width: 14px; height: 14px;"></i>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-3">Surat Keputusan Terbit</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Konfigurasi Alur per Jenis Surat --}}
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3">Konfigurasi Alur Pelayanan untuk Setiap Jenis Surat:</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold text-uppercase">No & Nama Surat</th>
                            <th class="text-secondary small fw-bold text-uppercase">Kategori</th>
                            <th class="text-secondary small fw-bold text-uppercase">Alur Persetujuan Saat Ini</th>
                            <th class="text-end pe-3 text-secondary small fw-bold text-uppercase">Ubah Alur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allTypes as $idx => $typeItem)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $typeItem->nama }}</div>
                                    <span class="badge bg-light text-secondary border font-monospace small">{{ $typeItem->singkatan }}</span>
                                </td>
                                <td>{{ $typeItem->kategori_label }}</td>
                                <td>
                                    <span class="badge bg-{{ $typeItem->alur_badge_color }}-subtle text-{{ $typeItem->alur_badge_color }} border border-{{ $typeItem->alur_badge_color }}-subtle rounded-pill px-3 py-1">
                                        {{ $typeItem->alur_label }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('rw.surat_master.update_alur', $typeItem->id) }}" method="POST" class="d-inline-flex gap-2 justify-content-end align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <select name="alur_persetujuan" class="form-select form-select-sm rounded-3" style="max-width: 260px;">
                                            <option value="rt_then_rw" {{ $typeItem->alur_persetujuan == 'rt_then_rw' ? 'selected' : '' }}>Persetujuan Berjenjang (RT & RW)</option>
                                            <option value="rt_only" {{ $typeItem->alur_persetujuan == 'rt_only' ? 'selected' : '' }}>Persetujuan RT Saja</option>
                                            <option value="pengantar_kelurahan" {{ $typeItem->alur_persetujuan == 'pengantar_kelurahan' ? 'selected' : '' }}>Verifikasi RT & Pengantar Kelurahan</option>
                                            <option value="internal_rw" {{ $typeItem->alur_persetujuan == 'internal_rw' ? 'selected' : '' }}>Administrasi Internal RW</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold text-nowrap">
                                            Simpan Alur
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- =========================================================================
         TAB E — PRATINJAU LAYANAN WARGA (SIMULASI TAMPILAN WARGA - READ ONLY)
         ========================================================================= --}}
    @if($activeTab == 'pratinjau')
        {{-- Banner Simulasi --}}
        <div class="p-3 rounded-4 mb-4 border d-flex align-items-center justify-content-between flex-wrap gap-2 bg-white shadow-sm" style="border-left: 4px solid #1456A0 !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2.5 rounded-circle text-primary" style="background: #E3F2FD;">
                    <i data-lucide="eye" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <strong class="text-dark d-block">Mode Pratinjau Tampilan Warga (Read-Only)</strong>
                    <span class="text-muted small">Simulasi visual bagaimana katalog 18 layanan surat terlihat di smartphone dan komputer warga. Tombol pengajuan dinonaktifkan demi keamanan.</span>
                </div>
            </div>
            <span class="badge bg-primary text-white rounded-pill px-3 py-1.5 small">
                Tampilan Publik Warga RW 013
            </span>
        </div>

        {{-- Filter Kategori Warga --}}
        <div class="d-flex gap-2 mb-4 overflow-x-auto pb-1" style="scrollbar-width: none;">
            @foreach($previewCategories as $catKey => $catInfo)
                <a href="{{ route('rw.surat.index', ['tab' => 'pratinjau', 'kategori_preview' => $catKey]) }}" 
                   class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ (request('kategori_preview', 'semua') == $catKey) ? 'btn-primary shadow-sm' : 'btn-light border text-secondary' }}">
                    {{ $catInfo['label'] }} ({{ $catInfo['count'] }})
                </a>
            @endforeach
        </div>

        {{-- Grid Katalog Layanan Warga --}}
        <div class="row g-4 mb-4">
            @forelse($katalogPreview as $item)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border rounded-4 shadow-sm h-100 p-4 d-flex flex-column justify-content-between position-relative overflow-hidden" style="background: #ffffff;">
                        <div>
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    {{ $item->kategori_label }}
                                </span>
                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 11px;">
                                    {{ $item->singkatan }}
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-2" style="font-size: 1.05rem;">{{ $item->nama }}</h5>
                            <p class="text-muted small mb-3" style="font-size: 0.85rem; line-height: 1.45;">
                                {{ $item->deskripsi }}
                            </p>

                            {{-- Persyaratan --}}
                            <div class="p-3 bg-light rounded-3 mb-3 border">
                                <span class="small fw-bold text-dark d-block mb-1">
                                    <i data-lucide="check-square" style="width: 13px; height: 13px; vertical-align: middle;" class="text-primary me-1"></i>
                                    Persyaratan Dokumen:
                                </span>
                                <ul class="mb-0 ps-3 small text-secondary">
                                    @if(is_array($item->persyaratan) && count($item->persyaratan) > 0)
                                        @foreach(array_slice($item->persyaratan, 0, 3) as $syarat)
                                            <li>{{ $syarat }}</li>
                                        @endforeach
                                        @if(count($item->persyaratan) > 3)
                                            <li class="text-muted">+ {{ count($item->persyaratan) - 3 }} berkas pendukung lainnya</li>
                                        @endif
                                    @else
                                        <li>KTP & Kartu Keluarga pemohon</li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3 small border-top pt-3">
                                <div>
                                    <span class="text-muted d-block" style="font-size: 11px;">Estimasi Waktu:</span>
                                    <strong class="text-dark">{{ $item->estimasi_hari }}</strong>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted d-block" style="font-size: 11px;">Biaya Pelayanan:</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">{{ $item->biaya }}</span>
                                </div>
                            </div>

                            <button type="button" disabled class="btn btn-light border text-muted w-100 rounded-3 d-flex align-items-center justify-content-center gap-1.5 py-2 small" style="cursor: not-allowed;">
                                <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                <span>Mode Pratinjau (Non-Aktif untuk Pengurus)</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Tidak ada jenis layanan aktif pada kategori ini.</p>
                </div>
            @endforelse
        </div>
    @endif

</div>

{{-- Scoped Styles --}}
<style>
    #suratTabs .nav-link {
        color: #475569;
        background-color: transparent;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    #suratTabs .nav-link:hover {
        color: #0f172a;
        background-color: #f1f5f9;
    }
    #suratTabs .nav-link.active {
        color: #ffffff !important;
        background-color: #1456A0 !important;
        border-color: #1456A0 !important;
        box-shadow: 0 4px 12px rgba(20, 86, 160, 0.25);
    }
    #suratTabs .nav-link.active .badge {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.4) !important;
    }

    /* Dark Mode Scoped Hover for Surat Tabs */
    [data-theme="dark"] #suratTabs .nav-link,
    body.dark-mode #suratTabs .nav-link {
        color: #94A3B8;
    }
    [data-theme="dark"] #suratTabs .nav-link:not(.active):hover,
    body.dark-mode #suratTabs .nav-link:not(.active):hover {
        color: #F8FAFC !important;
        background-color: #1E2D4A !important;
        border-color: #2B3E60 !important;
    }
</style>
@endsection
