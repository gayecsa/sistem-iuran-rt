@extends('layouts.app')

@section('content')
<div class="dashboard-shell"> 
    <div class="row g-4">        <div class="col-lg-4">
            <div class="card sideboard-panel p-4 animate__animated animate__fadeInUp">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small">Iuran-RT</p>
                        <h4 class="mb-1">Halo, {{ $user->name ?? 'Warga RT 001' }}</h4>
                        <p class="text-muted mb-0">Selamat datang di dashboard Iuran RT Gandaria</p>
                    </div>
                    <div class="icon-circle shadow-sm">
                        <i class="fas fa-house-user fa-lg"></i>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="metric-card p-3">
                            <small class="text-muted">Saldo Saat Ini</small>
                            <div class="d-flex align-items-end justify-content-between mt-3">
                                <div>
                                    <h3 class="mb-0">Rp {{ number_format($saldo_kas ?? 0, 0, ',', '.') }}</h3>
                                    <p class="mb-0 text-muted">Per {{ date('d M Y') }}</p>
                                </div>
                                <span class="gradient-pill">+12%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card p-3 text-center">
                            <small class="text-muted">Pemasukan</small>
                            <h5 class="mt-2 mb-0">Rp {{ number_format($total_pemasukan ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card p-3 text-center">
                            <small class="text-muted">Pengeluaran</small>
                            <h5 class="mt-2 mb-0">Rp {{ number_format($total_pengeluaran ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>

                <div class="card p-3">
                    <h6 class="mb-3">Ringkasan</h6>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="text-muted">Total Warga</span>
                            <h5 class="mb-0">{{ $total_warga ?? 0 }}</h5>
                        </div>
                        <div class="badge gradient-pill">Aktif</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">RT</span>
                            <h5 class="mb-0">001</h5>
                        </div>
                        <div>
                            <span class="text-muted">Role</span>
                            <h5 class="mb-0">{{ ucfirst($user->role ?? 'warga') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card hero-banner p-4 animate__animated animate__fadeInUp">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small">Dashboard</p>
                        <h2 class="mb-2">Selamat datang, {{ $user->name ?? 'Admin RT' }} 👋</h2>
                        <p class="text-muted mb-0">Kelola keuangan RT dengan mudah dan transparan.</p>
                    </div>
                    <div class="text-end">
                        <div class="gradient-pill" style="cursor: pointer;">Tambahkan Informasi</div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4 mb-2">
                    @if(in_array(auth()->user()->role ?? 'admin', ['admin', 'bendahara']))
                        <a href="{{ route('laporan.keuangan') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 {{ request()->routeIs('laporan.keuangan') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice-dollar me-2"></i> Riwayat Transaksi
                        </a>
                    @endif

                    @if(in_array(auth()->user()->role ?? 'admin', ['admin', 'bendahara']))
                        <a href="{{ route('warga.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 {{ request()->routeIs('warga.*') ? 'active' : '' }}">
                            <i class="fas fa-users me-2"></i> Data Warga
                        </a>
                    @endif
                </div>

                <div class="row mt-4 gy-3">
                    <!-- CLASS h-100 DIHAPUS DARI 3 KOTAK INI AGAR TIDAK MELAR -->
                    <div class="col-sm-4">
                        <div class="card metric-card p-3 text-center">
                            <span class="text-muted">Total Pemasukan</span>
                            <h5 class="mt-2 mb-0">Rp {{ number_format($total_pemasukan ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card metric-card p-3 text-center">
                            <span class="text-muted">Total Pengeluaran</span>
                            <h5 class="mt-2 mb-0">Rp {{ number_format($total_pengeluaran ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <a href="{{ route('keuangan.detail') }}" class="card metric-card p-3 text-center text-decoration-none shadow-sm" style="transition: 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <span class="text-muted d-block">Saldo Iuran</span>
                            <h5 class="mt-2 mb-2 text-dark">Rp {{ number_format($saldo_kas ?? 0, 0, ',', '.') }}</h5>
                            <small class="text-primary mt-2 d-block"><i class="fas fa-chart-pie me-1"></i> Lihat Detail ➔</small>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <!-- BAGIAN GRAFIK GENDER WARGA -->
                <div class="col-md-7">
                    <div class="card chart-card p-4 animate__animated animate__fadeInUp" style="background: #ffffff; border: none; border-radius: 24px; box-shadow: 0 10px 30px rgba(236, 72, 153, 0.05), 0 10px 30px rgba(59, 130, 246, 0.05);">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h6 class="mb-1" style="font-weight: 700; font-size: 1.1rem; color: #1f2937;">Komposisi Gender Warga</h6>
                                <small class="text-muted">Perbandingan jumlah warga Laki-laki dan Perempuan</small>
                            </div>
                            <div class="icon-circle shadow-sm" style="background: #f8fafc; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-venus-mars text-primary"></i>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-center gap-5" style="padding: 10px;">
                            <div style="position: relative; width: 170px; height: 170px;">
                                <canvas id="chartGenderWarga"></canvas>
                            </div>
                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <span style="color: #6b7280; font-size: 0.95rem; display: block; margin-bottom: 4px;"><i class="fas fa-circle me-2" style="color:#ec4899; font-size:8px;"></i>Perempuan</span>
                                    <strong style="font-size: 2rem; color: #ec4899; font-weight: 700; display: block; line-height: 1;">{{ $total_perempuan ?? 33 }}</strong>
                                </div>
                                <div>
                                    <span style="color: #6b7280; font-size: 0.95rem; display: block; margin-bottom: 4px;"><i class="fas fa-circle me-2" style="color:#3b82f6; font-size:8px;"></i>Laki-laki</span>
                                    <strong style="font-size: 2rem; color: #3b82f6; font-weight: 700; display: block; line-height: 1;">{{ $total_laki ?? 67 }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card chart-card p-4 animate__animated animate__fadeInUp h-100">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h6 class="mb-1">Pengumuman</h6>
                                <small class="text-muted">Info penting RT</small>
                            </div>
                            <span class="badge bg-white text-secondary py-2 px-3 shadow-sm">Terbaru</span>
                        </div>
                        <div style="height: 180px; overflow: hidden; position: relative;">
    <marquee direction="up" scrollamount="2" onmouseover="this.stop();" onmouseout="this.start();">
        <ul class="list-group recent-list list-group-flush">
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">Rapat Pengurus RT</h6>
                        <small class="text-muted">28 April 2024</small>
                    </div>
                    <span class="badge bg-pink text-white rounded-pill">Penting</span>
                </div>
            </li>
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">Pembayaran Iuran Mei</h6>
                        <small class="text-muted">1 Mei 2024</small>
                    </div>
                    <span class="badge bg-info text-white rounded-pill">Informasi</span>
                </div>
            </li>
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">Kerja Bakti Lingkungan</h6>
                        <small class="text-muted">5 Mei 2024</small>
                    </div>
                    <span class="badge bg-success text-white rounded-pill">Selesai</span>
                </div>
            </li>
        </ul>
    </marquee>
</div>
<div class="mt-2 text-center">
    <a href="{{ url('/informasi-terbaru') }}" class="btn btn-sm btn-link text-decoration-none">
        Lihat Semua Informasi ➔
    </a>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT CHART.JS UNTUK GRAFIK DONAT -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctxGender = document.getElementById('chartGenderWarga');
        
        if(ctxGender) {
            const totalLaki = {{ $total_laki ?? 67 }};
            const totalPerempuan = {{ $total_perempuan ?? 33 }};

            new Chart(ctxGender.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        data: [totalLaki, totalPerempuan],
                        // Warna Pastel
                        backgroundColor: ['#7fb3ff', '#ff85b3'], 
                        // border putih setebal 4px agar ada pemisah tegas
                        borderColor: '#ffffff', 
                        borderWidth: 4, 
                        // cutout 60% bikin ring-nya jadi lebih tebal (tidak terlalu kurus)
                        cutout: '60%', 
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#ffffff',
                            titleColor: '#1f2937',
                            bodyColor: '#1f2937',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.raw + ' Warga';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection