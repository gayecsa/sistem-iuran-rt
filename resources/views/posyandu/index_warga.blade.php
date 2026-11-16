@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Hero Banner Portal Posyandu Warga -->
    <div class="wm-card mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #16865D 0%, #123766 100%); color: #fff; border: none; border-radius: 16px;">
        <div class="p-4 p-md-5 position-relative" style="z-index: 2;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div style="max-width: 720px;">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-light text-success fw-bold px-3 py-1 rounded-pill" style="color: #16865D !important; font-size: 11px; letter-spacing: 0.5px;">
                            <i data-lucide="heart" style="width: 14px; height: 14px; vertical-align: middle;"></i> LAYANAN KESEHATAN KELUARGA WARGA
                        </span>
                        <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill opacity-90" style="font-size: 11px;">
                            Posyandu RT {{ $rtStr }} RW 013
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">
                        Posyandu Keluarga RT {{ $rtStr }}
                    </h2>
                    <p class="mb-0 text-white text-opacity-75 lead fs-6">
                        Informasi jadwal posyandu terpadu, pemantauan riwayat penimbangan balita, kesehatan ibu hamil, serta skrining kesehatan lansia khusus bagi keluarga Anda.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($unregisteredFamilyMembers->count() > 0)
                        <button type="button" class="btn btn-light rounded-pill px-3 py-2 fw-bold text-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDaftarKeluarga">
                            <i data-lucide="user-plus" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Daftarkan Anggota Keluarga
                        </button>
                    @endif
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold">
                        <i data-lucide="layout-grid" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Ke Dashboard
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

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="rounded-circle bg-danger text-white p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="text-danger d-block">Peringatan</strong>
                <span class="small text-dark">{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 3 KARTU INFORMASI UTAMA: JADWAL BERIKUTNYA, LOKASI & KADER -->
    <div class="row g-3 mb-4">
        <!-- Jadwal Berikutnya -->
        <div class="col-12 col-md-4">
            <div class="wm-card text-center p-4 h-100 bg-white border rounded-4 shadow-sm">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-3" style="width: 48px; height: 48px; background: #E8F5E9; color: #16865D;">
                    <i data-lucide="calendar" style="width: 24px; height: 24px;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Jadwal Posyandu RT {{ $rtStr }}</h6>
                @if($nextEvent)
                    <p class="text-success fw-bold mb-1 fs-5">{{ $nextEvent->tanggal_formatted }}</p>
                    <small class="text-muted">Pukul {{ $nextEvent->jam_mulai }} - {{ $nextEvent->jam_selesai }} WIB</small>
                @else
                    <p class="text-muted fw-semibold mb-1">Jadwal Rutin Bulanan</p>
                    <small class="text-muted">{{ $posyanduUnit->jadwal ?? 'Sabtu Pertama (08.00 - 11.00 WIB)' }}</small>
                @endif
            </div>
        </div>

        <!-- Lokasi Pelayanan -->
        <div class="col-12 col-md-4">
            <div class="wm-card text-center p-4 h-100 bg-white border rounded-4 shadow-sm">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-3" style="width: 48px; height: 48px; background: #E1F5FE; color: #0288D1;">
                    <i data-lucide="map-pin" style="width: 24px; height: 24px;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Lokasi Pos Pelayanan</h6>
                <p class="text-primary fw-bold mb-1 fs-6 text-truncate" title="{{ $posyanduUnit->lokasi ?? 'Balai Pertemuan Warga RT ' . $rtStr }}">
                    {{ $posyanduUnit->lokasi ?? 'Balai Pertemuan Warga RT ' . $rtStr }}
                </p>
                <small class="text-muted">UPTD Puskesmas Arcamanik</small>
            </div>
        </div>

        <!-- Kader Penanggung Jawab -->
        <div class="col-12 col-md-4">
            <div class="wm-card text-center p-4 h-100 bg-white border rounded-4 shadow-sm">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-3" style="width: 48px; height: 48px; background: #FFF8E1; color: #F59E0B;">
                    <i data-lucide="phone-call" style="width: 24px; height: 24px;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Kontak Kader RT {{ $rtStr }}</h6>
                @if($kaders->first())
                    <p class="text-dark fw-bold mb-1">{{ $kaders->first()->nama_kader }}</p>
                    <small class="text-muted">{{ $kaders->first()->no_telepon ?? 'Hubungi Pengurus RT' }}</small>
                @else
                    <p class="text-dark fw-bold mb-1">Pengurus RT {{ $rtStr }}</p>
                    <small class="text-muted">Balai Warga Sukamiskin</small>
                @endif
            </div>
        </div>
    </div>

    <!-- ANGGOTA KELUARGA TERDAFTAR POSYANDU -->
    <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i data-lucide="users" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Anggota Keluarga Terdaftar di Posyandu
                </h5>
                <p class="mb-0 text-muted small">
                    Daftar balita, ibu hamil, dan lansia dalam kartu keluarga Anda yang terhubung dengan buku pemantauan kesehatan digital.
                </p>
            </div>
            @if($unregisteredFamilyMembers->count() > 0)
                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalDaftarKeluarga">
                    <i data-lucide="plus" style="width: 14px; height: 14px; vertical-align: middle;"></i> Daftarkan Anggota Keluarga
                </button>
            @endif
        </div>

        <div class="row g-3">
            @forelse($myFamilyPeserta as $fp)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="p-3 border rounded-3 h-100 bg-white shadow-sm d-flex flex-column justify-content-between position-relative overflow-hidden">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong class="text-dark fs-6 d-block">{{ $fp->user->name }}</strong>
                                    <span class="badge {{ $fp->kategori_badge }} px-2 py-1 rounded-pill" style="font-size: 11px;">
                                        {{ $fp->kategori_label }}
                                    </span>
                                </div>
                                <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">
                                    {{ $fp->usia_text }}
                                </span>
                            </div>

                            @php $latest = $fp->pengukurans->first(); @endphp
                            <div class="p-2 rounded-2 bg-light-subtle border mb-2">
                                <small class="text-muted d-block fw-semibold mb-1" style="font-size: 11px;">Pemeriksaan Terakhir:</small>
                                @if($latest)
                                    <div class="d-flex align-items-center gap-2 small">
                                        @if($latest->berat_badan) <span>BB: <strong>{{ $latest->berat_badan }} kg</strong></span> @endif
                                        @if($latest->tinggi_badan) <span>TB: <strong>{{ $latest->tinggi_badan }} cm</strong></span> @endif
                                        @if($latest->tekanan_darah) <span>TD: <strong>{{ $latest->tekanan_darah }}</strong></span> @endif
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 10px;">{{ $latest->tanggal_formatted }}</small>
                                @else
                                    <small class="text-muted">Belum ada catatan pengukuran</small>
                                @endif
                            </div>

                            @if($latest && $latest->anjuran_rujukan)
                                <div class="p-2 rounded-2 bg-warning-subtle text-warning-emphasis small mb-2 border border-warning-subtle" style="font-size: 11px;">
                                    <i data-lucide="info" style="width: 12px; height: 12px; vertical-align: middle;"></i>
                                    <strong>Catatan Nakes:</strong> {{ $latest->anjuran_rujukan }}
                                </div>
                            @endif
                        </div>

                        <div class="pt-2 border-top text-end">
                            <a href="{{ route('posyandu.peserta.show', $fp->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                <i data-lucide="activity" style="width: 14px; height: 14px; vertical-align: middle;"></i> Lihat Buku Riwayat &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-4 text-center text-muted">
                    <i data-lucide="user-x" class="mb-2" style="width: 32px; height: 32px;"></i>
                    <p class="mb-2">Belum ada anggota keluarga Anda yang terdaftar sebagai peserta Posyandu RT {{ $rtStr }}.</p>
                    @if($unregisteredFamilyMembers->count() > 0)
                        <button type="button" class="btn btn-sm btn-success rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalDaftarKeluarga">
                            Daftarkan Balita / Ibu Hamil / Lansia Sekarang
                        </button>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    <!-- RIWAYAT PENGUKURAN & CATATAN LAYANAN ANGGOTA KELUARGA -->
    <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i data-lucide="clipboard-list" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Riwayat Kunjungan & Pengukuran Keluarga Anda
                </h5>
                <p class="mb-0 text-muted small">Catatan resmi pelayanan dari kader Posyandu dan tenaga kesehatan UPTD Puskesmas Arcamanik.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 text-secondary small fw-bold">Nama Anggota</th>
                        <th class="text-secondary small fw-bold">Tanggal</th>
                        <th class="text-secondary small fw-bold">Hasil Pengukuran</th>
                        <th class="text-secondary small fw-bold">Layanan Diberikan</th>
                        <th class="text-secondary small fw-bold">Anjuran / Catatan Nakes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatPengukuran as $rp)
                        <tr>
                            <td class="ps-3">
                                <strong class="text-dark d-block">{{ $rp->peserta->user->name ?? 'Anggota' }}</strong>
                                <span class="badge {{ $rp->peserta->kategori_badge ?? 'bg-light text-dark' }} px-2 py-0 rounded-pill" style="font-size: 10px;">
                                    {{ $rp->peserta->kategori_label ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-dark fw-medium">{{ $rp->tanggal_formatted }}</small>
                            </td>
                            <td>
                                <div class="small text-dark">
                                    @if($rp->berat_badan) <span>BB: <strong>{{ $rp->berat_badan }} kg</strong></span> @endif
                                    @if($rp->tinggi_badan) <span class="ms-1">TB: <strong>{{ $rp->tinggi_badan }} cm</strong></span> @endif
                                    @if($rp->lingkar_kepala) <span class="ms-1">LK: <strong>{{ $rp->lingkar_kepala }} cm</strong></span> @endif
                                    @if($rp->tekanan_darah) <span class="ms-1">TD: <strong>{{ $rp->tekanan_darah }}</strong></span> @endif
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle mt-1" style="font-size: 10px;">
                                    {{ $rp->status_pertumbuhan ?? 'Tercatat' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-secondary">{{ $rp->tindakan_layanan ?? 'Pelayanan Posyandu Rutin' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $rp->catatan_petugas ?? '-' }}</small>
                                @if($rp->anjuran_rujukan)
                                    <div class="text-danger fw-semibold small mt-1" style="font-size: 11px;">
                                        <i data-lucide="alert-circle" style="width: 12px; height: 12px; vertical-align: middle;"></i>
                                        Rujukan: {{ $rp->anjuran_rujukan }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pengukuran tercatat untuk anggota keluarga Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 10 LAYANAN KESEHATAN POSYANDU TERPADU RW 013 -->
    <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
        <h5 class="fw-bold mb-1 text-dark">
            <i data-lucide="check-circle" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
            Daftar 10 Layanan Terpadu Posyandu Melati 13 RW 013
        </h5>
        <p class="text-muted small mb-3">Layanan promotif dan preventif resmi berbasis koordinasi pengurus RT dan Puskesmas.</p>

        <div class="row g-3">
            @php
                $layananKatalog = [
                    ['title' => 'Penimbangan Bayi & Balita', 'desc' => 'Deteksi dini pertumbuhan dengan timbangan terstandar berkala.'],
                    ['title' => 'Pengukuran Tinggi/Panjang & LK', 'desc' => 'Pemantauan stunting dan lingkar kepala bayi/balita.'],
                    ['title' => 'Pemantauan Tumbuh Kembang Anak', 'desc' => 'Pencatatan kurva Kartu Menuju Sehat (KMS) digital.'],
                    ['title' => 'Edukasi Gizi & Pemberian PMT', 'desc' => 'Pemberian makanan tambahan gizi seimbang untuk balita dan ibu hamil.'],
                    ['title' => 'Pelayanan Kesehatan Ibu Hamil', 'desc' => 'Pemeriksaan berat badan, tekanan darah, dan edukasi masa kehamilan.'],
                    ['title' => 'Pemantauan Ibu Menyusui', 'desc' => 'Pemberian edukasi ASI eksklusif dan nutrisi ibu pasca persalinan.'],
                    ['title' => 'Pencatatan Imunisasi Dasar', 'desc' => 'Pencatatan bukti imunisasi resmi yang diberikan nakes Puskesmas.'],
                    ['title' => 'Pemantauan Kesehatan Lansia', 'desc' => 'Pemeriksaan tekanan darah, berat badan, dan senam kebugaran lansia.'],
                    ['title' => 'Penyuluhan Kesehatan Keluarga', 'desc' => 'Edukasi PHBS (Perilaku Hidup Bersih & Sehat) di lingkungan RW 013.'],
                    ['title' => 'Anjuran Rujukan ke Puskesmas', 'desc' => 'Konsultasi rujukan tepat waktu bagi keluhan yang memerlukan dokter.']
                ];
            @endphp
            @foreach($layananKatalog as $idx => $lk)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="p-3 border rounded-3 bg-light-subtle h-100">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary text-white rounded-circle" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">
                                {{ $idx + 1 }}
                            </span>
                            <strong class="text-dark small">{{ $lk['title'] }}</strong>
                        </div>
                        <p class="text-muted mb-0 small" style="font-size: 12px; margin-left: 30px;">{{ $lk['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- MODAL DAFTARKAN ANGGOTA KELUARGA -->
@if($unregisteredFamilyMembers->count() > 0)
<div class="modal fade" id="modalDaftarKeluarga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i data-lucide="user-plus" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Daftarkan Anggota Keluarga ke Posyandu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posyandu.daftar_keluarga') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Pilih anggota keluarga dalam Kartu Keluarga Anda untuk didaftarkan ke Posyandu Melati 13 RT {{ $rtStr }}.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Anggota Keluarga *</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Pilih Anggota Keluarga --</option>
                            @foreach($unregisteredFamilyMembers as $ufm)
                                <option value="{{ $ufm->id }}">
                                    {{ $ufm->name }} ({{ $ufm->gender }}, Lahir: {{ $ufm->tanggal_lahir ? Carbon\Carbon::parse($ufm->tanggal_lahir)->format('d/m/Y') : '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kategori Layanan *</label>
                        <select name="kategori" class="form-select" required>
                            <option value="balita">Balita (1 - 5 Tahun)</option>
                            <option value="bayi">Bayi (0 - 11 Bulan)</option>
                            <option value="anak">Anak Pra-Sekolah</option>
                            <option value="ibu_hamil">Ibu Hamil</option>
                            <option value="ibu_menyusui">Ibu Menyusui</option>
                            <option value="lansia">Lansia (60+ Tahun)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan Kondisi Khusus / Riwayat</label>
                        <textarea name="catatan_khusus" class="form-control" rows="2" placeholder="Informasi kesehatan awal yang ingin disampaikan kepada kader..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Daftarkan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
