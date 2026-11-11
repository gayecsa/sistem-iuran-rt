<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Resmi RW 013 Sukamiskin - {{ $startDate }} s/d {{ $endDate }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .font-mono { font-family: 'Courier Prime', monospace; }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .print-container { box-shadow: none !important; border: none !important; width: 100% !important; max-width: 100% !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="py-6 px-4">
    <!-- Action Bar -->
    <div class="max-w-4xl mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ route('keuangan.laporan.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 inline-flex items-center gap-1">
            ← Kembali ke Menu Laporan
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-[#1456A0] hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Dokumen Laporan
        </button>
    </div>

    <!-- Print Container -->
    <div class="print-container max-w-4xl mx-auto bg-white rounded-2xl shadow-xl border border-slate-200 p-8 sm:p-12">
        <!-- Kop Surat RW 013 -->
        <div class="text-center pb-5 border-b-2 border-slate-900">
            <p class="text-xs font-bold tracking-widest uppercase text-slate-500">Pemerintah Daerah Kota Bandung • Kecamatan Arcamanik</p>
            <h1 class="text-xl font-black tracking-tight text-slate-900 uppercase">RUKUN WARGA 013 KELURAHAN SUKAMISKIN</h1>
            <p class="text-xs text-slate-600 mt-0.5">Sekretariat: Jl. Pacuan Kuda No. 13 RW 013 Sukamiskin, Arcamanik, Kota Bandung 40293</p>
            <div class="mt-2.5 inline-block px-4 py-1 bg-slate-900 text-white text-xs font-extrabold tracking-wider rounded uppercase">
                LAPORAN PERTANGGUNGJAWABAN PERBENDAHARAAN & ARUS KAS
            </div>
            <p class="text-xs text-slate-500 mt-2 font-medium">
                Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                @if($targetBuku)
                    • Entitas: <strong>{{ $targetBuku->nama_kas }}</strong>
                @else
                    • Konsolidasi: <strong>Seluruh Buku Kas RW 013</strong>
                @endif
            </p>
        </div>

        <!-- Ringkasan Keuangan -->
        <div class="mt-6 grid grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl">
                <span class="text-emerald-700 font-semibold uppercase text-[10px]">Total Penerimaan (Debit)</span>
                <p class="text-lg font-black text-emerald-800 mt-0.5">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl">
                <span class="text-rose-700 font-semibold uppercase text-[10px]">Total Pengeluaran (Kredit)</span>
                <p class="text-lg font-black text-rose-800 mt-0.5">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-xl">
                <span class="text-[#1456A0] font-semibold uppercase text-[10px]">Surplus / (Defisit)</span>
                <p class="text-lg font-black text-[#1456A0] mt-0.5">
                    Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Tabel Rincian Jurnal Transaksi -->
        <div class="mt-6 border border-slate-300 rounded-xl overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100 text-slate-800 font-bold border-b border-slate-300 text-[11px]">
                    <tr>
                        <th class="py-2.5 px-3">Tanggal</th>
                        <th class="py-2.5 px-3">No. Transaksi</th>
                        <th class="py-2.5 px-3">Buku Kas</th>
                        <th class="py-2.5 px-3">Kategori & Uraian</th>
                        <th class="py-2.5 px-3 text-right">Debit (Masuk)</th>
                        <th class="py-2.5 px-3 text-right">Kredit (Keluar)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium text-[11px]">
                    @forelse($transaksis as $trx)
                        <tr>
                            <td class="py-2 px-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d/m/Y') }}</td>
                            <td class="py-2 px-3 font-mono font-bold">{{ $trx->nomor_transaksi }}</td>
                            <td class="py-2 px-3 text-slate-600">{{ $trx->bukuKas->nama_kas ?? '-' }}</td>
                            <td class="py-2 px-3">
                                <strong>{{ $trx->kategori }}</strong>
                                <div class="text-[10px] text-slate-500">{{ $trx->keterangan }}</div>
                            </td>
                            <td class="py-2 px-3 text-right font-bold text-emerald-700 whitespace-nowrap">
                                {{ in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']) ? 'Rp ' . number_format($trx->nominal, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-2 px-3 text-right font-bold text-rose-700 whitespace-nowrap">
                                {{ in_array($trx->jenis_transaksi, ['pengeluaran', 'transfer_keluar']) ? 'Rp ' . number_format($trx->nominal, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Tidak ada transaksi keuangan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-100 font-black border-t-2 border-slate-400 text-slate-900 text-[11px]">
                    <tr>
                        <td colspan="4" class="py-2.5 px-3 text-right uppercase">TOTAL ARUS KAS:</td>
                        <td class="py-2.5 px-3 text-right text-emerald-800">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-3 text-right text-rose-800">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Kolom Tanda Tangan Resmi -->
        <div class="mt-12 pt-6 grid grid-cols-2 gap-8 text-xs text-center">
            <div>
                <p class="text-slate-500">Mengetahui & Menyetujui,</p>
                <p class="font-bold text-slate-800 mt-0.5">Ketua RW 013 Sukamiskin</p>
                <div class="h-20 flex items-center justify-center">
                    <span class="text-sm font-serif italic text-blue-900 font-bold underline">Gayeca</span>
                </div>
                <p class="font-bold text-slate-900 underline text-sm">Gayeca</p>
                <p class="text-[10px] text-slate-400">Ketua RW 013 Arcamanik</p>
            </div>

            <div>
                <p class="text-slate-500">Sukamiskin, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-800 mt-0.5">Bendahara Perbendaharaan RW 013</p>
                <div class="h-20 flex items-center justify-center">
                    <span class="text-sm font-serif italic text-slate-700 font-bold underline">Pengurus Keuangan RW</span>
                </div>
                <p class="font-bold text-slate-900 underline text-sm">Bendahara RW 013</p>
                <p class="text-[10px] text-slate-400">Satgas Keuangan Terpadu</p>
            </div>
        </div>
    </div>
</body>
</html>
