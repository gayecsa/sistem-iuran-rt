<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keaslian Surat Resmi | RW 013 Sukamiskin Bandung</title>
    
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
            background: linear-gradient(135deg, #F0F4F8 0%, #E2E8F0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .cert-card {
            max-width: 680px;
            width: 100%;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px -5px rgba(18, 55, 102, 0.12), 0 5px 15px rgba(0,0,0,0.06);
            overflow: hidden;
            border: 1px solid #E2E8F0;
        }
    </style>
</head>
<body>

    <div class="cert-card">
        @if($letter && $letter->canPrint())
            {{-- HEADER: VALID / AUTHENTIC DOKUMEN --}}
            <div class="p-4 text-center text-white position-relative" style="background: linear-gradient(135deg, #065F46 0%, #16865D 50%, #047857 100%);">
                <div class="rounded-circle bg-white text-success d-inline-flex align-items-center justify-content-center p-3 mb-2 shadow" style="width:68px;height:68px;">
                    <i data-lucide="shield-check" style="width:38px;height:38px;color:#16865D;"></i>
                </div>
                <h4 class="fw-bold mb-1">DOKUMEN RESMI TERVERIFIKASI SAH</h4>
                <p class="mb-0 text-white text-opacity-90 small">
                    Sistem Penerbitan Surat Terpadu Warkas Machi &bull; RW 013 Kota Bandung
                </p>
                <span class="badge bg-white text-success fw-bold px-3 py-1 rounded-pill mt-2 shadow-sm" style="font-size: 0.8rem;">
                    Status: ASLI & TERCATAT RESMI
                </span>
            </div>

            <div class="p-4">
                {{-- KOP KECIL IDENTITAS WILAYAH --}}
                <div class="text-center pb-3 mb-3 border-bottom">
                    <div class="text-muted text-uppercase small fw-semibold" style="letter-spacing: 0.5px;">Wilayah Penerbit:</div>
                    <div class="fw-bold text-dark fs-6">Kelurahan Sukamiskin &bull; Kecamatan Arcamanik</div>
                    <div class="text-secondary small">Rukun Warga 013 &bull; Rukun Tetangga {{ $letter->rt }} Kota Bandung 40293</div>
                </div>

                {{-- RINCIAN DOKUMEN (DATA PRIVASI DILINDUNGI) --}}
                <div class="row g-3 mb-4" style="font-size: 0.88rem;">
                    <div class="col-12 col-md-6">
                        <span class="text-muted small">Nomor Surat Resmi:</span>
                        <div class="fw-bold text-primary font-monospace fs-6">{{ $letter->nomor_surat }}</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <span class="text-muted small">Jenis Surat:</span>
                        <div class="fw-bold text-dark">{{ $letter->judul_surat }}</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <span class="text-muted small">Nama Pemohon:</span>
                        <div class="fw-bold text-dark">{{ $letter->nama_lengkap }}</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <span class="text-muted small">Nomor Induk Kependudukan (NIK):</span>
                        <div class="fw-bold text-dark font-monospace">
                            {{ substr($letter->nik, 0, 6) }}******{{ substr($letter->nik, -4) }}
                            <span class="badge bg-light text-secondary border ms-1" style="font-size:0.65rem;">Terlindungi</span>
                        </div>
                    </div>

                    <div class="col-12">
                        <span class="text-muted small">Keperluan Penggunaan:</span>
                        <div class="p-2 rounded bg-light border text-dark fw-semibold">
                            {{ $letter->keperluan }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <span class="text-muted small">Tanggal Terbit & Pengesahan RW:</span>
                        <div class="fw-semibold text-dark">{{ $letter->rw_approved_at ? $letter->rw_approved_at->translatedFormat('d F Y, H:i') : '-' }} WIB</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <span class="text-muted small">ID Token Verifikasi:</span>
                        <div class="fw-bold font-monospace text-secondary" style="font-size: 0.8rem;">
                            {{ $letter->qr_verification_code }}
                        </div>
                    </div>
                </div>

                {{-- DUAL VALIDATION BOX --}}
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-3 border h-100" style="background: #F0FDF4; border-color: #86EFAC !important;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i data-lucide="check-circle" class="text-success" style="width:16px;height:16px;"></i>
                                <span class="fw-bold small text-success">Verifikasi Ketua RT {{ $letter->rt }}</span>
                            </div>
                            <div class="fw-semibold text-dark small">{{ $letter->rt_approved_name }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $letter->rt_approved_at ? $letter->rt_approved_at->translatedFormat('d M Y, H:i') : '-' }} WIB</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-3 border h-100" style="background: #EFF6FF; border-color: #93C5FD !important;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i data-lucide="award" class="text-primary" style="width:16px;height:16px;"></i>
                                <span class="fw-bold small text-primary">Pengesahan Ketua RW 013</span>
                            </div>
                            <div class="fw-semibold text-dark small">{{ $letter->rw_approved_name }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $letter->rw_approved_at ? $letter->rw_approved_at->translatedFormat('d M Y, H:i') : '-' }} WIB</div>
                        </div>
                    </div>
                </div>

                <div class="text-center border-top pt-3 text-muted small" style="font-size: 0.75rem;">
                    <i data-lucide="lock" style="width:12px;height:12px;" class="me-1"></i>
                    Informasi ini diterbitkan resmi oleh Pengurus RW 013 Sukamiskin Arcamanik Bandung sebagai verifikasi keaslian dokumen tanda tangan digital warga.
                </div>
            </div>
        @else
            {{-- INVALID / NOT FOUND / UNAPPROVED --}}
            <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #991B1B 0%, #DC2626 100%);">
                <div class="rounded-circle bg-white text-danger d-inline-flex align-items-center justify-content-center p-3 mb-2 shadow" style="width:68px;height:68px;">
                    <i data-lucide="alert-circle" style="width:38px;height:38px;color:#DC2626;"></i>
                </div>
                <h4 class="fw-bold mb-1">DOKUMEN TIDAK VALID / BELUM SAH</h4>
                <p class="mb-0 text-white text-opacity-90 small">
                    Kode Verifikasi: <code>{{ $code }}</code>
                </p>
            </div>

            <div class="p-4 text-center">
                <p class="text-muted mb-4">
                    Dokumen dengan kode identifikasi di atas tidak ditemukan di basis data resmi Warkas Machi RW 013, atau permohonan surat tersebut belum memperoleh persetujuan lengkap dari Ketua RT dan Ketua RW.
                </p>
                <div class="p-3 rounded bg-light border small text-secondary mx-auto mb-4" style="max-width: 480px;">
                    Pastikan Anda memindai QR Code langsung dari surat resmi cetak ber-Kop Sukamiskin Arcamanik yang telah bertanda tangan ganda RT dan RW 013.
                </div>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm px-4 py-2 rounded-3">
                    Kembali ke Beranda
                </a>
            </div>
        @endif
    </div>

    <script>
        if (window.lucide) {
            window.lucide.createIcons();
        }
    </script>
</body>
</html>
