@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0 p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="mb-1">Tambah Warga Baru</h3>
                <p class="text-muted mb-0">Isi biodata warga RT 001.</p>
            </div>
            <a href="{{ route('warga.index') }}" class="btn btn-secondary text-white rounded-pill px-3">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <form action="{{ route('warga.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="no_kk" class="form-label fw-semibold">No. Kartu Keluarga (KK)</label>
                    <input type="text" class="form-control" id="no_kk" name="no_kk" value="{{ old('no_kk') }}" maxlength="16" placeholder="Masukkan 16 digit No. KK">
                </div>
                <div class="col-md-6">
                    <label for="nik" class="form-label fw-semibold">NIK (KTP)</label>
                    <input type="text" class="form-control" id="nik" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="Masukkan 16 digit NIK">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">Password Akun Warga</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">No. Telepon / WhatsApp</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="no_rumah" class="form-label fw-semibold">No. Rumah</label>
                    <input type="text" class="form-control" id="no_rumah" name="no_rumah" value="{{ old('no_rumah') }}">
                </div>
                <div class="col-md-6">
                    <label for="status_rumah" class="form-label fw-semibold">Status Rumah</label>
                    <select class="form-select" id="status_rumah" name="status_rumah">
                        <option value="Milik Sendiri" {{ old('status_rumah') == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                        <option value="Kontrak/Sewa" {{ old('status_rumah') == 'Kontrak/Sewa' ? 'selected' : '' }}>Kontrak/Sewa</option>
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="address" class="form-label fw-semibold">Alamat Lengkap</label>
                    <textarea class="form-control" id="address" name="address" rows="3" placeholder="Contoh: Jl. Merdeka Raya Blok A1..." style="height: 58px;">{{ old('address') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="gender" class="form-label fw-semibold">Gender</label>
                    <select class="form-select" id="gender" name="gender" style="height: 58px;" required>
                        <option value="" selected disabled>-- Pilih Gender --</option>
                        <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
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