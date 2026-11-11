<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Layanan Publik Surat & Administrasi Warga | RW 013 Sukamiskin Bandung</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: #1E293B;
            min-height: 100vh;
        }
        .public-navbar {
            background: #ffffff;
            border-bottom: 1px solid #E2E8F0;
        }
        .hero-banner {
            background: linear-gradient(135deg, #0F2D54 0%, #123766 50%, #1456A0 100%);
            color: #ffffff;
            padding: 50px 0 60px 0;
        }
        .card-service {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            transition: all 0.25s ease;
        }
        .card-service:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(18, 55, 102, 0.12) !important;
            border-color: #1456A0 !important;
        }
    </style>
</head>
<body>

    {{-- PUBLIC NAVBAR --}}
    <nav class="public-navbar sticky-top py-3">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <div class="rounded-3 p-2 bg-primary text-white d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i data-lucide="building" style="width:20px;height:20px;"></i>
                </div>
                <div>
                    <strong class="text-dark d-block" style="font-size: 1.05rem; line-height: 1.2;">Warkas Machi</strong>
                    <span class="text-muted small" style="font-size: 0.75rem;">RW 013 Kelurahan Sukamiskin &bull; Kota Bandung</span>
                </div>
            </a>

            <div class="d-flex align-items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold">
                        <i data-lucide="layout-dashboard" style="width:15px;height:15px;" class="me-1"></i> Dashboard Anda
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-3 py-2 rounded-3 fw-semibold">
                        Masuk Akun Warga
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold">
                        Registrasi Warga
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section class="hero-banner text-center">
        <div class="container" style="max-width: 900px;">
            <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 rounded-pill small mb-3 border border-white border-opacity-25">
                Portal Informasi Layanan Publik Terpadu 2026
            </span>
            <h1 class="fw-bold mb-3">Katalog Layanan Surat & Administrasi Warga</h1>
            <p class="text-white text-opacity-80 fs-6 mb-4">
                Informasi resmi persyaratan, estimasi waktu penyelesaian, dan prosedur pengajuan surat keterangan serta pengantar untuk seluruh warga RT 001 sampai RT 008, RW 013 Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung 40293.
            </p>

            {{-- Form Pencarian Cepat --}}
            <form action="{{ route('surat.public_catalog') }}" method="GET" class="d-flex align-items-center justify-content-center gap-2 mx-auto" style="max-width: 600px;">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i data-lucide="search" style="width:18px;height:18px;"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari layanan (misal: domisili, nikah, beasiswa, kematian)..." value="{{ $search }}" style="font-size: 0.95rem;">
                    <button type="submit" class="btn btn-primary fw-semibold px-4" style="background:#1456A0;">Cari</button>
                </div>
            </form>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <main class="container py-5">

        {{-- PROSEDUR RINGKAS WORKFLOW --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-5" style="background: #ffffff;">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i data-lucide="git-branch" class="text-primary" style="width:20px;height:20px;"></i>
                <span>Prosedur Pengajuan Surat bagi Warga Lingkungan RW 013</span>
            </h5>
            <div class="row g-3">
                <div class="col-12 col-md-3">
                    <div class="p-3 rounded-3 bg-light h-100 border">
                        <div class="fw-bold text-primary mb-1 small">1. Masuk / Registrasi</div>
                        <p class="text-muted small mb-0">Warga login menggunakan akun terdaftar atau melapor ke pengurus RT setempat.</p>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 rounded-3 bg-light h-100 border">
                        <div class="fw-bold text-primary mb-1 small">2. Isi Formulir Online</div>
                        <p class="text-muted small mb-0">Pilih salah satu dari 18 jenis surat resmi, lengkapi keperluan dan lampirkan berkas KTP/KK.</p>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 rounded-3 bg-light h-100 border">
                        <div class="fw-bold text-primary mb-1 small">3. Verifikasi RT & RW</div>
                        <p class="text-muted small mb-0">Ketua RT memverifikasi data lapangan dan Ketua RW mengesahkan nomor surat resmi.</p>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 rounded-3 bg-light h-100 border">
                        <div class="fw-bold text-primary mb-1 small">4. Terbit & Cetak A4</div>
                        <p class="text-muted small mb-0">Dokumen resmi ber-KOP dan QR Code validasi keaslian siap diunduh & dicetak mandiri.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER KATEGORI TABS --}}
        <div class="d-flex flex-wrap align-items-center gap-2 mb-4 p-2 rounded-4 border bg-white shadow-sm overflow-x-auto">
            @foreach($categories as $catKey => $catData)
                <a href="{{ route('surat.public_catalog', ['kategori' => $catKey, 'search' => $search]) }}" 
                   class="btn btn-sm rounded-pill d-inline-flex align-items-center gap-2 px-3 py-2 text-decoration-none {{ $kategoriFilter === $catKey ? 'btn-primary shadow-sm text-white' : 'btn-light text-secondary border-0' }}"
                   style="font-size: 0.84rem; font-weight: 600; white-space: nowrap;">
                    <span>{{ $catData['label'] }}</span>
                    <span class="badge {{ $kategoriFilter === $catKey ? 'bg-white text-primary' : 'bg-secondary bg-opacity-10 text-secondary' }} rounded-pill" style="font-size: 0.72rem;">
                        {{ $catData['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- GRID 18 LAYANAN SURAT --}}
        <div class="row g-4 mb-5">
            @forelse($katalogSurat as $item)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-service h-100 d-flex flex-column p-4">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                            <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: rgba(20, 86, 160, 0.08); color: #1456A0;">
                                <i data-lucide="{{ $item->icon }}" style="width:24px;height:24px;"></i>
                            </div>
                            <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill small font-monospace">
                                {{ $item->singkatan ?: $item->kode }}
                            </span>
                        </div>

                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">{{ $item->nama }}</h5>
                        <div class="text-primary small fw-semibold mb-2" style="font-size: 0.76rem;">{{ $item->kategori_label }}</div>
                        <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.45;">{{ $item->deskripsi }}</p>

                        {{-- Persyaratan --}}
                        <div class="p-3 rounded-3 mb-3" style="background: #F8FAFC; border: 1px dashed #CBD5E1; font-size: 0.76rem;">
                            <strong class="text-secondary d-block mb-1">Persyaratan Dokumen:</strong>
                            <ul class="list-unstyled mb-0 text-muted">
                                @if(!empty($item->persyaratan))
                                    @foreach(array_slice($item->persyaratan, 0, 3) as $syarat)
                                        <li class="d-flex align-items-start gap-1 mb-1">
                                            <i data-lucide="check" class="text-success mt-0" style="width:12px;height:12px;flex-shrink:0;"></i>
                                            <span class="text-truncate">{{ $syarat }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li>KTP & Kartu Keluarga Terdaftar</li>
                                @endif
                            </ul>
                        </div>

                        {{-- Estimasi & Biaya --}}
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top mb-3" style="font-size: 0.78rem;">
                            <span class="text-muted d-flex align-items-center gap-1">
                                <i data-lucide="clock" style="width:13px;height:13px;"></i>
                                <span>{{ $item->estimasi_hari }}</span>
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                {{ $item->biaya }}
                            </span>
                        </div>

                        {{-- Button CTA --}}
                        <div>
                            @auth
                                <a href="{{ route('surat.create', ['jenis' => $item->kode]) }}" class="btn btn-outline-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 py-2 rounded-3" style="font-size: 0.88rem;">
                                    <span>Ajukan Permohonan</span>
                                    <i data-lucide="arrow-right" style="width:14px;height:14px;"></i>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 py-2 rounded-3" style="font-size: 0.88rem;">
                                    <span>Masuk untuk Mengajukan</span>
                                    <i data-lucide="log-in" style="width:14px;height:14px;"></i>
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Tidak ada jenis surat yang sesuai pencarian.</p>
                </div>
            @endforelse
        </div>

        {{-- KONTAK & SEKRETARIAT WILAYAH --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #123766 0%, #0F2D54 100%);">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-8">
                    <h5 class="fw-bold mb-1">Sekretariat Pelayanan Warga RW 013 Sukamiskin</h5>
                    <p class="text-white text-opacity-80 small mb-2">
                        Jl. Pacantel No. 12, Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung 40293.
                    </p>
                    <div class="d-flex flex-wrap gap-3 small text-white text-opacity-90">
                        <span><i data-lucide="phone" style="width:14px;height:14px;" class="me-1"></i> (022) 7208130</span>
                        <span><i data-lucide="clock" style="width:14px;height:14px;" class="me-1"></i> Pelayanan: Senin - Sabtu (08.00 - 17.00 WIB)</span>
                        <span><i data-lucide="mail" style="width:14px;height:14px;" class="me-1"></i> rw013.sukamiskin@bandung.go.id</span>
                    </div>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <a href="{{ route('surat.index') }}" class="btn btn-light px-4 py-2 rounded-3 fw-bold text-primary">
                        Akses Portal Warga
                    </a>
                </div>
            </div>
        </div>

    </main>

    {{-- FOOTER --}}
    <footer class="py-4 border-top bg-white text-center text-muted small">
        <div class="container">
            &copy; 2026 Warkas Machi &bull; Sistem Koordinasi & Pelayanan Terpadu RW 013 Sukamiskin Arcamanik Kota Bandung. Seluruh hak cipta dilindungi.
        </div>
    </footer>

    <script>
        if (window.lucide) {
            window.lucide.createIcons();
        }
    </script>
</body>
</html>
