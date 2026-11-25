@extends('layouts.app')

@section('title', 'Haven — Kelola CCTV RW 013')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" style="max-width: 1650px;">
    {{-- Hero Banner Biru Haven / Warkas --}}
    <div class="wm-card mb-4 overflow-hidden position-relative sa-hero-card" style="background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%) !important; color: #fff !important; border: none !important; border-radius: 16px;">
        <div class="p-4 p-md-4 position-relative" style="z-index: 2;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge rounded-pill fw-bold text-white px-3 py-1.5" style="background: rgba(255, 255, 255, 0.2) !important; letter-spacing: 0.5px; font-size: 0.72rem;">
                            <i class="fas fa-video me-1"></i> PEMANTAUAN KAWASAN RW 013
                        </span>
                        <span class="badge rounded-pill fw-semibold text-white px-2.5 py-1" style="background: rgba(255, 255, 255, 0.15) !important; font-size: 0.72rem;">
                            RW 013 Sukamiskin Arcamanik
                        </span>
                        <span class="badge rounded-pill fw-semibold text-white px-2.5 py-1" style="background: rgba(16, 185, 129, 0.3) !important; border: 1px solid rgba(16, 185, 129, 0.4) !important; font-size: 0.72rem;">
                            <i class="fas fa-check-circle me-1"></i> {{ $cctvs->count() }} Titik CCTV Terdata
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1 text-white" style="letter-spacing: -0.5px;">Kelola Titik CCTV RW 013</h3>
                    <p class="mb-0 text-white-50 small" style="max-width: 700px;">
                        Kelola titik pemantauan CCTV kawasan RW 013 untuk monitoring pos ronda, pengurus wilayah, dan integrasi titik koordinat di Peta Wilayah.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('ronda.index') }}" class="btn btn-light rounded-pill px-3 py-2 fw-bold small text-white hero-btn-light" style="background: rgba(255, 255, 255, 0.18) !important; color: #FFFFFF !important; border: 1px solid rgba(255, 255, 255, 0.35) !important;">
                        <i class="fas fa-shield-alt me-1.5"></i> Pos Ronda &rarr;
                    </a>
                    <a href="{{ url('/peta') }}" class="btn btn-light rounded-pill px-3 py-2 fw-bold small text-white hero-btn-light" style="background: rgba(255, 255, 255, 0.18) !important; color: #FFFFFF !important; border: 1px solid rgba(255, 255, 255, 0.35) !important;">
                        <i class="fas fa-map-marked-alt me-1.5"></i> Peta Wilayah
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Notifikasi Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fas fa-check-circle fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle fs-5"></i>
            <div>{{ $errors->first() }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Konten Utama: 2 Kolom --}}
    <div class="row g-4">
        {{-- Kolom Kiri: Form Tambah Kamera --}}
        <div class="col-lg-4">
            <div class="wm-card card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 85px;">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center justify-content-center rounded-3 cctv-icon-box" style="width: 36px; height: 36px; background: rgba(20, 86, 160, 0.12); color: #1456A0;">
                        <i data-lucide="video" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Tambah Titik CCTV</h6>
                        <small class="text-muted">Daftarkan kamera baru ke sistem</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('cctv.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Kamera <span class="text-danger">*</span></label>
                        <input name="nama" class="form-control" placeholder="Contoh: Gerbang Utama RT 001" required value="{{ old('nama') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Wilayah RT</label>
                        <select name="rt" class="form-select">
                            <option value="">-- Pilih RT (Opsional / Umum RW) --</option>
                            @for($i = 1; $i <= 8; $i++)
                                @php $rtVal = sprintf('%03d', $i); @endphp
                                <option value="{{ $rtVal }}" @selected(old('rt') === $rtVal)>RT {{ $rtVal }}</option>
                            @endfor
                        </select>
                        <div class="form-text small">Tentukan wilayah RT untuk mempermudah filter petugas ronda.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Lokasi Spesifik</label>
                        <input name="lokasi" class="form-control" placeholder="Contoh: Pos Ronda Depan Blok A" value="{{ old('lokasi') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">URL Stream <span class="text-danger">*</span></label>
                        <input name="stream_url" type="url" class="form-control" placeholder="https://.../live.m3u8 atau RTSP/HTTP" required value="{{ old('stream_url') }}">
                        <div class="form-text small">Dukung HLS (.m3u8), MP4 stream, atau MJPEG / Web Snapshot.</div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Latitude (GPS)</label>
                            <input name="latitude" type="number" step="any" class="form-control form-control-sm" placeholder="-6.914744" value="{{ old('latitude') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Longitude (GPS)</label>
                            <input name="longitude" type="number" step="any" class="form-control form-control-sm" placeholder="107.665123" value="{{ old('longitude') }}">
                        </div>
                        <div class="col-12">
                            <div class="form-text small">Koordinat untuk menampilkan ikon CCTV di Peta Wilayah.</div>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-4 p-2 rounded-3 bg-light-subtle border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="aktif" value="1" id="aktifNew" checked>
                        <label class="form-check-label small fw-semibold" for="aktifNew">Aktifkan Kamera Segera</label>
                        <div class="text-muted small ps-4">Kamera aktif akan langsung muncul di halaman Pos Ronda & Peta.</div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2.5 shadow-sm">
                        <i class="fas fa-plus-circle me-1.5"></i> Simpan Kamera CCTV
                    </button>
                </form>
            </div>
        </div>

        {{-- Kolom Kanan: Daftar Kamera --}}
        <div class="col-lg-8">
            <div class="wm-card card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center justify-content-center rounded-3 cctv-icon-box" style="width: 36px; height: 36px; background: rgba(20, 86, 160, 0.12); color: #1456A0;">
                            <i data-lucide="list" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Daftar Titik CCTV Terpasang</h6>
                            <small class="text-muted">Total {{ $cctvs->count() }} titik kamera termonitor</small>
                        </div>
                    </div>
                </div>

                @forelse($cctvs as $c)
                    <div class="wm-card card border rounded-4 p-3 mb-3 shadow-none position-relative">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge rounded-pill {{ $c->aktif ? 'bg-success' : 'bg-secondary' }} px-2.5 py-1 text-white small fw-bold">
                                    <i class="fas {{ $c->aktif ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $c->aktif ? 'AKTIF' : 'NONAKTIF' }}
                                </span>
                                @if($c->rt)
                                    <span class="badge rounded-pill bg-primary bg-opacity-15 text-primary border border-primary border-opacity-25 px-2.5 py-1 small fw-bold">
                                        RT {{ $c->clean_rt }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-secondary bg-opacity-15 text-secondary border px-2.5 py-1 small fw-bold">
                                        RW / Publik
                                    </span>
                                @endif
                                <h6 class="fw-bold mb-0 text-truncate" style="max-width: 280px;">{{ $c->nama }}</h6>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ $c->stream_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 small" title="Buka URL Stream">
                                    <i class="fas fa-external-link-alt me-1"></i> Test Stream
                                </a>
                                <button type="submit" form="del{{ $c->id }}" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 small" title="Hapus Kamera">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </button>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('cctv.update', $c) }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold mb-1">Nama Kamera</label>
                                    <input name="nama" value="{{ $c->nama }}" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold mb-1">Wilayah RT</label>
                                    <select name="rt" class="form-select form-select-sm">
                                        <option value="">RW / Publik</option>
                                        @for($i = 1; $i <= 8; $i++)
                                            @php $rtOpt = sprintf('%03d', $i); @endphp
                                            <option value="{{ $rtOpt }}" @selected($c->clean_rt === $rtOpt)>RT {{ $rtOpt }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Status Kamera</label>
                                    <div class="form-check form-switch pt-1">
                                        <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktif{{ $c->id }}" @checked($c->aktif)>
                                        <label class="form-check-label small" for="aktif{{ $c->id }}">
                                            {{ $c->aktif ? 'Aktif (Tampil)' : 'Nonaktif (Sembunyikan)' }}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Lokasi Fisik</label>
                                    <input name="lokasi" value="{{ $c->lokasi }}" class="form-control form-control-sm" placeholder="Lokasi penempatan">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-semibold mb-1">URL Stream</label>
                                    <input name="stream_url" type="url" value="{{ $c->stream_url }}" class="form-control form-control-sm" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Latitude</label>
                                    <input name="latitude" type="number" step="any" value="{{ $c->latitude }}" class="form-control form-control-sm" placeholder="-6.914744">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Longitude</label>
                                    <input name="longitude" type="number" step="any" value="{{ $c->longitude }}" class="form-control form-control-sm" placeholder="107.665123">
                                </div>
                                <div class="col-md-4 d-flex align-items-end justify-content-end">
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-bold w-100" style="background: #16A34A !important; border: none !important; color: #FFFFFF !important;">
                                        <i class="fas fa-save me-1.5"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>

                        <form id="del{{ $c->id }}" method="POST" action="{{ route('cctv.destroy', $c) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kamera {{ $c->nama }}?')">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-muted mb-3" style="width: 64px; height: 64px;">
                            <i class="fas fa-video-slash fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Belum Ada Kamera CCTV</h6>
                        <p class="text-muted small mb-0">Silakan tambahkan titik kamera baru melalui formulir di samping kiri.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) {
        lucide.createIcons();
    }
});
</script>
@endpush
@endsection