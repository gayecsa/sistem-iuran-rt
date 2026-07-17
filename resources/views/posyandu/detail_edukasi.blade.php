@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 animate__animated animate__fadeInUp">
                
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small">Detail Layanan</p>
                        <h2 class="mb-2">Edukasi Kesehatan Keluarga</h2>
                    </div>
                    <a href="{{ route('posyandu') }}" class="btn btn-soft">Kembali</a>
                </div>

                <div class="alert alert-info">
                    Program penyuluhan dan diskusi interaktif mengenai Perilaku Hidup Bersih dan Sehat (PHBS) di lingkungan keluarga.
                </div>

                <div class="mt-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-list-check text-primary me-2"></i>Topik Edukasi Bulan Ini:</h5>
                    <ul class="list-group list-group-flush rounded-3 border-0 mb-4">
                        <li class="list-group-item bg-light border-0 mb-1 rounded-3"><i class="fas fa-check-circle text-success me-2"></i> Pentingnya sanitasi dan cuci tangan pakai sabun (CTPS)</li>
                        <li class="list-group-item bg-light border-0 mb-1 rounded-3"><i class="fas fa-check-circle text-success me-2"></i> Pencegahan stunting sejak 1.000 Hari Pertama Kehidupan (HPK)</li>
                        <li class="list-group-item bg-light border-0 mb-1 rounded-3"><i class="fas fa-check-circle text-success me-2"></i> Pengolahan sampah rumah tangga & pembuatan komposting mandiri</li>
                        <li class="list-group-item bg-light border-0 mb-1 rounded-3"><i class="fas fa-check-circle text-success me-2"></i> Edukasi Keluarga Berencana (KB) modern & kesehatan reproduksi</li>
                    </ul>
                </div>

                <hr class="my-4 border-secondary opacity-25">

                <!-- Section Seminar Edukasi 3 Bulan Sekali (Triwulan) -->
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-1"><i class="fas fa-chalkboard-teacher text-primary me-2"></i>Jadwal Seminar Edukasi (Setiap 3 Bulan Sekali)</h5>
                            <p class="text-muted small mb-0">Diselenggarakan di lokasi berbeda di wilayah RW 013 untuk seluruh warga.</p>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill"><i class="fas fa-users me-1"></i> Target Audiens: Warga RW 013</span>
                    </div>

                    <div class="row g-4 mt-1">
                        @foreach($seminarJadwal as $index => $sem)
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm seminar-card overflow-hidden" 
                                     style="border-radius: 18px; cursor: pointer; transition: all 0.3s ease;"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#modalSeminarDetail"
                                     data-index="{{ $index }}">
                                    
                                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <span class="badge {{ $sem['badge'] }} px-3 py-2 rounded-pill shadow-sm fs-6">{{ $sem['periode'] }}</span>
                                                <small class="badge bg-light text-dark border"><i class="fas fa-ticket-alt me-1 text-primary"></i> {{ $sem['kuota'] }}</small>
                                            </div>
                                            
                                            <h5 class="fw-bold text-dark mb-2 lh-base">{{ $sem['judul'] }}</h5>
                                            
                                            <p class="text-muted small mb-2">
                                                <i class="far fa-calendar-alt text-primary me-1"></i> {{ $sem['tanggal'] }}
                                            </p>
                                            
                                            <p class="text-muted small mb-3">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i> <strong>{{ $sem['lokasi'] }}</strong>
                                            </p>
                                            
                                            <div class="p-2 bg-light rounded-3 mb-3">
                                                <small class="text-uppercase text-secondary d-block fw-bold mb-1" style="font-size: 0.72rem;">Narasumber / Dokter</small>
                                                <span class="text-dark small fw-medium"><i class="fas fa-user-md text-primary me-1"></i> {{ $sem['narasumber'] }}</span>
                                            </div>
                                        </div>

                                        <div class="pt-3 border-top d-flex align-items-center justify-content-between text-primary fw-semibold small">
                                            <span>Lihat Rincian Seminar & Pendaftaran</span>
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

