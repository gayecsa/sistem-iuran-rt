@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Banner Agenda & Rapat RW 013 -->
    <div class="wm-card mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0D233A 0%, #164070 100%); color: #fff; border: none; border-radius: 16px;">
        <div class="p-4 p-md-5 position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div style="max-width: 760px;">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-light text-primary fw-bold px-3 py-1 rounded-pill" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i data-lucide="calendar-days" style="width: 14px; height: 14px; vertical-align: middle;"></i> MANAJEMEN AGENDA & KOORDINASI
                        </span>
                        <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill opacity-90" style="font-size: 11px;">
                            RW 013 Suryatmajan Danurejan
                        </span>
                        <span class="badge bg-success text-white fw-bold px-3 py-1 rounded-pill" style="font-size: 11px;">
                            <i data-lucide="shield-check" style="width: 12px; height: 12px; vertical-align: middle;"></i> Monitoring Sentral 8 RT
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Agenda & Rapat Koordinasi RW 013</h2>
                    <p class="mb-0 text-white text-opacity-75 lead fs-6">
                        Pusat penjadwalan musyawarah warga, rapat koordinasi pleno pengurus RW, serta monitoring pelaksanaan kegiatan sosial, kesehatan, dan keamanan di seluruh RT 001 s/d RT 008.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if(in_array(auth()->user()->role, ['rw', 'admin', 'super_admin']))
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold small shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAgenda">
                            <i data-lucide="plus-circle" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Buat Agenda RW Baru
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <!-- Decorative Glow -->
        <div class="position-absolute rounded-circle" style="width: 320px; height: 320px; background: rgba(255,255,255,0.04); top: -80px; right: -60px; pointer-events: none;"></div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i>
                <strong>Perhatian:</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Kartu Metrik Ringkasan Agenda RW & RT -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="wm-card p-3 bg-white border rounded-4 shadow-sm h-100 d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle text-primary agenda-kpi-circle-blue" style="background: #E3F2FD; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="calendar-clock" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <span class="small text-muted d-block">Agenda Mendatang</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $totalUpcoming }} Kegiatan</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="wm-card p-3 bg-white border rounded-4 shadow-sm h-100 d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle text-success agenda-kpi-circle-green" style="background: #E8F5E9; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="layout-grid" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <span class="small text-muted d-block">Kegiatan RT Dimonitor</span>
                    <h4 class="fw-bold text-success mb-0">{{ $totalMonitoringRt }} Kegiatan</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="wm-card p-3 bg-white border rounded-4 shadow-sm h-100 d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle text-info agenda-kpi-circle-cyan" style="background: #E0F2FE; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="check-circle-2" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <span class="small text-muted d-block">Laporan Lengkap</span>
                    <h4 class="fw-bold text-info mb-0">{{ $totalLaporanLengkap }} Selesai</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="wm-card p-3 bg-white border rounded-4 shadow-sm h-100 d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle text-secondary agenda-kpi-circle-gray" style="background: #F1F5F9; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="history" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <span class="small text-muted d-block">Riwayat Arsip Agenda</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $totalRiwayat }} Terdata</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SISTEM NAVIGASI 3 TAB HORIZONTAL (AGENDA & RAPAT RW)
         ========================================================================= -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-2">
        <ul class="nav nav-pills flex-nowrap overflow-x-auto gap-2 p-1" id="agendaTabs" role="tablist" style="scrollbar-width: thin;">
            <!-- TAB 1: AGENDA MENDATANG -->
            <li class="nav-item flex-shrink-0" role="presentation">
                <a class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'mendatang' ? 'active' : '' }}" 
                   id="tab-btn-mendatang" href="{{ route('agenda-rw.index', ['tab' => 'mendatang']) }}" role="tab">
                    <i data-lucide="calendar-clock" style="width: 17px; height: 17px;"></i>
                    <span>A. Agenda Mendatang</span>
                    <span class="badge rounded-pill bg-light text-secondary border ms-1" style="font-size: 11px;">{{ $totalUpcoming }}</span>
                </a>
            </li>

            <!-- TAB 2: AGENDA & LAPORAN RT -->
            <li class="nav-item flex-shrink-0" role="presentation">
                <a class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'monitoring_rt' ? 'active' : '' }}" 
                   id="tab-btn-monitoring-rt" href="{{ route('agenda-rw.index', ['tab' => 'monitoring_rt']) }}" role="tab">
                    <i data-lucide="layout-grid" style="width: 17px; height: 17px;"></i>
                    <span>B. Agenda & Laporan RT</span>
                    <span class="badge rounded-pill bg-light text-secondary border ms-1" style="font-size: 11px;">8 RT</span>
                </a>
            </li>

            <!-- TAB 3: RIWAYAT AGENDA -->
            <li class="nav-item flex-shrink-0" role="presentation">
                <a class="nav-link rounded-pill py-2.5 px-4 fw-bold small d-flex align-items-center gap-2 {{ $activeTab == 'riwayat' ? 'active' : '' }}" 
                   id="tab-btn-riwayat" href="{{ route('agenda-rw.index', ['tab' => 'riwayat']) }}" role="tab">
                    <i data-lucide="history" style="width: 17px; height: 17px;"></i>
                    <span>C. Riwayat Agenda</span>
                    <span class="badge rounded-pill bg-light text-secondary border ms-1" style="font-size: 11px;">{{ $totalRiwayat }} Arsip</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- =========================================================================
         KONTEN TAB SESUAI SELEKSI
         ========================================================================= -->

    <!-- =========================================================================
         TAB 1: AGENDA MENDATANG
         ========================================================================= -->
    @if($activeTab == 'mendatang')
        <!-- Filter Bar Tab Agenda Mendatang -->
        <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-3">
            <form method="GET" action="{{ route('agenda-rw.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="mendatang">
                <div class="col-12 col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Cari nama agenda / lokasi / peserta..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-3">
                    <select name="kategori" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kKey => $kVal)
                            <option value="{{ $kKey }}" {{ request('kategori') == $kKey ? 'selected' : '' }}>{{ $kVal }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="penyelenggara" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Penyelenggara</option>
                        <option value="rw" {{ request('penyelenggara') == 'rw' ? 'selected' : '' }}>Pengurus RW 013</option>
                        @for($i = 1; $i <= 8; $i++)
                            @php $rtCode = 'rt_' . str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $rtCode }}" {{ request('penyelenggara') == $rtCode ? 'selected' : '' }}>Wilayah RT 00{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold flex-grow-1">
                        <i data-lucide="filter" style="width: 14px; height: 14px; vertical-align: middle;"></i> Saring
                    </button>
                    <a href="{{ route('agenda-rw.index', ['tab' => 'mendatang']) }}" class="btn btn-sm btn-light border rounded-pill px-3 text-muted">Reset</a>
                </div>
            </form>
        </div>

        <!-- Grid Kartu Agenda Mendatang -->
        <div class="row g-4 mb-4">
            @forelse($agendaMendatang as $agenda)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="wm-card bg-white border rounded-4 shadow-sm h-100 p-4 d-flex flex-column justify-content-between position-relative overflow-hidden hover-shadow transition">
                        
                        <!-- Header Kartu: Judul, Status & Prioritas -->
                        <div>
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-2.5 flex-wrap">
                                <span class="wm-agenda-badge {{ $agenda->prioritas_badge['bg'] }}" style="{{ $agenda->prioritas_badge['style'] }}">
                                    <i data-lucide="{{ $agenda->prioritas_badge['icon'] }}" style="width: 13px; height: 13px; flex-shrink: 0;"></i>
                                    <span>{{ $agenda->prioritas_badge['label'] }}</span>
                                </span>
                                <span class="wm-agenda-badge {{ $agenda->status_badge['bg'] }}" style="{{ $agenda->status_badge['style'] }}">
                                    <span>{{ $agenda->status_badge['label'] ?? 'Status Belum Ditentukan' }}</span>
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-2 line-clamp-2" style="font-size: 1.05rem; min-height: 2.9rem; line-height: 1.45;">
                                {{ $agenda->judul }}
                            </h5>

                            <!-- Pemisah Visual -->
                            <hr class="my-2 border-slate-200">

                            <!-- Bagian Informasi: Tanggal, Waktu, Lokasi, Penyelenggara, Kategori -->
                            <div class="d-flex flex-column gap-2 mb-3 small text-secondary">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-1.5 rounded-3 bg-light text-primary d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                        <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                                    </div>
                                    <strong class="text-dark">{{ $agenda->formatted_tanggal }}</strong>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-1.5 rounded-3 bg-light text-primary d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                        <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                                    </div>
                                    <span>{{ $agenda->waktu }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-1.5 rounded-3 bg-light text-danger d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                        <i data-lucide="map-pin" style="width: 14px; height: 14px;"></i>
                                    </div>
                                    <span class="text-truncate" title="{{ $agenda->lokasi }}">{{ $agenda->lokasi }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-1.5 rounded-3 bg-light text-success d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                        <i data-lucide="building" style="width: 14px; height: 14px;"></i>
                                    </div>
                                    <span class="fw-semibold text-dark">{{ $agenda->penyelenggara_label }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-1.5 rounded-3 bg-light text-info d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                        <i data-lucide="tag" style="width: 14px; height: 14px;"></i>
                                    </div>
                                    <span class="badge bg-light text-secondary border">{{ $agenda->kategori_label }}</span>
                                </div>
                            </div>

                            <!-- Deskripsi Singkat -->
                            <p class="text-muted small mb-3 p-2.5 rounded-3 bg-light-subtle border line-clamp-2" style="font-size: 0.82rem;">
                                {{ Str::limit($agenda->deskripsi ?: 'Tidak ada rincian catatan tambahan.', 110) }}
                            </p>
                        </div>

                        <!-- Bagian Bawah: Aksi Lihat Detail & Otorisasi RW -->
                        <div class="border-top pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            @if($agenda->penyelenggara === 'rt')
                                <span class="badge bg-light text-muted border small" style="font-size: 10px;">
                                    <i data-lucide="eye" style="width: 11px; height: 11px; vertical-align: middle;"></i> Monitoring RT
                                </span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small" style="font-size: 10px;">
                                    <i data-lucide="crown" style="width: 11px; height: 11px; vertical-align: middle;"></i> Agenda Resmi RW
                                </span>
                            @endif

                            <div class="d-flex align-items-center gap-2">
                                @if($agenda->penyelenggara === 'rw' && in_array(auth()->user()->role, ['rw', 'admin', 'super_admin']))
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 small fw-semibold" data-bs-toggle="modal" data-bs-target="#modalStatusAgenda{{ $agenda->id }}" title="Ubah Status Pelaksanaan">
                                        <i data-lucide="refresh-cw" style="width: 13px; height: 13px; vertical-align: middle;"></i> Status
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 small fw-semibold" onclick="bukaDetailAgenda({{ $agenda->id }})">
                                    <i data-lucide="eye" style="width: 13px; height: 13px; vertical-align: middle;" class="me-1"></i> Lihat Detail
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Modal Update Status Khusus Agenda RW -->
                @if($agenda->penyelenggara === 'rw')
                    <div class="modal fade text-start" id="modalStatusAgenda{{ $agenda->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <form action="{{ route('agenda-rw.status', $agenda->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom">
                                        <h6 class="modal-title fw-bold text-dark">Perbarui Status Kegiatan RW</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark mb-1">Nama Agenda</label>
                                            <input type="text" class="form-control form-control-sm rounded-3 bg-light" value="{{ $agenda->judul }}" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark mb-1">Status Pelaksanaan</label>
                                            <select name="status_kegiatan" class="form-select form-select-sm rounded-3" required>
                                                <option value="akan_datang" {{ $agenda->status_kegiatan == 'akan_datang' ? 'selected' : '' }}>Akan Datang</option>
                                                <option value="sedang_berlangsung" {{ $agenda->status_kegiatan == 'sedang_berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                                                <option value="selesai" {{ $agenda->status_kegiatan == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                <option value="dibatalkan" {{ $agenda->status_kegiatan == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                                <option value="tidak_terlaksana" {{ $agenda->status_kegiatan == 'tidak_terlaksana' ? 'selected' : '' }}>Tidak Terlaksana</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark mb-1">Notulen / Ringkasan Hasil Kegiatan</label>
                                            <textarea name="ringkasan_hasil" class="form-control form-control-sm rounded-3" rows="3" placeholder="Masukkan poin kesepakatan musyawarah atau hasil kegiatan...">{{ $agenda->ringkasan_hasil }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top pt-2">
                                        <button type="button" class="btn btn-secondary rounded-pill btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-12">
                    <div class="wm-card bg-white border rounded-4 shadow-sm p-5 text-center">
                        <div class="p-3 rounded-circle bg-light text-muted mx-auto mb-3" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="calendar-x" style="width: 28px; height: 28px;"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak Ada Agenda Mendatang</h5>
                        <p class="text-muted small mb-0">Belum ada kegiatan atau musyawarah terjadwal yang sesuai dengan kriteria filter.</p>
                    </div>
                </div>
            @endforelse
        </div>
    @endif

    <!-- =========================================================================
         TAB 2: MONITORING AGENDA RT (RT 001 - RT 008)
         ========================================================================= -->
    @if($activeTab == 'monitoring_rt')
        <!-- Penjelasan Hak Akses Monitoring RW -->
        <div class="p-3 rounded-4 mb-4 border d-flex align-items-start gap-3 bg-white shadow-sm">
            <div class="p-2.5 rounded-circle text-primary" style="background: #E3F2FD;">
                <i data-lucide="shield-alert" style="width: 22px; height: 22px;"></i>
            </div>
            <div class="small text-secondary">
                <strong class="text-dark d-block mb-1">Kewenangan Monitoring Pengurus RW 013:</strong>
                Pengurus RW berwenang memantau jadwal pelaksanaan, rekapitulasi status, serta dokumen laporan kegiatan yang dilaporkan oleh 8 RT (RT 001 s/d RT 008). 
                <span class="text-primary fw-semibold">Sesuai SOP, Pengurus RW tidak memiliki hak mengubah atau menghapus agenda operasional milik RT.</span>
            </div>
        </div>

        <!-- Filter Bar Tab Monitoring RT -->
        <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-3">
            <form method="GET" action="{{ route('agenda-rw.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="monitoring_rt">
                <div class="col-12 col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Cari kegiatan / penanggung jawab RT..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-2">
                    <select name="rt" class="form-select form-select-sm rounded-3">
                        <option value="">Semua RT (8 RT)</option>
                        @for($i = 1; $i <= 8; $i++)
                            @php $rtVal = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $rtVal }}" {{ request('rt') == $rtVal ? 'selected' : '' }}>RT 00{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Status Pelaksanaan</option>
                        <option value="akan_datang" {{ request('status') == 'akan_datang' ? 'selected' : '' }}>Akan Datang</option>
                        <option value="sedang_berlangsung" {{ request('status') == 'sedang_berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        <option value="tidak_terlaksana" {{ request('status') == 'tidak_terlaksana' ? 'selected' : '' }}>Tidak Terlaksana</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status_laporan" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Status Laporan</option>
                        <option value="belum_ada_laporan" {{ request('status_laporan') == 'belum_ada_laporan' ? 'selected' : '' }}>Belum Ada Laporan</option>
                        <option value="menunggu_laporan" {{ request('status_laporan') == 'menunggu_laporan' ? 'selected' : '' }}>Menunggu Laporan</option>
                        <option value="laporan_dikirim" {{ request('status_laporan') == 'laporan_dikirim' ? 'selected' : '' }}>Laporan Dikirim</option>
                        <option value="laporan_lengkap" {{ request('status_laporan') == 'laporan_lengkap' ? 'selected' : '' }}>Laporan Lengkap</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold flex-grow-1">
                        <i data-lucide="search" style="width: 14px; height: 14px; vertical-align: middle;"></i> Saring
                    </button>
                    <a href="{{ route('agenda-rw.index', ['tab' => 'monitoring_rt']) }}" class="btn btn-sm btn-light border rounded-pill px-3 text-muted">Reset</a>
                </div>
            </form>
        </div>

        <!-- Tabel Monitoring Agenda Seluruh RT -->
        <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i data-lucide="layout-grid" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Tabel Agenda & Laporan Kegiatan 8 RT
                    </h5>
                    <p class="mb-0 text-muted small">
                        Rekapitulasi agenda dan laporan kegiatan yang dilaporkan oleh masing-masing Ketua RT 001 s/d RT 008 di lingkungan RW 013.
                    </p>
                </div>
                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">
                    <i data-lucide="shield-check" style="width: 13px; height: 13px; vertical-align: middle;" class="text-success me-1"></i> Mode Read-Only Pengawas RW
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-secondary small fw-bold text-uppercase">Wilayah RT</th>
                            <th class="text-secondary small fw-bold text-uppercase">Nama Kegiatan</th>
                            <th class="text-secondary small fw-bold text-uppercase">Tanggal & Waktu</th>
                            <th class="text-secondary small fw-bold text-uppercase">Kategori</th>
                            <th class="text-secondary small fw-bold text-uppercase">Penanggung Jawab</th>
                            <th class="text-secondary small fw-bold text-uppercase text-center">Status Agenda</th>
                            <th class="text-secondary small fw-bold text-uppercase text-center">Status Laporan</th>
                            <th class="text-end pe-3 text-secondary small fw-bold text-uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monitoringRtList as $rtAgenda)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: #E8F5E9; color: #16865D; border: 1px solid #C8E6C9;">
                                        {{ $rtAgenda->clean_rt }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark d-block small">{{ $rtAgenda->judul }}</strong>
                                    <small class="text-muted"><i data-lucide="map-pin" style="width: 11px; height: 11px; vertical-align: middle;"></i> {{ Str::limit($rtAgenda->lokasi, 25) }}</small>
                                </td>
                                <td>
                                    <span class="text-dark d-block small fw-semibold">{{ $rtAgenda->formatted_tanggal }}</span>
                                    <small class="text-muted">{{ $rtAgenda->waktu }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border small">{{ $rtAgenda->kategori_label }}</span>
                                </td>
                                <td>
                                    <span class="small text-dark fw-semibold d-block">{{ $rtAgenda->penanggung_jawab }}</span>
                                    <small class="text-muted">{{ $rtAgenda->peserta ? Str::limit($rtAgenda->peserta, 25) : '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="wm-agenda-badge {{ $rtAgenda->status_badge['bg'] }}" style="{{ $rtAgenda->status_badge['style'] }}">
                                        {{ $rtAgenda->status_badge['label'] ?? 'Status Belum Ditentukan' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="wm-agenda-badge {{ $rtAgenda->status_laporan_badge['bg'] }}" style="{{ $rtAgenda->status_laporan_badge['style'] }}">
                                        <i data-lucide="{{ $rtAgenda->status_laporan_badge['icon'] }}" style="width: 12px; height: 12px; flex-shrink: 0;"></i>
                                        <span>{{ $rtAgenda->status_laporan_badge['label'] }}</span>
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 small fw-semibold" onclick="bukaDetailAgenda({{ $rtAgenda->id }})">
                                        <i data-lucide="eye" style="width: 13px; height: 13px; vertical-align: middle;" class="me-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada agenda kegiatan RT yang sesuai dengan filter pencarian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- =========================================================================
         TAB 3: RIWAYAT AGENDA (SELESAI / DIBATALKAN / TIDAK TERLAKSANA)
         ========================================================================= -->
    @if($activeTab == 'riwayat')
        <!-- Filter Bar Tab Riwayat -->
        <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-3">
            <form method="GET" action="{{ route('agenda-rw.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="riwayat">
                <div class="col-12 col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Cari arsip notulen / hasil..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Status Akhir</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        <option value="tidak_terlaksana" {{ request('status') == 'tidak_terlaksana' ? 'selected' : '' }}>Tidak Terlaksana</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="penyelenggara" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Penyelenggara</option>
                        <option value="rw" {{ request('penyelenggara') == 'rw' ? 'selected' : '' }}>Pengurus RW 013</option>
                        @for($i = 1; $i <= 8; $i++)
                            @php $rtCode = 'rt_' . str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $rtCode }}" {{ request('penyelenggara') == $rtCode ? 'selected' : '' }}>RT 00{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="periode" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Tahun</option>
                        <option value="2026" {{ request('periode') == '2026' ? 'selected' : '' }}>Tahun 2026</option>
                        <option value="2025" {{ request('periode') == '2025' ? 'selected' : '' }}>Tahun 2025</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold flex-grow-1">
                        <i data-lucide="filter" style="width: 14px; height: 14px; vertical-align: middle;"></i> Filter Arsip
                    </button>
                    <a href="{{ route('agenda-rw.index', ['tab' => 'riwayat']) }}" class="btn btn-sm btn-light border rounded-pill px-3 text-muted">Reset</a>
                </div>
            </form>
        </div>

        <!-- Daftar Arsip Riwayat Agenda -->
        <div class="row g-4 mb-4">
            @forelse($riwayatList as $hist)
                <div class="col-12 col-lg-6">
                    <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-2.5 flex-wrap">
                                <span class="wm-agenda-badge {{ $hist->status_badge['bg'] }}" style="{{ $hist->status_badge['style'] }}">
                                    <span>{{ $hist->status_badge['label'] ?? 'Status Belum Ditentukan' }}</span>
                                </span>
                                <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-pill small" style="font-size: 11.5px;">
                                    {{ $hist->kategori_label }}
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-1">{{ $hist->judul }}</h5>
                            <small class="text-muted d-block mb-3">
                                Diselenggarakan oleh: <strong class="text-dark">{{ $hist->penyelenggara_label }}</strong> &bull; {{ $hist->formatted_tanggal }}
                            </small>

                            <!-- Ringkasan Hasil Kegiatan / Notulen -->
                            <div class="p-3 rounded-3 bg-light border mb-3">
                                <span class="small fw-bold text-dark d-block mb-1">
                                    <i data-lucide="file-text" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1 text-primary"></i>
                                    Ringkasan Hasil Kegiatan / Notulen:
                                </span>
                                <p class="small text-secondary mb-0">
                                    {{ $hist->ringkasan_hasil ?: 'Belum ada notulen atau rangkuman tertulis yang dilampirkan.' }}
                                </p>
                            </div>
                        </div>

                        <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                            <span class="badge {{ $hist->status_laporan_badge['bg'] }} px-2.5 py-1 rounded-pill small">
                                <i data-lucide="{{ $hist->status_laporan_badge['icon'] }}" style="width: 12px; height: 12px; vertical-align: middle;" class="me-1"></i>
                                {{ $hist->status_laporan_badge['label'] }}
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 small fw-semibold" onclick="bukaDetailAgenda({{ $hist->id }})">
                                <i data-lucide="eye" style="width: 13px; height: 13px; vertical-align: middle;" class="me-1"></i> Lihat Dokumen Lengkap
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="wm-card bg-white border rounded-4 shadow-sm p-5 text-center">
                        <div class="p-3 rounded-circle bg-light text-muted mx-auto mb-3" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="archive" style="width: 28px; height: 28px;"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Tidak Ada Arsip Riwayat</h5>
                        <p class="text-muted small mb-0">Belum ada riwayat kegiatan yang selesai atau dibatalkan sesuai dengan filter.</p>
                    </div>
                </div>
            @endforelse
        </div>
    @endif

</div>

<!-- =========================================================================
     MODAL DETAIL LENGKAP AGENDA (11 POIN INFORMASI)
     ========================================================================= -->
<div class="modal fade" id="modalDetailAgenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="modalDetailJudul">Detail Agenda Kegiatan</h5>
                    <small class="text-muted" id="modalDetailPenyelenggara">Pengurus Lingkungan RW 013</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- Notice Banner Otorisasi / Hak Akses -->
                <div id="modalDetailHakAksesBox" class="p-3 rounded-3 mb-3 border d-flex align-items-center gap-2 small">
                    <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                    <span id="modalDetailHakAksesText">Mode Monitoring Pengurus RW 013 (Read-Only)</span>
                </div>

                <!-- 1. Status & Prioritas -->
                <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                    <span id="modalDetailBadgePrioritas" class="wm-agenda-badge">Prioritas</span>
                    <span id="modalDetailBadgeStatus" class="wm-agenda-badge">Status</span>
                    <span id="modalDetailBadgeLaporan" class="wm-agenda-badge">Laporan</span>
                </div>

                <!-- 2. Rincian Informasi Waktu & Lokasi -->
                <div class="row g-3 p-3 bg-light rounded-3 mb-3 border">
                    <div class="col-12 col-sm-6">
                        <small class="text-muted d-block">Tanggal Pelaksanaan:</small>
                        <strong class="text-dark" id="modalDetailTanggal">-</strong>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted d-block">Waktu Kegiatan:</small>
                        <strong class="text-dark" id="modalDetailWaktu">-</strong>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted d-block">Lokasi Tempat:</small>
                        <strong class="text-dark" id="modalDetailLokasi">-</strong>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted d-block">Kategori Kegiatan:</small>
                        <strong class="text-dark" id="modalDetailKategori">-</strong>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted d-block">Penanggung Jawab:</small>
                        <strong class="text-dark" id="modalDetailPenanggungJawab">-</strong>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted d-block">Target Peserta:</small>
                        <strong class="text-dark" id="modalDetailPeserta">-</strong>
                    </div>
                </div>

                <!-- 3. Deskripsi Lengkap -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Deskripsi Lengkap Kegiatan:</label>
                    <p class="p-3 bg-white rounded-3 small text-secondary border mb-0" id="modalDetailDeskripsi">-</p>
                </div>

                <!-- 4. Laporan Hasil / Notulen -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Laporan Hasil Kegiatan / Notulen Rapat:</label>
                    <div class="p-3 bg-light rounded-3 small text-secondary border" id="modalDetailNotulen">
                        Belum ada notulen atau catatan hasil kegiatan yang dimasukkan.
                    </div>
                </div>

                <!-- 5. Dokumentasi Kegiatan -->
                <div>
                    <label class="form-label small fw-bold text-dark mb-1">Dokumentasi Foto / Lampiran Kegiatan:</label>
                    <div class="p-3 text-center border rounded-3 bg-light-subtle text-muted small" id="modalDetailDokumentasi">
                        <i data-lucide="image-off" class="d-block mx-auto mb-1" style="width: 24px; height: 24px;"></i>
                        Belum ada file dokumentasi foto yang diunggah.
                    </div>
                </div>

            </div>
            <div class="modal-footer border-top pt-2">
                <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL BUAT AGENDA RW BARU (KHUSUS PENGURUS RW)
     ========================================================================= -->
<div class="modal fade" id="modalTambahAgenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('agenda-rw.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <div>
                        <h6 class="modal-title fw-bold text-dark">Buat Agenda Kegiatan / Rapat RW 013 Baru</h6>
                        <small class="text-muted">Jadwal kegiatan koordinasi tingkat Rukun Warga</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-8">
                            <label class="form-label small fw-bold text-dark mb-1">Judul Agenda Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control rounded-3" placeholder="Contoh: Rapat Pleno Koordinasi Pengurus RT & RW..." required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Kategori Kegiatan <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select rounded-3" required>
                                <option value="rapat_koordinasi" selected>Rapat Koordinasi</option>
                                <option value="kegiatan_sosial">Kegiatan Sosial</option>
                                <option value="kegiatan_warga">Kegiatan Warga</option>
                                <option value="kesehatan">Kesehatan</option>
                                <option value="kebersihan">Kebersihan</option>
                                <option value="keamanan">Keamanan</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control rounded-3" value="{{ Carbon\Carbon::now()->addDays(2)->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Waktu Pelaksanaan <span class="text-danger">*</span></label>
                            <input type="text" name="waktu" class="form-control rounded-3" placeholder="19.30 - 22.00 WIB" value="19.30 - 21.30 WIB" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Tingkat Prioritas <span class="text-danger">*</span></label>
                            <select name="prioritas" class="form-select rounded-3" required>
                                <option value="rendah">Rendah</option>
                                <option value="sedang" selected>Sedang</option>
                                <option value="tinggi">Tinggi</option>
                                <option value="darurat">Mendesak / Darurat</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Lokasi Tempat Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" class="form-control rounded-3" placeholder="Contoh: Balai Pertemuan RW 013..." value="Balai Pertemuan RW 013" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Penanggung Jawab (PIC) <span class="text-danger">*</span></label>
                            <input type="text" name="penanggung_jawab" class="form-control rounded-3" placeholder="Nama Penanggung Jawab..." value="Ketua RW 013" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Target Peserta yang Diundang</label>
                        <input type="text" name="peserta" class="form-control rounded-3" placeholder="Contoh: Pengurus RW, Seluruh Ketua RT 001 - RT 008, Tokoh Masyarakat..." value="Ketua RW, Sekretaris, Bendahara, & Seluruh Ketua RT 001 - RT 008">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Deskripsi & Rincian Agenda Kegiatan</label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="3" placeholder="Jelaskan tujuan kegiatan, poin musyawarah, dan persiapan yang dibutuhkan..."></textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_public" id="switchPublic" value="1">
                        <label class="form-check-label small text-dark fw-semibold" for="switchPublic">
                            Tampilkan agenda ini pada Portal Informasi Publik Warga (Khusus kegiatan warga non-rahasia)
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top pt-2">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Terbitkan Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scoped Styles -->
<style>
    #agendaTabs .nav-link {
        color: #475569;
        background-color: transparent;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    #agendaTabs .nav-link:hover {
        color: #0f172a;
        background-color: #f1f5f9;
    }
    #agendaTabs .nav-link.active {
        color: #ffffff !important;
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    #agendaTabs .nav-link.active .badge {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.4) !important;
    }
    .hover-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.03) !important;
    }
    .transition {
        transition: all 0.2s ease-in-out;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ==============================================================
       STANDARISASI BADGE PRIORITAS & STATUS AGENDA RW 013
       Menjamin kontras tinggi, font jelas, padding rapi, no clipping
       ============================================================== */
    .wm-agenda-badge {
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        font-size: 0.75rem !important; /* 12px terbaca jelas */
        line-height: 1.25 !important;
        font-weight: 600 !important;
        padding: 4px 11px !important;
        border-radius: 9999px !important;
        white-space: nowrap !important;
        letter-spacing: 0.015em !important;
        box-sizing: border-box !important;
        flex-shrink: 0 !important;
        text-decoration: none !important;
    }

    /* 1. Badge Prioritas */
    .wm-badge-prioritas-tinggi {
        background-color: #FEE2E2 !important;
        color: #991B1B !important;
        border: 1px solid #FCA5A5 !important;
    }
    .wm-badge-prioritas-sedang {
        background-color: #FEF3C7 !important;
        color: #92400E !important;
        border: 1px solid #FCD34D !important;
    }
    .wm-badge-prioritas-rendah {
        background-color: #DCFCE7 !important;
        color: #166534 !important;
        border: 1px solid #86EFAC !important;
    }
    .wm-badge-prioritas-darurat {
        background-color: #DC2626 !important;
        color: #FFFFFF !important;
        border: 1px solid #B91C1C !important;
    }

    /* 2. Badge Status Kegiatan */
    .wm-badge-status-akan_datang {
        background-color: #DBEAFE !important;
        color: #1E40AF !important;
        border: 1px solid #93C5FD !important;
    }
    .wm-badge-status-sedang_berlangsung {
        background-color: #FEF3C7 !important;
        color: #B45309 !important;
        border: 1px solid #FCD34D !important;
    }
    .wm-badge-status-selesai {
        background-color: #DCFCE7 !important;
        color: #15803D !important;
        border: 1px solid #86EFAC !important;
    }
    .wm-badge-status-dibatalkan {
        background-color: #FEE2E2 !important;
        color: #B91C1C !important;
        border: 1px solid #FCA5A5 !important;
    }
    .wm-badge-status-tidak_terlaksana {
        background-color: #F1F5F9 !important;
        color: #475569 !important;
        border: 1px solid #CBD5E1 !important;
    }
    .wm-badge-status-fallback {
        background-color: #F1F5F9 !important;
        color: #64748B !important;
        border: 1px solid #CBD5E1 !important;
    }

    /* 3. Badge Status Laporan */
    .wm-badge-laporan-lengkap {
        background-color: #DCFCE7 !important;
        color: #166534 !important;
        border: 1px solid #86EFAC !important;
    }
    .wm-badge-laporan-dikirim {
        background-color: #E0F2FE !important;
        color: #0369A1 !important;
        border: 1px solid #BAE6FD !important;
    }
    .wm-badge-laporan-menunggu {
        background-color: #FEF3C7 !important;
        color: #92400E !important;
        border: 1px solid #FCD34D !important;
    }
    .wm-badge-laporan-belum {
        background-color: #F1F5F9 !important;
        color: #475569 !important;
        border: 1px solid #CBD5E1 !important;
    }
</style>

<!-- Script AJAX Detail Modal -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function bukaDetailAgenda(id) {
        fetch(`/agenda-rw/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(res => {
            if (res.success && res.data) {
                const d = res.data;
                document.getElementById('modalDetailJudul').innerText = d.judul;
                document.getElementById('modalDetailPenyelenggara').innerText = d.penyelenggara_label;
                document.getElementById('modalDetailTanggal').innerText = d.tanggal;
                document.getElementById('modalDetailWaktu').innerText = d.waktu;
                document.getElementById('modalDetailLokasi').innerText = d.lokasi;
                document.getElementById('modalDetailKategori').innerText = d.kategori_label;
                document.getElementById('modalDetailPenanggungJawab').innerText = d.penanggung_jawab;
                document.getElementById('modalDetailPeserta').innerText = d.peserta;
                document.getElementById('modalDetailDeskripsi').innerText = d.deskripsi || 'Tidak ada deskripsi tambahan.';

                // Status Badge
                const bStatus = document.getElementById('modalDetailBadgeStatus');
                bStatus.className = 'wm-agenda-badge ' + (d.status_badge.bg || '');
                bStatus.setAttribute('style', d.status_badge.style || '');
                bStatus.innerHTML = `<span>${d.status_badge.label || 'Status Belum Ditentukan'}</span>`;

                // Prioritas Badge
                const bPrior = document.getElementById('modalDetailBadgePrioritas');
                bPrior.className = 'wm-agenda-badge ' + (d.prioritas_badge.bg || '');
                bPrior.setAttribute('style', d.prioritas_badge.style || '');
                bPrior.innerHTML = `<i data-lucide="${d.prioritas_badge.icon || 'clock'}" style="width: 13px; height: 13px; flex-shrink: 0;"></i> <span>${d.prioritas_badge.label || 'Prioritas Sedang'}</span>`;

                // Laporan Badge
                const bLap = document.getElementById('modalDetailBadgeLaporan');
                bLap.className = 'wm-agenda-badge ' + (d.status_laporan_badge.bg || '');
                bLap.setAttribute('style', d.status_laporan_badge.style || '');
                bLap.innerHTML = `<i data-lucide="${d.status_laporan_badge.icon || 'file-text'}" style="width: 13px; height: 13px; flex-shrink: 0;"></i> <span>${d.status_laporan_badge.label || 'Belum Ada Laporan'}</span>`;

                // Notulen
                const elNotulen = document.getElementById('modalDetailNotulen');
                if (d.ringkasan_hasil) {
                    elNotulen.innerHTML = `<p class="mb-0 text-dark">${d.ringkasan_hasil}</p>`;
                } else {
                    elNotulen.innerHTML = `<em class="text-muted">Belum ada notulen atau catatan hasil kegiatan yang dimasukkan.</em>`;
                }

                // Hak Akses Banner
                const elHakAksesBox = document.getElementById('modalDetailHakAksesBox');
                const elHakAksesText = document.getElementById('modalDetailHakAksesText');
                if (d.is_rt_agenda) {
                    elHakAksesBox.className = 'p-3 rounded-3 mb-3 border d-flex align-items-center gap-2 small bg-warning-subtle text-warning-emphasis border-warning-subtle';
                    elHakAksesText.innerText = 'Agenda Wilayah RT • Mode Monitoring Pengurus RW 013 (Akses Read-Only Terkunci)';
                } else {
                    elHakAksesBox.className = 'p-3 rounded-3 mb-3 border d-flex align-items-center gap-2 small bg-primary-subtle text-primary border-primary-subtle';
                    elHakAksesText.innerText = 'Agenda Resmi Lingkungan RW 013 • Pengurus RW Memiliki Hak Kelola Penuh';
                }

                // Show modal
                const modalEl = document.getElementById('modalDetailAgenda');
                if (typeof bootstrap !== 'undefined') {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            } else {
                alert('Gagal memuat rincian agenda.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan saat mengambil data agenda.');
        });
    }
</script>
@endsection
