@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card sideboard-panel p-4 h-100 animate__animated animate__fadeInUp">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small fw-bold">Warkas Machi</p>
                        <h4 class="mb-1 fw-bold text-dark">Halo, {{ $user->name ?? 'Warga RW 013' }}</h4>
                        <p class="text-muted mb-0">Selamat datang di dashboard Warkas Machi RW 013</p>
                    </div>
                    <div class="icon-circle shadow-sm">
                        <i class="fas fa-house-user fa-lg"></i>
                    </div>
                </div>

                <div class="card p-3 mb-4">
                    <h6 class="mb-3 fw-bold text-dark">Ringkasan</h6>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="text-muted fw-semibold">Total Warga</span>
                            <h5 class="mb-0 fw-bold text-dark">{{ $total_warga ?? 0 }}</h5>
                        </div>
                        <div class="badge gradient-pill fw-bold">Aktif</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-semibold">Wilayah</span>
                            <h5 class="mb-0 fw-bold text-dark">RW 013</h5>
                        </div>
                        <div>
                            <span class="text-muted fw-semibold">Role</span>
                            <h5 class="mb-0 fw-bold text-dark">{{ ucfirst($user->role ?? 'warga') }}</h5>
                        </div>
                    </div>
                </div>

                <div class="mt-2">
                    <p class="text-uppercase text-secondary mb-3 small fw-bold">Layanan & Informasi</p>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card service-card p-3 h-100 animate__animated animate__fadeInUp">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <p class="text-uppercase text-secondary mb-2 small fw-bold">Posyandu</p>
                                        <h5 class="mb-1 fw-bold text-dark">Info Posyandu</h5>
                                        <p class="text-muted mb-0">Jadwal layanan kesehatan keluarga dan balita.</p>
                                    </div>
                                    <div class="icon-circle shadow-sm" style="background:#d1fae5;color:#047857;">
                                        <i class="fas fa-heartbeat"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-success">Sabtu, 08.00 WIB</span>
                                    <a href="{{ route('posyandu') }}" class="small text-primary text-decoration-none fw-semibold">Lihat detail</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="card service-card p-3 h-100 animate__animated animate__fadeInUp">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <p class="text-uppercase text-secondary mb-2 small fw-bold">UMKM</p>
                                        <h5 class="mb-1 fw-bold text-dark">UMKM Terdekat</h5>
                                        <p class="text-muted mb-0">Temukan tempat makanan dan UMKM terdekat.</p>
                                    </div>
                                    <div class="icon-circle shadow-sm" style="background:#fef08a;color:#ca8a04;">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-warning text-dark">8 Tempat</span>
                                    <a href="{{ route('umkm.index') }}" class="small text-primary text-decoration-none fw-semibold">Lihat detail</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card service-card p-3 h-100 animate__animated animate__fadeInUp">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <p class="text-uppercase text-secondary mb-2 small fw-bold">Administrasi</p>
                                        <h5 class="mb-1 fw-bold text-dark">Penerbitan Surat</h5>
                                        <p class="text-muted mb-0">Layanan penerbitan surat keterangan resmi.</p>
                                    </div>
                                    <div class="icon-circle shadow-sm" style="background:#e8daef;color:#7d3c98;">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-secondary">4 Tipe</span>
                                    <a href="{{ route('surat.index') }}" class="small text-primary text-decoration-none fw-semibold">Lihat detail</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card service-card p-3 h-100 animate__animated animate__fadeInUp">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <p class="text-uppercase text-secondary mb-2 small fw-bold">Peta Wisata</p>
                                        <h5 class="mb-1 fw-bold text-dark">Wisata Terdekat</h5>
                                        <p class="text-muted mb-0">Lokasi wisata & rekreasi keluarga di sekitar RW 013.</p>
                                    </div>
                                    <div class="icon-circle shadow-sm" style="background:#dbeafe;color:#2563eb;">
                                        <i class="fas fa-umbrella-beach"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-primary">6 Lokasi</span>
                                    <a href="{{ route('wisata.index') }}" class="small text-primary text-decoration-none fw-semibold">Lihat detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Peta Wilayah Card (Di Atas Banner Selamat Datang / Pict 2) -->
            <div class="card service-card p-3 mb-4 animate__animated animate__fadeInUp border-0 shadow-sm" style="background: linear-gradient(135deg, #e0f2fe 0%, #ffffff 100%); border-radius: 18px; border: 1px solid #bae6fd !important;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-1">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-circle shadow-sm flex-shrink-0" style="background:#0284c7;color:#ffffff; width: 48px; height: 48px; font-size: 1.25rem;">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div>
                            <p class="text-uppercase text-secondary mb-1 small fw-bold" style="letter-spacing: 0.5px;">Peta Wilayah</p>
                            <h5 class="mb-1 fw-bold text-dark">Peta Wilayah & Lokasi Penting RW 013</h5>
                            <p class="text-muted mb-0 small">Lihat batas wilayah RT 001 - RT 008, posyandu, dan lokasi UMKM.</p>
                        </div>
                    </div>
                    <a href="{{ route('peta.index') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
                        <i class="fas fa-map-marker-alt me-1"></i> Buka Peta Wilayah
                    </a>
                </div>
            </div>

            <div class="card hero-banner p-4 animate__animated animate__fadeInUp">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small fw-bold">Dashboard RW 013</p>
                        <h2 class="mb-2 fw-bold text-dark">Selamat datang, {{ $user->name ?? 'Pengurus RW' }} 👋</h2>
                        <p class="text-muted mb-0">Kelola keuangan & layanan Warkas Machi RW 013 dengan mudah dan transparan.</p>
                    </div>
                </div>

                <div class="mt-4 mb-3">
                    @if(in_array(auth()->user()->role, ['admin', 'bendahara']))
                        <a href="{{ route('warga.index') }}" class="btn btn-outline-primary me-2 mb-2 fw-semibold">
                            <i class="fas fa-users me-2"></i>Data Warga
                        </a>
                    @endif
                    <a href="{{ route('laporan.keuangan') }}" class="btn btn-outline-info me-2 mb-2 fw-semibold">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Laporan Keuangan
                    </a>
                    <a href="{{ route('keuangan.detail') }}" class="btn btn-outline-success mb-2 fw-semibold">
                        <i class="fas fa-balance-scale me-2"></i>Saldo Kas
                    </a>
                </div>

                <div class="row mt-3 gy-3">
                    <div class="col-sm-4">
                        <div class="card metric-card p-3 text-center">
                            <span class="text-muted fw-semibold">Total Pemasukan</span>
                            <h5 class="mt-2 mb-0 fw-bold text-dark">Rp {{ number_format($total_pemasukan ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card metric-card p-3 text-center">
                            <span class="text-muted fw-semibold">Total Pengeluaran</span>
                            <h5 class="mt-2 mb-0 fw-bold text-dark">Rp {{ number_format($total_pengeluaran ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card metric-card p-3 text-center">
                            <span class="text-muted fw-semibold">Saldo Kas</span>
                            <h5 class="mt-2 mb-0 fw-bold text-dark">Rp {{ number_format($saldo_kas ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-7">
                    <div class="card chart-card p-4 animate__animated animate__fadeInUp">
                        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Komposisi Gender Warga</h6>
                                <small class="text-muted">Perbandingan jumlah warga Laki-laki dan Perempuan</small>
                            </div>
                            <div class="icon-circle shadow-sm" style="background: #f8fafc; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-venus-mars text-primary"></i>
                            </div>
                        </div>
                        <div class="row gx-3 mb-4">
                            <div class="col-sm-6">
                                <div class="card metric-card p-3 h-100">
                                    <span class="text-muted fw-semibold">Laki-laki</span>
                                    <h5 class="mt-2 mb-1 fw-bold text-dark">{{ $total_laki ?? 0 }}</h5>
                                    <small class="text-success fw-bold">{{ $total_warga > 0 ? round(($total_laki / $total_warga) * 100, 1) : 0 }}%</small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card metric-card p-3 h-100">
                                    <span class="text-muted fw-semibold">Perempuan</span>
                                    <h5 class="mt-2 mb-1 fw-bold text-dark">{{ $total_perempuan ?? 0 }}</h5>
                                    <small class="text-pink fw-bold">{{ $total_warga > 0 ? round(($total_perempuan / $total_warga) * 100, 1) : 0 }}%</small>
                                </div>
                            </div>
                        </div>
                        <div style="height: 260px; display: flex; align-items: center; justify-content: center;">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-5">
                    <div class="card chart-card p-4 animate__animated animate__fadeInUp">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-bullhorn text-primary me-2"></i>Pengumuman & Informasi</h6>
                                <small class="text-muted"><i class="fas fa-layer-group text-info me-1"></i> Info Penting RT & RW 013</small>
                            </div>
                            <a href="{{ route('pengumuman.index') }}" class="badge bg-white text-secondary py-2 px-3 shadow-sm text-decoration-none border">Terbaru</a>
                        </div>

                        <p class="text-muted small mb-2" style="font-size: 0.75rem;"><i class="fas fa-info-circle me-1 text-primary"></i> Pengumuman berjalan otomatis. Arahkan kursor untuk berhenti.</p>

                        <!-- Ticker Pengumuman Berjalan -->
                        <div class="pengumuman-ticker-container position-relative overflow-hidden rounded-3 p-1" id="pengumumanTickerContainer" style="height: 250px; overflow-y: hidden;">
                            <ul class="list-group recent-list list-group-flush border-0" id="pengumumanTickerList">
                                @forelse($pengumuman ?? [] as $p)
                                    <li class="list-group-item pengumuman-item border-0 mb-2 rounded-3 bg-light shadow-sm p-3" style="cursor: pointer;" data-pengumuman-id="{{ $p->id }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1 me-2" style="min-width: 0;">
                                                <h6 class="mb-1 fw-bold text-dark text-truncate">{{ $p->judul }}</h6>
                                                <small class="text-muted"><i class="far fa-calendar-alt me-1 text-primary"></i>{{ \Carbon\Carbon::parse($p->tanggal_aktif)->format('d M Y') }}</small>
                                            </div>
                                            <span class="badge {{ $p->kategori == 'Penting' ? 'bg-danger' : ($p->kategori == 'Informasi' ? 'bg-info' : 'bg-success') }} text-white rounded-pill px-2 py-1 small">{{ $p->kategori }}</span>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted text-center py-3 border-0">Belum ada pengumuman</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pengumumanModal" tabindex="-1" aria-labelledby="pengumumanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-5" style="border: none; box-shadow: 0 25px 60px rgba(46, 61, 94, 0.12);">
            <div class="modal-header border-0 pb-0">
                <div class="w-100">
                    <h5 class="modal-title fw-bold" id="pengumumanModalLabel">-</h5>
                    <small class="text-muted" id="pengumumanTanggal">-</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 mb-3">
                    <span class="badge" id="pengumumanKategori">-</span>
                </div>
                <div id="pengumumanIsi" class="text-muted lh-lg">
                    -
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari controller
    const totalLaki = {{ $total_laki ?? 0 }};
    const totalPerempuan = {{ $total_perempuan ?? 0 }};
    const total = totalLaki + totalPerempuan;

    // Hitung persentase
    const persenLaki = total > 0 ? ((totalLaki / total) * 100).toFixed(1) : 0;
    const persenPerempuan = total > 0 ? ((totalPerempuan / total) * 100).toFixed(1) : 0;

    const ctx = document.getElementById('genderChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    'Laki-laki (' + totalLaki + ')',
                    'Perempuan (' + totalPerempuan + ')'
                ],
                datasets: [{
                    data: [totalLaki, totalPerempuan],
                    backgroundColor: [
                        '#7cb3f1', // Biru Laki-laki yang lebih soft/pastel
                        '#f29bb5'  // Pink Perempuan yang lebih soft/pastel
                    ],
                    borderColor: '#ffffff', // Warna garis batas (putih)
                    borderWidth: 4,         // Ketebalan batas putih
                    hoverOffset: 4          // Efek pop-up sedikit saat di-hover
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + percentage + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    // Pengumuman item click handler
    document.querySelectorAll('.pengumuman-item').forEach(item => {
        item.addEventListener('click', function() {
            const pengumumanId = this.getAttribute('data-pengumuman-id');
            fetch(`/pengumuman/${pengumumanId}/detail`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('pengumumanModalLabel').textContent = data.judul;
                    document.getElementById('pengumumanTanggal').textContent = new Date(data.tanggal_aktif).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
                    document.getElementById('pengumumanIsi').innerHTML = data.isi || '<em>Tidak ada detail pengumuman</em>';
                    
                    const badgeEl = document.getElementById('pengumumanKategori');
                    badgeEl.textContent = data.kategori;
                    badgeEl.className = 'badge ' + 
                        (data.kategori === 'Penting' ? 'bg-pink' : 
                         data.kategori === 'Informasi' ? 'bg-info' : 
                         'bg-success');
                    
                    const modal = new bootstrap.Modal(document.getElementById('pengumumanModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat detail pengumuman');
                });
        });
    });

    // Auto-scroll Ticker Pengumuman Berjalan Smooth
    const tickerContainer = document.getElementById('pengumumanTickerContainer');
    const tickerList = document.getElementById('pengumumanTickerList');

    if (tickerContainer && tickerList && tickerList.children.length >= 2) {
        // Duplicate list items to create seamless loop
        tickerList.innerHTML += tickerList.innerHTML;

        let isTickerHovered = false;
        tickerContainer.addEventListener('mouseenter', () => isTickerHovered = true);
        tickerContainer.addEventListener('mouseleave', () => isTickerHovered = false);

        setInterval(() => {
            if (!isTickerHovered) {
                if (tickerContainer.scrollTop >= (tickerList.scrollHeight / 2)) {
                    tickerContainer.scrollTop = 0;
                } else {
                    tickerContainer.scrollTop += 1;
                }
            }
        }, 35);
    }
});
</script>
@endsection