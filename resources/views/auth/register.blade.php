@extends('layouts.app')

@section('content')
<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-6">
        <div class="card animate__animated animate__fadeInUp">
            <div class="card-header bg-transparent text-center pt-4">
                <i class="fas fa-users fa-4x text-success mb-3"></i>
                <h3 class="fw-bold">Pendaftaran Warga</h3>
                <p class="text-muted">Bergabung dengan RT 001</p>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-user me-2"></i>Nama Lengkap
                            </label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" id="regPassword" class="form-control @error('password') is-invalid @enderror" required placeholder="Min. 6 karakter">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="togglePasswordVisibility('regPassword', 'eyeIconReg')" title="Lihat Password">
                                    <i class="fas fa-eye" id="eyeIconReg"></i>
                                </button>
                                @error('password')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock me-2"></i>Konfirmasi Password
                            </label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="regPasswordConfirm" class="form-control" required placeholder="Ulangi password">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="togglePasswordVisibility('regPasswordConfirm', 'eyeIconRegConfirm')" title="Lihat Password">
                                    <i class="fas fa-eye" id="eyeIconRegConfirm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-home me-2"></i>No. Rumah
                            </label>
                            <input type="text" name="house_number" class="form-control @error('house_number') is-invalid @enderror" 
                                   value="{{ old('house_number') }}" required>
                            @error('house_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-phone me-2"></i>No. Telepon
                            </label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}" required>
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-building me-2"></i>Status Rumah
                            </label>
                            <select name="status_rumah" class="form-control">
                                <option value="milik_sendiri">Milik Sendiri</option>
                                <option value="kontrak">Kontrak</option>
                                <option value="sewa">Sewa</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Alamat Lengkap
                        </label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                                  rows="3" required>{{ old('address') }}</textarea>
                        @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <p class="mb-0">Sudah punya akun? 
                        <a href="{{ route('login') }}" class="text-success">Login disini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection