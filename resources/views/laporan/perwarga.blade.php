@extends('layouts.app')

@section('content')
<div class="animate-fadeInUp">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4><i class="fas fa-users me-2"></i>Laporan Per Warga</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Warga</th>
                            <th>No. Rumah</th>
                            <th>Total Pembayaran</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data_warga as $key => $warga)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $warga['nama'] }}</td>
                            <td>{{ $warga['no_rumah'] }}</td>
                            <td>Rp {{ number_format($warga['total_bayar'], 0, ',', '.') }}</td>
                            <td>
                                @if($warga['status'] == 'Aktif')
                                    <span class="badge bg-success">Aktif Membayar</span>
                                @else
                                    <span class="badge bg-danger">Belum Pernah Bayar</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data warga</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="text-end mt-3">
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print me-2"></i>Cetak Laporan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection