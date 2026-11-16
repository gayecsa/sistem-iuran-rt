@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Hero Banner -->
    <div class="wm-card mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #123766 0%, #1E4E8C 60%, #16865D 100%); color: #fff; border: none; border-radius: 16px;">
        <div class="p-4 p-md-5 position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div style="max-width: 760px;">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill opacity-95">
                            <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;" class="text-success me-1"></i> SISTEM KEPENDUDUKAN TERPADU
                        </span>
                        <span class="badge bg-emerald-500 text-white fw-bold px-3 py-1 rounded-pill" style="background: #10B981 !important;">
                            RW 013 Sukamiskin Arcamanik
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Data Kependudukan RW 013</h2>
                    <p class="mb-0 text-white text-opacity-75 lead fs-6">
                        Pusat pengelolaan terintegrasi data individu warga, catatan kartu keluarga, dan dokumen kependudukan resmi RT 001 s/d RT 008.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($user->isRw() || $user->isRt())
                        <a href="{{ route('warga.create') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-primary shadow-sm">
                            <i data-lucide="user-plus" class="me-1" style="width: 18px; height: 18px; vertical-align: middle;"></i> Tambah Warga Baru
                        </a>
                    @endif
                    <a href="{{ route('kependudukan.laporan') }}" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold">
                        <i data-lucide="file-text" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Laporan Demografi
                    </a>
                </div>
            </div>
        </div>
        <div class="position-absolute rounded-circle" style="width: 340px; height: 340px; background: rgba(255,255,255,0.06); top: -90px; right: -70px; pointer-events: none;"></div>
    </div>

    <!-- Alert Notifikasi Session Flash -->
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

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="rounded-circle bg-danger text-white p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="text-danger d-block">Perhatian</strong>
                <span class="small text-dark">{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 1. DASHBOARD RINGKASAN KEPENDUDUKAN (METRIK AKTUAL DATABASE) -->
    <div class="row g-3 mb-4">
        <!-- Total Warga -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm position-relative overflow-hidden" style="border-left: 4px solid #1456A0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Total Warga Terdaftar</span>
                    <div class="p-2 rounded-3 bg-sky-50 text-primary" style="background: #EAF3FF;">
                        <i data-lucide="users" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ number_format($totalWarga) }} <span class="fs-6 fw-normal text-muted">Jiwa</span></h3>
                <div class="d-flex align-items-center gap-2 small text-muted">
                    <span><i data-lucide="user" style="width: 12px; height: 12px;"></i> L: <strong>{{ $totalLaki }}</strong></span>
                    <span>&bull;</span>
                    <span><i data-lucide="user" style="width: 12px; height: 12px;"></i> P: <strong>{{ $totalPerempuan }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Total Kepala Keluarga / KK -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm position-relative overflow-hidden" style="border-left: 4px solid #10B981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Kepala Keluarga / KK</span>
                    <div class="p-2 rounded-3 bg-emerald-50 text-success" style="background: #E8F5E9;">
                        <i data-lucide="home" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-success">{{ number_format($totalKk) }} <span class="fs-6 fw-normal text-muted">KK</span></h3>
                <span class="small text-muted">Rata-rata {{ $totalKk > 0 ? round($totalWarga / $totalKk, 1) : 0 }} anggota / KK</span>
            </div>
        </div>

        <!-- KK Terverifikasi -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm position-relative overflow-hidden" style="border-left: 4px solid #059669 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">KK Terverifikasi</span>
                    <div class="p-2 rounded-3 bg-emerald-50 text-emerald-600" style="background: #D1FAE5; color: #059669;">
                        <i data-lucide="check-circle-2" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ number_format($kkTerverifikasi) }} <span class="fs-6 fw-normal text-success">({{ $totalKk > 0 ? round(($kkTerverifikasi / $totalKk) * 100) : 0 }}%)</span></h3>
                <span class="small text-muted">Tervalidasi sah & siap dicetak</span>
            </div>
        </div>

        <!-- KK Menunggu / Perlu Pembaruan -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm position-relative overflow-hidden" style="border-left: 4px solid #F59E0B !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Perlu Verifikasi / Pembaruan</span>
                    <div class="p-2 rounded-3 bg-amber-50 text-warning" style="background: #FFF8E1;">
                        <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $kkMenunggu + $kkPerluPembaruan }} <span class="fs-6 fw-normal text-muted">KK</span></h3>
                <div class="d-flex align-items-center gap-2 small text-muted">
                    <span>Menunggu: <strong class="text-warning">{{ $kkMenunggu }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembaruan: <strong class="text-danger">{{ $kkPerluPembaruan }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sebaran Warga per RT (001 - 008) -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <span class="small fw-bold text-muted text-uppercase">
                <i data-lucide="map-pin" style="width: 14px; height: 14px; vertical-align: middle;" class="text-primary me-1"></i>
                Sebaran Penduduk per RT (RW 013 Sukamiskin Arcamanik):
            </span>
            <span class="small text-muted">Total 8 RT Aktif</span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @foreach($distribusiRt as $d)
                <a href="{{ route('kependudukan.index', ['tab' => $activeTab, 'rt' => intval($d['rt'])]) }}" 
                   class="badge text-decoration-none px-3 py-2 rounded-pill border {{ request('rt') == intval($d['rt']) ? 'bg-primary text-white border-primary shadow-sm' : 'bg-light text-dark border-light-subtle' }}">
                    <strong class="d-block">RT {{ $d['rt'] }}</strong>
                    <span style="font-size: 11px; opacity: 0.85;">{{ $d['warga'] }} Jiwa ({{ $d['kk'] }} KK)</span>
                </a>
            @endforeach
            @if(request()->filled('rt'))
                <a href="{{ route('kependudukan.index', ['tab' => $activeTab]) }}" class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill text-decoration-none align-self-center">
                    Tampilkan Semua RT &times;
                </a>
            @endif
        </div>
    </div>

    <!-- 2. TAB NAVIGASI TUNGGAL: INDIVIDU, KELUARGA, DOKUMEN KK -->
    <div class="wm-card bg-white border rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="border-bottom bg-light px-3 pt-3">
            <ul class="nav nav-tabs border-0 gap-2" id="kependudukanTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{ route('kependudukan.index', array_merge(request()->except(['page']), ['tab' => 'individu'])) }}" 
                       class="nav-link px-4 py-3 fw-bold rounded-top-3 border-0 d-flex align-items-center gap-2 {{ $activeTab === 'individu' ? 'active bg-white text-primary border-top border-primary' : 'text-secondary' }}"
                       style="{{ $activeTab === 'individu' ? 'border-top: 3px solid #1456A0 !important;' : '' }}">
                        <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                        <span>A. Data Individu</span>
                        <span class="badge {{ $activeTab === 'individu' ? 'bg-primary text-white' : 'bg-secondary text-white' }} rounded-pill small">
                            {{ number_format($totalWarga) }}
                        </span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('kependudukan.index', array_merge(request()->except(['page']), ['tab' => 'keluarga'])) }}" 
                       class="nav-link px-4 py-3 fw-bold rounded-top-3 border-0 d-flex align-items-center gap-2 {{ $activeTab === 'keluarga' ? 'active bg-white text-success border-top border-success' : 'text-secondary' }}"
                       style="{{ $activeTab === 'keluarga' ? 'border-top: 3px solid #10B981 !important;' : '' }}">
                        <i data-lucide="users-round" style="width: 18px; height: 18px;"></i>
                        <span>B. Data Keluarga</span>
                        <span class="badge {{ $activeTab === 'keluarga' ? 'bg-success text-white' : 'bg-secondary text-white' }} rounded-pill small">
                            {{ number_format($totalKk) }}
                        </span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('kependudukan.index', array_merge(request()->except(['page']), ['tab' => 'dokumen'])) }}" 
                       class="nav-link px-4 py-3 fw-bold rounded-top-3 border-0 d-flex align-items-center gap-2 {{ $activeTab === 'dokumen' ? 'active bg-white text-indigo border-top border-indigo' : 'text-secondary' }}"
                       style="{{ $activeTab === 'dokumen' ? 'border-top: 3px solid #4F46E5 !important;' : '' }}">
                        <i data-lucide="file-check" style="width: 18px; height: 18px;"></i>
                        <span>C. Dokumen KK</span>
                        <span class="badge {{ $activeTab === 'dokumen' ? 'bg-dark text-white' : 'bg-secondary text-white' }} rounded-pill small">
                            {{ $kkTerverifikasi }}/{{ $totalKk }} Sah
                        </span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ====================================================================== -->
        <!-- TAB A: DATA INDIVIDU (WARGA) -->
        <!-- ====================================================================== -->
        @if($activeTab === 'individu')
            <div class="p-4 border-bottom bg-light-subtle">
                <form action="{{ route('kependudukan.index') }}" method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="tab" value="individu">

                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, NIK, No KK, alamat..." value="{{ request('search') }}">
                        </div>
                    </div>

                    @if($user->isRw())
                        <div class="col-6 col-md-2">
                            <select name="rt" class="form-select">
                                <option value="all">Semua RT (001-008)</option>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}" {{ request('rt') == $i ? 'selected' : '' }}>
                                        RT {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    @endif

                    <div class="col-6 col-md-2">
                        <select name="gender" class="form-select">
                            <option value="all">Semua Gender</option>
                            <option value="Laki-laki" {{ request('gender') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ request('gender') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="status_warga" class="form-select">
                            <option value="all">Status Warga</option>
                            <option value="Warga Tetap" {{ request('status_warga') === 'Warga Tetap' ? 'selected' : '' }}>Warga Tetap</option>
                            <option value="Warga Sementara" {{ request('status_warga') === 'Warga Sementara' ? 'selected' : '' }}>Warga Sementara</option>
                            <option value="Pendatang" {{ request('status_warga') === 'Pendatang' ? 'selected' : '' }}>Pendatang</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-auto ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-sm">
                            <i data-lucide="filter" style="width: 14px; height: 14px; vertical-align: middle;"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'rt', 'gender', 'status_warga', 'status_verifikasi']))
                            <a href="{{ route('kependudukan.index', ['tab' => 'individu']) }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama Lengkap & NIK</th>
                            <th>Gender / Usia</th>
                            <th>Status Keluarga</th>
                            <th>Wilayah RT</th>
                            <th>Alamat & Telepon</th>
                            <th>Pekerjaan</th>
                            <th>Status Kependudukan</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wargas as $w)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $w->avatar_url }}" alt="{{ $w->name }}" class="rounded-circle border" style="width: 36px; height: 36px; object-fit: cover;">
                                        <div>
                                            <a href="javascript:void(0)" class="fw-bold text-dark text-decoration-none btn-quickview-warga" data-id="{{ $w->id }}">
                                                {{ $w->name }}
                                            </a>
                                            <small class="text-muted d-block font-monospace">NIK: {{ $w->masked_nik }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $w->gender === 'Laki-laki' ? 'bg-sky-100 text-primary' : 'bg-pink-100 text-danger' }} border small fw-semibold" style="background: {{ $w->gender === 'Laki-laki' ? '#E1F5FE' : '#FCE4EC' }};">
                                        {{ $w->gender ?? '-' }}
                                    </span>
                                    <small class="text-muted d-block">{{ $w->age ? $w->age . ' Th' : '-' }}</small>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $w->hubungan_keluarga ?: 'Warga' }}</span>
                                    @if($w->kartuKeluarga)
                                        <small class="text-muted d-block">
                                            <a href="{{ route('kependudukan.keluarga.detail', $w->kartuKeluarga->id) }}" class="text-decoration-none font-monospace">
                                                KK: {{ $w->kartuKeluarga->masked_no_kk }}
                                            </a>
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border fw-bold">
                                        RT {{ str_pad($w->rt_number, 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-dark d-block">{{ Str::limit($w->address ?? '-', 35) }}</span>
                                    <small class="text-muted">{{ $w->phone ?: '-' }}</small>
                                </td>
                                <td>
                                    <span class="small text-secondary">{{ $w->pekerjaan ?: '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small fw-semibold">
                                        {{ $w->status_verifikasi ?: 'Terdaftar' }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold btn-quickview-warga" data-id="{{ $w->id }}">
                                            Detail
                                        </button>
                                        @if($user->isRw() || $user->isRt())
                                            <a href="{{ route('warga.edit', $w->id) }}" class="btn btn-sm btn-light border rounded-circle p-1 text-secondary" title="Edit Biodata">
                                                <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i data-lucide="users" style="width: 44px; height: 44px;" class="mb-2 text-secondary opacity-50 d-block mx-auto"></i>
                                    Tidak ada data warga yang sesuai dengan pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($wargas->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $wargas->links() }}
                </div>
            @endif

        <!-- ====================================================================== -->
        <!-- TAB B: DATA KELUARGA (KARTU KELUARGA) -->
        <!-- ====================================================================== -->
        @elseif($activeTab === 'keluarga')
            <div class="p-4 border-bottom bg-light-subtle">
                <form action="{{ route('kependudukan.index') }}" method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="tab" value="keluarga">

                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No KK, nama kepala keluarga, alamat..." value="{{ request('search') }}">
                        </div>
                    </div>

                    @if($user->isRw())
                        <div class="col-6 col-md-3">
                            <select name="rt" class="form-select">
                                <option value="all">Semua Wilayah RT (001-008)</option>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}" {{ request('rt') == $i ? 'selected' : '' }}>
                                        RT {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    @endif

                    <div class="col-6 col-md-2">
                        <select name="status_verifikasi" class="form-select">
                            <option value="all">Status Verifikasi</option>
                            <option value="Terverifikasi" {{ request('status_verifikasi') === 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="Menunggu Verifikasi" {{ request('status_verifikasi') === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="Belum Ada Dokumen" {{ request('status_verifikasi') === 'Belum Ada Dokumen' ? 'selected' : '' }}>Belum Ada Dokumen</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-auto ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold btn-sm">
                            <i data-lucide="filter" style="width: 14px; height: 14px; vertical-align: middle;"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'rt', 'status_verifikasi']))
                            <a href="{{ route('kependudukan.index', ['tab' => 'keluarga']) }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nomor Kartu Keluarga</th>
                            <th>Kepala Keluarga</th>
                            <th>Wilayah RT</th>
                            <th>Alamat Lengkap</th>
                            <th>Jumlah Anggota</th>
                            <th>Status Tempat Tinggal</th>
                            <th>Status Verifikasi KK</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keluargas as $kk)
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-dark font-monospace">{{ $kk->masked_no_kk }}</strong>
                                    <small class="text-muted d-block">ID: #{{ $kk->id }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-emerald-50 text-success p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i data-lucide="user-check" style="width: 16px; height: 16px;"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('kependudukan.keluarga.detail', $kk->id) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $kk->nama_kepala_keluarga ?: 'Belum Ditetapkan' }}
                                            </a>
                                            <small class="text-muted d-block font-monospace">NIK: {{ $kk->nik_kepala_keluarga ?: '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-1 rounded-pill fw-bold" style="background: #E8F5E9; color: #1B5E20;">
                                        RT {{ str_pad($kk->rt, 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ Str::limit($kk->alamat ?: '-', 40) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border fw-bold px-3 py-1 rounded-pill">
                                        {{ $kk->anggota_count }} Orang
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-secondary">{{ $kk->status_rumah ?: 'Milik Sendiri' }}</span>
                                </td>
                                <td>
                                    @if($kk->status_verifikasi_dukcapil === 'Terverifikasi')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                            <i data-lucide="check" style="width: 12px; height: 12px; vertical-align: middle;"></i> Terverifikasi
                                        </span>
                                    @elseif($kk->status_verifikasi_dukcapil === 'Menunggu Verifikasi')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold">
                                            <i data-lucide="clock" style="width: 12px; height: 12px; vertical-align: middle;"></i> Menunggu Verifikasi
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 rounded-pill fw-bold">
                                            Belum Ada Dokumen
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('kependudukan.keluarga.detail', $kk->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">
                                        Detail Keluarga &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i data-lucide="home" style="width: 44px; height: 44px;" class="mb-2 text-secondary opacity-50 d-block mx-auto"></i>
                                    Tidak ada data keluarga yang sesuai dengan pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($keluargas->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $keluargas->links() }}
                </div>
            @endif

        <!-- ====================================================================== -->
        <!-- TAB C: DOKUMEN KK RESMI -->
        <!-- ====================================================================== -->
        @else
            <div class="p-4 border-bottom bg-light-subtle">
                <form action="{{ route('kependudukan.index') }}" method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="tab" value="dokumen">

                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No KK, nama kepala keluarga..." value="{{ request('search') }}">
                        </div>
                    </div>

                    @if($user->isRw())
                        <div class="col-6 col-md-3">
                            <select name="rt" class="form-select">
                                <option value="all">Semua Wilayah RT (001-008)</option>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}" {{ request('rt') == $i ? 'selected' : '' }}>
                                        RT {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    @endif

                    <div class="col-6 col-md-2">
                        <select name="status_verifikasi" class="form-select">
                            <option value="all">Status Verifikasi</option>
                            <option value="Terverifikasi" {{ request('status_verifikasi') === 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="Menunggu Verifikasi" {{ request('status_verifikasi') === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="Belum Ada Dokumen" {{ request('status_verifikasi') === 'Belum Ada Dokumen' ? 'selected' : '' }}>Belum Ada Dokumen</option>
                            <option value="Perlu Pembaruan" {{ request('status_verifikasi') === 'Perlu Pembaruan' ? 'selected' : '' }}>Perlu Pembaruan</option>
                            <option value="Ditolak" {{ request('status_verifikasi') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-auto ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-dark rounded-pill px-4 fw-semibold btn-sm">
                            <i data-lucide="filter" style="width: 14px; height: 14px; vertical-align: middle;"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'rt', 'status_verifikasi']))
                            <a href="{{ route('kependudukan.index', ['tab' => 'dokumen']) }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No. Kartu Keluarga</th>
                            <th>Kepala Keluarga</th>
                            <th>RT</th>
                            <th>Berkas KK Digital</th>
                            <th>Tanggal Penerbitan</th>
                            <th>Status Verifikasi Dokumen</th>
                            <th>Petugas Verifikator</th>
                            <th class="pe-4 text-end">Aksi Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumens as $dok)
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-dark font-monospace">{{ $dok->masked_no_kk }}</strong>
                                </td>
                                <td>
                                    <strong class="text-dark">{{ $dok->nama_kepala_keluarga ?: '-' }}</strong>
                                    <small class="text-muted d-block">{{ $dok->anggota_count }} Anggota</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border fw-bold">
                                        RT {{ str_pad($dok->rt, 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td>
                                    @if($dok->dokumen_kk_path)
                                        <span class="badge bg-primary-subtle text-primary border">
                                            <i data-lucide="file-text" style="width: 12px; height: 12px; vertical-align: middle;"></i> PDF Tersedia (v{{ $dok->versi_dokumen }})
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-muted border">
                                            Belum Diunggah
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-dark">{{ $dok->tgl_diterbitkan_dukcapil ? $dok->tgl_diterbitkan_dukcapil->format('d/m/Y') : '-' }}</small>
                                </td>
                                <td>
                                    @if($dok->status_verifikasi_dukcapil === 'Terverifikasi')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                            <i data-lucide="shield-check" style="width: 12px; height: 12px; vertical-align: middle;"></i> Terverifikasi Sah
                                        </span>
                                    @elseif($dok->status_verifikasi_dukcapil === 'Menunggu Verifikasi')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold">
                                            <i data-lucide="clock" style="width: 12px; height: 12px; vertical-align: middle;"></i> Menunggu Verifikasi
                                        </span>
                                    @elseif($dok->status_verifikasi_dukcapil === 'Ditolak')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold">
                                            <i data-lucide="x-circle" style="width: 12px; height: 12px; vertical-align: middle;"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 rounded-pill fw-bold">
                                            Belum Ada Dokumen
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($dok->verifikator)
                                        <small class="fw-semibold text-dark d-block">{{ $dok->verifikator->name }}</small>
                                        <small class="text-muted" style="font-size: 11px;">{{ $dok->verified_at ? $dok->verified_at->format('d/m/Y') : '-' }}</small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('kependudukan.keluarga.detail', $dok->id) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold">
                                            Kelola Berkas
                                        </a>
                                        @if($dok->isVerifiedDukcapil())
                                            <a href="{{ route('kependudukan.keluarga.cetak', $dok->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" title="Cetak KK Resmi">
                                                <i data-lucide="printer" style="width: 14px; height: 14px; vertical-align: middle;"></i> Cetak
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i data-lucide="file-question" style="width: 44px; height: 44px;" class="mb-2 text-secondary opacity-50 d-block mx-auto"></i>
                                    Tidak ada data dokumen KK yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($dokumens->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $dokumens->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- MODAL QUICKVIEW BIODATA WARGA (INTERAKTIF & RESPONSIF) -->
<div class="modal fade" id="modalQuickviewWarga" tabindex="-1" aria-labelledby="modalQuickviewWargaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalQuickviewWargaLabel">
                    <i data-lucide="user-check" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Biodata Lengkap Warga
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="quickviewLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted small mt-2 mb-0">Memuat biodata warga dari database...</p>
                </div>
                <div id="quickviewContent" style="display: none;">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <img id="qvAvatar" src="" class="rounded-circle border" style="width: 64px; height: 64px; object-fit: cover;">
                        <div>
                            <h4 id="qvName" class="fw-bold mb-1 text-dark">-</h4>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span id="qvNik" class="badge bg-light text-dark border font-monospace fs-6">NIK: -</span>
                                <span id="qvHubungan" class="badge bg-primary-subtle text-primary border">-</span>
                                <span id="qvRt" class="badge bg-secondary-subtle text-secondary border">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Jenis Kelamin & Usia:</span>
                                <strong id="qvGenderAge" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Tempat, Tanggal Lahir:</span>
                                <strong id="qvTtl" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Agama & Status Kawin:</span>
                                <strong id="qvAgamaKawin" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Pekerjaan:</span>
                                <strong id="qvPekerjaan" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Nomor Telepon / WhatsApp:</span>
                                <strong id="qvPhone" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light rounded-3 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Status Kependudukan:</span>
                                <strong id="qvStatusDomisili" class="text-dark">-</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded-3">
                                <span class="text-muted d-block" style="font-size: 11px;">Alamat Tempat Tinggal:</span>
                                <strong id="qvAddress" class="text-dark">-</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex gap-2">
                    <a id="qvFullDetailBtn" href="#" class="btn btn-primary rounded-pill px-4 fw-bold">
                        Buka Profil Lengkap &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Quickview Warga AJAX handler
        var modalEl = document.getElementById('modalQuickviewWarga');
        var bsModal = new bootstrap.Modal(modalEl);

        document.querySelectorAll('.btn-quickview-warga').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var wargaId = this.getAttribute('data-id');
                document.getElementById('quickviewLoading').style.display = 'block';
                document.getElementById('quickviewContent').style.display = 'none';
                bsModal.show();

                fetch('/kependudukan/api/warga/' + wargaId)
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (data.error) {
                            alert(data.error);
                            bsModal.hide();
                            return;
                        }
                        document.getElementById('qvAvatar').src = data.avatar_url;
                        document.getElementById('qvName').textContent = data.name;
                        document.getElementById('qvNik').textContent = 'NIK: ' + data.full_nik;
                        document.getElementById('qvHubungan').textContent = data.hubungan_keluarga;
                        document.getElementById('qvRt').textContent = data.rt;
                        document.getElementById('qvGenderAge').textContent = data.gender + ' (' + (data.age ? data.age + ' Tahun' : '-') + ')';
                        document.getElementById('qvTtl').textContent = (data.tempat_lahir || 'Bandung') + ', ' + data.tanggal_lahir;
                        document.getElementById('qvAgamaKawin').textContent = (data.agama || 'Islam') + ' / ' + (data.status_perkawinan || 'Belum Kawin');
                        document.getElementById('qvPekerjaan').textContent = data.pekerjaan || '-';
                        document.getElementById('qvPhone').textContent = data.phone;
                        document.getElementById('qvStatusDomisili').textContent = (data.status_domisili || 'Warga Tetap') + ' (' + (data.status_verifikasi || 'Terdaftar') + ')';
                        document.getElementById('qvAddress').textContent = data.address || '-';
                        document.getElementById('qvFullDetailBtn').href = data.detail_url;

                        document.getElementById('quickviewLoading').style.display = 'none';
                        document.getElementById('quickviewContent').style.display = 'block';
                    })
                    .catch(function (err) {
                        alert('Gagal memuat biodata warga.');
                        bsModal.hide();
                    });
            });
        });
    });
</script>
@endsection
