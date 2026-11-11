@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- ======================================================================
         PAGE HEADER & BREADCRUMBS (SESUAI MOCKUP WARKAS MACHI)
         ====================================================================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-profile-header-icon shadow-sm">
                <i data-lucide="user-cog" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--wm-text, #172B4D);">Edit Profil</h3>
                <p class="text-muted mb-0 small">Kelola informasi profil, data akun, dan pengaturan role Anda</p>
            </div>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0 align-items-center small">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted d-inline-flex align-items-center gap-1">
                        <i data-lucide="home" style="width: 14px; height: 14px;"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="breadcrumb-item active fw-semibold" aria-current="page" style="color: var(--wm-primary, #1456A0);">
                    Edit Profil
                </li>
            </ol>
        </nav>
    </div>

    {{-- Alert Berhasil / Gagal --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert" style="background: #E3F5EC; color: #16865D; border-radius: 14px;">
            <i data-lucide="check-circle-2" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-medium small">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert" style="background: #FEE2E2; color: #DC2626; border-radius: 14px;">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-medium small">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Tersembunyi untuk Quick Upload Foto Profil --}}
    <form action="{{ route('profile.upload-foto') }}" method="POST" enctype="multipart/form-data" id="formFotoProfilQuick" class="d-none">
        @csrf
        <input type="file" name="foto_profil" id="quickFotoInput" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="submitQuickFoto(this)">
    </form>

    {{-- ======================================================================
         LAYOUT DUA KOLOM (SPLIT LAYOUT DESKTOP: 30% KIRI / 70% KANAN)
         ====================================================================== --}}
    <div class="row g-4">
        {{-- ==================================================================
             KOLOM KIRI: CARD PROFIL PENGGUNA (~30% DESKTOP)
             ================================================================== --}}
        <div class="col-12 col-lg-4 col-xl-4">
            <div class="wm-card wm-profile-card position-relative overflow-hidden shadow-sm">
                {{-- Decorative Curved Wave Header Background --}}
                <div class="wm-card-decorative-banner">
                    <svg viewBox="0 0 500 150" preserveAspectRatio="none" style="height: 100%; width: 100%;">
                        <path d="M0,0 L500,0 L500,80 Q250,140 0,80 Z" fill="#EAF3FF" opacity="0.9"></path>
                    </svg>
                </div>

                {{-- Card Header: Title & Active Role Badge --}}
                <div class="d-flex align-items-center justify-content-between mb-3 position-relative z-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="wm-icon-pill">
                            <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                        </div>
                        <span class="fw-bold fs-6" style="color: var(--wm-text, #172B4D);">Profil Pengguna</span>
                    </div>

                    @if($user->isRw())
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #16865D; color: #FFFFFF; font-size: 0.75rem;">
                            Ketua RW
                        </span>
                    @elseif($user->isRt())
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #1456A0; color: #FFFFFF; font-size: 0.75rem;">
                            Ketua {{ $user->clean_rt_label }}
                        </span>
                    @elseif($user->isBendahara())
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #D97706; color: #FFFFFF; font-size: 0.75rem;">
                            Bendahara RW
                        </span>
                    @else
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #2878D0; color: #FFFFFF; font-size: 0.75rem;">
                            Warga
                        </span>
                    @endif
                </div>

                {{-- Avatar & Identity --}}
                <div class="text-center position-relative z-1 pt-2 pb-1">
                    <div class="wm-avatar-wrapper position-relative d-inline-block mx-auto">
                        @php
                            $avatarUrl = $user->foto_profil 
                                ? asset('storage/' . $user->foto_profil) 
                                : ($user->profile_photo 
                                    ? asset('storage/' . $user->profile_photo) 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1456A0&color=FFFFFF&bold=true');
                        @endphp
                        <img src="{{ $avatarUrl }}"
                             alt="{{ $user->name }}" 
                             id="previewAvatarImg"
                             class="rounded-circle shadow-sm"
                             style="width: 112px; height: 112px; object-fit: cover; border: 4px solid #FFFFFF;">
                        
                        {{-- Camera Float Badge --}}
                        <label for="quickFotoInput" class="wm-camera-badge" title="Klik untuk ubah foto profil" style="cursor: pointer;">
                            <i data-lucide="camera" style="width: 15px; height: 15px;"></i>
                        </label>
                    </div>

                    {{-- Tombol Ubah Foto --}}
                    <div class="mt-3">
                        <label for="quickFotoInput" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-2 wm-btn-photo" style="cursor: pointer;">
                            <i data-lucide="camera" style="width: 14px; height: 14px;"></i>
                            <span id="photoBtnText">Ubah Foto</span>
                            <span id="photoBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </label>
                    </div>

                    {{-- Nama Pengguna --}}
                    <h4 class="fw-bold mt-3 mb-1" style="color: var(--wm-text, #172B4D);">{{ $user->name }}</h4>
                    
                    {{-- Subtitle Role --}}
                    <div class="d-inline-flex align-items-center gap-1 fw-semibold small mb-1" style="color: var(--wm-primary, #1456A0);">
                        @if($user->isRw())
                            <i data-lucide="crown" style="width: 15px; height: 15px; color: #D97706;"></i>
                            <span>Ketua RW 013</span>
                        @elseif($user->isRt())
                            <i data-lucide="home" style="width: 15px; height: 15px; color: var(--wm-primary, #1456A0);"></i>
                            <span>Ketua {{ $user->clean_rt_label }}</span>
                        @elseif($user->isBendahara())
                            <i data-lucide="wallet" style="width: 15px; height: 15px; color: #D97706;"></i>
                            <span>Bendahara RW 013</span>
                        @else
                            <i data-lucide="user-check" style="width: 15px; height: 15px; color: #16865D;"></i>
                            <span>Warga {{ $user->clean_rt_label }}</span>
                        @endif
                    </div>

                    {{-- Identitas Wilayah --}}
                    <div class="text-muted small mb-3 d-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="map-pin" style="width: 14px; height: 14px; color: #DC2626;"></i>
                        <span>RW 013 - Kelurahan Sukamiskin ({{ $user->clean_rt_label }})</span>
                    </div>

                    {{-- Box Deskripsi Jabatan & Wewenang --}}
                    <div class="wm-authority-box p-3 rounded-3 text-start small mb-4">
                        <div class="d-flex align-items-start gap-2">
                            <i data-lucide="lightbulb" style="width: 18px; height: 18px; color: var(--wm-primary, #1456A0); flex-shrink: 0; margin-top: 1px;"></i>
                            <span class="lh-sm">
                                @if($user->isRw())
                                    Sebagai <strong>Ketua RW</strong>, Anda memiliki akses untuk mengelola seluruh data RT, monitoring, pengawasan kas, dan koordinasi dengan warga.
                                @elseif($user->isRt())
                                    Sebagai <strong>Ketua RT</strong>, Anda memiliki wewenang mengelola data kependudukan, monitoring kegiatan warga, kas RT, dan verifikasi administrasi di wilayah {{ $user->clean_rt_label }}.
                                @elseif($user->isBendahara())
                                    Sebagai <strong>Bendahara RW</strong>, Anda memiliki wewenang mengelola rekap kas RW, verifikasi pembayaran iuran, dan pembukuan keuangan RW 013.
                                @else
                                    Sebagai <strong>Warga RW 013</strong>, Anda dapat melihat riwayat iuran keluarga, mengajukan surat pengantar, memantau posyandu, dan pengaduan lingkungan.
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- ==========================================================
                         BAGIAN PILIH ROLE / JABATAN (INTEGRATED DI DALAM CARD)
                         ========================================================== --}}
                    <div class="border-top pt-3 text-start">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="wm-icon-pill" style="width: 28px; height: 28px;">
                                    <i data-lucide="user-check" style="width: 15px; height: 15px;"></i>
                                </div>
                                <span class="fw-bold small" style="color: var(--wm-text, #172B4D);">Pilih Role / Jabatan</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-muted p-0" onclick="openRoleModal()" title="Lihat rincian hak akses role">
                                <i data-lucide="info" style="width: 16px; height: 16px;"></i>
                            </button>
                        </div>

                        {{-- 2x2 Grid Role Buttons Sesuai Screenshot --}}
                        <div class="row g-2">
                            {{-- 1. KETUA RW --}}
                            @php $isRwActive = $user->isRw(); @endphp
                            <div class="col-6">
                                <button type="button" 
                                        class="wm-role-btn w-100 {{ $isRwActive ? 'active' : 'inactive' }}" 
                                        onclick="handleRoleClick('rw', 'Ketua RW', {{ $isRwActive ? 'true' : 'false' }})">
                                    <i data-lucide="crown" style="width: 16px; height: 16px;"></i>
                                    <span>Ketua RW</span>
                                </button>
                            </div>

                            {{-- 2. KETUA RT --}}
                            @php $isRtActive = $user->isRt(); @endphp
                            <div class="col-6">
                                <button type="button" 
                                        class="wm-role-btn w-100 {{ $isRtActive ? 'active' : 'inactive' }}" 
                                        onclick="handleRoleClick('rt', 'Ketua RT', {{ $isRtActive ? 'true' : 'false' }})">
                                    <i data-lucide="home" style="width: 16px; height: 16px;"></i>
                                    <span>Ketua RT</span>
                                </button>
                            </div>

                            {{-- 3. WARGA --}}
                            @php $isWargaActive = $user->isWarga(); @endphp
                            <div class="col-6">
                                <button type="button" 
                                        class="wm-role-btn w-100 {{ $isWargaActive ? 'active' : 'inactive' }}" 
                                        onclick="handleRoleClick('warga', 'Warga', {{ $isWargaActive ? 'true' : 'false' }})">
                                    <i data-lucide="users" style="width: 16px; height: 16px;"></i>
                                    <span>Warga</span>
                                </button>
                            </div>

                            {{-- 4. ADMIN --}}
                            @php $isAdminActive = ($user->role === 'admin' || $user->role === 'super_admin'); @endphp
                            <div class="col-6">
                                <button type="button" 
                                        class="wm-role-btn w-100 {{ $isAdminActive ? 'active' : 'inactive' }}" 
                                        onclick="handleRoleClick('admin', 'Admin', {{ $isAdminActive ? 'true' : 'false' }})">
                                    <i data-lucide="settings" style="width: 16px; height: 16px;"></i>
                                    <span>Admin</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================
             KOLOM KANAN: CARD INFORMASI AKUN & DATA DIRI (~70% DESKTOP)
             ================================================================== --}}
        <div class="col-12 col-lg-8 col-xl-8">
            <div class="wm-card wm-account-form-card shadow-sm p-4">
                {{-- Form Header --}}
                <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
                    <div class="wm-icon-pill p-2 rounded-3" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="sliders-horizontal" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: var(--wm-text, #172B4D);">Informasi Akun & Data Diri</h5>
                    </div>
                </div>

                {{-- Formulir Data Diri --}}
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileUpdateForm">
                    @csrf

                    <div class="row g-3">
                        {{-- 1. NAMA LENGKAP --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1" style="color: var(--wm-text, #172B4D);">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text wm-input-icon"><i data-lucide="user" style="width: 16px; height: 16px;"></i></span>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control wm-form-input @error('name') is-invalid @enderror" required placeholder="Nama lengkap">
                            </div>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 2. EMAIL (GUNAKAN UNTUK LOGIN) --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1" style="color: var(--wm-text, #172B4D);">
                                Email (Gunakan untuk Login) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text wm-input-icon"><i data-lucide="mail" style="width: 16px; height: 16px;"></i></span>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control wm-form-input @error('email') is-invalid @enderror" required placeholder="email@domain.com">
                            </div>
                            <div class="d-flex align-items-center gap-1 text-success mt-1" style="font-size: 0.78rem;">
                                <i data-lucide="check-circle" style="width: 13px; height: 13px;"></i>
                                <span class="fw-medium">Email sudah terverifikasi</span>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 3. NIK --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1" style="color: var(--wm-text, #172B4D);">
                                NIK <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text wm-input-icon"><i data-lucide="credit-card" style="width: 16px; height: 16px;"></i></span>
                                <input type="text" name="nik" value="{{ old('nik', $user->nik) }}" maxlength="20" class="form-control wm-form-input @error('nik') is-invalid @enderror" placeholder="3201010505050252">
                            </div>
                            @error('nik')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 4. NO. KK --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1" style="color: var(--wm-text, #172B4D);">
                                No. KK <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text wm-input-icon"><i data-lucide="users" style="width: 16px; height: 16px;"></i></span>
                                <input type="text" name="no_kk" value="{{ old('no_kk', $user->no_kk) }}" maxlength="20" class="form-control wm-form-input @error('no_kk') is-invalid @enderror" placeholder="3201011003030851">
                            </div>
                            @error('no_kk')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 5. NO. RT, NO. RW, NO. RUMAH (ROW 3 KOLOM SESUAI MOCKUP) --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small mb-1" style="color: var(--wm-text, #172B4D);">
                                No. RT <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text wm-input-icon"><i data-lucide="search" style="width: 15px; height: 15px;"></i></span>
                                <select name="rt_number" class="form-select wm-form-input @error('rt_number') is-invalid @enderror">
                                    @for($i = 1; $i <= 8; $i++)
                                        @php $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $rtStr }}" {{ old('rt_number', $user->clean_rt_number ? str_pad($user->clean_rt_number, 3, '0', STR_PAD_LEFT) : '001') == $rtStr ? 'selected' : '' }}>
                                            RT {{ $rtStr }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            @error('rt_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small mb-1" style="color: var(--wm-text, #172B4D);">
                                No. RW <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text wm-input-icon"><i data-lucide="flag" style="width: 15px; height: 15px;"></i></span>
                                <select name="rw_number" class="form-select wm-form-input @error('rw_number') is-invalid @enderror">
                                    <option value="013" {{ old('rw_number', $user->rw_number ?? '013') == '013' ? 'selected' : '' }}>013</option>
                                </select>
                            </div>
                            @error('rw_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small mb-1" style="color: var(--wm-text, #172B4D);">
                                No. Rumah <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text wm-input-icon"><i data-lucide="home" style="width: 15px; height: 15px;"></i></span>
                                <input type="text" name="house_number" value="{{ old('house_number', $user->house_number ?? '001') }}" class="form-control wm-form-input @error('house_number') is-invalid @enderror" placeholder="001">
                            </div>
                            @error('house_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 6. NO. TELEPON / WHATSAPP --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1" style="color: var(--wm-text, #172B4D);">
                                No. Telepon / WhatsApp <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text wm-input-icon"><i data-lucide="phone" style="width: 16px; height: 16px;"></i></span>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control wm-form-input @error('phone') is-invalid @enderror" placeholder="0888192341">
                            </div>
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 7. STATUS KEPEMILIKAN RUMAH --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1" style="color: var(--wm-text, #172B4D);">
                                Status Kepemilikan Rumah <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text wm-input-icon"><i data-lucide="home" style="width: 16px; height: 16px;"></i></span>
                                <select name="status_rumah" class="form-select wm-form-input @error('status_rumah') is-invalid @enderror">
                                    <option value="milik_sendiri" {{ old('status_rumah', $user->status_rumah) == 'milik_sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                                    <option value="sewa_kontrak" {{ old('status_rumah', $user->status_rumah) == 'sewa_kontrak' ? 'selected' : '' }}>Sewa / Kontrak</option>
                                    <option value="menumpang" {{ old('status_rumah', $user->status_rumah) == 'menumpang' ? 'selected' : '' }}>Menumpang</option>
                                </select>
                            </div>
                            @error('status_rumah')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 8. ALAMAT TEMPAT TINGGAL (FULL WIDTH) --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small mb-1" style="color: var(--wm-text, #172B4D);">
                                Alamat Tempat Tinggal <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text wm-input-icon align-items-start pt-2"><i data-lucide="map-pin" style="width: 16px; height: 16px;"></i></span>
                                <textarea name="address" rows="2" class="form-control wm-form-input @error('address') is-invalid @enderror" placeholder="Jl. Sancaka No. 13">{{ old('address', $user->address) }}</textarea>
                            </div>
                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ==========================================================
                         SECTION UBAH PASSWORD LOGIN (SESUAI MOCKUP)
                         ========================================================== --}}
                    <div class="wm-password-section mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="wm-icon-pill p-2 rounded-3" style="background: #EAF3FF; color: #1456A0;">
                                <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                            </div>
                            <h6 class="fw-bold mb-0 fs-6" style="color: var(--wm-text, #172B4D);">Ubah Password Login</h6>
                        </div>
                        <p class="text-muted small mb-3">Kosongkan bagian password jika Anda tidak ingin mengubah password akun Anda.</p>

                        <div class="row g-3">
                            {{-- PASSWORD SAAT INI --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold small mb-1" style="color: var(--wm-text, #172B4D);">Password Saat Ini</label>
                                <div class="input-group">
                                    <span class="input-group-text wm-input-icon"><i data-lucide="lock-keyhole" style="width: 15px; height: 15px;"></i></span>
                                    <input type="password" name="current_password" id="inputCurrentPass" class="form-control wm-form-input @error('current_password') is-invalid @enderror" placeholder="Masukkan password lama">
                                    <button class="btn btn-outline-secondary wm-toggle-pass border-start-0" type="button" onclick="togglePassVisibility('inputCurrentPass', this)" title="Tampilkan/Sembunyikan password">
                                        <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- PASSWORD BARU --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold small mb-1" style="color: var(--wm-text, #172B4D);">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text wm-input-icon"><i data-lucide="lock" style="width: 15px; height: 15px;"></i></span>
                                    <input type="password" name="password" id="inputNewPass" class="form-control wm-form-input @error('password') is-invalid @enderror" placeholder="Min. 8 karakter">
                                    <button class="btn btn-outline-secondary wm-toggle-pass border-start-0" type="button" onclick="togglePassVisibility('inputNewPass', this)" title="Tampilkan/Sembunyikan password">
                                        <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- KONFIRMASI PASSWORD BARU --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold small mb-1" style="color: var(--wm-text, #172B4D);">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text wm-input-icon"><i data-lucide="shield-check" style="width: 15px; height: 15px;"></i></span>
                                    <input type="password" name="password_confirmation" id="inputConfirmPass" class="form-control wm-form-input" placeholder="Ulangi password baru">
                                    <button class="btn btn-outline-secondary wm-toggle-pass border-start-0" type="button" onclick="togglePassVisibility('inputConfirmPass', this)" title="Tampilkan/Sembunyikan password">
                                        <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ==========================================================
                         TOMBOL SIMPAN PERUBAHAN (BOTTOM RIGHT SESUAI MOCKUP)
                         ========================================================== --}}
                    <div class="mt-4 pt-3 d-flex justify-content-end">
                        <button type="submit" id="btnSubmitProfile" class="btn btn-primary rounded-3 px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm wm-btn-save">
                            <i data-lucide="save" id="saveIcon" style="width: 18px; height: 18px;"></i>
                            <span id="saveBtnText">Simpan Perubahan</span>
                            <span id="saveBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==========================================================================
     MODAL PILIH ROLE / JABATAN (SESUAI PREVIEW BOTTOM-RIGHT PADA SCREENSHOT)
     ========================================================================== --}}
<div class="modal fade" id="modalPilihRole" tabindex="-1" aria-labelledby="modalPilihRoleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content wm-modal-card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="wm-icon-pill p-2 rounded-3" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                    </div>
                    <h5 class="modal-title fw-bold" id="modalPilihRoleLabel" style="color: var(--wm-text, #172B4D);">Pilih Role / Jabatan</h5>
                    <span class="text-muted" title="Role akun"><i data-lucide="info" style="width: 16px; height: 16px;"></i></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                {{-- 2x2 Big Role Cards --}}
                <div class="row g-3">
                    {{-- 1. KETUA RW --}}
                    <div class="col-12 col-md-6">
                        <div class="wm-role-detail-card {{ $isRwActive ? 'active' : '' }}" onclick="selectRolePrompt('rw', 'Ketua RW', {{ $isRwActive ? 'true' : 'false' }})">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="wm-role-detail-icon">
                                    <i data-lucide="crown" style="width: 24px; height: 24px;"></i>
                                </div>
                                <div class="wm-role-radio">
                                    <input type="radio" name="modal_role" id="role_rw" {{ $isRwActive ? 'checked' : '' }} disabled>
                                    <label for="role_rw"></label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="fw-bold mb-1 wm-role-title">Ketua RW</h6>
                                <p class="text-muted small mb-0 wm-role-desc">Mengelola seluruh RT dan wilayah RW 013</p>
                            </div>
                        </div>
                    </div>

                    {{-- 2. KETUA RT --}}
                    <div class="col-12 col-md-6">
                        <div class="wm-role-detail-card {{ $isRtActive ? 'active' : '' }}" onclick="selectRolePrompt('rt', 'Ketua RT', {{ $isRtActive ? 'true' : 'false' }})">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="wm-role-detail-icon">
                                    <i data-lucide="home" style="width: 24px; height: 24px;"></i>
                                </div>
                                <div class="wm-role-radio">
                                    <input type="radio" name="modal_role" id="role_rt" {{ $isRtActive ? 'checked' : '' }} disabled>
                                    <label for="role_rt"></label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="fw-bold mb-1 wm-role-title">Ketua RT</h6>
                                <p class="text-muted small mb-0 wm-role-desc">Mengelola data warga dan kegiatan RT</p>
                            </div>
                        </div>
                    </div>

                    {{-- 3. WARGA --}}
                    <div class="col-12 col-md-6">
                        <div class="wm-role-detail-card {{ $isWargaActive ? 'active' : '' }}" onclick="selectRolePrompt('warga', 'Warga', {{ $isWargaActive ? 'true' : 'false' }})">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="wm-role-detail-icon">
                                    <i data-lucide="users" style="width: 24px; height: 24px;"></i>
                                </div>
                                <div class="wm-role-radio">
                                    <input type="radio" name="modal_role" id="role_warga" {{ $isWargaActive ? 'checked' : '' }} disabled>
                                    <label for="role_warga"></label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="fw-bold mb-1 wm-role-title">Warga</h6>
                                <p class="text-muted small mb-0 wm-role-desc">Akses layanan dasar dan informasi</p>
                            </div>
                        </div>
                    </div>

                    {{-- 4. ADMIN --}}
                    <div class="col-12 col-md-6">
                        <div class="wm-role-detail-card {{ $isAdminActive ? 'active' : '' }}" onclick="selectRolePrompt('admin', 'Admin', {{ $isAdminActive ? 'true' : 'false' }})">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="wm-role-detail-icon">
                                    <i data-lucide="settings" style="width: 24px; height: 24px;"></i>
                                </div>
                                <div class="wm-role-radio">
                                    <input type="radio" name="modal_role" id="role_admin" {{ $isAdminActive ? 'checked' : '' }} disabled>
                                    <label for="role_admin"></label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="fw-bold mb-1 wm-role-title">Admin</h6>
                                <p class="text-muted small mb-0 wm-role-desc">Kelola sistem dan pengaturan</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Lightbulb Tip Box --}}
                <div class="mt-4 p-3 rounded-3 d-flex align-items-center gap-2" style="background: #EAF3FF; color: #1456A0; border: 1px solid #D0E4FF;">
                    <i data-lucide="lightbulb" style="width: 18px; height: 18px; flex-shrink: 0;"></i>
                    <span class="small">Pilih role yang sesuai dengan jabatan Anda untuk mendapatkan akses menu yang tepat.</span>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-muted" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ==========================================================================
     CUSTOM STYLES SESUAI MOCKUP & DESIGN SYSTEM WARKAS MACHI
     ========================================================================== --}}
<style>
    .wm-profile-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #EAF3FF;
        color: #1456A0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .wm-card {
        background: var(--wm-card, #FFFFFF);
        border: 1px solid var(--wm-border, #E2E8F0);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .wm-card-decorative-banner {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 120px;
        pointer-events: none;
        overflow: hidden;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }

    .wm-icon-pill {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: #EAF3FF;
        color: #1456A0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .wm-avatar-wrapper {
        width: 112px;
        height: 112px;
    }

    .wm-camera-badge {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #1D68FE;
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #FFFFFF;
        box-shadow: 0 2px 8px rgba(29, 104, 254, 0.35);
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .wm-camera-badge:hover {
        background: #1456A0;
        transform: scale(1.08);
    }

    .wm-btn-photo {
        border-color: #1D68FE;
        color: #1D68FE;
        font-size: 0.82rem;
        background: #FFFFFF;
        padding: 6px 18px;
        transition: all 0.2s ease;
    }

    .wm-btn-photo:hover {
        background: #1D68FE;
        color: #FFFFFF;
        border-color: #1D68FE;
    }

    .wm-authority-box {
        background: #F4F8FE;
        border: 1px solid #DCE7F6;
        color: #1456A0;
    }

    /* 2x2 Role Buttons inside Profil Card */
    .wm-role-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 12px;
        font-size: 0.84rem;
        font-weight: 600;
        transition: all 0.2s ease;
        border: 1px solid var(--wm-border, #E2E8F0);
        cursor: pointer;
    }

    .wm-role-btn.active {
        background: #1D68FE !important;
        border-color: #1D68FE !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 12px rgba(29, 104, 254, 0.28);
    }

    .wm-role-btn.active i {
        color: #FFFFFF !important;
    }

    .wm-role-btn.inactive {
        background: #FFFFFF;
        color: var(--wm-text, #172B4D);
    }

    .wm-role-btn.inactive i {
        color: #1D68FE;
    }

    .wm-role-btn.inactive:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
    }

    /* Modal Big Role Cards */
    .wm-role-detail-card {
        border: 2px solid var(--wm-border, #E2E8F0);
        border-radius: 16px;
        padding: 18px;
        background: #FFFFFF;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .wm-role-detail-card:hover {
        border-color: #1D68FE;
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(29, 104, 254, 0.1);
    }

    .wm-role-detail-card.active {
        border-color: #1D68FE;
        background: #F8FAFF;
    }

    .wm-role-detail-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #EAF3FF;
        color: #1D68FE;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .wm-role-radio input[type="radio"] {
        width: 20px;
        height: 20px;
        accent-color: #1D68FE;
    }

    /* Inputs Styling */
    .wm-input-icon {
        background-color: #F8FAFC;
        border-color: var(--wm-border, #E2E8F0);
        color: #1D68FE;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
        padding: 8px 12px;
    }

    .wm-form-input {
        border-color: var(--wm-border, #E2E8F0);
        padding: 10px 14px;
        font-size: 0.9rem;
        border-radius: 10px;
        color: var(--wm-text, #172B4D);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .input-group .wm-form-input {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .wm-form-input:focus {
        border-color: #1D68FE;
        box-shadow: 0 0 0 3px rgba(29, 104, 254, 0.12);
    }

    .wm-toggle-pass {
        border-color: var(--wm-border, #E2E8F0);
        background: #F8FAFC;
        color: #64748B;
        border-top-right-radius: 10px !important;
        border-bottom-right-radius: 10px !important;
    }

    .wm-toggle-pass:hover {
        background: #E2E8F0;
        color: #1D68FE;
    }

    .wm-btn-save {
        background-color: #1D68FE;
        border-color: #1D68FE;
        font-size: 0.92rem;
        padding: 10px 24px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .wm-btn-save:hover {
        background-color: #1456A0;
        border-color: #1456A0;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(29, 104, 254, 0.3);
    }

    /* Dark Mode Overrides */
    [data-theme="dark"] .wm-card,
    body.dark-mode .wm-card {
        background: var(--wm-card, #16213A);
        border-color: var(--wm-border, #1E293B);
    }

    [data-theme="dark"] .wm-modal-card,
    body.dark-mode .wm-modal-card {
        background: var(--wm-card, #16213A);
        color: var(--wm-text, #F1F5F9);
    }

    [data-theme="dark"] .wm-card-decorative-banner svg path,
    body.dark-mode .wm-card-decorative-banner svg path {
        fill: #1A2A44;
    }

    [data-theme="dark"] .wm-authority-box,
    body.dark-mode .wm-authority-box {
        background: #1A2A44;
        border-color: #243B60;
        color: #93C5FD;
    }

    [data-theme="dark"] .wm-input-icon,
    [data-theme="dark"] .wm-toggle-pass,
    body.dark-mode .wm-input-icon,
    body.dark-mode .wm-toggle-pass {
        background-color: #1E293B;
        border-color: #334155;
        color: #94A3B8;
    }

    [data-theme="dark"] .wm-form-input,
    body.dark-mode .wm-form-input {
        background-color: #0F172A;
        border-color: #334155;
        color: #F1F5F9;
    }

    [data-theme="dark"] .wm-role-btn.inactive,
    body.dark-mode .wm-role-btn.inactive {
        background: #0F172A;
        border-color: #1E293B;
        color: #CBD5E1;
    }

    [data-theme="dark"] .wm-role-detail-card,
    body.dark-mode .wm-role-detail-card {
        background: #0F172A;
        border-color: #1E293B;
        color: #CBD5E1;
    }

    [data-theme="dark"] .wm-role-detail-card.active,
    body.dark-mode .wm-role-detail-card.active {
        background: #1A2A44;
        border-color: #1D68FE;
    }
</style>

{{-- ==========================================================================
     SCRIPTS: INSTANT PHOTO UPLOAD, TOGGLE PASS, ROLE MODAL & FORM SUBMISSION
     ========================================================================== --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    // Buka Modal Detail Role
    function openRoleModal() {
        const modalEl = document.getElementById('modalPilihRole');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    // Toggle Tampil/Sembunyikan Password
    function togglePassVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const icon = btn.querySelector('[data-lucide]');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.setAttribute('data-lucide', 'eye-off');
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.setAttribute('data-lucide', 'eye');
            }
        }
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    // Quick Upload Foto Profil dengan Live Preview
    function submitQuickFoto(input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto terlalu besar. Maksimal 2MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewAvatarImg');
            if (preview) preview.src = e.target.result;
        };
        reader.readAsDataURL(file);

        const btnText = document.getElementById('photoBtnText');
        const btnSpinner = document.getElementById('photoBtnSpinner');
        if (btnText && btnSpinner) {
            btnText.textContent = 'Mengunggah...';
            btnSpinner.classList.remove('d-none');
        }

        document.getElementById('formFotoProfilQuick').submit();
    }

    // Handle Interaksi Klik pada Tombol Role di Card
    function handleRoleClick(roleKey, roleName, isActive) {
        if (isActive) {
            openRoleModal();
        } else {
            selectRolePrompt(roleKey, roleName, false);
        }
    }

    // Notifikasi Otorisasi Role Sesuai Standar Warkas Machi
    function selectRolePrompt(roleKey, roleName, isActive) {
        if (isActive) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Role Aktif: ' + roleName,
                    text: 'Akun Anda saat ini aktif dengan kewenangan sebagai ' + roleName + ' Warkas Machi RW 013.',
                    confirmButtonColor: '#1D68FE',
                    confirmButtonText: 'Tutup'
                });
            } else {
                alert('Role Aktif: Anda sedang aktif sebagai ' + roleName + '.');
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Akses Role Dibatasi',
                    html: '<p class="mb-2">Akun Anda saat ini terdaftar sebagai <strong>{{ $user->role_badge_label }}</strong>.</p>' +
                          '<p class="small text-muted mb-0">Penetapan atau pergantian role resmi hanya dapat dilakukan oleh <strong>Administrator RW 013</strong> demi keamanan data wilayah.</p>',
                    confirmButtonColor: '#1D68FE',
                    confirmButtonText: 'Baik, Saya Paham'
                });
            } else {
                alert('Akses Role Dibatasi: Penetapan atau pergantian role hanya dapat dilakukan resmi oleh Administrator RW 013.');
            }
        }
    }

    // Loading State saat Simpan Formulir Profil
    const profileForm = document.getElementById('profileUpdateForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            const btn = document.getElementById('btnSubmitProfile');
            const saveIcon = document.getElementById('saveIcon');
            const saveBtnText = document.getElementById('saveBtnText');
            const saveBtnSpinner = document.getElementById('saveBtnSpinner');

            if (btn && saveBtnText && saveBtnSpinner) {
                btn.disabled = true;
                if (saveIcon) saveIcon.classList.add('d-none');
                saveBtnSpinner.classList.remove('d-none');
                saveBtnText.textContent = 'Menyimpan...';
            }
        });
    }
</script>
@endsection
