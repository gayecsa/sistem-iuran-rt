@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-icon-pill shadow-sm" style="width: 48px; height: 48px; border-radius: 14px; background: #EAF3FF; color: #1456A0; font-size: 20px;">
                <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="color: var(--wm-text) !important;">Pusat Verifikasi Kependudukan</h3>
                <p class="text-muted mb-0 small">Pemeriksaan dan validasi berjenjang data identitas kependudukan warga RT &bull; RW 013.</p>
            </div>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0 align-items-center small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active fw-semibold text-primary">Verifikasi Warga</li>
            </ol>
        </nav>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <form action="{{ route('kependudukan.verifikasi') }}" method="GET" class="row g-2 align-items-center mb-0">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; color: #1456A0;">
                        <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-1" placeholder="Cari nama, NIK, atau No. KK..." style="border-radius: 0 12px 12px 0; font-size: 0.88rem;">
                </div>
            </div>

            @if(!auth()->user()->isRt())
                <div class="col-6 col-md-3 col-lg-2">
                    <select name="rt" class="form-select" onchange="this.form.submit()" style="border-radius: 12px; font-size: 0.88rem;">
                        <option value="all">Semua RT (001-008)</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ request('rt') == $i ? 'selected' : '' }}>RT {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>
            @endif

            <div class="col-6 col-md-3 col-lg-3">
                <select name="status" class="form-select" onchange="this.form.submit()" style="border-radius: 12px; font-size: 0.88rem;">
                    <option value="all">Semua Status Verifikasi</option>
                    <option value="Menunggu Verifikasi RT" {{ request('status') == 'Menunggu Verifikasi RT' ? 'selected' : '' }}>Menunggu Verifikasi RT</option>
                    <option value="Menunggu Verifikasi RW" {{ request('status') == 'Menunggu Verifikasi RW' ? 'selected' : '' }}>Menunggu Verifikasi RW</option>
                    <option value="Belum Diverifikasi" {{ request('status') == 'Belum Diverifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
                    <option value="Terverifikasi RT" {{ request('status') == 'Terverifikasi RT' ? 'selected' : '' }}>Terverifikasi RT</option>
                    <option value="Terverifikasi RW" {{ request('status') == 'Terverifikasi RW' ? 'selected' : '' }}>Terverifikasi RW</option>
                    <option value="Perlu Perbaikan" {{ request('status') == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                </select>
            </div>

            <div class="col-12 col-md-auto ms-auto d-flex align-items-center gap-2">
                @if(request('search') || request('rt') || request('status'))
                    <a href="{{ route('kependudukan.verifikasi') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small">
                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Antrean Verifikasi --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4" style="width: 50px;">#</th>
                        <th>Nama Warga</th>
                        <th>NIK (Masked)</th>
                        <th>No. KK</th>
                        <th>Wilayah</th>
                        <th>Status Domisili</th>
                        <th>Status Verifikasi Saat Ini</th>
                        <th class="pe-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wargaList as $w)
                        <tr>
                            <td class="ps-4 text-muted small fw-semibold">
                                {{ $loop->iteration + ($wargaList->currentPage() - 1) * $wargaList->perPage() }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $w->avatar_url }}" alt="{{ $w->name }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                    <div>
                                        <a href="{{ route('warga.show', $w->id) }}" class="fw-bold text-dark text-decoration-none small d-block">
                                            {{ $w->name }}
                                        </a>
                                        <span class="text-muted" style="font-size: 0.72rem;">{{ $w->hubungan_keluarga ?: 'Warga' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="font-monospace small">{{ $w->masked_nik }}</td>
                            <td class="font-monospace small text-primary">{{ $w->masked_no_kk }}</td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-2 py-1 small">
                                    {{ $w->clean_rt_label }} / RW 013
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1 small fw-semibold" style="background: #E3F5EC; color: #16865D; font-size: 0.72rem;">
                                    {{ $w->status_domisili ?: 'Warga Tetap' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $vColor = '#64748B'; $vBg = '#F1F5F9';
                                    if ($w->status_verifikasi === 'Terverifikasi RW') { $vColor = '#16865D'; $vBg = '#E3F5EC'; }
                                    elseif ($w->status_verifikasi === 'Terverifikasi RT') { $vColor = '#1456A0'; $vBg = '#EAF3FF'; }
                                    elseif (str_contains($w->status_verifikasi ?? '', 'Menunggu')) { $vColor = '#D97706'; $vBg = '#FEF3C7'; }
                                    elseif (in_array($w->status_verifikasi, ['Perlu Perbaikan', 'Ditolak'])) { $vColor = '#DC2626'; $vBg = '#FEE2E2'; }
                                @endphp
                                <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: {{ $vBg }}; color: {{ $vColor }}; font-size: 0.72rem;">
                                    {{ $w->status_verifikasi ?: 'Belum Diverifikasi' }}
                                </span>
                            </td>
                            <td class="pe-4 text-center">
                                <a href="{{ route('warga.show', $w->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold small d-inline-flex align-items-center gap-1" style="background: #1456A0;">
                                    <i data-lucide="shield-check" style="width: 13px; height: 13px;"></i>
                                    <span>Periksa & Verifikasi</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted small">
                                Tidak ada antrean warga yang memerlukan verifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">Menampilkan {{ $wargaList->firstItem() ?? 0 }} - {{ $wargaList->lastItem() ?? 0 }} dari {{ $wargaList->total() }} warga</span>
            <div>{{ $wargaList->links() }}</div>
        </div>
    </div>
</div>
@endsection
