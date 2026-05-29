@extends('layouts.app')

@section('content')
<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-5">
        <div class="card animate__animated animate__fadeInUp">
            <div class="card-header bg-transparent text-center pt-4">
                <i class="fas fa-hand-holding-usd fa-4x text-primary mb-3"></i>
                <h3 class="fw-bold">Login</h3>
                <p class="text-muted">La Caisse</p>
            </div>
            <div class="card-body p-4">

                {{-- ================= TAMBAHAN: NOTIFIKASI SUKSES / GAGAL ================= --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                {{-- ======================================================================= --}}

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email Address
                        </label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="mb-0">Belum punya akun? 
                        <a href="{{ route('register') }}" class="text-primary">Daftar Sekarang</a>
                    </p>
                                        </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection