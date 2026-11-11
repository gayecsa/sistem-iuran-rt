@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-icon-pill shadow-sm" style="width: 48px; height: 48px; border-radius: 14px; background: #EAF3FF; color: #1456A0; font-size: 20px;">
                <i data-lucide="history" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="color: var(--wm-text) !important;">Riwayat Perubahan Data & Audit Log</h3>
                <p class="text-muted mb-0 small">Catatan audit rekam jejak setiap mutasi domisili, verifikasi berkas, dan perubahan kredensial akun.</p>
            </div>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0 align-items-center small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active fw-semibold text-primary">Riwayat Perubahan Data</li>
            </ol>
        </nav>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <form action="{{ route('kependudukan.riwayat') }}" method="GET" class="row g-2 align-items-center mb-0">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; color: #1456A0;">
                        <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-1" placeholder="Cari nama warga, petugas, atau catatan..." style="border-radius: 0 12px 12px 0; font-size: 0.88rem;">
                </div>
            </div>

            <div class="col-6 col-md-4">
                <select name="tipe" class="form-select" onchange="this.form.submit()" style="border-radius: 12px; font-size: 0.88rem;">
                    <option value="all">Semua Tipe Perubahan</option>
                    <option value="pendaftaran_awal" {{ request('tipe') == 'pendaftaran_awal' ? 'selected' : '' }}>Pendaftaran Awal</option>
                    <option value="status_verifikasi" {{ request('tipe') == 'status_verifikasi' ? 'selected' : '' }}>Verifikasi Status</option>
                    <option value="status_domisili" {{ request('tipe') == 'status_domisili' ? 'selected' : '' }}>Mutasi Domisili</option>
                    <option value="tambah_anggota_keluarga" {{ request('tipe') == 'tambah_anggota_keluarga' ? 'selected' : '' }}>Tambah Anggota Keluarga</option>
                    <option value="reset_password" {{ request('tipe') == 'reset_password' ? 'selected' : '' }}>Reset Password Akun</option>
                    <option value="toggle_akun" {{ request('tipe') == 'toggle_akun' ? 'selected' : '' }}>Aktivasi / Nonaktif Akun</option>
                </select>
            </div>

            <div class="col-12 col-md-auto ms-auto d-flex align-items-center gap-2">
                @if(request('search') || request('tipe'))
                    <a href="{{ route('kependudukan.riwayat') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small">
                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4" style="width: 50px;">#</th>
                        <th>Waktu Kejadian</th>
                        <th>Warga Terkait</th>
                        <th>Tipe Perubahan</th>
                        <th>Perubahan Status</th>
                        <th>Catatan / Keterangan</th>
                        <th class="pe-4">Petugas Pencatat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $h)
                        <tr>
                            <td class="ps-4 text-muted small fw-semibold">
                                {{ $loop->iteration + ($histories->currentPage() - 1) * $histories->perPage() }}
                            </td>
                            <td class="small text-muted font-monospace" style="font-size: 0.76rem;">
                                {{ $h->created_at ? $h->created_at->translatedFormat('d M Y, H:i') : '-' }} WIB
                            </td>
                            <td>
                                @if($h->user)
                                    <a href="{{ route('warga.show', $h->user_id) }}" class="fw-bold text-dark text-decoration-none small d-block">
                                        {{ $h->user->name }}
                                    </a>
                                    <span class="text-muted font-monospace" style="font-size: 0.72rem;">NIK: {{ $h->user->masked_nik }}</span>
                                @else
                                    <span class="text-muted small">User ID #{{ $h->user_id }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1 small fw-bold" style="background: #EAF3FF; color: #1456A0; font-size: 0.72rem;">
                                    {{ str_replace('_', ' ', strtoupper($h->tipe_perubahan)) }}
                                </span>
                            </td>
                            <td>
                                <span class="small text-muted">{{ $h->status_lama ? "{$h->status_lama} → " : "" }}</span>
                                <strong class="text-primary small">{{ $h->status_baru }}</strong>
                            </td>
                            <td class="small text-muted text-truncate" style="max-width: 250px;" title="{{ $h->catatan }}">
                                {{ $h->catatan ?: '-' }}
                            </td>
                            <td class="pe-4 small">
                                <strong class="text-dark d-block">{{ $h->petugas_nama ?: 'Sistem Warkas Machi' }}</strong>
                                <span class="text-muted" style="font-size: 0.72rem;">{{ strtoupper($h->petugas_role ?: 'Admin') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted small">
                                Belum ada catatan riwayat perubahan data kependudukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">Menampilkan {{ $histories->firstItem() ?? 0 }} - {{ $histories->lastItem() ?? 0 }} dari {{ $histories->total() }} riwayat</span>
            <div>{{ $histories->links() }}</div>
        </div>
    </div>
</div>
@endsection
