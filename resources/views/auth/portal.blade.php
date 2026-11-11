@php
    $registerFields = ['name', 'house_number', 'phone', 'address', 'password_confirmation', 'status_rumah'];
    $hasRegisterInput = collect($registerFields)->contains(fn ($f) => old($f) !== null);
    $activeTab = $hasRegisterInput ? 'register' : ($mode ?? 'login');
@endphp
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $activeTab === 'register' ? 'Daftar Akun' : 'Masuk' }} | Warkas Machi RW 013</title>
    <meta name="description" content="Warkas Machi – Sistem Informasi dan Layanan Terpadu RW 013. Masuk atau daftar untuk mengakses layanan warga.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        (function () {
            var t = localStorage.getItem('wm-theme');
            if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        :root {
            --primary: #1456A0; --secondary: #2878D0; --light-blue: #EAF3FF;
            --green: #16865D; --white: #FFFFFF; --bg: #F4F7FB; --text: #172B4D;
            --muted: #5E6C84; --border: #DCE4EE; --card: #FFFFFF; --input: #FFFFFF;
            --panel: linear-gradient(180deg, #EAF3FF 0%, #F4F7FB 100%);
            --danger: #C9372C; --danger-bg: #FFECEB; --success-bg: #E3F5EC;
            --shadow: 0 10px 30px rgba(20, 86, 160, .08);
        }
        [data-theme="dark"] {
            --light-blue: #1A2A44; --bg: #0F1724; --text: #E6EDF7; --muted: #9AA8BC;
            --border: #2A3A52; --card: #16213A; --input: #0F1A2E;
            --panel: linear-gradient(180deg, #13203A 0%, #0F1724 100%);
            --danger-bg: #3A1D1D; --success-bg: #12352A; --shadow: 0 10px 30px rgba(0,0,0,.35);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: var(--bg); color: var(--text);
               min-height: 100vh; transition: background .3s, color .3s; }
        a { color: var(--secondary); text-decoration: none; font-weight: 600; }
        a:hover { text-decoration: underline; }

        .auth { display: grid; grid-template-columns: 1fr; min-height: 100vh;
                background: linear-gradient(180deg, var(--light-blue), var(--bg) 60%); }
        .auth > .brand { display: none !important; }
        .back-home { position: absolute; top: 22px; left: 24px; display: inline-flex; gap: 8px; align-items: center;
                     color: var(--muted); font-size: .9rem; }
        .back-home:hover { color: var(--secondary); text-decoration: none; }
        @media (max-width: 900px) { .card-head { display: block !important; } .side { padding-top: 76px !important; } }

        /* ---------- Branding ---------- */
        .brand { background: var(--panel); position: relative; overflow: hidden;
                 display: flex; flex-direction: column; padding: 40px 56px 0; }
        .logo { display: flex; align-items: center; gap: 12px; }
        .logo svg { width: 48px; height: 48px; flex-shrink: 0; }
        .logo strong { display: block; font-size: 1.25rem; color: var(--primary); }
        [data-theme="dark"] .logo strong { color: #7DB4F5; }
        .logo small { color: var(--muted); font-size: .8rem; }
        .brand h1 { font-size: clamp(2rem, 3.2vw, 3rem); line-height: 1.15; margin: 40px 0 8px; font-weight: 800; }
        .brand h1 span { color: var(--secondary); display: block; }
        .underline { width: 120px; height: 5px; border-radius: 4px; background: #F2B33D; margin-bottom: 20px; }
        .brand p.desc { color: var(--muted); max-width: 440px; line-height: 1.65; }
        .pillars { display: flex; gap: 0; margin-top: 24px; flex-wrap: wrap; }
        .pillar { text-align: center; padding: 0 18px; border-left: 1px solid var(--border); font-size: .78rem; color: var(--muted); }
        .pillar:first-child { border-left: 0; padding-left: 0; }
        .pillar i { display: grid; place-items: center; width: 40px; height: 40px; margin: 0 auto 6px;
                    border-radius: 50%; background: var(--card); box-shadow: var(--shadow); font-size: 1rem; }
        .illustration { margin-top: auto; padding-top: 24px; }
        .illustration img { width: 100%; max-height: 46vh; object-fit: cover; object-position: top;
                            display: block; border-radius: 24px 24px 0 0;
                            -webkit-mask-image: linear-gradient(to bottom, transparent 0, #000 18%); mask-image: linear-gradient(to bottom, transparent 0, #000 18%); }

        /* ---------- Form side ---------- */
        .side { display: flex; align-items: center; justify-content: center; padding: 40px 24px; position: relative; }
        .theme-toggle { position: absolute; top: 20px; right: 20px; width: 42px; height: 42px; border-radius: 50%;
                        border: 1px solid var(--border); background: var(--card); color: var(--text); cursor: pointer;
                        transition: transform .2s; }
        .theme-toggle:hover { transform: rotate(20deg); }
        .card { width: 100%; max-width: 460px; background: var(--card); border-radius: 24px; padding: 36px 32px;
                box-shadow: var(--shadow); border: 1px solid var(--border); animation: rise .5s ease; }
        .card.wide { max-width: 560px; }
        @keyframes rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .card-head { text-align: center; margin-bottom: 22px; }
        .card-head svg { width: 56px; height: 56px; }
        .card-head h2 { font-size: 1.5rem; margin-top: 6px; }
        .card-head small { color: var(--muted); }

        .tabs { display: grid; grid-template-columns: 1fr 1fr; background: var(--light-blue); border-radius: 14px; padding: 4px;
                margin-bottom: 24px; position: relative; }
        .tabs .slider { position: absolute; top: 4px; bottom: 4px; left: 4px; width: calc(50% - 4px); background: var(--primary);
                        border-radius: 11px; transition: transform .3s ease; }
        .tabs[data-active="register"] .slider { transform: translateX(100%); }
        .tab { position: relative; z-index: 1; border: 0; background: none; padding: 11px; font: inherit; font-weight: 600;
               color: var(--muted); cursor: pointer; border-radius: 11px; transition: color .3s; }
        .tab.active { color: #fff; }

        .pane { display: none; }
        .pane.active { display: block; animation: fade .3s ease; }
        @keyframes fade { from { opacity: 0; } to { opacity: 1; } }

        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: 6px; }
        .control { position: relative; }
        .control > i.lead { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: .9rem; pointer-events: none; }
        .control.area > i.lead { top: 16px; transform: none; }
        .control input, .control select, .control textarea {
            width: 100%; padding: 12px 44px 12px 42px; border: 1.5px solid var(--border); border-radius: 12px;
            background: var(--input); color: var(--text); font: inherit; font-size: .92rem; transition: border .2s, box-shadow .2s; }
        .control textarea { resize: vertical; min-height: 76px; }
        .control select { appearance: none; cursor: pointer; }
        .control input:focus, .control select:focus, .control textarea:focus {
            outline: none; border-color: var(--secondary); box-shadow: 0 0 0 4px rgba(40, 120, 208, .15); }
        .control .is-invalid { border-color: var(--danger); }
        .eye { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: 0; background: none;
               color: var(--muted); width: 34px; height: 34px; cursor: pointer; border-radius: 8px; }
        .eye:hover { color: var(--secondary); }
        .error { color: var(--danger); font-size: .78rem; margin-top: 5px; display: flex; gap: 5px; align-items: center; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0 14px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0 14px; }

        .row-between { display: flex; justify-content: space-between; align-items: center; margin: 4px 0 20px; font-size: .88rem; }
        .check { display: flex; gap: 8px; align-items: center; cursor: pointer; }
        .check input { width: 17px; height: 17px; accent-color: var(--primary); }

        .btn { width: 100%; border: 0; border-radius: 12px; padding: 13px; font: inherit; font-weight: 700; color: #fff;
               background: var(--primary); cursor: pointer; display: flex; gap: 10px; justify-content: center; align-items: center;
               transition: background .2s, transform .1s; }
        .btn:hover { background: var(--secondary); }
        .btn:active { transform: scale(.99); }
        .btn:disabled { opacity: .7; cursor: wait; }
        .btn.green { background: var(--green); }
        .btn.green:hover { background: #12704E; }
        .switch { text-align: center; margin-top: 20px; font-size: .88rem; color: var(--muted); }
        .switch button { border: 0; background: none; color: var(--secondary); font: inherit; font-weight: 600; cursor: pointer; }

        .alert { border-radius: 12px; padding: 12px 14px; margin-bottom: 18px; font-size: .86rem; display: flex; gap: 10px; align-items: flex-start; }
        .alert.ok { background: var(--success-bg); color: var(--green); }
        .alert.err { background: var(--danger-bg); color: var(--danger); }
        .strength { height: 4px; border-radius: 4px; background: var(--border); margin-top: 6px; overflow: hidden; }
        .strength span { display: block; height: 100%; width: 0; transition: width .3s, background .3s; }

        /* ---------- Responsive ---------- */
        @media (max-width: 1100px) { .brand { padding: 32px 36px 0; } .pillar { padding: 0 10px; } }
        @media (max-width: 900px) {
            .auth { grid-template-columns: 1fr; }
            .brand { padding: 22px 24px 18px; }
            .brand h1 { font-size: 1.5rem; margin: 16px 0 4px; }
            .brand h1 span { display: inline; }
            .underline, .pillars, .illustration, .brand p.desc { display: none; }
            .side { padding: 20px 16px 40px; align-items: flex-start; }
            .card-head { display: none; }
            .theme-toggle { top: 22px; right: 20px; position: fixed; z-index: 5; }
        }
        @media (max-width: 520px) {
            .card { padding: 24px 18px; border-radius: 20px; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
@php
$logo = '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M8 30 32 10l24 20" fill="none" stroke="#1456A0" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 27v20h36V27" fill="none" stroke="#2878D0" stroke-width="3.5" stroke-linejoin="round"/><circle cx="26" cy="30" r="3.5" fill="#1456A0"/><path d="M21 44v-6a5 5 0 0 1 10 0v6" fill="#1456A0"/><circle cx="38" cy="32" r="3" fill="#2878D0"/><path d="M34 44v-5a4 4 0 0 1 8 0v5" fill="#2878D0"/><path d="M10 52c8-8 18-8 22-2 4-6 14-6 22 2-10 4-34 4-44 0Z" fill="#16865D"/></svg>';
@endphp
<main class="auth">
    {{-- ================= BRANDING ================= --}}
    <section class="brand" aria-label="Tentang Warkas Machi">
        <div class="logo">
            {!! $logo !!}
            <div><strong>Warkas Machi</strong><small>RW 013 &bull; Kelurahan Machi</small></div>
        </div>
        <h1>Pelayanan Mudah, <span>Warga Sejahtera</span></h1>
        <div class="underline"></div>
        <p class="desc">Sistem Informasi dan Layanan Terpadu RW 013 untuk mewujudkan lingkungan yang lebih baik, sehat, dan sejahtera.</p>
        <div class="pillars">
            <div class="pillar"><i class="fas fa-users" style="color:#2878D0"></i>Pelayanan<br>Terpadu</div>
            <div class="pillar"><i class="fas fa-heart-pulse" style="color:#16865D"></i>Kesehatan<br>Masyarakat</div>
            <div class="pillar"><i class="fas fa-chart-line" style="color:#E59A1A"></i>Ekonomi<br>Warga</div>
            <div class="pillar"><i class="fas fa-shield-halved" style="color:#7A5AF8"></i>Lingkungan<br>Aman</div>
        </div>
        <div class="illustration">
            <img src="{{ asset('images/auth-warga.jpg') }}" alt="Ilustrasi keluarga dan warga Indonesia di lingkungan RW 013">
        </div>
    </section>

    {{-- ================= FORM ================= --}}
    <section class="side">
        <a href="{{ route('cover') }}" class="back-home" id="backHome"><i class="fas fa-arrow-left"></i> Beranda</a>
        <button class="theme-toggle" id="themeToggle" type="button" aria-label="Ganti mode gelap/terang">
            <i class="fas fa-moon"></i>
        </button>

        <div class="card {{ $activeTab === 'register' ? 'wide' : '' }}" id="authCard">
            <div class="card-head">
                {!! $logo !!}
                <h2>Warkas Machi</h2>
                <small>RW 013 &bull; Kelurahan Machi</small>
            </div>

            <div class="tabs" id="tabs" data-active="{{ $activeTab }}" role="tablist">
                <span class="slider"></span>
                <button type="button" class="tab {{ $activeTab === 'login' ? 'active' : '' }}" id="tab-login" data-tab="login" role="tab"><i class="fas fa-lock me"></i> Masuk</button>
                <button type="button" class="tab {{ $activeTab === 'register' ? 'active' : '' }}" id="tab-register" data-tab="register" role="tab"><i class="fas fa-user-plus"></i> Daftar Akun</button>
            </div>

            @if (session('success'))
                <div class="alert ok"><i class="fas fa-circle-check"></i><span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div class="alert err"><i class="fas fa-circle-exclamation"></i><span>{{ session('error') }}</span></div>
            @endif

            {{-- ---------- LOGIN ---------- --}}
            <div class="pane {{ $activeTab === 'login' ? 'active' : '' }}" id="pane-login" role="tabpanel">
                <form method="POST" action="{{ route('login') }}" class="js-submit" novalidate>
                    @csrf
                    <div class="field">
                        <label for="loginEmail">Email</label>
                        <div class="control">
                            <i class="fas fa-envelope lead"></i>
                            <input type="email" id="loginEmail" name="email" value="{{ $hasRegisterInput ? '' : old('email') }}"
                                   class="@if(!$hasRegisterInput) @error('email') is-invalid @enderror @endif"
                                   placeholder="Masukkan email terdaftar" autocomplete="username" required>
                        </div>
                        @if(!$hasRegisterInput) @error('email') <div class="error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</div> @enderror @endif
                    </div>
                    <div class="field">
                        <label for="loginPassword">Password</label>
                        <div class="control">
                            <i class="fas fa-lock lead"></i>
                            <input type="password" id="loginPassword" name="password" placeholder="Masukkan password"
                                   class="@if(!$hasRegisterInput) @error('password') is-invalid @enderror @endif" autocomplete="current-password" required>
                            <button type="button" class="eye" data-target="loginPassword" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                        </div>
                        @if(!$hasRegisterInput) @error('password') <div class="error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</div> @enderror @endif
                    </div>
                    <div class="row-between">
                        <label class="check"><input type="checkbox" name="remember" id="remember"> Ingat saya</label>
                        <a href="{{ route('password.request') }}">Lupa password?</a>
                    </div>
                    <button type="submit" class="btn" id="btnLogin"><i class="fas fa-right-to-bracket"></i> Masuk</button>
                </form>
                <p class="switch">Belum punya akun? <button type="button" data-tab="register">Daftar sekarang</button></p>
            </div>

            {{-- ---------- REGISTER ---------- --}}
            <div class="pane {{ $activeTab === 'register' ? 'active' : '' }}" id="pane-register" role="tabpanel">
                <form method="POST" action="{{ route('register') }}" class="js-submit" novalidate>
                    @csrf
                    @php $re = fn($f) => $hasRegisterInput && $errors->has($f); @endphp
                    <div class="grid-2">
                        <div class="field">
                            <label for="regName">Nama Lengkap</label>
                            <div class="control"><i class="fas fa-user lead"></i>
                                <input type="text" id="regName" name="name" value="{{ old('name') }}" class="{{ $re('name') ? 'is-invalid' : '' }}" placeholder="Nama sesuai KTP" required>
                            </div>
                            @if($re('name'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('name') }}</div>@endif
                        </div>
                        <div class="field">
                            <label for="regEmail">Email</label>
                            <div class="control"><i class="fas fa-envelope lead"></i>
                                <input type="email" id="regEmail" name="email" value="{{ $hasRegisterInput ? old('email') : '' }}" class="{{ $re('email') ? 'is-invalid' : '' }}" placeholder="nama@email.com" required>
                            </div>
                            @if($re('email'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('email') }}</div>@endif
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="field">
                            <label for="regPassword">Password</label>
                            <div class="control"><i class="fas fa-lock lead"></i>
                                <input type="password" id="regPassword" name="password" class="{{ $re('password') ? 'is-invalid' : '' }}" placeholder="Min. 6 karakter" autocomplete="new-password" required>
                                <button type="button" class="eye" data-target="regPassword" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="strength"><span id="strengthBar"></span></div>
                            @if($re('password'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('password') }}</div>@endif
                        </div>
                        <div class="field">
                            <label for="regPasswordConfirm">Konfirmasi Password</label>
                            <div class="control"><i class="fas fa-lock lead"></i>
                                <input type="password" id="regPasswordConfirm" name="password_confirmation" placeholder="Ulangi password" autocomplete="new-password" required>
                                <button type="button" class="eye" data-target="regPasswordConfirm" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="error" id="matchError" hidden><i class="fas fa-circle-exclamation"></i>Password tidak sama</div>
                        </div>
                    </div>
                    <div class="grid-3">
                        <div class="field">
                            <label for="regHouse">No. Rumah</label>
                            <div class="control"><i class="fas fa-house lead"></i>
                                <input type="text" id="regHouse" name="house_number" value="{{ old('house_number') }}" class="{{ $re('house_number') ? 'is-invalid' : '' }}" placeholder="A-12" required>
                            </div>
                            @if($re('house_number'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('house_number') }}</div>@endif
                        </div>
                        <div class="field">
                            <label for="regPhone">No. Telepon</label>
                            <div class="control"><i class="fas fa-phone lead"></i>
                                <input type="text" id="regPhone" name="phone" value="{{ old('phone') }}" class="{{ $re('phone') ? 'is-invalid' : '' }}" placeholder="08xx" required>
                            </div>
                            @if($re('phone'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('phone') }}</div>@endif
                        </div>
                        <div class="field">
                            <label for="regStatus">Status Rumah</label>
                            <div class="control"><i class="fas fa-building lead"></i>
                                <select id="regStatus" name="status_rumah">
                                    @foreach (['milik_sendiri' => 'Milik Sendiri', 'kontrak' => 'Kontrak', 'sewa' => 'Sewa'] as $v => $l)
                                        <option value="{{ $v }}" @selected(old('status_rumah') === $v)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label for="regAddress">Alamat Lengkap</label>
                        <div class="control area"><i class="fas fa-location-dot lead"></i>
                            <textarea id="regAddress" name="address" rows="2" class="{{ $re('address') ? 'is-invalid' : '' }}" placeholder="Jalan, RT/RW, Kelurahan" required>{{ old('address') }}</textarea>
                        </div>
                        @if($re('address'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('address') }}</div>@endif
                    </div>
                    <button type="submit" class="btn green" id="btnRegister"><i class="fas fa-user-plus"></i> Daftar Sekarang</button>
                </form>
                <p class="switch">Sudah punya akun? <button type="button" data-tab="login">Masuk di sini</button></p>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    var root = document.documentElement, toggle = document.getElementById('themeToggle');
    function syncIcon() { toggle.innerHTML = root.dataset.theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>'; }
    syncIcon();
    toggle.addEventListener('click', function () {
        root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('wm-theme', root.dataset.theme); syncIcon();
    });

    // Tab switching without reload; URL follows existing Laravel routes
    var urls = { login: @json(route('login')), register: @json(route('register')) };
    var tabs = document.getElementById('tabs'), card = document.getElementById('authCard');
    function show(tab) {
        tabs.dataset.active = tab;
        document.querySelectorAll('.tab').forEach(function (b) { b.classList.toggle('active', b.dataset.tab === tab); });
        document.querySelectorAll('.pane').forEach(function (p) { p.classList.toggle('active', p.id === 'pane-' + tab); });
        card.classList.toggle('wide', tab === 'register');
        document.title = (tab === 'register' ? 'Daftar Akun' : 'Masuk') + ' | Warkas Machi RW 013';
        if (location.pathname !== '/') history.replaceState(null, '', urls[tab]);
    }
    document.querySelectorAll('[data-tab]').forEach(function (b) { b.addEventListener('click', function () { show(b.dataset.tab); }); });

    document.querySelectorAll('.eye').forEach(function (b) {
        b.addEventListener('click', function () {
            var i = document.getElementById(b.dataset.target), shown = i.type === 'text';
            i.type = shown ? 'password' : 'text';
            b.innerHTML = shown ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    });

    var pw = document.getElementById('regPassword'), pc = document.getElementById('regPasswordConfirm'),
        bar = document.getElementById('strengthBar'), me = document.getElementById('matchError');
    pw.addEventListener('input', function () {
        var v = pw.value, s = 0;
        if (v.length >= 6) s++; if (v.length >= 10) s++; if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++; if (/\d/.test(v) || /\W/.test(v)) s++;
        bar.style.width = (s * 25) + '%';
        bar.style.background = ['#C9372C', '#C9372C', '#E59A1A', '#2878D0', '#16865D'][s];
    });
    function checkMatch() { me.hidden = !pc.value || pc.value === pw.value; }
    pc.addEventListener('input', checkMatch); pw.addEventListener('input', checkMatch);

    // Light client-side validation feedback; server validation remains authoritative
    document.querySelectorAll('form.js-submit').forEach(function (f) {
        f.addEventListener('submit', function (e) {
            var bad = null;
            f.querySelectorAll('[required]').forEach(function (el) {
                var ok = el.checkValidity(); el.classList.toggle('is-invalid', !ok); if (!ok && !bad) bad = el;
            });
            if (f.contains(pc) && pc.value !== pw.value) { pc.classList.add('is-invalid'); me.hidden = false; bad = bad || pc; }
            if (bad) { e.preventDefault(); bad.focus(); return; }
            var btn = f.querySelector('button[type=submit]'); btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';
        });
    });
})();
</script>
</body>
</html>
