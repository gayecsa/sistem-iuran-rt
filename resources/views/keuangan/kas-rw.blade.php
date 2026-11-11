@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Kas RW -->
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #123766 0%, #16865D 100%); color: #fff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-light text-dark mb-2 fw-bold px-3 py-1 rounded-pill">KEUANGAN RW 013</span>
                <h3 class="fw-bold mb-1 text-white">Sistem Transparansi Kas RW & Rekap Iuran 8 RT</h3>
                <p class="mb-0 text-white opacity-75 small">
                    Pemisahan tegas antara Kas RW (operasional tingkat RW & tanggap darurat) dengan Kas Mandiri RT 001 s/d RT 008.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('laporan.keuangan') }}" class="btn btn-light rounded-pill px-3 fw-bold small text-primary">
                    <i data-lucide="file-text" style="width: 16px; height: 16px;"></i> Laporan Keuangan
                </a>
            </div>
        </div>
    </div>

    <!-- 3 Kartu Saldo Kas RW -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="wm-card text-center p-3 h-100">
                <span class="text-muted fw-bold small text-uppercase">Total Pemasukan Kas RW</span>
                <h3 class="fw-bold text-success mt-2 mb-1">Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</h3>
                <small class="text-muted">Iuran Wajib Lintas RT, Donasi & Kelurahan</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="wm-card text-center p-3 h-100">
                <span class="text-muted fw-bold small text-uppercase">Total Pengeluaran Kas RW</span>
                <h3 class="fw-bold text-danger mt-2 mb-1">Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</h3>
                <small class="text-muted">Kegiatan RW, Sarana, Linmas & Sosial</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="wm-card text-center p-3 h-100" style="background: #E3F5EC; border-color: rgba(22, 134, 93, 0.4) !important;">
                <span class="text-success fw-bold small text-uppercase">Saldo Kas RW Bersih</span>
                <h3 class="fw-bold text-success mt-2 mb-1">Rp {{ number_format($saldo_kas_rw, 0, ',', '.') }}</h3>
                <small class="text-success fw-semibold">Saldo Kas Tingkat RW (Bukan Kas RT)</small>
            </div>
        </div>
    </div>

    <!-- Rekap Iuran Warga Per RT (RT 001 - RT 008) -->
    <div class="wm-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i data-lucide="receipt" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Rekap Penerimaan Iuran Warga per RT (Periode Berjalan)
                </h5>
                <p class="text-muted small mb-0">Capaian setoran iuran warga per RT ke bendahara RW 013.</p>
            </div>
            <a href="{{ route('iuran.index') }}" class="wm-btn-outline" style="padding: 6px 14px; font-size: 0.82rem;">
                Kelola Tagihan Iuran &rarr;
            </a>
        </div>

        <div class="table-responsive">
            <table class="wm-table">
                <thead>
                    <tr>
                        <th>Wilayah RT</th>
                        <th>Target KK</th>
                        <th>KK Lunas</th>
                        <th>Total Terkumpul</th>
                        <th>Persentase Capaian</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapIuranRt as $rekap)
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $rekap['rt'] }}</strong>
                                <small class="text-muted d-block">RW 013</small>
                            </td>
                            <td>{{ $rekap['target_kk'] }} KK</td>
                            <td><span class="badge bg-light text-dark border">{{ $rekap['kk_lunas'] }} KK</span></td>
                            <td class="fw-bold text-success">Rp {{ number_format($rekap['total'], 0, ',', '.') }}</td>
                            <td style="min-width: 140px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $rekap['persen'] }}%"></div>
                                    </div>
                                    <small class="fw-bold text-dark">{{ $rekap['persen'] }}%</small>
                                </div>
                            </td>
                            <td>
                                @if($rekap['persen'] >= 95)
                                    <span class="status-badge status-lengkap">Tuntas</span>
                                @elseif($rekap['persen'] >= 80)
                                    <span class="status-badge status-periksa">Baik</span>
                                @else
                                    <span class="status-badge status-tindak-lanjut">Perlu Follow-up</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Transaksi Kas RW Terbaru & Bukti -->
    <div class="wm-card">
        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
            <i data-lucide="history" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
            Riwayat Transaksi Kas RW Terbaru & Bukti Pembukuan
        </h5>

        <div class="table-responsive">
            <table class="wm-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Uraian & Keterangan</th>
                        <th>Kategori</th>
                        <th>Pemasukan</th>
                        <th>Pengeluaran</th>
                        <th>Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksiRw as $t)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($t->tanggal_transaksi)->translatedFormat('d M Y') }}</td>
                            <td>
                                <strong class="text-dark">{{ $t->keterangan }}</strong>
                                @if($t->nama_warga)
                                    <small class="text-muted d-block">Dari: {{ $t->nama_warga }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $t->kategori }}</span></td>
                            <td class="fw-bold text-success">
                                {{ $t->pemasukan > 0 ? '+ Rp ' . number_format($t->pemasukan, 0, ',', '.') : '-' }}
                            </td>
                            <td class="fw-bold text-danger">
                                {{ $t->pengeluaran > 0 ? '- Rp ' . number_format($t->pengeluaran, 0, ',', '.') : '-' }}
                            </td>
                            <td class="small text-muted">{{ $t->dibuat_oleh ?? 'Bendahara RW' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada catatan transaksi kas terbaru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
