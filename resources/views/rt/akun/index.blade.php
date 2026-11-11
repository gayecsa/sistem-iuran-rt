@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    {{-- ======================================================================
         BAGIAN ATAS: JUDUL UTAMA & DESKRIPSI SESUAI SPESIFIKASI
         ====================================================================== --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background: var(--wm-primary); color: #fff; font-size: 0.75rem; border-radius: 6px; padding: 4px 8px;">
                    PUSAT KENDALI RW
                </span>
                <span class="text-muted small">RW 013 &bull; Kelurahan Machi</span>
            </div>
            <h3 class="fw-bold mb-1 text-dark mt-1">
                <i data-lucide="key-round" class="text-primary me-2" style="width: 28px; height: 28px; vertical-align: middle;"></i>
                Manajemen Akun RT
            </h3>
            <p class="text-muted small mb-0">
                RW 013 – Kelurahan Machi &bull; Pengelolaan terpusat kredensial login, aktivasi akses, dan audit login Ketua RT 001 sampai RT 008.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($totalBelumAdaAkun > 0)
                <form method="POST" action="{{ route('rt.akun.generate') }}" class="m-0" onsubmit="return confirm('Siapkan akun awal default untuk RT yang belum memiliki akun?');">
                    @csrf
                    <button type="submit" class="wm-btn-outline" style="border-color: #D97706; color: #D97706;" title="Generate akun awal RT yang belum terdaftar">
                        <i data-lucide="wand-2" style="width: 17px; height: 17px;"></i> Siapkan Akun Otomatis
                    </button>
                </form>
            @endif
            <button type="button" class="wm-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAkun">
                <i data-lucide="user-plus" style="width: 17px; height: 17px;"></i> + Tambah Akun RT
            </button>
            <a href="{{ route('dashboard.rw') }}" class="wm-btn-outline">
                <i data-lucide="arrow-left" style="width: 17px; height: 17px;"></i> Dashboard RW
            </a>
        </div>
    </div>

    {{-- ======================================================================
         ALERT FEEDBACK / NOTIFIKASI SUKSES & ERROR
         ====================================================================== --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-left: 5px solid var(--wm-green); background: #E3F5EC; color: #0F5132;">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="check-circle-2" style="width: 22px; height: 22px; color: var(--wm-green); flex-shrink: 0;"></i>
                <div>
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-left: 5px solid #DC2626; background: #FEE2E2; color: #842029;">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="alert-octagon" style="width: 22px; height: 22px; color: #DC2626; flex-shrink: 0;"></i>
                <div>
                    <strong>Peringatan Keamanan:</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-left: 5px solid #DC2626;">
            <div class="d-flex align-items-start gap-2">
                <i data-lucide="alert-circle" style="width: 20px; height: 20px; color: #DC2626; flex-shrink: 0; margin-top: 2px;"></i>
                <div>
                    <strong>Terdapat kesalahan pada input form:</strong>
                    <ul class="mb-0 mt-1 small ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ======================================================================
         EMPAT (4) STATISTIK UTAMA SESUAI SPESIFIKASI KEBUTUHAN
         ====================================================================== --}}
    <div class="row g-3 mb-4">
        <!-- 1. Total Wilayah RT: 8 -->
        <div class="col-xl-3 col-md-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Total Wilayah RT</span>
                    <div class="wm-stat-icon-wrap" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="building-2"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-primary">{{ $totalWilayahRt }}</div>
                <div class="small text-muted fw-semibold">Cakupan RT 001 - RT 008</div>
            </div>
        </div>

        <!-- 2. Jumlah Akun RT Aktif -->
        <div class="col-xl-3 col-md-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Akun RT Aktif</span>
                    <div class="wm-stat-icon-wrap" style="background: #E3F5EC; color: #16865D;">
                        <i data-lucide="check-circle-2"></i>
                    </div>
                </div>
                <div class="wm-stat-val text-success">{{ $totalAktif }}</div>
                <div class="small text-muted fw-semibold">Dapat Login & Kelola Warga</div>
            </div>
        </div>

        <!-- 3. Jumlah Akun RT Nonaktif -->
        <div class="col-xl-3 col-md-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Akun RT Nonaktif</span>
                    <div class="wm-stat-icon-wrap" style="background: #FEE2E2; color: #DC2626;">
                        <i data-lucide="user-x"></i>
                    </div>
                </div>
                <div class="wm-stat-val {{ $totalNonaktif > 0 ? 'text-danger' : 'text-muted' }}">{{ $totalNonaktif }}</div>
                <div class="small text-muted fw-semibold">Akses Terkunci Sementara</div>
            </div>
        </div>

        <!-- 4. Jumlah RT yang Belum Memiliki Akun -->
        <div class="col-xl-3 col-md-6">
            <div class="wm-stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="wm-stat-lbl">Belum Memiliki Akun</span>
                    <div class="wm-stat-icon-wrap" style="background: {{ $totalBelumAdaAkun > 0 ? '#FEF3C7' : '#F1F5F9' }}; color: {{ $totalBelumAdaAkun > 0 ? '#D97706' : '#64748B' }};">
                        <i data-lucide="user-minus"></i>
                    </div>
                </div>
                <div class="wm-stat-val {{ $totalBelumAdaAkun > 0 ? 'text-warning' : 'text-success' }}">{{ $totalBelumAdaAkun }}</div>
                <div class="small text-muted fw-semibold">
                    @if($totalBelumAdaAkun > 0)
                        Wilayah: RT {{ implode(', ', $missingRt) }}
                    @else
                        Seluruh RT Sudah Terkonfigurasi
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         FILTER PENCARIAN & FILTER WILAYAH RT / STATUS
         ====================================================================== --}}
    <div class="wm-card mb-4">
        <div class="wm-card-body p-3">
            <form method="GET" action="{{ route('rt.akun.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama Ketua RT, email login, atau WhatsApp..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="rt" class="form-select">
                        <option value="all">Semua Wilayah RT (001 - 008)</option>
                        @for($i = 1; $i <= 8; $i++)
                            @php $padded = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $padded }}" {{ $filterRt === $padded ? 'selected' : '' }}>
                                RT {{ $padded }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="all">Semua Status</option>
                        <option value="aktif" {{ $filterStatus === 'aktif' ? 'selected' : '' }}>Aktif Saja</option>
                        <option value="nonaktif" {{ $filterStatus === 'nonaktif' ? 'selected' : '' }}>Nonaktif Saja</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="wm-btn-primary w-100 justify-content-center">
                        <i data-lucide="filter" style="width: 15px; height: 15px;"></i> Filter
                    </button>
                    @if($search || ($filterRt && $filterRt !== 'all') || ($filterStatus && $filterStatus !== 'all'))
                        <a href="{{ route('rt.akun.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i data-lucide="rotate-ccw" style="width: 15px; height: 15px;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================================
         TABEL UTAMA AKUN RT (RESPONSIF DESKTOP & SMARTPHONE)
         ====================================================================== --}}
    <div class="wm-card mb-4">
        <div class="wm-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="wm-card-title mb-0">Daftar Akun Ketua RT (RW 013)</h5>
                <p class="text-muted small mb-0 mt-1">Daftar pengguna dengan otoritas khusus tingkat RT di lingkungan RW 013.</p>
            </div>
            <span class="badge bg-light text-primary border px-3 py-2">
                Menampilkan {{ count($rtAccounts) }} dari {{ $totalWilayahRt }} RT
            </span>
        </div>
        <div class="wm-card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 0.8rem; text-transform: uppercase;">
                            <th style="width: 90px;">Nomor RT</th>
                            <th style="width: 60px;">Foto</th>
                            <th>Nama Ketua RT</th>
                            <th>Email Login</th>
                            <th>Nomor WhatsApp</th>
                            <th>Status Akun</th>
                            <th>Terakhir Login</th>
                            <th class="text-end" style="min-width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rtAccounts as $acc)
                            <tr>
                                <!-- 1. Nomor RT -->
                                <td>
                                    <span class="badge" style="background: #1456A0; color: #fff; font-size: 0.85rem; border-radius: 999px; padding: 6px 12px; font-weight: 700;">
                                        {{ $acc->clean_rt_label }}
                                    </span>
                                </td>

                                <!-- 2. Foto Profil -->
                                <td>
                                    <img src="{{ $acc->profile_photo ? asset('storage/' . $acc->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($acc->name) . '&background=1456A0&color=FFFFFF&size=64' }}"
                                         alt="Foto {{ $acc->name }}" class="rounded-circle border shadow-sm" style="width: 38px; height: 38px; object-fit: cover;">
                                </td>

                                <!-- 3. Nama Ketua RT -->
                                <td>
                                    <div class="fw-bold text-dark">{{ $acc->name }}</div>
                                    <small class="text-muted">
                                        <i data-lucide="map-pin" style="width: 11px; height: 11px; color: var(--wm-green);"></i>
                                        {{ Str::limit($acc->address ?: "RT {$acc->clean_rt_number} / RW 013", 32) }}
                                    </small>
                                </td>

                                <!-- 4. Email Login -->
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <code class="text-primary fw-semibold px-2 py-1 rounded bg-light border small font-monospace">{{ $acc->email }}</code>
                                        <button type="button" class="btn btn-sm btn-light border p-1" onclick="copyEmail('{{ $acc->email }}')" title="Salin Email">
                                            <i data-lucide="copy" style="width: 12px; height: 12px;"></i>
                                        </button>
                                    </div>
                                </td>

                                <!-- 5. Nomor WhatsApp -->
                                <td>
                                    @if($acc->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $acc->phone) }}" target="_blank" class="text-decoration-none text-dark small fw-semibold">
                                            <i data-lucide="phone" style="width: 12px; height: 12px; color: var(--wm-green);"></i> {{ $acc->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <!-- 6. Status Akun -->
                                <td>
                                    @if($acc->is_active)
                                        <span class="badge" style="background: #E3F5EC; color: #0F5132; border: 1px solid #A7F3D0; font-size: 0.75rem; border-radius: 999px; padding: 5px 10px;">
                                            <i data-lucide="check-circle" style="width: 11px; height: 11px; display: inline-block; vertical-align: middle;"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge" style="background: #FEE2E2; color: #842029; border: 1px solid #FECACA; font-size: 0.75rem; border-radius: 999px; padding: 5px 10px;">
                                            <i data-lucide="x-circle" style="width: 11px; height: 11px; display: inline-block; vertical-align: middle;"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <!-- 7. Terakhir Login -->
                                <td>
                                    @if($acc->last_login_at)
                                        <div class="small fw-semibold text-dark">
                                            {{ \Carbon\Carbon::parse($acc->last_login_at)->translatedFormat('d M Y, H:i') }} WIB
                                        </div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($acc->last_login_at)->diffForHumans() }}</small>
                                    @else
                                        <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">Belum pernah login</span>
                                    @endif
                                </td>

                                <!-- 8. Aksi (Edit/Detail, Reset Password, Toggle Aktif) -->
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        {{-- Tombol Edit / Detail --}}
                                        <button type="button" class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem; padding: 4px 8px;"
                                                onclick="openEditModal({{ json_encode($acc) }})" title="Lihat Detail & Edit Akun">
                                            <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i> Edit
                                        </button>

                                        {{-- Tombol Reset Password --}}
                                        <button type="button" class="btn btn-sm btn-outline-warning" style="font-size: 0.75rem; padding: 4px 8px;"
                                                onclick="openResetModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ $acc->clean_rt_label }}', '{{ $acc->email }}')" title="Reset Password">
                                            <i data-lucide="key" style="width: 13px; height: 13px;"></i> Reset
                                        </button>

                                        {{-- Tombol Toggle Aktif / Nonaktif --}}
                                        <button type="button" class="btn btn-sm {{ $acc->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" style="font-size: 0.75rem; padding: 4px 8px;"
                                                onclick="openToggleModal({{ $acc->id }}, '{{ addslashes($acc->name) }}', '{{ $acc->clean_rt_label }}', {{ $acc->is_active ? 'true' : 'false' }})" title="{{ $acc->is_active ? 'Kunci / Nonaktifkan Akun' : 'Aktifkan Akun Kembali' }}">
                                            <i data-lucide="{{ $acc->is_active ? 'lock' : 'unlock' }}" style="width: 13px; height: 13px;"></i>
                                            {{ $acc->is_active ? 'Kunci' : 'Buka' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i data-lucide="user-x" style="width: 42px; height: 42px; margin-bottom: 8px; color: #94A3B8;"></i>
                                        <h6 class="fw-bold mb-1 text-dark">Tidak Ada Akun RT yang Cocok</h6>
                                        <p class="small mb-3">Tidak ditemukan akun Ketua RT berdasarkan kata kunci atau filter yang Anda pilih.</p>
                                        <a href="{{ route('rt.akun.index') }}" class="btn btn-sm btn-outline-primary">
                                            Reset Pencarian
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ======================================================================
         LOG RIWAYAT AKTIVITAS & AUDIT KEAMANAN AKUN RT
         ====================================================================== --}}
    <div class="row g-4">
        <!-- Kolom Log Aktivitas -->
        <div class="col-lg-7">
            <div class="wm-card h-100">
                <div class="wm-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="wm-card-title mb-0">
                            <i data-lucide="history" class="text-primary me-2" style="width: 18px; height: 18px;"></i>
                            Riwayat Aktivitas & Audit Akun RT
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Log tercatat otomatis: siapa yang membuat, mereset password, atau mengubah status akun.</p>
                    </div>
                </div>
                <div class="wm-card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivities as $act)
                            <div class="list-group-item p-3 d-flex align-items-start gap-3">
                                <div class="p-2 rounded-circle bg-light border text-primary mt-1" style="flex-shrink:0;">
                                    @if(str_contains($act->activity, 'RESET'))
                                        <i data-lucide="key" style="width: 16px; height: 16px; color: #D97706;"></i>
                                    @elseif(str_contains($act->activity, 'TOGGLE'))
                                        <i data-lucide="lock" style="width: 16px; height: 16px; color: #DC2626;"></i>
                                    @elseif(str_contains($act->activity, 'CREATE'))
                                        <i data-lucide="user-plus" style="width: 16px; height: 16px; color: #16865D;"></i>
                                    @else
                                        <i data-lucide="edit-3" style="width: 16px; height: 16px; color: #1456A0;"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-light text-dark border small">{{ $act->activity }}</span>
                                        <span class="small text-muted">{{ $act->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="small text-dark mb-1">{{ $act->description }}</p>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                                        <i data-lucide="globe" style="width: 10px; height: 10px;"></i> IP: {{ $act->ip_address ?: '127.0.0.1' }} &bull; {{ $act->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">
                                Belum ada aktivitas modifikasi akun RT tercatat pada sistem.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Prinsip Keamanan & Ketentuan Otorisasi -->
        <div class="col-lg-5">
            <div class="wm-card h-100" style="background: linear-gradient(135deg, #F8FAFC 0%, #FFFFFF 100%); border-left: 5px solid var(--wm-primary);">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="p-2 rounded bg-primary text-white">
                        <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-dark">Prinsip Keamanan Sistem</h5>
                </div>
                <p class="small text-muted mb-3">
                    Kebijakan otorisasi data hierarki RW 013 &bull; Kelurahan Machi:
                </p>
                <div class="list-group list-group-flush small">
                    <div class="list-group-item px-0 py-2 border-0 d-flex gap-2">
                        <i data-lucide="check" class="text-success mt-1" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                        <span><strong>Isolasi Wilayah Ketat:</strong> Ketua RT 001 hanya dapat melihat dan mengelola warga RT 001. Percobaan manipulasi URL ke RT lain otomatis ditolak dengan <code>403 Forbidden</code>.</span>
                    </div>
                    <div class="list-group-item px-0 py-2 border-0 d-flex gap-2">
                        <i data-lucide="check" class="text-success mt-1" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                        <span><strong>Enkripsi Password Satu Arah:</strong> Password di-hash menggunakan algoritma <code>bcrypt</code> Laravel. Sistem tidak pernah menampilkan atau menyimpan password dalam bentuk teks terbuka.</span>
                    </div>
                    <div class="list-group-item px-0 py-2 border-0 d-flex gap-2">
                        <i data-lucide="check" class="text-success mt-1" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                        <span><strong>Pencegahan Manipulasi Identitas:</strong> Field nomor RT dan RW dikunci saat pengeditan agar tidak terjadi perpindahan data wilayah tanpa izin.</span>
                    </div>
                    <div class="list-group-item px-0 py-2 border-0 d-flex gap-2">
                        <i data-lucide="check" class="text-success mt-1" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                        <span><strong>Integritas Data Warga:</strong> Menonaktifkan akun RT tidak akan menghapus data warga, kas RT, atau laporan bulanan yang telah diserahkan.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ======================================================================
     MODAL 1: TAMBAH AKUN RT BARU
     ====================================================================== --}}
<div class="modal fade" id="modalTambahAkun" tabindex="-1" aria-labelledby="modalTambahAkunLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('rt.akun.store') }}">
                @csrf
                <div class="modal-header text-white" style="background: var(--wm-primary);">
                    <h5 class="modal-title fw-bold" id="modalTambahAkunLabel">
                        <i data-lucide="user-plus" class="me-2" style="width:20px;height:20px;display:inline-block;vertical-align:middle;"></i>
                        Tambah Akun Ketua RT Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-4">
                        Lengkapi formulir berikut untuk mendaftarkan akun Ketua RT baru. Akun akan otomatis terhubung dengan hierarki RW 013 dan role <code>rt</code>.
                    </p>

                    <div class="row g-3">
                        <!-- Wilayah RT & RW -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor RT (001 - 008) <span class="text-danger">*</span></label>
                            <select name="rt_number" class="form-select" required>
                                <option value="" disabled selected>Pilih Wilayah RT...</option>
                                @for($i = 1; $i <= 8; $i++)
                                    @php $padded = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $padded }}">RT {{ $padded }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor RW (Terkunci)</label>
                            <input type="text" class="form-control bg-light" value="RW 013 (Kelurahan Machi)" readonly>
                        </div>

                        <!-- Nama Lengkap Ketua RT -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap Ketua RT <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Bpk. Bambang Suprayitno" required>
                        </div>

                        <!-- Email Login -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Login <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="Contoh: rt001@warkas.machi.id" required>
                            <small class="text-muted">Gunakan alamat email unik untuk autentikasi sistem.</small>
                        </div>

                        <!-- Nomor WhatsApp -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor WhatsApp / HP</label>
                            <input type="text" name="phone" class="form-control" placeholder="Contoh: 081234567890">
                        </div>

                        <!-- Nomor Rumah -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Rumah</label>
                            <input type="text" name="house_number" class="form-control" placeholder="Contoh: 012">
                        </div>

                        <!-- Alamat Pos / Sekretariat RT -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Rumah / Pos RT</label>
                            <input type="text" name="address" class="form-control" placeholder="Contoh: Jl. Gandaria No. 12, RT 001 / RW 013">
                        </div>

                        <hr class="my-3">

                        <!-- Password Awal & Konfirmasi -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password Awal <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                            <small class="text-muted">Password akan dienkripsi dengan <code>bcrypt</code>.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password di atas" required minlength="6">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="wm-btn-primary px-4">
                        <i data-lucide="check" style="width:16px;height:16px;"></i> Simpan & Buat Akun RT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================================================================
     MODAL 2: EDIT & DETAIL AKUN RT
     ====================================================================== --}}
