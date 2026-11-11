@extends('layouts.app')

@section('title', 'Dashboard Keuangan RW 013 - Warkas Machi')

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
                    wmGreen: '#16865D',
                }
            }
        }
    }
</script>

<style>
    /* Styling Terpadu & Kompatibilitas Dark/Light Mode */
    .wm-fin-card {
        background-color: var(--wm-card) !important;
        border: 1px solid var(--wm-border) !important;
        color: var(--wm-text) !important;
        border-radius: 20px;
        box-shadow: var(--wm-shadow);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .wm-fin-card:hover {
        box-shadow: var(--wm-shadow-lg);
    }
    .wm-fin-header {
        background: linear-gradient(135deg, #0F3966 0%, #1456A0 50%, #1E6EC8 100%);
        border-radius: 22px;
        color: #FFFFFF !important;
    }
    .wm-fin-header h1, .wm-fin-header h2, .wm-fin-header p, .wm-fin-header span {
        color: #FFFFFF !important;
    }
    .wm-text-main {
        color: var(--wm-text) !important;
    }
    .wm-text-sub {
        color: var(--wm-muted) !important;
    }
    .wm-bg-subtle {
        background-color: var(--wm-bg) !important;
    }
    .wm-border-subtle {
        border-color: var(--wm-border) !important;
    }
    .wm-input-fin {
        background-color: var(--wm-card) !important;
        color: var(--wm-text) !important;
        border: 1.5px solid var(--wm-border) !important;
        border-radius: 12px;
    }
    .wm-input-fin:focus {
        border-color: var(--wm-primary) !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(20, 86, 160, 0.15);
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
    [data-theme="dark"] table thead, body.dark-mode table thead {
        background-color: #1A2A44 !important;
        color: #7DB4F5 !important;
    }
    .chart-container-wrap {
        position: relative;
        width: 100%;
        height: 280px;
    }
    @media (max-width: 768px) {
        .chart-container-wrap {
            height: 230px;
        }
    }
</style>

<div class="container-fluid p-0 space-y-6">

    {{-- ======================================================================
         A. HEADER DASHBOARD KEUANGAN RW 013
         ====================================================================== --}}
    <div class="wm-fin-header p-6 md:p-8 relative overflow-hidden shadow-lg">
        <!-- Background Icon Watermark -->
        <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
            <i data-lucide="landmark" style="width: 280px; height: 280px;"></i>
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md border border-white/30 tracking-wide">
                        PERBENDAHARAAN & KAS TERPADU
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/30 text-emerald-100 border border-emerald-400/30 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Kas Aktif & Transparan
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-400/25 text-blue-100 border border-white/20">
                        8 RT &bull; 68 KK
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight">Dashboard Keuangan RW 013</h1>
                <p class="text-blue-100 text-xs sm:text-sm mt-1 max-w-2xl">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 inline text-emerald-300"></i> Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung 40293 &bull; Periode Laporan: <strong>{{ \Carbon\Carbon::parse($currentPeriode . '-01')->translatedFormat('F Y') }}</strong>
                </p>
            </div>

            <!-- Kontrol Periode & Tombol Aksi Utama -->
            <div class="flex flex-wrap items-center gap-2.5">
                <form method="GET" action="{{ route('keuangan.dashboard') }}" class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 py-2 rounded-xl border border-white/25">
                    <label for="filter_periode" class="text-xs font-medium text-white mb-0 whitespace-nowrap">Periode:</label>
                    <input type="month" name="periode" id="filter_periode" value="{{ $currentPeriode }}" onchange="this.form.submit()" 
                           class="bg-white text-slate-800 text-xs font-bold rounded-lg px-2.5 py-1.5 border-0 focus:ring-2 focus:ring-blue-300 outline-none cursor-pointer">
                </form>

                <a href="{{ route('keuangan.transaksi.create') }}" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-md transition duration-150">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Transaksi
                </a>

                <a href="{{ route('keuangan.laporan.cetak', ['start_date' => $currentPeriode . '-01', 'end_date' => Carbon\Carbon::parse($currentPeriode . '-01')->endOfMonth()->toDateString()]) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-white/20 hover:bg-white/30 border border-white/30 text-white font-semibold text-xs sm:text-sm px-3.5 py-2.5 rounded-xl transition duration-150">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
                </a>
            </div>
        </div>

        <!-- Banner Akumulasi Likuid RW -->
        <div class="mt-6 pt-5 border-t border-white/20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <span class="text-xs text-blue-200 font-semibold uppercase tracking-wider">Total Kekayaan Likuid Kas</span>
                <p class="text-2xl sm:text-3xl font-black text-white mt-0.5">
                    Rp {{ number_format($totalLikuidRw, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-blue-200/90">Konsolidasi 1 Kas RW + 8 Kas RT + Sampah + Kegiatan</span>
            </div>
            <div>
                <span class="text-xs text-emerald-200 font-semibold uppercase tracking-wider">Pemasukan Periode Ini</span>
                <p class="text-xl sm:text-2xl font-bold text-emerald-300 mt-0.5">
                    +Rp {{ number_format($totalPemasukanPeriode, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-blue-200/90">Iuran keluarga, donasi & sumbangan warga</span>
            </div>
            <div>
                <span class="text-xs text-rose-200 font-semibold uppercase tracking-wider">Pengeluaran Periode Ini</span>
                <p class="text-xl sm:text-2xl font-bold text-rose-300 mt-0.5">
                    -Rp {{ number_format($totalPengeluaranPeriode, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-blue-200/90">Honor satpam, cator sampah, listrik & ATK</span>
            </div>
            <div>
                <span class="text-xs text-blue-200 font-semibold uppercase tracking-wider">Kolektibilitas Iuran Bulanan</span>
                <div class="flex items-center gap-2 mt-0.5">
                    <p class="text-xl sm:text-2xl font-black text-white">{{ $persenKolektibilitas }}%</p>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-bold {{ $persenKolektibilitas >= 80 ? 'bg-emerald-500/40 text-emerald-200' : 'bg-amber-500/40 text-amber-200' }}">
                        {{ $totalKkLunas }}/{{ $totalKkTerdaftar }} KK Lunas
                    </span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-400 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $persenKolektibilitas) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-0" role="alert" style="background: #E3F5EC; color: #16865D; border-radius: 14px;">
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

    {{-- ======================================================================
         B. RINGKASAN KEUANGAN (7 KARTU TERPISAH DENGAN WARNA & AKSEN TEGAS)
         ====================================================================== --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2 mb-0">
                <i data-lucide="layout-grid" class="w-4 h-4 text-[#1456A0]"></i> Ringkasan Parameter Finansial RW 013
            </h2>
            <span class="text-xs text-slate-400">Update Real-Time Database</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-3.5">
            <!-- 1. Saldo Kas RW -->
            <div class="wm-fin-card p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Kas Induk RW</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1456A0] flex items-center justify-center font-bold">
                        <i data-lucide="landmark" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-lg font-black text-slate-800 mt-2 mb-0">
                    Rp {{ number_format($kasRw->saldo_sekarang ?? 0, 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span>Sentral RW</span>
                    <a href="{{ route('keuangan.buku_kas.show', $kasRw->id ?? 1) }}" class="text-[#1456A0] font-bold hover:underline">Detail</a>
                </div>
            </div>

            <!-- 2. Total Kas RT 001 - 008 -->
            <div class="wm-fin-card p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Total Kas 8 RT</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-lg font-black text-emerald-600 mt-2 mb-0">
                    Rp {{ number_format($totalSaldoRt, 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span>RT 001 - RT 008</span>
                    <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'kas_rt']) }}" class="text-emerald-700 font-bold hover:underline">8 Akun</a>
                </div>
            </div>

            <!-- 3. Kas Operasional Sampah -->
            <div class="wm-fin-card p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-teal-600">Kas Sampah</span>
                    <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-lg font-black text-teal-700 mt-2 mb-0">
                    Rp {{ number_format($kasSampah->saldo_sekarang ?? 0, 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span>Satgas Kebersihan</span>
                    <a href="{{ route('keuangan.buku_kas.show', $kasSampah->id ?? 2) }}" class="text-teal-700 font-bold hover:underline">Detail</a>
                </div>
            </div>

            <!-- 4. Total Pemasukan -->
            <div class="wm-fin-card p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Pemasukan Bulan</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-lg font-black text-emerald-600 mt-2 mb-0">
                    +Rp {{ number_format($totalPenerimaanBulanIni, 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span>{{ $totalKkLunas }} KK Telah Bayar</span>
                    <span class="text-emerald-600 font-bold">Inflow</span>
                </div>
            </div>

            <!-- 5. Total Pengeluaran -->
            <div class="wm-fin-card p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Pengeluaran Bulan</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-lg font-black text-rose-600 mt-2 mb-0">
                    -Rp {{ number_format($totalPengeluaranPeriode, 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span>Belanja Operasional</span>
                    <span class="text-rose-600 font-bold">Outflow</span>
                </div>
            </div>

            <!-- 6. Total Piutang Iuran -->
            <div class="wm-fin-card p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Piutang Iuran</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-lg font-black text-amber-700 mt-2 mb-0">
                    Rp {{ number_format($sisaPiutangBulanIni, 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span>{{ $totalKkBelum }} KK Belum Bayar</span>
                    <a href="{{ route('tagihan.index', ['status' => 'Belum Dibayar']) }}" class="text-amber-700 font-bold hover:underline">Tagih</a>
                </div>
            </div>

            <!-- 7. Persentase Pembayaran Warga -->
            <div class="wm-fin-card p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-purple-600">Kolektibilitas</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                        <i data-lucide="percent" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-lg font-black text-purple-700 mt-2 mb-0">
                    {{ $persenKolektibilitas }}%
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span>{{ $totalKkDibebaskan }} KK Bebas (Dhuafa)</span>
                    <span class="text-purple-700 font-bold">Rate</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         C. KARTU REKENING DAN KAS (KAS RW, KAS SAMPAH, 8 KAS RT, DANA KEGIATAN)
         ====================================================================== --}}
    <div class="wm-fin-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-5">
            <div>
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                    <i data-lucide="wallet-cards" class="w-5 h-5 text-[#1456A0]"></i> Entitas Rekening & Buku Kas RW 013
                </h2>
                <p class="text-xs text-slate-500 mt-1 mb-0">Setiap kas memiliki akun terpisah, nomor rekening resmi, peruntukan spesifik, dan rekapitulasi mutasi</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.buku_kas.index') }}" class="wm-btn-outline" style="font-size: 0.8rem; padding: 6px 14px;">
                    Lihat Semua 18 Buku Kas <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                </a>
            </div>
        </div>

        <!-- 3 Pilar Kas Utama: RW, Sampah, dan Ringkasan RT -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Card Kas RW -->
            <div class="p-4 rounded-2xl border border-slate-200 bg-gradient-to-b from-white to-blue-50/30 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-blue-100 text-[#1456A0]">
                            {{ $kasRw->kode_kas ?? 'KAS-RW-013' }}
                        </span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-800 border border-blue-200">
                            Sentral RW
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-1">{{ $kasRw->nama_kas ?? 'Kas Induk RW 013' }}</h3>
                    <p class="text-xs text-slate-500 mb-3">{{ $kasRw->deskripsi ?? 'Operasional balai, honor satpam kawasan, CCTV' }}</p>

                    <div class="p-3 bg-white rounded-xl border border-slate-200 mb-3">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Saldo Rekening Saat Ini</span>
                        <p class="text-2xl font-black text-slate-900 mt-0.5 mb-0">
                            Rp {{ number_format($kasRw->saldo_sekarang ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-[11px] text-slate-500 font-mono mt-1 mb-0">
                            {{ $kasRw->nama_bank ?? 'Bank Mandiri' }}: <strong>{{ $kasRw->no_rekening ?? '130-00-1928374-1' }}</strong>
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                        <div class="p-2 rounded-lg bg-emerald-50 text-emerald-800">
                            <span class="text-[10px] block text-emerald-600 font-semibold">Total Masuk</span>
                            <strong class="font-extrabold">+Rp {{ number_format($kasRwMasuk ?? 0, 0, ',', '.') }}</strong>
                        </div>
                        <div class="p-2 rounded-lg bg-rose-50 text-rose-800">
                            <span class="text-[10px] block text-rose-600 font-semibold">Total Keluar</span>
                            <strong class="font-extrabold">-Rp {{ number_format($kasRwKeluar ?? 0, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-[11px] text-slate-500">PIC: {{ $kasRw->nama_penanggung_jawab ?? 'Ketua RW' }}</span>
                    <a href="{{ route('keuangan.buku_kas.show', $kasRw->id ?? 1) }}" class="wm-btn-primary" style="font-size: 0.78rem; padding: 6px 12px;">
                        Buka Buku Kas &rarr;
                    </a>
                </div>
            </div>

            <!-- Card Kas Sampah -->
            <div class="p-4 rounded-2xl border border-slate-200 bg-gradient-to-b from-white to-teal-50/30 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-teal-100 text-teal-800">
                            {{ $kasSampah->kode_kas ?? 'KAS-SAMPAH' }}
                        </span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-teal-50 text-teal-800 border border-teal-200">
                            Kebersihan Lingkungan
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-1">{{ $kasSampah->nama_kas ?? 'Kas Pengelolaan Sampah' }}</h3>
                    <p class="text-xs text-slate-500 mb-3">{{ $kasSampah->deskripsi ?? 'Cator motor sampah, BBM, servis berkala, TPS' }}</p>

                    <div class="p-3 bg-white rounded-xl border border-slate-200 mb-3">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Saldo Kas Sampah</span>
                        <p class="text-2xl font-black text-teal-700 mt-0.5 mb-0">
                            Rp {{ number_format($kasSampah->saldo_sekarang ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-[11px] text-slate-500 font-mono mt-1 mb-0">
                            {{ $kasSampah->nama_bank ?? 'Bank BJB' }}: <strong>{{ $kasSampah->no_rekening ?? '008-219-4820-101' }}</strong>
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                        <div class="p-2 rounded-lg bg-emerald-50 text-emerald-800">
                            <span class="text-[10px] block text-emerald-600 font-semibold">Total Masuk</span>
                            <strong class="font-extrabold">+Rp {{ number_format($kasSampahMasuk ?? 0, 0, ',', '.') }}</strong>
                        </div>
                        <div class="p-2 rounded-lg bg-rose-50 text-rose-800">
                            <span class="text-[10px] block text-rose-600 font-semibold">Total Keluar</span>
                            <strong class="font-extrabold">-Rp {{ number_format($kasSampahKeluar ?? 0, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-[11px] text-slate-500">PIC: Satgas Kebersihan</span>
                    <a href="{{ route('keuangan.buku_kas.show', $kasSampah->id ?? 2) }}" class="wm-btn-primary" style="font-size: 0.78rem; padding: 6px 12px; background: #0D9488; border-color: #0D9488;">
                        Buka Buku Kas &rarr;
                    </a>
                </div>
            </div>

            <!-- Card Total 8 RT & Progres Penagihan -->
            <div class="p-4 rounded-2xl border border-slate-200 bg-gradient-to-b from-white to-emerald-50/30 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-emerald-100 text-emerald-800">
                            KAS-RT-001 s/d 008
                        </span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                            8 Rukun Tetangga
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-1">Konsolidasi Kas Lingkungan RT</h3>
                    <p class="text-xs text-slate-500 mb-3">Akun otonom operasional kerja bakti & perbaikan lokal RT</p>

                    <div class="p-3 bg-white rounded-xl border border-slate-200 mb-3">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Total Saldo 8 RT</span>
                        <p class="text-2xl font-black text-emerald-600 mt-0.5 mb-0">
                            Rp {{ number_format($totalSaldoRt, 0, ',', '.') }}
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1 mb-0">
                            Terkumpul Iuran RT: <strong>Rp {{ number_format($tagihans->sum('nominal_rt'), 0, ',', '.') }}</strong>
                        </p>
                    </div>

                    <div class="space-y-1 text-xs mb-3">
                        <div class="flex justify-between text-slate-600">
                            <span>Target KK RW 013:</span>
                            <strong>{{ $totalKkTerdaftar }} KK (Rp 45.000/KK)</strong>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Status Lunas:</span>
                            <strong class="text-emerald-700">{{ $totalKkLunas }} KK ({{ $persenKolektibilitas }}%)</strong>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-[11px] text-slate-500">8 Buku Kas Terpisah</span>
                    <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'kas_rt']) }}" class="wm-btn-success" style="font-size: 0.78rem; padding: 6px 12px;">
                        Kelola 8 Kas RT &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Grid 8 Kas RT (001 s/d 008) -->
        <div class="pt-3">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Daftar Buku Kas RT 001 - RT 008</span>
                <span class="text-[11px] text-slate-400">Pemberdayaan Wilayah Rukun Tetangga</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2.5">
                @foreach($kasRtList as $rtKas)
                    <div class="p-3 rounded-xl border border-slate-200 bg-white hover:border-emerald-300 transition text-center shadow-sm">
                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded bg-emerald-100 text-emerald-800">
                            RT {{ $rtKas->rt_number }}
                        </span>
                        <p class="text-xs sm:text-sm font-extrabold text-slate-800 mt-2 mb-0 truncate">
                            Rp {{ number_format($rtKas->saldo_sekarang, 0, ',', '.') }}
                        </p>
                        <a href="{{ route('keuangan.buku_kas.show', $rtKas->id) }}" class="text-[10px] font-bold text-[#1456A0] hover:underline mt-1.5 inline-block">
                            Mutasi &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ======================================================================
         D. GRAFIK KEUANGAN (CHART.JS: ARUS KAS, SALDO, SUMBER IN & OUT)
         ====================================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- 1. Grafik Pemasukan vs Pengeluaran Bulanan (7 Kolom) -->
        <div class="lg:col-span-7 wm-fin-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 text-emerald-600"></i> Tren Arus Kas Masuk vs Keluar
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5 mb-0">Pergerakan riil pemasukan dan pengeluaran 6 bulan terakhir</p>
                    </div>
                    <span class="badge" style="background:#E3F5EC;color:#16865D;border-radius:999px;font-size:0.75rem;padding:6px 12px;">
                        6 Bulan Terakhir
                    </span>
                </div>

                <div class="chart-container-wrap">
                    <canvas id="chartArusKas"></canvas>
                </div>
            </div>

            <div class="flex items-center justify-center gap-6 pt-3 border-t border-slate-100 text-xs font-semibold text-slate-600 mt-2">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Total Pemasukan (+Debit)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Total Pengeluaran (-Kredit)</span>
            </div>
        </div>

        <!-- 2. Grafik Perkembangan Saldo Kas (5 Kolom) -->
        <div class="lg:col-span-5 wm-fin-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                            <i data-lucide="trending-up" class="w-5 h-5 text-[#1456A0]"></i> Perkembangan Saldo Likuid
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5 mb-0">Pertumbuhan kekayaan kas konsolidasi RW 013</p>
                    </div>
                </div>

                <div class="chart-container-wrap">
                    <canvas id="chartSaldoPerkembangan"></canvas>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 mt-2">
                <span>Posisi Saldo Saat Ini:</span>
                <strong class="text-slate-900 font-extrabold text-sm">Rp {{ number_format($totalLikuidRw, 0, ',', '.') }}</strong>
            </div>
        </div>

        <!-- 3. Ringkasan Sumber Pemasukan (6 Kolom) -->
        <div class="lg:col-span-6 wm-fin-card p-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-0">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-emerald-600"></i> Sumber Pemasukan Terbesar
                </h3>
                <span class="text-xs text-slate-400">Top 5 Kategori</span>
            </div>

            <div class="space-y-3">
                @forelse($sumberPemasukan as $sp)
                    @php
                        $totIn = max($totalPemasukanPeriode, 1);
                        $pctIn = min(100, round(($sp->total / $totIn) * 100));
                    @endphp
                    <div>
                        <div class="flex justify-between items-center text-xs mb-1">
                            <span class="font-bold text-slate-800">{{ $sp->kategori }}</span>
                            <span class="font-extrabold text-emerald-700">+Rp {{ number_format($sp->total, 0, ',', '.') }} ({{ $pctIn }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pctIn }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-slate-400 text-xs py-4 mb-0">Belum ada kategori pemasukan tercatat.</p>
                @endforelse
            </div>
        </div>

        <!-- 4. Ringkasan Kategori Pengeluaran (6 Kolom) -->
        <div class="lg:col-span-6 wm-fin-card p-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-0">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-rose-600"></i> Kategori Pengeluaran Terbesar
                </h3>
                <span class="text-xs text-slate-400">Top 5 Kategori</span>
            </div>

            <div class="space-y-3">
                @forelse($kategoriPengeluaran as $kp)
                    @php
                        $totOut = max($totalPengeluaranPeriode, 1);
                        $pctOut = min(100, round(($kp->total / $totOut) * 100));
                    @endphp
                    <div>
                        <div class="flex justify-between items-center text-xs mb-1">
                            <span class="font-bold text-slate-800">{{ $kp->kategori }}</span>
                            <span class="font-extrabold text-rose-600">-Rp {{ number_format($kp->total, 0, ',', '.') }} ({{ $pctOut }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-rose-500 h-2 rounded-full" style="width: {{ $pctOut }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-slate-400 text-xs py-4 mb-0">Belum ada kategori pengeluaran tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ======================================================================
         E. RIWAYAT TRANSAKSI KEUANGAN DENGAN FILTER MULTI-KRITERIA
         ====================================================================== --}}
    <div class="wm-fin-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-5">
            <div>
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                    <i data-lucide="history" class="w-5 h-5 text-[#1456A0]"></i> Jurnal Riwayat Transaksi Keuangan RW 013
                </h2>
                <p class="text-xs text-slate-500 mt-1 mb-0">Mutasi kas lengkap dengan nomor transaksi otomatis, verifikasi saldo, dan bukti lampiran</p>
            </div>
            <a href="{{ route('keuangan.transaksi.create') }}" class="wm-btn-primary" style="font-size: 0.8rem; padding: 7px 16px;">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Catat Transaksi Baru
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mb-5">
            <form method="GET" action="{{ route('keuangan.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
                <input type="hidden" name="periode" value="{{ $currentPeriode }}">
                
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. TRX, pihak, uraian..." 
                           class="w-full wm-input-fin px-3 py-2 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Buku Kas</label>
                    <select name="buku_kas_id" class="w-full wm-input-fin px-3 py-2 text-xs">
                        <option value="">Semua Buku Kas</option>
                        @foreach($allBukuKas as $buku)
                            <option value="{{ $buku->id }}" {{ request('buku_kas_id') == $buku->id ? 'selected' : '' }}>
                                {{ $buku->nama_kas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Transaksi</label>
                    <select name="jenis_transaksi" class="w-full wm-input-fin px-3 py-2 text-xs">
                        <option value="">Semua Jenis</option>
                        <option value="pemasukan" {{ request('jenis_transaksi') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="pengeluaran" {{ request('jenis_transaksi') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        <option value="transfer_keluar" {{ request('jenis_transaksi') === 'transfer_keluar' ? 'selected' : '' }}>Transfer Keluar</option>
                        <option value="transfer_masuk" {{ request('jenis_transaksi') === 'transfer_masuk' ? 'selected' : '' }}>Transfer Masuk</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kategori</label>
                    <select name="kategori" class="w-full wm-input-fin px-3 py-2 text-xs">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2 bg-[#1456A0] text-white font-bold rounded-xl hover:bg-blue-800 transition">
                        Terapkan
                    </button>
                    <a href="{{ route('keuangan.dashboard', ['periode' => $currentPeriode]) }}" class="py-2 px-3 bg-white text-slate-600 font-semibold rounded-xl border border-slate-300 hover:bg-slate-100 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Jurnal Transaksi -->
        <div class="table-responsive">
            <table class="table wm-table mb-0">
                <thead>
                    <tr>
                        <th class="py-3 px-3">Tanggal</th>
                        <th class="py-3 px-3">No. Transaksi</th>
                        <th class="py-3 px-3">Kategori & Uraian</th>
                        <th class="py-3 px-3">Buku Kas (Sumber Dana)</th>
                        <th class="py-3 px-3">Pihak / Penyetor</th>
                        <th class="py-3 px-3 text-end">Pemasukan</th>
                        <th class="py-3 px-3 text-end">Pengeluaran</th>
                        <th class="py-3 px-3 text-end">Saldo Berjalan</th>
                        <th class="py-3 px-3 text-center">Petugas</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $trx)
                        <tr class="{{ $trx->status === 'dibatalkan' ? 'opacity-50 text-decoration-line-through' : '' }}">
                            <td class="py-3 px-3 text-nowrap text-muted font-mono" style="font-size: 0.8rem;">
                                {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d/m/Y') }}
                            </td>
                            <td class="py-3 px-3 text-nowrap font-mono fw-bold" style="color: var(--wm-text);">
                                {{ $trx->nomor_transaksi }}
                                @if($trx->status === 'dibatalkan')
                                    <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">BATAL</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <span class="badge {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? 'bg-success' : 'bg-danger' }} mb-1" style="font-size: 0.65rem;">
                                    {{ strtoupper(str_replace('_', ' ', $trx->jenis_transaksi)) }}
                                </span>
                                <div class="fw-bold" style="color: var(--wm-text); font-size: 0.85rem;">{{ $trx->kategori }}</div>
                                <div class="text-muted small truncate" style="max-width: 200px;">{{ $trx->keterangan }}</div>
                            </td>
                            <td class="py-3 px-3 text-nowrap">
                                <span class="badge bg-light text-dark border" style="font-size: 0.72rem;">
                                    {{ $trx->bukuKas->nama_kas ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-3" style="font-size: 0.82rem;">
                                {{ $trx->penyetor_penerima ?: '-' }}
                            </td>
                            <td class="py-3 px-3 text-end text-nowrap fw-bold text-success" style="font-size: 0.85rem;">
                                @if(in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']))
                                    +Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 px-3 text-end text-nowrap fw-bold text-danger" style="font-size: 0.85rem;">
                                @if(in_array($trx->jenis_transaksi, ['pengeluaran', 'transfer_keluar']))
                                    -Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 px-3 text-end text-nowrap font-mono fw-bold" style="color: var(--wm-primary); font-size: 0.85rem;">
                                Rp {{ number_format($trx->saldo_setelahnya, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center text-muted small text-nowrap">
                                {{ $trx->petugas->name ?? $trx->nama_petugas ?? 'Sistem' }}
                            </td>
                            <td class="py-3 px-3 text-center text-nowrap">
                                <div class="d-inline-flex align-items-center gap-1">
                                    @if($trx->bukti_transaksi_path)
                                        <a href="{{ asset('storage/' . $trx->bukti_transaksi_path) }}" target="_blank" class="btn btn-sm btn-outline-primary p-1" title="Lihat Bukti">
                                            <i data-lucide="paperclip" style="width: 14px; height: 14px;"></i>
                                        </a>
                                    @endif
                                    @if($trx->status === 'selesai')
                                        <button onclick="bukaModalBatal('{{ $trx->id }}', '{{ $trx->nomor_transaksi }}', '{{ number_format($trx->nominal, 0, ',', '.') }}')" 
                                                class="btn btn-sm btn-outline-danger p-1" title="Batalkan Transaksi">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-5 text-center text-muted">
                                Tidak ada catatan transaksi yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">Menampilkan {{ $recentTransactions->count() }} data per halaman</span>
            {{ $recentTransactions->links() }}
        </div>
    </div>
</div>

<!-- Modal Pembatalan Transaksi -->
<div class="modal fade" id="modalBatalTransaksi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--wm-card); color: var(--wm-text); border-radius: 20px; border: 1px solid var(--wm-border);">
            <div class="modal-header border-bottom" style="border-color: var(--wm-border) !important;">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i data-lucide="alert-triangle" style="width: 20px; height: 20px;"></i> Batalkan Transaksi Keuangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBatalTrxModal" method="POST" action="">
                @csrf
                <div class="modal-body space-y-3">
                    <div class="p-3 rounded-xl" style="background: #FEE2E2; color: #DC2626;">
                        <p class="mb-1 fw-bold">Nomor Transaksi: <span id="batalNomorTrxText" class="font-mono"></span></p>
                        <p class="mb-0">Nominal: <strong id="batalNominalTrxText"></strong></p>
                        <p class="small text-danger mt-1 mb-0">Saldo buku kas akan otomatis disesuaikan secara terbalik dan riwayat pembatalan dicatat di Audit Log.</p>
                    </div>

                    <div>
                        <label class="form-label fw-bold small">Alasan Pembatalan (Wajib Diisi) *</label>
                        <textarea name="alasan_batal" required rows="3" placeholder="Contoh: Salah input nominal / transaksi ganda..." 
                                  class="form-control wm-input-fin text-sm"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top" style="border-color: var(--wm-border) !important;">
                    <button type="button" class="btn btn-light rounded-pill px-3 fw-semibold" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Konfirmasi Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function bukaModalBatal(id, nomor, nominal) {
    document.getElementById('formBatalTrxModal').action = '/keuangan/transaksi/' + id + '/batal';
    document.getElementById('batalNomorTrxText').innerText = nomor;
    document.getElementById('batalNominalTrxText').innerText = 'Rp ' + nominal;
    var modal = new bootstrap.Modal(document.getElementById('modalBatalTransaksi'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function () {
    // 1. Chart Arus Kas Bulanan (Bar Chart)
    const ctxArusKas = document.getElementById('chartArusKas');
    if (ctxArusKas) {
        new Chart(ctxArusKas, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labelsChart) !!},
                datasets: [
                    {
                        label: 'Pemasukan (+)',
                        data: {!! json_encode($dataPemasukanChart) !!},
                        backgroundColor: '#16865D',
                        borderRadius: 8,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Pengeluaran (-)',
                        data: {!! json_encode($dataPengeluaranChart) !!},
                        backgroundColor: '#DC2626',
                        borderRadius: 8,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10 },
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Chart Saldo Perkembangan (Line Chart)
    const ctxSaldo = document.getElementById('chartSaldoPerkembangan');
    if (ctxSaldo) {
        new Chart(ctxSaldo, {
            type: 'line',
            data: {
                labels: {!! json_encode($labelsChart) !!},
                datasets: [{
                    label: 'Saldo Kas',
                    data: {!! json_encode($dataSaldoChart) !!},
                    borderColor: '#1456A0',
                    backgroundColor: 'rgba(20, 86, 160, 0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 3,
                    pointBackgroundColor: '#1456A0',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Saldo: Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                    },
                    y: {
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10 },
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(0) + 'M';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
