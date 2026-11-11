@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Bar (Hidden when printing) -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 d-print-none">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('bank-sampah') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3 fw-semibold">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px; vertical-align: middle;"></i> Portal
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Laporan Akuntabilitas Bank Sampah Terpadu RW 013</h4>
                <small class="text-muted">Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="window.print()">
                <i data-lucide="printer" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Cetak Laporan Resmi
            </button>
        </div>
    </div>

    <!-- Filter Form (Hidden when printing) -->
    <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4 d-print-none">
        <form action="{{ route('bank-sampah.laporan') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small text-dark">Pilih Bulan</label>
                <select name="bulan" class="form-select rounded-3">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small text-dark">Pilih Tahun</label>
                <select name="tahun" class="form-select rounded-3">
                    @for($y = 2024; $y <= 2027; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small text-dark">Wilayah RT</label>
                <select name="rt" class="form-select rounded-3">
                    <option value="">-- Seluruh RW 013 (RT 001 - RT 008) --</option>
                    @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ $rt == $i ? 'selected' : '' }}>
                            RT {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold flex-grow-1">
                    <i data-lucide="filter" style="width: 14px; height: 14px; vertical-align: middle;"></i> Tampilkan
                </button>
                <a href="{{ route('bank-sampah.laporan') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Official Printable Report Document -->
    <div class="wm-card bg-white border rounded-4 shadow-sm p-4 p-md-5 mb-4 print-container">
        <!-- Kop Surat Resmi RW 013 -->
        <div class="border-bottom pb-3 mb-4 text-center">
            <h5 class="fw-bold mb-0 text-dark text-uppercase" style="letter-spacing: 0.5px;">RUKUN WARGA 013</h5>
            <h6 class="fw-bold mb-1 text-dark text-uppercase">KELURAHAN SUKAMISKIN, KECAMATAN ARCAMANIK, KOTA BANDUNG</h6>
            <p class="mb-0 text-muted small">Sekretariat: Balai RW 013, Jl. Golf Raya No. 15, Sukamiskin, Arcamanik, Bandung 40293</p>
            <div style="border-top: 2px solid #000; border-bottom: 1px solid #000; height: 4px; margin-top: 8px;"></div>
        </div>

        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1 text-dark text-uppercase">LAPORAN REKAPITULASI BANK SAMPAH TERPADU</h4>
            <p class="text-muted small mb-0">
                Periode: <strong>{{ Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}</strong>
                @if($rt)
                    | Wilayah: <strong>RT {{ str_pad($rt, 3, '0', STR_PAD_LEFT) }}</strong>
                @else
                    | Wilayah: <strong>Seluruh RT (RT 001 s/d RT 008)</strong>
                @endif
            </p>
        </div>

        <!-- 3 Kotak Summary Cetak -->
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="p-3 border rounded-3 text-center bg-light">
                    <span class="text-muted small d-block">Total Sampah Terkumpul</span>
                    <h4 class="fw-bold text-dark mb-0">{{ number_format($totalKg, 2, ',', '.') }} kg</h4>
                </div>
            </div>
            <div class="col-4">
                <div class="p-3 border rounded-3 text-center bg-light">
                    <span class="text-muted small d-block">Nilai Tabungan Warga</span>
                    <h4 class="fw-bold text-success mb-0">Rp {{ number_format($totalRupiah, 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="col-4">
                <div class="p-3 border rounded-3 text-center bg-light">
                    <span class="text-muted small d-block">Warga Berpartisipasi</span>
                    <h4 class="fw-bold text-primary mb-0">{{ $totalWarga }} Orang</h4>
                </div>
            </div>
        </div>

        <!-- Tabel 1: Rekap Menurut Jenis Sampah -->
        <h6 class="fw-bold text-dark mb-2 border-bottom pb-1">I. Rekapitulasi Berdasarkan Jenis Sampah Anorganik</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">No</th>
                        <th>Kategori Sampah</th>
                        <th class="text-end" style="width: 150px;">Volume / Berat Terkumpul</th>
                        <th class="text-end" style="width: 180px;">Total Nilai Ekonomi (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapKategori as $i => $rk)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td><strong class="text-dark">{{ $rk->nama_kategori }}</strong></td>
                            <td class="text-end fw-bold">{{ number_format($rk->total_berat, 2, ',', '.') }} {{ $rk->satuan }}</td>
                            <td class="text-end fw-bold text-success">Rp {{ number_format($rk->total_subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-2">Tidak ada data setoran sampah pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2" class="text-end fw-bold">TOTAL KESELURUHAN:</th>
                        <th class="text-end fw-bold text-dark">{{ number_format($totalKg, 2, ',', '.') }} kg</th>
                        <th class="text-end fw-bold text-success">Rp {{ number_format($totalRupiah, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Tabel 2: Rekap Menurut RT -->
        @if(!$rt)
            <h6 class="fw-bold text-dark mb-2 border-bottom pb-1">II. Rekapitulasi Kontribusi Sampah Per Wilayah RT</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;" class="text-center">No</th>
                            <th>Wilayah RT</th>
                            <th class="text-center" style="width: 120px;">Jumlah Transaksi</th>
                            <th class="text-end" style="width: 150px;">Total Berat (kg)</th>
                            <th class="text-end" style="width: 180px;">Total Nilai (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapPerRt as $j => $rrt)
                            <tr>
                                <td class="text-center">{{ $j + 1 }}</td>
                                <td><strong>Wilayah RT {{ str_pad($rrt->rt_number, 3, '0', STR_PAD_LEFT) }}</strong></td>
                                <td class="text-center">{{ $rrt->total_transaksi }} kali</td>
                                <td class="text-end fw-bold">{{ number_format($rrt->total_kg, 2, ',', '.') }} kg</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($rrt->total_rp, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-2">Belum ada transaksi di seluruh RT pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Tanda Tangan Pengurus Resmi -->
        <div class="row mt-5 pt-4">
            <div class="col-6 text-center">
                <p class="mb-1 text-muted small">Mengetahui,</p>
                <p class="fw-bold mb-5 text-dark">Ketua RW 013 Sukamiskin Arcamanik</p>
                <p class="fw-bold text-dark mb-0 text-decoration-underline">GAYECA / PENGURUS RW 013</p>
                <small class="text-muted">NIP/ID RW: RW013-BDG-2026</small>
            </div>
            <div class="col-6 text-center">
                <p class="mb-1 text-muted small">Bandung, {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="fw-bold mb-5 text-dark">Koordinator Bank Sampah Terpadu</p>
                <p class="fw-bold text-dark mb-0 text-decoration-underline">{{ $user->name }}</p>
                <small class="text-muted">ID Petugas: #{{ $user->id }}</small>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body {
            background: #fff !important;
            color: #000 !important;
        }
        .navbar, .sidebar, .wm-header, footer, .d-print-none {
            display: none !important;
        }
        .print-container {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
