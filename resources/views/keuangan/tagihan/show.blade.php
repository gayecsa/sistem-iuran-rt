@extends('layouts.app')

@section('title', 'Detail Tagihan - ' . $tagihan->nomor_tagihan)

@section('content')
<div class="container py-3" style="max-width: 1000px;">

    {{-- Breadcrumb Navigasi --}}
    <div class="d-flex align-items-center gap-2 small text-muted mb-3">
        <a href="{{ route('keuangan.dashboard') }}" class="text-decoration-none text-muted">Dashboard Keuangan</a>
        <span>/</span>
        <a href="{{ route('tagihan.index') }}" class="text-decoration-none text-muted">Manajemen Tagihan</a>
        <span>/</span>
        <span class="text-dark fw-bold font-monospace">{{ $tagihan->nomor_tagihan }}</span>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4" role="alert" style="background-color: #ECFDF5; color: #065F46; border-left: 4px solid #10B981 !important;">
            <i data-lucide="check-circle-2" style="width: 22px; height: 22px; color: #10B981;" class="flex-shrink-0"></i>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4" role="alert" style="background-color: #FEF2F2; color: #991B1B; border-left: 4px solid #EF4444 !important;">
            <i data-lucide="alert-triangle" style="width: 22px; height: 22px; color: #EF4444;" class="flex-shrink-0"></i>
            <div>
                <strong>Perhatian:</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ======================================================================
         LEMBAR INVOICE INDUK RESMI HAVEN
         ====================================================================== --}}
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4" style="background: #FFFFFF;">
        {{-- Header Lembar Tagihan --}}
        <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1 font-monospace fw-bold" style="font-size: 0.78rem;">
                        {{ $tagihan->nomor_tagihan }}
                    </span>
                    <span class="badge bg-white text-secondary border rounded-pill px-3 py-1 fw-bold" style="font-size: 0.78rem;">
                        Periode {{ \Carbon\Carbon::parse($tagihan->periode . '-01')->translatedFormat('F Y') }}
                    </span>
                </div>
                <h4 class="fw-bold text-dark mb-0">Lembar Tagihan Iuran Warga Terpadu</h4>
                <small class="text-muted">Rukun Warga 013 Kelurahan Suryatmajan, Kecamatan Danurejan Kota Yogyakarta</small>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if($tagihan->status === 'Lunas')
                    <a href="{{ route('tagihan.kuitansi', $tagihan->id) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                        <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak Kuitansi
                    </a>
                @endif
                <a href="{{ route('tagihan.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Kembali
                </a>
            </div>
        </div>

        <div class="p-4">
            {{-- Bagian Identitas KK & Status Tagihan --}}
            <div class="row g-4 mb-4">
                <div class="col-md-7">
                    <h6 class="text-secondary small fw-bold text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        Identitas Kepala Keluarga & Rumah Tangga
                    </h6>
                    <div class="p-3 rounded-4 border bg-light">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Nama Kepala Keluarga:</span>
                                <strong class="text-dark fs-6">{{ $tagihan->kartuKeluarga->nama_kepala_keluarga ?? ($tagihan->kepalaKeluarga->name ?? 'Warga') }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Nomor Kartu Keluarga (KK):</span>
                                <span class="font-monospace fw-bold text-secondary">{{ $tagihan->kartuKeluarga->no_kk ?? '-' }}</span>
                            </div>
                            <div class="col-sm-6 mt-2">
                                <span class="text-muted small d-block">Wilayah Domisili:</span>
                                <span class="badge bg-primary text-white rounded-pill px-2 py-0.5">RT {{ $tagihan->clean_rt }} / RW 013</span>
                            </div>
                            <div class="col-sm-6 mt-2">
                                <span class="text-muted small d-block">Tanggungan / Jiwa:</span>
                                <span class="text-dark fw-semibold">{{ $tagihan->kartuKeluarga->anggota ? $tagihan->kartuKeluarga->anggota->count() : 1 }} Jiwa Terdaftar</span>
                            </div>
                            <div class="col-12 mt-2">
                                <span class="text-muted small d-block">Alamat Tempat Tinggal:</span>
                                <span class="text-dark small">{{ $tagihan->kartuKeluarga->alamat ?? 'Jl. Pacantel No. 12, Suryatmajan' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status & Ringkasan Pembayaran --}}
                <div class="col-md-5">
                    <h6 class="text-secondary small fw-bold text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        Status & Kewajiban Finansial
                    </h6>
                    <div class="p-3 rounded-4 border h-100 d-flex flex-column justify-content-between" style="background: #F8FAFC;">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Status Tagihan:</span>
                                @if($tagihan->status === 'Lunas')
                                    @if(isset($allComponentsAllocated) && !$allComponentsAllocated)
                                        <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">
                                            Menunggu Alokasi Komponen
                                        </span>
                                    @else
                                        <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #DCFCE7; color: #166534; border: 1px solid #86EFAC;">
                                            <i data-lucide="check-circle-2" style="width: 12px; height: 12px; vertical-align: middle;"></i> Lunas Sepenuhnya
                                        </span>
                                    @endif
                                @elseif($tagihan->status === 'Dibayar Sebagian')
                                    <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD;">
                                        Dibayar Sebagian
                                    </span>
                                @elseif($tagihan->status === 'Menunggu Konfirmasi')
                                    <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">
                                        Menunggu Verifikasi
                                    </span>
                                @else
                                    <span class="badge px-2.5 py-1 rounded-pill fw-bold" style="background-color: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;">
                                        Belum Lunas
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Total Tagihan Gabungan:</span>
                                <strong class="text-dark">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Jumlah Sudah Terbayar:</span>
                                <strong class="text-success">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                <span class="fw-bold text-dark">Sisa Tagihan:</span>
                                <strong class="fs-5 {{ $tagihan->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                    Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>

                        {{-- Tombol Tindakan Pembayaran --}}
                        @if($tagihan->sisa_tagihan > 0 && ($isRw || $isRt))
                            <div class="mt-3 pt-2 border-top d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold flex-grow-1" data-bs-toggle="modal" data-bs-target="#modalInputBayar">
                                    <i data-lucide="credit-card" style="width: 14px; height: 14px;"></i> Catat Pembayaran
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalDigitalPay">
                                    <i data-lucide="qr-code" style="width: 14px; height: 14px;"></i> QRIS / Digital
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ======================================================================
                 TABEL 1: RINCIAN KOMPONEN IURAN (ITEMS BREAKDOWN)
                 ====================================================================== --}}
            <h6 class="text-secondary small fw-bold text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                Rincian Komponen Tagihan & Target Rekening Kas
            </h6>
            <div class="table-responsive rounded-4 border overflow-hidden mb-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold" style="width: 40px;">No</th>
                            <th class="text-secondary small fw-bold">Komponen Iuran</th>
                            <th class="text-secondary small fw-bold">Pemilik Dana</th>
                            <th class="text-secondary small fw-bold">Rekening Kas Tujuan</th>
                            <th class="text-secondary small fw-bold text-end">Tarif Satuan</th>
                            <th class="text-secondary small fw-bold text-center">Qty</th>
                            <th class="text-secondary small fw-bold text-end">Subtotal</th>
                            <th class="text-secondary small fw-bold text-end">Terbayar</th>
                            <th class="text-secondary small fw-bold text-end">Sisa</th>
                            <th class="text-secondary small fw-bold text-center pe-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihan->items as $idx => $item)
                            <tr>
                                <td class="ps-3 text-center text-muted small">{{ $idx + 1 }}</td>
                                <td>
                                    <strong class="text-dark d-block">{{ $item->nama_iuran }}</strong>
                                    <small class="text-muted" style="font-size: 0.72rem;">{{ $item->metode_label ?? 'Per KK' }}</small>
                                </td>
                                <td>
                                    @php
                                        $pDana = strtoupper($item->pemilik_dana ?: 'RW');
                                    @endphp
                                    <span class="badge px-2 py-0.5 rounded-pill" style="font-size: 0.7rem; background: #F1F5F9; color: #334155; border: 1px solid #CBD5E1;">
                                        {{ $pDana }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark small fw-semibold">
                                        {{ $item->bukuKas->nama_kas ?? 'Rekening Induk ' . $pDana }}
                                    </span>
                                </td>
                                <td class="text-end small">Rp {{ number_format($item->tarif_satuan, 0, ',', '.') }}</td>
                                <td class="text-center small">{{ $item->kuantitas }}</td>
                                <td class="text-end"><strong>Rp {{ number_format($item->nominal_akhir, 0, ',', '.') }}</strong></td>
                                <td class="text-end text-success fw-bold">Rp {{ number_format($item->nominal_terbayar, 0, ',', '.') }}</td>
                                <td class="text-end {{ $item->sisa_item > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                    Rp {{ number_format($item->sisa_item, 0, ',', '.') }}
                                </td>
                                <td class="text-center pe-3">
                                    @if($item->status_pembayaran === 'lunas')
                                        <span class="badge bg-success-subtle text-success px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                            Lunas
                                        </span>
                                    @elseif($item->nominal_terbayar > 0)
                                        <span class="badge bg-primary-subtle text-primary px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                            Sebagian
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                            Belum
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-3">Tidak ada item rincian komponen tagihan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="6" class="text-end ps-3">TOTAL KEWAJIBAN BULANAN:</td>
                            <td class="text-end">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</td>
                            <td class="text-end text-success">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</td>
                            <td class="text-end text-danger">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- ======================================================================
                 TABEL 2: RIWAYAT PEMBAYARAN DAN BUKTI
                 ====================================================================== --}}
            <h6 class="text-secondary small fw-bold text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                Riwayat Pembayaran Diterima
            </h6>
            <div class="table-responsive rounded-4 border overflow-hidden mb-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold" style="width: 40px;">No</th>
                            <th class="text-secondary small fw-bold">No Pembayaran</th>
                            <th class="text-secondary small fw-bold">Tanggal Bayar</th>
                            <th class="text-secondary small fw-bold text-end">Nominal</th>
                            <th class="text-secondary small fw-bold">Metode / Kanal</th>
                            <th class="text-secondary small fw-bold">Penerima</th>
                            <th class="text-secondary small fw-bold">Status Setoran RT</th>
                            <th class="text-secondary small fw-bold text-center pe-3">Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihan->pembayarans as $idx => $pay)
                            <tr>
                                <td class="ps-3 text-center text-muted small">{{ $idx + 1 }}</td>
                                <td class="font-monospace fw-bold small text-primary">{{ $pay->nomor_pembayaran }}</td>
                                <td class="small">{{ $pay->tanggal_bayar ? $pay->tanggal_bayar->translatedFormat('d M Y, H:i') : '-' }}</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($pay->nominal_bayar, 0, ',', '.') }}</td>
                                <td class="small">
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-0.5">
                                        {{ strtoupper(str_replace('_', ' ', $pay->metode_pembayaran)) }}
                                    </span>
                                </td>
                                <td class="small">{{ $pay->nama_penerima ?? ($pay->penerima->name ?? 'System') }}</td>
                                <td class="small">
                                    @if($pay->status_setoran_rw === 'menunggu_setor_ke_rw')
                                        <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                            Titipan di RT (Menunggu Setor)
                                        </span>
                                    @elseif($pay->status_setoran_rw === 'sudah_disetor_ke_rw')
                                        <span class="badge bg-success text-white rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                            Sudah Disetor ke RW
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                            Langsung Masuk Kas
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    @if($pay->bukti_pembayaran_path)
                                        <a href="{{ asset('storage/' . $pay->bukti_pembayaran_path) }}" target="_blank" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0.5">
                                            <i data-lucide="image" style="width: 12px; height: 12px;"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">Belum ada riwayat pembayaran untuk tagihan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ======================================================================
                 TABEL 3: STATUS SETIAP KOMPONEN ALOKASI (TRANSPARANSI KAS INDUK & RT)
                 ====================================================================== --}}
            <h6 class="text-secondary small fw-bold text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                Status Realisasi Alokasi Otomatis ke Masing-Masing Kas
            </h6>
            <div class="table-responsive rounded-4 border overflow-hidden">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold" style="width: 40px;">No</th>
                            <th class="text-secondary small fw-bold">Komponen</th>
                            <th class="text-secondary small fw-bold">Hak Entitas</th>
                            <th class="text-secondary small fw-bold">Rekening Buku Kas</th>
                            <th class="text-secondary small fw-bold text-end">Nominal Alokasi</th>
                            <th class="text-secondary small fw-bold text-center">Status Alokasi</th>
                            <th class="text-secondary small fw-bold text-center pe-3">Status Settlement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihan->alokasis as $idx => $alo)
                            <tr>
                                <td class="ps-3 text-center text-muted small">{{ $idx + 1 }}</td>
                                <td><strong class="text-dark small">{{ $alo->nama_komponen }}</strong></td>
                                <td>
                                    <span class="badge px-2 py-0.5 rounded-pill" style="font-size: 0.7rem; background: #F1F5F9; color: #334155; border: 1px solid #CBD5E1;">
                                        {{ strtoupper($alo->pemilik_dana) }}
                                    </span>
                                </td>
                                <td class="small">{{ $alo->bukuKas->nama_kas ?? 'Kas Terkait' }}</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($alo->nominal_alokasi, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($alo->status_alokasi === 'masuk_kas')
                                        <span class="badge bg-success-subtle text-success px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                            Masuk Kas Resmi
                                        </span>
                                    @elseif($alo->status_alokasi === 'titipan_rt')
                                        <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                            Titipan Tunai RT
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                            Disetor ke RW
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    @if($alo->settlement_status === 'settled')
                                        <span class="badge bg-success text-white rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                            Settled
                                        </span>
                                    @elseif($alo->settlement_status === 'dalam_proses')
                                        <span class="badge bg-secondary text-white rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                            Dalam Proses Split
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">Belum ada dana yang dialokasikan untuk tagihan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================================
     MODAL: INPUT PEMBAYARAN MANUAL (TUNAI / TRANSFER)
     ====================================================================== --}}
<div class="modal fade" id="modalInputBayar" tabindex="-1" aria-labelledby="modalInputBayarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalInputBayarLabel">
                    <i data-lucide="credit-card" class="text-primary"></i> Catat Pembayaran Iuran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tagihan.bayar_sebagian', $tagihan->id) }}" method="POST">
                @csrf
                <div class="modal-body space-y-3">
                    <div class="p-3 rounded-3 border bg-light mb-3">
                        <div class="d-flex justify-content-between text-muted small mb-1">
                            <span>Sisa Kewajiban Saat Ini:</span>
                            <strong class="text-danger fs-6">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</strong>
                        </div>
                        <div class="small text-muted" style="font-size: 0.75rem;">
                            Pembayaran akan langsung dipecah dan dialokasikan ke rekening Kas RT, RW, dan Sampah secara berurutan.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nominal Bayar (Rp) *</label>
                        <input type="number" name="nominal_bayar" max="{{ $tagihan->sisa_tagihan }}" value="{{ $tagihan->sisa_tagihan }}" required class="form-control rounded-3 fw-bold fs-5">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Kanal Pembayaran *</label>
                        <select name="kanal_pembayaran" class="form-select rounded-3" required>
                            @if($isRt)
                                <option value="tunai_melalui_rt">Tunai Melalui Pengurus RT {{ $tagihan->clean_rt }}</option>
                            @endif
                            <option value="langsung_rekening">Transfer Langsung ke Rekening Kas</option>
                            <option value="tunai_rw">Tunai Melalui Pengurus RW 013</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Catatan / Keterangan</label>
                        <input type="text" name="catatan" class="form-control rounded-3" placeholder="Contoh: Diterima tunai di sekretariat RT">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                        <i data-lucide="check" style="width: 14px; height: 14px;"></i> Proses Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================================================================
     MODAL: PEMBAYARAN DIGITAL & SIMULASI SPLIT SETTLEMENT
     ====================================================================== --}}
