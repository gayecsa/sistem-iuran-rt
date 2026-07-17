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
                                <label class="form-label font-weight-bold">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Email (Gunakan untuk Login)</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                                <small class="text-muted d-block mt-1" style="font-size: 0.8rem;"><i class="fas fa-info-circle text-primary me-1"></i> Email ini digunakan saat login ke aplikasi Warkas Machi.</small>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">NIK</label>
                                <input type="text" name="nik" value="{{ old('nik', $user->nik) }}" class="form-control @error('nik') is-invalid @enderror">
                                @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">No. KK</label>
                                <input type="text" name="no_kk" value="{{ old('no_kk', $user->no_kk) }}" class="form-control @error('no_kk') is-invalid @enderror">
                                @error('no_kk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">No. RT</label>
                                <select name="rt_number" class="form-select @error('rt_number') is-invalid @enderror">
                                    @for($i = 1; $i <= 8; $i++)
                                        @php $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $rtStr }}" {{ old('rt_number', $user->rt_number ?? '001') == $rtStr ? 'selected' : '' }}>
                                            RT {{ $rtStr }}
                                        </option>
                                    @endfor
                                </select>
                                @error('rt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">No. RW</label>
                                <input type="text" name="rw_number" value="{{ old('rw_number', $user->rw_number ?? '013') }}" class="form-control @error('rw_number') is-invalid @enderror">
                                @error('rw_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">No. Rumah</label>
                                <input type="text" name="house_number" value="{{ old('house_number', $user->house_number) }}" class="form-control @error('house_number') is-invalid @enderror">
                                @error('house_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">No. Telepon / WhatsApp</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Status Kepemilikan Rumah</label>
                                <select name="status_rumah" class="form-select @error('status_rumah') is-invalid @enderror">
                                    <option value="milik_sendiri" {{ old('status_rumah', $user->status_rumah) == 'milik_sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                                    <option value="sewa_kontrak" {{ old('status_rumah', $user->status_rumah) == 'sewa_kontrak' ? 'selected' : '' }}>Sewa / Kontrak</option>
                                    <option value="menumpang" {{ old('status_rumah', $user->status_rumah) == 'menumpang' ? 'selected' : '' }}>Menumpang</option>
                                </select>
                                @error('status_rumah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label font-weight-bold">Alamat Tempat Tinggal</label>
                                <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- Section Ubah Password Login -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-key text-primary me-2 fa-lg"></i>
                                <h5 class="mb-0 fw-bold text-dark">Ubah Password Login</h5>
                            </div>
                            <p class="text-muted small mb-3">Kosongkan bagian password ini jika Anda tidak ingin mengubah password akun Anda.</p>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Password Saat Ini</label>
                                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Password lama Anda">
                                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Password Baru</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 6 karakter">
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                                </div>
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