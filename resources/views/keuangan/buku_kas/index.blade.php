@extends('layouts.app')

@section('title', 'Buku Kas & Rekening Keuangan - Warkas Machi RW 013')

@section('content')
<!-- Tailwind CSS Setup dengan Preflight Disabled agar Selaras dengan Tema Warkas Machi -->
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
                    wmNavy: '#0F2C59',
                    wmNavyDark: '#0A1E3F',
                    wmPrimary: '#1456A0',
                    wmSecondary: '#2878D0',
                    wmGreen: '#16865D',
                    wmGreenLight: '#E8F5E9',
                    wmRed: '#DC2626',
                    wmRedLight: '#FEE2E2',
                    wmBgSoft: '#F4F7FB',
                }
            }
        }
    }
</script>

<style>
    /* Styling Terpadu Warkas Machi Financial Dashboard */
    .fin-card {
        background-color: var(--wm-card, #FFFFFF);
        border: 1px solid var(--wm-border, #E2E8F0);
        color: var(--wm-text, #172B4D);
        border-radius: 20px;
        transition: all 0.25s ease;
    }
    .fin-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(15, 44, 89, 0.08), 0 8px 10px -6px rgba(15, 44, 89, 0.04);
    }
    .fin-card-rw {
        background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
        border: 1.5px solid #2878D0 !important;
        box-shadow: 0 8px 20px -4px rgba(20, 86, 160, 0.12);
    }
    .segmented-tab {
        transition: all 0.2s ease;
    }
    .segmented-tab.active {
        background-color: #1456A0;
        color: #FFFFFF !important;
        box-shadow: 0 4px 12px rgba(20, 86, 160, 0.25);
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div class="space-y-6">

    <!-- 1. BREADCRUMB & HEADER HALAMAN -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-sm">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-3" aria-label="Breadcrumb">
            <a href="{{ route('keuangan.dashboard') }}" class="hover:text-blue-700 transition flex items-center gap-1">
                <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                <span>Dashboard RW</span>
            </a>
            <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
            <a href="{{ route('keuangan.dashboard') }}" class="hover:text-blue-700 transition">
                <span>Keuangan</span>
            </a>
            <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
            <span class="text-slate-800 font-bold">Buku Kas</span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Buku Kas & Rekening Keuangan
                    </h1>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-blue-600"></i>
                        {{ $isRw ? 'Monitoring Terpadu RW 013' : 'Buku Kas RT ' . $rtStr }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-3xl leading-relaxed">
                    Pusat pemantauan keuangan RW 013, kas RT 001–008, Bank Sampah, dan dana kegiatan.
                </p>
            </div>

            <!-- Tombol Aksi Utama -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.laporan.cetak') }}" target="_blank"
                   class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 shadow-sm transition inline-flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4 text-slate-600"></i>
                    <span>Unduh Laporan</span>
                </a>

                <a href="{{ route('keuangan.transaksi.create') }}" 
                   class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-[#1456A0] hover:bg-blue-800 text-white shadow-md hover:shadow-lg transition inline-flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>+ Catat Transaksi Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. FILTER PERIODE & KARTU RINGKASAN KEUANGAN -->
    <div class="space-y-4">
        <!-- Bar Navigasi Filter Periode -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider px-2 hidden sm:inline">Periode:</span>
                
                <a href="{{ route('keuangan.buku_kas.index', ['periode' => 'bulan_ini', 'jenis' => request('jenis')]) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $periode === 'bulan_ini' ? 'bg-[#1456A0] text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('keuangan.buku_kas.index', ['periode' => 'bulan_lalu', 'jenis' => request('jenis')]) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $periode === 'bulan_lalu' ? 'bg-[#1456A0] text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Bulan Sebelumnya
                </a>
                <a href="{{ route('keuangan.buku_kas.index', ['periode' => 'tahun_ini', 'jenis' => request('jenis')]) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $periode === 'tahun_ini' ? 'bg-[#1456A0] text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Tahun Ini
                </a>

                <!-- Tombol Rentang Tanggal Khusus -->
                <button type="button" onclick="document.getElementById('customDateModal').classList.remove('hidden')" 
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 {{ $periode === 'custom' ? 'bg-[#1456A0] text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i data-lucide="calendar-range" class="w-3.5 h-3.5"></i>
                    <span>{{ $periode === 'custom' ? 'Rentang: ' . $periodeLabel : 'Rentang Khusus' }}</span>
                </button>
            </div>

            <div class="text-xs text-slate-500 font-medium px-2 flex items-center gap-1.5">
                <i data-lucide="info" class="w-3.5 h-3.5 text-blue-500"></i>
                <span>Data mutasi dihitung untuk: <strong class="text-slate-800">{{ $periodeLabel }}</strong></span>
            </div>
        </div>

        <!-- 4 Kartu Statistik Keuangan Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- 1. Total Saldo Seluruh Kas (Saldo Berjalan Akumulasi) -->
            <div class="fin-card p-5 bg-white rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Saldo Seluruh Kas</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shadow-xs">
                        <i data-lucide="landmark" class="w-5 h-5"></i>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                </h3>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Saldo berjalan aktif di {{ $totalRekeningAktif }} rekening</span>
                </div>
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-50/50 rounded-full pointer-events-none"></div>
            </div>

            <!-- 2. Total Pemasukan Periode -->
            <div class="fin-card p-5 bg-white rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Total Pemasukan</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shadow-xs">
                        <i data-lucide="trending-up" class="w-5 h-5"></i>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight">
                    +Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </h3>
                <div class="mt-2 text-xs text-slate-500 font-medium flex items-center gap-1">
                    <i data-lucide="arrow-down-left" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>Pemasukan & transfer masuk</span>
                </div>
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-50/50 rounded-full pointer-events-none"></div>
            </div>

            <!-- 3. Total Pengeluaran Periode -->
            <div class="fin-card p-5 bg-white rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-rose-700 uppercase tracking-wider">Total Pengeluaran</span>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center shadow-xs">
                        <i data-lucide="trending-down" class="w-5 h-5"></i>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-rose-600 tracking-tight">
                    -Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </h3>
                <div class="mt-2 text-xs text-slate-500 font-medium flex items-center gap-1">
                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-rose-600"></i>
                    <span>Pengeluaran & operasional</span>
                </div>
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rose-50/50 rounded-full pointer-events-none"></div>
            </div>

            <!-- 4. Jumlah Rekening Aktif -->
            <div class="fin-card p-5 bg-white rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider">Rekening Terdaftar</span>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center shadow-xs">
                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                    </div>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    {{ $totalRekeningAktif }} <span class="text-sm font-semibold text-slate-500">Rekening</span>
                </h3>
                <div class="mt-2 text-xs text-slate-500 font-medium flex items-center gap-1">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-blue-600"></i>
                    <span>Seluruh rekening berstatus aktif</span>
                </div>
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-indigo-50/50 rounded-full pointer-events-none"></div>
            </div>

        </div>
    </div>

    <!-- 3. NAVIGASI KATEGORI KAS & REALTIME SEARCH BAR -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        <!-- Segmented Navigation Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1" id="categoryTabs">
            <button type="button" onclick="switchCategory('all', this)"
                    class="segmented-tab px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap active">
                <span>Semua Kas</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-white/20 text-white font-extrabold" id="badge-all">
                    {{ $kategoriCounts['all'] }}
                </span>
            </button>

            @if($isRw)
                <button type="button" onclick="switchCategory('kas_rw', this)"
                        class="segmented-tab px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <i data-lucide="landmark" class="w-3.5 h-3.5"></i>
                    <span>Kas Induk RW</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] bg-slate-200 text-slate-700 font-extrabold">
                        {{ $kategoriCounts['kas_rw'] }}
                    </span>
                </button>
            @endif

            <button type="button" onclick="switchCategory('kas_rt', this)"
                    class="segmented-tab px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap bg-slate-100 text-slate-600 hover:bg-slate-200">
                <i data-lucide="home" class="w-3.5 h-3.5"></i>
                <span>Kas RT (001–008)</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-slate-200 text-slate-700 font-extrabold">
                    {{ $kategoriCounts['kas_rt'] }}
                </span>
            </button>

            <button type="button" onclick="switchCategory('kas_sampah', this)"
                    class="segmented-tab px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap bg-slate-100 text-slate-600 hover:bg-slate-200">
                <i data-lucide="recycle" class="w-3.5 h-3.5"></i>
                <span>Kas Sampah & Kebersihan</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-slate-200 text-slate-700 font-extrabold">
                    {{ $kategoriCounts['kas_sampah'] }}
                </span>
            </button>

            @if($isRw)
                <button type="button" onclick="switchCategory('dana_kegiatan', this)"
                        class="segmented-tab px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    <span>Dana Kegiatan Khusus</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] bg-slate-200 text-slate-700 font-extrabold">
                        {{ $kategoriCounts['dana_kegiatan'] }}
                    </span>
                </button>
            @endif
        </div>

        <!-- Realtime Live Search -->
        <div class="relative w-full md:w-72">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="text" id="kasSearchInput" onkeyup="filterCards()" placeholder="Cari nama kas, PIC, bank..." 
                   class="w-full pl-9 pr-4 py-2 rounded-xl text-xs bg-slate-50 border border-slate-300 focus:bg-white focus:ring-2 focus:ring-[#1456A0] focus:border-transparent outline-none transition">
        </div>
    </div>

    <!-- 4. DAFTAR REKENING KAS (GRID MODERN CARD) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="kasGrid">
        @forelse($allBukuKas as $kas)
            <div class="fin-card bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col justify-between kas-card {{ $kas->jenis_kas === 'kas_rw' ? 'fin-card-rw' : '' }}"
                 data-jenis="{{ $kas->jenis_kas }}"
                 data-nama="{{ strtolower($kas->nama_kas) }}"
                 data-kode="{{ strtolower($kas->kode_kas) }}"
                 data-pic="{{ strtolower($kas->nama_penanggung_jawab) }}"
                 data-bank="{{ strtolower($kas->nama_bank) }}">

                <div>
                    <!-- Top Badge Bar -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <!-- Icon Box -->
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm shadow-xs {{ $kas->jenis_kas === 'kas_rw' ? 'bg-[#0F2C59] text-white' : ($kas->jenis_kas === 'kas_rt' ? 'bg-emerald-100 text-emerald-800' : ($kas->jenis_kas === 'kas_sampah' ? 'bg-teal-100 text-teal-800' : 'bg-indigo-100 text-indigo-800')) }}">
                                <i data-lucide="{{ $kas->jenis_icon }}" class="w-4 h-4"></i>
                            </div>

                            <div>
                                <span class="font-mono text-xs font-bold text-slate-700 block leading-tight">
                                    {{ $kas->kode_kas }}
                                </span>
                                @if($kas->rt_number)
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.2 rounded">
                                        RT {{ str_pad($kas->rt_number, 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-500">RW 013</span>
                                @endif
                            </div>
                        </div>

                        <!-- Status & Category Badge -->
                        <div class="flex items-center gap-1.5">
                            @if($kas->jenis_kas === 'kas_rw')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-900 border border-blue-200 uppercase tracking-wider">
                                    KAS PUSAT
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $kas->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $kas->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                {{ $kas->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    <!-- Nama Rekening -->
                    <h3 class="text-base font-extrabold text-slate-900 line-clamp-1 group-hover:text-blue-700 transition">
                        {{ $kas->nama_kas }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2 leading-relaxed">
                        {{ $kas->deskripsi ?: 'Buku kas perbendaharaan resmi warga Warkas Machi RW 013.' }}
                    </p>

                    <!-- PIC & Bank Info -->
                    <div class="mt-4 p-3 rounded-xl bg-slate-50/80 border border-slate-200/60 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">PIC:</span>
                            <span class="font-bold text-slate-800 text-right line-clamp-1">
                                {{ $kas->nama_penanggung_jawab ?: 'Pengurus Lingkungan' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Rekening:</span>
                            <span class="font-mono font-semibold text-slate-700 flex items-center gap-1 text-right">
                                <i data-lucide="credit-card" class="w-3 h-3 text-slate-400"></i>
                                {{ $kas->masked_rekening }}
                            </span>
                        </div>
                    </div>

                    <!-- Saldo Terkini Section -->
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                            Saldo Saat Ini
                        </span>
                        <div class="flex items-baseline justify-between gap-2 mt-0.5">
                            <p class="text-2xl font-black text-slate-900 tracking-tight">
                                Rp {{ number_format($kas->saldo_sekarang, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Target Anggaran Bar (jika tersedia) -->
                    @if($kas->target_anggaran > 0)
                        @php $pct = min(100, round(($kas->saldo_sekarang / $kas->target_anggaran) * 100)); @endphp
                        <div class="mt-2.5">
                            <div class="flex justify-between text-[11px] text-slate-500 mb-1">
                                <span>Target: Rp {{ number_format($kas->target_anggaran, 0, ',', '.') }}</span>
                                <span class="font-bold text-slate-700">{{ $pct }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-[#1456A0] h-1.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer Card & Aksi -->
                <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-between gap-2">
                    <span class="text-[11px] text-slate-400 font-medium">
                        Awal: Rp {{ number_format($kas->saldo_awal, 0, ',', '.') }}
                    </span>

                    <div class="flex items-center gap-1.5">
                        <!-- Tombol Detail Quick Modal -->
                        <button type="button" 
                                onclick="openDetailModal({{ json_encode($kas) }})"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                            Detail
                        </button>

                        <!-- Tombol Lihat Buku Kas Penuh -->
                        <a href="{{ route('keuangan.buku_kas.show', $kas->id) }}" 
                           class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white shadow-xs transition inline-flex items-center gap-1">
                            <span>Lihat Buku Kas</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="wallet-cards" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Rekening Kas</h3>
                <p class="text-xs text-slate-500 mt-1">Belum ada buku kas yang terdaftar pada sistem.</p>
            </div>
        @endforelse
    </div>

    <!-- Empty State Pencarian Client-Side -->
    <div id="noMatchState" class="hidden py-12 text-center bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
            <i data-lucide="search-x" class="w-8 h-8"></i>
        </div>
        <h3 class="text-base font-bold text-slate-800">Tidak Ditemukan Rekening Kas</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
            Tidak ada buku kas yang cocok dengan kata kunci atau filter kategori yang dipilih.
        </p>
        <button type="button" onclick="resetSearchAndFilter()" class="mt-3 px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
            Reset Pencarian
        </button>
    </div>

</div>

<!-- 5. MODAL: QUICK DETAIL REKENING KAS -->
<div id="quickDetailModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden animate__animated animate__fadeInUp animate__faster">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50 to-blue-50/30">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-[#1456A0] text-white flex items-center justify-center font-bold">
                    <i data-lucide="landmark" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-slate-900" id="modalNamaKas">-</h4>
                    <span class="text-[11px] font-mono text-slate-500" id="modalKodeKas">-</span>
                </div>
            </div>
            <button type="button" onclick="closeDetailModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-700 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-5 space-y-4 text-xs">
            <!-- Saldo Card -->
            <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-100 text-center">
                <span class="text-[11px] font-bold text-blue-800 uppercase tracking-wider block">Saldo Saat Ini</span>
                <p class="text-2xl font-black text-blue-900 mt-0.5" id="modalSaldo">-</p>
                <div class="mt-1 flex items-center justify-center gap-2 text-[11px] text-blue-700">
                    <span>Saldo Awal: <strong id="modalSaldoAwal">-</strong></span>
                </div>
            </div>

            <!-- Detail Information List -->
            <div class="space-y-2 border-t border-slate-100 pt-3">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">Kategori Kas:</span>
                    <span class="font-bold text-slate-800" id="modalJenisKas">-</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">Wilayah RT:</span>
                    <span class="font-bold text-slate-800" id="modalRt">-</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">Penanggung Jawab (PIC):</span>
                    <span class="font-bold text-slate-800" id="modalPic">-</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">Rekening Bank:</span>
                    <span class="font-mono font-semibold text-slate-800" id="modalRekening">-</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">Atas Nama:</span>
                    <span class="font-bold text-slate-800" id="modalAtasNama">-</span>
                </div>
                <div class="py-1">
                    <span class="text-slate-400 font-medium block mb-0.5">Deskripsi:</span>
                    <p class="text-slate-600 text-xs leading-relaxed" id="modalDeskripsi">-</p>
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between gap-2">
            <button type="button" onclick="closeDetailModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition">
                Tutup
            </button>
            <a href="#" id="modalShowLink" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white shadow-xs transition inline-flex items-center gap-1.5">
                <span>Buka Rekening Koran Lengkap</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>
</div>

<!-- 6. MODAL: FILTER RENTANG TANGGAL KHUSUS -->
<div id="customDateModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full p-5">
        <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-3">
            <h4 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                <span>Pilih Rentang Tanggal</span>
            </h4>
            <button type="button" onclick="document.getElementById('customDateModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('keuangan.buku_kas.index') }}" method="GET" class="space-y-3 text-xs">
            <input type="hidden" name="periode" value="custom">
            <input type="hidden" name="jenis" value="{{ request('jenis', 'all') }}">

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate ?: date('Y-m-01') }}" required
                       class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate ?: date('Y-m-d') }}" required
                       class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-600 outline-none">
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('customDateModal').classList.add('hidden')"
                        class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#1456A0] text-white font-bold hover:bg-blue-800 shadow">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 7. JAVASCRIPT: INSTANT TAB FILTERING, LIVE SEARCH & MODALS -->
<script>
    let activeCategory = 'all';

    function switchCategory(category, btnElement) {
        activeCategory = category;

        // Update active class on tabs
        document.querySelectorAll('.segmented-tab').forEach(tab => {
            tab.classList.remove('active', 'bg-[#1456A0]', 'text-white', 'shadow');
            tab.classList.add('bg-slate-100', 'text-slate-600');
        });

        btnElement.classList.add('active', 'bg-[#1456A0]', 'text-white', 'shadow');
        btnElement.classList.remove('bg-slate-100', 'text-slate-600');

        filterCards();
    }

    function filterCards() {
        const searchVal = document.getElementById('kasSearchInput').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.kas-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardJenis = card.getAttribute('data-jenis');
            const cardNama = card.getAttribute('data-nama') || '';
            const cardKode = card.getAttribute('data-kode') || '';
            const cardPic = card.getAttribute('data-pic') || '';
            const cardBank = card.getAttribute('data-bank') || '';

            const matchesCategory = (activeCategory === 'all' || cardJenis === activeCategory);
            const matchesSearch = !searchVal || 
                cardNama.includes(searchVal) || 
                cardKode.includes(searchVal) || 
                cardPic.includes(searchVal) || 
                cardBank.includes(searchVal);

            if (matchesCategory && matchesSearch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Toggle No Match State
        const noMatch = document.getElementById('noMatchState');
        if (visibleCount === 0) {
            noMatch.classList.remove('hidden');
        } else {
            noMatch.classList.add('hidden');
        }
    }

    function resetSearchAndFilter() {
        document.getElementById('kasSearchInput').value = '';
        const allTab = document.querySelector('.segmented-tab');
        if (allTab) {
            switchCategory('all', allTab);
        } else {
            filterCards();
        }
    }

    // Quick Detail Modal Handler
    function openDetailModal(kas) {
        document.getElementById('modalNamaKas').textContent = kas.nama_kas;
        document.getElementById('modalKodeKas').textContent = kas.kode_kas;
        document.getElementById('modalSaldo').textContent = 'Rp ' + Number(kas.saldo_sekarang || 0).toLocaleString('id-ID');
        document.getElementById('modalSaldoAwal').textContent = 'Rp ' + Number(kas.saldo_awal || 0).toLocaleString('id-ID');
        document.getElementById('modalJenisKas').textContent = (kas.jenis_kas || '').toUpperCase().replace(/_/g, ' ');
        document.getElementById('modalRt').textContent = kas.rt_number ? 'RT ' + String(kas.rt_number).padStart(3, '0') : 'Seluruh Wilayah RW 013';
        document.getElementById('modalPic').textContent = kas.nama_penanggung_jawab || 'Pengurus Lingkungan';
        document.getElementById('modalRekening').textContent = kas.no_rekening ? (kas.nama_bank || 'Bank') + ' ' + kas.no_rekening : 'Kas Fisik / Tunai';
        document.getElementById('modalAtasNama').textContent = kas.atas_nama || '-';
        document.getElementById('modalDeskripsi').textContent = kas.deskripsi || 'Tidak ada deskripsi tambahan.';

        const linkEl = document.getElementById('modalShowLink');
        linkEl.href = "{{ url('/keuangan/buku-kas') }}/" + kas.id;

        document.getElementById('quickDetailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('quickDetailModal').classList.add('hidden');
    }

    // Inisialisasi ikon Lucide
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Cek URL params untuk initial active tab
        const urlParams = new URLSearchParams(window.location.search);
        const jenisParam = urlParams.get('jenis');
        if (jenisParam && jenisParam !== 'all') {
            const matchBtn = document.querySelector(`button[onclick*="'${jenisParam}'"]`);
            if (matchBtn) {
                switchCategory(jenisParam, matchBtn);
            }
        }
    });
</script>
@endsection
