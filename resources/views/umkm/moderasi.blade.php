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

    {{-- Breadcrumbs & Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <span>&rsaquo;</span>
                <a href="{{ route('umkm.index') }}" class="hover:text-blue-600 transition-colors">UMKM</a>
                <span>&rsaquo;</span>
                <span class="text-blue-600 font-bold">Pengawasan & Verifikasi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i data-lucide="shield-check" class="w-7 h-7 text-blue-600"></i>
                <span>Pengawasan & Verifikasi UMKM</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                @if(auth()->user()->role === 'rt')
                    Wilayah Kewenangan: <strong>Ketua {{ auth()->user()->clean_rt_label }}</strong> &bull; Verifikasi awal & rekomendasi UMKM RT Anda.
                @else
                    Wilayah Kewenangan: <strong>Pengurus RW 013</strong> &bull; Pengawasan sentral RT 001 s/d RT 008, penetapan verifikasi & moderasi publikasi.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('umkm.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition-colors">
                <i data-lucide="shopping-bag" class="w-4 h-4 text-blue-600"></i>
                <span>Katalog Marketplace Publik</span>
            </a>
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

    {{-- Banner Aturan Pengawasan & Otorisasi --}}
    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex items-start gap-3">
        <i data-lucide="info" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5"></i>
        <div class="space-y-0.5">
            <strong class="font-bold text-blue-950">Prinsip Moderasi Pengurus Warkas Machi:</strong>
            <p class="mb-0 text-blue-800">
                Pengurus RT/RW memiliki wewenang untuk <strong>menyetujui, meminta revisi, menolak, atau menonaktifkan status publikasi</strong> usaha demi kepatuhan kependudukan dan ketertiban lingkungan. Pengurus <strong>TIDAK BERHAK</strong> mengubah harga menu, foto, atau isi produk milik warga.
            </p>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('umkm.moderasi') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            {{-- Search input --}}
            <div class="lg:col-span-2">
                <label class="block font-bold text-slate-700 mb-1">Cari Usaha / Pemilik</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama usaha atau pemilik..." class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 outline-none focus:bg-white focus:border-blue-600">
            </div>

            {{-- Filter RT (hanya tampil jika role RW/admin) --}}
            @if(in_array(auth()->user()->role, ['rw', 'admin', 'super_admin']))
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Wilayah RT</label>
                    <select name="rt" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 outline-none focus:bg-white focus:border-blue-600">
                        <option value="Semua">Semua RT (001 - 008)</option>
                        @for($i = 1; $i <= 8; $i++)
                            @php $rtP = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $rtP }}" {{ request('rt') == $rtP ? 'selected' : '' }}>RT {{ $rtP }}</option>
                        @endfor
                    </select>
                </div>
            @endif

            {{-- Filter Status Verifikasi --}}
            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Verifikasi</label>
                <select name="status_verifikasi" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 outline-none focus:bg-white focus:border-blue-600">
                    <option value="Semua">Semua Status</option>
                    <option value="menunggu" {{ request('status_verifikasi') == 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="diverifikasi" {{ request('status_verifikasi') == 'diverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="perlu_perbaikan" {{ request('status_verifikasi') == 'perlu_perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                    <option value="ditolak" {{ request('status_verifikasi') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dinonaktifkan" {{ request('status_verifikasi') == 'dinonaktifkan' ? 'selected' : '' }}>Dinonaktifkan</option>
                </select>
            </div>

            {{-- Filter Status Kepemilikan --}}
            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Akun Pemilik</label>
                <select name="status_kepemilikan" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 outline-none focus:bg-white focus:border-blue-600">
                    <option value="Semua">Semua Kepemilikan</option>
                    <option value="terhubung" {{ request('status_kepemilikan') == 'terhubung' ? 'selected' : '' }}>Akun Terhubung</option>
                    <option value="belum_terhubung" {{ request('status_kepemilikan') == 'belum_terhubung' ? 'selected' : '' }}>Belum Terhubung</option>
                </select>
            </div>

            <div class="col-span-full flex items-center justify-end gap-2 pt-1 border-t border-slate-100">
                <a href="{{ route('umkm.moderasi') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition-colors">Reset Filter</a>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors">Terapkan Filter</button>
            </div>
        </form>
    </div>

    {{-- Tabel Pemeriksaan & Moderasi UMKM --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 mb-0">Daftar Pengawasan UMKM</h2>
                <span class="text-xs text-slate-500">Menampilkan {{ $umkms->count() }} usaha dalam wilayah pengawasan Anda</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Nama Usaha & Kategori</th>
                        <th class="py-3.5 px-4">Wilayah & Alamat</th>
                        <th class="py-3.5 px-4">Pemilik & Akun</th>
                        <th class="py-3.5 px-4">Status Verifikasi</th>
                        <th class="py-3.5 px-4">Publikasi</th>
                        <th class="py-3.5 px-4 text-center">Tindakan Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($umkms as $u)
                        @php
                            $vBadge = $u->status_verifikasi_badge;
                            $isOwnerLinked = !is_null($u->user_id);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Nama Usaha & Kategori --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $u->foto_url }}" alt="{{ $u->nama_umkm }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0" onerror="this.onerror=null; this.src='{{ $u->category_placeholder_image }}';">
                                    <div>
                                        <strong class="font-bold text-slate-900 text-sm block">{{ $u->nama_umkm }}</strong>
                                        <span class="text-[11px] text-slate-500">{{ $u->kategori }} &bull; {{ $u->produks->count() }} Menu</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Wilayah & Alamat --}}
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md font-bold text-[10px] bg-blue-50 text-blue-700 block w-max mb-1">
                                    {{ $u->clean_rt_label }}
                                </span>
                                <span class="text-slate-600 line-clamp-1 max-w-[200px]" title="{{ $u->alamat }}">{{ $u->alamat }}</span>
                            </td>

                            {{-- Pemilik & Akun --}}
                            <td class="py-3.5 px-4">
                                @if($isOwnerLinked)
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        <div>
                                            <strong class="text-slate-900 block font-semibold">{{ $u->owner_name }}</strong>
                                            <span class="text-[11px] text-slate-400 font-mono">{{ $u->no_hp }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            <i data-lucide="help-circle" class="w-3 h-3"></i> Belum Terhubung
                                        </span>
                                        <span class="text-[11px] text-slate-500 block">{{ $u->nama_pemilik }}</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Status Verifikasi --}}
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold"
                                      style="background: {{ $vBadge['bg'] }}; color: {{ $vBadge['text'] }}; border: 1px solid {{ $vBadge['border'] }};">
                                    <i data-lucide="{{ $vBadge['icon'] }}" class="w-3.5 h-3.5"></i>
                                    <span>{{ $vBadge['label'] }}</span>
                                </span>
                                @if($u->catatan_verifikasi)
                                    <p class="text-[10px] text-slate-500 line-clamp-1 mt-1 mb-0" title="{{ $u->catatan_verifikasi }}">Catatan: {{ $u->catatan_verifikasi }}</p>
                                @endif
                            </td>

                            {{-- Status Publikasi --}}
                            <td class="py-3.5 px-4">
                                @if($u->is_active && $u->status_verifikasi === 'diverifikasi')
                                    <span class="text-emerald-600 font-bold flex items-center gap-1">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Tayang
                                    </span>
                                @else
                                    <span class="text-slate-400 font-semibold flex items-center gap-1">
                                        <i data-lucide="eye-off" class="w-3.5 h-3.5"></i> Disembunyikan
                                    </span>
                                @endif
                            </td>

                            {{-- Tindakan Moderasi --}}
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Buka Modal Keputusan Moderasi --}}
                                    <button type="button" 
                                            onclick="openModerasiModal({{ json_encode($u) }})"
                                            class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white font-bold text-xs transition-colors">
                                        <i data-lucide="sliders" class="w-3.5 h-3.5 inline mr-0.5"></i> Moderasi
                                    </button>

                                    {{-- Jika belum terhubung dan user RW --}}
                                    @if(!$isOwnerLinked && in_array(auth()->user()->role, ['rw', 'admin', 'super_admin']))
                                        <button type="button" 
                                                onclick="openTautkanModal({{ $u->id }}, '{{ $u->nama_umkm }}', '{{ $u->nama_pemilik }}')"
                                                class="px-2.5 py-1.5 rounded-xl bg-amber-50 text-amber-800 hover:bg-amber-600 hover:text-white font-bold text-xs transition-colors"
                                                title="Hubungkan Akun Pemilik">
                                            <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                Tidak ada data UMKM yang cocok dengan filter pengawasan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($umkms->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $umkms->links() }}
            </div>
        @endif
    </div>

    {{-- ======================================================================
         AUDIT TRAIL MODERASI & PENGAWASAN TERKINI
         ====================================================================== --}}
    <div class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-sm space-y-3">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <i data-lucide="history" class="w-4 h-4 text-blue-600"></i>
            <span>Log Audit Pengawasan & Moderasi Wilayah (Append-Only)</span>
        </h3>

        <div class="divide-y divide-slate-100 text-xs">
            @forelse($recentAudits as $log)
                <div class="py-2.5 flex items-start justify-between gap-3">
                    <div class="flex items-start gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600 mt-1.5 shrink-0"></span>
                        <div>
                            <strong class="text-slate-800 block font-semibold">{{ $log->keterangan }}</strong>
                            <span class="text-[11px] text-slate-400">
                                Dicatat oleh: <strong>{{ $log->user_name }}</strong> ({{ strtoupper($log->role_saat_itu) }}) &bull; Usaha: {{ $log->umkm?->nama_umkm }}
                            </span>
                        </div>
                    </div>
                    <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-4 mb-0">Belum ada riwayat aktivitas moderasi yang tercatat.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- Modal Dialog Moderasi (Keputusan Verifikasi / Status) --}}
<div class="modal fade" id="modalModerasiAksi" tabindex="-1" aria-labelledby="modalModerasiAksiLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            <div class="modal-header bg-slate-900 text-white p-4 border-0">
                <div>
                    <h5 class="modal-title font-bold text-base text-white" id="modalModerasiAksiLabel">Keputusan Moderasi Pengurus</h5>
                    <p class="text-xs text-slate-300 mb-0" id="moderasiNamaUmkmSub">-</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formModerasiAction" action="" method="POST">
                @csrf
                <div class="modal-body p-5 space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Tindakan Keputusan Pengurus <span class="text-rose-500">*</span></label>
                        <select name="action" id="moderasiActionSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold focus:border-blue-600 outline-none">
                            <option value="diverifikasi">✅ Setujui & Terbitkan Verifikasi Resmi</option>
                            <option value="perlu_perbaikan">⚠️ Minta Perbaikan (Kirimkan Catatan Revisi ke Pemilik)</option>
                            <option value="ditolak">❌ Tolak Pengajuan Verifikasi</option>
                            <option value="dinonaktifkan">⏸️ Nonaktifkan Sementara Publikasi (Pelanggaran/Klarifikasi)</option>
                            <option value="aktifkan_kembali">🔄 Aktifkan Kembali Publikasi Usaha</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Catatan / Alasan Keputusan Pengurus <span class="text-rose-500">*</span></label>
                        <textarea name="catatan" rows="3" required placeholder="Jelaskan alasan persetujuan, detail data yang perlu diperbaiki pemilik, atau alasan penonaktifan..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-600"></textarea>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 text-[11px] space-y-0.5">
                        <strong class="font-bold text-slate-700 block">Jejak Audit:</strong>
                        <span>Keputusan ini akan otomatis tercatat secara permanen atas nama akun Anda untuk transparansi pengawasan RW 013.</span>
                    </div>
                </div>

                <div class="modal-footer bg-slate-50 border-t border-slate-200 px-5 py-3 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 text-xs font-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 shadow-sm">Simpan Keputusan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Hubungkan Akun Pemilik (Khusus Admin RW) --}}
