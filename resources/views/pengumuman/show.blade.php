@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <a href="{{ route('pengumuman.index') }}" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            
            <div class="card p-5 shadow-sm border-0 animate__animated animate__fadeIn">
                <div class="d-flex align-items-start justify-content-between mb-4">
                    <div>
                        <h2 class="fw-bold mb-2">{{ $pengumuman->judul }}</h2>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($pengumuman->tanggal_aktif)->format('d F Y') }}
                        </small>
                    </div>
                    <span class="badge {{ $pengumuman->kategori == 'Penting' ? 'bg-pink' : ($pengumuman->kategori == 'Informasi' ? 'bg-info' : 'bg-success') }} text-white fs-6">
                        {{ $pengumuman->kategori }}
                    </span>
                </div>

                <hr class="my-4">

                <div class="content lh-lg">
                    {!! nl2br($pengumuman->isi) !!}
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Dibuat pada {{ \Carbon\Carbon::parse($pengumuman->created_at)->format('d M Y H:i') }}
                    </small>
                    <a href="{{ route('pengumuman.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i>Lihat Semua
                    </a>
                </div>
            </div>

            <!-- Related Announcements -->
            @if($relatedAnnouncements ?? false)
            <div class="mt-5">
                <h4 class="fw-bold mb-3">Pengumuman Lainnya</h4>
                <div class="row g-3">
                    @foreach($relatedAnnouncements as $related)
                    <div class="col-md-6">
                        <a href="{{ route('pengumuman.show', $related->id) }}" class="text-decoration-none">
                            <div class="card p-3 h-100 shadow-sm border-0 transition" style="transition: transform 0.3s ease;">
                                <h6 class="fw-bold text-dark">{{ $related->judul }}</h6>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($related->tanggal_aktif)->format('d M Y') }}</small>
                                <span class="badge {{ $related->kategori == 'Penting' ? 'bg-pink' : ($related->kategori == 'Informasi' ? 'bg-info' : 'bg-success') }} text-white mt-2 d-inline-block">{{ $related->kategori }}</span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 20px;
    }

    .content {
        font-size: 1.05rem;
        color: #2d3d5e;
        line-height: 1.8;
    }

    .content p {
        margin-bottom: 1rem;
    }

    .transition:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 12px 30px rgba(46, 61, 94, 0.12) !important;
    }
</style>
@endsection
