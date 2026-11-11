@extends('layouts.app')

@section('content')
<div class="cover-container min-vh-100 d-flex align-items-center py-5" style="background: linear-gradient(135deg, #d4f3ff 0%, #fbe4f3 45%, #fff9e6 100%);">
    <div class="container py-4">
        <div class="row align-items-stretch gy-4">
            
            <!-- Left Card: Hero Title & Description -->
            <div class="col-lg-6 d-flex">
                <div class="card cover-card border-0 shadow-lg p-4 p-md-5 w-100 d-flex flex-column justify-content-between" style="border-radius: 28px; background: rgba(255,255,255,0.96);">
                    <div>
                        <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm mb-3 fs-6">
                            <i class="fas fa-city me-1"></i> Selamat Datang di Warkas Machi RW 013
                        </span>
                        <h1 class="display-5 fw-bold text-dark mb-3">Sistem Informasi & Layanan Terpadu RW 013</h1>
                        <p class="lead text-muted mb-4" style="font-size: 1.05rem;">
                            Platform digital modern untuk warga <strong>RT 001 s/d RT 008</strong> di wilayah RW 013. Pantau laporan kas & iuran warga, jadwal Posyandu kesehatan, lokasi UMKM kuliner, penerbitan surat digital, hingga rute navigasi wisata terdekat dalam satu tempat.
                        </p>
                    </div>

                    <div class="p-3 rounded-4 bg-light border info-footer-box">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-primary text-white shadow-sm flex-shrink-0" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-shield-alt fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Aman, Transparan, & Terintegrasi</h6>
                                <small class="text-muted">Akses informasi real-time dan terpercaya untuk seluruh warga RW 013.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card: 4 Upgraded Feature Cards + Action Buttons Below Them -->
            <div class="col-lg-6 d-flex">
                <div class="card cover-card border-0 shadow-lg p-4 w-100 d-flex flex-column justify-content-between" style="border-radius: 28px; background: rgba(255,255,255,0.96);">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-th-large text-primary me-2"></i>Layanan Utama RW 013</h5>
                            <span class="badge bg-success-subtle text-success border px-3 py-1 rounded-pill">RT 001 - RT 008</span>
                        </div>

                        <!-- 4 Feature Cards Grid -->
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 h-100 shadow-sm border feature-box feature-box-1" style="background: linear-gradient(135deg, #e0f2fe, #f5f0ff);">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="icon-circle bg-white text-primary shadow-sm" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                            <i class="fas fa-users-cog"></i>
                                        </div>
                                        <span class="badge bg-primary small">Keuangan</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Data Warga & Kas</h6>
                                    <p class="small text-muted mb-0">Kelola iuran & laporan kas transparan.</p>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 h-100 shadow-sm border feature-box feature-box-2" style="background: linear-gradient(135deg, #d1fae5, #f4ecff);">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="icon-circle bg-white text-success shadow-sm" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                            <i class="fas fa-heartbeat"></i>
                                        </div>
                                        <span class="badge bg-success small">Kesehatan</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Posyandu & Ibu Hamil</h6>
                                    <p class="small text-muted mb-0">Jadwal imunisasi & edukasi keluarga.</p>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 h-100 shadow-sm border feature-box feature-box-3" style="background: linear-gradient(135deg, #fef08a, #fff0f8);">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="icon-circle bg-white text-warning shadow-sm" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                            <i class="fas fa-store"></i>
                                        </div>
                                        <span class="badge bg-warning text-dark small">8 UMKM</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">UMKM & Menu</h6>
                                    <p class="small text-muted mb-0">Menu harga & rute lokasi kuliner.</p>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 h-100 shadow-sm border feature-box feature-box-4" style="background: linear-gradient(135deg, #e8daef, #e5fff0);">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="icon-circle bg-white text-primary shadow-sm" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                            <i class="fas fa-map-marked-alt text-primary"></i>
                                        </div>
                                        <span class="badge bg-secondary small">Peta Rute</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Surat & Wisata</h6>
                                    <p class="small text-muted mb-0">Peta destinasi & surat digital.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Masuk & Daftar Akun DI BAWAH 4 Kotak -->
                    <div class="mt-4 pt-3 border-top">
                        <p class="text-muted small mb-2"><i class="fas fa-arrow-circle-right me-1 text-primary"></i> Akses layanan Warkas Machi RW 013:</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm fw-bold flex-grow-1">
                                <i class="fas fa-sign-in-alt me-2"></i>Masuk
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4 shadow-sm fw-bold flex-grow-1">
                                <i class="fas fa-user-plus me-2"></i>Daftar Akun
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
