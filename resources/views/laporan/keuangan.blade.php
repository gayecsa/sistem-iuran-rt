@extends('layouts.app')

@section('content')
<div class="animate-fadeInUp container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-info text-white py-3">
            <h4 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i>Laporan Keuangan</h4>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('laporan.keuangan') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Bulan</label>
                        <select name="bulan" class="form-select">
                            @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0,0,0,$i,1)) }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tahun</label>
                        <select name="tahun" class="form-select">
                            @for($i=2020; $i<=date('Y'); $i++)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-search me-2"></i>Tampilkan
                            </button>
                            <a href="{{ route('kas-rt.create') }}" class="btn btn-outline-primary flex-grow-1 text-nowrap">
                                <i class="fas fa-plus me-2"></i>Tambah Transaksi
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="alert alert-success border-0 shadow-sm mb-0 py-3">
                        <h5 class="fw-semibold text-success"><i class="fas fa-arrow-up me-2"></i>Total Pemasukan</h5>
                        <h3 class="fw-bold mb-0">Rp {{ number_format($pemasukan, 0, ',', '.') }}</h3>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-danger border-0 shadow-sm mb-0 py-3">
                        <h5 class="fw-semibold text-danger"><i class="fas fa-arrow-down me-2"></i>Total Pengeluaran</h5>
                        <h3 class="fw-bold mb-0">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            
            <h5 class="fw-bold mb-3"><i class="fas fa-list-alt me-2"></i>Detail Arus Dana Masuk & Keluar</h5>
            <div class="table-responsive mb-4">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Tanggal</th>
                            <th>Nama Penyetor / Warga</th>
                            <th>Keterangan Transaksi</th>
                            <th>Jumlah Masuk</th>
                            <th>Jumlah Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp

                        {{-- 1. Menampilkan Data Iuran Resmi Warga --}}
                        @foreach($detail_pembayaran as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ date('d/m/Y', strtotime($item->tanggal_bayar)) }}</td>
                            <td>{{ $item->user->name ?? 'Warga RT 001' }}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle me-2">Iuran Resmi</span>
                                {{ $item->iuran->nama_iuran ?? 'Iuran Bulanan Warga' }}
                            </td>
                            <td class="text-success fw-bold">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="text-muted">-</td>
                        </tr>
                        @endforeach

                        {{-- 2. Menampilkan Data Arus Kas Umum (Iuran Bulanan, Donasi Kebakaran, Donasi Gaza) --}}
                        @foreach($detail_kas_umum as $kas)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ date('d/m/Y', strtotime($kas->tanggal_transaksi)) }}</td>
                            {{-- Membaca kolom nama_warga atau fallback ke 'Umum / Non-Warga' --}}
                            <td>{{ $kas->nama_warga ?? 'Umum / Non-Warga' }}</td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info-subtle me-2">{{ $kas->kategori }}</span>
                                {{ $kas->keterangan ?? 'Tanpa keterangan tambahan' }}
                            </td>
                            {{-- Kolom Pemasukan --}}
                            <td class="{{ $kas->pemasukan > 0 ? 'text-success fw-bold' : 'text-muted' }}">
                                {{ $kas->pemasukan > 0 ? 'Rp ' . number_format($kas->pemasukan, 0, ',', '.') : '-' }}
                            </td>
                            {{-- Kolom Pengeluaran --}}
                            <td class="{{ $kas->pengeluaran > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                {{ $kas->pengeluaran > 0 ? 'Rp ' . number_format($kas->pengeluaran, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @endforeach

                        {{-- 3. Jika kedua sumber data kosong --}}
                        @if($detail_pembayaran->isEmpty() && $detail_kas_umum->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-folder-open d-block mb-2 fa-2x"></i>
                                Tidak ada data transaksi pada periode bulan ini.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="text-end">
                <button onclick="window.print()" class="btn btn-secondary px-4">
                    <i class="fas fa-print me-2"></i>Cetak Laporan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection