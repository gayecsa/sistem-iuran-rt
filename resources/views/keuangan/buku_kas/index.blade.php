@extends('layouts.app')

@section('title', 'Daftar Buku Kas - Warkas Machi RW 013')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Buku Kas & Rekening Keuangan</h1>
            <p class="text-xs text-slate-500 mt-1">
                Pemisahan perbendaharaan transparan: Kas RW, Kas RT 001 s/d RT 008, Kas Sampah, dan Dana Kegiatan RW 013
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('keuangan.transaksi.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#1456A0] text-white hover:bg-blue-800 shadow flex items-center gap-1.5">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Catat Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Filter Tab Jenis Kas -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('keuangan.buku_kas.index') }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ !request('jenis') ? 'bg-[#1456A0] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Semua Kas ({{ $bukuKas->count() }})
        </a>
        <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'kas_rw']) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ request('jenis') === 'kas_rw' ? 'bg-[#1456A0] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Kas Induk RW
        </a>
        <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'kas_rt']) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ request('jenis') === 'kas_rt' ? 'bg-[#1456A0] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Kas RT (001 - 008)
        </a>
        <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'kas_sampah']) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ request('jenis') === 'kas_sampah' ? 'bg-[#1456A0] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Kas Sampah & Kebersihan
        </a>
        @if($isRw)
            <a href="{{ route('keuangan.buku_kas.index', ['jenis' => 'dana_kegiatan']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ request('jenis') === 'dana_kegiatan' ? 'bg-[#1456A0] text-white shadow' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Dana Kegiatan Khusus
            </a>
        @endif
    </div>

    <!-- Grid Buku Kas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($bukuKas as $kas)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono text-[11px] font-bold px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700">
                            {{ $kas->kode_kas }}
                        </span>
                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full {{ $kas->jenis_kas === 'kas_rw' ? 'bg-blue-100 text-blue-800' : ($kas->jenis_kas === 'kas_rt' ? 'bg-emerald-100 text-emerald-800' : ($kas->jenis_kas === 'kas_sampah' ? 'bg-teal-100 text-teal-800' : 'bg-indigo-100 text-indigo-800')) }}">
                            {{ strtoupper(str_replace('_', ' ', $kas->jenis_kas)) }}
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-800 line-clamp-1">{{ $kas->nama_kas }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5 line-clamp-2">{{ $kas->deskripsi }}</p>

                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <span class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Saldo Buku Kas Saat Ini</span>
                        <p class="text-2xl font-black text-slate-900 mt-0.5">
                            Rp {{ number_format($kas->saldo_sekarang, 0, ',', '.') }}
                        </p>
                    </div>

                    @if($kas->target_anggaran > 0)
                        <div class="mt-2 text-xs">
                            @php $pct = min(100, round(($kas->saldo_sekarang / $kas->target_anggaran) * 100)); @endphp
                            <div class="flex justify-between text-[11px] text-slate-500 mb-1">
                                <span>Target Anggaran:</span>
                                <span class="font-bold">Rp {{ number_format($kas->target_anggaran, 0, ',', '.') }} ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-3 text-xs text-slate-500 space-y-0.5">
                        <p><strong>PIC:</strong> {{ $kas->nama_penanggung_jawab ?: '-' }}</p>
                        <p><strong>Rekening:</strong> {{ $kas->nama_bank ? $kas->nama_bank . ' - ' . $kas->no_rekening : 'Kas Tunai' }}</p>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">
                        Awal: Rp {{ number_format($kas->saldo_awal, 0, ',', '.') }}
                    </span>
                    <a href="{{ route('keuangan.buku_kas.show', $kas->id) }}" class="text-xs font-bold text-[#1456A0] hover:underline inline-flex items-center gap-1">
                        Buka Mutasi Rekening <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