<div class="modal fade" id="modalDigitalPay" tabindex="-1" aria-labelledby="modalDigitalPayLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalDigitalPayLabel">
                    <i data-lucide="qr-code" class="text-success"></i> Pembayaran Digital QRIS & Split Settlement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="p-3 rounded-4 border bg-light d-inline-block mb-3 shadow-sm" style="max-width: 250px;">
                    {{-- QR Code Placeholder / Simulated QRIS --}}
                    <div class="bg-white p-3 rounded-3 border d-flex flex-column align-items-center justify-content-center">
                        <i data-lucide="qr-code" style="width: 140px; height: 140px; color: #1456A0;"></i>
                        <span class="badge bg-dark text-white mt-2 px-3 py-1 font-monospace" style="font-size: 0.72rem;">QRIS HAVEN</span>
                    </div>
                </div>

                <div class="fs-5 fw-black text-dark mb-1">
                    Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                </div>
                <p class="text-muted small mb-3">
                    Pindai dengan aplikasi mobile banking atau e-wallet (BCA, Mandiri, GoPay, OVO, Dana).
                </p>

                <div class="text-start p-3 rounded-3 border bg-light small mb-3" style="font-size: 0.78rem;">
                    <strong class="d-block text-dark mb-1"><i data-lucide="git-fork" style="width: 14px; height: 14px;"></i> Arsitektur Split Settlement Gateway:</strong>
                    <div class="text-muted">• Dana porsi RT masuk rekening Kas RT {{ $tagihan->clean_rt }}</div>
                    <div class="text-muted">• Dana porsi RW masuk rekening Kas Induk RW 013</div>
                    <div class="text-muted">• Retribusi sampah masuk rekening Kas Bank Sampah / Kebersihan</div>
                    <div class="text-muted mt-1 pt-1 border-top" style="font-size: 0.72rem;">
                        Status: <span class="badge bg-success-subtle text-success">Split Engine Ready</span>
                    </div>
                </div>

                {{-- Tombol Simulasi Webhook Pembayaran Berhasil --}}
                <form action="{{ route('tagihan.bayar_sebagian', $tagihan->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="nominal_bayar" value="{{ $tagihan->sisa_tagihan }}">
                    <input type="hidden" name="kanal_pembayaran" value="QRIS">
                    <input type="hidden" name="catatan" value="Simulasi pembayaran sukses via Gateway QRIS">
                    <input type="hidden" name="idempotency_key" value="MOCK-PAY-{{ $tagihan->id }}-{{ time() }}">
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 fw-bold w-100 shadow-sm">
                        <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Simulasikan Sukses Bayar Digital
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
