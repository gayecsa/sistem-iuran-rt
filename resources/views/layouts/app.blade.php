<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>La Caisse</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --soft-pink: #f8d4e4;
            --soft-blue: #c7e3ff;
            --deep-pink: #f5a6c5;
            --deep-blue: #7db7ff;
            /* Mengubah opacity dasar agar efek kaca transparan muncul */
            --surface: rgba(255, 255, 255, 0.45); 
            --surface-strong: rgba(255, 255, 255, 0.7);
            --text-strong: #27314a;
            --shadow: 0 20px 50px rgba(46, 61, 94, 0.06);
        }
        
        body {
            background: linear-gradient(135deg, #f9d8e5 0%, #d3e8ff 45%, #a7d7ff 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: var(--text-strong);
        }
        
        .navbar {
            background: rgba(255,255,255,0.85) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: 0 12px 30px rgba(45, 62, 88, 0.06);
        }
        
        .card {
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
            border-radius: 22px;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* Efek Glassmorphism halus */
            background: rgba(255, 255, 255, 0.5) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 25px 60px rgba(46, 61, 94, 0.1);
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
        
        /* Merapikan gradasi transparan banner utama */
        .hero-banner {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.65), rgba(255, 243, 252, 0.45)) !important;
        }
        
        /* Merapikan panel samping */
        .sideboard-panel {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.65), rgba(251, 240, 252, 0.55)) !important;
            border: 1px solid rgba(255,255,255,0.6) !important;
        }
        
        /* Mengembalikan warna kotak kecil (saldo, pemasukan, pengeluaran) agar putih semi-transparan tipis */
        .metric-card {
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.45) !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            box-shadow: 0 8px 24px rgba(46, 61, 94, 0.04);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
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
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 12px 30px rgba(46, 61, 94, 0.05);
            color: var(--deep-pink);
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
            background: rgba(255, 255, 255, 0.8);
            color: var(--text-strong);
            border: 1px solid rgba(255,255,255,0.7);
        }
        
        .btn-soft:hover {
            background: rgba(255, 255, 255, 0.95);
        }
        
        .sideboard-nav .nav-link {
            border-radius: 15px;
            margin-bottom: 12px;
            color: #4b4c6d;
            background: rgba(255,255,255,0.7);
            border: 1px solid rgba(255,255,255,0.6);
        }
        
        .sideboard-nav .nav-link.active,
        .sideboard-nav .nav-link:hover {
            background: linear-gradient(135deg, #ffd4e6, #c8e7ff);
            color: #1b2843;
        }
        
        .chart-card {
            min-height: 320px;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.65), rgba(233, 240, 255, 0.55)) !important;
        }
        
        .recent-list .list-group-item {
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,0.7);
            margin-bottom: 12px;
            background: rgba(255,255,255,0.65);
        }
        
        .recent-list .list-group-item:last-child {
            margin-bottom: 0;
        }
        
        .footer {
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: #4d5673;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            border-top: 1px solid rgba(255,255,255,0.5);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="fas fa-hand-holding-usd me-2"></i>
                Sistem Iuran RT 001
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
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
            <p class="mb-0">&copy; 2024 Sistem Informasi Iuran RT 001. All rights reserved.</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Auto hide alert after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 3000);
    </script>
    
    @stack('scripts')
</body>
</html>