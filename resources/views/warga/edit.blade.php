@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="wm-icon-pill shadow-sm" style="width: 48px; height: 48px; border-radius: 14px; background: #EAF3FF; color: #1456A0; font-size: 20px;">
                <i data-lucide="edit-3" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark" style="color: var(--wm-text) !important;">Edit Data Kependudukan Warga</h3>
                <p class="text-muted mb-0 small">Perbarui data identitas sipil, status domisili, atau dokumen pendukung warga <strong>{{ $warga->name }}</strong>.</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('warga.show', $warga->id) }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
                <span>Kembali ke Profil</span>
            </a>
        </div>
    </div>

    {{-- Error Alert --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-4" role="alert" style="background: #FEE2E2; color: #DC2626;">
            <div class="d-flex align-items-start gap-2">
                <i data-lucide="alert-circle" style="width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;"></i>
                <div>
                    <strong class="d-block mb-1">Terdapat kesalahan pengisian data:</strong>
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('warga.update', $warga->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- KOLOM KIRI: DATA PRIBADI & KELUARGA --}}
            <div class="col-12 col-lg-6">
                {{-- Card 1: Identitas Pribadi --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i data-lucide="user" style="width: 18px; height: 18px; color: #1456A0;"></i>
                        <h6 class="fw-bold mb-0 text-dark">Identitas Pribadi (Sesuai KTP)</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $warga->name) }}" class="form-control" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">NIK (16 Digit) <span class="text-danger">*</span></label>
                            <input type="text" name="nik" value="{{ old('nik', $warga->nik) }}" class="form-control font-monospace" maxlength="16" minlength="16" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="Laki-laki" {{ old('gender', $warga->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('gender', $warga->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $warga->tempat_lahir) }}" class="form-control">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $warga->tanggal_lahir ? \Carbon\Carbon::parse($warga->tanggal_lahir)->format('Y-m-d') : '') }}" class="form-control" max="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small mb-1">Agama</label>
                            <select name="agama" class="form-select">
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Penghayat Kepercayaan'] as $ag)
                                    <option value="{{ $ag }}" {{ old('agama', $warga->agama ?? 'Islam') == $ag ? 'selected' : '' }}>{{ $ag }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small mb-1">Status Perkawinan</label>
                            <select name="status_perkawinan" class="form-select">
                                @foreach(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $sp)
                                    <option value="{{ $sp }}" {{ old('status_perkawinan', $warga->status_perkawinan ?? 'Kawin') == $sp ? 'selected' : '' }}>{{ $sp }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small mb-1">Kewarganegaraan</label>
                            <select name="kewarganegaraan" class="form-select">
                                <option value="WNI" {{ old('kewarganegaraan', $warga->kewarganegaraan ?? 'WNI') == 'WNI' ? 'selected' : '' }}>WNI</option>
                                <option value="WNA" {{ old('kewarganegaraan', $warga->kewarganegaraan) == 'WNA' ? 'selected' : '' }}>WNA</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Hubungan Keluarga & Status Domisili --}}
                <div class="card border-0 shadow-sm rounded-4 p-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i data-lucide="users" style="width: 18px; height: 18px; color: #1456A0;"></i>
                        <h6 class="fw-bold mb-0 text-dark">Data Keluarga & Status Kependudukan</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Nomor Kartu Keluarga (KK) <span class="text-danger">*</span></label>
                            <input type="text" name="no_kk" value="{{ old('no_kk', $warga->no_kk) }}" class="form-control font-monospace" maxlength="16" minlength="16" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Hubungan dalam Keluarga <span class="text-danger">*</span></label>
                            <select name="hubungan_keluarga" class="form-select" required>
                                @foreach(['Kepala Keluarga', 'Istri', 'Suami', 'Anak', 'Orang Tua', 'Saudara', 'Famili Lain'] as $hk)
                                    <option value="{{ $hk }}" {{ old('hubungan_keluarga', $warga->hubungan_keluarga) == $hk ? 'selected' : '' }}>{{ $hk }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Status Domisili <span class="text-danger">*</span></label>
                            <select name="status_domisili" class="form-select" required>
                                @foreach(['Warga Tetap', 'Warga Sementara', 'Pendatang Baru', 'Akan Pindah', 'Sudah Pindah', 'Meninggal Dunia'] as $sd)
                                    <option value="{{ $sd }}" {{ old('status_domisili', $warga->status_domisili ?? 'Warga Tetap') == $sd ? 'selected' : '' }}>{{ $sd }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Status Rumah <span class="text-danger">*</span></label>
                            <select name="status_rumah" class="form-select" required>
                                @foreach(['Milik Sendiri', 'Kontrak', 'Kos', 'Menumpang', 'Lainnya'] as $sr)
                                    <option value="{{ $sr }}" {{ old('status_rumah', ucfirst(str_replace('_', ' ', $warga->status_rumah ?? 'Milik Sendiri'))) == $sr ? 'selected' : '' }}>{{ $sr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small mb-1">Catatan Mutasi / Perubahan Data</label>
                            <input type="text" name="catatan_perubahan" class="form-control" placeholder="Tuliskan alasan perubahan data untuk dicatat di audit log...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: ALAMAT, KONTAK & DOKUMEN --}}
            <div class="col-12 col-lg-6">
                {{-- Card 3: Alamat Tempat Tinggal --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i data-lucide="map-pin" style="width: 18px; height: 18px; color: #1456A0;"></i>
                        <h6 class="fw-bold mb-0 text-dark">Alamat Tempat Tinggal (Sukamiskin Arcamanik)</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small mb-1">Wilayah RT <span class="text-danger">*</span></label>
                            <select name="rt_number" class="form-select" {{ auth()->user()->isRt() ? 'disabled' : '' }} required>
                                @foreach($availableRts as $rt)
                                    <option value="{{ $rt['val'] }}" {{ old('rt_number', str_pad($warga->clean_rt_number, 3, '0', STR_PAD_LEFT)) == $rt['val'] ? 'selected' : '' }}>
                                        {{ $rt['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small mb-1">Nomor Rumah <span class="text-danger">*</span></label>
                            <input type="text" name="house_number" value="{{ old('house_number', $warga->house_number) }}" class="form-control" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small mb-1">Blok (Opsional)</label>
                            <input type="text" name="blok_rumah" value="{{ old('blok_rumah', $warga->blok_rumah) }}" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small mb-1">Jalan / Gang</label>
                            <input type="text" name="gang_jalan" value="{{ old('gang_jalan', $warga->gang_jalan ?? 'Jl. Pacuan Kuda') }}" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small mb-1">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea name="address" rows="2" class="form-control" required>{{ old('address', $warga->address) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Kontak, Pekerjaan & Dokumen --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: var(--wm-card, #FFFFFF); border: 1px solid var(--wm-border, #E2E8F0) !important;">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i data-lucide="phone" style="width: 18px; height: 18px; color: #1456A0;"></i>
                        <h6 class="fw-bold mb-0 text-dark">Kontak, Profesi & Dokumen</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">No. WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $warga->phone) }}" class="form-control" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Email Login <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $warga->email) }}" class="form-control" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Pekerjaan</label>
                            <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $warga->pekerjaan) }}" class="form-control">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir" class="form-select">
                                @foreach(['S1 / Sarjana', 'SMA / SMK / Sederajat', 'Diploma (D3)', 'S2 / Magister', 'S3 / Doktor', 'SMP / Sederajat', 'SD / Sederajat', 'Tidak / Belum Sekolah'] as $pd)
                                    <option value="{{ $pd }}" {{ old('pendidikan_terakhir', $warga->pendidikan_terakhir) == $pd ? 'selected' : '' }}>{{ $pd }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Unggah Dokumen KTP Baru</label>
                            <input type="file" name="dokumen_ktp" class="form-control form-control-sm" accept="application/pdf,image/jpeg,image/png">
                            @if($warga->dokumen_ktp_path)
                                <small class="text-success d-block mt-1" style="font-size: 0.72rem;"><i data-lucide="check" style="width: 12px; height: 12px;"></i> Berkas KTP sudah tersimpan</small>
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small mb-1">Dokumen Pendukung Lain</label>
                            <input type="file" name="dokumen_pendukung" class="form-control form-control-sm" accept="application/pdf,image/jpeg,image/png">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small mb-1">Ganti Password Akun (Opsional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password akun">
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex align-items-center justify-content-end gap-2">
                    <a href="{{ route('warga.show', $warga->id) }}" class="btn btn-light rounded-pill px-4 py-2 fw-semibold">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm" style="background: #1456A0;">
                        <i data-lucide="save" style="width: 16px; height: 16px;" class="me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
