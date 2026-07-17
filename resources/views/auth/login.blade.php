@extends('layouts.app')

@section('content')
<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-5">
        <div class="card animate__animated animate__fadeInUp">
            <div class="card-header bg-transparent text-center pt-4">
                <div class="brand-icon">
                    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="32" cy="32" r="28" fill="#7db7ff" opacity="0.16" />
                        <path d="M23 24h18a6 6 0 0 1 6 6v4a6 6 0 0 1-6 6H23v-4h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H23v-4Z" fill="none" stroke="#7db7ff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M25 30c0-4 3-7 7-7h5c4 0 7 3 7 7s-3 7-7 7h-5c-4 0-7-3-7-7Z" fill="#7db7ff" opacity="0.4" />
                        <path d="M28 33h8m-4-4v8" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <h3 class="fw-bold">Login</h3>
                <p class="text-muted">Warkas Machi (RW 013)</p>
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
                        <div class="input-group">
                            <input type="password" name="password" id="loginPassword" class="form-control @error('password') is-invalid @enderror" required placeholder="Masukkan password Anda">
                            <button type="button" class="btn btn-outline-secondary px-3" onclick="togglePasswordVisibility('loginPassword', 'eyeIconLogin')" title="Tampilkan / Sembunyikan Password">
                                <i class="fas fa-eye" id="eyeIconLogin"></i>
                            </button>
                            @error('password')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>

                <div class="mt-3 text-center">
                    <a href="{{ route('password.request') }}" class="text-secondary small">Lupa Password?</a>
                </div>

                <div class="text-center mt-4">
                    <p class="mb-0">Belum punya akun? 
                        <a href="{{ route('register') }}" class="text-primary">Daftar Sekarang</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection