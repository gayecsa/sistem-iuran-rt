@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #d4f3ff 0%, #fbe4f3 45%, #fff9e6 100%);">
    <div class="container py-5">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg p-5" style="border-radius: 28px; background: rgba(255,255,255,0.96);">
                    <span class="badge bg-primary mb-3">Selamat Datang di RT 001</span>
                    <h1 class="display-5 fw-bold">Kelola Iuran dan Data Warga dengan Mudah</h1>
                    <p class="lead text-muted mb-4">Sistem iuran RT lengkap untuk admin, bendahara, dan warga. Pantau pembayaran, data warga, dan laporan keuangan dalam satu tempat.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-4">
                            <i class="fas fa-user-plus me-2"></i>Daftar Akun
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg p-4" style="border-radius: 28px; background: rgba(255,255,255,0.96);">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #e7f7ff, #f5f0ff);">
                                <h5 class="mb-2">Data Warga</h5>
                                <p class="mb-0 text-primary fw-semibold">Mudah dikelola dan terpusat</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #fff4e8, #f4ecff);">
                                <h5 class="mb-2">Iuran</h5>
                                <p class="mb-0 text-warning fw-semibold">Lacak pembayaran per warga</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #e5fff0, #e8f8ff);">
                                <h5 class="mb-2">Laporan</h5>
                                <p class="mb-0 text-success fw-semibold">Laporan kas otomatis</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #fff0f8, #f7f0ff);">
                                <h5 class="mb-2">Aman</h5>
                                <p class="mb-0 text-danger fw-semibold">Hanya akun terdaftar yang bisa akses</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
