@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('kependudukan.index', ['tab' => 'keluarga']) }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3 fw-semibold shadow-sm d-inline-flex align-items-center gap-1">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Kembali ke Data Kependudukan
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Detail Terpadu Kartu Keluarga</h4>
                <small class="text-muted">Nomor KK: <strong class="text-dark font-monospace">{{ $kk->masked_no_kk }}</strong> &bull; Wilayah: <span class="badge bg-secondary-subtle text-secondary">RT {{ str_pad($kk->rt, 3, '0', STR_PAD_LEFT) }} RW 013</span></small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($kk->isVerifiedDukcapil())
                <a href="{{ route('kependudukan.keluarga.cetak', $kk->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold btn-sm shadow-sm">
                    <i data-lucide="printer" style="width: 16px; height: 16px; vertical-align: middle;"></i> Cetak Salinan KK Resmi
                </a>
            @else
                <button type="button" class="btn btn-light rounded-pill px-3 py-2 fw-semibold btn-sm border text-muted" title="Dokumen KK belum berstatus Terverifikasi resmi." disabled>
                    <i data-lucide="lock" style="width: 14px; height: 14px; vertical-align: middle;"></i> Cetak KK (Terkunci)
                </button>
            @endif

            <span class="badge {{ $kk->status_verifikasi_dukcapil === 'Terverifikasi' ? 'bg-success text-white' : ($kk->status_verifikasi_dukcapil === 'Menunggu Verifikasi' ? 'bg-warning text-dark' : 'bg-secondary text-white') }} px-3 py-2 rounded-pill fs-6 fw-bold">
                <i data-lucide="{{ $kk->status_verifikasi_dukcapil === 'Terverifikasi' ? 'shield-check' : 'clock' }}" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                {{ $kk->status_verifikasi_dukcapil }}
            </span>
        </div>
    </div>

    <!-- Alert Notifikasi Session Flash -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i data-lucide="check" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="text-success d-block">Berhasil!</strong>
                <span class="small text-dark">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="rounded-circle bg-danger text-white p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="text-danger d-block">Perhatian</strong>
                <span class="small text-dark">{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 1. KARTU PROFIL KELUARGA & KEPALA KELUARGA -->
    <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-emerald-100 text-success p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #E8F5E9;">
                        <i data-lucide="home" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Keluarga {{ $kk->nama_kepala_keluarga ?: 'Warga' }}</h4>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-light text-dark border font-monospace">No. KK: {{ ($user->isRw() || $user->isRt()) ? $kk->no_kk : $kk->masked_no_kk }}</span>
                            <span class="badge bg-primary-subtle text-primary border">{{ $kk->anggota->count() }} Anggota Keluarga</span>
                            <span class="badge bg-secondary-subtle text-secondary border">RT {{ str_pad($kk->rt, 3, '0', STR_PAD_LEFT) }} RW 013</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 small">
                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Kepala Keluarga (KTP):</span>
                            <strong class="text-dark">{{ $kk->nama_kepala_keluarga ?: '-' }}</strong>
                            <span class="text-muted d-block font-monospace" style="font-size: 11px;">NIK: {{ $kk->nik_kepala_keluarga ?: '-' }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Status Tempat Tinggal:</span>
                            <strong class="text-dark">{{ $kk->status_rumah ?: 'Milik Sendiri' }}</strong>
                            <span class="text-muted d-block" style="font-size: 11px;">Penerbitan: {{ $kk->tgl_diterbitkan_dukcapil ? $kk->tgl_diterbitkan_dukcapil->format('d/m/Y') : 'Data Induk RW' }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-2 bg-light rounded-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Alamat Tempat Tinggal Resmi:</span>
                            <strong class="text-dark">{{ $kk->alamat ?: '-' }}</strong>
                            <span class="text-muted d-block" style="font-size: 11px;">Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung 40293</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Status Verifikasi & Berkas Digital -->
            <div class="col-md-4 border-start-md ps-md-4">
                <div class="p-3 rounded-4 {{ $kk->status_verifikasi_dukcapil === 'Terverifikasi' ? 'bg-emerald-50 border border-emerald-200' : 'bg-light border' }}" style="{{ $kk->status_verifikasi_dukcapil === 'Terverifikasi' ? 'background: #E8F5E9;' : '' }}">
                    <h6 class="fw-bold mb-2 text-dark">
                        <i data-lucide="file-check" style="width: 18px; height: 18px; vertical-align: middle;" class="text-success me-1"></i>
                        Status Berkas KK Digital
                    </h6>
                    <div class="mb-2">
                        <span class="badge {{ $kk->status_verifikasi_dukcapil === 'Terverifikasi' ? 'bg-success' : ($kk->status_verifikasi_dukcapil === 'Menunggu Verifikasi' ? 'bg-warning text-dark' : 'bg-secondary') }} px-3 py-1 rounded-pill">
                            {{ $kk->status_verifikasi_dukcapil }}
                        </span>
                    </div>

                    @if($kk->dokumen_kk_path)
                        <p class="small text-muted mb-2">
                            Berkas: <strong>{{ $kk->original_filename ?: 'KK_Digital.pdf' }}</strong> (v{{ $kk->versi_dokumen }})
                        </p>
                    @else
                        <p class="small text-muted mb-2">
                            <em>Belum ada salinan berkas fisik KK digital yang diunggah.</em>
                        </p>
                    @endif

                    @if($kk->verified_by && $kk->verifikator)
                        <div class="small text-muted mb-3" style="font-size: 11px;">
                            Diverifikasi oleh: <strong>{{ $kk->verifikator->name }}</strong><br>
                            Waktu: {{ $kk->verified_at ? $kk->verified_at->format('d/m/Y H:i') : '-' }}<br>
                            Metode: {{ $kk->metode_verifikasi ?: 'Pemeriksaan Berkas' }}
                        </div>
                    @endif

                    <div class="d-flex gap-2 flex-wrap">
                        @if($kk->dokumen_kk_path)
                            <a href="{{ route('kependudukan.keluarga.cetak', $kk->id) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-semibold">
                                <i data-lucide="eye" style="width: 14px; height: 14px; vertical-align: middle;"></i> Buka File
                            </a>
                        @endif

                        @if($user->isRw() || $user->isRt())
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalUploadKk">
                                <i data-lucide="upload" style="width: 14px; height: 14px; vertical-align: middle;"></i> Unggah KK
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalVerifikasiKk">
                                <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;"></i> Verifikasi
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. DAFTAR ANGGOTA KELUARGA TERPADU -->
    <div class="wm-card bg-white border rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i data-lucide="users" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Daftar Anggota Keluarga Terdaftar
                </h5>
                <p class="mb-0 text-muted small">
                    Klik pada nama anggota keluarga untuk melihat biodata individu secara lengkap.
                </p>
            </div>

            @if($user->isRw() || $user->isRt())
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahAnggota">
                        <i data-lucide="user-plus" style="width: 14px; height: 14px; vertical-align: middle;"></i> Tambah Anggota
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalGantiKepala">
                        <i data-lucide="repeat" style="width: 14px; height: 14px; vertical-align: middle;"></i> Ganti Kepala Keluarga
                    </button>
                </div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th>Nama Anggota (Klik Biodata)</th>
                        <th>NIK</th>
                        <th>Hubungan Keluarga</th>
                        <th>Gender</th>
                        <th>Usia / Tgl Lahir</th>
                        <th>Status Kawin</th>
                        <th>Pekerjaan</th>
                        <th>Status Verifikasi</th>
                        @if($user->isRw() || $user->isRt())
                            <th class="pe-4 text-end">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($kk->anggota as $idx => $m)
                        <tr class="{{ $m->hubungan_keluarga === 'Kepala Keluarga' ? 'table-light' : '' }}">
                            <td class="ps-4 fw-bold text-muted">{{ $idx + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $m->avatar_url }}" alt="{{ $m->name }}" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                                    <div>
                                        <a href="javascript:void(0)" class="fw-bold text-dark text-decoration-none btn-quickview-warga" data-id="{{ $m->id }}">
                                            {{ $m->name }}
                                        </a>
                                        @if($m->hubungan_keluarga === 'Kepala Keluarga')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 10px;">Kepala KK</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-monospace text-dark small">
                                    {{ ($user->isRw() || $user->isRt()) ? $m->nik : $m->masked_nik }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border fw-semibold">
                                    {{ $m->hubungan_keluarga ?: 'Anggota Keluarga' }}
                                </span>
                            </td>
                            <td>
                                <span class="small text-muted">{{ $m->gender }}</span>
                            </td>
                            <td>
                                <span class="text-dark fw-semibold">{{ $m->age ? $m->age . ' Th' : '-' }}</span>
                                <small class="text-muted d-block" style="font-size: 11px;">{{ $m->tanggal_lahir ? $m->tanggal_lahir->format('d/m/Y') : '-' }}</small>
                            </td>
                            <td>
                                <span class="small text-secondary">{{ $m->status_perkawinan ?: '-' }}</span>
                            </td>
                            <td>
                                <span class="small text-secondary">{{ $m->pekerjaan ?: '-' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                    {{ $m->status_verifikasi ?: 'Terdaftar' }}
                                </span>
                            </td>
                            @if($user->isRw() || $user->isRt())
                                <td class="pe-4 text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 btn-quickview-warga" data-id="{{ $m->id }}" title="Lihat Biodata Lengkap">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <a href="{{ route('warga.edit', $m->id) }}" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-secondary" title="Edit Data Warga">
                                            <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                        </a>
                                        @if($kk->anggota->count() > 1 && $otherKks->count() > 0)
                                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-warning btn-trigger-pindah" 
                                                    data-id="{{ $m->id }}" data-name="{{ $m->name }}" title="Pindahkan ke KK Lain">
                                                <i data-lucide="arrow-right-left" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ ($user->isRw() || $user->isRt()) ? 10 : 9 }}" class="text-center text-muted py-4">
                                Belum ada anggota keluarga terdaftar pada Kartu Keluarga ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3. RIWAYAT PERUBAHAN & AUDIT DOKUMEN KK -->
    <div class="wm-card bg-white border rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="p-4 border-bottom bg-light">
            <h5 class="fw-bold mb-0 text-dark">
                <i data-lucide="history" class="me-2 text-warning" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                Log Audit Riwayat Verifikasi & Perubahan KK
            </h5>
            <small class="text-muted">Setiap perubahan anggota, verifikasi dokumen, dan pergantian kepala keluarga tercatat secara permanen.</small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Waktu</th>
                        <th>Perubahan</th>
                        <th>Status Sebelum</th>
                        <th>Status Sesudah</th>
                        <th>Petugas Pengurus</th>
                        <th class="pe-4">Keterangan / Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kk->verifikasiHistories as $vh)
                        <tr>
                            <td class="ps-4">
                                <small class="text-dark fw-semibold d-block">{{ $vh->created_at->format('d/m/Y') }}</small>
                                <small class="text-muted" style="font-size: 11px;">{{ $vh->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $vh->metode_verifikasi }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">{{ $vh->status_lama }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $vh->status_baru }}</span>
                            </td>
                            <td>
                                <small class="fw-semibold text-dark">{{ $vh->petugas_nama }}</small>
                                <small class="text-muted d-block" style="font-size: 11px;">{{ strtoupper($vh->petugas_role) }}</small>
                            </td>
                            <td class="pe-4">
                                <small class="text-secondary">{{ $vh->catatan }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat audit tercatat untuk Kartu Keluarga ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- MODAL: TAMBAH ANGGOTA KELUARGA -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalTambahAnggota" tabindex="-1" aria-labelledby="modalTambahAnggotaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalTambahAnggotaLabel">
                    <i data-lucide="user-plus" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Tambah Anggota ke KK: {{ $kk->nama_kepala_keluarga }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kependudukan.keluarga.tambah_anggota', $kk->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Pilih Cara Penambahan Anggota <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check p-3 border rounded-3 flex-fill">
                                <input class="form-check-input" type="radio" name="mode" id="modeExisting" value="pilih_warga" checked>
                                <label class="form-check-label fw-semibold small" for="modeExisting">
                                    Pilih Warga Terdaftar (Tanpa KK)
                                </label>
                            </div>
                            <div class="form-check p-3 border rounded-3 flex-fill">
                                <input class="form-check-input" type="radio" name="mode" id="modeNew" value="input_baru">
                                <label class="form-check-label fw-semibold small" for="modeNew">
                                    Daftarkan Warga Baru
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Panel 1: Pilih Warga Terdaftar -->
                    <div id="panelExistingWarga" class="mb-3">
                        <label class="form-label fw-bold small text-dark">Pilih Warga di RT {{ str_pad($kk->rt, 3, '0', STR_PAD_LEFT) }}</label>
                        <select name="existing_user_id" class="form-select rounded-3">
                            <option value="">-- Pilih Nama Warga --</option>
                            @foreach($wargaTanpaKk as $wtk)
                                <option value="{{ $wtk->id }}">{{ $wtk->name }} (NIK: {{ $wtk->masked_nik }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Menampilkan warga yang belum terhubung ke Kartu Keluarga mana pun.</small>
                    </div>

                    <!-- Panel 2: Input Warga Baru (Hidden by default) -->
                    <div id="panelNewWarga" style="display: none;">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-3" placeholder="Nama lengkap sesuai akta/KTP">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                                <input type="text" name="nik" maxlength="16" class="form-control rounded-3 font-monospace" placeholder="16 digit NIK">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select rounded-3">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control rounded-3" placeholder="Pekerjaan saat ini">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">No. Telepon / WhatsApp</label>
                                <input type="text" name="phone" class="form-control rounded-3" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Status Hubungan dalam Keluarga <span class="text-danger">*</span></label>
                        <select name="hubungan_keluarga" class="form-select rounded-3" required>
                            <option value="Istri">Istri</option>
                            <option value="Suami">Suami</option>
                            <option value="Anak" selected>Anak</option>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Mertua">Mertua</option>
                            <option value="Famili Lain">Famili Lain</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Tambahkan ke Keluarga</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- MODAL: PINDAH KELUARGA -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalPindahKeluarga" tabindex="-1" aria-labelledby="modalPindahKeluargaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalPindahKeluargaLabel">
                    <i data-lucide="arrow-right-left" class="me-2 text-warning" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Pindah Kartu Keluarga: <span id="pindahMemberName" class="text-primary">-</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kependudukan.keluarga.pindah', $kk->id) }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="pindahUserId">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Pilih Kartu Keluarga Tujuan (RT {{ str_pad($kk->rt, 3, '0', STR_PAD_LEFT) }}) <span class="text-danger">*</span></label>
                        <select name="target_kk_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih KK Tujuan --</option>
                            @foreach($otherKks as $ok)
                                <option value="{{ $ok->id }}">
                                    KK: {{ $ok->nama_kepala_keluarga }} (No. {{ $ok->masked_no_kk }}) - {{ Str::limit($ok->alamat, 25) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Hubungan Keluarga Baru di KK Tujuan <span class="text-danger">*</span></label>
                        <select name="hubungan_keluarga_baru" class="form-select rounded-3" required>
                            <option value="Istri">Istri</option>
                            <option value="Suami">Suami</option>
                            <option value="Anak">Anak</option>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Mertua">Mertua</option>
                            <option value="Famili Lain">Famili Lain</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Alasan Kepindahan <span class="text-danger">*</span></label>
                        <textarea name="alasan_pindah" class="form-control rounded-3" rows="2" required placeholder="Contoh: Menikah, berpindah tempat tinggal dengan orang tua, dsb."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Konfirmasi Pindah KK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- MODAL: GANTI KEPALA KELUARGA -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalGantiKepala" tabindex="-1" aria-labelledby="modalGantiKepalaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalGantiKepalaLabel">
                    <i data-lucide="repeat" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Penetapan Kepala Keluarga Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kependudukan.keluarga.ganti_kepala', $kk->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Pilih salah satu anggota keluarga dewasa yang akan ditetapkan sebagai Kepala Keluarga resmi pada Kartu Keluarga ini.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Pilih Kepala Keluarga Pengganti <span class="text-danger">*</span></label>
                        <select name="new_kepala_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Anggota Keluarga --</option>
                            @foreach($kk->anggota as $m)
                                @if($m->hubungan_keluarga !== 'Kepala Keluarga')
                                    <option value="{{ $m->id }}">
                                        {{ $m->name }} ({{ $m->hubungan_keluarga }} - Usia: {{ $m->age }} Th)
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Hubungan Baru Kepala Keluarga Sebelumnya <span class="text-danger">*</span></label>
                        <select name="old_kepala_hubungan" class="form-select rounded-3" required>
                            <option value="Istri">Istri</option>
                            <option value="Suami">Suami</option>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Famili Lain">Famili Lain</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Alasan Pergantian <span class="text-danger">*</span></label>
                        <textarea name="alasan_pergantian" class="form-control rounded-3" rows="2" required placeholder="Contoh: Kepala keluarga sebelumnya telah wafat/pensiun/berpindah domisili."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Penetapan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- MODAL: UNGGAH DOKUMEN KK -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalUploadKk" tabindex="-1" aria-labelledby="modalUploadKkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalUploadKkLabel">
                    <i data-lucide="upload" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Unggah Salinan Dokumen KK Resmi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kependudukan.keluarga.upload', $kk->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Pilih Berkas Dokumen KK (PDF / JPG / PNG, Max 5MB) <span class="text-danger">*</span></label>
                        <input type="file" name="dokumen_kk" class="form-control rounded-3" required accept=".pdf,.jpg,.jpeg,.png">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Tanggal Penerbitan pada Dokumen KK</label>
                        <input type="date" name="tgl_diterbitkan_dukcapil" class="form-control rounded-3" value="{{ $kk->tgl_diterbitkan_dukcapil ? $kk->tgl_diterbitkan_dukcapil->format('Y-m-d') : '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Sumber Dokumen</label>
                        <input type="text" name="sumber_dokumen" class="form-control rounded-3" placeholder="Disdukcapil Kota Bandung / Kantor Kelurahan Sukamiskin" value="{{ $kk->sumber_dokumen }}">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Unggah Berkas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ====================================================================== -->
<!-- MODAL: VERIFIKASI DOKUMEN KK -->
<!-- ====================================================================== -->
<div class="modal fade" id="modalVerifikasiKk" tabindex="-1" aria-labelledby="modalVerifikasiKkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalVerifikasiKkLabel">
                    <i data-lucide="shield-check" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Verifikasi Dokumen Kartu Keluarga
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kependudukan.keluarga.verifikasi', $kk->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Status Verifikasi <span class="text-danger">*</span></label>
                        <select name="status_verifikasi" class="form-select rounded-3" required>
                            <option value="Terverifikasi" {{ $kk->status_verifikasi_dukcapil === 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi (Sah & Siap Cetak)</option>
                            <option value="Menunggu Verifikasi" {{ $kk->status_verifikasi_dukcapil === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="Perlu Pembaruan" {{ $kk->status_verifikasi_dukcapil === 'Perlu Pembaruan' ? 'selected' : '' }}>Perlu Pembaruan</option>
                            <option value="Ditolak" {{ $kk->status_verifikasi_dukcapil === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Metode Verifikasi <span class="text-danger">*</span></label>
                        <select name="metode_verifikasi" class="form-select rounded-3" required>
                            <option value="Pemeriksaan Berkas Fisik Asli">Pemeriksaan Berkas Fisik Asli</option>
                            <option value="Konfirmasi Langsung ke Warga / RT">Konfirmasi Langsung ke Warga / RT</option>
                            <option value="Validasi Arsip Disdukcapil Kota Bandung">Validasi Arsip Disdukcapil Kota Bandung</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Catatan Verifikasi <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control rounded-3" rows="3" required placeholder="Catatan keabsahan dokumen, nomor bukti verifikasi fisik, dsb.">{{ $kk->catatan_verifikasi_dukcapil }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL QUICKVIEW BIODATA WARGA (TERINTEGRASI) -->
<div class="modal fade" id="modalQuickviewWarga" tabindex="-1" aria-labelledby="modalQuickviewWargaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalQuickviewWargaLabel">
                    <i data-lucide="user-check" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Biodata Lengkap Anggota Keluarga
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="quickviewLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted small mt-2 mb-0">Memuat biodata...</p>
                </div>
                <div id="quickviewContent" style="display: none;">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <img id="qvAvatar" src="" class="rounded-circle border" style="width: 64px; height: 64px; object-fit: cover;">
                        <div>
                            <h4 id="qvName" class="fw-bold mb-1 text-dark">-</h4>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span id="qvNik" class="badge bg-light text-dark border font-monospace fs-6">NIK: -</span>
                                <span id="qvHubungan" class="badge bg-primary-subtle text-primary border">-</span>
                                <span id="qvRt" class="badge bg-secondary-subtle text-secondary border">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Jenis Kelamin & Usia:</span>
                                <strong id="qvGenderAge" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Tempat, Tanggal Lahir:</span>
                                <strong id="qvTtl" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Agama & Status Kawin:</span>
                                <strong id="qvAgamaKawin" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Pekerjaan:</span>
                                <strong id="qvPekerjaan" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Nomor Telepon:</span>
                                <strong id="qvPhone" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Status Kependudukan:</span>
                                <strong id="qvStatusDomisili" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded-3">
                                <span class="text-muted d-block" style="font-size: 11px;">Alamat Tempat Tinggal:</span>
                                <strong id="qvAddress" class="text-dark">-</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex gap-2">
                    <a id="qvFullDetailBtn" href="#" class="btn btn-primary rounded-pill px-4 fw-bold">
                        Buka Halaman Biodata &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Toggle radio mode tambah anggota
        var radioExisting = document.getElementById('modeExisting');
        var radioNew = document.getElementById('modeNew');
        var panelExisting = document.getElementById('panelExistingWarga');
        var panelNew = document.getElementById('panelNewWarga');

        if (radioExisting && radioNew) {
            radioExisting.addEventListener('change', function () {
                panelExisting.style.display = 'block';
                panelNew.style.display = 'none';
            });
            radioNew.addEventListener('change', function () {
                panelExisting.style.display = 'none';
                panelNew.style.display = 'block';
            });
        }

        // Trigger Pindah KK
        var modalPindah = document.getElementById('modalPindahKeluarga');
        if (modalPindah) {
            var bsPindah = new bootstrap.Modal(modalPindah);
            document.querySelectorAll('.btn-trigger-pindah').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var mId = this.getAttribute('data-id');
                    var mName = this.getAttribute('data-name');
                    document.getElementById('pindahUserId').value = mId;
                    document.getElementById('pindahMemberName').textContent = mName;
                    bsPindah.show();
                });
            });
        }

        // Quickview Warga AJAX handler
        var modalEl = document.getElementById('modalQuickviewWarga');
        if (modalEl) {
            var bsModal = new bootstrap.Modal(modalEl);
            document.querySelectorAll('.btn-quickview-warga').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var wargaId = this.getAttribute('data-id');
                    document.getElementById('quickviewLoading').style.display = 'block';
                    document.getElementById('quickviewContent').style.display = 'none';
                    bsModal.show();

                    fetch('/kependudukan/api/warga/' + wargaId)
                        .then(function (response) { return response.json(); })
                        .then(function (data) {
                            if (data.error) {
                                alert(data.error);
                                bsModal.hide();
                                return;
                            }
                            document.getElementById('qvAvatar').src = data.avatar_url;
                            document.getElementById('qvName').textContent = data.name;
                            document.getElementById('qvNik').textContent = 'NIK: ' + data.full_nik;
                            document.getElementById('qvHubungan').textContent = data.hubungan_keluarga;
                            document.getElementById('qvRt').textContent = data.rt;
                            document.getElementById('qvGenderAge').textContent = data.gender + ' (' + (data.age ? data.age + ' Tahun' : '-') + ')';
                            document.getElementById('qvTtl').textContent = (data.tempat_lahir || 'Bandung') + ', ' + data.tanggal_lahir;
                            document.getElementById('qvAgamaKawin').textContent = (data.agama || 'Islam') + ' / ' + (data.status_perkawinan || 'Belum Kawin');
                            document.getElementById('qvPekerjaan').textContent = data.pekerjaan || '-';
                            document.getElementById('qvPhone').textContent = data.phone;
                            document.getElementById('qvStatusDomisili').textContent = (data.status_domisili || 'Warga Tetap') + ' (' + (data.status_verifikasi || 'Terdaftar') + ')';
                            document.getElementById('qvAddress').textContent = data.address || '-';
                            document.getElementById('qvFullDetailBtn').href = data.detail_url;

                            document.getElementById('quickviewLoading').style.display = 'none';
                            document.getElementById('quickviewContent').style.display = 'block';
                        })
                        .catch(function (err) {
                            alert('Gagal memuat biodata warga.');
                            bsModal.hide();
                        });
                });
            });
        }
    });
</script>
@endsection