@if(in_array(auth()->user()->role, ['rw', 'admin', 'super_admin']))
<div class="modal fade" id="modalTautkanAkun" tabindex="-1" aria-labelledby="modalTautkanAkunLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            <div class="modal-header bg-amber-600 text-white p-4 border-0">
                <h5 class="modal-title font-bold text-base text-white" id="modalTautkanAkunLabel">Hubungkan Akun Pemilik UMKM</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formTautkanAction" action="" method="POST">
                @csrf
                <div class="modal-body p-5 space-y-3 text-xs">
                    <p class="text-slate-600 mb-2">
                        Pilih akun warga terdaftar yang merupakan pemilik sah dari usaha <strong id="tautkanNamaUmkm">-</strong>:
                    </p>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Pilih Akun Warga RW 013 <span class="text-rose-500">*</span></label>
                        <select name="user_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs outline-none focus:border-amber-600">
                            <option value="">-- Cari Nama Warga --</option>
                            @foreach($wargaUsers as $wu)
                                <option value="{{ $wu->id }}">{{ $wu->name }} ({{ $wu->clean_rt_label }} - {{ $wu->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Dasar / Catatan Penetapan Kepemilikan <span class="text-rose-500">*</span></label>
                        <textarea name="alasan" rows="2" required placeholder="Contoh: Konfirmasi langsung warga yang bersangkutan / verifikasi KTP pengurus RT..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-amber-600"></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-slate-50 border-t border-slate-200 px-5 py-3 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 text-xs font-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 text-white font-bold text-xs hover:bg-amber-700 shadow-sm">Tetapkan Pemilik Sah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    function openModerasiModal(u) {
        document.getElementById('formModerasiAction').action = `/umkm/${u.id}/moderasi`;
        document.getElementById('moderasiNamaUmkmSub').textContent = `${u.nama_umkm} (Pemilik: ${u.nama_pemilik})`;
        
        const modalEl = document.getElementById('modalModerasiAksi');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    function openTautkanModal(id, namaUmkm, pemilik) {
        document.getElementById('formTautkanAction').action = `/umkm/${id}/hubungkan-pemilik`;
        document.getElementById('tautkanNamaUmkm').textContent = `${namaUmkm} (${pemilik})`;

        const modalEl = document.getElementById('modalTautkanAkun');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }
</script>
@endsection
