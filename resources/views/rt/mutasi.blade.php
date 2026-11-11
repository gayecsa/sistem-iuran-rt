@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i data-lucide="arrow-left-right" class="text-primary me-2" style="width: 26px; height: 26px;"></i>
                Log Mutasi Kependudukan - {{ $rtLabel }}
            </h4>
            <p class="text-muted small mb-0">
                Pencatatan data mutasi warga: pindah datang, pindah keluar, kelahiran, dan kematian di wilayah {{ $rtLabel }}.
            </p>
        </div>
        <div>
            <a href="{{ route('dashboard.rt') }}" class="wm-btn-outline">
                <i data-lucide="arrow-left" style="width: 17px; height: 17px;"></i> Dashboard RT
            </a>
        </div>
    </div>

    <div class="wm-card">
        <div class="wm-card-header d-flex justify-content-between align-items-center">
            <h5 class="wm-card-title mb-0">Riwayat Mutasi Warga ({{ $rtLabel }})</h5>
            <span class="badge bg-light text-dark border">{{ count($mutasiList) }} Mutasi</span>
        </div>
        <div class="wm-card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size:0.8rem; text-transform:uppercase;">
                            <th>Nama Warga</th>
                            <th>NIK</th>
                            <th>Jenis Mutasi</th>
                            <th>Tanggal Lapor</th>
                            <th>Asal / Keterangan</th>
                            <th>Status Administrasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mutasiList as $m)
                            <tr>
                                <td class="fw-bold text-dark">{{ $m['nama'] }}</td>
                                <td class="font-monospace small text-muted">{{ $m['nik'] }}</td>
                                <td>
                                    @if($m['jenis'] === 'Pindah Datang')
                                        <span class="badge bg-success">Pindah Datang</span>
                                    @elseif($m['jenis'] === 'Pindah Keluar')
                                        <span class="badge bg-warning text-dark">Pindah Keluar</span>
                                    @else
                                        <span class="badge bg-info text-white">{{ $m['jenis'] }}</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $m['tanggal'] }}</td>
                                <td class="small text-dark">{{ $m['asal_tujuan'] }}</td>
                                <td>
                                    <span class="badge bg-light text-success border">
                                        <i data-lucide="check" style="width:11px;height:11px;"></i> {{ $m['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">
                                    Belum ada data mutasi warga di {{ $rtLabel }}.
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
