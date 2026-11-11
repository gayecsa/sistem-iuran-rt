@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i data-lucide="archive" class="text-primary me-2" style="width: 26px; height: 26px;"></i>
                Arsip & Dokumen Legalitas - {{ $rtLabel }}
            </h4>
            <p class="text-muted small mb-0">
                Penyimpanan digital SK Kepengurusan RT, risalah musyawarah warga, inventaris aset, dan laporan pertanggungjawaban kas.
            </p>
        </div>
        <div>
            <a href="{{ route('dashboard.rt') }}" class="wm-btn-outline">
                <i data-lucide="arrow-left" style="width: 17px; height: 17px;"></i> Dashboard RT
            </a>
        </div>
    </div>

    <div class="row g-3">
        @foreach($arsipList as $arsip)
            <div class="col-md-6">
                <div class="wm-card p-3 h-100 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded bg-light border text-primary">
                            <i data-lucide="file-text" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark border mb-1" style="font-size:0.75rem;">{{ $arsip['kategori'] }}</span>
                            <h6 class="fw-bold mb-1 text-dark">{{ $arsip['nama'] }}</h6>
                            <small class="text-muted">
                                <i data-lucide="calendar" style="width:12px;height:12px;"></i> {{ $arsip['tanggal'] }} &bull; {{ $arsip['ukuran'] }}
                            </small>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary" onclick="alert('Mengunduh berkas {{ $arsip['nama'] }}...')">
                            <i data-lucide="download" style="width: 15px; height: 15px;"></i> Unduh
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
