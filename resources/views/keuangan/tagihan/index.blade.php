@extends('layouts.app')

@section('title', 'Manajemen Tagihan Iuran Warga - Warkas Machi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Manajemen Tagihan Iuran Warga</h1>
            <p class="text-xs text-slate-500 mt-1">
                Sistem tagihan iuran berbasis 1 Kartu Keluarga (KK) per bulan • Nominal standar Rp 45.000 / KK (RT + RW + Sampah)
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($isRw)
                <button onclick="document.getElementById('modal-generate-tagihan').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white shadow flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-300"></i> Generate Tagihan Baru
                </button>
            @endif
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

    <!-- 3 Banner Metrik Tagihan -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Target Tagihan</span>
            <p class="text-2xl font-black text-slate-900 mt-1">
                Rp {{ number_format($totalTagihanNominal, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-slate-400">Total kewajiban seluruh KK terfilter</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs text-emerald-600 font-semibold uppercase tracking-wider">Telah Terkumpul (Lunas)</span>
            <p class="text-2xl font-black text-emerald-600 mt-1">
                Rp {{ number_format($totalTerkumpulNominal, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-emerald-600/80">Telah masuk ke kas RT, RW & Sampah</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs text-rose-600 font-semibold uppercase tracking-wider">Sisa Piutang / Belum Terbayar</span>
            <p class="text-2xl font-black text-rose-600 mt-1">
                Rp {{ number_format($totalSisaNominal, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-rose-600/80">Menunggu pembayaran & verifikasi</span>
        </div>
    </div>

    <!-- Filter Bar Tagihan -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('tagihan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Periode Tagihan</label>
                <select name="periode" class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="">Semua Periode</option>
                    @foreach($daftarPeriode as $p)
                        <option value="{{ $p }}" {{ $periode === $p ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($p . '-01')->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($isRw)
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Wilayah RT</label>
                    <select name="rt" class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
                        <option value="">Semua RT (001 - 008)</option>
                        @for($i = 1; $i <= 8; $i++)
                            @php $rtLoop = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $rtLoop }}" {{ $rtFilter == $rtLoop ? 'selected' : '' }}>RT {{ $rtLoop }}</option>
                        @endfor
                    </select>
                </div>
            @endif

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Status Tagihan</label>
                <select name="status" class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="">Semua Status</option>
                    <option value="Belum Dibayar" {{ $status === 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="Menunggu Konfirmasi" {{ $status === 'Menunggu Konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                    <option value="Lunas" {{ $status === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="Terlambat" {{ $status === 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="Dibebaskan" {{ $status === 'Dibebaskan' ? 'selected' : '' }}>Dibebaskan</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Cari KK / Kepala Keluarga</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama kepala keluarga, KK, alamat..." 
                       class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-[#1456A0] text-white font-bold rounded-lg hover:bg-blue-800">
                    Filter
                </button>
                <a href="{{ route('tagihan.index') }}" class="py-2 px-3 bg-slate-100 text-slate-600 font-medium rounded-lg hover:bg-slate-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Tagihan Keluarga -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-3">No. Tagihan</th>
                        <th class="py-3 px-3">Kepala Keluarga</th>
                        <th class="py-3 px-3">RT & Alamat</th>
                        <th class="py-3 px-3">Periode</th>
                        <th class="py-3 px-3 text-right">Rincian (RT/RW/Smph)</th>
                        <th class="py-3 px-3 text-right">Total</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($tagihans as $tagihan)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 font-mono font-bold text-slate-800 whitespace-nowrap">
                                {{ $tagihan->nomor_tagihan }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-800">{{ $tagihan->kartuKeluarga->nama_kepala_keluarga ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">No. KK: {{ $tagihan->kartuKeluarga->masked_no_kk ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 mr-1">
                                    RT {{ $tagihan->clean_rt }}
                                </span>
                                <span>{{ $tagihan->kartuKeluarga->alamat ?? '-' }} No. {{ $tagihan->kartuKeluarga->nomor_rumah ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-700 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($tagihan->periode . '-01')->translatedFormat('M Y') }}
                            </td>
                            <td class="py-3 px-3 text-right text-[11px] text-slate-500 whitespace-nowrap font-mono">
                                20k / 10k / 15k
                            </td>
                            <td class="py-3 px-3 text-right font-extrabold text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-full border {{ $tagihan->badge_class }}">
                                    {{ $tagihan->status }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('tagihan.show', $tagihan->id) }}" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#1456A0] hover:bg-blue-100 border border-blue-200">
                                        Detail
                                    </a>
                                    @if($tagihan->status === 'Lunas')
                                        <a href="{{ route('tagihan.kuitansi', $tagihan->id) }}" target="_blank" class="p-1 text-slate-600 hover:text-slate-900 rounded" title="Cetak Kuitansi">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-400">
                                Tidak ada data tagihan yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tagihans->links() }}
        </div>
    </div>
</div>

@if($isRw)
<!-- Modal: Generate Tagihan Bulanan Otomatis -->
<div id="modal-generate-tagihan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl mx-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="sparkles" class="w-5 h-5 text-amber-500"></i> Generate Tagihan Bulanan KK
            </h3>
            <button onclick="document.getElementById('modal-generate-tagihan').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('tagihan.generate') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Periode Tagihan *</label>
                <input type="month" name="periode" value="{{ Carbon\Carbon::now()->format('Y-m') }}" required 
                       class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                <p class="text-[11px] text-slate-400 mt-1">Sistem akan memeriksa 68 KK dan secara idempoten hanya membuat tagihan bagi yang belum memiliki di periode ini.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Jatuh Tempo *</label>
                <input type="date" name="jatuh_tempo" value="{{ Carbon\Carbon::now()->addDays(20)->toDateString() }}" required 
                       class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-900 space-y-1">
                <p class="font-bold flex items-center gap-1.5"><i data-lucide="info" class="w-4 h-4 text-blue-700"></i> Komposisi Tagihan Tiap KK:</p>
                <div class="flex justify-between text-[11px]"><span>• Iuran Kas RT:</span> <strong>Rp 20.000</strong></div>
                <div class="flex justify-between text-[11px]"><span>• Iuran Kas RW:</span> <strong>Rp 10.000</strong></div>
                <div class="flex justify-between text-[11px]"><span>• Retribusi Sampah:</span> <strong>Rp 15.000</strong></div>
                <div class="flex justify-between text-[11px] font-bold border-t border-blue-200 pt-1">
                    <span>Total Tagihan / KK:</span> <strong>Rp 45.000</strong>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-generate-tagihan').classList.add('hidden')" 
                        class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white rounded-xl shadow">
                    Generate Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
