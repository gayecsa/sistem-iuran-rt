@extends('layouts.app')

@section('title', 'Formulir Pengajuan ' . $letterType->nama)

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" style="max-width: 1400px;">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i data-lucide="home" class="me-1" style="width:14px;height:14px;"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('surat.index') }}" class="text-decoration-none text-muted">Layanan Surat</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Pengajuan Baru</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span>{{ $letterType->nama }}</span>
                <span class="badge bg-primary text-white rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                    {{ $letterType->singkatan ?: strtoupper(substr($letterType->kode, 0, 4)) }}
                </span>
            </h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                {{ $letterType->deskripsi }}
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            {{-- Quick Switch Service Dropdown --}}
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle d-inline-flex align-items-center gap-2 shadow-sm rounded-3" style="font-size: 0.88rem;" type="button" data-bs-toggle="dropdown">
                    <i data-lucide="shuffle" style="width:16px;height:16px;"></i>
                    <span>Ganti Jenis Surat</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2" style="max-height: 380px; overflow-y: auto; width: 320px; font-size: 0.84rem;">
                    <li class="dropdown-header fw-bold text-uppercase text-secondary" style="font-size: 0.72rem;">Pilih dari 18 Jenis Layanan:</li>
                    @foreach($allTypes as $ot)
                        <li>
                            <a class="dropdown-item rounded-2 py-2 d-flex align-items-center justify-content-between {{ $ot->kode === $letterType->kode ? 'active fw-bold' : '' }}" href="{{ route('surat.create', ['jenis' => $ot->kode]) }}">
                                <span class="text-truncate me-2">{{ $ot->nama }}</span>
                                <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">{{ $ot->singkatan }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <a href="{{ route('surat.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-2 shadow-sm rounded-3" style="font-size: 0.88rem;">
                <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- Meta Banner: Alur Persetujuan & Estimasi --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: #ffffff; border-left: 4px solid #1456A0 !important;">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center">
                    <i data-lucide="{{ $letterType->icon }}" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark small">Kategori: {{ $letterType->kategori_label }}</div>
                    <div class="text-muted small">Alur Proses: <strong class="text-primary">{{ $letterType->alur_label }}</strong></div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 text-md-end small">
                <div>
                    <span class="text-muted d-block">Estimasi Selesai:</span>
                    <strong class="text-dark">{{ $letterType->estimasi_hari }}</strong>
                </div>
                <div class="border-start ps-3">
                    <span class="text-muted d-block">Biaya Administrasi:</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">{{ $letterType->biaya }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Container --}}
    <form action="{{ route('surat.store') }}" method="POST" enctype="multipart/form-data" id="formSurat" novalidate>
        @csrf
        <input type="hidden" name="jenis_surat" value="{{ $letterType->kode }}" id="input_jenis_surat">

        {{-- STEPPER NAVIGATION INDICATOR --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center position-relative px-2 px-md-5 py-2">
                
                {{-- Line connecting steps --}}
                <div class="position-absolute start-0 end-0 top-50 translate-middle-y mx-4 mx-md-5 d-none d-md-block" style="height: 3px; background: #E2E8F0; z-index: 1;">
                    <div id="stepperProgressBar" style="height: 100%; width: 0%; background: #1456A0; transition: width 0.3s ease;"></div>
                </div>

                {{-- Step 1 --}}
                <div class="stepper-item active position-relative text-center" id="stepIndicator-1" style="z-index: 2;" onclick="goToStep(1)">
                    <div class="step-circle mx-auto rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                        <i data-lucide="user" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="step-label mt-2 small fw-bold">1. Identitas</div>
                </div>

                {{-- Step 2 --}}
                <div class="stepper-item position-relative text-center" id="stepIndicator-2" style="z-index: 2;" onclick="goToStep(2)">
                    <div class="step-circle mx-auto rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                        <i data-lucide="map-pin" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="step-label mt-2 small fw-bold">2. Alamat</div>
                </div>

                {{-- Step 3 --}}
                <div class="stepper-item position-relative text-center" id="stepIndicator-3" style="z-index: 2;" onclick="goToStep(3)">
                    <div class="step-circle mx-auto rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                        <i data-lucide="file-text" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="step-label mt-2 small fw-bold">3. Keperluan</div>
                </div>

                {{-- Step 4 --}}
                <div class="stepper-item position-relative text-center" id="stepIndicator-4" style="z-index: 2;" onclick="goToStep(4)">
                    <div class="step-circle mx-auto rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                        <i data-lucide="upload" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="step-label mt-2 small fw-bold">4. Berkas</div>
                </div>

                {{-- Step 5 --}}
                <div class="stepper-item position-relative text-center" id="stepIndicator-5" style="z-index: 2;" onclick="goToStep(5)">
                    <div class="step-circle mx-auto rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                        <i data-lucide="check" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="step-label mt-2 small fw-bold">5. Konfirmasi</div>
                </div>

            </div>
        </div>

        {{-- STEP 1: IDENTITAS LENGKAP PEMOHON --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 step-pane" id="stepPane-1" style="background: #ffffff;">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:0.85rem;">1</span>
                        <span>Identitas Kependudukan Pemohon</span>
                    </h5>
                    <p class="text-muted small mb-0">Data ini diisi otomatis dari database profil Anda. Pastikan keakuratan sebelum melanjutkan.</p>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small">
                    <i data-lucide="shield-check" style="width:14px;height:14px;" class="me-1"></i>Terverifikasi Kependudukan
                </span>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i data-lucide="credit-card" style="width:16px;height:16px;"></i></span>
                        <input type="text" name="nik" id="nik" class="form-control border-start-0 font-monospace" value="{{ old('nik', $user->nik) }}" required placeholder="16 digit NIK resmi">
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Nomor Kartu Keluarga (No. KK) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i data-lucide="users" style="width:16px;height:16px;"></i></span>
                        <input type="text" name="no_kk" id="no_kk" class="form-control border-start-0 font-monospace" value="{{ old('no_kk', $user->no_kk) }}" required placeholder="16 digit No. KK">
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i data-lucide="user" style="width:16px;height:16px;"></i></span>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control border-start-0" value="{{ old('nama_lengkap', $user->name) }}" required placeholder="Nama lengkap pemohon">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Tempat Lahir <span class="text-danger">*</span></label>
                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $user->tempat_lahir ?? 'Bandung') }}" required placeholder="Contoh: Bandung">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Tanggal Lahir <span class="text-danger">*</span></label>
                    @php
                        $tglVal = '1990-01-01';
                        if (old('tanggal_lahir')) {
                            $tglVal = old('tanggal_lahir');
                        } elseif (!empty($user->tanggal_lahir)) {
                            $tglVal = $user->tanggal_lahir instanceof \Carbon\Carbon ? $user->tanggal_lahir->format('Y-m-d') : substr($user->tanggal_lahir, 0, 10);
                        }
                    @endphp
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ $tglVal }}" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $user->gender ?? $user->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $user->gender ?? $user->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Agama <span class="text-danger">*</span></label>
                    <select name="agama" id="agama" class="form-select" required>
                        @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agm)
                            <option value="{{ $agm }}" {{ old('agama', $user->agama ?? 'Islam') === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Status Perkawinan <span class="text-danger">*</span></label>
                    <select name="status_perkawinan" id="status_perkawinan" class="form-select" required>
                        @foreach(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $stat)
                            <option value="{{ $stat }}" {{ old('status_perkawinan', $user->status_perkawinan ?? 'Kawin') === $stat ? 'selected' : '' }}>{{ $stat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Pekerjaan <span class="text-danger">*</span></label>
                    <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" value="{{ old('pekerjaan', $user->pekerjaan ?? 'Karyawan Swasta') }}" required placeholder="Contoh: Karyawan Swasta, Wiraswasta, Pegawai Negeri">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Pendidikan Terakhir <span class="text-danger">*</span></label>
                    <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-select" required>
                        @foreach(['SD / Sederajat', 'SMP / Sederajat', 'SMA / SMK / Sederajat', 'Diploma (D3/D4)', 'Sarjana (S1)', 'Magister (S2)', 'Doktor (S3)'] as $pdk)
                            <option value="{{ $pdk }}" {{ old('pendidikan_terakhir', $user->pendidikan_terakhir ?? 'Sarjana (S1)') === $pdk ? 'selected' : '' }}>{{ $pdk }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">No. Telepon / WhatsApp Aktif <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i data-lucide="phone" style="width:16px;height:16px;"></i></span>
                        <input type="text" name="phone" id="phone" class="form-control border-start-0" value="{{ old('phone', $user->phone ?? '081234567890') }}" required placeholder="Contoh: 081234567890">
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Email Pemohon <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i data-lucide="mail" style="width:16px;height:16px;"></i></span>
                        <input type="email" name="email" id="email" class="form-control border-start-0" value="{{ old('email', $user->email) }}" required placeholder="Email aktif untuk notifikasi">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 rounded-3" onclick="goToStep(2)">
                    <span>Lanjut ke Alamat</span>
                    <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
                </button>
            </div>
        </div>

        {{-- STEP 2: ALAMAT LENGKAP PEMOHON --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 step-pane d-none" id="stepPane-2" style="background: #ffffff;">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:0.85rem;">2</span>
                        <span>Alamat Lengkap & Wilayah Domisili</span>
                    </h5>
                    <p class="text-muted small mb-0">Standar lokasi resmi wilayah binaan Sukamiskin, Arcamanik, Kota Bandung 40293.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill small">
                    Bandung Raya
                </span>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold small text-secondary">Nama Jalan / Gang / Blok <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i data-lucide="map-pin" style="width:16px;height:16px;"></i></span>
                        <input type="text" name="alamat_jalan" id="alamat_jalan" class="form-control border-start-0" value="{{ old('alamat_jalan', $user->alamat ?? 'Jl. Pacantel No. 12') }}" required placeholder="Contoh: Jl. Pacantel, Komplek Sukamiskin">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Nomor Rumah <span class="text-danger">*</span></label>
                    <input type="text" name="no_rumah" id="no_rumah" class="form-control" value="{{ old('no_rumah', $user->no_rumah ?? '12') }}" required placeholder="Contoh: 12 / Blok B-4">
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Rukun Tetangga (RT) <span class="text-danger">*</span></label>
                    <input type="text" name="rt" id="rt" class="form-control bg-light" value="{{ old('rt', $cleanRt) }}" readonly>
                    <span class="text-muted" style="font-size:0.72rem;">Sesuai akun login pemohon</span>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Rukun Warga (RW) <span class="text-danger">*</span></label>
                    <input type="text" name="rw" id="rw" class="form-control bg-light" value="{{ old('rw', $cleanRw) }}" readonly>
                    <span class="text-muted" style="font-size:0.72rem;">RW 013 Sukamiskin</span>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Kelurahan / Desa <span class="text-danger">*</span></label>
                    <input type="text" name="kelurahan" id="kelurahan" class="form-control bg-light" value="{{ old('kelurahan', 'Sukamiskin') }}" readonly>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Kecamatan <span class="text-danger">*</span></label>
                    <input type="text" name="kecamatan" id="kecamatan" class="form-control bg-light" value="{{ old('kecamatan', 'Arcamanik') }}" readonly>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Kota / Kabupaten <span class="text-danger">*</span></label>
                    <input type="text" name="kota" id="kota" class="form-control bg-light" value="{{ old('kota', 'Kota Bandung') }}" readonly>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Provinsi <span class="text-danger">*</span></label>
                    <input type="text" name="provinsi" id="provinsi" class="form-control bg-light" value="{{ old('provinsi', 'Jawa Barat') }}" readonly>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Kode Pos <span class="text-danger">*</span></label>
                    <input type="text" name="kode_pos" id="kode_pos" class="form-control bg-light" value="{{ old('kode_pos', '40293') }}" readonly>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3" onclick="goToStep(1)">
                    <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
                    <span>Kembali</span>
                </button>
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 rounded-3" onclick="goToStep(3)">
                    <span>Lanjut ke Detail Permohonan</span>
                    <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
                </button>
            </div>
        </div>

        {{-- STEP 3: DETAIL PERMOHONAN KHUSUS (DINAMIS SESUAI MASTER JENIS SURAT) --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 step-pane d-none" id="stepPane-3" style="background: #ffffff;">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:0.85rem;">3</span>
                        <span>Detail & Keperluan Khusus Permohonan</span>
                    </h5>
                    <p class="text-muted small mb-0">Isi atribut spesifik untuk penerbitan: <strong>{{ $letterType->nama }}</strong></p>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill small">
                    {{ $letterType->kategori_label }}
                </span>
            </div>

            <div class="row g-3">
                {{-- Keperluan Utama --}}
                <div class="col-12">
                    <label class="form-label fw-semibold small text-secondary">Tujuan / Keperluan Pembuatan Surat <span class="text-danger">*</span></label>
                    <textarea name="keperluan" id="keperluan" rows="3" class="form-control" required placeholder="Jelaskan alasan dan keperluan surat ini secara spesifik...">{{ old('keperluan') }}</textarea>
                    <span class="text-muted" style="font-size:0.75rem;">Contoh: {{ $letterType->tujuan ?: 'Persyaratan administrasi resmi' }}</span>
                </div>

                {{-- DINAMIS FIELD FORM KHUSUS DARI MASTER SURAT --}}
                @if(!empty($letterType->form_fields))
                    <div class="col-12">
                        <div class="p-3 rounded-3 border" style="background: #F8FAFC;">
                            <div class="fw-bold text-primary mb-3 small d-flex align-items-center gap-2">
                                <i data-lucide="sliders" style="width:16px;height:16px;"></i>
                                <span>Informasi Tambahan Khusus {{ $letterType->singkatan }}</span>
                            </div>

                            <div class="row g-3">
                                @foreach($letterType->form_fields as $field)
                                    @php
                                        $fName = $field['name'];
                                        $fLabel = $field['label'];
                                        $fType = $field['type'] ?? 'text';
                                        $fReq = $field['required'] ?? false;
                                        $fPlaceholder = $field['placeholder'] ?? '';
                                        $fOptions = $field['options'] ?? [];
                                        $colClass = in_array($fType, ['textarea']) ? 'col-12' : (in_array($fType, ['select', 'number', 'date']) ? 'col-12 col-md-6' : 'col-12 col-md-6');
                                    @endphp

                                    <div class="{{ $colClass }}">
                                        <label class="form-label fw-semibold small text-secondary">
                                            {{ $fLabel }} @if($fReq)<span class="text-danger">*</span>@endif
                                        </label>

                                        @if($fType === 'select')
                                            <select name="{{ $fName }}" class="form-select" @if($fReq) required @endif>
                                                <option value="">-- Pilih {{ $fLabel }} --</option>
                                                @foreach($fOptions as $opt)
                                                    <option value="{{ $opt }}" {{ old($fName) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($fType === 'textarea')
                                            <textarea name="{{ $fName }}" rows="2" class="form-control" placeholder="{{ $fPlaceholder }}" @if($fReq) required @endif>{{ old($fName) }}</textarea>
                                        @elseif($fType === 'date')
                                            <input type="date" name="{{ $fName }}" class="form-control" value="{{ old($fName) }}" @if($fReq) required @endif>
                                        @elseif($fType === 'number')
                                            <input type="number" name="{{ $fName }}" class="form-control" value="{{ old($fName) }}" placeholder="{{ $fPlaceholder }}" @if($fReq) required @endif>
                                        @else
                                            <input type="text" name="{{ $fName }}" class="form-control" value="{{ old($fName) }}" placeholder="{{ $fPlaceholder }}" @if($fReq) required @endif>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Keterangan Tambahan untuk Pengurus --}}
                <div class="col-12">
                    <label class="form-label fw-semibold small text-secondary">Catatan Tambahan untuk Pengurus RT / RW (Opsional)</label>
                    <textarea name="keterangan_tambahan" rows="2" class="form-control" placeholder="Tuliskan catatan khusus atau informasi lain jika diperlukan...">{{ old('keterangan_tambahan') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3" onclick="goToStep(2)">
                    <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
                    <span>Kembali</span>
                </button>
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 rounded-3" onclick="goToStep(4)">
                    <span>Lanjut ke Berkas Lampiran</span>
                    <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
                </button>
            </div>
        </div>

        {{-- STEP 4: UPLOAD BERKAS LAMPIRAN PENDUKUNG --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 step-pane d-none" id="stepPane-4" style="background: #ffffff;">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:0.85rem;">4</span>
                        <span>Upload Berkas & Dokumen Pendukung</span>
                    </h5>
                    <p class="text-muted small mb-0">Format yang didukung: JPG, PNG, atau PDF (Ukuran maksimal 5 MB per berkas).</p>
                </div>
            </div>

            {{-- Ringkasan Syarat Dokumen yang Diperlukan --}}
            @if(!empty($letterType->persyaratan))
                <div class="alert alert-info border-0 rounded-3 p-3 mb-4 d-flex align-items-start gap-2" style="background: #EFF6FF;">
                    <i data-lucide="info" class="text-primary mt-1" style="width:18px;height:18px;flex-shrink:0;"></i>
                    <div class="small text-secondary">
                        <strong class="text-dark d-block mb-1">Persyaratan Dokumen untuk {{ $letterType->nama }}:</strong>
                        <ul class="mb-0 ps-3">
                            @foreach($letterType->persyaratan as $syaratItem)
                                <li>{{ $syaratItem }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="row g-4">
                {{-- KTP --}}
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3 text-center h-100 d-flex flex-column justify-content-between" style="border-style: dashed !important; background: #F8FAFC;">
                        <div>
                            <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center p-3 mb-2" style="width:48px;height:48px;">
                                <i data-lucide="credit-card" style="width:22px;height:22px;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Scan / Foto KTP Asli</h6>
                            <p class="text-muted small mb-2">Pastikan NIK dan nama pemohon terlihat jelas.</p>
                        </div>
                        <div>
                            <input type="file" name="dokumen_ktp" id="dokumen_ktp" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                    </div>
                </div>

                {{-- KK --}}
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3 text-center h-100 d-flex flex-column justify-content-between" style="border-style: dashed !important; background: #F8FAFC;">
                        <div>
                            <div class="rounded-circle bg-info-subtle text-info d-inline-flex align-items-center justify-content-center p-3 mb-2" style="width:48px;height:48px;">
                                <i data-lucide="users" style="width:22px;height:22px;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Kartu Keluarga (KK)</h6>
                            <p class="text-muted small mb-2">Halaman utama yang memuat data anggota keluarga.</p>
                        </div>
                        <div>
                            <input type="file" name="dokumen_kk" id="dokumen_kk" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                    </div>
                </div>

                {{-- Berkas Khusus / Dokumen Tambahan --}}
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3 text-center h-100 d-flex flex-column justify-content-between" style="border-style: dashed !important; background: #F8FAFC;">
                        <div>
                            <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center p-3 mb-2" style="width:48px;height:48px;">
                                <i data-lucide="file-check" style="width:22px;height:22px;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Dokumen Pendukung Spesifik</h6>
                            <p class="text-muted small mb-2">Foto usaha, surat pengantar lama, surat pernyataan, dll.</p>
                        </div>
                        <div>
                            <input type="file" name="dokumen_pendukung" id="dokumen_pendukung" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3" onclick="goToStep(3)">
                    <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
                    <span>Kembali</span>
                </button>
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 rounded-3" onclick="goToStep(5)">
                    <span>Lanjut ke Review & Konfirmasi</span>
                    <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
                </button>
            </div>
        </div>

        {{-- STEP 5: REVIEW, PERNYATAAN & SUBMIT --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 step-pane d-none" id="stepPane-5" style="background: #ffffff;">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:0.85rem;">5</span>
                        <span>Konfirmasi Data & Pernyataan Keabsahan</span>
                    </h5>
                    <p class="text-muted small mb-0">Tinjau kembali seluruh rincian permohonan sebelum dikirimkan ke pengurus untuk verifikasi berjenjang.</p>
                </div>
            </div>

            {{-- Summary Card Preview --}}
            <div class="card border rounded-3 p-4 mb-4" style="background: #F8FAFC;">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="text-secondary small fw-semibold">Nama Pemohon:</div>
                        <div class="fw-bold text-dark fs-6" id="review-nama">{{ $user->name }}</div>
                        <div class="text-muted small" id="review-nik-kk">NIK: {{ $user->nik }} | KK: {{ $user->no_kk }}</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="text-secondary small fw-semibold">Alamat Domisili Terdaftar:</div>
                        <div class="fw-bold text-dark" id="review-alamat">{{ $user->alamat ?? 'Jl. Pacantel No. 12' }}, RT {{ $cleanRt }}/RW {{ $cleanRw }}</div>
                        <div class="text-muted small">Kel. Sukamiskin, Kec. Arcamanik, Kota Bandung 40293</div>
                    </div>

                    <div class="col-12 border-top pt-2">
                        <div class="text-secondary small fw-semibold">Jenis Surat yang Dimohon:</div>
                        <div class="fw-bold text-primary fs-6">{{ $letterType->nama }} ({{ $letterType->singkatan }})</div>
                        <div class="text-muted small">Alur Proses: {{ $letterType->alur_label }} &bull; Estimasi: {{ $letterType->estimasi_hari }}</div>
                    </div>

                    <div class="col-12">
                        <div class="text-secondary small fw-semibold">Keperluan Permohonan:</div>
                        <div class="text-dark p-2 rounded bg-white border" id="review-keperluan" style="min-height: 48px; font-size: 0.88rem;">
                            (Akan terisi sesuai input Anda)
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pernyataan Kebenaran --}}
            <div class="alert alert-warning border-warning-subtle rounded-3 p-3 mb-4 d-flex align-items-start gap-3">
                <input type="checkbox" id="pernyataanSetuju" class="form-check-input mt-1" style="width:20px;height:20px;flex-shrink:0;" required>
                <label for="pernyataanSetuju" class="text-dark small mb-0 cursor-pointer" style="line-height: 1.5;">
                    <strong>Pernyataan Kebenaran Data:</strong> Saya menyatakan dengan sebenar-benarnya bahwa seluruh data, alamat, dan berkas yang saya lampirkan adalah sah, valid, dan benar adanya. Apabila di kemudian hari ditemukan ketidakbenaran atau pemalsuan data, saya bersedia menerima sanksi administrasi dan hukum yang berlaku.
                </label>
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3" onclick="goToStep(4)">
                    <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
                    <span>Kembali</span>
                </button>

                <div class="d-flex align-items-center gap-2 w-100 w-sm-auto justify-content-end">
                    <button type="submit" name="action" value="draft" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3">
                        <i data-lucide="save" style="width:16px;height:16px;"></i>
                        <span>Simpan Draft</span>
                    </button>

                    <button type="submit" name="action" value="submit" id="btnSubmitForm" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2 px-4 py-2 rounded-3 shadow-sm" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%); font-weight: 600;">
                        <i data-lucide="send" style="width:16px;height:16px;"></i>
                        <span>Kirim Permohonan Surat</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>

<style>
.stepper-item {
    cursor: pointer;
    flex: 1;
}
.step-circle {
    width: 42px;
    height: 42px;
    background: #F1F5F9;
    color: #64748B;
    border: 2px solid #CBD5E1;
    transition: all 0.25s ease;
}
.stepper-item.active .step-circle {
    background: #1456A0;
    color: #ffffff;
    border-color: #1456A0;
    box-shadow: 0 4px 10px rgba(20, 86, 160, 0.3) !important;
}
.stepper-item.completed .step-circle {
    background: #16865D;
    color: #ffffff;
    border-color: #16865D;
}
.stepper-item.active .step-label {
    color: #1456A0;
}
.stepper-item.completed .step-label {
    color: #16865D;
}
.cursor-pointer {
    cursor: pointer;
}
</style>

<script>
let currentStep = 1;
const totalSteps = 5;

function goToStep(step) {
    if (step < 1 || step > totalSteps) return;

    for (let i = 1; i <= totalSteps; i++) {
        const pane = document.getElementById(`stepPane-${i}`);
        const indicator = document.getElementById(`stepIndicator-${i}`);
        if (pane) {
            if (i === step) {
                pane.classList.remove('d-none');
            } else {
                pane.classList.add('d-none');
            }
        }
        if (indicator) {
            indicator.classList.remove('active', 'completed');
            if (i < step) {
                indicator.classList.add('completed');
            } else if (i === step) {
                indicator.classList.add('active');
            }
        }
    }

    const progressPercent = ((step - 1) / (totalSteps - 1)) * 100;
    const bar = document.getElementById('stepperProgressBar');
    if (bar) bar.style.width = progressPercent + '%';

    currentStep = step;

    if (window.lucide) {
        window.lucide.createIcons();
    }

    if (step === 5) {
        updateReviewSummary();
    }

    window.scrollTo({ top: 120, behavior: 'smooth' });
}

function updateReviewSummary() {
    const nama = document.getElementById('nama_lengkap')?.value || '-';
    const nik = document.getElementById('nik')?.value || '-';
    const noKk = document.getElementById('no_kk')?.value || '-';
    const jalan = document.getElementById('alamat_jalan')?.value || '';
    const noRumah = document.getElementById('no_rumah')?.value || '';
    const keperluan = document.getElementById('keperluan')?.value || 'Belum diisi';

    const reviewNama = document.getElementById('review-nama');
    const reviewNikKk = document.getElementById('review-nik-kk');
    const reviewAlamat = document.getElementById('review-alamat');
    const reviewKeperluan = document.getElementById('review-keperluan');

    if (reviewNama) reviewNama.innerText = nama;
    if (reviewNikKk) reviewNikKk.innerText = `NIK: ${nik} | No. KK: ${noKk}`;
    if (reviewAlamat) reviewAlamat.innerText = `${jalan} No. ${noRumah}, RT {{ $cleanRt }}/RW {{ $cleanRw }}`;
    if (reviewKeperluan) reviewKeperluan.innerText = keperluan;
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formSurat');
    form.addEventListener('submit', function (e) {
        const submitter = e.submitter;
        if (submitter && submitter.value === 'draft') {
            return true;
        }

        const checkbox = document.getElementById('pernyataanSetuju');
        if (!checkbox || !checkbox.checked) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Persetujuan Diperlukan',
                text: 'Harap centang pernyataan kebenaran data sebelum mengirimkan permohonan surat.',
                confirmButtonColor: '#1456A0',
            });
            return false;
        }
    });
});
</script>
@endsection
