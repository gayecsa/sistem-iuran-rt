@extends('layouts.app')

@section('title', 'Sistem Ronda & Keamanan')

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
        --wm-success: #16865D;
        --wm-success-bg: #E8F6EF;
        --wm-warning: #D97706;
    }

    /* Hero Banner Ronda */
    .ronda-hero-header {
        background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #FFFFFF;
        box-shadow: 0 4px 14px rgba(20, 86, 160, 0.15);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .ronda-hero-header::after {
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

    /* Standard Warkas Card */
    .wm-card-box {
        background: #FFFFFF;
        border: 1px solid var(--wm-border-card);
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        padding: 24px;
        margin-bottom: 24px;
    }

    /* Pulse Dot Indicator */
    .cctv-pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: currentColor;
        animation: cctvPulse 1.5s infinite ease-in-out;
        vertical-align: middle;
    }

    @keyframes cctvPulse {
        0% { transform: scale(0.85); opacity: 0.6; }
        50% { transform: scale(1.25); opacity: 1; }
        100% { transform: scale(0.85); opacity: 0.6; }
    }

    /* Warga Status Keamanan Panel */
    .warga-cctv-banner {
        background: var(--wm-light-blue);
        border: 1px solid #C2DCFF;
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 20px;
    }

    .cctv-point-card {
        background: #F8FAFC;
        border: 1px solid var(--wm-border-card);
        border-radius: 12px;
        padding: 18px;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .cctv-point-card:hover {
        background: #FFFFFF;
        border-color: var(--wm-secondary);
        box-shadow: 0 4px 12px rgba(20, 86, 160, 0.08);
        transform: translateY(-2px);
    }

    /* High-Tech CCTV Monitoring Viewport */
    .cctv-monitor-box {
        background: #0B1120;
        border: 1px solid #1E293B;
        border-radius: 16px;
        padding: 24px;
        color: #FFFFFF;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }

    .cctv-viewport {
        position: relative;
        aspect-ratio: 16/9;
        background: radial-gradient(circle at center, #111C33 0%, #080D1A 100%);
        border: 1px solid #1E293B;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.85);
    }

    .cctv-scanlines {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%);
        background-size: 100% 4px;
        z-index: 2;
        pointer-events: none;
        opacity: 0.6;
    }

    .cctv-reticle {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
        pointer-events: none;
    }

    .cctv-hud-top {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        padding: 10px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 3;
        background: linear-gradient(rgba(0, 0, 0, 0.7) 0%, transparent 100%);
    }

    .cctv-hud-bottom {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 12px 14px;
        z-index: 3;
        background: linear-gradient(transparent 0%, rgba(8, 13, 26, 0.95) 80%);
    }

    /* Table Styles */
    .table-ronda {
        margin-bottom: 0;
        font-size: 0.88rem;
    }

    .table-ronda th {
        background: #F8FAFC;
        color: var(--wm-text-dark);
        font-weight: 700;
        padding: 12px 16px;
        border-bottom: 2px solid var(--wm-border-card);
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-ronda td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #EDF2F7;
    }

    /* Badges & Steps */
    .step-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Tab System Navigation (Warkas Blue Design) */
    .ronda-tab-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        font-weight: 700;
        font-size: 0.92rem;
        color: #64748B;
        text-decoration: none;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .ronda-tab-item:hover {
        color: var(--wm-primary);
        background: #F8FAFC;
        border-radius: 8px 8px 0 0;
    }
    .ronda-tab-item.active {
        color: var(--wm-primary);
        border-bottom-color: var(--wm-primary);
        background: rgba(20, 86, 160, 0.05);
        border-radius: 8px 8px 0 0;
    }

    @media (max-width: 991.98px) {
        .ronda-hero-header {
            padding: 20px;
        }
        .wm-card-box {
            padding: 18px;
        }
    }

    /* Dark Mode Overrides for Ronda Module */
    [data-theme="dark"] .wm-card-box,
    body.dark-mode .wm-card-box {
        background: #131F35 !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
    }

    [data-theme="dark"] .wm-card-box h5,
    body.dark-mode .wm-card-box h5,
    [data-theme="dark"] .wm-card-box h6,
    body.dark-mode .wm-card-box h6,
    [data-theme="dark"] .wm-card-box strong,
    body.dark-mode .wm-card-box strong,
    [data-theme="dark"] .wm-card-box .text-dark,
    body.dark-mode .wm-card-box .text-dark {
        color: #F1F5F9 !important;
    }

    [data-theme="dark"] .wm-card-box .text-muted,
    body.dark-mode .wm-card-box .text-muted,
    [data-theme="dark"] .wm-card-box .text-secondary,
    body.dark-mode .wm-card-box .text-secondary {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .table-ronda,
    body.dark-mode .table-ronda {
        color: #F1F5F9 !important;
    }

    [data-theme="dark"] .table-ronda th,
    body.dark-mode .table-ronda th {
        background: #182742 !important;
        color: #CBD5E1 !important;
        border-color: #24344F !important;
    }

    [data-theme="dark"] .table-ronda td,
    body.dark-mode .table-ronda td {
        background: transparent !important;
        color: #F1F5F9 !important;
        border-color: #24344F !important;
    }

    [data-theme="dark"] .table-ronda tr:hover td,
    body.dark-mode .table-ronda tr:hover td {
        background: rgba(30, 45, 74, 0.5) !important;
    }

    [data-theme="dark"] .table-ronda .table-warning td,
    body.dark-mode .table-ronda .table-warning td {
        background: rgba(217, 119, 6, 0.16) !important;
        color: #FCD34D !important;
    }

    [data-theme="dark"] .cctv-point-card,
    body.dark-mode .cctv-point-card {
        background: #16243D !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
    }

    [data-theme="dark"] .cctv-point-card:hover,
    body.dark-mode .cctv-point-card:hover {
        background: #1A2B49 !important;
        border-color: #3B82F6 !important;
    }

    [data-theme="dark"] .warga-cctv-banner,
    body.dark-mode .warga-cctv-banner {
        background: rgba(40, 120, 208, 0.14) !important;
        border-color: rgba(40, 120, 208, 0.3) !important;
        color: #BFDBFE !important;
    }

    [data-theme="dark"] .ronda-nav-tabs,
    body.dark-mode .ronda-nav-tabs {
        border-bottom-color: #24344F !important;
        background: transparent !important;
    }
    [data-theme="dark"] .ronda-tab-item,
    body.dark-mode .ronda-tab-item {
        color: #94A3B8 !important;
        background: transparent !important;
    }
    [data-theme="dark"] .ronda-tab-item:hover,
    body.dark-mode .ronda-tab-item:hover {
        color: #60A5FA !important;
        background: transparent !important;
    }
    [data-theme="dark"] .ronda-tab-item.active,
    body.dark-mode .ronda-tab-item.active {
        color: #60A5FA !important;
        border-bottom-color: #3B82F6 !important;
        background: rgba(59, 130, 246, 0.1) !important;
    }

    /* Portal Gate Boom Barrier Visualizer */
    .portal-gate-visualizer {
        background: radial-gradient(circle at center, #111C33 0%, #080D1A 100%);
        border: 1px solid #1E293B;
        border-radius: 16px;
        padding: 24px;
        color: #FFFFFF;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    .boom-gate-box {
        position: relative;
        height: 180px;
        width: 100%;
        display: flex;
        align-items: flex-end;
        padding-bottom: 20px;
        border-bottom: 3px dashed #334155;
    }
    .boom-barrier-housing {
        width: 38px;
        height: 90px;
        background: linear-gradient(180deg, #F59E0B 0%, #D97706 100%);
        border-radius: 8px 8px 0 0;
        position: relative;
        box-shadow: 0 4px 10px rgba(0,0,0,0.4);
        z-index: 2;
        border: 2px solid #B45309;
    }
    .boom-barrier-lamp {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #EF4444;
        box-shadow: 0 0 10px #EF4444;
        margin: 8px auto 0;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    .boom-barrier-lamp.open {
        background: #10B981;
        box-shadow: 0 0 15px #10B981;
    }
    .boom-barrier-arm {
        position: absolute;
        bottom: 75px;
        left: 20px;
        height: 10px;
        width: calc(100% - 40px);
        max-width: 480px;
        background: repeating-linear-gradient(45deg, #EF4444, #EF4444 15px, #FFFFFF 15px, #FFFFFF 30px);
        border-radius: 4px;
        transform-origin: 0% 50%;
        transition: transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 2px 6px rgba(0,0,0,0.5);
        z-index: 1;
    }
    .boom-barrier-arm.open {
        transform: rotate(-65deg);
    }
    .portal-live-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 0.78rem;
    }
    .rfid-chip-card {
        background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%);
        border-radius: 16px;
        padding: 24px;
        color: #FFFFFF;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(20, 86, 160, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .rfid-chip-card::after {
        content: "";
        position: absolute;
        top: -40px;
        right: -30px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    @php
        // Deteksi hak akses pengurus (RW, RT, Administrator, Bendahara)
        $isPengurus = $user->isRw() || $user->isRt() || in_array($user->role, ['super_admin', 'admin', 'bendahara']);
        $hasApprovedCctv = count($allowedCctvIds) > 0;
    @endphp

    {{-- ======================================================================
         1. HERO BANNER RONDA & KEAMANAN (WARKAS BLUE DESIGN SYSTEM)
         ====================================================================== --}}
    <div class="ronda-hero-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                    <i data-lucide="shield" style="width: 12px; height: 12px; vertical-align: middle;"></i>
                    SISTEM KEAMANAN &amp; SISKAMLING TERPADU
                </span>
                <span class="text-white-50 small">•</span>
                <span class="text-white small fw-medium">Kelurahan Sukamiskin, Arcamanik</span>
            </div>
            <h3 class="fw-bold mb-1 text-white">Ronda Malam &amp; Keamanan RW 013</h3>
            <p class="mb-0 text-white-50 small">
                <i data-lucide="map-pin" style="width: 14px; height: 14px;" class="me-1"></i>
                Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293 &bull; Integrasi Siskamling 8 RT, Linmas, &amp; Titik Pantau Kawasan.
            </p>
        </div>

        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('peta.index') }}" class="btn btn-outline-light rounded-pill px-3 fw-bold small d-inline-flex align-items-center gap-2">
                <i data-lucide="map" style="width: 16px; height: 16px;"></i>
                <span>Lihat Titik di Peta</span>
            </a>
            @if($user->role === 'admin' || $user->role === 'super_admin' || $user->isRw())
                <a href="{{ route('cctv.index') }}" class="btn btn-outline-light rounded-pill px-3 fw-bold small d-inline-flex align-items-center gap-2">
                    <i data-lucide="video" style="width: 16px; height: 16px;"></i>
                    <span>Kelola Titik CCTV</span>
                </a>
            @endif
            <a href="tel:112" class="btn btn-danger rounded-pill px-3 fw-bold small shadow d-inline-flex align-items-center gap-2">
                <i data-lucide="phone-call" style="width: 16px; height: 16px;"></i>
                <span>Darurat Bandung (112)</span>
            </a>
        </div>
    </div>

    {{-- ======================================================================
         BANNER PERINGATAN DINI KEAMANAN URGENT LINTAS RT
         (Hanya muncul jika ada minimal 1 laporan Darurat/Segera aktif di RW 013)
         ====================================================================== --}}
    @if(isset($urgentWarnings) && $urgentWarnings->isNotEmpty())
        @php
            $latestUrgent = $urgentWarnings->first();
            $isTopDarurat = ($daruratCount > 0);
            $bannerBg = $isTopDarurat ? '#FEF2F2' : '#FFFBEB';
            $bannerBorder = $isTopDarurat ? '#FCA5A5' : '#FCD34D';
            $bannerText = $isTopDarurat ? '#991B1B' : '#92400E';
            $badgeClass = $isTopDarurat ? 'bg-danger text-white' : 'bg-warning text-dark';
        @endphp
        <div class="card border-0 shadow-sm mb-4" style="background: {{ $bannerBg }}; border: 1.5px solid {{ $bannerBorder }} !important; border-radius: 14px;">
            <div class="card-body p-3 p-md-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-circle" style="width: 42px; height: 42px; background: {{ $isTopDarurat ? '#FEE2E2' : '#FEF3C7' }}; border: 1px solid {{ $bannerBorder }};">
                        <span class="wm-pulse-dot {{ $isTopDarurat ? 'danger' : 'warning' }}" style="width: 12px; height: 12px;"></span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <span class="badge {{ $badgeClass }} fw-bold px-2 py-1" style="font-size: 0.75rem;">
                                <i data-lucide="{{ $isTopDarurat ? 'alert-octagon' : 'alert-triangle' }}" style="width: 12px; height: 12px; display: inline-block; vertical-align: -1px;"></i>
                                {{ $isTopDarurat ? 'SIAGA DARURAT' : 'PERINGATAN WASPADA' }}
                            </span>
                            <span class="badge bg-white text-dark border px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                RT {{ str_pad($latestUrgent->rt_number, 3, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="small fw-bold" style="color: {{ $bannerText }};">
                                • {{ $latestUrgent->jenis_kejadian }} di {{ $latestUrgent->lokasi }}
                            </span>
                            <span class="small text-muted">
                                • {{ $latestUrgent->created_at ? $latestUrgent->created_at->diffForHumans() : 'Baru saja' }}
                            </span>
                        </div>
                        <p class="mb-0 small" style="color: {{ $bannerText }}; line-height: 1.45;">
                            {{ Str::limit($latestUrgent->kronologi_clean, 140) }}
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0 text-md-end">
                    <a href="{{ route('ronda.index', ['tab' => 'riwayat']) }}#peringatan-dini" class="btn btn-sm {{ $isTopDarurat ? 'btn-danger' : 'btn-warning text-dark' }} fw-bold rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1.5" style="white-space: nowrap;">
                        <span>Lihat Semua Peringatan ({{ $urgentWarnings->count() }})</span>
                        <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- ======================================================================
         BANNER KABAR KONDUSIF KEAMANAN TERKINI (24 JAM TERAKHIR)
         (Ditampilkan ke SEMUA ROLE saat ada laporan urgent yang baru dinyatakan aman oleh RW)
         ====================================================================== --}}
    @if(isset($recentKondusifWarnings) && $recentKondusifWarnings->isNotEmpty())
        @php
            $latestKondusif = $recentKondusifWarnings->first();
        @endphp
        <div class="card border-0 shadow-sm mb-4" style="background: #E8F6EF; border: 1.5px solid rgba(22, 134, 93, 0.3) !important; border-radius: 14px;">
            <div class="card-body p-3 p-md-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-circle" style="width: 42px; height: 42px; background: #D1F0E2; border: 1px solid rgba(22, 134, 93, 0.4);">
                        <i data-lucide="shield-check" style="width: 22px; height: 22px; color: #16865D;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <span class="badge text-white fw-bold px-2 py-1" style="font-size: 0.75rem; background: #16865D !important;">
                                <i data-lucide="check-circle" style="width: 12px; height: 12px; display: inline-block; vertical-align: -1px;"></i>
                                INFORMASI TERKINI: SITUASI KEMBALI KONDUSIF
                            </span>
                            <span class="badge bg-white text-dark border px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                RT {{ str_pad($latestKondusif->rt_number, 3, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="small fw-bold text-success" style="color: #0E5A3E !important;">
                                • {{ $latestKondusif->jenis_kejadian }} di {{ $latestKondusif->lokasi }}
                            </span>
                            <span class="small text-muted">
                                • Dinyatakan aman {{ $latestKondusif->rw_resolved_at ? $latestKondusif->rw_resolved_at->diffForHumans() : 'baru saja' }}
                            </span>
                        </div>
                        <p class="mb-0 small text-dark" style="line-height: 1.45;">
                            <strong>Pesan Resmi Ketua RW:</strong> <em>"{{ $latestKondusif->rw_resolve_note ?: 'Situasi di lokasi telah diverifikasi aman terkendali bersama siskamling setempat.' }}"</em>
                        </p>
                    </div>
                </div>
                @if($recentKondusifWarnings->count() > 1)
                    <div class="flex-shrink-0 text-md-end">
                        <span class="badge bg-white text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill fw-bold" style="font-size: 0.75rem;">
                            {{ $recentKondusifWarnings->count() }} Laporan Selesai 24 Jam Terakhir
                        </span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ======================================================================
         TAB BAR SISTEM RONDA & KEAMANAN (WARKAS BLUE DESIGN)
         ====================================================================== --}}
    <div class="ronda-nav-tabs d-flex align-items-center gap-2 mb-4 overflow-auto pb-1" style="border-bottom: 2px solid #E2E8F0;">
        <a href="{{ route('ronda.index', ['tab' => 'cctv']) }}" class="ronda-tab-item {{ $activeTab === 'cctv' ? 'active' : '' }}">
            <i data-lucide="video" style="width: 18px; height: 18px;"></i>
            <span>CCTV &amp; Jadwal Ronda</span>
        </a>
        <a href="{{ route('ronda.index', ['tab' => 'lapor']) }}" class="ronda-tab-item {{ $activeTab === 'lapor' ? 'active' : '' }}">
            <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
            <span>Lapor Hal Mencurigakan</span>
        </a>
        <a href="{{ route('ronda.index', ['tab' => 'riwayat']) }}" class="ronda-tab-item {{ $activeTab === 'riwayat' ? 'active' : '' }}">
            <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
            <span>Riwayat Laporan Keamanan</span>
            @if(isset($urgentWarnings) && $urgentWarnings->isNotEmpty())
                <span class="badge rounded-pill bg-danger text-white ms-1 fw-bold shadow-sm" style="font-size: 0.72rem; padding: 3px 8px;">
                    <i data-lucide="alert-triangle" style="width: 11px; height: 11px; display: inline-block; vertical-align: -1px;"></i>
                    {{ $daruratCount > 0 ? $daruratCount . ' Darurat' : $urgentWarnings->count() . ' Urgent' }}
                </span>
            @elseif(isset($laporanKeamananList) && $laporanKeamananList->count() > 0)
                <span class="badge rounded-pill bg-primary ms-1" style="font-size: 0.72rem;">{{ $laporanKeamananList->count() }}</span>
            @endif
        </a>
        <a href="{{ route('ronda.index', ['tab' => 'portal']) }}" class="ronda-tab-item {{ $activeTab === 'portal' ? 'active' : '' }}">
            <i data-lucide="radio" style="width: 18px; height: 18px;"></i>
            <span>Portal Gerbang &amp; IoT</span>
            @if(isset($iotDevice) && $iotDevice->isOnline())
                <span class="badge rounded-pill bg-success text-white ms-1 d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.68rem; padding: 2px 7px;">
                    <span class="cctv-pulse-dot" style="width: 6px; height: 6px; background: #FFF;"></span> Online
                </span>
            @else
                <span class="badge rounded-pill bg-secondary text-white ms-1" style="font-size: 0.68rem; padding: 2px 7px;">Offline</span>
            @endif
        </a>
    </div>

    @if($activeTab === 'cctv')
    {{-- ======================================================================
         2. VIEWPORT CCTV KHUSUS JIKA PENGGUNA BERHAK MEMANTAU
         (Untuk Pengurus: RT memantau RT-nya, RW memantau seluruh RW;
          Untuk Warga: HANYA jika sudah disetujui penuh RT & RW)
         ====================================================================== --}}
    @if($isPengurus || ($hasApprovedCctv && $monitoredCctvs->isNotEmpty()))
        <div class="cctv-monitor-box">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        @if($isPengurus)
                            @if($user->isRt())
                                <span class="badge rounded-pill px-2 py-0 fw-bold" style="background: rgba(20, 86, 160, 0.25); color: #60A5FA; border: 1px solid rgba(96, 165, 250, 0.4); font-size: 0.7rem;">
                                    <span class="cctv-pulse-dot me-1" style="background: #3B82F6;"></span> MONITORING RT {{ $user->clean_rt_label }}
                                </span>
                                <span class="text-white-50 small">•</span>
                                <span class="text-white-50 small">Hak Akses Teritorial: Kamera CCTV Wilayah RT {{ $user->clean_rt_label }}</span>
                            @else
                                <span class="badge rounded-pill px-2 py-0 fw-bold" style="background: rgba(220, 38, 38, 0.2); color: #F87171; border: 1px solid rgba(220, 38, 38, 0.4); font-size: 0.7rem;">
                                    <span class="cctv-pulse-dot me-1" style="background: #EF4444;"></span> MONITORING PUSAT RW 013
                                </span>
                                <span class="text-white-50 small">•</span>
                                <span class="text-white-50 small">Hak Akses Penuh: Ketua RW, Administrator &amp; Linmas</span>
                            @endif
                        @else
                            <span class="badge rounded-pill px-2 py-0 fw-bold" style="background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.4); font-size: 0.7rem;">
                                <span class="cctv-pulse-dot me-1" style="background: #10B981;"></span> AKSES KHUSUS RESMI DISETUJUI
                            </span>
                            <span class="text-white-50 small">•</span>
                            <span class="text-white-50 small">Persetujuan Berjenjang: Ketua RT &amp; Ketua RW 013</span>
                        @endif
                    </div>
                    <h5 class="fw-bold mb-0 text-white">
                        <i data-lucide="tv" style="width: 20px; height: 20px; vertical-align: middle;" class="text-info me-2"></i>
                        @if(!$isPengurus)
                            Pantauan Langsung CCTV (Izin Akses Warga Aktif)
                        @else
                            Pantauan Kamera CCTV Kawasan Sukamiskin Arcamanik
                        @endif
                    </h5>
                    <small class="text-white-50">
                        @if(!$isPengurus)
                            Gunakan tayangan pemantauan ini secara bertanggung jawab demi kepentingan keamanan lingkungan.
                        @else
                            Sistem monitoring portal utama, persimpangan jalan lingkungan, dan pos ronda 24 jam.
                        @endif
                    </small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill px-3 py-1" style="background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.3);">
                        <span class="cctv-pulse-dot me-1"></span> {{ $monitoredCctvs->count() }} Kamera Aktif
                    </span>
                    @if($user->role === 'admin' || $user->role === 'super_admin' || $user->isRw())
                        <a href="{{ route('cctv.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3" style="font-size: 0.75rem;">
                            <i data-lucide="settings" style="width: 12px; height: 12px;"></i> Pengaturan
                        </a>
                    @endif
                </div>
            </div>

            <div class="row g-3">
                @forelse($monitoredCctvs as $cam)
                    <div class="col-12 col-md-6 {{ $monitoredCctvs->count() > 2 ? 'col-lg-3' : 'col-lg-6' }}">
                        <div class="cctv-viewport">
                            {{-- Scanlines effect --}}
                            <div class="cctv-scanlines"></div>

                            {{-- SVG Reticle & Viewfinder HUD --}}
                            <svg class="cctv-reticle" viewBox="0 0 400 225" preserveAspectRatio="none">
                                {{-- Corner brackets --}}
                                <path d="M 16 26 L 16 16 L 26 16" stroke="rgba(56, 189, 248, 0.45)" stroke-width="2" fill="none" />
                                <path d="M 384 26 L 384 16 L 374 16" stroke="rgba(56, 189, 248, 0.45)" stroke-width="2" fill="none" />
                                <path d="M 16 199 L 16 209 L 26 209" stroke="rgba(56, 189, 248, 0.45)" stroke-width="2" fill="none" />
                                <path d="M 384 199 L 384 209 L 374 209" stroke="rgba(56, 189, 248, 0.45)" stroke-width="2" fill="none" />
                                {{-- Center Crosshair --}}
                                <circle cx="200" cy="112.5" r="28" stroke="rgba(56, 189, 248, 0.22)" stroke-width="1.5" stroke-dasharray="3,3" fill="none" />
                                <line x1="188" y1="112.5" x2="212" y2="112.5" stroke="rgba(56, 189, 248, 0.4)" stroke-width="1.5" />
                                <line x1="200" y1="100.5" x2="200" y2="124.5" stroke="rgba(56, 189, 248, 0.4)" stroke-width="1.5" />
                            </svg>

                            {{-- Top HUD --}}
                            <div class="cctv-hud-top">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-danger rounded-pill px-2 py-0 fw-bold" style="font-size: 0.65rem;">
                                        <span class="cctv-pulse-dot me-1"></span> REC
                                    </span>
                                    <span class="badge bg-dark text-white-50 border border-secondary" style="font-size: 0.65rem;">
                                        CAM-0{{ $loop->iteration }} (RT {{ $cam->clean_rt }})
                                    </span>
                                </div>
                                <div class="font-monospace text-white-50" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                    <span class="cctv-clock">{{ now()->format('H:i:s') }}</span> WIB
                                </div>
                            </div>

                            {{-- Center Subject Silhouette --}}
                            <div class="position-absolute top-50 start-50 translate-middle text-center" style="z-index: 1;">
                                <div class="mb-1" style="color: rgba(56, 189, 248, 0.35);">
                                    <i data-lucide="video" style="width: 32px; height: 32px;"></i>
                                </div>
                                <div class="font-monospace text-uppercase" style="font-size: 0.65rem; letter-spacing: 1.5px; color: rgba(255, 255, 255, 0.4);">
                                    LIVE MONITORING
                                </div>
                            </div>

                            {{-- Bottom HUD --}}
                            <div class="cctv-hud-bottom">
                                <div class="d-flex justify-content-between align-items-end">
                                    <div style="max-width: 72%;">
                                        <strong class="d-block text-white text-truncate small" style="font-size: 0.8rem;">{{ $cam->nama }}</strong>
                                        <small class="text-white-50 d-block text-truncate" style="font-size: 0.72rem;">
                                            <i data-lucide="map-pin" style="width: 10px; height: 10px;"></i> {{ $cam->lokasi ?: 'Sukamiskin Arcamanik' }} (RT {{ $cam->clean_rt }})
                                        </small>
                                    </div>
                                    <div class="text-end" style="flex-shrink: 0;">
                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25" style="font-size: 0.65rem;">
                                            HD 1080p
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-white-50">
                        <i data-lucide="video-off" style="width: 36px; height: 36px;" class="mb-2"></i>
                        <p class="mb-0 small">Tidak ada titik kamera yang tercakup dalam wilayah otoritas Anda saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ======================================================================
         3. PANEL TITIK CCTV LINGKUNGAN UNTUK WARGA (Atau Informasi Umum)
         ====================================================================== --}}
    @if(!$isPengurus)
        <div class="wm-card-box">
            {{-- Banner Ringkasan Status & SOP Privasi --}}
            <div class="warga-cctv-banner">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 rounded-3 bg-white border shadow-xs text-primary" style="flex-shrink: 0; color: var(--wm-primary);">
                            <i data-lucide="shield-check" style="width: 28px; height: 28px;"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h5 class="fw-bold mb-0" style="color: var(--wm-primary-dark);">
                                    4 Titik CCTV Kawasan RW 013 Aktif 24 Jam
                                </h5>
                                <span class="badge rounded-pill fw-bold" style="background: var(--wm-success-bg); color: var(--wm-success); border: 1px solid #A3E0C1; font-size: 0.72rem;">
                                    <span class="cctv-pulse-dot me-1"></span> Terpantau Linmas &amp; Pengurus
                                </span>
                            </div>
                            <p class="mb-0 small" style="color: var(--wm-text-dark); line-height: 1.5;">
                                Dipantau langsung oleh Pengurus RT/RW dan Linmas 24 jam. Demi privasi warga dan standar operasional keamanan lingkungan (SOP), tayangan langsung video (live feed) dibatasi. Warga dapat mengajukan izin akses pantauan sementara yang akan diverifikasi berjenjang oleh <strong>Ketua RT</strong> dan <strong>Ketua RW</strong>.
                            </p>
                        </div>
                    </div>

                    <div style="flex-shrink: 0;" class="d-flex gap-2">
                        <button type="button" class="btn btn-primary rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-xs" style="background: var(--wm-primary); border-color: var(--wm-primary); font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#modalAjukanCctv">
                            <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                            <span>Ajukan Izin Akses CCTV</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Grid Daftar 4 Lokasi Titik CCTV Lingkungan --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">
                    <i data-lucide="map-pin" style="width: 14px; height: 14px; vertical-align: middle;" class="text-primary me-1"></i>
                    Daftar Titik Pemantauan CCTV Wilayah RW 013
                </span>
                <span class="badge bg-light text-secondary border px-2 py-1 small">Total 4 Titik Strategis</span>
            </div>

            <div class="row g-3">
                @forelse($cctvs as $cam)
                    @php
                        $isAuthorized = in_array($cam->id, $allowedCctvIds);
                    @endphp
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="cctv-point-card">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-3" style="width: 38px; height: 38px; background: var(--wm-light-blue); color: var(--wm-primary);">
                                        <i data-lucide="camera" style="width: 18px; height: 18px;"></i>
                                    </div>
                                    @if($isAuthorized)
                                        <span class="badge rounded-pill fw-bold" style="background: var(--wm-success-bg); color: var(--wm-success); border: 1px solid #A3E0C1; font-size: 0.72rem;">
                                            <i data-lucide="unlock" style="width: 11px; height: 11px;" class="me-1"></i> Akses Aktif
                                        </span>
                                    @else
                                        <span class="badge rounded-pill fw-bold text-secondary bg-light border" style="font-size: 0.72rem;">
                                            <i data-lucide="lock" style="width: 11px; height: 11px;" class="me-1"></i> Perlu Izin
                                        </span>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-1" style="color: var(--wm-text-dark); font-size: 0.9rem; line-height: 1.35;">
                                    {{ $cam->nama }}
                                </h6>
                                <p class="text-muted small mb-2" style="font-size: 0.8rem; line-height: 1.4;">
                                    <i data-lucide="map-pin" style="width: 12px; height: 12px;" class="text-danger me-1"></i>
                                    {{ $cam->lokasi ?: 'Sukamiskin Arcamanik' }} (RT {{ $cam->clean_rt }})
                                </p>
                            </div>
                            <div class="pt-3 mt-3 border-top">
                                @if($isAuthorized)
                                    <span class="btn btn-sm btn-outline-success w-100 rounded-pill disabled fw-bold" style="font-size: 0.75rem;">
                                        <i data-lucide="check" style="width: 14px; height: 14px;"></i> Sedang Ditampilkan
                                    </span>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-1 btn-open-request-modal" data-cctv-id="{{ $cam->id }}" data-cctv-name="{{ $cam->nama }} (RT {{ $cam->clean_rt }})" style="font-size: 0.78rem;">
                                        <i data-lucide="key" style="width: 13px; height: 13px;"></i>
                                        <span>Ajukan Izin Akses</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <i data-lucide="video-off" style="width: 36px; height: 36px;" class="mb-2"></i>
                        <p class="mb-0 small">Belum ada titik kamera terdaftar aktif.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- RIWAYAT PERMINTAAN IZIN AKSES CCTV OLEH WARGA --}}
        <div class="wm-card-box">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0" style="color: var(--wm-text-dark);">
                        <i data-lucide="history" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Status Permohonan Izin Akses CCTV Saya
                    </h5>
                    <small class="text-muted">Pelacakan proses persetujuan bertingkat (Ketua RT &rarr; Ketua RW) untuk akun Anda.</small>
                </div>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAjukanCctv">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Ajukan Izin Baru
                </button>
            </div>

            @if($accessRequests->isEmpty())
                <div class="text-center py-4 text-muted">
                    <div class="p-3 bg-light rounded-circle d-inline-flex mb-2">
                        <i data-lucide="file-text" style="width: 28px; height: 28px;" class="text-muted"></i>
                    </div>
                    <p class="mb-1 fw-bold text-dark">Belum Ada Permohonan Izin Akses</p>
                    <p class="small text-muted mb-3">Jika Anda memerlukan pemantauan untuk urusan keamanan mendesak, silakan ajukan izin akses.</p>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAjukanCctv">
                        Ajukan Izin Akses Sekarang
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-ronda align-middle">
                        <thead>
                            <tr>
                                <th style="min-width: 140px;">Kamera CCTV</th>
                                <th style="min-width: 200px;">Alasan Permohonan</th>
                                <th style="min-width: 160px;">Persetujuan RT</th>
                                <th style="min-width: 160px;">Persetujuan RW</th>
                                <th style="min-width: 140px;">Status Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accessRequests as $req)
                                <tr>
                                    <td>
                                        <strong class="d-block" style="color: var(--wm-text-dark);">{{ $req->cctv ? $req->cctv->nama : 'Kamera #' . $req->cctv_id }}</strong>
                                        <small class="text-muted">
                                            <i data-lucide="map-pin" style="width: 11px; height: 11px;"></i> RT {{ $req->cctv ? $req->cctv->clean_rt : '-' }} &bull; {{ $req->created_at->format('d M Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="small text-dark">{{ $req->alasan }}</span>
                                        @if($req->catatan_pengurus)
                                            <div class="mt-1 p-2 rounded bg-light border text-danger small" style="font-size: 0.76rem;">
                                                <strong>Catatan Pengurus:</strong> {{ $req->catatan_pengurus }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($req->status_rt === 'approved')
                                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2 py-1">
                                                <i data-lucide="check" style="width: 12px; height: 12px;"></i> Disetujui RT
                                            </span>
                                            <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">
                                                Oleh: {{ $req->approverRt ? $req->approverRt->name : 'Ketua RT' }}
                                            </small>
                                        @elseif($req->status_rt === 'rejected')
                                            <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 px-2 py-1">
                                                <i data-lucide="x" style="width: 12px; height: 12px;"></i> Ditolak RT
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 px-2 py-1">
                                                <i data-lucide="clock" style="width: 12px; height: 12px;"></i> Menunggu RT
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($req->status_rw === 'approved')
                                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2 py-1">
                                                <i data-lucide="check" style="width: 12px; height: 12px;"></i> Disetujui RW
                                            </span>
                                            <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">
                                                Oleh: {{ $req->approverRw ? $req->approverRw->name : 'Ketua RW' }}
                                            </small>
                                        @elseif($req->status_rw === 'rejected')
                                            <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 px-2 py-1">
                                                <i data-lucide="x" style="width: 12px; height: 12px;"></i> Ditolak RW
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                                <i data-lucide="clock" style="width: 12px; height: 12px;"></i> Menunggu RW
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($req->isFullyApproved())
                                            <span class="badge bg-success text-white px-2 py-1">
                                                <i data-lucide="shield-check" style="width: 12px; height: 12px;"></i> AKSES AKTIF
                                            </span>
                                            <small class="d-block text-muted mt-1" style="font-size: 0.72rem;">
                                                s.d. {{ $req->expires_at ? $req->expires_at->format('d M Y') : '-' }}
                                            </small>
                                        @elseif($req->status_rt === 'rejected' || $req->status_rw === 'rejected')
                                            <span class="badge bg-danger text-white px-2 py-1">DITOLAK</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-1">DALAM PROSES</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- ======================================================================
         4. DAFTAR PERSETUJUAN PERMINTAAN IZIN AKSES CCTV UNTUK PENGURUS (RT & RW)
         ====================================================================== --}}
    @if($isPengurus)
        <div class="wm-card-box">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0" style="color: var(--wm-text-dark);">
                        <i data-lucide="clipboard-check" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Verifikasi Permintaan Izin Akses CCTV Warga
                    </h5>
                    <small class="text-muted">
                        @if($user->isRt())
                            Daftar permohonan warga untuk titik CCTV di lingkungan <strong>RT {{ $user->clean_rt_label }}</strong>. Persetujuan RT akan meneruskan tiket ke Ketua RW.
                        @else
                            Daftar seluruh permohonan izin akses CCTV warga di lingkungan <strong>RW 013 Sukamiskin</strong>.
                        @endif
                    </small>
                </div>
                <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill">
                    Total: {{ $accessRequests->count() }} Permohonan
                </span>
            </div>

            @if($accessRequests->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i data-lucide="check-circle" style="width: 36px; height: 36px;" class="text-success mb-2"></i>
                    <p class="mb-0 small fw-bold text-dark">Tidak ada permohonan izin CCTV yang memerlukan tindakan saat ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-ronda align-middle">
                        <thead>
                            <tr>
                                <th style="min-width: 140px;">Tanggal &amp; Warga</th>
                                <th style="min-width: 150px;">Kamera Diminta</th>
                                <th style="min-width: 220px;">Alasan Permohonan</th>
                                <th style="min-width: 150px;">Status Verifikasi</th>
                                <th style="min-width: 180px; text-align: center;">Aksi Pengurus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accessRequests as $req)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width: 34px; height: 34px; background: #1456A0; flex-shrink: 0; font-size: 0.8rem;">
                                                {{ strtoupper(substr($req->user?->name ?: 'W', 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong class="d-block" style="color: var(--wm-text-dark); font-size: 0.88rem;">{{ $req->user ? $req->user->name : 'Warga #' . $req->user_id }}</strong>
                                                <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                                                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25" style="font-size: 0.7rem;">
                                                        RT {{ $req->user ? $req->user->clean_rt_label : '-' }}
                                                    </span>
                                                    @if($req->user && $req->user->phone)
                                                        <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                                                            <i data-lucide="phone" style="width: 9px; height: 9px;"></i> {{ $req->user->phone }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($req->user && $req->user->address)
                                                    <small class="text-muted d-block text-truncate mt-1" style="max-width: 190px; font-size: 0.72rem;" title="{{ $req->user->address }}">
                                                        <i data-lucide="home" style="width: 10px; height: 10px;"></i> {{ $req->user->address }}
                                                    </small>
                                                @endif
                                                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                                    <i data-lucide="clock" style="width: 10px; height: 10px;"></i> {{ $req->created_at->translatedFormat('d M Y, H:i') }} WIB
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="d-block" style="color: var(--wm-text-dark);">{{ $req->cctv ? $req->cctv->nama : 'CCTV #' . $req->cctv_id }}</strong>
                                        <small class="text-muted">
                                            <i data-lucide="map-pin" style="width: 10px; height: 10px;"></i> Wilayah RT {{ $req->cctv ? $req->cctv->clean_rt : '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        <p class="mb-1 small text-dark">{{ $req->alasan }}</p>
                                        @if($req->catatan_pengurus)
                                            <div class="p-2 rounded bg-light border text-danger small" style="font-size: 0.75rem;">
                                                <strong>Catatan:</strong> {{ $req->catatan_pengurus }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <div>
                                                <small class="text-muted me-1">RT:</small>
                                                @if($req->status_rt === 'approved')
                                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25" style="font-size: 0.72rem;">Disetujui</span>
                                                @elseif($req->status_rt === 'rejected')
                                                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25" style="font-size: 0.72rem;">Ditolak</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25" style="font-size: 0.72rem;">Menunggu</span>
                                                @endif
                                            </div>
                                            <div>
                                                <small class="text-muted me-1">RW:</small>
                                                @if($req->status_rw === 'approved')
                                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25" style="font-size: 0.72rem;">Disetujui</span>
                                                @elseif($req->status_rw === 'rejected')
                                                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25" style="font-size: 0.72rem;">Ditolak</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.72rem;">Menunggu</span>
                                                @endif
                                            </div>
                                            @if($req->isFullyApproved())
                                                <small class="text-success fw-bold" style="font-size: 0.7rem;">
                                                    Aktif s.d. {{ $req->expires_at ? $req->expires_at->format('d/m/Y') : '-' }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{-- Aksi untuk KETUA RT --}}
                                        @if($user->isRt())
                                            @if($req->status_rt === 'pending')
                                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                    <form method="POST" action="{{ route('ronda.cctv-request.approve-rt', $req->id) }}" onsubmit="return confirm('Setujui permohonan akses CCTV ini untuk diteruskan ke Ketua RW?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                                            <i data-lucide="check" style="width: 12px; height: 12px;"></i> Setujui RT
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 fw-bold btn-open-reject-modal" data-action="{{ route('ronda.cctv-request.reject', $req->id) }}" data-user-name="{{ $req->user ? $req->user->name : 'Warga' }}" style="font-size: 0.75rem;">
                                                        <i data-lucide="x" style="width: 12px; height: 12px;"></i> Tolak
                                                    </button>
                                                </div>
                                            @else
                                                <span class="badge bg-light text-secondary border">Sudah diproses RT</span>
                                            @endif
                                        @endif

                                        {{-- Aksi untuk KETUA RW / ADMIN / SUPER ADMIN --}}
                                        @if($user->isRw() || in_array($user->role, ['super_admin', 'admin']))
                                            @if($req->status_rw === 'pending' && $req->status_rt !== 'rejected')
                                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                    <form method="POST" action="{{ route('ronda.cctv-request.approve-rw', $req->id) }}" onsubmit="return confirm('Setujui penuh permohonan akses CCTV ini? Akses pantauan warga akan diaktifkan selama 7 hari.');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                                            <i data-lucide="shield-check" style="width: 12px; height: 12px;"></i> Setujui RW
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 fw-bold btn-open-reject-modal" data-action="{{ route('ronda.cctv-request.reject', $req->id) }}" data-user-name="{{ $req->user ? $req->user->name : 'Warga' }}" style="font-size: 0.75rem;">
                                                        <i data-lucide="x" style="width: 12px; height: 12px;"></i> Tolak
                                                    </button>
                                                </div>
                                            @else
                                                <span class="badge bg-light text-secondary border">Selesai / Non-aktif</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- ======================================================================
         5. JADWAL RONDA BERGILIR (TERHUBUNG KE DATABASE RIIL)
         ====================================================================== --}}
    <div class="wm-card-box">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0" style="color: var(--wm-text-dark);">
                    <i data-lucide="calendar" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Jadwal Ronda Malam &amp; Siskamling RW 013 (Data Terdaftar)
                </h5>
                <small class="text-muted">Data koordinator piket diambil langsung dari data pengurus/warga terdaftar di database.</small>
            </div>
            <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill">
                Wilayah: Sukamiskin, Arcamanik
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-ronda align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 130px;">Hari &amp; Jam</th>
                        <th style="min-width: 130px;">Cakupan RT</th>
                        <th style="min-width: 250px;">Koordinator Piket (Warga Terdaftar)</th>
                        <th style="min-width: 210px;">Pos Ronda &amp; Area Patroli</th>
                        <th style="min-width: 130px;">Kekuatan Regu</th>
                        <th style="min-width: 110px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalRonda as $r)
                        @php
                            $isToday = str_contains($r->hari, 'Malam Ini') || (date('l') == 'Wednesday' && str_contains($r->hari, 'Rabu'));
                        @endphp
                        <tr class="{{ $isToday ? 'table-warning' : '' }}">
                            <td>
                                <strong class="d-block" style="color: var(--wm-text-dark);">{{ $r->hari }}</strong>
                                <small class="text-muted"><i data-lucide="clock" style="width: 12px; height: 12px;"></i> {{ substr($r->jam_mulai, 0, 5) }} - {{ substr($r->jam_selesai, 0, 5) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary text-white px-2 py-1">{{ $r->rt_list }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($r->coordinator1)
                                        <div class="position-relative">
                                            @php
                                                $c1Avatar = $r->coordinator1->foto_profil 
                                                    ? asset('storage/' . $r->coordinator1->foto_profil) 
                                                    : ($r->coordinator1->profile_photo 
                                                        ? asset('storage/' . $r->coordinator1->profile_photo) 
                                                        : 'https://ui-avatars.com/api/?name=' . urlencode($r->coordinator1->name) . '&background=1456A0&color=FFFFFF&bold=true');
                                            @endphp
                                            <img src="{{ $c1Avatar }}" 
                                                 alt="{{ $r->coordinator1->name }}" 
                                                 class="rounded-circle border" 
                                                 style="width: 38px; height: 38px; object-fit: cover;">
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block" style="color: var(--wm-text-dark); font-size: 0.9rem;">{{ $r->coordinator1->name }}</span>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                {{ $r->coordinator1->isRt() ? 'Ketua ' . $r->coordinator1->clean_rt_label : ($r->coordinator1->isRw() ? 'Ketua RW 013' : 'Warga ' . $r->coordinator1->clean_rt_label) }}
                                                @if($r->coordinator2)
                                                    &bull; &amp; {{ $r->coordinator2->name }}
                                                @endif
                                            </small>
                                        </div>
                                    @else
                                        <div>
                                            <span class="text-secondary fw-semibold">{{ $r->koordinator_text }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong class="d-block" style="color: var(--wm-text-dark); font-size: 0.85rem;">{{ $r->pos_ronda }}</strong>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">
                                    <i data-lucide="navigation" style="width: 11px; height: 11px;"></i> {{ $r->area_patroli }}
                                </small>
                            </td>
                            <td>
                                <span class="small fw-medium" style="color: var(--wm-text-dark);">{{ $r->anggota_label }}</span>
                            </td>
                            <td>
                                @if($r->status === 'Bertugas')
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2 py-1">
                                        <i data-lucide="radio" style="width: 12px; height: 12px; vertical-align: middle;"></i> Bertugas
                                    </span>
                                @elseif($r->status === 'Selesai')
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Selesai</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-2 py-1">Terjadwal</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($activeTab === 'lapor')
        {{-- ======================================================================
             TAB 2: LAPOR HAL MENCURIGAKAN (DATA IDENTITAS OTOMATIS TERDETEKSI)
             ====================================================================== --}}
        <div class="row g-4">
            <div class="col-12 col-lg-4">
                {{-- Panel Data Diri Pelapor Terdeteksi Otomatis --}}
                <div class="wm-card-box mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div class="p-2 rounded-circle" style="background: var(--wm-light-blue); color: var(--wm-primary);">
                            <i data-lucide="user-check" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="color: var(--wm-primary-dark);">Identitas Pelapor</h6>
                            <small class="text-success fw-semibold">
                                <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Terverifikasi Otomatis
                            </small>
                        </div>
                    </div>

                    <div class="mb-3 text-center p-3 rounded-3" style="background: #F8FAFC; border: 1px dashed var(--wm-border-card);">
                        @php
                            $userAvatar = $user->foto_profil 
                                ? asset('storage/' . $user->foto_profil) 
                                : ($user->profile_photo 
                                    ? asset('storage/' . $user->profile_photo) 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1456A0&color=FFFFFF&bold=true');
                        @endphp
                        <img src="{{ $userAvatar }}" alt="{{ $user->name }}" class="rounded-circle mb-2 border shadow-xs" style="width: 68px; height: 68px; object-fit: cover;">
                        <h6 class="fw-bold mb-0 text-dark">{{ $user->name }}</h6>
                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary border-opacity-25 mt-1">
                            {{ $user->isRt() ? 'Ketua RT ' . $user->clean_rt_label : ($user->isRw() ? 'Ketua RW 013' : 'Warga RT ' . $user->clean_rt_label) }}
                        </span>
                    </div>

                    <div class="list-group list-group-flush small">
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Wilayah Domisili:</span>
                            <strong class="text-dark">RT {{ $user->clean_rt_label }} / RW 013</strong>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Nomor Kontak / WA:</span>
                            <strong class="text-dark">{{ $user->phone ?: '-' }}</strong>
                        </div>
                        <div class="list-group-item px-0 py-2">
                            <span class="text-muted d-block mb-1">Alamat Terdaftar:</span>
                            <span class="text-dark fw-medium">{{ $user->address ?: 'Warga Sukamiskin Arcamanik' }}</span>
                        </div>
                    </div>

                    <div class="alert alert-light border mt-3 mb-0 small" style="background: #F8FAFC; font-size: 0.78rem; line-height: 1.45;">
                        <i data-lucide="info" style="width: 14px; height: 14px;" class="text-primary me-1"></i>
                        Identitas Anda otomatis terhubung dengan sistem pengurus RT &amp; Satgas Keamanan RW 013 agar laporan ditindaklanjuti secara akurat dan anti-spam.
                    </div>
                </div>

                {{-- Nomor Kontak Darurat Wilayah --}}
                <div class="wm-card-box">
                    <h6 class="fw-bold mb-3" style="color: var(--wm-primary-dark);">
                        <i data-lucide="phone-call" style="width: 18px; height: 18px;" class="text-danger me-1"></i>
                        Kontak Cepat Keamanan
                    </h6>
                    <div class="d-flex flex-column gap-2 small">
                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light border">
                            <div>
                                <strong class="d-block text-dark">Pos Keamanan Utama RW 013</strong>
                                <small class="text-muted">Siaga 24 Jam Linmas</small>
                            </div>
                            <span class="badge bg-primary rounded-pill px-2 py-1">Radio Ch-4</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light border">
                            <div>
                                <strong class="d-block text-dark">Call Center Darurat Bandung</strong>
                                <small class="text-muted">Polisi, Damkar, Medis</small>
                            </div>
                            <a href="tel:112" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 fw-bold">112</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="wm-card-box">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: var(--wm-primary-dark);">
                                <i data-lucide="alert-triangle" style="width: 22px; height: 22px;" class="text-warning me-2"></i>
                                Formulir Laporan Keamanan &amp; Ketertiban Kilat
                            </h5>
                            <small class="text-muted">Laporkan hal mencurigakan, dugaan tindak kriminal, atau fasilitas keamanan bermasalah secara langsung.</small>
                        </div>
                        <span class="badge rounded-pill fw-bold" style="background: var(--wm-light-blue); color: var(--wm-primary); border: 1px solid #C2DCFF;">
                            Respons Prioritas Pengurus
                        </span>
                    </div>

                    <form action="{{ route('ronda.lapor-keamanan') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            {{-- Jenis Kejadian --}}
                            <div class="col-12 col-md-6">
                                <label for="jenisKejadian" class="form-label fw-bold small text-dark">Jenis Kejadian / Temuan <span class="text-danger">*</span></label>
                                <select class="form-select @error('jenis_kejadian') is-invalid @enderror" id="jenisKejadian" name="jenis_kejadian" required>
                                    <option value="">-- Pilih Jenis Kejadian --</option>
                                    <option value="Aktivitas / Orang Mencurigakan" {{ old('jenis_kejadian') == 'Aktivitas / Orang Mencurigakan' ? 'selected' : '' }}>Aktivitas / Orang Mencurigakan</option>
                                    <option value="Kendaraan Asing Mencurigakan" {{ old('jenis_kejadian') == 'Kendaraan Asing Mencurigakan' ? 'selected' : '' }}>Kendaraan Asing Mencurigakan</option>
                                    <option value="Gangguan Kamtibmas / Keributan" {{ old('jenis_kejadian') == 'Gangguan Kamtibmas / Keributan' ? 'selected' : '' }}>Gangguan Kamtibmas / Keributan</option>
                                    <option value="Dugaan Pencurian / Kehilangan" {{ old('jenis_kejadian') == 'Dugaan Pencurian / Kehilangan' ? 'selected' : '' }}>Dugaan Pencurian / Kehilangan</option>
                                    <option value="Fasilitas Keamanan / Lampu Jalan Rusak" {{ old('jenis_kejadian') == 'Fasilitas Keamanan / Lampu Jalan Rusak' ? 'selected' : '' }}>Fasilitas Keamanan / Lampu Jalan Rusak</option>
                                    <option value="Lainnya" {{ old('jenis_kejadian') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('jenis_kejadian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tingkat Urgensi --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-dark d-block">Tingkat Urgensi <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <div class="form-check form-check-inline p-2 border rounded-3 flex-fill text-center m-0">
                                        <input class="form-check-input" type="radio" name="urgensi" id="urgensiNormal" value="Normal" {{ old('urgensi', 'Normal') == 'Normal' ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold text-secondary" for="urgensiNormal">
                                            Normal
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline p-2 border rounded-3 flex-fill text-center m-0">
                                        <input class="form-check-input" type="radio" name="urgensi" id="urgensiSegera" value="Segera" {{ old('urgensi') == 'Segera' ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold text-warning" for="urgensiSegera">
                                            Segera
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline p-2 border rounded-3 flex-fill text-center m-0">
                                        <input class="form-check-input" type="radio" name="urgensi" id="urgensiDarurat" value="Darurat" {{ old('urgensi') == 'Darurat' ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold text-danger" for="urgensiDarurat">
                                            Darurat
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Lokasi Kejadian --}}
                            <div class="col-12">
                                <label for="lokasiKejadian" class="form-label fw-bold small text-dark">Lokasi / Titik Kejadian <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i data-lucide="map-pin" style="width: 16px; height: 16px;"></i></span>
                                    <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasiKejadian" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Jl. Sukamiskin Raya No. 12 di depan portal RT 003" required maxlength="255">
                                </div>
                                @error('lokasi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Kronologi / Deskripsi --}}
                            <div class="col-12">
                                <label for="kronologiKejadian" class="form-label fw-bold small text-dark">Kronologi &amp; Rincian Kejadian <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('kronologi') is-invalid @enderror" id="kronologiKejadian" name="kronologi" rows="4" placeholder="Jelaskan apa yang terjadi, waktu perkiraan kejadian, ciri-ciri pelaku / orang asing, plat nomor kendaraan, atau situasi kondisi lingkungan saat ini secara jelas..." required minlength="10">{{ old('kronologi') }}</textarea>
                                <small class="text-muted">Minimal 10 karakter. Berikan informasi yang sejelas mungkin agar petugas ronda / satgas dapat segera bertindak.</small>
                                @error('kronologi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Lampiran Foto Bukti --}}
                            <div class="col-12">
                                <label for="fotoBukti" class="form-label fw-bold small text-dark">Lampiran Foto Bukti (Opsional)</label>
                                <input type="file" class="form-control @error('foto') is-invalid @enderror" id="fotoBukti" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp">
                                <small class="text-muted">Format gambar JPG, PNG, atau WEBP (Maksimal ukuran 3 MB).</small>
                                @error('foto')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="col-12 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <a href="{{ route('ronda.index', ['tab' => 'riwayat']) }}" class="btn btn-light rounded-pill px-3 fw-medium">
                                    Lihat Riwayat Laporan
                                </a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-xs" style="background: var(--wm-primary); border-color: var(--wm-primary);">
                                    <i data-lucide="send" style="width: 16px; height: 16px;"></i>
                                    <span>Kirim Laporan Keamanan Sekarang</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($activeTab === 'riwayat')
        {{-- ======================================================================
             BLOK PERINGATAN DINI KEAMANAN RW 013 (LINTAS RT)
             (Ditampilkan paling atas jika ada peringatan Darurat/Segera aktif)
             ====================================================================== --}}
        @if(isset($urgentWarnings) && $urgentWarnings->isNotEmpty())
            <div id="peringatan-dini" class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border: 1.5px solid #FCA5A5 !important; background: #FFF;">
                <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: linear-gradient(135deg, #FEF2F2 0%, #FFF5F5 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <span class="wm-pulse-dot danger"></span>
                        <div>
                            <h6 class="mb-0 fw-bold text-danger d-flex align-items-center gap-1.5">
                                <i data-lucide="shield-alert" style="width: 18px; height: 18px;"></i>
                                Peringatan Dini Keamanan RW 013 (Lintas RT Aktif)
                            </h6>
                            <small class="text-muted">
                                Laporan kejadian berstatus darurat &amp; segera dari seluruh wilayah RT 001–008 agar seluruh warga dan siskamling tetap waspada.
                            </small>
                        </div>
                    </div>
                    <div>
                        <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill fw-bold shadow-sm" style="font-size: 0.78rem;">
                            {{ $urgentWarnings->count() }} Peringatan Aktif
                        </span>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        @foreach($urgentWarnings as $w)
                            @php
                                $isDarurat = ($w->urgensi === 'Darurat');
                                $isMyReport = ($user->id === $w->user_id);
                                $isPengurusOrAdmin = in_array($user->role, ['rt', 'rw', 'admin', 'super_admin']) || $user->isRt() || $user->isRw();
                            @endphp
                            <div class="col-12 col-lg-6">
                                <div class="p-3 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: {{ $isDarurat ? '#FEF2F2' : '#FFFBEB' }}; border: 1px solid {{ $isDarurat ? '#FCA5A5' : '#FCD34D' }}; border-radius: 12px;">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2 gap-2 flex-wrap">
                                            <div class="d-flex align-items-center gap-1.5">
                                                @if($isDarurat)
                                                    <span class="badge bg-danger text-white px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                                        <i data-lucide="alert-octagon" style="width: 11px; height: 11px;"></i> Darurat
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                                        <i data-lucide="alert-triangle" style="width: 11px; height: 11px;"></i> Segera
                                                    </span>
                                                @endif
                                                <span class="badge bg-white text-dark border px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                                    RT {{ str_pad($w->rt_number, 3, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                                {{ $w->created_at ? $w->created_at->translatedFormat('d M Y, H:i') : '-' }} WIB
                                            </small>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $w->jenis_kejadian }}</h6>
                                        <div class="small fw-semibold text-danger mb-2">
                                            <i data-lucide="map-pin" style="width: 12px; height: 12px;"></i>
                                            {{ $w->lokasi }}
                                        </div>
                                        <p class="small text-secondary mb-2" style="line-height: 1.45; white-space: pre-line;">
                                            {{ $w->kronologi_clean }}
                                        </p>

                                        {{-- Jejak Pengecekan Lapangan RT --}}
                                        @if($w->rt_checked_at)
                                            <div class="mt-2 p-2.5 rounded-3" style="background: #EAF3FF; border: 1px solid #B8D5FA; font-size: 0.75rem;">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                                    <span class="badge bg-primary text-white px-2 py-0.5 fw-bold" style="font-size: 0.68rem;">
                                                        <i data-lucide="check-check" style="width: 11px; height: 11px; display: inline-block;"></i> Sudah Dicek Ketua RT {{ $w->rt_number }}
                                                    </span>
                                                    <small class="text-muted" style="font-size: 0.7rem;">
                                                        {{ $w->rt_checked_at->translatedFormat('d M Y, H:i') }} WIB ({{ $w->rt_checked_at->diffForHumans() }})
                                                    </small>
                                                </div>
                                                <div class="text-dark fw-medium" style="line-height: 1.35;">
                                                    <em>"{{ $w->rt_check_note }}"</em>
                                                </div>
                                                <div class="text-muted mt-1" style="font-size: 0.7rem;">
                                                    Status: Menunggu Konfirmasi Kondusif Resmi dari Ketua RW 013
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="pt-2 border-top border-secondary border-opacity-10 d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size: 0.75rem;">
                                        <div>
                                            <span class="text-muted me-1">Pelapor:</span>
                                            @if($isPengurusOrAdmin)
                                                <strong class="text-dark">{{ $w->pelapor_nama }}</strong>
                                                @if($w->pelapor_phone && $w->pelapor_phone !== '-')
                                                    <span class="text-muted">({{ $w->pelapor_phone }})</span>
                                                @endif
                                            @elseif($isMyReport)
                                                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 fw-bold">Laporan Anda Sendiri</span>
                                            @else
                                                <span class="badge bg-light text-muted border px-2 py-0.5"><i data-lucide="shield" style="width: 10px; height: 10px;"></i> Warga Terdaftar (Disamarkan)</span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 px-2 py-0.5">
                                                Status: {{ $w->status ?: 'Diproses' }}
                                            </span>
                                            @if($w->foto)
                                                @php
                                                    $imgUrl = str_starts_with($w->foto, 'http') ? $w->foto : asset('storage/' . $w->foto);
                                                @endphp
                                                <a href="{{ $imgUrl }}" target="_blank" class="btn btn-xs btn-outline-danger py-0.5 px-2 rounded" style="font-size: 0.7rem;">
                                                    <i data-lucide="image" style="width: 10px; height: 10px;"></i> Foto
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    @php
                                        $userCleanRt = str_pad(intval(preg_replace('/\D/', '', $user->clean_rt_number ?? '')), 3, '0', STR_PAD_LEFT);
                                        $warningRt = str_pad(intval(preg_replace('/\D/', '', $w->rt_number ?? '')), 3, '0', STR_PAD_LEFT);
                                        $canRtConfirm = ($user->isRt() || $user->role === 'rt') && ($userCleanRt === $warningRt) && !$w->rt_checked_at;
                                        $canRwResolve = $user->isRw() || in_array($user->role, ['rw', 'admin', 'super_admin']);
                                    @endphp

                                    @if($canRtConfirm || $canRwResolve)
                                        <div class="mt-2 pt-2 border-top border-secondary border-opacity-10 d-flex gap-2 flex-wrap justify-content-end">
                                            @if($canRtConfirm)
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3 d-inline-flex align-items-center gap-1 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalKonfirmasiRt{{ $w->id }}" style="font-size: 0.75rem;">
                                                    <i data-lucide="clipboard-check" style="width: 14px; height: 14px;"></i>
                                                    <span>Konfirmasi Sudah Dicek &amp; Aman (Lapor ke RW)</span>
                                                </button>
                                            @endif
                                            @if($canRwResolve)
                                                <button type="button" class="btn btn-sm btn-success text-white rounded-pill fw-bold px-3 d-inline-flex align-items-center gap-1 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalUmumkanRw{{ $w->id }}" style="background: #16865D !important; border-color: #16865D !important; font-size: 0.75rem;">
                                                    <i data-lucide="shield-check" style="width: 14px; height: 14px;"></i>
                                                    <span>Cabut Peringatan &amp; Umumkan Kondusif</span>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Modal Konfirmasi RT --}}
                            @if($canRtConfirm)
                                <div class="modal fade" id="modalKonfirmasiRt{{ $w->id }}" tabindex="-1" aria-labelledby="modalKonfirmasiRtLabel{{ $w->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                                            <div class="modal-header border-bottom py-3 px-4" style="background: #F4F7FB;">
                                                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalKonfirmasiRtLabel{{ $w->id }}">
                                                    <i data-lucide="clipboard-check" class="text-primary" style="width: 20px; height: 20px;"></i>
                                                    Konfirmasi Pengecekan Lapangan (RT {{ $w->rt_number }})
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <form method="POST" action="{{ route('ronda.peringatan.konfirmasi-rt', $w->id) }}">
                                                @csrf
                                                <div class="modal-body p-4">
                                                    <div class="alert alert-primary py-2 px-3 small rounded-3 mb-3">
                                                        <strong>Kejadian:</strong> {{ $w->jenis_kejadian }} di {{ $w->lokasi }} (RT {{ $w->rt_number }})
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold text-dark small">Catatan Hasil Pengecekan Lapangan <span class="text-danger">*</span></label>
                                                        <textarea name="catatan" rows="3" class="form-control" placeholder="Contoh: Sudah dicek bersama petugas pos ronda RT {{ $w->rt_number }}, situasi di lokasi aman terkendali dan nihil gangguan lanjutan." required minlength="5"></textarea>
                                                        <div class="form-text small text-muted">Catatan ini akan langsung diteruskan ke Ketua RW untuk konfirmasi pencabutan peringatan.</div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top py-2.5 px-4 bg-light">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                                                        Kirim Konfirmasi Aman ke RW
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Modal Umumkan Kondusif RW --}}
                            @if($canRwResolve)
                                <div class="modal fade" id="modalUmumkanRw{{ $w->id }}" tabindex="-1" aria-labelledby="modalUmumkanRwLabel{{ $w->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                                            <div class="modal-header border-bottom py-3 px-4" style="background: #E8F6EF;">
                                                <h6 class="modal-title fw-bold text-success d-flex align-items-center gap-2" id="modalUmumkanRwLabel{{ $w->id }}">
                                                    <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                                                    Cabut Peringatan &amp; Deklarasi Aman (RW 013)
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <form method="POST" action="{{ route('ronda.peringatan.umumkan-kondusif', $w->id) }}">
                                                @csrf
                                                <div class="modal-body p-4">
                                                    @if($w->rt_checked_at)
                                                        <div class="p-2.5 rounded-3 mb-3" style="background: #EAF3FF; border: 1px solid #B8D5FA; font-size: 0.8rem;">
                                                            <strong class="text-primary d-block mb-1">
                                                                <i data-lucide="check-circle" style="width: 14px; height: 14px; display: inline-block;"></i>
                                                                Ketua RT {{ $w->rt_number }} Telah Mengonfirmasi Aman:
                                                            </strong>
                                                            <div class="text-dark"><em>"{{ $w->rt_check_note }}"</em></div>
                                                            <small class="text-muted d-block mt-1">Diverifikasi {{ $w->rt_checked_at->diffForHumans() }}</small>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-warning py-2 px-3 small rounded-3 mb-3">
                                                            <i data-lucide="info" style="width: 14px; height: 14px; display: inline-block;"></i>
                                                            Ketua RT {{ $w->rt_number }} belum mengirim konfirmasi cek lapangan, namun Ketua RW/Admin dapat langsung menyatakan situasi kondusif.
                                                        </div>
                                                    @endif

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold text-dark small">Pesan Resmi Kondisi Kondusif ke Warga <span class="text-danger">*</span></label>
                                                        <textarea name="pesan_kondusif" rows="3" class="form-control" required minlength="5">{{ $w->rt_check_note ? "Situasi keamanan di RT {$w->rt_number} telah diverifikasi bersama Ketua RT: {$w->rt_check_note}. Lingkungan dinyatakan aman dan kondusif." : "Situasi keamanan di RT {$w->rt_number} telah diverifikasi bersama siskamling lingkungan. Kondisi saat ini telah kondusif dan terkendali." }}</textarea>
                                                        <div class="form-text small text-muted">Pesan ini akan ditampilkan pada Banner Hijau Kondusif untuk seluruh warga RW 013.</div>
                                                    </div>

                                                    <div class="form-check p-2.5 rounded-3 bg-light border">
                                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="terbitkan_pengumuman" value="1" id="checkPengumuman{{ $w->id }}" checked>
                                                        <label class="form-check-label small fw-bold text-dark" for="checkPengumuman{{ $w->id }}">
                                                            Sekalian terbitkan pemberitahuan ke Pengumuman RW
                                                        </label>
                                                        <div class="small text-muted ps-4" style="font-size: 0.75rem;">
                                                            Otomatis membuat pengumuman resmi berkategori Keamanan agar terbaca oleh warga yang mengunjungi portal Pengumuman (tanpa nama/HP pelapor).
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top py-2.5 px-4 bg-light">
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-success text-white rounded-pill px-4 fw-bold" style="background: #16865D !important; border-color: #16865D !important;">
                                                        Cabut Peringatan &amp; Umumkan Kondusif
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- ======================================================================
             TAB 3: RIWAYAT LAPORAN KEAMANAN & SISKAMLING
             ====================================================================== --}}
        <div class="wm-card-box">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0" style="color: var(--wm-primary-dark);">
                        <i data-lucide="shield-check" class="me-2 text-primary" style="width: 22px; height: 22px; vertical-align: middle;"></i>
                        Riwayat Laporan Keamanan &amp; Siskamling
                    </h5>
                    <small class="text-muted">
                        @if($user->isRt())
                            Menampilkan seluruh laporan keamanan warga di lingkungan <strong>RT {{ $user->clean_rt_label }}</strong>.
                        @elseif($user->isRw() || in_array($user->role, ['super_admin', 'admin']))
                            Menampilkan seluruh arsip laporan keamanan di wilayah <strong>RW 013 Sukamiskin</strong>.
                        @else
                            Daftar laporan hal mencurigakan atau pengaduan keamanan yang Anda kirimkan.
                        @endif
                    </small>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill">
                        Total: {{ $laporanKeamananList->count() }} Laporan
                    </span>
                    <a href="{{ route('ronda.index', ['tab' => 'lapor']) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1" style="background: var(--wm-primary); border-color: var(--wm-primary);">
                        <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                        <span>Lapor Kejadian Baru</span>
                    </a>
                </div>
            </div>

            @if($laporanKeamananList->isEmpty())
                <div class="text-center py-5 text-muted">
                    <div class="p-3 bg-light rounded-circle d-inline-flex mb-3">
                        <i data-lucide="shield" style="width: 38px; height: 38px;" class="text-muted"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Catatan Laporan Keamanan</h6>
                    <p class="small text-muted mb-3" style="max-width: 420px; margin: 0 auto;">
                        @if(!$isPengurus)
                            Jika Anda melihat orang/kendaraan mencurigakan atau gangguan keamanan di lingkungan, segera laporkan melalui sistem siskamling.
                        @else
                            Situasi lingkungan aman dan kondusif. Belum ada insiden keamanan yang dilaporkan.
                        @endif
                    </p>
                    <a href="{{ route('ronda.index', ['tab' => 'lapor']) }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                        Buat Laporan Keamanan Sekarang
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-ronda align-middle">
                        <thead>
                            <tr>
                                <th style="min-width: 140px;">Waktu &amp; Pelapor</th>
                                <th style="min-width: 160px;">Jenis &amp; Urgensi</th>
                                <th style="min-width: 180px;">Lokasi &amp; Foto</th>
                                <th style="min-width: 250px;">Kronologi Kejadian</th>
                                <th style="min-width: 130px;">Status</th>
                                <th style="min-width: 180px;">Tindak Lanjut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporanKeamananList as $lap)
                                @php
                                    $desk = $lap->deskripsi;
                                    $urgensiMatch = [];
                                    preg_match('/\[Tingkat Urgensi:\s*([^\]]+)\]/', $desk, $urgensiMatch);
                                    $urgensiVal = $urgensiMatch[1] ?? 'Normal';

                                    $jenisMatch = [];
                                    preg_match('/\[Jenis Kejadian:\s*([^\]]+)\]/', $desk, $jenisMatch);
                                    $jenisVal = $jenisMatch[1] ?? 'Keamanan';

                                    // Bersihkan prefix tag untuk membaca kronologi murni
                                    $kronologiClean = preg_replace('/\[Jenis Kejadian:[^\]]+\]\s*\[Tingkat Urgensi:[^\]]+\]\s*Kronologi Kejadian:\s*/', '', $desk);
                                    if (!$kronologiClean) {
                                        $kronologiClean = $desk;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong class="d-block text-dark">{{ $lap->created_at ? $lap->created_at->translatedFormat('d M Y') : '-' }}</strong>
                                        <small class="text-muted d-block">{{ $lap->created_at ? $lap->created_at->format('H:i') . ' WIB' : '-' }}</small>
                                        <div class="mt-1">
                                            @if(!$isPengurus && $user->id === $lap->user_id)
                                                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25" style="font-size: 0.72rem;">
                                                    Anda Sendiri
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark border" style="font-size: 0.72rem;">
                                                    {{ $lap->pelapor_nama }}
                                                </span>
                                            @endif
                                            <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25" style="font-size: 0.7rem;">
                                                RT {{ str_pad($lap->rt_number, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="d-block small text-dark">{{ $jenisVal }}</strong>
                                        <div class="mt-1">
                                            @if($urgensiVal === 'Darurat')
                                                <span class="badge bg-danger text-white px-2 py-1">
                                                    <i data-lucide="alert-octagon" style="width: 11px; height: 11px;"></i> Darurat
                                                </span>
                                            @elseif($urgensiVal === 'Segera')
                                                <span class="badge bg-warning text-dark px-2 py-1">
                                                    <i data-lucide="alert-triangle" style="width: 11px; height: 11px;"></i> Segera
                                                </span>
                                            @else
                                                <span class="badge bg-secondary text-white px-2 py-1">
                                                    Normal
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-medium text-dark mb-1">
                                            <i data-lucide="map-pin" style="width: 12px; height: 12px;" class="text-danger me-1"></i>
                                            {{ $lap->lokasi }}
                                        </div>
                                        @if($lap->foto)
                                            @php
                                                $imgUrl = str_starts_with($lap->foto, 'http') ? $lap->foto : asset('storage/' . $lap->foto);
                                            @endphp
                                            <a href="{{ $imgUrl }}" target="_blank" class="d-inline-flex align-items-center gap-1 btn btn-xs btn-outline-primary py-1 px-2 rounded mt-1" style="font-size: 0.72rem;">
                                                <i data-lucide="image" style="width: 11px; height: 11px;"></i>
                                                <span>Lihat Foto Bukti</span>
                                            </a>
                                        @else
                                            <span class="text-muted small" style="font-size: 0.72rem;">Tanpa lampiran foto</span>
                                        @endif
                                    </td>
                                    <td>
                                        <p class="mb-0 small text-dark" style="line-height: 1.45; white-space: pre-line;">{{ Str::limit($kronologiClean, 140) }}</p>
                                    </td>
                                    <td>
                                        @if($lap->status === 'Selesai')
                                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2 py-1">
                                                <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Selesai
                                            </span>
                                        @elseif($lap->status === 'Ditolak')
                                            <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 px-2 py-1">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 px-2 py-1">
                                                <i data-lucide="clock" style="width: 12px; height: 12px;"></i> {{ $lap->status ?: 'Diproses' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                            {{ $lap->catatan_tindak_lanjut ?: 'Dalam penanganan pengurus RT & Satgas RW.' }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @elseif($activeTab === 'portal')
        @include('ronda.tabs.portal')
    @endif
</div>

{{-- ======================================================================
     MODAL AJUKAN PERMOHONAN IZIN AKSES CCTV (UNTUK WARGA)
     ====================================================================== --}}
<div class="modal fade" id="modalAjukanCctv" tabindex="-1" aria-labelledby="modalAjukanCctvLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-circle" style="background: var(--wm-light-blue); color: var(--wm-primary);">
                        <i data-lucide="shield-alert" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold" id="modalAjukanCctvLabel" style="color: var(--wm-text-dark);">
                            Permohonan Izin Akses CCTV
                        </h5>
                        <small class="text-muted">Verifikasi berjenjang oleh Ketua RT &amp; Ketua RW 013</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ronda.cctv-request.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 small mb-3" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="info" style="width: 14px; height: 14px;" class="me-1"></i>
                        Demi menjaga privasi warga dan keamanan teritorial, tayangan CCTV diberikan secara selektif untuk keperluan yang sah dan dapat dipertanggungjawabkan.
                    </div>

                    {{-- Panel Identitas Pemohon (Terdeteksi Otomatis) --}}
                    <div class="p-3 mb-3 rounded-3 border" style="background: #F8FAFC; border-color: #E2E8F0;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-uppercase" style="color: var(--wm-primary); letter-spacing: 0.5px; font-size: 0.72rem;">
                                <i data-lucide="user-check" style="width: 13px; height: 13px;" class="me-1"></i>
                                Identitas Pemohon (Terdeteksi Otomatis)
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25" style="font-size: 0.68rem;">
                                Akun Terverifikasi
                            </span>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Nama Lengkap:</span>
                                <strong class="text-dark">{{ $user->name }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Wilayah Domisili:</span>
                                <strong class="text-dark">RT {{ $user->clean_rt_label }} / RW 013</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Nomor Telepon:</span>
                                <strong class="text-dark">{{ $user->phone ?: '-' }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Alamat:</span>
                                <span class="text-dark text-truncate d-block" title="{{ $user->address }}">{{ $user->address ?: 'Warga Terdaftar RW 013' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="selectCctvId" class="form-label fw-bold small text-dark">Titik Kamera CCTV yang Diminta <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectCctvId" name="cctv_id" required>
                            <option value="">-- Pilih Titik CCTV --</option>
                            @foreach($cctvs as $c)
                                <option value="{{ $c->id }}" {{ request('request_cctv') == $c->id ? 'selected' : '' }}>
                                    {{ $c->nama }} — {{ $c->lokasi }} (RT {{ $c->clean_rt }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="textareaAlasan" class="form-label fw-bold small text-dark">Alasan / Kepentingan Akses <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="textareaAlasan" name="alasan" rows="3" placeholder="Contoh: Kehilangan barang di sekitar portal depan, pengecekan kendaraan mencurigakan, dll." required minlength="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: var(--wm-primary); border-color: var(--wm-primary);">
                        Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================================================================
     MODAL TOLAK PERMOHONAN IZIN AKSES CCTV (UNTUK PENGURUS)
     ====================================================================== --}}
<div class="modal fade" id="modalTolakCctv" tabindex="-1" aria-labelledby="modalTolakCctvLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-circle bg-danger-subtle text-danger">
                        <i data-lucide="x-circle" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-danger" id="modalTolakCctvLabel">
                            Tolak Permohonan Akses CCTV
                        </h5>
                        <small class="text-muted" id="rejectUserSubtitle">Permohonan Warga</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRejectCctv" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Berikan alasan penolakan agar pemohon memahami pertimbangan pengurus demi ketertiban dan SOP privasi lingkungan.
                    </p>
                    <div class="mb-3">
                        <label for="catatanPengurus" class="form-label fw-bold small text-dark">Catatan Penolakan (Opsional)</label>
                        <textarea class="form-control" id="catatanPengurus" name="catatan_pengurus" rows="3" placeholder="Contoh: Titik kamera tidak mencakup area yang dimaksud atau permohonan kurang spesifik."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        Konfirmasi Tolak
                    </button>
                </div>
            </form>
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

        // Live clock for CCTV viewports
        function updateCctvClocks() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeStr = `${hours}:${minutes}:${seconds}`;

            document.querySelectorAll('.cctv-clock').forEach(el => {
                el.textContent = timeStr;
            });
        }
        setInterval(updateCctvClocks, 1000);

        // Handle Quick Request Buttons from Card Grid
        document.querySelectorAll('.btn-open-request-modal').forEach(btn => {
            btn.addEventListener('click', function () {
                const cctvId = this.getAttribute('data-cctv-id');
                const selectEl = document.getElementById('selectCctvId');
                if (selectEl && cctvId) {
                    selectEl.value = cctvId;
                }
                const modal = new bootstrap.Modal(document.getElementById('modalAjukanCctv'));
                modal.show();
            });
        });

        // Handle Reject Modal dynamic action
        document.querySelectorAll('.btn-open-reject-modal').forEach(btn => {
            btn.addEventListener('click', function () {
                const actionUrl = this.getAttribute('data-action');
                const userName = this.getAttribute('data-user-name');
                const form = document.getElementById('formRejectCctv');
                const subTitle = document.getElementById('rejectUserSubtitle');

                if (form && actionUrl) {
                    form.action = actionUrl;
                }
                if (subTitle && userName) {
                    subTitle.textContent = 'Pemohon: ' + userName;
                }
                const modal = new bootstrap.Modal(document.getElementById('modalTolakCctv'));
                modal.show();
            });
        });

        // Auto open request modal if request_cctv parameter exists in URL
        const urlParams = new URLSearchParams(window.location.search);
        const reqCctvId = urlParams.get('request_cctv');
        if (reqCctvId) {
            const selectEl = document.getElementById('selectCctvId');
            if (selectEl) {
                selectEl.value = reqCctvId;
                const modal = new bootstrap.Modal(document.getElementById('modalAjukanCctv'));
                modal.show();
            }
        }

        // Real-Time Polling for IoT Portal Gerbang (2s)
        @if($activeTab === 'portal')
        (function() {
            var lastSeenTapId = {{ $portalLogs->first()?->id ?? 0 }};
            var boomCloseTimer = null;

            function updateLiveStatus() {
                fetch('{{ route('api.iot.portal.live_status') }}')
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (!data) return;

                        // 1. Update Device Online Status
                        var dev = data.device;
                        var badge = document.getElementById('deviceOnlineBadge');
                        var badgeText = document.getElementById('deviceOnlineText');
                        var heartbeatText = document.getElementById('deviceHeartbeatText');

                        if (badge && badgeText) {
                            if (dev.is_online) {
                                badge.className = 'portal-live-badge bg-success text-white';
                                badgeText.textContent = 'Perangkat Online';
                            } else {
                                badge.className = 'portal-live-badge bg-secondary text-white';
                                badgeText.textContent = 'Perangkat Offline';
                            }
                        }

                        if (heartbeatText) {
                            heartbeatText.textContent = 'Terakhir aktif: ' + dev.last_seen_human + ' • Lokasi: Pos Keamanan Gerbang Utama RW 013';
                        }

                        // 2. Check Last Tap Animation
                        var tap = data.last_tap;
                        if (tap && tap.id !== lastSeenTapId) {
                            lastSeenTapId = tap.id;
                            handleNewTapEvent(tap);
                        }
                    })
                    .catch(function(err) {});
            }

            function handleNewTapEvent(tap) {
                var barrierArm = document.getElementById('visualBarrierArm');
                var lamp = document.getElementById('visualLamp');
                var statusCard = document.getElementById('visualStatusCard');
                var statusIcon = document.getElementById('visualStatusIcon');
                var statusTitle = document.getElementById('visualStatusTitle');
                var statusDesc = document.getElementById('visualStatusDesc');
                var extraAction = document.getElementById('visualExtraAction');

                if (!statusCard) return;

                if (tap.status === 'granted') {
                    if (tap.mode === 'portal') {
                        if (barrierArm) barrierArm.classList.add('open');
                        if (lamp) lamp.classList.add('open');
                        statusCard.style.background = 'rgba(22, 134, 93, 0.2)';
                        statusCard.style.borderColor = '#16865D';
                        statusIcon.style.background = 'rgba(22, 134, 93, 0.3)';
                        statusIcon.style.color = '#34D399';
                        statusIcon.innerHTML = '<i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>';
                        statusTitle.innerHTML = '<span class="text-success">SELAMAT DATANG: ' + (tap.user ? tap.user.name : 'Warga') + ' (' + (tap.user ? tap.user.rt : '') + ')</span>';
                        statusDesc.textContent = 'Akses Diterima — Portal Palang Terbuka Otomatis.';
                        if (extraAction) extraAction.style.display = 'none';

                        clearTimeout(boomCloseTimer);
                        boomCloseTimer = setTimeout(function() {
                            if (barrierArm) barrierArm.classList.remove('open');
                            if (lamp) lamp.classList.remove('open');
                            statusCard.style.background = 'rgba(15, 23, 42, 0.6)';
                            statusCard.style.borderColor = '#334155';
                            statusIcon.style.background = 'rgba(59, 130, 246, 0.15)';
                            statusIcon.style.color = '#60A5FA';
                            statusIcon.innerHTML = '<i data-lucide="radio" style="width: 20px; height: 20px;"></i>';
                            statusTitle.textContent = 'Siaga Menunggu Tap Kartu RFID...';
                            statusDesc.textContent = 'Dekatkan kartu RFID warga pada sensor RFID-RC522 di pos gerbang.';
                            if (typeof lucide !== 'undefined') lucide.createIcons();
                        }, 5000);
                    } else if (tap.mode === 'ronda') {
                        statusCard.style.background = 'rgba(124, 58, 237, 0.2)';
                        statusCard.style.borderColor = '#7C3AED';
                        statusIcon.style.background = 'rgba(124, 58, 237, 0.3)';
                        statusIcon.style.color = '#A78BFA';
                        statusIcon.innerHTML = '<i data-lucide="moon" style="width: 20px; height: 20px;"></i>';
                        statusTitle.innerHTML = '<span style="color: #A78BFA;">ABSENSI RONDA TERCATAT: ' + (tap.user ? tap.user.name : 'Warga') + ' (' + (tap.user ? tap.user.rt : '') + ')</span>';
                        statusDesc.textContent = 'Kehadiran petugas ronda malam tercatat di buku kontrol keamanan.';
                        if (extraAction) extraAction.style.display = 'none';
                    } else if (tap.mode === 'bank_sampah') {
                        var saldoText = tap.bank_sampah ? tap.bank_sampah.saldo_formatted : 'Rp 0';
                        statusCard.style.background = 'rgba(22, 134, 93, 0.2)';
                        statusCard.style.borderColor = '#16865D';
                        statusIcon.style.background = 'rgba(22, 134, 93, 0.3)';
                        statusIcon.style.color = '#34D399';
                        statusIcon.innerHTML = '<i data-lucide="recycle" style="width: 20px; height: 20px;"></i>';
                        statusTitle.innerHTML = '<span class="text-success">NASABAH TERIDENTIFIKASI: ' + (tap.user ? tap.user.name : 'Warga') + ' (' + (tap.user ? tap.user.rt : '') + ')</span>';
                        statusDesc.textContent = 'Saldo Tabungan Sampah: ' + saldoText;
                        if (extraAction) {
                            extraAction.style.display = 'block';
                            extraAction.innerHTML = '<a href="{{ route('bank-sampah') }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">Lanjut Input Setoran Sampah &rarr;</a>';
                        }
                    } else if (tap.mode === 'register') {
                        statusCard.style.background = 'rgba(217, 119, 6, 0.2)';
                        statusCard.style.borderColor = '#D97706';
                        statusIcon.style.background = 'rgba(217, 119, 6, 0.3)';
                        statusIcon.style.color = '#FBBF24';
                        statusIcon.innerHTML = '<i data-lucide="check" style="width: 20px; height: 20px;"></i>';
                        statusTitle.innerHTML = '<span style="color: #FBBF24;">KARTU BERHASIL DIDAFTARKAN: ' + (tap.user ? tap.user.name : '') + '</span>';
                        statusDesc.textContent = 'UID ' + tap.rfid_uid + ' berhasil dipasangkan ke akun warga.';
                        if (extraAction) extraAction.style.display = 'none';
                    }
                } else {
                    statusCard.style.background = 'rgba(239, 68, 68, 0.2)';
                    statusCard.style.borderColor = '#EF4444';
                    statusIcon.style.background = 'rgba(239, 68, 68, 0.3)';
                    statusIcon.style.color = '#F87171';
                    statusIcon.innerHTML = '<i data-lucide="alert-triangle" style="width: 20px; height: 20px;"></i>';
                    statusTitle.innerHTML = '<span class="text-danger">AKSES DITOLAK: Kartu Asing / Tidak Terdaftar</span>';
                    statusDesc.textContent = 'UID: ' + tap.rfid_uid + ' — ' + tap.reason;
                    if (extraAction) extraAction.style.display = 'none';
                }

                if (typeof lucide !== 'undefined') lucide.createIcons();

                if (tap.mode === 'register') {
                    setTimeout(function() { location.reload(); }, 2000);
                }
            }

            setInterval(updateLiveStatus, 2000);
        })();
        @endif

        // Handler Ambil Lokasi GPS untuk Super Admin
        var btnGetLoc = document.getElementById('btnGetAdminLocation');
        if (btnGetLoc) {
            btnGetLoc.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Browser Anda tidak mendukung Geolocation GPS.');
                    return;
                }
                var origText = btnGetLoc.innerHTML;
                btnGetLoc.disabled = true;
                btnGetLoc.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengunci GPS HP...';

                navigator.geolocation.getCurrentPosition(function(pos) {
                    var latInput = document.getElementById('inputGateLat');
                    var lngInput = document.getElementById('inputGateLng');
                    if (latInput) latInput.value = pos.coords.latitude.toFixed(7);
                    if (lngInput) lngInput.value = pos.coords.longitude.toFixed(7);
                    btnGetLoc.disabled = false;
                    btnGetLoc.innerHTML = '<i data-lucide="check" style="width: 14px; height: 14px; vertical-align: -2px;"></i> Koordinat Terkunci!';
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }, function(err) {
                    btnGetLoc.disabled = false;
                    btnGetLoc.innerHTML = origText;
                    alert('Gagal mengambil koordinat GPS: ' + err.message + '. Pastikan izin akses lokasi telah aktif.');
                }, { enableHighAccuracy: true, timeout: 10000 });
            });
        }
    });
</script>
@endpush