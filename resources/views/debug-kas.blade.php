@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>🔍 Debug Info - Kas RT</h2>
    
    <div class="alert alert-info">
        <strong>Update:</strong> Saldo sekarang menggunakan selectRaw yang lebih akurat. Jika masih nambah, kemungkinan ada form implicit submission.
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-4">
                <h5>📊 Database Statistics</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Total Records:</strong></td>
                        <td>{{ $total_records }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Pemasukan:</strong></td>
                        <td>Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Pengeluaran:</strong></td>
                        <td>Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="table-success">
                        <td><strong>Saldo:</strong></td>
                        <td><strong>Rp {{ number_format($saldo, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4">
                <h5>📝 Breakdown by Category</h5>
                <table class="table table-sm">
                    @foreach($categories as $cat)
                        <tr>
                            <td>{{ $cat['kategori'] }}</td>
                            <td class="text-end">{{ $cat['count'] }} x</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card p-4">
                <h5>✅ Solutions Applied</h5>
                <ul>
                    <li><strong>selectRaw() Query:</strong> Menggunakan SQL aggregate function yang lebih akurat</li>
                    <li><strong>COALESCE:</strong> Mengatasi NULL values</li>
                    <li><strong>No Implicit Joins:</strong> Query tidak melakukan join yang bisa duplikasi</li>
                </ul>
                
                <h6 class="mt-4">🔍 Checking for Issues:</h6>
                @if($duplicate_check['status'] === 'ok')
                    <div class="alert alert-success">✅ Tidak ada duplikasi data</div>
                @else
                    <div class="alert alert-warning">⚠️ {{ $duplicate_check['message'] }}</div>
                @endif

                @if($pengeluaran_check > 0)
                    <div class="alert alert-success">✅ Pengeluaran terdeteksi: Rp {{ number_format($pengeluaran_check, 0, ',', '.') }}</div>
                @else
                    <div class="alert alert-danger">❌ Pengeluaran 0 - Kemungkinan ada masalah dengan data</div>
                @endif
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card p-4">
                <h5>📋 Recent Transactions</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Pemasukan</th>
                                <th>Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_transactions as $tx)
                                <tr>
                                    <td>{{ $tx->tanggal_transaksi->format('d/m/Y') }}</td>
                                    <td>{{ $tx->keterangan }}</td>
                                    <td class="text-success">{{ $tx->pemasukan > 0 ? 'Rp ' . number_format($tx->pemasukan, 0, ',', '.') : '-' }}</td>
                                    <td class="text-danger">{{ $tx->pengeluaran > 0 ? 'Rp ' . number_format($tx->pengeluaran, 0, ',', '.') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Kembali ke Dashboard</a>
    </div>
</div>
@endsection
