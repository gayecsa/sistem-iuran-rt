@extends('layouts.app')

@section('title', 'Detail Permohonan Surat - ' . $letter->nomor_registrasi)

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" style="max-width: 1400px;">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i data-lucide="home" class="me-1" style="width:14px;height:14px;"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('surat.index') }}" class="text-decoration-none text-muted">Layanan Surat</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Detail & Tracking</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span>{{ $letter->judul_surat }}</span>
                <span class="badge bg-secondary-subtle text-secondary font-monospace rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                    {{ $letter->nomor_registrasi }}
                </span>
            </h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Wilayah RT {{ $letter->rt }} / RW {{ $letter->rw }}, Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm rounded-3" style="font-size: 0.88rem;">
                <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
                <span>Kembali</span>
            </a>

            @if($letter->canPrint())
                <a href="{{ route('surat.cetak', $letter->id) }}" target="_blank" class="btn text-white d-inline-flex align-items-center gap-2 shadow-sm rounded-3" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%); font-weight: 600; font-size: 0.88rem;">
                    <i data-lucide="printer" style="width:18px;height:18px;"></i>
                    <span>Cetak Dokumen Resmi A4</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Alert Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center gap-3 p-3 mb-4">
            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;flex-shrink:0;">
                <i data-lucide="check" style="width:20px;height:20px;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold text-success">Berhasil!</div>
                <div class="text-secondary small">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center gap-3 p-3 mb-4">
            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;flex-shrink:0;">
                <i data-lucide="alert-triangle" style="width:20px;height:20px;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold text-danger">Perhatian</div>
                <div class="text-secondary small">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- STATUS HEADER BANNER --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: #ffffff; border-left: 5px solid {{ $letter->canPrint() ? '#16865D' : (str_contains($letter->status, 'Ditolak') ? '#DC2626' : '#1456A0') }} !important;">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Status Terkini Dokumen:</span>
                <div class="d-flex align-items-center gap-2 mt-1">
                    @php
                        $badgeBg = 'bg-secondary-subtle text-secondary';
                        $iconStatus = 'clock';
                        if ($letter->status === 'Menunggu Verifikasi RT') {
                            $badgeBg = 'bg-warning-subtle text-warning border border-warning-subtle';
                            $iconStatus = 'hourglass';
                        } elseif ($letter->status === 'Menunggu Persetujuan RW') {
                            $badgeBg = 'bg-info-subtle text-info border border-info-subtle';
                            $iconStatus = 'file-clock';
                        } elseif (in_array($letter->status, ['Siap Dicetak', 'Selesai'])) {
                            $badgeBg = 'bg-success-subtle text-success border border-success-subtle';
                            $iconStatus = 'check-circle-2';
                        } elseif (str_contains($letter->status, 'Ditolak')) {
                            $badgeBg = 'bg-danger-subtle text-danger border border-danger-subtle';
                            $iconStatus = 'x-circle';
                        }
                    @endphp
                    <span class="badge {{ $badgeBg }} px-3 py-2 rounded-pill fs-6 d-inline-flex align-items-center gap-2">
                        <i data-lucide="{{ $iconStatus }}" style="width:16px;height:16px;"></i>
                        <span>{{ $letter->status }}</span>
                    </span>

                    @if($letter->nomor_surat)
                        <span class="badge bg-primary text-white font-monospace px-3 py-2 rounded-pill fs-6">
                            No: {{ $letter->nomor_surat }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="text-md-end">
                <div class="text-muted small">Kode Verifikasi QR Code:</div>
                <div class="fw-bold font-monospace text-primary" style="letter-spacing: 1px;">
                    {{ $letter->qr_verification_code }}
                </div>
                <a href="{{ route('surat.verifikasi', $letter->qr_verification_code) }}" target="_blank" class="small text-decoration-none d-inline-flex align-items-center gap-1 mt-1">
                    <i data-lucide="qr-code" style="width:13px;height:13px;"></i>
                    <span>Tinjau Halaman Validasi Publik</span>
                </a>
            </div>
        </div>
    </div>

    {{-- STEPPER TIMELINE PROGRES VERIFIKASI --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: #ffffff;">
        <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
            <i data-lucide="milestone" class="text-primary" style="width:20px;height:20px;"></i>
            <span>Timeline Persetujuan Berjenjang</span>
        </h5>

        <div class="row g-3">
            {{-- Stage 1: Pengajuan --}}
            <div class="col-12 col-md-3">
                <div class="p-3 rounded-3 border h-100" style="background: #F8FAFC; border-color: #10B981 !important; border-left: 4px solid #10B981 !important;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:0.75rem;">
                            <i data-lucide="check" style="width:14px;height:14px;"></i>
                        </span>
                        <span class="fw-bold text-dark small">1. Pengajuan Warga</span>
                    </div>
                    <div class="text-muted small mb-1">{{ $letter->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                    <div class="text-secondary" style="font-size: 0.78rem;">Diajukan oleh: <strong>{{ $letter->nama_lengkap }}</strong></div>
                </div>
            </div>

            {{-- Stage 2: Verifikasi RT --}}
            <div class="col-12 col-md-3">
                @php
                    $isRtDone = !is_null($letter->rt_approved_at);
                    $isRtRejected = $letter->status === 'Ditolak RT';
                    $borderColor = $isRtDone ? '#10B981' : ($isRtRejected ? '#EF4444' : '#F59E0B');
                    $bgLight = $isRtDone ? '#F8FAFC' : ($isRtRejected ? '#FEF2F2' : '#FFFBEB');
                @endphp
                <div class="p-3 rounded-3 border h-100" style="background: {{ $bgLight }}; border-color: {{ $borderColor }} !important; border-left: 4px solid {{ $borderColor }} !important;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($isRtDone)
                            <span class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:0.75rem;">
                                <i data-lucide="check" style="width:14px;height:14px;"></i>
                            </span>
                        @elseif($isRtRejected)
                            <span class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:0.75rem;">
                                <i data-lucide="x" style="width:14px;height:14px;"></i>
                            </span>
                        @else
                            <span class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:0.75rem;">
                                <i data-lucide="hourglass" style="width:14px;height:14px;"></i>
                            </span>
                        @endif
                        <span class="fw-bold text-dark small">2. Verifikasi Ketua RT</span>
                    </div>
                    @if($isRtDone)
                        <div class="text-muted small mb-1">{{ $letter->rt_approved_at->translatedFormat('d M Y, H:i') }} WIB</div>
                        <div class="text-secondary" style="font-size: 0.78rem;">Oleh: <strong>{{ $letter->rt_approved_name }}</strong></div>
                    @elseif($isRtRejected)
                        <div class="text-danger small fw-semibold">Ditolak oleh Ketua RT</div>
                    @else
                        <div class="text-warning small fw-semibold">Sedang ditinjau oleh RT {{ $letter->rt }}</div>
                    @endif
                </div>
            </div>

            {{-- Stage 3: Persetujuan RW --}}
            <div class="col-12 col-md-3">
                @php
                    $isRwDone = !is_null($letter->rw_approved_at);
                    $isRwRejected = $letter->status === 'Ditolak RW';
                    $borderRw = $isRwDone ? '#10B981' : ($isRwRejected ? '#EF4444' : ($isRtDone ? '#0284C7' : '#E2E8F0'));
                    $bgRw = $isRwDone ? '#F8FAFC' : ($isRwRejected ? '#FEF2F2' : ($isRtDone ? '#F0F9FF' : '#FAFAFA'));
                @endphp
                <div class="p-3 rounded-3 border h-100" style="background: {{ $bgRw }}; border-color: {{ $borderRw }} !important; border-left: 4px solid {{ $borderRw }} !important;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($isRwDone)
                            <span class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:0.75rem;">
                                <i data-lucide="check" style="width:14px;height:14px;"></i>
                            </span>
                        @elseif($isRwRejected)
                            <span class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:0.75rem;">
                                <i data-lucide="x" style="width:14px;height:14px;"></i>
                            </span>
                        @elseif($isRtDone)
                            <span class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:0.75rem;">
                                <i data-lucide="hourglass" style="width:14px;height:14px;"></i>
                            </span>
                        @else
                            <span class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:0.75rem;">
                                3
                            </span>
                        @endif
                        <span class="fw-bold text-dark small">3. Pengesahan Ketua RW</span>
                    </div>
                    @if($isRwDone)
                        <div class="text-muted small mb-1">{{ $letter->rw_approved_at->translatedFormat('d M Y, H:i') }} WIB</div>
                        <div class="text-secondary" style="font-size: 0.78rem;">Oleh: <strong>{{ $letter->rw_approved_name }}</strong></div>
                    @elseif($isRwRejected)
                        <div class="text-danger small fw-semibold">Ditolak oleh Ketua RW 013</div>
                    @elseif($isRtDone)
                        <div class="text-info small fw-semibold">Menunggu persetujuan RW 013</div>
                    @else
                        <div class="text-muted small">Menunggu tahap 2 selesai</div>
                    @endif
                </div>
            </div>

            {{-- Stage 4: Surat Siap Dicetak --}}
            <div class="col-12 col-md-3">
                @php
                    $isReady = $letter->canPrint();
                    $borderFinal = $isReady ? '#10B981' : '#E2E8F0';
                    $bgFinal = $isReady ? '#ECFDF5' : '#FAFAFA';
                @endphp
                <div class="p-3 rounded-3 border h-100" style="background: {{ $bgFinal }}; border-color: {{ $borderFinal }} !important; border-left: 4px solid {{ $borderFinal }} !important;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($isReady)
                            <span class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:0.75rem;">
                                <i data-lucide="check" style="width:14px;height:14px;"></i>
                            </span>
                        @else
                            <span class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:0.75rem;">
                                4
                            </span>
                        @endif
                        <span class="fw-bold text-dark small">4. Dokumen Terbit</span>
                    </div>
                    @if($isReady)
                        <div class="text-success small fw-bold mb-1">Siap Dicetak & Sah</div>
                        <div class="text-secondary" style="font-size: 0.78rem;">Nomor & stempel resmi aktif</div>
                    @else
                        <div class="text-muted small">Terkunci hingga tahap 3</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- SPLIT CONTENT: RINCIAN & AUDIT TRAIL --}}
    <div class="row g-4">
        {{-- KOLOM KIRI (70%): RINCIAN DATA PERMOHONAN --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: #ffffff;">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-3 d-flex align-items-center gap-2">
                    <i data-lucide="file-text" class="text-primary" style="width:20px;height:20px;"></i>
                    <span>Rincian Berkas Permohonan Surat</span>
                </h5>

                {{-- Sub-section: Biodata Pemohon --}}
                <div class="mb-4">
                    <h6 class="fw-bold text-secondary text-uppercase small mb-3" style="letter-spacing: 0.5px;">A. Identitas Kependudukan Pemohon</h6>
                    <div class="row g-3" style="font-size: 0.88rem;">
                        <div class="col-6 col-md-4">
                            <span class="text-muted small">NIK:</span>
                            <div class="fw-bold text-dark font-monospace">{{ $letter->nik }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <span class="text-muted small">Nomor KK:</span>
                            <div class="fw-bold text-dark font-monospace">{{ $letter->no_kk }}</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <span class="text-muted small">Nama Lengkap:</span>
                            <div class="fw-bold text-dark">{{ $letter->nama_lengkap }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <span class="text-muted small">Tempat, Tgl Lahir:</span>
                            <div class="fw-semibold text-dark">{{ $letter->tempat_lahir }}, {{ $letter->tanggal_lahir ? $letter->tanggal_lahir->translatedFormat('d F Y') : '-' }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <span class="text-muted small">Jenis Kelamin:</span>
                            <div class="fw-semibold text-dark">{{ $letter->jenis_kelamin }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <span class="text-muted small">Agama:</span>
                            <div class="fw-semibold text-dark">{{ $letter->agama }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <span class="text-muted small">Status Perkawinan:</span>
                            <div class="fw-semibold text-dark">{{ $letter->status_perkawinan }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <span class="text-muted small">Pekerjaan:</span>
                            <div class="fw-semibold text-dark">{{ $letter->pekerjaan }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <span class="text-muted small">Pendidikan Terakhir:</span>
                            <div class="fw-semibold text-dark">{{ $letter->pendidikan_terakhir }}</div>
                        </div>
                    </div>
                </div>

                {{-- Sub-section: Alamat Domisili --}}
                <div class="mb-4 border-top pt-3">
                    <h6 class="fw-bold text-secondary text-uppercase small mb-3" style="letter-spacing: 0.5px;">B. Alamat Domisili Resmi</h6>
                    <div class="p-3 rounded-3" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 0.88rem;">
                        <div class="fw-bold text-dark mb-1">
                            {{ $letter->alamat_jalan }} No. {{ $letter->no_rumah }}
                        </div>
                        <div class="text-secondary">
                            Rukun Tetangga {{ $letter->rt }} / Rukun Warga {{ $letter->rw }}, Kelurahan {{ $letter->kelurahan }}, Kecamatan {{ $letter->kecamatan }}
                        </div>
                        <div class="text-muted small mt-1">
                            {{ $letter->kota }}, Provinsi {{ $letter->provinsi }} - {{ $letter->kode_pos }}
                        </div>
                    </div>
                </div>

                {{-- Sub-section: Keperluan & Atribut Khusus --}}
                <div class="mb-4 border-top pt-3">
                    <h6 class="fw-bold text-secondary text-uppercase small mb-3" style="letter-spacing: 0.5px;">C. Keperluan & Keterangan Khusus</h6>
                    <div class="p-3 rounded-3 bg-light mb-3" style="font-size: 0.88rem;">
                        <span class="text-muted small fw-semibold">Keperluan Pembuatan:</span>
                        <div class="text-dark fw-semibold mt-1">{{ $letter->keperluan }}</div>
                    </div>

                    @if(!empty($letter->detail_khusus))
                        <div class="p-3 rounded-3 border" style="background: #FAFAFA; font-size: 0.85rem;">
                            <span class="text-muted small fw-semibold">Atribut Tambahan Jenis Surat ({{ strtoupper($letter->jenis_surat) }}):</span>
                            <div class="row g-2 mt-1">
                                @foreach($letter->detail_khusus as $k => $v)
                                    <div class="col-12 col-md-6">
                                        <span class="text-muted">{{ ucwords(str_replace('_', ' ', $k)) }}:</span>
                                        <span class="fw-semibold text-dark">{{ is_array($v) ? json_encode($v) : $v }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($letter->keterangan_tambahan)
                        <div class="mt-3" style="font-size: 0.85rem;">
                            <span class="text-muted small">Catatan Tambahan Pemohon:</span>
                            <div class="text-secondary fst-italic">{{ $letter->keterangan_tambahan }}</div>
                        </div>
                    @endif
                </div>

                {{-- Sub-section: Dokumen Lampiran --}}
                <div class="border-top pt-3">
                    <h6 class="fw-bold text-secondary text-uppercase small mb-3" style="letter-spacing: 0.5px;">D. Dokumen Berkas Lampiran</h6>
                    @if(empty($letter->dokumen_lampiran))
                        <div class="text-muted small fst-italic">Tidak ada dokumen berkas tambahan yang diunggah.</div>
                    @else
                        <div class="row g-2">
                            @foreach($letter->dokumen_lampiran as $doc)
                                <div class="col-12 col-md-6">
                                    <div class="border rounded-3 p-3 d-flex align-items-center justify-content-between" style="background: #ffffff;">
                                        <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                            <i data-lucide="file-check" class="text-primary" style="width:18px;height:18px;flex-shrink:0;"></i>
                                            <div class="text-truncate">
                                                <div class="fw-bold small text-dark text-truncate">{{ $doc['nama'] }}</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">{{ $doc['ukuran'] ?? 'Berkas Terlampir' }}</div>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $doc['file']) }}" target="_blank" class="btn btn-sm btn-outline-primary px-2 py-1 rounded" style="font-size: 0.75rem;">
                                            <i data-lucide="external-link" style="width:12px;height:12px;"></i> Buka
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (30%): TINDAKAN PEJABAT & AUDIT TRAIL --}}
        <div class="col-12 col-lg-4">

            {{-- ACTION BOX UNTUK KETUA RT (JIKA STATUS MENUNGGU RT) --}}
            @if(Auth::user()->isRt() && $letter->status === 'Menunggu Verifikasi RT')
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-white" style="background: linear-gradient(135deg, #1E3A8A 0%, #172554 100%);">
                    <h6 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <i data-lucide="shield" style="width:18px;height:18px; color: #60A5FA;"></i>
                        <span>Tindakan Verifikasi Ketua RT</span>
                    </h6>
                    <p class="small text-white text-opacity-80 mb-3">
                        Sebagai Ketua {{ Auth::user()->clean_rt_label }}, silakan periksa kelengkapan data warga di atas dan tentukan tindakan persetujuan.
                    </p>

                    <form action="{{ route('rt.surat.approve', $letter->id) }}" method="POST" class="mb-2">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="notes" class="form-control form-control-sm text-dark" placeholder="Catatan verifikasi RT (opsional)...">
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2 shadow-sm rounded-3">
                            <i data-lucide="check-circle" style="width:16px;height:16px;"></i>
                            <span>Setujui & Teruskan ke RW</span>
                        </button>
                    </form>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-light btn-sm w-50" data-bs-toggle="modal" data-bs-target="#modalRtRevisi">
                            <i data-lucide="edit-3" style="width:14px;height:14px;" class="me-1"></i> Minta Revisi
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm w-50 bg-white text-danger border-0" data-bs-toggle="modal" data-bs-target="#modalRtTolak">
                            <i data-lucide="x" style="width:14px;height:14px;" class="me-1"></i> Tolak
                        </button>
                    </div>
                </div>

                {{-- Modal Revisi RT --}}
                <div class="modal fade" id="modalRtRevisi" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content rounded-4 border-0">
                            <form action="{{ route('rt.surat.revisi', $letter->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Minta Perbaikan / Revisi Berkas</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label small fw-semibold">Rincian yang Perlu Diperbaiki Pemohon <span class="text-danger">*</span></label>
                                    <textarea name="notes" class="form-control" rows="4" required placeholder="Contoh: Lampirkan scan KTP asli yang lebih jelas dan cantumkan nomor rumah lengkap..."></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-warning">Kirim Permintaan Revisi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Modal Tolak RT --}}
                <div class="modal fade" id="modalRtTolak" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content rounded-4 border-0">
                            <form action="{{ route('rt.surat.reject', $letter->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold text-danger">Tolak Permohonan Surat</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label small fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control" rows="4" required placeholder="Jelaskan alasan penolakan secara spesifik..."></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Tolak Permohonan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ACTION BOX UNTUK KETUA RW (JIKA STATUS MENUNGGU RW) --}}
            @if(Auth::user()->isRw() && $letter->status === 'Menunggu Persetujuan RW')
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-white" style="background: linear-gradient(135deg, #123766 0%, #0F2D54 100%);">
                    <h6 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <i data-lucide="award" style="width:18px;height:18px; color: #38BDF8;"></i>
                        <span>Pengesahan Akhir Ketua RW 013</span>
                    </h6>
                    <p class="small text-white text-opacity-80 mb-3">
                        Ketua RT {{ $letter->rt }} telah menyetujui berkas ini. Pengesahan Anda akan menerbitkan Nomor Surat Resmi dan mengaktifkan izin cetak.
                    </p>

                    <form action="{{ route('rw.surat.approve', $letter->id) }}" method="POST" class="mb-2">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="notes" class="form-control form-control-sm text-dark" placeholder="Catatan pengesahan RW (opsional)...">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2 shadow-sm rounded-3" style="background: #2563EB;">
                            <i data-lucide="check-check" style="width:16px;height:16px;"></i>
                            <span>Sahkan & Terbitkan Nomor Surat</span>
                        </button>
                    </form>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-light btn-sm w-50" data-bs-toggle="modal" data-bs-target="#modalRwRevisi">
                            <i data-lucide="edit-3" style="width:14px;height:14px;" class="me-1"></i> Revisi
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm w-50 bg-white text-danger border-0" data-bs-toggle="modal" data-bs-target="#modalRwTolak">
                            <i data-lucide="x" style="width:14px;height:14px;" class="me-1"></i> Tolak
                        </button>
                    </div>
                </div>

                {{-- Modal Revisi RW --}}
                <div class="modal fade" id="modalRwRevisi" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content rounded-4 border-0">
                            <form action="{{ route('rw.surat.revisi', $letter->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Revisi Pengesahan RW</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label small fw-semibold">Catatan Revisi dari RW 013 <span class="text-danger">*</span></label>
                                    <textarea name="notes" class="form-control" rows="4" required placeholder="Catatan perbaikan berkas untuk diteruskan ke pemohon/RT..."></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-warning">Kirim Revisi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Modal Tolak RW --}}
                <div class="modal fade" id="modalRwTolak" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content rounded-4 border-0">
                            <form action="{{ route('rw.surat.reject', $letter->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold text-danger">Tolak Permohonan Surat (RW)</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label small fw-semibold">Alasan Penolakan Resmi RW 013 <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control" rows="4" required placeholder="Alasan penolakan pengesahan dokumen..."></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Tolak Permohonan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- AUDIT TRAIL LOG --}}
            <div class="card border-0 shadow-sm rounded-4 p-4" style="background: #ffffff;">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i data-lucide="scroll" class="text-secondary" style="width:18px;height:18px;"></i>
                    <span>Log Aktivitas & Audit Trail</span>
                </h6>

                @if($letter->histories->isEmpty())
                    <div class="text-muted small fst-italic">Belum ada riwayat perubahan status.</div>
                @else
                    <div class="timeline-log">
                        @foreach($letter->histories as $log)
                            <div class="timeline-item mb-3 pb-3 border-bottom position-relative ps-4" style="font-size: 0.82rem;">
                                <div class="timeline-dot position-absolute start-0 top-0 mt-1 rounded-circle bg-primary" style="width:10px;height:10px;"></div>
                                <div class="d-flex justify-content-between text-muted" style="font-size: 0.74rem;">
                                    <span>{{ $log->actor_role ?? 'Pengguna' }}</span>
                                    <span>{{ $log->created_at->translatedFormat('d M, H:i') }}</span>
                                </div>
                                <div class="fw-bold text-dark mt-1">{{ $log->status_baru }}</div>
                                <div class="text-secondary mt-1" style="line-height: 1.35;">{{ $log->catatan }}</div>
                                <div class="text-muted mt-1" style="font-size: 0.7rem;">Oleh: {{ $log->actor_name }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>
@endsection