<!-- Modal Detail Seminar -->
<div class="modal fade" id="modalSeminarDetail" tabindex="-1" aria-labelledby="modalSeminarDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <span class="badge mb-2 shadow-sm fs-6 px-3 py-2" id="sPeriode">Triwulan I</span>
                    <h4 class="modal-title fw-bold text-dark" id="sJudul">Judul Seminar</h4>
                    <p class="text-muted small mb-0"><i class="far fa-calendar-alt me-1 text-primary"></i> <span id="sTanggal"></span></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="align-self: flex-start;"></button>
            </div>
            
            <div class="modal-body px-4 py-4">
                
                <!-- Detail Tempat & Pemateri -->
                <div class="card card-body bg-light border-0 rounded-4 p-3 mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> Lokasi Berbeda (RW 013)</span>
                            <strong class="text-dark small" id="sLokasi">-</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1"><i class="fas fa-user-md text-primary me-1"></i> Narasumber / Pemateri</span>
                            <strong class="text-primary small" id="sNarasumber">-</strong>
                        </div>
                    </div>
                    <hr class="my-2 border-secondary opacity-25">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1"><i class="fas fa-users text-success me-1"></i> Target Audiens / Peserta</span>
                            <strong class="text-success small" id="sAudiens">-</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block mb-1"><i class="fas fa-gift text-warning me-1"></i> Fasilitas Peserta</span>
                            <span class="text-dark small" id="sFasilitas">-</span>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi Seminar -->
                <h6 class="fw-bold mb-2 text-secondary small text-uppercase">Ringkasan Pembahasan Seminar</h6>
                <div class="p-3 bg-white border rounded-4 shadow-sm mb-4">
                    <p class="mb-0 text-muted small lh-lg" id="sDeskripsi">-</p>
                </div>

                <!-- Form Pendaftaran Seminar -->
                <div class="mb-4">
                    <button class="btn btn-primary rounded-pill w-100 shadow-sm py-2 text-white fw-bold mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#formSeminarCollapse" aria-expanded="false" aria-controls="formSeminarCollapse">
                        <i class="fas fa-edit me-1"></i> Form Pendaftaran & Konfirmasi Kehadiran
                    </button>

                    <div class="collapse" id="formSeminarCollapse">
                        <div class="card card-body border-0 bg-light rounded-4 shadow-sm p-3">
                            <h6 class="fw-bold mb-3 text-dark small"><i class="fas fa-user-check text-primary me-1"></i> Form Registrasi Peserta Seminar</h6>
                            <form id="formPendaftaranSeminar">
                                @csrf
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Nama Lengkap Peserta *</label>
                                        <input type="text" class="form-control form-control-sm rounded-3" id="semInputNama" name="nama" value="{{ auth()->user()->name ?? '' }}" placeholder="Nama Warga" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">No. WhatsApp / HP *</label>
                                        <input type="text" class="form-control form-control-sm rounded-3" id="semInputPhone" name="phone" value="{{ auth()->user()->phone ?? '' }}" placeholder="08xxx" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Wilayah RT Domisili *</label>
                                        <select class="form-select form-select-sm rounded-3" id="semInputRt" name="rt" required>
                                            <option value="RT 001">RT 001 / RW 013</option>
                                            <option value="RT 002">RT 002 / RW 013</option>
                                            <option value="RT 003">RT 003 / RW 013</option>
                                            <option value="RT 004">RT 004 / RW 013</option>
                                            <option value="RT 005">RT 005 / RW 013</option>
                                            <option value="RT 006">RT 006 / RW 013</option>
                                            <option value="RT 007">RT 007 / RW 013</option>
                                            <option value="RT 008">RT 008 / RW 013</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Jumlah Anggota Ikut *</label>
                                        <select class="form-select form-select-sm rounded-3" name="jumlah_peserta" required>
                                            <option value="1 Orang (Sendiri)">1 Orang (Sendiri)</option>
                                            <option value="2 Orang (Suami & Istri)">2 Orang (Suami & Istri)</option>
                                            <option value="3+ Orang (Keluarga Besar)">3+ Orang (Keluarga Besar)</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill shadow-sm py-2 fw-semibold" id="btnSubmitSeminar">
                                    <i class="fas fa-paper-plane me-1"></i> Konfirmasi Pendaftaran Seminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Notifikasi Pendaftaran Berhasil -->
                <div class="alert alert-success border-0 rounded-4 shadow-sm mb-0 d-none" id="notifSuksesSeminar">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fs-2 me-3 text-success"></i>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Pendaftaran Berhasil! 🎉</h6>
                            <p class="mb-0 small text-secondary">E-Tiket & Bukti Pendaftaran telah berhasil dicatat untuk <strong id="namaSeminarTerdaftar"></strong>. Informasi kehadiran akan dikirim ke WhatsApp Anda.</p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4 w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .seminar-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const seminarData = @json($seminarJadwal);
        const modalElement = document.getElementById('modalSeminarDetail');
        let activeSeminarJudul = '';
        
        modalElement.addEventListener('show.bs.modal', function (event) {
            const card = event.relatedTarget;
            const index = card.getAttribute('data-index');
            const sem = seminarData[index];

            if (!sem) return;

            activeSeminarJudul = sem.judul;
            document.getElementById('sJudul').textContent = sem.judul;
            document.getElementById('sTanggal').textContent = sem.tanggal;
            document.getElementById('sLokasi').textContent = sem.lokasi;
            document.getElementById('sNarasumber').textContent = sem.narasumber;
            document.getElementById('sAudiens').textContent = sem.audiens;
            document.getElementById('sFasilitas').textContent = sem.fasilitas;
            document.getElementById('sDeskripsi').textContent = sem.deskripsi;

            const badgeEl = document.getElementById('sPeriode');
            badgeEl.className = 'badge mb-2 shadow-sm fs-6 px-3 py-2 ' + sem.badge;
            badgeEl.textContent = sem.periode;

            // Reset form collapse & notifikasi
            const collapseEl = document.getElementById('formSeminarCollapse');
            if (collapseEl && collapseEl.classList.contains('show')) {
                const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                if (bsCollapse) bsCollapse.hide();
            }
            const notifEl = document.getElementById('notifSuksesSeminar');
            if (notifEl) notifEl.classList.add('d-none');
        });

        // Form Submit Handler
        const formPendaftaran = document.getElementById('formPendaftaranSeminar');
        if (formPendaftaran) {
            formPendaftaran.addEventListener('submit', function (e) {
                e.preventDefault();

                const btn = document.getElementById('btnSubmitSeminar');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses Pendaftaran...';

                setTimeout(function () {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Konfirmasi Pendaftaran Seminar';

                    // Collapse form
                    const collapseEl = document.getElementById('formSeminarCollapse');
                    if (collapseEl) {
                        const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                        if (bsCollapse) bsCollapse.hide();
                    }

                    // Tampilkan Notifikasi Berhasil
                    const notifEl = document.getElementById('notifSuksesSeminar');
                    document.getElementById('namaSeminarTerdaftar').textContent = activeSeminarJudul;
                    if (notifEl) {
                        notifEl.classList.remove('d-none');
                        notifEl.scrollIntoView({ behavior: 'smooth' });
                    }
                }, 800);
            });
        }
    });
</script>
@endsection