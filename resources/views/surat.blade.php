@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 animate__animated animate__fadeInUp">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small">Penerbitan Surat</p>
                        <h2 class="mb-2">Layanan Penerbitan Surat RT 001</h2>
                        <p class="text-muted mb-0">Urus penerbitan surat keterangan dari RT dengan cepat dan mudah.</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-soft">Kembali ke Dashboard</a>
                </div>

                <div class="row g-3 mt-4">
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm hover" style="cursor: pointer; transition: all 0.3s;">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">Surat Keterangan Domisili</h5>
                                        <p class="text-muted small mb-2">Surat bukti tempat tinggal resmi dari RT</p>
                                    </div>
                                    <i class="fas fa-home text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                                <ul class="small text-muted mb-3">
                                    <li>✓ Proses cepat 1-2 hari kerja</li>
                                    <li>✓ Gratis untuk warga RT</li>
                                    <li>✓ Sah secara administratif</li>
                                </ul>
                                <a href="{{ route('surat.create', ['jenis' => 'domisili']) }}" class="btn btn-sm btn-primary btn-soft">
                                    <i class="fas fa-file me-1"></i>Buat Permohonan
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm hover" style="cursor: pointer; transition: all 0.3s;">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">Surat Keterangan Tidak Mampu</h5>
                                        <p class="text-muted small mb-2">Untuk keperluan bantuan sosial & beasiswa</p>
                                    </div>
                                    <i class="fas fa-heart text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                                <ul class="small text-muted mb-3">
                                    <li>✓ Verifikasi langsung ke rumah</li>
                                    <li>✓ Proses 3-5 hari kerja</li>
                                    <li>✓ Berlaku 1 tahun</li>
                                </ul>
                                <a href="{{ route('surat.create', ['jenis' => 'tidak-mampu']) }}" class="btn btn-sm btn-danger btn-soft">
                                    <i class="fas fa-file me-1"></i>Buat Permohonan
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm hover" style="cursor: pointer; transition: all 0.3s;">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">Surat Keterangan Usaha</h5>
                                        <p class="text-muted small mb-2">Untuk kebutuhan mendirikan usaha atau UMKM</p>
                                    </div>
                                    <i class="fas fa-store text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <ul class="small text-muted mb-3">
                                    <li>✓ Surat legalisir dari RT</li>
                                    <li>✓ Proses 1 hari kerja</li>
                                    <li>✓ Biaya Rp 10.000,-</li>
                                </ul>
                                <a href="{{ route('surat.create', ['jenis' => 'usaha']) }}" class="btn btn-sm btn-success btn-soft">
                                    <i class="fas fa-file me-1"></i>Buat Permohonan
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm hover" style="cursor: pointer; transition: all 0.3s;">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">Surat Pengantar</h5>
                                        <p class="text-muted small mb-2">Untuk berbagai keperluan administrasi pemerintah</p>
                                    </div>
                                    <i class="fas fa-envelope text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <ul class="small text-muted mb-3">
                                    <li>✓ Pengantar ke kelurahan/camat</li>
                                    <li>✓ Proses 1 hari kerja</li>
                                    <li>✓ Gratis untuk warga RT</li>
                                </ul>
                                <a href="{{ route('surat.create', ['jenis' => 'pengantar']) }}" class="btn btn-sm btn-warning btn-soft">
                                    <i class="fas fa-file me-1"></i>Buat Permohonan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-4 mt-4">
                    <h5 class="mb-3">Persyaratan Umum</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-2">Persyaratan Dokumen:</h6>
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item">✓ Fotokopi KTP/SIM</li>
                                <li class="list-group-item">✓ Fotokopi Kartu Keluarga</li>
                                <li class="list-group-item">✓ Pas Foto 2x3 (jika diperlukan)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-2">Kontak Admin RT:</h6>
                            <p class="mb-1">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <a href="tel:{{ $admin_contact_phone ?? '0812-3456-7890' }}">{{ $admin_contact_phone ?? '0812-3456-7890' }}</a>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                Balai RT 001, Jl. Gandaria
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover {
        transition: all 0.3s ease;
    }
    .hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection