@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Pengaduan Warga -->
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%); color: #fff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-warning text-dark mb-2 fw-bold px-3 py-1 rounded-pill">LAYANAN MASYARAKAT</span>
                <h3 class="fw-bold mb-1 text-white">Sistem Pengaduan & Layanan Aspirasi Warga RW 013</h3>
                <p class="mb-0 text-white opacity-75 small">
                    Pusat penanganan aspirasi, keluhan keamanan, ketertiban, kebersihan, dan fasilitas lingkungan RW 013 secara transparan dan responsif.
                </p>
            </div>
            <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold text-dark shadow" data-bs-toggle="modal" data-bs-target="#modalTambahPengaduan">
                <i data-lucide="plus-circle" style="width: 18px; height: 18px; vertical-align: middle;"></i> Buat Pengaduan Baru
            </button>
        </div>
    </div>

    <!-- Filter Pengaduan -->
    <div class="wm-card mb-4">
        <form method="GET" action="{{ route('pengaduan.index') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-bold text-dark small">Filter Kategori</label>
                <select name="kategori" class="form-select rounded-3">
                    <option value="">Semua Kategori</option>
                    <option value="Fasilitas & Lampu Jalan" {{ ($kategori == 'Fasilitas & Lampu Jalan') ? 'selected' : '' }}>Fasilitas & Lampu Jalan</option>
                    <option value="Kebersihan & Sampah" {{ ($kategori == 'Kebersihan & Sampah') ? 'selected' : '' }}>Kebersihan & Sampah</option>
                    <option value="Keamanan & Ketertiban" {{ ($kategori == 'Keamanan & Ketertiban') ? 'selected' : '' }}>Keamanan & Ketertiban</option>
                    <option value="Drainase & Saluran Air" {{ ($kategori == 'Drainase & Saluran Air') ? 'selected' : '' }}>Drainase & Saluran Air</option>
                    <option value="Sosial & Ketertiban Umum" {{ ($kategori == 'Sosial & Ketertiban Umum') ? 'selected' : '' }}>Sosial & Ketertiban Umum</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark small">Filter Status Penanganan</label>
                <select name="status" class="form-select rounded-3">
                    <option value="">Semua Status</option>
                    <option value="Menunggu" {{ ($status == 'Menunggu') ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Diproses" {{ ($status == 'Diproses') ? 'selected' : '' }}>Sedang Ditangani</option>
                    <option value="Selesai" {{ ($status == 'Selesai') ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="wm-btn-primary flex-grow-1 justify-content-center">
                    <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Filter
                </button>
                <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary rounded-3 px-3 d-flex align-items-center" title="Reset">
                    <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Daftar Pengaduan Warga -->
    <div class="wm-card">
        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
            <i data-lucide="list-ordered" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
            Daftar Laporan Pengaduan Warga Terdata ({{ count($pengaduanList) }})
        </h5>

        <div class="d-flex flex-column gap-3">
            @forelse($pengaduanList as $adu)
                <div class="p-3 rounded-4 border bg-light">
                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap mb-2">
                        <div>
                            <span class="badge bg-primary text-white me-1">{{ $adu['rt'] }}</span>
                            <span class="badge bg-light text-dark border">{{ $adu['kategori'] }}</span>
                            <span class="text-muted small ms-2"><i class="far fa-clock me-1"></i>{{ $adu['created_at'] }}</span>
                            <h6 class="fw-bold text-dark mb-0 mt-2">{{ $adu['lokasi'] }}</h6>
                            <small class="text-muted">Pelapor: <strong>{{ $adu['pelapor'] }}</strong> ({{ $adu['phone'] ?? '-' }})</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($adu['status'] === 'Selesai')
                                <span class="status-badge status-lengkap"><i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Selesai</span>
                            @elseif($adu['status'] === 'Diproses')
                                <span class="status-badge status-periksa"><i data-lucide="clock" style="width: 14px; height: 14px;"></i> Diproses</span>
                            @else
                                <span class="status-badge status-tindak-lanjut"><i data-lucide="alert-triangle" style="width: 14px; height: 14px;"></i> Menunggu Verifikasi</span>
                            @endif

                            @if(in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'bendahara']))
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalUpdatePengaduan{{ $adu['id'] }}">
                                    <i data-lucide="edit" style="width: 13px; height: 13px;"></i> Tindak Lanjut RW
                                </button>
                            @endif
                        </div>
                    </div>

                    <p class="text-secondary small mb-2 p-2 rounded-3 bg-white border">{{ $adu['deskripsi'] }}</p>

                    @if(!empty($adu['foto']))
                        <div class="mb-2">
                            <a href="{{ $adu['foto'] }}" target="_blank">
                                <img src="{{ $adu['foto'] }}" alt="Foto Lampiran" class="rounded-3 border" style="max-height: 120px; object-fit: cover;">
                            </a>
                        </div>
                    @endif

                    <div class="p-2 rounded-3 bg-white border small text-secondary">
                        <strong class="text-dark"><i data-lucide="message-circle" class="text-primary me-1" style="width: 14px; height: 14px; vertical-align: middle;"></i>Catatan Tindak Lanjut Pengurus:</strong>
                        {{ $adu['catatan_tindak_lanjut'] ?? 'Pengurus RW sedang mengoordinasikan penanganan pengaduan ini.' }}
                    </div>
                </div>

                <!-- Modal Update Status Tindak Lanjut Pengaduan (Khusus Admin/Ketua RW) -->
                @if(in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'bendahara']))
                    <div class="modal fade" id="modalUpdatePengaduan{{ $adu['id'] }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <form method="POST" action="{{ route('pengaduan.status', $adu['id']) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-header border-bottom">
                                        <h6 class="modal-title fw-bold text-dark">
                                            <i data-lucide="edit-3" class="me-2 text-primary" style="width: 18px; height: 18px;"></i>
                                            Tindak Lanjut Pengaduan #{{ $adu['id'] }}
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-dark">Status Penanganan</label>
                                            <select name="status" class="form-select rounded-3">
                                                <option value="Menunggu" {{ $adu['status'] === 'Menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                                <option value="Diproses" {{ $adu['status'] === 'Diproses' ? 'selected' : '' }}>Sedang Diproses / Ditangani</option>
                                                <option value="Selesai" {{ $adu['status'] === 'Selesai' ? 'selected' : '' }}>Selesai Ditangani</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-dark">Catatan Tindak Lanjut RW</label>
                                            <textarea name="catatan_tindak_lanjut" rows="4" class="form-control rounded-3" placeholder="Masukkan instruksi penanganan, nama penanggung jawab, atau hasil pekerjaan...">{{ $adu['catatan_tindak_lanjut'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="wm-btn-primary">
                                            <i data-lucide="save" style="width: 15px; height: 15px;"></i> Simpan Tindak Lanjut
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center py-5 text-muted">
                    <i data-lucide="inbox" style="width: 48px; height: 48px;" class="mb-2"></i>
                    <p class="mb-0">Tidak ada data pengaduan yang sesuai dengan filter.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Tambah Pengaduan Baru -->
<div class="modal fade" id="modalTambahPengaduan" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <form method="POST" action="{{ route('pengaduan.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="modalTambahLabel">
                        <i data-lucide="plus-circle" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Formulir Pengaduan & Aspirasi Warga RW 013
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Kategori Pengaduan <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select rounded-3" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Keamanan & Ketertiban">Keamanan & Ketertiban Lingkungan</option>
                                <option value="Kebersihan & Sampah">Kebersihan & Pengangkutan Sampah</option>
                                <option value="Fasilitas & Lampu Jalan">Fasilitas Umum & Lampu PJU</option>
                                <option value="Drainase & Saluran Air">Drainase, Banjir & Gorong-gorong</option>
                                <option value="Sosial & Ketertiban Umum">Sosial & Ketertiban Tetangga</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Wilayah RT Terkait <span class="text-danger">*</span></label>
                            <select name="rt" class="form-select rounded-3" required>
                                <option value="">-- Pilih RT --</option>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="RT 00{{ $i }}" {{ (auth()->user()->rt_number == $i) ? 'selected' : '' }}>
                                        RT 00{{ $i }} RW 013
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Lokasi Spesifik / Patokan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" class="form-control rounded-3" placeholder="Contoh: Depan Musholla RT 003, Jl. Anggrek No. 12" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Deskripsi Pengaduan Lengkap <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" rows="4" class="form-control rounded-3" placeholder="Jelaskan secara jelas kendala yang dialami, sejak kapan terjadi, dan harapan penanganannya..." required minlength="10"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Lampirkan Foto Bukti (Opsional)</label>
                            <input type="file" name="foto" class="form-control rounded-3" accept="image/*">
                            <small class="text-muted">Format JPG, PNG maks. 3 MB</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="wm-btn-primary">
                        <i data-lucide="send" style="width: 16px; height: 16px;"></i> Kirim Pengaduan ke RW 013
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
