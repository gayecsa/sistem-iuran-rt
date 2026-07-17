@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0 p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="mb-1">Tambah Warga Baru</h3>
                <p class="text-muted mb-0">Isi biodata warga lingkungan RW 013 (RT 001 - RT 008) Warkas Machi.</p>
            </div>
            <a href="{{ route('warga.index') }}" class="btn btn-secondary text-white rounded-pill px-3">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <i class="fas fa-exclamation-circle me-2"></i> Terdapat kesalahan pada input Anda. Silakan periksa kolom di bawah.
            </div>
        @endif

        <form action="{{ route('warga.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="no_kk" class="form-label fw-semibold">No. Kartu Keluarga (KK)</label>
                    <input type="text" class="form-control @error('no_kk') is-invalid @enderror" id="no_kk" name="no_kk" value="{{ old('no_kk') }}" maxlength="16" placeholder="Masukkan 16 digit No. KK">
                    @error('no_kk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="nik" class="form-label fw-semibold">NIK (KTP)</label>
                    <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="Masukkan 16 digit NIK">
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">Password Akun Warga</label>
                    <div class="input-group">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="wargaPassword" name="password" required placeholder="Masukkan password akun">
                        <button type="button" class="btn btn-outline-secondary px-3" onclick="togglePasswordVisibility('wargaPassword', 'eyeIconWarga')" title="Lihat Password">
                            <i class="fas fa-eye" id="eyeIconWarga"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">No. Telepon / WhatsApp</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="rt_number" class="form-label fw-semibold">No. RT</label>
                    <select class="form-select @error('rt_number') is-invalid @enderror" id="rt_number" name="rt_number" required>
                        <option value="" disabled>-- Pilih RT --</option>
                        @for($i = 1; $i <= 8; $i++)
                            @php $rtVal = 'RT ' . str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $rtVal }}" {{ old('rt_number', 'RT 001') == $rtVal ? 'selected' : '' }}>{{ $rtVal }}</option>
                        @endfor
                    </select>
                    @error('rt_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="rw_number" class="form-label fw-semibold">No. RW</label>
                    <input type="text" class="form-control @error('rw_number') is-invalid @enderror" id="rw_number" name="rw_number" value="{{ old('rw_number', 'RW 013') }}" required readonly style="background-color: #f8fafc;">
                    @error('rw_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="house_number" class="form-label fw-semibold">No. Rumah</label>
                    <input type="text" class="form-control @error('house_number') is-invalid @enderror" id="house_number" name="house_number" value="{{ old('house_number') }}">
                    @error('house_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
    <label for="status_rumah" class="form-label fw-semibold">Status Rumah</label>
    <select class="form-select @error('status_rumah') is-invalid @enderror" id="status_rumah" name="status_rumah" required>
        <option value="">-- Pilih Status --</option>
        <option value="milik_sendiri" {{ old('status_rumah') == 'milik_sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
        <option value="kontrak" {{ old('status_rumah') == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
        <option value="sewa" {{ old('status_rumah') == 'sewa' ? 'selected' : '' }}>Sewa</option>
    </select>
    @error('status_rumah')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="address" class="form-label fw-semibold">Alamat Lengkap</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" placeholder="Contoh: Jl. Merdeka Raya Blok A1..." style="height: 58px;">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="gender" class="form-label fw-semibold">Gender</label>
                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                        <option value="" selected disabled>-- Pilih Gender --</option>
                        <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="tanggal_lahir" class="form-label fw-semibold">Tanggal Lahir</label>
                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                    @error('tanggal_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="reset" class="btn btn-light me-2">Reset</button>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i>Simpan Warga
                </button>
            </div>
        </form>
    </div>
</div>
@endsection