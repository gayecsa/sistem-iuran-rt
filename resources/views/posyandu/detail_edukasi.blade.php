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
                    <h5>Topik Edukasi Bulan Ini:</h5>
                    <ul>
                        <li>Pentingnya sanitasi dan cuci tangan pakai sabun</li>
                        <li>Pencegahan stunting sejak dini</li>
                        <li>Pengolahan sampah rumah tangga yang baik</li>
                        <li>Keluarga Berencana (KB)</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection