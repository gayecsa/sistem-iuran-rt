@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 animate__animated animate__fadeInUp">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small">Bank Sampah</p>
                        <h2 class="mb-2">Program Bank Sampah RT 001</h2>
                        <p class="text-muted mb-0">Tempat pengumpulan sampah terpilah untuk dikumpulkan dan dimanfaatkan kembali.</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-soft">Kembali ke Dashboard</a>
                </div>

                <div class="row g-3 mt-4">
                    <div class="col-md-6">
                        <div class="card metric-card p-3">
                            <h6>Jadwal Layanan</h6>
                            <p class="mb-1">Setiap Rabu</p>
                            <p class="text-muted mb-0">Pukul 10.00 - 13.00 WIB</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card metric-card p-3">
                            <h6>Jenis Sampah</h6>
                            <p class="mb-1">Plastik, Kertas, Logam</p>
                            <p class="text-muted mb-0">Sampah bersih dan terpisah.</p>
                        </div>
                    </div>
                </div>

                <div class="card p-4 mt-4">
                    <h5 class="mb-3">Keuntungan</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Mendukung lingkungan bersih</li>
                        <li class="list-group-item">Menambah tabungan RT</li>
                        <li class="list-group-item">Meningkatkan kesadaran daur ulang</li>
                        <li class="list-group-item">Menerima insentif untuk sampah bernilai</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
