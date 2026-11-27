@extends('layouts.app')

@section('title', 'Profil Saya')

@push('styles')
<style>
    :root:not([data-theme="dark"]) {
        --wm-primary: #1456A0;
        --wm-primary-dark: #0F3966;
        --wm-secondary: #2878D0;
        --wm-light-blue: #EAF3FF;
        --wm-bg: #F4F7FB;
        --wm-text-dark: #172B4D;
        --wm-text-muted: #64748B;
        --wm-border-card: #E2E8F0;
        --wm-success: #16865D;
        --wm-warning: #D97706;
    }

    /* Page Header */
    .profile-hero-header {
        background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #FFFFFF;
        box-shadow: 0 4px 14px rgba(20, 86, 160, 0.15);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .profile-hero-header::after {
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
    .wm-profile-box {
        background: var(--wm-card, #FFFFFF);
        border: 1px solid var(--wm-border, #E2E8F0);
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        padding: 24px;
        margin-bottom: 24px;
        transition: all 0.2s ease;
    }

    /* Left Card: Profile Summary */
    .profile-avatar-wrap {
        position: relative;
        width: 110px;
        height: 110px;
        margin: 0 auto 16px;
    }

    .profile-avatar-img {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--wm-card, #FFFFFF);
        box-shadow: 0 4px 12px rgba(20, 86, 160, 0.18);
    }

    .profile-avatar-camera-btn {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--wm-primary);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--wm-card, #FFFFFF);
        cursor: pointer;
        transition: transform 0.2s ease, background-color 0.2s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }

    .profile-avatar-camera-btn:hover {
        background: var(--wm-primary-dark);
        transform: scale(1.08);
    }

    /* Mini Statistics (3 Kolom) */
    .mini-stat-item {
        background: var(--wm-light-blue, #F8FAFD);
        border: 1px solid var(--wm-border, #EDF2F7);
        border-radius: 12px;
        padding: 12px 8px;
        text-align: center;
        transition: background-color 0.2s ease;
    }

    .mini-stat-item:hover {
        background: rgba(40, 120, 208, 0.15);
    }

    .mini-stat-val {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--wm-primary);
        line-height: 1.1;
        margin-bottom: 2px;
    }

    .mini-stat-lbl {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--wm-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Vertical Navigation Tabs */
    .profile-nav-menu {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .profile-nav-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid transparent;
        background: transparent;
        color: var(--wm-text-dark);
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        text-align: left;
    }

    .profile-nav-btn .nav-left-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .profile-nav-btn i.nav-icon {
        width: 18px;
        height: 18px;
        color: var(--wm-text-muted);
        transition: color 0.2s ease;
    }

    .profile-nav-btn:hover {
        background: var(--wm-light-blue, #F4F7FB);
        color: var(--wm-primary);
    }

    .profile-nav-btn:hover i.nav-icon {
        color: var(--wm-primary);
    }

    .profile-nav-btn.active {
        background: var(--wm-primary) !important;
        color: #FFFFFF !important;
        border-color: var(--wm-primary) !important;
        box-shadow: 0 3px 10px rgba(20, 86, 160, 0.22);
    }

    .profile-nav-btn.active i.nav-icon {
        color: #FFFFFF !important;
    }

    .profile-nav-btn.active .nav-chevron {
        color: #FFFFFF !important;
        opacity: 0.9;
    }

    .profile-nav-btn .nav-chevron {
        width: 16px;
        height: 16px;
        color: var(--wm-text-muted, #CBD5E1);
        transition: transform 0.2s ease;
    }

    /* Right Panel Content Header */
    .panel-header-title {
        color: var(--wm-text-dark);
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .panel-header-desc {
        color: var(--wm-text-muted);
        font-size: 0.85rem;
        margin-bottom: 20px;
    }

    /* Form Fields Styling */
    .wm-label {
        font-size: 0.83rem;
        font-weight: 600;
        color: var(--wm-text-dark);
        margin-bottom: 6px;
    }

    .wm-input {
        border-radius: 10px;
        border: 1px solid var(--wm-border-card);
        background-color: var(--wm-card, #FFFFFF);
        padding: 10px 14px;
        font-size: 0.88rem;
        color: var(--wm-text-dark);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .wm-input:focus {
        border-color: var(--wm-primary);
        box-shadow: 0 0 0 3px rgba(20, 86, 160, 0.12);
    }

    .wm-input-readonly {
        background-color: var(--wm-light-blue, #F8FAFC) !important;
        border-color: var(--wm-border, #E2E8F0);
        color: var(--wm-muted, #475569);
        font-family: inherit;
    }

    /* Readonly Badges & Info Boxes */
    .locked-field-badge {
        font-size: 0.72rem;
        padding: 3px 8px;
        background: var(--wm-light-blue, #F1F5F9);
        color: var(--wm-muted, #64748B);
        border: 1px solid var(--wm-border, transparent);
        border-radius: 6px;
        font-weight: 600;
    }

    .info-notice-box {
        background: var(--wm-light-blue, #EAF3FF);
        border: 1px solid var(--wm-border, #C2DCFF);
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 0.84rem;
        color: var(--wm-text-dark, #0F3966);
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    /* Dark Mode Overrides for Profile */
    [data-theme="dark"] .wm-profile-box,
    body.dark-mode .wm-profile-box {
        background-color: #131F35 !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
    }
    [data-theme="dark"] .mini-stat-item,
    body.dark-mode .mini-stat-item {
        background-color: #0F1A2E !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
    }
    [data-theme="dark"] .mini-stat-item:hover,
    body.dark-mode .mini-stat-item:hover {
        background-color: #1E2D47 !important;
    }
    [data-theme="dark"] .profile-nav-btn,
    body.dark-mode .profile-nav-btn {
        color: #E2E8F0 !important;
    }
    [data-theme="dark"] .profile-nav-btn:hover:not(.active),
    body.dark-mode .profile-nav-btn:hover:not(.active) {
        background-color: #0F1A2E !important;
        color: #7DB4F5 !important;
    }
    [data-theme="dark"] .profile-avatar-img,
    body.dark-mode .profile-avatar-img {
        border-color: #131F35 !important;
    }
    [data-theme="dark"] .profile-avatar-camera-btn,
    body.dark-mode .profile-avatar-camera-btn {
        border-color: #131F35 !important;
    }
    [data-theme="dark"] .wm-label,
    body.dark-mode .wm-label,
    [data-theme="dark"] .panel-header-title,
    body.dark-mode .panel-header-title {
        color: #F1F5F9 !important;
    }
    [data-theme="dark"] .wm-input,
    body.dark-mode .wm-input {
        background-color: #0F1A2E !important;
        border-color: #2B3E5E !important;
        color: #F1F5F9 !important;
    }
    [data-theme="dark"] .wm-input-readonly,
    body.dark-mode .wm-input-readonly {
        background-color: #0B1324 !important;
        border-color: #24344F !important;
        color: #94A3B8 !important;
    }
    [data-theme="dark"] .info-notice-box,
    body.dark-mode .info-notice-box {
        background-color: #0F1A2E !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
    }

    /* Action Buttons */
    .btn-warkas-primary {
        background: var(--wm-primary);
        color: #FFFFFF;
        border: none;
        border-radius: 10px;
        padding: 10px 22px;
        font-size: 0.88rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-warkas-primary:hover {
        background: var(--wm-primary-dark);
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(20, 86, 160, 0.25);
    }

    /* Timeline Tab 5 */
    .activity-timeline {
        position: relative;
        padding-left: 28px;
    }

    .activity-timeline::before {
        content: "";
        position: absolute;
        top: 8px;
        bottom: 8px;
        left: 10px;
        width: 2px;
        background: #E2E8F0;
    }

    .activity-item {
        position: relative;
        margin-bottom: 20px;
    }

    .activity-item:last-child {
        margin-bottom: 0;
    }

    .activity-dot {
        position: absolute;
        left: -28px;
        top: 4px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #FFFFFF;
        border: 2px solid var(--wm-primary);
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }

    /* Switch toggles */
    .form-switch .form-check-input {
        width: 2.75em;
        height: 1.45em;
        cursor: pointer;
    }
    .form-switch .form-check-input:checked {
        background-color: var(--wm-primary);
        border-color: var(--wm-primary);
    }

    @media (max-width: 991.98px) {
        .profile-hero-header {
            padding: 20px;
        }
        .wm-profile-box {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">

    {{-- ======================================================================
         1. HERO HEADER ATAS
         ====================================================================== --}}
    <div class="profile-hero-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                    <i data-lucide="shield" style="width: 12px; height: 12px; vertical-align: middle;"></i>
                    @if($user->role === 'super_admin')
                        Super Administrator
                    @elseif($user->isRw())
                        Ketua RW 013
                    @elseif($user->isRt())
                        Ketua {{ $user->clean_rt_label }}
                    @elseif($user->isBendahara())
                        Bendahara RW 013
                    @else
                        Warga {{ $user->clean_rt_label }}
                    @endif
                </span>
                <span class="text-white-50 small">•</span>
                <span class="text-white small fw-medium">Kelurahan Sukamiskin, Arcamanik</span>
            </div>
            <h3 class="fw-bold mb-1 text-white">Pengaturan Profil Saya</h3>
            <p class="mb-0 text-white-50 small">Kelola informasi pribadi, data akun, keamanan sesi, dan preferensi privasi Anda</p>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0 align-items-center small">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-white-50 text-decoration-none d-inline-flex align-items-center gap-1">
                        <i data-lucide="home" style="width: 14px; height: 14px;"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">
                    Profil Saya
                </li>
            </ol>
        </nav>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert" style="background: #E3F5EC; color: #16865D; border-radius: 12px;">
            <i data-lucide="check-circle-2" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-medium small">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert" style="background: #FEE2E2; color: #DC2626; border-radius: 12px;">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-medium small">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background: #FEE2E2; color: #DC2626; border-radius: 12px;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i data-lucide="alert-triangle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                <strong class="small">Periksa kembali isian formulir:</strong>
            </div>
            <ul class="mb-0 small ps-4">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Tersembunyi untuk Quick Upload Avatar --}}
    <form action="{{ route('profile.upload-foto') }}" method="POST" enctype="multipart/form-data" id="formUploadAvatar" class="d-none">
        @csrf
        <input type="file" name="foto_profil" id="avatarUploadInput" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="document.getElementById('formUploadAvatar').submit();">
    </form>

    {{-- ======================================================================
         2. SPLIT LAYOUT 2 KOLOM (KIRI: PROFIL & 6 TAB | KANAN: KONTEN TAB)
         ====================================================================== --}}
    <div class="row g-4">
        {{-- ==================================================================
             KOLOM KIRI: PANEL PROFIL RINGKAS & NAVIGASI 6 TAB VERTIKAL (~33%)
             ================================================================== --}}
        <div class="col-12 col-lg-4 col-xl-4">
            {{-- Kartu Profil Ringkas --}}
            <div class="wm-profile-box text-center">
                <div class="profile-avatar-wrap">
                    <img src="{{ $user->avatar_url }}" onerror="this.onerror=null;this.src='{{ $user->initials_svg }}';" alt="{{ $user->name }}" class="profile-avatar-img" id="userAvatarPreview">
                    <label for="avatarUploadInput" class="profile-avatar-camera-btn" title="Ganti Foto Profil">
                        <i data-lucide="camera" style="width: 16px; height: 16px;"></i>
                    </label>
                </div>

                <h5 class="fw-bold mb-1" style="color: var(--wm-text-dark);">{{ $user->name }}</h5>
                <p class="text-muted small mb-2 text-truncate" title="{{ $user->email }}">{{ $user->email }}</p>

                <div class="d-flex flex-wrap justify-content-center gap-1 mb-3">
                    @if($user->role === 'super_admin')
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #0F3966; color: #FFFFFF; font-size: 0.75rem;">
                            Super Admin
                        </span>
                        <span class="badge rounded-pill px-3 py-1 fw-semibold bg-light text-secondary border" style="font-size: 0.75rem;">
                            Sistem Pusat
                        </span>
                    @elseif($user->isRw())
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #16865D; color: #FFFFFF; font-size: 0.75rem;">
                            Ketua RW 013
                        </span>
                        <span class="badge rounded-pill px-3 py-1 fw-semibold bg-light text-secondary border" style="font-size: 0.75rem;">
                            Wilayah RW
                        </span>
                    @elseif($user->isRt())
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #1456A0; color: #FFFFFF; font-size: 0.75rem;">
                            Ketua {{ $user->clean_rt_label }}
                        </span>
                        <span class="badge rounded-pill px-3 py-1 fw-semibold bg-light text-secondary border" style="font-size: 0.75rem;">
                            RW 013
                        </span>
                    @elseif($user->isBendahara())
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #D97706; color: #FFFFFF; font-size: 0.75rem;">
                            Bendahara RW
                        </span>
                        <span class="badge rounded-pill px-3 py-1 fw-semibold bg-light text-secondary border" style="font-size: 0.75rem;">
                            RW 013
                        </span>
                    @else
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #2878D0; color: #FFFFFF; font-size: 0.75rem;">
                            Warga
                        </span>
                        <span class="badge rounded-pill px-3 py-1 fw-semibold bg-light text-secondary border" style="font-size: 0.75rem;">
                            {{ $user->clean_rt_label }} / RW 013
                        </span>
                    @endif
                </div>

                {{-- 3 Mini-Statistik Riil dari DB --}}
                <div class="row g-2 pt-2 border-top">
                    <div class="col-4">
                        <div class="mini-stat-item">
                            <div class="mini-stat-val">{{ $totalAktivitas }}</div>
                            <div class="mini-stat-lbl">Aktivitas</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mini-stat-item">
                            <div class="mini-stat-val">{{ $suratCount }}</div>
                            <div class="mini-stat-lbl">Surat</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mini-stat-item">
                            @if($user->isWarga())
                                <div class="mini-stat-val">{{ $iuranCount }}</div>
                                <div class="mini-stat-lbl">Iuran</div>
                            @else
                                <div class="mini-stat-val">{{ $pengaduanCount }}</div>
                                <div class="mini-stat-lbl">Aspirasi</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu Akses e-KTP / RFID Digital --}}
            <div class="wm-profile-box p-3 mb-4 text-start">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-dark d-flex align-items-center gap-1.5">
                        <i data-lucide="radio" style="width: 16px; height: 16px; color: var(--wm-primary);"></i>
                        Akses e-KTP &amp; Kartu RFID
                    </span>
                    @if($user->rfid_uid)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                            <i data-lucide="check" style="width: 10px; height: 10px; display: inline-block; vertical-align: -1px;"></i> Terdaftar
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                            Belum Terdaftar
                        </span>
                    @endif
                </div>

                <div class="small text-muted mb-3" style="font-size: 0.78rem; line-height: 1.45;">
                    @if($user->rfid_uid)
                        Chip Fisik: <strong class="font-monospace text-primary">{{ $user->masked_rfid_uid }}</strong>
                        <div class="text-secondary mt-0.5">Dapat digunakan untuk akses gerbang masuk portal &amp; absensi ronda.</div>
                    @else
                        <span>Tautkan chip e-KTP asli (14 hex) atau kartu RFID (8 hex) Anda untuk akses gerbang masuk otomatis.</span>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold" data-bs-toggle="modal" data-bs-target="#modalProfileRekamEktp" style="font-size: 0.78rem;">
                        <i data-lucide="scan" style="width: 13px; height: 13px; vertical-align: -1px;"></i> {{ $user->rfid_uid ? 'Ganti e-KTP' : 'Rekam e-KTP' }}
                    </button>
                    @if($user->rfid_uid)
                        <form action="{{ route('ronda.portal.unlink') }}" method="POST" class="d-inline" onsubmit="return confirm('Cabut tautan chip e-KTP/RFID dari akun Anda?');">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-bold" style="font-size: 0.78rem;">
                                <i data-lucide="trash-2" style="width: 13px; height: 13px; vertical-align: -1px;"></i> Cabut
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Kartu Menu 6 Tab Vertikal --}}
            <div class="wm-profile-box p-3">
                <div class="d-flex align-items-center justify-content-between mb-3 px-2 pt-1">
                    <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Menu Navigasi Profil</span>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0" style="font-size: 0.72rem;">6 Menu</span>
                </div>

                <div class="profile-nav-menu" role="tablist">
                    {{-- Tab 1: Informasi Pribadi --}}
                    <button type="button" 
                            class="profile-nav-btn {{ $activeTab === 'biodata' ? 'active' : '' }}" 
                            onclick="switchProfileTab('biodata')" 
                            id="btn-tab-biodata"
                            role="tab"
                            aria-controls="panel-biodata"
                            aria-selected="{{ $activeTab === 'biodata' ? 'true' : 'false' }}">
                        <span class="nav-left-content">
                            <i data-lucide="user" class="nav-icon"></i>
                            <span>Informasi Pribadi</span>
                        </span>
                        <i data-lucide="chevron-right" class="nav-chevron"></i>
                    </button>

                    {{-- Tab 2: Informasi Akun --}}
                    <button type="button" 
                            class="profile-nav-btn {{ $activeTab === 'akun' ? 'active' : '' }}" 
                            onclick="switchProfileTab('akun')" 
                            id="btn-tab-akun"
                            role="tab"
                            aria-controls="panel-akun"
                            aria-selected="{{ $activeTab === 'akun' ? 'true' : 'false' }}">
                        <span class="nav-left-content">
                            <i data-lucide="user-check" class="nav-icon"></i>
                            <span>Informasi Akun</span>
                        </span>
                        <i data-lucide="chevron-right" class="nav-chevron"></i>
                    </button>

                    {{-- Tab 3: Keamanan & Sesi --}}
                    <button type="button" 
                            class="profile-nav-btn {{ $activeTab === 'keamanan' ? 'active' : '' }}" 
                            onclick="switchProfileTab('keamanan')" 
                            id="btn-tab-keamanan"
                            role="tab"
                            aria-controls="panel-keamanan"
                            aria-selected="{{ $activeTab === 'keamanan' ? 'true' : 'false' }}">
                        <span class="nav-left-content">
                            <i data-lucide="shield-check" class="nav-icon"></i>
                            <span>Keamanan & Sesi</span>
                        </span>
                        <i data-lucide="chevron-right" class="nav-chevron"></i>
                    </button>

                    {{-- Tab 4: Preferensi Notifikasi --}}
                    <button type="button" 
                            class="profile-nav-btn {{ $activeTab === 'notifikasi' ? 'active' : '' }}" 
                            onclick="switchProfileTab('notifikasi')" 
                            id="btn-tab-notifikasi"
                            role="tab"
                            aria-controls="panel-notifikasi"
                            aria-selected="{{ $activeTab === 'notifikasi' ? 'true' : 'false' }}">
                        <span class="nav-left-content">
                            <i data-lucide="bell" class="nav-icon"></i>
                            <span>Preferensi Notifikasi</span>
                        </span>
                        <i data-lucide="chevron-right" class="nav-chevron"></i>
                    </button>

                    {{-- Tab 5: Aktivitas Saya --}}
                    <button type="button" 
                            class="profile-nav-btn {{ $activeTab === 'aktivitas' ? 'active' : '' }}" 
                            onclick="switchProfileTab('aktivitas')" 
                            id="btn-tab-aktivitas"
                            role="tab"
                            aria-controls="panel-aktivitas"
                            aria-selected="{{ $activeTab === 'aktivitas' ? 'true' : 'false' }}">
                        <span class="nav-left-content">
                            <i data-lucide="activity" class="nav-icon"></i>
                            <span>Aktivitas Saya</span>
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge rounded-pill {{ $activeTab === 'aktivitas' ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' }} fw-bold" style="font-size: 0.72rem;">
                                {{ $totalAktivitas }}
                            </span>
                            <i data-lucide="chevron-right" class="nav-chevron"></i>
                        </span>
                    </button>

                    {{-- Tab 6: Pengaturan & Privasi --}}
                    <button type="button" 
                            class="profile-nav-btn {{ $activeTab === 'privasi' ? 'active' : '' }}" 
                            onclick="switchProfileTab('privasi')" 
                            id="btn-tab-privasi"
                            role="tab"
                            aria-controls="panel-privasi"
                            aria-selected="{{ $activeTab === 'privasi' ? 'true' : 'false' }}">
                        <span class="nav-left-content">
                            <i data-lucide="lock" class="nav-icon"></i>
                            <span>Pengaturan & Privasi</span>
                        </span>
                        <i data-lucide="chevron-right" class="nav-chevron"></i>
                    </button>
                </div>
            </div>

            {{-- Kartu Info Bawah Kiri: Integritas Wilayah RW 013 --}}
            <div class="wm-profile-box p-3" style="background: #F8FAFD; border-color: #E2E8F0;">
                <div class="d-flex align-items-start gap-3">
                    <div class="p-2 rounded-3 bg-white border shadow-xs text-primary" style="flex-shrink: 0;">
                        <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 small" style="color: var(--wm-primary-dark);">Integritas Data Kependudukan</h6>
                        <p class="mb-0 text-muted" style="font-size: 0.77rem; line-height: 1.45;">
                            Data kependudukan RW 013 Sukamiskin terintegrasi aman. Perubahan data NIK/No. KK wajib melalui validasi pengurus RT setempat.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================
             KOLOM KANAN: PANEL KONTEN TAB AKTIF (~67%)
             ================================================================== --}}
        <div class="col-12 col-lg-8 col-xl-8">

            {{-- ==============================================================
                 TAB 1: INFORMASI PRIBADI
                 ============================================================== --}}
            <div class="wm-profile-box tab-content-panel {{ $activeTab === 'biodata' ? '' : 'd-none' }}" id="panel-biodata">
                <div class="panel-header-title">
                    <i data-lucide="user" style="width: 20px; height: 20px; color: var(--wm-primary);"></i>
                    <span>Informasi Pribadi & Biodata</span>
                </div>
                <div class="panel-header-desc">
                    Perbarui biodata dan identitas domisili Anda. Data NIK dan No. KK terkunci untuk menjaga validitas data kependudukan.
                </div>

                {{-- Banner Ganti Foto Profil --}}
                <div class="p-3 rounded-3 mb-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3" style="background: #F8FAFC; border: 1px dashed #CBD5E1;">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="rounded-circle border" style="width: 52px; height: 52px; object-fit: cover;">
                        <div>
                            <div class="fw-bold small" style="color: var(--wm-text-dark);">Foto Profil Akun</div>
                            <div class="text-muted" style="font-size: 0.78rem;">Format didukung: JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                        </div>
                    </div>
                    <label for="avatarUploadInput" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-2" style="cursor: pointer;">
                        <i data-lucide="upload" style="width: 14px; height: 14px;"></i>
                        <span>Unggah Foto Baru</span>
                    </label>
                </div>

                {{-- Form Edit Biodata Pribadi --}}
                <form action="{{ route('profile.biodata.update') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        {{-- Nama Lengkap --}}
                        <div class="col-12 col-md-6">
                            <label class="wm-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control wm-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap sesuai KTP">
                            @error('name')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>

                        {{-- Nomor WhatsApp / HP --}}
                        <div class="col-12 col-md-6">
                            <label class="wm-label">Nomor WhatsApp / HP</label>
                            <input type="text" name="phone" class="form-control wm-input @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890">
                            @error('phone')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="col-12 col-md-4">
                            <label class="wm-label">Jenis Kelamin</label>
                            @php $currGender = old('gender', $user->gender); @endphp
                            <select name="gender" class="form-select wm-input">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ in_array($currGender, ['L', 'Laki-laki']) ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ in_array($currGender, ['P', 'Perempuan']) ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        {{-- Tempat Lahir --}}
                        <div class="col-12 col-md-4">
                            <label class="wm-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control wm-input" value="{{ old('tempat_lahir', $user->tempat_lahir) }}" placeholder="Kota kelahiran">
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="col-12 col-md-4">
                            <label class="wm-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control wm-input" value="{{ old('tanggal_lahir', $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : '') }}">
                        </div>

                        {{-- Agama --}}
                        <div class="col-12 col-md-6">
                            <label class="wm-label">Agama</label>
                            @php $currAgama = old('agama', $user->agama); @endphp
                            <select name="agama" class="form-select wm-input">
                                <option value="">Pilih Agama</option>
                                @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                                    <option value="{{ $agm }}" {{ $currAgama === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Perkawinan --}}
                        <div class="col-12 col-md-6">
                            <label class="wm-label">Status Perkawinan</label>
                            @php $currStatusKawin = old('status_perkawinan', $user->status_perkawinan); @endphp
                            <select name="status_perkawinan" class="form-select wm-input">
                                <option value="">Pilih Status Perkawinan</option>
                                @foreach(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $stk)
                                    <option value="{{ $stk }}" {{ $currStatusKawin === $stk ? 'selected' : '' }}>{{ $stk }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pekerjaan --}}
                        <div class="col-12 col-md-6">
                            <label class="wm-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control wm-input" value="{{ old('pekerjaan', $user->pekerjaan) }}" placeholder="Contoh: Karyawan Swasta, Wiraswasta, PNS">
                        </div>

                        {{-- Blok & Nomor Rumah --}}
                        <div class="col-6 col-md-3">
                            <label class="wm-label">Blok Rumah</label>
                            <input type="text" name="blok_rumah" class="form-control wm-input" value="{{ old('blok_rumah', $user->blok_rumah) }}" placeholder="Contoh: Blok B2">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="wm-label">No. Rumah</label>
                            <input type="text" name="house_number" class="form-control wm-input" value="{{ old('house_number', $user->house_number) }}" placeholder="Contoh: No. 14">
                        </div>

                        {{-- Alamat Domisili --}}
                        <div class="col-12">
                            <label class="wm-label">Alamat Lengkap Domisili</label>
                            <textarea name="address" rows="2" class="form-control wm-input" placeholder="Nama jalan, nomor rumah, dan patokan">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>

                    {{-- ======================================================
                         FIELD TERKUNCI (READ-ONLY) DENGAN MASKING NIK/KK
                         ====================================================== --}}
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold mb-0 small" style="color: var(--wm-text-dark);">
                                    <i data-lucide="lock" style="width: 14px; height: 14px; vertical-align: middle;" class="text-secondary me-1"></i>
                                    Data Kependudukan Terkunci (Read-Only)
                                </h6>
                                <span class="text-muted" style="font-size: 0.77rem;">Data identitas berikut tersinkronisasi dengan Dukcapil & buku induk kependudukan RT/RW.</span>
                            </div>
                            <span class="locked-field-badge">Resmi RT/RW</span>
                        </div>

                        <div class="row g-3">
                            {{-- NIK --}}
                            <div class="col-12 col-md-6">
                                <label class="wm-label d-flex justify-content-between align-items-center">
                                    <span>Nomor Induk Kependudukan (NIK)</span>
                                    @if($user->nik)
                                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none small text-primary" onclick="toggleNikMask()" id="btnToggleNik">
                                            <i data-lucide="eye" style="width: 13px; height: 13px;" id="iconToggleNik"></i>
                                            <span id="textToggleNik">Tampilkan</span>
                                        </button>
                                    @endif
                                </label>
                                @php
                                    $defaultMasked = $user->isMaskNikKk();
                                    $displayNik = $user->nik ? ($defaultMasked ? $user->masked_nik : $user->nik) : '-';
                                @endphp
                                <input type="text" 
                                       class="form-control wm-input wm-input-readonly" 
                                       id="inputNikValue"
                                       readonly 
                                       value="{{ $displayNik }}"
                                       data-real="{{ $user->nik ?: '-' }}"
                                       data-masked="{{ $user->masked_nik ?: '-' }}"
                                       data-is-masked="{{ $defaultMasked ? '1' : '0' }}">
                            </div>

                            {{-- No. KK --}}
                            <div class="col-12 col-md-6">
                                <label class="wm-label d-flex justify-content-between align-items-center">
                                    <span>Nomor Kartu Keluarga (No. KK)</span>
                                    @if($user->no_kk)
                                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none small text-primary" onclick="toggleKkMask()" id="btnToggleKk">
                                            <i data-lucide="eye" style="width: 13px; height: 13px;" id="iconToggleKk"></i>
                                            <span id="textToggleKk">Tampilkan</span>
                                        </button>
                                    @endif
                                </label>
                                @php
                                    $displayKk = $user->no_kk ? ($defaultMasked ? $user->masked_no_kk : $user->no_kk) : '-';
                                @endphp
                                <input type="text" 
                                       class="form-control wm-input wm-input-readonly" 
                                       id="inputKkValue"
                                       readonly 
                                       value="{{ $displayKk }}"
                                       data-real="{{ $user->no_kk ?: '-' }}"
                                       data-masked="{{ $user->masked_no_kk ?: '-' }}"
                                       data-is-masked="{{ $defaultMasked ? '1' : '0' }}">
                            </div>

                            {{-- Wilayah RT --}}
                            <div class="col-12 col-md-6">
                                <label class="wm-label">Wilayah RT</label>
                                <input type="text" class="form-control wm-input wm-input-readonly" readonly value="{{ $user->clean_rt_label ?: '-' }}">
                            </div>

                            {{-- Wilayah RW --}}
                            <div class="col-12 col-md-6">
                                <label class="wm-label">Wilayah RW</label>
                                <input type="text" class="form-control wm-input wm-input-readonly" readonly value="RW {{ $user->rw_number ? str_pad($user->rw_number, 3, '0', STR_PAD_LEFT) : '013' }} Kelurahan Sukamiskin">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 text-end">
                        <button type="submit" class="btn-warkas-primary">
                            <i data-lucide="save" style="width: 16px; height: 16px;"></i>
                            <span>Simpan Perubahan Pribadi</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- ==============================================================
                 TAB 2: INFORMASI AKUN
                 ============================================================== --}}
            <div class="wm-profile-box tab-content-panel {{ $activeTab === 'akun' ? '' : 'd-none' }}" id="panel-akun">
                <div class="panel-header-title">
                    <i data-lucide="user-check" style="width: 20px; height: 20px; color: var(--wm-primary);"></i>
                    <span>Informasi Akun & Login</span>
                </div>
                <div class="panel-header-desc">
                    Kelola alamat email utama yang digunakan untuk masuk ke sistem Haven dan tinjau status keanggotaan Anda.
                </div>

                {{-- Form Ubah Email --}}
                <div class="p-4 rounded-3 mb-4" style="background: #F8FAFD; border: 1px solid #E2E8F0;">
                    <h6 class="fw-bold mb-3 small" style="color: var(--wm-primary-dark);">
                        <i data-lucide="mail" style="width: 16px; height: 16px; vertical-align: middle;" class="me-1"></i>
                        Ubah Email Login
                    </h6>

                    <form action="{{ route('profile.email.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="wm-label">Alamat Email Baru <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control wm-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required placeholder="email@domain.com">
                                @error('email')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="wm-label">Password Saat Ini <span class="text-danger">*</span></label>
                                <input type="password" name="current_password" class="form-control wm-input @error('current_password') is-invalid @enderror" required placeholder="Konfirmasi password login saat ini">
                                <span class="text-muted" style="font-size: 0.75rem;">Diperlukan demi keamanan sebelum email diubah.</span>
                                @error('current_password')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 text-end pt-1">
                                <button type="submit" class="btn-warkas-primary">
                                    <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                                    <span>Perbarui Email Login</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Ringkasan Status Akun & Keanggotaan --}}
                <h6 class="fw-bold mb-3 small" style="color: var(--wm-text-dark);">Detail Hak Akses & Status Akun</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle small mb-0">
                        <tbody>
                            <tr>
                                <th class="bg-light text-muted" style="width: 32%;">Peran / Jabatan Sistem</th>
                                <td class="fw-semibold">
                                    @if($user->role === 'super_admin')
                                        <span class="badge bg-dark">Super Administrator</span> (Akses Penuh Manajemen Sistem & Audit)
                                    @elseif($user->isRw())
                                        <span class="badge bg-success">Ketua RW 013</span> (Pengawasan Seluruh RT & Kas Induk RW)
                                    @elseif($user->isRt())
                                        <span class="badge bg-primary">Ketua {{ $user->clean_rt_label }}</span> (Administrasi Warga & Kas RT)
                                    @elseif($user->isBendahara())
                                        <span class="badge bg-warning text-dark">Bendahara RW</span> (Rekap Kas & Keuangan RW)
                                    @else
                                        <span class="badge bg-info text-dark">Warga</span> (Layanan Mandiri Warga)
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Status Akun</th>
                                <td>
                                    <span class="badge bg-success-subtle text-success px-2 py-1 fw-bold">
                                        <i data-lucide="check-circle" style="width: 12px; height: 12px; vertical-align: middle;"></i>
                                        {{ $user->is_active ? 'Aktif' : 'Aktif Terverifikasi' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Tanggal Terdaftar</th>
                                <td>{{ $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Ringkasan Kartu Keluarga (Jika Terhubung) --}}
                <h6 class="fw-bold mb-3 small" style="color: var(--wm-text-dark);">
                    <i data-lucide="users" style="width: 16px; height: 16px; vertical-align: middle;" class="text-primary me-1"></i>
                    Ringkasan Kartu Keluarga Terhubung
                </h6>

                @if($kartuKeluarga)
                    <div class="p-3 rounded-3 border" style="background: #FAFCFF;">
                        <div class="row g-3 small">
                            <div class="col-12 col-md-6">
                                <span class="text-muted d-block">Nomor KK:</span>
                                <strong class="fs-6" style="color: var(--wm-primary);">{{ $kartuKeluarga->no_kk }}</strong>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="text-muted d-block">Kepala Keluarga:</span>
                                <strong>{{ $kartuKeluarga->nama_kepala_keluarga ?: '-' }}</strong>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="text-muted d-block">Jumlah Jiwa Terdaftar:</span>
                                <span class="badge bg-primary-subtle text-primary fw-bold">{{ $kartuKeluarga->anggota ? $kartuKeluarga->anggota->count() : 1 }} Jiwa</span>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="text-muted d-block">Status Verifikasi Dukcapil:</span>
                                <span class="badge bg-success-subtle text-success">{{ $kartuKeluarga->status_verifikasi_dukcapil ?: 'Terdaftar' }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-3 rounded-3 bg-light text-muted small text-center">
                        <i data-lucide="info" style="width: 18px; height: 18px; vertical-align: middle;" class="me-1"></i>
                        Akun ini belum ditautkan ke arsip Kartu Keluarga digital (atau merupakan akun administrator sistem).
                    </div>
                @endif
            </div>

            {{-- ==============================================================
                 TAB 3: KEAMANAN & SESI
                 ============================================================== --}}
            <div class="wm-profile-box tab-content-panel {{ $activeTab === 'keamanan' ? '' : 'd-none' }}" id="panel-keamanan">
                <div class="panel-header-title">
                    <i data-lucide="shield-check" style="width: 20px; height: 20px; color: var(--wm-primary);"></i>
                    <span>Keamanan Akun & Manajemen Sesi</span>
                </div>
                <div class="panel-header-desc">
                    Amankan akun Anda dengan kata sandi kuat dan pantau sesi perangkat yang saat ini sedang aktif masuk ke akun Anda.
                </div>

                {{-- Form Ubah Password --}}
                <div class="p-4 rounded-3 mb-4" style="background: #F8FAFD; border: 1px solid #E2E8F0;">
                    <h6 class="fw-bold mb-3 small" style="color: var(--wm-primary-dark);">
                        <i data-lucide="key" style="width: 16px; height: 16px; vertical-align: middle;" class="me-1"></i>
                        Perbarui Kata Sandi
                    </h6>

                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="wm-label">Password Saat Ini <span class="text-danger">*</span></label>
                                <input type="password" name="current_password" class="form-control wm-input @error('current_password') is-invalid @enderror" required placeholder="Masukkan password saat ini">
                                @error('current_password')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="wm-label">Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control wm-input @error('password') is-invalid @enderror" required minlength="8" placeholder="Minimal 8 karakter">
                                @error('password')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="wm-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control wm-input" required minlength="8" placeholder="Ulangi password baru">
                            </div>

                            <div class="col-12 text-end pt-2">
                                <button type="submit" class="btn-warkas-primary">
                                    <i data-lucide="shield" style="width: 16px; height: 16px;"></i>
                                    <span>Perbarui Password</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Sesi Aktif Saat Ini --}}
                <h6 class="fw-bold mb-3 small" style="color: var(--wm-text-dark);">
                    <i data-lucide="monitor" style="width: 16px; height: 16px; vertical-align: middle;" class="text-primary me-1"></i>
                    Sesi Aktif Saat Ini
                </h6>

                <div class="p-3 rounded-3 border mb-3" style="background: #FFFFFF;">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 rounded-3" style="background: #EAF3FF; color: var(--wm-primary);">
                                <i data-lucide="laptop" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <strong class="small" style="color: var(--wm-text-dark);">{{ $sessionInfo['device'] }}</strong>
                                    <span class="badge bg-success-subtle text-success px-2 py-0 fw-bold" style="font-size: 0.72rem;">Sesi Ini</span>
                                </div>
                                <div class="text-muted small mt-1">
                                    <span>IP: <code>{{ $sessionInfo['ip'] }}</code></span> • 
                                    <span>Aktif Terakhir: {{ $sessionInfo['last_active'] }}</span>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1">
                                <i data-lucide="log-out" style="width: 14px; height: 14px;"></i>
                                <span>Keluar (Logout)</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="info-notice-box mb-0">
                    <i data-lucide="info" style="width: 18px; height: 18px; flex-shrink: 0; margin-top: 2px;"></i>
                    <div style="font-size: 0.82rem;">
                        <strong>Tips Keamanan:</strong> Jangan berikan password atau kode kredensial Anda kepada siapapun. Jika Anda merasa login dari perangkat tidak dikenal, segera perbarui kata sandi Anda.
                    </div>
                </div>
            </div>

            {{-- ==============================================================
                 TAB 4: PREFERENSI NOTIFIKASI
                 ============================================================== --}}
            <div class="wm-profile-box tab-content-panel {{ $activeTab === 'notifikasi' ? '' : 'd-none' }}" id="panel-notifikasi">
                <div class="panel-header-title">
                    <i data-lucide="bell" style="width: 20px; height: 20px; color: var(--wm-primary);"></i>
                    <span>Preferensi Notifikasi Sistem</span>
                </div>
                <div class="panel-header-desc">
                    Atur jenis pemberitahuan sistem dan layanan yang ingin Anda terima melalui dashboard Haven.
                </div>

                <form action="{{ route('profile.notifications.update') }}" method="POST">
                    @csrf

                    <div class="d-flex flex-column gap-3 mb-4">
                        {{-- 1. Pengingat Tagihan Iuran --}}
                        <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between gap-3" style="background: #FFFFFF;">
                            <div>
                                <strong class="small d-block mb-1" style="color: var(--wm-text-dark);">
                                    <i data-lucide="receipt" style="width: 15px; height: 15px; vertical-align: middle;" class="text-primary me-1"></i>
                                    Pengingat Jatuh Tempo Tagihan Iuran Bulanan
                                </strong>
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    Dapatkan notifikasi sistem saat tagihan iuran bulanan warga telah diterbitkan dan mendekati masa jatuh tempo.
                                </span>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="notif_tagihan" value="1" id="switchTagihan" {{ $user->getNotificationPreference('notif_tagihan', true) ? 'checked' : '' }}>
                            </div>
                        </div>

                        {{-- 2. Status Layanan Surat & Pengaduan --}}
                        <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between gap-3" style="background: #FFFFFF;">
                            <div>
                                <strong class="small d-block mb-1" style="color: var(--wm-text-dark);">
                                    <i data-lucide="file-check" style="width: 15px; height: 15px; vertical-align: middle;" class="text-success me-1"></i>
                                    Update Status Layanan Surat & Pengaduan Warga
                                </strong>
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    Notifikasi perkembangan status pengajuan surat pengantar dan laporan aspirasi yang sedang diproses.
                                </span>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="notif_layanan" value="1" id="switchLayanan" {{ $user->getNotificationPreference('notif_layanan', true) ? 'checked' : '' }}>
                            </div>
                        </div>

                        {{-- 3. Pengumuman RW & Agenda Kegiatan --}}
                        <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between gap-3" style="background: #FFFFFF;">
                            <div>
                                <strong class="small d-block mb-1" style="color: var(--wm-text-dark);">
                                    <i data-lucide="megaphone" style="width: 15px; height: 15px; vertical-align: middle;" class="text-warning me-1"></i>
                                    Pengumuman RW & Agenda Kegiatan Lingkungan
                                </strong>
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    Pemberitahuan surat edaran RW 013, jadwal posyandu balita & lansia, jadwal pos ronda, dan kerja bakti warga.
                                </span>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="notif_kegiatan" value="1" id="switchKegiatan" {{ $user->getNotificationPreference('notif_kegiatan', true) ? 'checked' : '' }}>
                            </div>
                        </div>

                        {{-- Khusus Peran RT / RW / Administrator --}}
                        @if($user->isRt() || $user->isRw() || in_array($user->role, ['super_admin', 'admin', 'bendahara']))
                            <div class="pt-2">
                                <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1 mb-2" style="font-size: 0.72rem;">
                                    KHUSUS PENGURUS RT / RW
                                </span>
                            </div>

                            {{-- 4. Notifikasi Konfirmasi Pembayaran Baru --}}
                            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between gap-3" style="background: #FAFCFF; border-color: #C2DCFF !important;">
                                <div>
                                    <strong class="small d-block mb-1" style="color: var(--wm-primary-dark);">
                                        <i data-lucide="wallet" style="width: 15px; height: 15px; vertical-align: middle;" class="text-primary me-1"></i>
                                        Notifikasi Konfirmasi Pembayaran Baru
                                    </strong>
                                    <span class="text-muted" style="font-size: 0.8rem;">
                                        Pemberitahuan saat warga mengunggah bukti bayar iuran atau transaksi baru terkonfirmasi oleh kasir kas.
                                    </span>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="notif_konfirmasi_bayar" value="1" id="switchBayar" {{ $user->getNotificationPreference('notif_konfirmasi_bayar', true) ? 'checked' : '' }}>
                                </div>
                            </div>

                            {{-- 5. Surat Menunggu Verifikasi --}}
                            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between gap-3" style="background: #FAFCFF; border-color: #C2DCFF !important;">
                                <div>
                                    <strong class="small d-block mb-1" style="color: var(--wm-primary-dark);">
                                        <i data-lucide="clock" style="width: 15px; height: 15px; vertical-align: middle;" class="text-primary me-1"></i>
                                        Surat Permohonan Menunggu Verifikasi
                                    </strong>
                                    <span class="text-muted" style="font-size: 0.8rem;">
                                        Pemberitahuan saat terdapat permohonan surat baru dari warga yang membutuhkan tandatangan / persetujuan Anda.
                                    </span>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="notif_verifikasi_surat" value="1" id="switchSurat" {{ $user->getNotificationPreference('notif_verifikasi_surat', true) ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn-warkas-primary">
                            <i data-lucide="save" style="width: 16px; height: 16px;"></i>
                            <span>Simpan Preferensi Notifikasi</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- ==============================================================
                 TAB 5: AKTIVITAS SAYA
                 ============================================================== --}}
            <div class="wm-profile-box tab-content-panel {{ $activeTab === 'aktivitas' ? '' : 'd-none' }}" id="panel-aktivitas">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="panel-header-title m-0">
                        <i data-lucide="activity" style="width: 20px; height: 20px; color: var(--wm-primary);"></i>
                        <span>Riwayat Aktivitas Saya</span>
                    </div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold">
                        {{ $totalAktivitas }} Aktivitas Terdata
                    </span>
                </div>
                <div class="panel-header-desc">
                    Log riwayat transaksi pembayaran iuran, permohonan surat, dan laporan pengaduan yang telah Anda kirimkan.
                </div>

                @if($activities->count() > 0)
                    <div class="activity-timeline">
                        @foreach($activities as $act)
                            <div class="activity-item">
                                <div class="activity-dot" style="border-color: {{ $act['type'] === 'surat' ? '#1456A0' : ($act['type'] === 'iuran' ? '#16865D' : '#D97706') }};">
                                    <i data-lucide="{{ $act['type'] === 'surat' ? 'file-text' : ($act['type'] === 'iuran' ? 'receipt' : 'message-square') }}" style="width: 11px; height: 11px; color: {{ $act['type'] === 'surat' ? '#1456A0' : ($act['type'] === 'iuran' ? '#16865D' : '#D97706') }};"></i>
                                </div>
                                <div class="p-3 rounded-3 border bg-white shadow-xs">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-1 mb-1">
                                        <strong class="small" style="color: var(--wm-text-dark);">{{ $act['title'] }}</strong>
                                        <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.7rem;">
                                            {{ $act['date'] ? \Carbon\Carbon::parse($act['date'])->translatedFormat('d M Y, H:i') : '-' }}
                                        </span>
                                    </div>
                                    <p class="text-muted mb-2 small" style="font-size: 0.8rem;">{{ $act['desc'] }}</p>
                                    <div>
                                        <span class="badge rounded-pill px-2 py-0 fw-semibold" style="font-size: 0.7rem; background: #EAF3FF; color: #1456A0;">
                                            Status: {{ ucfirst($act['status']) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mx-auto mb-3 p-3 rounded-circle bg-light d-inline-flex text-muted">
                            <i data-lucide="inbox" style="width: 38px; height: 38px;"></i>
                        </div>
                        <h6 class="fw-bold mb-1" style="color: var(--wm-text-dark);">Belum Ada Riwayat Aktivitas</h6>
                        <p class="text-muted small mb-0" style="max-width: 420px; margin: 0 auto;">
                            Aktivitas pengajuan surat pengantar, pembayaran iuran kas keluarga, dan pengaduan Anda akan otomatis dicatat di sini.
                        </p>
                    </div>
                @endif
            </div>

            {{-- ==============================================================
                 TAB 6: PENGATURAN & PRIVASI
                 ============================================================== --}}
            <div class="wm-profile-box tab-content-panel {{ $activeTab === 'privasi' ? '' : 'd-none' }}" id="panel-privasi">
                <div class="panel-header-title">
                    <i data-lucide="lock" style="width: 20px; height: 20px; color: var(--wm-primary);"></i>
                    <span>Pengaturan Privasi & Keamanan Data</span>
                </div>
                <div class="panel-header-desc">
                    Sesuaikan kendali visibilitas data identitas dan informasi kontak Anda sesuai preferensi keamanan pribadi.
                </div>

                <form action="{{ route('profile.privacy.update') }}" method="POST">
                    @csrf

                    <div class="d-flex flex-column gap-3 mb-4">
                        {{-- 1. Toggle Samarkan NIK & No. KK Secara Default --}}
                        <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between gap-3" style="background: #FFFFFF;">
                            <div>
                                <strong class="small d-block mb-1" style="color: var(--wm-text-dark);">
                                    <i data-lucide="eye-off" style="width: 15px; height: 15px; vertical-align: middle;" class="text-primary me-1"></i>
                                    Samarkan NIK & No. KK Secara Default
                                </strong>
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    Jika diaktifkan, nomor NIK dan No. KK pada formulir profil akan otomatis disamarkan (contoh: <code>320101******0252</code>) saat halaman dibuka demi perlindungan privasi layar.
                                </span>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="mask_nik_kk" value="1" id="switchMaskNikKk" {{ $user->isMaskNikKk() ? 'checked' : '' }}>
                            </div>
                        </div>

                        {{-- 2. Visibilitas Nomor HP / WhatsApp --}}
                        <div class="p-3 rounded-3 border" style="background: #FFFFFF;">
                            <div class="mb-2">
                                <strong class="small d-block mb-1" style="color: var(--wm-text-dark);">
                                    <i data-lucide="phone-call" style="width: 15px; height: 15px; vertical-align: middle;" class="text-success me-1"></i>
                                    Visibilitas Nomor HP / WhatsApp
                                </strong>
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    Tentukan siapa saja yang diizinkan melihat kontak telepon Anda pada direktori warga atau etalase UMKM warga.
                                </span>
                            </div>

                            @php $currPhoneVis = $user->getPhoneVisibility(); @endphp
                            <div class="d-flex flex-column gap-2 mt-3 ps-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="phone_visibility" id="visInternal" value="internal" {{ $currPhoneVis === 'internal' ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="visInternal" style="color: var(--wm-text-dark);">
                                        Hanya Terlihat oleh Pengurus RT & RW <span class="badge bg-primary-subtle text-primary ms-1">Direkomendasikan</span>
                                        <div class="text-muted fw-normal" style="font-size: 0.77rem;">Nomor telepon hanya dapat diakses oleh Ketua RT dan RW untuk keperluan kedaruratan atau administrasi.</div>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="phone_visibility" id="visPublic" value="public" {{ $currPhoneVis === 'public' ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="visPublic" style="color: var(--wm-text-dark);">
                                        Terlihat di Direktori & Katalog UMKM Warga
                                        <div class="text-muted fw-normal" style="font-size: 0.77rem;">Nomor telepon dapat dilihat oleh sesama warga terdaftar untuk mempermudah komunikasi usaha dan sosial.</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kebijakan Privasi Ringkas --}}
                    <div class="p-3 rounded-3 border mb-4" style="background: #F8FAFD;">
                        <h6 class="fw-bold mb-2 small" style="color: var(--wm-primary-dark);">
                            <i data-lucide="info" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i>
                            Kebijakan Perlindungan Data Kependudukan RW 013 Sukamiskin
                        </h6>
                        <ul class="text-muted small mb-0 ps-3" style="font-size: 0.79rem; line-height: 1.5;">
                            <li>Seluruh berkas dokumen KTP dan KK disimpan di penyimpanan terproteksi dan hanya digunakan untuk verifikasi administrasi kependudukan.</li>
                            <li>Sistem Haven tunduk pada UU Pelindungan Data Pribadi (UU PDP No. 27 Tahun 2022).</li>
                            <li>Data kependudukan tidak pernah dipindahtangankan kepada pihak ketiga di luar kepengurusan RT/RW.</li>
                        </ul>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn-warkas-primary">
                            <i data-lucide="save" style="width: 16px; height: 16px;"></i>
                            <span>Simpan Pengaturan Privasi</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- MODAL REKAM e-KTP PROFIL SAYA --}}
<div class="modal fade" id="modalProfileRekamEktp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-start" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header p-3.5 text-white" style="background: linear-gradient(135deg, #1456A0 0%, #2878D0 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="radio" style="width: 18px; height: 18px;"></i>
                    <h6 class="modal-title fw-bold mb-0">Tautkan Chip e-KTP / Kartu RFID</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="alert alert-info border-0 rounded-3 mb-3 p-3 d-flex gap-2.5">
                    <i data-lucide="shield-check" class="text-primary flex-shrink-0" style="width: 20px; height: 20px; margin-top: 2px;"></i>
                    <div class="small text-dark">
                        <strong>Kepatuhan Privasi:</strong> Sistem hanya membaca serial UID chip publik fisik kartu (4-byte / 7-byte). Sistem sama sekali <u>tidak</u> membaca data biometrik/terenkripsi Kemendagri.
                    </div>
                </div>

                @if($user->isSuperAdmin() || $user->isRw() || $user->isRt())
                    {{-- Opsi 1: Pindai via Reader ESP32 --}}
                    <div class="p-3 rounded-3 bg-white border mb-3">
                        <h6 class="fw-bold text-dark small mb-1 d-flex align-items-center gap-1.5">
                            <i data-lucide="scan" style="width: 15px; height: 15px; color: #D97706;"></i>
                            Opsi 1: Pindai Otomatis via Reader ESP32
                        </h6>
                        <p class="text-muted small mb-2" style="font-size: 0.76rem;">
                            Aktifkan mode pendaftaran pada reader gerbang GATE-01, lalu tempelkan e-KTP fisik ke reader.
                        </p>
                        <form action="{{ route('ronda.portal.set-mode') }}" method="POST">
                            @csrf
                            <input type="hidden" name="mode" value="register">
                            <input type="hidden" name="target_user_id" value="{{ $user->id }}">
                            <input type="hidden" name="redirect_to" value="portal">
                            <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3">
                                <i data-lucide="play" style="width: 13px; height: 13px; vertical-align: -1px;"></i> Aktifkan Scanner ESP32
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Opsi 2: Simpan UID Manual Langsung --}}
                <div class="p-3 rounded-3 bg-white border">
                    <h6 class="fw-bold text-dark small mb-1 d-flex align-items-center gap-1.5">
                        <i data-lucide="keyboard" style="width: 15px; height: 15px; color: #1456A0;"></i>
                        Input UID Manual (4-byte / 7-byte)
                    </h6>
                    <p class="text-muted small mb-2" style="font-size: 0.76rem;">
                        Masukkan nomor UID chip kartu (14 digit e-KTP fisik atau 8 digit kartu RFID):
                    </p>
                    <form action="{{ route('ronda.portal.manual-register') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <div class="input-group input-group-sm mb-1">
                            <input type="text" name="rfid_uid" class="form-control font-monospace text-uppercase" placeholder="Contoh: 04A1B2C3D4E5F6" value="{{ $user->rfid_uid }}" required>
                            <button type="submit" class="btn btn-primary fw-bold" style="background: #1456A0;">
                                <i data-lucide="save" style="width: 13px; height: 13px; vertical-align: -1px;"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer p-2.5 bg-white border-top">
                <button type="button" class="btn btn-light rounded-pill px-3 py-1 small" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab Switcher dengan History State (Anti Error & Mendukung Refresh)
    function switchProfileTab(tabName) {
        // 1. Sembunyikan semua tab panel
        const panels = document.querySelectorAll('.tab-content-panel');
        panels.forEach(p => p.classList.add('d-none'));

        // 2. Tampilkan panel yang dipilih
        const activePanel = document.getElementById('panel-' + tabName);
        if (activePanel) {
            activePanel.classList.remove('d-none');
        }

        // 3. Update styling tombol menu vertikal
        const navBtns = document.querySelectorAll('.profile-nav-btn');
        navBtns.forEach(btn => {
            btn.classList.remove('active');
            btn.setAttribute('aria-selected', 'false');
        });

        const targetBtn = document.getElementById('btn-tab-' + tabName);
        if (targetBtn) {
            targetBtn.classList.add('active');
            targetBtn.setAttribute('aria-selected', 'true');
        }

        // 4. Update query string tanpa reload halaman
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('tab', tabName);
        window.history.replaceState({ tab: tabName }, '', newUrl.toString());

        // Refresh Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Toggle Masking NIK
    function toggleNikMask() {
        const input = document.getElementById('inputNikValue');
        const icon = document.getElementById('iconToggleNik');
        const text = document.getElementById('textToggleNik');
        if (!input) return;

        const isCurrentlyMasked = input.getAttribute('data-is-masked') === '1';
        if (isCurrentlyMasked) {
            input.value = input.getAttribute('data-real');
            input.setAttribute('data-is-masked', '0');
            if (text) text.innerText = 'Sembunyikan';
            if (icon) icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.value = input.getAttribute('data-masked');
            input.setAttribute('data-is-masked', '1');
            if (text) text.innerText = 'Tampilkan';
            if (icon) icon.setAttribute('data-lucide', 'eye');
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Toggle Masking No. KK
    function toggleKkMask() {
        const input = document.getElementById('inputKkValue');
        const icon = document.getElementById('iconToggleKk');
        const text = document.getElementById('textToggleKk');
        if (!input) return;

        const isCurrentlyMasked = input.getAttribute('data-is-masked') === '1';
        if (isCurrentlyMasked) {
            input.value = input.getAttribute('data-real');
            input.setAttribute('data-is-masked', '0');
            if (text) text.innerText = 'Sembunyikan';
            if (icon) icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.value = input.getAttribute('data-masked');
            input.setAttribute('data-is-masked', '1');
            if (text) text.innerText = 'Tampilkan';
            if (icon) icon.setAttribute('data-lucide', 'eye');
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Inisialisasi ikon saat pertama kali load
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
