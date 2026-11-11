@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%); color: #fff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-light text-primary mb-2 fw-bold px-3 py-1 rounded-pill">MANAJEMEN RT / RW 013</span>
                <h3 class="fw-bold mb-1 text-white">Data Profil 8 Rukun Tetangga (RT 001 - RT 008)</h3>
                <p class="mb-0 text-white opacity-75 small">
                    Daftar lengkap kepengurusan, domisili sekretariat pos warga, kependudukan, dan kontak pengurus RT di RW 013.
                </p>
            </div>
            <a href="{{ route('monitoring-rt.index') }}" class="btn btn-light rounded-pill px-3 fw-bold small text-primary">
                <i data-lucide="clipboard-check" style="width: 16px; height: 16px;"></i> Ke Halaman Audit RT
            </a>
        </div>
    </div>

    <div class="row g-4">
        @foreach($rtList as $rt)
            <div class="col-md-6 col-xl-3">
                <div class="wm-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary text-white rounded-4 fs-5 p-2 px-3 fw-bold">
                                {{ $rt['nomor'] }}
                            </span>
                            <span class="badge bg-light text-muted border">{{ $rt['jumlah_kk'] }} KK &bull; {{ $rt['jumlah_warga'] }} Warga</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $rt['nama'] }} RW 013</h5>
                        <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $rt['alamat_pos'] }}</p>

                        <div class="p-2 rounded-3 bg-light border mb-2 small">
                            <span class="text-muted d-block fw-bold" style="font-size: 0.7rem;">KETUA RT</span>
                            <strong class="text-dark">{{ $rt['ketua_rt'] }}</strong>
                            <div class="text-primary small"><i class="fas fa-phone me-1"></i>{{ $rt['kontak'] }}</div>
                        </div>

                        <div class="p-2 rounded-3 bg-light border mb-2 small">
                            <span class="text-muted d-block fw-bold" style="font-size: 0.7rem;">SEKRETARIS</span>
                            <span class="text-dark fw-semibold">{{ $rt['sekretaris'] }}</span>
                        </div>

                        <div class="p-2 rounded-3 bg-light border mb-3 small">
                            <span class="text-muted d-block fw-bold" style="font-size: 0.7rem;">BENDAHARA</span>
                            <span class="text-dark fw-semibold">{{ $rt['bendahara'] }}</span>
                        </div>
                    </div>

                    <a href="{{ route('monitoring-rt.show', $rt['rt']) }}" class="wm-btn-outline w-100 justify-content-center" style="font-size: 0.82rem;">
                        <i data-lucide="clipboard-check" style="width: 15px; height: 15px;"></i> Detail Evaluasi
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
