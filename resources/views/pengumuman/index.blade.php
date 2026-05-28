@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Daftar Pengumuman Aktif</h3>
    <div class="row">
        @foreach($semua_pengumuman as $p)
        <div class="col-md-6 mb-4">
            <div class="card p-4 shadow-sm border-0" style="border-radius: 15px;">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-primary">{{ $p->kategori }}</span>
                    <small class="text-muted">Aktif sejak: {{ \Carbon\Carbon::parse($p->tanggal_aktif)->format('d M Y') }}</small>
                </div>
                <h5>{{ $p->judul }}</h5>
                <p class="text-muted">{{ $p->isi }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection