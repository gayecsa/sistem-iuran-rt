@extends('layouts.app')

@section('title', 'Layanan Penerbitan Surat Resmi Terpadu')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" style="max-width: 1650px;">

    {{-- Breadcrumb & Header Title --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i data-lucide="home" class="me-1" style="width:14px;height:14px;"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Layanan Surat Terpadu</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span>Katalog Layanan Surat Administrasi Terpadu</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem; font-weight: 600;">
                    18 Layanan Resmi &bull; RW 013 Sukamiskin
                </span>
            </h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Sistem pelayanan surat keterangan lingkungan, surat pengantar resmi, dan rekomendasi warga RT 001 - RT 008, Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung 40293.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @if(Auth::user()->isRt())
                <a href="{{ route('rt.surat.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 shadow-sm" style="font-weight: 600; font-size: 0.88rem; border-radius: 10px;">
                    <i data-lucide="file-check-2" style="width:18px;height:18px;"></i>
                    <span>Inbox Verifikasi RT</span>
                </a>
            @elseif(Auth::user()->isRw())
                <a href="{{ route('rw.surat_master.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm" style="font-weight: 600; font-size: 0.88rem; border-radius: 10px;" title="Kelola Katalog, Biaya & Template Surat">
                    <i data-lucide="settings-2" style="width:18px;height:18px;"></i>
                    <span>Kelola Katalog & Template</span>
                </a>
                <a href="{{ route('rw.surat.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 shadow-sm" style="font-weight: 600; font-size: 0.88rem; border-radius: 10px;">
                    <i data-lucide="file-badge-2" style="width:18px;height:18px;"></i>
                    <span>Persetujuan Surat RW</span>
                </a>
            @endif

            <a href="#katalog-layanan" class="btn text-white d-inline-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%); font-weight: 600; font-size: 0.88rem; border-radius: 10px; padding: 9px 18px;">
                <i data-lucide="plus-circle" style="width:18px;height:18px;"></i>
                <span>Pilih Surat & Ajukan</span>
            </a>
        </div>
    </div>

    {{-- Alert Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center gap-3 p-3 mb-4">
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
        <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center gap-3 p-3 mb-4">
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

    {{-- STATISTIK REAL-TIME PENGAJUAN SURAT --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #1456A0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Total Pengajuan</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(20, 86, 160, 0.1); color: #1456A0;">
                        <i data-lucide="files" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['total'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Semua berkas kependudukan</span>
            </div>
        </div>

        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #F59E0B !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Menunggu RT</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.15); color: #D97706;">
                        <i data-lucide="hourglass" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['menunggu_rt'] }}</h3>
                <span class="text-warning fw-semibold" style="font-size: 0.75rem;">Verifikasi awal tingkat RT</span>
            </div>
        </div>

        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #0284C7 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Menunggu RW</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(2, 132, 199, 0.15); color: #0284C7;">
                        <i data-lucide="file-clock" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['menunggu_rw'] }}</h3>
                <span class="text-info fw-semibold" style="font-size: 0.75rem;">Pengesahan nomor resmi RW</span>
            </div>
        </div>

        <div class="col-6 col-md-3 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #16865D !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Siap Dicetak / Sah</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(22, 134, 93, 0.15); color: #16865D;">
                        <i data-lucide="check-circle-2" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ $stats['disetujui'] }}</h3>
                <span class="text-success fw-semibold" style="font-size: 0.75rem;">Lengkap bertanda tangan sah</span>
            </div>
        </div>

        <div class="col-12 col-md-3 col-xl">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #DC2626 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Ditolak / Revisi</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(220, 38, 38, 0.12); color: #DC2626;">
                        <i data-lucide="x-circle" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-danger mb-0">{{ $stats['ditolak'] }}</h3>
                <span class="text-danger" style="font-size: 0.75rem;">Perlu evaluasi pemohon</span>
            </div>
        </div>
    </div>

    {{-- WORKFLOW BANNER SOP --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, #0F2D54 0%, #123766 50%, #16437B 100%); color: #ffffff;">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3 border-bottom border-white border-opacity-10 pb-3">
            <div>
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <i data-lucide="git-commit" style="width:20px;height:20px; color:#38BDF8;"></i>
                    <span>Alur Persetujuan & Batasan Kewenangan Administrasi RT & RW</span>
                </h5>
                <p class="text-white text-opacity-75 mb-0" style="font-size: 0.85rem;">
                    Pengurus RT/RW menerbitkan surat keterangan lingkungan, surat pengantar pengurusan instansi, dan rekomendasi warga. Tidak menggantikan dokumen resmi Disdukcapil atau Kepolisian.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-20 px-3 py-2 rounded-pill small">
                    Standar SOP Pelayanan 2026
                </span>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="p-3 rounded-3 h-100" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary px-2 py-1 rounded">Alur 1</span>
                        <strong class="text-white small">Persetujuan RT & RW</strong>
                    </div>
                    <div class="text-white text-opacity-80" style="font-size: 0.8rem; line-height: 1.45;">
                        Untuk surat formal wilayah seperti <strong>SKD, SKTM, SKU, Belum Menikah</strong>. Warga mengajukan &rarr; Diverifikasi RT &rarr; Disahkan Ketua RW &rarr; Siap Cetak.
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-3 rounded-3 h-100" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-info px-2 py-1 rounded">Alur 2</span>
                        <strong class="text-white small">Pengantar ke Kelurahan / Instansi</strong>
                    </div>
                    <div class="text-white text-opacity-80" style="font-size: 0.8rem; line-height: 1.45;">
                        Untuk <strong>e-KTP, KK Baru, Pindah Domisili, Kematian, Acara Izin Keramaian</strong>. Menjadi surat pengantar resmi ke Kelurahan Sukamiskin, Disdukcapil, atau Polsek.
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-3 rounded-3 h-100" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success px-2 py-1 rounded">Alur 3</span>
                        <strong class="text-white small">Persetujuan Cepat RT Saja</strong>
                    </div>
                    <div class="text-white text-opacity-80" style="font-size: 0.8rem; line-height: 1.45;">
                        Untuk <strong>Pendataan Tamu Menginap / Penghuni Baru RT</strong>. Cukup disetujui oleh Ketua RT setempat dan langsung sah untuk ketertiban lingkungan.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================================
         KATALOG 18 LAYANAN SURAT RESMI DENGAN KATEGORI & PENCARIAN
         ===================================================================== --}}
    <div id="katalog-layanan" class="mb-5">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
            <div>
                <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i data-lucide="layout-grid" class="text-primary" style="width:24px;height:24px;"></i>
                    <span>Katalog Layanan Administrasi Terpadu</span>
                </h4>
                <p class="text-muted small mb-0">Temukan dan ajukan surat keterangan resmi sesuai kategori kebutuhan Anda.</p>
            </div>

            {{-- Form Pencarian Cepat Katalog --}}
            <form action="{{ route('surat.index') }}#katalog-layanan" method="GET" class="d-flex align-items-center gap-2" style="max-width: 360px; width: 100%;">
                <input type="hidden" name="kategori" value="{{ $kategoriFilter }}">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i data-lucide="search" style="width:14px;height:14px;"></i></span>
                    <input type="text" name="search_katalog" class="form-control border-start-0" placeholder="Cari nama surat / kata kunci..." value="{{ $searchKatalog }}">
                    @if($searchKatalog)
                        <a href="{{ route('surat.index', ['kategori' => $kategoriFilter]) }}#katalog-layanan" class="btn btn-outline-secondary" title="Hapus Pencarian">
                            <i data-lucide="x" style="width:12px;height:12px;"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3">Cari</button>
            </form>
        </div>

        {{-- KATEGORI TABS FILTER --}}
        <div class="d-flex flex-wrap align-items-center gap-2 mb-4 p-2 rounded-4 border bg-white shadow-sm overflow-x-auto">
            @foreach($categories as $catKey => $catData)
                <a href="{{ route('surat.index', ['kategori' => $catKey, 'search_katalog' => $searchKatalog]) }}#katalog-layanan" 
                   class="btn btn-sm rounded-pill d-inline-flex align-items-center gap-2 px-3 py-2 text-decoration-none transition-all {{ $kategoriFilter === $catKey ? 'btn-primary shadow-sm text-white' : 'btn-light text-secondary border-0' }}"
                   style="font-size: 0.84rem; font-weight: 600; white-space: nowrap;">
                    <span>{{ $catData['label'] }}</span>
                    <span class="badge {{ $kategoriFilter === $catKey ? 'bg-white text-primary' : 'bg-secondary bg-opacity-10 text-secondary' }} rounded-pill" style="font-size: 0.72rem;">
                        {{ $catData['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- GRID KARTU LAYANAN SURAT (18 JENIS) --}}
        @if($katalogSurat->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-4 mx-auto mb-3" style="width:70px;height:70px;">
                    <i data-lucide="search-x" class="text-muted" style="width:32px;height:32px;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Layanan Surat Tidak Ditemukan</h5>
                <p class="text-muted small mb-3">Tidak ada jenis surat yang cocok dengan kata kunci pencarian "{{ $searchKatalog }}".</p>
                <div>
                    <a href="{{ route('surat.index') }}#katalog-layanan" class="btn btn-outline-primary btn-sm px-3 py-2 rounded-3">
                        Tampilkan Semua Layanan
                    </a>
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach($katalogSurat as $item)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column transition-hover" style="background: #ffffff; border: 1px solid #E2E8F0 !important;">
                            <div class="p-4 flex-grow-1">
                                
                                {{-- Card Header: Icon & Badges --}}
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                                    <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" 
                                         style="background: rgba(20, 86, 160, 0.08); color: #1456A0;">
                                        <i data-lucide="{{ $item->icon }}" style="width:24px;height:24px;"></i>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="badge px-2 py-1 rounded-pill fw-bold" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                            {{ $item->singkatan ?: strtoupper(substr($item->kode, 0, 4)) }}
                                        </span>
                                        <span class="badge bg-{{ $item->alur_badge_color }}-subtle text-{{ $item->alur_badge_color }} border border-{{ $item->alur_badge_color }}-subtle rounded-pill" style="font-size: 0.68rem; font-weight: 600;">
                                            {{ $item->alur_label }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Title & Description --}}
                                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">{{ $item->nama }}</h5>
                                <div class="text-primary fw-semibold small mb-2" style="font-size: 0.78rem;">
                                    <i data-lucide="tag" style="width:12px;height:12px;" class="me-1"></i>{{ $item->kategori_label }}
                                </div>
                                <p class="text-muted mb-3" style="font-size: 0.83rem; min-height: 40px; line-height: 1.45;">
                                    {{ $item->deskripsi }}
                                </p>

                                {{-- Persyaratan Berkas --}}
                                <div class="p-3 rounded-3 mb-3" style="background: #F8FAFC; border: 1px dashed #CBD5E1;">
                                    <div class="fw-semibold text-secondary mb-2 d-flex align-items-center gap-1" style="font-size: 0.76rem;">
                                        <i data-lucide="clipboard-list" style="width:13px;height:13px;"></i>
                                        <span>Persyaratan Dokumen:</span>
                                    </div>
                                    <ul class="list-unstyled mb-0" style="font-size: 0.75rem; color: #475569;">
                                        @if(!empty($item->persyaratan))
                                            @foreach(array_slice($item->persyaratan, 0, 3) as $syarat)
                                                <li class="d-flex align-items-start gap-1 mb-1">
                                                    <i data-lucide="check" class="text-success mt-0" style="width:12px;height:12px;flex-shrink:0;"></i>
                                                    <span class="text-truncate">{{ $syarat }}</span>
                                                </li>
                                            @endforeach
                                            @if(count($item->persyaratan) > 3)
                                                <li class="text-muted fst-italic" style="font-size: 0.7rem;">+ {{ count($item->persyaratan) - 3 }} syarat tambahan lainnya...</li>
                                            @endif
                                        @else
                                            <li class="text-muted">KTP & Kartu Keluarga Terdaftar</li>
                                        @endif
                                    </ul>
                                </div>

                                {{-- Meta Info (Waktu & Biaya) --}}
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="font-size: 0.78rem;">
                                    <span class="text-muted d-flex align-items-center gap-1">
                                        <i data-lucide="clock" style="width:13px;height:13px;"></i>
                                        <span>{{ $item->estimasi_hari }}</span>
                                    </span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                        {{ $item->biaya }}
                                    </span>
                                </div>
                            </div>

                            {{-- Action Button --}}
                            <div class="p-3 pt-0">
                                <a href="{{ route('surat.create', ['jenis' => $item->kode]) }}" class="btn btn-outline-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 transition-all" style="font-size: 0.88rem;">
                                    <span>Ajukan Permohonan Ini</span>
                                    <i data-lucide="arrow-right" style="width:15px;height:15px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- =====================================================================
         DAFTAR RIWAYAT PENGAJUAN SURAT SAYA / LINGKUNGAN
         ===================================================================== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #ffffff;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i data-lucide="history" class="text-primary" style="width:20px;height:20px;"></i>
                        <span>Riwayat Pengajuan Surat Anda</span>
                        @if($user->isWarga())
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill small ms-1">Akun Warga: {{ $user->name }}</span>
                        @elseif($user->isRt())
                            <span class="badge bg-primary-subtle text-primary rounded-pill small ms-1">Wilayah {{ $user->clean_rt_label }}</span>
                        @else
                            <span class="badge bg-info-subtle text-info rounded-pill small ms-1">Arsip RW 013</span>
                        @endif
                    </h5>
                    <p class="text-muted small mb-0">Pantau progres persetujuan, catatan perbaikan dari RT/RW, dan unduh/cetak dokumen resmi setelah disetujui sah.</p>
                </div>

                {{-- Filter & Search Form --}}
                <form action="{{ route('surat.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="status" class="form-select form-select-sm rounded-3" style="width: auto; font-size: 0.82rem;" onchange="this.form.submit()">
                        <option value="">Semua Status Berkas</option>
                        <option value="Menunggu Verifikasi RT" {{ request('status') === 'Menunggu Verifikasi RT' ? 'selected' : '' }}>Menunggu Verifikasi RT</option>
                        <option value="Menunggu Persetujuan RW" {{ request('status') === 'Menunggu Persetujuan RW' ? 'selected' : '' }}>Menunggu Persetujuan RW</option>
                        <option value="Siap Dicetak" {{ request('status') === 'Siap Dicetak' ? 'selected' : '' }}>Siap Dicetak (Sah)</option>
                        <option value="Perlu Perbaikan (RT)" {{ request('status') === 'Perlu Perbaikan (RT)' ? 'selected' : '' }}>Perlu Perbaikan</option>
                        <option value="Ditolak RT" {{ request('status') === 'Ditolak RT' ? 'selected' : '' }}>Ditolak RT</option>
                        <option value="Ditolak RW" {{ request('status') === 'Ditolak RW' ? 'selected' : '' }}>Ditolak RW</option>
                    </select>

                    <select name="jenis" class="form-select form-select-sm rounded-3" style="width: auto; font-size: 0.82rem;" onchange="this.form.submit()">
                        <option value="">Semua Jenis Surat</option>
                        @foreach($katalogSurat as $kat)
                            <option value="{{ $kat->kode }}" {{ request('jenis') === $kat->kode ? 'selected' : '' }}>{{ $kat->nama }}</option>
                        @endforeach
                    </select>

                    @if(request()->hasAny(['status', 'jenis']))
                        <a href="{{ route('surat.index') }}" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset Filter">
                            <i data-lucide="refresh-cw" style="width:14px;height:14px;"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            @if($myLetters->isEmpty())
                <div class="text-center py-5 px-3">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width:70px;height:70px;">
                        <i data-lucide="file-question" class="text-muted" style="width:36px;height:36px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Pengajuan Surat</h5>
                    <p class="text-muted small mx-auto mb-3" style="max-width: 450px;">
                        Anda belum memiliki riwayat pengajuan surat keterangan atau pengantar di lingkungan RW 013 Sukamiskin.
                    </p>
                    <a href="#katalog-layanan" class="btn btn-primary btn-sm px-3 py-2 rounded-3">
                        <i data-lucide="plus" style="width:14px;height:14px;" class="me-1"></i>
                        Pilih Layanan & Ajukan Sekarang
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light text-secondary text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-4 py-3">No. Registrasi & Tanggal</th>
                                <th class="py-3">Jenis Dokumen</th>
                                <th class="py-3">Pemohon & Wilayah</th>
                                <th class="py-3">Keperluan / Catatan</th>
                                <th class="py-3 text-center">Status Alur</th>
                                <th class="pe-4 py-3 text-end" style="min-width: 170px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myLetters as $letter)
                                <tr>
                                    {{-- No Registrasi & Tanggal --}}
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark font-monospace" style="font-size: 0.82rem;">
                                            {{ $letter->nomor_registrasi }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.74rem;">
                                            <i data-lucide="calendar" style="width:11px;height:11px;" class="me-1"></i>
                                            {{ $letter->created_at->translatedFormat('d M Y, H:i') }}
                                        </div>
                                    </td>

                                    {{-- Jenis Dokumen --}}
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $letter->judul_surat }}</div>
                                        @if($letter->nomor_surat)
                                            <div class="text-primary font-monospace fw-bold" style="font-size: 0.75rem;">
                                                <i data-lucide="hash" style="width:11px;height:11px;" class="me-1"></i>{{ $letter->nomor_surat }}
                                            </div>
                                        @else
                                            <span class="text-muted" style="font-size: 0.74rem;">
                                                ({{ $letter->alur_persetujuan === 'rt_only' ? 'Nomor terbit setelah verifikasi RT' : 'Nomor terbit setelah pengesahan RW' }})
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Pemohon & Wilayah --}}
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $letter->nama_lengkap }}</div>
                                        <div class="text-muted" style="font-size: 0.74rem;">
                                            NIK: {{ substr($letter->nik, 0, 6) }}****** | RT {{ $letter->rt }}/RW {{ $letter->rw }}
                                        </div>
                                    </td>

                                    {{-- Keperluan --}}
                                    <td>
                                        <div class="text-truncate" style="max-width: 240px;" title="{{ $letter->keperluan }}">
                                            {{ $letter->keperluan }}
                                        </div>
                                        @if($letter->rt_approved_at && !$letter->rw_approved_at && $letter->alur_persetujuan !== 'rt_only')
                                            <div class="text-success small" style="font-size: 0.72rem;">
                                                <i data-lucide="check" style="width:11px;height:11px;"></i> RT diverifikasi: {{ $letter->rt_approved_name }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Status Berjenjang --}}
                                    <td class="text-center">
                                        @php
                                            $badgeClass = 'bg-secondary-subtle text-secondary';
                                            $icon = 'clock';
                                            if ($letter->status === 'Menunggu Verifikasi RT') {
                                                $badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                                                $icon = 'hourglass';
                                            } elseif ($letter->status === 'Menunggu Persetujuan RW') {
                                                $badgeClass = 'bg-info-subtle text-info border border-info-subtle';
                                                $icon = 'file-clock';
                                            } elseif (in_array($letter->status, ['Siap Dicetak', 'Selesai'])) {
                                                $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                                $icon = 'check-circle-2';
                                            } elseif (str_contains($letter->status, 'Ditolak')) {
                                                $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                                $icon = 'x-circle';
                                            } elseif (str_contains($letter->status, 'Perbaikan')) {
                                                $badgeClass = 'bg-warning-subtle text-danger border border-warning-subtle';
                                                $icon = 'alert-circle';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.75rem; font-weight: 600;">
                                            <i data-lucide="{{ $icon }}" style="width:12px;height:12px;"></i>
                                            <span>{{ $letter->status }}</span>
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="pe-4 text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            {{-- Tombol Tracking / Detail --}}
                                            <a href="{{ route('surat.show', $letter->id) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Lihat Status & Detail">
                                                <i data-lucide="eye" style="width:14px;height:14px;"></i>
                                                <span class="d-none d-lg-inline">Detail</span>
                                            </a>

                                            {{-- Tombol Cetak Dokumen Resmi --}}
                                            @if($letter->canPrint())
                                                <a href="{{ route('surat.cetak', $letter->id) }}" target="_blank" class="btn btn-sm btn-primary rounded-3 px-2 py-1 d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.78rem; background: #1456A0; border-color: #1456A0;" title="Cetak Surat Resmi A4">
                                                    <i data-lucide="printer" style="width:14px;height:14px;"></i>
                                                    <span>Cetak A4</span>
                                                </a>
                                            @else
                                                <button type="button" class="btn btn-sm btn-light border text-muted rounded-3 px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem; cursor: not-allowed;" title="Surat baru dapat dicetak setelah persetujuan sah lengkap" disabled>
                                                    <i data-lucide="lock" style="width:14px;height:14px;"></i>
                                                    <span class="d-none d-lg-inline">Cetak</span>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($myLetters->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $myLetters->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>

<style>
.transition-hover {
    transition: all 0.25s ease-in-out;
}
.transition-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -10px rgba(18, 55, 102, 0.15) !important;
    border-color: #1456A0 !important;
}
</style>
@endsection
