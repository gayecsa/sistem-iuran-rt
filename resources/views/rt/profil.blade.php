@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i data-lucide="landmark" class="text-primary me-2" style="width: 26px; height: 26px;"></i>
                Profil Wilayah & Pengurus {{ $rtLabel }}
            </h4>
            <p class="text-muted small mb-0">
                Informasi legalitas, struktur kepengurusan, kontak sekretariat, dan fasilitas pos ronda di wilayah {{ $rtLabel }} / RW 013.
            </p>
        </div>
        <div>
            <a href="{{ route('dashboard.rt') }}" class="wm-btn-outline">
                <i data-lucide="arrow-left" style="width: 17px; height: 17px;"></i> Dashboard RT
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kolom Kiri: Identitas & Struktur Pengurus --}}
        <div class="col-lg-6">
            <div class="wm-card mb-4" style="border-left: 5px solid var(--wm-primary);">
                <div class="wm-card-header">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="users" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                        Struktur Pengurus {{ $rtLabel }} Periode 2024 - 2029
                    </h5>
                </div>
                <div class="wm-card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="small text-muted text-uppercase fw-bold">Ketua RT</span>
                                <h6 class="fw-bold text-dark mb-0">{{ $rtInfo['ketua_rt'] ?? $user->name }}</h6>
                            </div>
                            <span class="badge bg-primary">Penanggung Jawab</span>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="small text-muted text-uppercase fw-bold">Sekretaris RT</span>
                                <h6 class="fw-bold text-dark mb-0">{{ $rtInfo['sekretaris'] ?? 'Sekretaris RT' }}</h6>
                            </div>
                            <span class="badge bg-light text-dark border">Administrasi</span>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="small text-muted text-uppercase fw-bold">Bendahara RT</span>
                                <h6 class="fw-bold text-dark mb-0">{{ $rtInfo['bendahara'] ?? 'Bendahara RT' }}</h6>
                            </div>
                            <span class="badge bg-light text-dark border">Keuangan Kas</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wm-card" style="border-left: 5px solid var(--wm-green);">
                <div class="wm-card-header">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="phone-call" class="text-success me-2" style="width: 20px; height: 20px;"></i>
                        Kontak & Sekretariat RT
                    </h5>
                </div>
                <div class="wm-card-body">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold">Alamat Pos Ronda & Sekretariat:</label>
                        <p class="text-dark fw-semibold mb-0">{{ $rtInfo['alamat_pos'] ?? ('Sekretariat ' . $rtLabel . ' / RW 013') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold">Nomor Kontak / WhatsApp Resmi:</label>
                        <p class="text-dark fw-semibold mb-0">{{ $rtInfo['kontak'] ?? ($user->phone ?? '0812-XXXX-XXXX') }}</p>
                    </div>
                    <div>
                        <label class="small text-muted fw-bold">Wilayah Koordinasi:</label>
                        <p class="text-dark fw-semibold mb-0">Rukun Warga 013 &bull; Kelurahan Machi</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Cakupan Statistik & Batas Wilayah --}}
        <div class="col-lg-6">
            <div class="wm-card mb-4">
                <div class="wm-card-header">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="bar-chart-2" class="text-warning me-2" style="width: 20px; height: 20px;"></i>
                        Cakupan Kependudukan & Wilayah
                    </h5>
                </div>
                <div class="wm-card-body">
                    <div class="row g-3 text-center mb-3">
                        <div class="col-4">
                            <div class="p-3 rounded bg-light border">
                                <span class="small text-muted">Kepala Keluarga</span>
                                <h4 class="fw-bold text-primary mb-0 mt-1">{{ $rtInfo['jumlah_kk'] ?? 45 }}</h4>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded bg-light border">
                                <span class="small text-muted">Total Warga</span>
                                <h4 class="fw-bold text-success mb-0 mt-1">{{ $rtInfo['jumlah_warga'] ?? 142 }}</h4>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded bg-light border">
                                <span class="small text-muted">Skor Kinerja</span>
                                <h4 class="fw-bold text-warning mb-0 mt-1">{{ $rtInfo['skor_kinerja'] ?? 85 }}</h4>
                            </div>
                        </div>
                    </div>
                    <p class="small text-muted mb-0">
                        Data statistik diperbarui secara berkala melalui sinkronisasi sensus kependudukan Warkas Machi dan laporan bulanan RT ke RW.
                    </p>
                </div>
            </div>

            <div class="wm-card">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="map" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                        Peta Wilayah Lingkungan {{ $rtLabel }}
                    </h5>
                    <a href="{{ route('peta.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:0.75rem;">
                        Buka Peta RW
                    </a>
                </div>
                <div class="wm-card-body">
                    <div class="p-4 rounded border text-center" style="background: #F4F7FB;">
                        <i data-lucide="map-pin" style="width: 42px; height: 42px; color: var(--wm-primary); margin-bottom: 8px;"></i>
                        <h6 class="fw-bold text-dark mb-1">Zona Wilayah {{ $rtLabel }}</h6>
                        <p class="small text-muted mb-3">Terhubung dengan jaringan siskamling dan CCTV komunal RW 013.</p>
                        <a href="{{ route('peta.index') }}" class="wm-btn-primary" style="padding: 6px 14px; font-size: 0.8rem;">
                            <i data-lucide="compass" style="width: 14px; height: 14px;"></i> Lihat Peta Interaktif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
