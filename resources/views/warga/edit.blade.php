@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card p-4 shadow-sm">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1">Edit Data Warga</h4>
                <p class="text-muted mb-0">Perbarui biodata warga lingkungan RW 013 (RT 001 - RT 008) Warkas Machi.</p>
            </div>
            <a href="{{ route('warga.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('warga.update', $warga) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $warga->name) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" value="{{ $warga->email }}" class="form-control" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password Baru <small class="text-muted">(opsional)</small></label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. Rumah</label>
                    <input type="text" value="{{ $warga->house_number }}" class="form-control" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $warga->phone) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Rumah</label>
                    <select name="status_rumah" class="form-select" required>
                        <option value="milik_sendiri" {{ old('status_rumah', $warga->status_rumah) == 'milik_sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                        <option value="kontrak" {{ old('status_rumah', $warga->status_rumah) == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                        <option value="sewa" {{ old('status_rumah', $warga->status_rumah) == 'sewa' ? 'selected' : '' }}>Sewa</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik', $warga->nik) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. KK</label>
                    <input type="text" name="no_kk" value="{{ old('no_kk', $warga->no_kk) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">No. RT</label>
                    <select name="rt_number" class="form-select" required>
                        @for($i = 1; $i <= 8; $i++)
                            @php $rtVal = 'RT ' . str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $rtVal }}" {{ old('rt_number', $warga->rt_number) == $rtVal || old('rt_number', $warga->rt_number) == str_pad($i, 3, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ $rtVal }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">No. RW</label>
                    <input type="text" name="rw_number" value="{{ old('rw_number', $warga->rw_number ?? 'RW 013') }}" class="form-control" readonly style="background-color: #f8fafc;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">-- Pilih Gender --</option>
                        <option value="Laki-laki" {{ old('gender', $warga->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender', $warga->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $warga->tanggal_lahir ? \Carbon\Carbon::parse($warga->tanggal_lahir)->format('Y-m-d') : '') }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" rows="3" class="form-control" required>{{ old('address', $warga->address) }}</textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection