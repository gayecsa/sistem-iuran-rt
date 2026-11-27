<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Audit (LHA) RW 013 - Tahun {{ $tahun }}</title>
    <style>
        /* ========================================================
           SCREEN & BASE STYLES
           ======================================================== */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
            font-size: 13px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Screen toolbar */
        .no-print-bar {
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.25);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .no-print-bar .bar-title {
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .no-print-bar .bar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-act {
            padding: 7px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-primary-print {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-primary-print:hover {
            background: #1d4ed8;
        }
        .btn-secondary-back {
            background: #334155;
            color: #e2e8f0;
        }
        .btn-secondary-back:hover {
            background: #475569;
            color: #ffffff;
        }

        /* Printable Sheet Paper */
        .paper-container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 24px auto;
            background: #ffffff;
            padding: 20mm 20mm 22mm 20mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 4px;
        }

        /* Kop Dokumen Resmi */
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000000;
            padding-bottom: 12px;
            margin-bottom: 22px;
            position: relative;
        }
        .header-kop .instansi-1 {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            color: #000000;
        }
        .header-kop .instansi-2 {
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin: 2px 0;
            color: #000000;
        }
        .header-kop .instansi-3 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            color: #000000;
        }
        .header-kop .alamat-kop {
            font-size: 11px;
            font-style: italic;
            margin-top: 5px;
            margin-bottom: 0;
            color: #1f2937;
        }

        /* Judul Dokumen */
        .judul-dokumen {
            text-align: center;
            margin-bottom: 22px;
        }
        .judul-dokumen h3 {
            margin: 0;
            font-size: 16px;
            text-decoration: underline;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #000000;
        }
        .judul-dokumen .nomor-surat {
            font-size: 12px;
            color: #111827;
            margin-top: 4px;
            display: block;
        }

        /* Section Headings */
        h4.section-title {
            font-size: 13.5px;
            font-weight: bold;
            margin-top: 22px;
            margin-bottom: 8px;
            border-bottom: 1px solid #000000;
            padding-bottom: 4px;
            text-transform: uppercase;
            color: #000000;
            page-break-after: avoid;
            break-after: avoid;
        }

        p.paragraf-isi {
            margin-top: 0;
            margin-bottom: 10px;
            text-align: justify;
            color: #111827;
            font-size: 12.5px;
            line-height: 1.5;
        }

        /* Tabel Standar Cetak */
        table.lha-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 11.5px;
            line-height: 1.4;
            color: #000000;
            page-break-inside: auto;
        }
        table.lha-table th, 
        table.lha-table td {
            border: 1px solid #1f2937;
            padding: 6px 8px;
            vertical-align: top;
            color: #000000 !important;
        }
        table.lha-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: left;
            color: #000000 !important;
        }
        table.lha-table tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        table.lha-table thead {
            display: table-header-group;
        }
        table.lha-table tfoot {
            display: table-footer-group;
        }
        .text-center { text-align: center !important; }
        .text-end { text-align: right !important; }
        .fw-bold { font-weight: bold !important; }

        .row-total {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 2px solid #000000;
        }

        /* Tanda Tangan */
        .tanda-tangan-wrapper {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .ttd-box {
            text-align: center;
            width: 280px;
            color: #000000;
            font-size: 12.5px;
            line-height: 1.4;
        }
        .ttd-space {
            height: 70px;
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
            color: #000000;
            font-size: 13px;
        }
        .ttd-jabatan {
            font-size: 12px;
            color: #111827;
            margin: 0;
        }

        /* ========================================================
           PRINT MEDIA SPECIFICATIONS
           ======================================================== */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 12px;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .paper-container {
                max-width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 15mm 15mm 15mm;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            table.lha-table th {
                background-color: #f3f4f6 !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
            }
            table.lha-table td {
                border: 1px solid #000000 !important;
                color: #000000 !important;
            }
            .header-kop {
                border-bottom: 3px double #000000 !important;
            }
            .tanda-tangan-wrapper {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- SCREEN NAVIGATION TOOLBAR (HIDDEN SAAT PRINT) -->
    <div class="no-print-bar">
        <div class="bar-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <span>Dokumen Resmi: Laporan Hasil Audit (LHA) RW 013 &bull; Tahun Anggaran {{ $tahun }}</span>
        </div>
        <div class="bar-actions">
            <a href="{{ route('rw.audit.index', ['tahun' => $tahun]) }}" class="btn-act btn-secondary-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Kembali ke Dashboard Audit
            </a>
            <button onclick="window.print()" class="btn-act btn-primary-print">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Cetak Dokumen / Simpan PDF
            </button>
        </div>
    </div>

    <!-- PRINTABLE PAPER CONTAINER -->
    <div class="paper-container">

        <!-- KOP RESMI PEMERINTAHAN RW 013 -->
        <div class="header-kop">
            <h2 class="instansi-1">PENGURUS RUKUN WARGA 013</h2>
            <h1 class="instansi-2">KELURAHAN Suryatmajan &bull; KECAMATAN Danurejan</h1>
            <h3 class="instansi-3">Kota Yogyakarta — PROVINSI DI Yogyakarta</h3>
            <p class="alamat-kop">Sekretariat: Balai Pertemuan Warga RW 013, Jl. Mataram No. 15, Suryatmajan, Danurejan, Kota Yogyakarta 55213 &bull; Pos-el: rw013.Suryatmajan@gmail.com</p>
        </div>

        <!-- JUDUL DAN NOMOR SURAT RESMI -->
        <div class="judul-dokumen">
            <h3>LAPORAN HASIL AUDIT & EVALUASI AKUNTABILITAS TAHUNAN</h3>
            <span class="nomor-surat">Nomor: 013/LHA-RW/SKM/{{ $tahun }} &bull; Periode Tahun Anggaran {{ $tahun }}</span>
        </div>

        <!-- 1. RINGKASAN EKSEKUTIF & LIKUIDITAS KAS -->
        <h4 class="section-title">I. Ringkasan Eksekutif & Likuiditas Kas RW 013</h4>
        <p class="paragraf-isi">
            Berdasarkan pemeriksaan komprehensif terhadap tata kelola pembukuan, realisasi program kerja, dan kepatuhan administrasi keuangan pada lingkungan RW 013 Suryatmajan yang membawahi 8 Rukun Tetangga (RT 001 s/d RT 008), posisi saldo likuid per tanggal {{ Carbon\Carbon::now()->translatedFormat('d F Y') }} tercatat sebagai berikut:
        </p>

        <table class="lha-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">No</th>
                    <th style="width: 45%;">Akun Buku Kas / Wilayah Pengelolaan</th>
                    <th style="width: 25%;">Kategori Rekening</th>
                    <th class="text-end" style="width: 25%;">Saldo Terkini</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bukuKasList as $idx => $bk)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="fw-bold">{{ $bk->nama_kas }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $bk->jenis_kas)) }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($bk->saldo_sekarang, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="font-style: italic; padding: 10px;">Tidak ada rekening kas aktif yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="row-total">
                    <td colspan="3" class="text-center fw-bold">TOTAL SALDO LIKUID SELURUH RW 013</td>
                    <td class="text-end fw-bold">Rp {{ number_format($totalSaldoSeluruh, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- 2. HASIL EVALUASI KEPATUHAN & PERTANGGUNGJAWABAN 8 RT -->
        <h4 class="section-title">II. Hasil Evaluasi Kepatuhan & Pertanggungjawaban 8 RT (RT 001 – RT 008)</h4>
        <p class="paragraf-isi">
            Rekapitulasi evaluasi akuntabilitas, ketertiban pelaporan bulanan, serta kepatuhan tata tertib keuangan dari seluruh unit RT di wilayah RW 013:
        </p>

        <table class="lha-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 8%;">Wilayah</th>
                    <th style="width: 18%;">Penanggung Jawab</th>
                    <th class="text-center" style="width: 10%;">Skor (0-100)</th>
                    <th class="text-center" style="width: 16%;">Predikat</th>
                    <th style="width: 24%;">Catatan Evaluasi Ketua RW</th>
                    <th style="width: 24%;">Rekomendasi Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scorecardRt as $sc)
                    <tr>
                        <td class="text-center fw-bold">{{ $sc['rt_name'] }}</td>
                        <td>{{ $sc['pic_name'] }}</td>
                        <td class="text-center fw-bold">{{ $sc['skor'] }}/100</td>
                        <td class="text-center fw-bold">{{ $sc['status_label'] }}</td>
                        <td>{{ $sc['catatan'] }}</td>
                        <td>{{ $sc['rekomendasi'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- 3. AUDIT REALISASI PROGRAM KERJA -->
        <h4 class="section-title">III. Audit Realisasi Program Kerja & Status Audit</h4>
        <p class="paragraf-isi">
            Evaluasi kepatuhan serapan anggaran terhadap rencana kerja kegiatan RW dan RT selama Tahun Anggaran {{ $tahun }}:
        </p>

        <table class="lha-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Nama Kegiatan / Proyek</th>
                    <th class="text-center" style="width: 10%;">Wilayah</th>
                    <th class="text-end" style="width: 18%;">Pagu Anggaran</th>
                    <th class="text-end" style="width: 18%;">Realisasi Pengeluaran</th>
                    <th class="text-center" style="width: 10%;">Serapan</th>
                    <th class="text-center" style="width: 14%;">Status Audit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarKegiatan as $kegiatan)
                    <tr>
                        <td class="fw-bold">{{ $kegiatan->nama_kegiatan }}</td>
                        <td class="text-center">{{ $kegiatan->clean_rt }}</td>
                        <td class="text-end">Rp {{ number_format($kegiatan->anggaran_awal, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($kegiatan->realisasi_anggaran, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $kegiatan->persentase_realisasi }}%</td>
                        <td class="text-center fw-bold">{{ ucfirst(str_replace('_', ' ', $kegiatan->status_audit)) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="font-style: italic; padding: 10px;">
                            Tidak ada program kerja khusus yang tercatat pada tahun anggaran {{ $tahun }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($daftarKegiatan) > 0)
                <tfoot>
                    <tr class="row-total">
                        <td colspan="2" class="text-center fw-bold">TOTAL PROGRAM KERJA</td>
                        <td class="text-end fw-bold">Rp {{ number_format($totalAnggaranKegiatan, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($totalRealisasiKegiatan, 0, ',', '.') }}</td>
                        <td colspan="2" class="text-center fw-bold">
                            {{ $totalAnggaranKegiatan > 0 ? round(($totalRealisasiKegiatan / $totalAnggaranKegiatan) * 100, 1) : 0 }}% Terpakai
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <!-- 4. REKAPITULASI TEMUAN AUDIT -->
        <h4 class="section-title">IV. Rekapitulasi Temuan Audit (Audit Findings)</h4>
        <p class="paragraf-isi">
            Daftar temuan hasil investigasi pembukuan, kelengkapan kuitansi/bukti transaksi, dan kepatuhan SOP per {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}:
        </p>

        <table class="lha-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 12%;">Kode</th>
                    <th style="width: 48%;">Judul & Uraian Temuan</th>
                    <th class="text-center" style="width: 10%;">Wilayah</th>
                    <th class="text-center" style="width: 12%;">Risiko</th>
                    <th class="text-center" style="width: 18%;">Status Perbaikan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarTemuan as $temuan)
                    <tr>
                        <td class="text-center fw-bold">{{ $temuan->kode_temuan }}</td>
                        <td>
                            <strong>{{ $temuan->judul_temuan }}</strong><br>
                            <span style="font-size: 11px; color: #374151;">{{ $temuan->deskripsi_masalah }}</span>
                            @if($temuan->catatan_evaluasi_rw)
                                <br><small style="color: #0369a1; font-style: italic;">Rekomendasi RW: {{ $temuan->catatan_evaluasi_rw }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $temuan->clean_rt }}</td>
                        <td class="text-center fw-bold">{{ $temuan->prioritas_badge['label'] }}</td>
                        <td class="text-center fw-bold">{{ $temuan->status_badge['label'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="font-style: italic; padding: 10px;">
                            Nihil — Tidak ditemukan penyimpangan atau temuan kepatuhan aktif pada periode audit ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- BLOK TANDA TANGAN RESMI -->
        <div class="tanda-tangan-wrapper">
            <div class="ttd-box">
                <p>Mengetahui / Memeriksa,<br><strong>Koordinator Tim Audit RW 013</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name"><u>( {{ (Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin') ? Auth::user()->name : 'Tim Auditor Internal RW 013' }} )</u></p>
                <p class="ttd-jabatan">Auditor Internal RW 013</p>
            </div>
            <div class="ttd-box">
                <p>Yogyakarta, {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}<br><strong>Ketua RW 013 Suryatmajan</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name"><u>( {{ $ketuaRw->name ?? $user->name ?? 'Pengurus RW 013' }} )</u></p>
                <p class="ttd-jabatan">Ketua RW 013</p>
            </div>
        </div>

    </div>

</body>
</html>
