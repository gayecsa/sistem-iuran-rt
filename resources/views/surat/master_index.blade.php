@extends('layouts.app')

@section('title', 'Manajemen Katalog & Template Surat RW 013')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" style="max-width: 1650px;">

    {{-- Breadcrumb & Header Title --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i data-lucide="home" class="me-1" style="width:14px;height:14px;"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('surat.index') }}" class="text-decoration-none text-muted">Layanan Surat</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Manajemen Master Surat</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span>Manajemen Jenis, Template & Alur Surat RW 013</span>
                <span class="badge bg-primary text-white rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                    Konfigurasi Admin RW
                </span>
            </h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Kelola 18 jenis surat terpadu, konfigurasi alur persetujuan (RT saja vs RT & RW), template kalimat resmi, persyaratan berkas, dan status aktif/nonaktif.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('rw.surat.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 shadow-sm rounded-3" style="font-size: 0.88rem;">
                <i data-lucide="inbox" style="width:16px;height:16px;"></i>
                <span>Inbox Persetujuan RW</span>
            </a>
            <a href="{{ route('surat.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-2 shadow-sm rounded-3" style="font-size: 0.88rem;">
                <i data-lucide="eye" style="width:16px;height:16px;"></i>
                <span>Lihat Tampilan Warga</span>
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
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

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #1456A0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Total Jenis Surat</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(20, 86, 160, 0.1); color: #1456A0;">
                        <i data-lucide="layers" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['total'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">5 kategori administrasi</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #16865D !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Layanan Aktif</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(22, 134, 93, 0.15); color: #16865D;">
                        <i data-lucide="check-circle" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ $stats['active'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Dapat diajukan warga</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #DC2626 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Dinonaktifkan</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(220, 38, 38, 0.12); color: #DC2626;">
                        <i data-lucide="slash" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-danger mb-0">{{ $stats['inactive'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Disembunyikan sementara</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #0284C7 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Total Pengajuan Warga</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(2, 132, 199, 0.15); color: #0284C7;">
                        <i data-lucide="file-text" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-info mb-0">{{ $stats['total_requests'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Berkas tercatat di database</span>
            </div>
        </div>
    </div>

    {{-- TABEL MASTER JENIS SURAT --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #ffffff;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i data-lucide="list-filter" class="text-primary" style="width:20px;height:20px;"></i>
                        <span>Daftar 18 Layanan Surat Resmi Warkas Machi</span>
                    </h5>
                    <p class="text-muted small mb-0">Klik tombol edit untuk memperbarui teks template, persyaratan berkas, atau ubah status aktif/nonaktif.</p>
                </div>

                {{-- Filter Form --}}
                <form action="{{ route('rw.surat_master.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="kategori" class="form-select form-select-sm rounded-3" style="width: auto; font-size: 0.82rem;" onchange="this.form.submit()">
                        <option value="semua">Semua Kategori (5)</option>
                        <option value="kependudukan" {{ request('kategori') === 'kependudukan' ? 'selected' : '' }}>Kependudukan (4)</option>
                        <option value="sosial_keluarga" {{ request('kategori') === 'sosial_keluarga' ? 'selected' : '' }}>Sosial & Keluarga (4)</option>
                        <option value="usaha_ekonomi" {{ request('kategori') === 'usaha_ekonomi' ? 'selected' : '' }}>Usaha & Ekonomi (4)</option>
                        <option value="lingkungan_keamanan" {{ request('kategori') === 'lingkungan_keamanan' ? 'selected' : '' }}>Lingkungan & Keamanan (3)</option>
                        <option value="internal" {{ request('kategori') === 'internal' ? 'selected' : '' }}>Administrasi Internal (3)</option>
                    </select>

                    <div class="input-group input-group-sm" style="max-width: 240px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama / kode..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary"><i data-lucide="search" style="width:13px;height:13px;"></i></button>
                    </div>

                    @if(request()->hasAny(['kategori', 'search']))
                        <a href="{{ route('rw.surat_master.index') }}" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset Filter">
                            <i data-lucide="refresh-cw" style="width:13px;height:13px;"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light text-secondary text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3" style="width: 60px;">No</th>
                            <th class="py-3">Nama Dokumen & Singkatan</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Alur Persetujuan</th>
                            <th class="py-3">Estimasi & Biaya</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="pe-4 py-3 text-end" style="min-width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($types as $item)
                            <tr>
                                <td class="ps-4 fw-bold text-muted">{{ $item->urutan }}</td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 p-2 bg-light text-primary">
                                            <i data-lucide="{{ $item->icon }}" style="width:18px;height:18px;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->nama }}</div>
                                            <div class="text-muted small" style="font-size: 0.74rem;">
                                                Kode: <code>{{ $item->kode_klasifikasi }}/{{ $item->singkatan }}</code>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill" style="font-size: 0.74rem;">
                                        {{ $item->kategori_label }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-{{ $item->alur_badge_color }}-subtle text-{{ $item->alur_badge_color }} border border-{{ $item->alur_badge_color }}-subtle px-2 py-1 rounded-pill small">
                                        {{ $item->alur_label }}
                                    </span>
                                </td>

                                <td>
                                    <div class="text-dark small">{{ $item->estimasi_hari }}</div>
                                    <div class="text-success small fw-semibold">{{ $item->biaya }}</div>
                                </td>

                                <td class="text-center">
                                    <form action="{{ route('rw.surat_master.toggle', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($item->is_active)
                                            <button type="submit" class="btn btn-sm btn-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;" title="Klik untuk menonaktifkan">
                                                <i data-lucide="check" style="width:12px;height:12px;" class="me-1"></i> Aktif
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;" title="Klik untuk mengaktifkan">
                                                <i data-lucide="x" style="width:12px;height:12px;" class="me-1"></i> Nonaktif
                                            </button>
                                        @endif
                                    </form>
                                </td>

                                <td class="pe-4 text-end">
                                    <a href="{{ route('rw.surat_master.edit', $item->id) }}" class="btn btn-sm btn-primary rounded-3 px-3 py-1 d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.8rem; background: #1456A0; border-color: #1456A0;">
                                        <i data-lucide="edit-2" style="width:13px;height:13px;"></i>
                                        <span>Edit Template</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($types->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $types->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
