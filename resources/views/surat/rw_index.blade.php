@extends('layouts.app')

@section('title', 'Inbox Persetujuan Surat RW 013')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" style="max-width: 1600px;">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i data-lucide="home" class="me-1" style="width:14px;height:14px;"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('surat.index') }}" class="text-decoration-none text-muted">Layanan Surat</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Inbox Persetujuan RW</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span>Persetujuan & Pengesahan Surat RW 013</span>
                <span class="badge bg-primary text-white rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                    Arcamanik, Kota Bandung
                </span>
            </h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Koordinasi dan pengesahan akhir permohonan surat warga dari seluruh RT 001 s/d RT 008, penerbitan Nomor Surat Resmi, dan tanda tangan digital.
            </p>
        </div>

        <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm rounded-3" style="font-size: 0.88rem;">
            <i data-lucide="list" style="width:16px;height:16px;"></i>
            <span>Katalog Layanan Surat</span>
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

    {{-- STATS CARDS RW --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #0284C7 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Menunggu Persetujuan RW</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(2, 132, 199, 0.15); color: #0284C7;">
                        <i data-lucide="file-clock" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-info mb-0">{{ $counts['waiting_rw'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Sudah lolos verifikasi RT</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #16865D !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Disahkan & Terbit Sah</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(22, 134, 93, 0.15); color: #16865D;">
                        <i data-lucide="check-circle-2" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ $counts['approved'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Nomor surat telah diterbitkan</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #DC2626 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Ditolak</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(220, 38, 38, 0.12); color: #DC2626;">
                        <i data-lucide="x-circle" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-danger mb-0">{{ $counts['rejected'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Penolakan RT atau RW</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #1456A0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Total Arsip RW 013</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(20, 86, 160, 0.1); color: #1456A0;">
                        <i data-lucide="folder-archive" style="width:18px;height:18px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $counts['total'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">Total seluruh riwayat pengajuan</span>
            </div>
        </div>
    </div>

    {{-- TABEL INBOX PERMOHONAN RW --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #ffffff;">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i data-lucide="inbox" class="text-primary" style="width:20px;height:20px;"></i>
                        <span>Pengesahan Berkas Tingkat RW 013</span>
                    </h5>
                    <p class="text-muted small mb-0">Hanya berkas yang telah disetujui oleh Ketua RT yang dapat disahkan dan diterbitkan nomor resminya.</p>
                </div>

                {{-- Filter RT & Status Form --}}
                <form action="{{ route('rw.surat.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="rt" class="form-select form-select-sm rounded-3" style="width: auto; font-size: 0.82rem;" onchange="this.form.submit()">
                        <option value="">Semua Wilayah RT (001 - 008)</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ request('rt') == $i ? 'selected' : '' }}>RT {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>

                    <select name="status" class="form-select form-select-sm rounded-3" style="width: auto; font-size: 0.82rem;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Menunggu Persetujuan RW" {{ request('status') === 'Menunggu Persetujuan RW' ? 'selected' : '' }}>Menunggu Persetujuan RW (Siap Sahkan)</option>
                        <option value="Siap Dicetak" {{ request('status') === 'Siap Dicetak' ? 'selected' : '' }}>Sudah Disahkan (Siap Dicetak)</option>
                        <option value="Menunggu Verifikasi RT" {{ request('status') === 'Menunggu Verifikasi RT' ? 'selected' : '' }}>Masih di Tingkat RT</option>
                        <option value="Ditolak RW" {{ request('status') === 'Ditolak RW' ? 'selected' : '' }}>Ditolak RW</option>
                    </select>

                    <select name="jenis" class="form-select form-select-sm rounded-3" style="width: auto; font-size: 0.82rem;" onchange="this.form.submit()">
                        <option value="">Semua Jenis Surat</option>
                        @foreach(\App\Models\LetterType::orderBy('urutan')->get() as $t)
                            <option value="{{ $t->kode }}" {{ request('jenis') === $t->kode ? 'selected' : '' }}>{{ $t->nama }}</option>
                        @endforeach
                    </select>

                    @if(request()->hasAny(['rt', 'status', 'jenis']))
                        <a href="{{ route('rw.surat.index') }}" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset">
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
                        <i data-lucide="check-check" class="text-success" style="width:36px;height:36px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Tidak Ada Antrean Berkas Masuk</h5>
                    <p class="text-muted small mx-auto mb-0" style="max-width: 420px;">
                        Semua permohonan surat yang diajukan telah selesai diproses.
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light text-secondary text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-4 py-3">No. Reg / No. Surat</th>
                                <th class="py-3">Pemohon & RT</th>
                                <th class="py-3">Jenis Surat & Keperluan</th>
                                <th class="py-3">Verifikasi RT</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="pe-4 py-3 text-end" style="min-width: 220px;">Aksi Pengesahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($letters as $item)
                                <tr>
                                    {{-- No. Reg & No Surat --}}
                                    <td class="ps-4">
                                        <div class="fw-bold font-monospace text-dark" style="font-size: 0.82rem;">
                                            {{ $item->nomor_registrasi }}
                                        </div>
                                        @if($item->nomor_surat)
                                            <div class="text-primary font-monospace fw-bold" style="font-size: 0.75rem;">
                                                {{ $item->nomor_surat }}
                                            </div>
                                        @else
                                            <div class="text-muted" style="font-size: 0.74rem;">
                                                <i data-lucide="calendar" style="width:11px;height:11px;" class="me-1"></i>
                                                {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Pemohon & RT --}}
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->nama_lengkap }}</div>
                                        <div class="text-muted small" style="font-size: 0.74rem;">
                                            <span class="badge bg-light text-secondary border">RT {{ $item->rt }}</span>
                                            NIK: {{ substr($item->nik, 0, 8) }}****
                                        </div>
                                    </td>

                                    {{-- Jenis & Keperluan --}}
                                    <td>
                                        <div class="fw-semibold text-primary">{{ $item->judul_surat }}</div>
                                        <div class="text-dark small text-truncate" style="max-width: 240px;" title="{{ $item->keperluan }}">
                                            {{ $item->keperluan }}
                                        </div>
                                    </td>

                                    {{-- Verifikasi RT --}}
                                    <td>
                                        @if($item->rt_approved_at)
                                            <div class="text-success small fw-semibold">
                                                <i data-lucide="check-circle" style="width:13px;height:13px;"></i> Diverifikasi
                                            </div>
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                {{ $item->rt_approved_name }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                {{ $item->rt_approved_at->translatedFormat('d/m/Y H:i') }}
                                            </div>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.72rem;">
                                                Belum Disetujui RT
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="text-center">
                                        @php
                                            $stClass = 'bg-secondary-subtle text-secondary';
                                            if ($item->status === 'Menunggu Persetujuan RW') $stClass = 'bg-info-subtle text-info border border-info-subtle';
                                            elseif (in_array($item->status, ['Siap Dicetak', 'Selesai'])) $stClass = 'bg-success-subtle text-success border border-success-subtle';
                                            elseif (str_contains($item->status, 'Ditolak')) $stClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                            elseif ($item->status === 'Menunggu Verifikasi RT') $stClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                                        @endphp
                                        <span class="badge {{ $stClass }} px-2 py-1 rounded-pill" style="font-size: 0.75rem; font-weight: 600;">
                                            {{ $item->status }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="pe-4 text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('surat.show', $item->id) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1" style="font-size: 0.78rem;" title="Lihat Detail Lengkap">
                                                <i data-lucide="eye" style="width:14px;height:14px;"></i>
                                            </a>

                                            @if($item->status === 'Menunggu Persetujuan RW')
                                                {{-- Sahkan & Terbitkan Nomor Surat --}}
                                                <form action="{{ route('rw.surat.approve', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menyahkan dokumen ini dan menerbitkan Nomor Surat Resmi RW 013?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-2 py-1 d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.78rem; background: #1456A0; border-color: #1456A0;">
                                                        <i data-lucide="file-check" style="width:14px;height:14px;"></i>
                                                        <span>Sahkan & Beri No.</span>
                                                    </button>
                                                </form>

                                                {{-- Modal Revisi / Tolak RW --}}
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded-3 px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalRwItemRevisi-{{ $item->id }}" title="Minta Revisi">
                                                    <i data-lucide="edit-3" style="width:14px;height:14px;"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalRwItemTolak-{{ $item->id }}" title="Tolak">
                                                    <i data-lucide="x" style="width:14px;height:14px;"></i>
                                                </button>
                                            @elseif($item->canPrint())
                                                <a href="{{ route('surat.cetak', $item->id) }}" target="_blank" class="btn btn-sm btn-success rounded-3 px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                                    <i data-lucide="printer" style="width:14px;height:14px;"></i>
                                                    <span>Cetak Dokumen</span>
                                                </a>
                                            @endif
                                        </div>

                                        {{-- MODAL REVISI RW --}}
                                        <div class="modal fade text-start" id="modalRwItemRevisi-{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content rounded-4 border-0">
                                                    <form action="{{ route('rw.surat.revisi', $item->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Permintaan Revisi Pengesahan RW</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-2 text-muted small">Pemohon: <strong>{{ $item->nama_lengkap }}</strong> (RT {{ $item->rt }})</div>
                                                            <label class="form-label small fw-semibold">Catatan Perbaikan <span class="text-danger">*</span></label>
                                                            <textarea name="notes" class="form-control" rows="3" required placeholder="Tuliskan catatan perbaikan untuk pemohon/RT..."></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-warning">Kirim Revisi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- MODAL TOLAK RW --}}
                                        <div class="modal fade text-start" id="modalRwItemTolak-{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content rounded-4 border-0">
                                                    <form action="{{ route('rw.surat.reject', $item->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold text-danger">Tolak Permohonan Surat</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-2 text-muted small">Pemohon: <strong>{{ $item->nama_lengkap }}</strong></div>
                                                            <label class="form-label small fw-semibold">Alasan Penolakan Resmi RW 013 <span class="text-danger">*</span></label>
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