<div class="modal fade" id="modalEditAkun" tabindex="-1" aria-labelledby="modalEditAkunLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form method="POST" id="formEditAkun" action="">
                @csrf
                @method('PUT')
                <div class="modal-header text-white" style="background: var(--wm-navy);">
                    <h5 class="modal-title fw-bold" id="modalEditAkunLabel">
                        <i data-lucide="user-cog" class="me-2" style="width:20px;height:20px;display:inline-block;vertical-align:middle;"></i>
                        Detail & Edit Profil Akun Ketua RT
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 rounded bg-light border mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="small text-muted text-uppercase fw-bold">Wilayah Otoritas</span>
                            <h5 class="fw-bold text-primary mb-0" id="editRtBadge">RT 001 - RW 013</h5>
                        </div>
                        <div>
                            <span class="badge bg-secondary" id="editStatusBadge">Status</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap Ketua RT <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Login <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor WhatsApp / HP</label>
                            <input type="text" name="phone" id="editPhone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Rumah</label>
                            <input type="text" name="house_number" id="editHouseNumber" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat Rumah / Pos RT</label>
                            <input type="text" name="address" id="editAddress" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top small text-muted">
                        <i data-lucide="lock" style="width:12px;height:12px;color:var(--wm-primary);"></i>
                        <em>Nomor RT dan RW dikunci secara permanen demi menjaga integritas isolasi data warga.</em>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="wm-btn-primary px-4">
                        <i data-lucide="check-circle" style="width:16px;height:16px;"></i> Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================================================================
     MODAL 3: RESET PASSWORD AKUN RT
     ====================================================================== --}}
