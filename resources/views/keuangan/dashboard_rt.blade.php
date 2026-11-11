@extends('layouts.app')

@section('title', 'Dashboard Keuangan RT ' . $rtStr . ' - Warkas Machi')

@section('content')
<!-- Tailwind CDN with Preflight Disabled -->
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
                    wmGreen: '#16865D',
                }
            }
        }
    }
</script>

<style>
    .wm-fin-card {
        background-color: var(--wm-card) !important;
        border: 1px solid var(--wm-border) !important;
        color: var(--wm-text) !important;
        border-radius: 20px;
        box-shadow: var(--wm-shadow);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .wm-fin-card:hover {
        box-shadow: var(--wm-shadow-lg);
    }
    .wm-rt-header {
        background: linear-gradient(135deg, #064E3B 0%, #0D9488 50%, #16865D 100%);
        border-radius: 22px;
        color: #FFFFFF !important;
    }
    .wm-rt-header h1, .wm-rt-header h2, .wm-rt-header p, .wm-rt-header span {
        color: #FFFFFF !important;
    }
    [data-theme="dark"] .bg-white, body.dark-mode .bg-white {
        background-color: var(--wm-card) !important;
        color: var(--wm-text) !important;
    }
    [data-theme="dark"] .bg-slate-50, body.dark-mode .bg-slate-50 {
        background-color: #121C30 !important;
        color: var(--wm-text) !important;
    }
    [data-theme="dark"] .border-slate-100, [data-theme="dark"] .border-slate-200,
    body.dark-mode .border-slate-100, body.dark-mode .border-slate-200 {
        border-color: var(--wm-border) !important;
    }
    [data-theme="dark"] .text-slate-800, [data-theme="dark"] .text-slate-900,
    body.dark-mode .text-slate-800, body.dark-mode .text-slate-900 {
        color: #F1F5F9 !important;
    }
    [data-theme="dark"] .text-slate-700, [data-theme="dark"] .text-slate-600,
    body.dark-mode .text-slate-700, body.dark-mode .text-slate-600 {
        color: #CBD5E1 !important;
    }
    [data-theme="dark"] .text-slate-500, [data-theme="dark"] .text-slate-400,
    body.dark-mode .text-slate-500, body.dark-mode .text-slate-400 {
        color: #94A3B8 !important;
    }
</style>

<div class="container-fluid p-0 space-y-6">
    <!-- Header Wilayah RT -->
    <div class="wm-rt-header p-6 md:p-8 relative overflow-hidden shadow-lg">
        <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
            <i data-lucide="wallet" style="width: 280px; height: 280px;"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md border border-white/30 text-white">
                        OTONOM KAS LINGKUNGAN RT {{ $rtStr }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-400/30 text-emerald-100 border border-emerald-300/30">
                        RW 013 Sukamiskin
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight">Dashboard Keuangan RT {{ $rtStr }}</h1>
                <p class="text-emerald-100 text-xs sm:text-sm mt-1">
                    Buku Kas Lingkungan & Manajemen Iuran Warga RT {{ $rtStr }} &bull; Periode {{ \Carbon\Carbon::parse($currentPeriode . '-01')->translatedFormat('F Y') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('keuangan.dashboard') }}" class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 py-2 rounded-xl border border-white/20">
                    <label for="periode_rt" class="text-xs text-white mb-0 font-medium">Periode:</label>
                    <input type="month" name="periode" id="periode_rt" value="{{ $currentPeriode }}" onchange="this.form.submit()" 
                           class="bg-white text-slate-800 text-xs font-bold rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-emerald-400 outline-none">
                </form>

                <a href="{{ route('keuangan.transaksi.create') }}" class="inline-flex items-center gap-1.5 bg-white text-emerald-900 font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow hover:bg-emerald-50 transition">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-700"></i> Catat Kas RT
                </a>
            </div>
        </div>

        <!-- Banner Metrik RT -->
        <div class="mt-6 pt-5 border-t border-white/15 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <span class="text-xs text-emerald-200 font-semibold uppercase tracking-wider">Saldo Kas RT {{ $rtStr }}</span>
                <p class="text-2xl sm:text-3xl font-black text-white mt-0.5">
                    Rp {{ number_format($bukuKasRt->saldo_sekarang ?? 0, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-emerald-100/90">{{ $bukuKasRt->nama_bank ?? 'Kas Tunai' }} &bull; {{ $bukuKasRt->no_rekening ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-emerald-200 font-semibold uppercase tracking-wider">Pemasukan RT Bulan Ini</span>
                <p class="text-xl sm:text-2xl font-bold text-emerald-200 mt-0.5">
                    +Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-emerald-100/90">Iuran warga & sumbangan</span>
            </div>
            <div>
                <span class="text-xs text-emerald-200 font-semibold uppercase tracking-wider">Pengeluaran RT Bulan Ini</span>
                <p class="text-xl sm:text-2xl font-bold text-rose-200 mt-0.5">
                    -Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-emerald-100/90">Kerja bakti, ATK, pemeliharaan</span>
            </div>
            <div>
                <span class="text-xs text-emerald-200 font-semibold uppercase tracking-wider">Kolektibilitas Iuran RT</span>
                <div class="flex items-center gap-2 mt-0.5">
                    <p class="text-xl sm:text-2xl font-black text-white">{{ $persenKolektibilitas }}%</p>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-white/20 text-white font-bold">
                        {{ $totalLunas }}/{{ $totalKkRt }} KK
                    </span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-white h-1.5 rounded-full" style="width: {{ min(100, $persenKolektibilitas) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-0" role="alert" style="background: #E3F5EC; color: #16865D; border-radius: 14px;">
            <i data-lucide="check-circle-2" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-semibold small">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Kartu Status Iuran Warga RT -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="wm-fin-card p-4">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">KK Lunas</span>
            <p class="text-2xl font-black text-emerald-600 mt-1 mb-0">{{ $totalLunas }}</p>
            <span class="text-[11px] text-slate-400">Telah diverifikasi</span>
        </div>
        <div class="wm-fin-card p-4">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Menunggu Konfirmasi</span>
            <p class="text-2xl font-black text-amber-600 mt-1 mb-0">{{ $totalMenunggu }}</p>
            <span class="text-[11px] text-slate-400">Bukti transfer masuk</span>
        </div>
        <div class="wm-fin-card p-4">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Belum Bayar</span>
            <p class="text-2xl font-black text-rose-600 mt-1 mb-0">{{ $totalBelum }}</p>
            <span class="text-[11px] text-slate-400">Perlu penagihan</span>
        </div>
        <div class="wm-fin-card p-4">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Dibebaskan (Dhuafa)</span>
            <p class="text-2xl font-black text-purple-600 mt-1 mb-0">{{ $totalDibebaskan }}</p>
            <span class="text-[11px] text-slate-400">SKTM / Bantuan sosial</span>
        </div>
    </div>

    <!-- Tabel Monitoring Tagihan Iuran Warga RT Ini -->
    <div class="wm-fin-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                    <i data-lucide="receipt" class="w-5 h-5 text-emerald-600"></i> Tagihan Iuran Warga RT {{ $rtStr }} (Periode {{ $currentPeriode }})
                </h2>
                <p class="text-xs text-slate-500 mt-1 mb-0">Iuran Rp 45.000/KK (Kas RT Rp 20.000 + Kas RW Rp 10.000 + Sampah Rp 15.000)</p>
            </div>
            <a href="{{ route('tagihan.index', ['rt' => $rtStr]) }}" class="wm-btn-outline" style="font-size: 0.8rem; padding: 6px 14px;">
                Kelola Semua Tagihan RT &rarr;
            </a>
        </div>

        <div class="table-responsive">
            <table class="table wm-table mb-0">
                <thead>
                    <tr>
                        <th class="py-3 px-3">No. Tagihan</th>
                        <th class="py-3 px-3">Kepala Keluarga</th>
                        <th class="py-3 px-3">No. Rumah / Alamat</th>
                        <th class="py-3 px-3 text-end">Nominal</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihansRt as $tagihan)
                        <tr>
                            <td class="py-3 px-3 font-mono font-bold text-nowrap" style="color: var(--wm-text);">
                                {{ $tagihan->nomor_tagihan }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="fw-bold" style="color: var(--wm-text);">{{ $tagihan->kartuKeluarga->nama_kepala_keluarga ?? '-' }}</div>
                                <div class="text-muted small font-mono">KK: {{ $tagihan->kartuKeluarga->masked_no_kk ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-3">
                                {{ $tagihan->kartuKeluarga->alamat ?? '-' }} No. {{ $tagihan->kartuKeluarga->nomor_rumah ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-end fw-bold text-nowrap" style="color: var(--wm-primary);">
                                Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center text-nowrap">
                                <span class="badge {{ $tagihan->status === 'Lunas' ? 'bg-success' : ($tagihan->status === 'Menunggu Konfirmasi' ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-1.5 font-bold" style="font-size: 0.72rem;">
                                    {{ $tagihan->status }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center text-nowrap">
                                <a href="{{ route('tagihan.show', $tagihan->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 font-semibold" style="font-size: 0.75rem;">
                                    Detail / Verifikasi
                                </a>
                                @if($tagihan->status === 'Lunas')
                                    <a href="{{ route('tagihan.kuitansi', $tagihan->id) }}" target="_blank" class="btn btn-sm btn-light rounded-circle p-1 ms-1 text-muted" title="Cetak Kuitansi">
                                        <i data-lucide="printer" style="width: 14px; height: 14px;"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted">
                                Belum ada data tagihan untuk RT {{ $rtStr }} di periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mutasi Terakhir Buku Kas RT Ini -->
    <div class="wm-fin-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100 mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-0">
                    <i data-lucide="book-open" class="w-5 h-5 text-emerald-600"></i> Mutasi Terakhir Buku Kas RT {{ $rtStr }}
                </h2>
                <p class="text-xs text-slate-500 mt-1 mb-0">Catatan transaksi pemasukan dan pengeluaran kas lingkungan RT {{ $rtStr }}</p>
            </div>
            <a href="{{ route('keuangan.buku_kas.show', $bukuKasRt->id) }}" class="wm-btn-outline" style="font-size: 0.8rem; padding: 6px 14px;">
                Buku Kas Lengkap &rarr;
            </a>
        </div>

        <div class="table-responsive">
            <table class="table wm-table mb-0">
                <thead>
                    <tr>
                        <th class="py-3 px-3">No. Transaksi</th>
                        <th class="py-3 px-3">Tanggal</th>
                        <th class="py-3 px-3">Kategori & Keterangan</th>
                        <th class="py-3 px-3">Penyetor / Penerima</th>
                        <th class="py-3 px-3 text-end">Nominal</th>
                        <th class="py-3 px-3 text-end">Saldo Berjalan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $trx)
                        <tr>
                            <td class="py-3 px-3 font-mono fw-bold text-nowrap" style="color: var(--wm-text);">{{ $trx->nomor_transaksi }}</td>
                            <td class="py-3 px-3 text-muted text-nowrap" style="font-size: 0.82rem;">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-3">
                                <div class="fw-bold" style="color: var(--wm-text);">{{ $trx->kategori }}</div>
                                <div class="text-muted small truncate" style="max-width: 250px;">{{ $trx->keterangan }}</div>
                            </td>
                            <td class="py-3 px-3">{{ $trx->penyetor_penerima ?: '-' }}</td>
                            <td class="py-3 px-3 text-end fw-bold text-nowrap {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? 'text-success' : 'text-danger' }}">
                                {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? '+' : '-' }}Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-end font-mono fw-bold text-nowrap" style="color: var(--wm-primary);">
                                Rp {{ number_format($trx->saldo_setelahnya, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted">
                                Belum ada transaksi yang tercatat pada buku kas RT ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
