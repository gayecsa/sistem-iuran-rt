@extends('layouts.app')

@section('title', 'Log Aktivitas')

@push('styles')
<style>
    :root {
        --wm-primary: #1456A0;
        --wm-primary-dark: #0F3966;
        --wm-secondary: #2878D0;
        --wm-light-blue: #EAF3FF;
        --wm-bg: #F4F7FB;
        --wm-text-dark: #172B4D;
        --wm-text-muted: #64748B;
        --wm-border-card: #E2E8F0;
    }

    /* Hero Banner Super Admin Blue Gradient */
    .sa-hero-card {
        background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%) !important;
        border-radius: 16px !important;
        padding: 28px 32px !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 18px rgba(20, 86, 160, 0.2) !important;
        margin-bottom: 24px !important;
        position: relative !important;
        overflow: hidden !important;
        border: none !important;
    }

    .sa-hero-card::after {
        content: "";
        position: absolute;
        top: -40px;
        right: -30px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }

    /* KPI Summary Cards */
    .sa-kpi-card {
        background-color: var(--wm-card, #FFFFFF) !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
        border-radius: 16px !important;
        padding: 20px 24px !important;
        box-shadow: 0 2px 8px rgba(23, 43, 77, 0.04) !important;
        height: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    }

    .sa-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(20, 86, 160, 0.08) !important;
        border-color: #CBD5E1 !important;
    }

    /* Box Container */
    .sa-box-container {
        background: var(--wm-card, #FFFFFF) !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
        border-radius: 16px !important;
        box-shadow: 0 2px 10px rgba(23, 43, 77, 0.04) !important;
        padding: 24px !important;
        margin-bottom: 24px !important;
    }

    /* Dark Mode Overrides */
    [data-theme="dark"] .sa-kpi-card,
    body.dark-mode .sa-kpi-card,
    [data-theme="dark"] .sa-box-container,
    body.dark-mode .sa-box-container {
        background-color: #131F35 !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
    }

    /* Audit Table Styles */
    .table-audit {
        margin-bottom: 0;
        font-size: 0.85rem;
    }

    .table-audit th {
        background: #F8FAFC !important;
        color: #172B4D !important;
        font-weight: 700 !important;
        padding: 12px 16px !important;
        border-bottom: 2px solid #E2E8F0 !important;
        font-size: 0.78rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }

    .table-audit td {
        padding: 14px 16px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #EDF2F7 !important;
    }

    .table-audit tr:hover td {
        background-color: #F8FAFC !important;
    }

    @media (max-width: 991.98px) {
        .sa-hero-card {
            padding: 20px !important;
        }
        .sa-box-container {
            padding: 18px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    {{-- ======================================================================
         1. HERO BANNER LOG AKTIVITAS & AUDIT TRAIL
         ====================================================================== --}}
    <div class="sa-hero-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                    <i data-lucide="shield" style="width: 12px; height: 12px; vertical-align: middle;"></i>
                    CONTROL CENTER SUPER ADMIN
                </span>
                <span class="text-white-50 small">•</span>
                <span class="badge rounded-pill px-2.5 py-0.5 fw-bold" style="background: rgba(16, 185, 129, 0.25); color: #A7F3D0; border: 1px solid rgba(167, 243, 208, 0.4); font-size: 0.72rem;">
                    REAL-TIME AUDIT TRAIL
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-white">Log Aktivitas &amp; Audit Trail Sistem</h3>
            <p class="mb-0 text-white-50 small">
                Pencatatan otomatis fail-safe seluruh aktivitas penting: otentikasi, switch role simulasi, mutasi akun, transaksi keuangan, dan izin keamanan.
            </p>
        </div>

        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('super-admin.audit-log.export', request()->query()) }}" class="btn btn-success rounded-pill px-3 py-2 fw-bold small d-inline-flex align-items-center gap-2 shadow-sm" style="background: #16865D; border-color: #16865D;">
                <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                <span>Export CSV</span>
            </a>
            <a href="{{ route('dashboard.super-admin') }}" class="btn btn-outline-light rounded-pill px-3 py-2 fw-bold small d-inline-flex align-items-center gap-2">
                <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                <span>Control Center</span>
            </a>
        </div>
    </div>

    {{-- ======================================================================
         2. 4 KARTU KPI RINGKASAN AKTIVITAS SISTEM (100% DATA RIIL DB)
         ====================================================================== --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Log Tercatat --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="sa-kpi-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        Total Log Tercatat
                    </span>
                    <div class="p-2 rounded-3" style="background: var(--wm-light-blue); color: var(--wm-primary);">
                        <i data-lucide="scroll-text" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold mb-1" style="color: var(--wm-text-dark);">
                        {{ number_format($totalLogs, 0, ',', '.') }}
                    </h3>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Seluruh rekaman audit di database
                    </small>
                </div>
            </div>
        </div>

        {{-- Card 2: Aktivitas Hari Ini --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="sa-kpi-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        Aktivitas Hari Ini
                    </span>
                    <div class="p-2 rounded-3" style="background: #E8F6EF; color: #16865D;">
                        <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold mb-1 text-success">
                        {{ number_format($todayLogs, 0, ',', '.') }}
                    </h3>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Aksi tercatat sejak 00:00 WIB hari ini
                    </small>
                </div>
            </div>
        </div>

        {{-- Card 3: Aktivitas Simulasi / Super Admin --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="sa-kpi-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        Simulasi / Super Admin
                    </span>
                    <div class="p-2 rounded-3" style="background: #F3E8FF; color: #7C3AED;">
                        <i data-lucide="shuffle" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold mb-1" style="color: #7C3AED;">
                        {{ number_format($simulasiLogs, 0, ',', '.') }}
                    </h3>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Switch role, mode guard &amp; kontrol admin
                    </small>
                </div>
            </div>
        </div>

        {{-- Card 4: Aksi Keamanan & Master --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="sa-kpi-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        Keamanan &amp; Master
                    </span>
                    <div class="p-2 rounded-3" style="background: #FEF3C7; color: #D97706;">
                        <i data-lucide="shield-alert" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold mb-1 text-warning">
                        {{ number_format($securityMasterLogs, 0, ',', '.') }}
                    </h3>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Izin CCTV, rekening kas &amp; tarif iuran
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         3. BAR FILTER & PENCARIAN AUDIT LOG
         ====================================================================== --}}
    <div class="sa-box-container mb-4">
        <form method="GET" action="{{ route('super-admin.audit-log.index') }}">
            <div class="row g-2 align-items-end">
                {{-- Filter Kategori --}}
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.75rem;">Kategori Aktivitas</label>
                    <select name="category" class="form-select form-select-sm rounded-3" style="font-size: 0.82rem;">
                        <option value="">-- Semua Kategori --</option>
                        <option value="auth" {{ request('category') === 'auth' ? 'selected' : '' }}>Otentikasi &amp; Login (auth)</option>
                        <option value="super_admin" {{ request('category') === 'super_admin' ? 'selected' : '' }}>Super Admin &amp; Simulasi (super_admin)</option>
                        <option value="keuangan" {{ request('category') === 'keuangan' ? 'selected' : '' }}>Keuangan &amp; Kas (keuangan)</option>
                        <option value="keamanan" {{ request('category') === 'keamanan' ? 'selected' : '' }}>Keamanan &amp; CCTV (keamanan)</option>
                        <option value="profil" {{ request('category') === 'profil' ? 'selected' : '' }}>Profil &amp; Akun (profil)</option>
                        <option value="sistem" {{ request('category') === 'sistem' ? 'selected' : '' }}>Sistem &amp; Operasional (sistem)</option>
                    </select>
                </div>

                {{-- Tanggal Dari --}}
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.75rem;">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm rounded-3" style="font-size: 0.82rem;">
                </div>

                {{-- Tanggal Sampai --}}
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.75rem;">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm rounded-3" style="font-size: 0.82rem;">
                </div>

                {{-- Pencarian Kata Kunci --}}
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.75rem;">Pencarian Kata Kunci</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <i data-lucide="search" style="width: 14px; height: 14px; color: var(--wm-text-muted);"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Deskripsi, aksi, IP, nama..." style="font-size: 0.82rem;">
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="col-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold flex-grow-1" style="font-size: 0.8rem; background: var(--wm-primary); border-color: var(--wm-primary);">
                        <i data-lucide="filter" style="width: 13px; height: 13px;"></i> Filter
                    </button>
                    @if(request()->hasAny(['category', 'date_from', 'date_to', 'search']))
                        <a href="{{ route('super-admin.audit-log.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Reset Filter" style="font-size: 0.8rem;">
                            <i data-lucide="rotate-ccw" style="width: 13px; height: 13px;"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ======================================================================
         4. TABEL LOG KRONOLOGIS AKTIVITAS SISTEM
         ====================================================================== --}}
    <div class="sa-box-container p-0 overflow-hidden mb-4">
        <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2 bg-white">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i data-lucide="list-ordered" style="width: 18px; height: 18px; vertical-align: middle;" class="text-primary me-1"></i>
                    Daftar Rekaman Aktivitas Sistem
                </h5>
                <small class="text-muted">Urutan kronologis (terbaru di atas) &bull; Menampilkan {{ $logs->firstItem() ?? 0 }}-{{ $logs->lastItem() ?? 0 }} dari total {{ $logs->total() }} record</small>
            </div>
            <div>
                <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">
                    Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-audit align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 150px;">Waktu &amp; IP</th>
                        <th style="min-width: 140px;">Kategori</th>
                        <th style="min-width: 200px;">Pelaku Aktivitas</th>
                        <th style="min-width: 160px;">Kode Aksi</th>
                        <th style="min-width: 280px;">Deskripsi Aktivitas</th>
                        <th style="min-width: 110px; text-align: center;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            {{-- Waktu & IP --}}
                            <td>
                                <strong class="d-block text-dark" style="font-size: 0.82rem;">
                                    {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                                </strong>
                                <small class="text-muted font-monospace" style="font-size: 0.72rem;">
                                    <i data-lucide="globe" style="width: 11px; height: 11px;"></i> {{ $log->ip_address ?: '127.0.0.1' }}
                                </small>
                            </td>

                            {{-- Kategori --}}
                            <td>
                                <span class="badge rounded-pill fw-bold {{ $log->category_badge_class }}" style="font-size: 0.72rem;">
                                    {{ $log->category_label }}
                                </span>
                            </td>

                            {{-- Pelaku Aktivitas --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 32px; height: 32px; background: #1456A0; font-size: 0.75rem; flex-shrink: 0;">
                                        {{ $log->user ? substr($log->user->name, 0, 1) : 'S' }}
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark" style="font-size: 0.84rem;">
                                            {{ $log->user ? $log->user->name : 'Sistem / Tamu' }}
                                        </strong>
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            @if($log->user)
                                                <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;">
                                                    {{ strtoupper($log->user->role) }}
                                                </span>
                                            @endif
                                            @if($log->impersonator_id)
                                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 fw-bold" style="font-size: 0.65rem;" title="Aksi dijalankan saat Super Admin sedang simulasi peran">
                                                    <i data-lucide="shuffle" style="width: 9px; height: 9px;"></i> via Super Admin
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kode Aksi --}}
                            <td>
                                <code class="p-1 px-2 rounded bg-light border text-primary" style="font-size: 0.75rem;">
                                    {{ $log->action ?: $log->activity }}
                                </code>
                            </td>

                            {{-- Deskripsi Aktivitas --}}
                            <td>
                                <p class="mb-0 text-dark small" style="line-height: 1.45;">
                                    {{ $log->description }}
                                </p>
                            </td>

                            {{-- Detail Metadata --}}
                            <td class="text-center">
                                @if(!empty($log->metadata))
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 btn-view-metadata" data-log-id="{{ $log->id }}" data-metadata='@json($log->metadata)' data-action="{{ $log->action }}" data-time="{{ $log->created_at->format('d/m/Y H:i:s') }}" style="font-size: 0.75rem;">
                                        <i data-lucide="eye" style="width: 12px; height: 12px;"></i> Data
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="p-3 bg-light rounded-circle d-inline-flex mb-2">
                                    <i data-lucide="inbox" style="width: 32px; height: 32px;" class="text-muted"></i>
                                </div>
                                <p class="mb-1 fw-bold text-dark">Tidak Ada Log Aktivitas yang Cocok</p>
                                <p class="small text-muted mb-0">Coba ubah filter kategori, rentang tanggal, atau kata kunci pencarian Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-3 px-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-2 bg-light">
                <small class="text-muted">Menampilkan {{ $logs->firstItem() }} s/d {{ $logs->lastItem() }} dari {{ $logs->total() }} log</small>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ======================================================================
     MODAL DETAIL METADATA JSON
     ====================================================================== --}}
<div class="modal fade" id="modalMetadata" tabindex="-1" aria-labelledby="modalMetadataLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-circle" style="background: var(--wm-light-blue); color: var(--wm-primary);">
                        <i data-lucide="code-2" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="modalMetadataLabel">
                            Detail Metadata Konteks
                        </h5>
                        <small class="text-muted" id="modalMetadataSub">Aksi: -</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-2 d-flex justify-content-between align-items-center">
                    <span class="small fw-bold text-muted">Format JSON Konteks:</span>
                    <span class="badge bg-light text-secondary border font-monospace small" id="modalMetadataTime">-</span>
                </div>
                <pre class="bg-dark text-white p-3 rounded-3 mb-0" id="jsonViewer" style="font-size: 0.78rem; max-height: 320px; overflow-y: auto; font-family: Consolas, monospace;"></pre>
            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Event listener untuk tombol lihat metadata
        document.querySelectorAll('.btn-view-metadata').forEach(btn => {
            btn.addEventListener('click', function () {
                const metadataStr = this.getAttribute('data-metadata');
                const action = this.getAttribute('data-action');
                const time = this.getAttribute('data-time');

                let formatted = '{}';
                try {
                    const parsed = JSON.parse(metadataStr);
                    formatted = JSON.stringify(parsed, null, 2);
                } catch (e) {
                    formatted = metadataStr;
                }

                document.getElementById('modalMetadataSub').textContent = 'Kode Aksi: ' + action;
                document.getElementById('modalMetadataTime').textContent = time;
                document.getElementById('jsonViewer').textContent = formatted;

                const modal = new bootstrap.Modal(document.getElementById('modalMetadata'));
                modal.show();
            });
        });
    });
</script>
@endpush
