@extends('layouts.app')

@section('title', 'Detail Tagihan - ' . $tagihan->nomor_tagihan . ' - Warkas Machi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('keuangan.dashboard') }}" class="hover:underline">Dashboard Keuangan</a>
        <span>/</span>
        <a href="{{ route('tagihan.index') }}" class="hover:underline">Tagihan Iuran</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">{{ $tagihan->nomor_tagihan }}</span>
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

    <!-- Card Lembar Tagihan Resmi -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
        <!-- Header Tagihan -->
        <div class="p-6 border-b border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded bg-blue-100 text-[#1456A0]">
                        {{ $tagihan->nomor_tagihan }}
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700">
                        Periode {{ \Carbon\Carbon::parse($tagihan->periode . '-01')->translatedFormat('F Y') }}
                    </span>
                </div>
                <h1 class="text-xl font-black text-slate-800">Lembar Tagihan Iuran Warga Terpadu</h1>
                <p class="text-xs text-slate-500">Diterbitkan oleh Pengurus RW 013 Kelurahan Sukamiskin Arcamanik</p>
            </div>

            <div class="text-right">
                <span class="px-3.5 py-1.5 text-xs font-bold rounded-full border {{ $tagihan->badge_class }} inline-block">
                    {{ $tagihan->status }}
                </span>
                <p class="text-[11px] text-slate-400 mt-1.5">
                    Jatuh Tempo: {{ \Carbon\Carbon::parse($tagihan->jatuh_tempo)->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Informasi Keluarga Wajib Iuran -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <div>
                    <span class="text-slate-400 font-semibold uppercase text-[10px]">Data Kartu Keluarga</span>
                    <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $tagihan->kartuKeluarga->nama_kepala_keluarga ?? '-' }}</p>
                    <p class="font-mono text-slate-600 mt-0.5">No. KK: {{ $tagihan->kartuKeluarga->masked_no_kk ?? '-' }}</p>
                    <p class="text-slate-500 mt-0.5">{{ $tagihan->kartuKeluarga->total_anggota ?? 0 }} Anggota Keluarga Terdaftar</p>
                </div>

                <div>
                    <span class="text-slate-400 font-semibold uppercase text-[10px]">Alamat & Wilayah</span>
                    <p class="text-sm font-bold text-slate-800 mt-0.5">RT {{ $tagihan->clean_rt }} / RW 013</p>
                    <p class="text-slate-600 mt-0.5">{{ $tagihan->kartuKeluarga->alamat ?? '-' }} No. {{ $tagihan->kartuKeluarga->nomor_rumah ?? '-' }}</p>
                    <p class="text-slate-500 mt-0.5">Kelurahan Sukamiskin, Kec. Arcamanik, Bandung</p>
                </div>
            </div>

            <!-- Tabel Rincian Tagihan -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Komponen & Rincian Iuran</h3>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-100/80 font-bold text-slate-700">
                            <tr>
                                <th class="py-2.5 px-4">No</th>
                                <th class="py-2.5 px-4">Komponen Layanan</th>
                                <th class="py-2.5 px-4">Tujuan Akun Kas</th>
                                <th class="py-2.5 px-4 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            <tr>
                                <td class="py-2.5 px-4 text-slate-400">1</td>
                                <td class="py-2.5 px-4">
                                    <strong class="text-slate-800">Iuran Operasional RT {{ $tagihan->clean_rt }}</strong>
                                    <p class="text-[11px] text-slate-400">Pemeliharaan jalan lingkungan, konsumsi kerja bakti, ATK RT</p>
                                </td>
                                <td class="py-2.5 px-4 text-slate-600">Buku Kas RT {{ $tagihan->clean_rt }}</td>
                                <td class="py-2.5 px-4 text-right font-bold text-slate-800">Rp {{ number_format($tagihan->nominal_rt, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 px-4 text-slate-400">2</td>
                                <td class="py-2.5 px-4">
                                    <strong class="text-slate-800">Iuran Sentral Kas RW 013</strong>
                                    <p class="text-[11px] text-slate-400">Honor keamanan 24 jam, pemeliharaan CCTV kawasan, operasional balai RW</p>
                                </td>
                                <td class="py-2.5 px-4 text-slate-600">Kas Induk RW 013</td>
                                <td class="py-2.5 px-4 text-right font-bold text-slate-800">Rp {{ number_format($tagihan->nominal_rw, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 px-4 text-slate-400">3</td>
                                <td class="py-2.5 px-4">
                                    <strong class="text-slate-800">Retribusi Kebersihan & Pengangkutan Sampah</strong>
                                    <p class="text-[11px] text-slate-400">Petugas motor cator sampah door-to-door dan retribusi TPS Sukamiskin</p>
                                </td>
                                <td class="py-2.5 px-4 text-slate-600">Kas Sampah RW 013</td>
                                <td class="py-2.5 px-4 text-right font-bold text-slate-800">Rp {{ number_format($tagihan->nominal_sampah, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-50 font-extrabold text-slate-900 border-t border-slate-200">
                            <tr>
                                <td colspan="3" class="py-3 px-4 text-right">TOTAL TAGIHAN KELUARGA:</td>
                                <td class="py-3 px-4 text-right text-base text-[#1456A0]">
                                    Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Bagian Bukti Transfer Warga Jika Ada -->
            @if($tagihan->bukti_pembayaran_path || $tagihan->catatan_warga)
                <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-200 text-xs space-y-2">
                    <span class="font-bold text-amber-900 uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                        <i data-lucide="receipt" class="w-3.5 h-3.5 text-amber-600"></i> Informasi Pengajuan Pembayaran Warga
                    </span>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <p class="text-slate-700"><strong>Metode:</strong> {{ $tagihan->metode_pembayaran_terakhir ?: '-' }}</p>
                            @if($tagihan->catatan_warga)
                                <p class="text-slate-600 mt-0.5"><strong>Catatan Warga:</strong> {{ $tagihan->catatan_warga }}</p>
                            @endif
                        </div>
                        @if($tagihan->bukti_pembayaran_path)
                            <a href="{{ asset('storage/' . $tagihan->bukti_pembayaran_path) }}" target="_blank" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-amber-300 text-amber-900 font-semibold hover:bg-amber-100 shadow-sm">
                                <i data-lucide="eye" class="w-4 h-4"></i> Buka Lampiran Bukti Transfer
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Rincian Mutasi Kas Terkait Jika Lunas -->
            @if($tagihan->status === 'Lunas' && $tagihan->transaksis->count() > 0)
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs">
                    <span class="font-bold text-emerald-900 uppercase text-[10px] tracking-wider flex items-center gap-1.5 mb-2">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i> Transaksi Kas Terverifikasi & Teralokasi
                    </span>
                    <div class="space-y-1.5">
                        @foreach($tagihan->transaksis as $trx)
                            <div class="flex justify-between items-center py-1 border-b border-emerald-100 text-[11px]">
                                <span class="font-mono font-bold text-emerald-800">{{ $trx->nomor_transaksi }}</span>
                                <span class="text-emerald-700">[{{ $trx->bukuKas->nama_kas ?? '-' }}] {{ $trx->kategori }}</span>
                                <span class="font-bold text-emerald-900">+Rp {{ number_format($trx->nominal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Panel Tombol Aksi Sesuai Role & Status -->
            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('tagihan.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    ← Kembali ke Daftar Tagihan
                </a>

                <div class="flex flex-wrap items-center gap-2">
                    @if($tagihan->status === 'Lunas')
                        <a href="{{ route('tagihan.kuitansi', $tagihan->id) }}" target="_blank" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow inline-flex items-center gap-2">
                            <i data-lucide="printer" class="w-4 h-4"></i> Cetak Kuitansi Sah
                        </a>
                    @else
                        <!-- Tombol Verifikasi (Untuk RW / RT) -->
                        @if($isRw || $isRt)
                            <button onclick="document.getElementById('modal-verifikasi-pembayaran').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white shadow inline-flex items-center gap-1.5">
                                <i data-lucide="check-square" class="w-4 h-4"></i> Verifikasi Pembayaran (Lunas)
                            </button>
                            <button onclick="document.getElementById('modal-bebaskan-tagihan').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200">
                                Bebaskan Iuran (SKTM)
                            </button>
                        @endif

                        <!-- Tombol Bayar bagi Warga -->
                        <button onclick="document.getElementById('modal-warga-bayar').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow inline-flex items-center gap-1.5">
                            <i data-lucide="upload" class="w-4 h-4"></i> Konfirmasi Pembayaran
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi Pengurus (RT / RW) -->
@if($isRw || $isRt)
<div id="modal-verifikasi-pembayaran" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl mx-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i> Verifikasi Pembayaran Lunas
            </h3>
            <button onclick="document.getElementById('modal-verifikasi-pembayaran').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('tagihan.verifikasi', $tagihan->id) }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-900">
                <p>Total Nominal Diterima: <strong>Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</strong></p>
                <p class="text-[11px] text-blue-700 mt-1">Sistem otomatis mendistribusikan dana ke Buku Kas RT {{ $tagihan->clean_rt }} (Rp 20.000), Kas RW (Rp 10.000), dan Kas Sampah (Rp 15.000).</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Penerimaan *</label>
                <select name="metode_verifikasi" required class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="Transfer Bank">Transfer Bank (Telah Dicek di Rekening)</option>
                    <option value="Tunai">Tunai (Diterima Langsung oleh Petugas)</option>
                    <option value="QRIS">QRIS RW 013</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Verifikasi (Opsional)</label>
                <input type="text" name="catatan_petugas" placeholder="Contoh: Diterima lunas di kantor RT / rekening koran valid" 
                       class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-verifikasi-pembayaran').classList.add('hidden')" 
                        class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow">
                    Setujui Lunas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pembebasan Iuran (SKTM) -->
<div id="modal-bebaskan-tagihan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl mx-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-purple-700 flex items-center gap-2">
                <i data-lucide="shield-alert" class="w-5 h-5 text-purple-600"></i> Pembebasan Iuran Warga
            </h3>
            <button onclick="document.getElementById('modal-bebaskan-tagihan').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('tagihan.bebaskan', $tagihan->id) }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Pembebasan *</label>
                <textarea name="alasan_pembebasan" required rows="3" placeholder="Contoh: Warga lansia dhuafa / Memiliki Surat Keterangan Tidak Mampu (SKTM) Sukamiskin" 
                          class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-purple-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-bebaskan-tagihan').classList.add('hidden')" 
                        class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow">
                    Bebaskan Tagihan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Upload Warga -->
<div id="modal-warga-bayar" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl mx-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="upload" class="w-5 h-5 text-blue-600"></i> Konfirmasi Pembayaran
            </h3>
            <button onclick="document.getElementById('modal-warga-bayar').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('tagihan.bayar', $tagihan->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pembayaran *</label>
                <select name="metode_pembayaran" required class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="Transfer Bank">Transfer Bank (Mandiri / BCA / BJB)</option>
                    <option value="Tunai">Tunai ke Bendahara RT</option>
                    <option value="QRIS">QRIS RW 013</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Upload Bukti Transfer (Opsional jika tunai)</label>
                <input type="file" name="bukti_pembayaran" accept="image/*,application/pdf" 
                       class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#1456A0] hover:file:bg-blue-100">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                <input type="text" name="catatan_warga" placeholder="Keterangan pengirim..." 
                       class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-warga-bayar').classList.add('hidden')" 
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
@endsection
