@extends('layouts.app')

@section('title', 'Riwayat Pembayaran Iuran - Warkas Machi')

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
    <!-- Header -->
    <div class="bg-gradient-to-r from-[#0F3966] via-[#1456A0] to-[#1E6EC8] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm border border-white/30 text-white inline-block">
                    Keuangan Saya • Catatan Historis
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mt-2 text-white">Riwayat Pembayaran Iuran</h1>
                <p class="text-blue-100 text-sm mt-1">
                    @if($kartuKeluarga)
                        Keluarga {{ $kartuKeluarga->nama_kepala_keluarga }} • No. KK: {{ $kartuKeluarga->masked_no_kk }} • {{ $kartuKeluarga->clean_rt_label }} RW 013 Sukamiskin
                    @else
                        Akun Warga RW 013 Sukamiskin
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('warga.keuangan.tagihan') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 backdrop-blur-sm transition flex items-center gap-1.5">
                    <i data-lucide="receipt" class="w-4 h-4"></i> Tagihan Aktif
                </a>
                <a href="{{ route('warga.keuangan.bayar') }}" class="px-4 py-2 rounded-xl bg-white text-[#1456A0] hover:bg-blue-50 font-bold text-xs shadow transition flex items-center gap-1.5">
                    <i data-lucide="credit-card" class="w-4 h-4"></i> Bayar Iuran
                </a>
            </div>
        </div>
    </div>

    @if(!$kartuKeluarga)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center shadow-sm">
            <i data-lucide="alert-circle" class="w-12 h-12 text-amber-500 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-amber-900">Data Kartu Keluarga Belum Terhubung</h3>
            <p class="text-xs text-amber-700 max-w-md mx-auto mt-1 leading-relaxed">
                Akun Anda belum ditautkan dengan data Kartu Keluarga (KK). Silakan hubungi Ketua RT setempat untuk verifikasi data kependudukan.
            </p>
        </div>
    @else
        <!-- Ringkasan Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Iuran Terbayar (Lunas)</span>
                <p class="text-2xl font-black text-[#16865D] mt-1">
                    Rp {{ number_format($totalLunas, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Akumulasi iuran terverifikasi</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Sisa Kewajiban Saat Ini</span>
                <p class="text-2xl font-black {{ $totalTertunggak > 0 ? 'text-rose-600' : 'text-slate-800' }} mt-1">
                    Rp {{ number_format($totalTertunggak, 0, ',', '.') }}
                </p>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Tagihan belum dibayar / sebagian</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Transaksi</span>
                <p class="text-2xl font-black text-[#1456A0] mt-1">
                    {{ $pembayarans->total() }} <span class="text-sm font-semibold text-slate-500">kali bayar</span>
                </p>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Tercatat dalam sistem kas terpadu</span>
            </div>
        </div>

        <!-- Tabel Log Transaksi Pembayaran -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5 text-[#1456A0]"></i> Catatan Riwayat Transaksi Pembayaran
                </h2>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700">
                    {{ $pembayarans->total() }} Data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3 px-4">Tanggal Bayar</th>
                            <th class="py-3 px-4">No. Pembayaran</th>
                            <th class="py-3 px-4">Periode Tagihan</th>
                            <th class="py-3 px-4 text-right">Nominal Bayar</th>
                            <th class="py-3 px-4">Metode & Kanal</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi / Kuitansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($pembayarans as $p)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    {{ $p->tanggal_bayar ? $p->tanggal_bayar->translatedFormat('d M Y H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-500">
                                    {{ $p->nomor_pembayaran ?: ('#PAY-' . $p->id) }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-800">
                                    @if($p->tagihanKeluarga)
                                        <span class="font-semibold">{{ \Carbon\Carbon::parse($p->tagihanKeluarga->periode . '-01')->translatedFormat('F Y') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                    Rp {{ number_format($p->nominal_bayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    <div class="font-semibold">{{ $p->metode_pembayaran ?: 'Transfer Bank' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $p->penerima ? ('Diterima: ' . $p->penerima->name) : 'Sistem Otomatis' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($p->status === 'dibatalkan')
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-800">Dibatalkan</span>
                                    @else
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800">Berhasil Sah</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($p->tagihanKeluarga && $p->tagihanKeluarga->status === 'Lunas')
                                        <a href="{{ route('tagihan.kuitansi', $p->tagihanKeluarga->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg bg-blue-50 text-[#1456A0] hover:bg-blue-100 transition">
                                            <i data-lucide="printer" class="w-3.5 h-3.5"></i> Kuitansi
                                        </a>
                                    @elseif($p->bukti_pembayaran_path)
                                        <a href="{{ asset('storage/' . $p->bukti_pembayaran_path) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Bukti
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                    Belum ada catatan riwayat transaksi pembayaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pembayarans->hasPages())
                <div class="pt-4 border-t border-slate-100">
                    {{ $pembayarans->links() }}
                </div>
            @endif
        </div>

        <!-- Rekap Tagihan per Periode -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="calendar" class="w-5 h-5 text-[#1456A0]"></i> Rekap Tagihan Bulanan Keluarga
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3 px-4">Periode</th>
                            <th class="py-3 px-4">No. Tagihan</th>
                            <th class="py-3 px-4 text-right">Total Tagihan</th>
                            <th class="py-3 px-4 text-right">Terbayar</th>
                            <th class="py-3 px-4 text-right">Sisa</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($tagihans as $t)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($t->periode . '-01')->translatedFormat('F Y') }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-500">
                                    {{ $t->nomor_tagihan }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-slate-800">
                                    Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right text-emerald-700 font-semibold">
                                    Rp {{ number_format($t->total_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-black {{ $t->sisa_tagihan > 0 ? 'text-[#1456A0]' : 'text-slate-400' }}">
                                    Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full border {{ $t->badge_class }}">
                                        {{ $t->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">
                                    Belum ada data tagihan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
