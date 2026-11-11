@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- ======================================================================
         PAGE HEADER & BREADCRUMBS
         ====================================================================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-icon-pill shadow-sm" style="width: 48px; height: 48px; border-radius: 14px; background: #EAF3FF; color: #1456A0; font-size: 20px;">
                <i data-lucide="user-check" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="color: var(--wm-text) !important;">Profil Kependudukan Warga</h3>
                <p class="text-muted mb-0 small">Berkas administrasi sipil terpadu, data keluarga, dan dokumen kependudukan resmi.</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('warga.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
                <span>Kembali</span>
            </a>
            <a href="{{ route('warga.edit', $warga->id) }}" class="btn btn-primary rounded-pill px-3 py-2 fw-bold small d-inline-flex align-items-center gap-2 shadow-sm" style="background: #1456A0; border-color: #1456A0;">
                <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                <span>Edit Biodata</span>
            </a>
            <button type="button" class="btn btn-outline-success rounded-pill px-3 py-2 fw-bold small d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalVerifikasiWarga">
                <i data-lucide="shield-check" style="width: 15px; height: 15px;"></i>
                <span>Verifikasi Status</span>
            </button>
        </div>
    </div>

    {{-- Notifikasi Sukses / Gagal --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert" style="background: #E3F5EC; color: #16865D; border-radius: 14px;">
            <i data-lucide="check-circle-2" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-medium small">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert" style="background: #FEE2E2; color: #DC2626; border-radius: 14px;">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
            <div class="fw-medium small">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ======================================================================
         HERO PROFILE SUMMARY CARD
         ====================================================================== --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%); border-left: 5px solid #1456A0 !important;">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $warga->avatar_url }}" alt="{{ $warga->name }}" class="rounded-circle shadow-sm border" style="width: 80px; height: 80px; object-fit: cover; border-width: 3px !important; border-color: #FFFFFF !important;">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h4 class="fw-bold mb-0 text-dark">{{ $warga->name }}</h4>
                        {{-- Badge Hubungan Keluarga --}}
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #EAF3FF; color: #1456A0; font-size: 0.76rem;">
                            {{ $warga->hubungan_keluarga ?: 'Warga' }}
                        </span>
                        {{-- Badge Status Domisili --}}
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #E3F5EC; color: #16865D; font-size: 0.76rem;">
                            {{ $warga->status_domisili ?: 'Warga Tetap' }}
                        </span>
                    </div>

                    <div class="text-muted small d-flex align-items-center gap-3 flex-wrap">
                        <span><i data-lucide="credit-card" style="width: 14px; height: 14px; color: #1456A0;" class="me-1"></i> NIK: <strong class="text-dark font-monospace">{{ $warga->masked_nik }}</strong></span>
                        <span><i data-lucide="folder" style="width: 14px; height: 14px; color: #1456A0;" class="me-1"></i> No. KK: <strong class="text-dark font-monospace">{{ $warga->masked_no_kk }}</strong></span>
                        <span><i data-lucide="map-pin" style="width: 14px; height: 14px; color: #DC2626;" class="me-1"></i> {{ $warga->clean_rt_label }} / RW 013 (No. {{ $warga->house_number }})</span>
                    </div>
                </div>
            </div>

            {{-- Status Verifikasi Badge Box --}}
            <div class="d-flex flex-column align-items-md-end gap-2">
                <div>
                    <span class="text-muted small d-block mb-1 text-md-end">Status Verifikasi Kependudukan:</span>
                    @php
                        $vColor = '#64748B'; $vBg = '#F1F5F9';
                        if ($warga->status_verifikasi === 'Terverifikasi RW') { $vColor = '#16865D'; $vBg = '#E3F5EC'; }
                        elseif ($warga->status_verifikasi === 'Terverifikasi RT') { $vColor = '#1456A0'; $vBg = '#EAF3FF'; }
                        elseif (str_contains($warga->status_verifikasi ?? '', 'Menunggu')) { $vColor = '#D97706'; $vBg = '#FEF3C7'; }
                        elseif (in_array($warga->status_verifikasi, ['Perlu Perbaikan', 'Ditolak'])) { $vColor = '#DC2626'; $vBg = '#FEE2E2'; }
                    @endphp
                    <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: {{ $vBg }}; color: {{ $vColor }}; font-size: 0.85rem;">
                        <i data-lucide="shield-check" style="width: 14px; height: 14px;" class="me-1"></i>
                        {{ $warga->status_verifikasi ?: 'Belum Diverifikasi' }}
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 fw-bold small" data-bs-toggle="modal" data-bs-target="#modalResetPassword">
                        <i data-lucide="key" style="width: 13px; height: 13px;" class="me-1"></i> Reset Password
                    </button>
                    <form action="{{ route('warga.toggleActive', $warga->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm {{ $warga->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} rounded-pill px-3 py-1 fw-bold small">
                            <i data-lucide="{{ $warga->is_active ? 'user-x' : 'user-check' }}" style="width: 13px; height: 13px;" class="me-1"></i>
                            {{ $warga->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         TAB NAVIGATION
         ====================================================================== --}}
    <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-2" id="wargaDetailTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2 fw-bold small" id="biodata-tab" data-bs-toggle="tab" data-bs-target="#biodata-content" type="button" role="tab">
                <i data-lucide="user" style="width: 15px; height: 15px;" class="me-1"></i> Biodata Lengkap
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 fw-bold small" id="keluarga-tab" data-bs-toggle="tab" data-bs-target="#keluarga-content" type="button" role="tab">
                <i data-lucide="users" style="width: 15px; height: 15px;" class="me-1"></i> Anggota Keluarga (KK)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 fw-bold small" id="dokumen-tab" data-bs-toggle="tab" data-bs-target="#dokumen-content" type="button" role="tab">
                <i data-lucide="file-text" style="width: 15px; height: 15px;" class="me-1"></i> Dokumen Kependudukan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 fw-bold small" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-content" type="button" role="tab">
                <i data-lucide="history" style="width: 15px; height: 15px;" class="me-1"></i> Riwayat Audit Verifikasi
            </button>
        </li>
    </ul>

    {{-- ======================================================================
         TAB CONTENT
         ====================================================================== --}}
    <div class="tab-content" id="wargaDetailTabContent">
        {{-- TAB 1: BIODATA LENGKAP --}}
        <div class="tab-pane fade show active" id="biodata-content" role="tabpanel">
            <div class="row g-4">
                {{-- Data Sipil & Identitas --}}
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i data-lucide="badge-check" style="width: 18px; height: 18px; color: #1456A0;"></i>
                            <h6 class="fw-bold mb-0 text-dark">Data Sipil Pribadi</h6>
                        </div>
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <tr><td class="text-muted small" style="width: 40%;">Nama Lengkap</td><td class="fw-bold text-dark small">{{ $warga->name }}</td></tr>
                                <tr><td class="text-muted small">NIK (KTP)</td><td class="fw-bold text-primary font-monospace small">{{ $warga->masked_nik }}</td></tr>
                                <tr><td class="text-muted small">Jenis Kelamin</td><td class="text-dark small">{{ $warga->gender ?: '-' }}</td></tr>
                                <tr><td class="text-muted small">Tempat, Tanggal Lahir</td><td class="text-dark small">{{ $warga->tempat_lahir ?: '-' }}, {{ $warga->tanggal_lahir ? $warga->tanggal_lahir->translatedFormat('d F Y') : '-' }} ({{ $warga->age }} tahun)</td></tr>
                                <tr><td class="text-muted small">Status Perkawinan</td><td class="text-dark small">{{ $warga->status_perkawinan ?: 'Belum Kawin' }}</td></tr>
                                <tr><td class="text-muted small">Agama</td><td class="text-dark small">{{ $warga->agama ?: 'Islam' }}</td></tr>
                                <tr><td class="text-muted small">Kewarganegaraan</td><td class="text-dark small">{{ $warga->kewarganegaraan ?: 'WNI' }}</td></tr>
                                <tr><td class="text-muted small">Pekerjaan</td><td class="text-dark small">{{ $warga->pekerjaan ?: '-' }}</td></tr>
                                <tr><td class="text-muted small">Pendidikan Terakhir</td><td class="text-dark small">{{ $warga->pendidikan_terakhir ?: '-' }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Data Alamat & Tempat Tinggal --}}
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i data-lucide="map-pinned" style="width: 18px; height: 18px; color: #1456A0;"></i>
                            <h6 class="fw-bold mb-0 text-dark">Alamat & Tempat Tinggal</h6>
                        </div>
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <tr><td class="text-muted small" style="width: 40%;">Rukun Tetangga (RT)</td><td class="fw-bold text-dark small">{{ $warga->clean_rt_label }}</td></tr>
                                <tr><td class="text-muted small">Rukun Warga (RW)</td><td class="fw-bold text-dark small">RW 013</td></tr>
                                <tr><td class="text-muted small">Nomor Rumah / Blok</td><td class="text-dark small">No. {{ $warga->house_number }} {{ $warga->blok_rumah ? '(Blok ' . $warga->blok_rumah . ')' : '' }}</td></tr>
                                <tr><td class="text-muted small">Jalan / Gang</td><td class="text-dark small">{{ $warga->gang_jalan ?: 'Jl. Pacuan Kuda' }}</td></tr>
                                <tr><td class="text-muted small">Kelurahan / Kecamatan</td><td class="text-dark small">Kel. Sukamiskin, Kec. Arcamanik</td></tr>
                                <tr><td class="text-muted small">Kota / Kode Pos</td><td class="text-dark small">Kota Bandung, 40293</td></tr>
                                <tr><td class="text-muted small">Status Rumah</td><td class="text-dark small">{{ ucfirst(str_replace('_', ' ', $warga->status_rumah ?? 'Milik Sendiri')) }}</td></tr>
                                <tr><td class="text-muted small">Status Domisili</td><td class="fw-bold text-success small">{{ $warga->status_domisili ?: 'Warga Tetap' }}</td></tr>
                                <tr><td class="text-muted small">Alamat Lengkap</td><td class="text-dark small">{{ $warga->address }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kontak & Akun --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i data-lucide="phone-call" style="width: 18px; height: 18px; color: #1456A0;"></i>
                            <h6 class="fw-bold mb-0 text-dark">Kontak Komunikasi & Akses Akun</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <span class="text-muted small d-block">Nomor Telepon / WhatsApp:</span>
                                <span class="fw-bold text-dark">{{ $warga->phone ?: '-' }}</span>
                            </div>
                            <div class="col-12 col-md-4">
                                <span class="text-muted small d-block">Email Login Warga:</span>
                                <span class="fw-bold text-dark">{{ $warga->email }}</span>
                            </div>
                            <div class="col-12 col-md-4">
                                <span class="text-muted small d-block">Status Akun Login:</span>
                                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: {{ $warga->is_active ? '#E3F5EC' : '#FEE2E2' }}; color: {{ $warga->is_active ? '#16865D' : '#DC2626' }};">
                                    {{ $warga->is_active ? 'Akun Aktif' : 'Akun Dinonaktifkan' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: DATA KELUARGA (KK) --}}
        <div class="tab-pane fade" id="keluarga-content" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-4 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="wm-icon-pill p-2 rounded-3" style="background: #EAF3FF; color: #1456A0;">
                            <i data-lucide="folder-check" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Kartu Keluarga No. <span class="text-primary font-monospace">{{ $warga->masked_no_kk }}</span></h5>
                            <small class="text-muted">Kepala Keluarga: <strong>{{ $kartuKeluarga->nama_kepala_keluarga ?? $warga->name }}</strong> &bull; Total Anggota: {{ $anggotaKeluarga->count() }} Jiwa</small>
                        </div>
                    </div>

                    @if($kartuKeluarga)
                        <a href="{{ route('arsip-kk.show', $kartuKeluarga->id) }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-2">
                            <i data-lucide="archive" style="width: 15px; height: 15px;"></i>
                            <span>Buka Arsip KK Resmi</span>
                        </a>
                    @endif
                </div>

                {{-- Tabel Anggota Keluarga --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small text-uppercase">
                                <th style="width: 50px;">No</th>
                                <th>Nama Anggota</th>
                                <th>NIK</th>
                                <th>Hubungan</th>
                                <th>Gender</th>
                                <th>Usia</th>
                                <th>Status Domisili</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anggotaKeluarga as $idx => $m)
                                <tr>
                                    <td class="small text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $m->avatar_url }}" alt="{{ $m->name }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                            <div>
                                                <a href="{{ route('warga.show', $m->id) }}" class="fw-bold text-decoration-none text-dark small d-block">
                                                    {{ $m->name }}
                                                </a>
                                                @if($m->id === $warga->id)
                                                    <span class="badge bg-primary text-white" style="font-size: 0.65rem;">Profil Saat Ini</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-monospace small">{{ $m->masked_nik }}</td>
                                    <td>
                                        <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #EAF3FF; color: #1456A0; font-size: 0.72rem;">
                                            {{ $m->hubungan_keluarga ?: 'Anggota' }}
                                        </span>
                                    </td>
                                    <td class="small">{{ $m->gender ?: '-' }}</td>
                                    <td class="small">{{ $m->age !== null ? $m->age . ' Thn' : '-' }}</td>
                                    <td>
                                        <span class="badge rounded-pill px-2 py-1 small fw-semibold" style="background: #E3F5EC; color: #16865D; font-size: 0.72rem;">
                                            {{ $m->status_domisili ?: 'Warga Tetap' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('warga.show', $m->id) }}" class="btn btn-sm btn-light text-primary rounded-circle" title="Lihat Profil">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 3: DOKUMEN KEPENDUDUKAN --}}
        <div class="tab-pane fade" id="dokumen-content" role="tabpanel">
            <div class="row g-4">
                {{-- Dokumen KTP --}}
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="wm-icon-pill p-2 rounded-3" style="background: #EAF3FF; color: #1456A0;">
                                <i data-lucide="credit-card" style="width: 20px; height: 20px;"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">KTP Elektronik / IKD</h6>
                        </div>
                        <p class="text-muted small mb-3">Salinan digital identitas kependudukan resmi warga pemilik NIK {{ $warga->masked_nik }}.</p>
                        
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            @if($warga->dokumen_ktp_path)
                                <span class="badge bg-success-subtle text-success fw-bold small">Tersedia</span>
                                <a href="{{ route('warga.dokumen.download', [$warga->id, 'ktp']) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i data-lucide="download" style="width: 13px; height: 13px;" class="me-1"></i> Buka / Unduh
                                </a>
                            @else
                                <span class="badge bg-secondary-subtle text-muted small">Belum Diunggah</span>
                                <a href="{{ route('warga.edit', $warga->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i data-lucide="upload" style="width: 13px; height: 13px;" class="me-1"></i> Unggah
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Dokumen KK Dukcapil --}}
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="wm-icon-pill p-2 rounded-3" style="background: #E3F5EC; color: #16865D;">
                                <i data-lucide="folder-check" style="width: 20px; height: 20px;"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Salinan KK Resmi Dukcapil</h6>
                        </div>
                        <p class="text-muted small mb-3">Berkas salinan KK resmi berkode QR / tanda tangan elektronik autentik Dukcapil.</p>
                        
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            @if($kartuKeluarga && $kartuKeluarga->dokumen_kk_path)
                                <div>
                                    <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: {{ $kartuKeluarga->isVerifiedDukcapil() ? '#E3F5EC' : '#FEF3C7' }}; color: {{ $kartuKeluarga->isVerifiedDukcapil() ? '#16865D' : '#D97706' }}; font-size: 0.72rem;">
                                        {{ $kartuKeluarga->status_verifikasi_dukcapil }}
                                    </span>
                                </div>
                                @if($kartuKeluarga->isVerifiedDukcapil())
                                    <a href="{{ route('arsip-kk.stream', $kartuKeluarga->id) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3">
                                        <i data-lucide="printer" style="width: 13px; height: 13px;" class="me-1"></i> Cetak / Unduh
                                    </a>
                                @else
                                    <a href="{{ route('arsip-kk.show', $kartuKeluarga->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                        <i data-lucide="shield-alert" style="width: 13px; height: 13px;" class="me-1"></i> Cek Status
                                    </a>
                                @endif
                            @else
                                <span class="badge bg-secondary-subtle text-muted small">Belum Tersedia</span>
                                @if($kartuKeluarga)
                                    <a href="{{ route('arsip-kk.show', $kartuKeluarga->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i data-lucide="upload" style="width: 13px; height: 13px;" class="me-1"></i> Unggah PDF
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Dokumen Pendukung Lain --}}
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="wm-icon-pill p-2 rounded-3" style="background: #F1F5F9; color: #475569;">
                                <i data-lucide="paperclip" style="width: 20px; height: 20px;"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Dokumen Pendukung Lain</h6>
                        </div>
                        <p class="text-muted small mb-3">Berkas pelengkap seperti Akta Kelahiran, Surat Nikah, Surat Keterangan Pindah, dll.</p>
                        
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            @if($warga->dokumen_pendukung_path)
                                <span class="badge bg-success-subtle text-success fw-bold small">Tersedia</span>
                                <a href="{{ route('warga.dokumen.download', [$warga->id, 'pendukung']) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i data-lucide="download" style="width: 13px; height: 13px;" class="me-1"></i> Buka / Unduh
                                </a>
                            @else
                                <span class="badge bg-secondary-subtle text-muted small">Belum Diunggah</span>
                                <a href="{{ route('warga.edit', $warga->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    <i data-lucide="upload" style="width: 13px; height: 13px;" class="me-1"></i> Unggah
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 4: RIWAYAT AUDIT VERIFIKASI --}}
        <div class="tab-pane fade" id="riwayat-content" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                    <i data-lucide="history" style="width: 20px; height: 20px; color: #1456A0;"></i>
                    <h6 class="fw-bold mb-0 text-dark">Log Riwayat Perubahan Status & Audit Pemeriksaan</h6>
                </div>

                @if($statusHistories->isEmpty())
                    <div class="text-center py-4 text-muted small">
                        <i data-lucide="check-circle" style="width: 32px; height: 32px; opacity: 0.5;" class="mb-2 d-block mx-auto"></i>
                        Belum ada catatan riwayat perubahan status pada akun ini.
                    </div>
                @else
                    <div class="timeline position-relative ps-4" style="border-left: 2px solid #E2E8F0;">
                        @foreach($statusHistories as $h)
                            <div class="mb-4 position-relative">
                                <div class="position-absolute rounded-circle" style="width: 12px; height: 12px; background: #1456A0; left: -23px; top: 4px; border: 2px solid #FFFFFF;"></div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <span class="badge rounded-pill px-2 py-1 small fw-bold" style="background: #EAF3FF; color: #1456A0;">
                                        {{ str_replace('_', ' ', strtoupper($h->tipe_perubahan)) }}
                                    </span>
                                    <span class="text-dark fw-bold small">
                                        {{ $h->status_lama ? "{$h->status_lama} → " : "" }}<span class="text-primary">{{ $h->status_baru }}</span>
                                    </span>
                                    <span class="text-muted small ms-auto">{{ $h->created_at ? $h->created_at->translatedFormat('d M Y, H:i') : '-' }} WIB</span>
                                </div>
                                <p class="text-muted small mb-1">{{ $h->catatan }}</p>
                                <small class="text-muted" style="font-size: 0.74rem;">
                                    Petugas: <strong>{{ $h->petugas_nama ?: 'Sistem Warkas Machi' }}</strong> ({{ strtoupper($h->petugas_role ?: 'Admin') }})
                                </small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ======================================================================
     MODAL VERIFIKASI STATUS WARGA (RT & RW LEVEL)
     ====================================================================== --}}
