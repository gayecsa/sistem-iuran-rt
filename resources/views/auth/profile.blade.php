@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">Edit Profil</h4>
                            <p class="text-muted mb-0">Perbarui nama, email, informasi kontak, dan foto profil Anda.</p>
                        </div>
                        <div class="text-end">
                            <span class="badge gradient-pill">Akun Anda</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> Mohon periksa kembali data yang dimasukkan.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-4 text-center">
                        <div class="d-inline-flex flex-column align-items-center">
                            <img src="{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=F9D8E5&color=4B3A5C' }}"
                                 alt="Foto Profil" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;">
                            
                            <p class="mt-3 mb-1 text-muted">Foto saat ini</p>
                            
                            <form action="{{ route('profile.upload-foto') }}" method="POST" enctype="multipart/form-data" id="formFotoProfil">
                                @csrf
                                <label class="btn btn-soft btn-sm" style="cursor: pointer;">
                                    <i class="fas fa-camera me-2"></i>Pilih Foto Baru
                                    <input type="file" name="foto_profil" class="d-none" accept="image/*" onchange="document.getElementById('formFotoProfil').submit();">
                                </label>
                            </form>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Rumah</label>
                                <input type="text" name="house_number" value="{{ old('house_number', $user->house_number) }}" class="form-control @error('house_number') is-invalid @enderror">
                                @error('house_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Rumah</label>
                                <input type="text" name="status_rumah" value="{{ old('status_rumah', $user->status_rumah) }}" class="form-control @error('status_rumah') is-invalid @enderror">
                                @error('status_rumah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Profil
                            </button>
                        </div>
                    </form>

                    @if($user->role !== 'admin')
                        <div class="mt-4">
                            <form method="POST" action="{{ route('profile.makeAdmin') }}">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-user-shield me-2"></i>Jadikan Saya Admin Permanen
                                </button>
                            </form>
                            <p class="text-muted small mt-2">Tombol ini akan menjadikan akun Anda sebagai admin secara permanen.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection