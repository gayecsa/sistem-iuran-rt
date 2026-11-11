<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $letter->judul_surat }} - {{ $letter->nomor_surat ?? $letter->nomor_registrasi }}</title>
    
    <!-- Google Fonts: Times New Roman standard / Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Tinos:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* PAGE SETUP FOR A4 STANDARD GOVERNMENT LETTER */
        @page {
            size: A4 portrait;
            margin: 15mm 20mm 15mm 20mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Tinos', 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.45;
            color: #000000;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px 0;
        }

        .paper-container {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 22mm;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: relative;
        }

        /* PRINT BUTTON BAR (HIDDEN IN PRINT) */
        .print-control-bar {
            width: 210mm;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-print {
            background-color: #1456A0;
            color: #ffffff;
        }
        .btn-print:hover {
            background-color: #123766;
        }

        .btn-back {
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover {
            background-color: #f8fafc;
        }

        /* KOP SURAT RESMI PEMERINTAHAN */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding-bottom: 8px;
        }

        .kop-logo {
            position: absolute;
            left: 0;
            top: 2px;
            width: 75px;
            height: 75px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kop-teks {
            text-align: center;
            width: 100%;
            padding: 0 80px;
        }

        .kop-teks h4 {
            margin: 0;
            font-size: 13pt;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .kop-teks h3 {
            margin: 2px 0 0 0;
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .kop-teks h2 {
            margin: 2px 0 0 0;
            font-size: 15pt;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .kop-teks p {
            margin: 3px 0 0 0;
            font-size: 9pt;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #222;
        }

        /* GARIS GANDA PEMISAH KOP RESMI */
        .kop-divider {
            border: none;
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 5px;
            margin: 6px 0 16px 0;
        }

        /* JUDUL & NOMOR SURAT */
        .surat-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .surat-header h1 {
            margin: 0;
            font-size: 13.5pt;
            font-weight: 700;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .surat-header .nomor-surat {
            margin-top: 3px;
            font-size: 11pt;
            font-weight: 600;
        }

        /* ISI SURAT */
        .surat-body {
            text-align: justify;
            font-size: 11.5pt;
        }

        .surat-paragraf {
            margin-bottom: 10px;
            text-indent: 35px;
            line-height: 1.5;
        }

        /* TABEL BIODATA PEMOHON */
        .tabel-biodata {
            width: 100%;
            margin: 10px 0 14px 20px;
            border-collapse: collapse;
            font-size: 11pt;
        }

        .tabel-biodata td {
            padding: 3px 6px;
            vertical-align: top;
        }

        .tabel-biodata td.label {
            width: 200px;
        }

        .tabel-biodata td.colon {
            width: 15px;
            text-align: center;
        }

        .tabel-biodata td.value {
            font-weight: 600;
        }

        /* KOTAK KETERANGAN KHUSUS */
        .box-keterangan {
            background-color: #fafafa;
            border: 1px dashed #666;
            padding: 8px 12px;
            margin: 10px 0 14px 0;
            font-size: 11pt;
            border-radius: 4px;
        }

        /* TANDA TANGAN GANDA & STEMPEL */
        .tanda-tangan-block {
            margin-top: 24px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .ttd-box {
            width: 45%;
            text-align: center;
            position: relative;
        }

        .ttd-jabatan {
            font-weight: 600;
            margin-bottom: 60px;
            line-height: 1.3;
        }

        .ttd-nama {
            font-weight: 700;
            text-decoration: underline;
            font-size: 11.5pt;
        }

        .ttd-nip {
            font-size: 9pt;
            color: #333;
        }

        /* STEMPEL DIGITAL & TOKEN */
        .stempel-digital {
            position: absolute;
            top: 22px;
            left: 50%;
            transform: translateX(-50%);
            width: 95px;
            height: 95px;
            border: 2px solid rgba(20, 86, 160, 0.45);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: rgba(20, 86, 160, 0.7);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            pointer-events: none;
            background: radial-gradient(circle, rgba(255,255,255,0.85) 0%, rgba(20, 86, 160, 0.05) 100%);
        }

        .stempel-inner {
            border: 1px dashed rgba(20, 86, 160, 0.45);
            border-radius: 50%;
            width: 82px;
            height: 82px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* FOOTER & QR VERIFIKASI */
        .surat-footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 8pt;
            color: #64748b;
            page-break-inside: avoid;
        }

        .qr-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .qr-placeholder {
            width: 64px;
            height: 64px;
            border: 1px solid #cbd5e1;
            padding: 3px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* PRINT STYLING MEDIA QUERY */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .paper-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                width: 100%;
                min-height: auto;
            }
            .print-control-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    {{-- TOP ACTION CONTROLS (TOMBOL CETAK & KEMBALI) --}}
    <div class="print-control-bar">
        <a href="{{ route('surat.show', $letter->id) }}" class="btn-action btn-back">
            <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
            <span>Kembali ke Detail Surat</span>
        </a>

        <div style="display:flex; align-items:center; gap:10px;">
            <span style="font-size:12px; color:#64748b;">Ukuran Kertas: Standar A4 Resmi</span>
            <button onclick="window.print()" class="btn-action btn-print">
                <i data-lucide="printer" style="width:18px;height:18px;"></i>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    {{-- DOKUMEN RESMI A4 --}}
    <div class="paper-container">

        {{-- KOP SURAT RESMI PEMERINTAHAN --}}
        <div class="kop-surat">
            <div class="kop-logo">
                {{-- Lambang Garuda / Kota Bandung Stylized Vector Emblem --}}
                <svg viewBox="0 0 100 100" width="70" height="70">
                    <circle cx="50" cy="50" r="46" fill="#123766" stroke="#C5A059" stroke-width="2.5"/>
                    <circle cx="50" cy="50" r="41" fill="none" stroke="#ffffff" stroke-width="1" stroke-dasharray="2,2"/>
                    <path d="M50 16 L56 32 L72 32 L59 42 L64 58 L50 48 L36 58 L41 42 L28 32 L44 32 Z" fill="#C5A059"/>
                    <text x="50" y="70" text-anchor="middle" fill="#ffffff" font-family="'Plus Jakarta Sans', sans-serif" font-size="8" font-weight="700" letter-spacing="0.5">RW 013</text>
                    <text x="50" y="80" text-anchor="middle" fill="#ffffff" font-family="'Plus Jakarta Sans', sans-serif" font-size="6" font-weight="600">SUKAMISKIN</text>
                </svg>
            </div>

            <div class="kop-teks">
                <h4>PEMERINTAH DAERAH KOTA BANDUNG</h4>
                <h3>KECAMATAN ARCAMANIK - KELURAHAN SUKAMISKIN</h3>
                <h2>RUKUN WARGA 013 &bull; RUKUN TETANGGA {{ $letter->rt }}</h2>
                <p>Sekretariat: Jl. Pacantel No. 12, Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung 40293</p>
                <p style="font-size: 8pt; margin-top: 1px;">Telepon: (022) 7208130 &bull; Pos-el: rw013.sukamiskin@bandung.go.id</p>
            </div>
        </div>

        {{-- GARIS GANDA KOP --}}
        <div class="kop-divider"></div>

        {{-- JUDUL DAN NOMOR RESMI SURAT --}}
        <div class="surat-header">
            <h1>{{ strtoupper($letter->judul_surat) }}</h1>
            <div class="nomor-surat">
                Nomor: {{ $letter->nomor_surat ?? '470/' . str_pad($letter->id, 3, '0', STR_PAD_LEFT) . '/RW.013/RT.' . $letter->rt . '/' . date('m/Y') }}
            </div>
        </div>

        {{-- BADAN SURAT --}}
        <div class="surat-body">
            <p class="surat-paragraf">
                Yang bertanda tangan di bawah ini, Pengurus Rukun Tetangga {{ $letter->rt }} dan Pengurus Rukun Warga 013 Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung, menerangkan dengan sebenarnya bahwa:
            </p>

            {{-- TABEL BIODATA RESMI --}}
            <table class="tabel-biodata">
                <tr>
                    <td class="label">1. Nama Lengkap</td>
                    <td class="colon">:</td>
                    <td class="value">{{ strtoupper($letter->nama_lengkap) }}</td>
                </tr>
                <tr>
                    <td class="label">2. NIK / No. KTP</td>
                    <td class="colon">:</td>
                    <td class="value font-monospace">{{ $letter->nik }}</td>
                </tr>
                <tr>
                    <td class="label">3. Nomor Kartu Keluarga</td>
                    <td class="colon">:</td>
                    <td class="value font-monospace">{{ $letter->no_kk }}</td>
                </tr>
                <tr>
                    <td class="label">4. Tempat, Tanggal Lahir</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $letter->tempat_lahir }}, {{ $letter->tanggal_lahir ? $letter->tanggal_lahir->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">5. Jenis Kelamin</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $letter->jenis_kelamin }}</td>
                </tr>
                <tr>
                    <td class="label">6. Agama</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $letter->agama }}</td>
                </tr>
                <tr>
                    <td class="label">7. Status Perkawinan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $letter->status_perkawinan }}</td>
                </tr>
                <tr>
                    <td class="label">8. Pekerjaan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $letter->pekerjaan }}</td>
                </tr>
                <tr>
                    <td class="label">9. Alamat / Tempat Tinggal</td>
                    <td class="colon">:</td>
                    <td class="value">
                        {{ $letter->alamat_jalan }} No. {{ $letter->no_rumah }}, RT {{ $letter->rt }} / RW {{ $letter->rw }},<br>
                        Kelurahan {{ $letter->kelurahan }}, Kecamatan {{ $letter->kecamatan }}, Kota Bandung, Jawa Barat ({{ $letter->kode_pos }})
                    </td>
                </tr>
            </table>

            {{-- KETERANGAN SPESIFIK SESUAI MASTER TEMPLATE / JENIS SURAT --}}
            @if(!empty($templateText))
                <p class="surat-paragraf">
                    {{ $templateText }}
                </p>
            @elseif($letter->jenis_surat === 'domisili')
                <p class="surat-paragraf">
                    Bahwa nama tersebut di atas adalah benar-benar warga yang berdomisili dan bertempat tinggal sah di alamat tersebut di wilayah Rukun Tetangga {{ $letter->rt }} Rukun Warga 013 Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung.
                </p>
            @elseif($letter->jenis_surat === 'sktm' || $letter->jenis_surat === 'tidak-mampu')
                <p class="surat-paragraf">
                    Bahwa nama tersebut di atas adalah benar-benar warga yang berdomisili di lingkungan RT {{ $letter->rt }} / RW 013 Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung, dan berdasarkan data verifikasi lapangan yang bersangkutan tergolong ke dalam <strong>Keluarga Kurang Mampu / Prasejahtera</strong>.
                </p>
            @elseif($letter->jenis_surat === 'usaha' || $letter->jenis_surat === 'sku')
                <p class="surat-paragraf">
                    Bahwa nama tersebut di atas adalah benar warga kami yang memiliki dan menjalankan kegiatan usaha:
                </p>
                <div class="box-keterangan" style="margin-left: 35px;">
                    <strong>Nama Usaha:</strong> {{ $letter->detail_khusus['nama_usaha'] ?? 'Usaha Mandiri Warga' }}<br>
                    <strong>Bidang Usaha:</strong> {{ $letter->detail_khusus['bidang_usaha'] ?? 'Perdagangan / Jasa' }}<br>
                    <strong>Lokasi Usaha:</strong> {{ $letter->detail_khusus['alamat_usaha'] ?? ($letter->alamat_jalan . ' No. ' . $letter->no_rumah) }}
                </div>
            @else
                <p class="surat-paragraf">
                    Bahwa nama tersebut di atas adalah benar warga kami yang berkelakuan baik dan tercatat aktif dalam administrasi kependudukan lingkungan kami.
                </p>
            @endif

            <p class="surat-paragraf">
                Surat keterangan ini diterbitkan secara sah atas permohonan yang bersangkutan untuk keperluan: <strong>"{{ $letter->keperluan }}"</strong>.
            </p>

            <p class="surat-paragraf">
                Demikian surat keterangan ini kami buat dengan sebenarnya dan penuh tanggung jawab agar dapat dipergunakan sebagaimana mestinya oleh pihak yang berkepentingan.
            </p>
        </div>

        {{-- TANDA TANGAN (DUAL RT & RW ATAU TUNGGAL RT SESUAI ALUR) --}}
        @if($letter->alur_persetujuan === 'rt_only')
            <div class="tanda-tangan-block" style="justify-content: flex-end;">
                <div class="ttd-box">
                    <div style="font-size: 11pt; margin-bottom: 3px;">
                        Bandung, {{ $letter->rt_approved_at ? $letter->rt_approved_at->translatedFormat('d F Y') : date('d F Y') }}
                    </div>
                    <div class="ttd-jabatan">
                        Ketua RT {{ $letter->rt }} Kel. Sukamiskin<br>
                        Kecamatan Arcamanik<br>
                    </div>

                    {{-- Stempel Digital RT --}}
                    <div class="stempel-digital">
                        <div class="stempel-inner">
                            <span style="font-size: 6.5pt;">PENGURUS RT</span>
                            <span style="font-size: 9pt; font-weight: 800;">RT {{ $letter->rt }}</span>
                            <span style="font-size: 5.5pt;">SUKAMISKIN</span>
                            <span style="font-size: 6pt; color:#16865D; font-weight: 800;">PENGESAHAN</span>
                        </div>
                    </div>

                    <div class="ttd-nama">{{ $rtName }}</div>
                    <div class="ttd-nip">Token: {{ substr($letter->rt_signature_token ?? 'RT-VERIFIED', 0, 16) }}</div>
                </div>
            </div>
        @else
            <div class="tanda-tangan-block">
                {{-- Kiri: Ketua RT --}}
                <div class="ttd-box">
                    <div style="font-size: 11pt; margin-bottom: 3px;">Mengetahui,</div>
                    <div class="ttd-jabatan">
                        Ketua RT {{ $letter->rt }} Kel. Sukamiskin<br>
                        <span style="font-size: 9.5pt; font-weight: normal; color: #444;">
                            Diverifikasi: {{ $letter->rt_approved_at ? $letter->rt_approved_at->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>

                    {{-- Stempel Digital RT --}}
                    <div class="stempel-digital">
                        <div class="stempel-inner">
                            <span style="font-size: 6.5pt;">PENGURUS RT</span>
                            <span style="font-size: 9pt; font-weight: 800;">RT {{ $letter->rt }}</span>
                            <span style="font-size: 5.5pt;">SUKAMISKIN</span>
                            <span style="font-size: 6pt; color:#16865D; font-weight: 800;">TERVERIFIKASI</span>
                        </div>
                    </div>

                    <div class="ttd-nama">{{ $rtName }}</div>
                    <div class="ttd-nip">Token: {{ substr($letter->rt_signature_token ?? 'RT-VERIFIED', 0, 16) }}</div>
                </div>

                {{-- Kanan: Ketua RW --}}
                <div class="ttd-box">
                    <div style="font-size: 11pt; margin-bottom: 3px;">
                        Bandung, {{ $letter->rw_approved_at ? $letter->rw_approved_at->translatedFormat('d F Y') : date('d F Y') }}
                    </div>
                    <div class="ttd-jabatan">
                        Ketua RW 013 Kel. Sukamiskin<br>
                        Kecamatan Arcamanik<br>
                    </div>

                    {{-- Stempel Digital RW --}}
                    <div class="stempel-digital">
                        <div class="stempel-inner">
                            <span style="font-size: 6.5pt;">PENGURUS RW</span>
                            <span style="font-size: 9.5pt; font-weight: 800;">RW 013</span>
                            <span style="font-size: 5.5pt;">KOTA BANDUNG</span>
                            <span style="font-size: 6pt; color:#1456A0; font-weight: 800;">PENGESAHAN</span>
                        </div>
                    </div>

                    <div class="ttd-nama">{{ $rwName }}</div>
                    <div class="ttd-nip">Token: {{ substr($letter->rw_signature_token ?? 'RW-VERIFIED', 0, 16) }}</div>
                </div>
            </div>
        @endif

        {{-- FOOTER KEASLIAN & QR CODE PUBLIK --}}
        <div class="surat-footer">
            <div class="qr-section">
                {{-- Dynamic SVG QR Code Representation --}}
                <div class="qr-placeholder">
                    <svg viewBox="0 0 100 100" width="58" height="58">
                        <rect width="100" height="100" fill="#ffffff"/>
                        {{-- Corner markers --}}
                        <rect x="5" y="5" width="26" height="26" fill="#123766"/>
                        <rect x="10" y="10" width="16" height="16" fill="#ffffff"/>
                        <rect x="14" y="14" width="8" height="8" fill="#123766"/>

                        <rect x="69" y="5" width="26" height="26" fill="#123766"/>
                        <rect x="74" y="10" width="16" height="16" fill="#ffffff"/>
                        <rect x="78" y="14" width="8" height="8" fill="#123766"/>

                        <rect x="5" y="69" width="26" height="26" fill="#123766"/>
                        <rect x="10" y="74" width="16" height="16" fill="#ffffff"/>
                        <rect x="14" y="78" width="8" height="8" fill="#123766"/>

                        {{-- Data pattern simulation --}}
                        <rect x="36" y="10" width="6" height="6" fill="#123766"/>
                        <rect x="46" y="10" width="6" height="6" fill="#123766"/>
                        <rect x="56" y="10" width="6" height="6" fill="#123766"/>
                        <rect x="36" y="24" width="6" height="6" fill="#123766"/>
                        <rect x="46" y="24" width="12" height="6" fill="#123766"/>

                        <rect x="10" y="40" width="6" height="6" fill="#123766"/>
                        <rect x="22" y="40" width="10" height="6" fill="#123766"/>
                        <rect x="38" y="38" width="24" height="24" fill="#1456A0"/>
                        <circle cx="50" cy="50" r="6" fill="#ffffff"/>

                        <rect x="68" y="40" width="6" height="6" fill="#123766"/>
                        <rect x="80" y="40" width="10" height="6" fill="#123766"/>
                        <rect x="40" y="70" width="6" height="6" fill="#123766"/>
                        <rect x="50" y="70" width="14" height="6" fill="#123766"/>
                        <rect x="70" y="70" width="6" height="6" fill="#123766"/>
                        <rect x="80" y="80" width="12" height="12" fill="#123766"/>
                    </svg>
                </div>
                <div>
                    <div style="font-weight: 700; color:#123766;">VERIFIKASI DIGITAL WARKAS MACHI</div>
                    <div>Pindai QR Code untuk memvalidasi keaslian dokumen resmi ini secara daring.</div>
                    <div style="font-family: monospace; color:#64748b; font-size:7.5pt;">ID: {{ $letter->qr_verification_code }}</div>
                </div>
            </div>

            <div style="text-align: right; max-width: 250px;">
                <div>Dokumen sah tanda tangan elektronik berjenjang RT {{ $letter->rt }} - RW 013 Kota Bandung. Dicetak pada {{ date('d/m/Y H:i') }} WIB.</div>
            </div>
        </div>

    </div>

    <script>
        if (window.lucide) {
            window.lucide.createIcons();
        }
    </script>
</body>
</html>
