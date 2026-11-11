@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Ronda & Keamanan Bandung -->
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #123766 0%, #0F172A 100%); color: #fff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-danger text-white mb-2 fw-bold px-3 py-1 rounded-pill">SISTEM KEAMANAN & SISKAMLING TERPADU</span>
                <h3 class="fw-bold mb-1 text-white">Ronda Malam & Keamanan RW 013</h3>
                <p class="mb-0 text-white opacity-75 small">
                    <i data-lucide="map-pin" style="width: 14px; height: 14px;" class="me-1"></i>
                    Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293 &bull; Integrasi Siskamling 8 RT, Linmas, & CCTV Lingkungan.
                </p>
            </div>
            <div class="d-flex gap-2">
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'rw')
                    <a href="{{ route('cctv.index') }}" class="btn btn-outline-light rounded-pill px-3 fw-bold small">
                        <i data-lucide="video" style="width: 16px; height: 16px;"></i> Kelola CCTV
                    </a>
                @endif
                <a href="tel:112" class="btn btn-danger rounded-pill px-3 fw-bold small shadow">
                    <i data-lucide="phone-call" style="width: 16px; height: 16px;"></i> Darurat Bandung (112)
                </a>
            </div>
        </div>
    </div>

    <!-- Live CCTV Grid -->
    <div class="wm-card mb-4 bg-dark text-white border-0">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-white">
                    <i class="fas fa-video text-danger me-2"></i>Pantauan Kamera CCTV Kawasan Sukamiskin Arcamanik
                </h5>
                <small class="text-secondary">Siaran langsung pemantauan portal utama, persimpangan jalan, dan pos ronda 24 jam.</small>
            </div>
            <span class="badge bg-danger rounded-pill px-3 py-1"><span class="wm-pulse-dot bg-white me-1 d-inline-block"></span> 24 Jam Aktif</span>
        </div>

        <div class="row g-3">
            @forelse($cctvs as $cam)
                <div class="col-md-6 col-lg-3">
                    <div class="rounded-4 overflow-hidden position-relative bg-black" style="aspect-ratio: 16/9; border: 1px solid rgba(255,255,255,0.1);">
                        @if(preg_match('/mjpe?g|\.cgi|snapshot|\.jpg|\.png|unsplash/i', $cam->stream_url))
                            <img src="{{ $cam->stream_url }}" alt="{{ $cam->nama }}" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <video src="{{ $cam->stream_url }}" class="w-100 h-100" style="object-fit: cover;" autoplay muted playsinline controls></video>
                        @endif
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2 fw-bold" style="font-size: 0.7rem;">LIVE CAM</span>
                        <div class="position-absolute bottom-0 start-0 end-0 p-2 bg-gradient text-white" style="background: linear-gradient(transparent, rgba(0,0,0,0.85));">
                            <strong class="d-block small text-truncate">{{ $cam->nama }}</strong>
                            <small class="text-secondary d-block text-truncate" style="font-size: 0.75rem;">{{ $cam->lokasi ?? 'Sukamiskin Arcamanik' }}</small>
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

    <!-- Jadwal Ronda Bergilir RT 001 - RT 008 (Dinamis dari Database Warga) -->
    <div class="wm-card">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i data-lucide="calendar" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Jadwal Ronda Malam & Siskamling RW 013 (Terhubung Data Warga)
                </h5>
                <small class="text-muted">Data koordinator piket diambil langsung dari data warga/pengurus terdaftar di database.</small>
            </div>
            <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill">
                Wilayah: Sukamiskin, Arcamanik
            </span>
        </div>

        <div class="table-responsive">
            <table class="wm-table align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 130px;">Hari & Jam</th>
                        <th style="min-width: 140px;">Cakupan RT</th>
                        <th style="min-width: 240px;">Koordinator Piket (Warga Terdaftar)</th>
                        <th style="min-width: 200px;">Pos Ronda & Area Patroli</th>
                        <th style="min-width: 130px;">Kekuatan Regu</th>
                        <th style="min-width: 110px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalRonda as $r)
                        @php
                            $isToday = str_contains($r->hari, 'Malam Ini') || (date('l') == 'Wednesday' && str_contains($r->hari, 'Rabu'));
                        @endphp
                        <tr class="{{ $isToday ? 'table-warning' : '' }}">
                            <td>
                                <strong class="text-dark d-block">{{ $r->hari }}</strong>
                                <small class="text-muted"><i data-lucide="clock" style="width: 12px; height: 12px;"></i> {{ substr($r->jam_mulai, 0, 5) }} - {{ substr($r->jam_selesai, 0, 5) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary text-white px-2 py-1">{{ $r->rt_list }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($r->coordinator1)
                                        <div class="position-relative">
                                            <img src="{{ $r->coordinator1->avatar_url }}" 
                                                 alt="{{ $r->coordinator1->name }}" 
                                                 class="rounded-circle border" 
                                                 style="width: 38px; height: 38px; object-fit: cover;">
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">{{ $r->coordinator1->name }}</span>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                {{ $r->coordinator1->isRt() ? 'Ketua ' . $r->coordinator1->clean_rt_label : ($r->coordinator1->isRw() ? 'Ketua RW 013' : 'Warga ' . $r->coordinator1->clean_rt_label) }}
                                                @if($r->coordinator2)
                                                    &bull; &amp; {{ $r->coordinator2->name }}
                                                @endif
                                            </small>
                                        </div>
                                    @else
                                        <div>
                                            <span class="text-secondary fw-semibold">{{ $r->koordinator_text }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong class="text-dark d-block" style="font-size: 0.85rem;">{{ $r->pos_ronda }}</strong>
                                <small class="text-muted d-block" style="font-size: 0.75rem;"><i data-lucide="navigation" style="width: 11px; height: 11px;"></i> {{ $r->area_patroli }}</small>
                            </td>
                            <td>
                                <span class="text-dark small fw-medium">{{ $r->anggota_label }}</span>
                            </td>
                            <td>
                                @if($r->status === 'Bertugas')
                                    <span class="status-badge status-lengkap"><i data-lucide="radio" style="width: 12px; height: 12px;"></i> Bertugas</span>
                                @elseif($r->status === 'Selesai')
                                    <span class="status-badge status-belum">Selesai</span>
                                @else
                                    <span class="status-badge status-periksa">Terjadwal</span>
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
