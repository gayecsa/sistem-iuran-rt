<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') | Haven Portal @else{{ $title ?? 'Dashboard' }} | Haven Portal @endif</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (Fallback & Compatibility) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom Theme: Navy Blue Sidebar (#123766), Primary Blue (#1456A0), Welfare Green (#16865D) -->
    <link rel="stylesheet" href="{{ asset('css/warkas-theme.css') }}?v={{ time() }}">

    <!-- Page Specific Styles -->
    @stack('styles')

    <script>
        // Inisialisasi tema sebelum render untuk mencegah flash
        (function () {
            var saved = localStorage.getItem('wm-theme') || localStorage.getItem('warkas_theme');
            if (!saved) {
                saved = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-theme', saved);
            if (saved === 'dark') {
                document.addEventListener('DOMContentLoaded', function () {
                    document.body.classList.add('dark-mode');
                });
            }
            // Check sidebar state
            if (localStorage.getItem('wm-sidebar-collapsed') === 'true' && window.innerWidth >= 992) {
                document.addEventListener('DOMContentLoaded', function () {
                    document.body.classList.add('sidebar-collapsed');
                });
            }
        })();
    </script>
</head>
<body class="{{ request()->cookie('theme') === 'dark' ? 'dark-mode' : '' }}">

    {{-- Backdrop untuk Mobile Drawer --}}
    <div class="wm-sidebar-backdrop" id="sidebarBackdrop" onclick="toggleMobileSidebar()"></div>

    {{-- ======================================================================
         SIDEBAR NAVIGASI VERTIKAL DI SEBELAH KIRI (NAVY BLUE #123766)
         ====================================================================== --}}
    <aside class="wm-sidebar" id="sidebarMenu" aria-label="Navigasi Utama">
        <!-- Sidebar Brand Header -->
        <div class="wm-sidebar-brand">
            <a href="{{ route('dashboard') }}" class="wm-brand-link" title="Haven Portal — Safe Space, Seamless Service">
                <div class="wm-brand-icon" style="background: transparent; box-shadow: none; width: 38px; height: 38px;">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="width: 38px; height: 38px;">
                        <rect width="64" height="64" rx="16" fill="url(#havenGrad)"/>
                        <path d="M16 19L32 10L48 19" stroke="#7DD3FC" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 23V46M44 23V46M20 34H44" stroke="#FFFFFF" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <defs>
                            <linearGradient id="havenGrad" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#1456A0"/>
                                <stop offset="1" stop-color="#2878D0"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="wm-brand-info">
                    <span class="wm-brand-name" style="letter-spacing: -0.3px;">Haven Portal</span>
                    <span class="wm-brand-sub" style="font-size: 0.60rem; letter-spacing: 0.4px; color: #93C5FD; text-transform: uppercase;">Safe Space, Seamless Service</span>
                </div>
            </a>
            <button type="button" class="wm-sidebar-toggle-btn d-none d-lg-flex" id="sidebarCollapseBtn" onclick="toggleSidebarCollapse()" title="Kecilkan/Perluas Menu">
                <i data-lucide="chevrons-left" id="collapseIcon"></i>
            </button>
            <button type="button" class="wm-sidebar-toggle-btn d-lg-none" onclick="toggleMobileSidebar()" title="Tutup Menu">
                <i data-lucide="x"></i>
            </button>
        </div>

        <!-- Sidebar Navigation Groups (Role Separated) -->
        <ul class="wm-sidebar-nav">
            @php
                $currentUser = auth()->user();
                $isImpersonating = session()->has('impersonator_id');
                $isSuperAdmin = $currentUser && ($currentUser->role === 'super_admin' || $isImpersonating);
                $isNativeSuperAdmin = $currentUser && $currentUser->role === 'super_admin' && !$isImpersonating;
                $isRw = $currentUser && ($currentUser->isRw() || in_array($currentUser->role, ['admin', 'bendahara', 'super_admin']));
                $isRt = $currentUser && ($currentUser->isRt() || $currentUser->role === 'rt');
                $allRtUsers = $isSuperAdmin ? \App\Models\User::where('role', 'rt')->orderBy('rt_number')->get() : collect();
                $rwUser = $isSuperAdmin ? \App\Models\User::whereIn('role', ['admin', 'rw'])->where('email', '!=', 'annisasfzahra@gmail.com')->first() : null;

                if (!isset($securityStatus)) {
                    try {
                        $activeDaruratCount = \App\Models\Pengaduan::where('kategori', 'Keamanan & Ketertiban')
                            ->whereNotIn('status', ['Selesai', 'Ditolak'])
                            ->where('deskripsi', 'LIKE', '%[Tingkat Urgensi: Darurat]%')
                            ->count();
                        $activeSegeraCount = \App\Models\Pengaduan::where('kategori', 'Keamanan & Ketertiban')
                            ->whereNotIn('status', ['Selesai', 'Ditolak'])
                            ->where('deskripsi', 'LIKE', '%[Tingkat Urgensi: Segera]%')
                            ->count();
                        $secLevel = 'aman';
                        if ($activeDaruratCount > 0) {
                            $secLevel = 'darurat';
                        } elseif ($activeSegeraCount > 0) {
                            $secLevel = 'segera';
                        }
                        $securityStatus = [
                            'darurat_count' => $activeDaruratCount,
                            'segera_count' => $activeSegeraCount,
                            'total_urgent' => $activeDaruratCount + $activeSegeraCount,
                            'level' => $secLevel,
                        ];
                    } catch (\Throwable $e) {
                        $securityStatus = [
                            'darurat_count' => 0,
                            'segera_count' => 0,
                            'total_urgent' => 0,
                            'level' => 'aman',
                        ];
                    }
                }
            @endphp

            @if($isNativeSuperAdmin)
                {{-- ==========================================================
                     SIDEBAR KHUSUS SUPER ADMIN (CONTROL CENTER HAVEN)
                     ========================================================== --}}
                <li class="wm-nav-header">CONTROL CENTER</li>
                <li class="wm-nav-item">
                    <a href="{{ route('dashboard.super-admin') }}" class="wm-nav-link {{ request()->routeIs('dashboard.super-admin') ? 'active' : '' }}" title="Control Center Super Admin">
                        <span class="wm-nav-icon"><i data-lucide="shield"></i></span>
                        <span class="wm-nav-text">Control Center</span>
                        <span class="wm-nav-badge" style="background:#1456A0;">Utama</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('super-admin.users.index') }}" class="wm-nav-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}" title="Manajemen Pengguna & Role">
                        <span class="wm-nav-icon"><i data-lucide="users"></i></span>
                        <span class="wm-nav-text">Manajemen Pengguna</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('super-admin.master-keuangan.index') }}" class="wm-nav-link {{ request()->routeIs('super-admin.master-keuangan.*') ? 'active' : '' }}" title="Master Rekening Buku Kas & Tarif Iuran">
                        <span class="wm-nav-icon"><i data-lucide="database"></i></span>
                        <span class="wm-nav-text">Master Rekening & Tarif</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('super-admin.audit-log.index') }}" class="wm-nav-link {{ request()->routeIs('super-admin.audit-log.*') ? 'active' : '' }}" title="Log Aktivitas & Audit Trail">
                        <span class="wm-nav-icon"><i data-lucide="history"></i></span>
                        <span class="wm-nav-text">Log Aktivitas</span>
                    </a>
                </li>

                <li class="wm-nav-header">SIMULATOR PERAN</li>
                <li class="wm-nav-item">
                    <a href="javascript:void(0)" class="wm-nav-link" data-bs-toggle="modal" data-bs-target="#modalSwitchRole" title="Switch Role & Simulasi Akses">
                        <span class="wm-nav-icon"><i data-lucide="shuffle"></i></span>
                        <span class="wm-nav-text">Switch Role</span>
                        <span class="wm-nav-badge" style="background:#10B981;">Simulasi</span>
                    </a>
                </li>

                <li class="wm-nav-header">PENGATURAN AKUN</li>
                <li class="wm-nav-item">
                    <a href="{{ route('profile.edit') }}" class="wm-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Profil Akun Super Admin">
                        <span class="wm-nav-icon"><i data-lucide="user-cog"></i></span>
                        <span class="wm-nav-text">Profil Saya</span>
                    </a>
                </li>

            @elseif($isRw)
                {{-- ==========================================================
                     SIDEBAR KHUSUS KETUA RW & PENGURUS RW 013
                     ========================================================== --}}
                <li class="wm-nav-header">DASHBOARD RW</li>
                <li class="wm-nav-item">
                    <a href="{{ route('dashboard.rw') }}" class="wm-nav-link {{ request()->routeIs('dashboard*') && !request()->routeIs('dashboard.rt') && !request()->routeIs('dashboard.warga') ? 'active' : '' }}" title="Dashboard Utama RW">
                        <span class="wm-nav-icon"><i data-lucide="layout-dashboard"></i></span>
                        <span class="wm-nav-text">Ringkasan RW 013</span>
                    </a>
                </li>

                <li class="wm-nav-header">MONITORING & KOORDINASI RT</li>
                <li class="wm-nav-item">
                    <a href="{{ route('data-rt.index') }}" class="wm-nav-link {{ request()->routeIs('data-rt.*') ? 'active' : '' }}" title="Data 8 RT">
                        <span class="wm-nav-icon"><i data-lucide="building-2"></i></span>
                        <span class="wm-nav-text">Data RT (RT 001-008)</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('monitoring-rt.index') }}" class="wm-nav-link {{ request()->routeIs('monitoring-rt.*') || request()->routeIs('laporan-rt.*') ? 'active' : '' }}" title="Pusat Monitoring Terpadu RT 001-008">
                        <span class="wm-nav-icon"><i data-lucide="activity"></i></span>
                        <span class="wm-nav-text">Monitoring RT</span>
                        <span class="wm-nav-badge" style="background:#10B981;">Terpadu</span>
                    </a>
                </li>

                <li class="wm-nav-header">AUDIT & EVALUASI</li>
                <li class="wm-nav-item">
                    <a href="{{ route('rw.audit.index') }}" class="wm-nav-link {{ request()->routeIs('rw.audit.*') ? 'active' : '' }}" title="Audit & Evaluasi RW 013">
                        <span class="wm-nav-icon"><i data-lucide="clipboard-check"></i></span>
                        <span class="wm-nav-text">Audit & Evaluasi RW</span>
                        <span class="wm-nav-badge" style="background:#2878D0;">Audit</span>
                    </a>
                </li>

                <li class="wm-nav-header">KEUANGAN RW</li>
                <li class="wm-nav-item">
                    <a href="{{ route('kas-rw.index') }}" class="wm-nav-link {{ request()->routeIs('kas-rw.*') || request()->routeIs('keuangan.*') ? 'active' : '' }}" title="Manajemen Keuangan RW">
                        <span class="wm-nav-icon"><i data-lucide="wallet"></i></span>
                        <span class="wm-nav-text">Manajemen Keuangan RW</span>
                    </a>
                </li>

                {{-- ==========================================================
                     MODUL DATA KEPENDUDUKAN TERPADU RW 013
                     ========================================================== --}}
                <li class="wm-nav-header">DATA KEPENDUDUKAN</li>
                <li class="wm-nav-item">
                    <a href="{{ route('kependudukan.index') }}" class="wm-nav-link {{ request()->routeIs('kependudukan.*') || request()->routeIs('warga.*') || request()->routeIs('arsip-kk.*') ? 'active' : '' }}" title="Pusat Data Kependudukan RW 013">
                        <span class="wm-nav-icon"><i data-lucide="contact-2"></i></span>
                        <span class="wm-nav-text">Data Kependudukan</span>
                        <span class="wm-nav-badge" style="background:#16865D;">Terpadu</span>
                    </a>
                </li>

                <li class="wm-nav-header">LAYANAN MASYARAKAT</li>
                <li class="wm-nav-item">
                    <a href="{{ route('posyandu') }}" class="wm-nav-link {{ request()->routeIs('posyandu*') ? 'active' : '' }}" title="Posyandu & Kesehatan">
                        <span class="wm-nav-icon"><i data-lucide="heart-pulse"></i></span>
                        <span class="wm-nav-text">Posyandu & Kesehatan</span>
                        <span class="wm-nav-badge">Sabtu</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('bank-sampah') }}" class="wm-nav-link {{ request()->routeIs('bank-sampah') ? 'active' : '' }}" title="Bank Sampah">
                        <span class="wm-nav-icon"><i data-lucide="recycle"></i></span>
                        <span class="wm-nav-text">Bank Sampah</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('ronda.index') }}" class="wm-nav-link {{ request()->routeIs('ronda.*') || request()->routeIs('cctv.*') ? 'active' : '' }}" title="Ronda & Keamanan">
                        <span class="wm-nav-icon"><i data-lucide="shield-check"></i></span>
                        <span class="wm-nav-text">Ronda & Keamanan</span>
                        <span class="wm-nav-badge" style="background:#16865D;">Live</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('umkm.index') }}" class="wm-nav-link {{ request()->routeIs('umkm.*') ? 'active' : '' }}" title="UMKM Warga">
                        <span class="wm-nav-icon"><i data-lucide="store"></i></span>
                        <span class="wm-nav-text">UMKM Warga</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('pengaduan.index') }}" class="wm-nav-link {{ request()->routeIs('pengaduan.*') ? 'active' : '' }}" title="Pengaduan Warga">
                        <span class="wm-nav-icon"><i data-lucide="message-square-warning"></i></span>
                        <span class="wm-nav-text">Pengaduan Warga</span>
                        <span class="wm-nav-badge bg-warning text-dark">Layanan</span>
                    </a>
                </li>

                <li class="wm-nav-header">ADMINISTRASI RW</li>
                <li class="wm-nav-item">
                    <a href="{{ route('rw.surat.index') }}" class="wm-nav-link {{ request()->routeIs('rw.surat*') || request()->routeIs('rw.surat_master*') ? 'active' : '' }}" title="Manajemen Layanan Surat All-in-One RW 013">
                        <span class="wm-nav-icon"><i data-lucide="file-text"></i></span>
                        <span class="wm-nav-text">Manajemen Layanan Surat</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('agenda-rw.index') }}" class="wm-nav-link {{ request()->routeIs('agenda-rw.*') ? 'active' : '' }}" title="Agenda & Rapat RW">
                        <span class="wm-nav-icon"><i data-lucide="calendar-days"></i></span>
                        <span class="wm-nav-text">Agenda & Rapat RW</span>
                    </a>
                </li>

                <li class="wm-nav-header">INFORMASI WILAYAH</li>
                <li class="wm-nav-item">
                    <a href="{{ route('peta.index') }}" class="wm-nav-link {{ request()->routeIs('peta.*') ? 'active' : '' }}" title="Peta Wilayah">
                        <span class="wm-nav-icon"><i data-lucide="map-pinned"></i></span>
                        <span class="wm-nav-text">Peta Wilayah</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('pengumuman.index') }}" class="wm-nav-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}" title="Pengumuman">
                        <span class="wm-nav-icon"><i data-lucide="megaphone"></i></span>
                        <span class="wm-nav-text">Pengumuman</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('wisata.index') }}" class="wm-nav-link {{ request()->routeIs('wisata.*') ? 'active' : '' }}" title="Wisata & Fasilitas">
                        <span class="wm-nav-icon"><i data-lucide="trees"></i></span>
                        <span class="wm-nav-text">Wisata & Fasilitas</span>
                    </a>
                </li>

                <li class="wm-nav-header">KELOLA PENGGUNA</li>
                <li class="wm-nav-item">
                    <a href="{{ route('profile.edit') }}" class="wm-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Manajemen Pengguna">
                        <span class="wm-nav-icon"><i data-lucide="user-cog"></i></span>
                        <span class="wm-nav-text">Manajemen Pengguna</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('rt.akun.index') }}" class="wm-nav-link {{ request()->routeIs('rt.akun.*') ? 'active' : '' }}" title="Manajemen Akun RT">
                        <span class="wm-nav-icon"><i data-lucide="key-round"></i></span>
                        <span class="wm-nav-text">Manajemen Akun RT</span>
                        <span class="wm-nav-badge" style="background:#16865D;">8 RT</span>
                    </a>
                </li>

            @elseif($isRt)
                {{-- ==========================================================
                     SIDEBAR KHUSUS KETUA RT (RT 001 S/D RT 008)
                     ========================================================== --}}
                <li class="wm-nav-header text-uppercase" style="color: #A7F3D0 !important; font-weight:700;">
                    <i data-lucide="shield" style="width:12px;height:12px;margin-right:4px;"></i> {{ $currentUser->clean_rt_label }} / RW 013
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('dashboard.rt') }}" class="wm-nav-link {{ request()->routeIs('dashboard.rt') ? 'active' : '' }}" title="Ringkasan RT">
                        <span class="wm-nav-icon"><i data-lucide="layout-dashboard"></i></span>
                        <span class="wm-nav-text">Ringkasan {{ $currentUser->clean_rt_label }}</span>
                    </a>
                </li>

                <li class="wm-nav-header">MANAJEMEN KEPENDUDUKAN</li>
                <li class="wm-nav-item">
                    <a href="{{ route('kependudukan.index') }}" class="wm-nav-link {{ request()->routeIs('kependudukan.*') || request()->routeIs('warga.*') || request()->routeIs('arsip-kk.*') || request()->routeIs('rt.pendatang') || request()->routeIs('rt.mutasi') ? 'active' : '' }}" title="Pusat Data Kependudukan RT">
                        <span class="wm-nav-icon"><i data-lucide="contact-2"></i></span>
                        <span class="wm-nav-text">Data Kependudukan</span>
                        <span class="wm-nav-badge" style="background:#1456A0;">Terpadu</span>
                    </a>
                </li>

                <li class="wm-nav-header">PELAYANAN WARGA</li>
                <li class="wm-nav-item">
                    <a href="{{ route('rt.surat.index') }}" class="wm-nav-link {{ request()->routeIs('rt.surat.*') ? 'active' : '' }}" title="Verifikasi Surat RT">
                        <span class="wm-nav-icon"><i data-lucide="file-check-2"></i></span>
                        <span class="wm-nav-text">Verifikasi Surat</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('surat.index') }}" class="wm-nav-link {{ request()->routeIs('surat.index') || request()->routeIs('surat.create') || request()->routeIs('surat.show') ? 'active' : '' }}" title="Daftar Pengajuan Surat">
                        <span class="wm-nav-icon"><i data-lucide="file-text"></i></span>
                        <span class="wm-nav-text">Daftar Pengajuan</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('pengaduan.index') }}" class="wm-nav-link {{ request()->routeIs('pengaduan.*') ? 'active' : '' }}" title="Pengaduan Warga RT">
                        <span class="wm-nav-icon"><i data-lucide="message-square-warning"></i></span>
                        <span class="wm-nav-text">Pengaduan Warga</span>
                    </a>
                </li>

                <li class="wm-nav-header">KEGIATAN RT</li>
                <li class="wm-nav-item">
                    <a href="{{ route('posyandu') }}" class="wm-nav-link {{ request()->routeIs('posyandu*') ? 'active' : '' }}" title="Posyandu & Balita">
                        <span class="wm-nav-icon"><i data-lucide="heart-pulse"></i></span>
                        <span class="wm-nav-text">Posyandu RT</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('bank-sampah') }}" class="wm-nav-link {{ request()->routeIs('bank-sampah*') ? 'active' : '' }}" title="Bank Sampah RT">
                        <span class="wm-nav-icon"><i data-lucide="recycle"></i></span>
                        <span class="wm-nav-text">Bank Sampah</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('ronda.index') }}" class="wm-nav-link {{ request()->routeIs('ronda.*') ? 'active' : '' }}" title="Jadwal Ronda & Keamanan">
                        <span class="wm-nav-icon"><i data-lucide="shield-alert"></i></span>
                        <span class="wm-nav-text">Ronda & Keamanan</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('umkm.index') }}" class="wm-nav-link {{ request()->routeIs('umkm.*') ? 'active' : '' }}" title="UMKM Warga">
                        <span class="wm-nav-icon"><i data-lucide="store"></i></span>
                        <span class="wm-nav-text">UMKM Warga</span>
                    </a>
                </li>

                <li class="wm-nav-header">MANAJEMEN KEUANGAN RT</li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.dashboard') }}" class="wm-nav-link {{ (request()->routeIs('keuangan.*') || request()->routeIs('tagihan.*') || request()->is('keuangan*')) ? 'active' : '' }}" title="Manajemen Keuangan RT">
                        <span class="wm-nav-icon"><i data-lucide="wallet"></i></span>
                        <span class="wm-nav-text">Manajemen Keuangan RT</span>
                    </a>
                </li>

                <li class="wm-nav-header">ADMINISTRASI & KOORDINASI RW</li>
                <li class="wm-nav-item">
                    <a href="{{ route('rt.laporan') }}" class="wm-nav-link {{ request()->routeIs('rt.laporan*') ? 'active' : '' }}" title="Laporan Bulanan ke RW">
                        <span class="wm-nav-icon"><i data-lucide="send"></i></span>
                        <span class="wm-nav-text">Laporan Bulanan RT</span>
                        <span class="wm-nav-badge" style="background:#16865D;">Ke RW</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('rt.evaluasi') }}" class="wm-nav-link {{ request()->routeIs('rt.evaluasi') ? 'active' : '' }}" title="Catatan Evaluasi RW">
                        <span class="wm-nav-icon"><i data-lucide="clipboard-pen"></i></span>
                        <span class="wm-nav-text">Catatan Evaluasi RW</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('rt.arsip') }}" class="wm-nav-link {{ request()->routeIs('rt.arsip') ? 'active' : '' }}" title="Arsip & Dokumen RT">
                        <span class="wm-nav-icon"><i data-lucide="archive"></i></span>
                        <span class="wm-nav-text">Arsip & SK RT</span>
                    </a>
                </li>
                <li class="wm-nav-header">INFORMASI WILAYAH</li>
                <li class="wm-nav-item">
                    <a href="{{ route('peta.index') }}" class="wm-nav-link {{ request()->routeIs('peta.*') ? 'active' : '' }}" title="Peta Wilayah">
                        <span class="wm-nav-icon"><i data-lucide="map-pinned"></i></span>
                        <span class="wm-nav-text">Peta Wilayah</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('pengumuman.index') }}" class="wm-nav-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}" title="Pengumuman RW">
                        <span class="wm-nav-icon"><i data-lucide="megaphone"></i></span>
                        <span class="wm-nav-text">Pengumuman RW</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('wisata.index') }}" class="wm-nav-link {{ request()->routeIs('wisata.*') ? 'active' : '' }}" title="Wisata & Fasilitas">
                        <span class="wm-nav-icon"><i data-lucide="trees"></i></span>
                        <span class="wm-nav-text">Wisata & Fasilitas</span>
                    </a>
                </li>

                <li class="wm-nav-header">PENGATURAN RT</li>
                <li class="wm-nav-item">
                    <a href="{{ route('rt.profil') }}" class="wm-nav-link {{ request()->routeIs('rt.profil') ? 'active' : '' }}" title="Profil RT & Pengurus">
                        <span class="wm-nav-icon"><i data-lucide="landmark"></i></span>
                        <span class="wm-nav-text">Profil & Pos RT</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('profile.edit') }}" class="wm-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Profil Akun">
                        <span class="wm-nav-icon"><i data-lucide="user-cog"></i></span>
                        <span class="wm-nav-text">Profil Akun</span>
                    </a>
                </li>

            @else
                {{-- ==========================================================
                     SIDEBAR KHUSUS WARGA RW 013
                     ========================================================== --}}
                <li class="wm-nav-header">DASHBOARD WARGA</li>
                <li class="wm-nav-item">
                    <a href="{{ route('dashboard.warga') }}" class="wm-nav-link {{ request()->routeIs('dashboard.warga') || request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard Warga">
                        <span class="wm-nav-icon"><i data-lucide="home"></i></span>
                        <span class="wm-nav-text">Dashboard Warga</span>
                    </a>
                </li>

                <li class="wm-nav-header">KEUANGAN SAYA</li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.dashboard') }}" class="wm-nav-link {{ (request()->routeIs('keuangan.dashboard') || request()->routeIs('warga.keuangan.*') || request()->is('keuangan/saya*')) && !request()->routeIs('keuangan.buku_kas.*') && !request()->routeIs('keuangan.transaksi.*') && !request()->routeIs('keuangan.laporan.*') ? 'active' : '' }}" title="Iuran & Keuangan Saya">
                        <span class="wm-nav-icon"><i data-lucide="wallet"></i></span>
                        <span class="wm-nav-text">Iuran & Keuangan Saya</span>
                    </a>
                </li>

                <li class="wm-nav-header">LAYANAN MASYARAKAT</li>
                <li class="wm-nav-item">
                    <a href="{{ route('surat.index') }}" class="wm-nav-link {{ request()->routeIs('surat.*') ? 'active' : '' }}" title="Layanan Penerbitan Surat">
                        <span class="wm-nav-icon"><i data-lucide="file-signature"></i></span>
                        <span class="wm-nav-text">Layanan Surat</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('pengaduan.index') }}" class="wm-nav-link {{ request()->routeIs('pengaduan.*') ? 'active' : '' }}" title="Pengaduan Saya">
                        <span class="wm-nav-icon"><i data-lucide="message-square-warning"></i></span>
                        <span class="wm-nav-text">Pengaduan Warga</span>
                    </a>
                </li>

                <li class="wm-nav-header">INFORMASI LINGKUNGAN</li>
                <li class="wm-nav-item">
                    <a href="{{ route('posyandu') }}" class="wm-nav-link {{ request()->routeIs('posyandu*') ? 'active' : '' }}" title="Jadwal Posyandu">
                        <span class="wm-nav-icon"><i data-lucide="heart-pulse"></i></span>
                        <span class="wm-nav-text">Posyandu & Kesehatan</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('bank-sampah') }}" class="wm-nav-link {{ request()->routeIs('bank-sampah*') ? 'active' : '' }}" title="Jadwal Bank Sampah">
                        <span class="wm-nav-icon"><i data-lucide="recycle"></i></span>
                        <span class="wm-nav-text">Bank Sampah</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('ronda.index') }}" class="wm-nav-link {{ request()->routeIs('ronda.*') ? 'active' : '' }}" title="Jadwal Ronda Malam">
                        <span class="wm-nav-icon"><i data-lucide="shield-check"></i></span>
                        <span class="wm-nav-text">Ronda & Keamanan</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('umkm.index') }}" class="wm-nav-link {{ request()->routeIs('umkm.*') ? 'active' : '' }}" title="UMKM Lingkungan">
                        <span class="wm-nav-icon"><i data-lucide="store"></i></span>
                        <span class="wm-nav-text">UMKM Warga</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('pengumuman.index') }}" class="wm-nav-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}" title="Pengumuman Lingkungan">
                        <span class="wm-nav-icon"><i data-lucide="megaphone"></i></span>
                        <span class="wm-nav-text">Pengumuman RW</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('peta.index') }}" class="wm-nav-link {{ request()->routeIs('peta.*') ? 'active' : '' }}" title="Peta Wilayah RW 013">
                        <span class="wm-nav-icon"><i data-lucide="map-pinned"></i></span>
                        <span class="wm-nav-text">Peta Wilayah</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('wisata.index') }}" class="wm-nav-link {{ request()->routeIs('wisata.*') ? 'active' : '' }}" title="Wisata & Fasilitas">
                        <span class="wm-nav-icon"><i data-lucide="trees"></i></span>
                        <span class="wm-nav-text">Wisata & Fasilitas</span>
                    </a>
                </li>

                <li class="wm-nav-header">PENGATURAN</li>
                <li class="wm-nav-item">
                    <a href="{{ route('profile.edit') }}" class="wm-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Profil Akun Saya">
                        <span class="wm-nav-icon"><i data-lucide="user-cog"></i></span>
                        <span class="wm-nav-text">Profil Saya</span>
                    </a>
                </li>
            @endif
        </ul>

        <!-- Sidebar User Footer Card -->
        @auth
            <div class="wm-sidebar-footer">
                <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1456A0&color=FFFFFF' }}"
                     alt="Avatar" class="wm-user-avatar">
                <div class="wm-user-info">
                    <p class="wm-user-name" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</p>
                    <span class="wm-user-role">
                        {{ Auth::user()->role_badge_label ?? 'Warga RW 013' }}
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="wm-logout-btn" title="Keluar dari Sistem">
                        <i data-lucide="log-out"></i>
                    </button>
                </form>
            </div>
        @endauth
    </aside>

    {{-- ======================================================================
         TOPBAR HEADER (FIXED TOP)
         ====================================================================== --}}
    <header class="wm-topbar">
        <div class="wm-topbar-left">
            <!-- Mobile Hamburger & Desktop Collapse -->
            <button type="button" class="wm-hamburger d-lg-none" onclick="toggleMobileSidebar()" aria-label="Buka Menu">
                <i data-lucide="menu"></i>
            </button>
            <button type="button" class="wm-hamburger d-none d-lg-flex" onclick="toggleSidebarCollapse()" aria-label="Kecilkan Menu">
                <i data-lucide="menu"></i>
            </button>

            <!-- Brand Sapaan & Wilayah -->
            <div class="wm-topbar-title">
                {{-- Laptop / Desktop (>= 768px): Haven Portal • RW 013 Sukamiskin --}}
                <div class="d-none d-md-block text-truncate">
                    <h6 class="mb-0 text-truncate">Haven Portal &bull; RW 013 Sukamiskin</h6>
                    <small class="d-block text-truncate">Sistem Koordinasi &amp; Pelayanan Terpadu Warga</small>
                </div>

                {{-- HP / Mobile (< 768px): Ringkas 1 baris Haven Portal • RW 013 --}}
                <div class="d-md-none text-truncate" style="max-width: 100%;">
                    <h6 class="mb-0 text-truncate" style="font-size: 0.95rem; line-height: 1.2;">Haven Portal <span class="fw-semibold" style="font-size: 0.80rem; opacity: 0.85;">&bull; RW 013</span></h6>
                    <small class="d-block text-truncate" style="font-size: 0.68rem; line-height: 1.15;">RW 013 Sukamiskin</small>
                </div>
            </div>
        </div>

        <div class="wm-topbar-right">
            @if($isSuperAdmin)
                <!-- Tombol Switch Role Super Admin -->
                <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-2 shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#modalSwitchRole"
                        style="background: {{ $isImpersonating ? '#EAF3FF' : '#1456A0' }}; color: {{ $isImpersonating ? '#1456A0' : '#FFFFFF' }}; border: 1.5px solid #1456A0; font-size: 0.78rem;"
                        title="Buka Menu Switch Role Super Admin">
                    <i data-lucide="shuffle" style="width: 14px; height: 14px; margin-right: 2px;"></i>
                    <span class="d-none d-sm-inline">{{ $isImpersonating ? 'Ganti Peran' : 'Switch Role' }}</span>
                    @if($isImpersonating)
                        <span class="badge bg-warning text-dark rounded-pill px-1.5" style="font-size: 0.65rem;">Aktif</span>
                    @endif
                </button>
            @endif

            <!-- Informasi Tanggal Hari Ini -->
            <div class="wm-pill-badge d-none d-md-inline-flex" id="currentDatePill">
                <i data-lucide="calendar" style="width: 15px; height: 15px; color: var(--wm-primary);"></i>
                <span id="liveDateText">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>

            <!-- Status Wilayah RW 013 -->
            @if(isset($securityStatus) && $securityStatus['level'] === 'darurat')
                <a href="{{ route('ronda.index', ['tab' => 'riwayat']) }}" class="wm-pill-badge danger d-none d-sm-inline-flex text-decoration-none shadow-sm" title="Siaga Darurat! Klik untuk melihat rincian laporan darurat">
                    <span class="wm-pulse-dot danger"></span>
                    <span>Siaga Darurat ({{ $securityStatus['darurat_count'] }})</span>
                </a>
            @elseif(isset($securityStatus) && $securityStatus['level'] === 'segera')
                <a href="{{ route('ronda.index', ['tab' => 'riwayat']) }}" class="wm-pill-badge warning d-none d-sm-inline-flex text-decoration-none shadow-sm" title="Waspada Keamanan! Klik untuk melihat rincian laporan">
                    <span class="wm-pulse-dot warning"></span>
                    <span>Waspada ({{ $securityStatus['segera_count'] }})</span>
                </a>
            @else
                <div class="wm-pill-badge success d-none d-sm-inline-flex" title="Kondisi Lingkungan Aman dan Kondusif">
                    <span class="wm-pulse-dot"></span>
                    <span>Kondusif &amp; Aman</span>
                </div>
            @endif

            <!-- Tombol Notifikasi -->
            <div class="dropdown">
                <button type="button" class="wm-topbar-action-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Pemberitahuan">
                    <i data-lucide="bell" style="width: 18px; height: 18px;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="transform: translate(-30%, 30%) !important;"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="width: 310px; border-radius: 16px;">
                    <li class="px-2 py-1 border-bottom d-flex justify-content-between align-items-center">
                        <strong class="small text-dark">Pemberitahuan RW 013</strong>
                        <span class="badge bg-primary rounded-pill">3 Baru</span>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 px-2 rounded-3 mt-1 d-flex gap-2 align-items-start" href="{{ route('pengaduan.index') }}">
                            <div class="badge bg-warning text-dark mt-1 p-2 rounded-circle"><i class="fas fa-exclamation"></i></div>
                            <div style="font-size: 0.8rem;">
                                <div class="fw-bold text-dark">Pengaduan Masuk (RT 005)</div>
                                <div class="text-muted">Lampu PJU gang 3 mati sejak 3 hari lalu.</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 px-2 rounded-3 d-flex gap-2 align-items-start" href="{{ route('monitoring-rt.index') }}">
                            <div class="badge bg-info text-white mt-1 p-2 rounded-circle"><i class="fas fa-file-alt"></i></div>
                            <div style="font-size: 0.8rem;">
                                <div class="fw-bold text-dark">Laporan RT 003 Diterima</div>
                                <div class="text-muted">Kelengkapan administrasi 100% diverifikasi.</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 px-2 rounded-3 d-flex gap-2 align-items-start" href="{{ route('posyandu') }}">
                            <div class="badge bg-success text-white mt-1 p-2 rounded-circle"><i class="fas fa-heartbeat"></i></div>
                            <div style="font-size: 0.8rem;">
                                <div class="fw-bold text-dark">Jadwal Posyandu Balita</div>
                                <div class="text-muted">Sabtu besok di Balai RT 001 jam 08.00 WIB.</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Tombol Dark / Light Mode Toggle -->
            <button type="button" class="wm-topbar-action-btn" id="themeToggleBtn" onclick="toggleTheme()" title="Ubah Mode Gelap / Terang">
                <i data-lucide="moon" id="themeToggleIcon" style="width: 18px; height: 18px;"></i>
            </button>

            <!-- Profil Dropdown -->
            @auth
                <div class="dropdown">
                    <button class="btn btn-link p-0 text-decoration-none dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1456A0&color=FFFFFF' }}"
                             alt="Avatar" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                        <span class="d-none d-xl-inline text-dark fw-bold small text-start lh-1">
                            {{ Auth::user()->name }}
                            <small class="d-block text-muted" style="font-size: 0.7rem;">{{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}</small>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="border-radius: 16px; min-width: 200px;">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                            <small class="text-muted">{{ Auth::user()->email }}</small>
                        </li>
                        <li><a class="dropdown-item py-2 rounded-2 mt-1" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2 text-primary"></i>Profil Saya</a></li>
                        <li><a class="dropdown-item py-2 rounded-2" href="{{ route('profile.edit') }}"><i class="fas fa-key me-2 text-warning"></i>Ubah Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger rounded-2">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </header>

    {{-- ======================================================================
         AREA KONTEN UTAMA (PADDING DINAMIS MENYESUAIKAN SIDEBAR)
         ====================================================================== --}}
    <main class="wm-main-content">
        {{-- Banner Indikator Mode Simulasi Super Admin --}}
        @if($isImpersonating)
            @php
                $impersonationMode = session('impersonation_mode', 'view_only');
                $isViewOnly = ($impersonationMode === 'view_only');
            @endphp
            <div class="card border-0 shadow-sm mb-4 overflow-hidden wm-simulasi-banner" style="border-radius: 16px; border-left: 5px solid {{ $isViewOnly ? '#D97706' : '#DC2626' }} !important; border: 1px solid #B8D5FA;">
                <div class="card-body py-2.5 px-3 px-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2.5 flex-wrap">
                        <span class="badge rounded-pill fw-bold text-white px-3 py-1.5 shadow-sm" style="background: #1456A0; font-size: 0.75rem;">
                            <i data-lucide="shield-alert" style="width: 14px; height: 14px; vertical-align: middle;"></i> SIMULASI SUPER ADMIN
                        </span>

                        {{-- Mode Status Badge --}}
                        @if($isViewOnly)
                            <span class="badge rounded-pill px-2.5 py-1.5 fw-bold border shadow-xs d-inline-flex align-items-center gap-1" style="background: #FEF3C7; color: #92400E; border-color: #FCD34D !important; font-size: 0.74rem;">
                                <i data-lucide="eye" style="width: 13px; height: 13px;"></i> Mode Lihat Saja (Read-Only)
                            </span>
                        @else
                            <span class="badge rounded-pill px-2.5 py-1.5 fw-bold border shadow-xs text-white d-inline-flex align-items-center gap-1" style="background: #DC2626; border-color: #B91C1C !important; font-size: 0.74rem;">
                                <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i> Mode Edit (Akses Penuh)
                            </span>
                        @endif

                        <div class="text-dark small">
                            Sedang berperan sebagai:
                            <strong class="text-primary">{{ Auth::user()->name }}</strong>
                            <span class="badge bg-white text-dark border px-2 py-0.5 rounded-pill ms-1">{{ Auth::user()->role_badge_label }}</span>
                        </div>
                        <small class="text-muted d-none d-xl-inline">&bull; Akun Asli: {{ session('impersonator_name', 'Super Admin') }}</small>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        {{-- Toggle Mode Cepat 1-Klik --}}
                        <form method="POST" action="{{ route('switch.role.mode') }}" class="m-0">
                            @csrf
                            @if($isViewOnly)
                                <input type="hidden" name="mode" value="edit">
                                <button type="submit" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold text-white shadow-sm d-inline-flex align-items-center gap-1" style="background: #16865D; border: none; font-size: 0.78rem;" title="Klik untuk mengaktifkan izin simpan/edit transaksi & pengumuman">
                                    <i data-lucide="unlock" style="width: 14px; height: 14px;"></i> Beralih ke Mode Edit
                                </button>
                            @else
                                <input type="hidden" name="mode" value="view_only">
                                <button type="submit" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1" style="background: #FFFBEB; color: #92400E; border: 1.5px solid #F59E0B; font-size: 0.78rem;" title="Klik untuk mengunci ke mode aman (Read-Only)">
                                    <i data-lucide="lock" style="width: 14px; height: 14px;"></i> Kunci ke Lihat Saja
                                </button>
                            @endif
                        </form>

                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSwitchRole" style="font-size: 0.78rem;">
                            <i data-lucide="shuffle" style="width: 14px; height: 14px;"></i> Ganti Peran
                        </button>

                        <form method="POST" action="{{ route('switch.role.revert') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.78rem; background: #1456A0; border-color: #1456A0;">
                                <i data-lucide="corner-up-left" style="width: 14px; height: 14px;"></i> Kembali ke Super Admin
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Banner Tambahan Saat Mode Lihat Saja Aktif --}}
                @if($isViewOnly)
                    <div class="py-1 px-3 px-md-4 text-dark d-flex align-items-center gap-2" style="background: rgba(254, 243, 199, 0.75); border-top: 1px solid #FDE68A; font-size: 0.74rem;">
                        <i data-lucide="shield-check" style="width: 13px; height: 13px; color: #B45309; flex-shrink: 0;"></i>
                        <span>
                            <strong>Proteksi Backend Aktif:</strong> Permintaan penambahan, perubahan, dan penghapusan data (POST/PUT/DELETE) sedang diblokir otomatis. Klik <strong>Beralih ke Mode Edit</strong> untuk memodifikasi data.
                        </span>
                    </div>
                @endif
            </div>
        @endif

        <!-- Global Floating Toast Notification (Kompak, Mengambang di Pojok Kanan Atas, Auto-dismiss 5s) -->
        <style>
            .wm-toast-container {
                position: fixed;
                top: 80px;
                right: 16px;
                z-index: 1080;
                width: calc(100vw - 32px);
                max-width: 380px;
                pointer-events: none;
            }
            @media (min-width: 576px) {
                .wm-toast-container {
                    right: 24px;
                }
            }
            .wm-toast-item {
                pointer-events: auto;
                border-radius: 12px;
                padding: 12px 16px;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
                transition: opacity 0.35s ease, transform 0.35s ease;
                animation: wmToastIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            @keyframes wmToastIn {
                from {
                    opacity: 0;
                    transform: translateY(-12px) scale(0.97);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            .wm-toast-item.wm-toast-fadeout {
                opacity: 0 !important;
                transform: translateY(-10px) scale(0.97) !important;
            }
        </style>

        @php
            $toasts = [];
            if (session('success')) {
                $toasts[] = [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => session('success'),
                    'icon' => 'check-circle-2',
                    'bg' => '#ECFDF5',
                    'border' => '#A7F3D0',
                    'text' => '#16865D',
                    'icon_bg' => '#D1FAE5',
                ];
            }
            if (session('error')) {
                $toasts[] = [
                    'type' => 'error',
                    'title' => 'Kesalahan',
                    'message' => session('error'),
                    'icon' => 'alert-circle',
                    'bg' => '#FEF2F2',
                    'border' => '#FECACA',
                    'text' => '#DC2626',
                    'icon_bg' => '#FEE2E2',
                ];
            }
            if (session('warning')) {
                $toasts[] = [
                    'type' => 'warning',
                    'title' => 'Perhatian',
                    'message' => session('warning'),
                    'icon' => 'alert-triangle',
                    'bg' => '#FFFBEB',
                    'border' => '#FDE68A',
                    'text' => '#D97706',
                    'icon_bg' => '#FEF3C7',
                ];
            }
            if (session('info')) {
                $toasts[] = [
                    'type' => 'info',
                    'title' => 'Informasi',
                    'message' => session('info'),
                    'icon' => 'info',
                    'bg' => '#EAF3FF',
                    'border' => '#BFDBFE',
                    'text' => '#1456A0',
                    'icon_bg' => '#DBEAFE',
                ];
            }
            if (isset($errors) && $errors->any()) {
                $errorMsg = $errors->first();
                if ($errors->count() > 1) {
                    $errorMsg .= ' (dan ' . ($errors->count() - 1) . ' kendala input lainnya)';
                }
                $toasts[] = [
                    'type' => 'error',
                    'title' => 'Validasi Gagal',
                    'message' => $errorMsg,
                    'icon' => 'x-circle',
                    'bg' => '#FEF2F2',
                    'border' => '#FECACA',
                    'text' => '#DC2626',
                    'icon_bg' => '#FEE2E2',
                ];
            }
        @endphp

        @if(!empty($toasts))
            <div id="wmToastContainer" class="wm-toast-container">
                @foreach($toasts as $toast)
                    <div class="wm-toast-item mb-2.5 d-flex align-items-start gap-2.5 wm-toast-{{ $toast['type'] }}" 
                         style="background: {{ $toast['bg'] }}; border: 1px solid {{ $toast['border'] }};" 
                         role="alert">
                        <div class="wm-toast-icon-wrap d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" 
                             style="width: 28px; height: 28px; background: {{ $toast['icon_bg'] }}; color: {{ $toast['text'] }}; margin-top: 1px;">
                            <i data-lucide="{{ $toast['icon'] }}" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="fw-bold wm-toast-title" style="font-size: 13px; color: {{ $toast['text'] }}; line-height: 1.25;">
                                {{ $toast['title'] }}
                            </div>
                            <div class="text-dark mt-0.5 wm-toast-message" style="font-size: 13px; line-height: 1.4; word-break: break-word;">
                                {{ $toast['message'] }}
                            </div>
                        </div>
                        <button type="button" class="btn-close p-1 flex-shrink-0 wm-toast-close" aria-label="Close" 
                                onclick="dismissToast(this.closest('.wm-toast-item'))" 
                                style="font-size: 10px; margin-top: 2px; filter: grayscale(1); opacity: 0.65; cursor: pointer;">
                        </button>
                    </div>
                @endforeach
            </div>

            <script>
                function dismissToast(el) {
                    if (!el) return;
                    el.classList.add('wm-toast-fadeout');
                    setTimeout(function() {
                        el.remove();
                    }, 350);
                }

                document.addEventListener('DOMContentLoaded', function() {
                    if (window.lucide) {
                        lucide.createIcons();
                    }
                    var toastItems = document.querySelectorAll('.wm-toast-item');
                    if (toastItems.length > 0) {
                        setTimeout(function() {
                            toastItems.forEach(function(item) {
                                dismissToast(item);
                            });
                        }, 5000);
                    }
                });
            </script>
        @endif

        <!-- Global Multi-step Back Button for subpages (Disembunyikan pada Halaman Utama Menu Sidebar & Subhalaman Posyandu yang Memiliki Navigasi Sendiri) -->
        @auth
            @unless(request()->routeIs('dashboard*') || request()->routeIs('super-admin.*') || request()->routeIs('kependudukan.index') || request()->routeIs('pengumuman.*') || request()->routeIs('keuangan.dashboard') || request()->routeIs('keuangan.index') || request()->routeIs('warga.keuangan.*') || request()->routeIs('kas-rw.*') || request()->routeIs('rw.audit.*') || request()->routeIs('monitoring-rt.index') || request()->routeIs('data-rt.index') || request()->routeIs('bank-sampah*') || request()->routeIs('profile.*') || request()->routeIs('ronda.*') || request()->routeIs('peta.*') || request()->routeIs('wisata.*') || request()->routeIs('pengaduan.*') || request()->routeIs('posyandu') || request()->routeIs('posyandu.*') || request()->routeIs('umkm.*') || request()->routeIs('agenda-rw*') || request()->routeIs('rt.akun.*') || request()->routeIs('rw.surat.*') || request()->routeIs('rt.surat.*') || request()->routeIs('surat.*') || request()->routeIs('rw.surat_master.*'))
                <div class="mb-3">
                    <a href="{{ route('nav.back') }}" id="globalBackBtn" class="wm-btn-outline" style="padding: 7px 14px; font-size: 0.85rem;">
                        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Kembali
                    </a>
                </div>
            @endunless
        @endauth

        <!-- Dynamic Page Content -->
        @yield('content')

        <!-- Footer RW 013 -->
        <footer class="text-center py-4 mt-5 text-muted small" style="border-top: 1px solid var(--wm-border);">
            <p class="mb-1 fw-semibold">&copy; {{ date('Y') }} Haven Portal &bull; Safe Space, Seamless Service &bull; havenportal.mehape.com</p>
            <p class="mb-0 text-secondary" style="font-size: 0.74rem;">Sistem Koordinasi &amp; Pelayanan Terpadu RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293.</p>
        </footer>
    </main>

    {{-- ======================================================================
         FLOATING SPEED DIAL CONTACT WIDGET (WHATSAPP & TELEGRAM) - DIPERTAHANKAN
         ====================================================================== --}}
    <div class="floating-contact-container" id="floatingContactContainer">
        <!-- Bubble Telegram -->
        <a href="https://t.me/icrvye" target="_blank" rel="noopener noreferrer" class="contact-bubble-item telegram-bubble" id="telegramBubble" title="Hubungi via Telegram">
            <span class="bubble-icon-box">
                <i class="fab fa-telegram-plane"></i>
            </span>
            <span class="bubble-label-text">Telegram RW</span>
        </a>

        <!-- Bubble WhatsApp -->
        <div class="contact-bubble-item whatsapp-bubble" id="whatsappBubble" onclick="toggleContactBubbles(event)" title="Pusat Bantuan WhatsApp">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $admin_contact_phone ?? '6281234567890') }}" target="_blank" rel="noopener noreferrer" class="whatsapp-link-area text-white text-decoration-none d-flex align-items-center gap-2" onclick="event.stopPropagation();">
                <span class="bubble-icon-box">
                    <i class="fab fa-whatsapp"></i>
                </span>
                <span class="bubble-label-text">WhatsApp RW</span>
            </a>
            <button type="button" class="bubble-toggle-arrow" title="Tampilkan Pilihan Kontak">
                <i class="fas fa-chevron-up" id="arrowToggleIcon"></i>
            </button>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Chart.js Global Auto-Theming (Dark/Light Mode Sync)
        function getThemeChartColors(theme) {
            const isDark = (theme === 'dark');
            return {
                text: isDark ? '#CBD5E1' : '#475569',
                grid: isDark ? 'rgba(148, 163, 184, 0.14)' : 'rgba(15, 23, 42, 0.08)',
                border: isDark ? '#2A3E5E' : '#E2E8F0',
                doughnutBorder: isDark ? '#131F35' : '#FFFFFF'
            };
        }

        function applyChartThemeDefaults(theme) {
            if (typeof Chart === 'undefined') return;
            const colors = getThemeChartColors(theme);
            Chart.defaults.color = colors.text;
            if (Chart.defaults.plugins && Chart.defaults.plugins.legend) {
                Chart.defaults.plugins.legend.labels = Chart.defaults.plugins.legend.labels || {};
                Chart.defaults.plugins.legend.labels.color = colors.text;
            }
            if (Chart.defaults.scales) {
                ['x', 'y', 'r'].forEach(axis => {
                    Chart.defaults.scales[axis] = Chart.defaults.scales[axis] || {};
                    Chart.defaults.scales[axis].ticks = Chart.defaults.scales[axis].ticks || {};
                    Chart.defaults.scales[axis].ticks.color = colors.text;
                    Chart.defaults.scales[axis].grid = Chart.defaults.scales[axis].grid || {};
                    Chart.defaults.scales[axis].grid.color = colors.grid;
                });
            }
        }

        function syncChartsWithTheme(theme) {
            if (typeof Chart === 'undefined') return;
            applyChartThemeDefaults(theme);
            const colors = getThemeChartColors(theme);

            if (Chart.instances) {
                Object.values(Chart.instances).forEach(chart => {
                    try {
                        if (chart.options) {
                            if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                                chart.options.plugins.legend.labels.color = colors.text;
                            }
                            if (chart.options.scales) {
                                Object.values(chart.options.scales).forEach(scale => {
                                    if (scale.ticks) scale.ticks.color = colors.text;
                                    if (scale.grid) scale.grid.color = colors.grid;
                                });
                            }
                            if (chart.data && chart.data.datasets) {
                                chart.data.datasets.forEach(ds => {
                                    if (chart.config.type === 'doughnut' || chart.config.type === 'pie') {
                                        ds.borderColor = colors.doughnutBorder;
                                    }
                                });
                            }
                        }
                        chart.update();
                    } catch (err) {
                        console.warn('Chart theme sync exception:', err);
                    }
                });
            }
        }

        // Jalankan apply awal segera setelah Chart.js termuat
        (function() {
            const currentTheme = localStorage.getItem('wm-theme') || localStorage.getItem('warkas_theme') || document.documentElement.getAttribute('data-theme') || 'light';
            applyChartThemeDefaults(currentTheme);
        })();

        // Inisialisasi Lucide Icons
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                lucide.createIcons();
            }
        });

        // Toggle Sidebar Desktop (Collapse / Expand)
        function toggleSidebarCollapse() {
            document.body.classList.toggle('sidebar-collapsed');
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('wm-sidebar-collapsed', isCollapsed ? 'true' : 'false');
            
            const icon = document.getElementById('collapseIcon');
            if (icon) {
                icon.setAttribute('data-lucide', isCollapsed ? 'chevrons-right' : 'chevrons-left');
                if (window.lucide) lucide.createIcons();
            }
        }

        // Toggle Sidebar Mobile Drawer
        function toggleMobileSidebar() {
            document.body.classList.toggle('sidebar-mobile-open');
        }

        // Floating Contacts Speed Dial
        function toggleContactBubbles(e) {
            if (e) e.stopPropagation();
            const container = document.getElementById('floatingContactContainer');
            if (container) container.classList.toggle('expanded');
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('floatingContactContainer');
            if (container && !container.contains(e.target)) {
                container.classList.remove('expanded');
            }
        });

        // Theme Toggle (Dark / Light Mode)
        function applyTheme(theme) {
            const root = document.documentElement;
            const icon = document.getElementById('themeToggleIcon');
            if (theme === 'dark') {
                root.setAttribute('data-theme', 'dark');
                document.body.classList.add('dark-mode');
                if (icon) icon.setAttribute('data-lucide', 'sun');
            } else {
                root.setAttribute('data-theme', 'light');
                document.body.classList.remove('dark-mode');
                if (icon) icon.setAttribute('data-lucide', 'moon');
            }
            if (window.lucide) lucide.createIcons();
            syncChartsWithTheme(theme);
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: theme } }));
        }

        function toggleTheme() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark' || document.body.classList.contains('dark-mode');
            const newTheme = isDark ? 'light' : 'dark';
            localStorage.setItem('wm-theme', newTheme);
            localStorage.setItem('warkas_theme', newTheme);
            applyTheme(newTheme);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('wm-theme') || localStorage.getItem('warkas_theme') || 'light';
            applyTheme(savedTheme);
        });

        // Auto close alert after 4 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert-dismissible').forEach(function(el) {
                var alert = bootstrap.Alert.getOrCreateInstance(el);
                if (alert) alert.close();
            });
        }, 4000);
    </script>

    @if($isSuperAdmin)
        {{-- ==================================================================
             MODAL SWITCH ROLE SUPER ADMIN (SIMULASI PERAN TANPA LOGOUT)
             ================================================================== --}}
        <div class="modal fade" id="modalSwitchRole" tabindex="-1" aria-labelledby="modalSwitchRoleLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; overflow: hidden;">
                    <div class="modal-header text-white p-4" style="background: linear-gradient(135deg, #0F3966 0%, #1456A0 50%, #2878D0 100%);">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-white/20 text-white rounded-pill px-2.5 py-1 text-xs font-bold">
                                    <i data-lucide="shield" style="width: 13px; height: 13px; vertical-align: middle;"></i> Super Admin Tool
                                </span>
                                @if($isImpersonating)
                                    <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 text-xs font-bold">Sedang Simulasi</span>
                                @endif
                            </div>
                            <h5 class="modal-title fw-black tracking-tight" id="modalSwitchRoleLabel">Switch Role & Simulasi Akses</h5>
                            <p class="text-blue-100 small mb-0 mt-1">Pindah peran secara instan tanpa logout untuk memverifikasi alur kerja, data, & batasan hak akses.</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4" style="background: var(--wm-bg, #F4F7FB);">
                        {{-- Status Saat Ini --}}
                        <div class="p-3 rounded-4 bg-white border mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm">
                            <div>
                                <small class="text-muted d-block fw-bold" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Peran Aktif Saat Ini</small>
                                <strong class="text-dark fs-6">{{ Auth::user()->name }}</strong>
                                <span class="badge bg-primary text-white ms-1 rounded-pill" style="font-size: 0.72rem;">{{ Auth::user()->role_badge_label }}</span>
                            </div>
                            @if($isImpersonating)
                                <form method="POST" action="{{ route('switch.role.revert') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.75rem;">
                                        <i data-lucide="corner-up-left" style="width: 14px; height: 14px;"></i> Kembali ke Super Admin
                                    </button>
                                </form>
                            @endif
                        </div>

                        {{-- Pilihan Mode Sesi Master --}}
                        <div class="p-3 rounded-4 bg-white border mb-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <label class="form-label text-dark fw-bold small mb-0 d-block" style="font-size: 0.78rem;">
                                        <i data-lucide="shield" style="width: 14px; height: 14px; vertical-align: middle; color: #1456A0;"></i> Mode Sesi Simulasi:
                                    </label>
                                    <small class="text-muted" style="font-size: 0.72rem;">Mode Lihat Saja memblokir aksi mutasi (POST/PUT/DELETE) untuk keamanan.</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input" type="radio" name="global_mode_selector" id="modal_view_only" value="view_only" checked onchange="updateModalFormsMode('view_only')">
                                        <label class="form-check-label small fw-bold modal-view-only-label" for="modal_view_only" style="font-size: 0.76rem;">
                                            <i data-lucide="eye" style="width: 12px; height: 12px; vertical-align: middle;"></i> Lihat Saja (Default)
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input" type="radio" name="global_mode_selector" id="modal_edit" value="edit" onchange="updateModalFormsMode('edit')">
                                        <label class="form-check-label small fw-bold text-danger" for="modal_edit" style="font-size: 0.76rem;">
                                            <i data-lucide="edit-3" style="width: 12px; height: 12px; vertical-align: middle;"></i> Mode Edit
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Nav Tabs Pilihan Role --}}
                        <ul class="nav nav-pills nav-fill mb-3 p-1 rounded-4 bg-white border shadow-sm" id="pills-role-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill fw-bold py-2" id="pills-rw-tab" data-bs-toggle="pill" data-bs-target="#pills-rw" type="button" role="tab" style="font-size: 0.82rem;">
                                    <i data-lucide="landmark" style="width: 14px; height: 14px; vertical-align: middle;"></i> 1. Ketua RW 013
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill fw-bold py-2" id="pills-rt-tab" data-bs-toggle="pill" data-bs-target="#pills-rt" type="button" role="tab" style="font-size: 0.82rem;">
                                    <i data-lucide="building-2" style="width: 14px; height: 14px; vertical-align: middle;"></i> 2. Ketua RT (001 - 008)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill fw-bold py-2" id="pills-warga-tab" data-bs-toggle="pill" data-bs-target="#pills-warga" type="button" role="tab" style="font-size: 0.82rem;">
                                    <i data-lucide="users" style="width: 14px; height: 14px; vertical-align: middle;"></i> 3. Akun Warga
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="pills-role-tabContent">
                            {{-- Tab 1: RW --}}
                            <div class="tab-pane fade show active" id="pills-rw" role="tabpanel">
                                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #EAF3FF; color: #1456A0; width: 50px; height: 50px;">
                                                <i data-lucide="landmark" style="width: 26px; height: 26px;"></i>
                                            </div>
                                            <div>
                                                <span class="badge bg-primary text-white rounded-pill px-2 py-0.5 text-xs">Pimpinan Wilayah</span>
                                                <h6 class="fw-bold text-dark mb-0 mt-1">{{ $rwUser ? $rwUser->name : 'Ketua RW 013' }}</h6>
                                                <small class="text-muted">{{ $rwUser ? $rwUser->email : 'rw013@havenportal.id' }} &bull; Akses penuh monitoring 8 RT & Kas Induk RW</small>
                                            </div>
                                        </div>
                                        <form method="POST" action="{{ route('switch.role') }}" class="m-0">
                                            @csrf
                                            <input type="hidden" name="target_role" value="rw">
                                            <input type="hidden" name="mode" class="modal-form-mode-input" value="view_only">
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm" style="background: #1456A0; border-color: #1456A0;">
                                                <i data-lucide="arrow-right-circle" style="width: 16px; height: 16px;"></i> Masuk sebagai Ketua RW
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 2: RT (Dinamis dari Database) --}}
                            <div class="tab-pane fade" id="pills-rt" role="tabpanel">
                                <div class="row g-2.5">
                                    @forelse($allRtUsers as $rtUser)
                                        <div class="col-md-6">
                                            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white h-100 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <span class="badge bg-success text-white rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                                            {{ $rtUser->clean_rt_label }}
                                                        </span>
                                                        @if(Auth::id() === $rtUser->id)
                                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Sedang Aktif</span>
                                                        @endif
                                                    </div>
                                                    <h6 class="fw-bold text-dark mt-2 mb-0" style="font-size: 0.9rem;">{{ $rtUser->name }}</h6>
                                                    <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $rtUser->phone ?? '-' }} &bull; {{ $rtUser->address ?? 'Wilayah ' . $rtUser->clean_rt_label }}</small>
                                                </div>
                                                <form method="POST" action="{{ route('switch.role') }}" class="m-0 mt-3">
                                                    @csrf
                                                    <input type="hidden" name="target_role" value="rt">
                                                    <input type="hidden" name="rt_number" value="{{ $rtUser->rt_number }}">
                                                    <input type="hidden" name="target_user_id" value="{{ $rtUser->id }}">
                                                    <input type="hidden" name="mode" class="modal-form-mode-input" value="view_only">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill w-100 fw-bold d-flex align-items-center justify-content-center gap-1" {{ Auth::id() === $rtUser->id ? 'disabled' : '' }}>
                                                        <i data-lucide="shuffle" style="width: 13px; height: 13px;"></i> Simulasi {{ $rtUser->clean_rt_label }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-4 text-muted small">Tidak ada akun RT yang terdaftar di database.</div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Tab 3: Warga --}}
                            <div class="tab-pane fade" id="pills-warga" role="tabpanel">
                                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                                    <form method="POST" action="{{ route('switch.role') }}">
                                        @csrf
                                        <input type="hidden" name="target_role" value="warga">
                                        <input type="hidden" name="mode" class="modal-form-mode-input" value="view_only">

                                        <div class="mb-3">
                                            <label class="form-label text-dark fw-bold small">Pilih Wilayah RT Warga:</label>
                                            <select class="form-select rounded-3" id="selectWargaRt" onchange="loadWargaByRt(this.value)">
                                                <option value="">-- Pilih RT --</option>
                                                @foreach($allRtUsers as $rtUser)
                                                    <option value="{{ $rtUser->rt_number }}">RT {{ $rtUser->clean_rt_label }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-dark fw-bold small">Pilih Akun Warga:</label>
                                            <select class="form-select rounded-3" name="target_user_id" id="selectTargetWarga" required disabled>
                                                <option value="">-- Pilih RT Terlebih Dahulu --</option>
                                            </select>
                                            <div id="wargaLoadingSpinner" class="spinner-border spinner-border-sm text-primary mt-2 d-none" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <small class="text-muted d-block mt-1" id="wargaCountHint"></small>
                                        </div>

                                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold w-100 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm" id="btnSwitchWarga" style="background: #1456A0; border-color: #1456A0;" disabled>
                                            <i data-lucide="user-check" style="width: 16px; height: 16px;"></i> Masuk sebagai Akun Warga Terpilih
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function updateModalFormsMode(mode) {
                document.querySelectorAll('.modal-form-mode-input').forEach(function(el) {
                    el.value = mode;
                });
            }

            function loadWargaByRt(rtNumber) {
                var wargaSelect = document.getElementById('selectTargetWarga');
                var submitBtn = document.getElementById('btnSwitchWarga');
                var spinner = document.getElementById('wargaLoadingSpinner');
                var hint = document.getElementById('wargaCountHint');

                wargaSelect.innerHTML = '<option value="">-- Memuat Data Warga... --</option>';
                wargaSelect.disabled = true;
                submitBtn.disabled = true;
                if (!rtNumber) {
                    wargaSelect.innerHTML = '<option value="">-- Pilih RT Terlebih Dahulu --</option>';
                    hint.textContent = '';
                    return;
                }

                spinner.classList.remove('d-none');
                fetch('{{ route("switch.role.warga_options") }}?rt_number=' + encodeURIComponent(rtNumber))
                    .then(response => response.json())
                    .then(data => {
                        spinner.classList.add('d-none');
                        wargaSelect.innerHTML = '';
                        if (data.data && data.data.length > 0) {
                            wargaSelect.innerHTML = '<option value="">-- Pilih Salah Satu Warga (' + data.count + ' Warga Ditemukan) --</option>';
                            data.data.forEach(warga => {
                                var opt = document.createElement('option');
                                opt.value = warga.id;
                                opt.textContent = warga.name + ' (' + (warga.email || '-') + ' | Rumah: ' + (warga.house_number || '-') + ')';
                                wargaSelect.appendChild(opt);
                            });
                            wargaSelect.disabled = false;
                            hint.textContent = 'Menampilkan ' + data.count + ' warga terdaftar di RT ' + data.rt;
                        } else {
                            wargaSelect.innerHTML = '<option value="">-- Tidak ada warga terdaftar di RT ini --</option>';
                            hint.textContent = 'Tidak ditemukan akun warga untuk RT ini.';
                        }
                    })
                    .catch(err => {
                        spinner.classList.add('d-none');
                        wargaSelect.innerHTML = '<option value="">-- Gagal memuat warga --</option>';
                        console.error('Error fetching warga:', err);
                    });

                wargaSelect.onchange = function() {
                    submitBtn.disabled = !this.value;
                };
            }
        </script>
    @endif

    {{-- Frontend Auto-Check Idle Timeout Khusus Akun Warga (15 Menit) --}}
    @auth
        @if(Auth::user()->role === 'warga' && !session('is_impersonating'))
            <script>
                (function () {
                    var IDLE_TIMEOUT_MS = 15 * 60 * 1000; // 15 menit
                    var STORAGE_KEY = 'haven_warga_last_activity';
                    var loginUrl = @json(route('login'));

                    function updateLastActivity() {
                        try {
                            localStorage.setItem(STORAGE_KEY, Date.now().toString());
                        } catch (e) {}
                    }

                    function checkIdleTimeout() {
                        try {
                            var lastActive = parseInt(localStorage.getItem(STORAGE_KEY) || Date.now().toString(), 10);
                            var idleDuration = Date.now() - lastActive;
                            if (idleDuration >= IDLE_TIMEOUT_MS) {
                                // Sesi telah idle >= 15 menit, arahkan ke login langsung tanpa popup error 419
                                window.location.href = loginUrl;
                            }
                        } catch (e) {}
                    }

                    // Inisialisasi awal saat halaman dibuka
                    updateLastActivity();

                    // Catat interaksi pengguna dengan throttling 5 detik
                    var lastThrottle = 0;
                    function onUserActivity() {
                        var now = Date.now();
                        if (now - lastThrottle > 5000) {
                            lastThrottle = now;
                            updateLastActivity();
                        }
                    }

                    ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach(function (evt) {
                        window.addEventListener(evt, onUserActivity, { passive: true });
                    });

                    // Cek saat tab aktif kembali setelah ditinggal/dikecilkan
                    document.addEventListener('visibilitychange', function () {
                        if (document.visibilityState === 'visible') {
                            checkIdleTimeout();
                        }
                    });

                    window.addEventListener('focus', function () {
                        checkIdleTimeout();
                    });

                    // Cek berkala setiap 15 detik
                    setInterval(checkIdleTimeout, 15000);
                })();
            </script>
        @endif
    @endauth

    @stack('scripts')
</body>
</html>
