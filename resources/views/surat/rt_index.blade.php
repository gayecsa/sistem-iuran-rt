@extends('layouts.app')

@section('title', 'Inbox Verifikasi Surat - RT ' . str_pad($cleanRt, 3, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" style="max-width: 1600px;">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i data-lucide="home" class="me-1" style="width:14px;height:14px;"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('surat.index') }}" class="text-decoration-none text-muted">Layanan Surat</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Inbox Verifikasi RT</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span>Inbox Verifikasi Surat Tingkat RT</span>
                <span class="badge bg-primary text-white rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                    RT {{ str_pad($cleanRt, 3, '0', STR_PAD_LEFT) }} / RW 013
                </span>
            </h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Daftar permohonan surat keterangan & pengantar dari warga wilayah Anda yang memerlukan pemeriksaan dan tanda tangan verifikasi awal.
            </p>
        </div>

        <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm rounded-3" style="font-size: 0.88rem;">
            <i data-lucide="list" style="width:16px;height:16px;"></i>
            <span>Lihat Semua Katalog</span>
        </a>
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

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #F59E0B !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Menunggu Verifikasi RT</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.15); color: #D97706;">
                        <i data-lucide="hourglass" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-warning mb-0">{{ $counts['pending'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Perlu tindakan Anda segera</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #16865D !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Telah Disetujui RT</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(22, 134, 93, 0.15); color: #16865D;">
                        <i data-lucide="check-circle-2" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ $counts['approved'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Diteruskan ke RW / Siap Cetak</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #D97706 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Diminta Revisi</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(217, 119, 6, 0.15); color: #D97706;">
                        <i data-lucide="edit-3" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $counts['revision'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Menunggu perbaikan warga</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #DC2626 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Ditolak RT</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(220, 38, 38, 0.12); color: #DC2626;">
                        <i data-lucide="x-circle" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-danger mb-0">{{ $counts['rejected'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Berkas tidak memenuhi kriteria</span>
            </div>
        </div>
    </div>

    {{-- TABEL INBOX PERMOHONAN RT --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #ffffff;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i data-lucide="inbox" class="text-primary" style="width:20px;height:20px;"></i>
                        <span>Daftar Berkas Permohonan Masuk</span>
                    </h5>
                    <p class="text-muted small mb-0">Klik tombol setujui untuk memberikan tanda tangan verifikasi atau minta perbaikan jika berkas kurang lengkap.</p>
                </div>

                {{-- Filter Status & Jenis Form --}}
                <form action="{{ route('rt.surat.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="status" class="form-select form-select-sm rounded-3" style="width: auto; font-size: 0.82rem;" onchange="this.form.submit()">
                        <option value="">Semua Status Berkas</option>
                        <option value="Menunggu Verifikasi RT" {{ request('status') === 'Menunggu Verifikasi RT' ? 'selected' : '' }}>Menunggu Verifikasi RT (Baru)</option>
                        <option value="Menunggu Persetujuan RW" {{ request('status') === 'Menunggu Persetujuan RW' ? 'selected' : '' }}>Sudah Diverifikasi RT (Di RW)</option>
                        <option value="Siap Dicetak" {{ request('status') === 'Siap Dicetak' ? 'selected' : '' }}>Selesai Disahkan (Siap Cetak)</option>
                        <option value="Perlu Perbaikan (RT)" {{ request('status') === 'Perlu Perbaikan (RT)' ? 'selected' : '' }}>Perlu Revisi RT</option>
                        <option value="Ditolak RT" {{ request('status') === 'Ditolak RT' ? 'selected' : '' }}>Ditolak RT</option>
                    </select>

                    <select name="jenis" class="form-select form-select-sm rounded-3" style="width: auto; font-size: 0.82rem;" onchange="this.form.submit()">
                        <option value="">Semua Jenis Surat</option>
                        @foreach(\App\Models\LetterType::orderBy('urutan')->get() as $t)
                            <option value="{{ $t->kode }}" {{ request('jenis') === $t->kode ? 'selected' : '' }}>{{ $t->nama }}</option>
                        @endforeach
                    </select>

                    @if(request()->hasAny(['status', 'jenis']))
                        <a href="{{ route('rt.surat.index') }}" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset">
                            <i data-lucide="refresh-cw" style="width:14px;height:14px;"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            @if($letters->isEmpty())
                <div class="text-center py-5 px-3">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width:70px;height:70px;">
                        <i data-lucide="check-circle" class="text-success" style="width:36px;height:36px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Tidak Ada Berkas Menunggu</h5>
                    <p class="text-muted small mx-auto mb-0" style="max-width: 420px;">
                        Seluruh permohonan surat warga RT {{ str_pad($cleanRt, 3, '0', STR_PAD_LEFT) }} telah diproses atau belum ada pengajuan baru.
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light text-secondary text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-4 py-3">No. Reg & Tanggal</th>
                                <th class="py-3">Pemohon & Alamat</th>
                                <th class="py-3">Jenis Surat & Keperluan</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="pe-4 py-3 text-end" style="min-width: 220px;">Tindakan Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($letters as $item)
                                <tr>
                                    {{-- No. Reg & Tanggal --}}
                                    <td class="ps-4">
                                        <div class="fw-bold font-monospace text-dark" style="font-size: 0.82rem;">
                                            {{ $item->nomor_registrasi }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.74rem;">
                                            <i data-lucide="calendar" style="width:11px;height:11px;" class="me-1"></i>
                                            {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                                        </div>
                                    </td>

                                    {{-- Pemohon & Alamat --}}
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->nama_lengkap }}</div>
                                        <div class="text-muted small" style="font-size: 0.75rem;">
                                            NIK: {{ $item->nik }}
                                        </div>
                                        <div class="text-secondary small" style="font-size: 0.74rem;">
                                            {{ $item->alamat_jalan }} No. {{ $item->no_rumah }}
                                        </div>
                                    </td>

                                    {{-- Jenis & Keperluan --}}
                                    <td>
                                        <div class="fw-semibold text-primary">{{ $item->judul_surat }}</div>
                                        <div class="text-dark small text-truncate" style="max-width: 260px;" title="{{ $item->keperluan }}">
                                            {{ $item->keperluan }}
                                        </div>
                                        @if(!empty($item->dokumen_lampiran))
                                            <span class="badge bg-light text-secondary border px-2 py-0 mt-1" style="font-size: 0.7rem;">
                                                <i data-lucide="paperclip" style="width:10px;height:10px;"></i> {{ count($item->dokumen_lampiran) }} Berkas Lampiran
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="text-center">
                                        @php
                                            $stClass = 'bg-secondary-subtle text-secondary';
                                            if ($item->status === 'Menunggu Verifikasi RT') $stClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                                            elseif ($item->status === 'Menunggu Persetujuan RW') $stClass = 'bg-info-subtle text-info border border-info-subtle';
                                            elseif (in_array($item->status, ['Siap Dicetak', 'Selesai'])) $stClass = 'bg-success-subtle text-success border border-success-subtle';
                                            elseif (str_contains($item->status, 'Ditolak')) $stClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                        @endphp
                                        <span class="badge {{ $stClass }} px-2 py-1 rounded-pill" style="font-size: 0.75rem; font-weight: 600;">
                                            {{ $item->status }}
                                        </span>
                                    </td>

                                    {{-- Tindakan --}}
                                    <td class="pe-4 text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('surat.show', $item->id) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1" style="font-size: 0.78rem;" title="Lihat Detail Lengkap">
                                                <i data-lucide="eye" style="width:14px;height:14px;"></i>
                                            </a>

                                            @if($item->status === 'Menunggu Verifikasi RT')
                                                {{-- Quick Approve RT --}}
                                                <form action="{{ route('rt.surat.approve', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui surat ini dan meneruskannya ke Ketua RW 013?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-3 px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                                        <i data-lucide="check" style="width:14px;height:14px;"></i>
                                                        <span>Setujui</span>
                                                    </button>
                                                </form>

                                                {{-- Modal trigger buttons for Revisi / Tolak --}}
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded-3 px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalItemRevisi-{{ $item->id }}" title="Minta Revisi">
                                                    <i data-lucide="edit-3" style="width:14px;height:14px;"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalItemTolak-{{ $item->id }}" title="Tolak">
                                                    <i data-lucide="x" style="width:14px;height:14px;"></i>
                                                </button>
                                            @elseif($item->canPrint())
                                                <a href="{{ route('surat.cetak', $item->id) }}" target="_blank" class="btn btn-sm btn-primary rounded-3 px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                                    <i data-lucide="printer" style="width:14px;height:14px;"></i>
                                                    <span>Cetak</span>
                                                </a>
                                            @endif
                                        </div>

                                        {{-- MODAL REVISI --}}
                                        <div class="modal fade text-start" id="modalItemRevisi-{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content rounded-4 border-0">
                                                    <form action="{{ route('rt.surat.revisi', $item->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Permintaan Perbaikan Berkas</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-2 text-muted small">Pemohon: <strong>{{ $item->nama_lengkap }}</strong> ({{ $item->nomor_registrasi }})</div>
                                                            <label class="form-label small fw-semibold">Rincian yang Perlu Direvisi <span class="text-danger">*</span></label>
                                                            <textarea name="notes" class="form-control" rows="3" required placeholder="Jelaskan bagian apa yang harus diperbaiki warga..."></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-warning">Kirim Permintaan Revisi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- MODAL TOLAK --}}
                                        <div class="modal fade text-start" id="modalItemTolak-{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content rounded-4 border-0">
                                                    <form action="{{ route('rt.surat.reject', $item->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold text-danger">Tolak Permohonan Surat</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-2 text-muted small">Pemohon: <strong>{{ $item->nama_lengkap }}</strong></div>
                                                            <label class="form-label small fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                                                            <textarea name="reason" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Tolak Permohonan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($letters->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $letters->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>
@endsection