<div class="modal fade" id="modalResetPassword" tabindex="-1" aria-labelledby="modalResetPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" id="formResetPassword" action="">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold" id="modalResetPasswordLabel">
                        <i data-lucide="key" class="me-2" style="width:20px;height:20px;display:inline-block;vertical-align:middle;"></i>
                        Reset Password Akun RT
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Sebagai Ketua RW atau Admin, Anda dapat mengatur ulang password akun Ketua RT jika lupa password atau terjadi pergantian pengurus.
                    </p>

                    <div class="p-3 rounded bg-light border mb-3">
                        <div class="small text-muted text-uppercase fw-bold" id="resetTargetRt">-</div>
                        <h6 class="fw-bold text-dark mb-1" id="resetTargetName">-</h6>
                        <code class="small text-primary font-monospace" id="resetTargetEmail">-</code>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="inputNewPassword" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="inputNewPasswordConfirm" class="form-control" placeholder="Ulangi password baru" required minlength="6">
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light border small">
                        <span class="text-muted">Kredensial Default Cepat:</span>
                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold" onclick="setDefaultPassword()">
                            Gunakan 'password123'
                        </button>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark px-4">
                        <i data-lucide="check" style="width:16px;height:16px;"></i> Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================================================================
     MODAL 4: KONFIRMASI AKTIFKAN / NONAKTIFKAN AKUN RT
     ====================================================================== --}}
