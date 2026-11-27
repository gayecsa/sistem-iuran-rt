@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- Header & Breadcrumb (Disembunyikan saat mencetak) --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 d-print-none">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-icon-pill shadow-sm" style="width: 48px; height: 48px; border-radius: 14px; background: #EAF3FF; color: #1456A0; font-size: 20px;">
                <i data-lucide="bar-chart-3" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="color: var(--wm-text) !important;">Laporan Demografi & Kependudukan RW 013</h3>
                <p class="text-muted mb-0 small">Analitik data kependudukan, sebaran kelompok usia, gender, status kartu keluarga, dan profil wilayah RT 001 - RT 008.</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="window.print()">
                <i data-lucide="printer" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Cetak Laporan Resmi
            </button>
            <a href="{{ route('kependudukan.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px; vertical-align: middle;"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Filter Bar (Disembunyikan saat cetak) --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 d-print-none" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <form action="{{ route('kependudukan.laporan') }}" method="GET" class="row g-2 align-items-center mb-0">
            @if(!auth()->user()->isRt())
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Wilayah RT</label>
                    <select name="rt" class="form-select rounded-3">
                        <option value="all" {{ ($selectedRt === 'all' || !$selectedRt) ? 'selected' : '' }}>Semua RT (RT 001 - RT 008)</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ (string)$selectedRt === (string)$i ? 'selected' : '' }}>
                                Wilayah RT {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}
                            </option>
                        @endfor
                    </select>
                </div>
            @endif

            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Bulan</label>
                <select name="bulan" class="form-select rounded-3">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Tahun</label>
                <select name="tahun" class="form-select rounded-3">
                    @for($y = 2024; $y <= 2027; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-12 col-md-auto ms-auto d-flex gap-2 align-self-end mt-2 mt-md-0">
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold btn-sm">
                    <i data-lucide="filter" style="width: 14px; height: 14px; vertical-align: middle;"></i> Tampilkan
                </button>
                @if(request()->hasAny(['rt', 'bulan', 'tahun']))
                    <a href="{{ route('kependudukan.laporan') }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- KOP SURAT RESMI (Hanya Tampil Saat Cetak / Dokumen Resmi) --}}
    <div class="d-none d-print-block text-center border-bottom pb-3 mb-4">
        <h4 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px;">RUKUN WARGA 013</h4>
        <h5 class="fw-bold mb-1 text-uppercase">KELURAHAN Suryatmajan, KECAMATAN Danurejan, Kota Yogyakarta</h5>
        <p class="mb-0 text-muted small">Sekretariat: Balai RW 013, Jl. Mataram No. 15, Suryatmajan, Danurejan, Yogyakarta 55213</p>
        <div style="border-top: 2px solid #000; border-bottom: 1px solid #000; height: 4px; margin-top: 8px;"></div>
        <div class="text-center mt-3">
            <h5 class="fw-bold text-uppercase mb-0">LAPORAN REKAPITULASI DATA KEPENDUDUKAN</h5>
            <small class="text-muted">
                Periode: {{ Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }} | 
                Wilayah: {{ $selectedRt && $selectedRt !== 'all' ? 'RT ' . str_pad($selectedRt, 3, '0', STR_PAD_LEFT) : 'Seluruh RW 013 (RT 001 - RT 008)' }}
            </small>
        </div>
    </div>

    {{-- 1. METRIK UTAMA KEPENDUDUKAN --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 5px solid #1456A0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Populasi</span>
                    <div class="p-2 rounded-circle" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="users" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ number_format($totalWarga) }} <span class="fs-6 fw-normal text-muted">Jiwa</span></h3>
                <small class="text-muted">Warga terdaftar di database</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 5px solid #10B981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Kepala Keluarga / KK</span>
                    <div class="p-2 rounded-circle" style="background: #E8F5E9; color: #10B981;">
                        <i data-lucide="home" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-1">{{ number_format($totalKk) }} <span class="fs-6 fw-normal text-muted">KK</span></h3>
                <small class="text-muted">Rata-rata {{ $totalKk > 0 ? round($totalWarga / $totalKk, 1) : 0 }} jiwa / KK</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 5px solid #2878D0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Gender Warga</span>
                    <div class="p-2 rounded-circle" style="background: #EAF3FF; color: #2878D0;">
                        <i data-lucide="user" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-1">
                    <span class="text-primary">{{ number_format($totalLaki) }} L</span> / <span style="color: #EC4899;">{{ number_format($totalPerempuan) }} P</span>
                </h4>
                <small class="text-muted">L: {{ $totalWarga > 0 ? round(($totalLaki / $totalWarga) * 100) : 0 }}% | P: {{ $totalWarga > 0 ? round(($totalPerempuan / $totalWarga) * 100) : 0 }}%</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border-left: 5px solid #059669 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Validitas KK Dukcapil</span>
                    <div class="p-2 rounded-circle" style="background: #D1FAE5; color: #059669;">
                        <i data-lucide="check-circle" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-1">
                    <span class="text-success">{{ number_format($kkTerverifikasi) }} Sah</span>
                </h4>
                <small class="text-muted">Menunggu: {{ $kkMenunggu }} | Belum Terverifikasi: {{ $kkBelumTerverifikasi }}</small>
            </div>
        </div>
    </div>

    {{-- 2. STATUS DOMISILI, WARGA MASUK & KELUAR --}}
    <div class="row g-4 mb-4">
        {{-- Status Domisili & Mutasi --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <i data-lucide="arrow-left-right" style="width: 20px; height: 20px; color: #16865D;"></i>
                    <h6 class="fw-bold mb-0 text-dark">Status Kependudukan & Dinamika Warga</h6>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <span class="text-muted small d-block mb-1">Warga Tetap</span>
                            <h4 class="fw-bold text-dark mb-0">{{ number_format($wargaTetapCount) }}</h4>
                            <small class="text-success fw-semibold">Domisili Resmi</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <span class="text-muted small d-block mb-1">Warga Sementara / Kontrak</span>
                            <h4 class="fw-bold text-primary mb-0">{{ number_format($wargaSementaraCount) }}</h4>
                            <small class="text-muted">Domisili Musiman</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <span class="text-muted small d-block mb-1">Pendatang Baru</span>
                            <h4 class="fw-bold text-info mb-0">{{ number_format($wargaPendatangCount) }}</h4>
                            <small class="text-muted">Lapor Masuk</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <span class="text-muted small d-block mb-1">Pindah / Mutasi Keluar</span>
                            <h4 class="fw-bold text-secondary mb-0">{{ number_format($wargaMutasiCount) }}</h4>
                            <small class="text-muted">Tercatat Keluar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Piramida / Distribusi Kelompok Usia --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <i data-lucide="pie-chart" style="width: 20px; height: 20px; color: #1456A0;"></i>
                    <h6 class="fw-bold mb-0 text-dark">Distribusi Kelompok Usia</h6>
                </div>

                @php
                    $ageCategories = [
                        ['label' => 'Balita (0 - 5 Tahun)', 'count' => $usiaBalita, 'color' => '#3B82F6'],
                        ['label' => 'Anak-anak (6 - 12 Tahun)', 'count' => $usiaAnak, 'color' => '#10B981'],
                        ['label' => 'Remaja (13 - 19 Tahun)', 'count' => $usiaRemaja, 'color' => '#F59E0B'],
                        ['label' => 'Dewasa (20 - 55 Tahun)', 'count' => $usiaDewasa, 'color' => '#1456A0'],
                        ['label' => 'Lansia (56+ Tahun)', 'count' => $usiaLansia, 'color' => '#8B5CF6'],
                    ];
                @endphp

                <div class="d-flex flex-column gap-2">
                    @foreach($ageCategories as $cat)
                        @php $pct = $totalWarga > 0 ? round(($cat['count'] / $totalWarga) * 100, 1) : 0; @endphp
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1 small">
                                <span class="fw-semibold text-dark">{{ $cat['label'] }}</span>
                                <span class="fw-bold text-dark">{{ $cat['count'] }} Jiwa ({{ $pct }}%)</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 6px; background: #F1F5F9;">
                                <div class="progress-bar" style="width: {{ $pct }}%; background: {{ $cat['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- 3. TABEL REKAPITULASI PER RT (RT 001 - RT 008) --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <div class="px-4 py-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">Rekapitulasi Kependudukan Wilayah RT 001 sampai RT 008</h6>
            <span class="badge rounded-pill bg-white text-dark border px-3 py-1 fw-bold small">RW 013 Suryatmajan</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4">Wilayah RT</th>
                        <th>Total Warga</th>
                        <th>Total KK</th>
                        <th>Laki-Laki</th>
                        <th>Perempuan</th>
                        <th>KK Terverifikasi</th>
                        <th>KK Belum Sah</th>
                        <th class="pe-4 text-center">Validitas KK</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapPerRt as $r)
                        @php
                            $validPct = $r['total_kk'] > 0 ? round(($r['kk_terverifikasi'] / $r['total_kk']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="ps-4 fw-bold text-dark small">
                                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #EAF3FF; color: #1456A0;">
                                    {{ $r['rt'] }}
                                </span>
                            </td>
                            <td class="fw-bold text-dark small">{{ number_format($r['total_warga']) }} Jiwa</td>
                            <td class="text-muted small">{{ number_format($r['total_kk']) }} KK</td>
                            <td class="text-muted small">{{ number_format($r['laki']) }}</td>
                            <td class="text-muted small">{{ number_format($r['perempuan']) }}</td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #E3F5EC; color: #16865D; font-size: 0.72rem;">
                                    {{ number_format($r['kk_terverifikasi']) }} Terverifikasi
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1 fw-semibold text-secondary bg-light border" style="font-size: 0.72rem;">
                                    {{ number_format($r['kk_belum']) }} Belum
                                </span>
                            </td>
                            <td class="pe-4 text-center">
                                <span class="fw-bold small {{ $validPct >= 80 ? 'text-success' : ($validPct >= 50 ? 'text-primary' : 'text-warning') }}">{{ $validPct }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. REKAP PERUBAHAN DATA WARGA TERBARU (AUDIT LOG KEPENDUDUKAN) --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 d-print-none" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
        <div class="px-4 py-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-0 text-dark">Log Audit Rekapitulasi Perubahan Data Warga Terbaru</h6>
                <small class="text-muted">Aktivitas penambahan, mutasi keluarga, pergantian kepala keluarga, dan verifikasi.</small>
            </div>
            <a href="{{ route('kependudukan.riwayat') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                Lihat Seluruh Log &rarr;
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase">
                        <th class="ps-4">Waktu</th>
                        <th>Nama Warga</th>
                        <th>Tipe Perubahan</th>
                        <th>Status Sebelum & Sesudah</th>
                        <th>Petugas Pelaksana</th>
                        <th class="pe-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapPerubahan as $h)
                        <tr>
                            <td class="ps-4 text-muted">
                                {{ $h->created_at ? $h->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td>
                                <strong class="text-dark">{{ $h->user->name ?? ($h->metadata['warga_nama'] ?? 'Warga') }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border">
                                    {{ $h->tipe_perubahan }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $h->status_lama }}</span> &rarr; <strong class="text-success">{{ $h->status_baru }}</strong>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $h->petugas_nama ?: 'Sistem' }}</span>
                                <small class="text-muted d-block" style="font-size: 10px;">{{ strtoupper($h->petugas_role ?: '-') }}</small>
                            </td>
                            <td class="pe-4 text-secondary">
                                {{ Str::limit($h->catatan, 40) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat perubahan data kependudukan tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TANDA TANGAN CETAK RESMI (Hanya Tampil Saat Cetak) --}}
    <div class="d-none d-print-block mt-5 pt-4">
        <div class="row">
            <div class="col-6 text-center">
                <p class="mb-1 text-muted small">Mengetahui,</p>
                <p class="fw-bold mb-5 text-dark">Ketua RW 013 Suryatmajan Danurejan</p>
                <p class="fw-bold text-dark mb-0 text-decoration-underline">( {{ \App\Models\User::where('role', 'rw')->value('name') ?? (Auth::user()->role === 'rw' ? Auth::user()->name : 'Ketua RW 013') }} )</p>
                <small class="text-muted">Ketua RW 013</small>
            </div>
            <div class="col-6 text-center">
                <p class="mb-1 text-muted small">Yogyakarta, {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="fw-bold mb-5 text-dark">Petugas Administrasi Kependudukan</p>
                <p class="fw-bold text-dark mb-0 text-decoration-underline">{{ $user->name }}</p>
                <small class="text-muted">ID Petugas: #{{ $user->id }} ({{ strtoupper($user->role) }})</small>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body {
            background: #fff !important;
            color: #000 !important;
        }
        .navbar, .sidebar, .wm-header, footer, .d-print-none {
            display: none !important;
        }
        .card {
            border: 1px solid #ccc !important;
            box-shadow: none !important;
        }
        @page {
            size: A4 portrait;
            margin: 1.2cm;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
