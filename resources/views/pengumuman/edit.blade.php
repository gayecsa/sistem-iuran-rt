@extends('layouts.app')

@section('title', 'Edit Pengumuman - ' . $pengumuman->judul)

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
    <div class="flex items-center justify-between">
        <a href="{{ route('pengumuman.show', $pengumuman->id) }}"
           class="inline-flex items-center gap-2 text-xs font-bold text-[#1456A0] bg-white hover:bg-blue-50 px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Detail</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 border border-blue-200 text-[#1456A0] inline-block">
                Edit Pengumuman
            </span>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#172B4D] mt-2">Perbarui Pengumuman</h1>
            <p class="text-xs text-slate-500 mt-0.5">Ubah rincian, kategori, tanggal atau perbarui gambar poster pengumuman.</p>
        </div>

        <form action="{{ route('pengumuman.update', $pengumuman->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Judul Pengumuman <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul', $pengumuman->judul) }}" required
                       class="w-full text-xs font-medium border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <select name="kategori" required class="w-full text-xs font-medium border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1456A0] outline-none bg-white">
                        <option value="Informasi" {{ old('kategori', $pengumuman->kategori) == 'Informasi' ? 'selected' : '' }}>Informasi Umum</option>
                        <option value="Penting" {{ old('kategori', $pengumuman->kategori) == 'Penting' ? 'selected' : '' }}>Penting / Darurat</option>
                        <option value="Kegiatan" {{ old('kategori', $pengumuman->kategori) == 'Kegiatan' ? 'selected' : '' }}>Kegiatan Warga</option>
                        <option value="Posyandu" {{ old('kategori', $pengumuman->kategori) == 'Posyandu' ? 'selected' : '' }}>Posyandu & Kesehatan</option>
                        <option value="Keamanan" {{ old('kategori', $pengumuman->kategori) == 'Keamanan' ? 'selected' : '' }}>Keamanan & Ketertiban</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Berlaku / Agenda</label>
                    <input type="date" name="tanggal_aktif" value="{{ old('tanggal_aktif', $pengumuman->tanggal_aktif ? \Carbon\Carbon::parse($pengumuman->tanggal_aktif)->format('Y-m-d') : '') }}"
                           class="w-full text-xs font-medium border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1456A0] outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Upload Foto / Poster Baru (Opsional)</label>
                @if($pengumuman->gambar_url)
                    <div class="mb-3 flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <img src="{{ $pengumuman->gambar_url }}" alt="Poster saat ini" class="w-16 h-16 object-cover rounded-lg border">
                        <div class="text-xs">
                            <span class="font-bold text-slate-700 block">Poster Saat Ini Tersimpan</span>
                            <span class="text-slate-400 text-[11px]">Pilih file baru di bawah jika ingin mengganti poster ini.</span>
                        </div>
                    </div>
                @endif
                <input type="file" name="gambar" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#1456A0] hover:file:bg-blue-100 border border-slate-300 rounded-xl p-1 bg-white">
                <p class="text-[11px] text-slate-400 mt-1">Format JPG, PNG, WEBP (Maksimal 3 MB).</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Isi Rincian Pengumuman <span class="text-rose-500">*</span></label>
                <textarea name="isi" rows="8" required
                          class="w-full text-xs font-medium border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1456A0] outline-none leading-relaxed">{{ old('isi', $pengumuman->isi) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <a href="{{ route('pengumuman.show', $pengumuman->id) }}" class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white rounded-xl shadow-md transition flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
