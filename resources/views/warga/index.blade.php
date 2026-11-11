@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- ======================================================================
         PAGE HEADER & BREADCRUMBS
         ====================================================================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-icon-pill shadow-sm" style="width: 48px; height: 48px; border-radius: 14px; background: #EAF3FF; color: #1456A0; font-size: 20px;">
                <i data-lucide="users" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="color: var(--wm-text) !important;">Data Warga RW 013</h3>
                <p class="text-muted mb-0 small">Sistem direktori kependudukan terpadu, data kartu keluarga, dan profil warga.</p>
            </div>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0 align-items-center small">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted d-inline-flex align-items-center gap-1">
                        <i data-lucide="layout-dashboard" style="width: 14px; height: 14px;"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page" style="color: var(--wm-primary) !important;">
                    Data Warga
                </li>
            </ol>
        </nav>
    </div>

    {{-- Notifikasi Sukses / Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert" style="background: #E3F5EC; color: #16865D; border-radius: 14px;">
            <i data-lucide="check-circle-2" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-medium small">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ======================================================================
         STATISTIK KEPENDUDUKAN (REAL DATABASE METRICS)
         ====================================================================== --}}
    @if(isset($stats))
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 4px solid #1456A0 !important;">
                    <span class="text-muted small fw-semibold d-block">Total Warga</span>
                    <h4 class="fw-bold text-dark mt-1 mb-0">{{ number_format($stats['total_warga']) }}</h4>
                    <small class="text-muted" style="font-size: 0.72rem;">Jiwa terdaftar</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 4px solid #16865D !important;">
                    <span class="text-muted small fw-semibold d-block">Terverifikasi</span>
                    <h4 class="fw-bold text-success mt-1 mb-0">{{ number_format($stats['total_terverifikasi']) }}</h4>
                    <small class="text-muted" style="font-size: 0.72rem;">Valid RT / RW</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 4px solid #D97706 !important;">
                    <span class="text-muted small fw-semibold d-block">Menunggu Verifikasi</span>
                    <h4 class="fw-bold text-warning mt-1 mb-0">{{ number_format($stats['total_menunggu']) }}</h4>
                    <small class="text-muted" style="font-size: 0.72rem;">Antrean periksa</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 4px solid #DC2626 !important;">
                    <span class="text-muted small fw-semibold d-block">Perlu Perbaikan</span>
                    <h4 class="fw-bold text-danger mt-1 mb-0">{{ number_format($stats['total_perlu_perbaikan']) }}</h4>
                    <small class="text-muted" style="font-size: 0.72rem;">Revisi berkas</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 4px solid #2563EB !important;">
                    <span class="text-muted small fw-semibold d-block">Total KK</span>
                    <h4 class="fw-bold text-primary mt-1 mb-0">{{ number_format($stats['total_kk']) }}</h4>
                    <small class="text-muted" style="font-size: 0.72rem;">Keluarga</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-center" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%); color: #FFFFFF;">
                    <span class="small opacity-75 d-block">Arsip KK Resmi</span>
                    <a href="{{ route('arsip-kk.index') }}" class="text-white fw-bold text-decoration-none small d-inline-flex align-items-center gap-1 mt-1">
                        <span>Buka Arsip</span>
                        <i data-lucide="arrow-up-right" style="width: 14px; height: 14px;"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- ======================================================================
         FILTER & SEARCH BAR CONTAINER
         ====================================================================== --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <form action="{{ route('warga.index') }}" method="GET" class="row g-2 align-items-center mb-0">
            {{-- 1. Search Bar (Nama, NIK, No. KK) --}}
            <div class="col-12 col-md-5 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; color: #1456A0;">
                        <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-1" placeholder="Cari nama, NIK, atau No. KK..." style="border-radius: 0 12px 12px 0; font-size: 0.88rem;">
                </div>
            </div>

            {{-- 2. Filter RT (Hanya aktif untuk RW / Admin, RT terkunci ke RT-nya) --}}
            @if(!auth()->user()->isRt())
                <div class="col-6 col-md-3 col-lg-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; color: #1456A0;">
                            <i data-lucide="building-2" style="width: 16px; height: 16px;"></i>
                        </span>
                        <select name="rt" class="form-select border-start-0 ps-1" onchange="this.form.submit()" style="border-radius: 0 12px 12px 0; font-size: 0.88rem;">
                            <option value="all">Semua Wilayah RT (001-008)</option>
                            @for($i = 1; $i <= 8; $i++)
                                @php $rtVal = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                                <option value="{{ $i }}" {{ request('rt') == $i ? 'selected' : '' }}>
                                    Wilayah RT {{ $rtVal }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            @endif

            {{-- 3. Filter Status Verifikasi --}}
            <div class="col-6 col-md-3 col-lg-2">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; color: #1456A0;">
                        <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i>
                    </span>
                    <select name="status_verifikasi" class="form-select border-start-0 ps-1" onchange="this.form.submit()" style="border-radius: 0 12px 12px 0; font-size: 0.88rem;">
                        <option value="all">Semua Status</option>
                        <option value="Terverifikasi RW" {{ request('status_verifikasi') == 'Terverifikasi RW' ? 'selected' : '' }}>Terverifikasi RW</option>
                        <option value="Terverifikasi RT" {{ request('status_verifikasi') == 'Terverifikasi RT' ? 'selected' : '' }}>Terverifikasi RT</option>
                        <option value="Menunggu Verifikasi RT" {{ request('status_verifikasi') == 'Menunggu Verifikasi RT' ? 'selected' : '' }}>Menunggu RT</option>
                        <option value="Menunggu Verifikasi RW" {{ request('status_verifikasi') == 'Menunggu Verifikasi RW' ? 'selected' : '' }}>Menunggu RW</option>
                        <option value="Belum Diverifikasi" {{ request('status_verifikasi') == 'Belum Diverifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
                        <option value="Perlu Perbaikan" {{ request('status_verifikasi') == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                    </select>
                </div>
            </div>

            {{-- 4. Filter Kelompok Usia --}}
            <div class="col-6 col-md-3 col-lg-2">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; color: #1456A0;">
                        <i data-lucide="calendar" style="width: 16px; height: 16px;"></i>
                    </span>
                    <select name="age_group" class="form-select border-start-0 ps-1" onchange="this.form.submit()" style="border-radius: 0 12px 12px 0; font-size: 0.88rem;">
                        <option value="all">Semua Kategori Usia</option>
                        <option value="balita" {{ request('age_group') == 'balita' ? 'selected' : '' }}>Balita (0 - 5 Thn)</option>
                        <option value="anak" {{ request('age_group') == 'anak' ? 'selected' : '' }}>Anak-anak (6 - 12 Thn)</option>
                        <option value="remaja" {{ request('age_group') == 'remaja' ? 'selected' : '' }}>Remaja (13 - 19 Thn)</option>
                        <option value="dewasa" {{ request('age_group') == 'dewasa' ? 'selected' : '' }}>Dewasa (20 - 55 Thn)</option>
                        <option value="lansia" {{ request('age_group') == 'lansia' ? 'selected' : '' }}>Lansia (56+ Thn)</option>
                    </select>
                </div>
            </div>

            {{-- 4. Tombol Filter & Reset / Tambah Warga --}}
            <div class="col-12 col-md-auto ms-auto d-flex align-items-center gap-2">
                @if(request('search') || (request('rt') && request('rt') !== 'all') || (request('age_group') && request('age_group') !== 'all'))
                    <a href="{{ route('warga.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small" title="Reset Filter">
                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    </a>
                @endif

                @if(in_array(auth()->user()->role, ['admin', 'bendahara', 'rt']))
                    <a href="{{ route('warga.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-bold small d-inline-flex align-items-center gap-2 shadow-sm" style="background: #1456A0; border-color: #1456A0;">
                        <i data-lucide="user-plus" style="width: 16px; height: 16px;"></i>
                        <span>Tambah Warga</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ======================================================================
         TABEL DATA WARGA (MODERN, CLEAN, DILENGKAPI FOTO & MODAL DETAIL KELUARGA)
         ====================================================================== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        {{-- Card Header Summary --}}
        <div class="px-4 py-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: #EAF3FF; color: #1456A0; font-size: 0.8rem;">
                    Total Warga: {{ method_exists($warga, 'total') ? $warga->total() : $warga->count() }} Orang
                </span>
                @if(request('rt') && request('rt') !== 'all')
                    <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-bold" style="font-size: 0.78rem;">
                        Wilayah RT {{ str_pad(request('rt'), 3, '0', STR_PAD_LEFT) }}
                    </span>
                @endif
            </div>

            @if(method_exists($warga, 'hasPages') && $warga->hasPages())
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted me-1">Hal. {{ $warga->currentPage() }} / {{ $warga->lastPage() }}</small>
                    <a href="{{ $warga->appends(request()->query())->previousPageUrl() ?? '#' }}"
                       class="btn btn-sm btn-outline-primary rounded-pill px-3 {{ $warga->onFirstPage() ? 'disabled' : '' }}" style="font-size: 0.8rem;">
                        Previous
                    </a>
                    <a href="{{ $warga->appends(request()->query())->nextPageUrl() ?? '#' }}"
                       class="btn btn-sm btn-primary rounded-pill px-3 {{ $warga->hasMorePages() ? '' : 'disabled' }}" style="background: #1456A0; border-color: #1456A0; font-size: 0.8rem;">
                        Next
                    </a>
                </div>
            @endif
        </div>

        {{-- Table Element --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead style="background: #F8FAFC; color: #475569; font-weight: 600; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4" style="width: 50px;">#</th>
                        <th>Profil & Nama Warga</th>
                        <th>Nomor KK & NIK</th>
                        <th>Gender & Usia</th>
                        <th>Wilayah</th>
                        <th>Kontak & Alamat</th>
                        <th>Status</th>
                        <th class="pe-4 text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warga as $item)
                        <tr>
                            {{-- 1. Number --}}
                            <td class="ps-4 text-muted small fw-semibold">
                                {{ $loop->iteration + ((method_exists($warga, 'currentPage') ? $warga->currentPage() : 1) - 1) * (method_exists($warga, 'perPage') ? $warga->perPage() : 100) }}
                            </td>

                            {{-- 2. Foto Profil & Nama Warga (Clickable ke Detail Keluarga) --}}
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <a href="javascript:void(0);" 
                                       class="warga-avatar-link position-relative d-inline-block text-decoration-none" 
                                       data-no-kk="{{ $item->no_kk }}" 
                                       title="Klik untuk melihat Detail Keluarga">
                                        <img src="{{ $item->avatar_url }}" 
                                             alt="Foto {{ $item->name }}" 
                                             class="rounded-circle border shadow-sm" 
                                             style="width: 44px; height: 44px; object-fit: cover; background: #EAF3FF;">
                                    </a>
                                    <div>
                                        <a href="javascript:void(0);" 
                                           class="warga-name-link fw-bold text-decoration-none text-dark d-inline-flex align-items-center gap-1 hover-primary" 
                                           data-no-kk="{{ $item->no_kk }}" 
                                           title="Klik untuk membuka Detail Keluarga">
                                            <span>{{ $item->name }}</span>
                                            @if($item->role === 'rt')
                                                <span class="badge rounded-pill fw-bold" style="background: #1456A0; color: #FFFFFF; font-size: 0.68rem;">
                                                    Ketua {{ $item->clean_rt_label }}
                                                </span>
                                            @endif
                                        </a>
                                        <div class="text-muted small" style="font-size: 0.76rem;">
                                            <span class="text-primary fw-medium"><i data-lucide="mail" style="width: 12px; height: 12px; margin-right: 2px;"></i>{{ $item->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- 3. Nomor KK & NIK (Masked Sesuai Standar Privasi Dukcapil) --}}
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <a href="javascript:void(0);" 
                                       class="warga-name-link font-monospace text-decoration-none fw-semibold text-primary" 
                                       data-no-kk="{{ $item->no_kk }}" 
                                       title="Lihat seluruh anggota KK ini">
                                        <i data-lucide="folder-check" style="width: 13px; height: 13px; margin-right: 3px;"></i>KK: {{ $item->masked_no_kk }}
                                    </a>
                                    <span class="font-monospace text-muted small" style="font-size: 0.76rem;" title="NIK Tersamarkan untuk Keamanan">
                                        NIK: {{ $item->masked_nik }}
                                    </span>
                                </div>
                            </td>

                            {{-- 4. Gender & Usia --}}
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div>
                                        @if($item->gender === 'Laki-laki')
                                            <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #EAF3FF; color: #1456A0; border: 1px solid #C7DFFF; font-size: 0.72rem;">
                                                Laki-laki
                                            </span>
                                        @elseif($item->gender === 'Perempuan')
                                            <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; font-size: 0.72rem;">
                                                Perempuan
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-light text-muted border" style="font-size: 0.72rem;">-</span>
                                        @endif
                                    </div>
                                    @if($item->tanggal_lahir)
                                        <div class="text-muted small d-flex align-items-center gap-1" style="font-size: 0.76rem;">
                                            <i data-lucide="calendar" style="width: 12px; height: 12px;"></i>
                                            <span>{{ \Carbon\Carbon::parse($item->tanggal_lahir)->translatedFormat('d M Y') }}</span>
                                            <span class="fw-bold text-dark">({{ $item->age ?? 0 }} thn)</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- 5. Wilayah RT / RW --}}
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-2 py-1 fw-semibold small" style="font-size: 0.75rem;">
                                    {{ $item->clean_rt_label }} / RW {{ $item->rw_number ?? '013' }}
                                </span>
                                <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                    No. Rumah: <strong>{{ $item->house_number ?? '-' }}</strong>
                                </div>
                            </td>

                            {{-- 6. Kontak & Alamat --}}
                            <td>
                                <div class="text-dark small d-flex align-items-center gap-1">
                                    <i data-lucide="phone" style="width: 13px; height: 13px; color: #16865D;"></i>
                                    <span>{{ $item->phone ?? '-' }}</span>
                                </div>
                                <div class="text-muted text-truncate" style="max-width: 180px; font-size: 0.76rem;" title="{{ $item->address }}">
                                    {{ $item->address ?? '-' }}
                                </div>
                            </td>

                            {{-- 7. Status Kependudukan & Verifikasi --}}
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @php
                                        $vColor = '#64748B'; $vBg = '#F1F5F9';
                                        if ($item->status_verifikasi === 'Terverifikasi RW') { $vColor = '#16865D'; $vBg = '#E3F5EC'; }
                                        elseif ($item->status_verifikasi === 'Terverifikasi RT') { $vColor = '#1456A0'; $vBg = '#EAF3FF'; }
                                        elseif (str_contains($item->status_verifikasi ?? '', 'Menunggu')) { $vColor = '#D97706'; $vBg = '#FEF3C7'; }
                                        elseif (in_array($item->status_verifikasi, ['Perlu Perbaikan', 'Ditolak'])) { $vColor = '#DC2626'; $vBg = '#FEE2E2'; }
                                    @endphp
                                    <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: {{ $vBg }}; color: {{ $vColor }}; font-size: 0.70rem;">
                                        {{ $item->status_verifikasi ?: 'Belum Diverifikasi' }}
                                    </span>
                                    <span class="badge rounded-pill px-2 py-1 fw-semibold text-muted bg-light border" style="font-size: 0.68rem;">
                                        {{ $item->status_domisili ?: 'Warga Tetap' }}
                                    </span>
                                </div>
                            </td>

                            {{-- 8. Aksi (Detail Profil, Detail Keluarga, Edit, Hapus) --}}
                            <td class="pe-4 text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    {{-- Tombol Detail Profil Lengkap --}}
                                    <a href="{{ route('warga.show', $item->id) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-circle p-2" 
                                       title="Buka Profil Lengkap & Dokumen"
                                       style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                    </a>

                                    {{-- Tombol Detail Keluarga --}}
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-circle p-2 warga-name-link text-primary" 
                                            data-no-kk="{{ $item->no_kk }}" 
                                            title="Lihat Detail Kartu Keluarga"
                                            style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i data-lucide="users" style="width: 14px; height: 14px;"></i>
                                    </button>

                                    @if(in_array(auth()->user()->role, ['admin', 'bendahara']))
                                        {{-- Edit Warga --}}
                                        <a href="{{ route('warga.edit', $item) }}" 
                                           class="btn btn-sm btn-light border rounded-circle p-2 text-primary" 
                                           title="Edit Data Warga"
                                           style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                        </a>

                                        {{-- Toggle Status --}}
                                        <form action="{{ route('warga.toggleActive', $item) }}" method="POST" class="m-0 d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-light border rounded-circle p-2 text-warning" 
                                                    title="Ubah Status Aktif/Nonaktif"
                                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i data-lucide="power" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>

                                        {{-- Hapus Warga --}}
                                        <form action="{{ route('warga.destroy', $item) }}" method="POST" class="m-0 d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-light border rounded-circle p-2 text-danger" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data warga ini?');" 
                                                    title="Hapus Warga"
                                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i data-lucide="user-x" style="width: 42px; height: 42px; color: #94A3B8; margin-bottom: 8px;"></i>
                                    <h6 class="fw-bold mb-1">Data Warga Tidak Ditemukan</h6>
                                    <p class="small text-muted mb-0">Coba periksa kata kunci pencarian atau ubah pilihan filter wilayah RT.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">
                Menampilkan {{ method_exists($warga, 'firstItem') ? ($warga->firstItem() ?? 0) : 1 }} - {{ method_exists($warga, 'lastItem') ? ($warga->lastItem() ?? 0) : count($warga) }} dari total {{ method_exists($warga, 'total') ? $warga->total() : count($warga) }} warga
            </span>
            <div>
                {{ method_exists($warga, 'links') ? $warga->links() : '' }}
            </div>
        </div>
    </div>
</div>

{{-- ======================================================================
     MODAL DETAIL KELUARGA & SELURUH ANGGOTA (DENGAN FOTO PROFIL SETIAP ANGGOTA)
     ====================================================================== --}}
<div class="modal fade" id="detailKeluargaModal" tabindex="-1" aria-labelledby="detailKeluargaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            {{-- Modal Header dengan Banner --}}
            <div class="modal-header border-0 text-white p-4 position-relative" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%);">
                <div class="d-flex align-items-center gap-3">
                    <img src="" id="modalHeadAvatar" alt="Kepala Keluarga" class="rounded-circle border border-2 border-white shadow" style="width: 56px; height: 56px; object-fit: cover; background: #fff;">
                    <div>
                        <span class="badge rounded-pill bg-light text-primary fw-bold mb-1 px-3 py-1" style="font-size: 0.72rem;">
                            <i data-lucide="id-card" style="width: 12px; height: 12px; margin-right: 2px;"></i> KARTU KELUARGA RW 013
                        </span>
                        <h5 class="modal-title fw-bold text-white mb-0" id="modalHeadName">-</h5>
                        <p class="text-white opacity-75 small mb-0 font-monospace" id="modalNoKkTitle">No. KK: -</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                {{-- Detail Alamat & Rumah --}}
                <div class="card border-0 rounded-3 p-3 mb-4" style="background: #F4F7FB;">
                    <div class="row g-3 small">
                        <div class="col-sm-6">
                            <span class="text-muted d-block small">Alamat Lengkap</span>
                            <strong class="text-dark" id="modalAlamatLengkap">-</strong>
                        </div>
                        <div class="col-sm-3">
                            <span class="text-muted d-block small">Status Kepemilikan</span>
                            <span class="badge rounded-pill bg-light text-primary border px-2 py-1 fw-bold" id="modalStatusRumah">-</span>
                        </div>
                        <div class="col-sm-3">
                            <span class="text-muted d-block small">Jumlah Anggota</span>
                            <strong class="text-primary fs-6" id="modalTotalAnggota">0 Orang</strong>
                        </div>
                    </div>
                </div>

                {{-- Collapse Form Tambah Anggota (Untuk Pengurus) --}}
                @if(in_array(strtolower(auth()->user()->role), ['admin', 'bendahara', 'rt']))
                    <div class="mb-4">
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold small d-inline-flex align-items-center gap-1 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formAnggotaCollapse" aria-expanded="false" aria-controls="formAnggotaCollapse">
                            <i data-lucide="user-plus" style="width: 14px; height: 14px;"></i>
                            <span>Tambah Anggota ke KK Ini</span>
                        </button>

                        <div class="collapse mt-3" id="formAnggotaCollapse">
                            <div class="card card-body border-0 bg-light rounded-4 shadow-sm p-3">
                                <h6 class="fw-bold mb-3 text-dark small d-flex align-items-center gap-2">
                                    <i data-lucide="user-plus" style="width: 16px; height: 16px; color: #1456A0;"></i>
                                    <span>Formulir Tambah Anggota Keluarga</span>
                                </h6>
                                <form id="formTambahAnggota">
                                    @csrf
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted mb-1">Nama Lengkap *</label>
                                            <input type="text" class="form-control form-control-sm rounded-3" name="name" placeholder="Contoh: Alesha Putri" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted mb-1">Jenis Kelamin *</label>
                                            <select class="form-select form-select-sm rounded-3" name="gender" required>
                                                <option value="Laki-laki">Laki-laki</option>
                                                <option value="Perempuan">Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted mb-1">Tanggal Lahir *</label>
                                            <input type="date" class="form-control form-control-sm rounded-3" name="tanggal_lahir" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted mb-1">NIK (16 Digit)</label>
                                            <input type="text" class="form-control form-control-sm rounded-3" name="nik" placeholder="Contoh: 327510...">
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted mb-1">No. WhatsApp / HP</label>
                                            <input type="text" class="form-control form-control-sm rounded-3" name="phone" placeholder="08xxx">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted mb-1">Email (Opsional)</label>
                                            <input type="email" class="form-control form-control-sm rounded-3" name="email" placeholder="Kosongkan jika otomatis">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold" id="btnSubmitAnggota" style="background: #1456A0; border-color: #1456A0;">
                                        Simpan Anggota Baru
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Tabel Daftar Seluruh Anggota Keluarga Lengkap dengan Foto --}}
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark small text-uppercase d-flex align-items-center gap-2">
                        <i data-lucide="users" style="width: 16px; height: 16px; color: #1456A0;"></i>
                        <span>Daftar Anggota Keluarga</span>
                    </h6>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle border rounded-3 overflow-hidden small mb-0">
                        <thead class="table-light text-muted" style="font-size: 0.76rem; text-transform: uppercase;">
                            <tr>
                                <th class="ps-3" style="width: 40px;">#</th>
                                <th>Anggota</th>
                                <th>Gender</th>
                                <th>Hubungan / Peran</th>
                                <th>Tgl Lahir / Usia</th>
                                <th>NIK</th>
                            </tr>
                        </thead>
                        <tbody id="modalMemberList">
                            {{-- Populated via AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold small" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-primary:hover {
        color: #1456A0 !important;
    }
</style>

{{-- ======================================================================
     JAVASCRIPT LOGIC: AJAX DETAIL KELUARGA & LUCIDE ICONS
     ====================================================================== --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            lucide.createIcons();
        }

        const modalElement = document.getElementById('detailKeluargaModal');
        const modal = new bootstrap.Modal(modalElement);
        let currentNoKk = null;

        function loadKeluarga(noKk) {
            currentNoKk = noKk;
            document.getElementById('modalNoKkTitle').textContent = 'Memuat Kartu Keluarga: ' + noKk + '...';
            document.getElementById('modalMemberList').innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span> Memuat data keluarga...</td></tr>';

            fetch(`/warga/keluarga/${noKk}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalNoKkTitle').textContent = 'No. KK: ' + data.no_kk;
                    document.getElementById('modalHeadName').textContent = data.head_name || 'Keluarga ' + data.no_kk;
                    document.getElementById('modalAlamatLengkap').textContent = data.address + ' (' + data.rt_rw + ' - No. ' + data.house_number + ')';
                    document.getElementById('modalStatusRumah').textContent = data.status_rumah;
                    document.getElementById('modalTotalAnggota').textContent = data.total_anggota + ' Orang';

                    const avatarImg = document.getElementById('modalHeadAvatar');
                    if (avatarImg) {
                        avatarImg.src = data.head_avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.head_name)}&background=1456A0&color=FFFFFF`;
                    }

                    const memberList = document.getElementById('modalMemberList');
                    let html = '';

                    data.members.forEach((m, index) => {
                        let genderBadge = m.gender === 'Laki-laki' 
                            ? '<span class="badge rounded-pill px-2 py-1 fw-bold" style="background:#EAF3FF;color:#1456A0;border:1px solid #C7DFFF;font-size:0.72rem;">Laki-laki</span>' 
                            : '<span class="badge rounded-pill px-2 py-1 fw-bold" style="background:#FEE2E2;color:#DC2626;border:1px solid #FECACA;font-size:0.72rem;">Perempuan</span>';

                        let peranBadge = '';
                        if (m.peran.includes('Kepala') || m.peran.includes('Ketua')) {
                            peranBadge = '<span class="badge rounded-pill px-3 py-1 text-white fw-bold" style="background:#1456A0;font-size:0.72rem;">👑 ' + m.peran + '</span>';
                        } else if (m.peran.includes('Ibu')) {
                            peranBadge = '<span class="badge rounded-pill px-3 py-1 fw-bold" style="background:#FEF3C7;color:#D97706;border:1px solid #FDE68A;font-size:0.72rem;">❤️ ' + m.peran + '</span>';
                        } else if (m.peran.includes('Balita')) {
                            peranBadge = '<span class="badge rounded-pill px-3 py-1 text-white fw-bold" style="background:#EC4899;font-size:0.72rem;">👶 ' + m.peran + '</span>';
                        } else if (m.peran.includes('Kakek') || m.peran.includes('Nenek')) {
                            peranBadge = '<span class="badge rounded-pill px-3 py-1 text-dark fw-bold" style="background:#E2E8F0;font-size:0.72rem;">👴 ' + m.peran + '</span>';
                        } else {
                            peranBadge = '<span class="badge rounded-pill bg-light text-dark border px-2 py-1 fw-semibold" style="font-size:0.72rem;">' + m.peran + '</span>';
                        }

                        let memberAvatar = m.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(m.name)}&background=1456A0&color=FFFFFF`;

                        html += `
                            <tr>
                                <td class="ps-3 text-muted">${index + 1}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="${memberAvatar}" alt="${m.name}" class="rounded-circle border" style="width:34px;height:34px;object-fit:cover;background:#EAF3FF;">
                                        <div>
                                            <div class="fw-bold text-dark">${m.name}</div>
                                            <div class="text-muted" style="font-size:0.74rem;">${m.phone !== '-' ? m.phone : ''}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>${genderBadge}</td>
                                <td>${peranBadge}</td>
                                <td>
                                    <div class="fw-medium text-dark">${m.tanggal_lahir}</div>
                                    <small class="text-muted">(${m.usia})</small>
                                </td>
                                <td class="font-monospace text-secondary" style="font-size:0.78rem;">${m.nik}</td>
                            </tr>
                        `;
                    });

                    memberList.innerHTML = html;
                    modal.show();
                    if (window.lucide) lucide.createIcons();
                })
                .catch(err => {
                    console.error('Error fetching keluarga:', err);
                    alert('Gagal mengambil data keluarga.');
                });
        }

        // Event Listener untuk semua link warga & avatar
        document.querySelectorAll('.warga-name-link, .warga-avatar-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const noKk = this.getAttribute('data-no-kk');
                if (!noKk || noKk === '-') {
                    alert('Nomor KK warga ini belum diatur.');
                    return;
                }

                // Reset collapse form
                const collapseEl = document.getElementById('formAnggotaCollapse');
                if (collapseEl && collapseEl.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                    if (bsCollapse) bsCollapse.hide();
                }

                loadKeluarga(noKk);
            });
        });

        // Submit Form Tambah Anggota Keluarga
        const formTambah = document.getElementById('formTambahAnggota');
        if (formTambah) {
            formTambah.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!currentNoKk) return;

                const btnSubmit = document.getElementById('btnSubmitAnggota');
                btnSubmit.disabled = true;
                btnSubmit.textContent = 'Menyimpan...';

                const formData = new FormData(formTambah);

                fetch(`/warga/keluarga/${currentNoKk}/anggota`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = 'Simpan Anggota Baru';

                    if (res.success) {
                        alert(res.message);
                        formTambah.reset();

                        const collapseEl = document.getElementById('formAnggotaCollapse');
                        if (collapseEl) {
                            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                            if (bsCollapse) bsCollapse.hide();
                        }

                        loadKeluarga(currentNoKk);
                    } else {
                        alert(res.message || 'Terjadi kesalahan.');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = 'Simpan Anggota Baru';
                    alert('Gagal menambah anggota keluarga.');
                });
            });
        }
    });
</script>
@endsection
