@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    {{-- ======================================================================
         BAGIAN ATAS: SAPAAN PERSONAL, IDENTITAS RT, STATUS & QUICK ACTIONS
         ====================================================================== --}}
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #FFFFFF 0%, #F4FBF7 100%); border-left: 5px solid var(--wm-green);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=16865D&color=FFFFFF' }}"
                     alt="Avatar" class="rounded-circle shadow-sm border" style="width: 58px; height: 58px; object-fit: cover;">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0 fw-bold text-dark">Halo, {{ $user->name }}</h4>
                        <span class="badge" style="background: var(--wm-green); color: #fff; font-size: 0.8rem; border-radius: 999px; padding: 6px 12px;">
                            <i data-lucide="shield" style="width:13px;height:13px;display:inline-block;vertical-align:middle;margin-right:2px;"></i>
                            Ketua {{ $rtLabel }} / RW 013
                        </span>
                        @php
                            $lapStatus = $myReport['status'] ?? 'Menunggu pemeriksaan';
                            $badgeColor = match($lapStatus) {
                                'Lengkap' => 'bg-success',
                                'Perlu perbaikan' => 'bg-warning text-dark',
                                'Menunggu pemeriksaan' => 'bg-info text-white',
                                default => 'bg-secondary text-white'
                            };
                        @endphp
                        <span class="badge {{ $badgeColor }}" style="border-radius: 999px; padding: 6px 12px; font-size:0.75rem;">
                            Status Laporan ke RW: {{ $lapStatus }}
                        </span>
                    </div>
                    <p class="text-muted mb-0 small mt-1">
                        <i data-lucide="map-pin" style="width: 14px; height: 14px; vertical-align: middle; color: var(--wm-green);"></i>
                        Pusat Pengelolaan Administrasi, Pelayanan Warga & Keuangan Lingkungan {{ $rtLabel }}
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('warga.index') }}" class="wm-btn-primary">
                    <i data-lucide="users" style="width: 17px; height: 17px;"></i> Warga {{ $rtLabel }}
                </a>
                <a href="{{ route('rt.laporan.create') }}" class="wm-btn-success">
                    <i data-lucide="send" style="width: 17px; height: 17px;"></i> Kirim Laporan ke RW
                </a>
                <a href="{{ route('kas-rt.index') }}" class="wm-btn-outline">
                    <i data-lucide="wallet" style="width: 17px; height: 17px;"></i> Kas RT
                </a>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         ALERT / BANNER EVALUASI DARI KETUA RW 013
         ====================================================================== --}}
    @if(!empty($myReport['evaluasi_rw']))
        <div class="wm-card mb-4" style="background: #FFFBEB; border: 1px solid #FDE68A;">
            <div class="d-flex align-items-start gap-3">
                <div class="p-2 rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; flex-shrink: 0;">
                    <i data-lucide="message-square-quote" style="width: 22px; height: 22px;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h6 class="fw-bold mb-1 text-dark">
                            <i data-lucide="shield-check" style="width: 16px; height: 16px; color: var(--wm-primary);"></i>
                            Catatan Evaluasi dari Pengurus RW 013 untuk {{ $rtLabel }}
                        </h6>
                        <span class="small text-muted">{{ $myReport['updated_at'] ?? 'Bulan Berjalan' }}</span>
                    </div>
                    <p class="mb-2 text-secondary small" style="line-height: 1.5;">
                        "{{ $myReport['evaluasi_rw'] }}"
                    </p>
                    @if(!empty($myReport['rekomendasi_rw']))
                        <div class="p-2 rounded bg-white border small text-dark mb-2">
                            <strong><i data-lucide="arrow-right-circle" style="width:14px;height:14px;color:var(--wm-green);"></i> Rekomendasi RW:</strong>
                            {{ $myReport['rekomendasi_rw'] }}
                        </div>
                    @endif
                    <div class="d-flex gap-2">
                        <a href="{{ route('rt.evaluasi') }}" class="btn btn-sm btn-outline-dark" style="font-size:0.75rem;">
                            <i data-lucide="clipboard-list" style="width:14px;height:14px;"></i> Riwayat Evaluasi Lengkap
                        </a>
                        <a href="{{ route('rt.laporan.create') }}" class="btn btn-sm btn-primary" style="font-size:0.75rem;">
                            <i data-lucide="file-edit" style="width:14px;height:14px;"></i> Tindak Lanjut / Perbarui Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ======================================================================
         STATISTIK UTAMA KHUSUS RT (6 KARTU METRIK)
         ====================================================================== --}}
    <div class="row g-3 mb-4">
        <!-- 1. Total Warga RT -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Warga {{ $rtLabel }}</span>
                    <div class="wm-stat-icon-wrap" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="users"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-primary">{{ number_format($totalWarga, 0, ',', '.') }}</div>
                <div class="small text-muted fw-semibold">
                    <span class="text-primary">{{ $totalLaki }} L</span> &bull; 
                    <span class="text-danger">{{ $totalPerempuan }} P</span>
                </div>
            </div>
        </div>

        <!-- 2. Kepala Keluarga (KK) RT -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Kepala Keluarga</span>
                    <div class="wm-stat-icon-wrap" style="background: #E3F5EC; color: #16865D;">
                        <i data-lucide="home"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-success">{{ $totalKk }}</div>
                <div class="small text-muted fw-semibold">Kartu Keluarga Terdata</div>
            </div>
        </div>

        <!-- 3. Warga Aktif RT -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Warga Aktif</span>
                    <div class="wm-stat-icon-wrap" style="background: #FEF3C7; color: #D97706;">
                        <i data-lucide="user-check"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-warning">{{ $wargaAktif }}</div>
                <div class="small text-muted fw-semibold">Status Domisili Sah</div>
            </div>
        </div>

        <!-- 4. Iuran Terkumpul RT -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Iuran RT Terkumpul</span>
                    <div class="wm-stat-icon-wrap" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="receipt"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-primary" style="font-size: 1.15rem;">
                    Rp {{ number_format($iuranTerkumpul, 0, ',', '.') }}
                </div>
                <div class="small text-success fw-semibold">
                    <i data-lucide="trending-up" style="width: 12px; height: 12px;"></i> Bulan Ini
                </div>
            </div>
        </div>

        <!-- 5. Saldo Kas RT -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Kas {{ $rtLabel }}</span>
                    <div class="wm-stat-icon-wrap" style="background: #E3F5EC; color: #16865D;">
                        <i data-lucide="wallet"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-success" style="font-size: 1.15rem;">
                    Rp {{ number_format($saldoKasRt, 0, ',', '.') }}
                </div>
                <div class="small text-muted fw-semibold">Saldo Kas Kasir RT</div>
            </div>
        </div>

        <!-- 6. Pengaduan Warga RT -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Pengaduan RT</span>
                    <div class="wm-stat-icon-wrap" style="background: #FEE2E2; color: #DC2626;">
                        <i data-lucide="message-square-warning"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-danger">{{ count($pengaduanRt) }}</div>
                <div class="small text-muted fw-semibold">Aspirasi Lingkungan RT</div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         KONTEN UTAMA: 2 KOLOM
         ====================================================================== --}}
    <div class="row g-4">
        
        <!-- KOLOM KIRI (PELAYANAN WARGA, SURAT, & PENGADUAN) -->
        <div class="col-lg-7">
            
            <!-- 1. Permohonan Surat Pengantar Warga RT -->
            <div class="wm-card mb-4">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="wm-card-title mb-0">
                            <i data-lucide="file-signature" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                            Permohonan Surat Pengantar Warga
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Daftar warga yang mengajukan surat pengantar domisili, KTP, SKCK, dan usaha.</p>
                    </div>
                    <a href="{{ route('surat.index') }}" class="wm-btn-outline" style="padding: 6px 12px; font-size: 0.8rem;">
                        Kelola Semua
                    </a>
                </div>
                <div class="wm-card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr style="font-size: 0.8rem; text-transform: uppercase;">
                                    <th>Nama Pemohon</th>
                                    <th>Jenis Surat</th>
                                    <th>Waktu Pengajuan</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permohonanSurat as $surat)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $surat['nama'] }}</div>
                                            <small class="text-muted">Warga {{ $rtLabel }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $surat['jenis'] }}</span>
                                        </td>
                                        <td class="small text-muted">{{ $surat['tanggal'] }}</td>
                                        <td>
                                            @if($surat['status'] === 'Selesai')
                                                <span class="badge bg-success">Selesai</span>
                                            @elseif($surat['status'] === 'Diproses')
                                                <span class="badge bg-warning text-dark">Diproses</span>
                                            @else
                                                <span class="badge bg-info text-white">Menunggu TTD</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('surat.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:0.75rem;">
                                                <i data-lucide="check-circle" style="width:13px;height:13px;"></i> Proses
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            Tidak ada permohonan surat yang sedang menunggu.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. Pengaduan Warga RT Sendiri -->
            <div class="wm-card mb-4">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="wm-card-title mb-0">
                            <i data-lucide="message-square-warning" class="text-danger me-2" style="width: 20px; height: 20px;"></i>
                            Pengaduan & Aspirasi Warga {{ $rtLabel }}
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Laporan masalah kebersihan, penerangan jalan, saluran air, dan keamanan di RT.</p>
                    </div>
                    <a href="{{ route('pengaduan.index') }}" class="wm-btn-outline" style="padding: 6px 12px; font-size: 0.8rem;">
                        Semua Aduan
                    </a>
                </div>
                <div class="wm-card-body">
                    @forelse($pengaduanRt as $aduan)
                        <div class="p-3 mb-3 rounded border" style="background: #FAFAFA;">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <span class="badge bg-light text-dark border me-1">{{ $aduan['kategori'] ?? 'Layanan' }}</span>
                                    <span class="fw-semibold text-dark">{{ $aduan['lokasi'] ?? $rtLabel }}</span>
                                </div>
                                <span class="badge {{ ($aduan['status'] ?? '') === 'Selesai' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $aduan['status'] ?? 'Menunggu' }}
                                </span>
                            </div>
                            <p class="small text-secondary mb-2">{{ $aduan['deskripsi'] ?? '-' }}</p>
                            <div class="d-flex justify-content-between align-items-center small text-muted pt-2 border-top">
                                <span><i data-lucide="user" style="width:12px;height:12px;"></i> {{ $aduan['pelapor'] ?? 'Warga' }}</span>
                                <span><i data-lucide="clock" style="width:12px;height:12px;"></i> {{ $aduan['created_at'] ?? 'Hari ini' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i data-lucide="check-circle-2" style="width: 32px; height: 32px; color: var(--wm-green); margin-bottom: 8px;"></i>
                            <p class="small mb-0">Lingkungan kondusif. Belum ada keluhan warga baru di wilayah {{ $rtLabel }}.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 3. Pintasan Manajemen Lingkungan RT -->
            <div class="row g-3">
                <div class="col-sm-4">
                    <a href="{{ route('rt.keluarga') }}" class="wm-card text-decoration-none text-dark p-3 d-flex align-items-center gap-3 h-100 hover-shadow transition">
                        <div class="p-2 rounded bg-primary text-white">
                            <i data-lucide="home" style="width:20px;height:20px;"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">Data Keluarga (KK)</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $totalKk }} KK Terdaftar</div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-4">
                    <a href="{{ route('rt.pendatang') }}" class="wm-card text-decoration-none text-dark p-3 d-flex align-items-center gap-3 h-100 hover-shadow transition">
                        <div class="p-2 rounded bg-success text-white">
                            <i data-lucide="user-check" style="width:20px;height:20px;"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">Data Pendatang</div>
                            <div class="text-muted" style="font-size:0.75rem;">Sewa / Kontrak</div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-4">
                    <a href="{{ route('rt.mutasi') }}" class="wm-card text-decoration-none text-dark p-3 d-flex align-items-center gap-3 h-100 hover-shadow transition">
                        <div class="p-2 rounded bg-warning text-dark">
                            <i data-lucide="arrow-left-right" style="width:20px;height:20px;"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">Mutasi Warga</div>
                            <div class="text-muted" style="font-size:0.75rem;">Pindah & Lahir</div>
                        </div>
                    </a>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN (KEGIATAN RT, STATUS LAPORAN, PENGUMUMAN RW) -->
        <div class="col-lg-5">
            
            <!-- 1. Status Laporan Bulanan ke RW -->
            <div class="wm-card mb-4" style="border-top: 4px solid var(--wm-primary);">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="send" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                        Laporan Bulanan ke RW 013
                    </h5>
                    <span class="badge {{ $badgeColor }}">{{ $lapStatus }}</span>
                </div>
                <div class="wm-card-body">
                    <div class="p-3 rounded mb-3" style="background: #F4F7FB;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">Periode Laporan:</span>
                            <span class="fw-bold text-dark">{{ $myReport['periode'] ?? 'November 2026' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">Capaian Iuran RT:</span>
                            <span class="fw-bold text-success">{{ $myReport['keuangan']['iuran_terkumpul_persen'] ?? 90 }}%</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Terakhir Diperbarui:</span>
                            <span class="small text-dark">{{ $myReport['updated_at'] ?? 'Hari ini' }}</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('rt.laporan') }}" class="wm-btn-primary text-center">
                            <i data-lucide="eye" style="width: 16px; height: 16px;"></i> Lihat Rincian Laporan
                        </a>
                        <a href="{{ route('rt.laporan.create') }}" class="wm-btn-outline text-center">
                            <i data-lucide="file-plus" style="width: 16px; height: 16px;"></i> Buat / Perbarui Laporan RT
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Jadwal Kegiatan RT & Ronda -->
            <div class="wm-card mb-4">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <h5 class="wm-card-title mb-0">
                        <i data-lucide="calendar" class="text-success me-2" style="width: 20px; height: 20px;"></i>
                        Agenda & Kegiatan {{ $rtLabel }}
                    </h5>
                    <a href="{{ route('ronda.index') }}" class="wm-btn-outline" style="padding: 5px 10px; font-size: 0.75rem;">
                        Pos Ronda
                    </a>
                </div>
                <div class="wm-card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($kegiatanRt as $keg)
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="fw-semibold text-dark">{{ $keg['nama'] }}</div>
                                    <span class="badge bg-light text-dark border">{{ $keg['status'] }}</span>
                                </div>
                                <div class="small text-muted mb-1">
                                    <i data-lucide="clock" style="width:12px;height:12px;"></i> {{ $keg['waktu'] }}
                                </div>
                                <div class="small text-muted">
                                    <i data-lucide="map-pin" style="width:12px;height:12px;color:var(--wm-green);"></i> {{ $keg['lokasi'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 3. Pengumuman Resmi dari RW 013 -->
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
                                    <span class="badge bg-light text-primary border">Resmi RW</span>
                                </div>
                            </a>
                        @empty
                            <div class="p-3 text-center text-muted small">
                                Belum ada pengumuman terbaru dari RW 013.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
