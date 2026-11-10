@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1"><i class="fas fa-video text-danger me-2"></i>Kelola CCTV</h3>
            <p class="text-muted mb-0">Tambah, ubah, atau hapus kamera yang tampil di dashboard warga.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger rounded-4">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card p-4 rounded-4 border-0 shadow-sm">
                <h6 class="fw-bold mb-3">Tambah Kamera</h6>
                <form method="POST" action="{{ route('cctv.store') }}">
                    @csrf
                    <div class="mb-2"><label class="form-label small fw-semibold">Nama Kamera</label>
                        <input name="nama" class="form-control" placeholder="Gerbang Utama" required></div>
                    <div class="mb-2"><label class="form-label small fw-semibold">Lokasi</label>
                        <input name="lokasi" class="form-control" placeholder="RT 001 - Pos Ronda"></div>
                    <div class="mb-2"><label class="form-label small fw-semibold">URL Stream (.m3u8 / .mp4 / MJPEG)</label>
                        <input name="stream_url" type="url" class="form-control" placeholder="https://.../live.m3u8" required></div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktifNew" checked>
                        <label class="form-check-label small" for="aktifNew">Aktif / tampil di dashboard</label>
                    </div>
                    <button class="btn btn-primary rounded-pill w-100"><i class="fas fa-plus me-1"></i> Simpan</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card p-4 rounded-4 border-0 shadow-sm">
                <h6 class="fw-bold mb-3">Daftar Kamera ({{ $cctvs->count() }})</h6>
                @forelse($cctvs as $c)
                    <form method="POST" action="{{ route('cctv.update', $c) }}" class="border rounded-4 p-3 mb-3">
                        @csrf @method('PUT')
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3"><label class="form-label small">Nama</label>
                                <input name="nama" value="{{ $c->nama }}" class="form-control form-control-sm" required></div>
                            <div class="col-md-3"><label class="form-label small">Lokasi</label>
                                <input name="lokasi" value="{{ $c->lokasi }}" class="form-control form-control-sm"></div>
                            <div class="col-md-4"><label class="form-label small">URL Stream</label>
                                <input name="stream_url" type="url" value="{{ $c->stream_url }}" class="form-control form-control-sm" required></div>
                            <div class="col-md-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktif{{ $c->id }}" @checked($c->aktif)>
                                    <label class="form-check-label small" for="aktif{{ $c->id }}">Aktif</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-2 justify-content-end">
                            <button class="btn btn-sm btn-success rounded-pill px-3"><i class="fas fa-save me-1"></i> Update</button>
                            <button type="submit" form="del{{ $c->id }}" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="fas fa-trash me-1"></i> Hapus</button>
                        </div>
                    </form>
                    <form id="del{{ $c->id }}" method="POST" action="{{ route('cctv.destroy', $c) }}" onsubmit="return confirm('Hapus kamera ini?')">
                        @csrf @method('DELETE')
                    </form>
                @empty
                    <p class="text-muted text-center py-4 mb-0">Belum ada kamera.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
