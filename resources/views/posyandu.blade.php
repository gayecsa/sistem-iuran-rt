@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 animate__animated animate__fadeInUp">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small">Posyandu</p>
                        <h2 class="mb-2">Layanan Posyandu RT 001</h2>
                        <p class="text-muted mb-0">Informasi jadwal dan layanan kesehatan keluarga, balita, dan ibu hamil.</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-soft">Kembali ke Dashboard</a>
                </div>

                <div class="row g-3 mt-4">
                    <!-- KOTAK JADWAL YANG BISA DIKLIK -->
                    <div class="col-md-6">
                        <a href="{{ route('posyandu.jadwal') }}" class="text-decoration-none" style="color: inherit;">
                            <div class="card metric-card p-3 h-100 shadow-sm" style="cursor: pointer;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 text-primary">Jadwal Rutin</h6>
                                    <span class="badge bg-primary rounded-pill" style="font-size: 0.7rem;">Lihat Detail</span>
                                </div>
                                <p class="mb-1">Setiap Sabtu</p>
                                <p class="text-muted mb-0">Pukul 08.00 - 11.00 WIB</p>
                            </div>
                        </a>
                    </div>
                    
                    <!-- KOTAK LOKASI -->
                    <div class="col-md-6">
                        <a href="{{ route('posyandu.lokasi') }}" class="text-decoration-none" style="color: inherit;">
                            <div class="card metric-card p-3 h-100 shadow-sm" style="cursor: pointer;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 text-primary">Lokasi</h6>
                                    <span class="badge bg-primary rounded-pill" style="font-size: 0.7rem;">Lihat Semua RW</span>
                                </div>
                                <p class="mb-1">Balai RT 001</p>
                                <p class="text-muted mb-0">Jl. Gandaria, Gandaria Selatan</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="card p-4 mt-4">
                    <h5 class="mb-3">Layanan yang tersedia</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="{{ route('posyandu.detail_balita') }}" class="text-decoration-none text-primary fw-medium d-block w-100">
                                Pemeriksaan kesehatan balita
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('posyandu.detail_imunisasi') }}" class="text-decoration-none text-primary fw-medium d-block w-100">
                                Imunisasi dan gizi anak
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('posyandu.detail_ibu_hamil') }}" class="text-decoration-none text-primary fw-medium d-block w-100">
                                Konsultasi ibu hamil
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('posyandu.detail_edukasi') }}" class="text-decoration-none text-primary fw-medium d-block w-100">
                                Edukasi kesehatan keluarga
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection