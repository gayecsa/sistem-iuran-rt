@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('bank-sampah') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3 fw-semibold">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px; vertical-align: middle;"></i> Portal
                </a>
                <h4 class="fw-bold mb-0 text-dark">Pengawasan & Pengelolaan Setoran Bank Sampah</h4>
            </div>
            <p class="text-muted small mb-0">
                Pusat kendali operasional penimbangan sampah, verifikasi berat aktual, dan konfirmasi tabungan warga.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($user->isRw())
                <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold">
                    <i data-lucide="shield" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Mode Pengawasan Seluruh RW 013
                </span>
            @else
                <span class="badge bg-success px-3 py-2 rounded-pill fw-bold">
                    <i data-lucide="map-pin" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Wilayah RT {{ str_pad($user->rt_number, 3, '0', STR_PAD_LEFT) }}
                </span>
            @endif
        </div>
    </div>

    <!-- Tab Counter Status Filter -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-3">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            @php
                $currStatus = request('status', 'semua');
                $tabs = [
                    'semua' => ['label' => 'Semua', 'icon' => 'layers'],
                    'menunggu' => ['label' => 'Menunggu Verifikasi', 'icon' => 'clock'],
                    'dijadwalkan' => ['label' => 'Dijadwalkan', 'icon' => 'calendar'],
                    'sedang_ditimbang' => ['label' => 'Sedang Ditimbang', 'icon' => 'scale'],
                    'menunggu_konfirmasi' => ['label' => 'Menunggu Konfirmasi', 'icon' => 'alert-circle'],
                    'selesai' => ['label' => 'Selesai & Masuk Kas', 'icon' => 'check-circle'],
                    'ditolak' => ['label' => 'Ditolak', 'icon' => 'x-circle'],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                <a href="{{ request()->fullUrlWithQuery(['status' => $key]) }}" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2 {{ $currStatus === $key ? 'btn-success text-white' : 'btn-light text-dark border' }}">
                    <i data-lucide="{{ $tab['icon'] }}" style="width: 14px; height: 14px;"></i>
                    <span>{{ $tab['label'] }}</span>
                    <span class="badge {{ $currStatus === $key ? 'bg-white text-success' : 'bg-secondary text-white' }} rounded-pill" style="font-size: 11px;">
                        {{ $counts[$key] ?? 0 }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Filter & Pencarian Form -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-3">
        <form action="{{ route('bank-sampah.kelola') }}" method="GET" class="row g-2 align-items-center">
            <input type="hidden" name="status" value="{{ request('status', 'semua') }}">

            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                    </span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Cari kode transaksi, nama warga..." value="{{ request('q') }}">
                </div>
            </div>

            @if($user->isRw())
                <div class="col-12 col-md-3">
                    <select name="rt" class="form-select">
                        <option value="">-- Semua RT (RT 001 - RT 008) --</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ request('rt') == $i ? 'selected' : '' }}>
                                Wilayah RT {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}
                            </option>
                        @endfor
                    </select>
                </div>
            @endif

            <div class="col-12 col-md-auto ms-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-sm">
                    Filter Data
                </button>
                @if(request()->hasAny(['q', 'rt']))
                    <a href="{{ route('bank-sampah.kelola', ['status' => request('status', 'semua')]) }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Transaksi Setoran -->
    <div class="wm-card bg-white border rounded-4 shadow-sm overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kode Transaksi</th>
                        <th>Warga Penyetor</th>
                        <th>Wilayah RT</th>
                        <th>Metode</th>
                        <th>Berat Aktual</th>
                        <th>Nilai Tabungan</th>
                        <th>Status</th>
                        <th>Waktu Diajukan</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $tx)
                        <tr>
                            <td class="ps-4">
                                <strong class="text-dark">{{ $tx->kode_transaksi }}</strong>
                                <small class="text-muted d-block">ID: #{{ $tx->id }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $tx->user->avatar_url ?? asset('images/avatar-default.svg') }}" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                                    <div>
                                        <span class="fw-semibold text-dark d-block">{{ $tx->user->name ?? 'Warga' }}</span>
                                        <small class="text-muted">{{ $tx->user->phone ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border fw-bold">
                                    RT {{ str_pad($tx->rt_number, 3, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>
                                @if($tx->metode_penyerahan === 'drop_point')
                                    <span class="badge bg-light text-primary border small">
                                        <i data-lucide="map-pin" style="width: 12px; height: 12px; vertical-align: middle;"></i> Drop Point
                                    </span>
                                @else
                                    <span class="badge bg-light text-success border small">
                                        <i data-lucide="truck" style="width: 12px; height: 12px; vertical-align: middle;"></i> Jemput
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $tx->total_berat_formatted }}</span>
                                <small class="text-muted d-block">{{ $tx->items->count() }} item</small>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ $tx->total_rupiah_formatted }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $tx->status_badge['bg'] }} border px-2 py-1 rounded-pill fw-bold">
                                    {{ $tx->status_label }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $tx->tanggal_pengajuan->format('d/m/Y H:i') }}</small>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <a href="{{ route('bank-sampah.show', $tx->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                        Periksa / Proses
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i data-lucide="inbox" style="width: 48px; height: 48px;" class="mb-2 text-secondary opacity-50 d-block mx-auto"></i>
                                Tidak ada data transaksi setoran sampah yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $transaksis->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
