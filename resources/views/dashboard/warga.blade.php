@extends('layouts.app')

@section('title', 'Dashboard Warga')

@section('content')
<div class="dashboard-container">

    {{-- ======================================================================
         BAGIAN ATAS: SAPAAN PERSONAL, IDENTITAS WARGA & STATUS
         ====================================================================== --}}
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #FFFFFF 0%, #F4F7FB 100%); border-left: 5px solid var(--wm-primary);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1456A0&color=FFFFFF' }}"
                     alt="Avatar" class="rounded-circle shadow-sm border" style="width: 58px; height: 58px; object-fit: cover;">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0 fw-bold text-dark">Selamat Datang, {{ $user->name }}</h4>
                        <span class="badge" style="background: var(--wm-primary); color: #fff; font-size: 0.8rem; border-radius: 999px; padding: 6px 12px;">
                            Warga {{ $user->clean_rt_label }} / RW 013
                        </span>
                        <span class="badge bg-light text-dark border" style="border-radius: 999px; padding: 6px 12px; font-size: 0.75rem;">
                            No. Rumah: {{ $user->house_number ?? '-' }}
                        </span>
                    </div>
                    <p class="text-muted mb-0 small mt-1 d-none d-md-block">
                        <i data-lucide="home" style="width: 14px; height: 14px; vertical-align: middle; color: var(--wm-green);"></i>
                        Kelurahan Suryatmajan, Kec. Danurejan, Kota Yogyakarta 55213 &bull; Layanan Terpadu Iuran, Administrasi Surat, Pengaduan, & Kegiatan Warga
                    </p>
                </div>
            </div>

            <div class="d-none d-md-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn text-white rounded-pill px-3 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2" id="btnScrollToGateGps" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); font-size: 0.88rem;">
                    <i data-lucide="shield-check" style="width: 17px; height: 17px;"></i> Buka Portal (GPS)
                </button>
                <a href="{{ route('keuangan.dashboard', ['tab' => 'bayar']) }}" class="wm-btn-primary">
                    <i data-lucide="credit-card" style="width: 17px; height: 17px;"></i> Bayar Iuran
                </a>
                <a href="{{ route('surat.create') }}" class="wm-btn-outline">
                    <i data-lucide="file-signature" style="width: 17px; height: 17px;"></i> Ajukan Surat
                </a>
                <a href="{{ route('pengaduan.index') }}" class="wm-btn-success">
                    <i data-lucide="message-square-plus" style="width: 17px; height: 17px;"></i> Sampaikan Aduan
                </a>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         MENU CEPAT MOBILE ALA GOPAY (KHUSUS < 768px - d-md-none)
         ====================================================================== --}}
    <div class="haven-quick-grid d-md-none mb-4">
        {{-- 1. Buka Portal GPS --}}
        <button type="button" class="haven-quick-item" onclick="document.getElementById('btnOpenGateGps').click()">
            <div class="haven-quick-icon-wrap" style="background: #E8F5E9; color: #16A34A;">
                <i data-lucide="shield-check"></i>
            </div>
            <span class="haven-quick-label">Buka Portal</span>
        </button>

        {{-- 2. Bayar Iuran --}}
        <a href="{{ route('keuangan.dashboard', ['tab' => 'bayar']) }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #EAF3FF; color: #1456A0;">
                <i data-lucide="credit-card"></i>
            </div>
            <span class="haven-quick-label">Bayar Iuran</span>
        </a>

        {{-- 3. Ajukan Surat --}}
        <a href="{{ route('surat.create') }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #FEF3C7; color: #D97706;">
                <i data-lucide="file-signature"></i>
            </div>
            <span class="haven-quick-label">Ajukan Surat</span>
        </a>

        {{-- 4. Pengaduan --}}
        <a href="{{ route('pengaduan.index') }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #FEE2E2; color: #DC2626;">
                <i data-lucide="message-square-plus"></i>
            </div>
            <span class="haven-quick-label">Lapor Aduan</span>
        </a>

        {{-- 5. Tabungan Sampah --}}
        <a href="{{ route('bank-sampah') }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #ECFDF5; color: #059669;">
                <i data-lucide="recycle"></i>
            </div>
            <span class="haven-quick-label">Bank Sampah</span>
        </a>

        {{-- 6. Pantau CCTV --}}
        <a href="{{ route('ronda.index', ['tab' => 'cctv']) }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #EDE9FE; color: #7C3AED;">
                <i data-lucide="video"></i>
            </div>
            <span class="haven-quick-label">CCTV RW</span>
        </a>

        {{-- 7. Agenda RW --}}
        <a href="{{ route('agenda-rw.index') }}" class="haven-quick-item">
            <div class="haven-quick-icon-wrap" style="background: #E0F2FE; color: #0284C7;">
                <i data-lucide="calendar"></i>
            </div>
            <span class="haven-quick-label">Agenda RW</span>
        </a>

        {{-- 8. Semua Layanan --}}
        <button type="button" class="haven-quick-item" onclick="toggleMobileSidebar()">
            <div class="haven-quick-icon-wrap" style="background: #F1F5F9; color: #475569;">
                <i data-lucide="layout-grid"></i>
            </div>
            <span class="haven-quick-label">Lainnya</span>
        </button>
    </div>

    {{-- ======================================================================
         FITUR MOBILE FIRST: BUKA PORTAL GERBANG VIA GPS HP
         ====================================================================== --}}
    <div class="wm-card mb-4" id="cardBukaPortalGps" style="background: linear-gradient(135deg, #0F2B48 0%, #1456A0 60%, #1D4ED8 100%); color: #FFFFFF; border-radius: 18px; border: none; box-shadow: 0 8px 24px rgba(20, 86, 160, 0.18);">
        <div class="p-3 p-md-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-7">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="badge rounded-pill fw-bold px-2.5 py-1" style="background: rgba(255, 255, 255, 0.95); color: #1456A0; font-size: 0.72rem; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                            <i data-lucide="radar" style="width: 12px; height: 12px; vertical-align: middle;"></i> SMART GATEWAY RW 013
                        </span>
                        <span class="badge rounded-pill fw-bold px-2.5 py-1" style="background: rgba(255, 255, 255, 0.22); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.45); font-size: 0.72rem; backdrop-filter: blur(4px);">
                            <i data-lucide="map-pin" style="width: 11px; height: 11px; vertical-align: middle;"></i> GPS Auto-Detect
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1 text-white">Buka Portal Gerbang Masuk (GPS HP)</h5>
                    <p class="text-white text-opacity-75 small mb-2" style="line-height: 1.45; font-size: 0.82rem;">
                        Dekati pos gerbang utama RW 013, aktifkan GPS di HP Anda, lalu tekan tombol untuk membuka palang pintu secara instan tanpa perlu kartu fisik.
                    </p>

                    <!-- Real-Time Distance Live Indicator -->
                    <div id="gpsStatusBox" class="p-2.5 rounded-3 d-inline-flex align-items-center gap-2" style="background: rgba(0, 0, 0, 0.25); border: 1px solid rgba(255, 255, 255, 0.15); max-width: 100%;">
                        <span class="cctv-pulse-dot" style="background: #34D399; width: 8px; height: 8px;"></span>
                        <span id="gpsDistanceText" class="small fw-semibold text-white" style="font-size: 0.78rem;">
                            Tekan tombol untuk membaca posisi &amp; jarak ke gerbang
                        </span>
                    </div>
                </div>

                <div class="col-12 col-md-5 text-md-end text-center">
                    <button type="button" class="btn btn-lg rounded-pill px-4 py-3 fw-bold shadow-lg w-100 w-md-auto d-inline-flex align-items-center justify-content-center gap-2 transition-all" id="btnOpenGateGps" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: #FFFFFF; border: none; font-size: 1rem;">
                        <i data-lucide="key-round" style="width: 22px; height: 22px;"></i>
                        <span>BUKA GERBANG SEKARANG</span>
                    </button>
                    <small class="d-block text-white text-opacity-50 mt-2" style="font-size: 0.7rem;">
                        <i data-lucide="shield-check" style="width: 12px; height: 12px; vertical-align: middle;"></i> Terverifikasi Koordinat Aman &bull; Cooldown 5 Detik
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         STATISTIK UTAMA WARGA (4 KARTU METRIK)
         ====================================================================== --}}
    <div class="row g-2 g-md-3 mb-4">
        <!-- 1. Status Iuran -->
        <div class="col-6 col-md-6 col-xl-3">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Status Iuran</span>
                    <div class="wm-stat-icon-wrap" style="background: {{ count($tagihan_belum_bayar) == 0 ? '#E3F5EC' : '#FEE2E2' }}; color: {{ count($tagihan_belum_bayar) == 0 ? '#16865D' : '#DC2626' }};">
                        <i data-lucide="{{ count($tagihan_belum_bayar) == 0 ? 'check-circle' : 'alert-circle' }}"></i>
                    </div>
                </div>
                <div class="wm-stat-val {{ count($tagihan_belum_bayar) == 0 ? 'text-success' : 'text-danger' }}">
                    {{ count($tagihan_belum_bayar) == 0 ? 'Lunas / Bebas' : count($tagihan_belum_bayar) . ' Belum Bayar' }}
                </div>
                <div class="small text-muted fw-semibold">Bulan Berjalan</div>
            </div>
        </div>

        <!-- 2. Tagihan Belum Dibayar -->
        <div class="col-6 col-md-6 col-xl-3">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Total Tagihan Tertunda</span>
                    <div class="wm-stat-icon-wrap" style="background: #FEF3C7; color: #D97706;">
                        <i data-lucide="receipt"></i>
                    </div>
                </div>
                @php
                    $totalTertunda = collect($tagihan_belum_bayar)->sum('sisa_tagihan');
                @endphp
                <div class="wm-stat-val text-warning" style="font-size: 1.25rem;">
                    Rp {{ number_format($totalTertunda, 0, ',', '.') }}
                </div>
                <div class="small text-muted fw-semibold">{{ count($tagihan_belum_bayar) }} Tagihan Tertunda</div>
            </div>
        </div>

        <!-- 3. Total Iuran Terbayar -->
        <div class="col-6 col-md-6 col-xl-3">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Riwayat Terbayar</span>
                    <div class="wm-stat-icon-wrap" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="wallet"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-primary" style="font-size: 1.25rem;">
                    Rp {{ number_format($total_bayar, 0, ',', '.') }}
                </div>
                <div class="small text-success fw-semibold">
                    <i data-lucide="check" style="width:12px;height:12px;"></i> Terverifikasi Sistem
                </div>
            </div>
        </div>

        <!-- 4. Pengaduan Saya -->
        <div class="col-6 col-md-6 col-xl-3">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Pengaduan Saya</span>
                    <div class="wm-stat-icon-wrap" style="background: #E3F5EC; color: #16865D;">
                        <i data-lucide="message-square-check"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-success">{{ count($pengaduanSaya) }}</div>
                <div class="small text-muted fw-semibold">Aspirasi Disampaikan</div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         KONTEN UTAMA: 2 KOLOM
         ====================================================================== --}}
    <div class="row g-4">
        
        <!-- KOLOM KIRI (TAGIHAN, RIWAYAT PEMBAYARAN, PENGADUAN SAYA) -->
        <div class="col-lg-7">
            
            {{-- 1. Tagihan Iuran Menunggu Pembayaran / Status Lunas --}}
            @if(count($tagihan_belum_bayar) > 0)
                <div class="wm-card mb-4" style="border-top: 4px solid #DC2626;">
                    <div class="wm-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="wm-card-title mb-0 text-danger">
                                <i data-lucide="alert-triangle" class="text-danger me-2" style="width: 20px; height: 20px;"></i>
                                Tagihan Iuran Menunggu Pembayaran
                            </h5>
                            <p class="text-muted small mb-0 mt-1">Harap segera menyelesaikan pembayaran iuran lingkungan terpadu keluarga Anda.</p>
                        </div>
                    </div>
                    <div class="wm-card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($tagihan_belum_bayar as $tagihan)
                                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div>
                                        <div class="fw-bold text-dark">
                                            Tagihan Iuran {{ \Carbon\Carbon::parse($tagihan->periode . '-01')->translatedFormat('F Y') }}
                                            <span class="text-muted fw-normal small">#{{ $tagihan->nomor_tagihan }}</span>
                                        </div>
                                        <small class="text-muted">
                                            Jatuh Tempo: {{ \Carbon\Carbon::parse($tagihan->jatuh_tempo)->translatedFormat('d M Y') }} • RT {{ $tagihan->clean_rt ?? ($user->clean_rt ?? '001') }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-danger mb-1">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</div>
                                        <a href="{{ route('keuangan.dashboard', ['tab' => 'bayar', 'tagihan_id' => $tagihan->id]) }}" class="btn btn-sm btn-primary" style="font-size:0.75rem; background-color: #1456A0; border-color: #1456A0;">
                                            <i data-lucide="credit-card" style="width:13px;height:13px;"></i> Bayar Sekarang
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <!-- State Positif: Semua Tagihan Sudah Lunas / Dibebaskan -->
                <div class="wm-card mb-4 warga-lunas-card" style="border-top: 4px solid #16865D; background: #FAFDFB;">
                    <div class="wm-card-body p-4 text-center">
                        <div class="warga-lunas-icon" style="width: 48px; height: 48px; background: #E8F6EF; color: #16865D; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                            <i data-lucide="check-circle" style="width: 26px; height: 26px;"></i>
                        </div>
                        <h5 class="fw-bold mb-1 warga-lunas-title" style="color: #16865D;">Semua Tagihan Iuran Sudah Lunas / Dibebaskan</h5>
                        <p class="text-muted small mb-3 warga-lunas-desc">Tidak ada tunggakan iuran atau kewajiban pembayaran yang tertunda untuk keluarga Anda.</p>
                        <a href="{{ route('keuangan.dashboard', ['tab' => 'riwayat']) }}" class="btn btn-sm warga-lunas-btn" style="background: #E8F6EF; color: #16865D; border: 1px solid #C6F6D5; font-weight: 600;">
                            <i data-lucide="file-check" style="width: 14px; height: 14px; vertical-align: -2px;"></i> Lihat Riwayat & Kuitansi
                        </a>
                    </div>
                </div>
            @endif

            <!-- 2. Riwayat Pembayaran Terbaru -->
            <div class="wm-card mb-4">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="history" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                        Riwayat Pembayaran Saya
                    </h5>
                    <a href="{{ route('keuangan.dashboard', ['tab' => 'riwayat']) }}" class="wm-btn-outline" style="padding: 6px 12px; font-size: 0.8rem;">
                        Semua Riwayat
                    </a>
                </div>
                <div class="wm-card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr style="font-size:0.8rem; text-transform:uppercase;">
                                    <th>Periode & Jenis</th>
                                    <th>Tanggal / Waktu</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($riwayat_bayar->count() > 0)
                                    @foreach($riwayat_bayar as $bayar)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-dark">
                                                    @if($bayar->tagihanKeluarga)
                                                        Iuran Periode {{ \Carbon\Carbon::parse($bayar->tagihanKeluarga->periode . '-01')->translatedFormat('F Y') }}
                                                    @else
                                                        Iuran Lingkungan Terpadu
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $bayar->metode_pembayaran ?: 'Transfer Bank' }} • {{ $bayar->nomor_pembayaran ?: ('#PAY-' . $bayar->id) }}</small>
                                            </td>
                                            <td class="small text-muted">{{ $bayar->tanggal_bayar ? \Carbon\Carbon::parse($bayar->tanggal_bayar)->translatedFormat('d M Y') : '-' }}</td>
                                            <td class="fw-bold text-dark">Rp {{ number_format($bayar->nominal_bayar, 0, ',', '.') }}</td>
                                            <td>
                                                @if($bayar->status === 'dibatalkan')
                                                    <span class="badge" style="background:#FEE2E2; color:#DC2626;">Dibatalkan</span>
                                                @else
                                                    <span class="badge" style="background:#E8F6EF; color:#16865D;">Lunas</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @elseif(isset($riwayat_tagihan) && $riwayat_tagihan->count() > 0)
                                    @foreach($riwayat_tagihan->take(5) as $rtg)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-dark">
                                                    Iuran Periode {{ \Carbon\Carbon::parse($rtg->periode . '-01')->translatedFormat('F Y') }}
                                                </div>
                                                <small class="text-muted">#{{ $rtg->nomor_tagihan }} • {{ $rtg->metode_pembayaran_terakhir ?: 'Sistem Terpadu' }}</small>
                                            </td>
                                            <td class="small text-muted">{{ $rtg->tanggal_lunas ? \Carbon\Carbon::parse($rtg->tanggal_lunas)->translatedFormat('d M Y') : \Carbon\Carbon::parse($rtg->updated_at)->translatedFormat('d M Y') }}</td>
                                            <td class="fw-bold text-dark">Rp {{ number_format($rtg->total_dibayar > 0 ? $rtg->total_dibayar : $rtg->total_tagihan, 0, ',', '.') }}</td>
                                            <td>
                                                @if($rtg->status === 'Dibebaskan')
                                                    <span class="badge" style="background:#EAF3FF; color:#1456A0;">Dibebaskan</span>
                                                @elseif($rtg->status === 'Lunas')
                                                    <span class="badge" style="background:#E8F6EF; color:#16865D;">Lunas</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $rtg->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            Belum ada catatan riwayat pembayaran.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Pengaduan Saya -->
            <div class="wm-card mb-4">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="message-square-check" class="text-success me-2" style="width: 20px; height: 20px;"></i>
                        Riwayat Aspirasi & Pengaduan Saya
                    </h5>
                    <a href="{{ route('pengaduan.index') }}" class="wm-btn-outline" style="padding: 6px 12px; font-size: 0.8rem;">
                        Buat Pengaduan
                    </a>
                </div>
                <div class="wm-card-body">
                    @forelse($pengaduanSaya as $pSaya)
                        <div class="p-3 mb-3 rounded border" style="background: #FAFAFA;">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <span class="badge bg-light text-dark border">{{ $pSaya['kategori'] ?? 'Layanan' }}</span>
                                <span class="badge {{ ($pSaya['status'] ?? '') === 'Selesai' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $pSaya['status'] ?? 'Menunggu' }}
                                </span>
                            </div>
                            <p class="small text-secondary mb-2">{{ $pSaya['deskripsi'] ?? '-' }}</p>
                            @if(!empty($pSaya['catatan_tindak_lanjut']))
                                <div class="p-2 rounded bg-white border small text-success">
                                    <strong><i data-lucide="corner-down-right" style="width:12px;height:12px;"></i> Tindak Lanjut:</strong>
                                    {{ $pSaya['catatan_tindak_lanjut'] }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted small">
                            Anda belum pernah mengajukan pengaduan. Ada keluhan fasilitas lingkungan? Sampaikan melalui form pengaduan.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN (PELAYANAN SURAT, KEGIATAN LINGKUNGAN, PENGUMUMAN) -->
        <div class="col-lg-5">
            
            <!-- 1. Layanan Surat Mandiri -->
            <div class="wm-card mb-4" style="background: linear-gradient(135deg, #EAF3FF 0%, #FFFFFF 100%); border: 1px solid #BFDBFE;">
                <div class="d-flex align-items-start gap-3">
                    <div class="p-2 rounded bg-primary text-white">
                        <i data-lucide="file-signature" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Butuh Surat Pengantar RT / RW?</h6>
                        <p class="small text-secondary mb-3">
                            Ajukan surat pengantar pembuatan KTP, KK baru, domisili, atau SKCK langsung secara mandiri tanpa antre lama.
                        </p>
                        <a href="{{ route('surat.create') }}" class="wm-btn-primary" style="padding: 7px 14px; font-size: 0.8rem;">
                            <i data-lucide="plus-circle" style="width:14px;height:14px;"></i> Ajukan Surat Pengantar
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Jadwal Layanan Lingkungan -->
            <div class="wm-card mb-4">
                <div class="wm-card-header">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="calendar-check" class="text-success me-2" style="width: 20px; height: 20px;"></i>
                        Jadwal Pelayanan Lingkungan
                    </h5>
                </div>
                <div class="wm-card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark"><i data-lucide="heart-pulse" class="text-danger me-1" style="width:14px;height:14px;"></i> Posyandu Balita & Lansia</span>
                                <span class="badge bg-success">Sabtu, 08.00 WIB</span>
                            </div>
                            <small class="text-muted">Balai Warga {{ $user->clean_rt_label }} &bull; Penimbangan & Skrining</small>
                        </div>
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark"><i data-lucide="recycle" class="text-success me-1" style="width:14px;height:14px;"></i> Setor Bank Sampah</span>
                                <span class="badge bg-info text-white">Rabu, 10.00 WIB</span>
                            </div>
                            <small class="text-muted">Titik Bank Sampah RW 013 &bull; Konversi Sampah Jadi Saldo</small>
                        </div>
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark"><i data-lucide="shield-check" class="text-primary me-1" style="width:14px;height:14px;"></i> Jadwal Ronda Malam</span>
                                <span class="badge bg-secondary">Malam Ini</span>
                            </div>
                            <small class="text-muted">Pos Ronda {{ $user->clean_rt_label }} &bull; 22.00 - 04.30 WIB</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Pengumuman RW 013 -->
            <div class="wm-card mb-4">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="megaphone" class="text-warning me-2" style="width: 20px; height: 20px;"></i>
                        Pengumuman RW 013
                    </h5>
                    <a href="{{ route('pengumuman.index') }}" class="wm-btn-outline" style="padding: 5px 10px; font-size: 0.75rem;">
                        Semua
                    </a>
                </div>
                <div class="wm-card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pengumuman as $p)
                            <a href="{{ route('pengumuman.show', $p->id) }}" class="list-group-item list-group-item-action p-3">
                                <div class="fw-semibold text-dark small mb-1">{{ $p->judul }}</div>
                                <div class="small text-muted d-flex justify-content-between">
                                    <span><i data-lucide="calendar" style="width:12px;height:12px;"></i> {{ Carbon\Carbon::parse($p->tanggal_aktif)->translatedFormat('d M Y') }}</span>
                                    <span class="badge bg-light text-primary border">Resmi</span>
                                </div>
                            </a>
                        @empty
                            <div class="p-3 text-center text-muted small">
                                Belum ada pengumuman terbaru.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btnScroll = document.getElementById('btnScrollToGateGps');
        var cardGps = document.getElementById('cardBukaPortalGps');
        if (btnScroll && cardGps) {
            btnScroll.addEventListener('click', function() {
                cardGps.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }

        var btnOpen = document.getElementById('btnOpenGateGps');
        var statusText = document.getElementById('gpsDistanceText');
        var statusBox = document.getElementById('gpsStatusBox');

        // Geolocation Haversine Client Preview Helper
        function getDistanceFromLatLonInM(lat1, lon1, lat2, lon2) {
            var R = 6371000; // Radius bumi dalam meter
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLon = (lon2 - lon1) * Math.PI / 180;
            var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon/2) * Math.sin(dLon/2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return Math.round(R * c);
        }

        // Live watch / poll position saat halaman aktif
        var cachedGateLat = null;
        var cachedGateLng = null;
        var cachedMaxRadius = 15;

        // Fetch gate location awal untuk info live
        fetch('{{ route('api.iot.portal.live_status') }}')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.device) {
                    cachedGateLat = data.device.gate_latitude;
                    cachedGateLng = data.device.gate_longitude;
                    cachedMaxRadius = data.device.gate_radius_meters || 15;

                    if (navigator.geolocation && cachedGateLat && cachedGateLng) {
                        navigator.geolocation.getCurrentPosition(function(pos) {
                            var dist = getDistanceFromLatLonInM(pos.coords.latitude, pos.coords.longitude, cachedGateLat, cachedGateLng);
                            if (statusText) {
                                statusText.innerHTML = 'Jarak Anda ke gerbang: <strong>' + dist + ' meter</strong> (Maks: ' + cachedMaxRadius + ' meter)';
                            }
                        }, function() {}, { enableHighAccuracy: true });
                    }
                }
            })
            .catch(function() {});

        if (btnOpen) {
            btnOpen.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Browser atau perangkat Anda tidak mendukung akses Geolocation GPS.');
                    return;
                }

                var originalHtml = btnOpen.innerHTML;
                btnOpen.disabled = true;
                btnOpen.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengunci GPS & Membuka...';
                if (statusText) statusText.textContent = 'Membaca koordinat GPS dengan akurasi tinggi...';

                navigator.geolocation.getCurrentPosition(function(position) {
                    var userLat = position.coords.latitude;
                    var userLng = position.coords.longitude;
                    var accuracy = position.coords.accuracy;

                    fetch('{{ route('ronda.portal.open-gps') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            latitude: userLat,
                            longitude: userLng,
                            accuracy: accuracy
                        })
                    })
                    .then(function(response) {
                        return response.json().then(function(data) {
                            return { ok: response.ok, status: response.status, data: data };
                        });
                    })
                    .then(function(res) {
                        btnOpen.disabled = false;
                        btnOpen.innerHTML = originalHtml;

                        if (res.data.status === 'granted') {
                            if (statusText) {
                                statusText.innerHTML = '<span class="text-success fw-bold">✓ Portal Terbuka!</span> Jarak: ' + res.data.distance + ' m (Maks: ' + res.data.max_radius + 'm)';
                            }
                            alert('BERHASIL! ' + res.data.message);
                        } else if (res.data.status === 'denied') {
                            if (statusText) {
                                statusText.innerHTML = '<span class="text-danger fw-bold">✗ Di Luar Radius:</span> ' + res.data.distance + ' m (Maks: ' + res.data.max_radius + 'm)';
                            }
                            alert('AKSES DITOLAK: ' + res.data.message);
                        } else if (res.data.status === 'cooldown') {
                            alert(res.data.message);
                        } else {
                            alert(res.data.message || 'Gagal memproses pembukaan portal gerbang.');
                        }

                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    })
                    .catch(function(err) {
                        btnOpen.disabled = false;
                        btnOpen.innerHTML = originalHtml;
                        alert('Terjadi kendala jaringan saat menghubungi server: ' + err.message);
                    });
                }, function(error) {
                    btnOpen.disabled = false;
                    btnOpen.innerHTML = originalHtml;
                    var msg = 'Gagal mengakses GPS: ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            msg += 'Izin akses lokasi ditolak oleh pengguna/browser. Silakan aktifkan izin lokasi.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            msg += 'Informasi lokasi tidak tersedia di perangkat Anda.';
                            break;
                        case error.TIMEOUT:
                            msg += 'Waktu permintaan lokasi GPS habis (timeout).';
                            break;
                        default:
                            msg += error.message;
                            break;
                    }
                    if (statusText) statusText.textContent = msg;
                    alert(msg);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            });
        }
    });
</script>
@endpush
@endsection
