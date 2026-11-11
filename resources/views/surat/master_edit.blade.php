@extends('layouts.app')

@section('title', 'Edit Konfigurasi Template Surat - ' . $type->nama)

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" style="max-width: 1400px;">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i data-lucide="home" class="me-1" style="width:14px;height:14px;"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('rw.surat_master.index') }}" class="text-decoration-none text-muted">Master Surat RW</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Edit Konfigurasi</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span>Edit Konfigurasi & Template: {{ $type->nama }}</span>
                <span class="badge bg-primary text-white rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                    {{ $type->singkatan }}
                </span>
            </h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Sesuaikan teks template surat resmi, persyaratan berkas kependudukan, alur persetujuan, estimasi waktu, dan biaya.
            </p>
        </div>

        <a href="{{ route('rw.surat_master.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm rounded-3" style="font-size: 0.88rem;">
            <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
            <span>Kembali ke Master</span>
        </a>
    </div>

    {{-- Error validation alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4">
            <div class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="alert-triangle" style="width:18px;height:18px;"></i>
                <span>Terdapat kesalahan pengisian:</span>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Edit --}}
    <form action="{{ route('rw.surat_master.update', $type->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- KOLOM KIRI (60%): INFORMASI UTAMA & TEMPLATE --}}
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: #ffffff;">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-3 d-flex align-items-center gap-2">
                        <i data-lucide="file-edit" class="text-primary" style="width:20px;height:20px;"></i>
                        <span>Informasi Pokok & Kalimat Template Surat</span>
                    </h5>

                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-semibold small text-secondary">Nama Dokumen Surat <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $type->nama) }}" required>
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="form-label fw-semibold small text-secondary">Singkatan <span class="text-danger">*</span></label>
                            <input type="text" name="singkatan" class="form-control font-monospace" value="{{ old('singkatan', $type->singkatan) }}" required>
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="form-label fw-semibold small text-secondary">Kode Klasifikasi <span class="text-danger">*</span></label>
                            <input type="text" name="kode_klasifikasi" class="form-control font-monospace" value="{{ old('kode_klasifikasi', $type->kode_klasifikasi) }}" required placeholder="Contoh: 470">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small text-secondary">Deskripsi Ringkas Layanan <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" rows="2" class="form-control" required>{{ old('deskripsi', $type->deskripsi) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small text-secondary">Tujuan / Manfaat Dokumen bagi Warga</label>
                            <input type="text" name="tujuan" class="form-control" value="{{ old('tujuan', $type->tujuan) }}">
                        </div>

                        {{-- Template Kalimat Resmi Surat --}}
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-semibold small text-secondary mb-0">Isi Kalimat Template Resmi Surat <span class="text-danger">*</span></label>
                                <span class="text-muted small" style="font-size:0.75rem;">Digunakan pada halaman cetak A4</span>
                            </div>
                            <textarea name="template_body" rows="6" class="form-control font-monospace" style="font-size: 0.88rem; line-height: 1.5;" required>{{ old('template_body', $type->template_body) }}</textarea>

                            {{-- Petunjuk Placeholder Dinamis --}}
                            <div class="p-3 rounded-3 mt-2 border" style="background: #F8FAFC; font-size: 0.78rem;">
                                <div class="fw-bold text-secondary mb-1">Petunjuk Variabel Dinamis (Akan otomatis digantikan saat dicetak):</div>
                                <div class="d-flex flex-wrap gap-2 text-dark font-monospace">
                                    <span class="badge bg-light text-primary border">{nama}</span>
                                    <span class="badge bg-light text-primary border">{nik}</span>
                                    <span class="badge bg-light text-primary border">{rt}</span>
                                    <span class="badge bg-light text-primary border">{rw}</span>
                                    <span class="badge bg-light text-primary border">{keperluan}</span>
                                    <span class="badge bg-light text-secondary border">Variabel khusus sesuai field jenis surat</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (40%): ALUR, SYARAT & STATUS --}}
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: #ffffff;">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-3 d-flex align-items-center gap-2">
                        <i data-lucide="sliders" class="text-primary" style="width:20px;height:20px;"></i>
                        <span>Alur Persetujuan & Parameter</span>
                    </h5>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-secondary">Alur Persetujuan Berjenjang <span class="text-danger">*</span></label>
                            <select name="alur_persetujuan" class="form-select" required>
                                <option value="rt_then_rw" {{ old('alur_persetujuan', $type->alur_persetujuan) === 'rt_then_rw' ? 'selected' : '' }}>Persetujuan Berjenjang (Warga &rarr; RT &rarr; RW &rarr; Siap Cetak)</option>
                                <option value="pengantar_kelurahan" {{ old('alur_persetujuan', $type->alur_persetujuan) === 'pengantar_kelurahan' ? 'selected' : '' }}>Verifikasi RT & Pengantar RW ke Kelurahan / Instansi</option>
                                <option value="rt_only" {{ old('alur_persetujuan', $type->alur_persetujuan) === 'rt_only' ? 'selected' : '' }}>Persetujuan RT Saja (Warga &rarr; RT &rarr; Siap Cetak)</option>
                                <option value="internal_rw" {{ old('alur_persetujuan', $type->alur_persetujuan) === 'internal_rw' ? 'selected' : '' }}>Administrasi Internal RW (Langsung RW)</option>
                            </select>
                            <div class="text-muted small mt-1" style="font-size: 0.74rem;">
                                Tentukan batas kewenangan sesuai jenis surat lingkungan.
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Estimasi Waktu Pelayanan <span class="text-danger">*</span></label>
                            <input type="text" name="estimasi_hari" class="form-control" value="{{ old('estimasi_hari', $type->estimasi_hari) }}" required placeholder="Contoh: 1 - 2 Hari Kerja">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Biaya Administrasi <span class="text-danger">*</span></label>
                            <input type="text" name="biaya" class="form-control" value="{{ old('biaya', $type->biaya) }}" required placeholder="Contoh: Gratis (Rp 0)">
                        </div>

                        {{-- Persyaratan Berkas Dokumen --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-secondary">Persyaratan Dokumen (1 baris per syarat) <span class="text-danger">*</span></label>
                            @php
                                $textPersyaratan = !empty($type->persyaratan) ? implode("\n", $type->persyaratan) : "Scan KTP Asli Pemohon\nKartu Keluarga yang Masih Berlaku";
                            @endphp
                            <textarea name="persyaratan_text" rows="5" class="form-control" required placeholder="Tuliskan syarat dokumen (satu syarat per baris)...">{{ old('persyaratan_text', $textPersyaratan) }}</textarea>
                            <span class="text-muted small" style="font-size: 0.74rem;">Tulis tiap poin syarat pada baris baru tanpa nomor atau tanda strip.</span>
                        </div>

                        {{-- Status Aktif / Nonaktif --}}
                        <div class="col-12 border-top pt-3">
                            <label class="form-label fw-semibold small text-secondary d-block">Status Ketersediaan Layanan <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_active" id="active1" value="1" {{ old('is_active', $type->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label text-success fw-bold small" for="active1">
                                        <i data-lucide="check-circle" style="width:14px;height:14px;" class="me-1"></i> Aktif (Dapat Diajukan)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_active" id="active0" value="0" {{ !old('is_active', $type->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger fw-bold small" for="active0">
                                        <i data-lucide="x-circle" style="width:14px;height:14px;" class="me-1"></i> Nonaktif (Disembunyikan)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Card --}}
                <div class="card border-0 shadow-sm rounded-4 p-4" style="background: #ffffff;">
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%);">
                        <i data-lucide="save" style="width:18px;height:18px;"></i>
                        <span>Simpan Perubahan Konfigurasi</span>
                    </button>
                    <a href="{{ route('rw.surat_master.index') }}" class="btn btn-outline-secondary w-100 mt-2 rounded-3 py-2" style="font-size: 0.88rem;">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection
