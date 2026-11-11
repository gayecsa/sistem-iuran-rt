@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-icon-pill shadow-sm" style="width: 48px; height: 48px; border-radius: 14px; background: #EAF3FF; color: #1456A0; font-size: 20px;">
                <i data-lucide="home" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="color: var(--wm-text) !important;">Data Keluarga RW 013</h3>
                <p class="text-muted mb-0 small">Direktori seluruh Kepala Keluarga, Kartu Keluarga, dan sebaran domisili RT 001 - RT 008.</p>
            </div>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0 align-items-center small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active fw-semibold text-primary">Data Keluarga</li>
            </ol>
        </nav>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <form action="{{ route('kependudukan.keluarga') }}" method="GET" class="row g-2 align-items-center mb-0">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; color: #1456A0;">
                        <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-1" placeholder="Cari nama kepala keluarga atau No. KK..." style="border-radius: 0 12px 12px 0; font-size: 0.88rem;">
                </div>
            </div>

            @if(!auth()->user()->isRt())
                <div class="col-6 col-md-3">
                    <select name="rt" class="form-select" onchange="this.form.submit()" style="border-radius: 12px; font-size: 0.88rem;">
                        <option value="all">Semua Wilayah RT</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ request('rt') == $i ? 'selected' : '' }}>RT {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>
            @endif

            <div class="col-12 col-md-auto ms-auto d-flex align-items-center gap-2">
                @if(request('search') || request('rt'))
                    <a href="{{ route('kependudukan.keluarga') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small">
                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    </a>
                @endif
                <a href="{{ route('arsip-kk.index') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold small d-inline-flex align-items-center gap-2">
                    <i data-lucide="folder-check" style="width: 15px; height: 15px;"></i>
                    <span>Arsip KK Resmi</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Keluarga --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4" style="width: 50px;">#</th>
                        <th>Nomor KK (Masked)</th>
                        <th>Kepala Keluarga</th>
                        <th>Wilayah</th>
                        <th>Alamat Rumah</th>
                        <th>Anggota</th>
                        <th>Status Verifikasi</th>
                        <th class="pe-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keluargas as $k)
                        <tr>
                            <td class="ps-4 text-muted small fw-semibold">
                                {{ $loop->iteration + ($keluargas->currentPage() - 1) * $keluargas->perPage() }}
                            </td>
                            <td>
                                <span class="font-monospace fw-bold text-primary">{{ $k->masked_no_kk }}</span>
                            </td>
                            <td>
                                <strong class="text-dark small d-block">{{ $k->nama_kepala_keluarga }}</strong>
                                <span class="text-muted font-monospace" style="font-size: 0.72rem;">NIK: {{ $k->kepalaKeluarga ? $k->kepalaKeluarga->masked_nik : ($k->nik_kepala_keluarga ? substr($k->nik_kepala_keluarga, 0, 6) . '******' . substr($k->nik_kepala_keluarga, -4) : '-') }}</span>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-2 py-1 small">
                                    {{ $k->clean_rt_label }} / RW 013
                                </span>
                            </td>
                            <td class="small text-muted text-truncate" style="max-width: 200px;" title="{{ $k->alamat }}">
                                {{ $k->alamat }}
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #EAF3FF; color: #1456A0; font-size: 0.76rem;">
                                    {{ $k->anggota_count }} Jiwa
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: {{ $k->status_verifikasi_dukcapil === 'Terverifikasi' ? '#E3F5EC' : '#FEF3C7' }}; color: {{ $k->status_verifikasi_dukcapil === 'Terverifikasi' ? '#16865D' : '#D97706' }}; font-size: 0.72rem;">
                                    {{ $k->status_verifikasi_dukcapil }}
                                </span>
                            </td>
                            <td class="pe-4 text-center">
                                <a href="{{ route('arsip-kk.show', $k->id) }}" class="btn btn-sm btn-outline-primary rounded-circle p-2" title="Detail Kartu Keluarga" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted small">
                                Belum ada data keluarga yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">Menampilkan {{ $keluargas->firstItem() ?? 0 }} - {{ $keluargas->lastItem() ?? 0 }} dari {{ $keluargas->total() }} data keluarga</span>
            <div>{{ $keluargas->links() }}</div>
        </div>
    </div>
</div>
@endsection
