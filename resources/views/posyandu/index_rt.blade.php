@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Hero Banner Posyandu RT -->
    <div class="wm-card mb-4 overflow-hidden position-relative sa-hero-card" style="background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%) !important; color: #fff !important; border: none !important; border-radius: 16px;">
        <div class="p-4 p-md-5 position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div style="max-width: 720px;">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge px-3 py-1 rounded-pill fw-bold hero-badge" style="background: rgba(255, 255, 255, 0.18) !important; color: #FFFFFF !important; border: 1px solid rgba(255, 255, 255, 0.30) !important; font-size: 11px; letter-spacing: 0.5px;">
                            <i data-lucide="heart-pulse" style="width: 14px; height: 14px; vertical-align: middle;"></i> OPERASIONAL POSYANDU RT {{ $rtStr }}
                        </span>
                        <span class="badge fw-bold px-3 py-1 rounded-pill hero-badge" style="background: rgba(255, 255, 255, 0.18) !important; color: #FFFFFF !important; border: 1px solid rgba(255, 255, 255, 0.30) !important; font-size: 11px;">
                            RW 013 Sukamiskin Arcamanik
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">
                        {{ $posyanduUnit->nama_posyandu ?? 'Posyandu Melati 13 Unit RT ' . $rtStr }}
                    </h2>
                    <p class="mb-0 text-white text-opacity-75 lead fs-6">
                        Pusat pelayanan kesehatan dasar warga RT {{ $rtStr }}: pemantauan tumbuh kembang balita, kesehatan ibu hamil/menyusui, skrining lansia, dan penyuluhan gizi bersama UPTD Puskesmas Arcamanik.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-light rounded-pill px-3 py-2 fw-bold text-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPeserta">
                        <i data-lucide="user-plus" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Tambah Peserta
                    </button>
                    <button type="button" class="btn btn-success rounded-pill px-3 py-2 fw-bold text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBuatJadwal" style="background: #10B981; border: none;">
                        <i data-lucide="calendar-plus" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Buat Jadwal
                    </button>
                </div>
            </div>
        </div>
        <!-- Decorative Circle Background -->
        <div class="position-absolute rounded-circle" style="width: 320px; height: 320px; background: rgba(255,255,255,0.05); top: -80px; right: -60px; pointer-events: none;"></div>
    </div>

    <!-- BANNER PENGINGAT RESMI DARI PENGURUS RW -->
    @if($rwReminder)
        <div class="alert alert-warning border border-warning shadow-sm rounded-4 mb-4 p-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: #FFFBEB;">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="background: #FEF3C7; color: #D97706; width: 44px; height: 44px; flex-shrink: 0;">
                    <i data-lucide="bell-ring" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="badge bg-danger text-white px-2 py-1 rounded-pill fw-bold" style="font-size: 10px;">
                            PENGINGAT RESMI PENGURUS RW 013
                        </span>
                        <small class="text-muted">{{ Carbon\Carbon::parse($rwReminder->created_at)->diffForHumans() }}</small>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Penyampaian Laporan Kegiatan Posyandu RT {{ $rtStr }} Diperlukan</h6>
                    <p class="text-secondary small mb-0">
                        Pengurus RW 013 mengingatkan agar laporan hasil kegiatan Posyandu RT {{ $rtStr }} (<strong>{{ $lastEvent->judul_kegiatan ?? 'Kegiatan Terakhir' }}</strong>) segera disusun dan dikirimkan untuk melengkapi rekapitulasi monitoring kesehatan kewilayahan.
                    </p>
                </div>
            </div>
            @if($lastEvent && !$laporanTerakhir)
                <button type="button" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBuatLaporan{{ $lastEvent->id }}">
                    <i data-lucide="file-plus" style="width: 16px; height: 16px; vertical-align: middle;" class="me-1"></i> Susun Laporan Sekarang
                </button>
            @elseif($laporanTerakhir)
                <a href="{{ route('posyandu.laporan.show', $laporanTerakhir->id) }}" class="btn btn-outline-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                    <i data-lucide="eye" style="width: 16px; height: 16px; vertical-align: middle;" class="me-1"></i> Cek Berkas Laporan
                </a>
            @endif
        </div>
    @endif

    <!-- QUICK ACTIONS TOOLBAR RT -->
    <div class="wm-card mb-4 p-3 bg-white border rounded-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-2 rounded-pill fw-bold" style="background: #E8F5E9; color: #1B5E20;">
                    <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i>
                    Akses Operasional: Pengurus RT {{ $rtStr }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold small" data-bs-toggle="modal" data-bs-target="#modalCatatKehadiran">
                    <i data-lucide="check-square" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Catat Kehadiran
                </button>
                <button type="button" class="btn btn-outline-success rounded-pill px-3 py-2 fw-bold small" data-bs-toggle="modal" data-bs-target="#modalTambahKader">
                    <i data-lucide="user-check" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Kelola Kader
                </button>
            </div>
        </div>
    </div>

    <!-- 7 METRIC CARDS RINGKASAN POSYANDU RT -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Peserta -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Peserta RT {{ $rtStr }}</span>
                    <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                        <i data-lucide="users" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $totalPeserta }} <span class="fs-6 fw-normal text-muted">Jiwa</span></h3>
                <span class="small text-muted">Warga terhubung di database</span>
            </div>
        </div>

        <!-- 2. Balita Terdaftar -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Balita & Bayi</span>
                    <div class="p-2 rounded-3 bg-sky-subtle text-primary">
                        <i data-lucide="baby" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-primary">{{ $totalBalita }} <span class="fs-6 fw-normal text-muted">Anak</span></h3>
                <span class="small text-muted">Usia 0 s.d. 5 tahun</span>
            </div>
        </div>

        <!-- 3. Ibu Hamil -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Ibu Hamil & Menyusui</span>
                    <div class="p-2 rounded-3 bg-pink-subtle text-danger" style="background: #FCE4EC;">
                        <i data-lucide="heart" style="width: 20px; height: 20px; color: #E91E63;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1" style="color: #E91E63;">{{ $totalIbuHamil }} <span class="fs-6 fw-normal text-muted">Ibu</span></h3>
                <span class="small text-muted">Pemantauan gizi & KIA</span>
            </div>
        </div>

        <!-- 4. Lansia -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Lansia Terdaftar</span>
                    <div class="p-2 rounded-3 bg-amber-subtle text-warning-emphasis" style="background: #FFF8E1;">
                        <i data-lucide="smile" style="width: 20px; height: 20px; color: #D97706;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1" style="color: #D97706;">{{ $totalLansia }} <span class="fs-6 fw-normal text-muted">Lansia</span></h3>
                <span class="small text-muted">Skrining berkala (60+ th)</span>
            </div>
        </div>
    </div>

    <!-- ROW: JADWAL BERIKUTNYA, KEGIATAN TERAKHIR, STATUS LAPORAN -->
    <div class="row g-3 mb-4">
        <!-- Jadwal Berikutnya -->
        <div class="col-12 col-lg-5">
            <div class="wm-card p-4 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="fw-bold text-dark mb-0">
                        <i data-lucide="calendar" class="me-2 text-primary" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                        Jadwal Posyandu Berikutnya
                    </h6>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 small">Akan Datang</span>
                </div>
                @if($nextEvent)
                    <h5 class="fw-bold text-dark mb-1">{{ $nextEvent->judul_kegiatan }}</h5>
                    <div class="d-flex align-items-center gap-2 text-muted small mb-3">
                        <span><i data-lucide="clock" style="width: 14px; height: 14px; vertical-align: middle;"></i> {{ $nextEvent->jam_mulai }} - {{ $nextEvent->jam_selesai }} WIB</span>
                        &bull;
                        <span><i data-lucide="map-pin" style="width: 14px; height: 14px; vertical-align: middle;"></i> {{ $nextEvent->lokasi }}</span>
                    </div>
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <small class="text-secondary fw-semibold d-block mb-1">Layanan Terencana:</small>
                        <div class="d-flex flex-wrap gap-1">
                            @if(is_array($nextEvent->jenis_layanan_list))
                                @foreach($nextEvent->jenis_layanan_list as $lay)
                                    <span class="badge bg-white text-dark border px-2 py-1" style="font-size: 11px;">{{ $lay }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <small class="text-muted">Mitra Nakes: {{ $nextEvent->mitra_nakes }}</small>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalBatalJadwal{{ $nextEvent->id }}">
                            Tunda / Batal
                        </button>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i data-lucide="calendar-x" class="mb-2" style="width: 32px; height: 32px;"></i>
                        <p class="mb-2 small">Belum ada agenda Posyandu mendatang di RT {{ $rtStr }}.</p>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalBuatJadwal">
                            Jadwalkan Sekarang
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kegiatan Terakhir & Status Laporan -->
        <div class="col-12 col-lg-7">
            <div class="wm-card p-4 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="fw-bold text-dark mb-0">
                        <i data-lucide="check-circle-2" class="me-2 text-success" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                        Pelaksanaan Terakhir & Laporan Kegiatan
                    </h6>
                    @if($laporanTerakhir)
                        <span class="badge {{ $laporanTerakhir->status_badge }} rounded-pill px-3 py-1 small">
                            {{ $laporanTerakhir->status_label }}
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1 small">Perlu Laporan</span>
                    @endif
                </div>

                @if($lastEvent)
                    <div class="row align-items-center g-3">
                        <div class="col-md-7">
                            <h6 class="fw-bold text-dark mb-1">{{ $lastEvent->judul_kegiatan }}</h6>
                            <p class="text-muted small mb-2">{{ $lastEvent->tanggal_formatted }} &bull; {{ $lastEvent->lokasi }}</p>
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <span class="fs-4 fw-bold text-success">{{ $lastAttendanceCount }}</span>
                                    <small class="text-muted d-block" style="font-size: 11px;">Peserta Hadir</small>
                                </div>
                                <div class="border-start ps-3">
                                    <span class="fs-4 fw-bold text-primary">{{ $totalPeserta > 0 ? round(($lastAttendanceCount / $totalPeserta) * 100) : 0 }}%</span>
                                    <small class="text-muted d-block" style="font-size: 11px;">Partisipasi RT</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end">
                            @if($laporanTerakhir)
                                <a href="{{ route('posyandu.laporan.show', $laporanTerakhir->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold mb-2 w-100">
                                    <i data-lucide="file-text" style="width: 14px; height: 14px; vertical-align: middle;"></i> Lihat Laporan
                                </a>
                                <a href="{{ route('posyandu.laporan.cetak', $laporanTerakhir->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 w-100">
                                    <i data-lucide="printer" style="width: 14px; height: 14px; vertical-align: middle;"></i> Cetak Laporan
                                </a>
                            @else
                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold w-100 mb-2" data-bs-toggle="modal" data-bs-target="#modalBuatLaporan{{ $lastEvent->id }}">
                                    <i data-lucide="file-plus" style="width: 14px; height: 14px; vertical-align: middle;"></i> Susun Laporan Kegiatan
                                </button>
                                <small class="text-muted d-block" style="font-size: 11px;">Wajib dikirim ke RW untuk evaluasi</small>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-muted small py-3 mb-0">Belum ada riwayat kegiatan selesai pada periode ini.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- NOTIFIKASI TINDAK LANJUT PEMERIKSAAN PESERTA -->
    @if($tindakLanjutList->count() > 0)
        <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-4">
            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                <i data-lucide="alert-triangle" class="me-2 text-warning" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                Perhatian: Peserta yang Memerlukan Tindak Lanjut / Rujukan Puskesmas
            </h6>
            <div class="row g-2">
                @foreach($tindakLanjutList as $tl)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="p-3 border rounded-3 bg-light-subtle h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark">{{ $tl->peserta->user->name ?? 'Peserta' }}</strong>
                                    <span class="badge {{ $tl->peserta->kategori_badge }} px-2 py-1 rounded-pill" style="font-size: 10px;">
                                        {{ $tl->peserta->kategori_label }}
                                    </span>
                                </div>
                                <small class="text-muted d-block mb-2">Pemeriksaan: {{ $tl->tanggal_formatted }}</small>
                                <p class="text-danger small mb-0 fw-semibold">
                                    <i data-lucide="alert-circle" style="width: 12px; height: 12px; vertical-align: middle;"></i>
                                    {{ $tl->anjuran_rujukan }}
                                </p>
                            </div>
                            <div class="mt-2 pt-2 border-top text-end">
                                <a href="{{ route('posyandu.peserta.show', $tl->peserta_id) }}" class="text-decoration-none small fw-bold text-primary">
                                    Profil Peserta &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- TABS PENGELOLAAN OPERASIONAL RT -->
    <div class="wm-card bg-white border rounded-4 shadow-sm overflow-hidden mb-4">
        <ul class="nav nav-tabs nav-fill border-bottom bg-light px-3 pt-2" id="posyanduTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold py-3" id="peserta-tab" data-bs-toggle="tab" data-bs-target="#pesertaTabPane" type="button" role="tab">
                    <i data-lucide="users" class="me-2" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                    Data Peserta Posyandu RT ({{ $totalPeserta }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold py-3" id="jadwal-tab" data-bs-toggle="tab" data-bs-target="#jadwalTabPane" type="button" role="tab">
                    <i data-lucide="calendar" class="me-2" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                    Jadwal & Agenda RT
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold py-3" id="kader-tab" data-bs-toggle="tab" data-bs-target="#kaderTabPane" type="button" role="tab">
                    <i data-lucide="badge-check" class="me-2" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                    Kader & Petugas ({{ $kaderList->count() }})
                </button>
            </li>
        </ul>

        <div class="tab-content p-4" id="posyanduTabsContent">
            <!-- TAB 1: DATA PESERTA -->
            <div class="tab-pane fade show active" id="pesertaTabPane" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <form action="{{ route('posyandu') }}" method="GET" class="d-flex gap-2 flex-grow-1" style="max-width: 480px;">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm rounded-pill" placeholder="Cari nama atau NIK warga...">
                        <select name="kategori" class="form-select form-select-sm rounded-pill" style="max-width: 160px;" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            <option value="bayi" {{ request('kategori') == 'bayi' ? 'selected' : '' }}>Bayi (0-11 bln)</option>
                            <option value="balita" {{ request('kategori') == 'balita' ? 'selected' : '' }}>Balita (1-5 th)</option>
                            <option value="ibu_hamil" {{ request('kategori') == 'ibu_hamil' ? 'selected' : '' }}>Ibu Hamil</option>
                            <option value="lansia" {{ request('kategori') == 'lansia' ? 'selected' : '' }}>Lansia (60+ th)</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Cari</button>
                    </form>
                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahPeserta">
                        <i data-lucide="user-plus" style="width: 14px; height: 14px; vertical-align: middle;"></i> Tambah Peserta Baru
                    </button>
                </div>

                <!-- PANEL AUTO-DETEKSI WARGA RT BELUM TERDAFTAR (BALITA & LANSIA) -->
                <div class="wm-card bg-white border border-primary-subtle rounded-4 shadow-sm p-4 mb-4" style="border-left: 4px solid #1456A0 !important;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge px-3 py-1 rounded-pill fw-bold text-white" style="background: #1456A0; font-size: 11px;">
                                    <i data-lucide="sparkles" style="width: 12px; height: 12px; vertical-align: middle;"></i> AUTO-DETEKSI SASARAN RT {{ $rtStr }}
                                </span>
                                <h6 class="fw-bold mb-0 text-dark fs-6">Warga RT Belum Terdaftar (Auto-Deteksi)</h6>
                            </div>
                            <p class="text-muted small mb-0">
                                Berdasarkan data kependudukan RT {{ $rtStr }}, sistem otomatis memindai warga usia Balita (0–5 th) dan Lansia (≥60 th) yang belum terdaftar sebagai peserta Posyandu.
                            </p>
                        </div>
                        <span class="badge bg-light text-primary border px-3 py-2 rounded-pill fw-bold">
                            {{ count($autoDetectedRtWarga) }} Warga Terdeteksi
                        </span>
                    </div>

                    @if(!empty($autoDetectedRtWarga))
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 text-secondary small fw-bold">Nama Warga</th>
                                        <th class="text-secondary small fw-bold">Kategori Sasaran</th>
                                        <th class="text-secondary small fw-bold">Usia</th>
                                        <th class="text-secondary small fw-bold">Alamat / RT</th>
                                        <th class="text-end pe-3 text-secondary small fw-bold">Aksi Cepat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($autoDetectedRtWarga, 0, 8) as $item)
                                        <tr>
                                            <td class="ps-3">
                                                <strong class="text-dark d-block">{{ $item['user']->name }}</strong>
                                                <small class="text-muted">NIK: {{ $item['user']->masked_nik ?? $item['user']->nik }} &bull; {{ $item['user']->gender ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $item['badge_class'] }} px-2 py-1 rounded-pill" style="font-size: 11px;">
                                                    <i data-lucide="{{ $item['icon'] }}" style="width: 12px; height: 12px; vertical-align: middle;" class="me-1"></i>
                                                    {{ $item['kategori_label'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="small fw-semibold text-dark">{{ $item['usia_text'] }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted text-truncate d-block" style="max-width: 200px;">{{ $item['user']->address ?? 'RT ' . $rtStr }}</small>
                                            </td>
                                            <td class="text-end pe-3">
                                                <form action="{{ route('posyandu.peserta.store') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $item['user']->id }}">
                                                    <input type="hidden" name="kategori" value="{{ $item['kategori'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">
                                                        <i data-lucide="user-plus" style="width: 14px; height: 14px; vertical-align: middle;"></i> Daftarkan
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if(count($autoDetectedRtWarga) > 8)
                            <div class="p-2 text-center border-top bg-light-subtle small text-muted">
                                Menampilkan 8 dari {{ count($autoDetectedRtWarga) }} warga yang terdeteksi. Gunakan tombol "Tambah Peserta Baru" di atas untuk mencari nama lainnya.
                            </div>
                        @endif
                    @else
                        <div class="p-3 bg-light-subtle rounded-3 border text-center text-muted small">
                            <i data-lucide="check-circle-2" class="text-success me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                            Seluruh warga sasaran Balita (0–5 th) dan Lansia (≥60 th) di RT {{ $rtStr }} saat ini sudah terdata dan terdaftar 100% sebagai peserta Posyandu.
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-secondary small fw-bold">Nama Peserta</th>
                                <th class="text-secondary small fw-bold">Kategori</th>
                                <th class="text-secondary small fw-bold">No. KK / Keluarga</th>
                                <th class="text-secondary small fw-bold">Usia</th>
                                <th class="text-secondary small fw-bold">Hasil Terakhir</th>
                                <th class="text-end pe-3 text-secondary small fw-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesertaList as $p)
                                <tr>
                                    <td class="ps-3">
                                        <strong class="text-dark d-block">{{ $p->user->name ?? 'Warga' }}</strong>
                                        <small class="text-muted">NIK: {{ $p->user->nik ? Str::mask($p->user->nik, '*', 6, 6) : '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $p->kategori_badge }} px-2 py-1 rounded-pill small">
                                            {{ $p->kategori_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-dark d-block">{{ $p->kartuKeluarga->nomor_kk ?? '-' }}</small>
                                        <small class="text-muted">{{ $p->kartuKeluarga->kepalaKeluarga->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-dark">{{ $p->usia_text }}</small>
                                    </td>
                                    <td>
                                        @php $lp = $p->pengukurans->first(); @endphp
                                        @if($lp)
                                            <div class="small text-dark">
                                                @if($lp->berat_badan) <span>BB: <strong>{{ $lp->berat_badan }} kg</strong></span> @endif
                                                @if($lp->tinggi_badan) <span class="ms-1">TB: <strong>{{ $lp->tinggi_badan }} cm</strong></span> @endif
                                                @if($lp->tekanan_darah) <span class="ms-1">TD: <strong>{{ $lp->tekanan_darah }}</strong></span> @endif
                                            </div>
                                            <small class="text-muted" style="font-size: 10px;">{{ $lp->tanggal_formatted }}</small>
                                        @else
                                            <small class="text-muted">Belum ada pengukuran</small>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('posyandu.peserta.show', $p->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                            <i data-lucide="eye" style="width: 14px; height: 14px; vertical-align: middle;"></i> Profil & Input
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada peserta Posyandu yang terdaftar di RT {{ $rtStr }}.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $pesertaList->links() }}
                </div>
            </div>

            <!-- TAB 2: JADWAL POSYANDU RT -->
            <div class="tab-pane fade" id="jadwalTabPane" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0">Agenda Pelayanan Posyandu RT {{ $rtStr }}</h6>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalBuatJadwal">
                        <i data-lucide="plus" style="width: 14px; height: 14px; vertical-align: middle;"></i> Tambah Jadwal Baru
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-secondary small fw-bold">Tanggal & Waktu</th>
                                <th class="text-secondary small fw-bold">Kegiatan</th>
                                <th class="text-secondary small fw-bold">Lokasi</th>
                                <th class="text-secondary small fw-bold">Mitra Nakes</th>
                                <th class="text-secondary small fw-bold">Status</th>
                                <th class="text-end pe-3 text-secondary small fw-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalList as $j)
                                <tr>
                                    <td class="ps-3">
                                        <strong class="text-dark d-block">{{ $j->tanggal_formatted }}</strong>
                                        <small class="text-muted">{{ $j->jam_mulai }} - {{ $j->jam_selesai }} WIB</small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $j->judul_kegiatan }}</span>
                                    </td>
                                    <td>
                                        <small class="text-secondary">{{ $j->lokasi }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $j->mitra_nakes }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $j->status_badge }} px-2 py-1 rounded-pill small">
                                            {{ $j->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($j->status === 'selesai')
                                            @if($j->laporan)
                                                <a href="{{ route('posyandu.laporan.show', $j->laporan->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-2" title="Lihat Laporan">
                                                    <i data-lucide="file-check" style="width: 14px; height: 14px; vertical-align: middle;"></i> Laporan
                                                </a>
                                            @else
                                                <button type="button" class="btn btn-sm btn-success rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalBuatLaporan{{ $j->id }}">
                                                    Susun Laporan
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalCatatKehadiran" title="Catat Kehadiran">
                                                <i data-lucide="check-square" style="width: 14px; height: 14px; vertical-align: middle;"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalBatalJadwal{{ $j->id }}" title="Tunda/Batal">
                                                <i data-lucide="x-circle" style="width: 14px; height: 14px; vertical-align: middle;"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada agenda kegiatan posyandu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: KADER & PETUGAS RT -->
            <div class="tab-pane fade" id="kaderTabPane" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0">Daftar Kader Posyandu Terdaftar RT {{ $rtStr }}</h6>
                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahKader">
                        <i data-lucide="plus" style="width: 14px; height: 14px; vertical-align: middle;"></i> Tambah Kader RT
                    </button>
                </div>

                <div class="row g-3">
                    @forelse($kaderList as $kader)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="p-3 border rounded-3 bg-white shadow-sm d-flex align-items-center gap-3">
                                <img src="{{ $kader->foto_url }}" alt="{{ $kader->nama_kader }}" class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <strong class="text-dark d-block">{{ $kader->nama_kader }}</strong>
                                    <span class="badge bg-emerald-100 text-emerald-800 border px-2 py-0 rounded-pill small" style="font-size: 10px;">
                                        {{ $kader->jabatan }}
                                    </span>
                                    <small class="text-muted d-block mt-1">
                                        <i data-lucide="phone" style="width: 12px; height: 12px; vertical-align: middle;"></i> {{ $kader->no_telepon ?? '-' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted">
                            Belum ada data kader di RT {{ $rtStr }}.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PESERTA DARI DATA WARGA -->
<div class="modal fade" id="modalTambahPeserta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i data-lucide="user-plus" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Pendaftaran Peserta Posyandu RT {{ $rtStr }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posyandu.peserta.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Pilih warga yang sudah terdaftar sah di database RT {{ $rtStr }}. Jika warga baru, daftarkan terlebih dahulu di Data Kependudukan.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Warga RT {{ $rtStr }} *</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Pilih Warga Terdaftar --</option>
                            @foreach($wargaBelumTerdaftar as $wb)
                                <option value="{{ $wb->id }}">
                                    {{ $wb->name }} ({{ $wb->gender }}, Lahir: {{ $wb->tanggal_lahir ? Carbon\Carbon::parse($wb->tanggal_lahir)->format('d/m/Y') : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kategori Sasaran Posyandu *</label>
                        <select name="kategori" class="form-select" required>
                            <option value="balita">Balita (1 - 5 Tahun)</option>
                            <option value="bayi">Bayi (0 - 11 Bulan)</option>
                            <option value="anak">Anak Pra-Sekolah</option>
                            <option value="ibu_hamil">Ibu Hamil</option>
                            <option value="ibu_menyusui">Ibu Menyusui</option>
                            <option value="lansia">Lansia (60+ Tahun)</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Golongan Darah</label>
                            <select name="golongan_darah" class="form-select">
                                <option value="">- Belum Tahu -</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Riwayat Alergi</label>
                            <input type="text" name="riwayat_alergi" class="form-control" placeholder="Contoh: Alergi telur, dingin">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan Pelayanan Khusus</label>
                        <textarea name="catatan_khusus" class="form-control" rows="2" placeholder="Catatan kondisi kesehatan yang perlu diperhatikan kader..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan Peserta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL BUAT JADWAL POSYANDU -->
<div class="modal fade" id="modalBuatJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i data-lucide="calendar-plus" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Buat Jadwal Posyandu RT {{ $rtStr }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posyandu.jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Judul / Tema Kegiatan *</label>
                        <input type="text" name="judul_kegiatan" class="form-control" required placeholder="Contoh: Posyandu Melati Rutin & Imunisasi Balita" value="Posyandu Melati 13 Rutin Bulanan RT {{ $rtStr }}">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Tanggal Pelaksanaan *</label>
                            <input type="date" name="tanggal" class="form-control" required value="{{ Carbon\Carbon::now()->addDays(7)->toDateString() }}">
                        </div>
                        <div class="col-3">
                            <label class="form-label fw-semibold small">Jam Mulai *</label>
                            <input type="time" name="jam_mulai" class="form-control" required value="08:30">
                        </div>
                        <div class="col-3">
                            <label class="form-label fw-semibold small">Jam Selesai *</label>
                            <input type="time" name="jam_selesai" class="form-control" required value="11:30">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Lokasi Pelayanan *</label>
                        <input type="text" name="lokasi" class="form-control" required value="{{ $posyanduUnit->lokasi ?? 'Balai Pertemuan Warga RT ' . $rtStr }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Layanan yang Disediakan *</label>
                        <div class="d-flex flex-column gap-2 small">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenis_layanan_list[]" value="Penimbangan bayi & deteksi tumbuh kembang" checked>
                                <span class="form-check-label">Penimbangan bayi & balita</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenis_layanan_list[]" value="Pengukuran panjang/tinggi badan & lingkar kepala" checked>
                                <span class="form-check-label">Pengukuran tinggi/panjang badan & lingkar kepala</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenis_layanan_list[]" value="Edukasi gizi & pemberian PMT biskuit sehat" checked>
                                <span class="form-check-label">Edukasi gizi & PMT makanan tambahan</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenis_layanan_list[]" value="Pemeriksaan konsultasi ibu hamil" checked>
                                <span class="form-check-label">Pemeriksaan & konsultasi ibu hamil</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenis_layanan_list[]" value="Pemeriksaan tensi & skrining lansia" checked>
                                <span class="form-check-label">Skrining tekanan darah & kesehatan lansia</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mitra Tenaga Medis</label>
                        <input type="text" name="mitra_nakes" class="form-control" value="Bidan Puskesmas Arcamanik & Kader RT {{ $rtStr }}">
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH KADER RT -->
<div class="modal fade" id="modalTambahKader" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i data-lucide="user-check" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Tambah Data Kader RT {{ $rtStr }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posyandu.kader.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Lengkap Kader *</label>
                        <input type="text" name="nama_kader" class="form-control" required placeholder="Nama lengkap kader...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="no_telepon" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tugas / Jabatan *</label>
                        <select name="jabatan" class="form-select" required>
                            <option value="Ketua Kader">Ketua Kader Posyandu RT</option>
                            <option value="Kader Meja 1 (Pendaftaran)">Kader Meja 1 (Pendaftaran)</option>
                            <option value="Kader Meja 2 (Penimbangan)">Kader Meja 2 (Penimbangan & Pengukuran)</option>
                            <option value="Kader Meja 3 (Pencatatan KMS)">Kader Meja 3 (Pencatatan KMS)</option>
                            <option value="Kader Meja 4 (Penyuluhan/PMT)">Kader Meja 4 (Penyuluhan & PMT)</option>
                            <option value="Kader Pelaksana">Kader Pelaksana Umum</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan Kader</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CATAT KEHADIRAN CEPAT -->
@if($nextEvent || $lastEvent)
@php $activeEvent = $nextEvent ?: $lastEvent; @endphp
<div class="modal fade" id="modalCatatKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i data-lucide="check-square" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Catat Kehadiran: {{ $activeEvent->judul_kegiatan }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posyandu.jadwal.kehadiran', $activeEvent->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Peserta Posyandu *</label>
                        <select name="peserta_id" class="form-select" required>
                            <option value="">-- Pilih Peserta --</option>
                            @foreach($pesertaList as $pl)
                                <option value="{{ $pl->id }}">{{ $pl->user->name }} ({{ $pl->kategori_label }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Status Kehadiran *</label>
                            <select name="status_kehadiran" class="form-select" required>
                                <option value="hadir">Hadir di Posyandu</option>
                                <option value="izin">Izin</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Jam Datang</label>
                            <input type="time" name="jam_datang" class="form-control" value="{{ Carbon\Carbon::now()->format('H:i') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan Kunjungan</label>
                        <textarea name="catatan_kunjungan" class="form-control" rows="2" placeholder="Catatan kehadiran atau keluhan umum..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Kehadiran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- MODAL FORM LAPORAN KEGIATAN UNTUK KEGIATAN SELESAI -->
@if($lastEvent && !$laporanTerakhir)
<div class="modal fade" id="modalBuatLaporan{{ $lastEvent->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i data-lucide="file-plus" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Penyusunan Laporan Kegiatan Posyandu RT {{ $rtStr }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posyandu.jadwal.laporan', $lastEvent->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <strong class="text-dark d-block">{{ $lastEvent->judul_kegiatan }}</strong>
                        <small class="text-muted">{{ $lastEvent->tanggal_formatted }} &bull; Lokasi: {{ $lastEvent->lokasi }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Ringkasan Pelaksanaan Layanan *</label>
                        <textarea name="ringkasan_layanan" class="form-control" rows="3" required placeholder="Jelaskan secara ringkas jalannya kegiatan penimbangan, kehadiran warga, peran bidan/kader, dan pembagian PMT..."></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kendala / Masalah yang Ditemukan</label>
                            <textarea name="kendala" class="form-control" rows="2" placeholder="Contoh: Alat timbang perlu kalibrasi, antrean cukup padat..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Catatan Evaluasi & Rencana Tindak Lanjut</label>
                            <textarea name="catatan_evaluasi_rt" class="form-control" rows="2" placeholder="Contoh: Menambah meja pendaftaran dan koordinasi jadwal PMT bulan depan..."></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Dokumentasi Foto Kegiatan (Opsional)</label>
                        <input type="file" name="foto_dokumentasi" class="form-control" accept="image/*">
                        <small class="text-muted">Maksimal 3 MB (format JPG, PNG)</small>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Kirim Laporan ke RW</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
