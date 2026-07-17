@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 animate__animated animate__fadeInUp">
                
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small">Detail Layanan</p>
                        <h2 class="mb-2">Imunisasi dan Gizi Anak</h2>
                    </div>
                    <a href="{{ route('posyandu') }}" class="btn btn-soft">Kembali</a>
                </div>

                <div class="alert alert-info border-0 shadow-sm rounded-4">
                    <i class="fas fa-info-circle me-2"></i>Layanan pemberian imunisasi dasar lengkap untuk mencegah penyakit yang dapat dicegah dengan imunisasi (PD3I) dan pemantauan asupan gizi. Jadwal Posyandu dan daftar anak disesuaikan secara otomatis berdasarkan wilayah RT tempat tinggal warga di RW 013.
                </div>

                <!-- Grid Card Posyandu Ringkas (Clickable) -->
                <div class="mt-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-clinic-medical text-primary me-2"></i>Pilih Posyandu Wilayah RW 013</h5>
                    <p class="text-muted small mb-4"><i class="fas fa-hand-pointer text-info me-1"></i> Klik kartu Posyandu untuk melihat jadwal lengkap, fokus pelayanan, dan daftar anak yang terdaftar sesuai alamat RT.</p>

                    <div class="row g-4">
                        @foreach($posyanduJadwal as $index => $pos)
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm posyandu-card overflow-hidden" 
                                     style="border-radius: 18px; cursor: pointer; transition: all 0.3s ease;"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#modalPosyanduDetail"
                                     data-index="{{ $index }}">
                                    
                                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <span class="badge {{ $pos['badge_color'] }} px-3 py-2 rounded-pill shadow-sm fs-6">{{ $pos['minggu'] }}</span>
                                                <span class="badge bg-light text-dark border"><i class="fas fa-users text-primary me-1"></i> {{ $pos['anak']->count() }} Anak Terdaftar</span>
                                            </div>
                                            
                                            <h4 class="fw-bold text-dark mb-2">{{ $pos['nama'] }}</h4>
                                            
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-clock text-primary me-1"></i> {{ $pos['jadwal'] }}
                                            </p>
                                            
                                            <p class="text-muted small mb-3">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $pos['lokasi'] }}
                                            </p>
                                            
                                            <div class="p-2 bg-light rounded-3 mb-3">
                                                <small class="text-uppercase text-secondary d-block fw-bold mb-1" style="font-size: 0.75rem;">Cakupan Wilayah</small>
                                                <span class="badge bg-primary-subtle text-primary border"><i class="fas fa-home me-1"></i> {{ $pos['cakupan'] }}</span>
                                            </div>
                                        </div>

                                        <div class="pt-3 border-top d-flex align-items-center justify-content-between text-primary fw-semibold small">
                                            <span>Lihat Detail & Daftar Anak</span>
                                            <i class="fas fa-arrow-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Posyandu & Daftar Anak -->
