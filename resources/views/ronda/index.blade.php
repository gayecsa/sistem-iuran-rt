@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Ronda & Keamanan -->
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #123766 0%, #0F172A 100%); color: #fff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-danger text-white mb-2 fw-bold px-3 py-1 rounded-pill">SISTEM KEAMANAN LINGKUNGAN</span>
                <h3 class="fw-bold mb-1 text-white">Ronda Malam & Pengawasan Keamanan RW 013</h3>
                <p class="mb-0 text-white opacity-75 small">
                    Integrasi jadwal siskamling 8 RT, kontak darurat linmas, dan pantauan CCTV lingkungan 24 jam.
                </p>
            </div>
            <div class="d-flex gap-2">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('cctv.index') }}" class="btn btn-outline-light rounded-pill px-3 fw-bold small">
                        <i data-lucide="video" style="width: 16px; height: 16px;"></i> Kelola CCTV
                    </a>
                @endif
                <a href="tel:112" class="btn btn-danger rounded-pill px-3 fw-bold small shadow">
                    <i data-lucide="phone-call" style="width: 16px; height: 16px;"></i> Darurat (112)
                </a>
            </div>
        </div>
    </div>

    <!-- Live CCTV Grid -->
    <div class="wm-card mb-4 bg-dark text-white border-0">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-white">
                    <i class="fas fa-video text-danger me-2"></i>Pantauan Kamera CCTV Lingkungan RW 013
                </h5>
                <small class="text-secondary">Siaran langsung titik strategis portal, jalan utama, dan pos ronda.</small>
            </div>
            <span class="badge bg-danger rounded-pill px-3 py-1"><span class="wm-pulse-dot bg-white me-1 d-inline-block"></span> 24 Jam Aktif</span>
        </div>

        <div class="row g-3">
            @forelse($cctvs as $cam)
                <div class="col-md-6">
                    <div class="rounded-4 overflow-hidden position-relative bg-black" style="aspect-ratio: 16/9; border: 1px solid rgba(255,255,255,0.1);">
                        @if(preg_match('/mjpe?g|\.cgi|snapshot|\.jpg/i', $cam->stream_url))
                            <img src="{{ $cam->stream_url }}" alt="{{ $cam->nama }}" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <video src="{{ $cam->stream_url }}" class="w-100 h-100" style="object-fit: cover;" autoplay muted playsinline controls></video>
                        @endif
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2 fw-bold" style="font-size: 0.7rem;">LIVE CAM</span>
                        <div class="position-absolute bottom-0 start-0 end-0 p-2 bg-gradient text-white" style="background: linear-gradient(transparent, rgba(0,0,0,0.85));">
                            <strong>{{ $cam->nama }}</strong>
                            <small class="d-block text-secondary">{{ $cam->lokasi ?? 'Wilayah RW 013' }}</small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-secondary">
                    <i data-lucide="video-off" style="width: 42px; height: 42px;" class="mb-2"></i>
                    <p class="mb-0">Belum ada kamera CCTV terdaftar aktif.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Jadwal Ronda Bergilir RT 001 - RT 008 -->
    <div class="wm-card">
        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
            <i data-lucide="calendar" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
            Jadwal Ronda Malam & Siskamling RW 013
        </h5>

        <div class="table-responsive">
            <table class="wm-table">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Cakupan Wilayah RT</th>
                        <th>Koordinator Piket</th>
                        <th>Kekuatan Regu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalRonda as $r)
                        <tr class="{{ str_contains($r['hari'], 'Malam Ini') ? 'table-warning' : '' }}">
                            <td>
                                <strong class="text-dark">{{ $r['hari'] }}</strong>
                            </td>
                            <td><span class="badge bg-primary text-white">{{ $r['rt'] }}</span></td>
                            <td class="fw-semibold text-dark">{{ $r['koordinator'] }}</td>
                            <td>{{ $r['anggota'] }}</td>
                            <td>
                                @if(str_contains($r['status'], 'Bertugas'))
                                    <span class="status-badge status-lengkap"><i data-lucide="radio" style="width: 12px; height: 12px;"></i> {{ $r['status'] }}</span>
                                @elseif($r['status'] === 'Selesai')
                                    <span class="status-badge status-belum">{{ $r['status'] }}</span>
                                @else
                                    <span class="status-badge status-periksa">{{ $r['status'] }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
