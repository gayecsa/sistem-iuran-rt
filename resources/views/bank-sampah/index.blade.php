@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Hero Banner Warkas Machi -->
    <div class="wm-card mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0F5132 0%, #123766 100%); color: #fff; border: none; border-radius: 16px;">
        <div class="p-4 p-md-5 position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div style="max-width: 720px;">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-emerald-500 text-white fw-bold px-3 py-1 rounded-pill" style="background: #10B981 !important;">
                            <i data-lucide="leaf" style="width: 14px; height: 14px; vertical-align: middle;"></i> LINGKUNGAN BERSIH & EKONOMI SIRKULAR
                        </span>
                        <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill opacity-90">
                            RW 013 Sukamiskin Arcamanik
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Bank Sampah Terpadu RW 013</h2>
                    <p class="mb-0 text-white text-opacity-75 lead fs-6">
                        Pengelolaan sampah anorganik terpilah (Plastik, Kertas, Logam, Minyak Jelantah) terintegrasi dengan rekening tabungan digital warga dan kas lingkungan 8 RT.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSetorSampah">
                        <i data-lucide="plus-circle" class="me-1" style="width: 18px; height: 18px; vertical-align: middle;"></i> Setor Sampah Sekarang
                    </button>
                    <a href="{{ route('bank-sampah.riwayat') }}" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold">
                        <i data-lucide="wallet" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Tabungan Saya
                    </a>
                </div>
            </div>
        </div>
        <!-- Decorative Circle Background -->
        <div class="position-absolute rounded-circle" style="width: 320px; height: 320px; background: rgba(255,255,255,0.05); top: -80px; right: -60px; pointer-events: none;"></div>
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

    @if(session('warning'))
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="rounded-circle bg-warning text-dark p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i data-lucide="alert-triangle" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="text-warning-emphasis d-block">Perhatian</strong>
                <span class="small text-dark">{{ session('warning') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Role-Based Navigation & Action Bar -->
    <div class="wm-card mb-4 p-3 bg-white border rounded-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fw-semibold">
                    <i data-lucide="user-check" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i>
                    Akses Anda: <strong class="text-dark">{{ $user->role_badge_label }}</strong>
                </span>
                @if($user->isPetugasBankSampah())
                    <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-2 rounded-pill fw-bold" style="background: #E8F5E9; color: #1B5E20;">
                        <i data-lucide="badge-check" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i> Petugas Pengelola Aktif
                    </span>
                @endif
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($user->isPetugasBankSampah() || $user->isRw())
                    <a href="{{ route('bank-sampah.kelola') }}" class="btn btn-outline-success rounded-pill px-3 py-2 fw-bold position-relative small">
                        <i data-lucide="clipboard-check" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                        Kelola Setoran RT
                        @if($pendingCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                @endif

                @if($user->isRw())
                    <a href="{{ route('bank-sampah.tarif') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold small">
                        <i data-lucide="tag" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Kelola Tarif Harga
                    </a>
                    <a href="{{ route('bank-sampah.laporan') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold small">
                        <i data-lucide="file-spreadsheet" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Laporan Bulanan RW
                    </a>
                @endif

                <a href="{{ route('bank-sampah.riwayat') }}" class="btn btn-light rounded-pill px-3 py-2 fw-bold small border text-dark">
                    <i data-lucide="history" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Riwayat & Saldo Saya
                </a>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik Sampah Terpadu (Aktual Database) -->
    <div class="row g-3 mb-4">
        <!-- Saldo Tabungan Pribadi -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Saldo Tabungan Saya</span>
                    <div class="p-2 rounded-3 bg-emerald-50 text-success" style="background: #E8F5E9;">
                        <i data-lucide="wallet" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-success">{{ $mySaldo->saldo_aktif_formatted }}</h4>
                <div class="d-flex align-items-center justify-content-between small text-muted">
                    <span>Total Disetor: <strong>{{ number_format($mySaldo->total_kg_terkumpul, 1) }} kg</strong></span>
                    <a href="{{ route('bank-sampah.riwayat') }}" class="text-decoration-none fw-bold text-success">Detail &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Terkumpul Hari Ini -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Terkumpul Hari Ini</span>
                    <div class="p-2 rounded-3 bg-sky-50 text-primary" style="background: #E1F5FE;">
                        <i data-lucide="calendar" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ number_format($stats['total_kg_hari_ini'], 1, ',', '.') }} <span class="fs-6 fw-normal text-muted">kg</span></h4>
                <span class="small text-muted">Transaksi selesai hari ini</span>
            </div>
        </div>

        <!-- Terkumpul Bulan Ini -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Terkumpul Bulan Ini</span>
                    <div class="p-2 rounded-3 bg-indigo-50 text-indigo" style="background: #E8EAF6; color: #3F51B5;">
                        <i data-lucide="scale" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ number_format($stats['total_kg_bulan_ini'], 1, ',', '.') }} <span class="fs-6 fw-normal text-muted">kg</span></h4>
                <span class="small text-muted">{{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
            </div>
        </div>

        <!-- Total Tabungan Beredar di Warga -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="wm-card p-3 h-100 bg-white border rounded-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Nilai Tabungan Warga</span>
                    <div class="p-2 rounded-3 bg-amber-50 text-warning" style="background: #FFF8E1;">
                        <i data-lucide="coins" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">Rp {{ number_format($stats['total_tabungan_beredar'], 0, ',', '.') }}</h4>
                <span class="small text-muted">{{ $stats['total_warga_aktif'] }} warga aktif berpartisipasi</span>
            </div>
        </div>
    </div>

    <!-- TABEL UTAMA: Titik Pengumpulan & Petugas Bank Sampah 8 RT -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm overflow-hidden">
        <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i data-lucide="map-pin" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Titik Pengumpulan & Petugas Bank Sampah 8 RT Sukamiskin Arcamanik
                </h5>
                <p class="mb-0 text-muted small">
                    Data unit bank sampah resmi terdaftar per wilayah RT 001 - RT 008 dengan penanggung jawab warga.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill small">
                    <i data-lucide="info" style="width: 14px; height: 14px; vertical-align: middle;"></i> Data Aktual dihitung dari transaksi selesai
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Unit Bank Sampah</th>
                        <th>Wilayah RT</th>
                        <th>Petugas Pengelola (Warga Terdaftar)</th>
                        <th>Lokasi Drop Point</th>
                        <th>Total Terkumpul Aktual</th>
                        <th>Data Simulasi Awal</th>
                        <th>Jadwal Penimbangan</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bankSampahList as $bs)
                        <tr>
                            <td class="ps-4">
                                <strong class="text-dark d-block">{{ $bs->nama_unit }}</strong>
                                <small class="text-muted">Kategori: {{ Str::limit($bs->kategori_diterima, 35) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-1 rounded-pill fw-bold" style="background: #E8F5E9; color: #1B5E20;">
                                    RT {{ str_pad($bs->rt_number, 3, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $bs->petugas_avatar_url }}" alt="{{ $bs->petugas_name }}" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                                    <div>
                                        <span class="fw-semibold text-dark d-block">{{ $bs->petugas_name }}</span>
                                        <small class="text-muted">ID: #{{ $bs->petugas_id ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="small text-muted">{{ $bs->lokasi }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold px-2 py-1">
                                        {{ number_format($bs->total_aktual_kg, 1, ',', '.') }} kg
                                    </span>
                                    <small class="text-muted">({{ $bs->total_transaksi_selesai_count }} tx)</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-inline-flex flex-column">
                                    <span class="badge bg-slate-100 text-slate-700 border fw-semibold px-2 py-1" style="background: #F1F5F9; color: #475569;">
                                        {{ $bs->total_terkumpul_kg }} kg
                                    </span>
                                    <span class="badge bg-light text-muted border-0 p-0 text-start" style="font-size: 10px;">
                                        <i data-lucide="tag" style="width: 10px; height: 10px; vertical-align: middle;"></i> Data Simulasi Awal
                                    </span>
                                </div>
                            </td>
                            <td>
                                <small class="text-secondary">{{ $bs->jadwal }}</small>
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold btn-setor-unit" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalSetorSampah"
                                    data-unit-id="{{ $bs->id }}"
                                    data-unit-name="{{ $bs->nama_unit }} (RT {{ str_pad($bs->rt_number, 3, '0', STR_PAD_LEFT) }})">
                                    <i data-lucide="plus" style="width: 14px; height: 14px; vertical-align: middle;"></i> Setor
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada unit bank sampah terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- KATALOG JENIS SAMPAH & TARIF AKTIF -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i data-lucide="tag" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Daftar Kategori Sampah & Tarif Resmi RW 013
                </h5>
                <p class="mb-0 text-muted small">Tarif pembelian sampah anorganik yang dijamin transparan dan dikelola pengurus RW.</p>
            </div>
            @if($user->isRw())
                <a href="{{ route('bank-sampah.tarif') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                    <i data-lucide="edit" style="width: 14px; height: 14px; vertical-align: middle;"></i> Atur Tarif Harga
                </a>
            @endif
        </div>

        <div class="row g-3">
            @foreach($kategoriList as $kat)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="p-3 border rounded-3 h-100 bg-light-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <strong class="text-dark fs-6">{{ $kat->nama_kategori }}</strong>
                                <span class="badge bg-emerald-100 text-emerald-800 fw-bold border border-emerald-300" style="background: #E8F5E9; color: #1B5E20;">
                                    {{ $kat->harga_formatted }}
                                </span>
                            </div>
                            <p class="text-muted small mb-2">{{ $kat->deskripsi }}</p>
                        </div>
                        <div class="pt-2 border-top">
                            <small class="text-secondary d-block" style="font-size: 11px;">
                                <i data-lucide="check" style="width: 12px; height: 12px; vertical-align: middle;" class="text-success"></i>
                                <strong>Petunjuk:</strong> {{ $kat->petunjuk_pemilahan }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TRANSAKSI TERBARU & PANDUAN -->
    <div class="row g-4 mb-4">
        <!-- Transaksi Terbaru (Disesuaikan Role) -->
        <div class="col-lg-7">
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i data-lucide="activity" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        @if($user->isRw())
                            Setoran Terbaru Seluruh RW 013
                        @elseif($user->isPetugasBankSampah())
                            Setoran Terbaru Wilayah RT {{ str_pad($user->rt_number, 3, '0', STR_PAD_LEFT) }}
                        @else
                            Riwayat Setoran Saya
                        @endif
                    </h5>
                    <a href="{{ $user->isRw() || $user->isPetugasBankSampah() ? route('bank-sampah.kelola') : route('bank-sampah.riwayat') }}" class="small fw-bold text-primary text-decoration-none">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Warga / RT</th>
                                <th>Berat</th>
                                <th>Total Nilai</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $tx)
                                <tr>
                                    <td>
                                        <strong class="text-dark">{{ $tx->kode_transaksi }}</strong>
                                        <small class="text-muted d-block">{{ $tx->tanggal_pengajuan->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-semibold d-block">{{ $tx->user->name ?? 'Warga' }}</span>
                                        <span class="badge bg-light text-secondary border">RT {{ str_pad($tx->rt_number, 3, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $tx->total_berat_formatted }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">{{ $tx->total_rupiah_formatted }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $tx->status_badge['bg'] }} border px-2 py-1 rounded-pill fw-bold">
                                            {{ $tx->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('bank-sampah.show', $tx->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada transaksi setoran tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Panduan Pemilahan & Manfaat -->
        <div class="col-lg-5">
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                    <i data-lucide="help-circle" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Alur Kerja Setoran Bank Sampah
                </h5>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; min-width: 32px;">1</div>
                        <div>
                            <strong class="text-dark">Warga Mengajukan Setoran</strong>
                            <p class="text-muted small mb-0">Warga memilah sampah di rumah dan mengajukan setoran (antar ke drop point atau jemput ke rumah).</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; min-width: 32px;">2</div>
                        <div>
                            <strong class="text-dark">Petugas RT Memeriksa & Menimbang</strong>
                            <p class="text-muted small mb-0">Petugas memeriksa kondisi sampah dan menimbang per kategori jenis sampah secara transparan.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; min-width: 32px;">3</div>
                        <div>
                            <strong class="text-dark">Perhitungan Otomatis & Konfirmasi</strong>
                            <p class="text-muted small mb-0">Sistem mengalikan berat aktual dengan tarif resmi aktif untuk menentukan nilai rupiah.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; min-width: 32px;">4</div>
                        <div>
                            <strong class="text-dark">Saldo Masuk & Resi Digital Terbit</strong>
                            <p class="text-muted small mb-0">Saldo tabungan sampah warga otomatis bertambah dan riwayat transaksi tercatat abadi di buku ledger.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-3 bg-emerald-50 border border-emerald-200" style="background: #E8F5E9;">
                    <div class="d-flex align-items-center gap-2 text-success fw-bold mb-1">
                        <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i> Transparansi Finansial
                    </div>
                    <p class="small text-muted mb-0">
                        Saldo tidak akan bertambah sebelum transaksi berstatus <strong>Selesai</strong> dan dikonfirmasi oleh petugas berwenang. Seluruh mutasi dana tercatat pada audit ledger.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: AJUKAN SETORAN SAMPAH -->
<div class="modal fade" id="modalSetorSampah" tabindex="-1" aria-labelledby="modalSetorSampahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalSetorSampahLabel">
                    <i data-lucide="recycle" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Formulir Pengajuan Setoran Sampah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bank-sampah.setor') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Pilih Unit Drop Point Bank Sampah <span class="text-danger">*</span></label>
                        <select name="bank_sampah_id" id="selectBankSampah" class="form-select rounded-3" required>
                            @foreach($bankSampahList as $bs)
                                <option value="{{ $bs->id }}" {{ $user->rt_number == $bs->rt_number ? 'selected' : '' }}>
                                    {{ $bs->nama_unit }} (RT {{ str_pad($bs->rt_number, 3, '0', STR_PAD_LEFT) }}) - {{ $bs->lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Metode Penyerahan Sampah <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check p-3 border rounded-3 flex-fill">
                                <input class="form-check-input" type="radio" name="metode_penyerahan" id="metodeDropPoint" value="drop_point" checked>
                                <label class="form-check-label fw-semibold small" for="metodeDropPoint">
                                    <i data-lucide="map-pin" class="text-primary me-1" style="width: 16px; height: 16px;"></i> Antar ke Drop Point
                                </label>
                            </div>
                            <div class="form-check p-3 border rounded-3 flex-fill">
                                <input class="form-check-input" type="radio" name="metode_penyerahan" id="metodeJemput" value="jemput_ke_rumah">
                                <label class="form-check-label fw-semibold small" for="metodeJemput">
                                    <i data-lucide="truck" class="text-success me-1" style="width: 16px; height: 16px;"></i> Jemput ke Rumah
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Perkiraan Jenis & Volume Sampah</label>
                        <input type="text" name="estimasi_deskripsi" class="form-control rounded-3" placeholder="Contoh: 2 karung botol plastik mineral + 1 dus kardus" value="{{ old('estimasi_deskripsi') }}">
                        <small class="text-muted">Petugas akan melakukan penimbangan aktual saat penyerahan sampah.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Catatan Tambahan / Alamat Penjemputan</label>
                        <textarea name="catatan_warga" class="form-control rounded-3" rows="3" placeholder="Catatan untuk petugas atau patokan rumah bila meminta penjemputan...">{{ old('catatan_warga') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i data-lucide="send" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Set unit otomatis jika klik dari tabel
        document.querySelectorAll('.btn-setor-unit').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var unitId = this.getAttribute('data-unit-id');
                var select = document.getElementById('selectBankSampah');
                if (select && unitId) {
                    select.value = unitId;
                }
            });
        });
    });
</script>
@endsection
