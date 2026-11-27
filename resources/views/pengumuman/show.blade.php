@extends('layouts.app')

@section('title', $pengumuman->judul . ' - Pengumuman RW 013 Suryatmajan')

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

<div class="w-full space-y-6">
    <!-- Breadcrumb / Tombol Navigasi Kembali -->
    <div class="flex items-center justify-between">
        <a href="{{ route('pengumuman.index') }}"
           class="inline-flex items-center gap-2 text-xs font-bold text-[#1456A0] bg-white hover:bg-blue-50 px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Pengumuman</span>
        </a>

        @if($isPengurus)
            <div class="flex items-center gap-2">
                <a href="{{ route('pengumuman.edit', $pengumuman->id) }}"
                   class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm transition">
                    <i data-lucide="edit-3" class="w-4 h-4 text-[#1456A0]"></i>
                    <span>Edit Pengumuman</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Main Detail Article Card Full-Width -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <!-- Header Info -->
        <div class="space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-extrabold border shadow-xs {{ $pengumuman->badge_class }}">
                    {{ $pengumuman->kategori }}
                </span>
                <span class="text-xs text-slate-400 font-medium flex items-center gap-1">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#1456A0]"></i>
                    {{ \Carbon\Carbon::parse($pengumuman->tanggal_aktif ?? $pengumuman->created_at)->translatedFormat('l, d F Y') }}
                </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#172B4D] leading-tight">
                {{ $pengumuman->judul }}
            </h1>
        </div>

        <!-- Cover Image atau Fallback Visual Banner -->
        @if($pengumuman->gambar_url)
            <div class="w-full rounded-2xl overflow-hidden max-h-[500px] border border-slate-200 shadow-sm bg-slate-50">
                <img src="{{ $pengumuman->gambar_url }}" alt="{{ $pengumuman->judul }}" class="w-full h-full object-cover">
            </div>
        @else
            <div class="w-full h-48 sm:h-64 rounded-2xl bg-gradient-to-br {{ $pengumuman->fallback_gradient }} flex flex-col items-center justify-center text-white p-6 shadow-inner relative overflow-hidden select-none">
                <div class="w-20 h-20 rounded-full bg-white/15 backdrop-blur-sm flex items-center justify-center border border-white/20 mb-3 shadow-inner">
                    <i data-lucide="{{ $pengumuman->fallback_icon }}" class="w-10 h-10 text-white"></i>
                </div>
                <span class="text-xs font-extrabold tracking-widest uppercase text-white/90">
                    Pengumuman Resmi RW 013 Suryatmajan
                </span>
            </div>
        @endif

        <!-- Body Isi Pengumuman -->
        <div class="border-t border-b border-slate-100 py-6 text-slate-700 text-sm sm:text-base leading-relaxed whitespace-pre-line font-normal">
            {!! nl2br(e($pengumuman->isi)) !!}
        </div>

        <!-- Footer Meta & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-slate-500 pt-2">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-blue-100 text-[#1456A0] flex items-center justify-center font-bold">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="font-bold text-slate-800">Sekretariat RW 013 Suryatmajan</p>
                    <p class="text-[11px] text-slate-400">Diterbitkan pada {{ \Carbon\Carbon::parse($pengumuman->created_at)->translatedFormat('d M Y H:i') }} WIB</p>
                </div>
            </div>

            @if($isPengurus)
                <div class="flex items-center gap-2">
                    <a href="{{ route('pengumuman.edit', $pengumuman->id) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition inline-flex items-center gap-1.5 btn-pengumuman-edit">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5 text-[#1456A0]"></i> Edit
                    </a>
                    <form action="{{ route('pengumuman.destroy', $pengumuman->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 transition inline-flex items-center gap-1.5 border border-rose-200 btn-pengumuman-delete">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Pengumuman Lainnya -->
    @if(isset($relatedAnnouncements) && $relatedAnnouncements->count() > 0)
        <div class="space-y-4 pt-2">
            <h2 class="text-base font-extrabold text-[#172B4D] flex items-center gap-2">
                <i data-lucide="layers" class="w-5 h-5 text-[#1456A0]"></i>
                <span>Pengumuman Lainnya</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($relatedAnnouncements as $r)
                    <a href="{{ route('pengumuman.show', $r->id) }}" class="block text-decoration-none group">
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm group-hover:shadow-md group-hover:-translate-y-1 transition duration-200 h-full flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border {{ $r->badge_class }}">
                                    {{ $r->kategori }}
                                </span>
                                <h3 class="font-bold text-xs text-[#172B4D] group-hover:text-[#1456A0] transition line-clamp-2">
                                    {{ $r->judul }}
                                </h3>
                            </div>
                            <div class="text-[11px] text-slate-400 flex items-center gap-1 pt-2 border-t border-slate-100">
                                <i data-lucide="calendar" class="w-3 h-3 text-[#1456A0]"></i>
                                {{ \Carbon\Carbon::parse($r->tanggal_aktif ?? $r->created_at)->translatedFormat('d M Y') }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
