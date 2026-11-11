@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: { preflight: false },
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

<div class="w-full max-w-[1600px] mx-auto px-2 sm:px-4 py-2 space-y-6">

    {{-- Breadcrumbs & Top Navigation --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <span>&rsaquo;</span>
                <a href="{{ route('umkm.index') }}" class="hover:text-blue-600 transition-colors">UMKM RW 013</a>
                <span>&rsaquo;</span>
                <span class="text-blue-600 font-bold">Kelola UMKM Saya</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i data-lucide="store" class="w-7 h-7 text-blue-600"></i>
                <span>Kelola UMKM Saya</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Pusat kendali informasi usaha, daftar menu, harga, dan operasional bisnis mandiri Anda di RW 013.
            </p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('umkm.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Katalog Publik</span>
            </a>

            <button type="button" onclick="openDaftarModal()" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs inline-flex items-center gap-2 shadow-sm transition-colors">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Usaha Baru</span>
            </button>
        </div>
    </div>

    {{-- Alert Berhasil / Gagal --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" class="text-emerald-500 hover:text-emerald-700" onclick="this.parentElement.remove()">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    {{-- Ringkasan Statistik Usaha Saya (4 Cards) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        {{-- Card 1 --}}
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="store" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[11px] uppercase font-bold text-slate-400 block">Total Usaha</span>
                <span class="text-xl sm:text-2xl font-black text-slate-900">{{ $totalUsaha }}</span>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[11px] uppercase font-bold text-slate-400 block">Terverifikasi</span>
                <span class="text-xl sm:text-2xl font-black text-emerald-600">{{ $terverifikasiCount }}</span>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[11px] uppercase font-bold text-slate-400 block">Menunggu Verifikasi</span>
                <span class="text-xl sm:text-2xl font-black text-amber-600">{{ $menungguCount }}</span>
            </div>
        </div>

        {{-- Card 4 --}}
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[11px] uppercase font-bold text-slate-400 block">Perlu Perbaikan</span>
                <span class="text-xl sm:text-2xl font-black text-rose-600">{{ $perluPerbaikanCount }}</span>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         DAFTAR USAHA MILIK SAYA
         ====================================================================== --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <span>Daftar Usaha Terdaftar</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">{{ $totalUsaha }}</span>
            </h2>
            <span class="text-xs text-slate-500">Prinsip Hak Akses: Hanya Anda sebagai pemilik sah yang dapat mengubah informasi usaha dan harga menu.</span>
        </div>

        @forelse($myUmkms as $u)
            @php
                $vBadge = $u->status_verifikasi_badge;
            @endphp
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-5 sm:p-6 space-y-4 transition-all hover:shadow-md">
                
                {{-- Banner Revisi / Notifikasi Pengurus jika status Perlu Perbaikan atau Dinonaktifkan --}}
                @if($u->status_verifikasi === 'perlu_perbaikan' && $u->catatan_verifikasi)
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs space-y-1">
                        <strong class="font-bold flex items-center gap-1.5 text-amber-800">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                            Catatan Revisi dari Pengurus RT/RW:
                        </strong>
                        <p class="mb-0 text-amber-950 font-medium pl-5.5">{{ $u->catatan_verifikasi }}</p>
                        <p class="mb-0 text-[11px] text-amber-700 pl-5.5">Silakan perbarui data usaha Anda menggunakan tombol "Edit Profil Usaha" di bawah untuk diajukan ulang.</p>
                    </div>
                @elseif($u->status_verifikasi === 'dinonaktifkan' && $u->alasan_moderasi)
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs space-y-1">
                        <strong class="font-bold flex items-center gap-1.5 text-rose-800">
                            <i data-lucide="shield-alert" class="w-4 h-4 text-rose-600"></i>
                            Alasan Moderasi Penonaktifan Usaha:
                        </strong>
                        <p class="mb-0 text-rose-950 font-medium pl-5.5">{{ $u->alasan_moderasi }}</p>
                        <p class="mb-0 text-[11px] text-rose-700 pl-5.5">Hubungi Pengurus RT {{ $u->clean_rt_label }} atau Sekretariat RW 013 untuk klarifikasi dan pengaktifan kembali.</p>
                    </div>
                @endif

                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
                    {{-- Usaha Info & Image --}}
                    <div class="flex items-start gap-4">
                        <img src="{{ $u->foto_url }}" 
                             alt="{{ $u->nama_umkm }}" 
                             class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover shrink-0 border border-slate-100 shadow-sm"
                             onerror="this.onerror=null; this.src='{{ $u->category_placeholder_image }}';">

                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-0">
                                    {{ $u->nama_umkm }}
                                </h3>

                                {{-- Verification Badge --}}
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold"
                                      style="background: {{ $vBadge['bg'] }}; color: {{ $vBadge['text'] }}; border: 1px solid {{ $vBadge['border'] }};">
                                    <i data-lucide="{{ $vBadge['icon'] }}" class="w-3.5 h-3.5"></i>
                                    <span>{{ $vBadge['label'] }}</span>
                                </span>

                                {{-- Status Buka / Tutup --}}
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $u->is_buka_sekarang ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $u->is_buka_sekarang ? '● Buka' : 'Tutup' }}
                                </span>

                                <span class="px-2 py-0.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700">
                                    {{ $u->clean_rt_label }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-500 mb-0 flex items-center gap-1.5">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-rose-500 shrink-0"></i>
                                <span>{{ $u->alamat }}, Kelurahan Sukamiskin, Arcamanik</span>
                            </p>

                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600 pt-1">
                                <span><strong>Kategori:</strong> {{ $u->kategori }} ({{ $u->jenis_usaha }})</span>
                                <span>&bull;</span>
                                <span><strong>Menu Terdaftar:</strong> {{ $u->produks->count() }} Produk</span>
                                <span>&bull;</span>
                                <span><strong>Jam Buka:</strong> {{ $u->jam_buka }} - {{ $u->jam_tutup }} WIB</span>
                                <span>&bull;</span>
                                <span><strong>WhatsApp:</strong> {{ $u->no_hp }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Menu / Edit Buttons --}}
                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full lg:w-auto shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                        <a href="{{ route('umkm.menu', $u->id) }}" class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs inline-flex items-center justify-center gap-1.5 shadow-sm transition-colors">
                            <i data-lucide="utensils" class="w-4 h-4"></i>
                            <span>Kelola Menu ({{ $u->produks->count() }})</span>
                        </a>

                        <a href="{{ route('umkm.edit', $u->id) }}" class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs inline-flex items-center justify-center gap-1.5 transition-colors">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                            <span>Edit Profil Usaha</span>
                        </a>

                        <form action="{{ route('umkm.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus usaha {{ $u->nama_umkm }}? Data menu dan riwayat usaha akan dihapus permanen.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Usaha">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Preview 4 Menu Teratas --}}
                @if($u->produks->count() > 0)
                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-[11px] font-bold uppercase text-slate-400 block mb-2">Daftar Menu Aktif:</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach($u->produks->take(4) as $p)
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs flex flex-col justify-between">
                                    <div>
                                        <strong class="text-slate-800 line-clamp-1 block">{{ $p->nama_produk }}</strong>
                                        <span class="text-[10px] text-slate-400">{{ $p->is_best_seller ? '★ Best Seller' : $p->kategori_produk }}</span>
                                    </div>
                                    <div class="mt-1 flex items-center justify-between">
                                        <span class="font-bold text-emerald-600">{{ $p->harga_rupiah }}</span>
                                        <span class="text-[10px] {{ $p->is_tersedia ? 'text-emerald-700' : 'text-slate-400' }}">{{ $p->is_tersedia ? 'Tersedia' : 'Habis' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-dashed border-slate-300">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="store" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">Anda Belum Memiliki Usaha Terdaftar</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto mb-5">
                    Daftarkan usaha kuliner, warung, toko, atau jasa Anda agar dapat dipromosikan ke seluruh warga RW 013 Sukamiskin.
                </p>
                <button type="button" onclick="openDaftarModal()" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 shadow-sm inline-flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Daftarkan Usaha Pertama Saya</span>
                </button>
            </div>
        @endforelse
    </div>

    {{-- ======================================================================
         RIWAYAT AKTIVITAS & AUDIT LOG USAHA SAYA
         ====================================================================== --}}
    @if($recentActivities->count() > 0)
        <div class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-sm space-y-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-blue-600"></i>
                <span>Riwayat Aktivitas & Perubahan Usaha Saya</span>
            </h3>

            <div class="divide-y divide-slate-100 text-xs">
                @foreach($recentActivities as $act)
                    <div class="py-2.5 flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-blue-600 mt-1.5 shrink-0"></span>
                            <div>
                                <span class="font-bold text-slate-800">{{ $act->keterangan }}</span>
                                <span class="text-slate-400 block text-[11px]">{{ $act->user_name }} ({{ $act->role_saat_itu }})</span>
                            </div>
                        </div>
                        <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ $act->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

{{-- Modal Pendaftaran Usaha --}}
@include('umkm.partials.modal_daftar')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    function openDaftarModal() {
        const modalEl = document.getElementById('modalDaftarUmkm');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }
</script>
@endsection
