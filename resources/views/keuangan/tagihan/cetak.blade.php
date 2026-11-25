<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Tagihan & Realisasi Iuran Warga - Periode {{ $periode }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 10mm 15mm 10mm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 20px;
            font-size: 11px;
            line-height: 1.4;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-kop h2 {
            margin: 0 0 4px 0;
            font-size: 16px;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .header-kop h3 {
            margin: 0 0 4px 0;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }
        .header-kop p {
            margin: 0;
            font-size: 10px;
            color: #64748b;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
            background: #f8fafc;
            padding: 10px 14px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .summary-boxes {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }
        .summary-box {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }
        .summary-box .label {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .summary-box .value {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #334155;
            font-size: 10px;
            text-transform: uppercase;
        }
        tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-lunas { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-belum { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-sebagian { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-menunggu { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .footer-ttd {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .ttd-box {
            text-align: center;
            width: 200px;
        }
        .ttd-line {
            margin-top: 60px;
            border-bottom: 1px solid #334155;
            font-weight: bold;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #1456A0; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="header-kop">
        <h2>Pemerintah Kota Bandung • Kecamatan Arcamanik</h2>
        <h3>Rukun Warga 013 Sukamiskin • Rekapitulasi Tagihan & Realisasi Iuran Warga</h3>
        <p>Sekretariat: Jl. Pacantel No. 12, RW 013 Sukamiskin, Arcamanik, Bandung 40293 • Warkas Machi Integrated System</p>
    </div>

    <div class="meta-info">
        <div>
            <strong>Periode Tagihan:</strong> {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}<br>
            <strong>Wilayah Terlapor:</strong> {{ $rtFilter ? 'RT ' . $rtFilter : 'Seluruh RT (RT 001 - RT 008)' }}
        </div>
        <div style="text-align: right;">
            <strong>Dicetak Pada:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB<br>
            <strong>Petugas Pencetak:</strong> {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})
        </div>
    </div>

    <div class="summary-boxes">
        <div class="summary-box">
            <div class="label">Total Target Tagihan</div>
            <div class="value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box" style="border-left: 3px solid #16a34a;">
            <div class="label" style="color: #16a34a;">Sudah Diterima (Lunas/Alokasi)</div>
            <div class="value" style="color: #16a34a;">Rp {{ number_format($totalBayar, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box" style="border-left: 3px solid #dc2626;">
            <div class="label" style="color: #dc2626;">Sisa Tagihan / Piutang</div>
            <div class="value" style="color: #dc2626;">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box" style="border-left: 3px solid #2563eb;">
            <div class="label" style="color: #2563eb;">Tingkat Kolektibilitas</div>
            <div class="value" style="color: #2563eb;">{{ $pctKolektibilitas }}%</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 120px;">No Tagihan</th>
                <th style="width: 120px;">No KK</th>
                <th>Kepala Keluarga</th>
                <th class="text-center" style="width: 50px;">RT</th>
                <th class="text-right" style="width: 75px;">Porsi RT</th>
                <th class="text-right" style="width: 75px;">Porsi RW</th>
                <th class="text-right" style="width: 75px;">Sampah</th>
                <th class="text-right" style="width: 85px;">Total Tagihan</th>
                <th class="text-right" style="width: 85px;">Sudah Dibayar</th>
                <th class="text-right" style="width: 85px;">Sisa Tagihan</th>
                <th class="text-center" style="width: 90px;">Status</th>
                <th class="text-center" style="width: 80px;">Tgl Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tagihans as $idx => $t)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $t->nomor_tagihan }}</td>
                    <td style="font-family: monospace;">{{ $t->kartuKeluarga->no_kk ?? '-' }}</td>
                    <td><strong>{{ $t->kartuKeluarga->nama_kepala_keluarga ?? ($t->kepalaKeluarga->name ?? 'Warga') }}</strong></td>
                    <td class="text-center">RT {{ $t->clean_rt }}</td>
                    <td class="text-right">Rp {{ number_format($t->nominal_rt, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($t->nominal_rw, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($t->nominal_sampah, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}</strong></td>
                    <td class="text-right" style="color: #166534;">Rp {{ number_format($t->total_dibayar, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #991b1b;">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($t->status === 'Lunas')
                            <span class="badge badge-lunas">LUNAS</span>
                        @elseif($t->status === 'Dibayar Sebagian')
                            <span class="badge badge-sebagian">SEBAGIAN</span>
                        @elseif($t->status === 'Menunggu Konfirmasi')
                            <span class="badge badge-menunggu">MENUNGGU</span>
                        @else
                            <span class="badge badge-belum">BELUM BAYAR</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $t->tanggal_lunas ? $t->tanggal_lunas->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data tagihan pada periode dan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL KESELURUHAN:</td>
                <td class="text-right">Rp {{ number_format($tagihans->sum('nominal_rt'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($tagihans->sum('nominal_rw'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($tagihans->sum('nominal_sampah'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #166534;">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #991b1b;">Rp {{ number_format($totalSisa, 0, ',', '.') }}</td>
                <td colspan="2" class="text-center">Kolektibilitas: {{ $pctKolektibilitas }}%</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-ttd">
        <div class="ttd-box">
            Mengetahui,<br>
            <strong>Ketua RT {{ $rtFilter ?: 'Terkait' }}</strong>
            <div class="ttd-line">Pengurus Rukun Tetangga</div>
        </div>
        <div class="ttd-box">
            Bandung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
            <strong>Ketua RW 013 Sukamiskin</strong>
            <div class="ttd-line">Pengurus Rukun Warga</div>
        </div>
    </div>
</body>
</html>
