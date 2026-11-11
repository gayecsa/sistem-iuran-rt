@if($umkms->hasPages())
    <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 py-4 px-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="text-xs text-slate-500">
            Menampilkan <span class="font-bold text-slate-700">{{ $umkms->firstItem() }}</span> - 
            <span class="font-bold text-slate-700">{{ $umkms->lastItem() }}</span> dari 
            <span class="font-bold text-slate-700">{{ $umkms->total() }}</span> UMKM RW 013
        </div>

        <div class="flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($umkms->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-400 text-xs font-semibold cursor-not-allowed">
                    &larr; Sebelumnya
                </span>
            @else
                <a href="{{ $umkms->previousPageUrl() }}" 
                   onclick="navigateUmkmPage(event, '{{ $umkms->previousPageUrl() }}')" 
                   class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                    &larr; Sebelumnya
                </a>
            @endif

            {{-- Numbers --}}
            @foreach ($umkms->getUrlRange(1, $umkms->lastPage()) as $page => $url)
                @if ($page == $umkms->currentPage())
                    <span class="w-8 h-8 rounded-lg bg-blue-600 text-white text-xs font-bold flex items-center justify-center shadow-sm">
                        {{ $page }}
                    </span>
                @elseif($page == 1 || $page == $umkms->lastPage() || abs($page - $umkms->currentPage()) <= 1)
                    <a href="{{ $url }}" 
                       onclick="navigateUmkmPage(event, '{{ $url }}')" 
                       class="w-8 h-8 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold flex items-center justify-center transition-colors">
                        {{ $page }}
                    </a>
                @elseif(abs($page - $umkms->currentPage()) == 2)
                    <span class="w-8 h-8 text-slate-400 text-xs flex items-center justify-center">...</span>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($umkms->hasMorePages())
                <a href="{{ $umkms->nextPageUrl() }}" 
                   onclick="navigateUmkmPage(event, '{{ $umkms->nextPageUrl() }}')" 
                   class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                    Selanjutnya &rarr;
                </a>
            @else
                <span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-400 text-xs font-semibold cursor-not-allowed">
                    Selanjutnya &rarr;
                </span>
            @endif
        </div>
    </div>
@endif
