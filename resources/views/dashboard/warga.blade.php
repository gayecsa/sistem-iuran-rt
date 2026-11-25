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
                    <p class="text-muted mb-0 small mt-1">
                        <i data-lucide="home" style="width: 14px; height: 14px; vertical-align: middle; color: var(--wm-green);"></i>
                        Kelurahan Sukamiskin, Kec. Arcamanik, Kota Bandung 40293 &bull; Layanan Terpadu Iuran, Administrasi Surat, Pengaduan, & Kegiatan Warga
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
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
         STATISTIK UTAMA WARGA (4 KARTU METRIK)
         ====================================================================== --}}
    <div class="row g-3 mb-4">
        <!-- 1. Status Iuran -->
        <div class="col-xl-3 col-md-6">
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
        <div class="col-xl-3 col-md-6">
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
        <div class="col-xl-3 col-md-6">
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
        <div class="col-xl-3 col-md-6">
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
@endsection
