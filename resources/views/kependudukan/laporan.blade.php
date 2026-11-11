@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-icon-pill shadow-sm" style="width: 48px; height: 48px; border-radius: 14px; background: #EAF3FF; color: #1456A0; font-size: 20px;">
                <i data-lucide="bar-chart-3" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="color: var(--wm-text) !important;">Laporan Demografi Kependudukan RW 013</h3>
                <p class="text-muted mb-0 small">Analitik data kependudukan, sebaran kelompok usia, gender, dan profil wilayah RT 001 - RT 008.</p>
            </div>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0 align-items-center small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active fw-semibold text-primary">Laporan Kependudukan</li>
            </ol>
        </nav>
    </div>

    {{-- METRIK UTAMA --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 5px solid #1456A0 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Populasi</span>
                        <h3 class="fw-bold text-dark mt-2 mb-0">{{ number_format($totalWarga) }} <span class="fs-6 fw-normal text-muted">Jiwa</span></h3>
                        <small class="text-muted">RW 013 Kelurahan Sukamiskin</small>
                    </div>
                    <div class="p-3 rounded-circle" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="users" style="width: 28px; height: 28px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 5px solid #2878D0 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Laki-Laki</span>
                        <h3 class="fw-bold text-primary mt-2 mb-0">{{ number_format($totalLaki) }} <span class="fs-6 fw-normal text-muted">Jiwa</span></h3>
                        <small class="text-muted">{{ $totalWarga > 0 ? round(($totalLaki / $totalWarga) * 100, 1) : 0 }}% dari total penduduk</small>
                    </div>
                    <div class="p-3 rounded-circle" style="background: #EAF3FF; color: #2878D0;">
                        <i data-lucide="user" style="width: 28px; height: 28px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 5px solid #EC4899 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Perempuan</span>
                        <h3 class="fw-bold mt-2 mb-0" style="color: #EC4899;">{{ number_format($totalPerempuan) }} <span class="fs-6 fw-normal text-muted">Jiwa</span></h3>
                        <small class="text-muted">{{ $totalWarga > 0 ? round(($totalPerempuan / $totalWarga) * 100, 1) : 0 }}% dari total penduduk</small>
                    </div>
                    <div class="p-3 rounded-circle" style="background: #FCE7F3; color: #EC4899;">
                        <i data-lucide="user" style="width: 28px; height: 28px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KELOMPOK USIA & STATUS DOMISILI --}}
    <div class="row g-4 mb-4">
        {{-- Piramida / Distribusi Kelompok Usia --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                    <i data-lucide="pie-chart" style="width: 20px; height: 20px; color: #1456A0;"></i>
                    <h6 class="fw-bold mb-0 text-dark">Distribusi Kelompok Usia</h6>
                </div>

                @php
                    $ageCategories = [
                        ['label' => 'Balita (0 - 5 Tahun)', 'count' => $usiaBalita, 'color' => '#3B82F6'],
                        ['label' => 'Anak-anak (6 - 12 Tahun)', 'count' => $usiaAnak, 'color' => '#10B981'],
                        ['label' => 'Remaja (13 - 19 Tahun)', 'count' => $usiaRemaja, 'color' => '#F59E0B'],
                        ['label' => 'Dewasa (20 - 55 Tahun)', 'count' => $usiaDewasa, 'color' => '#1456A0'],
                        ['label' => 'Lansia (56+ Tahun)', 'count' => $usiaLansia, 'color' => '#8B5CF6'],
                    ];
                @endphp

                <div class="d-flex flex-column gap-3">
                    @foreach($ageCategories as $cat)
                        @php $pct = $totalWarga > 0 ? round(($cat['count'] / $totalWarga) * 100, 1) : 0; @endphp
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1 small">
                                <span class="fw-semibold text-dark">{{ $cat['label'] }}</span>
                                <span class="fw-bold text-dark">{{ $cat['count'] }} Jiwa ({{ $pct }}%)</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 6px; background: #F1F5F9;">
                                <div class="progress-bar" style="width: {{ $pct }}%; background: {{ $cat['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Status Domisili & Kepemilikan Rumah --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                    <i data-lucide="home" style="width: 20px; height: 20px; color: #16865D;"></i>
                    <h6 class="fw-bold mb-0 text-dark">Status Domisili & Tempat Tinggal</h6>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-2">Status Domisili</span>
                        <ul class="list-group list-group-flush small">
                            @foreach($domisiliStats as $dom => $cnt)
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0">
                                    <span>{{ $dom ?: 'Warga Tetap' }}</span>
                                    <span class="badge rounded-pill bg-light text-dark border fw-bold">{{ $cnt }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="col-12 col-sm-6">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-2">Kepemilikan Rumah</span>
                        <ul class="list-group list-group-flush small">
                            @foreach($rumahStats as $rmh => $cnt)
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0">
                                    <span>{{ ucfirst(str_replace('_', ' ', $rmh ?: 'Milik Sendiri')) }}</span>
                                    <span class="badge rounded-pill bg-light text-dark border fw-bold">{{ $cnt }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL REKAPITULASI PER RT (RT 001 - RT 008) --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <div class="px-4 py-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">Rekapitulasi Kependudukan Wilayah RT 001 sampai RT 008</h6>
            <span class="badge rounded-pill bg-white text-dark border px-3 py-1 fw-bold small">RW 013 Sukamiskin</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4">Wilayah RT</th>
                        <th>Total Warga</th>
                        <th>Total KK</th>
                        <th>Laki-Laki</th>
                        <th>Perempuan</th>
                        <th>Warga Terverifikasi</th>
                        <th class="pe-4 text-center">Persentase Valid</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapPerRt as $r)
                        @php
                            $validPct = $r['total_warga'] > 0 ? round(($r['terverifikasi'] / $r['total_warga']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="ps-4 fw-bold text-dark small">
                                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #EAF3FF; color: #1456A0;">
                                    {{ $r['rt'] }}
                                </span>
                            </td>
                            <td class="fw-bold text-dark small">{{ number_format($r['total_warga']) }} Jiwa</td>
                            <td class="text-muted small">{{ number_format($r['total_kk']) }} KK</td>
                            <td class="text-muted small">{{ number_format($r['laki']) }}</td>
                            <td class="text-muted small">{{ number_format($r['perempuan']) }}</td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #E3F5EC; color: #16865D; font-size: 0.72rem;">
                                    {{ number_format($r['terverifikasi']) }} Terverifikasi
                                </span>
                            </td>
                            <td class="pe-4 text-center">
                                <span class="fw-bold small {{ $validPct >= 80 ? 'text-success' : 'text-warning' }}">{{ $validPct }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
