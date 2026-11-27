@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <a href="{{ route('posyandu') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small">
            <i data-lucide="arrow-left" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Kembali ke Dashboard
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('posyandu.laporan.cetak', $laporan->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold small">
                <i data-lucide="printer" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Cetak Laporan Resmi
            </a>
            <span class="badge {{ $laporan->status_badge }} px-3 py-2 rounded-pill fw-bold">
                {{ $laporan->status_label }}
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Rincian Laporan Resmi -->
        <div class="col-12 col-lg-8">
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <span class="badge bg-light text-primary border px-3 py-1 rounded-pill small fw-bold mb-2">
                                NO: {{ $laporan->nomor_laporan }}
                            </span>
                            <h4 class="fw-bold text-dark mb-1">
                                Laporan Kegiatan: {{ $laporan->jadwal->judul_kegiatan ?? 'Posyandu Melati 13 RT ' . $laporan->rt_number }}
                            </h4>
                            <p class="text-muted small mb-0">
                                Diselenggarakan oleh Pengurus RT {{ $laporan->rt_number }} &bull; RW 013 Kelurahan Suryatmajan, Danurejan
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 4 Kotak Rekap Kehadiran Peserta -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light border text-center">
                            <span class="text-muted small d-block mb-1">Total Hadir</span>
                            <h4 class="fw-bold text-success mb-0">{{ $laporan->total_hadir }}</h4>
                            <small class="text-muted" style="font-size: 10px;">Peserta Terlayani</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light border text-center">
                            <span class="text-muted small d-block mb-1">Balita & Bayi</span>
                            <h4 class="fw-bold text-primary mb-0">{{ $laporan->total_bayi_balita }}</h4>
                            <small class="text-muted" style="font-size: 10px;">Timbang / Ukur</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light border text-center">
                            <span class="text-muted small d-block mb-1">Ibu Hamil</span>
                            <h4 class="fw-bold text-danger mb-0">{{ $laporan->total_ibu_hamil }}</h4>
                            <small class="text-muted" style="font-size: 10px;">Konsultasi KIA</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light border text-center">
                            <span class="text-muted small d-block mb-1">Lansia</span>
                            <h4 class="fw-bold text-warning-emphasis mb-0">{{ $laporan->total_lansia }}</h4>
                            <small class="text-muted" style="font-size: 10px;">Skrining Tensi</small>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pelaksanaan -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">1. Ringkasan Pelaksanaan Layanan</h6>
                    <p class="text-secondary small leading-relaxed">{{ $laporan->ringkasan_layanan }}</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <h6 class="fw-bold text-dark small mb-1">2. Kendala Pelaksanaan</h6>
                            <p class="text-muted small mb-0">{{ $laporan->kendala ?: 'Tidak ada kendala operasional yang berarti.' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <h6 class="fw-bold text-dark small mb-1">3. Evaluasi & Rencana Tindak Lanjut RT</h6>
                            <p class="text-muted small mb-0">{{ $laporan->catatan_evaluasi_rt ?: 'Kegiatan berjalan lancar sesuai protokol.' }}</p>
                        </div>
                    </div>
                </div>

                @if($laporan->foto_dokumentasi_path)
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">4. Dokumentasi Foto Kegiatan</h6>
                        <img src="{{ asset('storage/' . $laporan->foto_dokumentasi_path) }}" alt="Dokumentasi Kegiatan" class="img-fluid rounded-3 border shadow-sm" style="max-height: 360px;">
                    </div>
                @endif

                <div class="pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2 text-muted small">
                    <div>
                        Penanggung Jawab RT: <strong>{{ $laporan->penanggungJawab->name ?? 'Ketua RT ' . $laporan->rt_number }}</strong>
                    </div>
                    <div>
                        Tanggal Laporan: <strong>{{ $laporan->tanggal_laporan_formatted }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Status Evaluasi & Catatan RW 013 -->
        <div class="col-12 col-lg-4">
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <i data-lucide="shield-check" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Verifikasi & Evaluasi RW 013
                </h5>

                <div class="mb-3">
                    <span class="text-muted small d-block mb-1">Status Laporan Saat Ini:</span>
                    <span class="badge {{ $laporan->status_badge }} px-3 py-2 rounded-pill fw-bold fs-6">
                        {{ $laporan->status_label }}
                    </span>
                </div>

                @if($laporan->catatan_rw)
                    <div class="p-3 bg-light-subtle border rounded-3 mb-3">
                        <small class="text-muted d-block fw-semibold mb-1">Catatan dari Pengurus RW 013:</small>
                        <p class="text-dark small mb-1 fst-italic">"{{ $laporan->catatan_rw }}"</p>
                        <small class="text-secondary" style="font-size: 11px;">
                            Dievaluasi oleh: <strong>{{ $laporan->evaluatorRw->name ?? 'Admin RW 013' }}</strong> 
                            ({{ $laporan->tanggal_evaluasi_rw ? Carbon\Carbon::parse($laporan->tanggal_evaluasi_rw)->translatedFormat('d M Y H:i') : '-' }})
                        </small>
                    </div>
                @endif

                <!-- Form Evaluasi untuk Pengurus RW -->
                @if($user->isRw())
                    <div class="pt-3 border-top">
                        <h6 class="fw-bold text-dark mb-2">Formulir Catatan Evaluasi RW</h6>
                        <form action="{{ route('posyandu.laporan.evaluasi', $laporan->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Keputusan Status *</label>
                                <select name="status_laporan" class="form-select" required>
                                    <option value="disetujui_rw" {{ $laporan->status_laporan === 'disetujui_rw' ? 'selected' : '' }}>Setujui Laporan (Selesai & Sesuai Standar)</option>
                                    <option value="dievaluasi_rw" {{ $laporan->status_laporan === 'dievaluasi_rw' ? 'selected' : '' }}>Perlu Tindak Lanjut / Catatan Perbaikan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Catatan Masukan / Apresiasi RW *</label>
                                <textarea name="catatan_rw" class="form-control" rows="3" required placeholder="Tuliskan catatan evaluasi, saran perbaikan, atau apresiasi pelaksanaan..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold shadow-sm">
                                Simpan Evaluasi RW
                            </button>
                        </form>
                    </div>
                @else
                    <p class="text-muted small mb-0">
                        Hanya pengurus RW 013 yang berwenang memberikan verifikasi dan evaluasi akhir atas laporan kegiatan RT.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
