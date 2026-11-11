@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #16865D 0%, #123766 100%); color: #fff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-light text-success mb-2 fw-bold px-3 py-1 rounded-pill">LINGKUNGAN HIJAU & SEJAHTERA</span>
                <h3 class="fw-bold mb-1 text-white">Program Bank Sampah Terpadu RW 013</h3>
                <p class="mb-0 text-white opacity-75 small">
                    Pengelolaan sampah terpilah (Plastik, Kertas, Logam, Minyak Jelantah) untuk tabungan warga dan kas lingkungan 8 RT.
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-3 fw-bold small text-success">
                <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i> Ke Dashboard
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="wm-card text-center p-3 h-100">
                <div class="wm-stat-icon-wrap mx-auto mb-2" style="background: #E3F5EC; color: #16865D;">
                    <i data-lucide="calendar"></i>
                </div>
                <h6 class="fw-bold text-dark">Jadwal Setor Sampah</h6>
                <p class="text-success fw-bold mb-1">Setiap Hari Rabu</p>
                <small class="text-muted">Pukul 10.00 - 13.00 WIB</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="wm-card text-center p-3 h-100">
                <div class="wm-stat-icon-wrap mx-auto mb-2" style="background: #EAF3FF; color: #1456A0;">
                    <i data-lucide="map-pin"></i>
                </div>
                <h6 class="fw-bold text-dark">Pusat Penimbangan</h6>
                <p class="text-primary fw-bold mb-1">Pusat Bank Sampah RT 003</p>
                <small class="text-muted">Pos Penampungan Komunal RW 013</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="wm-card text-center p-3 h-100">
                <div class="wm-stat-icon-wrap mx-auto mb-2" style="background: #FEF3C7; color: #D97706;">
                    <i data-lucide="recycle"></i>
                </div>
                <h6 class="fw-bold text-dark">Jenis Sampah Diterima</h6>
                <p class="text-warning fw-bold mb-1">Plastik, Kertas, Kardus, Logam</p>
                <small class="text-muted">Dalam kondisi bersih & kering</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="wm-card h-100">
                <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                    <i data-lucide="award" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Keuntungan & Manfaat Bagi Warga RW 013
                </h5>
                <ul class="list-group list-group-flush border-0">
                    <li class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                        <i data-lucide="check-circle" class="text-success mt-1" style="width: 20px; height: 20px;"></i>
                        <div>
                            <strong class="text-dark">Lingkungan Bersih & Bebas Sarang Nyamuk</strong>
                            <p class="text-muted small mb-0">Mencegah timbunan barang bekas yang berpotensi menjadi sarang jentik nyamuk demam berdarah.</p>
                        </div>
                    </li>
                    <li class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                        <i data-lucide="wallet" class="text-primary mt-1" style="width: 20px; height: 20px;"></i>
                        <div>
                            <strong class="text-dark">Menjadi Tabungan Warga & Kas Lingkungan</strong>
                            <p class="text-muted small mb-0">Setiap kilogram sampah yang disetor dicatat ke buku tabungan sampah warga dan dapat dicairkan menjelang hari raya.</p>
                        </div>
                    </li>
                    <li class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                        <i data-lucide="sparkles" class="text-warning mt-1" style="width: 20px; height: 20px;"></i>
                        <div>
                            <strong class="text-dark">Penghargaan RT Terbersih & Teraktif</strong>
                            <p class="text-muted small mb-0">RW 013 memberikan penghargaan dan dana apresiasi kas bagi RT dengan kontribusi tonase bank sampah tertinggi setiap semester.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="wm-card h-100">
                <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                    <i data-lucide="help-circle" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Panduan Pemilahan Sampah
                </h5>
                <div class="d-flex flex-column gap-2 small">
                    <div class="p-3 rounded-3 bg-light border">
                        <strong class="text-primary">Kategori 1: Plastik & Botol Mineral</strong>
                        <p class="text-muted mb-0">Lepaskan label plastik, kempeskan botol, dan simpan dalam karung bersih.</p>
                    </div>
                    <div class="p-3 rounded-3 bg-light border">
                        <strong class="text-success">Kategori 2: Kardus, Karton & Kertas Arsip</strong>
                        <p class="text-muted mb-0">Lipat rapi, ikat dengan tali rafia agar mudah saat proses penimbangan.</p>
                    </div>
                    <div class="p-3 rounded-3 bg-light border">
                        <strong class="text-warning">Kategori 3: Kaleng, Besi & Tembaga</strong>
                        <p class="text-muted mb-0">Pastikan kaleng bebas dari sisa cairan dan makanan berminyak.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
