@php
    $registerFields = ['name', 'email', 'nik', 'no_kk', 'house_number', 'phone', 'address', 'password_confirmation', 'status_rumah'];
    $hasRegisterInput = collect($registerFields)->contains(fn ($f) => old($f) !== null);
    $activeTab = $hasRegisterInput ? 'register' : ($mode ?? 'login');
@endphp
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Haven Portal — Safe Space, Seamless Service</title>
    <meta name="description" content="Haven Portal – Safe Space, Seamless Service. Sistem Informasi dan Layanan Terpadu RW 013 Kel. Suryatmajan, Kec. Danurejan, Yogyakarta. Masuk atau daftar untuk mengakses layanan warga.">
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

        /* ---------- Split-Screen Main Wrapper ---------- */
        .auth-split-wrapper {
            width: 100%;
            max-width: 1000px;
            background: var(--card);
            border-radius: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            display: grid;
            grid-template-columns: 1fr 1.15fr;
            overflow: hidden;
            transition: max-width .3s ease;
            position: relative;
        }
        .auth-split-wrapper.wide {
            max-width: 1060px;
        }

        /* ---------- Brand Panel Kiri (Biru Haven) ---------- */
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
        .brand-top {
            position: relative;
            z-index: 1;
        }
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

        /* 3 Poin Keunggulan Utama */
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
            transition: background .2s;
        }
        .feature-item:hover {
            background: rgba(255, 255, 255, 0.13);
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

        /* ---------- Form Panel Kanan (Putih Bersih) ---------- */
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
            margin-bottom: 24px;
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
            margin-bottom: 24px;
        }
        .form-header svg {
            width: 52px;
            height: 52px;
            margin-bottom: 8px;
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(20, 86, 160, 0.2);
            background: linear-gradient(135deg, #1456A0 0%, #2878D0 100%);
            display: inline-block;
        }
        .form-header h2 {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--text);
        }
        .form-header small {
            color: var(--muted);
            font-size: .82rem;
            font-weight: 500;
        }

        /* Tabs Switch */
        .tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--light-blue);
            border-radius: 14px;
            padding: 4px;
            margin-bottom: 24px;
            position: relative;
        }
        .tabs .slider {
            position: absolute;
            top: 4px;
            bottom: 4px;
            left: 4px;
            width: calc(50% - 4px);
            background: var(--primary);
            border-radius: 11px;
            transition: transform .3s ease;
        }
        .tabs[data-active="register"] .slider {
            transform: translateX(100%);
        }
        .tab {
            position: relative;
            z-index: 1;
            border: 0;
            background: none;
            padding: 10px;
            font: inherit;
            font-weight: 700;
            font-size: .88rem;
            color: var(--muted);
            cursor: pointer;
            border-radius: 11px;
            transition: color .3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .tab.active {
            color: #FFFFFF;
        }

        .pane { display: none; }
        .pane.active { display: block; animation: fade .3s ease; }
        @keyframes fade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

        /* Form Inputs */
        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px 14px;
            background: rgba(20, 86, 160, 0.06);
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        .step-indicator .step-badge {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .step-indicator .step-dots {
            display: flex;
            gap: 6px;
        }
        .step-indicator .dot {
            width: 24px;
            height: 6px;
            border-radius: 3px;
            background: var(--border);
            transition: background 0.3s;
        }
        .step-indicator .dot.active {
            background: var(--primary);
        }
        .reg-step { display: none; }
        .reg-step.active { display: block; animation: fade .3s ease; }
        .info-badge-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 0.84rem;
            line-height: 1.4;
        }
        .info-badge-box.claim {
            background: #EBF8FF;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
        }
        .info-badge-box.join {
            background: #F0FDF4;
            color: #166534;
            border: 1px solid #BBF7D0;
        }
        .info-badge-box.warning {
            background: #FEF3C7;
            color: #92400E;
            border: 1px solid #FDE68A;
        }

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
        .control.area > i.lead { top: 16px; transform: none; }
        .control input, .control select, .control textarea {
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
        .control textarea { resize: vertical; min-height: 72px; }
        .control select { appearance: none; cursor: pointer; }
        .control input:focus, .control select:focus, .control textarea:focus {
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

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0 12px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0 12px; }

        .row-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 6px 0 20px;
            font-size: .86rem;
        }
        .check { display: flex; gap: 8px; align-items: center; cursor: pointer; }
        .check input { width: 16px; height: 16px; accent-color: var(--primary); }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 12px;
            font: inherit;
            font-weight: 700;
            font-size: .92rem;
            color: #fff;
            background: var(--primary);
            cursor: pointer;
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            transition: background .2s, transform .1s;
        }
        .btn:hover { background: var(--secondary); }
        .btn:active { transform: scale(.99); }
        .btn:disabled { opacity: .7; cursor: wait; }
        .btn.green { background: var(--green); }
        .btn.green:hover { background: #12704E; }
        .switch {
            text-align: center;
            margin-top: 20px;
            font-size: .86rem;
            color: var(--muted);
        }
        .switch button {
            border: 0;
            background: none;
            color: var(--secondary);
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        .alert {
            border-radius: 12px;
            padding: 11px 14px;
            margin-bottom: 18px;
            font-size: .85rem;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .alert.ok { background: var(--success-bg); color: var(--green); }
        .alert.err { background: var(--danger-bg); color: var(--danger); }
        .strength {
            height: 4px;
            border-radius: 4px;
            background: var(--border);
            margin-top: 6px;
            overflow: hidden;
        }
        .strength span { display: block; height: 100%; width: 0; transition: width .3s, background .3s; }

        /* ---------- Responsive Media Queries ---------- */
        @media (max-width: 900px) {
            body { padding: 16px 12px; }
            .auth-split-wrapper {
                grid-template-columns: 1fr;
                max-width: 480px;
            }
            .auth-brand-panel {
                display: none !important;
            }
            .auth-form-panel {
                padding: 32px 24px;
            }
        }
        @media (max-width: 520px) {
            .auth-form-panel { padding: 24px 18px; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

@php
$logoSvg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><defs><linearGradient id="portalHGrad" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse"><stop stop-color="#1456A0"/><stop offset="1" stop-color="#2878D0"/></linearGradient></defs><rect width="64" height="64" rx="16" fill="url(#portalHGrad)"/><path d="M16 19L32 10L48 19" stroke="#7DD3FC" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 23V46M44 23V46M20 34H44" stroke="#FFFFFF" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
@endphp

<main class="auth-split-wrapper {{ $activeTab === 'register' ? 'wide' : '' }}" id="authSplitWrapper">

    {{-- ======================================================================
         PANEL KIRI: BRANDING BIRU HAVEN SMART RW 013
         ====================================================================== --}}
    <section class="auth-brand-panel" aria-label="Portal Smart RW 013 Suryatmajan">
        <div class="brand-top">
            <div class="brand-logo-badge">
                {!! $logoSvg !!}
                <span>HAVEN PORTAL</span>
            </div>

            <h1>Safe Space, <span>Seamless Service</span></h1>
            <p class="desc">
                Selamat datang di Haven Portal. Portal digital terintegrasi warga RW 013 Suryatmajan Danurejan untuk kenyamanan, transparansi keuangan, dan pelayanan prima.
            </p>

            <div class="brand-features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="feature-text">
                        <strong>One Billing Terpadu</strong>
                        <small>Transparansi kas RT/RW, tagihan keluarga, &amp; kuitansi resmi otomatis.</small>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div class="feature-text">
                        <strong>Layanan Surat Online</strong>
                        <small>Pengajuan surat pengantar mandiri dengan QR Code verifikasi instan.</small>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div class="feature-text">
                        <strong>Keamanan &amp; CCTV Terverifikasi</strong>
                        <small>Monitoring CCTV teritorial berjenjang &amp; koordinasi siskamling 24 jam.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="brand-footer">
            <i class="fas fa-shield-halved" style="color: #6EE7B7;"></i>
            <span>Haven Portal &bull; havenportal.mehape.com &bull; RW 013 Suryatmajan</span>
        </div>
    </section>

    {{-- ======================================================================
         PANEL KANAN: FORM LOGIN & REGISTER BERSIH
         ====================================================================== --}}
    <section class="auth-form-panel">
        <div class="form-top-bar">
            <a href="{{ route('cover') }}" class="back-home" id="backHome">
                <i class="fas fa-arrow-left"></i>
                <span>Beranda</span>
            </a>
            <button class="theme-toggle" id="themeToggle" type="button" aria-label="Ganti mode gelap/terang">
                <i class="fas fa-moon"></i>
            </button>
        </div>

        <div class="form-header">
            {!! $logoSvg !!}
            <h2>Haven Portal</h2>
            <small style="font-weight: 600; letter-spacing: 0.3px; color: var(--secondary);">Safe Space, Seamless Service</small>
        </div>

        {{-- Switch Tab Masuk / Daftar Akun --}}
        <div class="tabs" id="tabs" data-active="{{ $activeTab }}" role="tablist">
            <span class="slider"></span>
            <button type="button" class="tab {{ $activeTab === 'login' ? 'active' : '' }}" id="tab-login" data-tab="login" role="tab">
                <i class="fas fa-lock"></i>
                <span>Masuk</span>
            </button>
            <button type="button" class="tab {{ $activeTab === 'register' ? 'active' : '' }}" id="tab-register" data-tab="register" role="tab">
                <i class="fas fa-user-plus"></i>
                <span>Daftar Akun</span>
            </button>
        </div>

        @if (session('status'))
            <div class="alert ok">
                <i class="fas fa-circle-check"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if (session('warning'))
            <div class="alert" style="background: rgba(245, 158, 11, 0.15); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.3);">
                <i class="fas fa-circle-exclamation"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @if (session('success'))
            <div class="alert ok">
                <i class="fas fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="alert err">
                <i class="fas fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ---------- TAB 1: FORM LOGIN ---------- --}}
        <div class="pane {{ $activeTab === 'login' ? 'active' : '' }}" id="pane-login" role="tabpanel">
            <form method="POST" action="{{ route('login') }}" class="js-submit" novalidate>
                @csrf
                <div class="field">
                    <label for="loginEmail">Email Terdaftar</label>
                    <div class="control">
                        <i class="fas fa-envelope lead"></i>
                        <input type="email" id="loginEmail" name="email" value="{{ $hasRegisterInput ? '' : old('email') }}"
                               class="@if(!$hasRegisterInput) @error('email') is-invalid @enderror @endif"
                               placeholder="nama@email.com" autocomplete="username" required>
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
                    <label class="check">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="{{ route('password.request') }}">Lupa password?</a>
                </div>

                <button type="submit" class="btn" id="btnLogin">
                    <i class="fas fa-right-to-bracket"></i>
                    <span>Masuk ke Akun</span>
                </button>
            </form>
            <p class="switch">Belum punya akun warga? <button type="button" data-tab="register">Daftar sekarang</button></p>
        </div>

        {{-- ---------- TAB 2: FORM REGISTER (2 TAHAP) ---------- --}}
        <div class="pane {{ $activeTab === 'register' ? 'active' : '' }}" id="pane-register" role="tabpanel">
            <form method="POST" action="{{ route('register') }}" class="js-submit" id="formRegister" novalidate>
                @csrf
                @php $re = fn($f) => $hasRegisterInput && $errors->has($f); @endphp
                <input type="hidden" name="reg_mode" id="regMode" value="{{ old('reg_mode', 'new') }}">
                <input type="hidden" name="claim_user_id" id="claimUserId" value="{{ old('claim_user_id') }}">

                <!-- Stepper Progress Header -->
                <div class="step-indicator">
                    <div class="step-badge" id="stepBadgeText">
                        <i class="fas fa-user-lock"></i>
                        <span>Langkah 1 dari 2: Buat Akun & Kredensial</span>
                    </div>
                    <div class="step-dots">
                        <div class="dot active" id="dotStep1"></div>
                        <div class="dot" id="dotStep2"></div>
                    </div>
                </div>

                {{-- TAHAP 1: KREDENSIAL AKUN --}}
                <div class="reg-step active" id="step1Container">
                    <div class="field">
                        <label for="regName">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
                        <div class="control">
                            <i class="fas fa-user lead"></i>
                            <input type="text" id="regName" name="name" value="{{ old('name') }}" class="{{ $re('name') ? 'is-invalid' : '' }}" placeholder="Nama lengkap sesuai e-KTP" required>
                        </div>
                        @if($re('name'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('name') }}</div>@endif
                    </div>

                    <div class="field">
                        <label for="regEmail">Alamat Email Aktif <span class="text-danger">*</span></label>
                        <div class="control">
                            <i class="fas fa-envelope lead"></i>
                            <input type="email" id="regEmail" name="email" value="{{ $hasRegisterInput ? old('email') : '' }}" class="{{ $re('email') ? 'is-invalid' : '' }}" placeholder="nama@email.com" required>
                        </div>
                        @if($re('email'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('email') }}</div>@endif
                        <small style="color: var(--muted); font-size: 0.76rem; display: block; margin-top: 4px;">Email ini akan digunakan untuk login mandiri dan menerima notifikasi resmi.</small>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="regPassword">Password <span class="text-danger">*</span></label>
                            <div class="control">
                                <i class="fas fa-lock lead"></i>
                                <input type="password" id="regPassword" name="password" class="{{ $re('password') ? 'is-invalid' : '' }}" placeholder="Min. 6 karakter" autocomplete="new-password" required>
                                <button type="button" class="eye" data-target="regPassword" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="strength"><span id="strengthBar"></span></div>
                            @if($re('password'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('password') }}</div>@endif
                        </div>
                        <div class="field">
                            <label for="regPasswordConfirm">Ulangi Password <span class="text-danger">*</span></label>
                            <div class="control">
                                <i class="fas fa-lock lead"></i>
                                <input type="password" id="regPasswordConfirm" name="password_confirmation" placeholder="Ulangi password" autocomplete="new-password" required>
                                <button type="button" class="eye" data-target="regPasswordConfirm" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="error" id="matchError" hidden><i class="fas fa-circle-exclamation"></i>Password tidak sama</div>
                        </div>
                    </div>

                    <button type="button" class="btn" id="btnNextStep" style="margin-top: 10px;">
                        <span>Lanjut ke Biodata KTP</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                {{-- TAHAP 2: BIODATA SESUAI KTP & AUTO-DETECT KELUARGA --}}
                <div class="reg-step" id="step2Container">
                    <!-- Dynamic Feedback Banner NIK / KK -->
                    <div id="nikKkAlertBox" style="display: none;"></div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="regNik">NIK (16 Digit Sesuai KTP) <span class="text-danger">*</span></label>
                            <div class="control">
                                <i class="fas fa-id-card lead"></i>
                                <input type="text" id="regNik" name="nik" value="{{ old('nik') }}" maxlength="16" pattern="[0-9]{16}" class="{{ $re('nik') ? 'is-invalid' : '' }}" placeholder="Contoh: 3275010101850001" required>
                            </div>
                            <div id="nikLoadingSpinner" style="display: none; font-size: 0.76rem; color: var(--secondary); margin-top: 4px;">
                                <i class="fas fa-circle-notch fa-spin"></i> Memeriksa data NIK di database RT/RW...
                            </div>
                            @if($re('nik'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('nik') }}</div>@endif
                        </div>

                        <div class="field">
                            <label for="regNoKk">Nomor KK (16 Digit) <span class="text-danger">*</span></label>
                            <div class="control">
                                <i class="fas fa-people-roof lead"></i>
                                <input type="text" id="regNoKk" name="no_kk" value="{{ old('no_kk') }}" maxlength="16" pattern="[0-9]{16}" class="{{ $re('no_kk') ? 'is-invalid' : '' }}" placeholder="Contoh: 3275100000000001" required>
                            </div>
                            <div id="kkLoadingSpinner" style="display: none; font-size: 0.76rem; color: var(--secondary); margin-top: 4px;">
                                <i class="fas fa-circle-notch fa-spin"></i> Mendeteksi Kartu Keluarga...
                            </div>
                            @if($re('no_kk'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('no_kk') }}</div>@endif
                        </div>
                    </div>

                    <div class="grid-3">
                        <div class="field">
                            <label for="regRtNumber">Wilayah RT <span class="text-danger">*</span></label>
                            <div class="control">
                                <i class="fas fa-map-location-dot lead"></i>
                                <select id="regRtNumber" name="rt_number" required>
                                    @for ($i = 1; $i <= 8; $i++)
                                        @php $rtStr = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $rtStr }}" @selected(old('rt_number', '001') == $rtStr)>RT {{ $rtStr }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="field">
                            <label for="regHouse">No. Rumah <span class="text-danger">*</span></label>
                            <div class="control">
                                <i class="fas fa-house lead"></i>
                                <input type="text" id="regHouse" name="house_number" value="{{ old('house_number') }}" class="{{ $re('house_number') ? 'is-invalid' : '' }}" placeholder="A-12" required>
                            </div>
                            @if($re('house_number'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('house_number') }}</div>@endif
                        </div>

                        <div class="field">
                            <label for="regStatus">Status Rumah <span class="text-danger">*</span></label>
                            <div class="control">
                                <i class="fas fa-building lead"></i>
                                <select id="regStatus" name="status_rumah" required>
                                    @foreach (['Milik Sendiri', 'Kontrak', 'Kos', 'Menumpang', 'Lainnya'] as $srOpt)
                                        <option value="{{ $srOpt }}" @selected(old('status_rumah', 'Milik Sendiri') === $srOpt)>{{ $srOpt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="regPhone">No. WhatsApp <span class="text-danger">*</span></label>
                            <div class="control">
                                <i class="fas fa-phone lead"></i>
                                <input type="text" id="regPhone" name="phone" value="{{ old('phone') }}" class="{{ $re('phone') ? 'is-invalid' : '' }}" placeholder="08xxxxxxxxxx" required>
                            </div>
                            @if($re('phone'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('phone') }}</div>@endif
                        </div>

                        <div class="field">
                            <label for="regHubungan">Hubungan Keluarga</label>
                            <div class="control">
                                <i class="fas fa-users lead"></i>
                                <select id="regHubungan" name="hubungan_keluarga">
                                    @foreach (['Kepala Keluarga', 'Istri', 'Suami', 'Anak', 'Orang Tua', 'Saudara', 'Famili Lain'] as $hkOpt)
                                        <option value="{{ $hkOpt }}" @selected(old('hubungan_keluarga', 'Kepala Keluarga') === $hkOpt)>{{ $hkOpt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label for="regAddress">Alamat Lengkap Domisili <span class="text-danger">*</span></label>
                        <div class="control area">
                            <i class="fas fa-location-dot lead"></i>
                            <textarea id="regAddress" name="address" rows="2" class="{{ $re('address') ? 'is-invalid' : '' }}" placeholder="Nama Jalan, Blok, RT/RW, Suryatmajan Danurejan" required>{{ old('address') }}</textarea>
                        </div>
                        @if($re('address'))<div class="error"><i class="fas fa-circle-exclamation"></i>{{ $errors->first('address') }}</div>@endif
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 16px;">
                        <button type="button" class="btn" id="btnPrevStep" style="width: 35%; background: transparent; color: var(--text); border: 1.5px solid var(--border);">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali</span>
                        </button>
                        <button type="submit" class="btn green" id="btnRegister" style="width: 65%;">
                            <i class="fas fa-user-check"></i>
                            <span>Selesaikan Pendaftaran</span>
                        </button>
                    </div>
                </div>
            </form>
            <p class="switch">Sudah memiliki akun? <button type="button" data-tab="login">Masuk di sini</button></p>
        </div>
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

    // Tab switching without reload; URL follows existing Laravel routes
    var urls = { login: @json(route('login')), register: @json(route('register')) };
    var tabs = document.getElementById('tabs'), wrapper = document.getElementById('authSplitWrapper');
    function show(tab) {
        tabs.dataset.active = tab;
        document.querySelectorAll('.tab').forEach(function (b) { b.classList.toggle('active', b.dataset.tab === tab); });
        document.querySelectorAll('.pane').forEach(function (p) { p.classList.toggle('active', p.id === 'pane-' + tab); });
        wrapper.classList.toggle('wide', tab === 'register');
        document.title = (tab === 'register' ? 'Daftar Akun' : 'Masuk') + ' | Haven Portal — Safe Space, Seamless Service';
        if (location.pathname !== '/') history.replaceState(null, '', urls[tab]);
    }
    document.querySelectorAll('[data-tab]').forEach(function (b) {
        b.addEventListener('click', function () { show(b.dataset.tab); });
    });

    document.querySelectorAll('.eye').forEach(function (b) {
        b.addEventListener('click', function () {
            var i = document.getElementById(b.dataset.target), shown = i.type === 'text';
            i.type = shown ? 'password' : 'text';
            b.innerHTML = shown ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    });

    var pw = document.getElementById('regPassword'), pc = document.getElementById('regPasswordConfirm'),
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
            // Pesan merah "Password tidak sama" HANYA muncul jika user sudah mengetik di field konfirmasi (tidak muncul saat kosong)
            if (pc.value.length === 0) {
                me.hidden = true;
            } else {
                me.hidden = (pc.value === pw.value);
            }
        }
    }
    if (pc && pw) {
        pc.addEventListener('input', checkMatch);
        pw.addEventListener('input', function() {
            if (pc.value.length > 0) checkMatch();
        });
    }

    // =========================================================================
    // 2-STEP REGISTRATION & AJAX AUTO-DETECT NIK / NOMOR KK
    // =========================================================================
    var step1 = document.getElementById('step1Container');
    var step2 = document.getElementById('step2Container');
    var btnNext = document.getElementById('btnNextStep');
    var btnPrev = document.getElementById('btnPrevStep');
    var dot1 = document.getElementById('dotStep1');
    var dot2 = document.getElementById('dotStep2');
    var badgeText = document.getElementById('stepBadgeText');
    var regName = document.getElementById('regName');
    var regEmail = document.getElementById('regEmail');
    var regNik = document.getElementById('regNik');
    var regNoKk = document.getElementById('regNoKk');
    var regRt = document.getElementById('regRtNumber');
    var regHouse = document.getElementById('regHouse');
    var regStatus = document.getElementById('regStatus');
    var regPhone = document.getElementById('regPhone');
    var regHubungan = document.getElementById('regHubungan');
    var regAddress = document.getElementById('regAddress');
    var regMode = document.getElementById('regMode');
    var claimUserId = document.getElementById('claimUserId');
    var nikAlertBox = document.getElementById('nikKkAlertBox');
    var nikSpinner = document.getElementById('nikLoadingSpinner');
    var kkSpinner = document.getElementById('kkLoadingSpinner');

    function goToStep(stepNum) {
        if (stepNum === 1) {
            if (step1) step1.classList.add('active');
            if (step2) step2.classList.remove('active');
            if (dot1) dot1.classList.add('active');
            if (dot2) dot2.classList.remove('active');
            if (badgeText) badgeText.innerHTML = '<i class="fas fa-user-lock"></i> <span>Langkah 1 dari 2: Buat Akun & Kredensial</span>';
        } else {
            if (step1) step1.classList.remove('active');
            if (step2) step2.classList.add('active');
            if (dot1) dot1.classList.add('active');
            if (dot2) dot2.classList.add('active');
            if (badgeText) badgeText.innerHTML = '<i class="fas fa-id-card"></i> <span>Langkah 2 dari 2: Biodata KTP & Auto-Detect KK</span>';
            // Trigger check jika NIK/KK sudah ada (misal dari old())
            if (regNik && regNik.value.length === 16) {
                triggerNikCheck();
            } else if (regNoKk && regNoKk.value.length === 16) {
                triggerKkCheck();
            }
        }
    }

    if (btnNext) {
        btnNext.addEventListener('click', function() {
            var isValid = true;
            // Validasi Step 1
            if (!regName.value.trim()) {
                regName.classList.add('is-invalid');
                regName.focus();
                isValid = false;
            } else {
                regName.classList.remove('is-invalid');
            }

            if (!regEmail.checkValidity() || !regEmail.value.trim()) {
                regEmail.classList.add('is-invalid');
                if (isValid) regEmail.focus();
                isValid = false;
            } else {
                regEmail.classList.remove('is-invalid');
            }

            if (!pw.value || pw.value.length < 6) {
                pw.classList.add('is-invalid');
                if (isValid) pw.focus();
                isValid = false;
            } else {
                pw.classList.remove('is-invalid');
            }

            if (!pc.value || pc.value !== pw.value) {
                pc.classList.add('is-invalid');
                if (me) me.hidden = false;
                if (isValid) pc.focus();
                isValid = false;
            } else {
                pc.classList.remove('is-invalid');
                if (me) me.hidden = true;
            }

            if (isValid) {
                goToStep(2);
            }
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', function() {
            goToStep(1);
        });
    }

    // Jika ada error pada NIK/KK/alamat dari submit sebelumnya (old inputs), buka step 2 langsung
    @if($hasRegisterInput && ($errors->has('nik') || $errors->has('no_kk') || $errors->has('house_number') || $errors->has('address') || $errors->has('phone')))
        goToStep(2);
    @endif

    // AJAX Lookup Handler
    var lookupTimer = null;
    var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

    function performNikKkCheck() {
        var nikVal = (regNik ? regNik.value.replace(/[^0-9]/g, '') : '');
        var kkVal = (regNoKk ? regNoKk.value.replace(/[^0-9]/g, '') : '');

        if (nikVal.length !== 16 && kkVal.length !== 16) {
            if (nikAlertBox) {
                nikAlertBox.style.display = 'none';
                nikAlertBox.innerHTML = '';
            }
            return;
        }

        if (nikVal.length === 16 && nikSpinner) nikSpinner.style.display = 'block';
        if (kkVal.length === 16 && kkSpinner) kkSpinner.style.display = 'block';

        fetch(@json(route('register.check-nik-kk')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nik: nikVal,
                no_kk: kkVal
            })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (nikSpinner) nikSpinner.style.display = 'none';
            if (kkSpinner) kkSpinner.style.display = 'none';

            if (!nikAlertBox) return;

            if (data.status === 'claim_existing') {
                // Skenario 1: NIK sudah didata RT tapi belum aktif
                regMode.value = 'claim_existing';
                claimUserId.value = data.user.id;

                nikAlertBox.className = 'info-badge-box claim';
                nikAlertBox.style.display = 'flex';
                nikAlertBox.innerHTML = '<i class="fas fa-circle-check" style="color: #2563EB; font-size: 1.1rem; margin-top: 2px;"></i>' +
                    '<div><strong>Data Kependudukan Teridentifikasi!</strong><br>' +
                    '<span>' + data.message + '</span></div>';

                // Auto-fill biodata
                if (data.user.name && !regName.value) regName.value = data.user.name;
                if (data.user.no_kk && regNoKk) regNoKk.value = data.user.no_kk;
                if (data.user.phone && regPhone && !regPhone.value) regPhone.value = data.user.phone;
                if (data.user.rt_number && regRt) regRt.value = data.user.rt_number;
                if (data.user.house_number && regHouse) regHouse.value = data.user.house_number;
                if (data.user.status_rumah && regStatus) regStatus.value = data.user.status_rumah;
                if (data.user.address && regAddress) regAddress.value = data.user.address;
                if (data.user.hubungan_keluarga && regHubungan) regHubungan.value = data.user.hubungan_keluarga;

            } else if (data.status === 'nik_exists_active') {
                // Skenario 2: NIK sudah aktif
                regMode.value = 'active_blocked';
                claimUserId.value = '';

                nikAlertBox.className = 'info-badge-box warning';
                nikAlertBox.style.display = 'flex';
                nikAlertBox.innerHTML = '<i class="fas fa-triangle-exclamation" style="color: #D97706; font-size: 1.1rem; margin-top: 2px;"></i>' +
                    '<div><strong>NIK Sudah Terdaftar & Aktif!</strong><br>' +
                    '<span>' + data.message + '</span> ' +
                    '<a href="' + urls.login + '" style="font-weight: 700; color: #B45309; text-decoration: underline; margin-left: 4px;">Masuk Sekarang &rarr;</a></div>';

            } else if (data.status === 'join_kk') {
                // Skenario 3: NIK baru, tapi No KK sudah ada (Join KK)
                regMode.value = 'join_kk';
                claimUserId.value = '';

                nikAlertBox.className = 'info-badge-box join';
                nikAlertBox.style.display = 'flex';
                nikAlertBox.innerHTML = '<i class="fas fa-people-roof" style="color: #16A34A; font-size: 1.1rem; margin-top: 2px;"></i>' +
                    '<div><strong>Terhubung ke Kartu Keluarga Terdaftar!</strong><br>' +
                    '<span>' + data.message + ' Domisili keluarga otomatis terisi. Lengkapi biodata Anda di bawah ini.</span></div>';

                // Auto-fill KK domisili
                if (data.kk.rt && regRt) regRt.value = data.kk.rt;
                if (data.kk.house_number && regHouse) regHouse.value = data.kk.house_number;
                if (data.kk.status_rumah && regStatus) regStatus.value = data.kk.status_rumah;
                if (data.kk.address && regAddress) regAddress.value = data.kk.address;
                if (regHubungan && regHubungan.value === 'Kepala Keluarga') regHubungan.value = 'Anggota Keluarga';

            } else {
                // Skenario 4: NIK & KK baru
                regMode.value = 'new';
                claimUserId.value = '';

                nikAlertBox.className = 'info-badge-box';
                nikAlertBox.style.display = 'none';
                nikAlertBox.innerHTML = '';
            }
        })
        .catch(function(err) {
            if (nikSpinner) nikSpinner.style.display = 'none';
            if (kkSpinner) kkSpinner.style.display = 'none';
        });
    }

    function triggerNikCheck() {
        clearTimeout(lookupTimer);
        lookupTimer = setTimeout(performNikKkCheck, 400);
    }

    function triggerKkCheck() {
        clearTimeout(lookupTimer);
        lookupTimer = setTimeout(performNikKkCheck, 400);
    }

    if (regNik) {
        regNik.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length === 16) {
                triggerNikCheck();
            } else if (this.value.length < 16 && regMode.value === 'claim_existing') {
                regMode.value = 'new';
                claimUserId.value = '';
                if (nikAlertBox) nikAlertBox.style.display = 'none';
            }
        });
    }

    if (regNoKk) {
        regNoKk.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length === 16 && (!regNik || regNik.value.length !== 16)) {
                triggerKkCheck();
            }
        });
    }

    // Light client-side validation feedback; server validation remains authoritative
    document.querySelectorAll('form.js-submit').forEach(function (f) {
        f.addEventListener('submit', function (e) {
            // Jika form register sedang di submit
            if (f.id === 'formRegister') {
                if (regMode.value === 'active_blocked') {
                    e.preventDefault();
                    alert('NIK ini sudah memiliki akun aktif. Silakan menuju halaman Masuk/Login.');
                    return;
                }
            }

            var bad = null;
            f.querySelectorAll('[required]').forEach(function (el) {
                // Abaikan input pada step yang tersembunyi
                if (el.offsetParent === null) return;

                var ok = el.checkValidity();
                el.classList.toggle('is-invalid', !ok);
                if (!ok && !bad) bad = el;
            });
            if (pc && pw && f.contains(pc) && pc.value !== pw.value) {
                pc.classList.add('is-invalid');
                if (me) me.hidden = false;
                bad = bad || pc;
            }
            if (bad) {
                e.preventDefault();
                bad.focus();
                return;
            }
            var btn = f.querySelector('button[type=submit]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';
            }
        });
    });
})();
</script>
</body>
</html>