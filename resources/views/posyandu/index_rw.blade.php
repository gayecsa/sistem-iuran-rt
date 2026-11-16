@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Hero Banner Monitoring RW 013 -->
    <div class="wm-card mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #123766 0%, #0D2340 100%); color: #fff; border: none; border-radius: 16px;">
        <div class="p-4 p-md-5 position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div style="max-width: 780px;">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-primary text-white fw-bold px-3 py-1 rounded-pill" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;"></i> DASHBOARD MONITORING & EVALUASI
                        </span>
                        <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill opacity-90" style="font-size: 11px;">
                            RW 013 Sukamiskin Arcamanik
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Pusat Pemantauan Posyandu RW 013</h2>
                    <p class="mb-0 text-white text-opacity-75 lead fs-6">
                        Monitoring agregat pelayanan Posyandu Melati 13 terpadu pada RT 001 s.d. RT 008, rekapitulasi kehadiran warga, status pelaporan kegiatan, dan koordinasi UPTD Puskesmas Arcamanik.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-3 py-2 fw-bold small text-primary shadow-sm">
                        <i data-lucide="layout-grid" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Ke Dashboard Utama
                    </a>
                </div>
            </div>
        </div>
        <!-- Decorative Circle Background -->
        <div class="position-absolute rounded-circle" style="width: 320px; height: 320px; background: rgba(255,255,255,0.04); top: -80px; right: -60px; pointer-events: none;"></div>
    </div>

    <!-- Alert Notifikasi Flash Session -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i data-lucide="check" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="text-success d-block">Berhasil!</strong>
                <span class="small text-dark">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!empty($rtBelumLapor))
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3 p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning text-dark p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
                </div>
                <div>
                    <strong class="text-warning-emphasis d-block">Perhatian Pemantauan Laporan</strong>
                    <span class="small text-dark">
                        Terdapat RT yang telah menyelesaikan kegiatan namun belum menyampaikan laporan: <strong>{{ implode(', ', $rtBelumLapor) }}</strong>.
                    </span>
                </div>
            </div>
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">Monitoring Kewilayahan</span>
        </div>
    @endif

    <!-- Kartu Statistik Agregat RW 013 -->
    <div class="row g-3 mb-4">
        <!-- Total Posyandu Aktif -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Unit Posyandu Aktif</span>
                    <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                        <i data-lucide="building-2" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $totalUnits }} <span class="fs-6 fw-normal text-muted">Pos Unit</span></h3>
                <span class="small text-muted">Melayani RT 001 – RT 008</span>
            </div>
        </div>

        <!-- Total Peserta Terdaftar -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Peserta Terdaftar</span>
                    <div class="p-2 rounded-3 bg-success-subtle text-success">
                        <i data-lucide="users" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-success">{{ $totalPeserta }} <span class="fs-6 fw-normal text-muted">Jiwa</span></h3>
                <div class="d-flex align-items-center gap-2 small text-muted">
                    <span>{{ $totalBalita }} Balita</span> &bull; 
                    <span>{{ $totalIbuHamil }} Bumil</span> &bull; 
                    <span>{{ $totalLansia }} Lansia</span>
                </div>
            </div>
        </div>

        <!-- Kegiatan Dilaksanakan -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Kegiatan Terlaksana</span>
                    <div class="p-2 rounded-3 bg-info-subtle text-info">
                        <i data-lucide="calendar-check" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $totalKegiatanSelesai }} <span class="fs-6 fw-normal text-muted">Sesi</span></h3>
                <span class="small text-muted">{{ $totalKegiatanAkanDatang }} kegiatan terjadwal berikutnya</span>
            </div>
        </div>

        <!-- Kepatuhan Pelaporan -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Kepatuhan Laporan RT</span>
                    <div class="p-2 rounded-3 bg-emerald-subtle text-success">
                        <i data-lucide="file-check" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-primary">{{ $persentaseLaporan }}%</h3>
                <span class="small text-muted">Laporan kegiatan masuk ke RW</span>
            </div>
        </div>
    </div>

    <!-- TABEL MONITORING UTAMA: STATUS POSYANDU RT 001 - RT 008 -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm overflow-hidden">
        <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i data-lucide="activity" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Matriks Monitoring Posyandu RT 001 – RT 008 Sukamiskin Arcamanik
                </h5>
                <p class="mb-0 text-muted small">
                    Pemantauan operasional per wilayah RT. Pengurus RW memantau agregat dan evaluasi tanpa mengubah data operasional RT.
                </p>
            </div>
            <div>
                <span class="badge bg-secondary-subtle text-secondary border px-3 py-2 rounded-pill small">
                    <i data-lucide="info" style="width: 14px; height: 14px; vertical-align: middle;"></i> RW Memantau & Mengevaluasi
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-secondary small fw-bold text-uppercase">Wilayah RT</th>
                        <th class="text-secondary small fw-bold text-uppercase">Peserta Terdaftar</th>
                        <th class="text-secondary small fw-bold text-uppercase">Kegiatan Terakhir</th>
                        <th class="text-secondary small fw-bold text-uppercase">Jadwal Berikutnya</th>
                        <th class="text-secondary small fw-bold text-uppercase">Kehadiran Terakhir</th>
                        <th class="text-secondary small fw-bold text-uppercase">Status Laporan</th>
                        <th class="text-end pe-4 text-secondary small fw-bold text-uppercase">Aksi Monitoring</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monitoringRt as $m)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge px-3 py-1 rounded-pill fw-bold" style="background: #E8F5E9; color: #16865D; border: 1px solid #C8E6C9;">
                                        RT {{ $m['rt_number'] }}
                                    </span>
                                    <div>
                                        <strong class="text-dark d-block">Posyandu Melati 13</strong>
                                        <small class="text-muted">{{ $m['kader_count'] }} Kader Terdaftar</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: #E1F5FE; color: #0288D1; font-size: 12px;">
                                    {{ $m['peserta_count'] }} Peserta
                                </span>
                            </td>
                            <td>
                                @if($m['last_event'])
                                    <strong class="text-dark d-block small">{{ $m['last_event']->tanggal_formatted }}</strong>
                                    <small class="text-muted">{{ Str::limit($m['last_event']->judul_kegiatan, 30) }}</small>
                                @else
                                    <span class="text-muted small">Belum ada kegiatan</span>
                                @endif
                            </td>
                            <td>
                                @if($m['next_event'])
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill small">
                                        {{ $m['next_event']->tanggal_formatted }}
                                    </span>
                                @else
                                    <span class="text-muted small">Belum dijadwalkan</span>
                                @endif
                            </td>
                            <td>
                                @if($m['last_event'])
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold px-2 py-1">
                                            {{ $m['last_attendance_count'] }} Hadir
                                        </span>
                                        <small class="text-muted">({{ $m['peserta_count'] > 0 ? round(($m['last_attendance_count'] / $m['peserta_count']) * 100) : 0 }}%)</small>
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($m['report'])
                                    <span class="badge {{ $m['report']->status_badge }} px-2 py-1 rounded-pill small">
                                        {{ $m['report']->status_label }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill small">
                                        Belum Lapor
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($m['report'])
                                    <a href="{{ route('posyandu.laporan.show', $m['report']->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                        <i data-lucide="eye" style="width: 14px; height: 14px; vertical-align: middle;"></i> Lihat Laporan
                                    </a>
                                @else
                                    <form action="{{ route('posyandu.monitoring.pengingat', $m['rt_number']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold" onclick="return confirm('Kirimkan notifikasi pengingat laporan ke Ketua RT {{ $m['rt_number'] }}?')">
                                            <i data-lucide="bell" style="width: 14px; height: 14px; vertical-align: middle;"></i> Ingatkan RT
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- DAFTAR LAPORAN KEGIATAN TERBARU MASUK DARI RT -->
    <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i data-lucide="file-text" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Laporan Pelaksanaan Kegiatan Posyandu Terbaru
                </h5>
                <p class="mb-0 text-muted small">Daftar laporan resmi yang disampaikan oleh para penanggung jawab RT untuk dievaluasi oleh RW 013.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 text-secondary small fw-bold">No. Laporan</th>
                        <th class="text-secondary small fw-bold">Wilayah RT</th>
                        <th class="text-secondary small fw-bold">Tanggal Kegiatan</th>
                        <th class="text-secondary small fw-bold">Peserta Hadir</th>
                        <th class="text-secondary small fw-bold">Penanggung Jawab</th>
                        <th class="text-secondary small fw-bold">Status Evaluasi RW</th>
                        <th class="text-end pe-3 text-secondary small fw-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLaporans as $lap)
                        <tr>
                            <td class="ps-3">
                                <span class="fw-semibold text-dark">{{ $lap->nomor_laporan }}</span>
                            </td>
                            <td>
                                <span class="badge px-3 py-1 rounded-pill fw-bold" style="background: #E8F5E9; color: #16865D; border: 1px solid #C8E6C9;">
                                    RT {{ $lap->rt_number }}
                                </span>
                            </td>
                            <td>
                                <small class="text-dark fw-medium">{{ $lap->tanggal_laporan_formatted }}</small>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fw-bold">
                                    {{ $lap->total_hadir }} Warga
                                </span>
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    {{ $lap->total_bayi_balita }} Balita, {{ $lap->total_ibu_hamil }} Bumil, {{ $lap->total_lansia }} Lansia
                                </small>
                            </td>
                            <td>
                                <small class="text-dark">{{ $lap->penanggungJawab->name ?? 'Pengurus RT' }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $lap->status_badge }} px-2 py-1 rounded-pill small">
                                    {{ $lap->status_label }}
                                </span>
                                @if($lap->catatan_rw)
                                    <small class="text-muted d-block text-truncate" style="max-width: 180px;" title="{{ $lap->catatan_rw }}">
                                        Catatan: "{{ $lap->catatan_rw }}"
                                    </small>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('posyandu.laporan.show', $lap->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                    <i data-lucide="eye" style="width: 14px; height: 14px; vertical-align: middle;"></i> Buka & Evaluasi
                                </a>
                                <a href="{{ route('posyandu.laporan.cetak', $lap->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Cetak Resmi">
                                    <i data-lucide="printer" style="width: 14px; height: 14px; vertical-align: middle;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada laporan kegiatan Posyandu yang masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
