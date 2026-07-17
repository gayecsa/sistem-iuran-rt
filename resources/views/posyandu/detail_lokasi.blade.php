@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 animate__animated animate__fadeInUp">
                
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small">Informasi Posyandu</p>
                        <h2 class="mb-2">Daftar Lokasi Posyandu se-RW</h2>
                    </div>
                    <a href="{{ route('posyandu') }}" class="btn btn-soft">Kembali</a>
                </div>

                <div class="alert alert-info">
                    Berikut adalah daftar lokasi pelayanan Posyandu yang tersebar di lingkungan RW. Anda dapat mengunjungi lokasi terdekat dari tempat tinggal Anda.
                </div>

                <div class="row g-3 mt-3">
                    
                    <!-- Lokasi 1 -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 p-3 h-100 shadow-sm">
                            <h6 class="mb-1 text-primary fw-bold">Posyandu Melati 1 (RT 001)</h6>
                            <p class="mb-1 small"><strong>Lokasi:</strong> Balai RT 001, Jl. Gandaria</p>
                            <p class="text-muted mb-0 small">Jadwal: Sabtu, Minggu Pertama</p>
                        </div>
                    </div>

                    <!-- Lokasi 2 -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 p-3 h-100 shadow-sm">
                            <h6 class="mb-1 text-primary fw-bold">Posyandu Melati 2 (RT 002)</h6>
                            <p class="mb-1 small"><strong>Lokasi:</strong> Lapangan Serbaguna RT 002</p>
                            <p class="text-muted mb-0 small">Jadwal: Sabtu, Minggu Kedua</p>
                        </div>
                    </div>

                    <!-- Lokasi 3 -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 p-3 h-100 shadow-sm">
                            <h6 class="mb-1 text-primary fw-bold">Posyandu Melati 3 (RT 003)</h6>
                            <p class="mb-1 small"><strong>Lokasi:</strong> Rumah Kader Posyandu RT 003</p>
                            <p class="text-muted mb-0 small">Jadwal: Sabtu, Minggu Ketiga</p>
                        </div>
                    </div>

                    <!-- Lokasi 4 -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 p-3 h-100 shadow-sm">
                            <h6 class="mb-1 text-primary fw-bold">Posyandu Melati 4 (RT 004)</h6>
                            <p class="mb-1 small"><strong>Lokasi:</strong> Gedung PAUD RW 002</p>
                            <p class="text-muted mb-0 small">Jadwal: Sabtu, Minggu Keempat</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection