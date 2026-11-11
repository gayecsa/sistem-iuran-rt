@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('bank-sampah') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3 fw-semibold">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px; vertical-align: middle;"></i> Portal
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Buku Rekening Tabungan Sampah Digital</h4>
                <small class="text-muted">Pemilik Rekening: <strong class="text-dark">{{ $user->name }}</strong> (RT {{ str_pad($user->rt_number, 3, '0', STR_PAD_LEFT) }})</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSetorSampah">
                <i data-lucide="plus-circle" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Setor Sampah
            </button>
        </div>
    </div>

    <!-- Tiga Kartu Ringkasan Saldo -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="wm-card p-4 bg-white border rounded-4 shadow-sm h-100 position-relative overflow-hidden" style="border-left: 5px solid #10B981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Saldo Tabungan Aktif</span>
                    <div class="p-2 rounded-circle bg-emerald-50 text-success" style="background: #E8F5E9;">
                        <i data-lucide="wallet" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-1">{{ $saldo->saldo_aktif_formatted }}</h3>
                <small class="text-muted">Dapat dicairkan menjelang hari raya atau dialihkan untuk iuran RT.</small>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="wm-card p-4 bg-white border rounded-4 shadow-sm h-100 position-relative overflow-hidden" style="border-left: 5px solid #3B82F6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Total Sampah Terkumpul</span>
                    <div class="p-2 rounded-circle bg-sky-50 text-primary" style="background: #E1F5FE;">
                        <i data-lucide="scale" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ number_format($saldo->total_kg_terkumpul, 2, ',', '.') }} <span class="fs-6 fw-normal text-muted">kg</span></h3>
                <small class="text-muted">Kontribusi nyata pencegahan sampah ke TPA Sarimukti.</small>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="wm-card p-4 bg-white border rounded-4 shadow-sm h-100 position-relative overflow-hidden" style="border-left: 5px solid #F59E0B !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Total Hasil Yang Pernah Didapat</span>
                    <div class="p-2 rounded-circle bg-amber-50 text-warning" style="background: #FFF8E1;">
                        <i data-lucide="coins" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $saldo->total_dihasilkan_formatted }}</h3>
                <small class="text-muted">Akumulasi seluruh nilai konversi sampah yang disetor.</small>
            </div>
        </div>
    </div>

    <!-- Mutasi Buku Rekening (Ledger) -->
    <div class="wm-card bg-white border rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i data-lucide="book-open" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                Buku Mutasi Kas Tabungan Sampah (Ledger Resmi)
            </h5>
            <span class="badge bg-secondary-subtle text-secondary small px-3 py-1 rounded-pill">
                Pencatatan Otomatis & Terverifikasi
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tanggal & Waktu</th>
                        <th>Keterangan Transaksi</th>
                        <th>Tipe</th>
                        <th class="text-end">Masuk (Debit)</th>
                        <th class="text-end">Keluar (Kredit)</th>
                        <th class="text-end">Saldo Akhir</th>
                        <th class="pe-4 text-end">Petugas Verifikator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgers as $ledger)
                        <tr>
                            <td class="ps-4">
                                <small class="text-dark fw-semibold d-block">{{ $ledger->created_at->format('d/m/Y') }}</small>
                                <small class="text-muted" style="font-size: 11px;">{{ $ledger->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $ledger->keterangan }}</span>
                                @if($ledger->transaksi_id)
                                    <small class="text-muted d-block">
                                        <a href="{{ route('bank-sampah.show', $ledger->transaksi_id) }}" class="text-decoration-none">
                                            Lihat Bukti Resi &rarr;
                                        </a>
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($ledger->tipe === 'setoran')
                                    <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-1 rounded-pill small" style="background: #E8F5E9; color: #1B5E20;">
                                        + Setoran
                                    </span>
                                @elseif($ledger->tipe === 'penarikan')
                                    <span class="badge bg-rose-100 text-rose-800 border border-rose-300 px-2 py-1 rounded-pill small">
                                        - Penarikan
                                    </span>
                                @else
                                    <span class="badge bg-amber-100 text-amber-800 border border-amber-300 px-2 py-1 rounded-pill small">
                                        &bull; Koreksi
                                    </span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-success">
                                {{ $ledger->debit > 0 ? '+ Rp ' . number_format($ledger->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-bold text-danger">
                                {{ $ledger->kredit > 0 ? '- Rp ' . number_format($ledger->kredit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-bold text-dark">
                                Rp {{ number_format($ledger->saldo_sesudah, 0, ',', '.') }}
                            </td>
                            <td class="pe-4 text-end">
                                <small class="text-muted">{{ $ledger->petugas->name ?? 'Sistem' }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i data-lucide="book" style="width: 40px; height: 40px;" class="mb-2 text-secondary opacity-50 d-block mx-auto"></i>
                                Belum ada catatan mutasi tabungan sampah. Setor sampah terpilah untuk mulai menabung!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ledgers->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $ledgers->links() }}
            </div>
        @endif
    </div>

    <!-- Riwayat Transaksi Pengajuan Saya -->
    <div class="wm-card bg-white border rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="p-4 border-bottom bg-light">
            <h5 class="fw-bold mb-0 text-dark">
                <i data-lucide="list" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                Daftar Riwayat Pengajuan & Penimbangan Saya
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kode Transaksi</th>
                        <th>Unit Bank Sampah</th>
                        <th>Berat Timbang</th>
                        <th>Nilai Tabungan</th>
                        <th>Status</th>
                        <th>Waktu Diajukan</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $tx)
                        <tr>
                            <td class="ps-4">
                                <strong class="text-dark">{{ $tx->kode_transaksi }}</strong>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $tx->bankSampah->nama_unit ?? '-' }}</span>
                                <small class="text-muted d-block">RT {{ str_pad($tx->rt_number, 3, '0', STR_PAD_LEFT) }}</small>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $tx->total_berat_formatted }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ $tx->total_rupiah_formatted }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $tx->status_badge['bg'] }} border px-2 py-1 rounded-pill fw-bold">
                                    {{ $tx->status_label }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $tx->tanggal_pengajuan->format('d/m/Y H:i') }}</small>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('bank-sampah.show', $tx->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                    Detail / Resi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada transaksi setoran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $transaksis->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
