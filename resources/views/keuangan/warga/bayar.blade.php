@extends('layouts.app')

@section('title', 'Bayar Iuran & Konfirmasi Transfer - Warkas Machi')

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
                    Keuangan Saya • Pembayaran Iuran
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mt-2 text-white">Bayar Iuran & Konfirmasi</h1>
                <p class="text-blue-100 text-sm mt-1">
                    @if($kartuKeluarga)
                        Keluarga {{ $kartuKeluarga->nama_kepala_keluarga }} • No. KK: {{ $kartuKeluarga->masked_no_kk }} • {{ $kartuKeluarga->clean_rt_label }} RW 013 Suryatmajan
                    @else
                        Akun Warga RW 013 Suryatmajan
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('warga.keuangan.tagihan') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 backdrop-blur-sm transition flex items-center gap-1.5">
                    <i data-lucide="receipt" class="w-4 h-4"></i> Lihat Tagihan
                </a>
                <a href="{{ route('warga.keuangan.riwayat') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 backdrop-blur-sm transition flex items-center gap-1.5">
                    <i data-lucide="history" class="w-4 h-4"></i> Riwayat
                </a>
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
                Akun Anda belum ditautkan dengan data Kartu Keluarga (KK). Silakan hubungi Ketua RT setempat untuk verifikasi data kependudukan.
            </p>
        </div>
    @else
        <!-- Daftar Tagihan Pending Verifikasi (Menunggu Konfirmasi Bendahara) -->
        @if($pendingTagihans->count() > 0)
            <div class="bg-amber-50/80 border border-amber-200 rounded-2xl p-5 shadow-sm space-y-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-amber-600 flex-shrink-0"></i>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900">Konfirmasi Sedang Diverifikasi Bendahara ({{ $pendingTagihans->count() }})</h3>
                        <p class="text-[11px] text-amber-700">Pembayaran berikut telah Anda konfirmasi dan sedang dicek oleh bendahara.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                    @foreach($pendingTagihans as $pt)
                        <div class="bg-white p-3.5 rounded-xl border border-amber-200 shadow-xs flex justify-between items-center text-xs">
                            <div>
                                <span class="font-bold text-slate-800">Periode {{ \Carbon\Carbon::parse($pt->periode . '-01')->translatedFormat('F Y') }}</span>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    <span>Metode: {{ $pt->metode_pembayaran_terakhir ?: 'Transfer' }}</span> • 
                                    <span class="font-mono text-slate-600 font-bold">Rp {{ number_format($pt->total_tagihan, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 inline-block mb-1">
                                    Menunggu Verifikasi
                                </span>
                                @if($pt->bukti_pembayaran_path)
                                    <div>
                                        <a href="{{ asset('storage/' . $pt->bukti_pembayaran_path) }}" target="_blank" class="text-[11px] text-[#1456A0] hover:underline font-semibold">
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
                                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Tagihan yang Akan Dibayar:</label>
                                <select onchange="window.location.href = '{{ route('warga.keuangan.bayar') }}?tagihan_id=' + this.value" class="w-full text-xs font-semibold border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none bg-slate-50">
                                    @foreach($unpaidTagihans as $ut)
                                        <option value="{{ $ut->id }}" {{ $selectedTagihan->id === $ut->id ? 'selected' : '' }}>
                                            Periode {{ \Carbon\Carbon::parse($ut->periode . '-01')->translatedFormat('F Y') }} — Sisa: Rp {{ number_format($ut->sisa_tagihan, 0, ',', '.') }} ({{ $ut->status }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Detail Tagihan yang Dipilih -->
                        <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-2 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-slate-700">Periode Tagihan:</span>
                                <span class="font-bold text-[#1456A0]">{{ \Carbon\Carbon::parse($selectedTagihan->periode . '-01')->translatedFormat('F Y') }} (#{{ $selectedTagihan->nomor_tagihan }})</span>
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
                            <div class="flex justify-between items-center pt-2 border-t border-blue-200">
                                <span class="font-bold text-sm text-slate-800">Jumlah yang Harus Ditransfer:</span>
                                <span class="text-lg font-black text-[#1456A0]">Rp {{ number_format($selectedTagihan->sisa_tagihan, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Form Input Bukti -->
                        <form action="{{ route('tagihan.bayar', $selectedTagihan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran <span class="text-rose-500">*</span></label>
                                <select name="metode_pembayaran" required class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                                    <option value="Transfer Bank">Transfer Bank (Mandiri / BCA / BJB)</option>
                                    <option value="QRIS">QRIS Standar RW 013 Suryatmajan</option>
                                    <option value="Tunai">Tunai ke Bendahara RT {{ $kartuKeluarga->clean_rt ?? '001' }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Upload Bukti Transfer / Resi / Struk <span class="text-rose-500">*</span></label>
                                <input type="file" name="bukti_pembayaran" required accept="image/*,application/pdf" 
                                       class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#1456A0] hover:file:bg-blue-100 border border-slate-300 rounded-xl p-1 bg-white">
                                <p class="text-[11px] text-slate-400 mt-1">Format gambar JPG, PNG, atau file PDF (Maks. 3 MB).</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                                <textarea name="catatan_warga" rows="2" placeholder="Contoh: Transfer via m-BCA a.n. {{ $kartuKeluarga->nama_kepala_keluarga }}" 
                                          class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none"></textarea>
                            </div>

                            <button type="submit" class="w-full py-3 rounded-xl bg-[#1456A0] hover:bg-blue-800 text-white font-bold text-xs shadow-md flex items-center justify-center gap-2 transition">
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
                            <a href="{{ route('warga.keuangan.kuitansi') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow transition inline-flex items-center gap-1.5">
                                <i data-lucide="file-check" class="w-4 h-4"></i> Lihat Kuitansi Resmi
                            </a>
                            <a href="{{ route('warga.keuangan.riwayat') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition inline-flex items-center gap-1.5">
                                <i data-lucide="history" class="w-4 h-4"></i> Riwayat Transaksi
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Kolom Kanan: Rekening Tujuan Pembayaran Resmi (5 Kolom) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                        <i data-lucide="landmark" class="w-4 h-4 text-[#1456A0]"></i> Rekening Resmi Iuran RW 013
                    </h3>

                    <!-- Rekening Kas RW -->
                    <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-100 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800">Kas Induk RW 013</span>
                            <span class="px-2 py-0.5 rounded bg-blue-200 text-[#0F3966] font-bold text-[10px]">{{ $kasRw->nama_bank ?? 'Bank Mandiri' }}</span>
                        </div>
                        <p class="font-mono text-base font-extrabold text-[#1456A0] tracking-wide select-all">{{ $kasRw->no_rekening ?? '130-00-1928374-1' }}</p>
                        <p class="text-[11px] text-slate-500">a.n. {{ $kasRw->atas_nama ?? 'PENGURUS RW 013 Suryatmajan' }}</p>
                    </div>

                    <!-- Rekening Kas RT -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800">{{ $kasRt ? $kasRt->clean_nama_kas : ('Kas Operasional RT ' . ($kartuKeluarga->clean_rt ?? '001')) }}</span>
                            <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold text-[10px]">{{ $kasRt->nama_bank ?? 'Bank BCA' }}</span>
                        </div>
                        <p class="font-mono text-base font-extrabold text-slate-800 tracking-wide select-all">
                            {{ $kasRt && $kasRt->no_rekening ? $kasRt->no_rekening : '-' }}
                        </p>
                        <p class="text-[11px] text-slate-500">
                            a.n. {{ $kasRt && $kasRt->atas_nama ? $kasRt->atas_nama : ('BENDAHARA RT ' . ($kartuKeluarga->clean_rt ?? '001')) }}
                        </p>
                    </div>

                    <!-- QRIS & Tunai -->
                    <div class="space-y-2 pt-1 text-xs text-slate-600">
                        <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <i data-lucide="qr-code" class="w-4 h-4 text-[#1456A0] flex-shrink-0 mt-0.5"></i>
                            <div>
                                <p class="font-bold text-slate-800">QRIS Warkas RW 013</p>
                                <p class="text-[11px] text-slate-500">Tersedia barcode QRIS resmi pada sekretariat RW atau saat sosialisasi warga.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <i data-lucide="banknote" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <p class="font-bold text-slate-800">Pembayaran Tunai</p>
                                <p class="text-[11px] text-slate-500">Dapat diserahkan langsung kepada Bendahara RT {{ $kartuKeluarga->clean_rt ?? '001' }} saat pertemuan bulanan warga.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