<div class="modal fade" id="modalToggleStatus" tabindex="-1" aria-labelledby="modalToggleStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" id="formToggleStatus" action="">
                @csrf
                @method('PATCH')
                <div class="modal-header" id="toggleHeader">
                    <h5 class="modal-title fw-bold" id="modalToggleStatusLabel">Konfirmasi Status Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3" id="toggleIcon">
                        <!-- Icon diisi JavaScript -->
                    </div>
                    <h5 class="fw-bold text-dark mb-2" id="toggleQuestion">Apakah Anda yakin?</h5>
                    <p class="small text-muted mb-0" id="toggleExplanation">
                        Data warga dan riwayat pelaporan RT tidak akan terhapus.
                    </p>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn px-4 fw-bold" id="toggleSubmitBtn">
                        Ya, Lanjutkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================================================================
     JAVASCRIPT HELPER UNTUK MODAL & SALIN KREDENSIAL
     ====================================================================== --}}
<script>
    function copyEmail(email) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(email).then(() => {
                alert('Email berhasil disalin ke clipboard:\n' + email);
            });
        }
    }

    function openEditModal(account) {
        document.getElementById('editName').value = account.name || '';
        document.getElementById('editEmail').value = account.email || '';
        document.getElementById('editPhone').value = account.phone || '';
        document.getElementById('editHouseNumber').value = account.house_number || '';
        document.getElementById('editAddress').value = account.address || '';
        document.getElementById('editRtBadge').innerText = 'RT ' + (account.rt_number || '001') + ' - RW 013';
        
        const badge = document.getElementById('editStatusBadge');
        if (account.is_active) {
            badge.className = 'badge bg-success';
            badge.innerText = 'Akun Aktif';
        } else {
            badge.className = 'badge bg-danger';
            badge.innerText = 'Akun Nonaktif';
        }

        document.getElementById('formEditAkun').action = '/manajemen-akun-rt/' + account.id;

        const modal = new bootstrap.Modal(document.getElementById('modalEditAkun'));
        modal.show();
    }

    function openResetModal(userId, name, rtLabel, email) {
        document.getElementById('resetTargetName').innerText = name;
        document.getElementById('resetTargetRt').innerText = rtLabel + ' - RW 013';
        document.getElementById('resetTargetEmail').innerText = email;
        document.getElementById('inputNewPassword').value = '';
        document.getElementById('inputNewPasswordConfirm').value = '';
        document.getElementById('formResetPassword').action = '/manajemen-akun-rt/' + userId + '/reset-password';
        
        const modal = new bootstrap.Modal(document.getElementById('modalResetPassword'));
        modal.show();
    }

    function setDefaultPassword() {
        document.getElementById('inputNewPassword').value = 'password123';
        document.getElementById('inputNewPasswordConfirm').value = 'password123';
    }

    function openToggleModal(userId, name, rtLabel, isActive) {
        const header = document.getElementById('toggleHeader');
        const icon = document.getElementById('toggleIcon');
        const question = document.getElementById('toggleQuestion');
        const explanation = document.getElementById('toggleExplanation');
        const submitBtn = document.getElementById('toggleSubmitBtn');
        const form = document.getElementById('formToggleStatus');

        form.action = '/manajemen-akun-rt/' + userId + '/toggle-active';

        if (isActive) {
            header.className = 'modal-header bg-danger text-white';
            icon.innerHTML = '<i data-lucide="lock" style="width:48px;height:48px;color:#DC2626;"></i>';
            question.innerText = 'Kunci / Nonaktifkan Akun ' + rtLabel + '?';
            explanation.innerText = 'Ketua RT ' + name + ' tidak akan dapat login ke sistem Warkas Machi sampai diaktifkan kembali. Seluruh data warga dan riwayat kas tetap aman tersimpan.';
            submitBtn.className = 'btn btn-danger px-4 fw-bold';
            submitBtn.innerText = 'Ya, Nonaktifkan Akun';
        } else {
            header.className = 'modal-header bg-success text-white';
            icon.innerHTML = '<i data-lucide="unlock" style="width:48px;height:48px;color:#16865D;"></i>';
            question.innerText = 'Aktifkan Kembali Akun ' + rtLabel + '?';
            explanation.innerText = 'Akun Ketua RT ' + name + ' akan dapat login kembali untuk mengelola administrasi warga di wilayahnya.';
            submitBtn.className = 'btn btn-success px-4 fw-bold';
            submitBtn.innerText = 'Ya, Aktifkan Akun';
        }

        if (window.lucide) {
            lucide.createIcons();
        }

        const modal = new bootstrap.Modal(document.getElementById('modalToggleStatus'));
        modal.show();
    }
</script>
@endsection
