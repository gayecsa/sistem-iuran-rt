@extends('layouts.app')

@section('content')
<div class="animate-fadeInUp container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-coins me-2"></i>Kas RT 001</h2>
        {{-- PERBAIKAN: Route diubah ke kas-rt.create agar sinkron dengan web.php --}}
        <a href="{{ route('kas-rt.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #fd79a8 0%, #74b9ff 100%); border: none;">
            <i class="fas fa-plus-circle me-2"></i>Catat Transaksi Baru
        </a>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white border-0 shadow-sm" style="background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%) !important;">
                <div class="card-body text-center py-4">
                    <h5 class="fw-semibold opacity-75">Saldo Kas</h5>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($saldo, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white border-0 shadow-sm" style="background: linear-gradient(135deg, #00b894 0%, #00cec9 100%) !important;">
                <div class="card-body text-center py-4">
                    <h5 class="fw-semibold opacity-75">Total Pemasukan</h5>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white border-0 shadow-sm" style="background: linear-gradient(135deg, #ff7675 0%, #d63031 100%) !important;">
                <div class="card-body text-center py-4">
                    <h5 class="fw-semibold opacity-75">Total Pengeluaran</h5>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header text-white py-3" style="background-color: #2d3436;">
            <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i>Riwayat Transaksi Kas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Nama Warga</th>
                            <th>No. HP</th>
                            <th>Pemasukan</th>
                            <th>Pengeluaran</th>
                            <th class="text-center">Bukti</th>
                            <th>Keterangan</th>
                            <th>Dibuat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kas as $key => $item)
                        <tr>
                            <td>{{ $kas->firstItem() + $key }}</td>
                            <td>{{ date('d/m/Y', strtotime($item->tanggal_transaksi)) }}</td>
                            <td><span class="badge bg-secondary px-2.5 py-1.5">{{ $item->kategori }}</span></td>
                            <td>{{ $item->nama_warga ?? 'Umum / Non-Warga' }}</td>
                            <td>{{ $item->no_hp ?? '-' }}</td>
                            <td class="text-success fw-bold">
                                @if($item->pemasukan > 0)
                                    Rp {{ number_format($item->pemasukan, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-danger fw-bold">
                                @if($item->pengeluaran > 0)
                                    Rp {{ number_format($item->pengeluaran, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            {{-- Kolom Menampilkan Bukti Pembayaran Gambar --}}
                            <td class="text-center">
                                @if($item->bukti_pembayaran)
                                    <a href="{{ asset('storage/bukti_transfer/' . $item->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline-info px-3">
                                        <i class="fas fa-image me-1"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            <td><small class="text-muted">{{ $item->dibuat_oleh }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Belum ada transaksi kas tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $kas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection