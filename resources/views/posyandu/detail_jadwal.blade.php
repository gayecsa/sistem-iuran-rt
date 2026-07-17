@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 animate__animated animate__fadeInUp">
                
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small">Informasi Posyandu</p>
                        <h2 class="mb-2">Jadwal Pelayanan Posyandu</h2>
                    </div>
                    <a href="{{ route('posyandu') }}" class="btn btn-soft">Kembali</a>
                </div>

                <div class="alert alert-info">
                    Pelayanan Posyandu rutin dilaksanakan setiap bulan dengan fokus kegiatan yang berbeda setiap minggunya. Berikut adalah rincian jadwal pelayanan yang dapat Anda ikuti.
                </div>

                <div class="mt-4">
                    <p class="text-info small mb-3"><i class="fas fa-hand-pointer me-1"></i> Klik pada baris jadwal untuk melihat detail dokter penanggung jawab dan lokasi Posyandu.</p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover shadow-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Minggu Ke-</th>
                                    <th>Hari & Waktu</th>
                                    <th>Fokus Pelayanan Utama</th>
                                    <th>Dokter / Tenaga Medis</th>
                                    <th>Lokasi Posyandu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <tr class="jadwal-row" style="cursor: pointer;" 
                                     data-minggu="Minggu 1"
                                     data-waktu="Sabtu, 08:00 - 11:00 WIB"
                                     data-fokus="Pemeriksaan Balita, Penimbangan, & Imunisasi Dasar"
                                     data-dokter="dr. Amanda Sp.A & Bidan Ratna, S.ST"
                                     data-lokasi="Posyandu Sejahtera (Balai Warga RT 001)"
                                     data-detail="Penimbangan berat badan, pengukuran tinggi badan, imunisasi rutin (BCG, DPT, Polio, Campak), pemberian Vitamin A gratis, serta konsultasi gizi balita.">
                                     <td><span class="fw-bold text-primary">Minggu 1</span></td>
                                     <td>Sabtu, 08:00 - 11:00 WIB</td>
                                     <td>Pemeriksaan Balita, Penimbangan, & Imunisasi Dasar</td>
                                     <td><span class="badge bg-primary-subtle text-primary border"><i class="fas fa-user-md me-1"></i> dr. Amanda Sp.A</span></td>
                                     <td><small class="text-secondary"><i class="fas fa-map-marker-alt text-danger me-1"></i> Posyandu Sejahtera (Balai RT 001)</small></td>
                                     <td class="text-center"><button class="btn btn-sm btn-outline-primary rounded-circle"><i class="fas fa-chevron-right"></i></button></td>
                                 </tr>
                                 <tr class="jadwal-row" style="cursor: pointer;" 
                                     data-minggu="Minggu 2"
                                     data-waktu="Sabtu, 08:00 - 11:00 WIB"
                                     data-fokus="Pemeriksaan Ibu Hamil & Konsultasi Persalinan"
                                     data-dokter="dr. Farhan Sp.OG & Bidan Melati, S.Tr.Keb"
                                     data-lokasi="Posyandu Kasih Ibu (Pos Kesehatan RT 003)"
                                     data-detail="Pemeriksaan kesehatan rutin ibu hamil, pengukuran tekanan darah dan LILA, pemberian suplemen zat besi/asam folat, pemeriksaan USG dasar, serta konsultasi kesiapan persalinan.">
                                     <td><span class="fw-bold text-success">Minggu 2</span></td>
                                     <td>Sabtu, 08:00 - 11:00 WIB</td>
                                     <td>Pemeriksaan Ibu Hamil & Konsultasi Persalinan</td>
                                     <td><span class="badge bg-success-subtle text-success border"><i class="fas fa-user-md me-1"></i> dr. Farhan Sp.OG</span></td>
                                     <td><small class="text-secondary"><i class="fas fa-map-marker-alt text-danger me-1"></i> Posyandu Kasih Ibu (RT 003)</small></td>
                                     <td class="text-center"><button class="btn btn-sm btn-outline-success rounded-circle"><i class="fas fa-chevron-right"></i></button></td>
                                 </tr>
                                 <tr class="jadwal-row" style="cursor: pointer;" 
                                     data-minggu="Minggu 3"
                                     data-waktu="Sabtu, 08:00 - 11:00 WIB"
                                     data-fokus="Pemberian Makanan Tambahan (PMT) & Cek Kesehatan Lansia"
                                     data-dokter="dr. Setyo Utomo (Dokter Umum) & Tim Ahli Gizi"
                                     data-lokasi="Posyandu Tunas Bangsa (Pos Sekretariat RW 013)"
                                     data-detail="Pembagian paket PMT bergizi untuk balita, pemeriksaan kesehatan lansia (cek gula darah, kolesterol, asam urat), penimbangan, dan penyuluhan gizi seimbang.">
                                     <td><span class="fw-bold text-warning">Minggu 3</span></td>
                                     <td>Sabtu, 08:00 - 11:00 WIB</td>
                                     <td>Pemberian Makanan Tambahan (PMT) & Cek Kesehatan Lansia</td>
                                     <td><span class="badge bg-warning-subtle text-warning-emphasis border"><i class="fas fa-user-md me-1"></i> dr. Setyo Utomo</span></td>
                                     <td><small class="text-secondary"><i class="fas fa-map-marker-alt text-danger me-1"></i> Posyandu Tunas Bangsa (RW 013)</small></td>
                                     <td class="text-center"><button class="btn btn-sm btn-outline-warning rounded-circle"><i class="fas fa-chevron-right"></i></button></td>
                                 </tr>
                                 <tr class="jadwal-row" style="cursor: pointer;" 
                                     data-minggu="Minggu 4"
                                     data-waktu="Sabtu, 08:00 - 11:00 WIB"
                                     data-fokus="Edukasi Kesehatan Keluarga, KB, & Penyuluhan PHBS"
                                     data-dokter="dr. Nabila Putri & Kader Kesehatan Posyandu"
                                     data-lokasi="Posyandu Harapan Bunda (Ruang Serbaguna RT 007)"
                                     data-detail="Penyuluhan interaktif mengenai Pola Hidup Bersih dan Sehat (PHBS), konsultasi pelayanan Keluarga Berencana (KB), pencegahan penyakit menular, serta pembagian alat kontrasepsi rutin.">
                                     <td><span class="fw-bold text-info">Minggu 4</span></td>
                                     <td>Sabtu, 08:00 - 11:00 WIB</td>
                                     <td>Edukasi Kesehatan Keluarga, KB, & Penyuluhan PHBS</td>
                                     <td><span class="badge bg-info-subtle text-info border"><i class="fas fa-user-md me-1"></i> dr. Nabila Putri</span></td>
                                     <td><small class="text-secondary"><i class="fas fa-map-marker-alt text-danger me-1"></i> Posyandu Harapan Bunda (RT 007)</small></td>
                                     <td class="text-center"><button class="btn btn-sm btn-outline-info rounded-circle"><i class="fas fa-chevron-right"></i></button></td>
                                 </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Jadwal -->
<div class="modal fade" id="modalJadwalDetail" tabindex="-1" aria-labelledby="modalJadwalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <span class="badge bg-primary mb-2 shadow-sm" id="jModalMinggu">Minggu 1</span>
                    <h4 class="modal-title fw-bold text-dark" id="jModalFokus">Fokus Pelayanan</h4>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="align-self: flex-start;"></button>
            </div>
            
            <div class="modal-body px-4 py-4">
                <div class="bg-light p-3 rounded-4 mb-4">
                    <div class="row g-2 mb-2">
                        <div class="col-4 text-muted small"><i class="far fa-clock me-1"></i> Waktu</div>
                        <div class="col-8 fw-semibold text-dark small" id="jModalWaktu">-</div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-4 text-muted small"><i class="fas fa-user-md me-1"></i> Dok/Tenaga Medis</div>
                        <div class="col-8 fw-bold text-primary small" id="jModalDokter">-</div>
                    </div>
                    <div class="row g-2">
                        <div class="col-4 text-muted small"><i class="fas fa-map-marker-alt me-1 text-danger"></i> Lokasi Posyandu</div>
                        <div class="col-8 small text-dark fw-medium" id="jModalLokasi">-</div>
                    </div>
                </div>

                <h6 class="fw-bold mb-2 text-secondary small text-uppercase">Rincian Kegiatan & Fasilitas</h6>
                <div class="p-3 bg-white border rounded-4 shadow-sm mb-3">
                    <p class="mb-0 text-muted small lh-lg" id="jModalDetail">-</p>
                </div>

                <div class="alert alert-success border-0 rounded-4 shadow-sm mb-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fs-4 me-3 text-success"></i>
                        <div class="small">
                            <strong>Gratis untuk Warga RW 013!</strong> Mohon membawa Buku KIA/KMS atau Kartu Identitas Berobat saat berkunjung.
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
        const rows = document.querySelectorAll('.jadwal-row');
        const modal = new bootstrap.Modal(document.getElementById('modalJadwalDetail'));

        rows.forEach(row => {
            row.addEventListener('click', function () {
                document.getElementById('jModalMinggu').textContent = this.getAttribute('data-minggu');
                document.getElementById('jModalFokus').textContent = this.getAttribute('data-fokus');
                document.getElementById('jModalWaktu').textContent = this.getAttribute('data-waktu');
                document.getElementById('jModalDokter').textContent = this.getAttribute('data-dokter');
                document.getElementById('jModalLokasi').textContent = this.getAttribute('data-lokasi');
                document.getElementById('jModalDetail').textContent = this.getAttribute('data-detail');

                modal.show();
            });
        });
    });
</script>
@endsection