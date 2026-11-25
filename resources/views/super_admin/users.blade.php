@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@push('styles')
<style>
    /* Scoped Styling Manajemen Pengguna Super Admin */
    .sa-users-header-card {
        border-radius: 18px !important;
        background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%) !important;
        padding: 24px 28px !important;
        color: #FFFFFF !important;
        border: none !important;
        box-shadow: 0 4px 18px rgba(10, 37, 64, 0.12) !important;
    }
    .sa-stat-card {
        padding: 20px 22px !important;
        border-radius: 16px !important;
        background-color: var(--wm-card, #FFFFFF) !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
        box-shadow: 0 2px 10px rgba(23, 43, 77, 0.05) !important;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .sa-table-card {
        border-radius: 16px !important;
        background-color: var(--wm-card, #FFFFFF) !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
        box-shadow: 0 2px 10px rgba(23, 43, 77, 0.05) !important;
        overflow: hidden;
    }
    .sa-filter-box {
        padding: 18px 20px !important;
        background-color: var(--wm-light-blue, #F8FAFD) !important;
        border-radius: 14px !important;
        border: 1px solid var(--wm-border, #E2E8F0) !important;
    }

    /* Dark Mode Overrides */
    [data-theme="dark"] .sa-stat-card,
    body.dark-mode .sa-stat-card,
    [data-theme="dark"] .sa-table-card,
    body.dark-mode .sa-table-card {
        background-color: #131F35 !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
    }
    [data-theme="dark"] .sa-filter-box,
    body.dark-mode .sa-filter-box {
        background-color: #0F1A2E !important;
        border-color: #24344F !important;
        color: #F1F5F9 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-md-4 py-2">
    {{-- Header Banner --}}
    <div class="sa-users-header-card mb-4" style="background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%) !important; color: #FFFFFF !important; border-radius: 18px; padding: 24px 28px; box-shadow: 0 4px 18px rgba(10, 37, 64, 0.12); border: none;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                    <span class="badge rounded-pill px-2.5 py-1 text-white fw-bold" style="background: rgba(255,255,255,0.2); font-size: 0.72rem;">
                        <i data-lucide="shield" style="width: 12px; height: 12px; vertical-align: middle;"></i> KONTROL PENGGUNA
                    </span>
                    <span class="badge rounded-pill bg-success text-white px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                        Total {{ number_format($userStats['total_user'], 0, ',', '.') }} Akun
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-1 tracking-tight">Manajemen Pengguna & Hak Akses Role</h3>
                <p class="text-white-50 mb-0 small" style="max-width: 650px;">
                    Kelola seluruh akun pengguna WarkasMachi, atur tingkatan role (Super Admin, RW, RT, Warga), perbarui penugasan wilayah RT, dan lakukan reset password darurat.
                </p>
            </div>
            <div>
                <a href="{{ route('dashboard.super-admin') }}" class="btn btn-light rounded-pill px-3 py-2 fw-bold text-primary d-inline-flex align-items-center gap-1.5 shadow-sm" style="font-size: 0.82rem;">
                    <i data-lucide="layout-dashboard" style="width: 15px; height: 15px;"></i> Kembali ke Control Center
                </a>
            </div>
        </div>
    </div>

    {{-- Ringkasan Pengguna Per Role --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="sa-stat-card" style="border-top: 3.5px solid #1456A0;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold" style="font-size: 0.72rem; text-transform: uppercase;">Total Pengguna</span>
                    <div class="p-1.5 rounded-3 bg-light text-primary"><i data-lucide="users" style="width: 16px; height: 16px;"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0" style="font-size: 1.5rem;">{{ number_format($userStats['total_user'], 0, ',', '.') }}</h3>
                <small class="text-muted" style="font-size: 0.74rem;">Seluruh tingkatan role</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sa-stat-card" style="border-top: 3.5px solid #2878D0;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold" style="font-size: 0.72rem; text-transform: uppercase;">Pengurus RW & Admin</span>
                    <div class="p-1.5 rounded-3 bg-light text-primary"><i data-lucide="landmark" style="width: 16px; height: 16px;"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0" style="font-size: 1.5rem;">{{ $userStats['rw'] }}</h3>
                <small class="text-muted" style="font-size: 0.74rem;">Ketua RW, Admin, & Bendahara</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sa-stat-card" style="border-top: 3.5px solid #16865D;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold" style="font-size: 0.72rem; text-transform: uppercase;">Ketua Rukun Tetangga</span>
                    <div class="p-1.5 rounded-3 bg-light text-success"><i data-lucide="building-2" style="width: 16px; height: 16px;"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0" style="font-size: 1.5rem;">{{ $userStats['rt'] }}</h3>
                <small class="text-muted" style="font-size: 0.74rem;">Ketua RT 001 s.d RT 008</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sa-stat-card" style="border-top: 3.5px solid #0284C7;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold" style="font-size: 0.72rem; text-transform: uppercase;">Akun Warga</span>
                    <div class="p-1.5 rounded-3 bg-light text-info"><i data-lucide="user-check" style="width: 16px; height: 16px;"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0" style="font-size: 1.5rem;">{{ number_format($userStats['warga'], 0, ',', '.') }}</h3>
                <small class="text-muted" style="font-size: 0.74rem;">Warga terdaftar di 8 RT</small>
            </div>
        </div>
    </div>

    {{-- Filter & Search Box --}}
    <div class="sa-table-card mb-4">
        <div class="p-3.5 bg-white border-bottom">
            <form method="GET" action="{{ route('super-admin.users.index') }}">
                <div class="row g-2 align-items-center">
                    {{-- Search Input --}}
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i data-lucide="search" style="width: 14px; height: 14px;"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Cari nama, email, NIK, No KK..." value="{{ request('search') }}" style="font-size: 0.82rem;">
                        </div>
                    </div>

                    {{-- Filter Role --}}
                    <div class="col-6 col-md-3">
                        <select name="role" class="form-select form-select-sm" style="font-size: 0.82rem;">
                            <option value="all" {{ request('role') === 'all' || !request('role') ? 'selected' : '' }}>-- Semua Role --</option>
                            <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="rw" {{ request('role') === 'rw' ? 'selected' : '' }}>Pengurus RW / Admin</option>
                            <option value="rt" {{ request('role') === 'rt' ? 'selected' : '' }}>Ketua RT (001-008)</option>
                            <option value="warga" {{ request('role') === 'warga' ? 'selected' : '' }}>Warga</option>
                        </select>
                    </div>

                    {{-- Filter RT --}}
                    <div class="col-6 col-md-3">
                        <select name="rt" class="form-select form-select-sm" style="font-size: 0.82rem;">
                            <option value="all" {{ request('rt') === 'all' || !request('rt') ? 'selected' : '' }}>-- Semua Wilayah RT --</option>
                            @foreach($rtList as $rt)
                                <option value="{{ $rt }}" {{ request('rt') == $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-12 col-md-2 d-flex gap-1.5">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1" style="background: #1456A0; border-color: #1456A0; font-size: 0.8rem;">
                            <i data-lucide="filter" style="width: 13px; height: 13px;"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'role', 'rt']))
                            <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill d-inline-flex align-items-center justify-content-center px-2.5" title="Reset Filter">
                                <i data-lucide="rotate-ccw" style="width: 13px; height: 13px;"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel Daftar Pengguna --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.83rem;">
                <thead class="table-light text-muted" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.4px;">
                    <tr>
                        <th class="ps-4 py-3" style="width: 28%;">Nama & Akun</th>
                        <th class="py-3" style="width: 16%;">Role Sistem</th>
                        <th class="py-3" style="width: 12%;">Wilayah</th>
                        <th class="py-3" style="width: 20%;">Kontak & Identitas</th>
                        <th class="py-3 text-center" style="width: 10%;">Status</th>
                        <th class="pe-4 py-3 text-end" style="width: 14%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    <img src="{{ $u->profile_photo ? asset('storage/' . $u->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=1456A0&color=FFFFFF' }}"
                                         alt="Avatar" class="rounded-circle border flex-shrink-0" style="width: 36px; height: 36px; object-fit: cover;">
                                    <div class="overflow-hidden">
                                        <strong class="text-dark d-block text-truncate" style="font-size: 0.86rem;">
                                            {{ $u->name }}
                                            @if($u->id === Auth::id())
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill ms-1" style="font-size: 0.65rem;">Anda</span>
                                            @endif
                                        </strong>
                                        <small class="text-muted d-block text-truncate" style="font-size: 0.75rem;">{{ $u->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($u->role === 'super_admin')
                                    <span class="badge rounded-pill text-white fw-bold px-2.5 py-1" style="background: #0A2540; font-size: 0.72rem;">
                                        <i data-lucide="shield" style="width: 11px; height: 11px; vertical-align: middle;"></i> Super Admin
                                    </span>
                                @elseif(in_array($u->role, ['admin', 'rw', 'bendahara']))
                                    <span class="badge rounded-pill text-white fw-bold px-2.5 py-1" style="background: #1456A0; font-size: 0.72rem;">
                                        <i data-lucide="landmark" style="width: 11px; height: 11px; vertical-align: middle;"></i> {{ $u->role_badge_label }}
                                    </span>
                                @elseif($u->role === 'rt')
                                    <span class="badge rounded-pill text-white fw-bold px-2.5 py-1" style="background: #16865D; font-size: 0.72rem;">
                                        <i data-lucide="building-2" style="width: 11px; height: 11px; vertical-align: middle;"></i> Ketua RT {{ $u->clean_rt_number ? str_pad($u->clean_rt_number, 3, '0', STR_PAD_LEFT) : $u->rt_number }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                        <i data-lucide="user" style="width: 11px; height: 11px; vertical-align: middle;"></i> Warga
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($u->role === 'super_admin' || $u->role === 'rw' || $u->role === 'admin' || $u->role === 'bendahara')
                                    <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">RW 013 (Wilayah)</span>
                                @else
                                    <span class="badge bg-light text-dark border px-2 py-0.5 rounded-pill fw-semibold" style="font-size: 0.72rem;">
                                        RT {{ str_pad(preg_replace('/[^0-9]/', '', (string)$u->rt_number) ?: '001', 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-muted" style="font-size: 0.76rem;">
                                    <div><i data-lucide="phone" style="width: 12px; height: 12px; vertical-align: middle;"></i> {{ $u->phone ?: '-' }}</div>
                                    @if($u->nik)
                                        <div><small class="text-secondary">NIK: {{ substr($u->nik, 0, 6) }}******</small></div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                @if($u->is_active ?? true)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Aktif</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Nonaktif</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    {{-- Tombol Edit User --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $u->id }}" title="Edit Pengguna">
                                        <i data-lucide="edit" style="width: 13px; height: 13px;"></i> Edit
                                    </button>

                                    {{-- Tombol Reset Password --}}
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 text-dark" data-bs-toggle="modal" data-bs-target="#modalResetPassword{{ $u->id }}" title="Reset Password">
                                        <i data-lucide="key" style="width: 13px; height: 13px;"></i>
                                    </button>

                                    {{-- Tombol Simulasi Peran Cepat (Hanya untuk akun lain, bukan diri sendiri) --}}
                                    @if($u->id !== Auth::id())
                                        <form method="POST" action="{{ route('switch.role') }}" class="m-0 d-inline">
                                            @csrf
                                            <input type="hidden" name="target_role" value="{{ $u->role === 'admin' ? 'rw' : ($u->role === 'rt' ? 'rt' : ($u->role === 'warga' ? 'warga' : 'rw')) }}">
                                            <input type="hidden" name="target_user_id" value="{{ $u->id }}">
                                            <input type="hidden" name="rt_number" value="{{ $u->rt_number }}">
                                            <input type="hidden" name="mode" value="view_only">
                                            <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-primary" title="Simulasi Masuk sebagai {{ $u->name }} (Lihat Saja)">
                                                <i data-lucide="shuffle" style="width: 13px; height: 13px;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                {{-- MODAL EDIT USER --}}
                                <div class="modal fade" id="modalEditUser{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg text-start" style="border-radius: 18px; overflow: hidden;">
                                            <div class="modal-header p-3.5 text-white" style="background: linear-gradient(135deg, #1456A0 0%, #2878D0 100%);">
                                                <h6 class="modal-title fw-bold mb-0">Edit Data Pengguna</h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ route('super-admin.users.update', $u->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4 bg-light">
                                                    @if($u->id === Auth::id())
                                                        <div class="alert alert-info py-2 px-3 small mb-3 rounded-3" style="font-size: 0.76rem;">
                                                            <i data-lucide="shield-alert" style="width: 14px; height: 14px; vertical-align: middle;"></i>
                                                            Anda sedang mengedit akun Super Admin Anda sendiri. Role tidak dapat diubah agar tidak kehilangan akses root sistem.
                                                        </div>
                                                    @endif

                                                    <div class="mb-3">
                                                        <label class="form-label text-dark fw-bold small mb-1">Nama Lengkap:</label>
                                                        <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $u->name) }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label text-dark fw-bold small mb-1">Alamat Email:</label>
                                                        <input type="email" name="email" class="form-control rounded-3" value="{{ old('email', $u->email) }}" required>
                                                    </div>

                                                    <div class="row g-2 mb-3">
                                                        <div class="col-6">
                                                            <label class="form-label text-dark fw-bold small mb-1">Role / Peran:</label>
                                                            <select name="role" class="form-select rounded-3" {{ $u->id === Auth::id() ? 'disabled' : '' }} required>
                                                                <option value="warga" {{ $u->role === 'warga' ? 'selected' : '' }}>Warga</option>
                                                                <option value="rt" {{ $u->role === 'rt' ? 'selected' : '' }}>Ketua RT</option>
                                                                <option value="rw" {{ in_array($u->role, ['rw', 'admin']) ? 'selected' : '' }}>Pengurus RW / Admin</option>
                                                                <option value="bendahara" {{ $u->role === 'bendahara' ? 'selected' : '' }}>Bendahara RW</option>
                                                                <option value="super_admin" {{ $u->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                                            </select>
                                                            @if($u->id === Auth::id())
                                                                <input type="hidden" name="role" value="super_admin">
                                                            @endif
                                                        </div>

                                                        <div class="col-6">
                                                            <label class="form-label text-dark fw-bold small mb-1">Penugasan RT:</label>
                                                            <select name="rt_number" class="form-select rounded-3">
                                                                <option value="000" {{ $u->rt_number === '000' || in_array($u->role, ['super_admin', 'rw', 'admin', 'bendahara']) ? 'selected' : '' }}>-- Seluruh RW (000) --</option>
                                                                @foreach($rtList as $rt)
                                                                    <option value="{{ $rt }}" {{ str_pad(preg_replace('/[^0-9]/', '', (string)$u->rt_number), 3, '0', STR_PAD_LEFT) === $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label text-dark fw-bold small mb-1">No. Handphone / WhatsApp:</label>
                                                        <input type="text" name="phone" class="form-control rounded-3" value="{{ old('phone', $u->phone) }}">
                                                    </div>

                                                    <div class="form-check form-switch pt-1">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveSwitch{{ $u->id }}" {{ $u->is_active ?? true ? 'checked' : '' }} {{ $u->id === Auth::id() ? 'disabled' : '' }}>
                                                        <label class="form-check-label small fw-semibold text-dark" for="isActiveSwitch{{ $u->id }}">Status Akun Aktif</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer p-3 bg-white border-top">
                                                    <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 small" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-1.5 fw-bold small" style="background: #1456A0; border-color: #1456A0;">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- MODAL RESET PASSWORD --}}
                                <div class="modal fade" id="modalResetPassword{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg text-start" style="border-radius: 18px; overflow: hidden;">
                                            <div class="modal-header p-3.5 text-white" style="background: linear-gradient(135deg, #0A2540 0%, #1456A0 100%);">
                                                <h6 class="modal-title fw-bold mb-0">Reset Password Darurat</h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ route('super-admin.users.reset-password', $u->id) }}">
                                                @csrf
                                                <div class="modal-body p-4 bg-light">
                                                    <div class="p-2.5 rounded-3 bg-white border mb-3">
                                                        <small class="text-muted d-block" style="font-size: 0.72rem;">Pengguna Target:</small>
                                                        <strong class="text-dark small">{{ $u->name }}</strong> &bull; <span class="text-muted small">{{ $u->email }}</span>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label text-dark fw-bold small mb-1">Password Baru (Min 8 Karakter):</label>
                                                        <input type="password" name="password" class="form-control rounded-3" placeholder="Minimal 8 karakter..." required minlength="8">
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label text-dark fw-bold small mb-1">Konfirmasi Password Baru:</label>
                                                        <input type="password" name="password_confirmation" class="form-control rounded-3" placeholder="Ulangi password baru..." required minlength="8">
                                                    </div>
                                                </div>
                                                <div class="modal-footer p-3 bg-white border-top">
                                                    <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 small" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning rounded-pill px-4 py-1.5 fw-bold small text-dark">Reset Password Sekarang</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i data-lucide="user-x" style="width: 32px; height: 32px; color: #94a3b8;" class="mb-2"></i>
                                <div>Tidak ditemukan pengguna yang sesuai dengan filter atau kata kunci pencarian.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if($users->hasPages())
            <div class="p-3 px-4 bg-white border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                <small class="text-muted" style="font-size: 0.76rem;">
                    Menampilkan {{ $users->firstItem() ?: 0 }} s.d {{ $users->lastItem() ?: 0 }} dari total {{ $users->total() }} akun
                </small>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
