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

                <div class="alert alert-info">
                    Layanan pemeriksaan kehamilan rutin dan persiapan persalinan untuk memastikan kesehatan ibu dan janin.
                </div>

                <div class="mt-4">
                    <h5>Kegiatan Rutin:</h5>
                    <ul>
                        <li>Pengukuran berat badan dan tekanan darah ibu hamil</li>
                        <li>Pengukuran Lingkar Lengan Atas (LiLA)</li>
                        <li>Pemberian tablet tambah darah (Fe)</li>
                        <li>Konsultasi persiapan persalinan dan ASI eksklusif</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection