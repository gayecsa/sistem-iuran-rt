@extends('layouts.app')

@section('title', 'Pengaturan Tarif Iuran Warga - Warkas Machi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('keuangan.dashboard') }}" class="hover:underline">Dashboard Keuangan</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Pengaturan Tarif Iuran</span>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                    <i data-lucide="sliders" class="w-6 h-6 text-[#1456A0]"></i> Konfigurasi Tarif Iuran Bulanan Warga
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Aturan besaran iuran per Kartu Keluarga (KK). Setiap perubahan wajib mencantumkan dasar musyawarah warga.
                </p>
            </div>

            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-right">
                <span class="text-xs text-blue-700 font-medium">Total Iuran Standar / KK</span>
                <p class="text-2xl font-black text-[#1456A0] mt-0.5">
                    Rp {{ number_format($totalTarifKk, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">/ bulan</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi -->
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

    <!-- 3 Komponen Tarif Aktif -->
    <div class="space-y-4">
        @foreach($tarifs as $tarif)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded bg-slate-100 text-slate-700">
                                {{ $tarif->kode_tarif }}
                            </span>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $tarif->jenis_kas === 'kas_rw' ? 'bg-blue-100 text-blue-800' : ($tarif->jenis_kas === 'kas_rt' ? 'bg-emerald-100 text-emerald-800' : 'bg-teal-100 text-teal-800') }}">
                                {{ strtoupper(str_replace('_', ' ', $tarif->jenis_kas)) }}
                            </span>
                        </div>
                        <h2 class="text-base font-bold text-slate-800">{{ $tarif->nama_tarif }}</h2>
                        <p class="text-xs text-slate-500">Dasar musyawarah: {{ $tarif->keterangan_musyawarah ?: 'Kesepakatan Musyawarah RW 013' }}</p>
                        <p class="text-[11px] text-slate-400">Berlaku sejak: {{ \Carbon\Carbon::parse($tarif->berlaku_mulai)->translatedFormat('d F Y') }}</p>
                    </div>

                    <div class="text-right sm:self-center">
                        <span class="text-xs text-slate-400 font-semibold uppercase">Nominal Aktif</span>
                        <p class="text-2xl font-black text-slate-900 mt-0.5">
                            Rp {{ number_format($tarif->nominal, 0, ',', '.') }}
                        </p>
                        <button onclick="bukaModalUbahTarif('{{ $tarif->id }}', '{{ $tarif->nama_tarif }}', '{{ $tarif->nominal }}')" 
                                class="mt-2 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#1456A0] text-white hover:bg-blue-800 shadow inline-flex items-center gap-1.5">
                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i> Ubah Besaran Tarif
                        </button>
                    </div>
                </div>

                <!-- Riwayat Perubahan untuk Tarif Ini -->
                @if($tarif->histories->count() > 0)
                    <div class="mt-4 pt-3 border-t border-slate-100 text-xs">
                        <span class="font-bold text-slate-600 text-[11px] uppercase tracking-wider">Riwayat Perubahan:</span>
                        <div class="space-y-1 mt-1">
                            @foreach($tarif->histories->take(3) as $hist)
                                <div class="flex justify-between text-[11px] text-slate-500 py-0.5">
                                    <span>
                                        {{ \Carbon\Carbon::parse($hist->tanggal_perubahan)->translatedFormat('d/m/Y') }}: 
                                        Rp {{ number_format($hist->nominal_lama, 0, ',', '.') }} → <strong>Rp {{ number_format($hist->nominal_baru, 0, ',', '.') }}</strong>
                                        ({{ $hist->alasan_perubahan }})
                                    </span>
                                    <span class="text-slate-400">Oleh: {{ $hist->diubahOleh->name ?? 'Admin' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<!-- Modal Ubah Besaran Tarif -->
<div id="modal-ubah-tarif" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl mx-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="sliders" class="w-5 h-5 text-[#1456A0]"></i> Perbarui Besaran Tarif Iuran
            </h3>
            <button onclick="document.getElementById('modal-ubah-tarif').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="form-ubah-tarif" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Tarif Komponen</label>
                <input type="text" id="modal-nama-tarif" readonly class="w-full text-xs font-bold bg-slate-100 border border-slate-300 rounded-xl px-3 py-2.5 text-slate-700 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Baru (Rp) *</label>
                <input type="number" name="nominal" id="modal-nominal-tarif" required min="0" step="1000" 
                       class="w-full text-xs font-black text-slate-900 border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Dasar / Catatan Musyawarah Warga (Wajib) *</label>
                <textarea name="alasan_perubahan" required rows="3" placeholder="Contoh: Berdasarkan Berita Acara Rapat Pleno RT/RW tanggal 10 November 2026..." 
                          class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none"></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Perubahan ini hanya berlaku untuk periode penagihan berikutnya dan tidak mengubah tagihan historis yang sudah terbit.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-ubah-tarif').classList.add('hidden')" 
                        class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white rounded-xl shadow">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModalUbahTarif(id, nama, nominal) {
    document.getElementById('form-ubah-tarif').action = '/keuangan/pengaturan-iuran/' + id;
    document.getElementById('modal-nama-tarif').value = nama;
    document.getElementById('modal-nominal-tarif').value = nominal;
    document.getElementById('modal-ubah-tarif').classList.remove('hidden');
}
</script>
@endsection
