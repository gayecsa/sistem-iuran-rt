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
                    <!-- Tombol untuk kembali ke halaman utama posyandu -->
                    <a href="{{ route('posyandu') }}" class="btn btn-soft">Kembali</a>
                </div>

                <div class="alert alert-info">
                    Halaman ini berisi informasi detail mengenai layanan pemeriksaan dan pemantauan tumbuh kembang balita di RT 001.
                </div>

                <div class="mt-4">
                    <h5>Kegiatan Rutin:</h5>
                    <ul>
                        <li>Penimbangan berat badan</li>
                        <li>Pengukuran tinggi badan</li>
                        <li>Pencatatan di KMS (Kartu Menuju Sehat)</li>
                        <li>Pemberian vitamin A (setiap bulan Februari dan Agustus)</li>
                    </ul>
                </div>
                    </div>
                </div>
                <!-- ========================================== -->

            </div>
        </div>
    </div>
</div>
@endsection