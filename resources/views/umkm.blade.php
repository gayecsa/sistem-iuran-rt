@extends('layouts.app')

@section('content')
{{-- Tailwind CDN dengan preflight false agar kompatibel dengan Bootstrap dan style Warkas Machi --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: {
            preflight: false
        },
        theme: {
            extend: {
                colors: {
                    wmNavy: '#0F3966',
                    wmBlue: '#1456A0',
                    wmLightBlue: '#1D68FE',
                    wmGreen: '#16865D',
                }
            }
        }
    }
</script>

{{-- Leaflet.js CSS & JS untuk Peta Navigasi Interaktif Rute Sukamiskin --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="w-full max-w-[1600px] mx-auto px-2 sm:px-4 py-2 space-y-6">

    {{-- ======================================================================
         TOP BAR: NAVIGASI ROLE BERBASIS KEPEMILIKAN
         ====================================================================== --}}
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-slate-200 shadow-sm text-xs font-bold">
        <div class="flex items-center gap-1.5 sm:gap-2">
            <a href="{{ route('umkm.index') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white shadow-sm flex items-center gap-1.5 transition-colors">
                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                <span>Katalog Publik</span>
            </a>

            <a href="{{ route('umkm.saya') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 flex items-center gap-1.5 transition-colors">
                <i data-lucide="store" class="w-4 h-4 text-blue-600"></i>
                <span>Kelola UMKM Saya ({{ $myUmkmCount }})</span>
            </a>

            @if(auth()->check() && in_array(auth()->user()->role, ['rw', 'admin', 'super_admin', 'rt']))
                <a href="{{ route('umkm.moderasi') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 flex items-center gap-1.5 transition-colors">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                    <span>Pengawasan & Moderasi</span>
                    @if($pendingCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-amber-500 text-white text-[10px]">{{ $pendingCount }}</span>
                    @endif
                </a>
            @endif
        </div>

        <div class="text-slate-500 text-[11px] font-normal hidden md:block">
            <span class="font-semibold text-slate-700">Prinsip Warkas Machi:</span> Pemilik Mengelola &bull; RT/RW Mengawasi &bull; Warga Menggunakan
        </div>
    </div>

    {{-- ======================================================================
         1. HERO HEADER: JELAJAHI UMKM RW 013
         ====================================================================== --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0F3966] via-[#1456A0] to-[#1D68FE] text-white p-6 sm:p-8 md:p-10 shadow-lg">
        {{-- Decorative Background Elements --}}
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/4 -bottom-24 w-96 h-96 rounded-full bg-blue-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="max-w-3xl space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-blue-100">
                    <i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-amber-300"></i>
                    <span>Marketplace Lokal Warga RW 013 Sukamiskin, Arcamanik</span>
                </div>

                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight text-white leading-tight">
                    Jelajahi UMKM RW 013
                </h1>

                <p class="text-blue-100/90 text-sm sm:text-base leading-relaxed">
                    Temukan kuliner, produk lokal, toko, dan berbagai jasa milik warga RW 013. Dukung perputaran ekonomi mandiri dan pertumbuhan usaha tetangga kita.
                </p>

                {{-- Counter Badges & Info --}}
                <div class="flex flex-wrap items-center gap-2 pt-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/15 backdrop-blur-md border border-white/10 font-bold">
                        <i data-lucide="store" class="w-4 h-4 text-emerald-300"></i>
                        <span>{{ $totalUmkm }} UMKM Terdaftar</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/15 backdrop-blur-md border border-white/10 font-bold">
                        <i data-lucide="layers" class="w-4 h-4 text-cyan-300"></i>
                        <span>{{ count($kategoriList) - 1 }} Kategori Usaha</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/15 backdrop-blur-md border border-white/10 font-bold">
                        <i data-lucide="utensils" class="w-4 h-4 text-amber-300"></i>
                        <span>{{ $totalProduk }}+ Produk & Menu</span>
                    </span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full lg:w-auto">
                <button type="button" 
                        onclick="openDaftarModal()" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white text-blue-800 hover:bg-blue-50 font-extrabold text-sm shadow-md transition-all duration-200">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-blue-600"></i>
                    <span>Daftarkan Usaha Saya</span>
                </button>

                @if(auth()->check() && in_array(auth()->user()->role, ['rw', 'admin', 'super_admin', 'rt']))
                    <a href="{{ route('umkm.moderasi') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-emerald-500 text-white hover:bg-emerald-600 font-extrabold text-sm shadow-md transition-all duration-200">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>Menu Pengawasan</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Alert Berhasil / Gagal --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" class="text-emerald-500 hover:text-emerald-700" onclick="this.parentElement.remove()">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    {{-- ======================================================================
         2. PENCARIAN & FILTER KATEGORI (RESPONSIVE CHIPS & REAL-TIME SEARCH)
         ====================================================================== --}}
    <div class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200 shadow-sm space-y-4">
        {{-- Search Input Bar --}}
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-5 h-5 text-blue-600"></i>
            </div>
            
            <input type="text" 
                   id="umkmSearchInput" 
                   value="{{ request('q') }}"
                   placeholder="Cari makanan, minuman, toko, atau jasa..." 
                   class="w-full pl-12 pr-12 py-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 text-sm sm:text-base focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all outline-none"
                   autocomplete="off"
                   oninput="handleSearchInput(this.value)">

            {{-- Clear Search Button & Spinner --}}
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                <span id="searchLoadingSpinner" class="hidden text-blue-600">
                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                </span>
                <button type="button" 
                        id="clearSearchBtn" 
                        onclick="clearSearch()" 
                        class="{{ request('q') ? '' : 'hidden' }} p-1.5 rounded-xl hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        {{-- Horizontal Scrollable Category Chips --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs sm:text-sm font-semibold select-none" id="kategoriChipsContainer">
            @foreach($kategoriList as $kat)
                @php
                    $isActive = (request('kategori') == $kat) || (!request('kategori') && $kat === 'Semua UMKM');
                @endphp
                <button type="button" 
                        onclick="selectKategori('{{ $kat }}', this)" 
                        data-kategori="{{ $kat }}"
                        class="umkm-chip px-4 py-2 rounded-xl whitespace-nowrap transition-all duration-200 flex items-center gap-1.5 {{ $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20 active-chip' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <span>{{ $kat }}</span>
                </button>
            @endforeach
        </div>

        {{-- Secondary Filter Toolbar: RT, Status Buka, Sorting --}}
        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs sm:text-sm">
            <div class="flex flex-wrap items-center gap-2">
                {{-- Filter RT Dropdown --}}
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-500 font-semibold text-xs">Wilayah:</span>
                    <select id="filterRtSelect" 
                            onchange="applyFilters()" 
                            class="py-1.5 px-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-xs font-semibold focus:border-blue-600 focus:bg-white outline-none cursor-pointer">
                        <option value="Semua" {{ request('rt') == 'Semua' || !request('rt') ? 'selected' : '' }}>Semua RT (001 - 008)</option>
                        @for($i = 1; $i <= 8; $i++)
                            @php $rtVal = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $rtVal }}" {{ request('rt') == $rtVal ? 'selected' : '' }}>RT {{ $rtVal }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Filter Buka Sekarang Button --}}
                <button type="button" 
                        id="toggleBukaBtn" 
                        onclick="toggleBukaSekarang()" 
                        class="px-3 py-1.5 rounded-xl border text-xs font-bold flex items-center gap-1.5 transition-colors cursor-pointer {{ request('status') === 'buka' ? 'bg-emerald-50 border-emerald-300 text-emerald-700 shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 {{ request('status') === 'buka' ? 'animate-pulse' : '' }}"></span>
                    <span>Buka Sekarang</span>
                </button>
            </div>

            {{-- Sorting Dropdown --}}
            <div class="flex items-center gap-1.5 ml-auto">
                <span class="text-slate-500 font-semibold text-xs">Urutkan:</span>
                <select id="sortSelect" 
                        onchange="applyFilters()" 
                        class="py-1.5 px-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-xs font-semibold focus:border-blue-600 focus:bg-white outline-none cursor-pointer">
                    <option value="terbaru" {{ request('sort') == 'terbaru' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                    <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama A &rarr; Z</option>
                    <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Nama Z &rarr; A</option>
                    <option value="harga_asc" {{ request('sort') == 'harga_asc' ? 'selected' : '' }}>Harga Termurah</option>
                    <option value="harga_desc" {{ request('sort') == 'harga_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         3. KATALOG GRID UMKM (3-4 KOLOM DESKTOP, 2 KOLOM TABLET/MOBILE)
         ====================================================================== --}}
    <div id="umkmGridContainer">
        @include('umkm.partials.grid', ['umkms' => $umkms])
    </div>

    {{-- Pagination Container --}}
    <div id="umkmPaginationContainer">
        @include('umkm.partials.pagination', ['umkms' => $umkms])
    </div>

</div>

{{-- ==========================================================================
     MODAL DETAIL UMKM (MENU LENGKAP, INFORMASI, & PETA RUTE NAVIGASI)
     ========================================================================== --}}
<div class="modal fade" id="modalDetailUmkm" tabindex="-1" aria-labelledby="modalDetailUmkmLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            
            {{-- Modal Header: Foto Usaha Banner + Ringkasan --}}
            <div class="relative h-48 sm:h-64 w-full bg-slate-800 overflow-hidden">
                <img id="detailFotoCover" src="" alt="Cover UMKM" class="w-full h-full object-cover opacity-85">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                {{-- Close Button --}}
                <button type="button" class="absolute top-4 right-4 z-20 w-9 h-9 rounded-full bg-black/40 text-white hover:bg-black/70 flex items-center justify-center backdrop-blur-md transition-colors" data-bs-dismiss="modal" aria-label="Close">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>

                {{-- Header Identity Info --}}
                <div class="absolute bottom-4 left-4 right-4 z-10 text-white space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="detailBadgeKategori" class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-amber-400 text-amber-950">
                            Kuliner
                        </span>
                        <span id="detailBadgeStatus" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500 text-white">
                            Buka
                        </span>
                        <span id="detailBadgeRt" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-600 text-white">
                            RT 001
                        </span>
                    </div>

                    <h2 id="detailNamaUmkm" class="text-xl sm:text-2xl md:text-3xl font-black text-white leading-tight">
                        Nama Usaha
                    </h2>

                    <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-200">
                        <i data-lucide="map-pin" class="w-4 h-4 text-rose-400 shrink-0"></i>
                        <span id="detailAlamatUmkm" class="truncate">Alamat UMKM Sukamiskin Arcamanik</span>
                    </div>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4 sm:p-6 space-y-6">
                {{-- Tabs Navigasi Modal --}}
                <div class="flex border-b border-slate-200 gap-4 sm:gap-6 text-sm font-bold">
                    <button type="button" id="tabBtnProduk" onclick="switchDetailTab('produk')" class="pb-3 border-b-2 border-blue-600 text-blue-600 flex items-center gap-2">
                        <i data-lucide="utensils" class="w-4 h-4"></i>
                        <span>Daftar Menu & Produk (<span id="detailCountProduk">0</span>)</span>
                    </button>
                    <button type="button" id="tabBtnInfo" onclick="switchDetailTab('info')" class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4"></i>
                        <span>Profil & Jam Buka</span>
                    </button>
                    <button type="button" id="tabBtnRute" onclick="switchDetailTab('rute')" class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2">
                        <i data-lucide="route" class="w-4 h-4"></i>
                        <span>Peta Rute Navigasi</span>
                    </button>
                </div>

                {{-- TAB 1: DAFTAR MENU & PRODUK --}}
                <div id="tabContentProduk" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-slate-500 mb-0">Klik tombol pesan untuk order langsung ke nomor WhatsApp pemilik usaha.</p>
                        <button type="button" 
                                id="btnBukaModalTambahProduk" 
                                onclick="openTambahProdukModal()" 
                                class="hidden text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            <span>Tambah Menu Baru</span>
                        </button>
                    </div>

                    {{-- Grid Produk --}}
                    <div id="detailProdukGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Injected dynamically via JS --}}
                    </div>
                </div>

                {{-- TAB 2: INFORMASI USAHA & JAM BUKA --}}
                <div id="tabContentInfo" class="hidden space-y-6">
                    <div class="bg-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-200">
                        <h4 class="text-sm font-bold text-slate-800 mb-2 flex items-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                            <span>Tentang Usaha</span>
                        </h4>
                        <p id="detailDeskripsiUmkm" class="text-sm text-slate-600 leading-relaxed whitespace-pre-line mb-0">
                            -
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Pemilik & Kontak --}}
                        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-4">
                            <img id="detailOwnerAvatar" src="" alt="Owner" class="w-12 h-12 rounded-full object-cover border-2 border-blue-100">
                            <div>
                                <span class="text-[11px] uppercase font-bold text-slate-400 block">Pemilik Usaha</span>
                                <strong id="detailNamaPemilik" class="text-sm text-slate-900 block">Nama Pemilik</strong>
                                <span id="detailNoHp" class="text-xs text-slate-500 font-mono">08...</span>
                            </div>
                        </div>

                        {{-- Jam Operasional --}}
                        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                <i data-lucide="clock" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <span class="text-[11px] uppercase font-bold text-slate-400 block">Jam Operasional</span>
                                <strong id="detailJamBukaTutup" class="text-sm text-slate-900 block">10:00 - 21:00 WIB</strong>
                                <span class="text-xs text-emerald-600 font-semibold">Senin s/d Minggu</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: PETA RUTE NAVIGASI LOKAL --}}
                <div id="tabContentRute" class="hidden space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-blue-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <i data-lucide="navigation-2" class="w-6 h-6 text-blue-600 shrink-0"></i>
                            <div>
                                <span class="text-xs font-bold block">Rute dari Wilayah RW 013 Sukamiskin</span>
                                <span class="text-xs text-blue-700">Estimasi ~3-5 menit berkendara / jalan kaki di lingkungan RW 013</span>
                            </div>
                        </div>
                        <a id="detailLinkGmapsNav" href="#" target="_blank" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 transition-colors inline-flex items-center gap-1.5 shrink-0 shadow-sm">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            <span>Buka di Google Maps</span>
                        </a>
                    </div>

                    <div id="modalLeafletMap" class="w-full h-80 rounded-2xl border border-slate-200 overflow-hidden shadow-inner relative z-10"></div>
                </div>
            </div>

            {{-- Modal Footer: Quick Links & Actions --}}
            <div class="modal-footer bg-slate-50 border-t border-slate-200 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-slate-500">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 inline text-emerald-600"></i>
                    <span>Terverifikasi resmi di RW 013 Sukamiskin, Arcamanik</span>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Khusus Pemilik Usaha Sah --}}
                    <a id="detailBtnKelolaMenu" href="#" class="hidden px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-colors">
                        <i data-lucide="utensils" class="w-3.5 h-3.5 inline mr-1"></i> Kelola Menu
                    </a>

                    <a id="detailBtnEditUmkm" href="#" class="hidden px-4 py-2.5 rounded-xl border border-blue-600 text-blue-600 hover:bg-blue-50 font-bold text-xs transition-colors">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5 inline mr-1"></i> Edit Usaha
                    </a>

                    {{-- Untuk Warga / Pengunjung --}}
                    <a id="detailBtnLaporkan" href="#" class="hidden px-3.5 py-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold text-xs transition-colors" title="Laporkan UMKM Bermasalah">
                        <i data-lucide="flag" class="w-3.5 h-3.5 inline mr-1"></i> Laporkan
                    </a>

                    <a id="detailBtnWaFooter" href="#" target="_blank" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-colors inline-flex items-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        <span>Hubungi WhatsApp</span>
                    </a>
                    
                    <button type="button" class="px-4 py-2.5 rounded-xl bg-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-300" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ==========================================================================
     MODAL DAFTAR USAHA UMKM BARU (UNTUK WARGA / RT / ADMIN)
     ========================================================================== --}}
