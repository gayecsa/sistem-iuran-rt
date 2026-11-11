@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%); color: #fff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-light text-primary mb-2 fw-bold px-3 py-1 rounded-pill">ARSIP LAPORAN RT</span>
                <h3 class="fw-bold mb-1 text-white">Laporan Pertanggungjawaban Bulanan RT 001 - RT 008</h3>
                <p class="mb-0 text-white opacity-75 small">
                    Dokumen rekapitulasi pelaporan administrasi, keuangan kas, dan kegiatan kemasyarakatan per RT di lingkungan RW 013.
                </p>
            </div>
            <a href="{{ route('monitoring-rt.index') }}" class="btn btn-light rounded-pill px-3 fw-bold small text-primary">
                <i data-lucide="clipboard-check" style="width: 16px; height: 16px;"></i> Ke Halaman Audit RT
            </a>
        </div>
    </div>

    <div class="wm-card">
        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
            <i data-lucide="folder-archive" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
            Daftar Berkas Laporan Bulanan 8 RT Periode November 2026
        </h5>

        <div class="table-responsive">
            <table class="wm-table">
                <thead>
                    <tr>
                        <th>RT</th>
                        <th>Ketua RT</th>
                        <th>Status Laporan</th>
                        <th>Kelengkapan Berkas</th>
                        <th>Saldo Kas Dilaporkan</th>
                        <th>Terakhir Diperbarui</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rtList as $rt)
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $rt['nama'] }}</strong>
                                <small class="text-muted d-block">RW 013</small>
                            </td>
                            <td>{{ $rt['ketua_rt'] }}</td>
                            <td>
                                @if($rt['status_laporan'] === 'Lengkap')
                                    <span class="status-badge status-lengkap">Lengkap</span>
                                @elseif($rt['status_laporan'] === 'Perlu diperiksa')
                                    <span class="status-badge status-periksa">Perlu diperiksa</span>
                                @elseif($rt['status_laporan'] === 'Belum dilaporkan')
                                    <span class="status-badge status-belum">Belum dilaporkan</span>
                                @else
                                    <span class="status-badge status-tindak-lanjut">Perlu tindak lanjut</span>
                                @endif
                            </td>
                            <td><span class="fw-semibold text-dark">{{ $rt['administrasi'] }}</span></td>
                            <td class="fw-bold text-success">{{ $rt['keuangan'] }}</td>
                            <td><small class="text-muted">{{ $rt['terakhir_update'] }}</small></td>
                            <td class="text-end">
                                <a href="{{ route('monitoring-rt.show', $rt['rt']) }}" class="wm-btn-primary" style="padding: 6px 14px; font-size: 0.82rem;">
                                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Evaluasi RW
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
