@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- ======================================================================
         PAGE HEADER & BREADCRUMBS
         ====================================================================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-icon-pill shadow-sm" style="width: 48px; height: 48px; border-radius: 14px; background: #EAF3FF; color: #1456A0; font-size: 20px;">
                <i data-lucide="folder-check" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="color: var(--wm-text) !important;">Arsip KK Resmi Dukcapil</h3>
                <p class="text-muted mb-0 small">Sistem manajemen dan verifikasi salinan digital Kartu Keluarga resmi dari Dukcapil di RW 013.</p>
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
                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">
                    Arsip KK Resmi
                </li>
            </ol>
        </nav>
    </div>

    {{-- Notifikasi Sukses / Gagal --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert" style="background: #E3F5EC; color: #16865D; border-radius: 14px;">
            <i data-lucide="check-circle-2" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-medium small">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert" style="background: #FEE2E2; color: #DC2626; border-radius: 14px;">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-medium small">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ======================================================================
         BANNER KEBIJAKAN INTEGRASI RESMI DUKCAPIL
         ====================================================================== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%); color: #FFFFFF;">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 rounded-circle bg-white bg-opacity-20 d-none d-md-block">
                <i data-lucide="shield-check" style="width: 28px; height: 28px; color: #FFFFFF;"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-white">Prinsip Legalitas Dokumen Kependudukan Resmi</h6>
                <p class="mb-0 small opacity-90" style="font-size: 0.82rem;">
                    Kartu Keluarga adalah dokumen negara yang diterbitkan resmi oleh Dinas Kependudukan dan Pencatatan Sipil (Dukcapil). Aplikasi Warkas Machi RW 013 berfungsi sebagai repositori arsip salinan autentik, verifikasi fisik dokumen asli, dan penatausahaan administrasi warga. Pencetakan salinan dokumen hanya diizinkan bagi berkas yang telah berstatus <strong>Terverifikasi</strong>.
                </p>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         STATISTIK ARSIP KK RESMI
         ====================================================================== --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 4px solid #1456A0 !important;">
                <span class="text-muted small fw-semibold d-block">Total KK Terdaftar</span>
                <h4 class="fw-bold text-dark mt-1 mb-0">{{ number_format($stats['total_kk']) }}</h4>
                <small class="text-muted" style="font-size: 0.72rem;">Kartu Keluarga</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 4px solid #16865D !important;">
                <span class="text-muted small fw-semibold d-block">Terverifikasi Dukcapil</span>
                <h4 class="fw-bold text-success mt-1 mb-0">{{ number_format($stats['terverifikasi']) }}</h4>
                <small class="text-muted" style="font-size: 0.72rem;">Dokumen Sah Dicetak</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 4px solid #D97706 !important;">
                <span class="text-muted small fw-semibold d-block">Menunggu Verifikasi</span>
                <h4 class="fw-bold text-warning mt-1 mb-0">{{ number_format($stats['menunggu']) }}</h4>
                <small class="text-muted" style="font-size: 0.72rem;">Salinan Baru Diunggah</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 4px solid #64748B !important;">
                <span class="text-muted small fw-semibold d-block">Belum Ada Dokumen</span>
                <h4 class="fw-bold text-secondary mt-1 mb-0">{{ number_format($stats['belum_ada']) }}</h4>
                <small class="text-muted" style="font-size: 0.72rem;">Perlu Penyerahan Fisik</small>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         FILTER & SEARCH BAR CONTAINER
         ====================================================================== --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <form action="{{ route('arsip-kk.index') }}" method="GET" class="row g-2 align-items-center mb-0">
            {{-- Search Bar --}}
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; color: #1456A0;">
                        <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-1" placeholder="Cari No. KK atau nama kepala keluarga..." style="border-radius: 0 12px 12px 0; font-size: 0.88rem;">
                </div>
            </div>

            {{-- Filter RT (Untuk RW / Admin) --}}
            @if(!auth()->user()->isRt())
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; color: #1456A0;">
                            <i data-lucide="building-2" style="width: 16px; height: 16px;"></i>
                        </span>
                        <select name="rt" class="form-select border-start-0 ps-1" onchange="this.form.submit()" style="border-radius: 0 12px 12px 0; font-size: 0.88rem;">
                            <option value="all">Semua RT (001-008)</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ request('rt') == $i ? 'selected' : '' }}>
                                    RT {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            @endif

            {{-- Filter Status Verifikasi Dukcapil --}}
            <div class="col-6 col-md-3 col-lg-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; color: #1456A0;">
                        <i data-lucide="shield" style="width: 16px; height: 16px;"></i>
                    </span>
                    <select name="status_verifikasi" class="form-select border-start-0 ps-1" onchange="this.form.submit()" style="border-radius: 0 12px 12px 0; font-size: 0.88rem;">
                        <option value="all">Semua Status Verifikasi</option>
                        <option value="Terverifikasi" {{ request('status_verifikasi') == 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="Menunggu Verifikasi" {{ request('status_verifikasi') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="Belum Ada Dokumen" {{ request('status_verifikasi') == 'Belum Ada Dokumen' ? 'selected' : '' }}>Belum Ada Dokumen</option>
                        <option value="Tidak Valid" {{ request('status_verifikasi') == 'Tidak Valid' ? 'selected' : '' }}>Tidak Valid</option>
                        <option value="Perlu Pembaruan" {{ request('status_verifikasi') == 'Perlu Pembaruan' ? 'selected' : '' }}>Perlu Pembaruan</option>
                    </select>
                </div>
            </div>

            {{-- Reset Button --}}
            <div class="col-12 col-md-auto ms-auto d-flex align-items-center gap-2">
                @if(request('search') || request('rt') || request('status_verifikasi'))
                    <a href="{{ route('arsip-kk.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small" title="Reset Filter">
                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ======================================================================
         TABEL ARSIP KK RESMI
         ====================================================================== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4" style="width: 50px;">#</th>
                        <th>Nomor KK Resmi (Masked)</th>
                        <th>Kepala Keluarga</th>
                        <th>Wilayah RT/RW</th>
                        <th>Anggota</th>
                        <th>Tgl Terbit Dukcapil</th>
                        <th>Status Verifikasi</th>
                        <th>Status Dokumen</th>
                        <th class="pe-4 text-center" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kartuKeluargas as $kk)
                        <tr>
                            <td class="ps-4 text-muted small fw-semibold">
                                {{ $loop->iteration + ($kartuKeluargas->currentPage() - 1) * $kartuKeluargas->perPage() }}
                            </td>

                            {{-- No KK Masked --}}
                            <td>
                                <a href="{{ route('arsip-kk.show', $kk->id) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                    <i data-lucide="folder-check" style="width: 14px; height: 14px;" class="me-1"></i>
                                    {{ $kk->masked_no_kk }}
                                </a>
                                @if($kk->dokumen_kk_path)
                                    <span class="badge bg-light text-muted border ms-1" style="font-size: 0.65rem;">PDF v{{ $kk->versi_dokumen }}</span>
                                @endif
                            </td>

                            {{-- Kepala Keluarga --}}
                            <td>
                                <div class="fw-bold text-dark small">{{ $kk->nama_kepala_keluarga }}</div>
                                <div class="text-muted font-monospace" style="font-size: 0.72rem;">NIK: {{ $kk->kepalaKeluarga ? $kk->kepalaKeluarga->masked_nik : ($kk->nik_kepala_keluarga ? substr($kk->nik_kepala_keluarga, 0, 6) . '******' . substr($kk->nik_kepala_keluarga, -4) : '-') }}</div>
                            </td>

                            {{-- Wilayah --}}
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-2 py-1 fw-semibold small" style="font-size: 0.72rem;">
                                    {{ $kk->clean_rt_label }} / RW 013
                                </span>
                                <div class="text-muted small mt-1" style="font-size: 0.72rem;">No. {{ $kk->nomor_rumah ?: '-' }}</div>
                            </td>

                            {{-- Anggota --}}
                            <td>
                                <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #EAF3FF; color: #1456A0; font-size: 0.74rem;">
                                    {{ $kk->anggota_count }} Jiwa
                                </span>
                            </td>

                            {{-- Tgl Terbit --}}
                            <td class="small text-muted">
                                {{ $kk->tgl_diterbitkan_dukcapil ? $kk->tgl_diterbitkan_dukcapil->translatedFormat('d M Y') : 'Belum Terdata' }}
                            </td>

                            {{-- Status Verifikasi Dukcapil --}}
                            <td>
                                @php
                                    $badgeBg = '#F1F5F9'; $badgeColor = '#64748B';
                                    if ($kk->status_verifikasi_dukcapil === 'Terverifikasi') { $badgeBg = '#E3F5EC'; $badgeColor = '#16865D'; }
                                    elseif ($kk->status_verifikasi_dukcapil === 'Menunggu Verifikasi') { $badgeBg = '#FEF3C7'; $badgeColor = '#D97706'; }
                                    elseif (in_array($kk->status_verifikasi_dukcapil, ['Tidak Valid', 'Perlu Pembaruan'])) { $badgeBg = '#FEE2E2'; $badgeColor = '#DC2626'; }
                                @endphp
                                <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-size: 0.72rem;">
                                    <i data-lucide="shield-check" style="width: 12px; height: 12px;" class="me-1"></i>
                                    {{ $kk->status_verifikasi_dukcapil }}
                                </span>
                            </td>

                            {{-- Status Dokumen --}}
                            <td>
                                <span class="badge rounded-pill px-2 py-1 small fw-semibold" style="background: {{ $kk->status_dokumen === 'Aktif' ? '#E3F5EC' : '#F1F5F9' }}; color: {{ $kk->status_dokumen === 'Aktif' ? '#16865D' : '#64748B' }}; font-size: 0.70rem;">
                                    {{ $kk->status_dokumen }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="pe-4 text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('arsip-kk.show', $kk->id) }}" class="btn btn-sm btn-outline-primary rounded-circle p-2" title="Buka Berkas Arsip KK" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                    </a>

                                    {{-- Tombol Cetak / Stream (Ketat: Hanya jika Terverifikasi) --}}
                                    @if($kk->isVerifiedDukcapil() && $kk->hasDokumenPdf())
                                        <a href="{{ route('arsip-kk.cetak', $kk->id) }}" target="_blank" class="btn btn-sm btn-success rounded-circle p-2 text-white" title="Cetak Salinan Resmi PDF Autentik" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i data-lucide="printer" style="width: 14px; height: 14px;"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-light border rounded-circle p-2 text-muted opacity-50" title="Cetak Dinonaktifkan: Dokumen belum berstatus Terverifikasi Dukcapil" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: not-allowed;" onclick="alert('Pencetakan salinan KK resmi dinonaktifkan karena dokumen ini berstatus: {{ $kk->status_verifikasi_dukcapil }}. Pencetakan hanya diizinkan untuk dokumen yang telah Terverifikasi secara sah.');">
                                            <i data-lucide="printer" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i data-lucide="folder-x" style="width: 40px; height: 40px; opacity: 0.5;" class="mb-2 d-block mx-auto"></i>
                                <h6 class="fw-bold mb-1">Arsip KK Tidak Ditemukan</h6>
                                <p class="small text-muted mb-0">Tidak ada data Kartu Keluarga yang cocok dengan filter pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">
                Menampilkan {{ $kartuKeluargas->firstItem() ?? 0 }} - {{ $kartuKeluargas->lastItem() ?? 0 }} dari total {{ $kartuKeluargas->total() }} Kartu Keluarga
            </span>
            <div>
                {{ $kartuKeluargas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
