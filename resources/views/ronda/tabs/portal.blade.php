{{-- ======================================================================
     TAB 4: PORTAL GERBANG & IOT MULTI-MODE RW 013
     ====================================================================== --}}

@if($isPengurus)
    {{-- 1. DEVICE HEADER & STATUS CARD --}}
    <div class="wm-card-box mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle shadow-sm" style="background: var(--wm-light-blue); color: var(--wm-primary);">
                    <i data-lucide="cpu" style="width: 28px; height: 28px;"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h5 class="fw-bold mb-0 text-dark" id="deviceCodeHeader">{{ $iotDevice->name }}</h5>
                        <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1" style="font-size: 0.75rem;">
                            ID: <strong>{{ $iotDevice->device_code }}</strong>
                        </span>
                        <span id="deviceOnlineBadge" class="portal-live-badge {{ $iotDevice->isOnline() ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                            <span class="cctv-pulse-dot" style="background: #FFF; width: 6px; height: 6px;"></span>
                            <span id="deviceOnlineText">{{ $iotDevice->isOnline() ? 'Perangkat Online' : 'Perangkat Offline' }}</span>
                        </span>
                    </div>
                    <small class="text-muted d-block mt-0.5" id="deviceHeartbeatText">
                        Terakhir aktif: {{ $iotDevice->last_seen_at ? $iotDevice->last_seen_at->diffForHumans() : 'Belum pernah terhubung' }} &bull; Lokasi: Pos Keamanan Gerbang Utama RW 013
                    </small>
                </div>
            </div>

            {{-- Mode Aktif Badge --}}
            <div>
                <div class="text-muted small mb-1 fw-semibold text-end d-none d-md-block">Mode Reader Aktif:</div>
                <div id="activeModeBadge">
                    @if($iotDevice->mode === 'portal')
                        <span class="badge px-3 py-2 fs-6 rounded-pill text-white shadow-sm" style="background: #1456A0;">
                            <i data-lucide="shield-check" style="width: 16px; height: 16px; display: inline-block; vertical-align: -2px;" class="me-1"></i>
                            Portal Gerbang Masuk
                        </span>
                    @elseif($iotDevice->mode === 'ronda')
                        <span class="badge px-3 py-2 fs-6 rounded-pill text-white shadow-sm" style="background: #7C3AED;">
                            <i data-lucide="moon" style="width: 16px; height: 16px; display: inline-block; vertical-align: -2px;" class="me-1"></i>
                            Absensi Ronda Malam
                        </span>
                    @elseif($iotDevice->mode === 'bank_sampah')
                        <span class="badge px-3 py-2 fs-6 rounded-pill text-white shadow-sm" style="background: #16865D;">
                            <i data-lucide="recycle" style="width: 16px; height: 16px; display: inline-block; vertical-align: -2px;" class="me-1"></i>
                            Identifikasi Bank Sampah
                        </span>
                    @elseif($iotDevice->mode === 'register')
                        <span class="badge px-3 py-2 fs-6 rounded-pill text-white shadow-sm" style="background: #D97706;">
                            <i data-lucide="scan" style="width: 16px; height: 16px; display: inline-block; vertical-align: -2px;" class="me-1"></i>
                            Rekam e-KTP / Kartu RFID
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($iotDevice->mode === 'register' && $iotDevice->targetUser)
            <div class="alert alert-warning border-0 rounded-3 mt-3 mb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="cctv-pulse-dot" style="background: #D97706;"></span>
                    <div>
                        <strong>Menunggu Tap e-KTP / Kartu Baru:</strong> Tempelkan fisik e-KTP atau kartu RFID ke reader ESP32 untuk warga <strong>{{ $iotDevice->targetUser->name }}</strong> (RT {{ $iotDevice->targetUser->clean_rt_number ?? $iotDevice->targetUser->rt_number }}).
                    </div>
                </div>
                <form action="{{ route('ronda.portal.set-mode') }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="mode" value="portal">
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                        Batalkan Mode Pindai
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- 2. PANEL KONTROL MODE PEMBACA (4 TOMBOL INSTAN) --}}
    <div class="wm-card-box mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i data-lucide="sliders" style="width: 18px; height: 18px; color: var(--wm-primary);"></i>
                Peralihan Mode Pembaca ESP32
            </h6>
            <small class="text-muted">1 Perangkat untuk seluruh integrasi RFID RW 013</small>
        </div>

        <div class="row g-2">
            <div class="col-6 col-md-3">
                <form action="{{ route('ronda.portal.set-mode') }}" method="POST">
                    @csrf
                    <input type="hidden" name="mode" value="portal">
                    <button type="submit" class="btn w-100 text-start p-2.5 p-md-3 rounded-3 border transition-all {{ $iotDevice->mode === 'portal' ? 'bg-primary text-white shadow-sm' : 'bg-light text-dark' }}">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                            @if($iotDevice->mode === 'portal')
                                <span class="badge bg-white text-primary rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Aktif</span>
                            @endif
                        </div>
                        <div class="fw-bold" style="font-size: 0.88rem;">1. Portal Gerbang</div>
                        <small class="{{ $iotDevice->mode === 'portal' ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.72rem; line-height: 1.2; display: block;">Buka palang untuk warga terdaftar</small>
                    </button>
                </form>
            </div>

            <div class="col-6 col-md-3">
                <form action="{{ route('ronda.portal.set-mode') }}" method="POST">
                    @csrf
                    <input type="hidden" name="mode" value="ronda">
                    <button type="submit" class="btn w-100 text-start p-2.5 p-md-3 rounded-3 border transition-all {{ $iotDevice->mode === 'ronda' ? 'text-white shadow-sm' : 'bg-light text-dark' }}" style="{{ $iotDevice->mode === 'ronda' ? 'background: #7C3AED; border-color: #7C3AED;' : '' }}">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <i data-lucide="moon" style="width: 20px; height: 20px;"></i>
                            @if($iotDevice->mode === 'ronda')
                                <span class="badge bg-white text-dark rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Aktif</span>
                            @endif
                        </div>
                        <div class="fw-bold" style="font-size: 0.88rem;">2. Absensi Ronda</div>
                        <small class="{{ $iotDevice->mode === 'ronda' ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.72rem; line-height: 1.2; display: block;">Catat kehadiran jaga pos malam</small>
                    </button>
                </form>
            </div>

            <div class="col-6 col-md-3">
                <form action="{{ route('ronda.portal.set-mode') }}" method="POST">
                    @csrf
                    <input type="hidden" name="mode" value="bank_sampah">
                    <button type="submit" class="btn w-100 text-start p-2.5 p-md-3 rounded-3 border transition-all {{ $iotDevice->mode === 'bank_sampah' ? 'text-white shadow-sm' : 'bg-light text-dark' }}" style="{{ $iotDevice->mode === 'bank_sampah' ? 'background: #16865D; border-color: #16865D;' : '' }}">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <i data-lucide="recycle" style="width: 20px; height: 20px;"></i>
                            @if($iotDevice->mode === 'bank_sampah')
                                <span class="badge bg-white text-dark rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Aktif</span>
                            @endif
                        </div>
                        <div class="fw-bold" style="font-size: 0.88rem;">3. Bank Sampah</div>
                        <small class="{{ $iotDevice->mode === 'bank_sampah' ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.72rem; line-height: 1.2; display: block;">Identifikasi saldo nasabah warga</small>
                    </button>
                </form>
            </div>

            <div class="col-6 col-md-3">
                <button type="button" class="btn w-100 text-start p-2.5 p-md-3 rounded-3 border transition-all {{ $iotDevice->mode === 'register' ? 'text-white shadow-sm' : 'bg-light text-dark' }}" style="{{ $iotDevice->mode === 'register' ? 'background: #D97706; border-color: #D97706;' : '' }}" data-bs-toggle="collapse" data-bs-target="#collapseRegisterCard" aria-expanded="{{ $iotDevice->mode === 'register' ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <i data-lucide="scan" style="width: 20px; height: 20px;"></i>
                        @if($iotDevice->mode === 'register')
                            <span class="badge bg-white text-dark rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Aktif</span>
                        @else
                            <i data-lucide="chevron-down" style="width: 14px; height: 14px;"></i>
                        @endif
                    </div>
                    <div class="fw-bold" style="font-size: 0.88rem;">4. Rekam e-KTP / RFID</div>
                    <small class="{{ $iotDevice->mode === 'register' ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.72rem; line-height: 1.2; display: block;">Rekam e-KTP atau kartu warga</small>
                </button>
            </div>
        </div>

        {{-- COLLAPSE REGISTRASI KARTU BARU --}}
        <div class="collapse {{ $iotDevice->mode === 'register' ? 'show' : '' }} mt-3 pt-3 border-top" id="collapseRegisterCard">
            <!-- Disclaimer Privasi Resmi Kemendagri -->
            <div class="alert alert-info border-0 rounded-3 mb-3 p-3 d-flex gap-2.5">
                <i data-lucide="shield-check" class="text-primary flex-shrink-0" style="width: 20px; height: 20px; margin-top: 2px;"></i>
                <div class="small text-dark">
                    <strong>Kepatuhan Privasi e-KTP:</strong> Sistem hanya membaca <em>UID chip fisik</em> publik kartu (4-byte atau 7-byte). Sistem sama sekali <u>tidak</u> membaca atau menyimpan data terenkripsi / biometrik Kemendagri.
                </div>
            </div>

            <div class="row g-3">
                {{-- Opsi A: Mode Scan Kartu Otomatis --}}
                <div class="col-12 col-md-6 border-end">
                    <h6 class="fw-bold text-dark mb-2">
                        <i data-lucide="radio" style="width: 16px; height: 16px; color: var(--wm-primary);"></i>
                        Opsi A: Pindai Otomatis via Reader ESP32
                    </h6>
                    <p class="text-muted small mb-3">Pilih nama warga, lalu klik tombol untuk mengaktifkan standby reader menunggu tap e-KTP fisik atau kartu RFID.</p>

                    <form action="{{ route('ronda.portal.set-mode') }}" method="POST">
                        @csrf
                        <input type="hidden" name="mode" value="register">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Pilih Pengguna / Warga Target:</label>
                            <select name="target_user_id" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Pengguna (Super Admin / RW / RT / Warga) --</option>
                                @foreach($wargaRfidList as $w)
                                    @php
                                        $roleTag = match($w->role) {
                                            'super_admin' => 'Super Admin',
                                            'rw', 'admin' => 'Ketua RW',
                                            'bendahara' => 'Bendahara RW',
                                            'rt' => 'Ketua RT ' . ($w->clean_rt_number ? str_pad($w->clean_rt_number, 3, '0', STR_PAD_LEFT) : $w->rt_number),
                                            default => 'Warga RT ' . ($w->clean_rt_number ? str_pad($w->clean_rt_number, 3, '0', STR_PAD_LEFT) : $w->rt_number)
                                        };
                                    @endphp
                                    <option value="{{ $w->id }}" {{ $iotDevice->target_user_id == $w->id ? 'selected' : '' }}>
                                        [{{ $roleTag }}] {{ $w->name }} - {{ $w->rfid_uid ? 'Sudah Ada UID (' . $w->masked_rfid_uid . ')' : 'Belum Ada e-KTP/Kartu' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm">
                            <i data-lucide="play" style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;"></i>
                            Aktifkan Mode Pindai Reader
                        </button>
                    </form>
                </div>

                {{-- Opsi B: Input Manual UID --}}
                <div class="col-12 col-md-6">
                    <h6 class="fw-bold text-dark mb-2">
                        <i data-lucide="keyboard" style="width: 16px; height: 16px; color: var(--wm-secondary);"></i>
                        Opsi B: Input UID Manual (4-byte / 7-byte)
                    </h6>
                    <p class="text-muted small mb-3">Jika UID chip e-KTP (14 hex) atau kartu RFID (8 hex) sudah dicatat dari serial monitor, Anda dapat memasukkannya langsung.</p>

                    <form action="{{ route('ronda.portal.manual-register') }}" method="POST">
                        @csrf
                        <div class="row g-2 mb-2">
                            <div class="col-7">
                                <select name="user_id" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Pengguna --</option>
                                    @foreach($wargaRfidList as $w)
                                        @php
                                            $roleTag = match($w->role) {
                                                'super_admin' => 'Super Admin',
                                                'rw', 'admin' => 'Ketua RW',
                                                'bendahara' => 'Bendahara RW',
                                                'rt' => 'Ketua RT ' . ($w->clean_rt_number ? str_pad($w->clean_rt_number, 3, '0', STR_PAD_LEFT) : $w->rt_number),
                                                default => 'Warga RT ' . ($w->clean_rt_number ? str_pad($w->clean_rt_number, 3, '0', STR_PAD_LEFT) : $w->rt_number)
                                            };
                                        @endphp
                                        <option value="{{ $w->id }}">
                                            [{{ $roleTag }}] {{ $w->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5">
                                <input type="text" name="rfid_uid" class="form-control form-select-sm font-monospace text-uppercase" placeholder="Contoh: 04A1B2C3D4E5F6" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                            <i data-lucide="save" style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;"></i>
                            Simpan UID Manual
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- KHUSUS SUPER ADMIN: PENGATURAN TITIK GPS GERBANG & RADIUS AKSES --}}
    @if(Auth::user()->isSuperAdmin())
        <div class="wm-card-box mb-4" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%); border-left: 4px solid #1456A0;">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-3 text-white" style="background: #1456A0;">
                        <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Konfigurasi Titik GPS Gerbang &amp; Radius Akses HP</h6>
                        <small class="text-muted">Khusus Super Admin: Menentukan koordinat fisik palang gerbang dan radius maksimal pembukaan via GPS HP</small>
                    </div>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold">
                    Super Admin Only
                </span>
            </div>

            <form action="{{ route('ronda.portal.update-gps') }}" method="POST">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i data-lucide="crosshair" style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;"></i>
                            Latitude Gerbang
                        </label>
                        <input type="number" step="0.0000001" name="gate_latitude" id="inputGateLat" class="form-control font-monospace" placeholder="-6.9147440" value="{{ old('gate_latitude', $iotDevice->gate_latitude) }}" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i data-lucide="compass" style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;"></i>
                            Longitude Gerbang
                        </label>
                        <input type="number" step="0.0000001" name="gate_longitude" id="inputGateLng" class="form-control font-monospace" placeholder="107.6098100" value="{{ old('gate_longitude', $iotDevice->gate_longitude) }}" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i data-lucide="radar" style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;"></i>
                            Radius Maksimal (Meter)
                        </label>
                        <div class="input-group">
                            <input type="number" min="3" max="500" name="gate_radius_meters" id="inputGateRadius" class="form-control font-monospace" placeholder="15" value="{{ old('gate_radius_meters', $iotDevice->gate_radius_meters ?? 15) }}" required>
                            <span class="input-group-text bg-light text-muted small">Meter</span>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill btn-sm px-3 fw-semibold" id="btnGetAdminLocation">
                            <i data-lucide="locate-fixed" style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;"></i>
                            Ambil Lokasi Saat Ini (GPS HP / Laptop)
                        </button>

                        <div class="d-flex align-items-center gap-2">
                            @if($iotDevice->gate_latitude && $iotDevice->gate_longitude)
                                <a href="https://www.google.com/maps?q={{ $iotDevice->gate_latitude }},{{ $iotDevice->gate_longitude }}" target="_blank" class="btn btn-light border rounded-pill btn-sm px-3 text-muted">
                                    <i data-lucide="external-link" style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;"></i>
                                    Cek di Google Maps
                                </a>
                            @endif
                            <button type="submit" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold shadow-sm" style="background: #1456A0;">
                                <i data-lucide="save" style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;"></i>
                                Simpan Titik &amp; Radius Gerbang
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif

    {{-- 3. VISUALISASI REAL-TIME PALANG GERBANG & SMART READER --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-7">
            <div class="portal-gate-visualizer h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-1.5 rounded-circle bg-primary bg-opacity-25 text-primary">
                            <i data-lucide="activity" style="width: 18px; height: 18px;"></i>
                        </div>
                        <span class="fw-bold" style="font-size: 0.95rem;">Simulasi Palang Pintu &amp; Radar Reader Real-Time</span>
                    </div>
                    <span class="badge rounded-pill bg-dark border border-secondary text-info px-2 py-1 small" style="font-size: 0.72rem;">
                        Polling 2s Live
                    </span>
                </div>

                {{-- Boom Barrier CSS Visualizer --}}
                <div class="boom-gate-box">
                    <div class="boom-barrier-housing">
                        <div class="boom-barrier-lamp" id="visualLamp"></div>
                    </div>
                    <div class="boom-barrier-arm" id="visualBarrierArm"></div>
                </div>

                {{-- Real-Time Status Notification Banner --}}
                <div id="visualStatusCard" class="p-3 rounded-3 mt-3 border transition-all" style="background: rgba(15, 23, 42, 0.6); border-color: #334155;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div id="visualStatusIcon" class="p-2 rounded-circle" style="background: rgba(59, 130, 246, 0.15); color: #60A5FA;">
                            <i data-lucide="radio" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold" id="visualStatusTitle" style="font-size: 0.92rem;">Siaga Menunggu Tap Kartu RFID...</div>
                            <small class="text-white-50 d-block" id="visualStatusDesc">Dekatkan kartu RFID warga pada sensor RFID-RC522 di pos gerbang.</small>
                        </div>
                        <div id="visualExtraAction" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Ringkas Kartu Terakhir --}}
        <div class="col-12 col-lg-5">
            <div class="wm-card-box h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="credit-card" style="width: 18px; height: 18px; color: var(--wm-primary);"></i>
                            Aktivitas Tap Terakhir
                        </h6>
                        <span class="badge bg-light text-muted border" id="lastTapTimeBadge">
                            {{ $portalLogs->first() ? $portalLogs->first()->tapped_at->diffForHumans() : 'Belum ada log' }}
                        </span>
                    </div>

                    <div id="lastTapDetailBox">
                        @if($lastTap = $portalLogs->first())
                            <div class="p-3 rounded-3 border mb-3 {{ $lastTap->status === 'granted' ? 'bg-success bg-opacity-10 border-success' : 'bg-danger bg-opacity-10 border-danger' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge {{ $lastTap->status === 'granted' ? 'bg-success' : 'bg-danger' }} rounded-pill px-2.5 py-1">
                                        {{ $lastTap->status === 'granted' ? 'AKSES DITERIMA' : 'DITOLAK' }}
                                    </span>
                                    <span class="font-monospace fw-bold text-dark" style="font-size: 0.85rem;">{{ $lastTap->rfid_uid }}</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">{{ $lastTap->user ? $lastTap->user->name : 'Tamu / Kartu Asing' }}</h6>
                                <div class="text-muted small">
                                    Mode: <strong>{{ ucfirst(str_replace('_', ' ', $lastTap->mode)) }}</strong> &bull;
                                    {{ $lastTap->user ? 'RT ' . ($lastTap->user->clean_rt_number ?? $lastTap->user->rt_number) : '-' }}
                                </div>
                                <div class="mt-2 text-dark small fw-semibold">
                                    Keterangan: {{ $lastTap->reason }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i data-lucide="inbox" style="width: 32px; height: 32px; opacity: 0.5;" class="mb-2"></i>
                                <div>Belum ada riwayat tap kartu hari ini.</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Ringkasan Statistik Kartu Terdaftar --}}
                <div class="p-3 rounded-3 bg-light border mt-3">
                    <div class="row g-2 text-center">
                        <div class="col-6 border-end">
                            <div class="fs-5 fw-bold text-primary">{{ $wargaRfidList->whereNotNull('rfid_uid')->count() }}</div>
                            <small class="text-muted" style="font-size: 0.72rem;">Kartu Warga Terdaftar</small>
                        </div>
                        <div class="col-6">
                            <div class="fs-5 fw-bold text-secondary">{{ $wargaRfidList->whereNull('rfid_uid')->count() }}</div>
                            <small class="text-muted" style="font-size: 0.72rem;">Warga Belum Ada Kartu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. TABEL RIWAYAT LOG KELUAR MASUK & IOT --}}
    <div class="wm-card-box mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="list" style="width: 18px; height: 18px; color: var(--wm-primary);"></i>
                    Riwayat Keluar-Masuk &amp; Tap RFID (Terbaru)
                </h6>
                <small class="text-muted">Mencatat otomatis setiap transaksi tap reader ESP32 di seluruh mode</small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="portalLogsTable" style="min-width: 680px;">
                <thead class="table-light">
                    <tr style="font-size: 0.8rem; text-transform: uppercase;">
                        <th>Waktu</th>
                        <th>Mode Layanan</th>
                        <th>UID Kartu</th>
                        <th>Nama Warga &amp; Wilayah</th>
                        <th>Status Akses</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="portalLogsTbody">
                    @forelse($portalLogs as $log)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark" style="font-size: 0.82rem;">{{ $log->tapped_at ? $log->tapped_at->translatedFormat('d M Y, H:i:s') : '-' }}</div>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $log->tapped_at ? $log->tapped_at->diffForHumans() : '-' }}</small>
                            </td>
                            <td>
                                @if($log->mode === 'portal')
                                    <span class="badge bg-primary rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">Portal Gerbang</span>
                                @elseif($log->mode === 'ronda')
                                    <span class="badge rounded-pill px-2.5 py-1 text-white" style="background: #7C3AED; font-size: 0.72rem;">Absensi Ronda</span>
                                @elseif($log->mode === 'bank_sampah')
                                    <span class="badge rounded-pill px-2.5 py-1 text-white" style="background: #16865D; font-size: 0.72rem;">Bank Sampah</span>
                                @elseif($log->mode === 'register')
                                    <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">Daftar Kartu</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-monospace fw-bold text-dark bg-light px-2 py-0.5 rounded border" style="font-size: 0.8rem;">
                                    {{ $log->rfid_uid }}
                                </span>
                            </td>
                            <td>
                                @if($log->user)
                                    @php
                                        $logRoleLabel = match($log->user->role) {
                                            'super_admin' => 'Super Admin Haven',
                                            'rw', 'admin' => 'Ketua RW 013',
                                            'bendahara' => 'Bendahara RW 013',
                                            'rt' => 'Ketua RT ' . ($log->user->clean_rt_number ? str_pad((string)$log->user->clean_rt_number, 3, '0', STR_PAD_LEFT) : ($log->user->rt_number ?? '001')),
                                            default => 'Warga RT ' . ($log->user->clean_rt_number ? str_pad((string)$log->user->clean_rt_number, 3, '0', STR_PAD_LEFT) : ($log->user->rt_number ?? '001'))
                                        };
                                    @endphp
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $log->user->name }}</div>
                                    <small class="text-muted" style="font-size: 0.74rem;">{{ $logRoleLabel }}</small>
                                @else
                                    <span class="text-muted fst-italic">Kartu Tidak Dikenal</span>
                                @endif
                            </td>
                            <td>
                                @if($log->status === 'granted')
                                    <span class="badge bg-success text-white rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                        <i data-lucide="check" style="width: 12px; height: 12px;"></i> Diterima
                                    </span>
                                @else
                                    <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                        <i data-lucide="x" style="width: 12px; height: 12px;"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="small text-secondary">{{ $log->reason }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Belum ada data tap kartu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 5. TABEL DAFTAR KARTU RFID WARGA TERDAFTAR --}}
    <div class="wm-card-box">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="users" style="width: 18px; height: 18px; color: var(--wm-primary);"></i>
                    Daftar Kartu Digital Warga (Kewenangan Pengurus)
                </h6>
                <small class="text-muted">Kelola kepemilikan kartu akses RFID warga di wilayah Anda</small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 720px;">
                <thead class="table-light">
                    <tr style="font-size: 0.8rem; text-transform: uppercase;">
                        <th>Pengguna &amp; Peran</th>
                        <th>Wilayah</th>
                        <th>No. Rumah</th>
                        <th>UID Kartu e-KTP / RFID</th>
                        <th>Status Kartu</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wargaRfidList as $w)
                        @php
                            $roleBadge = match($w->role) {
                                'super_admin' => ['Super Admin', 'bg-dark text-white'],
                                'rw', 'admin' => ['Ketua RW', 'bg-primary text-white'],
                                'bendahara' => ['Bendahara RW', 'bg-primary text-white'],
                                'rt' => ['Ketua RT ' . ($w->clean_rt_number ? str_pad((string)$w->clean_rt_number, 3, '0', STR_PAD_LEFT) : ($w->rt_number ?? '001')), 'bg-success text-white'],
                                default => ['Warga', 'bg-light text-dark border']
                            };
                            $wilayahBadge = match($w->role) {
                                'super_admin', 'rw', 'admin', 'bendahara' => 'RW 013 (Wilayah)',
                                default => 'RT ' . ($w->clean_rt_number ? str_pad((string)$w->clean_rt_number, 3, '0', STR_PAD_LEFT) : ($w->rt_number ?? '001'))
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $w->name }}</div>
                                <span class="badge {{ $roleBadge[1] }}" style="font-size: 0.68rem;">{{ $roleBadge[0] }}</span>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $wilayahBadge }}</span></td>
                            <td class="small text-muted">{{ $w->house_number ?? '-' }}</td>
                            <td>
                                @if($w->rfid_uid)
                                    <span class="font-monospace fw-bold text-primary bg-light px-2 py-0.5 rounded border">
                                        {{ $w->rfid_uid }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic small">Belum terdaftar</span>
                                @endif
                            </td>
                            <td>
                                @if($w->rfid_uid)
                                    <span class="badge bg-success text-white rounded-pill px-2.5 py-1">Aktif</span>
                                @else
                                    <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1">Kosong</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($w->rfid_uid)
                                    <form action="{{ route('ronda.portal.unlink') }}" method="POST" class="d-inline" onsubmit="return confirm('Cabut kartu RFID dari {{ $w->name }}?');">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $w->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                                            <i data-lucide="trash-2" style="width: 12px; height: 12px; display: inline-block; vertical-align: -1px;"></i> Cabut
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('ronda.portal.set-mode') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="mode" value="register">
                                        <input type="hidden" name="target_user_id" value="{{ $w->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                                            <i data-lucide="scan" style="width: 12px; height: 12px; display: inline-block; vertical-align: -1px;"></i> Pasangkan
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

@else
    {{-- VIEW KHUSUS WARGA BIASA --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-lg-5">
            {{-- Kartu Warga Digital Eksklusif --}}
            <div class="rfid-chip-card h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.78rem; opacity: 0.85;">
                            HAVEN SMART ACCESS &bull; RW 013
                        </span>
                        <i data-lucide="radio" style="width: 22px; height: 22px; color: #7DD3FC;"></i>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-4">
                        <svg width="44" height="34" viewBox="0 0 44 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="44" height="34" rx="6" fill="#F59E0B"/>
                            <rect x="2" y="2" width="40" height="30" rx="4" stroke="#D97706" stroke-width="1.5"/>
                            <path d="M2 17H16M28 17H42M16 2V32M28 2V32M16 10H28M16 24H28" stroke="#B45309" stroke-width="1.5"/>
                        </svg>
                        <span class="badge rounded-pill fw-bold px-2.5 py-1" style="background: rgba(255, 255, 255, 0.22); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.45); font-size: 0.72rem; backdrop-filter: blur(4px);">
                            <i data-lucide="radio" style="width: 11px; height: 11px; vertical-align: middle;"></i> RFID-RC522 Contactless
                        </span>
                    </div>

                    <h5 class="fw-bold mb-1 text-white">{{ $user->name }}</h5>
                    <div class="small opacity-75">
                        RT {{ $user->clean_rt_number ?? $user->rt_number ?? '001' }} / RW 013 &bull; No. Rumah: {{ $user->house_number ?? '-' }}
                    </div>
                </div>

                <div class="pt-4 border-top border-white border-opacity-20 mt-4 d-flex justify-content-between align-items-end">
                    <div>
                        <small class="text-white-50 d-block" style="font-size: 0.7rem; text-transform: uppercase;">ID Kartu Warga</small>
                        <span class="font-monospace fw-bold fs-6">{{ $user->masked_rfid_uid }}</span>
                    </div>
                    <span class="badge {{ $user->rfid_uid ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.75rem;">
                        {{ $user->rfid_uid ? 'KARTU TERDAFTAR' : 'BELUM AKTIF' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-7">
            <div class="wm-card-box h-100">
                <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                    <i data-lucide="info" style="width: 18px; height: 18px; color: var(--wm-primary);"></i>
                    Tentang Kartu Pintar Warga RW 013
                </h6>
                <p class="text-muted small">
                    Satu kartu RFID multifungsi ini dapat Anda gunakan untuk berbagai fasilitas terpadu di lingkungan RW 013 Suryatmajan:
                </p>

                <div class="row g-2 mb-3">
                    <div class="col-12 col-sm-4">
                        <div class="p-2.5 rounded-3 bg-light border text-center">
                            <i data-lucide="shield-check" style="width: 20px; height: 20px; color: #1456A0;" class="mb-1"></i>
                            <div class="fw-bold small text-dark">Portal Gerbang</div>
                            <small class="text-muted" style="font-size: 0.7rem;">Buka palang otomatis</small>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="p-2.5 rounded-3 bg-light border text-center">
                            <i data-lucide="moon" style="width: 20px; height: 20px; color: #7C3AED;" class="mb-1"></i>
                            <div class="fw-bold small text-dark">Absensi Ronda</div>
                            <small class="text-muted" style="font-size: 0.7rem;">Presensi pos siskamling</small>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="p-2.5 rounded-3 bg-light border text-center">
                            <i data-lucide="recycle" style="width: 20px; height: 20px; color: #16865D;" class="mb-1"></i>
                            <div class="fw-bold small text-dark">Bank Sampah</div>
                            <small class="text-muted" style="font-size: 0.7rem;">Identifikasi tabungan</small>
                        </div>
                    </div>
                </div>

                @if(!$user->rfid_uid)
                    <div class="alert alert-info border-0 rounded-3 small mb-0" style="background: #EAF3FF; color: #1456A0;">
                        <i data-lucide="alert-circle" style="width: 14px; height: 14px;" class="me-1"></i>
                        <strong>Belum punya kartu?</strong> Hubungi Pengurus RT / RW Anda untuk mengambil dan mengaktifkan kartu akses pintar warga RW 013 secara gratis.
                    </div>
                @else
                    <div class="alert alert-success border-0 rounded-3 small mb-0" style="background: #E8F6EF; color: #16865D;">
                        <i data-lucide="check-circle" style="width: 14px; height: 14px;" class="me-1"></i>
                        Kartu Anda aktif dan dapat langsung digunakan di gerbang masuk maupun pos ronda.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Riwayat Tap Pribadi Warga --}}
    <div class="wm-card-box">
        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i data-lucide="history" style="width: 18px; height: 18px; color: var(--wm-primary);"></i>
            Riwayat Akses Gerbang &amp; Layanan Pribadi Anda
        </h6>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 620px;">
                <thead class="table-light">
                    <tr style="font-size: 0.8rem; text-transform: uppercase;">
                        <th>Waktu Tap</th>
                        <th>Layanan / Mode</th>
                        <th>Perangkat</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($portalLogs as $log)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">{{ $log->tapped_at ? $log->tapped_at->translatedFormat('d M Y, H:i:s') : '-' }}</div>
                                <small class="text-muted">{{ $log->tapped_at ? $log->tapped_at->diffForHumans() : '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst(str_replace('_', ' ', $log->mode)) }}
                                </span>
                            </td>
                            <td><span class="badge bg-secondary">{{ $log->device_id }}</span></td>
                            <td>
                                @if($log->status === 'granted')
                                    <span class="badge bg-success text-white rounded-pill px-2.5 py-1">Berhasil</span>
                                @else
                                    <span class="badge bg-danger text-white rounded-pill px-2.5 py-1">Ditolak</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $log->reason }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Belum ada riwayat aktivitas tap kartu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
