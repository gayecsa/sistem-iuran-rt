@extends('layouts.app')

@section('title', 'Keuangan Saya - Tagihan Iuran Keluarga - Warkas Machi')

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
    <!-- Header Warga: High-Contrast Royal Navy Blue Gradient -->
    <div class="bg-gradient-to-r from-[#0F3966] via-[#1456A0] to-[#1E6EC8] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm border border-white/30 text-white inline-block">
                    Keuangan Saya • Tagihan Iuran
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mt-2 text-white">Tagihan Iuran Keluarga</h1>
                <p class="text-blue-100 text-sm mt-1">
                    @if($kartuKeluarga)
                        Keluarga {{ $kartuKeluarga->nama_kepala_keluarga }} • No. KK: {{ $kartuKeluarga->masked_no_kk }} • {{ $kartuKeluarga->clean_rt_label }} RW 013 Sukamiskin
                    @else
                        Akun Warga RW 013 Sukamiskin
                    @endif
                </p>
            </div>

            <!-- Kontras Tinggi: Card Putih Solid dengan Text Navy & Green -->
            <div class="text-right bg-white rounded-xl px-5 py-3.5 shadow-md border border-blue-100 flex-shrink-0">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Iuran Terbayar</span>
                <p class="text-xl font-black text-[#16865D] mt-0.5">
                    Rp {{ number_format($totalIuranTerkumpul, 0, ',', '.') }}
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

    @if(!$kartuKeluarga)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center shadow-sm">
            <i data-lucide="alert-circle" class="w-12 h-12 text-amber-500 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-amber-900">Data Kartu Keluarga Belum Terhubung</h3>
            <p class="text-xs text-amber-700 max-w-md mx-auto mt-1 leading-relaxed">
                Akun Anda belum ditautkan dengan data Kartu Keluarga (KK) terdaftar di sistem. Silakan hubungi Ketua RT setempat untuk verifikasi data kependudukan keluarga Anda.
            </p>
        </div>
    @else
        <!-- Card Tagihan Periode Aktif -->
        @if($tagihanBulanIni)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/70">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-[#1456A0]">
                                Periode {{ \Carbon\Carbon::parse($tagihanBulanIni->periode . '-01')->translatedFormat('F Y') }}
                            </span>
                            <span class="font-mono text-xs text-slate-500 font-semibold">#{{ $tagihanBulanIni->nomor_tagihan }}</span>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800 mt-1">Tagihan Iuran Bulanan Keluarga</h2>
                        <p class="text-xs text-slate-500">Jatuh tempo: {{ \Carbon\Carbon::parse($tagihanBulanIni->jatuh_tempo)->translatedFormat('d F Y') }}</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="px-3.5 py-1.5 text-xs font-bold rounded-full border {{ $tagihanBulanIni->badge_class }}">
                            {{ $tagihanBulanIni->status }}
                        </span>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-6">
                    <!-- Rincian Komponen (7 Kolom) -->
                    <div class="md:col-span-7 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Rincian Komponen Tagihan Gabungan</h3>
                            <span class="text-[11px] font-semibold text-[#1456A0] bg-blue-50 px-2 py-0.5 rounded-md">
                                One Billing Terpadu
                            </span>
                        </div>
                        
                        <div class="space-y-2.5">
                            @if($tagihanBulanIni->items && $tagihanBulanIni->items->count() > 0)
                                @foreach($tagihanBulanIni->items as $idx => $item)
                                    <div class="flex justify-between items-start p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs hover:border-blue-200 transition">
                                        <div class="space-y-0.5 pr-2">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-slate-800">{{ $idx + 1 }}. {{ $item->nama_item }}</span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 font-medium badge-metode-iuran">
                                                    {{ $item->metode_label }}
                                                </span>
                                                @if($item->status_item === 'lunas')
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">
                                                        Lunas
                                                    </span>
                                                @elseif($item->status_item === 'sebagian')
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">
                                                        Sebagian (Rp {{ number_format($item->dibayar, 0, ',', '.') }})
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-slate-500">
                                                {{ $item->keterangan_snapshot ?: ($item->kuantitas . ' ' . $item->satuan_kuantitas . ' x Rp ' . number_format($item->tarif_satuan, 0, ',', '.')) }}
                                            </p>
                                            <p class="text-[11px] text-slate-500">
                                                Tujuan: <span class="font-medium text-slate-700">{{ $item->bukuKas ? $item->bukuKas->clean_nama_kas : ('Kas RT ' . $tagihanBulanIni->clean_rt) }}</span>
                                            </p>
                                        </div>
                                        
                                        <!-- Nominal dengan Label yang Sangat Jelas (Tidak Dobel Tanpa Konteks) -->
                                        <div class="text-right flex-shrink-0 space-y-0.5">
                                            @if($item->potongan_keringanan > 0)
                                                <div class="text-[10px] text-slate-400">
                                                    <span>Tarif: </span>
                                                    <span class="line-through">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="text-[10px] text-emerald-600 font-medium">
                                                    Diskon: -Rp {{ number_format($item->potongan_keringanan, 0, ',', '.') }}
                                                </div>
                                            @endif
                                            <div class="text-xs font-extrabold text-slate-900">
                                                <span class="text-[10px] text-slate-500 font-normal">Kewajiban: </span>
                                                Rp {{ number_format($item->total_akhir, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex justify-between items-center p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                    <div>
                                        <span class="font-bold text-slate-800">1. Iuran Kas RT {{ $tagihanBulanIni->clean_rt }}</span>
                                        <p class="text-[11px] text-slate-400">Operasional lingkungan, ATK, pemeliharaan lampu jalan</p>
                                    </div>
                                    <span class="font-bold text-slate-800">Rp {{ number_format($tagihanBulanIni->nominal_rt, 0, ',', '.') }}</span>
                                </div>

                                <div class="flex justify-between items-center p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                    <div>
                                        <span class="font-bold text-slate-800">2. Iuran Kas RW 013</span>
                                        <p class="text-[11px] text-slate-400">Keamanan terpadu/satpam kawasan, CCTV, sekretariat</p>
                                    </div>
                                    <span class="font-bold text-slate-800">Rp {{ number_format($tagihanBulanIni->nominal_rw, 0, ',', '.') }}</span>
                                </div>

                                <div class="flex justify-between items-center p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                    <div>
                                        <span class="font-bold text-slate-800">3. Retribusi Pengangkutan Sampah</span>
                                        <p class="text-[11px] text-slate-400">Petugas cator motor sampah door-to-door & TPS Sukamiskin</p>
                                    </div>
                                    <span class="font-bold text-slate-800">Rp {{ number_format($tagihanBulanIni->nominal_sampah, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Ringkasan Total Tagihan & Sisa Kewajiban -->
                        <div class="pt-3 border-t border-slate-200 space-y-2 bg-slate-50/50 p-3.5 rounded-xl">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-semibold text-slate-600">Total Tagihan Gabungan:</span>
                                <span class="font-bold text-slate-800">Rp {{ number_format($tagihanBulanIni->total_tagihan, 0, ',', '.') }}</span>
                            </div>
                            @if($tagihanBulanIni->total_dibayar > 0)
                                <div class="flex justify-between items-center text-xs text-emerald-700 text-terbayar-wrap">
                                    <span class="font-semibold">Sudah Terbayar:</span>
                                    <span class="font-bold">- Rp {{ number_format($tagihanBulanIni->total_dibayar, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center pt-2 border-t border-dashed border-slate-300">
                                <span class="font-extrabold text-sm text-slate-800 sisa-kewajiban-lbl">Sisa Kewajiban Tagihan:</span>
                                <span class="text-xl font-black text-[#1456A0] sisa-kewajiban-val">
                                    Rp {{ number_format($tagihanBulanIni->sisa_tagihan, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tindakan / Status Pembayaran (5 Kolom) -->
                    <div class="md:col-span-5 bg-blue-50/60 p-5 rounded-2xl border border-blue-100 flex flex-col justify-between space-y-4 panel-lunas-wrap">
                        @if($tagihanBulanIni->status === 'Lunas')
                            <div class="text-center py-6">
                                <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3 shadow-inner">
                                    <i data-lucide="check" class="w-8 h-8"></i>
                                </div>
                                <h4 class="font-bold text-emerald-900 text-base text-lunas-heading">Pembayaran Lunas</h4>
                                <p class="text-xs text-emerald-700 mt-1 leading-relaxed text-lunas-desc">
                                    Terima kasih atas partisipasi aktif keluarga Anda dalam mendukung kelancaran fasilitas dan operasional lingkungan RW 013.
                                </p>
                                <p class="text-[11px] text-slate-500 mt-2 font-mono">
                                    Lunas pada: {{ $tagihanBulanIni->tanggal_lunas ? $tagihanBulanIni->tanggal_lunas->translatedFormat('d M Y H:i') : '-' }}
                                </p>
                            </div>
                            <a href="{{ route('tagihan.kuitansi', $tagihanBulanIni->id) }}" target="_blank" class="w-full text-center py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow inline-flex items-center justify-center gap-2 transition">
                                <i data-lucide="printer" class="w-4 h-4"></i> Cetak Kuitansi Digital Resmi
                            </a>
                        @elseif($tagihanBulanIni->status === 'Menunggu Konfirmasi')
                            <div class="text-center py-5">
                                <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-2.5 shadow-inner">
                                    <i data-lucide="clock" class="w-6 h-6"></i>
                                </div>
                                <h4 class="font-bold text-amber-900 text-sm">Menunggu Konfirmasi Bendahara</h4>
                                <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                                    Bukti transfer Anda telah dikirim dan sedang dalam proses verifikasi rekening oleh bendahara RT/RW.
                                </p>
                                @if($tagihanBulanIni->bukti_pembayaran_path)
                                    <a href="{{ asset('storage/' . $tagihanBulanIni->bukti_pembayaran_path) }}" target="_blank" class="text-xs text-[#1456A0] font-semibold hover:underline mt-3 inline-block">
                                        Lihat Bukti yang Telah Diunggah &rarr;
                                    </a>
                                @endif
                            </div>
                            <a href="{{ route('warga.keuangan.bayar') }}" class="w-full text-center py-2.5 rounded-xl bg-[#1456A0] hover:bg-blue-800 text-white font-bold text-xs shadow inline-flex items-center justify-center gap-2 transition">
                                <i data-lucide="eye" class="w-4 h-4"></i> Cek Status Konfirmasi
                            </a>
                        @else
                            <!-- Belum Dibayar / Terlambat / Sebagian -->
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Instruksi Pembayaran</h4>
                                </div>
                                
                                <div class="bg-white p-3.5 rounded-xl border border-slate-200 text-xs space-y-1.5 shadow-sm">
                                    <p class="text-slate-500 text-[11px]">Transfer ke Rekening Resmi RW 013:</p>
                                    <p class="font-bold text-slate-800">{{ $kasRw->nama_bank ?? 'Bank Mandiri' }}: <span class="font-mono text-[#1456A0]">{{ $kasRw->no_rekening ?? '130-00-1928374-1' }}</span></p>
                                    <p class="text-[11px] text-slate-500">a.n. {{ $kasRw->atas_nama ?? 'PENGURUS RW 013 SUKAMISKIN' }}</p>
                                    <hr class="border-slate-100 my-1">
                                    <p class="text-[11px] text-slate-500">Atau bayar tunai ke Bendahara RT {{ $tagihanBulanIni->clean_rt }}.</p>
                                </div>
                            </div>

                            <div class="space-y-2 pt-2">
                                <a href="{{ route('warga.keuangan.bayar', ['tagihan_id' => $tagihanBulanIni->id]) }}" class="w-full py-2.5 rounded-xl bg-[#1456A0] hover:bg-blue-800 text-white font-bold text-xs shadow flex items-center justify-center gap-2 transition">
                                    <i data-lucide="credit-card" class="w-4 h-4"></i> Bayar Sekarang / Konfirmasi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center shadow-sm">
                <i data-lucide="check-circle-2" class="w-12 h-12 text-emerald-500 mx-auto mb-2"></i>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Tagihan Aktif</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                    Semua tagihan iuran keluarga Anda sudah terpenuhi atau belum ada tagihan baru yang diterbitkan.
                </p>
            </div>
        @endif
    @endif
</div>
@endsection
