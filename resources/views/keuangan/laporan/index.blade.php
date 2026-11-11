@extends('layouts.app')

@section('title', 'Laporan Keuangan Terpadu - Warkas Machi RW 013')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Laporan Perbendaharaan & Keuangan</h1>
            <p class="text-xs text-slate-500 mt-1">
                Laporan komprehensif penerimaan, belanja operasional, dan surplus/defisit RW 013 Sukamiskin
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('keuangan.laporan.cetak', request()->all()) }}" target="_blank" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white shadow inline-flex items-center gap-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan Resmi Kop RW
            </a>
        </div>
    </div>

    <!-- Filter Laporan -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('keuangan.laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Periode Waktu</label>
                <select name="tipe_laporan" id="tipe_laporan" class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="bulanan" {{ $filterTipe === 'bulanan' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="harian" {{ $filterTipe === 'harian' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="mingguan" {{ $filterTipe === 'mingguan' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="tahunan" {{ $filterTipe === 'tahunan' ? 'selected' : '' }}>Tahun Berjalan</option>
                    <option value="kustom" {{ $filterTipe === 'kustom' ? 'selected' : '' }}>Rentang Tanggal Kustom</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Buku Kas</label>
                <select name="buku_kas_id" class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="">Semua Buku Kas</option>
                    @foreach($bukuKasList as $b)
                        <option value="{{ $b->id }}" {{ $bukuKasId == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_kas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-[#1456A0] text-white font-bold rounded-lg hover:bg-blue-800">
                    Filter Laporan
                </button>
                <a href="{{ route('keuangan.laporan.index') }}" class="py-2 px-3 bg-slate-100 text-slate-600 font-medium rounded-lg hover:bg-slate-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- 3 Card Ringkasan Arus Kas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Pemasukan</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </span>
            </div>
            <p class="text-2xl font-black text-emerald-600">
                +Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-slate-400">Dari iuran warga, sumbangan, & transfer masuk</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Pengeluaran</span>
                <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                    <i data-lucide="trending-down" class="w-4 h-4"></i>
                </span>
            </div>
            <p class="text-2xl font-black text-rose-600">
                -Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-slate-400">Belanja operasional, satpam, sampah, & ATK</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Surplus / Defisit Bersih</span>
                <span class="w-8 h-8 rounded-lg {{ $surplusDefisit >= 0 ? 'bg-blue-100 text-[#1456A0]' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center font-bold">
                    <i data-lucide="scale" class="w-4 h-4"></i>
                </span>
            </div>
            <p class="text-2xl font-black {{ $surplusDefisit >= 0 ? 'text-[#1456A0]' : 'text-amber-700' }}">
                Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-slate-400">
                {{ $surplusDefisit >= 0 ? 'Surplus kas operasional' : 'Defisit (Pengeluaran > Pemasukan)' }}
            </span>
        </div>
    </div>

    <!-- Breakdown Kategori & Mutasi -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Rincian per Kategori (5 Kolom) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                <i data-lucide="pie-chart" class="w-4 h-4 text-[#1456A0]"></i> Rincian Akun Berdasarkan Kategori
            </h2>

            <div class="space-y-3">
                @forelse($breakdownKategori as $kat)
                    <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-800">{{ $kat['kategori'] }}</span>
                            <p class="text-[11px] text-slate-400">{{ $kat['count'] }} transaksi</p>
                        </div>
                        <span class="font-extrabold {{ in_array($kat['jenis'], ['pemasukan', 'transfer_masuk']) ? 'text-emerald-700' : 'text-rose-600' }}">
                            {{ in_array($kat['jenis'], ['pemasukan', 'transfer_masuk']) ? '+' : '-' }}Rp {{ number_format($kat['total'], 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-slate-400 text-xs py-6">Belum ada transaksi di periode ini.</p>
                @endforelse
            </div>
        </div>

        <!-- Jurnal Terperinci untuk Cetak (7 Kolom) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                <i data-lucide="list" class="w-4 h-4 text-emerald-600"></i> Daftar Transaksi ({{ $transaksis->count() }})
            </h2>

            <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] sticky top-0">
                        <tr>
                            <th class="py-2.5 px-3">Tanggal</th>
                            <th class="py-2.5 px-3">Uraian / Kategori</th>
                            <th class="py-2.5 px-3 text-right">Debit</th>
                            <th class="py-2.5 px-3 text-right">Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-[11px]">
                        @forelse($transaksis as $t)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2 px-3 text-slate-500 whitespace-nowrap font-mono">
                                    {{ \Carbon\Carbon::parse($t->tanggal_transaksi)->translatedFormat('d/m/y') }}
                                </td>
                                <td class="py-2 px-3">
                                    <div class="font-bold text-slate-800">{{ $t->kategori }}</div>
                                    <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $t->keterangan }}</div>
                                </td>
                                <td class="py-2 px-3 text-right font-bold text-emerald-600 whitespace-nowrap">
                                    {{ in_array($t->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-2 px-3 text-right font-bold text-rose-600 whitespace-nowrap">
                                    {{ in_array($t->jenis_transaksi, ['pengeluaran', 'transfer_keluar']) ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    Tidak ada data transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
