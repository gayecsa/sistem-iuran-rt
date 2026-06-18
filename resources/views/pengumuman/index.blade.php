@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <h3 class="fw-bold mb-1">Daftar Pengumuman</h3>
            <p class="text-muted">Info penting seputar kegiatan RT Gandaria</p>
        </div>
        <i class="fas fa-bullhorn fa-3x text-muted opacity-25"></i>
    </div>

    @if($semua_pengumuman->count() > 0)
        <div class="row g-4">
            @foreach($semua_pengumuman as $p)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('pengumuman.show', $p->id) }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 animate__animated animate__fadeInUp transition-card" style="border-radius: 20px; overflow: hidden;">
                        <div class="card-header bg-gradient p-4 border-0" style="background: linear-gradient(135deg, {{ $p->kategori == 'Penting' ? '#ffa1c5, #ffb8d9' : ($p->kategori == 'Informasi' ? '#7db7ff, #a9d7ff' : '#52c41a, #7ed321') }});">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="fw-bold text-white mb-0" style="font-size: 1.1rem;">{{ $p->judul }}</h5>
                                <span class="badge bg-white text-dark fw-bold">{{ $p->kategori }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                <i class="fas fa-calendar me-2"></i>
                                {{ \Carbon\Carbon::parse($p->tanggal_aktif)->format('d M Y') }}
                            </p>
                            <p class="card-text text-dark lh-base" style="font-size: 0.95rem;">
                                {{ Str::limit($p->isi, 100, '...') }}
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-3">
                            <small class="text-primary fw-bold">
                                Baca selengkapnya <i class="fas fa-arrow-right ms-2"></i>
                            </small>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <div class="opacity-50 mb-3">
                <i class="fas fa-inbox fa-5x text-muted"></i>
            </div>
            <h5 class="text-muted">Belum ada pengumuman</h5>
            <p class="text-muted">Kembali lagi untuk melihat informasi terbaru RT Gandaria</p>
        </div>
    @endif
</div>

<style>
    .transition-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .transition-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 60px rgba(46, 61, 94, 0.12) !important;
    }

    .card-header {
        background-size: cover;
        background-position: center;
        min-height: 120px;
        display: flex;
        align-items: center;
    }

    .card-text {
        color: #4b5c7a;
        line-height: 1.6;
    }
</style>
@endsection