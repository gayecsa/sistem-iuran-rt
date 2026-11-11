@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    {{-- Breadcrumb & Title --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i data-lucide="file-check" class="text-primary me-2" style="width: 26px; height: 26px;"></i>
                Laporan Bulanan {{ $rtLabel }} ke RW 013
            </h4>
            <p class="text-muted small mb-0">
                Dokumentasi pelaporan berkala administrasi kependudukan, keuangan, kegiatan sosial, dan siskamling lingkungan {{ $rtLabel }}.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rt.laporan.create') }}" class="wm-btn-primary">
                <i data-lucide="plus-circle" style="width: 17px; height: 17px;"></i> Kirim / Perbarui Laporan
            </a>
            <a href="{{ route('dashboard.rt') }}" class="wm-btn-outline">
                <i data-lucide="arrow-left" style="width: 17px; height: 17px;"></i> Dashboard RT
            </a>
        </div>
    </div>

    {{-- Status Banner --}}
    @php
        $status = $myReport['status'] ?? 'Menunggu pemeriksaan';
        $badgeClass = match($status) {
            'Lengkap' => 'bg-success',
            'Perlu perbaikan' => 'bg-warning text-dark',
            'Menunggu pemeriksaan' => 'bg-info text-white',
            default => 'bg-secondary text-white'
        };
    @endphp

    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%); border-left: 5px solid {{ $status === 'Lengkap' ? 'var(--wm-green)' : ($status === 'Perlu perbaikan' ? '#D97706' : 'var(--wm-primary)') }};">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="small text-muted text-uppercase fw-bold">Status Laporan Periode {{ $myReport['periode'] ?? 'November 2026' }}</span>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <h5 class="fw-bold mb-0 text-dark">Laporan Administrasi & Keuangan {{ $rtLabel }}</h5>
                    <span class="badge {{ $badgeClass }}" style="border-radius: 999px; padding: 6px 14px; font-size: 0.85rem;">
                        {{ $status }}
                    </span>
                </div>
                <div class="small text-muted mt-2">
                    <i data-lucide="user" style="width: 13px; height: 13px;"></i> Diserahkan oleh: <strong>{{ $myReport['ketua_rt'] ?? $user->name }}</strong> &bull; 
                    <i data-lucide="clock" style="width: 13px; height: 13px;"></i> Pembaruan terakhir: {{ $myReport['updated_at'] ?? 'Hari ini' }}
                </div>
            </div>
            <div>
                <a href="{{ route('rt.laporan.create') }}" class="wm-btn-success">
                    <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i> Edit / Revisi Laporan
                </a>
            </div>
        </div>
    </div>

    {{-- Catatan Evaluasi RW jika ada --}}
    @if(!empty($myReport['evaluasi_rw']))
        <div class="wm-card mb-4" style="background: #FFFBEB; border: 1px solid #FDE68A;">
            <div class="d-flex align-items-start gap-3">
                <div class="p-2 rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; flex-shrink: 0;">
                    <i data-lucide="message-square-quote" style="width: 20px; height: 20px;"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1 text-dark">Catatan & Ulasan dari Ketua RW 013:</h6>
                    <p class="mb-2 text-secondary small">"{{ $myReport['evaluasi_rw'] }}"</p>
                    @if(!empty($myReport['rekomendasi_rw']))
                        <div class="p-2 rounded bg-white border small text-dark">
                            <strong><i data-lucide="arrow-right-circle" style="width:13px;height:13px;color:var(--wm-green);"></i> Rekomendasi Tindak Lanjut:</strong> {{ $myReport['rekomendasi_rw'] }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Detail 4 Pilar Laporan --}}
    <div class="row g-4 mb-4">
        
        <!-- 1. Administrasi Warga -->
        <div class="col-md-6 col-lg-3">
            <div class="wm-card h-100">
                <div class="wm-card-header d-flex justify-content-between align-items-center pb-2">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i data-lucide="users" class="text-primary me-2" style="width: 18px; height: 18px;"></i>
                        1. Kependudukan
                    </h6>
                </div>
                <div class="wm-card-body">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Jumlah KK:</span>
                        <span class="fw-bold text-dark">{{ $myReport['administrasi']['jumlah_kk'] ?? 0 }} KK</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Jumlah Warga:</span>
                        <span class="fw-bold text-dark">{{ $myReport['administrasi']['jumlah_warga'] ?? 0 }} Jiwa</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Warga Baru:</span>
                        <span class="fw-bold text-success">+{{ $myReport['administrasi']['warga_baru'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Mutasi Keluar:</span>
                        <span class="fw-bold text-danger">-{{ $myReport['administrasi']['mutasi_keluar'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Surat Pengantar:</span>
                        <span class="fw-bold text-primary">{{ $myReport['administrasi']['surat_dikeluarkan'] ?? 0 }} Surat</span>
                    </div>
                    <hr class="my-2">
                    <p class="small text-muted mb-0" style="font-size:0.75rem;">
                        <em>"{{ $myReport['administrasi']['catatan'] ?? 'Administrasi lengkap.' }}"</em>
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. Keuangan & Kas RT -->
        <div class="col-md-6 col-lg-3">
            <div class="wm-card h-100">
                <div class="wm-card-header d-flex justify-content-between align-items-center pb-2">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i data-lucide="wallet" class="text-success me-2" style="width: 18px; height: 18px;"></i>
                        2. Keuangan RT
                    </h6>
                </div>
                <div class="wm-card-body">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Saldo Awal:</span>
                        <span class="fw-bold text-dark">Rp {{ number_format($myReport['keuangan']['saldo_awal'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Pemasukan:</span>
                        <span class="fw-bold text-success">+Rp {{ number_format($myReport['keuangan']['pemasukan'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Pengeluaran:</span>
                        <span class="fw-bold text-danger">-Rp {{ number_format($myReport['keuangan']['pengeluaran'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small pt-1 border-top">
                        <span class="text-muted">Saldo Akhir:</span>
                        <span class="fw-bold text-primary">Rp {{ number_format($myReport['keuangan']['saldo_akhir'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Iuran Terkumpul:</span>
                        <span class="badge bg-success">{{ $myReport['keuangan']['iuran_terkumpul_persen'] ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Sosial & Posyandu -->
        <div class="col-md-6 col-lg-3">
            <div class="wm-card h-100">
                <div class="wm-card-header d-flex justify-content-between align-items-center pb-2">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i data-lucide="heart-pulse" class="text-danger me-2" style="width: 18px; height: 18px;"></i>
                        3. Sosial & Sehat
                    </h6>
                </div>
                <div class="wm-card-body">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Posyandu:</span>
                        <span class="fw-bold text-success">{{ $myReport['sosial']['posyandu_terlaksana'] ?? 'Ya' }}</span>
                    </div>
                    <div class="mb-2 small">
                        <span class="text-muted">Partisipasi:</span>
                        <div class="fw-bold text-dark mt-1">{{ $myReport['sosial']['partisipasi_posyandu'] ?? '-' }}</div>
                    </div>
                    <div class="mb-2 small">
                        <span class="text-muted">Kerja Bakti:</span>
                        <div class="fw-bold text-dark mt-1">{{ $myReport['sosial']['kerja_bakti'] ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Keamanan & Kebersihan -->
        <div class="col-md-6 col-lg-3">
            <div class="wm-card h-100">
                <div class="wm-card-header d-flex justify-content-between align-items-center pb-2">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i data-lucide="shield-check" class="text-warning me-2" style="width: 18px; height: 18px;"></i>
                        4. Keamanan & Lingkungan
                    </h6>
                </div>
                <div class="wm-card-body">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Ronda Berjalan:</span>
                        <span class="fw-bold text-success">{{ $myReport['keamanan']['jadwal_ronda_berjalan'] ?? 'Ya' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Bank Sampah:</span>
                        <span class="fw-bold text-dark">{{ $myReport['keamanan']['bank_sampah_terkumpul'] ?? '-' }}</span>
                    </div>
                    <div class="mb-2 small">
                        <span class="text-muted">Kejadian Menonjol:</span>
                        <div class="fw-bold text-dark mt-1">{{ $myReport['keamanan']['kejadian_menonjol'] ?? 'Aman' }}</div>
                    </div>
                    @if(!empty($myReport['usulan_rt']))
                        <div class="mt-2 pt-2 border-top small">
                            <span class="text-primary fw-bold">Usulan RT ke RW:</span>
                            <p class="text-muted mb-0 mt-1" style="font-size:0.75rem;">{{ $myReport['usulan_rt'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- Riwayat Koordinasi & Audit Trail Pelaporan --}}
    <div class="wm-card">
        <div class="wm-card-header">
            <h5 class="wm-card-title mb-0">
                <i data-lucide="history" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                Riwayat Koordinasi & Verifikasi Laporan RT - RW
            </h5>
        </div>
        <div class="wm-card-body p-0">
            <div class="list-group list-group-flush">
                @if(isset($myReport['history']) && is_array($myReport['history']))
                    @foreach($myReport['history'] as $hist)
                        <div class="list-group-item p-3 d-flex align-items-start gap-3">
                            <div class="p-2 rounded-circle bg-light border text-primary mt-1">
                                <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">{{ $hist['aktor'] ?? 'Sistem' }}</span>
                                    <span class="small text-muted">{{ $hist['tanggal'] ?? '-' }}</span>
                                </div>
                                <p class="small text-secondary mb-0">{{ $hist['aksi'] ?? '-' }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-4 text-center text-muted small">
                        Belum ada riwayat perubahan laporan.
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
