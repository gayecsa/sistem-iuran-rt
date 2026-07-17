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
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover shadow-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Minggu Ke-</th>
                                    <th>Hari & Waktu</th>
                                    <th>Fokus Pelayanan Utama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="fw-bold">Minggu 1</span></td>
                                    <td>Sabtu, 08:00 - 11:00 WIB</td>
                                    <td>Pemeriksaan Balita, Penimbangan, & Imunisasi Dasar</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">Minggu 2</span></td>
                                    <td>Sabtu, 08:00 - 11:00 WIB</td>
                                    <td>Pemeriksaan Ibu Hamil & Konsultasi Persalinan</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">Minggu 3</span></td>
                                    <td>Sabtu, 08:00 - 11:00 WIB</td>
                                    <td>Pemberian Makanan Tambahan (PMT) & Cek Kesehatan Lansia</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">Minggu 4</span></td>
                                    <td>Sabtu, 08:00 - 11:00 WIB</td>
                                    <td>Edukasi Kesehatan Keluarga, KB, & Penyuluhan PHBS</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection