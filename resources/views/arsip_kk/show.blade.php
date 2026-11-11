@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- ======================================================================
         PAGE HEADER & BREADCRUMBS
         ====================================================================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-icon-pill shadow-sm" style="width: 48px; height: 48px; border-radius: 14px; background: #EAF3FF; color: #1456A0; font-size: 20px;">
                <i data-lucide="folder-check" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="color: var(--wm-text) !important;">Berkas Arsip KK Resmi</h3>
                <p class="text-muted mb-0 small">No. KK: <strong class="font-monospace text-primary">{{ $kk->masked_no_kk }}</strong> &bull; {{ $kk->clean_rt_label }} / RW 013 Sukamiskin Bandung</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('arsip-kk.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
                <span>Kembali ke Daftar</span>
            </a>

            {{-- Tombol Unggah Salinan PDF --}}
            @if(auth()->user()->isRw() || auth()->user()->isRt())
                <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold small d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalUploadKk">
                    <i data-lucide="upload" style="width: 15px; height: 15px;"></i>
                    <span>Unggah Salinan PDF</span>
                </button>
            @endif

            {{-- Tombol Verifikasi Resmi (Khusus RW) --}}
            @if(auth()->user()->isRw())
                <button type="button" class="btn btn-primary rounded-pill px-3 py-2 fw-bold small d-inline-flex align-items-center gap-2 shadow-sm" style="background: #1456A0;" data-bs-toggle="modal" data-bs-target="#modalVerifikasiDukcapil">
                    <i data-lucide="shield-check" style="width: 15px; height: 15px;"></i>
                    <span>Verifikasi Berkas</span>
                </button>
            @endif

            {{-- Tombol Cetak Dokumen (HANYA AKTIF JIKA TERVERIFIKASI) --}}
            @if($kk->isVerifiedDukcapil() && $kk->hasDokumenPdf())
                <a href="{{ route('arsip-kk.cetak', $kk->id) }}" target="_blank" class="btn btn-success rounded-pill px-3 py-2 fw-bold small d-inline-flex align-items-center gap-2 shadow-sm">
                    <i data-lucide="printer" style="width: 15px; height: 15px;"></i>
                    <span>Cetak Salinan Resmi</span>
                </a>
            @else
                <button type="button" class="btn btn-secondary opacity-50 rounded-pill px-3 py-2 fw-bold small d-inline-flex align-items-center gap-2" onclick="alert('Pencetakan salinan KK resmi dinonaktifkan: Dokumen berstatus {{ $kk->status_verifikasi_dukcapil }}. Hanya dokumen dengan status Terverifikasi yang sah dicetak.');" title="Pencetakan dinonaktifkan hingga berkas Terverifikasi">
                    <i data-lucide="lock" style="width: 15px; height: 15px;"></i>
                    <span>Cetak Terkunci</span>
                </button>
            @endif
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
         STATUS BANNER & CRITICAL BOUNDARY
         ====================================================================== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4 position-relative overflow-hidden" 
         style="background: {{ $kk->isVerifiedDukcapil() ? '#E3F5EC' : '#FEF3C7' }}; border-left: 5px solid {{ $kk->isVerifiedDukcapil() ? '#16865D' : '#D97706' }} !important;">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: {{ $kk->isVerifiedDukcapil() ? '#16865D' : '#D97706' }}; color: #FFFFFF; font-size: 0.78rem;">
                        Status: {{ $kk->status_verifikasi_dukcapil }}
                    </span>
                    <span class="badge rounded-pill bg-white text-dark border px-3 py-1 fw-semibold small">
                        Status Dokumen: {{ $kk->status_dokumen }}
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">
                    @if($kk->isVerifiedDukcapil())
                        Dokumen Salinan KK Resmi Telah Terverifikasi Sah
                    @elseif($kk->status_verifikasi_dukcapil === 'Menunggu Verifikasi')
                        Salinan Dokumen Sedang Menunggu Pemeriksaan Legalitas oleh Sekretariat RW
                    @else
                        Dokumen Belum Terverifikasi Resmi Dukcapil
                    @endif
                </h5>
                <p class="mb-0 text-muted small">
                    {{ $kk->catatan_verifikasi_dukcapil ?: 'Pastikan kepala keluarga telah menyerahkan salinan PDF/cetak fisik KK resmi berkode QR Dukcapil untuk diverifikasi.' }}
                </p>
            </div>

            @if($kk->verified_at)
                <div class="text-md-end">
                    <span class="text-muted small d-block">Diverifikasi pada:</span>
                    <strong class="text-dark small">{{ $kk->verified_at->translatedFormat('d F Y, H:i') }} WIB</strong>
                    <div class="text-muted small">Oleh: {{ $kk->verifikator ? $kk->verifikator->name : 'Ketua RW 013' }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- ======================================================================
         GRID DETAIL DOKUMEN & ANGGOTA KELUARGA
         ====================================================================== --}}
    <div class="row g-4">
        {{-- KOLOM KIRI: IDENTITAS KK & FILE DOKUMEN --}}
        <div class="col-12 col-lg-5">
            <div class="d-flex flex-column gap-4">
                {{-- Card 1: Data Kepala Keluarga & Alamat --}}
                <div class="card border-0 shadow-sm rounded-4 p-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i data-lucide="home" style="width: 18px; height: 18px; color: #1456A0;"></i>
                        <h6 class="fw-bold mb-0 text-dark">Informasi Kartu Keluarga</h6>
                    </div>

                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted small" style="width: 45%;">Nomor KK</td>
                                <td class="fw-bold text-primary font-monospace small">
                                    {{ auth()->user()->isRw() ? $kk->no_kk : $kk->masked_no_kk }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Kepala Keluarga</td>
                                <td class="fw-bold text-dark small">{{ $kk->nama_kepala_keluarga }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">NIK Kepala Keluarga</td>
                                <td class="font-monospace text-dark small">
                                    {{ $kk->nik_kepala_keluarga ? (auth()->user()->isRw() ? $kk->nik_kepala_keluarga : substr($kk->nik_kepala_keluarga, 0, 6) . '******' . substr($kk->nik_kepala_keluarga, -4)) : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Wilayah</td>
                                <td class="text-dark small">{{ $kk->clean_rt_label }} / RW 013</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Alamat Rumah</td>
                                <td class="text-dark small">{{ $kk->alamat }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Kelurahan / Kecamatan</td>
                                <td class="text-dark small">{{ $kk->kelurahan }}, {{ $kk->kecamatan }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Kota / Kode Pos</td>
                                <td class="text-dark small">{{ $kk->kota }} {{ $kk->kode_pos }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Status Tempat Tinggal</td>
                                <td class="text-dark small">{{ $kk->status_rumah }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Card 2: Berkas PDF Salinan Resmi Dukcapil --}}
                <div class="card border-0 shadow-sm rounded-4 p-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="file-text" style="width: 18px; height: 18px; color: #16865D;"></i>
                            <h6 class="fw-bold mb-0 text-dark">Berkas Dokumen Digital</h6>
                        </div>
                        <span class="badge bg-light text-muted border small">Versi {{ $kk->versi_dokumen }}</span>
                    </div>

                    @if($kk->hasDokumenPdf())
                        <div class="p-3 rounded-3 mb-3 bg-light d-flex align-items-center gap-3">
                            <i data-lucide="file-check-2" style="width: 36px; height: 36px; color: #16865D; flex-shrink: 0;"></i>
                            <div class="overflow-hidden">
                                <strong class="d-block text-dark text-truncate small">{{ $kk->original_filename ?: 'Salinan_KK_Resmi.pdf' }}</strong>
                                <span class="text-muted small" style="font-size: 0.72rem;">
                                    Ukuran: {{ number_format($kk->file_size / 1024, 1) }} KB &bull; Sumber: {{ $kk->sumber_dokumen }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            @if($kk->isVerifiedDukcapil())
                                <a href="{{ route('arsip-kk.stream', $kk->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold small d-inline-flex align-items-center justify-content-center gap-2">
                                    <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                    <span>Buka Dokumen PDF Asli</span>
                                </a>
                                <a href="{{ route('arsip-kk.cetak', $kk->id) }}" target="_blank" class="btn btn-success rounded-pill px-4 py-2 fw-bold small d-inline-flex align-items-center justify-content-center gap-2">
                                    <i data-lucide="printer" style="width: 16px; height: 16px;"></i>
                                    <span>Cetak Salinan Dokumen Resmi</span>
                                </a>
                            @else
                                <div class="alert alert-warning border-0 rounded-3 mb-0 small">
                                    <i data-lucide="shield-alert" style="width: 16px; height: 16px;" class="me-1"></i>
                                    Dokumen ini belum berstatus Terverifikasi resmi. Tombol cetak dan download dinonaktifkan demi menjaga kepatuhan dan keabsahan hukum.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4 text-muted small">
                            <i data-lucide="file-question" style="width: 40px; height: 40px; opacity: 0.5;" class="mb-2 d-block mx-auto"></i>
                            Berkas salinan PDF belum diunggah untuk Kartu Keluarga ini.
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold" data-bs-toggle="modal" data-bs-target="#modalUploadKk">
                                    <i data-lucide="upload" style="width: 14px; height: 14px;" class="me-1"></i> Unggah Sekarang
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: TABEL ANGGOTA & AUDIT HISTORY --}}
        <div class="col-12 col-lg-7">
            <div class="d-flex flex-column gap-4">
                {{-- Card 1: Anggota Keluarga dalam KK Ini --}}
                <div class="card border-0 shadow-sm rounded-4 p-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="users" style="width: 18px; height: 18px; color: #1456A0;"></i>
                            <h6 class="fw-bold mb-0 text-dark">Daftar Anggota Keluarga ({{ $kk->anggota->count() }} Jiwa)</h6>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small text-uppercase">
                                    <th style="width: 40px;">#</th>
                                    <th>Nama Warga</th>
                                    <th>NIK</th>
                                    <th>Hubungan</th>
                                    <th>Usia</th>
                                    <th>Status Domisili</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kk->anggota as $idx => $m)
                                    <tr>
                                        <td class="small text-muted">{{ $idx + 1 }}</td>
                                        <td>
                                            <a href="{{ route('warga.show', $m->id) }}" class="fw-bold text-dark text-decoration-none small d-block">
                                                {{ $m->name }}
                                            </a>
                                            <span class="text-muted" style="font-size: 0.72rem;">{{ $m->gender }}</span>
                                        </td>
                                        <td class="font-monospace small">{{ $m->masked_nik }}</td>
                                        <td>
                                            <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #EAF3FF; color: #1456A0; font-size: 0.72rem;">
                                                {{ $m->hubungan_keluarga ?: 'Anggota' }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $m->age !== null ? $m->age . ' Thn' : '-' }}</td>
                                        <td>
                                            <span class="badge rounded-pill px-2 py-1 small fw-semibold" style="background: #E3F5EC; color: #16865D; font-size: 0.72rem;">
                                                {{ $m->status_domisili ?: 'Warga Tetap' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Card 2: Riwayat Pemeriksaan & Verifikasi Dokumen --}}
                <div class="card border-0 shadow-sm rounded-4 p-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i data-lucide="history" style="width: 18px; height: 18px; color: #1456A0;"></i>
                        <h6 class="fw-bold mb-0 text-dark">Riwayat Audit Verifikasi Dokumen Dukcapil</h6>
                    </div>

                    @if($kk->verifikasiHistories->isEmpty())
                        <p class="text-muted small text-center py-3 mb-0">Belum ada riwayat verifikasi yang tercatat pada berkas KK ini.</p>
                    @else
                        <div class="timeline position-relative ps-4" style="border-left: 2px solid #E2E8F0;">
                            @foreach($kk->verifikasiHistories as $h)
                                <div class="mb-3 position-relative">
                                    <div class="position-absolute rounded-circle" style="width: 10px; height: 10px; background: #1456A0; left: -21px; top: 4px; border: 2px solid #FFFFFF;"></div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #EAF3FF; color: #1456A0; font-size: 0.72rem;">
                                            {{ $h->status_lama ? "{$h->status_lama} → " : "" }}{{ $h->status_baru }}
                                        </span>
                                        <span class="text-muted small ms-auto">{{ $h->created_at ? $h->created_at->translatedFormat('d M Y, H:i') : '-' }} WIB</span>
                                    </div>
                                    <p class="text-muted small mb-0">{{ $h->catatan }}</p>
                                    @if($h->metode_verifikasi)
                                        <small class="text-primary d-block mt-1" style="font-size: 0.72rem;">
                                            <i data-lucide="check" style="width: 11px; height: 11px;"></i> Metode: {{ $h->metode_verifikasi }}
                                        </small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================================
     MODAL UPLOAD DOKUMEN PDF RESMI DUKCAPIL
     ====================================================================== --}}
<div class="modal fade" id="modalUploadKk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="upload" style="width: 20px; height: 20px; color: #1456A0;"></i>
                    Unggah Salinan PDF KK Resmi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('arsip-kk.upload', $kk->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">
                        Unggah berkas PDF salinan resmi Kartu Keluarga yang diterbitkan oleh Dinas Kependudukan dan Pencatatan Sipil.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Pilih Berkas PDF KK Resmi <span class="text-danger">*</span></label>
                        <input type="file" name="dokumen_kk" class="form-control" accept="application/pdf" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Hanya menerima file PDF autentik (Maksimal 10MB).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tanggal Diterbitkan Dukcapil</label>
                        <input type="date" name="tgl_diterbitkan" class="form-control" max="{{ date('Y-m-d') }}" value="{{ $kk->tgl_diterbitkan_dukcapil ? $kk->tgl_diterbitkan_dukcapil->format('Y-m-d') : '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Catatan Pengunggahan</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Tuliskan keterangan dokumen yang diserahkan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: #1456A0;">
                        Unggah Berkas PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================================================================
     MODAL VERIFIKASI RESMI DUKCAPIL (KETUA RW)
     ====================================================================== --}}
<div class="modal fade" id="modalVerifikasiDukcapil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="shield-check" style="width: 20px; height: 20px; color: #16865D;"></i>
                    Pemeriksaan & Penetapan Legalitas KK
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('arsip-kk.verifikasi', $kk->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Status Verifikasi Dokumen <span class="text-danger">*</span></label>
                        <select name="status_verifikasi_dukcapil" class="form-select" required>
                            <option value="Terverifikasi" {{ $kk->status_verifikasi_dukcapil === 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi (Sah Penuh Dicetak)</option>
                            <option value="Menunggu Verifikasi" {{ $kk->status_verifikasi_dukcapil === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="Perlu Pembaruan" {{ $kk->status_verifikasi_dukcapil === 'Perlu Pembaruan' ? 'selected' : '' }}>Perlu Pembaruan (Ada Perubahan Anggota)</option>
                            <option value="Tidak Valid" {{ $kk->status_verifikasi_dukcapil === 'Tidak Valid' ? 'selected' : '' }}>Tidak Valid (Dokumen Tidak Cocok)</option>
                            <option value="Dicabut atau Diganti" {{ $kk->status_verifikasi_dukcapil === 'Dicabut atau Diganti' ? 'selected' : '' }}>Dicabut atau Diganti</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Metode Pemeriksaan yang Digunakan <span class="text-danger">*</span></label>
                        <select name="metode_verifikasi" class="form-select" required>
                            <option value="Pemeriksaan Manual Fisik & Legalitas Dokumen Asli Dukcapil" selected>Pemeriksaan Manual Fisik & Legalitas Dokumen Asli Dukcapil</option>
                            <option value="Verifikasi Web Portal Resmi Disdukcapil Kota Bandung">Verifikasi Web Portal Resmi Disdukcapil Kota Bandung</option>
                            <option value="Verifikasi Melalui Aplikasi Identitas Kependudukan Digital (IKD)">Verifikasi Melalui Aplikasi Identitas Kependudukan Digital (IKD)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Catatan Hasil Pemeriksaan <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tuliskan catatan hasil pemeriksaan keaslian dokumen..." required>Dokumen salinan resmi telah diverifikasi fisik dan valid sesuai register kewilayahan RW 013.</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                        Simpan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
