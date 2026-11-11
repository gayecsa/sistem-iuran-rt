<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi Setoran Bank Sampah - {{ $transaksi->kode_transaksi }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }
        .slip-box {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            border: 1px dashed #cbd5e1;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .slip-box {
                box-shadow: none;
                border: 1px solid #000;
                margin: 0 auto;
                max-width: 100%;
            }
            .btn-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mt-3 mb-2 btn-print">
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-bold">
                Cetak Slip / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary rounded-pill px-3">
                Tutup
            </button>
        </div>

        <div class="slip-box">
            <!-- Header Resi -->
            <div class="text-center border-bottom pb-3 mb-3">
                <h5 class="fw-bold mb-0 text-uppercase">BANK SAMPAH TERPADU RW 013</h5>
                <small class="text-muted d-block">Kel. Sukamiskin, Kec. Arcamanik, Kota Bandung 40293</small>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill mt-2 fw-bold">
                    BUKTI PENIMBANGAN & TABUNGAN SAMPAH
                </span>
            </div>

            <!-- Detail Transaksi -->
            <div class="row g-2 mb-3 small">
                <div class="col-6">
                    <span class="text-muted d-block">Kode Transaksi:</span>
                    <strong class="text-dark fs-6">{{ $transaksi->kode_transaksi }}</strong>
                </div>
                <div class="col-6 text-end">
                    <span class="text-muted d-block">Tanggal Selesai:</span>
                    <strong class="text-dark">{{ $transaksi->tanggal_selesai ? $transaksi->tanggal_selesai->format('d/m/Y H:i') : $transaksi->tanggal_pengajuan->format('d/m/Y H:i') }}</strong>
                </div>
                <div class="col-6 mt-2">
                    <span class="text-muted d-block">Warga Penyetor:</span>
                    <strong class="text-dark">{{ $transaksi->user->name ?? 'Warga' }}</strong>
                    <div class="text-muted">RT {{ str_pad($transaksi->rt_number, 3, '0', STR_PAD_LEFT) }} RW 013</div>
                </div>
                <div class="col-6 text-end mt-2">
                    <span class="text-muted d-block">Petugas Penimbang:</span>
                    <strong class="text-dark">{{ $transaksi->petugas->name ?? 'Petugas RT' }}</strong>
                    <div class="text-muted">{{ $transaksi->bankSampah->nama_unit ?? '-' }}</div>
                </div>
            </div>

            <!-- Tabel Rincian -->
            <table class="table table-sm table-bordered align-middle small mb-3">
                <thead class="table-light">
                    <tr>
                        <th>Jenis Sampah</th>
                        <th class="text-end">Berat / Satuan</th>
                        <th class="text-end">Tarif Resmi</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi->items as $item)
                        <tr>
                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                            <td class="text-end fw-semibold">{{ number_format($item->berat_satuan, 2, ',', '.') }} {{ $item->satuan }}</td>
                            <td class="text-end text-muted">Rp {{ number_format($item->harga_per_satuan, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold text-dark">{{ $item->subtotal_formatted }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2" class="text-end">TOTAL BERAT:</th>
                        <th colspan="2" class="text-end fs-6 text-dark">{{ $transaksi->total_berat_formatted }}</th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-end">TOTAL TABUNGAN MASUK:</th>
                        <th colspan="2" class="text-end fs-5 text-success">{{ $transaksi->total_rupiah_formatted }}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="p-2 bg-light border rounded small mb-4">
                <strong>Status Saldo:</strong> Telah otomatis dikreditkan ke buku rekening tabungan digital warga dan tercatat dalam buku besar ledger RW 013.
            </div>

            <!-- Tanda Tangan Ringkas -->
            <div class="row text-center small mt-4">
                <div class="col-6">
                    <p class="mb-4">Warga Penyetor,</p>
                    <strong class="text-dark">({{ $transaksi->user->name ?? 'Warga' }})</strong>
                </div>
                <div class="col-6">
                    <p class="mb-4">Petugas Penimbang,</p>
                    <strong class="text-dark">({{ $transaksi->petugas->name ?? 'Petugas RT' }})</strong>
                </div>
            </div>

            <div class="text-center text-muted mt-4 pt-2 border-top" style="font-size: 11px;">
                <em>Warkas Machi &bull; Sistem Terpadu Manajemen Lingkungan RW 013 Sukamiskin Arcamanik</em>
            </div>
        </div>
    </div>
</body>
</html>
