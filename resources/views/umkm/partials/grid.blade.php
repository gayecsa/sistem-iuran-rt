<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
    @forelse($umkms as $umkm)
        @php
            $isBuka = $umkm->is_buka_sekarang;
            $badge = $umkm->kategori_badge;
            $isMyBusiness = auth()->check() && (auth()->user()->id === $umkm->user_id);
        @endphp

        <div class="wm-umkm-card bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group cursor-pointer relative"
             onclick="openUmkmDetail({{ $umkm->id }})">
            
            {{-- Foto Banner UMKM dengan Overlay Badges --}}
            <div class="relative w-full aspect-[4/3] sm:h-48 bg-slate-100 overflow-hidden">
                <img src="{{ $umkm->foto_url }}" 
                     alt="{{ $umkm->nama_umkm }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='{{ $umkm->category_placeholder_image }}';">

                {{-- Status Buka / Tutup Overlay (Kiri Atas) --}}
                <div class="absolute top-3 left-3 z-10 flex items-center gap-1.5">
                    @if($isBuka)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/90 text-white backdrop-blur-md shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                            Buka
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-800/80 text-slate-200 backdrop-blur-md shadow-sm">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            Tutup
                        </span>
                    @endif

                    @if($isMyBusiness)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-600 text-white shadow-sm border border-white/40">
                            Usaha Saya
                        </span>
                    @endif
                </div>

                {{-- RT Badge Overlay (Kanan Atas) --}}
                <div class="absolute top-3 right-3 z-10">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-600/90 text-white backdrop-blur-md shadow-sm">
                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                        {{ $umkm->clean_rt_label }}
                    </span>
                </div>

                {{-- Kategori Badge Overlay (Kiri Bawah) --}}
                <div class="absolute bottom-3 left-3 z-10">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold shadow-sm"
                          style="background-color: {{ $badge['bg'] }}; color: {{ $badge['text'] }};">
                        <i data-lucide="{{ $badge['icon'] }}" class="w-3.5 h-3.5"></i>
                        {{ $badge['label'] }}
                    </span>
                </div>

                {{-- Jumlah Menu / Produk Badge (Kanan Bawah) --}}
                @if($umkm->produks_count ?? $umkm->produks->count())
                    <div class="absolute bottom-3 right-3 z-10">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-black/60 text-white backdrop-blur-md">
                            <i data-lucide="utensils" class="w-3 h-3"></i>
                            {{ $umkm->produks_count ?? $umkm->produks->count() }} Menu
                        </span>
                    </div>
                @endif
            </div>

            {{-- Body Informasi Kartu --}}
            <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                    {{-- Nama Usaha --}}
                    <h3 class="font-bold text-slate-900 text-base sm:text-lg leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors mb-1.5" title="{{ $umkm->nama_umkm }}">
                        {{ $umkm->nama_umkm }}
                    </h3>

                    {{-- Pemilik & Wilayah --}}
                    <div class="flex items-center gap-2 mb-2 text-xs text-slate-500">
                        <img src="{{ $umkm->owner_avatar_url }}" alt="{{ $umkm->owner_name }}" class="w-4 h-4 rounded-full object-cover">
                        <span class="truncate max-w-[140px]">{{ $umkm->owner_name }}</span>
                        <span>&bull;</span>
                        <span class="truncate">Sukamiskin</span>
                    </div>

                    {{-- Alamat Singkat --}}
                    <p class="text-xs text-slate-500 line-clamp-1 mb-3 flex items-center gap-1">
                        <i data-lucide="navigation" class="w-3 h-3 text-slate-400 shrink-0"></i>
                        <span>{{ $umkm->alamat }}</span>
                    </p>
                </div>

                <div>
                    {{-- Kisaran Harga & Jam --}}
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between mb-3">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block leading-tight">Kisaran Harga</span>
                            <span class="text-xs sm:text-sm font-extrabold text-emerald-600">
                                {{ $umkm->kisaran_harga_formatted }}
                            </span>
                        </div>
                        @if($umkm->jam_buka && $umkm->jam_tutup)
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block leading-tight">Jam Buka</span>
                                <span class="text-xs font-semibold text-slate-600">
                                    {{ substr($uBuka = $umkm->jam_buka, 0, 5) }} - {{ substr($umkm->jam_tutup, 0, 5) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="grid grid-cols-5 gap-2 pt-1" onclick="event.stopPropagation();">
                        <button type="button" 
                                onclick="openUmkmDetail({{ $umkm->id }})"
                                class="col-span-3 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white font-bold text-xs transition-colors duration-200">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            <span>{{ $isMyBusiness ? 'Kelola / Detail' : 'Detail Menu' }}</span>
                        </button>
                        
                        <a href="{{ $umkm->whatsapp_url }}" 
                           target="_blank" 
                           title="Chat WhatsApp Pemilik Usaha"
                           class="col-span-1 inline-flex items-center justify-center py-2 px-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors duration-200">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                        </a>

                        <a href="{{ $umkm->google_maps_url }}" 
                           target="_blank" 
                           title="Rute Navigasi Google Maps"
                           class="col-span-1 inline-flex items-center justify-center py-2 px-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-700 hover:text-white transition-colors duration-200">
                            <i data-lucide="map" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full py-12 px-4 text-center">
            <div class="max-w-md mx-auto bg-white rounded-3xl p-8 border border-slate-200 shadow-sm text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="store" class="w-10 h-10"></i>
                </div>
                <h4 class="text-lg font-bold text-slate-800 mb-2">Tidak Ditemukan UMKM</h4>
                <p class="text-sm text-slate-500 mb-6">
                    Tidak ada usaha atau produk warga yang cocok dengan kata kunci atau filter yang Anda pilih.
                </p>
                <button type="button" onclick="resetUmkmFilter()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 shadow-sm transition-all">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    Reset Pencarian & Filter
                </button>
            </div>
        </div>
    @endforelse
</div>
