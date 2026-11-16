@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <a href="{{ route('posyandu') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small">
            <i data-lucide="arrow-left" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Kembali ke Posyandu
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-2 rounded-pill fw-bold" style="background: #E8F5E9; color: #1B5E20;">
                RT {{ $peserta->rt_number }} &bull; {{ $peserta->kategori_label }}
            </span>
        </div>
    </div>

    <!-- Alert Notifikasi Session Flash -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i data-lucide="check" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="text-success d-block">Berhasil!</strong>
                <span class="small text-dark">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Kartu Profil Peserta & Rekam Data Fisik -->
    <div class="row g-4 mb-4">
        <!-- Informasi Biodata Peserta -->
        <div class="col-12 col-lg-4">
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                <div class="text-center mb-3">
                    <img src="{{ $peserta->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($peserta->user->name) . '&background=E8F5E9&color=1B5E20' }}" 
                         alt="{{ $peserta->user->name }}" 
                         class="rounded-circle border mb-2 shadow-sm" 
                         style="width: 80px; height: 80px; object-fit: cover;">
                    <h5 class="fw-bold text-dark mb-0">{{ $peserta->user->name }}</h5>
                    <span class="badge {{ $peserta->kategori_badge }} px-3 py-1 rounded-pill mt-1" style="font-size: 11px;">
                        {{ $peserta->kategori_label }}
                    </span>
                </div>

                <ul class="list-group list-group-flush border-top border-bottom py-2 small">
                    <li class="list-group-item px-0 d-flex justify-content-between py-2 border-0">
                        <span class="text-muted">Usia Saat Ini:</span>
                        <strong class="text-dark">{{ $peserta->usia_text }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between py-2 border-0">
                        <span class="text-muted">Tanggal Lahir:</span>
                        <span class="text-dark">{{ $peserta->user->tanggal_lahir ? Carbon\Carbon::parse($peserta->user->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between py-2 border-0">
                        <span class="text-muted">Jenis Kelamin:</span>
                        <span class="text-dark">{{ $peserta->user->gender ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between py-2 border-0">
                        <span class="text-muted">Golongan Darah:</span>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0 fw-bold">{{ $peserta->golongan_darah ?? 'Belum Ada' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between py-2 border-0">
                        <span class="text-muted">No. Kartu Keluarga:</span>
                        <span class="text-dark">{{ $peserta->kartuKeluarga->nomor_kk ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between py-2 border-0">
                        <span class="text-muted">Kepala Keluarga:</span>
                        <span class="text-dark">{{ $peserta->kartuKeluarga->kepalaKeluarga->name ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between py-2 border-0">
                        <span class="text-muted">Riwayat Alergi:</span>
                        <span class="text-danger fw-semibold">{{ $peserta->riwayat_alergi ?: 'Tidak ada' }}</span>
                    </li>
                </ul>

                @if($user->isRt())
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary rounded-pill w-100 fw-bold py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInputPengukuran">
                            <i data-lucide="plus-circle" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Input Hasil Pengukuran Baru
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tabel Catatan Perkembangan & Pengukuran -->
        <div class="col-12 col-lg-8">
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i data-lucide="line-chart" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                            Buku Riwayat Perkembangan Fisik & Tanda Vital
                        </h5>
                        <p class="mb-0 text-muted small">Rekam pengukuran berkala oleh kader posyandu dan tenaga medis Puskesmas Arcamanik.</p>
                    </div>
                </div>

                <!-- Disclaimer Tenaga Kesehatan -->
                <div class="alert alert-info border-0 rounded-3 p-3 mb-3 d-flex align-items-start gap-2" style="background: #E1F5FE; color: #0277BD;">
                    <i data-lucide="info" class="mt-1 flex-shrink-0" style="width: 16px; height: 16px;"></i>
                    <small style="font-size: 11px;">
                        <strong>Catatan Resmi:</strong> Hasil pengukuran ini merupakan pencatatan deteksi tumbuh kembang dan skrining kesehatan awal di tingkat lingkungan. Diagnosis dan interpretasi medis resmi dilakukan oleh dokter atau bidan UPTD Puskesmas Arcamanik.
                    </small>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-secondary small fw-bold">Tanggal</th>
                                <th class="text-secondary small fw-bold">Hasil Pengukuran</th>
                                <th class="text-secondary small fw-bold">Pelayanan / PMT</th>
                                <th class="text-secondary small fw-bold">Catatan & Anjuran Nakes</th>
                                <th class="text-secondary small fw-bold">Pencatat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peserta->pengukurans as $peng)
                                <tr>
                                    <td class="ps-3">
                                        <strong class="text-dark d-block small">{{ $peng->tanggal_formatted }}</strong>
                                        <small class="text-muted">{{ $peng->jadwal->judul_kegiatan ?? 'Kunjungan Mandiri' }}</small>
                                    </td>
                                    <td>
                                        <div class="small text-dark">
                                            @if($peng->berat_badan) <span>BB: <strong>{{ $peng->berat_badan }} kg</strong></span> @endif
                                            @if($peng->tinggi_badan) <span class="ms-1">TB: <strong>{{ $peng->tinggi_badan }} cm</strong></span> @endif
                                            @if($peng->lingkar_kepala) <span class="ms-1">LK: <strong>{{ $peng->lingkar_kepala }} cm</strong></span> @endif
                                            @if($peng->usia_kehamilan_minggu) <span class="ms-1">Usia Hamil: <strong>{{ $peng->usia_kehamilan_minggu }} mgg</strong></span> @endif
                                            @if($peng->tekanan_darah) <span class="ms-1">TD: <strong>{{ $peng->tekanan_darah }}</strong></span> @endif
                                        </div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle mt-1" style="font-size: 10px;">
                                            {{ $peng->status_pertumbuhan }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-secondary">{{ $peng->tindakan_layanan ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-dark d-block">{{ $peng->catatan_petugas ?? '-' }}</small>
                                        @if($peng->anjuran_rujukan)
                                            <small class="text-danger fw-semibold d-block mt-1">
                                                <i data-lucide="alert-circle" style="width: 12px; height: 12px; vertical-align: middle;"></i>
                                                {{ $peng->anjuran_rujukan }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $peng->petugasPencatat->name ?? 'Kader RT' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pengukuran tercatat untuk peserta ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL INPUT PENGUKURAN BARU (RT ONLY) -->
@if($user->isRt())
<div class="modal fade" id="modalInputPengukuran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i data-lucide="scale" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Input Pengukuran Fisik: {{ $peserta->user->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posyandu.pengukuran.store', $peserta->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Hubungkan dengan Jadwal Kegiatan (Opsional)</label>
                            <select name="jadwal_id" class="form-select">
                                <option value="">- Kunjungan / Pemeriksaan Mandiri -</option>
                                @foreach($jadwalAktif as $ja)
                                    <option value="{{ $ja->id }}">{{ $ja->judul_kegiatan }} ({{ $ja->tanggal_formatted }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Pemeriksaan *</label>
                            <input type="date" name="tanggal_pemeriksaan" class="form-control" required value="{{ Carbon\Carbon::now()->toDateString() }}">
                        </div>
                    </div>

                    @if(in_array($peserta->kategori, ['bayi', 'balita', 'anak']))
                        <!-- Form Balita -->
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <h6 class="fw-bold text-primary mb-2">Parameter Pertumbuhan Bayi / Balita</h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Berat Badan (kg) *</label>
                                    <input type="number" step="0.05" name="berat_badan" class="form-control" placeholder="Contoh: 11.4" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Tinggi / Panjang Badan (cm) *</label>
                                    <input type="number" step="0.1" name="tinggi_badan" class="form-control" placeholder="Contoh: 85.5" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Lingkar Kepala (cm)</label>
                                    <input type="number" step="0.1" name="lingkar_kepala" class="form-control" placeholder="Contoh: 46.2">
                                </div>
                            </div>
                        </div>
                    @elseif($peserta->kategori === 'ibu_hamil')
                        <!-- Form Ibu Hamil -->
                        <div class="p-3 bg-pink-subtle rounded-3 mb-3 border border-pink-subtle" style="background: #FCE4EC;">
                            <h6 class="fw-bold text-danger mb-2">Parameter Kesehatan Ibu Hamil</h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Berat Badan (kg) *</label>
                                    <input type="number" step="0.1" name="berat_badan" class="form-control" placeholder="Contoh: 58.5" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Usia Kehamilan (Minggu)</label>
                                    <input type="number" name="usia_kehamilan_minggu" class="form-control" placeholder="Contoh: 22">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small">Tensi Sistol</label>
                                    <input type="number" name="tekanan_darah_sistol" class="form-control" placeholder="110">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small">Tensi Diastol</label>
                                    <input type="number" name="tekanan_darah_diastol" class="form-control" placeholder="75">
                                </div>
                            </div>
                        </div>
                    @elseif($peserta->kategori === 'lansia')
                        <!-- Form Lansia -->
                        <div class="p-3 bg-warning-subtle rounded-3 mb-3 border border-warning-subtle" style="background: #FFF8E1;">
                            <h6 class="fw-bold text-warning-emphasis mb-2">Parameter Skrining Kesehatan Lansia</h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Berat Badan (kg) *</label>
                                    <input type="number" step="0.1" name="berat_badan" class="form-control" placeholder="Contoh: 62.0" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Tensi Sistol (mmHg)</label>
                                    <input type="number" name="tekanan_darah_sistol" class="form-control" placeholder="125">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Tensi Diastol (mmHg)</label>
                                    <input type="number" name="tekanan_darah_diastol" class="form-control" placeholder="80">
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pelayanan / PMT yang Diberikan</label>
                        <input type="text" name="tindakan_layanan" class="form-control" placeholder="Contoh: Pemberian PMT Biskuit Balita, Vitamin A, Konsultasi Gizi">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Keluhan yang Disampaikan Warga</label>
                            <textarea name="keluhan" class="form-control" rows="2" placeholder="Keluhan ringan atau nafsu makan..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Catatan Petugas / Kader</label>
                            <textarea name="catatan_petugas" class="form-control" rows="2" placeholder="Catatan respon peserta saat penimbangan..."></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-danger">Anjuran Tindak Lanjut / Rujukan ke Puskesmas (Jika Ada)</label>
                        <input type="text" name="anjuran_rujukan" class="form-control border-danger" placeholder="Contoh: Disarankan periksa lanjutan ke Poli KIA Puskesmas Arcamanik">
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Hasil Pengukuran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