<div class="modal fade" id="modalPosyanduDetail" tabindex="-1" aria-labelledby="modalPosyanduDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <span class="badge mb-2 shadow-sm fs-6 px-3 py-2" id="mPosBadge">Minggu 1</span>
                    <h3 class="modal-title fw-bold text-dark" id="mPosNama">Nama Posyandu</h3>
                    <p class="text-muted small mb-0"><i class="fas fa-clock me-1 text-primary"></i> <span id="mPosJadwal"></span></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="align-self: flex-start;"></button>
            </div>
            
            <div class="modal-body px-4 py-4">
                
                <!-- Detail Posyandu & Lokasi -->
                <div class="card card-body bg-light border-0 rounded-4 p-3 mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> Lokasi Posyandu</span>
                            <strong class="text-dark small" id="mPosLokasi">-</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1"><i class="fas fa-home me-1 text-primary"></i> Wilayah Cakupan RT</span>
                            <strong class="text-primary small" id="mPosCakupan">-</strong>
                        </div>
                    </div>
                    <hr class="my-2 border-secondary opacity-25">
                    <div>
                        <small class="text-uppercase text-secondary fw-bold d-block mb-1">Fokus Pelayanan Utama</small>
                        <p class="mb-0 text-dark small fw-medium" id="mPosFokus">-</p>
                    </div>
                </div>

                <!-- Tabel Daftar Anak Terdaftar sesuai RT tempat tinggal -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-secondary small text-uppercase">
                        <i class="fas fa-baby text-primary me-1"></i> Daftar Anak / Balita Terdaftar (<span id="mPosTotalAnak">0</span> Anak)
                    </h6>
                    <span class="badge bg-primary-subtle text-primary small px-2 py-1" id="mPosCakupanBadge">Cakupan RT</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle border rounded-3 overflow-hidden mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Nama Anak / Balita</th>
                                <th>Jenis Kelamin</th>
                                <th>Usia (Bulan)</th>
                                <th>Alamat Tempat Tinggal</th>
                                <th>Status Imunisasi & Gizi</th>
                            </tr>
                        </thead>
                        <tbody id="mPosAnakList">
                            <!-- Data populated via JS -->
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4 w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .posyandu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const posyanduData = @json($posyanduJadwal);
        const modalElement = document.getElementById('modalPosyanduDetail');
        
        modalElement.addEventListener('show.bs.modal', function (event) {
            const card = event.relatedTarget;
            const index = card.getAttribute('data-index');
            const pos = posyanduData[index];

            if (!pos) return;

            document.getElementById('mPosNama').textContent = pos.nama;
            document.getElementById('mPosJadwal').textContent = pos.jadwal;
            document.getElementById('mPosLokasi').textContent = pos.lokasi;
            document.getElementById('mPosCakupan').textContent = pos.cakupan;
            document.getElementById('mPosCakupanBadge').textContent = 'Khusus Warga ' + pos.cakupan;
            document.getElementById('mPosFokus').textContent = pos.fokus;
            document.getElementById('mPosTotalAnak').textContent = pos.anak ? pos.anak.length : 0;

            const badgeEl = document.getElementById('mPosBadge');
            badgeEl.className = 'badge mb-2 shadow-sm fs-6 px-3 py-2 ' + pos.badge_color;
            badgeEl.textContent = pos.minggu;

            const tbody = document.getElementById('mPosAnakList');
            if (!pos.anak || pos.anak.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <em>Belum ada data anak terdaftar di wilayah ${pos.cakupan}.</em>
                        </td>
                    </tr>
                `;
            } else {
                let html = '';
                pos.anak.forEach((anak, i) => {
                    const birth = new Date(anak.tanggal_lahir);
                    const now = new Date();
                    let ageInMonths = (now.getFullYear() - birth.getFullYear()) * 12 + (now.getMonth() - birth.getMonth());
                    if (ageInMonths < 0) ageInMonths = 0;

                    const formattedDate = birth.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

                    let genderBadge = anak.gender === 'Laki-laki' 
                        ? '<span class="badge bg-primary-subtle text-primary border"><i class="fas fa-mars me-1"></i>Laki-laki</span>'
                        : '<span class="badge bg-danger-subtle text-danger border"><i class="fas fa-venus me-1"></i>Perempuan</span>';

                    let statusBadge = '';
                    if (ageInMonths <= 6) {
                        statusBadge = '<span class="badge bg-info text-white"><i class="fas fa-syringe me-1"></i> Imunisasi Dasar (BCG/Polio)</span>';
                    } else if (ageInMonths <= 18) {
                        statusBadge = '<span class="badge bg-success"><i class="fas fa-shield-alt me-1"></i> Imunisasi Lanjutan & Vitamin A</span>';
                    } else {
                        statusBadge = '<span class="badge bg-secondary"><i class="fas fa-apple-alt me-1"></i> Pemantauan PMT & Gizi</span>';
                    }

                    html += `
                        <tr>
                            <td>${i + 1}</td>
                            <td class="fw-bold text-dark">${anak.name}</td>
                            <td>${genderBadge}</td>
                            <td>${ageInMonths} bulan <br><small class="text-muted">(${formattedDate})</small></td>
                            <td><i class="fas fa-home text-muted me-1"></i> ${anak.address}</td>
                            <td>${statusBadge}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }
        });
    });
</script>
@endsection