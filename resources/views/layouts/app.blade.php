<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Warkas Machi</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --soft-pink: #f8d4e4;
            --soft-blue: #c7e3ff;
            --deep-pink: #f5a6c5;
            --deep-blue: #7db7ff;
            --surface: rgba(255, 255, 255, 0.92);
            --surface-strong: rgba(255, 255, 255, 1);
            --text-strong: #27314a;
            --shadow: 0 25px 60px rgba(46, 61, 94, 0.12);
        }
        
        body {
            background: linear-gradient(135deg, #f9d8e5 0%, #d3e8ff 45%, #a7d7ff 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: var(--text-strong);
        }
        
        .navbar {
            background: rgba(255,255,255,0.96) !important;
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 35px rgba(45, 62, 88, 0.12);
        }
        
        .card {
            border: none;
            border-radius: 22px;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: var(--surface);
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 28px 70px rgba(46, 61, 94, 0.15);
        }
        
        .btn {
            border-radius: 999px;
            transition: all 0.25s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 30px rgba(46, 61, 94, 0.12);
        }
        
        .dashboard-shell {
            min-height: calc(100vh - 110px);
        }
        
        .hero-banner {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 243, 252, 0.9));
        }
        
        .sideboard-panel {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(251, 240, 252, 0.98));
            border: 1px solid rgba(255,255,255,0.7);
        }
        
        .metric-card {
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 230, 245, 0.95));
            border: 1px solid rgba(255,255,255,0.8);
            box-shadow: 0 12px 30px rgba(46, 61, 94, 0.08);
        }
        
        .metric-card strong {
            color: #2a3a59;
        }
        
        .soft-gradient {
            background: linear-gradient(135deg, var(--soft-pink), var(--soft-blue));
            color: #1f324f;
        }
        
        .gradient-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
            font-weight: 600;
            background: linear-gradient(135deg, #f9bfce, #a9d7ff);
            color: #2a3a59;
            box-shadow: 0 10px 24px rgba(46, 61, 94, 0.08);
        }
        
        .icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            box-shadow: 0 16px 40px rgba(46, 61, 94, 0.08);
            color: var(--deep-pink);
        }

        .brand-logo {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(125, 183, 255, 0.16);
            box-shadow: 0 10px 24px rgba(46, 61, 94, 0.08);
        }

        .brand-logo svg {
            width: 20px;
            height: 20px;
        }

        .brand-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(125, 183, 255, 0.16);
            box-shadow: 0 18px 40px rgba(46, 61, 94, 0.08);
        }

        .brand-icon svg {
            width: 40px;
            height: 40px;
        }

        .profile-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 0.65rem;
        }
        
        .bg-pink {
            background-color: var(--deep-pink) !important;
        }
        
        .text-pink {
            color: var(--deep-pink) !important;
        }
        
        .btn-soft {
            background: rgba(255, 255, 255, 0.92);
            color: var(--text-strong);
            border: 1px solid rgba(255,255,255,0.9);
        }
        
        .btn-soft:hover {
            background: rgba(255, 255, 255, 1);
        }
        
        .sideboard-nav .nav-link {
            border-radius: 15px;
            margin-bottom: 12px;
            color: #4b4c6d;
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(255,255,255,0.85);
        }
        
        .sideboard-nav .nav-link.active,
        .sideboard-nav .nav-link:hover {
            background: linear-gradient(135deg, #ffd4e6, #c8e7ff);
            color: #1b2843;
        }
        
        .chart-card {
            min-height: 320px;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.98), rgba(233, 240, 255, 0.95));
        }
        
        .recent-list .list-group-item {
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,0.85);
            margin-bottom: 12px;
            background: rgba(255,255,255,0.92);
        }
        
        .recent-list .list-group-item:last-child {
            margin-bottom: 0;
        }

        .service-card {
            border: 1px solid rgba(255,255,255,0.85);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(237, 250, 242, 0.96));
        }

        /* Floating Speed Dial Contact Widget */
        .floating-contact-container {
            position: fixed;
            right: 1.5rem;
            bottom: 1.5rem;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.65rem;
        }

        .contact-bubble-item {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 1rem;
            border-radius: 999px;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.88rem;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(10px);
        }

        .whatsapp-bubble {
            background: #25d366;
            box-shadow: 0 12px 28px rgba(37, 211, 102, 0.35);
            cursor: pointer;
            user-select: none;
        }

        .whatsapp-bubble:hover {
            background: #20bd5a;
            transform: translateY(-2px);
        }

        .telegram-bubble {
            background: #0088cc;
            box-shadow: 0 12px 28px rgba(0, 136, 204, 0.35);
            opacity: 0;
            visibility: hidden;
            transform: translateY(25px) scale(0.75);
            pointer-events: none;
        }

        .telegram-bubble:hover {
            background: #0077b5;
            transform: translateY(-3px) scale(1.03);
        }

        .floating-contact-container.expanded .telegram-bubble {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        .floating-contact-container.expanded .whatsapp-bubble {
            transform: translateY(-2px);
        }

        .bubble-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .bubble-toggle-arrow {
            background: rgba(255, 255, 255, 0.25);
            border: none;
            color: #ffffff;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            margin-left: 0.2rem;
            transition: transform 0.3s ease, background 0.2s ease;
        }

        .bubble-toggle-arrow:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        .floating-contact-container.expanded #arrowToggleIcon {
            transform: rotate(180deg);
        }
                min-width: 140px;
                padding: 0.45rem 0.7rem;
            }

            .whatsapp-float .whatsapp-text {
                display: none;
            }
        }
        
        .footer {
            background: rgba(255,255,255,0.85);
            color: #4d5673;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            border-top: 1px solid rgba(255,255,255,0.9);
        }

        /* Dark Mode CSS Rules - Pitch Dark OLED Charcoal */
        body.dark-mode {
            background: #090c15 !important;
            color: #f8fafc !important;
        }

        body.dark-mode .cover-container {
            background: #090c15 !important;
        }

        body.dark-mode .card,
        body.dark-mode .cover-card,
        body.dark-mode .navbar,
        body.dark-mode .footer,
        body.dark-mode .dropdown-menu {
            background: #111726 !important;
            color: #f8fafc !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.7) !important;
        }

        body.dark-mode .hero-banner,
        body.dark-mode .sideboard-panel {
            background: #111726 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        body.dark-mode .navbar-brand,
        body.dark-mode .nav-link {
            color: #f8fafc !important;
        }

        body.dark-mode .feature-box,
        body.dark-mode .info-footer-box {
            background: #1a2337 !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        body.dark-mode .feature-box h6,
        body.dark-mode .feature-box p,
        body.dark-mode .feature-box .text-dark,
        body.dark-mode .feature-box .text-muted {
            color: #f8fafc !important;
        }

        body.dark-mode .text-dark,
        body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, 
        body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 {
            color: #f8fafc !important;
        }

        body.dark-mode .text-muted, 
        body.dark-mode .text-secondary {
            color: #94a3b8 !important;
        }

        body.dark-mode .bg-light {
            background: #182032 !important;
            color: #f8fafc !important;
        }

        body.dark-mode .bg-white {
            background: #111726 !important;
            color: #f8fafc !important;
        }

        body.dark-mode .border {
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select,
        body.dark-mode .input-group-text {
            background-color: #161e2e !important;
            color: #ffffff !important;
            border-color: #2d394e !important;
        }

        body.dark-mode .form-control::placeholder {
            color: #64748b !important;
        }

        body.dark-mode .btn-outline-secondary {
            background-color: #161e2e !important;
            color: #94a3b8 !important;
            border-color: #2d394e !important;
        }

        body.dark-mode .table {
            color: #f8fafc !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        body.dark-mode .table th,
        body.dark-mode .table td {
            background-color: transparent !important;
            color: #f8fafc !important;
        }

        body.dark-mode .modal-content {
            background-color: #111726 !important;
            color: #f8fafc !important;
        }

        body.dark-mode .list-group-item {
            background-color: #182032 !important;
            color: #f8fafc !important;
        }

        body.dark-mode .btn-light {
            background-color: #1a2337 !important;
            color: #f8fafc !important;
            border-color: #2d394e !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}">
                <span class="brand-logo me-2">
                    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="32" cy="32" r="28" fill="currentColor" opacity="0.12" />
                        <path d="M23 24h18a6 6 0 0 1 6 6v4a6 6 0 0 1-6 6H23v-4h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H23v-4Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M25 30c0-4 3-7 7-7h5c4 0 7 3 7 7s-3 7-7 7h-5c-4 0-7-3-7-7Z" fill="currentColor" opacity="0.4" />
                        <path d="M28 33h8m-4-4v8" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </span>
                Warkas Machi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center flex-row gap-2">
                    @auth
                        @if(in_array(Auth::user()->role, ['admin', 'bendahara']))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('warga.index') }}">
                                    <i class="fas fa-users me-1"></i>Data Warga
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=F9D8E5&color=4B3A5C' }}"
                                    alt="Avatar" class="profile-avatar">
                                <span>
                                    {{ Auth::user()->name }}
                                    <small class="text-muted">({{ ucfirst(Auth::user()->role) }})</small>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user me-2"></i>Profil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth

                    <!-- Tombol Toggle Dark/Light Mode di Pojok Atas Kanan -->
                    <li class="nav-item ms-2">
                        <button id="themeToggleBtn" onclick="toggleTheme()" class="btn btn-sm btn-light rounded-circle shadow-sm border p-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Ubah Mode Terang / Gelap">
                            <i class="fas fa-moon text-secondary" id="themeToggleIcon"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5 pt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                Terdapat kesalahan pada input Anda.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
    
    <div class="footer">
        <div class="container">
            <p class="mb-0">&copy; 2024 Warkas Machi. All rights reserved.</p>
        </div>
    </div>
    
    <!-- Floating Contact Speed Dial (WhatsApp & Telegram) -->
    <div class="floating-contact-container" id="floatingContactContainer">
        <!-- Bubble Telegram - Muncul di atas WhatsApp saat diklik -->
        <a href="https://t.me/icrvye" target="_blank" rel="noopener noreferrer" class="contact-bubble-item telegram-bubble" id="telegramBubble">
            <span class="bubble-icon-box">
                <i class="fab fa-telegram-plane"></i>
            </span>
            <span class="bubble-label-text">Telegram</span>
        </a>

        <!-- Bubble WhatsApp (Utama) -->
        <div class="contact-bubble-item whatsapp-bubble" id="whatsappBubble" onclick="toggleContactBubbles(event)">
            <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="whatsapp-link-area text-white text-decoration-none d-flex align-items-center gap-2" onclick="event.stopPropagation();">
                <span class="bubble-icon-box">
                    <i class="fab fa-whatsapp"></i>
                </span>
                <span class="bubble-label-text">WhatsApp</span>
            </a>
            <button type="button" class="bubble-toggle-arrow" title="Buka Kontak Telegram">
                <i class="fas fa-chevron-up" id="arrowToggleIcon"></i>
            </button>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Auto hide alert after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 3000);

        function toggleContactBubbles(e) {
            if (e) e.stopPropagation();
            const container = document.getElementById('floatingContactContainer');
            container.classList.toggle('expanded');
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('floatingContactContainer');
            if (container && !container.contains(e.target)) {
                container.classList.remove('expanded');
            }
        });

        // Global Password Visibility Toggle (Show / Hide Password)
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash text-primary';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // Functions Theme Toggle (Dark / Light Mode)
        function applyTheme(theme) {
            const icon = document.getElementById('themeToggleIcon');
            if (theme === 'dark') {
                document.body.classList.add('dark-mode');
                if (icon) {
                    icon.className = 'fas fa-sun text-warning';
                }
            } else {
                document.body.classList.remove('dark-mode');
                if (icon) {
                    icon.className = 'fas fa-moon text-secondary';
                }
            }
        }

        function toggleTheme() {
            const isDark = document.body.classList.contains('dark-mode');
            const newTheme = isDark ? 'light' : 'dark';
            localStorage.setItem('warkas_theme', newTheme);
            applyTheme(newTheme);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('warkas_theme') || 'light';
            applyTheme(savedTheme);
        });
    </script>
    
    @stack('scripts')
</body>
</html>