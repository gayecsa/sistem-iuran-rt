@extends('layouts.app')

@section('title', 'Pengumuman Resmi RW 013 Suryatmajan')

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
    <!-- Header Hero Banner Full-Width -->
    <div class="bg-gradient-to-r from-[#0F3966] via-[#1456A0] to-[#1E6EC8] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm border border-white/30 text-white inline-block">
                    Pusat Informasi & Pengumuman Resmi • RW 013 Suryatmajan
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mt-2 text-white">Pengumuman & Agenda Warga</h1>
                <p class="text-blue-100 text-sm mt-1">
                    Informasi resmi dan agenda kegiatan warga RW 013 Suryatmajan
                </p>
            </div>

            @if($isPengurus)
                <div class="flex items-center gap-2">
                    <button type="button" onclick="document.getElementById('modalTambahPengumuman').classList.remove('hidden')"
                            class="px-5 py-3 rounded-xl bg-white text-[#1456A0] hover:bg-blue-50 font-bold text-xs shadow-md transition flex items-center gap-2">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Buat Pengumuman Baru</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3 text-emerald-800 text-sm font-medium">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3 text-rose-800 text-sm font-medium">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Filter & Pencarian Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        <!-- Filter Kategori Chips -->
        <div class="flex items-center gap-1.5 flex-wrap w-full md:w-auto">
            @php
                $currentKategori = request('kategori', 'Semua');
                $kategories = ['Semua', 'Informasi', 'Penting', 'Kegiatan', 'Posyandu', 'Keamanan'];
            @endphp
            @foreach($kategories as $kat)
                <a href="{{ route('pengumuman.index', array_merge(request()->query(), ['kategori' => $kat])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border {{ $currentKategori === $kat ? 'bg-[#1456A0] text-white border-[#1456A0] shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-slate-200' }}">
                    {{ $kat }}
                </a>
            @endforeach
        </div>

        <!-- Search Form -->
        <form action="{{ route('pengumuman.index') }}" method="GET" class="w-full md:w-72 flex items-center gap-2">
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif
            <div class="relative w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengumuman..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl text-xs border border-slate-300 focus:ring-2 focus:ring-[#1456A0] outline-none bg-slate-50">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>
            @if(request('search'))
                <a href="{{ route('pengumuman.index', ['kategori' => request('kategori')]) }}" class="p-2 text-slate-400 hover:text-slate-600" title="Reset pencarian">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Grid Kartu Pengumuman Fleksibel Full-Width -->
    @if($semua_pengumuman->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($semua_pengumuman as $p)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition duration-200 flex flex-col overflow-hidden">
                    <!-- Cover Image atau Fallback Visual Banner -->
                    <div class="h-48 w-full relative overflow-hidden bg-slate-100 flex-shrink-0">
                        @if($p->gambar_url)
                            <img src="{{ $p->gambar_url }}" alt="{{ $p->judul }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br {{ $p->fallback_gradient }} flex flex-col items-center justify-center text-white p-4 relative select-none">
                                <div class="w-16 h-16 rounded-full bg-white/15 backdrop-blur-sm flex items-center justify-center border border-white/20 shadow-inner mb-1.5">
                                    <i data-lucide="{{ $p->fallback_icon }}" class="w-8 h-8 text-white"></i>
                                </div>
                                <span class="text-[11px] font-extrabold uppercase tracking-widest text-white/90">
                                    {{ $p->kategori }} RW 013
                                </span>
                            </div>
                        @endif

                        <!-- Floating Category Badge -->
                        <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-[11px] font-black border shadow-sm backdrop-blur-md bg-white/95 {{ $p->badge_class }}">
                            {{ $p->kategori }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex flex-col flex-grow space-y-2.5">
                        <div class="flex items-center gap-1.5 text-xs text-slate-400 font-medium">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#1456A0] flex-shrink-0"></i>
                            <span>{{ \Carbon\Carbon::parse($p->tanggal_aktif ?? $p->created_at)->translatedFormat('d F Y') }}</span>
                        </div>

                        <!-- Judul Pengumuman: Navy Text High Contrast -->
                        <h2 class="font-extrabold text-[#172B4D] text-base line-clamp-2 hover:text-[#1456A0] transition">
                            <a href="{{ route('pengumuman.show', $p->id) }}" class="hover:underline">
                                {{ $p->judul }}
                            </a>
                        </h2>

                        <!-- Excerpt Isi -->
                        <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed flex-grow">
                            {{ Str::limit(strip_tags($p->isi), 120, '...') }}
                        </p>

                        <!-- Card Footer Aksi -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs mt-auto">
                            <a href="{{ route('pengumuman.show', $p->id) }}" class="font-bold text-[#1456A0] hover:text-[#0F3966] inline-flex items-center gap-1">
                                <span>Baca Selengkapnya</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>

                            @if($isPengurus)
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('pengumuman.edit', $p->id) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-[#1456A0] hover:bg-blue-50 btn-pengumuman-edit transition" title="Edit Pengumuman">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('pengumuman.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 btn-pengumuman-delete transition" title="Hapus Pengumuman">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($semua_pengumuman->hasPages())
            <div class="pt-4">
                {{ $semua_pengumuman->links() }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
            <i data-lucide="inbox" class="w-16 h-16 text-slate-300 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-slate-800">Belum Ada Pengumuman</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                Tidak ada pengumuman yang sesuai dengan kategori atau kata kunci yang dipilih.
            </p>
        </div>
    @endif
</div>

@if($isPengurus)
<!-- Modal Tambah Pengumuman Baru -->
<div id="modalTambahPengumuman" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-[#172B4D] flex items-center gap-2">
                <i data-lucide="megaphone" class="w-5 h-5 text-[#1456A0]"></i>
                <span>Buat Pengumuman Baru</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahPengumuman').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('pengumuman.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Judul Pengumuman <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" required placeholder="Contoh: Jadwal Kerja Bakti Lingkungan RW 013"
                       class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <select name="kategori" required class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none bg-white">
                        <option value="Informasi">Informasi Umum</option>
                        <option value="Penting">Penting / Darurat</option>
                        <option value="Kegiatan">Kegiatan Warga</option>
                        <option value="Posyandu">Posyandu & Kesehatan</option>
                        <option value="Keamanan">Keamanan & Ketertiban</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Berlaku</label>
                    <input type="date" name="tanggal_aktif" value="{{ date('Y-m-d') }}"
                           class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Foto / Poster Pengumuman (Opsional)</label>
                <input type="file" name="gambar" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#1456A0] hover:file:bg-blue-100 border border-slate-300 rounded-xl p-1 bg-white">
                <p class="text-[11px] text-slate-400 mt-1">Format JPG, PNG, WEBP (Maksimal 3 MB). Jika kosong, cover visual otomatis diterapkan.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pengumuman <span class="text-rose-500">*</span></label>
                <textarea name="isi" rows="5" required placeholder="Tuliskan isi informasi atau rincian agenda warga secara lengkap..."
                          class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalTambahPengumuman').classList.add('hidden')"
                        class="px-4 py-2.5 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white rounded-xl shadow">
                    Publikasikan Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
