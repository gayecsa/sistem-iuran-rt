@extends('layouts.app')

@section('title', 'Kuitansi Digital & Bukti Bayar Resmi - Warkas Machi')

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
                    Keuangan Saya • Dokumen Sah
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mt-2 text-white">Bukti Pembayaran & Kuitansi Digital</h1>
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
                <a href="{{ route('warga.keuangan.bayar') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 backdrop-blur-sm transition flex items-center gap-1.5">
                    <i data-lucide="credit-card" class="w-4 h-4"></i> Konfirmasi Bayar
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
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="file-check" class="w-5 h-5 text-emerald-600"></i> Repositori Kuitansi Sah Resmi RW 013
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar dokumen kuitansi sah atas iuran keluarga yang telah diverifikasi lunas.</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 self-start sm:self-auto">
                    {{ $kuitansiList->total() }} Kuitansi Tersedia
                </span>
            </div>

            <!-- List Kuitansi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                @forelse($kuitansiList as $k)
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-blue-300 hover:shadow-md transition space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                    {{ $k->status === 'Dibebaskan' ? 'Dibebaskan Resmi' : 'Lunas Terverifikasi' }}
                                </span>
                                <h3 class="text-base font-extrabold text-slate-800 mt-1.5">
                                    Periode {{ \Carbon\Carbon::parse($k->periode . '-01')->translatedFormat('F Y') }}
                                </h3>
                                <p class="text-[11px] font-mono text-slate-500">No: {{ $k->nomor_tagihan }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 flex-shrink-0">
                                <i data-lucide="receipt" class="w-5 h-5"></i>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-200 text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Total Iuran:</span>
                                <span class="font-bold text-slate-900">Rp {{ number_format($k->total_tagihan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Tanggal Lunas:</span>
                                <span class="font-medium text-slate-700">
                                    {{ $k->tanggal_lunas ? $k->tanggal_lunas->translatedFormat('d M Y H:i') : ($k->diverifikasi_pada ? $k->diverifikasi_pada->translatedFormat('d M Y') : '-') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Verifikator:</span>
                                <span class="font-medium text-slate-700">
                                    {{ $k->diverifikasiOleh ? $k->diverifikasiOleh->name : 'Pengurus RW 013' }}
                                </span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <a href="{{ route('tagihan.kuitansi', $k->id) }}" target="_blank" class="w-full py-2.5 rounded-xl bg-[#1456A0] hover:bg-blue-800 text-white font-bold text-xs shadow-sm inline-flex items-center justify-center gap-2 transition">
                                <i data-lucide="printer" class="w-4 h-4"></i> Cetak / Unduh Kuitansi Digital
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-12 text-center text-slate-400 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <i data-lucide="file-x" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                        <h4 class="text-sm font-bold text-slate-700">Belum Ada Kuitansi Sah</h4>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                            Kuitansi resmi digital akan otomatis terbit begitu pembayaran iuran keluarga Anda diverifikasi lunas oleh bendahara.
                        </p>
                    </div>
                @endforelse
            </div>

            @if($kuitansiList->hasPages())
                <div class="pt-4 border-t border-slate-100">
                    {{ $kuitansiList->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
