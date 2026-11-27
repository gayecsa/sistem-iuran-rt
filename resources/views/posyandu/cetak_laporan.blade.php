<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kegiatan Posyandu RT {{ $laporan->rt_number }} - RW 013 Suryatmajan</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
            font-size: 12pt;
            line-height: 1.5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .kop-surat h4 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            font-weight: normal;
        }
        .kop-surat h3 {
            margin: 2px 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-surat p {
            margin: 2px 0;
            font-size: 10pt;
            font-style: italic;
        }
        .judul-dokumen {
            text-align: center;
            margin-bottom: 25px;
        }
        .judul-dokumen h2 {
            margin: 0;
            font-size: 14pt;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .judul-dokumen span {
            font-size: 11pt;
            display: block;
            margin-top: 4px;
        }
        table.info-meta {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        table.info-meta td {
            padding: 4px 6px;
            vertical-align: top;
        }
        table.data-rekap {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 25px 0;
        }
        table.data-rekap th, table.data-rekap td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
        }
        table.data-rekap th {
            background-color: #f2f2f2;
            text-align: center;
            font-size: 11pt;
        }
        .section-title {
            font-weight: bold;
            font-size: 12pt;
            margin-top: 15px;
            margin-bottom: 6px;
        }
        .isi-teks {
            text-align: justify;
            margin-bottom: 15px;
        }
        .catatan-evaluasi {
            border: 1px dashed #444;
            padding: 10px 14px;
            margin-bottom: 20px;
            background: #fafafa;
        }
        .ttd-container {
            margin-top: 40px;
            width: 100%;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .ttd-table td {
            width: 33.33%;
            vertical-align: top;
            padding: 0 10px;
        }
        .ttd-space {
            height: 70px;
        }
        .no-print-bar {
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 10px 20px;
            text-align: right;
            margin-bottom: 20px;
        }
        .btn-print {
            background: #10B981;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            font-size: 13px;
        }
        @media print {
            .no-print-bar {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <button class="btn-print" onclick="window.print()">Cetak Dokumen Laporan</button>
    </div>

    <div class="container">
        <!-- KOP SURAT RESMI RW 013 -->
        <div class="kop-surat">
            <h4>RUKUN WARGA 013</h4>
            <h3>KELURAHAN Suryatmajan KECAMATAN Danurejan</h3>
            <h3>Kota Yogyakarta</h3>
            <p>Sekretariat: Pos Balai Warga RW 013, Jl. Mataram No. 15, Suryatmajan, Danurejan, Kota Yogyakarta 55213</p>
        </div>

        <!-- JUDUL DOKUMEN -->
        <div class="judul-dokumen">
            <h2>LAPORAN KEGIATAN OPERASIONAL POSYANDU</h2>
            <span>Nomor Dokumen: {{ $laporan->nomor_laporan }}</span>
        </div>

        <!-- INFO PELAKSANAAN -->
        <table class="info-meta">
            <tr>
                <td style="width: 25%;"><strong>Wilayah Penyelenggara</strong></td>
                <td style="width: 2%;">:</td>
                <td>Rukun Tetangga (RT) {{ $laporan->rt_number }} / RW 013 Suryatmajan</td>
            </tr>
            <tr>
                <td><strong>Nama Unit Posyandu</strong></td>
                <td>:</td>
                <td>{{ $laporan->jadwal->unit->nama_posyandu ?? 'Posyandu Melati 13 Unit RT ' . $laporan->rt_number }}</td>
            </tr>
            <tr>
                <td><strong>Kegiatan / Tema</strong></td>
                <td>:</td>
                <td>{{ $laporan->jadwal->judul_kegiatan ?? 'Posyandu Rutin Bulanan' }}</td>
            </tr>
            <tr>
                <td><strong>Hari / Tanggal Pelaksanaan</strong></td>
                <td>:</td>
                <td>{{ $laporan->tanggal_laporan_formatted }}</td>
            </tr>
            <tr>
                <td><strong>Lokasi Pelayanan</strong></td>
                <td>:</td>
                <td>{{ $laporan->jadwal->lokasi ?? 'Balai Warga RT ' . $laporan->rt_number }}</td>
            </tr>
            <tr>
                <td><strong>Mitra Tenaga Medis</strong></td>
                <td>:</td>
                <td>{{ $laporan->jadwal->mitra_nakes ?? 'UPTD Puskesmas Danurejan Kota Yogyakarta' }}</td>
            </tr>
        </table>

        <!-- REKAPITULASI KEHADIRAN & LAYANAN -->
        <div class="section-title">I. REKAPITULASI KEHADIRAN & SASARAN PESERTA</div>
        <table class="data-rekap">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th>Kelompok Sasaran</th>
                    <th style="width: 25%;">Jumlah Terlayani</th>
                    <th>Bentuk Pelayanan Terlaksana</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>Bayi & Balita (0 - 5 Tahun)</td>
                    <td style="text-align: center;"><strong>{{ $laporan->total_bayi_balita }}</strong> Jiwa</td>
                    <td>Penimbangan BB, Pengukuran TB/PB, Lingkar Kepala, PMT Biskuit Gizi</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td>Ibu Hamil & Menyusui</td>
                    <td style="text-align: center;"><strong>{{ $laporan->total_ibu_hamil }}</strong> Jiwa</td>
                    <td>Pemeriksaan Berat Badan, Tensi Darah, Konsultasi Nutrisi Kehamilan</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td>Lansia (60+ Tahun)</td>
                    <td style="text-align: center;"><strong>{{ $laporan->total_lansia }}</strong> Jiwa</td>
                    <td>Skrining Tanda Vital, Pemeriksaan Tensi Darah, Senam Kebugaran</td>
                </tr>
                <tr style="background: #f9f9f9; font-weight: bold;">
                    <td colspan="2" style="text-align: right;">TOTAL WARGA HADIR</td>
                    <td style="text-align: center;">{{ $laporan->total_hadir }} Jiwa</td>
                    <td>Pelayanan Terpadu Terverifikasi</td>
                </tr>
            </tbody>
        </table>

        <!-- RINGKASAN PELAKSANAAN -->
        <div class="section-title">II. RINGKASAN PELAKSANAAN KEGIATAN</div>
        <p class="isi-teks">{{ $laporan->ringkasan_layanan }}</p>

        <!-- KENDALA & EVALUASI -->
        <div class="section-title">III. KENDALA & EVALUASI OPERASIONAL RT</div>
        <p class="isi-teks">
            <strong>Kendala:</strong> {{ $laporan->kendala ?: 'Pelaksanaan berjalan kondusif tanpa kendala fasilitas yang menghambat.' }}<br>
            <strong>Evaluasi RT:</strong> {{ $laporan->catatan_evaluasi_rt ?: 'Tingkat kesadaran warga hadir ke posyandu sangat baik.' }}
        </p>

        <!-- EVALUASI DARI PENGURUS RW -->
        @if($laporan->catatan_rw)
            <div class="section-title">IV. CATATAN EVALUASI & PERSETUJUAN RW 013</div>
            <div class="catatan-evaluasi">
                <em>"{{ $laporan->catatan_rw }}"</em><br>
                <small>Status: <strong>{{ $laporan->status_label }}</strong> &bull; Diverifikasi pada: {{ $laporan->tanggal_evaluasi_rw ? Carbon\Carbon::parse($laporan->tanggal_evaluasi_rw)->translatedFormat('d F Y') : '-' }}</small>
            </div>
        @endif

        <!-- TANDA TANGAN RESMI -->
        <div class="ttd-container">
            <table class="ttd-table">
                <tr>
                    <td>
                        Mengetahui,<br>
                        <strong>Ketua RW 013 Suryatmajan</strong>
                        <div class="ttd-space"></div>
                        <strong>( {{ \App\Models\User::where('role', 'rw')->value('name') ?? (Auth::user()->role === 'rw' ? Auth::user()->name : 'Ketua RW 013') }} )</strong>
                    </td>
                    <td>
                        Penanggung Jawab RT,<br>
                        <strong>Ketua RT {{ $laporan->rt_number }}</strong>
                        <div class="ttd-space"></div>
                        <strong>( {{ $laporan->penanggungJawab->name ?? 'Pengurus RT' }} )</strong>
                    </td>
                    <td>
                        Kader Pelaksana,<br>
                        <strong>Ketua Kader Posyandu</strong>
                        <div class="ttd-space"></div>
                        <strong>( Bidan / Kader Meja 1 )</strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
