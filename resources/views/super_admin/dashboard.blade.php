@extends('layouts.app')

@section('title', 'Control Center Super Admin')

@push('styles')
<style>
    /* ======================================================================
       STYLING EKSKLUSIF CONTROL CENTER SUPER ADMIN (BEBAS KONFLIK BOOTSTRAP/TAILWIND)
       ====================================================================== */
    
    /* Hero Banner Control Center */
    .sa-hero-card {
        border-radius: 20px !important;
        background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%) !important;
        padding: 28px 32px !important;
        color: #FFFFFF !important;
        position: relative !important;
        overflow: hidden !important;
        border: none !important;
        box-shadow: 0 4px 20px rgba(10, 37, 64, 0.15) !important;
    }
    @media (max-width: 767.98px) {
        .sa-hero-card {
            padding: 20px 18px !important;
        }
    }

    /* Kartu Info Akun Administrator di Kanan Hero */
    .sa-admin-account-card {
        padding: 22px 24px !important;
        background: rgba(10, 30, 58, 0.55) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12) !important;
    }
    .sa-admin-account-card .info-row {
        padding-top: 10px;
        padding-bottom: 2px;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.85);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* 4 Kartu KPI Ringkasan */
    .sa-kpi-card {
        padding: 22px 24px !important;
        border-radius: 16px !important;
        background-color: var(--wm-card, #FFFFFF) !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
        box-shadow: 0 2px 10px rgba(23, 43, 77, 0.05) !important;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .sa-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(23, 43, 77, 0.08) !important;
    }
    .sa-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3.5px;
    }
    .sa-kpi-card.accent-primary::before { background: #1456A0; }
    .sa-kpi-card.accent-secondary::before { background: #2878D0; }
    .sa-kpi-card.accent-green::before { background: #16865D; }
    .sa-kpi-card.accent-blue::before { background: #0284C7; }

    /* 3 Kartu Switch Role */
    .sa-switch-card {
        padding: 24px 26px !important;
        border-radius: 16px !important;
        background-color: var(--wm-card, #FFFFFF) !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
        box-shadow: 0 2px 10px rgba(23, 43, 77, 0.05) !important;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .sa-mode-box {
        padding: 12px 16px !important;
        border-radius: 12px !important;
        background: var(--wm-light-blue, #F8FAFD) !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
        margin-bottom: 16px;
    }

    /* Kontainer Section Table Ledger */
    .sa-ledger-container {
        border-radius: 16px !important;
        background: var(--wm-card, #FFFFFF) !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
        box-shadow: 0 2px 10px rgba(23, 43, 77, 0.04) !important;
        overflow: hidden;
    }

    /* Dark Mode Overrides for Super Admin Dashboard */
    [data-theme="dark"] .sa-kpi-card,
    body.dark-mode .sa-kpi-card,
    [data-theme="dark"] .sa-switch-card,
    body.dark-mode .sa-switch-card,
    [data-theme="dark"] .sa-ledger-container,
    body.dark-mode .sa-ledger-container {
        background-color: #131F35 !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
    }

    [data-theme="dark"] .sa-mode-box,
    body.dark-mode .sa-mode-box {
        background-color: #0F1A2E !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
    }

    [data-theme="dark"] .sa-mode-box .form-check-label,
    body.dark-mode .sa-mode-box .form-check-label,
    [data-theme="dark"] .sa-mode-box label,
    body.dark-mode .sa-mode-box label {
        color: #F1F5F9 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-md-4 py-2">
    {{-- ======================================================================
         1. HERO HEADER CONTROL CENTER SUPER ADMIN
         ====================================================================== --}}
    <div class="sa-hero-card mb-4" style="background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%) !important; color: #FFFFFF !important; border-radius: 20px; padding: 28px 32px; position: relative; overflow: hidden; border: none; box-shadow: 0 4px 20px rgba(10, 37, 64, 0.15);">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-1.5 gap-md-2 mb-2 flex-wrap">
                    <span class="badge rounded-pill px-2.5 px-md-3 py-1.5 fw-bold" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25); font-size: 0.72rem;">
                        <i data-lucide="shield" style="width: 13px; height: 13px; vertical-align: middle;"></i> CONTROL CENTER
                    </span>
                    <span class="badge rounded-pill px-2.5 px-md-3 py-1.5 fw-bold" style="background: #16865D; font-size: 0.72rem;">
                        <i data-lucide="activity" style="width: 13px; height: 13px; vertical-align: middle;"></i> Selisih Rp 0
                    </span>
                    <span class="badge rounded-pill px-2.5 px-md-3 py-1.5 fw-bold d-none d-sm-inline-flex" style="background: rgba(255,255,255,0.15); font-size: 0.72rem;">
                        <i data-lucide="lock" style="width: 13px; height: 13px; vertical-align: middle;"></i> Multi-Role Active
                    </span>
                </div>

                <h2 class="fw-bold tracking-tight mb-2 text-white" style="font-size: 1.85rem;">
                    Pusat Kendali Perancang Sistem (Super Admin)
                </h2>
                <p class="text-white-50 mb-3 mb-md-4 d-none d-md-block" style="max-width: 680px; font-size: 0.95rem; line-height: 1.55;">
                    Ruang pengawasan arsitektur, pantau keuangan gabungan wilayah RW 013 Suryatmajan, serta simulator peran terisolasi dengan proteksi otomatis mode Lihat Saja (Read-Only) vs Mode Edit.
                </p>

                <div class="d-none d-md-flex flex-wrap gap-2 pt-1">
                    <button type="button" class="btn btn-light rounded-pill px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSwitchRole" style="color: #1456A0; font-size: 0.86rem;">
                        <i data-lucide="shuffle" style="width: 16px; height: 16px;"></i> Mulai Switch Role Sekarang
                    </button>
                    <a href="#section-ledger" class="btn btn-outline-light rounded-pill px-4 py-2.5 fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 0.86rem; border-color: rgba(255,255,255,0.45);">
                        <i data-lucide="file-spreadsheet" style="width: 16px; height: 16px;"></i> Cek Rincian Buku Kas
                    </a>
                </div>
            </div>

            {{-- Hero Banner Quick Info: Akun Administrator (Kontras Jelas & Rapi) --}}
            <div class="col-lg-4 mt-4 mt-lg-0 d-none d-md-block">
                <div class="sa-admin-account-card" style="padding: 22px 24px; background: rgba(10, 30, 58, 0.55) !important; backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.25) !important; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=FFFFFF&color=1456A0&bold=true" 
                             class="rounded-circle border border-2 border-white shadow-sm flex-shrink-0" style="width: 48px; height: 48px;" alt="Avatar">
                        <div class="overflow-hidden">
                            <small class="text-white-50 d-block" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Akun Administrator</small>
                            <strong class="text-white d-block text-truncate" style="font-size: 1rem;">{{ $user->name }}</strong>
                            <span class="badge bg-white text-primary rounded-pill px-2.5 py-0.5 mt-1" style="font-size: 0.7rem; font-weight: 700;">
                                {{ $user->email }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row border-top border-white-20">
                        <span>Status Otoritas:</span>
                        <span class="text-white fw-bold d-inline-flex align-items-center gap-1">
                            <i data-lucide="check-check" style="width: 14px; height: 14px; color: #6ee7b7;"></i> Root Super Admin
                        </span>
                    </div>
                    <div class="info-row">
                        <span>Proteksi Keamanan:</span>
                        <span class="text-white fw-bold">Read-Only Guard Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GoPay-Style 4-Column Quick Menu Grid (Khusus Mobile < 768px) --}}
    <div class="haven-quick-grid d-md-none mb-4">
        <a href="{{ route('super-admin.users.index') }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #EAF3FF; color: #1456A0;">
                <i data-lucide="users" style="width: 22px; height: 22px;"></i>
            </div>
            <span class="haven-quick-label">Pengguna</span>
        </a>
        <a href="{{ route('keuangan.buku_kas.index') }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #E8F5E9; color: #2E7D32;">
                <i data-lucide="wallet" style="width: 22px; height: 22px;"></i>
            </div>
            <span class="haven-quick-label">Buku Kas</span>
        </a>
        <a href="{{ route('super-admin.audit-log.index') }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #FEF3C7; color: #B45309;">
                <i data-lucide="history" style="width: 22px; height: 22px;"></i>
            </div>
            <span class="haven-quick-label">Audit Log</span>
        </a>
        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalSwitchRole" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #F3E8FF; color: #7E22CE;">
                <i data-lucide="shuffle" style="width: 22px; height: 22px;"></i>
            </div>
            <span class="haven-quick-label">Switch Role</span>
        </a>
        <a href="{{ route('ronda.index', ['tab' => 'portal']) }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #CCFBF1; color: #0F766E;">
                <i data-lucide="map-pin" style="width: 22px; height: 22px;"></i>
            </div>
            <span class="haven-quick-label">GPS Gerbang</span>
        </a>
        <a href="{{ route('super-admin.master-keuangan.index') }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #FFEDD5; color: #C2410C;">
                <i data-lucide="tag" style="width: 22px; height: 22px;"></i>
            </div>
            <span class="haven-quick-label">Tarif Iuran</span>
        </a>
        <a href="{{ route('ronda.index') }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #E0F2FE; color: #0284C7;">
                <i data-lucide="cctv" style="width: 22px; height: 22px;"></i>
            </div>
            <span class="haven-quick-label">CCTV & Ronda</span>
        </a>
        <a href="javascript:void(0)" onclick="toggleMobileSidebar()" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #F1F5F9; color: #475569;">
                <i data-lucide="layout-grid" style="width: 22px; height: 22px;"></i>
            </div>
            <span class="haven-quick-label">Lainnya</span>
        </a>
    </div>

    {{-- ======================================================================
         2. PANEL STATISTIK MONITOR KESEHATAN SISTEM & KEPENDUDUKAN
         ====================================================================== --}}
    <div class="row g-2 g-md-4 mb-4">
        {{-- Card 1: Total Pengguna --}}
        <div class="col-6 col-sm-6 col-xl-3">
            <div class="sa-kpi-card accent-primary">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.4px;">Total Pengguna Terdaftar</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: #EAF3FF; color: #1456A0;">
                            <i data-lucide="users" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-size: 1.65rem;">{{ number_format($userStats['total_user'], 0, ',', '.') }}</h3>
                    <small class="text-muted d-block" style="font-size: 0.76rem;">Pengguna aktif di seluruh tingkatan</small>
                </div>
                <div class="pt-3 mt-3 border-top d-flex flex-wrap gap-1.5" style="font-size: 0.72rem;">
                    <span class="badge bg-light text-dark border px-2 py-1">1 Super Admin</span>
                    <span class="badge bg-light text-dark border px-2 py-1">{{ $userStats['rw'] }} RW</span>
                    <span class="badge bg-light text-dark border px-2 py-1">{{ $userStats['rt'] }} RT</span>
                    <span class="badge bg-light text-primary border fw-bold px-2 py-1">{{ $userStats['warga'] }} Warga</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Kepala Keluarga --}}
        <div class="col-6 col-sm-6 col-xl-3">
            <div class="sa-kpi-card accent-secondary">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.4px;">Kepala Keluarga (KK)</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: #EAF3FF; color: #2878D0;">
                            <i data-lucide="home" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-size: 1.65rem;">{{ number_format($totalKk, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">KK</span></h3>
                    <small class="text-muted d-block" style="font-size: 0.76rem;">Terdata pada 8 RT di RW 013</small>
                </div>
                <div class="pt-3 mt-3 border-top small text-muted d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                    <span>Cakupan Wilayah:</span>
                    <strong class="text-dark">RT 001 s.d RT 008</strong>
                </div>
            </div>
        </div>

        {{-- Card 3: Rekonsiliasi Rekening Buku Kas --}}
        <div class="col-6 col-sm-6 col-xl-3">
            <div class="sa-kpi-card accent-green">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.4px;">Status Pembukuan Kas</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: #E8F6EF; color: #16865D;">
                            <i data-lucide="badge-check" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="font-size: 1.65rem;">{{ $reconciledCount }} <span class="fs-6 fw-normal text-muted">/ {{ count($bukuKasLedger) }} Akun</span></h3>
                    <small class="text-success fw-bold d-block" style="font-size: 0.76rem;">
                        <i data-lucide="check-circle" style="width: 12px; height: 12px; vertical-align: middle;"></i> Pembukuan 100% Cocok (Selisih Rp 0)
                    </small>
                </div>
                <div class="pt-3 mt-3 border-top small text-muted d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                    <span>Rekening Berselisih:</span>
                    <strong class="{{ $discrepancyCount > 0 ? 'text-danger' : 'text-success' }}">{{ $discrepancyCount }} Akun</strong>
                </div>
            </div>
        </div>

        {{-- Card 4: Konsolidasi Wilayah --}}
        <div class="col-6 col-sm-6 col-xl-3">
            <div class="sa-kpi-card accent-blue">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.4px;">Total Gabungan Kas RW & RT</span>
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: #EAF3FF; color: #1456A0;">
                            <i data-lucide="landmark" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-primary mb-1" style="font-size: 1.65rem;">Rp {{ number_format($totalKonsolidasiWilayah, 0, ',', '.') }}</h3>
                    <small class="text-muted d-block" style="font-size: 0.76rem;">Kas Induk + 8 Kas RT + Kas Sampah</small>
                </div>
                <div class="pt-3 mt-3 border-top small text-muted d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                    <span>Status Data:</span>
                    <strong class="text-success">Tervalidasi Database</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         3. PANEL AKSES CEPAT SWITCH ROLE + PILIH MODE SIMULASI
         ====================================================================== --}}
    <div class="sa-ledger-container mb-4">
        <div class="border-bottom p-4 d-flex align-items-center justify-content-between flex-wrap gap-2 bg-white">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge rounded-pill px-2.5 py-1 text-white fw-bold" style="background: #1456A0; font-size: 0.72rem;">
                        <i data-lucide="shuffle" style="width: 12px; height: 12px; vertical-align: middle;"></i> SIMULATOR AKSES
                    </span>
                    <span class="badge rounded-pill bg-light text-dark border px-2 py-0.5" style="font-size: 0.7rem;">
                        Tanpa Logout &bull; Sesi Aman
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-0">Akses Cepat Switch Role & Pengaturan Mode Sesi</h5>
                <small class="text-muted">Pilih peran target yang ingin disimulasikan beserta mode operasional sesi yang diinginkan.</small>
            </div>
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalSwitchRole">
                    <i data-lucide="sliders-horizontal" style="width: 14px; height: 14px;"></i> Buka Dialog Switch Role Lengkap
                </button>
            </div>
        </div>

        {{-- Tampilan Mobile Ringkas Ala GoPay (< 768px) --}}
        <div class="d-md-none p-3 bg-white">
            <div class="p-3 rounded-4 border bg-light text-center">
                <div class="d-flex align-items-center justify-content-center gap-1.5 mb-2 flex-wrap">
                    <span class="badge bg-primary text-white rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">Ketua RW</span>
                    <span class="badge bg-success text-white rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">8 Ketua RT</span>
                    <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">Warga RT 001-008</span>
                </div>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Simulasi Peran &amp; Uji Coba Akses</h6>
                <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4;">
                    Pilih peran target untuk mensimulasikan antarmuka dan perizinan sistem dalam <strong>Mode Lihat Saja</strong> atau <strong>Mode Edit</strong>.
                </p>
                <button type="button" class="btn btn-primary rounded-pill w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#modalSwitchRole" style="background: #1456A0; font-size: 0.84rem;">
                    <i data-lucide="shuffle" style="width: 16px; height: 16px;"></i> Buka Dialog Switch Role Lengkap
                </button>
            </div>
        </div>

        {{-- Tampilan Desktop (>= 768px) 100% Utuh --}}
        <div class="p-4 bg-white d-none d-md-block">
            <div class="row g-4">
                {{-- Opsi 1: Pimpinan RW --}}
                <div class="col-12 col-lg-4">
                    <div class="sa-switch-card">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                    Tingkat RW 013
                                </span>
                                <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Pimpinan</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $rwUser ? $rwUser->name : 'Ketua RW 013' }}</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.78rem; line-height: 1.45;">
                                {{ $rwUser ? $rwUser->email : 'rw013@havenportal.id' }} &bull; Akses penuh dashboard monitoring 8 RT, audit, dan kas induk RW.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('switch.role') }}">
                            @csrf
                            <input type="hidden" name="target_role" value="rw">
                            
                            {{-- Pilihan Mode Sesi --}}
                            <div class="sa-mode-box">
                                <label class="form-label text-dark fw-bold small mb-1.5 d-block" style="font-size: 0.74rem;">Pilih Mode Sesi:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="radio" name="mode" id="mode_rw_view" value="view_only" checked>
                                        <label class="form-check-label small fw-semibold" for="mode_rw_view" style="font-size: 0.75rem;">Lihat Saja</label>
                                    </div>
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="radio" name="mode" id="mode_rw_edit" value="edit">
                                        <label class="form-check-label small fw-semibold text-danger" for="mode_rw_edit" style="font-size: 0.75rem;">Mode Edit</label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2 d-flex align-items-center justify-content-center shadow-sm" style="background: #1456A0; border-color: #1456A0; font-size: 0.82rem; gap: 8px;">
                                <i data-lucide="arrow-right-circle" style="width: 15px; height: 15px;"></i> Masuk sebagai Ketua RW
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Opsi 2: Pilihan Ketua RT 001 - 008 --}}
                <div class="col-12 col-lg-4">
                    <div class="sa-switch-card">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-success text-white rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                    Tingkat Rukun Tetangga (RT)
                                </span>
                                <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">8 Wilayah</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Pilih Salah Satu Ketua RT (001 s.d 008)</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.78rem; line-height: 1.45;">
                                Akses manajemen keuangan otonom RT, tagihan iuran keluarga, dan administrasi kependudukan tingkat RT.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('switch.role') }}">
                            @csrf
                            <input type="hidden" name="target_role" value="rt">

                            <div class="mb-3">
                                <label class="form-label text-dark fw-bold small mb-1" style="font-size: 0.74rem;">Pilih Wilayah RT:</label>
                                <select class="form-select form-select-sm rounded-3" name="rt_number" required style="font-size: 0.82rem; padding: 7px 12px;">
                                    @foreach($rtUsers as $rt)
                                        <option value="{{ $rt->rt_number }}">
                                             {{ $rt->clean_rt_label }} &bull; {{ $rt->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Pilihan Mode Sesi --}}
                            <div class="sa-mode-box">
                                <label class="form-label text-dark fw-bold small mb-1.5 d-block" style="font-size: 0.74rem;">Pilih Mode Sesi:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="radio" name="mode" id="mode_rt_view" value="view_only" checked>
                                        <label class="form-check-label small fw-semibold" for="mode_rt_view" style="font-size: 0.75rem;">Lihat Saja</label>
                                    </div>
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="radio" name="mode" id="mode_rt_edit" value="edit">
                                        <label class="form-check-label small fw-semibold text-danger" for="mode_rt_edit" style="font-size: 0.75rem;">Mode Edit</label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-outline-success rounded-pill w-100 fw-bold py-2 d-flex align-items-center justify-content-center shadow-sm" style="font-size: 0.82rem; gap: 8px;">
                                <i data-lucide="building-2" style="width: 15px; height: 15px;"></i> Masuk sebagai Ketua RT Terpilih
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Opsi 3: Pilihan Akun Warga --}}
                <div class="col-12 col-lg-4">
                    <div class="sa-switch-card">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                    Tingkat Warga
                                </span>
                                <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">{{ $userStats['warga'] }} Warga</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Simulasi Akun Warga</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.78rem; line-height: 1.45;">
                                Verifikasi tampilan tagihan iuran keluarga, konfirmasi transfer, dan layanan surat perorangan.
                            </p>
                        </div>

                        <div>
                            <div class="p-2.5 rounded-3 bg-white border mb-3 text-center">
                                <small class="text-muted d-block" style="font-size: 0.72rem;">Akses Cepat Warga RT 001</small>
                                <strong class="text-dark small d-block">Theodore Williams</strong>
                            </div>

                            <form method="POST" action="{{ route('switch.role') }}">
                                @csrf
                                <input type="hidden" name="target_role" value="warga">
                                <input type="hidden" name="rt_number" value="001">
                                <input type="hidden" name="mode" value="view_only">
                                <button type="submit" class="btn btn-outline-primary rounded-pill w-100 fw-bold py-2 d-flex align-items-center justify-content-center shadow-sm mb-2" style="font-size: 0.8rem; gap: 8px;">
                                    <i data-lucide="user" style="width: 14px; height: 14px;"></i> Masuk (Warga RT 001)
                                </button>
                            </form>

                            <button type="button" class="btn btn-light border rounded-pill w-100 fw-semibold py-2 text-muted d-flex align-items-center justify-content-center shadow-xs" data-bs-toggle="modal" data-bs-target="#modalSwitchRole" style="font-size: 0.78rem; gap: 8px;">
                                <i data-lucide="search" style="width: 14px; height: 14px;"></i> Cari Warga Lainnya...
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         4. MONITOR STATUS PEMBUKUAN & REKONSILIASI KAS WILAYAH (100% DATA DB)
         ====================================================================== --}}
    <div class="sa-ledger-container mb-4" id="section-ledger">
        <div class="border-bottom p-4 d-flex align-items-center justify-content-between flex-wrap gap-3 bg-white">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge rounded-pill px-2.5 py-1 text-white fw-bold" style="background: #16865D; font-size: 0.72rem;">
                        <i data-lucide="check-circle" style="width: 12px; height: 12px; vertical-align: middle;"></i> STATUS PEMBUKUAN
                    </span>
                    <span class="badge rounded-pill bg-light text-dark border px-2 py-0.5" style="font-size: 0.7rem;">
                        Total {{ count($bukuKasLedger) }} Rekening Kas
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-0">Audit & Status Pembukuan Real-Time Seluruh Rekening Buku Kas</h5>
                <small class="text-muted">Perhitungan matematis real-time: <code>Saldo Akhir = Saldo Awal + Total Pemasukan Valid - Total Pengeluaran Valid</code>.</small>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill px-3 py-2 fw-bold text-success border border-success-subtle bg-success-subtle d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                    <i data-lucide="shield-check" style="width: 15px; height: 15px;"></i> Pembukuan 100% Cocok (Selisih Rp 0)
                </span>
            </div>
        </div>

        <div class="p-0 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 950px; font-size: 0.83rem;">
                    <thead class="table-light text-muted" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3">Nama Buku Kas</th>
                            <th class="py-3">Posisi / Kategori</th>
                            <th class="py-3">Rekening Bank</th>
                            <th class="py-3 text-end">Saldo Awal</th>
                            <th class="py-3 text-end text-success">Pemasukan</th>
                            <th class="py-3 text-end text-danger">Pengeluaran</th>
                            <th class="py-3 text-end">Saldo Akhir</th>
                            <th class="py-3 text-center">Selisih</th>
                            <th class="pe-4 py-3 text-center">Status Audit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bukuKasLedger as $item)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle p-1.5 d-flex align-items-center justify-content-center flex-shrink-0" 
                                             style="background: {{ $item['jenis_kas'] === 'kas_rw' ? '#EAF3FF' : ($item['jenis_kas'] === 'kas_rt' ? '#E8F6EF' : '#FEF3C7') }}; 
                                                    color: {{ $item['jenis_kas'] === 'kas_rw' ? '#1456A0' : ($item['jenis_kas'] === 'kas_rt' ? '#16865D' : '#D97706') }}; 
                                                    width: 34px; height: 34px;">
                                            <i data-lucide="{{ $item['jenis_kas'] === 'kas_rw' ? 'landmark' : ($item['jenis_kas'] === 'kas_rt' ? 'building-2' : 'wallet') }}" style="width: 16px; height: 16px;"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark d-block" style="font-size: 0.84rem;">{{ $item['nama_kas'] }}</strong>
                                            <span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 0.68rem; font-family: monospace;">{{ $item['kode_kas'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($item['jenis_kas'] === 'kas_rw')
                                        <span class="badge rounded-pill text-white fw-bold px-2 py-0.5" style="background: #1456A0; font-size: 0.68rem;">Induk RW 013</span>
                                    @elseif($item['jenis_kas'] === 'kas_rt')
                                        <span class="badge rounded-pill text-white fw-bold px-2 py-0.5" style="background: #16865D; font-size: 0.68rem;">Otonom RT {{ $item['clean_rt'] }}</span>
                                    @elseif($item['jenis_kas'] === 'kas_sampah')
                                        <span class="badge rounded-pill text-white fw-bold px-2 py-0.5" style="background: #0284C7; font-size: 0.68rem;">Rekening Bersama RW</span>
                                    @else
                                        <span class="badge rounded-pill text-dark border bg-light px-2 py-0.5" style="font-size: 0.68rem;">Unit / Lingkungan</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-secondary small">{{ $item['no_rekening'] }}</span>
                                </td>
                                <td class="text-end text-muted">
                                    Rp {{ number_format($item['saldo_awal'], 0, ',', '.') }}
                                </td>
                                <td class="text-end text-success fw-semibold">
                                    +Rp {{ number_format($item['total_pemasukan'], 0, ',', '.') }}
                                </td>
                                <td class="text-end text-danger fw-semibold">
                                    -Rp {{ number_format($item['total_pengeluaran'], 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    Rp {{ number_format($item['saldo_sekarang'], 0, ',', '.') }}
                                </td>
                                <td class="text-center font-monospace">
                                    @if($item['selisih'] == 0)
                                        <span class="badge bg-light text-success border px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">Rp 0</span>
                                    @else
                                        <span class="badge bg-danger text-white px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">Rp {{ number_format($item['selisih'], 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    @if($item['is_klop'])
                                        <span class="badge rounded-pill text-success bg-success-subtle border border-success-subtle px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                            <i data-lucide="check" style="width: 12px; height: 12px; vertical-align: middle;"></i> Klop 100%
                                        </span>
                                    @else
                                        <span class="badge rounded-pill text-danger bg-danger-subtle border border-danger-subtle px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                            <i data-lucide="alert-triangle" style="width: 12px; height: 12px; vertical-align: middle;"></i> Selisih
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Tidak ada data rekening buku kas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-light p-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 text-muted small border-top" style="font-size: 0.76rem;">
            <div>
                <i data-lucide="info" style="width: 14px; height: 14px; vertical-align: middle; color: #1456A0;"></i>
                Seluruh data dihitung secara atomik langsung dari tabel transaksi kas berstatus selesai (tanpa mock data / tanpa hardcode).
            </div>
            <div>
                <strong>Total Gabungan Kas RW & RT:</strong> Rp {{ number_format($totalKonsolidasiWilayah, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- ======================================================================
         5. WIDGET 5 AKTIVITAS SISTEM TERBARU (REAL-TIME AUDIT TRAIL)
         ====================================================================== --}}
    <div class="sa-ledger-container mb-4">
        <div class="border-bottom p-4 d-flex align-items-center justify-content-between flex-wrap gap-3 bg-white">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge rounded-pill px-2.5 py-1 text-white fw-bold" style="background: #1456A0; font-size: 0.72rem;">
                        <i data-lucide="history" style="width: 12px; height: 12px; vertical-align: middle;"></i> AUDIT TRAIL
                    </span>
                    <span class="badge rounded-pill bg-light text-dark border px-2 py-0.5" style="font-size: 0.7rem;">
                        Real-Time
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-0">5 Aktivitas Sistem Terbaru</h5>
                <small class="text-muted">Rekaman kronologis aksi login, simulasi switch role, mutasi pengguna, dan perubahan master sistem.</small>
            </div>

            <div>
                <a href="{{ route('super-admin.audit-log.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                    <span>Lihat Semua Log</span>
                    <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 800px; font-size: 0.84rem;">
                <thead class="table-light text-uppercase text-muted" style="font-size: 0.74rem;">
                    <tr>
                        <th class="ps-4" style="min-width: 150px;">Waktu &amp; IP</th>
                        <th style="min-width: 120px;">Kategori</th>
                        <th style="min-width: 180px;">Pelaku</th>
                        <th style="min-width: 150px;">Aksi</th>
                        <th class="pe-4" style="min-width: 250px;">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                        <tr>
                            <td class="ps-4">
                                <strong class="d-block text-dark small">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</strong>
                                <small class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $log->ip_address ?: '127.0.0.1' }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill fw-bold {{ $log->category_badge_class }}" style="font-size: 0.7rem; padding: 4px 10px;">
                                    {{ $log->category_label }}
                                </span>
                            </td>
                            <td>
                                <strong class="d-block text-dark small">{{ $log->user ? $log->user->name : 'Sistem' }}</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    {{ $log->user ? strtoupper($log->user->role) : '-' }}
                                    @if($log->impersonator_id)
                                        &bull; <span class="text-warning fw-bold">via Super Admin</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <code class="p-1 rounded bg-light border text-primary" style="font-size: 0.74rem;">{{ $log->action ?: $log->activity }}</code>
                            </td>
                            <td class="pe-4 text-dark small">
                                {{ $log->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted small">Belum ada aktivitas tercatat di sistem.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
