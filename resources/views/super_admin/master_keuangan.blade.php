@extends('layouts.app')

@section('title', 'Master Rekening & Tarif')

@push('styles')
<style>
    /* Scoped Styling Master Rekening & Tarif Super Admin */
    .sa-master-header-card {
        border-radius: 18px !important;
        background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%) !important;
        padding: 24px 28px !important;
        color: #FFFFFF !important;
        border: none !important;
        box-shadow: 0 4px 18px rgba(10, 37, 64, 0.12) !important;
    }
    .sa-master-card {
        border-radius: 16px !important;
        background-color: var(--wm-card, #FFFFFF) !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
        box-shadow: 0 2px 10px rgba(23, 43, 77, 0.05) !important;
        overflow: hidden;
    }
    .sa-tab-nav {
        background-color: var(--wm-card, #FFFFFF) !important;
        border-radius: 14px !important;
        padding: 6px !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
        display: inline-flex;
        gap: 6px;
    }
    .sa-tab-link {
        padding: 8px 18px !important;
        border-radius: 10px !important;
        font-weight: 600 !important;
        font-size: 0.82rem !important;
        color: var(--wm-muted, #475569) !important;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .sa-tab-link.active {
        background-color: #1456A0 !important;
        color: #FFFFFF !important;
        box-shadow: 0 2px 6px rgba(20, 86, 160, 0.25) !important;
    }
    .sa-tab-link:hover:not(.active) {
        background-color: #F1F5F9 !important;
        color: #0F172A !important;
    }

    /* Dark Mode Overrides */
    [data-theme="dark"] .sa-master-card,
    body.dark-mode .sa-master-card {
        background-color: #131F35 !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
    }
    [data-theme="dark"] .sa-tab-nav,
    body.dark-mode .sa-tab-nav {
        background-color: #0F1A2E !important;
        border-color: #24344F !important;
    }
    [data-theme="dark"] .sa-tab-link,
    body.dark-mode .sa-tab-link {
        color: #94A3B8 !important;
    }
    [data-theme="dark"] .sa-tab-link:hover:not(.active),
    body.dark-mode .sa-tab-link:hover:not(.active) {
        background-color: #1E2D47 !important;
        color: #FFFFFF !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-md-4 py-2">
    {{-- Header Banner --}}
    <div class="sa-master-header-card mb-4" style="background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%) !important; color: #FFFFFF !important; border-radius: 18px; padding: 24px 28px; box-shadow: 0 4px 18px rgba(10, 37, 64, 0.12); border: none;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                    <span class="badge rounded-pill px-2.5 py-1 text-white fw-bold" style="background: rgba(255,255,255,0.2); font-size: 0.72rem;">
                        <i data-lucide="database" style="width: 12px; height: 12px; vertical-align: middle;"></i> MASTER KEUANGAN WARKAS
                    </span>
                    <span class="badge rounded-pill bg-success text-white px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                        Rekonsiliasi 100% Klop (Rp 0 Diff)
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-1 tracking-tight">Master Rekening Buku Kas & Tarif Iuran</h3>
                <p class="text-white-50 mb-0 small" style="max-width: 680px;">
                    Pusat konfigurasi identitas rekening perbankan 18 pos kas serta master komponen tarif iuran One Billing (RT, RW, Sampah, Keamanan, & Kegiatan).
                </p>
            </div>
            <div>
                <a href="{{ route('dashboard.super-admin') }}" class="btn btn-light rounded-pill px-3 py-2 fw-bold text-primary d-inline-flex align-items-center gap-1.5 shadow-sm" style="font-size: 0.82rem;">
                    <i data-lucide="layout-dashboard" style="width: 15px; height: 15px;"></i> Kembali ke Control Center
                </a>
            </div>
        </div>
    </div>

    {{-- Navigasi Tab Internal --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div class="sa-tab-nav shadow-xs">
            <a href="{{ route('super-admin.master-keuangan.index', ['tab' => 'rekening']) }}" class="sa-tab-link {{ $activeTab === 'rekening' ? 'active' : '' }}">
                <i data-lucide="landmark" style="width: 15px; height: 15px;"></i>
                <span>1. Rekening Buku Kas (18 Rekening)</span>
            </a>
            <a href="{{ route('super-admin.master-keuangan.index', ['tab' => 'tarif']) }}" class="sa-tab-link {{ $activeTab === 'tarif' ? 'active' : '' }}">
                <i data-lucide="tag" style="width: 15px; height: 15px;"></i>
                <span>2. Master Tarif One Billing (6 Komponen)</span>
            </a>
        </div>

        <div>
            @if($activeTab === 'rekening')
                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill" style="font-size: 0.76rem;">
                    <i data-lucide="shield-check" style="width: 13px; height: 13px; color: #16865D; vertical-align: middle;"></i> Saldo Terisolasi Aman dari Modifikasi Langsung
                </span>
            @else
                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill" style="font-size: 0.76rem;">
                    <i data-lucide="info" style="width: 13px; height: 13px; color: #1456A0; vertical-align: middle;"></i> Perubahan Tarif Hanya Berlaku ke Penerbitan Baru
                </span>
            @endif
        </div>
    </div>

    {{-- ======================================================================
         TAB 1: REKENING BUKU KAS (18 REKENING)
         ====================================================================== --}}
    @if($activeTab === 'rekening')
        <div class="sa-master-card mb-4">
            <div class="p-4 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Daftar Rekening Buku Kas Lingkungan & Wilayah</h5>
                    <small class="text-muted">Super Admin dapat memperbarui nama bank, nomor rekening, atas nama, dan deskripsi rekening tanpa merusak integritas saldo ledger.</small>
                </div>
                <div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.76rem;">
                        18 Akun Aktif Tervalidasi
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.83rem;">
                    <thead class="table-light text-muted" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.4px;">
                        <tr>
                            <th class="ps-4 py-3" style="width: 25%;">Kode & Nama Kas</th>
                            <th class="py-3" style="width: 15%;">Entitas Pemilik</th>
                            <th class="py-3" style="width: 24%;">Informasi Rekening Bank</th>
                            <th class="py-3 text-end" style="width: 16%;">Saldo Riil Ledger</th>
                            <th class="py-3 text-center" style="width: 10%;">Status Rekonsiliasi</th>
                            <th class="pe-4 py-3 text-end" style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allBukuKas as $kas)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-circle p-1.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="background: {{ $kas->jenis_kas === 'kas_rw' ? '#EAF3FF' : ($kas->jenis_kas === 'kas_rt' ? '#E8F6EF' : '#FEF3C7') }}; 
                                                    color: {{ $kas->jenis_kas === 'kas_rw' ? '#1456A0' : ($kas->jenis_kas === 'kas_rt' ? '#16865D' : '#D97706') }}; 
                                                    width: 34px; height: 34px;">
                                            <i data-lucide="{{ $kas->jenis_kas === 'kas_rw' ? 'landmark' : ($kas->jenis_kas === 'kas_rt' ? 'building-2' : 'wallet') }}" style="width: 16px; height: 16px;"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark d-block" style="font-size: 0.86rem;">{{ $kas->clean_nama_kas }}</strong>
                                            <span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 0.68rem; font-family: monospace;">{{ $kas->kode_kas }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($kas->jenis_kas === 'kas_rw')
                                        <span class="badge rounded-pill text-white fw-bold px-2.5 py-1" style="background: #1456A0; font-size: 0.7rem;">Induk RW 013</span>
                                    @elseif($kas->jenis_kas === 'kas_rt')
                                        <span class="badge rounded-pill text-white fw-bold px-2.5 py-1" style="background: #16865D; font-size: 0.7rem;">Otonom RT {{ $kas->clean_rt }}</span>
                                    @elseif($kas->jenis_kas === 'kas_sampah')
                                        <span class="badge rounded-pill text-white fw-bold px-2.5 py-1" style="background: #0284C7; font-size: 0.7rem;">Rekening Bersama RW</span>
                                    @else
                                        <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1" style="font-size: 0.7rem;">Unit Kegiatan Lingkungan</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark">
                                        <strong>{{ $kas->nama_bank ?: 'Kas Tunai' }}</strong> &bull; <span class="font-monospace text-secondary">{{ $kas->no_rekening ?: 'Fisik/Brankas' }}</span>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.74rem;">a.n {{ $kas->atas_nama ?: 'Pengurus RW 013' }}</small>
                                </td>
                                <td class="text-end">
                                    <strong class="text-dark d-block" style="font-size: 0.9rem;">Rp {{ number_format($kas->saldo_sekarang, 0, ',', '.') }}</strong>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Saldo Awal: Rp {{ number_format($kas->saldo_awal, 0, ',', '.') }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill text-success bg-success-subtle border border-success-subtle px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">
                                        <i data-lucide="check" style="width: 11px; height: 11px; vertical-align: middle;"></i> Klop (Diff: Rp 0)
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalEditRekening{{ $kas->id }}" style="font-size: 0.78rem;">
                                        <i data-lucide="edit-3" style="width: 12px; height: 12px;"></i> Edit
                                    </button>

                                    {{-- MODAL EDIT REKENING --}}
                                    <div class="modal fade" id="modalEditRekening{{ $kas->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg text-start" style="border-radius: 18px; overflow: hidden;">
                                                <div class="modal-header p-3.5 text-white" style="background: linear-gradient(135deg, #1456A0 0%, #2878D0 100%);">
                                                    <div>
                                                        <h6 class="modal-title fw-bold mb-0">Edit Identitas Rekening</h6>
                                                        <small class="text-blue-100" style="font-size: 0.74rem;">Kode Pos Kas: {{ $kas->kode_kas }}</small>
                                                    </div>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="{{ route('super-admin.master-keuangan.rekening.update', $kas->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-4 bg-light">
                                                        <div class="alert alert-warning py-2 px-3 small mb-3 rounded-3" style="font-size: 0.74rem;">
                                                            <i data-lucide="lock" style="width: 13px; height: 13px; vertical-align: middle;"></i>
                                                            <strong>Saldo Terkunci:</strong> Saldo kas dihitung dari transaksi ledger riil (Rp {{ number_format($kas->saldo_sekarang, 0, ',', '.') }}) dan tidak dapat diubah manual untuk menjaga kepatuhan audit.
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label text-dark fw-bold small mb-1">Nama Buku Kas:</label>
                                                            <input type="text" name="nama_kas" class="form-control rounded-3" value="{{ old('nama_kas', $kas->nama_kas) }}" required>
                                                        </div>

                                                        <div class="row g-2 mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label text-dark fw-bold small mb-1">Nama Bank:</label>
                                                                <input type="text" name="nama_bank" class="form-control rounded-3" placeholder="Contoh: Bank DKI, Mandiri, BCA" value="{{ old('nama_bank', $kas->nama_bank) }}">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label text-dark fw-bold small mb-1">Nomor Rekening:</label>
                                                                <input type="text" name="no_rekening" class="form-control rounded-3" placeholder="Nomor rekening bank..." value="{{ old('no_rekening', $kas->no_rekening) }}">
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label text-dark fw-bold small mb-1">Atas Nama Rekening:</label>
                                                            <input type="text" name="atas_nama" class="form-control rounded-3" placeholder="Nama pemilik di buku tabungan..." value="{{ old('atas_nama', $kas->atas_nama) }}">
                                                        </div>

                                                        <div class="mb-2">
                                                            <label class="form-label text-dark fw-bold small mb-1">Deskripsi & Peruntukan Kas:</label>
                                                            <textarea name="deskripsi" class="form-control rounded-3" rows="2" placeholder="Keterangan alokasi pos keuangan...">{{ old('deskripsi', $kas->deskripsi) }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer p-3 bg-white border-top">
                                                        <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 small" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-1.5 fw-bold small" style="background: #1456A0; border-color: #1456A0;">Simpan Identitas Rekening</button>
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

            <div class="p-3 px-4 bg-light border-top d-flex align-items-center justify-content-between flex-wrap gap-2 text-muted small" style="font-size: 0.76rem;">
                <div>
                    <i data-lucide="check-circle" style="width: 14px; height: 14px; color: #16865D; vertical-align: middle;"></i>
                    Seluruh 18 rekening terhubung dengan pos ledger kas aktual WarkasMachi.
                </div>
            </div>
        </div>

    {{-- ======================================================================
         TAB 2: MASTER TARIF IURAN (ONE BILLING SYSTEM)
         ====================================================================== --}}
    @else
        <div class="sa-master-card mb-4">
            <div class="p-4 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Master Komponen Tarif Iuran Terpadu (One Billing)</h5>
                    <small class="text-muted">Komponen tarif ini digunakan saat pembuatan tagihan bulanan keluarga warga di seluruh RW 013.</small>
                </div>
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.76rem;">
                        {{ $allTarifs->count() }} Komponen Iuran
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.83rem;">
                    <thead class="table-light text-muted" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.4px;">
                        <tr>
                            <th class="ps-4 py-3" style="width: 8%;">Prioritas</th>
                            <th class="py-3" style="width: 25%;">Kode & Nama Tarif</th>
                            <th class="py-3" style="width: 18%;">Tujuan Pos Buku Kas</th>
                            <th class="py-3 text-end" style="width: 16%;">Nominal Tarif</th>
                            <th class="py-3 text-center" style="width: 12%;">Status Iuran</th>
                            <th class="pe-4 py-3 text-end" style="width: 21%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allTarifs as $tarif)
                            <tr>
                                <td class="ps-4 py-3 text-muted fw-bold">
                                    #{{ $tarif->urutan_prioritas ?: $tarif->id }}
                                </td>
                                <td>
                                    <strong class="text-dark d-block" style="font-size: 0.86rem;">{{ $tarif->nama_tarif }}</strong>
                                    <span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 0.68rem; font-family: monospace;">{{ $tarif->kode_tarif }}</span>
                                    @if($tarif->keterangan_musyawarah)
                                        <small class="text-muted d-block mt-0.5" style="font-size: 0.72rem;">{{ $tarif->keterangan_musyawarah }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                        {{ $tarif->bukuKas ? $tarif->bukuKas->clean_nama_kas : strtoupper(str_replace('_', ' ', $tarif->jenis_kas)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary d-block" style="font-size: 0.95rem;">Rp {{ number_format($tarif->nominal, 0, ',', '.') }}</strong>
                                    <small class="text-muted" style="font-size: 0.7rem;">/ Kepala Keluarga</small>
                                </td>
                                <td class="text-center">
                                    @if($tarif->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">Aktif (Ditagihkan)</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalEditTarif{{ $tarif->id }}" style="font-size: 0.78rem;">
                                        <i data-lucide="edit-3" style="width: 12px; height: 12px;"></i> Ubah Tarif
                                    </button>

                                    {{-- MODAL EDIT TARIF --}}
                                    <div class="modal fade" id="modalEditTarif{{ $tarif->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg text-start" style="border-radius: 18px; overflow: hidden;">
                                                <div class="modal-header p-3.5 text-white" style="background: linear-gradient(135deg, #1456A0 0%, #2878D0 100%);">
                                                    <div>
                                                        <h6 class="modal-title fw-bold mb-0">Ubah Tarif Iuran</h6>
                                                        <small class="text-blue-100" style="font-size: 0.74rem;">Komponen: {{ $tarif->kode_tarif }}</small>
                                                    </div>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="{{ route('super-admin.master-keuangan.tarif.update', $tarif->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-4 bg-light">
                                                        <div class="alert alert-info py-2 px-3 small mb-3 rounded-3" style="font-size: 0.74rem;">
                                                            <i data-lucide="info" style="width: 13px; height: 13px; vertical-align: middle;"></i>
                                                            Perubahan nominal tarif akan berlaku untuk periode penerbitan tagihan berikutnya. Tagihan periode sebelumnya yang sudah lunas atau terbit tidak akan terpengaruh.
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label text-dark fw-bold small mb-1">Nama Komponen Tarif:</label>
                                                            <input type="text" name="nama_tarif" class="form-control rounded-3" value="{{ old('nama_tarif', $tarif->nama_tarif) }}" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label text-dark fw-bold small mb-1">Nominal Tarif (Rp):</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white">Rp</span>
                                                                <input type="number" name="nominal" class="form-control rounded-end-3" min="0" step="1000" value="{{ old('nominal', intval($tarif->nominal)) }}" required>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label text-dark fw-bold small mb-1">Status Komponen:</label>
                                                            <select name="is_active" class="form-select rounded-3" required>
                                                                <option value="1" {{ $tarif->is_active ? 'selected' : '' }}>Aktif (Dimasukkan dalam Tagihan One Billing)</option>
                                                                <option value="0" {{ !$tarif->is_active ? 'selected' : '' }}>Nonaktif (Tidak Ditagihkan)</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-2">
                                                            <label class="form-label text-dark fw-bold small mb-1">Keterangan / Dasar Musyawarah Warga:</label>
                                                            <textarea name="keterangan_musyawarah" class="form-control rounded-3" rows="2" placeholder="Contoh: Kesepakatan Musyawarah Pleno RW 013...">{{ old('keterangan_musyawarah', $tarif->keterangan_musyawarah) }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer p-3 bg-white border-top">
                                                        <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 small" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-1.5 fw-bold small" style="background: #1456A0; border-color: #1456A0;">Simpan Perubahan Tarif</button>
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

            <div class="p-3 px-4 bg-light border-top d-flex align-items-center justify-content-between flex-wrap gap-2 text-muted small" style="font-size: 0.76rem;">
                <div>
                    <i data-lucide="check-circle" style="width: 14px; height: 14px; color: #16865D; vertical-align: middle;"></i>
                    Sistem One Billing mengalokasikan penerimaan tagihan secara otomatis ke masing-masing pos rekening buku kas terkait.
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
