@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card sideboard-panel p-4 h-100 animate__animated animate__fadeInUp">
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
                        <div class="gradient-pill">Tambahkan Informasi</div>
                    </div>
                </div>

                <div class="mt-4 mb-3">
                    @if(in_array(auth()->user()->role, ['admin', 'bendahara']))
                        <a href="{{ route('warga.index') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-users me-2"></i>Data Warga
                        </a>
                    @endif
                    <a href="{{ route('laporan.keuangan') }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Laporan Keuangan
                    </a>
                    <a href="{{ route('keuangan.detail') }}" class="btn btn-outline-success">
                        <i class="fas fa-balance-scale me-2"></i>Saldo Kas
                    </a>
                </div>

                <div class="row mt-4 gy-3">
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
                        <div class="card metric-card p-3 text-center">
                            <span class="text-muted">Saldo Kas</span>
                            <h5 class="mt-2 mb-0">Rp {{ number_format($saldo_kas ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-7">
                    <div class="card chart-card p-4 animate__animated animate__fadeInUp">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h6 class="mb-1">Komposisi Gender Warga</h6>
                                <small class="text-muted">Perbandingan jumlah warga Laki-laki dan Perempuan</small>
                            </div>
                            <div class="icon-circle shadow-sm" style="background: #f8fafc; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-venus-mars text-primary"></i>
                            </div>
                        </div>
                        <div style="height: 260px; display: flex; align-items: center; justify-content: center;">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card chart-card p-4 animate__animated animate__fadeInUp">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h6 class="mb-1">Pengumuman</h6>
                                <small class="text-muted">Info penting RT</small>
                            </div>
                            <a href="{{ route('pengumuman.index') }}" class="badge bg-white text-secondary py-2 px-3 shadow-sm text-decoration-none">Terbaru</a>
                        </div>
                        <ul class="list-group recent-list list-group-flush">
                            @forelse($pengumuman ?? [] as $p)
                                <li class="list-group-item pengumuman-item" style="cursor: pointer;" data-pengumuman-id="{{ $p->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $p->judul }}</h6>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($p->tanggal_aktif)->format('d M Y') }}</small>
                                        </div>
                                        <span class="badge {{ $p->kategori == 'Penting' ? 'bg-pink' : ($p->kategori == 'Informasi' ? 'bg-info' : 'bg-success') }} text-white rounded-pill">{{ $p->kategori }}</span>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center py-3">Belum ada pengumuman</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pengumuman -->
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
                        '#3b82f6', // Blue for Laki-laki
                        '#ec4899'  // Pink for Perempuan
                    ],
                    borderColor: [
                        '#1e40af',
                        '#be185d'
                    ],
                    borderWidth: 2
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
});
</script>
@endsection