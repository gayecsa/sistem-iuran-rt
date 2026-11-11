@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- ======================================================================
         PAGE HEADER & BREADCRUMBS
         ====================================================================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-icon-pill shadow-sm" style="width: 48px; height: 48px; border-radius: 14px; background: #EAF3FF; color: #1456A0; font-size: 20px;">
                <i data-lucide="user-plus" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="color: var(--wm-text) !important;">Pendaftaran Warga Baru</h3>
                <p class="text-muted mb-0 small">Formulir administrasi kependudukan terpadu lingkungan RW 013 Kelurahan Sukamiskin.</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('warga.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
                <span>Kembali ke Direktori</span>
            </a>
        </div>
    </div>

    {{-- Alert Error Global --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-4" role="alert" style="background: #FEE2E2; color: #DC2626;">
            <div class="d-flex align-items-start gap-2">
                <i data-lucide="alert-circle" style="width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                <div>
                    <strong class="d-block mb-1">Terdapat kesalahan pengisian data:</strong>
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ======================================================================
         STEPPER INDICATOR WIZARD (6 TAHAPAN)
         ====================================================================== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <div class="stepper-wrapper d-none d-md-flex justify-content-between position-relative px-3 py-2">
            <div class="stepper-progress-line position-absolute top-50 start-0 w-100" style="height: 3px; background: #E2E8F0; z-index: 0; transform: translateY(-50%);">
                <div id="progressBarFill" style="height: 100%; width: 0%; background: #1456A0; transition: width 0.3s ease;"></div>
            </div>

            @php
                $steps = [
                    1 => ['title' => 'Identitas Pribadi', 'icon' => 'user'],
                    2 => ['title' => 'Data Keluarga', 'icon' => 'users'],
                    3 => ['title' => 'Alamat & Domisili', 'icon' => 'map-pin'],
                    4 => ['title' => 'Kontak & Pekerjaan', 'icon' => 'briefcase'],
                    5 => ['title' => 'Dokumen Pendukung', 'icon' => 'file-text'],
                    6 => ['title' => 'Review & Simpan', 'icon' => 'check-circle-2'],
                ];
            @endphp

            @foreach($steps as $num => $step)
                <div class="step-item text-center position-relative z-1" id="step-nav-{{ $num }}" style="cursor: pointer;" onclick="jumpToStep({{ $num }})">
                    <div class="step-circle mx-auto d-flex align-items-center justify-content-center rounded-circle fw-bold shadow-sm"
                         style="width: 40px; height: 40px; font-size: 0.9rem; background: {{ $num === 1 ? '#1456A0' : '#FFFFFF' }}; color: {{ $num === 1 ? '#FFFFFF' : '#64748B' }}; border: 2px solid {{ $num === 1 ? '#1456A0' : '#E2E8F0' }}; transition: all 0.3s;">
                        <i data-lucide="{{ $step['icon'] }}" style="width: 18px; height: 18px;"></i>
                    </div>
                    <span class="step-label d-block mt-2 fw-semibold small" style="color: {{ $num === 1 ? '#1456A0' : '#64748B' }}; font-size: 0.78rem;">
                        {{ $num }}. {{ $step['title'] }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Mobile Stepper Indicator --}}
        <div class="d-flex d-md-none align-items-center justify-content-between px-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: #1456A0; color: #FFFFFF;" id="mobileStepBadge">
                    Langkah 1 / 6
                </span>
                <span class="fw-bold text-dark small" id="mobileStepTitle">Identitas Pribadi</span>
            </div>
            <div class="progress" style="width: 80px; height: 6px;">
                <div id="mobileProgressBar" class="progress-bar" style="width: 16.6%; background: #1456A0;"></div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         FORM STEPPER CONTAINER
         ====================================================================== --}}
    <form action="{{ route('warga.store') }}" method="POST" enctype="multipart/form-data" id="wargaWizardForm">
        @csrf
        <input type="hidden" name="action_type" id="formActionType" value="simpan_verifikasi">

        {{-- ==================================================================
             STEP 1: IDENTITAS PRIBADI
             ================================================================== --}}
        <div class="wizard-step card border-0 shadow-sm rounded-4 p-4 mb-4" id="step-1" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
            <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
                <div class="wm-icon-pill p-2 rounded-3" style="background: #EAF3FF; color: #1456A0;">
                    <i data-lucide="user" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Tahap 1: Data Identitas Pribadi Sesuai KTP</h5>
                    <small class="text-muted">Masukkan data identitas kependudukan resmi dan NIK 16 digit yang valid.</small>
                </div>
            </div>

            <div class="row g-3">
                {{-- Nama Lengkap --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Nama Lengkap (Sesuai KTP) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i data-lucide="user" style="width: 16px; height: 16px; color: #1456A0;"></i></span>
                        <input type="text" name="name" id="input_name" class="form-control border-start-0" value="{{ old('name') }}" placeholder="Contoh: Ethan Williams" required>
                    </div>
                </div>

                {{-- NIK --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">NIK (Nomor Induk Kependudukan - 16 Digit) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i data-lucide="credit-card" style="width: 16px; height: 16px; color: #1456A0;"></i></span>
                        <input type="text" name="nik" id="input_nik" class="form-control border-start-0" value="{{ old('nik') }}" maxlength="16" minlength="16" placeholder="327318xxxxxxxxxx" required>
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">Harus tepat 16 digit angka resmi Dukcapil.</small>
                </div>

                {{-- Tempat Lahir --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Tempat Lahir</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i data-lucide="building" style="width: 16px; height: 16px; color: #1456A0;"></i></span>
                        <input type="text" name="tempat_lahir" id="input_tempat_lahir" class="form-control border-start-0" value="{{ old('tempat_lahir', 'Kota Bandung') }}" placeholder="Kota / Kabupaten Lahir">
                    </div>
                </div>

                {{-- Tanggal Lahir --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Tanggal Lahir <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i data-lucide="calendar" style="width: 16px; height: 16px; color: #1456A0;"></i></span>
                        <input type="date" name="tanggal_lahir" id="input_tanggal_lahir" class="form-control border-start-0" value="{{ old('tanggal_lahir') }}" max="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                {{-- Jenis Kelamin --}}
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold small mb-1">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="gender" id="input_gender" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                {{-- Status Perkawinan --}}
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold small mb-1">Status Perkawinan <span class="text-danger">*</span></label>
                    <select name="status_perkawinan" id="input_status_perkawinan" class="form-select" required>
                        <option value="Belum Kawin" {{ old('status_perkawinan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                        <option value="Kawin" {{ old('status_perkawinan', 'Kawin') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                        <option value="Cerai Hidup" {{ old('status_perkawinan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                        <option value="Cerai Mati" {{ old('status_perkawinan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                </div>

                {{-- Agama --}}
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold small mb-1">Agama <span class="text-danger">*</span></label>
                    <select name="agama" id="input_agama" class="form-select" required>
                        @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Penghayat Kepercayaan'] as $ag)
                            <option value="{{ $ag }}" {{ old('agama', 'Islam') == $ag ? 'selected' : '' }}>{{ $ag }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Kewarganegaraan --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Kewarganegaraan <span class="text-danger">*</span></label>
                    <select name="kewarganegaraan" id="input_kewarganegaraan" class="form-select" required>
                        <option value="WNI" {{ old('kewarganegaraan', 'WNI') == 'WNI' ? 'selected' : '' }}>WNI (Warga Negara Indonesia)</option>
                        <option value="WNA" {{ old('kewarganegaraan') == 'WNA' ? 'selected' : '' }}>WNA (Warga Negara Asing)</option>
                    </select>
                </div>

                {{-- Foto Profil Opsional --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Foto Profil (Opsional)</label>
                    <input type="file" name="foto_profil" class="form-control" accept="image/png,image/jpeg,image/webp">
                    <small class="text-muted" style="font-size: 0.75rem;">Maksimal 2MB (JPG, PNG, WebP).</small>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" onclick="nextStep(2)">
                    <span>Lanjut: Data Keluarga</span>
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                </button>
            </div>
        </div>

        {{-- ==================================================================
             STEP 2: DATA KELUARGA
             ================================================================== --}}
        <div class="wizard-step card border-0 shadow-sm rounded-4 p-4 mb-4 d-none" id="step-2" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
            <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
                <div class="wm-icon-pill p-2 rounded-3" style="background: #EAF3FF; color: #1456A0;">
                    <i data-lucide="users" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Tahap 2: Manajemen Data Keluarga (Kartu Keluarga)</h5>
                    <small class="text-muted">Hubungkan warga dengan Nomor KK 16 digit dan tentukan status hubungan keluarga.</small>
                </div>
            </div>

            <div class="row g-3">
                {{-- Nomor Kartu Keluarga --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Nomor Kartu Keluarga (KK - 16 Digit) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i data-lucide="folder-check" style="width: 16px; height: 16px; color: #1456A0;"></i></span>
                        <input type="text" name="no_kk" id="input_no_kk" class="form-control border-start-0" value="{{ old('no_kk') }}" maxlength="16" minlength="16" placeholder="327318xxxxxxxxxx" required>
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">Jika No. KK sudah ada di sistem, warga akan otomatis dihubungkan ke KK tersebut.</small>
                </div>

                {{-- Hubungan Keluarga --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Hubungan dalam Keluarga <span class="text-danger">*</span></label>
                    <select name="hubungan_keluarga" id="input_hubungan_keluarga" class="form-select" required>
                        <option value="Kepala Keluarga" {{ old('hubungan_keluarga', 'Kepala Keluarga') == 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                        <option value="Istri" {{ old('hubungan_keluarga') == 'Istri' ? 'selected' : '' }}>Istri</option>
                        <option value="Suami" {{ old('hubungan_keluarga') == 'Suami' ? 'selected' : '' }}>Suami</option>
                        <option value="Anak" {{ old('hubungan_keluarga') == 'Anak' ? 'selected' : '' }}>Anak</option>
                        <option value="Orang Tua" {{ old('hubungan_keluarga') == 'Orang Tua' ? 'selected' : '' }}>Orang Tua (Ayah/Ibu)</option>
                        <option value="Saudara" {{ old('hubungan_keluarga') == 'Saudara' ? 'selected' : '' }}>Saudara Kandung</option>
                        <option value="Famili Lain" {{ old('hubungan_keluarga') == 'Famili Lain' ? 'selected' : '' }}>Famili Lain / Kerabat</option>
                    </select>
                    <small class="text-muted" style="font-size: 0.75rem;">Status kepemilikan dan relasi formal dalam register KK.</small>
                </div>

                <div class="col-12">
                    <div class="alert alert-info border-0 rounded-3 mb-0 small d-flex align-items-center gap-2" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="info" style="width: 18px; height: 18px; flex-shrink: 0;"></i>
                        <span>Jika memilih <strong>Kepala Keluarga</strong>, nama warga ini akan dicantumkan sebagai penanggung jawab utama dokumen Kartu Keluarga.</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" onclick="prevStep(1)">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;" class="me-1"></i> Kembali
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" onclick="nextStep(3)">
                    <span>Lanjut: Alamat & Domisili</span>
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                </button>
            </div>
        </div>

        {{-- ==================================================================
             STEP 3: ALAMAT & TEMPAT TINGGAL (SUKAMISKIN ARCAMANIK BANDUNG)
             ================================================================== --}}
        <div class="wizard-step card border-0 shadow-sm rounded-4 p-4 mb-4 d-none" id="step-3" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
            <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
                <div class="wm-icon-pill p-2 rounded-3" style="background: #EAF3FF; color: #1456A0;">
                    <i data-lucide="map-pin" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Tahap 3: Alamat & Status Tempat Tinggal</h5>
                    <small class="text-muted">Wilayah administratif: RW 013, Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung 40293.</small>
                </div>
            </div>

            <div class="row g-3">
                {{-- RT --}}
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold small mb-1">Rukun Tetangga (RT) <span class="text-danger">*</span></label>
                    <select name="rt_number" id="input_rt_number" class="form-select" required>
                        @foreach($availableRts as $rt)
                            <option value="{{ $rt['val'] }}" {{ old('rt_number', $availableRts[0]['val']) == $rt['val'] ? 'selected' : '' }}>
                                {{ $rt['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- RW --}}
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold small mb-1">Rukun Warga (RW)</label>
                    <input type="text" name="rw_number" class="form-control bg-light" value="RW 013" readonly>
                </div>

                {{-- Nomor Rumah --}}
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold small mb-1">Nomor Rumah <span class="text-danger">*</span></label>
                    <input type="text" name="house_number" id="input_house_number" class="form-control" value="{{ old('house_number') }}" placeholder="Contoh: No. 15" required>
                </div>

                {{-- Blok --}}
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold small mb-1">Blok (Opsional)</label>
                    <input type="text" name="blok_rumah" id="input_blok_rumah" class="form-control" value="{{ old('blok_rumah') }}" placeholder="Contoh: Blok B2">
                </div>

                {{-- Gang / Jalan --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Nama Jalan / Gang</label>
                    <input type="text" name="gang_jalan" id="input_gang_jalan" class="form-control" value="{{ old('gang_jalan', 'Jl. Pacuan Kuda') }}" placeholder="Contoh: Jl. Pacuan Kuda / Gang Saluyu">
                </div>

                {{-- Status Tempat Tinggal --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Status Kepemilikan Rumah <span class="text-danger">*</span></label>
                    <select name="status_rumah" id="input_status_rumah" class="form-select" required>
                        <option value="Milik Sendiri" {{ old('status_rumah', 'Milik Sendiri') == 'Milik Sendiri' ? 'selected' : '' }}>Rumah Milik Sendiri</option>
                        <option value="Kontrak" {{ old('status_rumah') == 'Kontrak' ? 'selected' : '' }}>Kontrak / Sewa</option>
                        <option value="Kos" {{ old('status_rumah') == 'Kos' ? 'selected' : '' }}>Kos / Paviliun</option>
                        <option value="Menumpang" {{ old('status_rumah') == 'Menumpang' ? 'selected' : '' }}>Menumpang (Keluarga/Kerabat)</option>
                        <option value="Lainnya" {{ old('status_rumah') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                {{-- Status Domisili --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Status Domisili Kependudukan <span class="text-danger">*</span></label>
                    <select name="status_domisili" id="input_status_domisili" class="form-select" required>
                        <option value="Warga Tetap" {{ old('status_domisili', 'Warga Tetap') == 'Warga Tetap' ? 'selected' : '' }}>Warga Tetap (KTP/KK RW 013)</option>
                        <option value="Warga Sementara" {{ old('status_domisili') == 'Warga Sementara' ? 'selected' : '' }}>Warga Sementara (Domisili Non-KTP)</option>
                        <option value="Pendatang Baru" {{ old('status_domisili') == 'Pendatang Baru' ? 'selected' : '' }}>Pendatang Baru</option>
                        <option value="Akan Pindah" {{ old('status_domisili') == 'Akan Pindah' ? 'selected' : '' }}>Akan Pindah Keluar</option>
                        <option value="Sudah Pindah" {{ old('status_domisili') == 'Sudah Pindah' ? 'selected' : '' }}>Sudah Pindah</option>
                        <option value="Meninggal Dunia" {{ old('status_domisili') == 'Meninggal Dunia' ? 'selected' : '' }}>Meninggal Dunia</option>
                    </select>
                </div>

                {{-- Alamat Lengkap Teks --}}
                <div class="col-12">
                    <label class="form-label fw-bold small mb-1">Alamat Lengkap Tempat Tinggal <span class="text-danger">*</span></label>
                    <textarea name="address" id="input_address" class="form-control" rows="2" placeholder="Tuliskan alamat lengkap beserta patokan rumah..." required>{{ old('address', 'Jl. Pacuan Kuda No. 13, RT 001 / RW 013, Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" onclick="prevStep(2)">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;" class="me-1"></i> Kembali
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" onclick="nextStep(4)">
                    <span>Lanjut: Kontak & Pekerjaan</span>
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                </button>
            </div>
        </div>

        {{-- ==================================================================
             STEP 4: KONTAK & PEKERJAAN & AKUN LOGIN
             ================================================================== --}}
        <div class="wizard-step card border-0 shadow-sm rounded-4 p-4 mb-4 d-none" id="step-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
            <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
                <div class="wm-icon-pill p-2 rounded-3" style="background: #EAF3FF; color: #1456A0;">
                    <i data-lucide="briefcase" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Tahap 4: Kontak, Pekerjaan & Kredensial Akun</h5>
                    <small class="text-muted">Kelola nomor telepon WhatsApp, email login, profesi, serta password akun warga.</small>
                </div>
            </div>

            <div class="row g-3">
                {{-- No WhatsApp --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i data-lucide="phone" style="width: 16px; height: 16px; color: #1456A0;"></i></span>
                        <input type="text" name="phone" id="input_phone" class="form-control border-start-0" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>

                {{-- Email --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Email (Kredensial Login Akun) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i data-lucide="mail" style="width: 16px; height: 16px; color: #1456A0;"></i></span>
                        <input type="email" name="email" id="input_email" class="form-control border-start-0" value="{{ old('email') }}" placeholder="email@domain.com" required>
                    </div>
                </div>

                {{-- Pekerjaan --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Pekerjaan / Profesi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i data-lucide="briefcase" style="width: 16px; height: 16px; color: #1456A0;"></i></span>
                        <input type="text" name="pekerjaan" id="input_pekerjaan" class="form-control border-start-0" value="{{ old('pekerjaan', 'Karyawan Swasta') }}" placeholder="Contoh: Pegawai Negeri, Wiraswasta, Dokter, Guru">
                    </div>
                </div>

                {{-- Pendidikan Terakhir --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small mb-1">Pendidikan Terakhir</label>
                    <select name="pendidikan_terakhir" id="input_pendidikan" class="form-select">
                        <option value="S1 / Sarjana" selected>S1 / Sarjana</option>
                        <option value="SMA / SMK / Sederajat">SMA / SMK / Sederajat</option>
                        <option value="Diploma (D3)">Diploma (D3)</option>
                        <option value="S2 / Magister">S2 / Magister</option>
                        <option value="S3 / Doktor">S3 / Doktor</option>
                        <option value="SMP / Sederajat">SMP / Sederajat</option>
                        <option value="SD / Sederajat">SD / Sederajat</option>
                        <option value="Tidak / Belum Sekolah">Tidak / Belum Sekolah</option>
                    </select>
                </div>

                {{-- Password Akun --}}
                <div class="col-12">
                    <div class="p-3 rounded-3" style="background: #F8FAFC; border: 1px dashed #CBD5E1;">
                        <label class="form-label fw-bold small mb-1 text-dark">Password Awal Akun Warga</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i data-lucide="lock" style="width: 16px; height: 16px; color: #1456A0;"></i></span>
                            <input type="password" name="password" id="input_password" class="form-control border-start-0" placeholder="Minimal 6 karakter (Default: warga12345 jika kosong)">
                            <button type="button" class="btn btn-outline-secondary px-3" onclick="togglePassVisibility()">
                                <i data-lucide="eye" id="passToggleIcon" style="width: 16px; height: 16px;"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                            Password di-hash secara aman menggunakan algoritma bcrypt Laravel. Kredensial tidak pernah disimpan dalam teks biasa.
                        </small>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" onclick="prevStep(3)">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;" class="me-1"></i> Kembali
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" onclick="nextStep(5)">
                    <span>Lanjut: Dokumen Pendukung</span>
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                </button>
            </div>
        </div>

        {{-- ==================================================================
             STEP 5: DOKUMEN PENDUKUNG (PRIVATE STORAGE)
             ================================================================== --}}
        <div class="wizard-step card border-0 shadow-sm rounded-4 p-4 mb-4 d-none" id="step-5" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
            <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
                <div class="wm-icon-pill p-2 rounded-3" style="background: #EAF3FF; color: #1456A0;">
                    <i data-lucide="file-text" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Tahap 5: Unggah Dokumen Kependudukan (Private Storage)</h5>
                    <small class="text-muted">Dokumen identitas disimpan dalam folder privat aman dengan otorisasi backend ketat.</small>
                </div>
            </div>

            <div class="row g-4">
                {{-- Upload KTP --}}
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between" style="border: 2px dashed #CBD5E1; background: #F8FAFC;">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold small">
                                <i data-lucide="credit-card" style="width: 18px; height: 18px;"></i>
                                <span>1. Scan / Foto KTP Asli</span>
                            </div>
                            <p class="text-muted mb-3" style="font-size: 0.78rem;">Foto KTP fisik atau KTP Digital (IKD). Format PDF, JPG, PNG (Maks 4MB).</p>
                        </div>
                        <div>
                            <input type="file" name="dokumen_ktp" class="form-control form-control-sm" accept="application/pdf,image/jpeg,image/png">
                        </div>
                    </div>
                </div>

                {{-- Upload Salinan KK Dukcapil --}}
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between" style="border: 2px dashed #1456A0; background: #EAF3FF;">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2 fw-bold small" style="color: #1456A0;">
                                <i data-lucide="file-check" style="width: 18px; height: 18px;"></i>
                                <span>2. Salinan KK Resmi Dukcapil</span>
                            </div>
                            <p class="text-muted mb-3" style="font-size: 0.78rem;">Berkas PDF KK resmi bertanda tangan elektronik/barcode Dukcapil asli (Maks 5MB).</p>
                        </div>
                        <div>
                            <input type="file" name="dokumen_kk" class="form-control form-control-sm" accept="application/pdf,image/jpeg,image/png">
                        </div>
                    </div>
                </div>

                {{-- Upload Dokumen Lain --}}
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between" style="border: 2px dashed #CBD5E1; background: #F8FAFC;">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold small">
                                <i data-lucide="paperclip" style="width: 18px; height: 18px;"></i>
                                <span>3. Dokumen Pendukung Lain</span>
                            </div>
                            <p class="text-muted mb-3" style="font-size: 0.78rem;">Surat Pindah / Akta Kelahiran / Surat Nikah / Surat Pengantar (Opsional, PDF/Foto).</p>
                        </div>
                        <div>
                            <input type="file" name="dokumen_pendukung" class="form-control form-control-sm" accept="application/pdf,image/jpeg,image/png">
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="p-3 rounded-3 small d-flex align-items-center gap-2 text-muted" style="background: rgba(20, 86, 160, 0.05);">
                        <i data-lucide="shield-check" style="width: 20px; height: 20px; color: #1456A0; flex-shrink: 0;"></i>
                        <span><strong>Privasi Terjamin:</strong> File identitas tidak dapat diakses masyarakat umum melalui URL publik langsung, dan hanya dapat dibuka setelah verifikasi hak akses pengurus wilayah.</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" onclick="prevStep(4)">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;" class="me-1"></i> Kembali
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" onclick="nextStep(6)">
                    <span>Lanjut: Review & Simpan</span>
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                </button>
            </div>
        </div>

        {{-- ==================================================================
             STEP 6: REVIEW & SIMPAN (CONFIRMATION DASHBOARD)
             ================================================================== --}}
        <div class="wizard-step card border-0 shadow-sm rounded-4 p-4 mb-4 d-none" id="step-6" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
            <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
                <div class="wm-icon-pill p-2 rounded-3" style="background: #E3F5EC; color: #16865D;">
                    <i data-lucide="check-circle-2" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Tahap 6: Review Ringkasan & Verifikasi Data</h5>
                    <small class="text-muted">Pastikan seluruh informasi kependudukan telah sesuai sebelum disimpan ke basis data RW 013.</small>
                </div>
            </div>

            {{-- Ringkasan Review Card --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <div class="card border rounded-3 p-3 bg-light h-100">
                        <span class="text-muted fw-bold small text-uppercase mb-2 d-block">Identitas Warga</span>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr><td class="text-muted" style="width: 40%;">Nama Lengkap:</td><td class="fw-bold text-dark" id="rev_name">-</td></tr>
                            <tr><td class="text-muted">NIK (KTP):</td><td class="fw-bold text-primary font-monospace" id="rev_nik">-</td></tr>
                            <tr><td class="text-muted">TTL:</td><td class="text-dark" id="rev_ttl">-</td></tr>
                            <tr><td class="text-muted">Jenis Kelamin:</td><td class="text-dark" id="rev_gender">-</td></tr>
                            <tr><td class="text-muted">Status / Agama:</td><td class="text-dark" id="rev_status_agama">-</td></tr>
                        </table>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card border rounded-3 p-3 bg-light h-100">
                        <span class="text-muted fw-bold small text-uppercase mb-2 d-block">Keluarga & Alamat</span>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr><td class="text-muted" style="width: 40%;">Nomor KK:</td><td class="fw-bold text-primary font-monospace" id="rev_no_kk">-</td></tr>
                            <tr><td class="text-muted">Hubungan KK:</td><td class="fw-bold text-dark" id="rev_hubungan">-</td></tr>
                            <tr><td class="text-muted">Wilayah RT/RW:</td><td class="text-dark" id="rev_rtrw">-</td></tr>
                            <tr><td class="text-muted">Kepemilikan:</td><td class="text-dark" id="rev_status_rumah">-</td></tr>
                            <tr><td class="text-muted">Status Domisili:</td><td class="fw-bold text-success" id="rev_domisili">-</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Opsi Simpan --}}
            <div class="p-3 rounded-4 mb-4" style="background: #F4F7FB; border: 1px solid #E2E8F0;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <span class="fw-bold text-dark d-block">Status Verifikasi yang Diberikan:</span>
                        <small class="text-muted">
                            @if(auth()->user()->isRw())
                                Sebagai <strong>Ketua RW 013</strong>, data akan langsung mendapatkan status <strong>Terverifikasi RW</strong>.
                            @elseif(auth()->user()->isRt())
                                Sebagai <strong>Ketua RT</strong>, data akan berstatus <strong>Terverifikasi RT</strong> dan diteruskan ke RW.
                            @else
                                Data akan berstatus <strong>Menunggu Verifikasi RT</strong>.
                            @endif
                        </small>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold small" onclick="submitWargaForm('draft')">
                            <i data-lucide="file-edit" style="width: 15px; height: 15px;" class="me-1"></i> Simpan Draft
                        </button>
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold small shadow-sm" style="background: #1456A0;" onclick="submitWargaForm('simpan_verifikasi')">
                            <i data-lucide="shield-check" style="width: 16px; height: 16px;" class="me-1"></i> Simpan & Verifikasi
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold" onclick="prevStep(5)">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;" class="me-1"></i> Kembali
                </button>
                <button type="reset" class="btn btn-link text-danger text-decoration-none small" onclick="resetWizard()">
                    <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;" class="me-1"></i> Reset Formulir
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ======================================================================
     SCRIPT STEPPER WIZARD JAVASCRIPT
     ====================================================================== --}}
<script>
    let currentStep = 1;
    const totalSteps = 6;

    const stepTitles = {
        1: 'Identitas Pribadi',
        2: 'Data Keluarga',
        3: 'Alamat & Domisili',
        4: 'Kontak & Pekerjaan',
        5: 'Dokumen Pendukung',
        6: 'Review & Simpan'
    };

    function updateWizardUI() {
        // Toggle Step Views
        for (let i = 1; i <= totalSteps; i++) {
            const stepEl = document.getElementById('step-' + i);
            const navEl = document.getElementById('step-nav-' + i);
            if (stepEl) {
                if (i === currentStep) {
                    stepEl.classList.remove('d-none');
                } else {
                    stepEl.classList.add('d-none');
                }
            }

            if (navEl) {
                const circle = navEl.querySelector('.step-circle');
                const label = navEl.querySelector('.step-label');
                if (i === currentStep) {
                    circle.style.background = '#1456A0';
                    circle.style.color = '#FFFFFF';
                    circle.style.borderColor = '#1456A0';
                    label.style.color = '#1456A0';
                } else if (i < currentStep) {
                    circle.style.background = '#16865D';
                    circle.style.color = '#FFFFFF';
                    circle.style.borderColor = '#16865D';
                    label.style.color = '#16865D';
                } else {
                    circle.style.background = '#FFFFFF';
                    circle.style.color = '#64748B';
                    circle.style.borderColor = '#E2E8F0';
                    label.style.color = '#64748B';
                }
            }
        }

        // Progress line fill
        const progressFill = document.getElementById('progressBarFill');
        if (progressFill) {
            const pct = ((currentStep - 1) / (totalSteps - 1)) * 100;
            progressFill.style.width = pct + '%';
        }

        // Mobile Indicators
        const mBadge = document.getElementById('mobileStepBadge');
        const mTitle = document.getElementById('mobileStepTitle');
        const mBar = document.getElementById('mobileProgressBar');
        if (mBadge) mBadge.innerText = 'Langkah ' + currentStep + ' / ' + totalSteps;
        if (mTitle) mTitle.innerText = stepTitles[currentStep];
        if (mBar) mBar.style.width = ((currentStep / totalSteps) * 100) + '%';

        // Re-render lucide icons if available
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Scroll to top of card
        window.scrollTo({ top: 120, behavior: 'smooth' });
    }

    function validateStep(stepNum) {
        if (stepNum === 1) {
            const name = document.getElementById('input_name').value.trim();
            const nik = document.getElementById('input_nik').value.trim();
            const tgl = document.getElementById('input_tanggal_lahir').value;
            const gender = document.getElementById('input_gender').value;

            if (!name) { alert('Nama lengkap wajib diisi sesuai KTP.'); return false; }
            if (nik.length !== 16 || !/^\d{16}$/.test(nik)) {
                alert('NIK harus berupa tepat 16 digit angka.');
                return false;
            }
            if (!tgl) { alert('Tanggal lahir wajib diisi.'); return false; }
            if (!gender) { alert('Jenis kelamin wajib dipilih.'); return false; }
        } else if (stepNum === 2) {
            const noKk = document.getElementById('input_no_kk').value.trim();
            if (noKk.length !== 16 || !/^\d{16}$/.test(noKk)) {
                alert('Nomor Kartu Keluarga (KK) harus berupa tepat 16 digit angka.');
                return false;
            }
        } else if (stepNum === 3) {
            const house = document.getElementById('input_house_number').value.trim();
            const addr = document.getElementById('input_address').value.trim();
            if (!house) { alert('Nomor rumah wajib diisi.'); return false; }
            if (!addr) { alert('Alamat lengkap tempat tinggal wajib diisi.'); return false; }
        } else if (stepNum === 4) {
            const phone = document.getElementById('input_phone').value.trim();
            const email = document.getElementById('input_email').value.trim();
            if (!phone) { alert('Nomor telepon/WhatsApp wajib diisi.'); return false; }
            if (!email || !email.includes('@')) { alert('Alamat email valid wajib diisi untuk akun login.'); return false; }
        }
        return true;
    }

    function nextStep(target) {
        if (validateStep(currentStep)) {
            currentStep = target;
            if (target === 6) {
                populateReviewData();
            }
            updateWizardUI();
        }
    }

    function prevStep(target) {
        currentStep = target;
        updateWizardUI();
    }

    function jumpToStep(target) {
        if (target < currentStep) {
            currentStep = target;
            updateWizardUI();
        } else if (target > currentStep) {
            if (validateStep(currentStep)) {
                currentStep = target;
                if (target === 6) {
                    populateReviewData();
                }
                updateWizardUI();
            }
        }
    }

    function populateReviewData() {
        document.getElementById('rev_name').innerText = document.getElementById('input_name').value || '-';
        document.getElementById('rev_nik').innerText = document.getElementById('input_nik').value || '-';
        
        const tempat = document.getElementById('input_tempat_lahir').value || '';
        const tgl = document.getElementById('input_tanggal_lahir').value || '';
        document.getElementById('rev_ttl').innerText = (tempat ? tempat + ', ' : '') + tgl;

        document.getElementById('rev_gender').innerText = document.getElementById('input_gender').value || '-';
        document.getElementById('rev_status_agama').innerText = (document.getElementById('input_status_perkawinan').value || '-') + ' / ' + (document.getElementById('input_agama').value || '-');
        
        document.getElementById('rev_no_kk').innerText = document.getElementById('input_no_kk').value || '-';
        document.getElementById('rev_hubungan').innerText = document.getElementById('input_hubungan_keluarga').value || '-';
        
        const rtSel = document.getElementById('input_rt_number');
        const rtTxt = rtSel.options[rtSel.selectedIndex]?.text || 'RT 001';
        const house = document.getElementById('input_house_number').value || '';
        document.getElementById('rev_rtrw').innerText = rtTxt + ' / RW 013, No. ' + house;
        
        document.getElementById('rev_status_rumah').innerText = document.getElementById('input_status_rumah').value || '-';
        document.getElementById('rev_domisili').innerText = document.getElementById('input_status_domisili').value || '-';
    }

    function submitWargaForm(type) {
        document.getElementById('formActionType').value = type;
        const form = document.getElementById('wargaWizardForm');
        form.submit();
    }

    function togglePassVisibility() {
        const inp = document.getElementById('input_password');
        if (inp.type === 'password') {
            inp.type = 'text';
        } else {
            inp.type = 'password';
        }
    }

    function resetWizard() {
        if (confirm('Apakah Anda yakin ingin mengosongkan seluruh isian formulir?')) {
            document.getElementById('wargaWizardForm').reset();
            currentStep = 1;
            updateWizardUI();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateWizardUI();
    });
</script>
@endsection
