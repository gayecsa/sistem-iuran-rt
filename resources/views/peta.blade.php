@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card p-4 border-0 shadow-sm mb-4 header-card animate__animated animate__fadeInUp" style="border-radius: 16px;">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small fw-bold">Peta Wilayah</p>
                        <h2 class="mb-2 fw-bold text-dark">Peta Wilayah RW 013</h2>
                        <p class="text-muted mb-0">Informasi mengenai batas wilayah dan lokasi penting di lingkungan RW 013 (RT 001 - RT 008).</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4 shadow-sm border">Kembali ke Dashboard</a>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-2 animate__animated animate__fadeInUp" style="border-radius: 16px; overflow: hidden;">
                <!-- Menggunakan Google Maps Embed API (contoh koordinat/area) -->
                <div class="map-container" style="position: relative; width: 100%; padding-bottom: 56.25%; height: 0;">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15865.558284568603!2d106.82271815!3d-6.2122676!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e9b1d120a1%3A0x6b6900f9fc5b42df!2sJakarta!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; border-radius: 12px;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <div class="card-body mt-3 pb-2">
                    <h5 class="fw-bold mb-3"><i class="fas fa-map-marker-alt text-danger me-2"></i> Keterangan Lokasi Penting</h5>
                    <div class="row gy-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle shadow-sm bg-light text-primary" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-mosque"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Tempat Ibadah</h6>
                                    <p class="small text-muted mb-0">Masjid terdekat berada di pusat wilayah.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle shadow-sm bg-light text-success" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Pos Keamanan</h6>
                                    <p class="small text-muted mb-0">Pos Satpam di pintu masuk utama RT.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle shadow-sm bg-light text-info" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-clinic-medical"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Posyandu</h6>
                                    <p class="small text-muted mb-0">Lokasi kegiatan kesehatan bulanan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
