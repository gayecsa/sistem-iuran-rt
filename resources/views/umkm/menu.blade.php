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

<div class="w-full max-w-[1400px] mx-auto px-2 sm:px-4 py-2 space-y-6">

    {{-- Breadcrumbs & Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <span>&rsaquo;</span>
                <a href="{{ route('umkm.saya') }}" class="hover:text-blue-600 transition-colors">Kelola UMKM Saya</a>
                <span>&rsaquo;</span>
                <span class="text-blue-600 font-bold">Kelola Menu & Produk</span>
            </div>

            <div class="flex items-center gap-3">
                <img src="{{ $umkm->foto_url }}" alt="{{ $umkm->nama_umkm }}" class="w-14 h-14 rounded-2xl object-cover border border-slate-200 shadow-sm" onerror="this.onerror=null; this.src='{{ $umkm->category_placeholder_image }}';">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>{{ $umkm->nama_umkm }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">{{ $umkm->clean_rt_label }}</span>
                    </h1>
                    <p class="text-xs text-slate-500 mb-0">
                        Atur daftar menu, update harga, foto produk, ketersediaan, dan rekomendasi menu best seller.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('umkm.saya') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Usaha Saya</span>
            </a>

            <button type="button" onclick="openTambahProdukModal()" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs inline-flex items-center gap-2 shadow-sm transition-colors">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Menu Baru</span>
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

    {{-- Daftar Menu Cards / Table --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 mb-0">Daftar Menu & Produk Terdaftar</h2>
                <span class="text-xs text-slate-500">Total {{ $umkm->produks->count() }} item produk pada katalog online</span>
            </div>
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200">
                Rentang: {{ $umkm->kisaran_harga_formatted }}
            </span>
        </div>

        @if($umkm->produks->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
                @foreach($umkm->produks as $p)
                    <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50/50 hover:bg-white transition-all hover:shadow-md flex flex-col justify-between">
                        <div>
                            @if($p->foto_url)
                                <img src="{{ $p->foto_url }}" alt="{{ $p->nama_produk }}" class="w-full h-36 rounded-xl object-cover mb-3" onerror="this.style.display='none';">
                            @endif

                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                <h3 class="font-bold text-slate-900 text-base leading-snug">{{ $p->nama_produk }}</h3>
                                @if($p->is_best_seller)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-800 shrink-0">★ BEST SELLER</span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-500 line-clamp-2 mb-3">
                                {{ $p->deskripsi ?: 'Tidak ada deskripsi produk.' }}
                            </p>
                        </div>

                        <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Harga</span>
                                <span class="text-base font-black text-emerald-600">{{ $p->harga_rupiah }}</span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button type="button" 
                                        onclick="openEditProdukModal({{ json_encode($p) }})"
                                        class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors"
                                        title="Edit Menu">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>

                                <form action="{{ route('umkm.produk.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus menu {{ $p->nama_produk }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Menu">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="utensils" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-1">Belum Ada Menu atau Produk</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">Tambahkan menu pertama Anda agar warga dapat melihat dan memesan langsung via WhatsApp.</p>
                <button type="button" onclick="openTambahProdukModal()" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 shadow-sm inline-flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Tambah Menu Baru</span>
                </button>
            </div>
        @endif
    </div>

</div>

{{-- Modal Tambah Produk --}}
<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            <div class="modal-header bg-blue-600 text-white p-4 border-0">
                <h5 class="modal-title font-bold text-base text-white" id="modalTambahProdukLabel">Tambah Menu / Produk Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('umkm.produk.store', $umkm->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-5 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Menu / Produk <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_produk" required placeholder="Contoh: Nasi Timbel Komplit" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga" required placeholder="25000" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Produk</label>
                        <input type="text" name="kategori_produk" value="{{ $umkm->kategori }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Produk (Komposisi / Porsi)</label>
                        <textarea name="deskripsi" rows="2" placeholder="Penjelasan menu..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Foto Menu (Opsional)</label>
                        <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="is_best_seller" value="1" class="rounded text-blue-600">
                            <span>Tandai Menu Best Seller</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="is_tersedia" value="1" checked class="rounded text-blue-600">
                            <span>Status Tersedia</span>
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

{{-- Modal Edit Produk --}}
<div class="modal fade" id="modalEditProduk" tabindex="-1" aria-labelledby="modalEditProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            <div class="modal-header bg-slate-800 text-white p-4 border-0">
                <h5 class="modal-title font-bold text-base text-white" id="modalEditProdukLabel">Edit Menu / Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditProdukAction" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-5 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Menu / Produk <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_produk" id="editNamaProduk" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga" id="editHargaProduk" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Produk</label>
                        <input type="text" name="kategori_produk" id="editKategoriProduk" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Produk</label>
                        <textarea name="deskripsi" id="editDeskripsiProduk" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:border-blue-600"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ganti Foto Menu (Opsional)</label>
                        <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="is_best_seller" id="editBestSeller" value="1" class="rounded text-blue-600">
                            <span>Tandai Menu Best Seller</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="is_tersedia" id="editTersedia" value="1" class="rounded text-blue-600">
                            <span>Status Tersedia</span>
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-slate-50 border-t border-slate-200 px-5 py-3 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 text-xs font-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    function openTambahProdukModal() {
        const modalEl = document.getElementById('modalTambahProduk');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    function openEditProdukModal(p) {
        document.getElementById('formEditProdukAction').action = `/umkm/produk/${p.id}`;
        document.getElementById('editNamaProduk').value = p.nama_produk;
        document.getElementById('editHargaProduk').value = p.harga;
        document.getElementById('editKategoriProduk').value = p.kategori_produk || '';
        document.getElementById('editDeskripsiProduk').value = p.deskripsi || '';
        document.getElementById('editBestSeller').checked = Boolean(p.is_best_seller);
        document.getElementById('editTersedia').checked = Boolean(p.is_tersedia);

        const modalEl = document.getElementById('modalEditProduk');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }
</script>
@endsection
