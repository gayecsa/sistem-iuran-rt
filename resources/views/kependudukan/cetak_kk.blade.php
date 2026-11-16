<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salinan Kartu Keluarga - {{ $kk->no_kk }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            font-size: 11pt;
        }
        .kk-container {
            max-width: 1050px;
            margin: 20px auto;
            padding: 30px 40px;
            border: 2px solid #000;
            background: #fff;
        }
        .header-title {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header-title h2 {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 2px;
            letter-spacing: 2px;
        }
        .header-title h4 {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 0;
        }
        .table-kk {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 12px;
        }
        .table-kk th, .table-kk td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .table-kk th {
            text-align: center;
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            font-size: 10pt;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .kk-container {
                border: none;
                max-width: 100%;
                margin: 0;
                padding: 10px;
            }
            .d-print-none {
                display: none !important;
            }
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="text-center my-3 d-print-none">
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-bold me-2">
                Cetak Dokumen KK (Landscape)
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary rounded-pill px-3">
                Tutup Jendela
            </button>
        </div>

        <div class="kk-container">
            <div class="header-title">
                <h2>KARTU KELUARGA</h2>
                <h4>No. {{ $kk->no_kk }}</h4>
            </div>

            <!-- Identitas Keluarga -->
            <table class="info-table">
                <tr>
                    <td style="width: 20%;"><strong>Nama Kepala Keluarga</strong></td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 38%; font-weight: bold; text-transform: uppercase;">{{ $kk->nama_kepala_keluarga ?: '-' }}</td>
                    <td style="width: 18%;"><strong>Kecamatan</strong></td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 20%;">ARCAMANIK</td>
                </tr>
                <tr>
                    <td><strong>Alamat</strong></td>
                    <td>:</td>
                    <td>{{ $kk->alamat ?: 'RW 013 Sukamiskin' }}</td>
                    <td><strong>Kabupaten/Kota</strong></td>
                    <td>:</td>
                    <td>KOTA BANDUNG</td>
                </tr>
                <tr>
                    <td><strong>RT / RW</strong></td>
                    <td>:</td>
                    <td>{{ str_pad($kk->rt, 3, '0', STR_PAD_LEFT) }} / 013</td>
                    <td><strong>Kode Pos</strong></td>
                    <td>:</td>
                    <td>40293</td>
                </tr>
                <tr>
                    <td><strong>Kelurahan/Desa</strong></td>
                    <td>:</td>
                    <td>SUKAMISKIN</td>
                    <td><strong>Provinsi</strong></td>
                    <td>:</td>
                    <td>JAWA BARAT</td>
                </tr>
            </table>

            <!-- Tabel Data Individu Bagian I -->
            <table class="table-kk">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Nama Lengkap</th>
                        <th style="width: 140px;">NIK</th>
                        <th style="width: 80px;">Jenis Kelamin</th>
                        <th>Tempat Lahir</th>
                        <th style="width: 90px;">Tanggal Lahir</th>
                        <th style="width: 80px;">Agama</th>
                        <th>Pendidikan</th>
                        <th>Jenis Pekerjaan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kk->anggota as $idx => $m)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td style="font-weight: bold; text-transform: uppercase;">{{ $m->name }}</td>
                            <td class="text-center" style="font-family: monospace;">{{ $m->nik }}</td>
                            <td class="text-center">{{ $m->gender === 'Laki-laki' ? 'LAKI-LAKI' : 'PEREMPUAN' }}</td>
                            <td>{{ strtoupper($m->tempat_lahir ?: 'BANDUNG') }}</td>
                            <td class="text-center">{{ $m->tanggal_lahir ? $m->tanggal_lahir->format('d-m-Y') : '-' }}</td>
                            <td class="text-center">{{ strtoupper($m->agama ?: 'ISLAM') }}</td>
                            <td>{{ strtoupper($m->pendidikan_terakhir ?: 'SLTA/SEDERAJAT') }}</td>
                            <td>{{ strtoupper($m->pekerjaan ?: 'BELUM/TIDAK BEKERJA') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Tabel Data Individu Bagian II -->
            <table class="table-kk">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Status Perkawinan</th>
                        <th>Status Hubungan Dalam Keluarga</th>
                        <th style="width: 110px;">Kewarganegaraan</th>
                        <th>Status Kependudukan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kk->anggota as $idx => $m)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td class="text-center">{{ strtoupper($m->status_perkawinan ?: 'BELUM KAWIN') }}</td>
                            <td class="text-center" style="font-weight: bold;">{{ strtoupper($m->hubungan_keluarga ?: 'ANGGOTA KELUARGA') }}</td>
                            <td class="text-center">{{ strtoupper($m->kewarganegaraan ?: 'WNI') }}</td>
                            <td class="text-center">{{ strtoupper($m->status_domisili ?: 'WARGA TETAP') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Catatan Verifikasi & Tanda Tangan -->
            <div class="row mt-4 pt-2" style="font-size: 10pt;">
                <div class="col-4 text-center">
                    <p class="mb-4">KEPALA KELUARGA,</p>
                    <p style="margin-top: 55px; font-weight: bold; text-decoration: underline; text-transform: uppercase;">
                        {{ $kk->nama_kepala_keluarga ?: '.........................................' }}
                    </p>
                </div>
                <div class="col-4 text-center">
                    <p class="mb-1">STATUS DUKCAPIL:</p>
                    <div style="border: 1px dashed #000; padding: 6px; display: inline-block; font-size: 8.5pt;">
                        <strong>TERVERIFIKASI SAH</strong><br>
                        RW 013 Sukamiskin Arcamanik<br>
                        Tgl: {{ $kk->verified_at ? $kk->verified_at->format('d/m/Y') : date('d/m/Y') }}
                    </div>
                </div>
                <div class="col-4 text-center">
                    <p class="mb-0">Dikeluarkan di: Bandung</p>
                    <p class="mb-4">Pada Tanggal: {{ $kk->tgl_diterbitkan_dukcapil ? $kk->tgl_diterbitkan_dukcapil->format('d F Y') : date('d F Y') }}</p>
                    <p style="margin-top: 38px; font-weight: bold; text-decoration: underline; text-transform: uppercase;">
                        KETUA RW 013 SUKAMISKIN
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
