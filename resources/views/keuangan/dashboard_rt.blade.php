@extends('layouts.app')

@section('title', 'Manajemen Keuangan RT ' . $rtStr)

@section('content')
<!-- Tailwind CDN with Preflight Disabled to Prevent Conflict with Bootstrap & Warkas Theme -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: ['class', '[data-theme="dark"]'],
        corePlugins: {
            preflight: false,
        },
        theme: {
            extend: {
                colors: {
                    wmNavy: '#0F3966',
                    wmPrimary: '#1456A0',
                    wmSecondary: '#2878D0',
                    wmLightBlue: '#EAF3FF',
                    wmGreen: '#16865D',
                    wmLightGreen: '#E8F6EF',
                    wmGold: '#E9B949',
                }
            }
        }
    }
</script>

<style>
    /* Standarisasi Desain & Palet Warna Haven */
    .wm-fin-card {
        background-color: var(--wm-card, #FFFFFF) !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
        color: var(--wm-text, #172B4D) !important;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .wm-fin-card:hover {
        box-shadow: 0 8px 25px rgba(20, 86, 160, 0.08);
    }
    .wm-fin-header {
        background: linear-gradient(135deg, #0F3966 0%, #1456A0 50%, #1E6EC8 100%);
        border-radius: 22px;
        color: #FFFFFF !important;
    }
    .wm-fin-header h1, .wm-fin-header h2, .wm-fin-header p, .wm-fin-header span {
        color: #FFFFFF !important;
    }
    .wm-tab-btn {
        padding: 10px 18px;
        font-size: 0.84rem;
        font-weight: 700;
        border-radius: 12px;
        color: #4A5568;
        background: transparent;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap !important;
        flex-shrink: 0;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    .wm-tab-btn:hover {
        color: #1456A0;
        background: #EAF3FF;
    }
    .wm-tab-btn.active {
        color: #FFFFFF !important;
        background: #1456A0 !important;
        box-shadow: 0 4px 12px rgba(20, 86, 160, 0.25);
    }
    .wm-input-fin {
        background-color: var(--wm-card, #FFFFFF) !important;
        color: var(--wm-text, #172B4D) !important;
        border: 1.5px solid var(--wm-border, #CBD5E1) !important;
        border-radius: 12px;
    }
    .wm-input-fin:focus {
        border-color: #1456A0 !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(20, 86, 160, 0.15);
    }
    [data-theme="dark"] .wm-tab-btn {
        color: #CBD5E1;
    }
    [data-theme="dark"] .wm-tab-btn:hover {
        background: rgba(20, 86, 160, 0.2);
        color: #93C5FD;
    }
    [data-theme="dark"] .bg-white, body.dark-mode .bg-white {
        background-color: var(--wm-card) !important;
        color: var(--wm-text) !important;
    }
    [data-theme="dark"] .bg-slate-50, body.dark-mode .bg-slate-50 {
        background-color: #121C30 !important;
        color: var(--wm-text) !important;
    }
    [data-theme="dark"] .border-slate-100, [data-theme="dark"] .border-slate-200,
    body.dark-mode .border-slate-100, body.dark-mode .border-slate-200 {
        border-color: var(--wm-border) !important;
    }
    [data-theme="dark"] .text-slate-800, [data-theme="dark"] .text-slate-900,
    body.dark-mode .text-slate-800, body.dark-mode .text-slate-900 {
        color: #F1F5F9 !important;
    }
    [data-theme="dark"] .text-slate-700, [data-theme="dark"] .text-slate-600,
    body.dark-mode .text-slate-700, body.dark-mode .text-slate-600 {
        color: #CBD5E1 !important;
    }
    [data-theme="dark"] .text-slate-500, [data-theme="dark"] .text-slate-400,
    body.dark-mode .text-slate-500, body.dark-mode .text-slate-400 {
        color: #94A3B8 !important;
    }
</style>

<div class="container-fluid p-0 space-y-6">

    {{-- ======================================================================
         1. HEADER MANAJEMEN KEUANGAN RT
         Hero Banner Biru Besar HANYA tampil di tab utama (Ringkasan Keuangan).
         Tab lainnya menggunakan Header Compact yang hemat ruang layar.
         ====================================================================== --}}
    @if($activeTab === 'ringkasan')
    <div class="wm-fin-header p-6 md:p-8 relative overflow-hidden shadow-lg">
        <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
            <i data-lucide="wallet" style="width: 280px; height: 280px;"></i>
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md border border-white/30 tracking-wide">
                        OTONOM KEUANGAN RT {{ $rtStr }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/30 text-emerald-100 border border-emerald-400/30 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Rekening Otonom Terpisah
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-400/25 text-blue-100 border border-white/20">
                        RW 013 Sukamiskin &bull; {{ $totalKkRt }} KK Terdaftar
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight">Manajemen Keuangan RT {{ $rtStr }}</h1>
                <p class="text-blue-100 text-xs sm:text-sm mt-1 max-w-2xl">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 inline text-emerald-300"></i> Wilayah Rukun Tetangga {{ $rtStr }} &bull; Periode: <strong>{{ \Carbon\Carbon::parse($currentPeriode . '-01')->translatedFormat('F Y') }}</strong>
                </p>
            </div>

            <!-- Kontrol Periode & Tombol Aksi Utama -->
            <div class="flex flex-wrap items-center gap-2.5">
                <form method="GET" action="{{ route('keuangan.dashboard') }}" class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 py-2 rounded-xl border border-white/25">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">
                    <label for="filter_periode_rt" class="text-xs font-medium text-white mb-0 whitespace-nowrap">Periode:</label>
                    <input type="month" name="periode" id="filter_periode_rt" value="{{ $currentPeriode }}" onchange="this.form.submit()" 
                           class="text-xs font-bold rounded-lg px-2.5 py-1.5 outline-none cursor-pointer"
                           style="background: rgba(255, 255, 255, 0.18) !important; color: #FFFFFF !important; border: 1px solid rgba(255, 255, 255, 0.35) !important; color-scheme: dark;">
                </form>

                <a href="{{ route('keuangan.dashboard', ['tab' => 'transaksi', 'modal' => 'catat']) }}" class="inline-flex items-center gap-2 font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-md transition duration-150" style="background: #16A34A !important; color: #FFFFFF !important; border: none !important;">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Catat Transaksi RT
                </a>

                <a href="{{ route('keuangan.laporan.cetak', ['start_date' => $currentPeriode . '-01', 'end_date' => \Carbon\Carbon::parse($currentPeriode . '-01')->endOfMonth()->toDateString()]) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-white/20 hover:bg-white/30 border border-white/30 text-white font-semibold text-xs sm:text-sm px-3.5 py-2.5 rounded-xl transition duration-150">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan RT
                </a>
            </div>
        </div>

        <!-- Banner Saldo Utama RT (Termasuk Total Saldo Keseluruhan RT & Kolektibilitas Iuran RT) -->
        <div class="mt-6 pt-5 border-t border-white/20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- 1. Total Saldo Keseluruhan Lingkungan RT -->
            <div>
                <span class="text-xs text-emerald-200 font-bold uppercase tracking-wider">Total Saldo Keseluruhan</span>
                <p class="text-2xl sm:text-3xl font-black text-white mt-0.5">
                    Rp {{ number_format($totalSaldoKeseluruhanRt ?? 0, 0, ',', '.') }}
                </p>
                <span class="text-[10px] sm:text-[11px] text-emerald-100/90 font-medium leading-tight block">
                    Kas RT + Sampah + Keamanan/Ronda
                </span>
            </div>
            <!-- 2. Saldo Kas RT {{ $rtStr }} (Otonom) -->
            <div>
                <span class="text-xs text-blue-200 font-semibold uppercase tracking-wider">Kas RT {{ $rtStr }} (Otonom)</span>
                <p class="text-xl sm:text-2xl font-bold text-white mt-0.5">
                    Rp {{ number_format($bukuKasRt->saldo_sekarang ?? 0, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-blue-200/90">{{ $bukuKasRt->nama_bank ?? 'Kas Tunai' }} &bull; {{ $bukuKasRt->no_rekening ?? '-' }}</span>
            </div>
            <!-- 3. Pemasukan Kas RT Bulan Ini -->
            <div>
                <span class="text-xs text-emerald-200 font-semibold uppercase tracking-wider">Pemasukan Bulan Ini</span>
                <p class="text-xl sm:text-2xl font-bold text-emerald-300 mt-0.5">
                    +Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-blue-200/90">Iuran RT & Donasi</span>
            </div>
            <!-- 4. Pengeluaran Kas RT Bulan Ini -->
            <div>
                <span class="text-xs text-rose-200 font-semibold uppercase tracking-wider">Pengeluaran Bulan Ini</span>
                <p class="text-xl sm:text-2xl font-bold text-rose-300 mt-0.5">
                    -Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-blue-200/90">Operasional & Lingkungan RT</span>
            </div>
            <!-- 5. Kolektibilitas Iuran RT -->
            <div>
                <span class="text-xs text-blue-200 font-semibold uppercase tracking-wider">Kolektibilitas Iuran</span>
                <div class="flex items-center gap-2 mt-0.5">
                    <p class="text-xl sm:text-2xl font-black text-white">{{ $persenKolektibilitas }}%</p>
                    <span class="text-[11px] px-2 py-0.5 rounded-full font-bold {{ $persenKolektibilitas >= 80 ? 'bg-emerald-500/40 text-emerald-200' : 'bg-amber-500/40 text-amber-200' }}">
                        {{ $totalLunas }}/{{ $totalKkRt }} KK
                    </span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-400 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $persenKolektibilitas) }}%"></div>
                </div>
            </div>
        </div>
    </div>
    @else
    {{-- ======================================================================
         HEADER RAMPING / COMPACT UNTUK TAB OPERASIONAL
         Hemat ruang vertikal, langsung fokus ke data operasional tanpa scroll
         ====================================================================== --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200/70">
                    RT {{ $rtStr }} &bull; RW 013
                </span>
                <span class="text-xs text-slate-500">
                    Periode: <strong>{{ \Carbon\Carbon::parse($currentPeriode . '-01')->translatedFormat('F Y') }}</strong>
                </span>
                <span class="text-xs text-slate-300">&bull;</span>
                <span class="text-xs text-slate-600">
                    Kas RT: <strong class="text-emerald-700">Rp {{ number_format($bukuKasRt->saldo_sekarang ?? 0, 0, ',', '.') }}</strong>
                </span>
                <span class="text-xs text-slate-300">&bull;</span>
                <span class="text-xs text-slate-600">
                    Kas Sampah: <strong class="text-slate-700">Rp {{ number_format($kasSampah->saldo_sekarang ?? 0, 0, ',', '.') }}</strong>
                </span>
                <span class="text-xs text-slate-300">&bull;</span>
                <span class="text-xs text-slate-600">
                    Kas Ronda: <strong class="text-slate-700">Rp {{ number_format($kasKeamanan->saldo_sekarang ?? 0, 0, ',', '.') }}</strong>
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 mb-0 flex items-center gap-2">
                <i data-lucide="{{ $activeTab === 'buku_kas' ? 'wallet' : ($activeTab === 'kas_sampah' ? 'trash-2' : ($activeTab === 'tagihan' ? 'receipt' : ($activeTab === 'transaksi' ? 'arrow-left-right' : 'file-spreadsheet'))) }}" class="w-6 h-6 text-blue-600"></i>
                @if($activeTab === 'buku_kas')
                    Buku Kas RT {{ $rtStr }}
                @elseif($activeTab === 'kas_sampah')
                    Kas Pengelolaan Sampah
                @elseif($activeTab === 'tagihan')
                    Tagihan & Iuran Warga RT {{ $rtStr }}
                @elseif($activeTab === 'transaksi')
                    Transaksi & Mutasi Kas RT {{ $rtStr }}
                @elseif($activeTab === 'laporan')
                    Laporan Keuangan Bulanan RT {{ $rtStr }}
                @else
                    Manajemen Keuangan RT {{ $rtStr }}
                @endif
            </h1>
        </div>

        <!-- Filter Periode & Aksi Cepat Compact -->
        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('keuangan.dashboard') }}" class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <label for="filter_periode_rt_compact" class="text-xs font-semibold text-slate-600 mb-0 whitespace-nowrap">Periode:</label>
                <input type="month" name="periode" id="filter_periode_rt_compact" value="{{ $currentPeriode }}" onchange="this.form.submit()" 
                       class="text-xs font-bold text-slate-800 border-0 p-0 focus:ring-0 outline-none cursor-pointer bg-transparent">
            </form>

            <a href="{{ route('keuangan.dashboard', ['tab' => 'transaksi', 'modal' => 'catat']) }}" class="btn btn-sm btn-primary rounded-xl px-3.5 py-1.5 font-bold text-xs d-inline-flex align-items-center gap-1.5 shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Catat Transaksi
            </a>

            <a href="{{ route('keuangan.laporan.cetak', ['start_date' => $currentPeriode . '-01', 'end_date' => \Carbon\Carbon::parse($currentPeriode . '-01')->endOfMonth()->toDateString()]) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-xl px-3 py-1.5 font-semibold text-xs d-inline-flex align-items-center gap-1.5 bg-white shadow-sm">
                <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
            </a>
        </div>
    </div>
    @endif

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-0" role="alert" style="background: #E8F6EF; color: #16865D; border-radius: 14px;">
            <i data-lucide="check-circle-2" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-semibold small">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-0" role="alert" style="background: #FEE2E2; color: #DC2626; border-radius: 14px;">
            <i data-lucide="alert-triangle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-semibold small">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-0" role="alert" style="background: #EAF3FF; color: #1456A0; border-radius: 14px;">
            <i data-lucide="info" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-semibold small">{{ session('info') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ======================================================================
         2. NAVIGASI TAB TERPADU (MANAJEMEN KEUANGAN RT)
         Satukan: Ringkasan, Buku Kas RT, Kas Sampah, Tagihan, Transaksi, Laporan
         ====================================================================== --}}
    <div class="wm-fin-card p-2 bg-white flex flex-nowrap overflow-x-auto lg:flex-wrap items-center gap-1.5 shadow-sm" style="scrollbar-width: thin;">
        <a href="{{ route('keuangan.dashboard', ['tab' => 'ringkasan', 'periode' => $currentPeriode]) }}" class="wm-tab-btn {{ $activeTab === 'ringkasan' ? 'active' : '' }}">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
            <span>Ringkasan Keuangan</span>
        </a>
        <a href="{{ route('keuangan.dashboard', ['tab' => 'buku_kas', 'periode' => $currentPeriode]) }}" class="wm-tab-btn {{ $activeTab === 'buku_kas' ? 'active' : '' }}">
            <i data-lucide="wallet" class="w-4 h-4"></i>
            <span>Buku Kas RT {{ $rtStr }}</span>
        </a>
        <a href="{{ route('keuangan.dashboard', ['tab' => 'kas_sampah', 'periode' => $currentPeriode]) }}" class="wm-tab-btn {{ $activeTab === 'kas_sampah' ? 'active' : '' }}">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
            <span>Kas Sampah</span>
        </a>
        <a href="{{ route('keuangan.dashboard', ['tab' => 'tagihan', 'periode' => $currentPeriode]) }}" class="wm-tab-btn {{ $activeTab === 'tagihan' ? 'active' : '' }}">
            <i data-lucide="receipt" class="w-4 h-4"></i>
            <span>Tagihan & Iuran Warga</span>
        </a>
        <a href="{{ route('keuangan.dashboard', ['tab' => 'transaksi', 'periode' => $currentPeriode]) }}" class="wm-tab-btn {{ $activeTab === 'transaksi' ? 'active' : '' }}">
            <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
            <span>Transaksi Kas</span>
        </a>
        <a href="{{ route('keuangan.dashboard', ['tab' => 'laporan', 'periode' => $currentPeriode]) }}" class="wm-tab-btn {{ $activeTab === 'laporan' ? 'active' : '' }}">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            <span>Laporan Keuangan</span>
        </a>
    </div>

    {{-- ======================================================================
         3. TAB KONTEN
         ====================================================================== --}}

    {{-- TAB 1: RINGKASAN KEUANGAN --}}
    @if($activeTab === 'ringkasan')
        {{-- 3 Kartu Saldo Terpisah Pembentuk Total Saldo Keseluruhan Lingkungan RT --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Kartu 1: Kas Operasional RT --}}
            <div class="wm-fin-card p-6 border-l-4" style="border-left-color: #1456A0 !important;">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                            Kas Operasional RT (Otonom)
                        </span>
                        <h3 class="text-lg font-bold text-slate-800 mt-2 mb-0">Buku Kas RT {{ $rtStr }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Dikelola langsung oleh Pengurus RT {{ $rtStr }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center">
                        <i data-lucide="wallet" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-end justify-between">
                    <div>
                        <span class="text-xs text-slate-400 font-semibold uppercase">Saldo Kas RT</span>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-0.5">
                            Rp {{ number_format($bukuKasRt->saldo_sekarang ?? 0, 0, ',', '.') }}
                        </div>
                        <span class="text-[11px] text-slate-500 font-mono">{{ $bukuKasRt->nama_bank ?? 'Kas Tunai' }} &bull; {{ $bukuKasRt->no_rekening ?? '-' }}</span>
                    </div>
                    <a href="{{ route('keuangan.dashboard', ['tab' => 'buku_kas']) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        Buku Kas &rarr;
                    </a>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                    <span>Pemasukan Bulan Ini:</span>
                    <span class="font-bold text-emerald-700">+Rp {{ number_format($pemasukanBulanIni ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Kartu 2: Kas Pengelolaan Sampah --}}
            <div class="wm-fin-card p-6 border-l-4" style="border-left-color: #16865D !important;">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            Rekening Tingkat RW 013 (Bersama)
                        </span>
                        <h3 class="text-lg font-bold text-slate-800 mt-2 mb-0">Kas Pengelolaan Sampah</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Armada kebersihan & petugas sampah RW</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <i data-lucide="trash-2" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-end justify-between">
                    <div>
                        <span class="text-xs text-slate-400 font-semibold uppercase">Saldo Kas Sampah</span>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-0.5">
                            Rp {{ number_format($kasSampah->saldo_sekarang ?? 0, 0, ',', '.') }}
                        </div>
                        <span class="text-[11px] text-slate-500 font-mono">{{ $kasSampah->nama_bank ?? 'Bank BJB' }} &bull; {{ $kasSampah->no_rekening ?? '008-219-4820-101' }}</span>
                    </div>
                    <a href="{{ route('keuangan.dashboard', ['tab' => 'kas_sampah']) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
                        Buku Sampah &rarr;
                    </a>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                    <span>Kontribusi Warga RT {{ $rtStr }}:</span>
                    <span class="font-bold text-emerald-700">Rp {{ number_format($nominalTerkumpulSampahRt ?? 0, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">/ target Rp {{ number_format($nominalTargetSampahRt ?? 0, 0, ',', '.') }}</span></span>
                </div>
            </div>

            {{-- Kartu 3: Kas Keamanan & Pos Ronda --}}
            <div class="wm-fin-card p-6 border-l-4" style="border-left-color: #3B82F6 !important;">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                            Dana Operasional Lingkungan
                        </span>
                        <h3 class="text-lg font-bold text-slate-800 mt-2 mb-0">Kas Keamanan / Pos Ronda</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Operasional pos ronda & linmas siskamling</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-end justify-between">
                    <div>
                        <span class="text-xs text-slate-400 font-semibold uppercase">Saldo Kas Keamanan</span>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-0.5">
                            Rp {{ number_format($kasKeamanan->saldo_sekarang ?? 0, 0, ',', '.') }}
                        </div>
                        <span class="text-[11px] text-slate-500 font-mono">{{ $kasKeamanan->nama_bank ?? 'Bank Mandiri' }} &bull; {{ $kasKeamanan->no_rekening ?? '130-00-5839201-8' }}</span>
                    </div>
                    <span class="text-xs font-bold text-indigo-600 flex items-center gap-1">
                        Pos Ronda &bull; Siskamling
                    </span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                    <span>Penanggung Jawab:</span>
                    <span class="font-bold text-slate-700">{{ $kasKeamanan->nama_penanggung_jawab ?? 'Koordinator Keamanan' }}</span>
                </div>
            </div>
        </div>

        {{-- Note Banner: Rekonsiliasi Total Saldo Keseluruhan Lingkungan RT --}}
        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center gap-2.5">
            <i data-lucide="shield-check" class="w-5 h-5 text-blue-600 flex-shrink-0"></i>
            <span>
                <strong>Catatan Pemisahan Kas:</strong> Total Saldo Keseluruhan (<strong>Rp {{ number_format($totalSaldoKeseluruhanRt ?? 0, 0, ',', '.') }}</strong>) dihitung tepat dari penjumlahan 3 pos lingkungan di atas: Kas RT {{ $rtStr }} (Rp {{ number_format($bukuKasRt->saldo_sekarang ?? 0, 0, ',', '.') }}) + Kas Sampah (Rp {{ number_format($kasSampah->saldo_sekarang ?? 0, 0, ',', '.') }}) + Kas Keamanan/Ronda (Rp {{ number_format($kasKeamanan->saldo_sekarang ?? 0, 0, ',', '.') }}). Kas Induk RW dan buku kas RT lain dicatat terpisah dan tidak dicampur.
            </span>
        </div>

        {{-- Card Titipan Tunai RT ke RW/Unit (Jika Ada) --}}
        @if(($titipanPendingNominal ?? 0) > 0)
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow">
                        <i data-lucide="banknote" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">Titipan Iuran Tunai Warga RT ke RW</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-200 text-amber-900">{{ $titipanPendingCount }} Komponen</span>
                        </div>
                        <p class="text-xs text-amber-800 mt-0.5 mb-0">
                            Warga membayar tunai ke RT sejumlah <strong>Rp {{ number_format($titipanPendingNominal, 0, ',', '.') }}</strong> yang merupakan alokasi Kas RW / Unit. Dana ini tidak masuk saldo kas RT dan menunggu disetor ke RW.
                        </p>
                    </div>
                </div>

                <form action="{{ route('tagihan.setor_tunai_rw') }}" method="POST" onsubmit="return confirm('Setorkan seluruh dana titipan tunai RT {{ $rtStr }} sebesar Rp {{ number_format($titipanPendingNominal, 0, ',', '.') }} ke Kas RW/Unit terkait?')">
                    @csrf
                    <input type="hidden" name="rt_number" value="{{ $rtStr }}">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow flex items-center gap-2 transition whitespace-nowrap">
                        <i data-lucide="send" class="w-4 h-4"></i> Setorkan ke RW Sekarang
                    </button>
                </form>
            </div>
        @endif

        {{-- Kartu Metrik Iuran Warga RT --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- 1. KK Lunas -->
            <a href="{{ route('keuangan.dashboard', ['tab' => 'tagihan', 'status_tagihan' => 'Lunas']) }}" class="text-decoration-none group d-block h-full">
                <div class="wm-fin-card p-4 h-full flex flex-col justify-between border border-slate-200 rounded-2xl relative overflow-hidden transition-all duration-200 hover:-translate-y-1 hover:shadow-md bg-white border-t-[3.5px] border-t-[#16865D]">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">KK Lunas</span>
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold" style="background: #E8F6EF; color: #16865D;">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <p class="text-2xl font-black text-emerald-600 mb-1">{{ $totalLunas }} <span class="text-xs font-normal text-slate-400">KK</span></p>
                        <span class="text-[11px] text-slate-400">Telah diverifikasi lunas</span>
                    </div>
                    <div class="pt-2 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                        <span class="text-emerald-700 font-medium">Bulan Berjalan</span>
                        <span class="text-emerald-700 font-bold group-hover:underline flex items-center gap-0.5">Lihat &rarr;</span>
                    </div>
                </div>
            </a>

            <!-- 2. Menunggu Verifikasi -->
            <a href="{{ route('keuangan.dashboard', ['tab' => 'tagihan', 'status_tagihan' => 'Menunggu Konfirmasi']) }}" class="text-decoration-none group d-block h-full">
                <div class="wm-fin-card p-4 h-full flex flex-col justify-between border border-slate-200 rounded-2xl relative overflow-hidden transition-all duration-200 hover:-translate-y-1 hover:shadow-md bg-white border-t-[3.5px] border-t-[#D97706]">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Menunggu Verifikasi</span>
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold" style="background: #FFFBEB; color: #D97706;">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <p class="text-2xl font-black text-amber-600 mb-1">{{ $totalMenunggu }} <span class="text-xs font-normal text-slate-400">KK</span></p>
                        <span class="text-[11px] text-slate-400">Bukti bayar warga masuk</span>
                    </div>
                    <div class="pt-2 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                        <span class="text-amber-700 font-medium">Perlu Konfirmasi</span>
                        <span class="text-amber-700 font-bold group-hover:underline flex items-center gap-0.5">Verifikasi &rarr;</span>
                    </div>
                </div>
            </a>

            <!-- 3. Belum Lunas -->
            <a href="{{ route('keuangan.dashboard', ['tab' => 'tagihan', 'status_tagihan' => 'Belum Dibayar']) }}" class="text-decoration-none group d-block h-full">
                <div class="wm-fin-card p-4 h-full flex flex-col justify-between border border-slate-200 rounded-2xl relative overflow-hidden transition-all duration-200 hover:-translate-y-1 hover:shadow-md bg-white border-t-[3.5px] border-t-[#DC2626]">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Belum Lunas</span>
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold" style="background: #FEF2F2; color: #DC2626;">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <p class="text-2xl font-black text-rose-600 mb-1">{{ $totalBelum }} <span class="text-xs font-normal text-slate-400">KK</span></p>
                        <span class="text-[11px] text-slate-400">Perlu tindak lanjut RT</span>
                    </div>
                    <div class="pt-2 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                        <span class="text-rose-700 font-medium">Tagihan Terbit</span>
                        <span class="text-rose-700 font-bold group-hover:underline flex items-center gap-0.5">Follow up &rarr;</span>
                    </div>
                </div>
            </a>

            <!-- 4. Dibebaskan (Dhuafa) -->
            <a href="{{ route('keuangan.dashboard', ['tab' => 'tagihan', 'status_tagihan' => 'Dibebaskan']) }}" class="text-decoration-none group d-block h-full">
                <div class="wm-fin-card p-4 h-full flex flex-col justify-between border border-slate-200 rounded-2xl relative overflow-hidden transition-all duration-200 hover:-translate-y-1 hover:shadow-md bg-white border-t-[3.5px] border-t-[#4F46E5]">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Dibebaskan (Dhuafa)</span>
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold" style="background: #EEF2FF; color: #4F46E5;">
                                <i data-lucide="heart-handshake" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <p class="text-2xl font-black text-indigo-600 mb-1">{{ $totalDibebaskan }} <span class="text-xs font-normal text-slate-400">KK</span></p>
                        <span class="text-[11px] text-slate-400">Program subsidi RW 013</span>
                    </div>
                    <div class="pt-2 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                        <span class="text-indigo-700 font-medium">Keringanan Iuran</span>
                        <span class="text-indigo-700 font-bold group-hover:underline flex items-center gap-0.5">Rincian &rarr;</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Mutasi Terakhir Kas RT (Tab Ringkasan) --}}
        <div class="wm-fin-card p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                        <i data-lucide="book-open" class="w-5 h-5 text-blue-600"></i> Mutasi Terakhir Buku Kas RT {{ $rtStr }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 mb-0">Catatan transaksi pemasukan dan pengeluaran kas lingkungan RT {{ $rtStr }}</p>
                </div>
                <a href="{{ route('keuangan.dashboard', ['tab' => 'buku_kas']) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-semibold text-xs">
                    Buku Kas Lengkap &rarr;
                </a>
            </div>

            <div class="table-responsive">
                <table class="table wm-table mb-0">
                    <thead>
                        <tr>
                            <th class="py-3 px-3">No. Transaksi</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Kategori & Keterangan</th>
                            <th class="py-3 px-3">Penyetor / Penerima</th>
                            <th class="py-3 px-3 text-end">Nominal</th>
                            <th class="py-3 px-3 text-end">Saldo Berjalan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksisRingkasan as $trx)
                            <tr>
                                <td class="py-3 px-3 font-mono fw-bold text-nowrap" style="color: var(--wm-text);">{{ $trx->nomor_transaksi }}</td>
                                <td class="py-3 px-3 text-muted text-nowrap" style="font-size: 0.82rem;">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-3">
                                    <div class="fw-bold" style="color: var(--wm-text);">{{ $trx->kategori }}</div>
                                    <div class="text-muted small truncate" style="max-width: 250px;">{{ $trx->keterangan }}</div>
                                </td>
                                <td class="py-3 px-3">{{ $trx->penyetor_penerima ?: '-' }}</td>
                                <td class="py-3 px-3 text-end fw-bold text-nowrap {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? 'text-success' : 'text-danger' }}">
                                    {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? '+' : '-' }}Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-end font-mono fw-bold text-nowrap" style="color: #1456A0;">
                                    Rp {{ number_format($trx->saldo_setelahnya, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-center text-muted">
                                    Belum ada transaksi yang tercatat pada buku kas RT ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Status Pertanggungjawaban LPJ ke RW 013 --}}
        <div class="wm-fin-card p-6" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border-color: #86EFAC !important;">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow">
                        <i data-lucide="send" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">
                                Pertanggungjawaban Keuangan RT ke RW 013
                            </span>
                            <span class="badge {{ $evalRt && $evalRt->status_laporan === 'Terkirim' ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-3 py-1 font-bold text-xs">
                                {{ $evalRt && $evalRt->status_laporan ? $evalRt->status_laporan : 'Belum Lapor' }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1">Laporan Akuntabilitas Kas RT {{ $rtStr }} (Tahun {{ $currentYear }})</h3>
                        @if($evalRt && $evalRt->catatan_verifikasi_rw)
                            <p class="text-xs text-emerald-900 mb-0 bg-white/60 p-2 rounded-lg border border-emerald-200">
                                <strong>Catatan Evaluasi Ketua RW:</strong> {{ $evalRt->catatan_verifikasi_rw }}
                            </p>
                        @else
                            <p class="text-xs text-slate-600 mb-0">
                                Laporan mutasi kas dan partisipasi iuran warga disampaikan secara berkala kepada Ketua RW untuk evaluasi transparansi lingkungan.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('keuangan.rt.lapor') }}" onsubmit="return confirm('Kirimkan laporan keuangan RT {{ $rtStr }} ke Ketua RW 013?')">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-2 shadow transition">
                            <i data-lucide="send" class="w-4 h-4"></i> Kirim Laporan ke Ketua RW
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 2: BUKU KAS RT --}}
    @if($activeTab === 'buku_kas')
        <div class="wm-fin-card p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-0">
                        <i data-lucide="wallet" class="w-5 h-5 text-blue-600"></i> Buku Kas Otonom RT {{ $rtStr }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 mb-0">Saldo Saat Ini: <strong class="text-blue-700">Rp {{ number_format($bukuKasRt->saldo_sekarang ?? 0, 0, ',', '.') }}</strong> &bull; {{ $bukuKasRt->nama_bank ?? 'Kas Tunai' }} ({{ $bukuKasRt->no_rekening ?? '-' }})</p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('keuangan.dashboard', ['tab' => 'transaksi', 'modal' => 'catat']) }}" class="btn btn-sm btn-primary rounded-pill px-3 font-semibold text-xs">
                        <i data-lucide="plus" class="w-4 h-4 inline"></i> Catat Transaksi
                    </a>
                </div>
            </div>

            {{-- Filter Toolbar Buku Kas --}}
            <form method="GET" action="{{ route('keuangan.dashboard') }}" class="row g-2 mb-4">
                <input type="hidden" name="tab" value="buku_kas">
                <input type="hidden" name="periode" value="{{ $currentPeriode }}">

                <div class="col-md-3 col-sm-6">
                    <input type="text" name="buku_kas_search" value="{{ request('buku_kas_search') }}" placeholder="Cari transaksi / keterangan..." class="form-control form-control-sm rounded-3">
                </div>
                <div class="col-md-2 col-sm-6">
                    <select name="buku_kas_jenis" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Jenis</option>
                        <option value="pemasukan" {{ request('buku_kas_jenis') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="pengeluaran" {{ request('buku_kas_jenis') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <input type="date" name="buku_kas_start" value="{{ request('buku_kas_start') }}" class="form-control form-control-sm rounded-3" title="Tanggal Mulai">
                </div>
                <div class="col-md-2 col-sm-6">
                    <input type="date" name="buku_kas_end" value="{{ request('buku_kas_end') }}" class="form-control form-control-sm rounded-3" title="Tanggal Selesai">
                </div>
                <div class="col-md-3 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 font-bold text-xs flex-grow-1">Filter</button>
                    <a href="{{ route('keuangan.dashboard', ['tab' => 'buku_kas', 'periode' => $currentPeriode]) }}" class="btn btn-sm btn-light border rounded-pill px-2.5" title="Reset">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table wm-table mb-0">
                    <thead>
                        <tr>
                            <th class="py-3 px-3">No. Transaksi</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3">Keterangan</th>
                            <th class="py-3 px-3">Pihak Terkait</th>
                            <th class="py-3 px-3 text-end">Nominal</th>
                            <th class="py-3 px-3 text-end">Saldo Berjalan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksisBukuKas as $trx)
                            <tr>
                                <td class="py-3 px-3 font-mono fw-bold text-nowrap" style="color: var(--wm-text);">{{ $trx->nomor_transaksi }}</td>
                                <td class="py-3 px-3 text-muted text-nowrap" style="font-size: 0.82rem;">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-3"><span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">{{ $trx->kategori }}</span></td>
                                <td class="py-3 px-3 small">{{ $trx->keterangan }}</td>
                                <td class="py-3 px-3 text-muted small">{{ $trx->penyetor_penerima ?: '-' }}</td>
                                <td class="py-3 px-3 text-end fw-bold text-nowrap {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? 'text-success' : 'text-danger' }}">
                                    {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? '+' : '-' }}Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-end font-mono fw-bold text-nowrap" style="color: #1456A0;">
                                    Rp {{ number_format($trx->saldo_setelahnya, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-5 text-center text-muted">Belum ada mutasi yang tercatat untuk filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- TAB 3: KAS SAMPAH --}}
    @if($activeTab === 'kas_sampah')
        <div class="wm-fin-card p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            Kas Unit Terpisah Lingkungan
                        </span>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-0">
                        <i data-lucide="trash-2" class="w-5 h-5 text-emerald-600"></i> Kas Pengelolaan Sampah Lingkungan
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 mb-0">
                        Saldo Rekening Sampah (Tingkat RW 013): <strong class="text-emerald-700">Rp {{ number_format($kasSampah->saldo_sekarang ?? 0, 0, ',', '.') }}</strong> &bull; {{ $kasSampah->nama_bank ?? 'Bank BJB' }} ({{ $kasSampah->no_rekening ?? '008-219-4820-101' }})
                    </p>
                </div>

                <div>
                    <span class="text-xs text-slate-400 font-semibold">Penanggung Jawab:</span>
                    <div class="text-xs font-bold text-slate-700">{{ $kasSampah->nama_penanggung_jawab ?? 'Koordinator Kebersihan Lingkungan' }}</div>
                </div>
            </div>

            {{-- Form Pencarian Transaksi Sampah --}}
            <form method="GET" action="{{ route('keuangan.dashboard') }}" class="row g-2 mb-4">
                <input type="hidden" name="tab" value="kas_sampah">
                <input type="hidden" name="periode" value="{{ $currentPeriode }}">
                <div class="col-md-5 col-sm-8">
                    <input type="text" name="sampah_search" value="{{ request('sampah_search') }}" placeholder="Cari mutasi kas sampah / retribusi..." class="form-control form-control-sm rounded-3">
                </div>
                <div class="col-md-2 col-sm-4">
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 font-bold text-xs w-100">Cari</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table wm-table mb-0">
                    <thead>
                        <tr>
                            <th class="py-3 px-3">No. Transaksi</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3">Keterangan</th>
                            <th class="py-3 px-3">Pihak Terkait</th>
                            <th class="py-3 px-3 text-end">Nominal</th>
                            <th class="py-3 px-3 text-end">Saldo Berjalan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksisKasSampah as $trx)
                            <tr>
                                <td class="py-3 px-3 font-mono fw-bold text-nowrap" style="color: var(--wm-text);">{{ $trx->nomor_transaksi }}</td>
                                <td class="py-3 px-3 text-muted text-nowrap" style="font-size: 0.82rem;">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-3"><span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">{{ $trx->kategori }}</span></td>
                                <td class="py-3 px-3 small">{{ $trx->keterangan }}</td>
                                <td class="py-3 px-3 text-muted small">{{ $trx->penyetor_penerima ?: '-' }}</td>
                                <td class="py-3 px-3 text-end fw-bold text-nowrap {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? 'text-success' : 'text-danger' }}">
                                    {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? '+' : '-' }}Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-end font-mono fw-bold text-nowrap" style="color: #16865D;">
                                    Rp {{ number_format($trx->saldo_setelahnya, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-5 text-center text-muted">Belum ada mutasi yang tercatat pada kas sampah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- TAB 4: TAGIHAN & IURAN WARGA (VISUALISASI RINCIAN PER KK) --}}
    @if($activeTab === 'tagihan')
        <div class="wm-fin-card p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-0">
                        <i data-lucide="receipt" class="w-5 h-5 text-blue-600"></i> Tagihan Iuran Warga RT {{ $rtStr }} (Periode {{ $currentPeriode }})
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 mb-0">Rincian komponen iuran per Kepala Keluarga: Iuran Kas RT, Kas Sampah, Kegiatan, & Titipan RW</p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="badge bg-blue-100 text-blue-900 rounded-pill px-3 py-1 font-bold text-xs">
                        Kolektibilitas: {{ $persenKolektibilitas }}% ({{ $totalLunas }}/{{ $totalKkRt }} KK)
                    </span>
                </div>
            </div>

            {{-- Filter Tagihan RT --}}
            <form method="GET" action="{{ route('keuangan.dashboard') }}" class="row g-2 mb-4">
                <input type="hidden" name="tab" value="tagihan">
                <input type="hidden" name="periode" value="{{ $currentPeriode }}">

                <div class="col-md-5 col-sm-6">
                    <input type="text" name="search_tagihan" value="{{ request('search_tagihan') }}" placeholder="Cari Nama Kepala Keluarga / Nomor KK / Rumah..." class="form-control form-control-sm rounded-3">
                </div>
                <div class="col-md-4 col-sm-6">
                    <select name="status_tagihan" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Status Tagihan</option>
                        <option value="Lunas" {{ request('status_tagihan') === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="Menunggu Konfirmasi" {{ request('status_tagihan') === 'Menunggu Konfirmasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="Belum Dibayar" {{ request('status_tagihan') === 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                        <option value="Terlambat" {{ request('status_tagihan') === 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 font-bold text-xs flex-grow-1">Filter</button>
                    <a href="{{ route('keuangan.dashboard', ['tab' => 'tagihan', 'periode' => $currentPeriode]) }}" class="btn btn-sm btn-light border rounded-pill px-2.5" title="Reset">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table wm-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="py-3 px-3">No. Tagihan</th>
                            <th class="py-3 px-3">Kepala Keluarga</th>
                            <th class="py-3 px-3">Rincian Komponen Tagihan</th>
                            <th class="py-3 px-3 text-end">Total Tagihan</th>
                            <th class="py-3 px-3 text-end">Sudah Dibayar</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihansRt as $tagihan)
                            <tr>
                                <td class="py-3 px-3 font-mono font-bold text-nowrap" style="color: var(--wm-text);">
                                    {{ $tagihan->nomor_tagihan }}
                                </td>
                                <td class="py-3 px-3">
                                    <div class="fw-bold" style="color: var(--wm-text);">{{ $tagihan->kartuKeluarga->nama_kepala_keluarga ?? '-' }}</div>
                                    <div class="text-muted small font-mono">KK: {{ $tagihan->kartuKeluarga->masked_no_kk ?? '-' }} &bull; No. {{ $tagihan->kartuKeluarga->nomor_rumah ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($tagihan->items && $tagihan->items->count() > 0)
                                            @foreach($tagihan->items as $it)
                                                @php
                                                    $isRwItem = ($it->pemilik_dana === 'RW' || ($it->bukuKas && $it->bukuKas->jenis_kas === 'kas_rw'));
                                                    $badgeBg = $it->status_pembayaran === 'lunas' ? '#ECFDF5' : '#F8FAFC';
                                                    $badgeBorder = $it->status_pembayaran === 'lunas' ? '#A7F3D0' : '#E2E8F0';
                                                    $badgeColor = $it->status_pembayaran === 'lunas' ? '#065F46' : '#64748B';
                                                @endphp
                                                <span class="badge" style="background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }}; color: {{ $badgeColor }}; font-size: 0.68rem; font-weight: 600; padding: 2px 6px;">
                                                    {{ $it->nama_komponen }}: Rp {{ number_format($it->nominal_subtotal, 0, ',', '.') }}
                                                    @if($isRwItem)
                                                        <span class="badge bg-warning text-dark ms-0.5" style="font-size: 0.6rem; padding: 1px 3px;">Titipan RW</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="badge" style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; font-size: 0.68rem; font-weight: 600; padding: 2px 6px;">
                                                Kas RT: Rp {{ number_format($tagihan->nominal_rt, 0, ',', '.') }}
                                            </span>
                                            <span class="badge" style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; font-size: 0.68rem; font-weight: 600; padding: 2px 6px;">
                                                Kas RW: Rp {{ number_format($tagihan->nominal_rw, 0, ',', '.') }}
                                                <span class="badge bg-warning text-dark ms-0.5" style="font-size: 0.6rem; padding: 1px 3px;">Titipan RW</span>
                                            </span>
                                            <span class="badge" style="background: #FFFBEB; border: 1px solid #FDE68A; color: #B45309; font-size: 0.68rem; font-weight: 600; padding: 2px 6px;">
                                                Sampah: Rp {{ number_format($tagihan->nominal_sampah, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-end fw-bold text-nowrap" style="color: #1456A0;">
                                    Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-end fw-bold text-nowrap text-success">
                                    Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center text-nowrap">
                                    <span class="badge {{ $tagihan->status === 'Lunas' ? 'bg-success' : ($tagihan->status === 'Menunggu Konfirmasi' ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-1 font-bold text-xs">
                                        {{ $tagihan->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center text-nowrap">
                                    <a href="{{ route('tagihan.show', $tagihan->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 text-xs font-semibold">
                                        Detail
                                    </a>
                                    @if($tagihan->status === 'Lunas')
                                        <a href="{{ route('tagihan.kuitansi', $tagihan->id) }}" target="_blank" class="btn btn-sm btn-light border rounded-circle p-1 ms-1 text-muted" title="Cetak Kuitansi">
                                            <i data-lucide="printer" style="width: 14px; height: 14px;"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-5 text-center text-muted">Belum ada data tagihan warga RT {{ $rtStr }} di periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- TAB 5: TRANSAKSI KAS (FORM CATAT + JURNAL TRANSAKSI) --}}
    @if($activeTab === 'transaksi')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form Catat Transaksi Kas RT --}}
            <div class="lg:col-span-1">
                <div class="wm-fin-card p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-1 flex items-center gap-2">
                        <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-600"></i> Catat Transaksi Baru
                    </h3>
                    <p class="text-xs text-slate-500 mb-4">Pencatatan kas pemasukan atau pengeluaran lingkungan RT</p>

                    <form method="POST" action="{{ route('keuangan.transaksi.store') }}">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="form-label text-xs font-bold text-slate-700">Target Buku Kas *</label>
                                <select name="buku_kas_id" class="form-select form-select-sm wm-input-fin" required>
                                    @foreach($bukuKasPilihan as $bk)
                                        <option value="{{ $bk->id }}">{{ $bk->nama_kas }} (Saldo: Rp {{ number_format($bk->saldo_sekarang, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label text-xs font-bold text-slate-700">Jenis Transaksi *</label>
                                <select name="jenis_transaksi" id="transaksi_jenis" class="form-select form-select-sm wm-input-fin" required onchange="toggleKategori(this.value)">
                                    <option value="pemasukan">Pemasukan Kas (+)</option>
                                    <option value="pengeluaran">Pengeluaran Kas (-)</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label text-xs font-bold text-slate-700">Tanggal Transaksi *</label>
                                <input type="date" name="tanggal_transaksi" value="{{ date('Y-m-d') }}" class="form-control form-control-sm wm-input-fin" required>
                            </div>

                            <div>
                                <label class="form-label text-xs font-bold text-slate-700">Kategori Transaksi *</label>
                                <select name="kategori" id="transaksi_kategori" class="form-select form-select-sm wm-input-fin" required>
                                    <optgroup label="Pemasukan RT" id="opt_pemasukan">
                                        @foreach($kategoriPemasukanRt as $kat)
                                            <option value="{{ $kat }}">{{ $kat }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Pengeluaran RT" id="opt_pengeluaran" style="display: none;">
                                        @foreach($kategoriPengeluaranRt as $kat)
                                            <option value="{{ $kat }}">{{ $kat }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>

                            <div>
                                <label class="form-label text-xs font-bold text-slate-700">Nominal (Rp) *</label>
                                <input type="number" name="nominal" min="1000" step="1000" placeholder="Contoh: 50000" class="form-control form-control-sm wm-input-fin" required>
                            </div>

                            <div>
                                <label class="form-label text-xs font-bold text-slate-700">Penyetor / Penerima</label>
                                <input type="text" name="penyetor_penerima" placeholder="Nama warga / toko / petugas..." class="form-control form-control-sm wm-input-fin">
                            </div>

                            <div>
                                <label class="form-label text-xs font-bold text-slate-700">Keterangan / Deskripsi *</label>
                                <textarea name="keterangan" rows="2" placeholder="Uraian kebutuhan transaksi..." class="form-control form-control-sm wm-input-fin" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill w-100 font-bold text-xs py-2 mt-2 shadow-sm">
                                <i data-lucide="save" class="w-4 h-4 inline me-1"></i> Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Jurnal Mutasi RT --}}
            <div class="lg:col-span-2">
                <div class="wm-fin-card p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-1 flex items-center gap-2">
                        <i data-lucide="list" class="w-5 h-5 text-blue-600"></i> Jurnal Transaksi RT {{ $rtStr }}
                    </h3>
                    <p class="text-xs text-slate-500 mb-4">Daftar entri jurnal pembukuan kas yang tercatat</p>

                    <div class="table-responsive">
                        <table class="table wm-table mb-0">
                            <thead>
                                <tr>
                                    <th class="py-2.5 px-3">No. Transaksi</th>
                                    <th class="py-2.5 px-3">Tanggal</th>
                                    <th class="py-2.5 px-3">Kategori & Keterangan</th>
                                    <th class="py-2.5 px-3 text-end">Nominal</th>
                                    <th class="py-2.5 px-3 text-end">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jurnalTransaksis as $trx)
                                    <tr>
                                        <td class="py-2.5 px-3 font-mono fw-bold text-xs">{{ $trx->nomor_transaksi }}</td>
                                        <td class="py-2.5 px-3 text-muted text-xs">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d/m/Y') }}</td>
                                        <td class="py-2.5 px-3">
                                            <div class="fw-bold text-xs">{{ $trx->kategori }}</div>
                                            <div class="text-muted text-[11px] truncate" style="max-width: 200px;">{{ $trx->keterangan }}</div>
                                        </td>
                                        <td class="py-2.5 px-3 text-end fw-bold text-xs text-nowrap {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? 'text-success' : 'text-danger' }}">
                                            {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? '+' : '-' }}Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-3 text-end font-mono fw-bold text-xs text-nowrap text-blue-700">
                                            Rp {{ number_format($trx->saldo_setelahnya, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-muted text-xs">Belum ada transaksi di jurnal RT.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($jurnalTransaksis->hasPages())
                        <div class="mt-4">
                            {{ $jurnalTransaksis->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 6: LAPORAN KEUANGAN RT --}}
    @if($activeTab === 'laporan')
        <div class="wm-fin-card p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-0">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i> Rekapitulasi Laporan Keuangan RT {{ $rtStr }} (Tahun {{ $currentYear }})
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 mb-0">Kinerja arus kas bulanan RT {{ $rtStr }} tahun buku {{ $currentYear }}</p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('keuangan.laporan.cetak', ['start_date' => $currentYear . '-01-01', 'end_date' => $currentYear . '-12-31']) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3 font-semibold text-xs">
                        <i data-lucide="printer" class="w-4 h-4 inline me-1"></i> Cetak Laporan Tahunan
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table wm-table mb-0 text-center align-middle">
                    <thead>
                        <tr>
                            <th class="py-3 px-3 text-start">Bulan</th>
                            <th class="py-3 px-3 text-end">Total Pemasukan RT</th>
                            <th class="py-3 px-3 text-end">Total Pengeluaran RT</th>
                            <th class="py-3 px-3 text-end">Surplus / (Defisit)</th>
                            <th class="py-3 px-3 text-center">Status Periode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapBulananRt as $rek)
                            <tr>
                                <td class="py-3 px-3 text-start fw-bold text-slate-800">
                                    {{ $rek['nama_bulan'] }} {{ $currentYear }}
                                </td>
                                <td class="py-3 px-3 text-end text-success fw-bold">
                                    Rp {{ number_format($rek['pemasukan'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-end text-danger fw-bold">
                                    Rp {{ number_format($rek['pengeluaran'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-end fw-bold {{ $rek['selisih'] >= 0 ? 'text-primary' : 'text-danger' }}">
                                    {{ $rek['selisih'] >= 0 ? '+' : '' }}Rp {{ number_format($rek['selisih'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($rek['pemasukan'] > 0 || $rek['pengeluaran'] > 0)
                                        <span class="badge bg-success rounded-pill px-2.5 py-1 text-xs">Tercatat</span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 text-xs">Nihil</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

<script>
    function toggleKategori(val) {
        var optIn = document.getElementById('opt_pemasukan');
        var optOut = document.getElementById('opt_pengeluaran');
        var katSelect = document.getElementById('transaksi_kategori');

        if (val === 'pengeluaran') {
            optIn.style.display = 'none';
            optOut.style.display = 'block';
            katSelect.value = optOut.children[0].value;
        } else {
            optIn.style.display = 'block';
            optOut.style.display = 'none';
            katSelect.value = optIn.children[0].value;
        }
    }
</script>
@endsection