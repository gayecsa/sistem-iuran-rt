<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Atur Ulang Kata Sandi | Haven Portal — Safe Space, Seamless Service</title>
    <meta name="description" content="Atur ulang kata sandi akun Haven Portal RW 013 Sukamiskin. Buat kata sandi baru yang aman.">
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
            --primary: #1456A0;
            --primary-dark: #0F3966;
            --secondary: #2878D0;
            --light-blue: #EAF3FF;
            --green: #16865D;
            --white: #FFFFFF;
            --bg: #F4F7FB;
            --text: #172B4D;
            --muted: #5E6C84;
            --border: #DCE4EE;
            --card: #FFFFFF;
            --input: #FFFFFF;
            --danger: #C9372C;
            --danger-bg: #FFECEB;
            --success-bg: #E3F5EC;
            --shadow: 0 20px 60px rgba(20, 86, 160, 0.12);
        }
        [data-theme="dark"] {
            --primary-dark: #0A1E33;
            --light-blue: #1A2A44;
            --bg: #0F1724;
            --text: #E6EDF7;
            --muted: #9AA8BC;
            --border: #2A3A52;
            --card: #16213A;
            --input: #0F1A2E;
            --danger-bg: #3A1D1D;
            --success-bg: #12352A;
            --shadow: 0 20px 60px rgba(0, 0, 0, 0.45);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: linear-gradient(180deg, var(--light-blue) 0%, var(--bg) 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            transition: background .3s, color .3s;
        }
        a { color: var(--secondary); text-decoration: none; font-weight: 600; }
        a:hover { text-decoration: underline; }

        /* Split-Screen Main Wrapper */
        .auth-split-wrapper {
            width: 100%;
            max-width: 980px;
            background: var(--card);
            border-radius: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            display: grid;
            grid-template-columns: 1fr 1.15fr;
            overflow: hidden;
            position: relative;
        }

        /* Brand Panel Kiri */
        .auth-brand-panel {
            background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%);
            padding: 44px 36px;
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .auth-brand-panel::after {
            content: "";
            position: absolute;
            top: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }
        .auth-brand-panel::before {
            content: "";
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            pointer-events: none;
        }
        .brand-top { position: relative; z-index: 1; }
        .brand-logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 8px 16px 8px 10px;
            border-radius: 40px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            margin-bottom: 24px;
        }
        .brand-logo-badge svg {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .brand-logo-badge span {
            font-size: .88rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: 0.5px;
        }
        .brand-top h1 {
            font-size: clamp(1.8rem, 2.5vw, 2.3rem);
            font-weight: 800;
            line-height: 1.22;
            color: #FFFFFF;
            margin-bottom: 12px;
        }
        .brand-top p.desc {
            font-size: .92rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 28px;
        }
        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 28px;
        }
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 14px;
            padding: 12px 14px;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
        .feature-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.18);
            display: grid;
            place-items: center;
            font-size: 1rem;
            color: #FFFFFF;
            flex-shrink: 0;
        }
        .feature-text strong {
            display: block;
            font-size: .88rem;
            color: #FFFFFF;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .feature-text small {
            display: block;
            font-size: .78rem;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.4;
        }
        .brand-footer {
            position: relative;
            z-index: 1;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            font-size: .8rem;
            color: rgba(255, 255, 255, 0.78);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form Panel Kanan */
        .auth-form-panel {
            padding: 40px 36px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--card);
        }
        .form-top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .back-home {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            color: var(--muted);
            font-size: .88rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 10px;
            background: var(--light-blue);
            transition: all .2s;
        }
        .back-home:hover {
            color: var(--primary);
            text-decoration: none;
            background: rgba(40, 120, 208, 0.15);
        }
        .theme-toggle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: var(--card);
            color: var(--text);
            cursor: pointer;
            display: grid;
            place-items: center;
            transition: transform .2s, border-color .2s;
        }
        .theme-toggle:hover {
            transform: rotate(20deg);
            border-color: var(--secondary);
            color: var(--secondary);
        }
        .form-header {
            text-align: center;
            margin-bottom: 22px;
        }
        .form-header svg {
            width: 48px;
            height: 48px;
            margin-bottom: 8px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(20, 86, 160, 0.2);
        }
        .form-header h2 {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 4px;
        }
        .form-header p {
            color: var(--muted);
            font-size: .85rem;
            line-height: 1.4;
        }

        /* Inputs */
        .field { margin-bottom: 16px; }
        .field label {
            display: block;
            font-size: .84rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text);
        }
        .control { position: relative; }
        .control > i.lead {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: .9rem;
            pointer-events: none;
        }
        .control input {
            width: 100%;
            padding: 11px 40px 11px 40px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            background: var(--input);
            color: var(--text);
            font: inherit;
            font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .control input:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 4px rgba(40, 120, 208, 0.15);
        }
        .control input:-webkit-autofill,
        .control input:-webkit-autofill:hover, 
        .control input:-webkit-autofill:focus,
        .control input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px var(--input) inset !important;
            -webkit-text-fill-color: var(--text) !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        .control .is-invalid { border-color: var(--danger) !important; }
        .eye {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: none;
            color: var(--muted);
            width: 32px;
            height: 32px;
            cursor: pointer;
            border-radius: 8px;
            display: grid;
            place-items: center;
        }
        .eye:hover { color: var(--secondary); }
        .error {
            color: var(--danger);
            font-size: .78rem;
            margin-top: 5px;
            display: flex;
            gap: 6px;
            align-items: center;
        }
        .strength {
            height: 4px;
            border-radius: 4px;
            background: var(--border);
            margin-top: 6px;
            overflow: hidden;
        }
        .strength span { display: block; height: 100%; width: 0; transition: width .3s, background .3s; }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 12px;
            font: inherit;
            font-weight: 700;
            font-size: .92rem;
            color: #fff;
            background: var(--green);
            cursor: pointer;
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            transition: background .2s, transform .1s;
            margin-top: 8px;
        }
        .btn:hover { background: #12704E; }
        .btn:active { transform: scale(.99); }
        .btn:disabled { opacity: .7; cursor: wait; }

        .alert {
            border-radius: 12px;
            padding: 11px 14px;
            margin-bottom: 18px;
            font-size: .85rem;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .alert.ok { background: var(--success-bg); color: var(--green); border: 1px solid rgba(22, 134, 93, 0.25); }
        .alert.err { background: var(--danger-bg); color: var(--danger); border: 1px solid rgba(201, 55, 44, 0.25); }

        /* Responsive */
        @media (max-width: 900px) {
            body { padding: 16px 12px; }
            .auth-split-wrapper { grid-template-columns: 1fr; max-width: 480px; }
            .auth-brand-panel { display: none !important; }
            .auth-form-panel { padding: 32px 24px; }
        }
        @media (max-width: 520px) {
            .auth-form-panel { padding: 24px 18px; }
        }
    </style>
</head>
<body>

@php
$logoSvg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect width="64" height="64" rx="16" fill="url(#havenPassGrad)"/><path d="M16 19L32 10L48 19" stroke="#7DD3FC" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 23V46M44 23V46M20 34H44" stroke="#FFFFFF" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="havenPassGrad" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse"><stop stop-color="#1456A0"/><stop offset="1" stop-color="#2878D0"/></linearGradient></defs></svg>';
@endphp

<main class="auth-split-wrapper">

    {{-- PANEL KIRI: BRANDING BIRU HAVEN SMART RW 013 --}}
    <section class="auth-brand-panel" aria-label="Portal Smart RW 013 Sukamiskin">
        <div class="brand-top">
            <div class="brand-logo-badge">
                {!! $logoSvg !!}
                <span>HAVEN PORTAL</span>
            </div>

            <h1>Safe Space, <span>Seamless Service</span></h1>
            <p class="desc">
                Portal digital terintegrasi warga RW 013 Sukamiskin Arcamanik. Perbarui kata sandi Anda dan jaga keamanan akses layanan digital keluarga.
            </p>

            <div class="brand-features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="feature-text">
                        <strong>Enkripsi Standar Keamanan</strong>
                        <small>Kata sandi baru langsung dienkripsi dengan standar hash bcrypt tingkat tinggi.</small>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="feature-text">
                        <strong>Kombinasi Karakter Kuat</strong>
                        <small>Gunakan minimal 6 karakter dengan kombinasi huruf, angka, atau simbol.</small>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div class="feature-text">
                        <strong>Perlindungan Privasi Warga</strong>
                        <small>Data iuran, profil keluarga, dan surat pengantar tetap terjaga kerahasiaannya.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="brand-footer">
            <i class="fas fa-shield-halved" style="color: #6EE7B7;"></i>
            <span>Haven Portal &bull; havenportal.mehape.com &bull; RW 013 Sukamiskin</span>
        </div>
    </section>

    {{-- PANEL KANAN: FORM ATUR ULANG KATA SANDI --}}
    <section class="auth-form-panel">
        <div class="form-top-bar">
            <a href="{{ route('login') }}" class="back-home">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Login</span>
            </a>
            <button class="theme-toggle" id="themeToggle" type="button" aria-label="Ganti mode gelap/terang">
                <i class="fas fa-moon"></i>
            </button>
        </div>

        <div class="form-header">
            {!! $logoSvg !!}
            <h2>Atur Ulang Kata Sandi</h2>
            <p>Buat kata sandi baru yang kuat dan aman untuk akun Haven Portal Anda.</p>
        </div>

        @if (session('status'))
            <div class="alert ok">
                <i class="fas fa-circle-check" style="margin-top: 2px;"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert err">
                <i class="fas fa-circle-exclamation" style="margin-top: 2px;"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="js-submit" novalidate>
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="field">
                <label for="email">Alamat Email Terdaftar</label>
                <div class="control">
                    <i class="fas fa-envelope lead"></i>
                    <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}"
                           class="@error('email') is-invalid @enderror"
                           placeholder="nama@email.com" autocomplete="email" required autofocus>
                </div>
                @error('email')
                    <div class="error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Kata Sandi Baru</label>
                <div class="control">
                    <i class="fas fa-lock lead"></i>
                    <input type="password" id="password" name="password"
                           class="@error('password') is-invalid @enderror"
                           placeholder="Minimal 6 karakter" autocomplete="new-password" required>
                    <button type="button" class="eye" data-target="password" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                </div>
                <div class="strength"><span id="strengthBar"></span></div>
                @error('password')
                    <div class="error"><i class="fas fa-circle-exclamation"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                <div class="control">
                    <i class="fas fa-shield-check lead"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           placeholder="Ulangi kata sandi baru" autocomplete="new-password" required>
                    <button type="button" class="eye" data-target="password_confirmation" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                </div>
                <div class="error" id="matchError" hidden><i class="fas fa-circle-exclamation"></i>Konfirmasi password belum cocok</div>
            </div>

            <button type="submit" class="btn" id="btnReset">
                <i class="fas fa-circle-check"></i>
                <span>Simpan Kata Sandi Baru</span>
            </button>
        </form>
    </section>

</main>

<script>
(function () {
    var root = document.documentElement, toggle = document.getElementById('themeToggle');
    function syncIcon() {
        toggle.innerHTML = root.dataset.theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    }
    syncIcon();
    toggle.addEventListener('click', function () {
        root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('wm-theme', root.dataset.theme);
        syncIcon();
    });

    document.querySelectorAll('.eye').forEach(function (b) {
        b.addEventListener('click', function () {
            var i = document.getElementById(b.dataset.target), shown = i.type === 'text';
            i.type = shown ? 'password' : 'text';
            b.innerHTML = shown ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    });

    var pw = document.getElementById('password'), pc = document.getElementById('password_confirmation'),
        bar = document.getElementById('strengthBar'), me = document.getElementById('matchError');
    if (pw && bar) {
        pw.addEventListener('input', function () {
            var v = pw.value, s = 0;
            if (v.length >= 6) s++;
            if (v.length >= 10) s++;
            if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++;
            if (/\d/.test(v) || /\W/.test(v)) s++;
            bar.style.width = (s * 25) + '%';
            bar.style.background = ['#C9372C', '#C9372C', '#E59A1A', '#2878D0', '#16865D'][s];
        });
    }

    function checkMatch() {
        if (me && pc && pw) {
            me.hidden = !pc.value || pc.value === pw.value;
        }
    }
    if (pc && pw) {
        pc.addEventListener('input', checkMatch);
        pw.addEventListener('input', checkMatch);
    }

    var form = document.querySelector('form.js-submit');
    if (form) {
        form.addEventListener('submit', function (e) {
            var bad = null;
            form.querySelectorAll('[required]').forEach(function (el) {
                var ok = el.checkValidity();
                el.classList.toggle('is-invalid', !ok);
                if (!ok && !bad) bad = el;
            });
            if (pc && pw && form.contains(pc) && pc.value !== pw.value) {
                pc.classList.add('is-invalid');
                if (me) me.hidden = false;
                bad = bad || pc;
            }
            if (bad) {
                e.preventDefault();
                bad.focus();
                return;
            }
            var btn = document.getElementById('btnReset');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
            }
        });
    }
})();
</script>

</body>
</html>
