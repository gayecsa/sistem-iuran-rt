<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haven Portal — Safe Space, Seamless Service</title>
    <meta name="description" content="Haven Portal – Safe Space, Seamless Service. Sistem Informasi dan Layanan Terpadu RW 013 Sukamiskin Arcamanik Bandung. One Billing, Surat Digital, Ronda CCTV, dan Pelayanan Warga.">
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
            --green-light: #E8F6EF;
            --warning: #D97706;
            --warning-light: #FEF3C7;
            --bg: #F4F7FB;
            --card: #FFFFFF;
            --text: #172B4D;
            --muted: #5E6C84;
            --border: #DCE4EE;
            --shadow: 0 10px 30px rgba(20, 86, 160, 0.08);
            --shadow-card: 0 4px 16px rgba(15, 23, 42, 0.06);
            --shadow-hover: 0 12px 28px rgba(20, 86, 160, 0.14);
        }
        [data-theme="dark"] {
            --primary-dark: #0A1E33;
            --light-blue: #1A2A44;
            --bg: #0F1724;
            --text: #E6EDF7;
            --muted: #9AA8BC;
            --border: #2A3A52;
            --card: #16213A;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            --shadow-card: 0 4px 16px rgba(0, 0, 0, 0.3);
            --shadow-hover: 0 12px 28px rgba(0, 0, 0, 0.5);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background .3s, color .3s;
            line-height: 1.6;
        }

        /* ---------- Navbar ---------- */
        .navbar-wrap {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            transition: background .3s, border-color .3s;
        }
        [data-theme="dark"] .navbar-wrap {
            background: rgba(22, 33, 58, 0.92);
        }
        .navbar {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 14px clamp(16px, 4vw, 56px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo svg { width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; box-shadow: 0 4px 12px rgba(20, 86, 160, 0.18); }
        .logo strong { display: block; font-size: 1.18rem; color: var(--primary); font-weight: 800; line-height: 1.2; }
        [data-theme="dark"] .logo strong { color: #7DB4F5; }
        .logo small { color: var(--muted); font-size: .76rem; font-weight: 500; }

        .nav-links {
            display: flex;
            gap: 24px;
            align-items: center;
            list-style: none;
        }
        .nav-links a {
            color: var(--text);
            text-decoration: none;
            font-weight: 600;
            font-size: .9rem;
            transition: color .2s;
            padding: 6px 4px;
        }
        .nav-links a:hover {
            color: var(--secondary);
        }

        .nav-actions { display: flex; gap: 10px; align-items: center; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 12px;
            font: inherit;
            font-weight: 700;
            font-size: .88rem;
            text-decoration: none;
            border: 1.5px solid transparent;
            cursor: pointer;
            transition: all .2s;
            white-space: nowrap;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--secondary); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(20, 86, 160, 0.25); }
        .btn-outline { border-color: var(--primary); color: var(--primary); background: transparent; }
        [data-theme="dark"] .btn-outline { color: #7DB4F5; border-color: #7DB4F5; }
        .btn-outline:hover { background: var(--light-blue); transform: translateY(-2px); }
        .btn-soft { background: var(--light-blue); color: var(--primary); }
        [data-theme="dark"] .btn-soft { background: rgba(40, 120, 208, 0.15); color: #7DB4F5; }
        .btn-soft:hover { background: rgba(40, 120, 208, 0.22); transform: translateY(-2px); }
        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: var(--card);
            color: var(--text);
            cursor: pointer;
            display: grid;
            place-items: center;
            transition: all .2s;
        }
        .icon-btn:hover { border-color: var(--secondary); color: var(--secondary); transform: rotate(15deg); }

        /* ---------- Container & Section Layout ---------- */
        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 0 clamp(16px, 4vw, 56px);
        }
        section.page-section {
            padding: 64px 0;
        }
        .section-header {
            text-align: center;
            max-width: 780px;
            margin: 0 auto 48px;
        }
        .section-tag {
            display: inline-block;
            background: var(--light-blue);
            color: var(--primary);
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 20px;
            margin-bottom: 12px;
            border: 1px solid rgba(40, 120, 208, 0.2);
        }
        [data-theme="dark"] .section-tag {
            background: rgba(40, 120, 208, 0.18);
            color: #7DB4F5;
        }
        .section-header h2 {
            font-size: clamp(1.8rem, 3vw, 2.4rem);
            font-weight: 800;
            color: var(--text);
            line-height: 1.25;
            margin-bottom: 12px;
        }
        .section-header p {
            color: var(--muted);
            font-size: 1rem;
        }

        /* ---------- Hero Section ---------- */
        .hero {
            display: grid;
            grid-template-columns: 1.25fr 0.95fr;
            gap: clamp(28px, 4vw, 56px);
            align-items: center;
            padding: 48px 0 60px;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--green-light);
            color: var(--green);
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 6px 14px;
            border-radius: 20px;
            margin-bottom: 18px;
            border: 1px solid rgba(22, 134, 93, 0.25);
        }
        .hero h1 {
            font-size: clamp(2.3rem, 4.2vw, 3.5rem);
            line-height: 1.15;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 8px;
        }
        .hero h1 span {
            color: var(--secondary);
            display: block;
        }
        .underline {
            width: 120px;
            height: 5px;
            border-radius: 4px;
            background: #F2B33D;
            margin: 12px 0 20px;
        }
        .hero p.lead {
            color: var(--muted);
            line-height: 1.7;
            font-size: 1.05rem;
            max-width: 680px;
            margin-bottom: 28px;
        }
        .cta-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 34px;
        }
        .pillars {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        .pillar {
            text-align: center;
            font-size: .78rem;
            color: var(--muted);
            font-weight: 600;
        }
        .pillar i {
            display: grid;
            place-items: center;
            width: 40px;
            height: 40px;
            margin: 0 auto 6px;
            border-radius: 12px;
            background: var(--card);
            box-shadow: var(--shadow-card);
            font-size: 1rem;
            border: 1px solid var(--border);
        }
        .hero-art img {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
            border-radius: 28px;
            box-shadow: 0 16px 40px rgba(20, 86, 160, 0.12);
            border: 2px solid var(--card);
            display: block;
        }

        /* ---------- Stats Strip Section ---------- */
        .stats-section {
            background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%);
            border-radius: 24px;
            padding: 40px 32px;
            color: #FFFFFF;
            box-shadow: 0 12px 32px rgba(20, 86, 160, 0.22);
            margin-bottom: 60px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 18px;
            padding: 24px 20px;
            text-align: center;
            transition: all .2s;
        }
        .stat-card:hover {
            background: rgba(255, 255, 255, 0.16);
            transform: translateY(-3px);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            display: grid;
            place-items: center;
            margin: 0 auto 12px;
            font-size: 1.25rem;
            color: #FFFFFF;
        }
        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 4px;
            color: #FFFFFF;
        }
        .stat-label {
            font-size: .86rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
        }
        .stat-sub {
            font-size: .72rem;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 2px;
        }

        /* ---------- Services Grid ---------- */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .service-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px 24px;
            box-shadow: var(--shadow-card);
            transition: all .25s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .service-card:hover {
            transform: translateY(-4px);
            border-color: var(--secondary);
            box-shadow: var(--shadow-hover);
        }
        .service-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 1.35rem;
            margin-bottom: 18px;
        }
        .service-tag {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: inline-block;
        }
        .service-card h3 {
            font-size: 1.18rem;
            font-weight: 800;
            margin-bottom: 10px;
            color: var(--text);
            line-height: 1.3;
        }
        .service-card p {
            color: var(--muted);
            font-size: .88rem;
            line-height: 1.6;
            margin-bottom: 20px;
            flex: 1;
        }
        .service-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--secondary);
            font-weight: 700;
            font-size: .88rem;
            text-decoration: none;
            transition: gap .2s;
        }
        .service-link:hover {
            gap: 10px;
            text-decoration: none;
        }

        /* ---------- Announcements Section ---------- */
        .announcements-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .announcement-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-card);
            transition: all .25s ease;
            display: flex;
            flex-direction: column;
        }
        .announcement-card:hover {
            transform: translateY(-4px);
            border-color: var(--secondary);
            box-shadow: var(--shadow-hover);
        }
        .announcement-media {
            height: 170px;
            position: relative;
            background: linear-gradient(135deg, #0F3966, #1456A0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            overflow: hidden;
        }
        .announcement-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .announcement-badge-abs {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(255, 255, 255, 0.95);
            color: var(--primary);
            font-size: .72rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .announcement-body {
            padding: 22px 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .announcement-meta {
            font-size: .76rem;
            color: var(--muted);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .announcement-body h4 {
            font-size: 1.08rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 10px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .announcement-body p {
            color: var(--muted);
            font-size: .86rem;
            line-height: 1.55;
            margin-bottom: 18px;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ---------- Footer Section ---------- */
        footer.footer-main {
            background: var(--card);
            border-top: 1px solid var(--border);
            padding: 56px 0 24px;
            margin-top: auto;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.4fr 1.1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .footer-col h5 {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 16px;
        }
        .footer-col p {
            color: var(--muted);
            font-size: .88rem;
            line-height: 1.65;
            margin-bottom: 14px;
        }
        .footer-list {
            list-style: none;
            padding: 0;
            font-size: .88rem;
            color: var(--muted);
        }
        .footer-list li {
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.5;
        }
        .footer-list li i {
            color: var(--secondary);
            margin-top: 4px;
            flex-shrink: 0;
        }
        .contact-emergency-box {
            background: var(--light-blue);
            border: 1px solid rgba(40, 120, 208, 0.2);
            border-radius: 14px;
            padding: 14px 16px;
            margin-top: 10px;
        }
        [data-theme="dark"] .contact-emergency-box {
            background: rgba(40, 120, 208, 0.12);
        }
        .footer-bottom {
            padding-top: 24px;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--muted);
            font-size: .82rem;
        }

        /* ---------- Responsive Media Queries ---------- */
        @media (max-width: 992px) {
            .hero {
                grid-template-columns: 1fr;
                text-align: center;
                padding-top: 24px;
            }
            .hero p.lead, .underline, .hero-badge {
                margin-left: auto;
                margin-right: auto;
            }
            .cta-group {
                justify-content: center;
            }
            .hero-art {
                order: -1;
                max-width: 520px;
                margin: 0 auto;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .services-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .announcements-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
            .nav-links {
                display: none;
            }
        }

        /* Floating Telegram Button */
        .haven-floating-telegram {
            position: fixed;
            bottom: 26px;
            right: 22px;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2AABEE 0%, #229ED9 100%);
            color: #FFFFFF !important;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 18px rgba(34, 158, 217, 0.42);
            z-index: 1035;
            text-decoration: none !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .haven-floating-telegram:hover {
            transform: scale(1.08) translateY(-2px);
            box-shadow: 0 6px 22px rgba(34, 158, 217, 0.55);
            color: #FFFFFF !important;
        }
        .haven-floating-telegram:active {
            transform: scale(0.95);
        }

        /* ---------- Mobile Compact Styles (< 768px) - TANPA MENGUBAH DESKTOP >= 768px ---------- */
        @media (max-width: 767.98px) {
            /* 1. Navbar Mobile: Logo + Haven Portal + Masuk + Mode Muat Rapi 1 Baris */
            .navbar {
                padding: 10px 14px !important;
                gap: 8px !important;
            }
            .logo {
                gap: 8px !important;
                min-width: 0 !important;
                flex: 1 1 auto !important;
            }
            .logo svg {
                width: 34px !important;
                height: 34px !important;
                border-radius: 10px !important;
            }
            .logo strong {
                font-size: 1.05rem !important;
                white-space: nowrap !important;
            }
            .logo small {
                display: none !important; /* Sembunyikan slogan panjang agar tidak wrap bertumpuk di mobile */
            }
            .nav-actions {
                gap: 6px !important;
                flex-shrink: 0 !important;
            }
            .nav-actions .btn {
                padding: 6px 12px !important;
                font-size: 0.8rem !important;
                border-radius: 9px !important;
                gap: 5px !important;
            }
            .icon-btn {
                width: 34px !important;
                height: 34px !important;
                font-size: 0.85rem !important;
            }

            /* 2. Container & Spacing Umum */
            .container {
                padding: 0 14px !important;
            }
            section.page-section {
                padding: 28px 0 !important;
            }
            .section-header {
                margin-bottom: 20px !important;
            }
            .section-tag {
                font-size: 0.65rem !important;
                padding: 4px 10px !important;
                margin-bottom: 6px !important;
            }
            .section-header h2 {
                font-size: 1.25rem !important;
                line-height: 1.3 !important;
                margin-bottom: 6px !important;
            }
            .section-header p {
                font-size: 0.8rem !important;
                line-height: 1.4 !important;
            }

            /* 3. Hero Section */
            .hero {
                padding: 14px 0 20px !important;
                gap: 14px !important;
            }
            .hero-badge {
                font-size: 0.65rem !important;
                padding: 4px 10px !important;
                margin-bottom: 8px !important;
            }
            .hero h1 {
                font-size: 1.45rem !important;
                line-height: 1.22 !important;
                margin-bottom: 4px !important;
            }
            .underline {
                width: 48px !important;
                height: 3px !important;
                margin: 6px auto 10px !important;
            }
            .hero p.lead {
                font-size: 0.82rem !important;
                line-height: 1.45 !important;
                margin-bottom: 14px !important;
            }
            .cta-group {
                gap: 8px !important;
                margin-bottom: 16px !important;
            }
            .cta-group .btn {
                padding: 8px 14px !important;
                font-size: 0.8rem !important;
                border-radius: 10px !important;
                flex: 1 1 calc(50% - 6px);
                max-width: 175px;
                justify-content: center !important;
            }
            .cta-group .btn-soft {
                flex: 1 1 100% !important;
                max-width: 240px !important;
            }
            .hero-art {
                max-width: 100% !important;
                margin: 0 auto 4px !important;
            }
            .hero-art img {
                max-height: 155px !important;
                border-radius: 14px !important;
            }
            .pillars {
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 6px !important;
                padding-top: 10px !important;
            }
            .pillar {
                font-size: 0.65rem !important;
                line-height: 1.2 !important;
            }
            .pillar i {
                width: 32px !important;
                height: 32px !important;
                font-size: 0.82rem !important;
                margin: 0 auto 4px !important;
                border-radius: 8px !important;
            }

            /* 4. Strip Statistik Biru: Grid 2x2 Ringkas */
            .stats-section {
                padding: 16px 12px !important;
                border-radius: 16px !important;
                margin-bottom: 26px !important;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 8px !important;
            }
            .stat-card {
                padding: 10px 8px !important;
                border-radius: 12px !important;
            }
            .stat-icon {
                width: 32px !important;
                height: 32px !important;
                font-size: 0.95rem !important;
                margin: 0 auto 6px !important;
            }
            .stat-value {
                font-size: 1.25rem !important;
                margin-bottom: 2px !important;
            }
            .stat-label {
                font-size: 0.72rem !important;
                line-height: 1.2 !important;
            }
            .stat-sub {
                font-size: 0.62rem !important;
                margin-top: 1px !important;
                opacity: 0.8 !important;
            }

            /* 5. 6 Layanan Terpadu: Grid 2 Kolom Ringkas */
            .services-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 8px !important;
            }
            .service-card {
                padding: 12px 10px !important;
                border-radius: 14px !important;
            }
            .service-icon-box {
                width: 36px !important;
                height: 36px !important;
                border-radius: 10px !important;
                font-size: 1rem !important;
                margin-bottom: 6px !important;
            }
            .service-tag {
                font-size: 0.6rem !important;
                margin-bottom: 2px !important;
            }
            .service-card h3 {
                font-size: 0.82rem !important;
                line-height: 1.25 !important;
                margin-bottom: 4px !important;
            }
            .service-card p {
                font-size: 0.7rem !important;
                line-height: 1.35 !important;
                margin-bottom: 8px !important;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .service-link {
                font-size: 0.72rem !important;
            }

            /* 6. Pengumuman Terbaru: Horizontal-Scroll Compact Tanpa Banner Tinggi */
            .announcements-grid {
                display: flex !important;
                overflow-x: auto !important;
                scroll-snap-type: x mandatory !important;
                gap: 10px !important;
                padding-bottom: 8px !important;
                -webkit-overflow-scrolling: touch !important;
            }
            .announcement-card {
                min-width: 250px !important;
                max-width: 260px !important;
                flex-shrink: 0 !important;
                scroll-snap-align: start !important;
                border-radius: 14px !important;
            }
            .announcement-media {
                height: 70px !important; /* Padatkan dari tinggi 170px */
            }
            .announcement-media i {
                font-size: 1.3rem !important;
                margin-bottom: 2px !important;
            }
            .announcement-media span {
                font-size: 0.65rem !important;
            }
            .announcement-badge-abs {
                top: 6px !important;
                left: 6px !important;
                font-size: 0.62rem !important;
                padding: 2px 7px !important;
            }
            .announcement-body {
                padding: 12px 10px !important;
            }
            .announcement-meta {
                font-size: 0.68rem !important;
                margin-bottom: 4px !important;
            }
            .announcement-body h4 {
                font-size: 0.85rem !important;
                line-height: 1.3 !important;
                margin-bottom: 4px !important;
                -webkit-line-clamp: 2 !important;
            }
            .announcement-body p {
                font-size: 0.74rem !important;
                line-height: 1.35 !important;
                margin-bottom: 8px !important;
                -webkit-line-clamp: 2 !important;
            }

            /* 7. Footer Ringkas */
            footer.footer-main {
                padding: 24px 0 16px !important;
            }
            .footer-grid {
                grid-template-columns: 1fr !important;
                gap: 16px !important;
                margin-bottom: 16px !important;
            }
            .footer-col h5 {
                font-size: 0.92rem !important;
                margin-bottom: 8px !important;
            }
            .footer-col p {
                font-size: 0.78rem !important;
                line-height: 1.45 !important;
                margin-bottom: 8px !important;
            }
            .footer-list {
                font-size: 0.78rem !important;
            }
            .footer-list li {
                margin-bottom: 6px !important;
            }
            .contact-emergency-box {
                padding: 10px 12px !important;
                border-radius: 10px !important;
                margin-top: 6px !important;
            }
            .footer-bottom {
                padding-top: 14px !important;
                font-size: 0.7rem !important;
                line-height: 1.4 !important;
            }

            /* 8. Floating Telegram di Mobile */
            .haven-floating-telegram {
                bottom: 82px !important;
                right: 14px !important;
                width: 48px !important;
                height: 48px !important;
                font-size: 22px !important;
            }
        }
    </style>
</head>
<body>

{{-- ======================================================================
     NAVBAR ATAS SMART RW 013
     ====================================================================== --}}
<header class="navbar-wrap">
    <div class="navbar">
        <a href="{{ route('cover') }}" class="logo">
            <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="width: 44px; height: 44px; flex-shrink: 0;">
                <rect width="64" height="64" rx="16" fill="url(#coverHGrad)"/>
                <path d="M16 19L32 10L48 19" stroke="#7DD3FC" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M20 23V46M44 23V46M20 34H44" stroke="#FFFFFF" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                <defs>
                    <linearGradient id="coverHGrad" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#1456A0"/>
                        <stop offset="1" stop-color="#2878D0"/>
                    </linearGradient>
                </defs>
            </svg>
            <div>
                <strong>Haven Portal</strong>
                <small style="font-weight: 600; color: var(--secondary); letter-spacing: 0.3px;">Safe Space, Seamless Service</small>
            </div>
        </a>

        <ul class="nav-links">
            <li><a href="#layanan">Layanan</a></li>
            <li><a href="#statistik">Statistik</a></li>
            <li><a href="#pengumuman">Pengumuman</a></li>
            <li><a href="#kontak">Sekretariat</a></li>
        </ul>

        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn btn-outline" id="navLogin">
                <i class="fas fa-right-to-bracket"></i>
                <span>Masuk</span>
            </a>
            <button class="icon-btn" id="themeToggle" type="button" aria-label="Ganti mode gelap/terang">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </div>
</header>

<main>
    {{-- ======================================================================
         1. HERO SECTION
         ====================================================================== --}}
    <div class="container">
        <section class="hero">
            <div>
                <span class="hero-badge">
                    <i class="fas fa-shield-halved"></i> HAVEN PORTAL &bull; SAFE SPACE, SEAMLESS SERVICE
                </span>
                <h1>Pelayanan Mudah, <span>Warga Sejahtera</span></h1>
                <div class="underline"></div>

                @if (session('success'))
                    <div style="display:inline-flex;gap:8px;align-items:center;background:#E3F5EC;color:#16865D;padding:10px 14px;border-radius:12px;font-size:.9rem;margin-bottom:16px;">
                        <i class="fas fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <p class="lead">
                    Selamat datang di <strong>Haven Portal</strong>. Portal digital terintegrasi warga RW 013 Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung. Nikmati kemudahan iuran terpadu, surat pengantar digital, pemantauan keamanan lingkungan, hingga etalase UMKM warga.
                </p>

                <div class="cta-group">
                    <a href="{{ route('login') }}" class="btn btn-primary" id="ctaLogin">
                        <i class="fas fa-right-to-bracket"></i> Masuk Layanan Warga
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline" id="ctaRegister">
                        <i class="fas fa-user-plus"></i> Daftar Akun Baru
                    </a>
                    <a href="#layanan" class="btn btn-soft">
                        <i class="fas fa-compass"></i> Jelajahi Layanan
                    </a>
                </div>

                <div class="pillars">
                    <div class="pillar">
                        <i class="fas fa-wallet" style="color:#2878D0;"></i>
                        <span>One Billing<br>Transparan</span>
                    </div>
                    <div class="pillar">
                        <i class="fas fa-file-signature" style="color:#16865D;"></i>
                        <span>Surat Online<br>QR Code</span>
                    </div>
                    <div class="pillar">
                        <i class="fas fa-video" style="color:#D97706;"></i>
                        <span>Siskamling &amp;<br>CCTV Kawasan</span>
                    </div>
                    <div class="pillar">
                        <i class="fas fa-heart-pulse" style="color:#E11D48;"></i>
                        <span>Posyandu &amp;<br>Bank Sampah</span>
                    </div>
                </div>
            </div>

            <figure class="hero-art">
                <img src="{{ asset('images/auth-warga.jpg') }}" alt="Ilustrasi kerukunan dan pelayanan keluarga warga di lingkungan RW 013 Sukamiskin Arcamanik">
            </figure>
        </section>

        {{-- ======================================================================
             2. STRIP STATISTIK WILAYAH (100% DATA ASLI DARI DATABASE)
             ====================================================================== --}}
        <section id="statistik" class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div class="stat-value">8 RT</div>
                    <div class="stat-label">Wilayah Rukun Tetangga</div>
                    <div class="stat-sub">RT 001 s.d. RT 008 Aktif</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-people-roof"></i>
                    </div>
                    <div class="stat-value">{{ number_format($stats['total_kk'] ?? 0) }}</div>
                    <div class="stat-label">Kepala Keluarga</div>
                    <div class="stat-sub">Terdaftar di Basis Data KK</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="stat-value">{{ number_format($stats['total_warga'] ?? 0) }}</div>
                    <div class="stat-label">Warga Terdaftar</div>
                    <div class="stat-sub">Akun Warga Terverifikasi</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="stat-value">{{ number_format($stats['total_cctv'] ?? 0) }} Titik</div>
                    <div class="stat-label">CCTV Lingkungan</div>
                    <div class="stat-sub">Pemantauan Teritorial 24 Jam</div>
                </div>
            </div>
        </section>
    </div>

    {{-- ======================================================================
         3. SECTION LAYANAN UTAMA SMART RW
         ====================================================================== --}}
    <section id="layanan" class="page-section" style="background: var(--light-blue); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">SOLUSI DIGITAL KELUARGA &amp; LINGKUNGAN</span>
                <h2>6 Layanan Terpadu Smart RW 013</h2>
                <p>Seluruh urusan administrasi, iuran, pemantauan wilayah, dan kesehatan kini dapat diakses dengan cepat, aman, dan tanpa birokrasi berbelit.</p>
            </div>

            <div class="services-grid">
                {{-- 1. One Billing --}}
                <div class="service-card">
                    <div>
                        <div class="service-icon-box" style="background: #EAF3FF; color: #1456A0;">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <span class="service-tag" style="color: #1456A0;">Keuangan Digital</span>
                        <h3>One Billing &amp; Transparansi Kas</h3>
                        <p>Pembayaran iuran terpadu (RT, RW, Sampah, Ronda) dalam satu tagihan kuitansi digital otomatis dengan laporan saldo kas terbuka real-time.</p>
                    </div>
                    <div>
                        <a href="{{ route('login') }}" class="service-link">
                            <span>Akses Layanan</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                {{-- 2. Surat Pengantar Digital --}}
                <div class="service-card">
                    <div>
                        <div class="service-icon-box" style="background: #E8F6EF; color: #16865D;">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <span class="service-tag" style="color: #16865D;">Administrasi Warga</span>
                        <h3>Layanan Surat Pengantar Digital</h3>
                        <p>Pengajuan surat keterangan pengantar RT/RW mandiri via smartphone, verifikasi berjenjang instan, dan dilengkapi QR Code keaslian dokumen resmi.</p>
                    </div>
                    <div>
                        <a href="{{ route('login') }}" class="service-link">
                            <span>Akses Layanan</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                {{-- 3. Ronda & CCTV --}}
                <div class="service-card">
                    <div>
                        <div class="service-icon-box" style="background: #FEF3C7; color: #D97706;">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <span class="service-tag" style="color: #D97706;">Keamanan &amp; Siskamling</span>
                        <h3>Ronda &amp; CCTV Berjenjang</h3>
                        <p>Jadwal piket ronda malam terdata, kanal lapor kejadian keamanan darurat cepat tanggap, serta sistem izin pantauan CCTV teritorial berjenjang RT/RW.</p>
                    </div>
                    <div>
                        <a href="{{ route('login') }}" class="service-link">
                            <span>Akses Layanan</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                {{-- 4. Posyandu & Bank Sampah --}}
                <div class="service-card">
                    <div>
                        <div class="service-icon-box" style="background: #FFE4E6; color: #E11D48;">
                            <i class="fas fa-heart-pulse"></i>
                        </div>
                        <span class="service-tag" style="color: #E11D48;">Kesehatan &amp; Lingkungan</span>
                        <h3>Posyandu &amp; Bank Sampah</h3>
                        <p>Rekam tumbuh kembang balita/lansia Posyandu Melati dan program tabungan sampah terpilah untuk mewujudkan lingkungan RW yang sehat dan bersih.</p>
                    </div>
                    <div>
                        <a href="{{ route('login') }}" class="service-link">
                            <span>Akses Layanan</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                {{-- 5. UMKM Warga --}}
                <div class="service-card">
                    <div>
                        <div class="service-icon-box" style="background: #FDE68A; color: #B45309;">
                            <i class="fas fa-store"></i>
                        </div>
                        <span class="service-tag" style="color: #B45309;">Ekonomi Warga</span>
                        <h3>Direktori UMKM Warga</h3>
                        <p>Etalase promosi online produk kuliner, kerajinan, dan jasa usaha mikro warga RW 013 untuk menggerakkan perekonomian lokal lingkungan.</p>
                    </div>
                    <div>
                        <a href="{{ route('login') }}" class="service-link">
                            <span>Akses Layanan</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                {{-- 6. Peta Wilayah & Pengaduan --}}
                <div class="service-card">
                    <div>
                        <div class="service-icon-box" style="background: #E0F2FE; color: #0284C7;">
                            <i class="fas fa-map-location-dot"></i>
                        </div>
                        <span class="service-tag" style="color: #0284C7;">Fasilitas &amp; Aspirasi</span>
                        <h3>Peta Wilayah &amp; Pengaduan</h3>
                        <p>Peta interaktif titik fasilitas publik, pos ronda, dan bak sampah, serta portal penyampaian aspirasi warga dengan pelacakan respon pengurus.</p>
                    </div>
                    <div>
                        <a href="{{ route('login') }}" class="service-link">
                            <span>Akses Layanan</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================================================================
         4. SECTION CUPLIKAN 3 PENGUMUMAN RW TERBARU (DARI DATABASE)
         ====================================================================== --}}
    <section id="pengumuman" class="page-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">INFORMASI &amp; AGENDA KEWILAYAHAN</span>
                <h2>Pengumuman RW 013 Terbaru</h2>
                <p>Tetap terinformasi seputar pengumuman resmi pengurus, kegiatan gotong royong, dan kabar penting di lingkungan Sukamiskin Arcamanik.</p>
            </div>

            <div class="announcements-grid">
                @forelse($pengumumanTerbaru as $p)
                    <article class="announcement-card">
                        <div class="announcement-media" style="{{ $p->gambar ? '' : 'background: linear-gradient(135deg, #0F3966, #1456A0);' }}">
                            @if($p->gambar_url)
                                <img src="{{ $p->gambar_url }}" alt="{{ $p->judul }}">
                            @else
                                <div style="text-align: center; padding: 20px;">
                                    <i class="fas fa-bullhorn" style="font-size: 2.4rem; opacity: 0.85; margin-bottom: 8px;"></i>
                                    <span style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">Haven Info</span>
                                </div>
                            @endif
                            <span class="announcement-badge-abs">
                                {{ $p->kategori ?: 'Informasi' }}
                            </span>
                        </div>
                        <div class="announcement-body">
                            <div class="announcement-meta">
                                <i class="fas fa-calendar-day"></i>
                                <span>{{ $p->created_at ? $p->created_at->translatedFormat('d M Y') : 'Terbaru' }}</span>
                                <span>&bull;</span>
                                <span>RW 013</span>
                            </div>
                            <h4>{{ $p->judul }}</h4>
                            <p>{{ Str::limit(strip_tags($p->isi), 110) }}</p>
                            <div>
                                <a href="{{ route('login') }}" class="service-link">
                                    <span>Masuk untuk Baca Selengkapnya</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--muted);">
                        <i class="fas fa-bell-slash" style="font-size: 2.5rem; margin-bottom: 12px; opacity: 0.6;"></i>
                        <p>Belum ada pengumuman publik yang dipublikasikan saat ini.</p>
                    </div>
                @endforelse
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ route('login') }}" class="btn btn-outline" style="padding: 12px 28px;">
                    <i class="fas fa-newspaper"></i>
                    <span>Buka Semua Arsip Pengumuman (Masuk)</span>
                </a>
            </div>
        </div>
    </section>
</main>

{{-- ======================================================================
     5. FOOTER INFORMATIF SMART RW 013
     ====================================================================== --}}
<footer id="kontak" class="footer-main">
    <div class="container">
        <div class="footer-grid">
            {{-- Kolom 1: Profil Haven --}}
            <div class="footer-col">
                <div class="logo" style="margin-bottom: 14px;">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="width: 44px; height: 44px; flex-shrink: 0;">
                        <rect width="64" height="64" rx="16" fill="url(#footerHGrad)"/>
                        <path d="M16 19L32 10L48 19" stroke="#7DD3FC" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 23V46M44 23V46M20 34H44" stroke="#FFFFFF" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <defs>
                            <linearGradient id="footerHGrad" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#1456A0"/>
                                <stop offset="1" stop-color="#2878D0"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div>
                        <strong>Haven Portal</strong>
                        <small style="font-weight: 600; color: var(--secondary); letter-spacing: 0.3px;">Safe Space, Seamless Service</small>
                    </div>
                </div>
                <p>
                    Haven Portal — Sistem Tata Kelola &amp; Pelayanan Warga Terpadu RW 013. Menghadirkan lingkungan rukun warga yang modern, transparan, aman, dan berdaya saing di <strong style="color: var(--secondary);">havenportal.mehape.com</strong>.
                </p>
                <div style="display: flex; gap: 8px; margin-top: 12px;">
                    <a href="{{ route('login') }}" class="btn btn-soft" style="padding: 8px 14px; font-size: .8rem;">
                        <i class="fas fa-lock"></i> Portal Masuk Warga
                    </a>
                </div>
            </div>

            {{-- Kolom 2: Alamat & Jam Layanan Sekretariat --}}
            <div class="footer-col">
                <h5>Sekretariat &amp; Balai RW</h5>
                <ul class="footer-list">
                    <li>
                        <i class="fas fa-map-pin"></i>
                        <span>Balai Warga RW 013, Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung, Jawa Barat 40293</span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span><strong>Pelayanan Pengurus:</strong><br>Senin – Jumat: 08.00 – 16.00 WIB<br>Sabtu: 08.00 – 12.00 WIB (Minggu Libur)</span>
                    </li>
                    <li>
                        <i class="fas fa-building"></i>
                        <span>Menaungi 8 Rukun Tetangga (RT 001 – RT 008)</span>
                    </li>
                </ul>
            </div>

            {{-- Kolom 3: Kontak Darurat & Keamanan --}}
            <div class="footer-col">
                <h5>Kontak Darurat &amp; Keamanan</h5>
                <p style="margin-bottom: 8px;">Hubungi kontak resmi pos pengamanan wilayah apabila terdapat keadaan darurat atau gangguan kamtibmas:</p>
                
                <div class="contact-emergency-box">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: .82rem; font-weight: 700; color: var(--primary);">Bandung Siaga (Polisi / Medis / Damkar)</span>
                        <a href="tel:112" class="btn btn-primary" style="padding: 4px 10px; font-size: .75rem; border-radius: 8px; background: #DC2626;">
                            <i class="fas fa-phone"></i> 112
                        </a>
                    </div>
                    <small style="display: block; font-size: .75rem; color: var(--muted);">Layanan Panggilan Darurat Bebas Pulsa 24 Jam.</small>
                </div>

                <div class="contact-emergency-box" style="margin-top: 8px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                        <span style="font-size: .82rem; font-weight: 700; color: var(--text);">Pos Linmas RW 013</span>
                        <span style="font-size: .75rem; font-weight: 800; background: var(--green-light); color: var(--green); padding: 2px 8px; border-radius: 6px;">Radio Ch-4</span>
                    </div>
                    <small style="display: block; font-size: .75rem; color: var(--muted);">Petugas Ronda &amp; Satgas Keamanan Siaga 24 Jam.</small>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; {{ date('Y') }} Haven Portal &bull; Safe Space, Seamless Service &bull; havenportal.mehape.com &bull; Rukun Warga 013, Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung 40293. Seluruh hak cipta dilindungi.
        </div>
    </div>
</footer>

{{-- Floating Tombol Telegram (Pojok Kanan Bawah) --}}
<a href="https://t.me/paparazzied" target="_blank" rel="noopener noreferrer" class="haven-floating-telegram" aria-label="Telegram" title="Hubungi via Telegram">
    <i class="fab fa-telegram-plane"></i>
</a>

<script>
    (function () {
        var r = document.documentElement, b = document.getElementById('themeToggle');
        function sync() {
            b.innerHTML = r.dataset.theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        }
        sync();
        b.addEventListener('click', function () {
            r.dataset.theme = r.dataset.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('wm-theme', r.dataset.theme);
            sync();
        });
    })();
</script>
</body>
</html>