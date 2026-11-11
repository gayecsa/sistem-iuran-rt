<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Iuran - {{ $tagihan->nomor_tagihan }} - RW 013 Sukamiskin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .font-mono { font-family: 'Courier Prime', monospace; }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .print-container { box-shadow: none !important; border: 1px solid #cbd5e1 !important; margin: 0 auto; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="py-8 px-4">
    <!-- Action Bar -->
    <div class="max-w-2xl mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ route('tagihan.show', $tagihan->id) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 inline-flex items-center gap-1">
            ← Kembali ke Detail Tagihan
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-[#1456A0] hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Kuitansi Resmi
        </button>
    </div>

    <!-- Container Kuitansi -->
    <div class="print-container max-w-2xl mx-auto bg-white rounded-2xl shadow-xl border border-slate-200 p-8 relative overflow-hidden">
        <!-- Stempel Watermark Lunas -->
        <div class="absolute right-8 top-28 opacity-15 pointer-events-none rotate-[-15deg] border-4 border-emerald-600 text-emerald-700 font-black text-4xl px-6 py-2 rounded-xl uppercase tracking-widest">
            LUNAS SAH
        </div>

        <!-- Kop Surat RW 013 -->
        <div class="text-center pb-5 border-b-2 border-slate-900">
            <p class="text-[11px] font-bold tracking-widest uppercase text-slate-500">Pemerintah Kota Bandung • Kecamatan Arcamanik</p>
            <h1 class="text-lg font-black tracking-tight text-slate-900 uppercase">RUKUN WARGA 013 KELURAHAN SUKAMISKIN</h1>
            <p class="text-xs text-slate-600 mt-0.5">Sekretariat: Jl. Pacuan Kuda No. 13 RW 013 Sukamiskin Arcamanik, Kota Bandung 40293</p>
            <div class="mt-2 inline-block px-3 py-0.5 bg-slate-900 text-white text-[11px] font-bold tracking-wider rounded uppercase">
                TANDA BUKTI PEMBAYARAN IURAN WARGA (KUITANSI SAH)
            </div>
        </div>

        <!-- Header Kuitansi -->
        <div class="mt-5 grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-semibold text-[10px] uppercase">Nomor Kuitansi / Bukti:</span>
                <p class="font-mono font-bold text-slate-800 text-sm mt-0.5">{{ $tagihan->nomor_tagihan }}</p>
                <p class="text-[11px] text-slate-500 mt-1">
                    Periode: <strong>{{ \Carbon\Carbon::parse($tagihan->periode . '-01')->translatedFormat('F Y') }}</strong>
                </p>
            </div>
            <div class="text-right">
                <span class="text-slate-400 font-semibold text-[10px] uppercase">Tanggal Pembayaran:</span>
                <p class="font-bold text-slate-800 mt-0.5">
                    {{ $tagihan->tanggal_lunas ? $tagihan->tanggal_lunas->translatedFormat('d F Y') : \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
                <span class="inline-block mt-1 px-2.5 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 uppercase border border-emerald-300">
                    Status: Lunas
                </span>
            </div>
        </div>

        <!-- Identitas Wajib Iuran -->
        <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
            <div class="flex justify-between">
                <span class="text-slate-500">Telah Diterima Dari:</span>
                <strong class="text-slate-900 text-sm">{{ $tagihan->kartuKeluarga->nama_kepala_keluarga ?? '-' }}</strong>
            </div>
            <div class="flex justify-between font-mono text-[11px]">
                <span class="text-slate-500">Nomor Kartu Keluarga (KK):</span>
                <span class="text-slate-700">{{ $tagihan->kartuKeluarga->masked_no_kk ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Alamat / Wilayah:</span>
                <span class="text-slate-700 font-medium">RT {{ $tagihan->clean_rt }} / RW 013, {{ $tagihan->kartuKeluarga->alamat ?? '-' }} No. {{ $tagihan->kartuKeluarga->nomor_rumah ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Metode Pembayaran:</span>
                <span class="text-slate-800 font-bold uppercase">{{ $tagihan->metode_pembayaran_terakhir ?: 'Transfer Bank' }}</span>
            </div>
        </div>

        <!-- Tabel Rincian -->
        <div class="mt-5 border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100 text-slate-700 font-bold">
                    <tr>
                        <th class="py-2.5 px-3">No</th>
                        <th class="py-2.5 px-3">Komponen Iuran Lingkungan</th>
                        <th class="py-2.5 px-3 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-2 px-3 text-slate-400">1</td>
                        <td class="py-2 px-3">Iuran Kas Operasional RT {{ $tagihan->clean_rt }}</td>
                        <td class="py-2 px-3 text-right font-bold text-slate-800">Rp {{ number_format($tagihan->nominal_rt, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 text-slate-400">2</td>
                        <td class="py-2 px-3">Iuran Pemeliharaan Kawasan Kas RW 013</td>
                        <td class="py-2 px-3 text-right font-bold text-slate-800">Rp {{ number_format($tagihan->nominal_rw, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 text-slate-400">3</td>
                        <td class="py-2 px-3">Retribusi Pengangkutan Kebersihan & Sampah</td>
                        <td class="py-2 px-3 text-right font-bold text-slate-800">Rp {{ number_format($tagihan->nominal_sampah, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
                <tfoot class="bg-slate-50 font-black border-t-2 border-slate-300 text-slate-900">
                    <tr>
                        <td colspan="2" class="py-2.5 px-3 text-right text-xs uppercase tracking-wider">TOTAL YANG DIBAYAR:</td>
                        <td class="py-2.5 px-3 text-right text-base text-[#1456A0]">
                            Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-2 text-[11px] text-slate-500 italic">
            Terbilang: <strong>Empat Puluh Lima Ribu Rupiah</strong>
        </div>

        <!-- Tanda Tangan & QR Validasi -->
        <div class="mt-8 pt-6 border-t border-slate-200 grid grid-cols-2 gap-6 items-end text-xs">
            <div class="flex items-center gap-3">
                <div class="w-20 h-20 bg-slate-100 border border-slate-300 rounded-lg p-1.5 flex flex-col items-center justify-center text-center">
                    <span class="text-[9px] font-mono font-bold text-slate-500">QR VALIDASI</span>
                    <span class="text-[8px] text-slate-400 font-mono mt-1">RW013-{{ substr(md5($tagihan->id . $tagihan->nomor_tagihan), 0, 8) }}</span>
                    <span class="text-[7px] text-emerald-700 font-bold mt-1">VERIFIED</span>
                </div>
                <div class="text-[10px] text-slate-500">
                    <p class="font-bold text-slate-700">Validasi Digital Sistem</p>
                    <p>Kuitansi ini diterbitkan secara otomatis dan sah tanpa tanda tangan basah.</p>
                </div>
            </div>

            <div class="text-right">
                <p class="text-slate-500 text-[11px]">Sukamiskin, {{ $tagihan->tanggal_lunas ? $tagihan->tanggal_lunas->translatedFormat('d F Y') : \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-800 text-[11px] mt-0.5">Bendahara RW 013 / Petugas Penerima</p>
                <div class="h-12 flex items-center justify-end">
                    <span class="text-xs font-serif italic text-blue-900 font-bold underline">Gayeca / Pengurus RW</span>
                </div>
                <p class="font-bold text-slate-900 text-xs">Gayeca</p>
                <p class="text-[10px] text-slate-400">Ketua RW 013 Kel. Sukamiskin</p>
            </div>
        </div>
    </div>
</body>
</html>
