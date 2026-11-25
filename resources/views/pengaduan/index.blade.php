@extends('layouts.app')

@section('title', 'Layanan Pengaduan Warga')

@section('content')
<div class="container-fluid p-0">
    @php
        $isWarga = $user->isWarga();
        $isRt = $user->isRt();
        $isRwOrAdmin = $user->isRw() || in_array($user->role, ['admin', 'super_admin', 'bendahara']);
    @endphp

    <!-- 1. Header Hero Pengaduan Warga (Warkas Blue Design) -->
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%); color: #fff; border-radius: 16px; padding: 24px 28px;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-white text-primary mb-2 fw-bold px-3 py-1 rounded-pill" style="font-size: 0.75rem;">
                    @if($isWarga)
                        <i data-lucide="user-check" style="width: 12px; height: 12px; vertical-align: middle;"></i> LAYANAN ASPIRASI WARGA
                    @elseif($isRt)
                        <i data-lucide="shield" style="width: 12px; height: 12px; vertical-align: middle;"></i> PENGURUS RT {{ $user->clean_rt_label }}
                    @else
                        <i data-lucide="building" style="width: 12px; height: 12px; vertical-align: middle;"></i> KOORDINASI PUSAT RW 013
                    @endif
                </span>
                <h3 class="fw-bold mb-1 text-white">Sistem Pengaduan &amp; Aspirasi Warga RW 013</h3>
                <p class="mb-0 text-white opacity-75 small">
                    @if($isWarga)
                        Sampaikan keluhan fasilitas, kebersihan, atau keamanan di lingkungan Anda. Penanganan dipantau langsung oleh Pengurus RT &amp; RW secara transparan.
                    @elseif($isRt)
                        Pusat tindak lanjut pengaduan, keluhan fasilitas, dan aspirasi warga di lingkungan teritorial <strong>RT {{ $user->clean_rt_label }}</strong>.
                    @else
                        Pusat monitoring dan koordinasi tindak lanjut seluruh pengaduan warga dari 8 Rukun Tetangga (RT 001 – RT 008) se-RW 013.
                    @endif
                </p>
            </div>
            <button type="button" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahPengaduan">
                <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                <span>Buat Pengaduan Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. Strip Ringkasan Statistik Pengaduan (Sesuai Hak Akses Peran) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="wm-card p-3 d-flex align-items-center gap-3">
                <div class="p-2 rounded-3 bg-primary-subtle text-primary" style="font-size: 1.25rem;">
                    <i data-lucide="clipboard-list" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">{{ $stats['total'] ?? count($pengaduanList) }}</h4>
                    <small class="text-muted">Total Pengaduan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="wm-card p-3 d-flex align-items-center gap-3">
                <div class="p-2 rounded-3 bg-warning-subtle text-warning" style="font-size: 1.25rem;">
                    <i data-lucide="alert-triangle" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">{{ $stats['menunggu'] ?? 0 }}</h4>
                    <small class="text-muted">Menunggu Verifikasi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="wm-card p-3 d-flex align-items-center gap-3">
                <div class="p-2 rounded-3 bg-info-subtle text-info" style="font-size: 1.25rem;">
                    <i data-lucide="clock" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">{{ $stats['diproses'] ?? 0 }}</h4>
                    <small class="text-muted">Sedang Ditangani</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="wm-card p-3 d-flex align-items-center gap-3">
                <div class="p-2 rounded-3 bg-success-subtle text-success" style="font-size: 1.25rem;">
                    <i data-lucide="check-circle" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">{{ $stats['selesai'] ?? 0 }}</h4>
                    <small class="text-muted">Selesai Ditangani</small>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Filter Pengaduan (Disesuaikan Hak Akses) -->
    <div class="wm-card mb-4">
        <form method="GET" action="{{ route('pengaduan.index') }}" class="row g-3 align-items-end">
            <div class="{{ $isRwOrAdmin ? 'col-md-4' : 'col-md-5' }}">
                <label class="form-label fw-bold text-dark small">Filter Kategori</label>
                <select name="kategori" class="form-select rounded-3">
                    <option value="">Semua Kategori</option>
                    <option value="Fasilitas & Lampu Jalan" {{ ($kategori == 'Fasilitas & Lampu Jalan') ? 'selected' : '' }}>Fasilitas &amp; Lampu Jalan</option>
                    <option value="Kebersihan & Sampah" {{ ($kategori == 'Kebersihan & Sampah') ? 'selected' : '' }}>Kebersihan &amp; Sampah</option>
                    <option value="Keamanan & Ketertiban" {{ ($kategori == 'Keamanan & Ketertiban') ? 'selected' : '' }}>Keamanan &amp; Ketertiban</option>
                    <option value="Drainase & Saluran Air" {{ ($kategori == 'Drainase & Saluran Air') ? 'selected' : '' }}>Drainase &amp; Saluran Air</option>
                    <option value="Sosial & Ketertiban Umum" {{ ($kategori == 'Sosial & Ketertiban Umum') ? 'selected' : '' }}>Sosial &amp; Ketertiban Umum</option>
                </select>
            </div>
            <div class="{{ $isRwOrAdmin ? 'col-md-3' : 'col-md-4' }}">
                <label class="form-label fw-bold text-dark small">Filter Status Penanganan</label>
                <select name="status" class="form-select rounded-3">
                    <option value="">Semua Status</option>
                    <option value="Menunggu" {{ ($status == 'Menunggu') ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Diproses" {{ ($status == 'Diproses') ? 'selected' : '' }}>Sedang Ditangani</option>
                    <option value="Selesai" {{ ($status == 'Selesai') ? 'selected' : '' }}>Selesai Ditangani</option>
                </select>
            </div>

            {{-- Filter RT Khusus Pengurus RW / Administrator --}}
            @if($isRwOrAdmin)
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark small">Filter Wilayah RT</label>
                    <select name="rt" class="form-select rounded-3">
                        <option value="">Semua RT (RW 013)</option>
                        @for($i = 1; $i <= 8; $i++)
                            @php $rtVal = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $rtVal }}" {{ ($filterRt == $rtVal || $filterRt == $i) ? 'selected' : '' }}>
                                RT {{ $rtVal }}
                            </option>
                        @endfor
                    </select>
                </div>
            @endif

            <div class="{{ $isRwOrAdmin ? 'col-md-2' : 'col-md-3' }} d-flex gap-2">
                <button type="submit" class="wm-btn-primary flex-grow-1 justify-content-center">
                    <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Filter
                </button>
                <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary rounded-3 px-3 d-flex align-items-center" title="Reset Filter">
                    <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- 4. Daftar Pengaduan Warga (Terkunci Sesuai Peran) -->
    <div class="wm-card">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i data-lucide="list-ordered" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    @if($isWarga)
                        Riwayat Pengaduan Saya ({{ count($pengaduanList) }})
                    @elseif($isRt)
                        Daftar Pengaduan Warga RT {{ $user->clean_rt_label }} ({{ count($pengaduanList) }})
                    @else
                        Daftar Seluruh Pengaduan Warga RW 013 ({{ count($pengaduanList) }})
                    @endif
                </h5>
                <small class="text-muted">
                    @if($isWarga)
                        Menampilkan laporan pengaduan yang Anda sampaikan beserta status tindak lanjut pengurus.
                    @elseif($isRt)
                        Pengaduan warga di wilayah otoritas <strong>RT {{ $user->clean_rt_label }}</strong> yang perlu diverifikasi dan ditindaklanjuti.
                    @else
                        Monitoring dan tindak lanjut seluruh pengaduan warga se-wilayah RW 013 Sukamiskin.
                    @endif
                </small>
            </div>
            <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill">
                Total: {{ count($pengaduanList) }} Data
            </span>
        </div>

        <div class="d-flex flex-column gap-3">
            @forelse($pengaduanList as $adu)
                <div class="p-3 rounded-4 border bg-light">
                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap mb-2">
                        <div>
                            <span class="badge bg-primary text-white me-1">{{ $adu['rt'] }}</span>
                            <span class="badge bg-light text-dark border">{{ $adu['kategori'] }}</span>
                            <span class="text-muted small ms-2"><i class="far fa-clock me-1"></i>{{ $adu['created_at'] }}</span>
                            <h6 class="fw-bold text-dark mb-0 mt-2">{{ $adu['lokasi'] }}</h6>
                            <small class="text-muted d-flex align-items-center gap-1 mt-1">
                                <img src="{{ $adu['avatar'] ?? asset('images/avatar-default.svg') }}" alt="Avatar" class="rounded-circle border" style="width: 22px; height: 22px; object-fit: cover;">
                                @if($isWarga)
                                    Pelapor: <strong>Anda Sendiri</strong>
                                @else
                                    Pelapor: <strong>{{ $adu['pelapor'] }}</strong> ({{ $adu['phone'] ?? '-' }})
                                @endif
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($adu['status'] === 'Selesai')
                                <span class="status-badge status-lengkap"><i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Selesai</span>
                            @elseif($adu['status'] === 'Diproses')
                                <span class="status-badge status-periksa"><i data-lucide="clock" style="width: 14px; height: 14px;"></i> Diproses</span>
                            @else
                                <span class="status-badge status-tindak-lanjut"><i data-lucide="alert-triangle" style="width: 14px; height: 14px;"></i> Menunggu Verifikasi</span>
                            @endif

                            {{-- Tombol Tindak Lanjut HANYA untuk Pengurus (RT pada RT-nya, atau RW/Admin) --}}
                            @if(!$isWarga)
                                @php
                                    $canManage = $isRwOrAdmin || ($isRt && (str_contains($adu['rt'], $user->clean_rt_label) || intval(preg_replace('/\D/', '', $adu['rt'])) === intval($user->clean_rt_number)));
                                @endphp
                                @if($canManage)
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalUpdatePengaduan{{ $adu['id'] }}">
                                        <i data-lucide="edit" style="width: 13px; height: 13px;"></i> Tindak Lanjut
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    <p class="text-secondary small mb-2 p-2 rounded-3 bg-white border" style="line-height: 1.5; white-space: pre-line;">{{ $adu['deskripsi'] }}</p>

                    @if(!empty($adu['foto']))
                        <div class="mb-2">
                            <a href="{{ $adu['foto'] }}" target="_blank" class="d-inline-flex align-items-center gap-1 btn btn-sm btn-outline-secondary rounded-3 py-1 px-2">
                                <i data-lucide="image" style="width: 14px; height: 14px;"></i>
                                <span class="small">Lihat Foto Lampiran</span>
                            </a>
                        </div>
                    @endif

                    <div class="p-2 rounded-3 bg-white border small text-secondary">
                        <strong class="text-dark"><i data-lucide="message-circle" class="text-primary me-1" style="width: 14px; height: 14px; vertical-align: middle;"></i>Catatan Tindak Lanjut Pengurus:</strong>
                        {{ $adu['catatan_tindak_lanjut'] ?? 'Pengurus lingkungan sedang mengoordinasikan tindak lanjut atas pengaduan ini.' }}
                    </div>
                </div>

                <!-- Modal Update Status Tindak Lanjut Pengaduan (Khusus Pengurus RT & RW) -->
                @if(!$isWarga)
                    <div class="modal fade" id="modalUpdatePengaduan{{ $adu['id'] }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <form method="POST" action="{{ route('pengaduan.status', $adu['id']) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-header border-bottom">
                                        <h6 class="modal-title fw-bold text-dark">
                                            <i data-lucide="edit-3" class="me-2 text-primary" style="width: 18px; height: 18px;"></i>
                                            Tindak Lanjut Pengaduan #{{ $adu['id'] }} ({{ $adu['rt'] }})
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-dark">Status Penanganan</label>
                                            <select name="status" class="form-select rounded-3" required>
                                                <option value="Menunggu" {{ $adu['status'] === 'Menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                                <option value="Diproses" {{ $adu['status'] === 'Diproses' ? 'selected' : '' }}>Sedang Diproses / Ditangani</option>
                                                <option value="Selesai" {{ $adu['status'] === 'Selesai' ? 'selected' : '' }}>Selesai Ditangani</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-dark">Catatan Tindak Lanjut Pengurus</label>
                                            <textarea name="catatan_tindak_lanjut" rows="4" class="form-control rounded-3" placeholder="Masukkan instruksi penanganan, nama penanggung jawab, atau hasil pekerjaan...">{{ $adu['catatan_tindak_lanjut'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="wm-btn-primary">
                                            <i data-lucide="save" style="width: 15px; height: 15px;"></i> Simpan Tindak Lanjut
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center py-5 text-muted">
                    <div class="p-3 bg-light rounded-circle d-inline-flex mb-3">
                        <i data-lucide="shield-check" style="width: 44px; height: 44px;" class="text-primary"></i>
                    </div>
                    @if($isWarga)
                        <h6 class="fw-bold text-dark mb-1">Belum Ada Riwayat Pengaduan</h6>
                        <p class="small text-muted mb-3" style="max-width: 460px; margin: 0 auto;">
                            Anda belum pernah mengajukan laporan pengaduan. Jika Anda menemukan fasilitas umum yang rusak, permasalahan sampah, atau gangguan ketertiban di lingkungan RT {{ $user->clean_rt_label }}, silakan buat pengaduan baru.
                        </p>
                        <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahPengaduan" style="background: #1456A0; border-color: #1456A0;">
                            <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i> Buat Pengaduan Sekarang
                        </button>
                    @else
                        <h6 class="fw-bold text-dark mb-1">Tidak Ada Data Pengaduan</h6>
                        <p class="small text-muted mb-0">Tidak ditemukan laporan pengaduan yang sesuai dengan filter pencarian saat ini.</p>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Tambah Pengaduan Baru (Dengan Identitas Pelapor Terdeteksi Otomatis) -->
<div class="modal fade" id="modalTambahPengaduan" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <form method="POST" action="{{ route('pengaduan.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="modalTambahLabel">
                        <i data-lucide="plus-circle" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Formulir Pengaduan &amp; Aspirasi Warga RW 013
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Kartu Identitas Pelapor (Terdeteksi Otomatis) --}}
                    <div class="p-3 mb-3 rounded-3 border" style="background: #F8FAFC; border-color: #E2E8F0;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-uppercase" style="color: #1456A0; letter-spacing: 0.5px; font-size: 0.72rem;">
                                <i data-lucide="user-check" style="width: 13px; height: 13px;" class="me-1"></i>
                                Identitas Pelapor (Terdeteksi Otomatis)
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25" style="font-size: 0.68rem;">
                                Akun Terverifikasi
                            </span>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Nama Lengkap:</span>
                                <strong class="text-dark">{{ $user->name }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Wilayah Asal:</span>
                                <strong class="text-dark">RT {{ $user->clean_rt_label }} / RW 013</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Nomor Telepon/WA:</span>
                                <strong class="text-dark">{{ $user->phone ?: '-' }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Alamat:</span>
                                <span class="text-dark text-truncate d-block" title="{{ $user->address }}">{{ $user->address ?: 'Warga Sukamiskin Arcamanik' }}</span>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">
                            <i data-lucide="info" style="width: 11px; height: 11px;" class="me-1 text-primary"></i>
                            Identitas Anda otomatis terhubung dengan sistem untuk verifikasi dan respon cepat dari pengurus RT {{ $user->clean_rt_label }} &amp; RW 013.
                        </small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Kategori Pengaduan <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select rounded-3" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Keamanan & Ketertiban">Keamanan &amp; Ketertiban Lingkungan</option>
                                <option value="Kebersihan & Sampah">Kebersihan &amp; Pengangkutan Sampah</option>
                                <option value="Fasilitas & Lampu Jalan">Fasilitas Umum &amp; Lampu PJU</option>
                                <option value="Drainase & Saluran Air">Drainase, Banjir &amp; Gorong-gorong</option>
                                <option value="Sosial & Ketertiban Umum">Sosial &amp; Ketertiban Tetangga</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Wilayah RT Terkait <span class="text-danger">*</span></label>
                            @if($isWarga)
                                <input type="text" class="form-control rounded-3 bg-light" value="RT {{ $user->clean_rt_label }} RW 013 (Terkunci Sesuai Domisili)" readonly>
                                <input type="hidden" name="rt" value="RT {{ $user->clean_rt_label }}">
                            @else
                                <select name="rt" class="form-select rounded-3" required>
                                    <option value="">-- Pilih RT --</option>
                                    @for($i = 1; $i <= 8; $i++)
                                        @php $rtOption = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                                        <option value="RT {{ $rtOption }}" {{ ($user->clean_rt_number == $i || $user->clean_rt_number == $rtOption) ? 'selected' : '' }}>
                                            RT {{ $rtOption }} RW 013
                                        </option>
                                    @endfor
                                </select>
                            @endif
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Lokasi Spesifik / Patokan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" class="form-control rounded-3" placeholder="Contoh: Depan Musholla RT 003, Jl. Sukamiskin Raya No. 12" required maxlength="255">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Deskripsi Pengaduan Lengkap <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" rows="4" class="form-control rounded-3" placeholder="Jelaskan secara jelas kendala yang dialami, sejak kapan terjadi, dan harapan penanganannya..." required minlength="10"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Lampirkan Foto Bukti (Opsional)</label>
                            <input type="file" name="foto" class="form-control rounded-3" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted">Format JPG, PNG, atau WEBP (Maksimal ukuran 3 MB).</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="wm-btn-primary">
                        <i data-lucide="send" style="width: 16px; height: 16px;"></i> Kirim Pengaduan ke RW 013
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
