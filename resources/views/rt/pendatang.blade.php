@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i data-lucide="user-check" class="text-primary me-2" style="width: 26px; height: 26px;"></i>
                Data Warga Pendatang & Kontrak - {{ $rtLabel }}
            </h4>
            <p class="text-muted small mb-0">
                Pendataan warga yang berstatus domisili sementara, penyewa rumah, kontrakan, atau tempat tinggal sementara di wilayah {{ $rtLabel }}.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('warga.create') }}" class="wm-btn-primary">
                <i data-lucide="user-plus" style="width: 17px; height: 17px;"></i> Daftarkan Pendatang
            </a>
            <a href="{{ route('dashboard.rt') }}" class="wm-btn-outline">
                <i data-lucide="arrow-left" style="width: 17px; height: 17px;"></i> Dashboard RT
            </a>
        </div>
    </div>

    <div class="wm-card">
        <div class="wm-card-header d-flex justify-content-between align-items-center">
            <h5 class="wm-card-title mb-0">Daftar Warga Pendatang ({{ $rtLabel }})</h5>
            <span class="badge bg-light text-dark border">{{ count($pendatang) }} Terdata</span>
        </div>
        <div class="wm-card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size:0.8rem; text-transform:uppercase;">
                            <th>Nama Lengkap</th>
                            <th>Status Tinggal</th>
                            <th>No. Rumah / Lokasi</th>
                            <th>Kontak</th>
                            <th>Lama Tinggal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendatang as $p)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->name }}</div>
                                    <small class="text-muted">NIK: {{ $p->nik ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $p->status_rumah ?? 'Sewa / Kontrak' }}</span>
                                </td>
                                <td>
                                    <div class="small text-dark">{{ $p->address ?? ($rtLabel . ' RW 013') }}</div>
                                    <small class="text-muted">Rumah No. {{ $p->house_number ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $p->phone ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="small text-muted">Bulan Berjalan</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('warga.edit', $p->id) }}" class="btn btn-sm btn-outline-primary" style="font-size:0.75rem;">
                                        <i data-lucide="edit" style="width:13px;height:13px;"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">
                                    Belum ada catatan warga pendatang khusus di {{ $rtLabel }}.
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
