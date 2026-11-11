@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    {{-- Breadcrumb & Title --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i data-lucide="clipboard-pen" class="text-primary me-2" style="width: 26px; height: 26px;"></i>
                Catatan Evaluasi RW 013 untuk {{ $rtLabel }}
            </h4>
            <p class="text-muted small mb-0">
                Ulasan hasil audit, monitoring, dan rekomendasi berkala dari Ketua & Pengurus RW 013 demi peningkatan pelayanan warga.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rt.laporan.create') }}" class="wm-btn-primary">
                <i data-lucide="send" style="width: 17px; height: 17px;"></i> Tindak Lanjut / Perbarui Laporan
            </a>
            <a href="{{ route('dashboard.rt') }}" class="wm-btn-outline">
                <i data-lucide="arrow-left" style="width: 17px; height: 17px;"></i> Dashboard RT
            </a>
        </div>
    </div>

    {{-- Skor Kinerja & Status Banner --}}
    @php
        $skor = $rtAudit['skor_kinerja'] ?? 85;
        $statusLap = $myReport['status'] ?? ($rtAudit['status_laporan'] ?? 'Menunggu pemeriksaan');
        $skorColor = $skor >= 90 ? 'var(--wm-green)' : ($skor >= 80 ? 'var(--wm-primary)' : '#D97706');
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="wm-card h-100" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%); border-left: 5px solid {{ $skorColor }};">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <span class="small text-muted text-uppercase fw-bold">Audit & Monitoring Lingkungan</span>
                        <h4 class="fw-bold text-dark mt-1 mb-2">Evaluasi Kinerja {{ $rtLabel }}</h4>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                            <span class="badge bg-light text-dark border px-3 py-2">
                                Status Pelaporan: <strong>{{ $statusLap }}</strong>
                            </span>
                            <span class="badge bg-light text-dark border px-3 py-2">
                                Terakhir Dinilai: <strong>{{ $myReport['updated_at'] ?? 'November 2026' }}</strong>
                            </span>
                        </div>
                        <div class="p-3 rounded mb-2" style="background: #FFFBEB; border: 1px solid #FDE68A;">
                            <h6 class="fw-bold mb-1 text-dark">
                                <i data-lucide="quote" style="width:16px;height:16px;color:#D97706;"></i>
                                Catatan Evaluasi Pengurus RW:
                            </h6>
                            <p class="small text-secondary mb-0">
                                "{{ $myReport['evaluasi_rw'] ?? ($rtAudit['evaluasi_rw'] ?? 'Pengurus RW sedang meninjau pelaporan bulanan RT.') }}"
                            </p>
                        </div>
                        <div class="p-3 rounded" style="background: #E3F5EC; border: 1px solid #A7F3D0;">
                            <h6 class="fw-bold mb-1 text-dark">
                                <i data-lucide="check-circle" style="width:16px;height:16px;color:var(--wm-green);"></i>
                                Rekomendasi Tindak Lanjut RW:
                            </h6>
                            <p class="small text-secondary mb-0">
                                {{ $myReport['rekomendasi_rw'] ?? ($rtAudit['rekomendasi'] ?? 'Pertahankan komunikasi transparan dan ketertiban administrasi.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="wm-card h-100 text-center d-flex flex-column justify-content-center align-items-center p-4">
                <span class="text-muted small text-uppercase fw-bold mb-2">Indeks Skor Kinerja RT</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 110px; height: 110px; border: 6px solid {{ $skorColor }}; font-size: 2.2rem; font-weight: 800; color: {{ $skorColor }};">
                    {{ $skor }}
                </div>
                <div class="fw-bold text-dark mb-1">
                    @if($skor >= 90)
                        Sangat Memuaskan
                    @elseif($skor >= 80)
                        Baik & Tertib
                    @else
                        Perlu Perhatian Khusus
                    @endif
                </div>
                <small class="text-muted">Berdasarkan 4 pilar: Kependudukan, Kas RT, Kegiatan Sosial, & Keamanan</small>
            </div>
        </div>
    </div>

    {{-- 4 Aspek Penilaian --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="wm-card h-100 p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="p-2 rounded bg-primary text-white">
                        <i data-lucide="users" style="width:16px;height:16px;"></i>
                    </div>
                    <span class="fw-bold text-dark small">1. Administrasi</span>
                </div>
                <div class="small text-secondary mb-1">
                    {{ $rtAudit['administrasi'] ?? 'Lengkap' }}
                </div>
                <small class="text-muted" style="font-size:0.75rem;">Verifikasi berkas kependudukan & surat</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="wm-card h-100 p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="p-2 rounded bg-success text-white">
                        <i data-lucide="wallet" style="width:16px;height:16px;"></i>
                    </div>
                    <span class="fw-bold text-dark small">2. Keuangan RT</span>
                </div>
                <div class="small text-secondary mb-1">
                    {{ $rtAudit['keuangan'] ?? 'Lengkap' }}
                </div>
                <small class="text-muted" style="font-size:0.75rem;">Transparansi kas & kuitansi</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="wm-card h-100 p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="p-2 rounded bg-danger text-white">
                        <i data-lucide="heart-pulse" style="width:16px;height:16px;"></i>
                    </div>
                    <span class="fw-bold text-dark small">3. Sosial & Sehat</span>
                </div>
                <div class="small text-secondary mb-1">
                    {{ $rtAudit['sosial'] ?? 'Aktif' }}
                </div>
                <small class="text-muted" style="font-size:0.75rem;">Posyandu balita & gotong royong</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="wm-card h-100 p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="p-2 rounded bg-warning text-dark">
                        <i data-lucide="shield-check" style="width:16px;height:16px;"></i>
                    </div>
                    <span class="fw-bold text-dark small">4. Siskamling</span>
                </div>
                <div class="small text-secondary mb-1">
                    {{ $rtAudit['kebersihan_keamanan'] ?? 'Baik' }}
                </div>
                <small class="text-muted" style="font-size:0.75rem;">Ketertiban pos ronda & bank sampah</small>
            </div>
        </div>
    </div>

    {{-- Riwayat Evaluasi & Tindak Lanjut --}}
    <div class="wm-card">
        <div class="wm-card-header d-flex justify-content-between align-items-center">
            <h5 class="wm-card-title mb-0">
                <i data-lucide="history" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                Riwayat Komunikasi & Tindak Lanjut RW - {{ $rtLabel }}
            </h5>
            <a href="{{ route('rt.laporan.create') }}" class="wm-btn-primary" style="padding: 6px 14px; font-size: 0.8rem;">
                Tanggapi Catatan RW
            </a>
        </div>
        <div class="wm-card-body p-0">
            <div class="list-group list-group-flush">
                @if(isset($myReport['history']) && is_array($myReport['history']))
                    @foreach($myReport['history'] as $hist)
                        <div class="list-group-item p-3 d-flex align-items-start gap-3">
                            <div class="p-2 rounded-circle bg-light border text-primary mt-1">
                                <i data-lucide="message-square" style="width: 16px; height: 16px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">{{ $hist['aktor'] ?? 'Ketua RW 013' }}</span>
                                    <span class="small text-muted">{{ $hist['tanggal'] ?? '-' }}</span>
                                </div>
                                <p class="small text-secondary mb-0">{{ $hist['aksi'] ?? '-' }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-4 text-center text-muted small">
                        Belum ada riwayat catatan evaluasi.
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
