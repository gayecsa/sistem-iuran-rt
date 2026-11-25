@extends('layouts.app')

@section('title', 'Dashboard Keuangan RW 013')

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
        height: 270px;
    }
    @media (max-width: 768px) {
        .chart-container-wrap {
            height: 220px;
        }
    }
</style>

<div class="container-fluid p-0 space-y-6">

    {{-- ======================================================================
         1. HEADER DASHBOARD KEUANGAN RW 013 (SALDO UTAMA: KAS INDUK RW)
         Prinsip: SEPARATE FINANCIAL ACCOUNT, CENTRALIZED MONITORING
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
                        PERBENDAHARAAN KAS INDUK RW 013
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/30 text-emerald-100 border border-emerald-400/30 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Rekening Otonom Terpisah
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-400/25 text-blue-100 border border-white/20">
                        Monitoring 8 RT &bull; {{ \App\Models\KartuKeluarga::count() }} KK
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight">Dashboard Keuangan RW 013</h1>
                <p class="text-blue-100 text-xs sm:text-sm mt-1 max-w-2xl">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 inline text-emerald-300"></i> Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung &bull; Periode: <strong>{{ \Carbon\Carbon::parse($currentPeriode . '-01')->translatedFormat('F Y') }}</strong>
                </p>
            </div>

            <!-- Kontrol Periode & Tombol Aksi Utama -->
            <div class="flex flex-wrap items-center gap-2.5">
                <form method="GET" action="{{ route('keuangan.dashboard') }}" class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 py-2 rounded-xl border border-white/25">
                    <label for="filter_periode" class="text-xs font-medium text-white mb-0 whitespace-nowrap">Periode:</label>
                    <input type="month" name="periode" id="filter_periode" value="{{ $currentPeriode }}" onchange="this.form.submit()" 
                           class="text-xs font-bold rounded-lg px-2.5 py-1.5 outline-none cursor-pointer"
                           style="background: rgba(255, 255, 255, 0.18) !important; color: #FFFFFF !important; border: 1px solid rgba(255, 255, 255, 0.35) !important; color-scheme: dark;">
                </form>

                <a href="{{ route('keuangan.transaksi.create') }}" class="inline-flex items-center gap-2 font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-md transition duration-150" style="background: #16A34A !important; color: #FFFFFF !important; border: none !important;">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Catat Transaksi RW
                </a>

                <a href="{{ route('keuangan.laporan.cetak', ['start_date' => $currentPeriode . '-01', 'end_date' => Carbon\Carbon::parse($currentPeriode . '-01')->endOfMonth()->toDateString()]) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-white/20 hover:bg-white/30 border border-white/30 text-white font-semibold text-xs sm:text-sm px-3.5 py-2.5 rounded-xl transition duration-150">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan RW
                </a>
            </div>
        </div>

        <!-- Banner Saldo Utama RW (Murni Kas Induk RW, Bukan Total Likuid RT) -->
        <div class="mt-6 pt-5 border-t border-white/20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <span class="text-xs text-blue-200 font-semibold uppercase tracking-wider">Saldo Kas Induk RW 013</span>
                <p class="text-2xl sm:text-3xl font-black text-white mt-0.5">
                    Rp {{ number_format($saldoKasRw, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-blue-200/90">{{ $kasRw->nama_bank ?? 'Kas Tunai' }} &bull; {{ $kasRw->no_rekening ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-emerald-200 font-semibold uppercase tracking-wider">Pemasukan RW Bulan Ini</span>
                <p class="text-xl sm:text-2xl font-bold text-emerald-300 mt-0.5">
                    +Rp {{ number_format($pemasukanRwBulanIni, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-blue-200/90">Iuran RW & Donasi Terverifikasi (Exclude Transfer)</span>
            </div>
            <div>
                <span class="text-xs text-rose-200 font-semibold uppercase tracking-wider">Pengeluaran RW Bulan Ini</span>
                <p class="text-xl sm:text-2xl font-bold text-rose-300 mt-0.5">
                    -Rp {{ number_format($pengeluaranRwBulanIni, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-blue-200/90">Operasional Balai, Satpam, CCTV & Konsumsi</span>
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

    {{-- Banner Notifikasi Titipan Tunai RT Menunggu Setoran --}}
    @if(($totalTitipanMenunggu ?? 0) > 0)
        <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-300 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow">
                    <i data-lucide="banknote" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">Titipan Iuran Tunai di Pengurus RT</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-200 text-amber-900">{{ count($titipanRtList ?? []) }} Alokasi Menunggu</span>
                    </div>
                    <p class="text-xs text-amber-800 mt-0.5 mb-0">
                        Terdapat dana titipan tunai sebesar <strong>Rp {{ number_format($totalTitipanMenunggu, 0, ',', '.') }}</strong> yang telah dibayar warga ke Pengurus RT untuk pos RW, Sampah & Kegiatan. Dana ini tidak tercampur ke kas RT dan menunggu disetor ke kas RW/Unit.
                    </p>
                </div>
            </div>

            <a href="{{ route('rw.audit.index', ['tab' => 'monitoring_iuran']) }}" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow flex items-center gap-2 transition whitespace-nowrap text-decoration-none">
                <i data-lucide="check-square" class="w-4 h-4"></i> Tinjau & Cek Setoran
            </a>
        </div>
    @endif

    {{-- ======================================================================
         1. PARAMETER RINGKASAN & GABUNGAN SALDO WILAYAH
         ====================================================================== --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2 mb-0">
                    <i data-lucide="layout-grid" class="w-4 h-4 text-[#1456A0]"></i> Ringkasan & Gabungan Saldo Wilayah
                </h2>
                <p class="text-[11px] text-slate-400 mt-0.5 mb-0">Pemisahan kepemilikan dana otonom: Kas Induk RW, Otonomi 8 RT, dan Kas Unit Kerja/Kegiatan</p>
            </div>
            <span class="text-xs text-slate-400 font-mono">Separate Accounts, Centralized Monitoring</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- 1. Saldo Kas Induk RW -->
            <div class="wm-fin-card p-4 flex flex-col justify-between border-l-4 border-l-[#1456A0]">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Kas Induk RW</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1456A0] flex items-center justify-center font-bold">
                        <i data-lucide="landmark" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-xl font-black text-slate-900 mt-2 mb-0">
                    Rp {{ number_format($saldoKasRw, 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span class="text-blue-700 font-semibold">Dana Murni RW 013</span>
                    <a href="{{ route('keuangan.buku_kas.show', $kasRw->id ?? 1) }}" class="text-[#1456A0] font-bold hover:underline">Detail &rarr;</a>
                </div>
            </div>

            <!-- 2. Total Saldo 8 RT (MONITORING KAS RT) -->
            <div class="wm-fin-card p-4 flex flex-col justify-between border-l-4 border-l-emerald-500 bg-emerald-50/20 card-total-kas-rt">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 lbl-kas-rt">Total Kas 8 RT</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-xl font-black text-emerald-700 mt-2 mb-0 val-kas-rt">
                    Rp {{ number_format($totalSaldoRt, 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span class="text-emerald-800 font-bold bg-emerald-100 px-1.5 py-0.2 rounded tag-kas-rt">Monitoring 8 RT</span>
                    <a href="#tabel-monitoring-rt" class="text-emerald-700 font-bold hover:underline link-kas-rt">Rincian &darr;</a>
                </div>
            </div>

            <!-- 3. Kas Pengelolaan Sampah RW -->
            <div class="wm-fin-card p-4 flex flex-col justify-between border-l-4 border-l-amber-500 bg-amber-50/20 card-kas-sampah-rw">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 lbl-kas-sampah">Kas Sampah RW</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-xl font-black text-amber-900 mt-2 mb-0 val-kas-sampah">
                    Rp {{ number_format($saldoKasSampah ?? 0, 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span class="text-amber-800 font-semibold tag-kas-sampah">Rekening Bersama RW</span>
                    <a href="#tabel-keuangan-unit" class="text-amber-700 font-bold hover:underline link-kas-sampah">Detail &darr;</a>
                </div>
            </div>

            <!-- 4. Total Saldo Gabungan -->
            <div class="wm-fin-card p-4 flex flex-col justify-between border-l-4 border-l-indigo-600 bg-indigo-50/20">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-700">Total Saldo Gabungan</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                        <i data-lucide="calculator" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-xl font-black text-indigo-900 mt-2 mb-0">
                    Rp {{ number_format($saldoKonsolidasi ?? ($saldoKasRw + $totalSaldoRt + ($saldoKasSampah ?? 0)), 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span class="text-indigo-800 font-bold">Kas RW + 8 RT + Sampah</span>
                    <span class="text-indigo-600 font-mono">Tepat Cocok</span>
                </div>
            </div>

            <!-- 5. Total Semua Dana (+ Kegiatan) -->
            <div class="wm-fin-card p-4 flex flex-col justify-between border-l-4 border-l-slate-600 bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700">Total Semua Dana (+ Kegiatan)</span>
                    <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-bold">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                    </div>
                </div>
                <p class="text-xl font-black text-slate-900 mt-2 mb-0">
                    Rp {{ number_format($totalDanaTerpantau, 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between border-t border-slate-100 pt-1.5">
                    <span class="text-slate-600 font-semibold">+ Kas Kegiatan Sosial</span>
                    <span class="text-slate-400 font-mono">Transparansi</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         2. KELOMPOK A. KEUANGAN RW (RINGKASAN LENGKAP KAS INDUK RW 013)
         Sesuai Poin 4A: Saldo Awal, Total Pemasukan, Total Pengeluaran, Saldo Akhir
         ====================================================================== --}}
    <div class="wm-fin-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                        <i data-lucide="landmark" class="w-5 h-5 text-[#1456A0]"></i> Kelompok A: Keuangan Kas Induk RW 013 (Periode {{ $currentPeriode }})
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-[#1456A0] border border-blue-200">
                        Khusus Kas RW 013
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1 mb-0">
                    Pencatatan mutasi kas yang murni dimiliki oleh Pengurus RW 013 Sukamiskin. Bebas dari pencampuran kas RT maupun unit kerja.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-blue-50 text-[#1456A0] border border-blue-200">
                    Rekening: <strong>{{ $kasRw->masked_rekening }}</strong>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3.5">
            <!-- Saldo Awal RW -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col justify-between card-saldo-awal-rw">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Saldo Awal RW</span>
                <p class="text-lg font-black text-slate-800 mt-2 mb-0 val-saldo-awal">
                    Rp {{ number_format($saldoAwalRw, 0, ',', '.') }}
                </p>
                <span class="text-[10px] text-slate-400 mt-1 pt-1 border-t border-slate-200/60 sub-saldo-awal">Posisi awal bulan</span>
            </div>

            <!-- Total Pemasukan RW -->
            <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-200 flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Total Pemasukan RW</span>
                <p class="text-lg font-black text-emerald-600 mt-2 mb-0">
                    +Rp {{ number_format($pemasukanRwBulanIni, 0, ',', '.') }}
                </p>
                <span class="text-[10px] text-emerald-700/80 mt-1 pt-1 border-t border-emerald-200/60">Inflow riil Kas RW</span>
            </div>

            <!-- Total Pengeluaran RW -->
            <div class="p-4 rounded-xl bg-rose-50/60 border border-rose-200 flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700">Total Pengeluaran RW</span>
                <p class="text-lg font-black text-rose-600 mt-2 mb-0">
                    -Rp {{ number_format($pengeluaranRwBulanIni, 0, ',', '.') }}
                </p>
                <span class="text-[10px] text-rose-700/80 mt-1 pt-1 border-t border-rose-200/60">Outflow operasional RW</span>
            </div>

            <!-- Saldo Akhir RW -->
            <div class="p-4 rounded-xl bg-blue-50/60 border-2 border-blue-300 flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-900">Saldo Akhir RW</span>
                <p class="text-lg font-black text-[#1456A0] mt-2 mb-0">
                    Rp {{ number_format($saldoAkhirRw, 0, ',', '.') }}
                </p>
                <span class="text-[10px] text-blue-700 font-semibold mt-1 pt-1 border-t border-blue-200/60">Formula: Awal + Masuk - Keluar</span>
            </div>

            <!-- Piutang Iuran RW -->
            <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-200 flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Piutang Iuran RW</span>
                <p class="text-lg font-black text-amber-800 mt-2 mb-0">
                    Rp {{ number_format($piutangIuranRw, 0, ',', '.') }}
                </p>
                <div class="text-[10px] text-amber-700 mt-1 pt-1 border-t border-amber-200/60 flex items-center justify-between">
                    <span>{{ $totalKkBelum }} KK Belum Bayar</span>
                    <a href="{{ route('tagihan.index', ['status' => 'Belum Dibayar']) }}" class="text-amber-800 font-bold hover:underline">Lihat &rarr;</a>
                </div>
            </div>

            <!-- Kolektibilitas Iuran Bulanan -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col justify-between card-kolektibilitas-rw">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700">Kolektibilitas Iuran</span>
                <p class="text-lg font-black text-[#1456A0] mt-2 mb-0 val-kolektibilitas">
                    {{ $persenKolektibilitas }}%
                </p>
                <span class="text-[10px] text-slate-500 mt-1 pt-1 border-t border-slate-200/60 sub-kolektibilitas">{{ $totalKkLunas }}/{{ $totalKkTerdaftar }} KK Lunas</span>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         3. MONITORING RT 001 - RT 008 (RINCIAN LENGKAP & INDEPENDENSI KAS)
         Sesuai Poin 3: Saldo Awal, Pemasukan, Pengeluaran, Saldo Akhir, Iuran, Tunggakan, Status Lapor
         ====================================================================== --}}
    <div id="tabel-monitoring-rt" class="wm-fin-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                        <i data-lucide="building-2" class="w-5 h-5 text-emerald-600"></i> Monitoring Keuangan RT 001 s/d RT 008 (Periode {{ $currentPeriode }})
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-[#1456A0] border border-blue-200">
                        Read-Only & Audit
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1 mb-0">
                    Kewenangan Ketua RW adalah memantau dan mengaudit akuntabilitas lingkungan. Kas RT merupakan dana otonom milik masing-masing RT.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                    Total Kas 8 RT: <strong>Rp {{ number_format($totalSaldoRt, 0, ',', '.') }}</strong>
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table wm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="py-3 px-3">Wilayah & PIC RT</th>
                        <th class="py-3 px-3 text-end">Saldo Awal</th>
                        <th class="py-3 px-3 text-end text-success">Pemasukan (+)</th>
                        <th class="py-3 px-3 text-end text-danger">Pengeluaran (-)</th>
                        <th class="py-3 px-3 text-end font-bold">Saldo Akhir</th>
                        <th class="py-3 px-3 text-end">Iuran Terkumpul</th>
                        <th class="py-3 px-3 text-end">Tunggakan</th>
                        <th class="py-3 px-3 text-center">Status Laporan</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monitoringRtList as $rtData)
                        <tr>
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-emerald-100 text-emerald-800">
                                        {{ $rtData['rt_label'] }}
                                    </span>
                                    <span>{{ $rtData['pic_name'] }}</span>
                                </div>
                                <span class="text-[11px] text-slate-400 font-mono">Buku Kas RT {{ $rtData['rt_number'] }}</span>
                            </td>
                            <td class="py-3 px-3 text-end font-mono text-slate-600">
                                Rp {{ number_format($rtData['saldo_awal'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-end font-mono font-bold text-success text-nowrap">
                                +Rp {{ number_format($rtData['pemasukan'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-end font-mono font-bold text-danger text-nowrap">
                                -Rp {{ number_format($rtData['pengeluaran'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-end font-mono font-black text-slate-900 text-nowrap bg-slate-50/60">
                                Rp {{ number_format($rtData['saldo_akhir'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-end font-mono text-emerald-700 font-semibold text-nowrap">
                                Rp {{ number_format($rtData['iuran_terkumpul'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-end font-mono text-nowrap {{ $rtData['tunggakan'] > 0 ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                                Rp {{ number_format($rtData['tunggakan'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center text-nowrap">
                                @if($rtData['status_laporan'] === 'Terkirim')
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Terkirim
                                    </span>
                                @elseif($rtData['status_laporan'] === 'Terverifikasi')
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-300">
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-300">
                                        {{ $rtData['status_laporan'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center text-nowrap">
                                @if($rtData['buku_kas_id'])
                                    <a href="{{ route('keuangan.buku_kas.show', $rtData['buku_kas_id']) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 font-semibold text-[11px]">
                                        <i data-lucide="eye" class="w-3.5 h-3.5 inline mr-1"></i> Audit RT
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50/90 font-extrabold text-slate-800 border-t border-slate-200">
                    <tr>
                        <td class="py-3 px-3">TOTAL GABUNGAN KAS 8 RT:</td>
                        <td class="py-3 px-3 text-end font-mono text-slate-700">
                            Rp {{ number_format(collect($monitoringRtList)->sum('saldo_awal'), 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-end font-mono text-success">
                            +Rp {{ number_format(collect($monitoringRtList)->sum('pemasukan'), 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-end font-mono text-danger">
                            -Rp {{ number_format(collect($monitoringRtList)->sum('pengeluaran'), 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-end font-mono text-slate-900 bg-slate-100">
                            Rp {{ number_format($totalSaldoRt, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-end font-mono text-emerald-800">
                            Rp {{ number_format(collect($monitoringRtList)->sum('iuran_terkumpul'), 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-end font-mono text-amber-800">
                            Rp {{ number_format(collect($monitoringRtList)->sum('tunggakan'), 0, ',', '.') }}
                        </td>
                        <td colspan="2" class="text-center text-[11px] text-slate-500 italic">
                            *Saldo milik masing-masing RT
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ======================================================================
         KELOMPOK C. KEUANGAN UNIT (SAMPAH, KEAMANAN, POSYANDU & KEGIATAN)
         Sesuai Poin 4C: Saldo setiap unit, Pemasukan dan Pengeluaran Unit, Riwayat
         ====================================================================== --}}
    <div id="tabel-keuangan-unit" class="wm-fin-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                        <i data-lucide="layers" class="w-5 h-5 text-purple-600"></i> Kelompok C: Keuangan Kas Unit Lingkungan (Periode {{ $currentPeriode }})
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-800 border border-purple-200">
                        Otonomi Unit Kerja
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1 mb-0">
                    Pencatatan dan saldo terpisah untuk unit kebersihan, keamanan lingkungan, posyandu, peringatan hari besar, dan dana sosial.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-purple-50 text-purple-900 border border-purple-200">
                    Total Kas Unit: <strong>Rp {{ number_format($totalSaldoUnit, 0, ',', '.') }}</strong>
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table wm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="py-3 px-3">Unit Kerja & Akun Kas</th>
                        <th class="py-3 px-3">Penanggung Jawab</th>
                        <th class="py-3 px-3">Rekening / Penyimpanan</th>
                        <th class="py-3 px-3 text-end">Saldo Awal</th>
                        <th class="py-3 px-3 text-end text-success">Pemasukan (+)</th>
                        <th class="py-3 px-3 text-end text-danger">Pengeluaran (-)</th>
                        <th class="py-3 px-3 text-end font-bold">Saldo Akhir</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monitoringUnitList as $unit)
                        <tr>
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-100 text-purple-800">
                                        {{ $unit['kode_kas'] }}
                                    </span>
                                    <span>{{ $unit['nama_kas'] }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-xs text-slate-600 font-medium">
                                {{ $unit['penanggung_jawab'] }}
                            </td>
                            <td class="py-3 px-3 text-xs font-mono text-slate-500">
                                {{ $unit['rekening'] }}
                            </td>
                            <td class="py-3 px-3 text-end font-mono text-slate-600 text-nowrap">
                                Rp {{ number_format($unit['saldo_awal'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-end font-mono font-bold text-success text-nowrap">
                                +Rp {{ number_format($unit['pemasukan'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-end font-mono font-bold text-danger text-nowrap">
                                -Rp {{ number_format($unit['pengeluaran'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-end font-mono font-black text-purple-900 text-nowrap bg-purple-50/40">
                                Rp {{ number_format($unit['saldo_akhir'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center text-nowrap">
                                <a href="{{ route('keuangan.buku_kas.show', $unit['id']) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 font-semibold text-[11px]">
                                    <i data-lucide="book-open" class="w-3.5 h-3.5 inline mr-1"></i> Buka Buku Kas
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50/90 font-extrabold text-slate-800 border-t border-slate-200">
                    <tr>
                        <td colspan="3" class="py-3 px-3">TOTAL GABUNGAN KAS UNIT LINGKUNGAN:</td>
                        <td class="py-3 px-3 text-end font-mono text-slate-700">
                            Rp {{ number_format(collect($monitoringUnitList)->sum('saldo_awal'), 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-end font-mono text-success">
                            +Rp {{ number_format(collect($monitoringUnitList)->sum('pemasukan'), 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-end font-mono text-danger">
                            -Rp {{ number_format(collect($monitoringUnitList)->sum('pengeluaran'), 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-end font-mono text-purple-900 bg-purple-100/60">
                            Rp {{ number_format($totalSaldoUnit, 0, ',', '.') }}
                        </td>
                        <td class="text-center text-[11px] text-slate-500 italic">
                            *Saldo unit tersimpan independen
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ======================================================================
         4. STATUS INTEGRASI PEMBAGIAN IURAN & PEMBUKUAN MULTI-AKUN
         Sesuai Poin 5: Satu Invoice, Alokasi Otomatis, Transparansi Gateway
         ====================================================================== --}}
    <div class="wm-fin-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                        <i data-lucide="split" class="w-5 h-5 text-[#1456A0]"></i> Status Integrasi Pembagian Iuran & Pembukuan Kas
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                        One Billing Aktif
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1 mb-0">
                    Sistem mendistribusikan satu tagihan gabungan keluarga secara otomatis ke rekening kas masing-masing pemilik dana tanpa saldo bercampur.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-blue-50 text-blue-900 border border-blue-200">
                    Status Gateway: <strong>Sandbox Ready (Transfer & QRIS Statis Aktif)</strong>
                </span>
            </div>
        </div>

        <!-- Rincian Alokasi Split Settlement Komponen Iuran -->
        <div class="table-responsive">
            <table class="table wm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="py-2.5 px-3">Komponen Iuran</th>
                        <th class="py-2.5 px-3">Rekening Akun Tujuan</th>
                        <th class="py-2.5 px-3 text-end">Teralokasi Bulan Ini</th>
                        <th class="py-2.5 px-3 text-center">Metode Settlement</th>
                        <th class="py-2.5 px-3 text-center">Status Pembukuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapAlokasiSplit as $split)
                        <tr>
                            <td class="py-3 px-3 font-bold text-slate-800">
                                {{ $split['nama'] }}
                            </td>
                            <td class="py-3 px-3 text-xs text-slate-600 font-medium">
                                {{ $split['target_kas'] }}
                            </td>
                            <td class="py-3 px-3 text-end font-mono font-bold text-slate-900">
                                Rp {{ number_format($split['nominal'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center text-xs">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $split['status_settlement'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center justify-center gap-1 w-max mx-auto">
                                    <i data-lucide="check" class="w-3 h-3"></i> {{ $split['rekonsiliasi'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-200 text-xs text-blue-900 mt-4 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-[#1456A0] flex-shrink-0"></i>
                <span><strong>Catatan Split Settlement:</strong> Pembayaran transfer rekening langsung mengkredit buku kas tujuan. Pembayaran tunai via RT berstatus <em>Titipan RT</em> sampai disetorkan ke RW tanpa manipulasi saldo.</span>
            </span>
            <a href="{{ route('tagihan.index') }}" class="text-[#1456A0] font-bold hover:underline whitespace-nowrap ml-3">Kelola Tagihan &rarr;</a>
        </div>
    </div>

    {{-- ======================================================================
         5. GRAFIK KEUANGAN KHUSUS KAS INDUK RW 013 (CHART.JS)
         ====================================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- 1. Grafik Pemasukan vs Pengeluaran Kas RW (7 Kolom) -->
        <div class="lg:col-span-7 wm-fin-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 text-emerald-600"></i> Tren Arus Kas Induk RW (6 Bulan)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5 mb-0">Hanya mutasi riil Kas RW 013 (tidak mencampur kas RT atau unit)</p>
                    </div>
                    <span class="badge" style="background:#E3F5EC;color:#16865D;border-radius:999px;font-size:0.75rem;padding:6px 12px;">
                        Khusus Kas RW
                    </span>
                </div>

                <div class="chart-container-wrap">
                    <canvas id="chartArusKas"></canvas>
                </div>
            </div>

            <div class="flex items-center justify-center gap-6 pt-3 border-t border-slate-100 text-xs font-semibold text-slate-600 mt-2">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Pemasukan Kas RW (+Debit)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Pengeluaran Kas RW (-Kredit)</span>
            </div>
        </div>

        <!-- 2. Grafik Perkembangan Saldo Kas Induk RW (5 Kolom) -->
        <div class="lg:col-span-5 wm-fin-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                            <i data-lucide="trending-up" class="w-5 h-5 text-[#1456A0]"></i> Perkembangan Saldo Kas RW
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5 mb-0">Kurva saldo Kas Induk RW 013 Sukamiskin</p>
                    </div>
                </div>

                <div class="chart-container-wrap">
                    <canvas id="chartSaldoPerkembangan"></canvas>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 mt-2">
                <span>Posisi Saldo Kas RW Saat Ini:</span>
                <strong class="text-slate-900 font-extrabold text-sm">Rp {{ number_format($saldoKasRw, 0, ',', '.') }}</strong>
            </div>
        </div>

        <!-- 3. Ringkasan Sumber Pemasukan Kas RW (6 Kolom) -->
        <div class="lg:col-span-6 wm-fin-card p-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-0">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-emerald-600"></i> Sumber Pemasukan Kas RW Terbesar
                </h3>
                <span class="text-xs text-slate-400">Kas Induk RW</span>
            </div>

            <div class="space-y-3">
                @php $totRwIn = max($kasRwMasuk, 1); @endphp
                @forelse($sumberPemasukan as $sp)
                    @php $pctIn = min(100, round(($sp->total / $totRwIn) * 100)); @endphp
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
                    <p class="text-center text-slate-400 text-xs py-4 mb-0">Belum ada kategori pemasukan Kas RW tercatat.</p>
                @endforelse
            </div>
        </div>

        <!-- 4. Ringkasan Kategori Pengeluaran Kas RW (6 Kolom) -->
        <div class="lg:col-span-6 wm-fin-card p-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-0">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-rose-600"></i> Kategori Pengeluaran Kas RW Terbesar
                </h3>
                <span class="text-xs text-slate-400">Kas Induk RW</span>
            </div>

            <div class="space-y-3">
                @php $totRwOut = max($kasRwKeluar, 1); @endphp
                @forelse($kategoriPengeluaran as $kp)
                    @php $pctOut = min(100, round(($kp->total / $totRwOut) * 100)); @endphp
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
                    <p class="text-center text-slate-400 text-xs py-4 mb-0">Belum ada kategori pengeluaran Kas RW tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ======================================================================
         6. JURNAL RIWAYAT TRANSAKSI KEUANGAN DENGAN FILTER MULTI-KRITERIA
         ====================================================================== --}}
    <div class="wm-fin-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-5">
            <div>
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                    <i data-lucide="history" class="w-5 h-5 text-[#1456A0]"></i> Jurnal Riwayat Transaksi Keuangan Lingkungan
                </h2>
                <p class="text-xs text-slate-500 mt-1 mb-0">Setiap transaksi teridentifikasi akun pemilik buku kas, nomor unik otomatis, dan bukti verifikasi</p>
            </div>
            <a href="{{ route('keuangan.transaksi.create') }}" class="wm-btn-primary" style="font-size: 0.8rem; padding: 7px 16px;">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Catat Transaksi Baru
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mb-5">
            <form method="GET" action="{{ route('keuangan.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 text-xs">
                <input type="hidden" name="periode" value="{{ $currentPeriode }}">
                
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. TRX, pihak, uraian..." 
                           class="w-full wm-input-fin px-3 py-2 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pemilik Dana</label>
                    <select name="pemilik_dana" class="w-full wm-input-fin px-3 py-2 text-xs">
                        <option value="">Semua Pemilik Dana</option>
                        <option value="rw" {{ request('pemilik_dana') === 'rw' ? 'selected' : '' }}>Kas Induk RW 013</option>
                        <option value="rt" {{ request('pemilik_dana') === 'rt' ? 'selected' : '' }}>Kas Otonom RT (8 RT)</option>
                        <option value="unit" {{ request('pemilik_dana') === 'unit' ? 'selected' : '' }}>Kas Unit Lingkungan</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Buku Kas (Rekening)</label>
                    <select name="buku_kas_id" class="w-full wm-input-fin px-3 py-2 text-xs">
                        <option value="">Semua Buku Kas</option>
                        <optgroup label="--- KAS INDUK RW ---">
                            @foreach($allBukuKas->where('pemilik_dana', 'rw') as $buku)
                                <option value="{{ $buku->id }}" {{ request('buku_kas_id') == $buku->id ? 'selected' : '' }}>{{ $buku->nama_kas }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="--- KAS OTONOM 8 RT ---">
                            @foreach($allBukuKas->where('pemilik_dana', 'rt') as $buku)
                                <option value="{{ $buku->id }}" {{ request('buku_kas_id') == $buku->id ? 'selected' : '' }}>{{ $buku->nama_kas }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="--- KAS UNIT LINGKUNGAN ---">
                            @foreach($allBukuKas->where('pemilik_dana', 'unit') as $buku)
                                <option value="{{ $buku->id }}" {{ request('buku_kas_id') == $buku->id ? 'selected' : '' }}>{{ $buku->nama_kas }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Transaksi</label>
                    <select name="jenis_transaksi" class="w-full wm-input-fin px-3 py-2 text-xs">
                        <option value="">Semua Jenis</option>
                        <option value="pemasukan" {{ request('jenis_transaksi') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="pengeluaran" {{ request('jenis_transaksi') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        <option value="transfer_keluar" {{ request('jenis_transaksi') === 'transfer_keluar' ? 'selected' : '' }}>Transfer Keluar (Internal)</option>
                        <option value="transfer_masuk" {{ request('jenis_transaksi') === 'transfer_masuk' ? 'selected' : '' }}>Transfer Masuk (Internal)</option>
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
                        <th class="py-3 px-3">Pemilik Dana</th>
                        <th class="py-3 px-3">Kategori & Uraian</th>
                        <th class="py-3 px-3">Buku Kas (Pemilik Rekening)</th>
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
                                @if($trx->referensi_pembayaran)
                                    <div class="text-slate-400 font-mono font-normal" style="font-size: 0.68rem;">Ref: {{ $trx->referensi_pembayaran }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-nowrap">
                                @if($trx->pemilik_dana === 'rw')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-[#1456A0] border border-blue-200">KAS RW</span>
                                @elseif($trx->pemilik_dana === 'rt')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">KAS {{ $trx->bukuKas->clean_rt ? 'RT '.$trx->bukuKas->clean_rt : 'RT' }}</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">UNIT</span>
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
                            <td colspan="11" class="py-5 text-center text-muted">
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
    // 1. Chart Arus Kas Bulanan Kas Induk RW (Bar Chart)
    const ctxArusKas = document.getElementById('chartArusKas');
    if (ctxArusKas) {
        new Chart(ctxArusKas, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labelsChart) !!},
                datasets: [
                    {
                        label: 'Pemasukan Kas RW (+)',
                        data: {!! json_encode($dataPemasukanChart) !!},
                        backgroundColor: '#16865D',
                        borderRadius: 8,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Pengeluaran Kas RW (-)',
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

    // 2. Chart Saldo Perkembangan Kas Induk RW (Line Chart)
    const ctxSaldo = document.getElementById('chartSaldoPerkembangan');
    if (ctxSaldo) {
        new Chart(ctxSaldo, {
            type: 'line',
            data: {
                labels: {!! json_encode($labelsChart) !!},
                datasets: [{
                    label: 'Saldo Kas RW',
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
                                return 'Saldo Kas RW: Rp ' + context.parsed.y.toLocaleString('id-ID');
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
