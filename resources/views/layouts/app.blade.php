<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Warkas Machi' }} | Sistem Manajemen RW 013</title>
    
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
            <a href="{{ route('dashboard') }}" class="wm-brand-link" title="Warkas Machi RW 013">
                <div class="wm-brand-icon">
                    <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M8 30 32 10l24 20" stroke="#FFFFFF" stroke-width="4"/>
                        <path d="M14 27v20h36V27" stroke="#7DB7FF" stroke-width="3.5"/>
                        <circle cx="26" cy="30" r="3.5" fill="#FFFFFF"/>
                        <path d="M21 44v-6a5 5 0 0 1 10 0v6" fill="#FFFFFF"/>
                        <circle cx="38" cy="32" r="3" fill="#A7F3D0"/>
                        <path d="M34 44v-5a4 4 0 0 1 8 0v5" fill="#A7F3D0"/>
                        <path d="M10 52c8-8 18-8 22-2 4-6 14-6 22 2-10 4-34 4-44 0Z" fill="#10B981"/>
                    </svg>
                </div>
                <div class="wm-brand-info">
                    <span class="wm-brand-name">Warkas Machi</span>
                    <span class="wm-brand-sub">SISTEM MANAJEMEN RW 013</span>
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
                $isRw = $currentUser && ($currentUser->isRw() || in_array($currentUser->role, ['admin', 'bendahara', 'super_admin']));
                $isRt = $currentUser && ($currentUser->isRt() || $currentUser->role === 'rt');
            @endphp

            @if($isRw)
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
                    <a href="{{ route('monitoring-rt.index') }}" class="wm-nav-link {{ request()->routeIs('monitoring-rt.*') ? 'active' : '' }}" title="Audit & Monitoring RT">
                        <span class="wm-nav-icon"><i data-lucide="clipboard-check"></i></span>
                        <span class="wm-nav-text">Audit & Monitoring RT</span>
                        <span class="wm-nav-badge" style="background:#2878D0;">Audit</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('laporan-rt.index') }}" class="wm-nav-link {{ request()->routeIs('laporan-rt.*') ? 'active' : '' }}" title="Laporan Seluruh RT">
                        <span class="wm-nav-icon"><i data-lucide="file-check"></i></span>
                        <span class="wm-nav-text">Laporan Masuk RT</span>
                    </a>
                </li>

                {{-- ==========================================================
                     MODUL DATA KEPENDUDUKAN TERPADU RW 013
                     ========================================================== --}}
                <li class="wm-nav-header">DATA KEPENDUDUKAN</li>
                <li class="wm-nav-item">
                    <a href="{{ route('warga.index') }}" class="wm-nav-link {{ request()->routeIs('warga.index') || request()->routeIs('warga.create') || request()->routeIs('warga.show') || request()->routeIs('warga.edit') ? 'active' : '' }}" title="Database Warga RW 013">
                        <span class="wm-nav-icon"><i data-lucide="users"></i></span>
                        <span class="wm-nav-text">Database Warga</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('kependudukan.keluarga') }}" class="wm-nav-link {{ request()->routeIs('kependudukan.keluarga') ? 'active' : '' }}" title="Data Keluarga & Kartu Keluarga">
                        <span class="wm-nav-icon"><i data-lucide="home"></i></span>
                        <span class="wm-nav-text">Data Keluarga</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('kependudukan.verifikasi') }}" class="wm-nav-link {{ request()->routeIs('kependudukan.verifikasi') ? 'active' : '' }}" title="Pusat Verifikasi Warga Berjenjang">
                        <span class="wm-nav-icon"><i data-lucide="user-check"></i></span>
                        <span class="wm-nav-text">Verifikasi Warga</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('arsip-kk.index') }}" class="wm-nav-link {{ request()->routeIs('arsip-kk.*') ? 'active' : '' }}" title="Arsip KK Resmi Dukcapil">
                        <span class="wm-nav-icon"><i data-lucide="folder-check"></i></span>
                        <span class="wm-nav-text">Arsip KK Resmi</span>
                        <span class="wm-nav-badge" style="background:#1456A0;">Dukcapil</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('kependudukan.riwayat') }}" class="wm-nav-link {{ request()->routeIs('kependudukan.riwayat') ? 'active' : '' }}" title="Riwayat Perubahan Data & Mutasi">
                        <span class="wm-nav-icon"><i data-lucide="history"></i></span>
                        <span class="wm-nav-text">Riwayat Perubahan Data</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('kependudukan.laporan') }}" class="wm-nav-link {{ request()->routeIs('kependudukan.laporan') ? 'active' : '' }}" title="Laporan Kependudukan & Demografi">
                        <span class="wm-nav-icon"><i data-lucide="bar-chart-3"></i></span>
                        <span class="wm-nav-text">Laporan Kependudukan</span>
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

                <li class="wm-nav-header">KEUANGAN RW</li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.dashboard') }}" class="wm-nav-link {{ request()->routeIs('keuangan.dashboard') || request()->routeIs('keuangan.index') ? 'active' : '' }}" title="Dashboard Keuangan RW">
                        <span class="wm-nav-icon"><i data-lucide="layout-dashboard"></i></span>
                        <span class="wm-nav-text">Dashboard Keuangan</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'kas_rw']) }}" class="wm-nav-link {{ request()->routeIs('keuangan.buku_kas.*') && request('jenis') === 'kas_rw' ? 'active' : '' }}" title="Kas RW">
                        <span class="wm-nav-icon"><i data-lucide="landmark"></i></span>
                        <span class="wm-nav-text">Kas RW</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'kas_rt']) }}" class="wm-nav-link {{ request()->routeIs('keuangan.buku_kas.*') && request('jenis') === 'kas_rt' ? 'active' : '' }}" title="Kas RT (001 - 008)">
                        <span class="wm-nav-icon"><i data-lucide="users"></i></span>
                        <span class="wm-nav-text">Kas RT (001 - 008)</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'kas_sampah']) }}" class="wm-nav-link {{ request()->routeIs('keuangan.buku_kas.*') && request('jenis') === 'kas_sampah' ? 'active' : '' }}" title="Kas Sampah">
                        <span class="wm-nav-icon"><i data-lucide="trash-2"></i></span>
                        <span class="wm-nav-text">Kas Sampah</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'dana_kegiatan']) }}" class="wm-nav-link {{ request()->routeIs('keuangan.buku_kas.*') && request('jenis') === 'dana_kegiatan' ? 'active' : '' }}" title="Dana Kegiatan">
                        <span class="wm-nav-icon"><i data-lucide="calendar-check"></i></span>
                        <span class="wm-nav-text">Dana Kegiatan</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('tagihan.index') }}" class="wm-nav-link {{ request()->routeIs('tagihan.*') ? 'active' : '' }}" title="Tagihan & Iuran">
                        <span class="wm-nav-icon"><i data-lucide="receipt"></i></span>
                        <span class="wm-nav-text">Tagihan & Iuran</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.transaksi.index') }}" class="wm-nav-link {{ request()->routeIs('keuangan.transaksi.*') ? 'active' : '' }}" title="Transaksi">
                        <span class="wm-nav-icon"><i data-lucide="history"></i></span>
                        <span class="wm-nav-text">Transaksi</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.laporan.index') }}" class="wm-nav-link {{ request()->routeIs('keuangan.laporan.*') ? 'active' : '' }}" title="Laporan Keuangan">
                        <span class="wm-nav-icon"><i data-lucide="pie-chart"></i></span>
                        <span class="wm-nav-text">Laporan Keuangan</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.pengaturan') }}" class="wm-nav-link {{ request()->routeIs('keuangan.pengaturan*') ? 'active' : '' }}" title="Pengaturan Iuran">
                        <span class="wm-nav-icon"><i data-lucide="sliders"></i></span>
                        <span class="wm-nav-text">Pengaturan Iuran</span>
                    </a>
                </li>

                <li class="wm-nav-header">ADMINISTRASI RW</li>
                <li class="wm-nav-item">
                    <a href="{{ route('rw.surat.index') }}" class="wm-nav-link {{ request()->routeIs('rw.surat.*') ? 'active' : '' }}" title="Persetujuan Surat Masuk">
                        <span class="wm-nav-icon"><i data-lucide="file-badge-2"></i></span>
                        <span class="wm-nav-text">Persetujuan Surat</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('rw.surat_master.index') }}" class="wm-nav-link {{ request()->routeIs('rw.surat_master.*') ? 'active' : '' }}" title="Manajemen 18 Jenis & Template Surat">
                        <span class="wm-nav-icon"><i data-lucide="sliders"></i></span>
                        <span class="wm-nav-text">Master Jenis & Template</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('surat.index') }}" class="wm-nav-link {{ request()->routeIs('surat.index') || request()->routeIs('surat.create') || request()->routeIs('surat.show') ? 'active' : '' }}" title="Katalog Layanan Surat Warga">
                        <span class="wm-nav-icon"><i data-lucide="layout-grid"></i></span>
                        <span class="wm-nav-text">Katalog Layanan Surat</span>
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

                <li class="wm-nav-header">MANAJEMEN WARGA</li>
                <li class="wm-nav-item">
                    <a href="{{ route('warga.index') }}" class="wm-nav-link {{ request()->routeIs('warga.index') || request()->routeIs('warga.create') || request()->routeIs('warga.edit') ? 'active' : '' }}" title="Data Warga RT Sendiri">
                        <span class="wm-nav-icon"><i data-lucide="users"></i></span>
                        <span class="wm-nav-text">Warga {{ $currentUser->clean_rt_label }}</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('kependudukan.keluarga') }}" class="wm-nav-link {{ request()->routeIs('kependudukan.keluarga') ? 'active' : '' }}" title="Data Keluarga & Kepala Keluarga">
                        <span class="wm-nav-icon"><i data-lucide="home"></i></span>
                        <span class="wm-nav-text">Data Keluarga (KK)</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('kependudukan.verifikasi') }}" class="wm-nav-link {{ request()->routeIs('kependudukan.verifikasi') ? 'active' : '' }}" title="Verifikasi Warga RT">
                        <span class="wm-nav-icon"><i data-lucide="user-check"></i></span>
                        <span class="wm-nav-text">Verifikasi Warga</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('arsip-kk.index') }}" class="wm-nav-link {{ request()->routeIs('arsip-kk.*') ? 'active' : '' }}" title="Arsip KK Resmi RT">
                        <span class="wm-nav-icon"><i data-lucide="folder-check"></i></span>
                        <span class="wm-nav-text">Arsip KK Resmi</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('rt.pendatang') }}" class="wm-nav-link {{ request()->routeIs('rt.pendatang') ? 'active' : '' }}" title="Data Pendatang / Kontrak">
                        <span class="wm-nav-icon"><i data-lucide="user-check"></i></span>
                        <span class="wm-nav-text">Data Pendatang</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('rt.mutasi') }}" class="wm-nav-link {{ request()->routeIs('rt.mutasi') ? 'active' : '' }}" title="Mutasi Warga">
                        <span class="wm-nav-icon"><i data-lucide="arrow-left-right"></i></span>
                        <span class="wm-nav-text">Mutasi Warga</span>
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

                <li class="wm-nav-header">KEUANGAN RT</li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.dashboard') }}" class="wm-nav-link {{ request()->routeIs('keuangan.dashboard') || request()->routeIs('keuangan.index') ? 'active' : '' }}" title="Dashboard Keuangan RT">
                        <span class="wm-nav-icon"><i data-lucide="layout-dashboard"></i></span>
                        <span class="wm-nav-text">Dashboard Keuangan RT</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'kas_rt']) }}" class="wm-nav-link {{ request()->routeIs('keuangan.buku_kas.*') && request('jenis') === 'kas_rt' ? 'active' : '' }}" title="Buku Kas RT">
                        <span class="wm-nav-icon"><i data-lucide="wallet"></i></span>
                        <span class="wm-nav-text">Buku Kas {{ $currentUser->clean_rt_label }}</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'kas_sampah']) }}" class="wm-nav-link {{ request()->routeIs('keuangan.buku_kas.*') && request('jenis') === 'kas_sampah' ? 'active' : '' }}" title="Kas Sampah">
                        <span class="wm-nav-icon"><i data-lucide="trash-2"></i></span>
                        <span class="wm-nav-text">Kas Sampah {{ $currentUser->clean_rt_label }}</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('tagihan.index') }}" class="wm-nav-link {{ request()->routeIs('tagihan.*') ? 'active' : '' }}" title="Tagihan Iuran Warga">
                        <span class="wm-nav-icon"><i data-lucide="receipt"></i></span>
                        <span class="wm-nav-text">Tagihan Iuran Warga</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.transaksi.create') }}" class="wm-nav-link {{ request()->routeIs('keuangan.transaksi.create') ? 'active' : '' }}" title="Catat Transaksi">
                        <span class="wm-nav-icon"><i data-lucide="plus-circle"></i></span>
                        <span class="wm-nav-text">Catat Transaksi</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.laporan.index') }}" class="wm-nav-link {{ request()->routeIs('keuangan.laporan.*') ? 'active' : '' }}" title="Laporan Keuangan RT">
                        <span class="wm-nav-icon"><i data-lucide="pie-chart"></i></span>
                        <span class="wm-nav-text">Laporan Keuangan RT</span>
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
                <li class="wm-nav-item">
                    <a href="{{ route('pengumuman.index') }}" class="wm-nav-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}" title="Pengumuman RW">
                        <span class="wm-nav-icon"><i data-lucide="megaphone"></i></span>
                        <span class="wm-nav-text">Pengumuman RW</span>
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
                    <a href="{{ route('keuangan.dashboard') }}" class="wm-nav-link {{ request()->routeIs('keuangan.dashboard') || request()->routeIs('keuangan.index') ? 'active' : '' }}" title="Tagihan Iuran Keluarga">
                        <span class="wm-nav-icon"><i data-lucide="receipt"></i></span>
                        <span class="wm-nav-text">Tagihan Iuran Keluarga</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('tagihan.index') }}" class="wm-nav-link {{ request()->routeIs('tagihan.index') ? 'active' : '' }}" title="Riwayat Pembayaran">
                        <span class="wm-nav-icon"><i data-lucide="history"></i></span>
                        <span class="wm-nav-text">Riwayat Pembayaran</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('keuangan.dashboard') }}#bayar" class="wm-nav-link" title="Bayar Iuran / Konfirmasi">
                        <span class="wm-nav-icon"><i data-lucide="credit-card"></i></span>
                        <span class="wm-nav-text">Bayar Iuran / Konfirmasi</span>
                    </a>
                </li>
                <li class="wm-nav-item">
                    <a href="{{ route('tagihan.index', ['status' => 'Lunas']) }}" class="wm-nav-link" title="Bukti Pembayaran / Kuitansi">
                        <span class="wm-nav-icon"><i data-lucide="file-check"></i></span>
                        <span class="wm-nav-text">Bukti Pembayaran / Kuitansi</span>
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
                <h6>RW 013 &bull; Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293</h6>
                <small class="d-none d-sm-inline">Sistem Koordinasi & Pelayanan Terpadu Warga</small>
            </div>
        </div>

        <div class="wm-topbar-right">
            <!-- Informasi Tanggal Hari Ini -->
            <div class="wm-pill-badge d-none d-md-inline-flex" id="currentDatePill">
                <i data-lucide="calendar" style="width: 15px; height: 15px; color: var(--wm-primary);"></i>
                <span id="liveDateText">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>

            <!-- Status Wilayah RW 013 -->
            <div class="wm-pill-badge success d-none d-sm-inline-flex">
                <span class="wm-pulse-dot"></span>
                <span>Kondusif & Aman</span>
            </div>

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
                            <small class="d-block text-muted" style="font-size: 0.7rem;">{{ ucfirst(Auth::user()->role) }}</small>
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
        <!-- Flash Message Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm animate__animated animate__fadeInDown mb-4" role="alert" style="background:#E3F5EC;color:#16865D;border-radius:14px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle fa-lg"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm animate__animated animate__fadeInDown mb-4" role="alert" style="background:#FEE2E2;color:#DC2626;border-radius:14px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm animate__animated animate__fadeInDown mb-4" role="alert" style="background:#FEE2E2;color:#DC2626;border-radius:14px;">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-times-circle fa-lg mt-1"></i>
                    <div>
                        <strong>Terdapat kesalahan pada input:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Global Multi-step Back Button for subpages -->
        @auth
            @unless(request()->routeIs('dashboard'))
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
            <p class="mb-1 fw-semibold">&copy; {{ date('Y') }} Warkas Machi &bull; Sistem Koordinasi & Pelayanan Terpadu RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293</p>
            <p class="mb-0 text-secondary" style="font-size: 0.74rem;">Mewujudkan Lingkungan Rukun, Tertib, Transparan, dan Sejahtera di Bandung Raya.</p>
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

    @stack('scripts')
</body>
</html>
