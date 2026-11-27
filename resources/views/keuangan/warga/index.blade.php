@extends('layouts.app')

@section('title', 'Iuran & Keuangan Saya')

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
    <!-- Header Hero Terpadu (Tanpa Tombol Navigasi Internal Redundan) -->
    <div class="bg-gradient-to-r from-[#0F3966] via-[#1456A0] to-[#1E6EC8] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm border border-white/30 text-white inline-block">
                    Keuangan Saya • Pusat Layanan Terpadu
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mt-2 text-white">Iuran & Keuangan Saya</h1>
                <p class="text-blue-100 text-sm mt-1">
                    @if($kartuKeluarga)
                        Keluarga {{ $kartuKeluarga->nama_kepala_keluarga }} • No. KK: {{ $kartuKeluarga->masked_no_kk }} • {{ $kartuKeluarga->clean_rt_label }} RW 013 Suryatmajan
                    @else
                        Akun Warga RW 013 Suryatmajan
                    @endif
                </p>
            </div>

            <!-- Kontras Tinggi: Card Putih Solid -->
            <div class="text-right bg-white rounded-xl px-5 py-3.5 shadow-md border border-blue-100 flex-shrink-0">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Iuran Terbayar</span>
                <p class="text-xl font-black text-[#16865D] mt-0.5">
                    Rp {{ number_format($totalIuranTerkumpul, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Flash Session -->
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
        <!-- Tab Bar Bersih Tepat di Bawah Header -->
        <div class="bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-1.5">
            <!-- Tab 1 Button -->
            <button type="button" onclick="switchWargaTab('tagihan')" id="tab-btn-tagihan"
                    class="flex-1 py-3 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 border {{ $activeTab === 'tagihan' ? 'bg-[#1456A0] text-white shadow-sm border-[#1456A0]' : 'text-slate-600 hover:bg-blue-50/60 hover:text-[#1456A0] border-transparent' }}">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                <span>1. Tagihan Aktif</span>
                @if($tagihanBulanIni && in_array($tagihanBulanIni->status, ['Belum Dibayar', 'Terlambat', 'Dibayar Sebagian']))
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                @endif
            </button>

            <!-- Tab 2 Button -->
            <button type="button" onclick="switchWargaTab('bayar')" id="tab-btn-bayar"
                    class="flex-1 py-3 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 border {{ $activeTab === 'bayar' ? 'bg-[#1456A0] text-white shadow-sm border-[#1456A0]' : 'text-slate-600 hover:bg-blue-50/60 hover:text-[#1456A0] border-transparent' }}">
                <i data-lucide="credit-card" class="w-4 h-4"></i>
                <span>2. Bayar & Konfirmasi</span>
                @if($pendingTagihans->count() > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'bayar' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">
                        {{ $pendingTagihans->count() }}
                    </span>
                @endif
            </button>

            <!-- Tab 3 Button -->
            <button type="button" onclick="switchWargaTab('riwayat')" id="tab-btn-riwayat"
                    class="flex-1 py-3 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 border {{ $activeTab === 'riwayat' ? 'bg-[#1456A0] text-white shadow-sm border-[#1456A0]' : 'text-slate-600 hover:bg-blue-50/60 hover:text-[#1456A0] border-transparent' }}">
                <i data-lucide="history" class="w-4 h-4"></i>
                <span>3. Riwayat & Kuitansi</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'riwayat' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                    {{ $pembayarans->total() }}
                </span>
            </button>
        </div>

        <!-- ==================================================================== -->
        <!-- TAB 1: TAGIHAN AKTIF -->
        <!-- ==================================================================== -->
        <div id="tab-pane-tagihan" class="{{ $activeTab === 'tagihan' ? '' : 'hidden' }} space-y-6">
            @if($tagihanBulanIni)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/70">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-100 text-[#1456A0]">
                                    Periode {{ \Carbon\Carbon::parse($tagihanBulanIni->periode . '-01')->translatedFormat('F Y') }}
                                </span>
                                <span class="font-mono text-sm text-slate-500 font-semibold">#{{ $tagihanBulanIni->nomor_tagihan }}</span>
                            </div>
                            <h2 class="text-xl font-bold text-slate-800 mt-1">Tagihan Iuran Bulanan Keluarga</h2>
                            <p class="text-sm text-slate-500 mt-0.5">Jatuh tempo: {{ \Carbon\Carbon::parse($tagihanBulanIni->jatuh_tempo)->translatedFormat('d F Y') }}</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-4 py-1.5 text-sm font-bold rounded-full border {{ $tagihanBulanIni->badge_class }}">
                                {{ $tagihanBulanIni->status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-6">
                        <!-- Rincian Komponen (7 Kolom) -->
                        <div class="md:col-span-7 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600">Rincian Komponen Tagihan Gabungan</h3>
                                <span class="text-xs font-semibold text-[#1456A0] bg-blue-50 px-2.5 py-1 rounded-md">
                                    One Billing Terpadu
                                </span>
                            </div>
                            
                            <div class="space-y-2.5">
                                @if($tagihanBulanIni->items && $tagihanBulanIni->items->count() > 0)
                                    @foreach($tagihanBulanIni->items as $idx => $item)
                                        <div class="flex justify-between items-start p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-sm hover:border-blue-200 transition">
                                            <div class="space-y-1 pr-2">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-bold text-base text-slate-800">{{ $idx + 1 }}. {{ $item->nama_item }}</span>
                                                    <span class="text-xs px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-medium badge-metode-iuran">
                                                        {{ $item->metode_label }}
                                                    </span>
                                                    @if($item->status_item === 'lunas')
                                                        <span class="text-xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">
                                                            Lunas
                                                        </span>
                                                    @elseif($item->status_item === 'sebagian')
                                                        <span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">
                                                            Sebagian (Rp {{ number_format($item->dibayar, 0, ',', '.') }})
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-slate-600">
                                                    {{ $item->keterangan_snapshot ?: ($item->kuantitas . ' ' . $item->satuan_kuantitas . ' x Rp ' . number_format($item->tarif_satuan, 0, ',', '.')) }}
                                                </p>
                                                <p class="text-sm text-slate-600">
                                                    Tujuan: <span class="font-semibold text-slate-700">{{ $item->bukuKas ? $item->bukuKas->clean_nama_kas : ('Kas RT ' . $tagihanBulanIni->clean_rt) }}</span>
                                                </p>
                                            </div>
                                            
                                            <!-- Format Nominal Terang & Berlabel Jelas -->
                                            <div class="text-right flex-shrink-0 space-y-1">
                                                @if($item->potongan_keringanan > 0)
                                                    <div class="text-xs text-slate-400">
                                                        <span>Tarif: </span>
                                                        <span class="line-through">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                                    </div>
                                                    <div class="text-xs text-emerald-600 font-semibold">
                                                        Diskon: -Rp {{ number_format($item->potongan_keringanan, 0, ',', '.') }}
                                                    </div>
                                                @endif
                                                <div class="text-base font-extrabold text-slate-900">
                                                    <span class="text-xs text-slate-500 font-normal">Kewajiban: </span>
                                                    Rp {{ number_format($item->total_akhir, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex justify-between items-center p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-sm">
                                        <div>
                                            <span class="font-bold text-base text-slate-800">1. Iuran Kas RT {{ $tagihanBulanIni->clean_rt }}</span>
                                            <p class="text-sm text-slate-500">Operasional lingkungan, ATK, pemeliharaan lampu jalan</p>
                                        </div>
                                        <span class="font-bold text-base text-slate-800">Rp {{ number_format($tagihanBulanIni->nominal_rt, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="flex justify-between items-center p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-sm">
                                        <div>
                                            <span class="font-bold text-base text-slate-800">2. Iuran Kas RW 013</span>
                                            <p class="text-sm text-slate-500">Keamanan terpadu/satpam kawasan, CCTV, sekretariat</p>
                                        </div>
                                        <span class="font-bold text-base text-slate-800">Rp {{ number_format($tagihanBulanIni->nominal_rw, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="flex justify-between items-center p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-sm">
                                        <div>
                                            <span class="font-bold text-base text-slate-800">3. Retribusi Pengangkutan Sampah</span>
                                            <p class="text-sm text-slate-500">Petugas cator motor sampah door-to-door & TPS Suryatmajan</p>
                                        </div>
                                        <span class="font-bold text-base text-slate-800">Rp {{ number_format($tagihanBulanIni->nominal_sampah, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Ringkasan Total Tagihan & Sisa Kewajiban -->
                            <div class="pt-3 border-t border-slate-200 space-y-2 bg-slate-50/50 p-4 rounded-xl">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold text-slate-600">Total Tagihan Gabungan:</span>
                                    <span class="font-bold text-slate-800">Rp {{ number_format($tagihanBulanIni->total_tagihan, 0, ',', '.') }}</span>
                                </div>
                                @if($tagihanBulanIni->total_dibayar > 0)
                                    <div class="flex justify-between items-center text-sm text-emerald-700 text-terbayar-wrap">
                                        <span class="font-semibold">Sudah Terbayar:</span>
                                        <span class="font-bold">- Rp {{ number_format($tagihanBulanIni->total_dibayar, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center pt-2.5 border-t border-dashed border-slate-300">
                                    <span class="font-extrabold text-base text-slate-800 sisa-kewajiban-lbl">Sisa Kewajiban Tagihan:</span>
                                    <span class="text-2xl font-black text-[#1456A0] sisa-kewajiban-val">
                                        Rp {{ number_format($tagihanBulanIni->sisa_tagihan, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Status / Aksi Cepat (5 Kolom) -->
                        <div class="md:col-span-5 bg-blue-50/60 p-5 rounded-2xl border border-blue-100 flex flex-col justify-between space-y-4 panel-lunas-wrap">
                            @if($tagihanBulanIni->status === 'Lunas' || $tagihanBulanIni->status === 'Dibebaskan' || $tagihanBulanIni->sisa_tagihan <= 0)
                                <div class="text-center py-6">
                                    <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3 shadow-inner">
                                        <i data-lucide="check" class="w-8 h-8"></i>
                                    </div>
                                    <h4 class="font-bold text-emerald-900 text-base text-lunas-heading">
                                        {{ $tagihanBulanIni->status === 'Dibebaskan' ? 'Dibebaskan Resmi' : 'Pembayaran Lunas' }}
                                    </h4>
                                    <p class="text-sm text-emerald-700 mt-1 leading-relaxed text-lunas-desc">
                                        @if($tagihanBulanIni->status === 'Dibebaskan')
                                            Keluarga Anda telah dibebaskan dari kewajiban iuran untuk periode ini sesuai kebijakan pengurus RT/RW.
                                        @else
                                            Terima kasih atas partisipasi aktif keluarga Anda dalam mendukung kelancaran fasilitas dan operasional lingkungan RW 013.
                                        @endif
                                    </p>
                                    <p class="text-xs text-slate-500 mt-2 font-mono">
                                        {{ $tagihanBulanIni->status === 'Dibebaskan' ? 'Status: Bebas Iuran Resmi' : 'Lunas pada: ' . ($tagihanBulanIni->tanggal_lunas ? $tagihanBulanIni->tanggal_lunas->translatedFormat('d M Y H:i') : '-') }}
                                    </p>
                                </div>
                                <a href="{{ route('tagihan.kuitansi', $tagihanBulanIni->id) }}" target="_blank" class="w-full text-center py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow inline-flex items-center justify-center gap-2 transition">
                                    <i data-lucide="printer" class="w-4 h-4"></i> Lihat / Cetak Kuitansi Digital Resmi
                                </a>
                            @elseif($tagihanBulanIni->status === 'Menunggu Konfirmasi')
                                <div class="text-center py-5">
                                    <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-2.5 shadow-inner">
                                        <i data-lucide="clock" class="w-6 h-6"></i>
                                    </div>
                                    <h4 class="font-bold text-amber-900 text-base">Menunggu Konfirmasi Bendahara</h4>
                                    <p class="text-sm text-amber-700 mt-1 leading-relaxed">
                                        Bukti transfer Anda telah dikirim dan sedang dalam proses verifikasi rekening oleh bendahara RT/RW.
                                    </p>
                                    @if($tagihanBulanIni->bukti_pembayaran_path)
                                        <a href="{{ asset('storage/' . $tagihanBulanIni->bukti_pembayaran_path) }}" target="_blank" class="text-xs text-[#1456A0] font-semibold hover:underline mt-3 inline-block">
                                            Lihat Bukti yang Telah Diunggah &rarr;
                                        </a>
                                    @endif
                                </div>
                                <button type="button" onclick="switchWargaTab('bayar')" class="w-full text-center py-3 rounded-xl bg-[#1456A0] hover:bg-blue-800 text-white font-bold text-sm shadow inline-flex items-center justify-center gap-2 transition">
                                    <i data-lucide="eye" class="w-4 h-4"></i> Cek Status Konfirmasi
                                </button>
                            @else
                                <!-- Belum Dibayar / Terlambat / Sebagian -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                                        <h4 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Instruksi Pembayaran</h4>
                                    </div>
                                    
                                    <div class="bg-white p-4 rounded-xl border border-slate-200 text-sm space-y-1.5 shadow-sm">
                                        <p class="text-slate-500 text-xs">Transfer ke Rekening Resmi RW 013:</p>
                                        <p class="font-bold text-slate-800">{{ $kasRw->nama_bank ?? 'Bank Mandiri' }}: <span class="font-mono text-[#1456A0]">{{ $kasRw->no_rekening ?? '130-00-1928374-1' }}</span></p>
                                        <p class="text-xs text-slate-500">a.n. {{ $kasRw->atas_nama ?? 'PENGURUS RW 013 Suryatmajan' }}</p>
                                        <hr class="border-slate-100 my-1.5">
                                        <p class="text-xs text-slate-500">Atau bayar tunai ke Bendahara RT {{ $tagihanBulanIni->clean_rt }}.</p>
                                    </div>
                                </div>

                                <div class="space-y-2 pt-2">
                                    <button type="button" onclick="switchWargaTab('bayar')" class="w-full py-3 rounded-xl bg-[#1456A0] hover:bg-blue-800 text-white font-bold text-sm shadow flex items-center justify-center gap-2 transition">
                                        <i data-lucide="credit-card" class="w-4 h-4"></i> Bayar Sekarang / Konfirmasi &rarr;
                                    </button>
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
        </div>

        <!-- ==================================================================== -->
        <!-- TAB 2: BAYAR & KONFIRMASI -->
        <!-- ==================================================================== -->
        <div id="tab-pane-bayar" class="{{ $activeTab === 'bayar' ? '' : 'hidden' }} space-y-6">
            <!-- Daftar Tagihan Pending Verifikasi (Menunggu Konfirmasi Bendahara) -->
            @if($pendingTagihans->count() > 0)
                <div class="bg-amber-50/80 border border-amber-200 rounded-2xl p-5 shadow-sm space-y-3">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="clock" class="w-5 h-5 text-amber-600 flex-shrink-0"></i>
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-amber-900">Konfirmasi Sedang Diverifikasi Bendahara ({{ $pendingTagihans->count() }})</h3>
                            <p class="text-xs text-amber-800">Pembayaran berikut telah Anda konfirmasi dan sedang dicek mutasi oleh bendahara RT/RW.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 pt-1">
                        @foreach($pendingTagihans as $pt)
                            <div class="bg-white p-4 rounded-xl border border-amber-200 shadow-xs flex justify-between items-center text-sm">
                                <div>
                                    <span class="font-bold text-slate-800 text-base">Periode {{ \Carbon\Carbon::parse($pt->periode . '-01')->translatedFormat('F Y') }}</span>
                                    <div class="text-xs text-slate-600 mt-1">
                                        <span>Metode: {{ $pt->metode_pembayaran_terakhir ?: 'Transfer' }}</span> • 
                                        <span class="font-mono text-slate-800 font-bold">Rp {{ number_format($pt->total_tagihan, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 inline-block mb-1">
                                        Menunggu Verifikasi
                                    </span>
                                    @if($pt->bukti_pembayaran_path)
                                        <div>
                                            <a href="{{ asset('storage/' . $pt->bukti_pembayaran_path) }}" target="_blank" class="text-xs text-[#1456A0] hover:underline font-semibold">
                                                Lihat Bukti &rarr;
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Kolom Kiri: Form Pembayaran (7 Kolom) -->
                <div class="lg:col-span-7 space-y-6">
                    @if($unpaidTagihans->count() > 0 && $selectedTagihan)
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                    <i data-lucide="upload" class="w-5 h-5 text-[#1456A0]"></i> Unggah Bukti Pembayaran
                                </h2>
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 text-[#1456A0]">
                                    Form Konfirmasi
                                </span>
                            </div>

                            <!-- Dropdown Pilih Tagihan jika ada lebih dari 1 tagihan belum lunas -->
                            @if($unpaidTagihans->count() > 1)
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Pilih Tagihan yang Akan Dibayar:</label>
                                    <select onchange="window.location.href = '{{ route('keuangan.dashboard') }}?tab=bayar&tagihan_id=' + this.value" class="w-full text-sm font-semibold border border-slate-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none bg-slate-50">
                                        @foreach($unpaidTagihans as $ut)
                                            <option value="{{ $ut->id }}" {{ $selectedTagihan->id === $ut->id ? 'selected' : '' }}>
                                                Periode {{ \Carbon\Carbon::parse($ut->periode . '-01')->translatedFormat('F Y') }} — Sisa: Rp {{ number_format($ut->sisa_tagihan, 0, ',', '.') }} ({{ $ut->status }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Detail Tagihan yang Dipilih -->
                            <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-2 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-700">Periode Tagihan:</span>
                                    <span class="font-bold text-[#1456A0] text-base">{{ \Carbon\Carbon::parse($selectedTagihan->periode . '-01')->translatedFormat('F Y') }} (#{{ $selectedTagihan->nomor_tagihan }})</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600">Total Tagihan Gabungan:</span>
                                    <span class="font-semibold text-slate-800">Rp {{ number_format($selectedTagihan->total_tagihan, 0, ',', '.') }}</span>
                                </div>
                                @if($selectedTagihan->total_dibayar > 0)
                                    <div class="flex justify-between items-center text-emerald-700">
                                        <span>Sudah Dibayar Sebagian:</span>
                                        <span class="font-semibold">- Rp {{ number_format($selectedTagihan->total_dibayar, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center pt-2.5 border-t border-blue-200">
                                    <span class="font-bold text-base text-slate-800">Jumlah yang Harus Ditransfer:</span>
                                    <span class="text-xl font-black text-[#1456A0]">Rp {{ number_format($selectedTagihan->sisa_tagihan, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Form Input Bukti -->
                            <form action="{{ route('tagihan.bayar', $selectedTagihan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2">
                                @csrf
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Metode Pembayaran <span class="text-rose-500">*</span></label>
                                    <select name="metode_pembayaran" required class="w-full text-sm font-medium border border-slate-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                                        <option value="Transfer Bank">Transfer Bank (Mandiri / BCA / BJB)</option>
                                        <option value="QRIS">QRIS Standar RW 013 Suryatmajan</option>
                                        <option value="Tunai">Tunai ke Bendahara RT {{ $kartuKeluarga->clean_rt ?? '001' }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Upload Bukti Transfer / Resi / Struk <span class="text-rose-500">*</span></label>
                                    <input type="file" name="bukti_pembayaran" required accept="image/*,application/pdf" 
                                           class="w-full text-sm text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#1456A0] hover:file:bg-blue-100 border border-slate-300 rounded-xl p-1 bg-white">
                                    <p class="text-xs text-slate-500 mt-1">Format gambar JPG, PNG, atau file PDF (Maks. 3 MB).</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                                    <textarea name="catatan_warga" rows="2" placeholder="Contoh: Transfer via m-BCA a.n. {{ $kartuKeluarga->nama_kepala_keluarga }}" 
                                              class="w-full text-sm font-medium border border-slate-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none"></textarea>
                                </div>

                                <button type="submit" class="w-full py-3 rounded-xl bg-[#1456A0] hover:bg-blue-800 text-white font-bold text-sm shadow-md flex items-center justify-center gap-2 transition">
                                    <i data-lucide="send" class="w-4 h-4"></i> Kirim Konfirmasi Pembayaran
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Kondisi Semua Tagihan Lunas / Bebas -->
                        <div class="bg-white rounded-2xl border border-emerald-200 p-8 text-center shadow-sm space-y-4">
                            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-inner">
                                <i data-lucide="check-circle" class="w-10 h-10"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-extrabold text-slate-800">Semua Tagihan Sudah Lunas!</h2>
                                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 leading-relaxed">
                                    Tidak ada tagihan tertunggak atau kewajiban iuran yang perlu dibayarkan untuk keluarga Anda saat ini. Terima kasih telah tepat waktu!
                                </p>
                            </div>
                            <div class="flex justify-center gap-3 pt-2">
                                <button type="button" onclick="switchWargaTab('riwayat')" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow transition inline-flex items-center gap-1.5">
                                    <i data-lucide="file-check" class="w-4 h-4"></i> Lihat Riwayat & Kuitansi
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Kolom Kanan: Rekening Tujuan Pembayaran Resmi (5 Kolom) -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                            <i data-lucide="landmark" class="w-4 h-4 text-[#1456A0]"></i> Rekening Resmi Iuran RW 013
                        </h3>

                        <!-- Rekening Kas RW -->
                        <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-100 space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-base text-slate-800">Kas Induk RW 013</span>
                                <span class="px-2.5 py-0.5 rounded bg-blue-200 text-[#0F3966] font-bold text-xs">{{ $kasRw->nama_bank ?? 'Bank Mandiri' }}</span>
                            </div>
                            <p class="font-mono text-lg font-extrabold text-[#1456A0] tracking-wide select-all">{{ $kasRw->no_rekening ?? '130-00-1928374-1' }}</p>
                            <p class="text-xs text-slate-500">a.n. {{ $kasRw->atas_nama ?? 'PENGURUS RW 013 Suryatmajan' }}</p>
                        </div>

                        <!-- Rekening Kas RT -->
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-base text-slate-800">{{ $kasRt ? $kasRt->clean_nama_kas : ('Kas Operasional RT ' . ($kartuKeluarga->clean_rt ?? '001')) }}</span>
                                <span class="px-2.5 py-0.5 rounded bg-slate-200 text-slate-700 font-bold text-xs">{{ $kasRt->nama_bank ?? 'Bank BCA' }}</span>
                            </div>
                            <p class="font-mono text-lg font-extrabold text-slate-800 tracking-wide select-all">
                                {{ $kasRt && $kasRt->no_rekening ? $kasRt->no_rekening : '-' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                a.n. {{ $kasRt && $kasRt->atas_nama ? $kasRt->atas_nama : ('BENDAHARA RT ' . ($kartuKeluarga->clean_rt ?? '001')) }}
                            </p>
                        </div>

                        <!-- QRIS & Tunai -->
                        <div class="space-y-2.5 pt-1 text-sm text-slate-600">
                            <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <i data-lucide="qr-code" class="w-5 h-5 text-[#1456A0] flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">QRIS Warkas RW 013</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Tersedia barcode QRIS resmi pada sekretariat RW atau saat sosialisasi warga.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <i data-lucide="banknote" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">Pembayaran Tunai</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Dapat diserahkan langsung kepada Bendahara RT {{ $kartuKeluarga->clean_rt ?? '001' }} saat pertemuan bulanan warga.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================================================================== -->
        <!-- TAB 3: RIWAYAT & KUITANSI -->
        <!-- ==================================================================== -->
        <div id="tab-pane-riwayat" class="{{ $activeTab === 'riwayat' ? '' : 'hidden' }} space-y-6">
            <!-- Ringkasan Statistik -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Iuran Terbayar (Lunas)</span>
                    <p class="text-2xl font-black text-[#16865D] mt-1">
                        Rp {{ number_format($totalLunas, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-500 mt-1 block">Akumulasi iuran terverifikasi</span>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Sisa Kewajiban Saat Ini</span>
                    <p class="text-2xl font-black {{ $totalTertunggak > 0 ? 'text-rose-600' : 'text-slate-800' }} mt-1">
                        Rp {{ number_format($totalTertunggak, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-500 mt-1 block">Tagihan belum dibayar / sebagian</span>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Transaksi</span>
                    <p class="text-2xl font-black text-[#1456A0] mt-1">
                        {{ $pembayarans->total() }} <span class="text-base font-semibold text-slate-500">kali bayar</span>
                    </p>
                    <span class="text-xs text-slate-500 mt-1 block">Tercatat dalam sistem kas terpadu</span>
                </div>
            </div>

            <!-- Tabel Log Transaksi Pembayaran + Tombol Cetak Kuitansi Terintegrasi -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <i data-lucide="history" class="w-5 h-5 text-[#1456A0]"></i> Catatan Riwayat Pembayaran & Kuitansi Sah
                        </h2>
                        <p class="text-sm text-slate-500 mt-0.5">Semua bukti transfer dan kuitansi resmi atas pembayaran iuran keluarga Anda.</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700">
                        {{ $pembayarans->total() }} Data
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-xs">
                            <tr>
                                <th class="py-3.5 px-4">Tanggal Bayar</th>
                                <th class="py-3.5 px-4">No. Pembayaran</th>
                                <th class="py-3.5 px-4">Periode Tagihan</th>
                                <th class="py-3.5 px-4 text-right">Nominal Bayar</th>
                                <th class="py-3.5 px-4">Metode & Kanal</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Kuitansi / Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($pembayarans as $p)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-800">
                                        {{ $p->tanggal_bayar ? $p->tanggal_bayar->translatedFormat('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-xs text-slate-500">
                                        {{ $p->nomor_pembayaran ?: ('#PAY-' . $p->id) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-800">
                                        @if($p->tagihanKeluarga)
                                            <span class="font-semibold">{{ \Carbon\Carbon::parse($p->tagihanKeluarga->periode . '-01')->translatedFormat('F Y') }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-black text-slate-900 text-base">
                                        Rp {{ number_format($p->nominal_bayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700">
                                        <div class="font-semibold">{{ $p->metode_pembayaran ?: 'Transfer Bank' }}</div>
                                        <div class="text-xs text-slate-400">{{ $p->penerima ? ('Diterima: ' . $p->penerima->name) : 'Sistem Otomatis' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($p->status === 'dibatalkan')
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800">Dibatalkan</span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">Berhasil Sah</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($p->tagihanKeluarga && in_array($p->tagihanKeluarga->status, ['Lunas', 'Dibebaskan']))
                                            <a href="{{ route('tagihan.kuitansi', $p->tagihanKeluarga->id) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl bg-blue-50 text-[#1456A0] hover:bg-blue-100 border border-blue-200 transition">
                                                <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak Kuitansi
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

            <!-- Rekap Tagihan Bulanan per Periode -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-5 h-5 text-[#1456A0]"></i> Rekap Tagihan Bulanan Keluarga & Status Kuitansi
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-xs">
                            <tr>
                                <th class="py-3.5 px-4">Periode</th>
                                <th class="py-3.5 px-4">No. Tagihan</th>
                                <th class="py-3.5 px-4 text-right">Total Tagihan</th>
                                <th class="py-3.5 px-4 text-right">Terbayar</th>
                                <th class="py-3.5 px-4 text-right">Sisa</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Kuitansi Resmi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($tagihans as $t)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-800">
                                        {{ \Carbon\Carbon::parse($t->periode . '-01')->translatedFormat('F Y') }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-xs text-slate-500">
                                        {{ $t->nomor_tagihan }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-bold text-slate-800">
                                        Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right text-emerald-700 font-semibold">
                                        Rp {{ number_format($t->total_dibayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-black {{ $t->sisa_tagihan > 0 ? 'text-[#1456A0]' : 'text-slate-400' }}">
                                        Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $t->badge_class }}">
                                            {{ $t->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if(in_array($t->status, ['Lunas', 'Dibebaskan']))
                                            <a href="{{ route('tagihan.kuitansi', $t->id) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 transition border border-emerald-200">
                                                <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak Kuitansi
                                            </a>
                                        @else
                                            <span class="text-slate-400 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-slate-400">
                                        Belum ada data tagihan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function switchWargaTab(tabName) {
        // Daftar seluruh tab
        const tabs = ['tagihan', 'bayar', 'riwayat'];

        tabs.forEach(t => {
            const pane = document.getElementById('tab-pane-' + t);
            const btn = document.getElementById('tab-btn-' + t);

            if (t === tabName) {
                if (pane) pane.classList.remove('hidden');
                if (btn) {
                    btn.classList.add('bg-[#1456A0]', 'text-white', 'shadow-sm', 'border-[#1456A0]');
                    btn.classList.remove('text-slate-600', 'hover:bg-blue-50/60', 'hover:text-[#1456A0]', 'border-transparent');
                }
            } else {
                if (pane) pane.classList.add('hidden');
                if (btn) {
                    btn.classList.remove('bg-[#1456A0]', 'text-white', 'shadow-sm', 'border-[#1456A0]');
                    btn.classList.add('text-slate-600', 'hover:bg-blue-50/60', 'hover:text-[#1456A0]', 'border-transparent');
                }
            }
        });

        // Sinkronisasi parameter URL tanpa me-reload halaman
        try {
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        } catch (e) {}

        // Inisialisasi ulang lucide icons jika diperlukan
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    }
</script>
@endsection
