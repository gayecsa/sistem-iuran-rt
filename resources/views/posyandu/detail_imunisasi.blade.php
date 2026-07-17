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

                <div class="alert alert-info">
                    Layanan pemberian imunisasi dasar lengkap untuk mencegah penyakit yang dapat dicegah dengan imunisasi (PD3I) dan pemantauan asupan gizi.
                </div>

                <div class="mt-4">
                    <h5>Layanan yang diberikan:</h5>
                    <ul>
                        <li>Pemberian Imunisasi Dasar (BCG, DPT, Polio, Campak, Hepatitis B)</li>
                        <li>Pemberian Makanan Tambahan (PMT) untuk anak kurang gizi</li>
                        <li>Konsultasi pemenuhan gizi seimbang balita</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection