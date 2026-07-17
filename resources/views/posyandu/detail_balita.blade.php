@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 animate__animated animate__fadeInUp">
                
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small">Detail Layanan</p>
                        <h2 class="mb-2">Pemeriksaan Kesehatan Balita</h2>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ route('posyandu.detail_balita') }}" method="GET" class="d-flex mb-0">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama balita..." value="{{ request('search') }}" style="border-radius: 8px 0 0 8px;">
                            <button type="submit" class="btn btn-primary" style="border-radius: 0 8px 8px 0;"><i class="fas fa-search"></i></button>
                        </form>
                        <a href="{{ route('posyandu') }}" class="btn btn-soft">Kembali</a>
                    </div>
                </div>

                <div class="alert alert-info">
                    Halaman ini berisi informasi detail mengenai layanan pemeriksaan dan pemantauan tumbuh kembang balita di RW 013.
                </div>

                    <div class="mt-4 mb-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-baby text-primary me-2"></i>Daftar Balita RW 013</h5>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Balita / Bayi</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Usia (Bulan)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($balitas as $index => $balita)
                                        @php
                                            $birthDate = \Carbon\Carbon::parse($balita->tanggal_lahir);
                                            $ageInMonths = (int) $birthDate->diffInMonths(\Carbon\Carbon::now());
                                        @endphp
                                        <tr class="balita-row" data-id="{{ $balita->id }}" style="cursor: pointer;" title="Klik untuk melihat detail">
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-bold text-primary">
                                                {{ $balita->name }}
                                                <i class="fas fa-external-link-alt ms-1 small text-muted"></i>
                                            </td>
                                            <td>
                                                @if($balita->gender == 'Laki-laki')
                                                    <span class="badge bg-primary"><i class="fas fa-mars me-1"></i>Laki-laki</span>
                                                @else
                                                    <span class="badge bg-pink text-white" style="background-color: #e83e8c;"><i class="fas fa-venus me-1"></i>Perempuan</span>
                                                @endif
                                            </td>
                                            <td>{{ $birthDate->format('d M Y') }}</td>
                                            <td>{{ $ageInMonths }} bulan</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Belum ada data balita.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Balita -->
<div class="modal fade" id="detailBalitaModal" tabindex="-1" aria-labelledby="detailBalitaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <span class="badge mb-2 shadow-sm" id="modalGenderBadge">Jenis Kelamin</span>
                    <h4 class="modal-title fw-bold text-dark" id="modalNamaBalita">Nama Balita</h4>
                    <p class="text-muted small mb-0"><i class="fas fa-calendar-alt me-1"></i> <span id="modalTanggalLahir"></span> (<span id="modalUsia"></span>)</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="align-self: flex-start;"></button>
            </div>
            
            <div class="modal-body px-4 py-4">
                
                <h6 class="fw-bold mb-3 text-secondary small text-uppercase">Informasi Keluarga</h6>
                <div class="bg-light p-3 rounded-4 mb-4">
                    <div class="row g-2 mb-2">
                        <div class="col-4 text-muted small">Ayah</div>
                        <div class="col-8 fw-semibold" id="modalAyah">-</div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-4 text-muted small">Ibu</div>
                        <div class="col-8 fw-semibold" id="modalIbu">-</div>
                    </div>
                    <div class="row g-2">
                        <div class="col-4 text-muted small">Alamat</div>
                        <div class="col-8 small" id="modalAlamat">-</div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-secondary small text-uppercase mb-0">Perkembangan Terakhir</h6>
                    <small class="text-muted" id="modalTanggalTerakhir"></small>
                </div>
                <div class="row g-2 mb-4">
                    <div class="col-4">
                        <div class="border rounded-3 p-2 text-center bg-white shadow-sm">
                            <span class="d-block text-muted small mb-1">Tinggi</span>
                            <strong class="text-primary" id="modalTinggi">-</strong>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded-3 p-2 text-center bg-white shadow-sm">
                            <span class="d-block text-muted small mb-1">Berat</span>
                            <strong class="text-info" id="modalBerat">-</strong>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded-3 p-2 text-center bg-white shadow-sm">
                            <span class="d-block text-muted small mb-1">Status</span>
                            <strong class="text-success small" id="modalStatus">-</strong>
                        </div>
                    </div>
                </div>

                @if(in_array(strtolower(auth()->user()->role), ['admin', 'bendahara']))
                <!-- Form Tambah Perkembangan (Khusus Admin) -->
                <div class="mb-4">
                    <button class="btn btn-sm btn-outline-primary w-100 rounded-pill shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formPerkembanganCollapse" aria-expanded="false" aria-controls="formPerkembanganCollapse">
                        <i class="fas fa-plus-circle me-1"></i> Input Perkembangan Baru
                    </button>

                    <div class="collapse mt-3" id="formPerkembanganCollapse">
                        <div class="card card-body border-0 bg-light rounded-4 shadow-sm p-3">
                            <h6 class="fw-bold mb-3 text-dark small"><i class="fas fa-edit text-primary me-1"></i> Form Perkembangan Balita</h6>
                            <form id="formPerkembanganBalita">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Tanggal Pemeriksaan</label>
                                    <input type="date" class="form-control form-control-sm rounded-3" id="inputTanggal" name="tanggal_pemeriksaan" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label small text-muted mb-1">Tinggi Badan (cm)</label>
                                        <input type="number" step="0.1" class="form-control form-control-sm rounded-3" id="inputTinggi" name="tinggi_badan" placeholder="Contoh: 82.5" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-muted mb-1">Berat Badan (kg)</label>
                                        <input type="number" step="0.1" class="form-control form-control-sm rounded-3" id="inputBerat" name="berat_badan" placeholder="Contoh: 12.4" required>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small text-muted mb-1">Status Gizi</label>
                                    <select class="form-select form-select-sm rounded-3" id="inputStatus" name="status_gizi" required>
                                        <option value="Normal / Sehat" selected>Normal / Sehat</option>
                                        <option value="Risiko Gizi Lebih">Risiko Gizi Lebih</option>
                                        <option value="Gizi Kurang">Gizi Kurang</option>
                                        <option value="Stunting">Stunting</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">Catatan Tambahan</label>
                                    <input type="text" class="form-control form-control-sm rounded-3" id="inputCatatan" name="catatan" placeholder="Opsional (misal: nafsu makan baik)">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill shadow-sm" id="btnSubmitPerkembangan">
                                    <i class="fas fa-save me-1"></i> Simpan Data Perkembangan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Section Riwayat Pemeriksaan -->
                <div class="mb-4 d-none" id="sectionRiwayat">
                    <h6 class="fw-bold mb-2 text-secondary small text-uppercase">Riwayat Pemeriksaan</h6>
                    <div class="list-group list-group-flush rounded-3 border-0 small" id="modalRiwayatList" style="max-height: 180px; overflow-y: auto;">
                        <!-- Content via JS -->
                    </div>
                </div>

                <div class="alert alert-warning mb-0 border-0 rounded-4 shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="fs-3 me-3 text-warning">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">Jadwal Posyandu Berikutnya</h6>
                            <p class="mb-0 small" id="modalJadwal">-</p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary rounded-pill w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('.balita-row');
        const modalElement = document.getElementById('detailBalitaModal');
        const modal = new bootstrap.Modal(modalElement);
        let currentBalitaId = null;
        
        function loadBalitaDetail(id) {
            document.getElementById('modalNamaBalita').textContent = 'Memuat...';
            
            fetch(`/posyandu/detail-balita/${id}`)
                .then(response => response.json())
                .then(data => {
                    currentBalitaId = data.id;
                    
                    // Update Modal Content
                    document.getElementById('modalNamaBalita').textContent = data.nama;
                    document.getElementById('modalTanggalLahir').textContent = data.tanggal_lahir;
                    document.getElementById('modalUsia').textContent = data.usia;
                    
                    const genderBadge = document.getElementById('modalGenderBadge');
                    if(data.gender === 'Laki-laki') {
                        genderBadge.className = 'badge bg-primary mb-2 shadow-sm';
                        genderBadge.innerHTML = '<i class="fas fa-mars me-1"></i> Laki-laki';
                    } else {
                        genderBadge.className = 'badge text-white mb-2 shadow-sm';
                        genderBadge.style.backgroundColor = '#e83e8c';
                        genderBadge.innerHTML = '<i class="fas fa-venus me-1"></i> Perempuan';
                    }
                    
                    document.getElementById('modalAyah').textContent = data.ayah;
                    document.getElementById('modalIbu').textContent = data.ibu;
                    document.getElementById('modalAlamat').textContent = data.alamat;
                    
                    document.getElementById('modalTinggi').textContent = data.perkembangan.tinggi;
                    document.getElementById('modalBerat').textContent = data.perkembangan.berat;
                    document.getElementById('modalStatus').textContent = data.perkembangan.status;
                    
                    const elTanggalTerakhir = document.getElementById('modalTanggalTerakhir');
                    if (elTanggalTerakhir) {
                        elTanggalTerakhir.textContent = data.perkembangan.tanggal_terakhir;
                    }
                    
                    document.getElementById('modalJadwal').textContent = data.jadwal_berikutnya;

                    // Render Riwayat
                    const sectionRiwayat = document.getElementById('sectionRiwayat');
                    const riwayatList = document.getElementById('modalRiwayatList');
                    if (sectionRiwayat && riwayatList) {
                        if (data.riwayat && data.riwayat.length > 0) {
                            sectionRiwayat.classList.remove('d-none');
                            let html = '';
                            data.riwayat.forEach(item => {
                                html += `
                                    <div class="list-group-item bg-light border-0 mb-1 rounded-3 p-2">
                                        <div class="d-flex justify-content-between fw-bold text-dark mb-1">
                                            <span><i class="fas fa-calendar-day text-secondary me-1"></i> ${item.tanggal}</span>
                                            <span class="badge bg-success">${item.status}</span>
                                        </div>
                                        <div class="d-flex gap-3 text-muted small">
                                            <span>Tinggi: <strong>${item.tinggi}</strong></span>
                                            <span>Berat: <strong>${item.berat}</strong></span>
                                        </div>
                                        ${item.catatan && item.catatan !== '-' ? `<div class="text-muted fst-italic mt-1 small">"${item.catatan}"</div>` : ''}
                                    </div>
                                `;
                            });
                            riwayatList.innerHTML = html;
                        } else {
                            sectionRiwayat.classList.add('d-none');
                        }
                    }
                    
                    modal.show();
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    alert('Gagal mengambil data detail balita.');
                });
        }

        rows.forEach(row => {
            row.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                // Reset form collapse if open
                const collapseEl = document.getElementById('formPerkembanganCollapse');
                if (collapseEl && collapseEl.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                    if (bsCollapse) bsCollapse.hide();
                }
                loadBalitaDetail(id);
            });
        });

        // Form Submit Handler (Admin)
        const formPerkembangan = document.getElementById('formPerkembanganBalita');
        if (formPerkembangan) {
            formPerkembangan.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!currentBalitaId) return;

                const btnSubmit = document.getElementById('btnSubmitPerkembangan');
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

                const formData = new FormData(formPerkembangan);

                fetch(`/posyandu/detail-balita/${currentBalitaId}/perkembangan`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Data Perkembangan';

                    if (res.success) {
                        alert(res.message);
                        formPerkembangan.reset();
                        document.getElementById('inputTanggal').value = new Date().toISOString().split('T')[0];
                        
                        // Hide collapse
                        const collapseEl = document.getElementById('formPerkembanganCollapse');
                        if (collapseEl) {
                            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                            if (bsCollapse) bsCollapse.hide();
                        }

                        // Reload modal content
                        loadBalitaDetail(currentBalitaId);
                    } else {
                        alert(res.message || 'Terjadi kesalahan saat menyimpan data.');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Data Perkembangan';
                    alert('Gagal menyimpan data perkembangan.');
                });
            });
        }
    });
</script>
@endsection