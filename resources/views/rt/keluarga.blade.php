@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i data-lucide="home" class="text-primary me-2" style="width: 26px; height: 26px;"></i>
                Data Kepala Keluarga (KK) - {{ $rtLabel }}
            </h4>
            <p class="text-muted small mb-0">
                Manajemen buku induk data keluarga dan kepala keluarga yang terdaftar resmi di wilayah {{ $rtLabel }}.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('warga.create') }}" class="wm-btn-primary">
                <i data-lucide="user-plus" style="width: 17px; height: 17px;"></i> Tambah Warga / KK
            </a>
            <a href="{{ route('dashboard.rt') }}" class="wm-btn-outline">
                <i data-lucide="arrow-left" style="width: 17px; height: 17px;"></i> Dashboard RT
            </a>
        </div>
    </div>

    {{-- Statistik KK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="wm-stat-card">
                <span class="wm-stat-lbl">Total Kepala Keluarga</span>
                <div class="wm-stat-val text-primary">{{ count($keluargaList) }}</div>
                <div class="small text-muted">KK Terdata di {{ $rtLabel }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="wm-stat-card">
                <span class="wm-stat-lbl">Total Anggota Keluarga</span>
                <div class="wm-stat-val text-success">{{ count($wargaList) }}</div>
                <div class="small text-muted">Jiwa Terdaftar di Wilayah RT</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="wm-stat-card">
                <span class="wm-stat-lbl">Rata-rata Jiwa per KK</span>
                <div class="wm-stat-val text-warning">
                    {{ count($keluargaList) > 0 ? round(count($wargaList) / count($keluargaList), 1) : 0 }}
                </div>
                <div class="small text-muted">Orang / Rumah Tangga</div>
            </div>
        </div>
    </div>

    {{-- Daftar Kartu Keluarga --}}
    <div class="wm-card">
        <div class="wm-card-header d-flex justify-content-between align-items-center">
            <h5 class="wm-card-title mb-0">Daftar Buku Induk Kartu Keluarga ({{ $rtLabel }})</h5>
            <span class="badge bg-light text-dark border">{{ count($keluargaList) }} Berkas KK</span>
        </div>
        <div class="wm-card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size:0.8rem; text-transform:uppercase;">
                            <th>No. KK</th>
                            <th>Kepala Keluarga</th>
                            <th>Alamat / No. Rumah</th>
                            <th>Jumlah Anggota</th>
                            <th>Status Domisili</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keluargaList as $noKk => $anggotas)
                            @php
                                $kepala = $anggotas->first();
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">
                                        {{ $noKk !== 'KK_BELUM_TERDATA' ? $noKk : 'Belum Ada No. KK' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $kepala->name ?? 'Warga' }}</div>
                                    <small class="text-muted"><i data-lucide="phone" style="width:11px;height:11px;"></i> {{ $kepala->phone ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="small text-dark">{{ $kepala->address ?? ($rtLabel . ' RW 013') }}</div>
                                    <small class="text-muted">No. {{ $kepala->house_number ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill">{{ count($anggotas) }} Jiwa</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">Warga Tetap</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('warga.edit', $kepala->id) }}" class="btn btn-sm btn-outline-primary" style="font-size:0.75rem;">
                                        <i data-lucide="edit" style="width:13px;height:13px;"></i> Kelola
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">
                                    Belum ada data warga terdaftar untuk {{ $rtLabel }}. Klik "Tambah Warga / KK" untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
