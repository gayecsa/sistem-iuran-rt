@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Halaman Audit & Monitoring RT -->
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #123766 0%, #1A4D8C 100%); color: #fff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-success text-white mb-2 fw-bold px-3 py-1 rounded-pill">MANAJEMEN RT / RW 013</span>
                <h3 class="fw-bold mb-1 text-white">Audit & Monitoring 8 RT Wilayah RW 013</h3>
                <p class="mb-0 text-white opacity-75 small">
                    Pusat koordinasi evaluasi bulanan administrasi, keuangan, sosial, kebersihan, dan keamanan lingkungan RT 001 s/d RT 008.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-3 fw-bold small text-primary">
                    <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i> Ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Interaktif: Berdasarkan RT & Periode Laporan -->
    <div class="wm-card mb-4">
        <form method="GET" action="{{ route('monitoring-rt.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark small">Filter Wilayah RT</label>
                <select name="rt" class="form-select rounded-3">
                    <option value="all">Semua Wilayah RT (RT 001 - RT 008)</option>
                    @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ ($filterRt == $i) ? 'selected' : '' }}>
                            RT 00{{ $i }} - RW 013
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark small">Periode Laporan</label>
                <select name="periode" class="form-select rounded-3">
                    <option value="November 2026" {{ ($filterPeriode == 'November 2026') ? 'selected' : '' }}>November 2026 (Bulan Berjalan)</option>
                    <option value="Oktober 2026" {{ ($filterPeriode == 'Oktober 2026') ? 'selected' : '' }}>Oktober 2026</option>
                    <option value="September 2026" {{ ($filterPeriode == 'September 2026') ? 'selected' : '' }}>September 2026</option>
                    <option value="Triwulan 3 2026" {{ ($filterPeriode == 'Triwulan 3 2026') ? 'selected' : '' }}>Triwulan 3 (Juli - September 2026)</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="wm-btn-primary flex-grow-1 justify-content-center">
                    <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Terapkan Filter
                </button>
                <a href="{{ route('monitoring-rt.index') }}" class="btn btn-outline-secondary rounded-3 px-3 d-flex align-items-center" title="Reset Filter">
                    <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Ringkasan Status 4 Kategori -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="p-3 rounded-4 border bg-white shadow-sm d-flex align-items-center gap-3">
                <div class="p-3 rounded-3" style="background:#E3F5EC;color:#16865D;">
                    <i data-lucide="check-circle-2" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-success">3 RT</h4>
                    <span class="small text-muted fw-semibold">Status: Lengkap</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 rounded-4 border bg-white shadow-sm d-flex align-items-center gap-3">
                <div class="p-3 rounded-3" style="background:#FEF3C7;color:#D97706;">
                    <i data-lucide="alert-circle" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-warning">2 RT</h4>
                    <span class="small text-muted fw-semibold">Perlu Diperiksa</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 rounded-4 border bg-white shadow-sm d-flex align-items-center gap-3">
                <div class="p-3 rounded-3" style="background:#FEE2E2;color:#DC2626;">
                    <i data-lucide="alert-triangle" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-danger">2 RT</h4>
                    <span class="small text-muted fw-semibold">Perlu Tindak Lanjut</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 rounded-4 border bg-white shadow-sm d-flex align-items-center gap-3">
                <div class="p-3 rounded-3" style="background:#F1F5F9;color:#64748B;">
                    <i data-lucide="clock" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-secondary">1 RT</h4>
                    <span class="small text-muted fw-semibold">Belum Dilaporkan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Kartu Audit 8 RT -->
    <div class="row g-4">
        @foreach($rtMonitoringList ?? [] as $rt)
            <div class="col-xl-6 col-12">
                <div class="wm-card h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-primary text-white p-3 rounded-4 fw-bold fs-5" style="width: 52px; height: 52px; display: inline-flex; align-items: center; justify-content: center;">
                                {{ $rt['nomor'] }}
                            </span>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">{{ $rt['nama'] }} RW 013</h5>
                                <span class="text-muted small">Ketua RT: <strong>{{ $rt['ketua_rt'] }}</strong> ({{ $rt['kontak'] }})</span>
                            </div>
                        </div>
                        <div>
                            @if($rt['status_laporan'] === 'Lengkap')
                                <span class="status-badge status-lengkap"><i data-lucide="check-circle-2" style="width: 14px; height: 14px;"></i> Lengkap</span>
                            @elseif($rt['status_laporan'] === 'Perlu diperiksa')
                                <span class="status-badge status-periksa"><i data-lucide="alert-circle" style="width: 14px; height: 14px;"></i> Perlu diperiksa</span>
                            @elseif($rt['status_laporan'] === 'Belum dilaporkan')
                                <span class="status-badge status-belum"><i data-lucide="clock" style="width: 14px; height: 14px;"></i> Belum dilaporkan</span>
                            @else
                                <span class="status-badge status-tindak-lanjut"><i data-lucide="alert-triangle" style="width: 14px; height: 14px;"></i> Perlu tindak lanjut</span>
                            @endif
                        </div>
                    </div>

                    <!-- Indikator 4 Pilar Monitoring -->
                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <div class="p-2 rounded-3 bg-light border small">
                                <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 700;">1. ADMINISTRASI</span>
                                <strong class="text-dark">{{ $rt['administrasi'] }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-2 rounded-3 bg-light border small">
                                <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 700;">2. KEUANGAN & KAS RT</span>
                                <strong class="text-dark">{{ $rt['keuangan'] }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-2 rounded-3 bg-light border small">
                                <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 700;">3. KEGIATAN SOSIAL</span>
                                <strong class="text-dark">{{ $rt['sosial'] }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-2 rounded-3 bg-light border small">
                                <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 700;">4. KEBERSIHAN & KEAMANAN</span>
                                <strong class="text-dark">{{ $rt['kebersihan_keamanan'] }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Evaluasi RW & Rekomendasi Perbaikan -->
                    <div class="p-3 rounded-4 bg-light border mb-3">
                        <div class="mb-2">
                            <strong class="text-dark small d-block mb-1"><i data-lucide="clipboard-edit" class="text-primary me-1" style="width: 15px; height: 15px; vertical-align: middle;"></i>Catatan Evaluasi RW:</strong>
                            <p class="text-muted small mb-0">{{ $rt['evaluasi_rw'] }}</p>
                        </div>
                        <div class="pt-2 border-top">
                            <strong class="text-dark small d-block mb-1"><i data-lucide="lightbulb" class="text-warning me-1" style="width: 15px; height: 15px; vertical-align: middle;"></i>Rekomendasi Perbaikan:</strong>
                            <p class="text-secondary small mb-0">{{ $rt['rekomendasi'] }}</p>
                        </div>
                    </div>

                    <!-- Footer Kartu: Riwayat & Tombol Aksi -->
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="small text-muted"><i class="far fa-calendar-alt me-1"></i>Update: {{ $rt['terakhir_update'] }}</span>
                        <a href="{{ route('monitoring-rt.show', $rt['rt']) }}" class="wm-btn-primary" style="padding: 6px 16px; font-size: 0.82rem;">
                            Buka Detail & Beri Evaluasi <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
