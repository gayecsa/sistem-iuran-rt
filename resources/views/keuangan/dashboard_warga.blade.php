@extends('layouts.app')

@section('title', 'Keuangan Saya - Tagihan Iuran Keluarga - Warkas Machi')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Warga -->
    <div class="bg-gradient-to-r from-[#0F3966] via-[#1456A0] to-[#1E6EC8] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm border border-white/30 text-white">
                    Keuangan & Iuran Keluarga
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mt-1">Tagihan Iuran Warga</h1>
                <p class="text-blue-100 text-sm mt-0.5">
                    @if($kartuKeluarga)
                        Keluarga {{ $kartuKeluarga->nama_kepala_keluarga }} • No. KK: {{ $kartuKeluarga->masked_no_kk }} • {{ $kartuKeluarga->clean_rt_label }} RW 013 Sukamiskin
                    @else
                        Akun Warga RW 013 Sukamiskin
                    @endif
                </p>
            </div>

            <div class="text-right bg-white/10 backdrop-blur-md px-4 py-3 rounded-xl border border-white/20">
                <span class="text-xs text-blue-200">Total Iuran Terbayar</span>
                <p class="text-xl font-extrabold text-emerald-300">
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
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
            <i data-lucide="alert-circle" class="w-12 h-12 text-amber-500 mx-auto mb-2"></i>
            <h3 class="text-base font-bold text-amber-900">Data Kartu Keluarga Belum Terhubung</h3>
            <p class="text-xs text-amber-700 max-w-md mx-auto mt-1">
                Akun Anda belum ditautkan dengan Kartu Keluarga (KK) terdaftar. Silakan hubungi Ketua RT setempat untuk verifikasi data kependudukan.
            </p>
        </div>
    @else
        <!-- Card Tagihan Bulan Berjalan -->
        @if($tagihanBulanIni)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-[#1456A0]">
                                Periode {{ \Carbon\Carbon::parse($tagihanBulanIni->periode . '-01')->translatedFormat('F Y') }}
                            </span>
                            <span class="font-mono text-xs text-slate-400">#{{ $tagihanBulanIni->nomor_tagihan }}</span>
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
                    <!-- Rincian Nominal (7 Kolom) -->
                    <div class="md:col-span-7 space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rincian Komponen Iuran</h3>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                <div>
                                    <span class="font-bold text-slate-800">1. Iuran Kas RT {{ $tagihanBulanIni->clean_rt }}</span>
                                    <p class="text-[11px] text-slate-400">Operasional kerja bakti, ATK, pemeliharaan lampu jalan</p>
                                </div>
                                <span class="font-bold text-slate-800">Rp {{ number_format($tagihanBulanIni->nominal_rt, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                <div>
                                    <span class="font-bold text-slate-800">2. Iuran Kas RW 013</span>
                                    <p class="text-[11px] text-slate-400">Honor keamanan/satpam kawasan, CCTV, sekretariat</p>
                                </div>
                                <span class="font-bold text-slate-800">Rp {{ number_format($tagihanBulanIni->nominal_rw, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                <div>
                                    <span class="font-bold text-slate-800">3. Retribusi Pengangkutan Sampah</span>
                                    <p class="text-[11px] text-slate-400">Petugas cator motor sampah door-to-door & TPS Sukamiskin</p>
                                </div>
                                <span class="font-bold text-slate-800">Rp {{ number_format($tagihanBulanIni->nominal_sampah, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-200 flex justify-between items-center">
                            <span class="font-extrabold text-sm text-slate-800">Total Tagihan:</span>
                            <span class="text-xl font-black text-[#1456A0]">
                                Rp {{ number_format($tagihanBulanIni->total_tagihan, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Tindakan / Status Pembayaran (5 Kolom) -->
                    <div class="md:col-span-5 bg-blue-50/50 p-5 rounded-2xl border border-blue-100 flex flex-col justify-between">
                        @if($tagihanBulanIni->status === 'Lunas')
                            <div class="text-center py-4">
                                <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="check" class="w-8 h-8"></i>
                                </div>
                                <h4 class="font-bold text-emerald-900 text-base">Pembayaran Lunas</h4>
                                <p class="text-xs text-emerald-700 mt-1">
                                    Terima kasih atas partisipasi aktif keluarga Anda menjaga lingkungan RW 013.
                                </p>
                                <p class="text-[11px] text-slate-400 mt-2 font-mono">
                                    Lunas pada: {{ $tagihanBulanIni->tanggal_lunas ? $tagihanBulanIni->tanggal_lunas->translatedFormat('d M Y H:i') : '-' }}
                                </p>
                            </div>
                            <a href="{{ route('tagihan.kuitansi', $tagihanBulanIni->id) }}" target="_blank" class="w-full text-center py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow inline-flex items-center justify-center gap-2">
                                <i data-lucide="printer" class="w-4 h-4"></i> Cetak Kuitansi Digital Resmi
                            </a>
                        @elseif($tagihanBulanIni->status === 'Menunggu Konfirmasi')
                            <div class="text-center py-3">
                                <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-2">
                                    <i data-lucide="clock" class="w-6 h-6"></i>
                                </div>
                                <h4 class="font-bold text-amber-900 text-sm">Menunggu Konfirmasi Bendahara</h4>
                                <p class="text-xs text-amber-700 mt-1">
                                    Bukti transfer Anda telah dikirim dan sedang dalam proses verifikasi rekening.
                                </p>
                                @if($tagihanBulanIni->bukti_pembayaran_path)
                                    <a href="{{ asset('storage/' . $tagihanBulanIni->bukti_pembayaran_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-2 inline-block">
                                        Lihat Bukti yang Diunggah
                                    </a>
                                @endif
                            </div>
                        @else
                            <!-- Form Bayar / Konfirmasi -->
                            <div>
                                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-2">Instruksi Pembayaran</h4>
                                <div class="bg-white p-3 rounded-xl border border-slate-200 text-xs space-y-1 mb-3">
                                    <p class="text-slate-500">Transfer ke Rekening RW 013:</p>
                                    <p class="font-bold text-slate-800">Bank Mandiri: <span class="font-mono text-[#1456A0]">130-00-1928374-1</span></p>
                                    <p class="text-[11px] text-slate-500">a.n. PENGURUS RW 013 SUKAMISKIN</p>
                                    <p class="text-[11px] text-slate-400 pt-1">Atau bayar tunai ke Bendahara RT {{ $tagihanBulanIni->clean_rt }}.</p>
                                </div>

                                <button onclick="document.getElementById('modal-konfirmasi-bayar').classList.remove('hidden')" class="w-full py-2.5 rounded-xl bg-[#1456A0] hover:bg-blue-800 text-white font-bold text-xs shadow flex items-center justify-center gap-2">
                                    <i data-lucide="upload" class="w-4 h-4"></i> Konfirmasi / Upload Bukti Bayar
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Riwayat Tagihan Sebelumnya -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-4">
                <i data-lucide="history" class="w-5 h-5 text-[#1456A0]"></i> Riwayat Iuran Keluarga
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3 px-4">Periode</th>
                            <th class="py-3 px-4">No. Tagihan</th>
                            <th class="py-3 px-4 text-right">Total Iuran</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4">Metode Bayar</th>
                            <th class="py-3 px-4 text-center">Kuitansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($riwayatTagihans as $riwayat)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($riwayat->periode . '-01')->translatedFormat('F Y') }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-500">
                                    {{ $riwayat->nomor_tagihan }}
                                </td>
                                <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                    Rp {{ number_format($riwayat->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full border {{ $riwayat->badge_class }}">
                                        {{ $riwayat->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $riwayat->metode_pembayaran_terakhir ?: '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($riwayat->status === 'Lunas')
                                        <a href="{{ route('tagihan.kuitansi', $riwayat->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg bg-blue-50 text-[#1456A0] hover:bg-blue-100">
                                            <i data-lucide="printer" class="w-3.5 h-3.5"></i> Kuitansi
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada catatan riwayat iuran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@if($tagihanBulanIni && $tagihanBulanIni->status !== 'Lunas')
<!-- Modal Upload Bukti Transfer -->
<div id="modal-konfirmasi-bayar" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl mx-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="upload" class="w-5 h-5 text-blue-600"></i> Konfirmasi Pembayaran Iuran
            </h3>
            <button onclick="document.getElementById('modal-konfirmasi-bayar').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('tagihan.bayar', $tagihanBulanIni->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pembayaran *</label>
                <select name="metode_pembayaran" required class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="Transfer Bank">Transfer Bank (BCA / Mandiri / BJB)</option>
                    <option value="QRIS">QRIS Standar RW 013</option>
                    <option value="Tunai">Tunai ke Bendahara RT</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Upload Bukti Transfer / Struk *</label>
                <input type="file" name="bukti_pembayaran" accept="image/*,application/pdf" 
                       class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#1456A0] hover:file:bg-blue-100">
                <p class="text-[11px] text-slate-400 mt-1">Maksimal 3 MB (JPG, PNG, atau PDF).</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="catatan_warga" rows="2" placeholder="Contoh: Sudah transfer via m-BCA a.n. John Doe" 
                          class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-konfirmasi-bayar').classList.add('hidden')" 
                        class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white rounded-xl shadow">
                    Kirim Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
