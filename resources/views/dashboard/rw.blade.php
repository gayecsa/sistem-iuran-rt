@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    
    {{-- ======================================================================
         BAGIAN ATAS: SAPAAN PERSONAL, IDENTITAS RW 013, STATUS & QUICK ACTIONS
         ====================================================================== --}}
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%); border-left: 5px solid var(--wm-primary);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1456A0&color=FFFFFF' }}"
                     alt="Avatar" class="rounded-circle shadow-sm border" style="width: 58px; height: 58px; object-fit: cover;">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0 fw-bold text-dark">Halo, {{ $user->name ?? 'Pengurus RW' }}</h4>
                        <span class="badge" style="background: var(--wm-primary); color: #fff; font-size: 0.75rem; border-radius: 999px;">
                            @if(strtolower($user->role) === 'admin')
                                Ketua RW 013 / Admin
                            @elseif(strtolower($user->role) === 'bendahara')
                                Bendahara RW 013
                            @else
                                Warga RW 013 (RT 00{{ $user->rt_number ?? 1 }})
                            @endif
                        </span>
                    </div>
                    <p class="text-muted mb-0 small mt-1">
                        <i data-lucide="map-pin" style="width: 14px; height: 14px; vertical-align: middle; color: var(--wm-green);"></i>
                        Rukun Warga 013 &bull; Kelurahan Machi &bull; Pusat Komando & Pelayanan Terpadu
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('monitoring-rt.index') }}" class="wm-btn-primary">
                    <i data-lucide="clipboard-check" style="width: 17px; height: 17px;"></i> Audit 8 RT
                </a>
                <a href="{{ route('rt.akun.index') }}" class="wm-btn-outline">
                    <i data-lucide="key-round" style="width: 17px; height: 17px;"></i> Akun 8 RT
                </a>
                <a href="{{ route('pengaduan.index') }}" class="wm-btn-outline">
                    <i data-lucide="message-square-plus" style="width: 17px; height: 17px;"></i> Pengaduan Warga
                </a>
                <a href="{{ route('kas-rw.index') }}" class="wm-btn-success">
                    <i data-lucide="wallet-cards" style="width: 17px; height: 17px;"></i> Kas RW
                </a>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         STATISTIK UTAMA (6 KARTU METRIK KETUA RW & PENGURUS)
         ====================================================================== --}}
    <div class="row g-3 mb-4">
        <!-- 1. Total RT -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Wilayah RT</span>
                    <div class="wm-stat-icon-wrap" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="building-2"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-primary">{{ $total_rt ?? 8 }}</div>
                <div class="small text-muted fw-semibold">RT 001 - RT 008 Aktif</div>
            </div>
        </div>

        <!-- 2. Total Warga -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Total Warga</span>
                    <div class="wm-stat-icon-wrap" style="background: #E3F5EC; color: #16865D;">
                        <i data-lucide="users"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-success">{{ number_format($total_warga ?? 0, 0, ',', '.') }}</div>
                <div class="small text-muted fw-semibold">
                    <span class="text-primary">{{ $total_laki ?? 0 }} L</span> &bull; 
                    <span class="text-pink">{{ $total_perempuan ?? 0 }} P</span>
                </div>
            </div>
        </div>

        <!-- 3. Laporan RT -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Laporan RT</span>
                    <div class="wm-stat-icon-wrap" style="background: #FEF3C7; color: #D97706;">
                        <i data-lucide="file-check"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-warning">{{ $total_laporan_rt ?? 8 }}</div>
                <div class="small text-muted fw-semibold">Bulan Berjalan</div>
            </div>
        </div>

        <!-- 4. Total Iuran Terkumpul -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Iuran Masuk</span>
                    <div class="wm-stat-icon-wrap" style="background: #EAF3FF; color: #2878D0;">
                        <i data-lucide="receipt"></i>
                    </div>
                </div>
                <div class="wm-stat-val" style="font-size: 1.25rem; color: #2878D0;">
                    Rp {{ number_format($total_iuran_terkumpul ?? 0, 0, ',', '.') }}
                </div>
                <div class="small text-muted fw-semibold">Iuran Warga Terverifikasi</div>
            </div>
        </div>

        <!-- 5. Saldo Kas RW -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100" style="border-top: 3px solid #16865D;">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Saldo Kas RW</span>
                    <div class="wm-stat-icon-wrap" style="background: #E3F5EC; color: #16865D;">
                        <i data-lucide="wallet-cards"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-success" style="font-size: 1.25rem;">
                    Rp {{ number_format($saldo_kas_rw ?? 0, 0, ',', '.') }}
                </div>
                <div class="small text-muted fw-semibold">Kas Tingkat RW</div>
            </div>
        </div>

        <!-- 6. Pengaduan Belum Selesai -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="wm-stat-card h-100" style="border-top: 3px solid #DC2626;">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Pengaduan Aktif</span>
                    <div class="wm-stat-icon-wrap" style="background: #FEE2E2; color: #DC2626;">
                        <i data-lucide="message-square-warning"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-danger">{{ $pengaduan_pending_count ?? 3 }}</div>
                <div class="small text-danger fw-semibold">Perlu Tindak Lanjut</div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         BAGIAN 1 & 2: MONITORING 8 RT & STATUS LAPORAN ADMINISTRASI RT
         ====================================================================== --}}
    <div class="row g-4 mb-4">
        <!-- 1. Ringkasan Monitoring RT 001 - RT 008 -->
        <div class="col-lg-8">
            <div class="wm-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i data-lucide="clipboard-check" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                            Audit & Monitoring RT (Wilayah RW 013)
                        </h5>
                        <p class="text-muted small mb-0">Evaluasi berkala kinerja administrasi, keuangan, dan sosial 8 RT binaan RW 013.</p>
                    </div>
                    <a href="{{ route('monitoring-rt.index') }}" class="wm-btn-outline" style="padding: 6px 14px; font-size: 0.82rem;">
                        Buka Halaman Audit Lengkap <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="wm-table">
                        <thead>
                            <tr>
                                <th>RT & Wilayah</th>
                                <th>Ketua RT</th>
                                <th>Warga</th>
                                <th>Status Laporan</th>
                                <th>Administrasi</th>
                                <th>Keuangan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rt_monitoring_list ?? [] as $rt)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary text-white rounded-circle p-2 fw-bold" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                {{ $rt['nomor'] }}
                                            </span>
                                            <div>
                                                <strong class="text-dark">{{ $rt['nama'] }}</strong>
                                                <div class="text-muted" style="font-size: 0.74rem;">RW 013</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $rt['ketua_rt'] }}</div>
                                        <div class="text-muted small">{{ $rt['kontak'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $rt['jumlah_kk'] }} KK</span>
                                        <div class="text-muted small">{{ $rt['jumlah_warga'] }} Jiwa</div>
                                    </td>
                                    <td>
                                        @if($rt['status_laporan'] === 'Lengkap')
                                            <span class="status-badge status-lengkap"><i data-lucide="check-circle-2" style="width: 12px; height: 12px;"></i> Lengkap</span>
                                        @elseif($rt['status_laporan'] === 'Perlu diperiksa')
                                            <span class="status-badge status-periksa"><i data-lucide="alert-circle" style="width: 12px; height: 12px;"></i> Perlu diperiksa</span>
                                        @elseif($rt['status_laporan'] === 'Belum dilaporkan')
                                            <span class="status-badge status-belum"><i data-lucide="clock" style="width: 12px; height: 12px;"></i> Belum lapor</span>
                                        @else
                                            <span class="status-badge status-tindak-lanjut"><i data-lucide="alert-triangle" style="width: 12px; height: 12px;"></i> Tindak lanjut</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="fw-semibold text-dark">{{ Str::limit($rt['administrasi'], 22) }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($rt['keuangan'], 20) }}</small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('monitoring-rt.show', $rt['rt']) }}" class="btn btn-sm btn-light border rounded-pill px-2" title="Detail Evaluasi">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. Status Laporan Administrasi & Progress RW -->
        <div class="col-lg-4">
            <div class="wm-card h-100">
                <h6 class="fw-bold mb-1 text-dark">
                    <i data-lucide="pie-chart" class="me-2 text-success" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                    Kelengkapan Laporan Administrasi
                </h6>
                <p class="text-muted small mb-3">Monitoring penyampaian berkas bulanan 8 RT.</p>

                <!-- Status Progress Breakdown -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-success"><i class="fas fa-check-circle me-1"></i>Lengkap (3 RT)</span>
                        <span class="small fw-bold text-success">37.5%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px; border-radius: 6px;">
                        <div class="progress-bar bg-success" style="width: 37.5%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-warning"><i class="fas fa-exclamation-circle me-1"></i>Perlu Diperiksa (2 RT)</span>
                        <span class="small fw-bold text-warning">25.0%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px; border-radius: 6px;">
                        <div class="progress-bar bg-warning" style="width: 25%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Perlu Tindak Lanjut (2 RT)</span>
                        <span class="small fw-bold text-danger">25.0%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px; border-radius: 6px;">
                        <div class="progress-bar bg-danger" style="width: 25%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-muted"><i class="fas fa-clock me-1"></i>Belum Lapor (1 RT)</span>
                        <span class="small fw-bold text-muted">12.5%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 6px;">
                        <div class="progress-bar bg-secondary" style="width: 12.5%"></div>
                    </div>
                </div>

                <!-- Banner Peta Wilayah RW 013 -->
                <div class="p-3 rounded-4" style="background: linear-gradient(135deg, #EAF3FF 0%, #DCEEFF 100%); border: 1px solid #BFDBFE;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-primary text-white rounded-3">
                            <i data-lucide="map-pin" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1 text-primary">Peta & Batas Wilayah RW 013</h6>
                            <p class="text-muted small mb-0">Cakupan RT 001 s/d RT 008, posyandu, dan CCTV.</p>
                        </div>
                    </div>
                    <a href="{{ route('peta.index') }}" class="btn btn-sm btn-primary w-100 rounded-pill mt-3 fw-bold">
                        Buka Peta Wilayah RW
                    </a>
                </div>

                <!-- Banner Manajemen Akun RT -->
                <div class="p-3 rounded-4 mt-3" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border: 1px solid #BBF7D0;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-success text-white rounded-3">
                            <i data-lucide="key-round" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1 text-success">Kelola Akun Ketua RT (8 RT)</h6>
                            <p class="text-muted small mb-0">Kontrol kredensial login, aktivasi & reset password.</p>
                        </div>
                    </div>
                    <a href="{{ route('rt.akun.index') }}" class="btn btn-sm btn-success w-100 rounded-pill mt-3 fw-bold">
                        Buka Manajemen Akun RT
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         BAGIAN 3: RINGKASAN PEMASUKAN DAN PENGELUARAN KEUANGAN RW
         ====================================================================== --}}
    <div class="wm-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i data-lucide="wallet-cards" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Ringkasan Keuangan RW 013 (Transparansi Kas & Iuran)
                </h5>
                <p class="text-muted small mb-0">Pemisahan jelas antara Kas RW, Rekap Iuran Warga per RT, dan Kas Operasional RT.</p>
            </div>
            <a href="{{ route('kas-rw.index') }}" class="wm-btn-success" style="padding: 7px 16px; font-size: 0.85rem;">
                <i data-lucide="file-spreadsheet" style="width: 16px; height: 16px;"></i> Rekap Keuangan RW
            </a>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="p-3 rounded-4 border bg-light text-center h-100">
                    <span class="text-muted small fw-bold text-uppercase">Total Pemasukan Kas RW</span>
                    <h4 class="fw-bold text-success mt-2 mb-1">Rp {{ number_format($pemasukan_rw ?? 42800000, 0, ',', '.') }}</h4>
                    <small class="text-muted">Iuran Warga, Alokasi Kelurahan & Donasi</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-4 border bg-light text-center h-100">
                    <span class="text-muted small fw-bold text-uppercase">Total Pengeluaran Kas RW</span>
                    <h4 class="fw-bold text-danger mt-2 mb-1">Rp {{ number_format($pengeluaran_rw ?? 8300000, 0, ',', '.') }}</h4>
                    <small class="text-muted">Operasional, Keamanan, Kebersihan & Sosial</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-4 border h-100 text-center" style="background: #E3F5EC; border-color: rgba(22, 134, 93, 0.3) !important;">
                    <span class="text-success small fw-bold text-uppercase">Saldo Kas RW Saat Ini</span>
                    <h4 class="fw-bold text-success mt-2 mb-1">Rp {{ number_format($saldo_kas_rw ?? 34500000, 0, ',', '.') }}</h4>
                    <small class="text-success fw-semibold">Tersimpan Aman & Terverifikasi Pengurus</small>
                </div>
            </div>
        </div>

        <!-- Catatan Transparansi -->
        <div class="mt-3 p-3 rounded-3 border bg-white small text-muted d-flex align-items-center gap-2">
            <i data-lucide="shield-check" class="text-success flex-shrink-0" style="width: 20px; height: 20px;"></i>
            <span>
                <strong>Prinsip Transparansi RW 013:</strong> Saldo kas RT dikelola mandiri oleh masing-masing bendahara RT, sedangkan Kas RW dihimpun khusus untuk kegiatan lintas RT, sarana prasarana umum, serta penanganan tanggap darurat warga.
            </span>
        </div>
    </div>

    {{-- ======================================================================
         BAGIAN 4 & 5: PENGADUAN MASYARAKAT TERBARU & AKTIVITAS LINGKUNGAN
         ====================================================================== --}}
    <div class="row g-4 mb-4">
        <!-- 4. Pengaduan Masyarakat Terbaru -->
        <div class="col-lg-7">
            <div class="wm-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i data-lucide="message-square-warning" class="me-2 text-danger" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                            Pengaduan Masyarakat Terbaru
                        </h5>
                        <p class="text-muted small mb-0">Laporan keluhan, keamanan, dan sarana umum dari warga RW 013.</p>
                    </div>
                    <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                        Lihat Semua Pengaduan
                    </a>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse($pengaduan_terbaru ?? [] as $adu)
                        <div class="p-3 rounded-4 border bg-light">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <span class="badge bg-primary text-white me-1">{{ $adu['rt'] }}</span>
                                    <span class="badge bg-light text-dark border">{{ $adu['kategori'] }}</span>
                                    <strong class="d-block text-dark mt-1">{{ $adu['lokasi'] }}</strong>
                                </div>
                                <div>
                                    @if($adu['status'] === 'Selesai')
                                        <span class="status-badge status-lengkap"><i data-lucide="check" style="width: 12px; height: 12px;"></i> Selesai</span>
                                    @elseif($adu['status'] === 'Diproses')
                                        <span class="status-badge status-periksa"><i data-lucide="clock" style="width: 12px; height: 12px;"></i> Diproses</span>
                                    @else
                                        <span class="status-badge status-tindak-lanjut"><i data-lucide="alert-triangle" style="width: 12px; height: 12px;"></i> Menunggu</span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-muted small mb-2">{{ $adu['deskripsi'] }}</p>
                            <div class="p-2 rounded-3 bg-white border small text-secondary">
                                <strong class="text-dark"><i data-lucide="info" style="width: 14px; height: 14px; vertical-align: middle;"></i> Tindak Lanjut RW:</strong> 
                                {{ $adu['catatan_tindak_lanjut'] ?? 'Menunggu tindak lanjut dari pengurus terkait.' }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i data-lucide="check-check" class="mb-2" style="width: 36px; height: 36px;"></i>
                            <p class="mb-0">Tidak ada pengaduan aktif saat ini. Lingkungan RW 013 tertib dan aman.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 5. Aktivitas Lingkungan Terbaru & Layanan Warga -->
        <div class="col-lg-5">
            <div class="wm-card h-100">
                <h5 class="fw-bold mb-1 text-dark">
                    <i data-lucide="shield-check" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Aktivitas Lingkungan & Keamanan
                </h5>
                <p class="text-muted small mb-3">Pantauan keamanan pos ronda, bank sampah, dan layanan kesehatan.</p>

                <!-- Status Ronda Malam Ini -->
                <div class="p-3 rounded-4 border mb-3" style="background: linear-gradient(135deg, #123766 0%, #1A4D8C 100%); color: #fff;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-success text-white fw-bold"><i class="fas fa-moon me-1"></i>Ronda Malam Ini</span>
                        <span class="badge bg-light text-dark">{{ $aktivitas_lingkungan['ronda_malam_ini']['jam'] ?? '22.00 - 04.30' }}</span>
                    </div>
                    <h6 class="fw-bold mb-1">{{ $aktivitas_lingkungan['ronda_malam_ini']['pos'] ?? 'Pos Utama RW 013' }}</h6>
                    <small class="text-light opacity-75 d-block mb-2">{{ $aktivitas_lingkungan['ronda_malam_ini']['petugas'] ?? '8 Personel Linmas & Warga' }}</small>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-white border-opacity-25 small">
                        <span>Koordinator: {{ $aktivitas_lingkungan['ronda_malam_ini']['koordinator'] ?? 'Pak Hendra' }}</span>
                        <a href="{{ route('ronda.index') }}" class="text-white text-decoration-none fw-bold">Jadwal Ronda &rarr;</a>
                    </div>
                </div>

                <!-- Bank Sampah & Posyandu Widget -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-3 rounded-4 border bg-light h-100">
                            <span class="badge bg-success text-white mb-2"><i data-lucide="recycle" style="width: 12px; height: 12px;"></i> Bank Sampah</span>
                            <div class="fw-bold text-dark small">Pusat RT 003</div>
                            <small class="text-muted d-block">Rabu, 10.00 WIB</small>
                            <a href="{{ route('bank-sampah') }}" class="small text-primary fw-bold text-decoration-none mt-2 d-inline-block">Info Layanan</a>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-4 border bg-light h-100">
                            <span class="badge bg-primary text-white mb-2"><i data-lucide="heart-pulse" style="width: 12px; height: 12px;"></i> Posyandu</span>
                            <div class="fw-bold text-dark small">Balai RT 001</div>
                            <small class="text-muted d-block">Sabtu, 08.00 WIB</small>
                            <a href="{{ route('posyandu') }}" class="small text-primary fw-bold text-decoration-none mt-2 d-inline-block">Lihat Jadwal</a>
                        </div>
                    </div>
                </div>

                <!-- CCTV Ringkasan -->
                @php $cctvs = \App\Models\Cctv::where('aktif', true)->latest()->limit(2)->get(); @endphp
                <div class="p-3 rounded-4 border bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-dark small"><i class="fas fa-video text-danger me-1"></i>CCTV Lingkungan Aktif</strong>
                        <a href="{{ route('ronda.index') }}" class="small text-primary fw-bold text-decoration-none">Pantau Semua ({{ count($cctvs) }})</a>
                    </div>
                    <div class="row g-2">
                        @forelse($cctvs as $cam)
                            <div class="col-6">
                                <div class="rounded-3 overflow-hidden position-relative bg-dark" style="aspect-ratio: 16/9;">
                                    @if(preg_match('/mjpe?g|\.cgi|snapshot|\.jpg/i', $cam->stream_url))
                                        <img src="{{ $cam->stream_url }}" alt="{{ $cam->nama }}" class="w-100 h-100" style="object-fit: cover;">
                                    @else
                                        <video src="{{ $cam->stream_url }}" class="w-100 h-100" style="object-fit: cover;" autoplay muted playsinline></video>
                                    @endif
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-1" style="font-size: 0.6rem;">LIVE</span>
                                    <div class="position-absolute bottom-0 start-0 end-0 p-1 bg-dark bg-opacity-75 text-white" style="font-size: 0.65rem;">
                                        {{ Str::limit($cam->nama, 15) }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted small py-2">
                                Kamera CCTV terhubung ke Pos Ronda Utama.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         BAGIAN 6: AGENDA KEGIATAN TERDEKAT RW 013 & PENGUMUMAN
         ====================================================================== --}}
    <div class="row g-4 mb-4">
        <!-- Agenda Kegiatan Terdekat RW 013 -->
        <div class="col-lg-7">
            <div class="wm-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i data-lucide="calendar-days" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                            Agenda Kegiatan & Rapat RW 013
                        </h5>
                        <p class="text-muted small mb-0">Rencana rapat koordinasi, kerja bakti, dan agenda kemasyarakatan.</p>
                    </div>
                    <a href="{{ route('agenda-rw.index') }}" class="wm-btn-outline" style="padding: 6px 14px; font-size: 0.82rem;">
                        Lihat Kalender Agenda
                    </a>
                </div>

                <div class="d-flex flex-column gap-3">
                    @foreach($agenda_rw ?? [] as $agenda)
                        <div class="p-3 rounded-4 border bg-light d-flex align-items-start gap-3">
                            <div class="p-3 rounded-3 text-center bg-white border" style="min-width: 75px;">
                                <i data-lucide="calendar" class="text-primary mx-auto mb-1" style="width: 20px; height: 20px;"></i>
                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ explode(',', $agenda['tanggal'])[0] }}</div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                    <h6 class="fw-bold mb-1 text-dark">{{ $agenda['judul'] }}</h6>
                                    <span class="badge {{ $agenda['badge'] }} rounded-pill">{{ $agenda['status'] }}</span>
                                </div>
                                <div class="small text-muted mb-1">
                                    <i class="far fa-clock me-1 text-primary"></i>{{ $agenda['waktu'] }} &bull; 
                                    <i class="fas fa-map-marker-alt ms-1 me-1 text-danger"></i>{{ $agenda['lokasi'] }}
                                </div>
                                <div class="small text-secondary">{{ $agenda['agenda'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Pengumuman Warga & Komposisi Warga -->
        <div class="col-lg-5">
            <div class="wm-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i data-lucide="megaphone" class="me-2 text-warning" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Pengumuman RW 013
                    </h5>
                    <a href="{{ route('pengumuman.index') }}" class="small text-primary fw-bold text-decoration-none">Arsip</a>
                </div>

                <div class="d-flex flex-column gap-2 mb-4">
                    @forelse($pengumuman ?? [] as $p)
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge {{ $p->kategori == 'Penting' ? 'bg-danger' : 'bg-primary' }}">{{ $p->kategori }}</span>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($p->tanggal_aktif)->translatedFormat('d M Y') }}</small>
                            </div>
                            <div class="fw-bold text-dark mb-1">{{ $p->judul }}</div>
                            <p class="text-muted small mb-0">{{ Str::limit($p->isi, 90) }}</p>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted small">Belum ada pengumuman baru.</div>
                    @endforelse
                </div>

                <!-- Grafik Komposisi Gender Warga (DIPERTAHANKAN) -->
                <div class="p-3 rounded-4 border bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-dark small"><i class="fas fa-venus-mars text-primary me-1"></i>Komposisi Gender Warga</strong>
                        <span class="badge bg-light text-dark border">{{ $total_warga }} Jiwa</span>
                    </div>
                    <div style="height: 140px; display: flex; align-items: center; justify-content: center;">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Chart Komposisi Gender
    const totalLaki = {{ $total_laki ?? 0 }};
    const totalPerempuan = {{ $total_perempuan ?? 0 }};
    const ctx = document.getElementById('genderChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki (' + totalLaki + ')', 'Perempuan (' + totalPerempuan + ')'],
                datasets: [{
                    data: [totalLaki, totalPerempuan],
                    backgroundColor: ['#1456A0', '#EC4899'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11, family: 'Plus Jakarta Sans' } }
                    }
                },
                cutout: '65%'
            }
        });
    }

    if (window.lucide) {
        lucide.createIcons();
    }
});
</script>
@endpush
@endsection