<div class="modal fade" id="modalDaftarUmkm" tabindex="-1" aria-labelledby="modalDaftarUmkmLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-blue-700 to-blue-600 text-white p-5 border-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <i data-lucide="store" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-bold text-lg text-white" id="modalDaftarUmkmLabel">Pendaftaran Usaha UMKM Warga</h5>
                        <p class="text-xs text-blue-100 mb-0">Daftarkan usaha Anda di katalog marketplace RW 013 Sukamiskin</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('umkm.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Nama UMKM --}}
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Tempat Usaha / Usaha Mandiri <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_umkm" required placeholder="Contoh: Dapur Sunda Bu Maya" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none">
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Usaha <span class="text-rose-500">*</span></label>
                            <select name="kategori" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                                @foreach($kategoriList as $k)
                                    @if($k !== 'Semua UMKM')
                                        <option value="{{ $k }}">{{ $k }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        {{-- Jenis Usaha --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Sub-Jenis Usaha <span class="text-rose-500">*</span></label>
                            <input type="text" name="jenis_usaha" required placeholder="Contoh: Masakan Sunda / Cafe / Jahit" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                        </div>

                        {{-- Nama Pemilik --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemilik Usaha <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_pemilik" value="{{ auth()->user()->name }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                        </div>

                        {{-- No. WhatsApp --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                            <input type="text" name="no_hp" value="{{ auth()->user()->phone ?? '08' }}" required placeholder="08..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                        </div>

                        {{-- Wilayah RT --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Wilayah RT <span class="text-rose-500">*</span></label>
                            <select name="rt_number" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                                @for($i = 1; $i <= 8; $i++)
                                    @php $rtPad = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $rtPad }}" {{ auth()->user()->clean_rt_number == $rtPad ? 'selected' : '' }}>RT {{ $rtPad }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Jam Buka & Tutup --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jam Buka</label>
                                <input type="text" name="jam_buka" value="08:00" placeholder="08:00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jam Tutup</label>
                                <input type="text" name="jam_tutup" value="21:00" placeholder="21:00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                            </div>
                        </div>

                        {{-- Alamat Lengkap --}}
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Tempat Usaha (Sukamiskin, Arcamanik) <span class="text-rose-500">*</span></label>
                            <textarea name="alamat" rows="2" required placeholder="Contoh: Jl. Pacuan Kuda No. 12, RT 001 / RW 013, Kelurahan Sukamiskin" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">{{ auth()->user()->address }}</textarea>
                        </div>

                        {{-- Foto Usaha --}}
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Foto Tempat Usaha / Produk Utama (Maks. 2MB)</label>
                            <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                        {{-- Deskripsi Usaha --}}
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Singkat Usaha</label>
                            <textarea name="deskripsi" rows="3" placeholder="Ceritakan produk unggulan atau keistimewaan usaha Anda..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-slate-50 border-t border-slate-200 px-6 py-4 flex items-center justify-between">
                    <span class="text-xs text-slate-500">* Wajib diisi dengan benar.</span>
                    <div class="flex items-center gap-2">
                        <button type="button" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 text-xs font-semibold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 shadow-sm">Kirim Pendaftaran Usaha</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==========================================================================
     MODAL TAMBAH PRODUK / MENU BARU
     ========================================================================== --}}
<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            <div class="modal-header bg-blue-600 text-white p-4 border-0">
                <h5 class="modal-title font-bold text-base text-white" id="modalTambahProdukLabel">Tambah Menu / Produk Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formTambahProduk" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-5 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Menu / Produk <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_produk" required placeholder="Contoh: Nasi Timbel Ayam Komplit" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga" required placeholder="25000" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Menu</label>
                        <input type="text" name="kategori_produk" placeholder="Makanan Utama / Minuman / Camilan" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Produk Singkat</label>
                        <textarea name="deskripsi" rows="2" placeholder="Komposisi, porsi, atau rasa..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Foto Menu (Opsional)</label>
                        <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="is_best_seller" value="1" class="rounded text-blue-600">
                            <span>Tandai Menu Best Seller</span>
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-slate-50 border-t border-slate-200 px-5 py-3 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 text-xs font-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 shadow-sm">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==========================================================================
     STYLES KHUSUS MARKETPLACE UMKM & DARK MODE
     ========================================================================== --}}
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .scrollbar-none::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-none {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    /* Dark Mode Overrides */
    [data-theme="dark"] .wm-umkm-card,
    body.dark-mode .wm-umkm-card {
        background: #16213A !important;
        border-color: #1E293B !important;
    }
    [data-theme="dark"] .wm-umkm-card h3,
    body.dark-mode .wm-umkm-card h3 {
        color: #F1F5F9 !important;
    }
    [data-theme="dark"] .modal-content,
    body.dark-mode .modal-content {
        background: #16213A !important;
        color: #F1F5F9 !important;
    }
    [data-theme="dark"] .modal-footer,
    body.dark-mode .modal-footer {
        background: #0F172A !important;
        border-color: #1E293B !important;
    }
</style>

{{-- ==========================================================================
     SCRIPTS: LIVE SEARCH DEBOUNCE, FILTER AJAX, MODAL DETAIL & LEAFLET MAP
     ========================================================================== --}}
<script>
    let searchTimeout = null;
    let currentKategori = '{{ request('kategori', 'Semua UMKM') }}';
    let currentRt = '{{ request('rt', 'Semua') }}';
    let currentBukaOnly = '{{ request('status') === 'buka' ? '1' : '0' }}';
    let currentSort = '{{ request('sort', 'terbaru') }}';

    let currentUmkmDetailId = null;
    let leafletMapInstance = null;
    let userMarker = null;
    let umkmMarker = null;
    let polylineRoute = null;

    // Koordinat Rumah Warga RW 013 Sukamiskin
    const userLat = -6.915000;
    const userLng = 107.679000;

    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    // Handle Input Search dengan Debounce 300ms
    function handleSearchInput(val) {
        const clearBtn = document.getElementById('clearSearchBtn');
        if (clearBtn) {
            if (val.trim().length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchUmkmGrid();
        }, 320);
    }

    // Clear Search
    function clearSearch() {
        const input = document.getElementById('umkmSearchInput');
        if (input) {
            input.value = '';
            document.getElementById('clearSearchBtn').classList.add('hidden');
            fetchUmkmGrid();
        }
    }

    // Handle Category Chip Click
    function selectKategori(kategori, el) {
        currentKategori = kategori;

        // Update UI Chip Active State
        document.querySelectorAll('.umkm-chip').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-600/20', 'active-chip');
            btn.classList.add('bg-slate-100', 'text-slate-700');
        });

        if (el) {
            el.classList.remove('bg-slate-100', 'text-slate-700');
            el.classList.add('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-600/20', 'active-chip');
        }

        fetchUmkmGrid();
    }

    // Toggle Buka Sekarang
    function toggleBukaSekarang() {
        const btn = document.getElementById('toggleBukaBtn');
        if (currentBukaOnly === '1') {
            currentBukaOnly = '0';
            btn.classList.remove('bg-emerald-50', 'border-emerald-300', 'text-emerald-700', 'shadow-sm');
            btn.classList.add('border-slate-200', 'bg-slate-50', 'text-slate-600');
        } else {
            currentBukaOnly = '1';
            btn.classList.remove('border-slate-200', 'bg-slate-50', 'text-slate-600');
            btn.classList.add('bg-emerald-50', 'border-emerald-300', 'text-emerald-700', 'shadow-sm');
        }
        fetchUmkmGrid();
    }

    // Apply Secondary Filters (RT & Sort)
    function applyFilters() {
        const rtSelect = document.getElementById('filterRtSelect');
        const sortSelect = document.getElementById('sortSelect');

        if (rtSelect) currentRt = rtSelect.value;
        if (sortSelect) currentSort = sortSelect.value;

        fetchUmkmGrid();
    }

    // Reset Semua Filter
    function resetUmkmFilter() {
        currentKategori = 'Semua UMKM';
        currentRt = 'Semua';
        currentBukaOnly = '0';
        currentSort = 'terbaru';

        const input = document.getElementById('umkmSearchInput');
        if (input) input.value = '';
        const clearBtn = document.getElementById('clearSearchBtn');
        if (clearBtn) clearBtn.classList.add('hidden');

        const rtSelect = document.getElementById('filterRtSelect');
        if (rtSelect) rtSelect.value = 'Semua';

        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) sortSelect.value = 'terbaru';

        const toggleBtn = document.getElementById('toggleBukaBtn');
        if (toggleBtn) {
            toggleBtn.classList.remove('bg-emerald-50', 'border-emerald-300', 'text-emerald-700', 'shadow-sm');
            toggleBtn.classList.add('border-slate-200', 'bg-slate-50', 'text-slate-600');
        }

        // Reset chips UI
        document.querySelectorAll('.umkm-chip').forEach(btn => {
            if (btn.getAttribute('data-kategori') === 'Semua UMKM') {
                btn.classList.remove('bg-slate-100', 'text-slate-700');
                btn.classList.add('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-600/20', 'active-chip');
            } else {
                btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-600/20', 'active-chip');
                btn.classList.add('bg-slate-100', 'text-slate-700');
            }
        });

        fetchUmkmGrid();
    }

    // Filter Khusus Pending Verifikasi
    function filterPendingVerifikasi() {
        currentKategori = 'Semua UMKM';
        currentRt = 'Semua';
        fetchUmkmGrid(1, true);
    }

    // Fetch UMKM Grid via AJAX
    function fetchUmkmGrid(page = 1, isPending = false) {
        const spinner = document.getElementById('searchLoadingSpinner');
        if (spinner) spinner.classList.remove('hidden');

        const searchVal = document.getElementById('umkmSearchInput') ? document.getElementById('umkmSearchInput').value : '';

        const params = new URLSearchParams({
            q: searchVal,
            kategori: currentKategori,
            rt: currentRt,
            status: currentBukaOnly === '1' ? 'buka' : '',
            sort: currentSort,
            page: page,
        });

        if (isPending) {
            params.append('pending_only', '1');
        }

        // Update URL query string tanpa reload halaman
        window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);

        fetch(`/umkm?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            const gridContainer = document.getElementById('umkmGridContainer');
            const pagContainer = document.getElementById('umkmPaginationContainer');

            if (gridContainer && data.html) {
                gridContainer.innerHTML = data.html;
            }
            if (pagContainer && data.pagination !== undefined) {
                pagContainer.innerHTML = data.pagination;
            }

            if (window.lucide) {
                lucide.createIcons();
            }
        })
        .catch(err => {
            console.error('Error fetching UMKM grid:', err);
        })
        .finally(() => {
            if (spinner) spinner.classList.add('hidden');
        });
    }

    // Pagination Click Handler
    function navigateUmkmPage(e, url) {
        e.preventDefault();
        try {
            const parsed = new URL(url);
            const page = parsed.searchParams.get('page') || 1;
            fetchUmkmGrid(page);
            window.scrollTo({ top: 350, behavior: 'smooth' });
        } catch(err) {
            window.location.href = url;
        }
    }

    // =========================================================================
    // MODAL DETAIL UMKM LOGIC
    // =========================================================================
    let currentDetailUmkmData = null;

    function openUmkmDetail(id) {
        currentUmkmDetailId = id;
        const modalEl = document.getElementById('modalDetailUmkm');
        if (!modalEl) return;

        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        // Tampilkan placeholder loading di tabs
        document.getElementById('detailNamaUmkm').textContent = 'Memuat informasi UMKM...';
        document.getElementById('detailProdukGrid').innerHTML = '<div class="col-span-full py-8 text-center text-slate-400"><i data-lucide="loader-2" class="w-8 h-8 animate-spin mx-auto mb-2 text-blue-600"></i><p class="text-sm">Memuat daftar menu & produk...</p></div>';
        if (window.lucide) lucide.createIcons();

        // Switch ke tab produk secara default
        switchDetailTab('produk');

        fetch(`/umkm/${id}`)
            .then(res => res.json())
            .then(data => {
                currentDetailUmkmData = data;
                renderDetailModal(data);
            })
            .catch(err => {
                console.error('Error fetching detail:', err);
            });
    }

    function renderDetailModal(d) {
        document.getElementById('detailNamaUmkm').textContent = d.nama_umkm;
        document.getElementById('detailAlamatUmkm').textContent = `${d.alamat}, ${d.rt_number} / RW ${d.rw_number}, Sukamiskin, Arcamanik`;
        document.getElementById('detailFotoCover').src = d.foto_url;
        document.getElementById('detailNamaPemilik').textContent = d.nama_pemilik;
        document.getElementById('detailOwnerAvatar').src = d.owner_avatar;
        document.getElementById('detailNoHp').textContent = d.no_hp;
        document.getElementById('detailJamBukaTutup').textContent = `${d.jam_buka || '08:00'} - ${d.jam_tutup || '21:00'} WIB`;
        document.getElementById('detailDeskripsiUmkm').textContent = d.deskripsi || 'Tidak ada deskripsi tambahan.';
        document.getElementById('detailBadgeRt').textContent = d.rt_number;
        document.getElementById('detailBadgeKategori').textContent = d.kategori;
        document.getElementById('detailCountProduk').textContent = d.produks.length;

        // Status Buka Badge
        const statusBadge = document.getElementById('detailBadgeStatus');
        if (d.is_buka_sekarang) {
            statusBadge.textContent = '● Buka Sekarang';
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-sm';
        } else {
            statusBadge.textContent = 'Tutup';
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-700 text-slate-200 shadow-sm';
        }

        // WhatsApp Buttons
        document.getElementById('detailBtnWaFooter').href = d.whatsapp_url;
        document.getElementById('detailLinkGmapsNav').href = d.gmaps_url;

        // KEAMANAN KEPEMILIKAN: Tombol Edit & Kelola Menu HANYA tampil jika pengguna adalah pemilik sah
        const btnEdit = document.getElementById('detailBtnEditUmkm');
        const btnKelolaMenu = document.getElementById('detailBtnKelolaMenu');
        const btnLaporkan = document.getElementById('detailBtnLaporkan');
        const btnTambahProd = document.getElementById('btnBukaModalTambahProduk');

        if (d.is_owner) {
            btnEdit.href = `/umkm/${d.id}/edit`;
            btnEdit.classList.remove('hidden');
            if (btnKelolaMenu) {
                btnKelolaMenu.href = `/umkm/${d.id}/menu`;
                btnKelolaMenu.classList.remove('hidden');
            }
            if (btnTambahProd) {
                btnTambahProd.classList.remove('hidden');
            }
            if (btnLaporkan) btnLaporkan.classList.add('hidden');
        } else {
            btnEdit.classList.add('hidden');
            if (btnKelolaMenu) btnKelolaMenu.classList.add('hidden');
            if (btnTambahProd) btnTambahProd.classList.add('hidden');
            if (btnLaporkan) {
                btnLaporkan.href = `/pengaduan?subjek=Laporan+UMKM+${encodeURIComponent(d.nama_umkm)}`;
                btnLaporkan.classList.remove('hidden');
            }
        }

        // Tombol Tambah Produk Form Action
        const formProduk = document.getElementById('formTambahProduk');
        if (formProduk) {
            formProduk.action = `/umkm/${d.id}/produk`;
        }

        // Render Produk Grid
        const grid = document.getElementById('detailProdukGrid');
        if (d.produks.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full py-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <i data-lucide="utensils" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                    <p class="text-sm text-slate-500 font-semibold mb-0">Belum ada daftar menu atau produk yang ditambahkan.</p>
                </div>
            `;
        } else {
            let html = '';
            d.produks.forEach(p => {
                const waOrderText = encodeURIComponent(`Halo ${d.nama_umkm}, saya warga RW 013 ingin memesan menu "${p.nama_produk}" (${p.harga_rupiah}). Apakah masih tersedia?`);
                const phoneClean = (d.no_hp || '').replace(/[^0-9]/g, '');
                const waOrderUrl = `https://wa.me/${phoneClean.startsWith('0') ? '62' + phoneClean.slice(1) : phoneClean}?text=${waOrderText}`;

                html += `
                    <div class="bg-white rounded-2xl border border-slate-200 p-3.5 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            ${p.foto_url ? `<img src="${p.foto_url}" class="w-full h-32 rounded-xl object-cover mb-2.5" onerror="this.style.display='none';">` : ''}
                            <div class="flex items-start justify-between gap-1 mb-1">
                                <h5 class="text-sm font-bold text-slate-900 leading-snug">${p.nama_produk}</h5>
                                ${p.is_best_seller ? `<span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 shrink-0">Best Seller</span>` : ''}
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2 mb-2">${p.deskripsi || ''}</p>
                        </div>
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-sm font-black text-emerald-600">${p.harga_rupiah}</span>
                            <a href="${waOrderUrl}" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white font-bold text-xs inline-flex items-center gap-1 transition-colors">
                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                <span>Pesan</span>
                            </a>
                        </div>
                    </div>
                `;
            });
            grid.innerHTML = html;
        }

        if (window.lucide) {
            lucide.createIcons();
        }
    }

    // Switch Tabs in Modal Detail
    function switchDetailTab(tab) {
        const btnProduk = document.getElementById('tabBtnProduk');
        const btnInfo = document.getElementById('tabBtnInfo');
        const btnRute = document.getElementById('tabBtnRute');

        const cProduk = document.getElementById('tabContentProduk');
        const cInfo = document.getElementById('tabContentInfo');
        const cRute = document.getElementById('tabContentRute');

        // Reset
        [btnProduk, btnInfo, btnRute].forEach(b => {
            b.classList.remove('border-blue-600', 'text-blue-600');
            b.classList.add('border-transparent', 'text-slate-500');
        });
        [cProduk, cInfo, cRute].forEach(c => c.classList.add('hidden'));

        if (tab === 'produk') {
            btnProduk.classList.add('border-blue-600', 'text-blue-600');
            btnProduk.classList.remove('border-transparent', 'text-slate-500');
            cProduk.classList.remove('hidden');
        } else if (tab === 'info') {
            btnInfo.classList.add('border-blue-600', 'text-blue-600');
            btnInfo.classList.remove('border-transparent', 'text-slate-500');
            cInfo.classList.remove('hidden');
        } else if (tab === 'rute') {
            btnRute.classList.add('border-blue-600', 'text-blue-600');
            btnRute.classList.remove('border-transparent', 'text-slate-500');
            cRute.classList.remove('hidden');
            setTimeout(initLeafletMap, 200);
        }
    }

    // Inisialisasi Peta Leaflet untuk Rute
    function initLeafletMap() {
        if (!currentDetailUmkmData) return;

        const destLat = parseFloat(currentDetailUmkmData.latitude) || -6.916000;
        const destLng = parseFloat(currentDetailUmkmData.longitude) || 107.678000;

        const mapContainer = document.getElementById('modalLeafletMap');
        if (!mapContainer) return;

        if (!leafletMapInstance) {
            leafletMapInstance = L.map('modalLeafletMap').setView([userLat, userLng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap RW 013 Sukamiskin'
            }).addTo(leafletMapInstance);
        } else {
            leafletMapInstance.invalidateSize();
        }

        if (userMarker) leafletMapInstance.removeLayer(userMarker);
        if (umkmMarker) leafletMapInstance.removeLayer(umkmMarker);
        if (polylineRoute) leafletMapInstance.removeLayer(polylineRoute);

        const homeIcon = L.divIcon({
            className: 'custom-home-icon',
            html: "<div style='background-color:#16865D; color:white; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3);'><i class='fas fa-home'></i></div>",
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        const storeIcon = L.divIcon({
            className: 'custom-store-icon',
            html: "<div style='background-color:#1456A0; color:white; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3);'><i class='fas fa-store'></i></div>",
            iconSize: [36, 36],
            iconAnchor: [18, 18]
        });

        userMarker = L.marker([userLat, userLng], { icon: homeIcon })
            .addTo(leafletMapInstance)
            .bindPopup('<b>Titik Warga RW 013</b><br>Sukamiskin, Bandung');

        umkmMarker = L.marker([destLat, destLng], { icon: storeIcon })
            .addTo(leafletMapInstance)
            .bindPopup(`<b>${currentDetailUmkmData.nama_umkm}</b><br>${currentDetailUmkmData.alamat}`)
            .openPopup();

        const latlngs = [
            [userLat, userLng],
            [(userLat + destLat) / 2, (userLng + destLng) / 2],
            [destLat, destLng]
        ];

        polylineRoute = L.polyline(latlngs, {
            color: '#1D68FE',
            weight: 5,
            opacity: 0.9,
            dashArray: '8, 12'
        }).addTo(leafletMapInstance);

        const group = new L.featureGroup([userMarker, umkmMarker]);
        leafletMapInstance.fitBounds(group.getBounds().pad(0.2));
    }

    // Modal Pendaftaran UMKM Baru
    function openDaftarModal() {
        const modalEl = document.getElementById('modalDaftarUmkm');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    // Modal Tambah Produk Baru
    function openTambahProdukModal() {
        const modalEl = document.getElementById('modalTambahProduk');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }
</script>
@endsection
