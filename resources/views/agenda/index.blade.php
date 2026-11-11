@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%); color: #fff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-light text-primary mb-2 fw-bold px-3 py-1 rounded-pill">MANAJEMEN KOORDINASI RW</span>
                <h3 class="fw-bold mb-1 text-white">Agenda Kegiatan & Rapat Koordinasi RW 013</h3>
                <p class="mb-0 text-white opacity-75 small">
                    Jadwal musyawarah warga, rapat koordinasi pleno pengurus RT/RW, dan kegiatan sosial lingkungan RW 013.
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-3 fw-bold small text-primary">
                <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i> Ke Dashboard
            </a>
        </div>
    </div>

    <div class="wm-card">
        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
            <i data-lucide="calendar-days" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
            Daftar Agenda Kegiatan Terjadwal RW 013
        </h5>

        <div class="d-flex flex-column gap-3">
            @foreach($agendaList as $agenda)
                <div class="p-4 rounded-4 border bg-light d-flex align-items-start gap-3 flex-wrap flex-md-nowrap">
                    <div class="p-3 rounded-4 text-center bg-white border shadow-sm" style="min-width: 110px;">
                        <i data-lucide="calendar" class="text-primary mx-auto mb-1" style="width: 26px; height: 26px;"></i>
                        <h6 class="fw-bold text-dark mb-0">{{ explode(',', $agenda['tanggal'])[0] }}</h6>
                        <small class="text-muted d-block" style="font-size: 0.72rem;">{{ explode(',', $agenda['tanggal'])[1] ?? '' }}</small>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap mb-2">
                            <h5 class="fw-bold text-dark mb-0">{{ $agenda['judul'] }}</h5>
                            <span class="badge {{ $agenda['badge'] }} px-3 py-2 rounded-pill">{{ $agenda['status'] }}</span>
                        </div>
                        <div class="text-muted small mb-2">
                            <span class="me-3"><i class="far fa-clock text-primary me-1"></i>{{ $agenda['waktu'] }}</span>
                            <span class="me-3"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $agenda['lokasi'] }}</span>
                            <span><i class="fas fa-users text-success me-1"></i>Peserta: {{ $agenda['peserta'] }}</span>
                        </div>
                        <p class="text-secondary small mb-0 p-3 rounded-3 bg-white border">
                            <strong>Deskripsi Kegiatan:</strong> {{ $agenda['agenda'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
