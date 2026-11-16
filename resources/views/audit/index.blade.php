@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Audit & Evaluasi RW 013 -->
    <div class="wm-card mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); color: #fff; border: none; border-radius: 16px;">
        <div class="p-4 p-md-5 position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div style="max-width: 780px;">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-primary text-white fw-bold px-3 py-1 rounded-pill" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i data-lucide="clipboard-check" style="width: 14px; height: 14px; vertical-align: middle;"></i> BADAN AUDIT & EVALUASI
                        </span>
                        <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill opacity-90" style="font-size: 11px;">
                            RW 013 Sukamiskin Arcamanik
                        </span>
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill" style="font-size: 11px;">
                            Tahun Evaluasi {{ $tahun }}
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Audit & Evaluasi Akuntabilitas RW 013</h2>
                    <p class="mb-0 text-white text-opacity-75 lead fs-6">
                        Pemeriksaan berkala dan tahunan terhadap kepatuhan keuangan, pertanggungjawaban program kerja, pemeriksaan LPJ, dan penyelesaian temuan audit pada 8 rukun tetangga.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('rw.audit.cetak', ['tahun' => $tahun]) }}" target="_blank" class="btn btn-warning rounded-pill px-3 py-2 fw-bold small text-dark shadow-sm">
                        <i data-lucide="printer" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Cetak Laporan LHA
                    </a>
                    <a href="{{ route('rw.keuangan.monitoring') }}" class="btn btn-outline-light rounded-pill px-3 py-2 fw-bold small shadow-sm">
                        <i data-lucide="wallet" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Monitoring Keuangan
                    </a>
                </div>
            </div>
        </div>
        <div class="position-absolute rounded-circle" style="width: 320px; height: 320px; background: rgba(255,255,255,0.03); top: -80px; right: -60px; pointer-events: none;"></div>
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

    <!-- 1. DASHBOARD METRICS AUDIT (6 KARTU KPI) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Kegiatan Diperiksa</span>
                <h4 class="fw-bold mb-0 text-primary">{{ $totalKegiatan }}</h4>
                <small class="text-muted" style="font-size: 11px;">Program Proyek</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Transaksi Diaudit</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $totalTransaksiDiperiksa }}</h4>
                <small class="text-muted" style="font-size: 11px;">Entri Ledger</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Total Temuan</span>
                <h4 class="fw-bold mb-0 text-warning-emphasis">{{ $totalTemuan }}</h4>
                <small class="text-muted" style="font-size: 11px;">Audit Findings</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Temuan Terbuka</span>
                <h4 class="fw-bold mb-0 text-danger">{{ $temuanTerbuka }}</h4>
                <small class="text-muted" style="font-size: 11px;">Perlu Klarifikasi</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Temuan Selesai</span>
                <h4 class="fw-bold mb-0 text-success">{{ $temuanSelesai }}</h4>
                <small class="text-muted" style="font-size: 11px;">Tuntas Ditindak</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Skor Kepatuhan RT</span>
                <h4 class="fw-bold mb-0 text-success">{{ $rataSkorKepatuhan }}<span class="fs-6 fw-normal text-muted">/100</span></h4>
                <small class="text-muted" style="font-size: 11px;">Rata-rata 8 RT</small>
            </div>
        </div>
    </div>

    <!-- Navigasi Tab Audit & Evaluasi -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-2">
        <ul class="nav nav-pills nav-fill gap-2" id="auditTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'overview' ? 'active' : '' }}" href="{{ route('rw.audit.index', ['tab' => 'overview', 'tahun' => $tahun]) }}">
                    <i data-lucide="layout-grid" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Dashboard Kepatuhan RT
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'keuangan' ? 'active' : '' }}" href="{{ route('rw.audit.index', ['tab' => 'keuangan', 'tahun' => $tahun]) }}">
                    <i data-lucide="scale" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Audit Keuangan
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'kegiatan' ? 'active' : '' }}" href="{{ route('rw.audit.index', ['tab' => 'kegiatan', 'tahun' => $tahun]) }}">
                    <i data-lucide="briefcase" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Audit Kegiatan & Proyek
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'temuan' ? 'active' : '' }}" href="{{ route('rw.audit.index', ['tab' => 'temuan', 'tahun' => $tahun]) }}">
                    <i data-lucide="alert-circle" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Temuan Audit ({{ $totalTemuan }})
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill py-2 fw-bold small {{ $activeTab == 'evaluasi' ? 'active' : '' }}" href="{{ route('rw.audit.index', ['tab' => 'evaluasi', 'tahun' => $tahun]) }}">
                    <i data-lucide="award" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Evaluasi & Rekomendasi RT
                </a>
            </li>
        </ul>
    </div>

    <!-- TAB 1: OVERVIEW KEPATUHAN RT -->
    @if($activeTab == 'overview')
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                <i data-lucide="shield-check" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                                Scorecard Kepatuhan & Pertanggungjawaban 8 RT (Tahun {{ $tahun }})
                            </h5>
                            <p class="mb-0 text-muted small">
                                Hasil pemeringkatan transparansi pembukuan kas, kelengkapan kuitansi, dan pelaporan pertanggungjawaban RT 001 - RT 008.
                            </p>
                        </div>
                        <a href="{{ route('rw.audit.index', ['tab' => 'evaluasi', 'tahun' => $tahun]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                            <i data-lucide="edit-3" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Perbarui Skor RT
                        </a>
                    </div>

                    <div class="row g-3">
                        @foreach($scorecardRt as $sc)
                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="p-3 border rounded-3 h-100 bg-light-subtle d-flex flex-column justify-content-between position-relative overflow-hidden">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge px-3 py-1 rounded-pill fw-bold" style="background: #E8F5E9; color: #16865D; border: 1px solid #C8E6C9;">
                                                {{ $sc['rt_name'] }}
                                            </span>
                                            <span class="badge {{ $sc['status_badge'] }} border px-2 py-1 rounded-pill small fw-bold">
                                                {{ $sc['status_label'] }}
                                            </span>
                                        </div>
                                        <h4 class="fw-bold text-dark mb-1">{{ $sc['skor'] }}<span class="fs-6 fw-normal text-muted">/100</span></h4>
                                        <p class="small text-muted mb-2"><i data-lucide="user" style="width: 12px; height: 12px; vertical-align: middle;"></i> PIC: {{ $sc['pic_name'] }}</p>
                                        <div class="p-2 border rounded-2 bg-white small mb-2" style="font-size: 11px;">
                                            <strong>Catatan:</strong> {{ Str::limit($sc['catatan'], 70) }}
                                        </div>
                                    </div>
                                    <div class="border-top pt-2 d-flex justify-content-between align-items-center small text-muted">
                                        <span>Saldo: <strong>Rp {{ number_format($sc['saldo_kas'], 0, ',', '.') }}</strong></span>
                                        @if($sc['deadline'])
                                            <span class="text-danger"><i data-lucide="clock" style="width: 11px; height: 11px; vertical-align: middle;"></i> {{ $sc['deadline']->format('d/m') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Ringkasan Temuan Menurut Prioritas Risiko -->
            <div class="col-12 col-lg-6">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i data-lucide="alert-triangle" class="me-2 text-warning" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                        Tingkat Risiko Temuan Audit
                    </h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 border" style="background: #FEE2E2;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger rounded-pill px-2 py-1">Kritis</span>
                                <span class="small fw-semibold text-danger">Penyimpangan signifikan / bukti nota fiktif</span>
                            </div>
                            <strong class="text-danger fs-6">{{ $countKritis }} temuan</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 border" style="background: #FFEDD5;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1">Tinggi</span>
                                <span class="small fw-semibold text-warning-emphasis">Keterlambatan LPJ / nominal tanpa struk</span>
                            </div>
                            <strong class="text-warning-emphasis fs-6">{{ $countTinggi }} temuan</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 border" style="background: #FEF9C3;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white rounded-pill px-2 py-1">Sedang</span>
                                <span class="small fw-semibold text-dark">Kelalaian pengisian nomor rekening / format LPJ</span>
                            </div>
                            <strong class="text-dark fs-6">{{ $countSedang }} temuan</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 border" style="background: #E8F5E9;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success rounded-pill px-2 py-1">Rendah</span>
                                <span class="small fw-semibold text-success">Kerapian arsip dan dokumentasi pendukung</span>
                            </div>
                            <strong class="text-success fs-6">{{ $countRendah }} temuan</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rekomendasi Tindak Lanjut Utama -->
            <div class="col-12 col-lg-6">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i data-lucide="check-square" class="me-2 text-primary" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                        Fokus Tindak Lanjut Audit RW 013
                    </h6>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small text-muted">
                        <li class="d-flex align-items-start gap-2">
                            <i data-lucide="check-circle" class="text-success mt-1" style="width: 16px; height: 16px; min-width: 16px;"></i>
                            <span>Seluruh pengurus RT wajib mengunggah foto kuitansi/nota digital untuk transaksi di atas Rp 500.000.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i data-lucide="check-circle" class="text-success mt-1" style="width: 16px; height: 16px; min-width: 16px;"></i>
                            <span>Laporan Pertanggungjawaban (LPJ) program kerja fisik harus diserahkan maksimal 14 hari pasca-kegiatan selesai.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i data-lucide="check-circle" class="text-success mt-1" style="width: 16px; height: 16px; min-width: 16px;"></i>
                            <span>Rekonsiliasi saldo kas bank sampah dan buku kas lingkungan dilakukan setiap akhir bulan penimbangan.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i data-lucide="check-circle" class="text-success mt-1" style="width: 16px; height: 16px; min-width: 16px;"></i>
                            <span>Evaluasi kepatuhan digunakan sebagai dasar pemberian apresiasi RT Terbaik pada Rapat Tahunan RW 013.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 2: AUDIT KEUANGAN -->
    @if($activeTab == 'keuangan')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                <i data-lucide="file-warning" class="me-2 text-danger" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                Deteksi Transaksi Tanpa Bukti Pendukung ({{ $countTanpaBukti }} Transaksi)
            </h5>
            <p class="small text-muted mb-3">Daftar transaksi berstatus selesai namun belum melampirkan foto nota, faktur, atau kuitansi pembayaran resmi.</p>

            <div class="table-responsive mb-4">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-2 text-secondary small fw-bold">Nomor TX</th>
                            <th class="text-secondary small fw-bold">Tanggal</th>
                            <th class="text-secondary small fw-bold">Akun Kas</th>
                            <th class="text-secondary small fw-bold">Nominal</th>
                            <th class="text-secondary small fw-bold">Penerima/Penyetor</th>
                            <th class="text-secondary small fw-bold text-center">Status Bukti</th>
                            <th class="text-end pe-2 text-secondary small fw-bold">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiTanpaBukti as $tb)
                            <tr>
                                <td class="ps-2"><strong class="small text-dark">{{ $tb->nomor_transaksi }}</strong></td>
                                <td class="small text-muted">{{ $tb->tanggal_transaksi->format('d/m/Y') }}</td>
                                <td class="small">{{ $tb->bukuKas->nama_kas ?? 'Kas RW' }}</td>
                                <td class="small fw-bold text-danger">Rp {{ number_format($tb->nominal, 0, ',', '.') }}</td>
                                <td class="small text-dark">{{ $tb->penyetor_penerima ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small">
                                        <i data-lucide="x-circle" style="width: 12px; height: 12px; vertical-align: middle;"></i> Tanpa Bukti
                                    </span>
                                </td>
                                <td class="text-end pe-2">
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-0" data-bs-toggle="modal" data-bs-target="#modalTambahTemuanQuick{{ $tb->id }}">
                                        Catat Temuan
                                    </button>

                                    <!-- Quick Modal Tambah Temuan -->
                                    <div class="modal fade text-start" id="modalTambahTemuanQuick{{ $tb->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <form action="{{ route('rw.audit.temuan.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="transaksi_id" value="{{ $tb->id }}">
                                                    <input type="hidden" name="buku_kas_id" value="{{ $tb->buku_kas_id }}">
                                                    <input type="hidden" name="rt_number" value="{{ $tb->bukuKas->rt_number ?? '000' }}">
                                                    <div class="modal-header border-bottom">
                                                        <h6 class="modal-title fw-bold text-dark">Terbitkan Temuan Audit Transaksi</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-3">
                                                        <div class="mb-2">
                                                            <label class="form-label small fw-bold text-dark mb-1">Judul Temuan</label>
                                                            <input type="text" name="judul_temuan" class="form-control form-control-sm rounded-3" value="Transaksi Tanpa Bukti Fisik: {{ $tb->nomor_transaksi }}" required>
                                                        </div>
                                                        <div class="row g-2 mb-2">
                                                            <div class="col-6">
                                                                <label class="form-label small fw-bold text-dark mb-1">Kategori Masalah</label>
                                                                <select name="kategori_masalah" class="form-select form-select-sm rounded-3" required>
                                                                    <option value="transaksi_tanpa_bukti" selected>Transaksi Tanpa Bukti</option>
                                                                    <option value="keuangan">Keuangan</option>
                                                                    <option value="administrasi">Administrasi</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label small fw-bold text-dark mb-1">Prioritas</label>
                                                                <select name="tingkat_prioritas" class="form-select form-select-sm rounded-3" required>
                                                                    <option value="tinggi" selected>Tinggi</option>
                                                                    <option value="sedang">Sedang</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label small fw-bold text-dark mb-1">Deskripsi Masalah</label>
                                                            <textarea name="deskripsi_masalah" class="form-control form-control-sm rounded-3" rows="3" required>Transaksi {{ $tb->nomor_transaksi }} sebesar Rp {{ number_format($tb->nominal, 0, ',', '.') }} pada tanggal {{ $tb->tanggal_transaksi->format('d/m/Y') }} tidak memiliki lampiran kuitansi/nota sah.</textarea>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label small fw-bold text-dark mb-1">Catatan Evaluasi RW</label>
                                                            <input type="text" name="catatan_evaluasi_rw" class="form-control form-control-sm rounded-3" value="Petugas wajib mengunggah kuitansi sah atau surat pernyataan bermaterai.">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top pt-2">
                                                        <button type="button" class="btn btn-secondary rounded-pill btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">Simpan Temuan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-success py-3 small"><i data-lucide="check" style="width: 14px; height: 14px;"></i> Seluruh transaksi memiliki bukti pendukung lengkap.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row g-4">
                <!-- Transaksi Nominal Besar / Anomali -->
                <div class="col-12 col-lg-6">
                    <h6 class="fw-bold text-dark mb-2">
                        <i data-lucide="dollar-sign" class="me-1 text-primary" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                        Transaksi Pengeluaran Signifikan (&ge; Rp 2.500.000)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2 small text-secondary fw-bold">TX / Tanggal</th>
                                    <th class="small text-secondary fw-bold">Kas / Uraian</th>
                                    <th class="small text-secondary fw-bold text-end pe-2">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksiAnomali as $ta)
                                    <tr>
                                        <td class="ps-2">
                                            <span class="small fw-bold text-dark d-block">{{ $ta->nomor_transaksi }}</span>
                                            <small class="text-muted">{{ $ta->tanggal_transaksi->format('d/m/Y') }}</small>
                                        </td>
                                        <td>
                                            <span class="small text-dark fw-semibold d-block">{{ $ta->bukuKas->nama_kas ?? '-' }}</span>
                                            <small class="text-muted">{{ Str::limit($ta->keterangan, 30) }}</small>
                                        </td>
                                        <td class="text-end pe-2">
                                            <strong class="small text-danger">Rp {{ number_format($ta->nominal, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted small py-3">Tidak ada pengeluaran di atas batas batas uji.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Log Aktivitas Petugas Keuangan -->
                <div class="col-12 col-lg-6">
                    <h6 class="fw-bold text-dark mb-2">
                        <i data-lucide="shield" class="me-1 text-primary" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                        Audit Trail Petugas Keuangan
                    </h6>
                    <div class="d-flex flex-column gap-2" style="max-height: 320px; overflow-y: auto;">
                        @forelse($logAktivitasAudit as $auditLog)
                            <div class="p-2 border rounded-3 bg-light-subtle d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-dark small d-block">{{ $auditLog->user->name ?? 'Sistem' }}</strong>
                                    <span class="small text-muted d-block" style="font-size: 11px;">{{ $auditLog->deskripsi }}</span>
                                </div>
                                <span class="badge bg-secondary-subtle text-secondary small" style="font-size: 10px;">{{ $auditLog->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <div class="text-center text-muted small py-3">Belum ada riwayat audit log.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 3: AUDIT KEGIATAN & PROYEK -->
    @if($activeTab == 'kegiatan')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i data-lucide="briefcase" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Audit Program Kerja & Proyek Lingkungan (Tahun {{ $tahun }})
                    </h5>
                    <p class="mb-0 text-muted small">Pemeriksaan realisasi fisik vs serapan anggaran dan status audit kepatuhan program kerja.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small">
                        Sesuai: {{ $lpjDisetujui }}
                    </span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill small">
                        Perlu Perhatian: {{ $lpjPerluRevisi }}
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 rounded-pill small">
                        Belum Diaudit: {{ $lpjBelumDiperiksa }}
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold">Nama Proyek / Kegiatan</th>
                            <th class="text-secondary small fw-bold">Wilayah / PIC</th>
                            <th class="text-secondary small fw-bold">Anggaran Disetujui</th>
                            <th class="text-secondary small fw-bold">Realisasi Dana</th>
                            <th class="text-secondary small fw-bold">Sisa Pagu</th>
                            <th class="text-secondary small fw-bold">Status Proyek</th>
                            <th class="text-secondary small fw-bold text-center">Status Audit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarKegiatan as $keg)
                            <tr>
                                <td class="ps-3">
                                    <strong class="text-dark d-block">{{ $keg->nama_kegiatan }}</strong>
                                    <small class="badge bg-light text-secondary border">{{ $keg->kategori_label }}</small>
                                </td>
                                <td>
                                    <span class="badge px-2 py-1 rounded-pill small fw-bold" style="background: #E8F5E9; color: #16865D;">
                                        {{ $keg->clean_rt }}
                                    </span>
                                    <small class="text-muted d-block mt-1">{{ $keg->penanggung_jawab }}</small>
                                </td>
                                <td>
                                    <span class="small fw-semibold text-dark">Rp {{ number_format($keg->anggaran_awal, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <span class="small fw-bold text-danger">Rp {{ number_format($keg->realisasi_anggaran, 0, ',', '.') }}</span>
                                    <small class="text-muted d-block" style="font-size: 11px;">({{ $keg->persentase_realisasi }}%)</small>
                                </td>
                                <td>
                                    <strong class="small text-success">Rp {{ number_format($keg->sisa_anggaran, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill small">
                                        {{ ucfirst($keg->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($keg->status_audit == 'sesuai')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small fw-bold">
                                            <i data-lucide="check-check" style="width: 12px; height: 12px; vertical-align: middle;"></i> Sesuai
                                        </span>
                                    @elseif($keg->status_audit == 'perlu_perhatian')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill small fw-bold">
                                            <i data-lucide="alert-triangle" style="width: 12px; height: 12px; vertical-align: middle;"></i> Perlu Perhatian
                                        </span>
                                    @elseif($keg->status_audit == 'temuan_audit')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill small fw-bold">
                                            Ada Temuan
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 rounded-pill small">
                                            Belum Diaudit
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada kegiatan/proyek tercatat pada tahun ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 4: TEMUAN AUDIT (AUDIT FINDINGS) -->
    @if($activeTab == 'temuan')
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i data-lucide="alert-circle" class="me-2 text-danger" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Register Temuan Audit (Audit Findings)
                    </h5>
                    <p class="mb-0 text-muted small">Daftar temuan hasil pemeriksaan Ketua RW dan Auditor resmi serta status tindak lanjut klarifikasi RT.</p>
                </div>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold small" data-bs-toggle="modal" data-bs-target="#modalTambahTemuanBaru">
                    <i data-lucide="plus-circle" style="width: 15px; height: 15px; vertical-align: middle;" class="me-1"></i> Terbitkan Temuan Baru
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold">Nomor & Judul Temuan</th>
                            <th class="text-secondary small fw-bold">Kategori</th>
                            <th class="text-secondary small fw-bold text-center">Prioritas</th>
                            <th class="text-secondary small fw-bold">Wilayah Terkait</th>
                            <th class="text-secondary small fw-bold">Tenggat Waktu</th>
                            <th class="text-secondary small fw-bold text-center">Status</th>
                            <th class="text-end pe-3 text-secondary small fw-bold">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarTemuan as $tem)
                            <tr>
                                <td class="ps-3">
                                    <strong class="text-dark d-block small">{{ $tem->kode_temuan }}</strong>
                                    <span class="text-secondary small fw-semibold">{{ $tem->judul_temuan }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border small">{{ $tem->kategori_label }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $tem->prioritas_badge['bg'] }} border px-2 py-1 rounded-pill small fw-bold">
                                        {{ $tem->prioritas_badge['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge px-3 py-1 rounded-pill fw-bold" style="background: #E8F5E9; color: #16865D; border: 1px solid #C8E6C9;">
                                        {{ $tem->clean_rt }}
                                    </span>
                                </td>
                                <td>
                                    @if($tem->tenggat_tindak_lanjut)
                                        <span class="small text-danger fw-semibold"><i data-lucide="clock" style="width: 12px; height: 12px; vertical-align: middle;"></i> {{ $tem->tenggat_tindak_lanjut->format('d/m/Y') }}</span>
                                    @else
                                        <span class="small text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $tem->status_badge['bg'] }} border px-3 py-1 rounded-pill small fw-bold">
                                        {{ $tem->status_badge['label'] }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 small fw-semibold" data-bs-toggle="modal" data-bs-target="#modalKelolaTemuan{{ $tem->id }}">
                                        Kelola & Status
                                    </button>

                                    <!-- MODAL DETAIL & UPDATE STATUS TEMUAN -->
                                    <div class="modal fade text-start" id="modalKelolaTemuan{{ $tem->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <form action="{{ route('rw.audit.temuan.status', $tem->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header border-bottom">
                                                        <div>
                                                            <h6 class="modal-title fw-bold text-dark">{{ $tem->kode_temuan }}: {{ $tem->judul_temuan }}</h6>
                                                            <small class="text-muted">Wilayah: {{ $tem->clean_rt }} • Tanggal Temuan: {{ $tem->tanggal_temuan->format('d F Y') }}</small>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark mb-1">Deskripsi Masalah</label>
                                                            <p class="p-3 bg-light rounded-3 small text-dark mb-0 border">{{ $tem->deskripsi_masalah }}</p>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark mb-1">Catatan Evaluasi / Rekomendasi Auditor RW</label>
                                                            <p class="p-3 bg-light-subtle rounded-3 small text-dark mb-0 border">{{ $tem->catatan_evaluasi_rw ?? 'Belum ada rekomendasi spesifik.' }}</p>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark mb-1">Klarifikasi / Tanggapan dari Pihak RT</label>
                                                            <textarea name="tanggapan_rt" class="form-control form-control-sm rounded-3" rows="3" placeholder="Masukkan tanggapan atau bukti tindak lanjut dari Ketua RT...">{{ $tem->tanggapan_rt }}</textarea>
                                                        </div>

                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold text-dark mb-1">Perbarui Status Temuan</label>
                                                                <select name="status" class="form-select form-select-sm rounded-3" required>
                                                                    <option value="perlu_diperiksa" {{ $tem->status == 'perlu_diperiksa' ? 'selected' : '' }}>Perlu Diperiksa</option>
                                                                    <option value="dalam_pemeriksaan" {{ $tem->status == 'dalam_pemeriksaan' ? 'selected' : '' }}>Dalam Pemeriksaan</option>
                                                                    <option value="menunggu_klarifikasi" {{ $tem->status == 'menunggu_klarifikasi' ? 'selected' : '' }}>Menunggu Klarifikasi RT</option>
                                                                    <option value="perlu_tindak_lanjut" {{ $tem->status == 'perlu_tindak_lanjut' ? 'selected' : '' }}>Perlu Tindak Lanjut</option>
                                                                    <option value="selesai" {{ $tem->status == 'selesai' ? 'selected' : '' }}>Selesai (Resolved)</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold text-dark mb-1">Catatan Tambahan Auditor RW</label>
                                                                <input type="text" name="catatan_evaluasi_rw" class="form-control form-control-sm rounded-3" value="{{ $tem->catatan_evaluasi_rw }}" placeholder="Catatan auditor setelah evaluasi...">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top pt-2">
                                                        <button type="button" class="btn btn-secondary rounded-pill btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">Simpan Perubahan Status</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada temuan audit yang terdaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL FORM INPUT TEMUAN BARU -->
        <div class="modal fade" id="modalTambahTemuanBaru" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow">
                    <form action="{{ route('rw.audit.temuan.store') }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom">
                            <h6 class="modal-title fw-bold text-dark">Terbitkan Temuan Audit Baru</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold text-dark mb-1">Judul Temuan</label>
                                    <input type="text" name="judul_temuan" class="form-control rounded-3" placeholder="Contoh: Bukti Pengeluaran Kas RT 003 Tidak Dilampirkan..." required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-dark mb-1">Wilayah RT Terkait</label>
                                    <select name="rt_number" class="form-select rounded-3" required>
                                        @for($i = 1; $i <= 8; $i++)
                                            <option value="{{ $i }}">RT 00{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-dark mb-1">Kategori Masalah</label>
                                    <select name="kategori_masalah" class="form-select rounded-3" required>
                                        <option value="administrasi">Administrasi</option>
                                        <option value="keuangan">Keuangan</option>
                                        <option value="transaksi_tanpa_bukti">Transaksi Tanpa Bukti</option>
                                        <option value="selisih_anggaran">Selisih Anggaran</option>
                                        <option value="kegiatan_terlambat">Kegiatan Terlambat</option>
                                        <option value="prosedur_pelaporan">Prosedur Pelaporan</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-dark mb-1">Tingkat Risiko</label>
                                    <select name="tingkat_prioritas" class="form-select rounded-3" required>
                                        <option value="rendah">Rendah</option>
                                        <option value="sedang" selected>Sedang</option>
                                        <option value="tinggi">Tinggi</option>
                                        <option value="kritis">Kritis</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-dark mb-1">Batas Waktu Klarifikasi</label>
                                    <input type="date" name="tenggat_tindak_lanjut" class="form-control rounded-3">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Deskripsi Lengkap Masalah</label>
                                <textarea name="deskripsi_masalah" class="form-control rounded-3" rows="4" placeholder="Jelaskan fakta pemeriksaan, bukti, atau SOP yang dilanggar..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Catatan Evaluasi & Rekomendasi RW</label>
                                <textarea name="catatan_evaluasi_rw" class="form-control rounded-3" rows="2" placeholder="Instruksi perbaikan bagi pengurus RT..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top pt-2">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Terbitkan Temuan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 5: EVALUASI & REKOMENDASI RT -->
    @if($activeTab == 'evaluasi')
        <div class="row g-4 mb-4">
            <!-- Form Input Skor Evaluasi Pertanggungjawaban RT -->
            <div class="col-12 col-xl-5">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4">
                    <h5 class="fw-bold mb-2 text-dark border-bottom pb-2">
                        <i data-lucide="edit-3" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Form Penetapan Skor & Evaluasi RT
                    </h5>
                    <p class="text-muted small mb-3">Ketua RW menetapkan nilai kepatuhan berkala dan rekomendasi pembinaan resmi bagi 8 RT.</p>

                    <form action="{{ route('rw.audit.evaluasi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <input type="hidden" name="periode" value="Tahunan">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">Pilih Wilayah RT</label>
                            <select name="rt_number" class="form-select rounded-3" required>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}">RT 00{{ $i }} - RW 013</option>
                                @endfor
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-dark mb-1">Skor Kepatuhan (0-100)</label>
                                <input type="number" step="0.5" name="skor_kepatuhan" class="form-control rounded-3" min="0" max="100" placeholder="Contoh: 88.5" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-dark mb-1">Status Kepatuhan</label>
                                <select name="status_pertanggungjawaban" class="form-select rounded-3" required>
                                    <option value="sangat_baik">Sangat Baik</option>
                                    <option value="baik" selected>Baik</option>
                                    <option value="cukup">Cukup</option>
                                    <option value="perlu_pembinaan">Perlu Pembinaan</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">Catatan Evaluasi Ketua RW</label>
                            <textarea name="catatan_evaluasi" class="form-control rounded-3" rows="3" placeholder="Pencatatan kas dan administrasi berjalan tertib..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">Rekomendasi Perbaikan</label>
                            <textarea name="rekomendasi_rw" class="form-control rounded-3" rows="2" placeholder="Pertahankan konsistensi pelaporan bulanan..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark mb-1">Tenggat Perbaikan (Jika ada)</label>
                            <input type="date" name="tenggat_perbaikan" class="form-control rounded-3">
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2">
                            <i data-lucide="save" style="width: 16px; height: 16px; vertical-align: middle;" class="me-1"></i> Simpan Evaluasi RT
                        </button>
                    </form>
                </div>
            </div>

            <!-- Daftar Evaluasi Seluruh RT -->
            <div class="col-12 col-xl-7">
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i data-lucide="award" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                            Daftar Nilai & Rekomendasi 8 RT (Tahun {{ $tahun }})
                        </h5>
                        <a href="{{ route('rw.audit.cetak', ['tahun' => $tahun]) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold">
                            <i data-lucide="printer" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Cetak LHA & Evaluasi (PDF)
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-secondary small fw-bold">Wilayah RT</th>
                                    <th class="text-secondary small fw-bold text-center">Skor</th>
                                    <th class="text-secondary small fw-bold text-center">Status</th>
                                    <th class="text-secondary small fw-bold">Catatan & Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scorecardRt as $itemSc)
                                    <tr>
                                        <td class="ps-3">
                                            <strong class="text-dark d-block">{{ $itemSc['rt_name'] }}</strong>
                                            <small class="text-muted">{{ $itemSc['pic_name'] }}</small>
                                        </td>
                                        <td class="text-center">
                                            <strong class="fs-6 text-dark">{{ $itemSc['skor'] }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $itemSc['status_badge'] }} border px-2 py-1 rounded-pill small fw-bold">
                                                {{ $itemSc['status_label'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-dark d-block"><strong>Catatan:</strong> {{ $itemSc['catatan'] }}</small>
                                            <small class="text-muted d-block"><strong>Rekomendasi:</strong> {{ $itemSc['rekomendasi'] }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