<div class="modal fade" id="modalVerifikasiWarga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="shield-check" style="width: 20px; height: 20px; color: #1456A0;"></i>
                    Verifikasi Status Kependudukan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('warga.verifikasi', $warga->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="p-3 rounded-3 mb-3 bg-light small">
                        <div>Nama: <strong>{{ $warga->name }}</strong></div>
                        <div>NIK: <span class="font-monospace text-primary">{{ $warga->masked_nik }}</span></div>
                        <div>Status Saat Ini: <span class="badge bg-secondary-subtle text-dark">{{ $warga->status_verifikasi }}</span></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Pilih Status Baru <span class="text-danger">*</span></label>
                        <select name="status_verifikasi" class="form-select" required>
                            @if(auth()->user()->isRw())
                                <option value="Terverifikasi RW" selected>Terverifikasi RW (Sah Penuh)</option>
                                <option value="Perlu Perbaikan">Perlu Perbaikan (Kembalikan ke RT/Warga)</option>
                                <option value="Ditolak">Ditolak</option>
                            @else
                                <option value="Terverifikasi RT" selected>Terverifikasi RT (Teruskan ke RW)</option>
                                <option value="Menunggu Verifikasi RW">Menunggu Verifikasi RW</option>
                                <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                                <option value="Ditolak">Ditolak</option>
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Catatan Pemeriksaan / Verifikasi <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tuliskan catatan hasil verifikasi berkas dan legalitas identitas..." required>Data identitas dan domisili telah diperiksa serta sesuai dengan catatan register kewilayahan.</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: #1456A0;">
                        Simpan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================================================================
     MODAL RESET PASSWORD AKUN WARGA (SECURE HASHING)
     ====================================================================== --}}
<div class="modal fade" id="modalResetPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="key" style="width: 20px; height: 20px; color: #D97706;"></i>
                    Atur Ulang Password Akun
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('warga.reset_password', $warga->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Atur ulang password login akun warga <strong>{{ $warga->name }}</strong> ({{ $warga->email }}). Password akan dienkripsi secara aman menggunakan Laravel Hash.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" minlength="6" placeholder="Minimal 6 karakter" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="new_password_confirmation" class="form-control" minlength="6" placeholder="Ulangi password baru" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
