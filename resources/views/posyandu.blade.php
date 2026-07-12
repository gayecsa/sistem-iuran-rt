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
                    <div class="col-md-6">
                        <div class="card metric-card p-3">
                            <h6>Jadwal Rutin</h6>
                            <p class="mb-1">Setiap Sabtu</p>
                            <p class="text-muted mb-0">Pukul 08.00 - 11.00 WIB</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card metric-card p-3">
                            <h6>Lokasi</h6>
                            <p class="mb-1">Balai RT 001</p>
                            <p class="text-muted mb-0">Jl. Gandaria, Gandaria Selatan</p>
                        </div>
                    </div>
                </div>

                <div class="card p-4 mt-4">
                    <h5 class="mb-3">Layanan yang tersedia</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Pemeriksaan kesehatan balita</li>
                        <li class="list-group-item">Imunisasi dan gizi anak</li>
                        <li class="list-group-item">Konsultasi ibu hamil</li>
                        <li class="list-group-item">Edukasi kesehatan keluarga</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
