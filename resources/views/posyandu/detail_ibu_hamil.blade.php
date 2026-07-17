@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 animate__animated animate__fadeInUp">
                
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small">Detail Layanan</p>
                        <h2 class="mb-2">Konsultasi Ibu Hamil</h2>
                    </div>
                    <a href="{{ route('posyandu') }}" class="btn btn-soft">Kembali</a>
                </div>

                <div class="alert alert-info border-0 shadow-sm rounded-4">
                    <i class="fas fa-info-circle me-2"></i>Layanan pemeriksaan kehamilan rutin dan persiapan persalinan untuk memastikan kesehatan ibu dan janin. Jadwal Posyandu dan daftar peserta ibu terdaftar disesuaikan secara otomatis berdasarkan wilayah RT tempat tinggal warga di RW 013.
                </div>

                <!-- Grid Card Posyandu Ibu Hamil Ringkas (Clickable) -->
                <div class="mt-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-female text-primary me-2"></i>Pilih Posyandu Ibu Hamil Wilayah RW 013</h5>
                    <p class="text-muted small mb-4"><i class="fas fa-hand-pointer text-info me-1"></i> Klik kartu Posyandu untuk melihat jadwal lengkap, tenaga medis, dan daftar ibu hamil yang terdaftar sesuai alamat RT.</p>

                    <div class="row g-4">
                        @foreach($posyanduJadwal as $index => $pos)
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm posyandu-card overflow-hidden" 
                                     style="border-radius: 18px; cursor: pointer; transition: all 0.3s ease;"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#modalPosyanduIbuDetail"
                                     data-index="{{ $index }}">
                                    
                                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <span class="badge {{ $pos['badge_color'] }} px-3 py-2 rounded-pill shadow-sm fs-6">{{ $pos['minggu'] }}</span>
                                                <span class="badge bg-light text-dark border"><i class="fas fa-female text-primary me-1"></i> {{ $pos['ibu']->count() }} Ibu Terdaftar</span>
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
                                            <span>Lihat Detail & Daftar Ibu Hamil</span>
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

<!-- Modal Detail Posyandu Ibu Hamil & Daftar Peserta -->
<div class="modal fade" id="modalPosyanduIbuDetail" tabindex="-1" aria-labelledby="modalPosyanduIbuDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <span class="badge mb-2 shadow-sm fs-6 px-3 py-2" id="mIbuBadge">Minggu 1</span>
                    <h3 class="modal-title fw-bold text-dark" id="mIbuNama">Nama Posyandu</h3>
                    <p class="text-muted small mb-0"><i class="fas fa-clock me-1 text-primary"></i> <span id="mIbuJadwal"></span></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="align-self: flex-start;"></button>
            </div>
            
            <div class="modal-body px-4 py-4">
                
                <!-- Detail Posyandu & Lokasi -->
                <div class="card card-body bg-light border-0 rounded-4 p-3 mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1"><i class="fas fa-user-md text-primary me-1"></i> Dokter / Bidan Penanggung Jawab</span>
                            <strong class="text-primary small" id="mIbuTenagaMedis">-</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> Lokasi Pelayanan</span>
                            <strong class="text-dark small" id="mIbuLokasi">-</strong>
                        </div>
                    </div>
                    <hr class="my-2 border-secondary opacity-25">
                    <div>
                        <small class="text-uppercase text-secondary fw-bold d-block mb-1">Fokus Pelayanan Kehamilan</small>
                        <p class="mb-0 text-dark small fw-medium" id="mIbuFokus">-</p>
                    </div>
                </div>

                <!-- Tabel Daftar Ibu Hamil / Peserta sesuai RT tempat tinggal -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-secondary small text-uppercase">
                        <i class="fas fa-female text-primary me-1"></i> Daftar Peserta Ibu Hamil Terdaftar (<span id="mIbuTotal">0</span> Ibu)
                    </h6>
                    <span class="badge bg-primary-subtle text-primary small px-2 py-1" id="mIbuCakupanBadge">Cakupan RT</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle border rounded-3 overflow-hidden mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Nama Ibu</th>
                                <th>Usia</th>
                                <th>Alamat Tempat Tinggal</th>
                                <th>Usia Kehamilan (Trimester)</th>
                                <th>Status Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody id="mIbuAnakList">
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
        const modalElement = document.getElementById('modalPosyanduIbuDetail');
        
        modalElement.addEventListener('show.bs.modal', function (event) {
            const card = event.relatedTarget;
            const index = card.getAttribute('data-index');
            const pos = posyanduData[index];

            if (!pos) return;

            document.getElementById('mIbuNama').textContent = pos.nama;
            document.getElementById('mIbuJadwal').textContent = pos.jadwal;
            document.getElementById('mIbuLokasi').textContent = pos.lokasi;
            document.getElementById('mIbuTenagaMedis').textContent = pos.tenaga_medis;
            document.getElementById('mIbuCakupanBadge').textContent = 'Khusus Warga ' + pos.cakupan;
            document.getElementById('mIbuFokus').textContent = pos.fokus;
            document.getElementById('mIbuTotal').textContent = pos.ibu ? pos.ibu.length : 0;

            const badgeEl = document.getElementById('mIbuBadge');
            badgeEl.className = 'badge mb-2 shadow-sm fs-6 px-3 py-2 ' + pos.badge_color;
            badgeEl.textContent = pos.minggu;

            const tbody = document.getElementById('mIbuAnakList');
            if (!pos.ibu || pos.ibu.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <em>Belum ada data peserta ibu terdaftar di wilayah ${pos.cakupan}.</em>
                        </td>
                    </tr>
                `;
            } else {
                let html = '';
                const sampleTrimesters = ['Trimester 1 (8-12 Mgg)', 'Trimester 2 (20-24 Mgg)', 'Trimester 3 (32-36 Mgg)'];
                const sampleStatuses = ['Pemeriksaan Rutin', 'Perlu Suplemen Fe', 'Persiapan Persalinan', 'Cek Tekanan Darah'];

                pos.ibu.forEach((ibu, i) => {
                    const birth = new Date(ibu.tanggal_lahir);
                    const now = new Date();
                    let ageYears = now.getFullYear() - birth.getFullYear();

                    const trimesterIndex = (i % 3);
                    const trimesterText = sampleTrimesters[trimesterIndex];
                    const statusText = sampleStatuses[i % 4];

                    let statusBadge = '<span class="badge bg-success-subtle text-success border"><i class="fas fa-check-circle me-1"></i> ' + statusText + '</span>';

                    html += `
                        <tr>
                            <td>${i + 1}</td>
                            <td class="fw-bold text-dark">${ibu.name}</td>
                            <td>${ageYears} tahun</td>
                            <td><i class="fas fa-home text-muted me-1"></i> ${ibu.address}</td>
                            <td><span class="badge bg-primary-subtle text-primary border">${trimesterText}</span></td>
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