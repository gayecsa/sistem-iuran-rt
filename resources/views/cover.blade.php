<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warkas Machi | Pelayanan Mudah, Warga Sejahtera – RW 013</title>
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
        :root { --primary:#1456A0; --secondary:#2878D0; --light-blue:#EAF3FF; --green:#16865D; --bg:#F4F7FB;
                --text:#172B4D; --muted:#5E6C84; --border:#DCE4EE; --card:#FFFFFF; --shadow:0 10px 30px rgba(20,86,160,.08); }
        [data-theme="dark"] { --light-blue:#1A2A44; --bg:#0F1724; --text:#E6EDF7; --muted:#9AA8BC; --border:#2A3A52;
                --card:#16213A; --shadow:0 10px 30px rgba(0,0,0,.35); }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Plus Jakarta Sans',system-ui,sans-serif; background:linear-gradient(180deg,var(--light-blue),var(--bg) 70%);
               color:var(--text); min-height:100vh; display:flex; flex-direction:column; transition:background .3s,color .3s; }
        header { display:flex; justify-content:space-between; align-items:center; padding:20px 6vw; }
        .logo { display:flex; align-items:center; gap:12px; }
        .logo svg { width:46px; height:46px; }
        .logo strong { display:block; font-size:1.2rem; color:var(--primary); }
        [data-theme="dark"] .logo strong { color:#7DB4F5; }
        .logo small { color:var(--muted); font-size:.78rem; }
        .nav { display:flex; gap:10px; align-items:center; }
        .btn { display:inline-flex; align-items:center; gap:9px; padding:12px 22px; border-radius:12px; font:inherit;
               font-weight:700; text-decoration:none; border:1.5px solid transparent; cursor:pointer; transition:all .2s; }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-primary:hover { background:var(--secondary); transform:translateY(-2px); }
        .btn-outline { border-color:var(--primary); color:var(--primary); background:var(--card); }
        [data-theme="dark"] .btn-outline { color:#7DB4F5; border-color:#7DB4F5; }
        .btn-outline:hover { background:var(--light-blue); transform:translateY(-2px); }
        .icon-btn { width:42px; height:42px; border-radius:50%; border:1px solid var(--border); background:var(--card); color:var(--text); cursor:pointer; }

        .hero { flex:1; display:grid; grid-template-columns:1fr 1.1fr; gap:40px; align-items:center; padding:20px 6vw 40px; }
        .hero h1 { font-size:clamp(2.2rem,4.4vw,3.6rem); line-height:1.12; font-weight:800; animation:rise .6s ease; }
        .hero h1 span { color:var(--secondary); display:block; }
        .underline { width:130px; height:5px; border-radius:4px; background:#F2B33D; margin:14px 0 22px; }
        .hero p { color:var(--muted); line-height:1.7; max-width:480px; font-size:1.05rem; }
        .cta { display:flex; gap:12px; margin:30px 0 34px; flex-wrap:wrap; }
        .pillars { display:flex; flex-wrap:wrap; }
        .pillar { text-align:center; padding:0 20px; border-left:1px solid var(--border); font-size:.8rem; color:var(--muted); }
        .pillar:first-child { border-left:0; padding-left:0; }
        .pillar i { display:grid; place-items:center; width:44px; height:44px; margin:0 auto 6px; border-radius:50%;
                    background:var(--card); box-shadow:var(--shadow); font-size:1.05rem; }
        .art img { width:100%; border-radius:28px; box-shadow:var(--shadow); display:block; animation:rise .8s ease; }
        footer { text-align:center; padding:18px; color:var(--muted); font-size:.8rem; }
        @keyframes rise { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }

        @media (max-width:900px) {
            .hero { grid-template-columns:1fr; text-align:center; padding-top:10px; }
            .hero p, .underline { margin-left:auto; margin-right:auto; }
            .cta, .pillars { justify-content:center; }
            .art { order:-1; }
            .nav .btn { display:none; }
        }
        @media (max-width:520px) {
            .cta .btn { width:100%; justify-content:center; }
            .pillar { padding:0 8px; font-size:.72rem; }
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <svg viewBox="0 0 64 64" aria-hidden="true"><path d="M8 30 32 10l24 20" fill="none" stroke="#1456A0" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 27v20h36V27" fill="none" stroke="#2878D0" stroke-width="3.5" stroke-linejoin="round"/><circle cx="26" cy="30" r="3.5" fill="#1456A0"/><path d="M21 44v-6a5 5 0 0 1 10 0v6" fill="#1456A0"/><circle cx="38" cy="32" r="3" fill="#2878D0"/><path d="M34 44v-5a4 4 0 0 1 8 0v5" fill="#2878D0"/><path d="M10 52c8-8 18-8 22-2 4-6 14-6 22 2-10 4-34 4-44 0Z" fill="#16865D"/></svg>
        <div><strong>Warkas Machi</strong><small>RW 013 &bull; Kelurahan Machi</small></div>
    </div>
    <nav class="nav">
        <a href="{{ route('login') }}" class="btn btn-outline" id="navLogin"><i class="fas fa-right-to-bracket"></i> Masuk</a>
        <button class="icon-btn" id="themeToggle" type="button" aria-label="Ganti mode gelap/terang"><i class="fas fa-moon"></i></button>
    </nav>
</header>

<main class="hero">
    <section>
        <h1>Pelayanan Mudah, <span>Warga Sejahtera</span></h1>
        <div class="underline"></div>
        @if (session('success'))
            <p style="display:inline-flex;gap:8px;align-items:center;background:#E3F5EC;color:#16865D;padding:10px 14px;border-radius:12px;font-size:.9rem;margin-bottom:16px"><i class="fas fa-circle-check"></i>{{ session('success') }}</p>
        @endif
        <p>Sistem Informasi dan Layanan Terpadu RW 013 untuk mewujudkan lingkungan yang lebih baik, sehat, dan sejahtera.</p>
        <div class="cta">
            <a href="{{ route('login') }}" class="btn btn-primary" id="ctaLogin"><i class="fas fa-right-to-bracket"></i> Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-outline" id="ctaRegister"><i class="fas fa-user-plus"></i> Daftar Akun</a>
        </div>
        <div class="pillars">
            <div class="pillar"><i class="fas fa-users" style="color:#2878D0"></i>Pelayanan<br>Terpadu</div>
            <div class="pillar"><i class="fas fa-heart-pulse" style="color:#16865D"></i>Kesehatan<br>Masyarakat</div>
            <div class="pillar"><i class="fas fa-chart-line" style="color:#E59A1A"></i>Ekonomi<br>Warga</div>
            <div class="pillar"><i class="fas fa-shield-halved" style="color:#7A5AF8"></i>Lingkungan<br>Aman</div>
        </div>
    </section>
    <figure class="art">
        <img src="{{ asset('images/auth-warga.jpg') }}" alt="Ilustrasi keluarga dan warga Indonesia di lingkungan RW 013">
    </figure>
</main>

<footer>&copy; {{ date('Y') }} Warkas Machi &bull; RW 013 Kelurahan Machi</footer>

<script>
    (function () {
        var r = document.documentElement, b = document.getElementById('themeToggle');
        function sync() { b.innerHTML = r.dataset.theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>'; }
        sync();
        b.addEventListener('click', function () {
            r.dataset.theme = r.dataset.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('wm-theme', r.dataset.theme); sync();
        });
    })();
</script>
</body>
</html>
