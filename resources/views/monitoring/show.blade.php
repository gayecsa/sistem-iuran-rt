@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Back -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <a href="{{ route('monitoring-rt.index') }}" class="wm-btn-outline" style="padding: 6px 14px; font-size: 0.85rem;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Kembali ke Daftar Monitoring RT
        </a>
        <span class="badge bg-light text-muted border px-3 py-2">
            Wilayah Rukun Warga 013 &bull; Kelurahan Machi
        </span>
    </div>

    <!-- Banner Profil RT -->
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #1456A0 0%, #123766 100%); color: #fff;">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-4 bg-white text-primary p-3 fw-bold fs-3 shadow" style="width: 64px; height: 64px; display: inline-flex; align-items: center; justify-content: center;">
                    {{ $rtDetail['nomor'] }}
                </div>
                <div>
                    <h3 class="fw-bold mb-1 text-white">{{ $rtDetail['nama'] }} RW 013</h3>
                    <p class="mb-0 text-white opacity-75">
                        <i data-lucide="map-pin" style="width: 15px; height: 15px; vertical-align: middle;"></i>
                        {{ $rtDetail['alamat_pos'] }}
                    </p>
                </div>
            </div>
            <div>
                @if($rtDetail['status_laporan'] === 'Lengkap')
                    <span class="status-badge status-lengkap fs-6 px-3 py-2"><i data-lucide="check-circle-2" style="width: 16px; height: 16px;"></i> Lengkap</span>
                @elseif($rtDetail['status_laporan'] === 'Perlu diperiksa')
                    <span class="status-badge status-periksa fs-6 px-3 py-2"><i data-lucide="alert-circle" style="width: 16px; height: 16px;"></i> Perlu diperiksa</span>
                @elseif($rtDetail['status_laporan'] === 'Belum dilaporkan')
                    <span class="status-badge status-belum fs-6 px-3 py-2"><i data-lucide="clock" style="width: 16px; height: 16px;"></i> Belum dilaporkan</span>
                @else
                    <span class="status-badge status-tindak-lanjut fs-6 px-3 py-2"><i data-lucide="alert-triangle" style="width: 16px; height: 16px;"></i> Perlu tindak lanjut</span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Profil Pengurus & Warga -->
        <div class="col-lg-4">
            <div class="wm-card mb-4">
                <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">
                    <i data-lucide="users" class="me-2 text-primary" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                    Struktur Pengurus {{ $rtDetail['nama'] }}
                </h6>
                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="p-3 rounded-3 bg-light border">
                        <small class="text-muted fw-bold d-block">KETUA RT</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $rtDetail['ketua_rt'] }}</h6>
                        <span class="small text-primary"><i class="fas fa-phone me-1"></i>{{ $rtDetail['kontak'] }}</span>
                    </div>
                    <div class="p-3 rounded-3 bg-light border">
                        <small class="text-muted fw-bold d-block">SEKRETARIS RT</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $rtDetail['sekretaris'] ?? 'Sekretaris Terdaftar' }}</h6>
                    </div>
                    <div class="p-3 rounded-3 bg-light border">
                        <small class="text-muted fw-bold d-block">BENDAHARA RT</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $rtDetail['bendahara'] ?? 'Bendahara Terdaftar' }}</h6>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">
                    <i data-lucide="home" class="me-2 text-success" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                    Kependudukan Wilayah
                </h6>
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <span class="text-muted small fw-bold">JUMLAH KK</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $rtDetail['jumlah_kk'] }}</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <span class="text-muted small fw-bold">TOTAL WARGA</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $rtDetail['jumlah_warga'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Tindak Lanjut -->
            <div class="wm-card">
                <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">
                    <i data-lucide="history" class="me-2 text-secondary" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                    Riwayat Tindak Lanjut RW
                </h6>
                <div class="d-flex flex-column gap-2">
                    @forelse($rtDetail['riwayat_tindak_lanjut'] ?? [] as $riwayat)
                        <div class="p-2 rounded-3 bg-light border small">
                            <strong class="text-primary d-block">{{ $riwayat['tanggal'] }}</strong>
                            <span class="text-muted">{{ $riwayat['catatan'] }}</span>
                        </div>
                    @empty
                        <div class="text-muted small py-2">Belum ada riwayat tercatat.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Evaluasi 4 Pilar & Form Evaluasi Ketua RW -->
        <div class="col-lg-8">
            <div class="wm-card mb-4">
                <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                    <i data-lucide="check-square" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Pemeriksaan 4 Pilar Kinerja RT
                </h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-primary small">1. ADMINISTRASI & PERSURATAN</span>
                                <i data-lucide="file-check" class="text-primary" style="width: 18px; height: 18px;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $rtDetail['administrasi'] }}</h6>
                            <p class="text-muted small mb-0">Register surat pengantar, rekap data warga baru, dan buku inventaris RT.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-success small">2. KEUANGAN & IURAN WARGA</span>
                                <i data-lucide="wallet-cards" class="text-success" style="width: 18px; height: 18px;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $rtDetail['keuangan'] }}</h6>
                            <p class="text-muted small mb-0">Buku kas kasir, transparansi iuran sampah, dan setor kas RW.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-warning small">3. KEGIATAN SOSIAL MASYARAKAT</span>
                                <i data-lucide="heart-pulse" class="text-warning" style="width: 18px; height: 18px;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $rtDetail['sosial'] }}</h6>
                            <p class="text-muted small mb-0">Dukungan posyandu balita & lansia, bantuan warga duka, dan arisan warga.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-info small">4. KEBERSIHAN & KEAMANAN LINGKUNGAN</span>
                                <i data-lucide="shield-check" class="text-info" style="width: 18px; height: 18px;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $rtDetail['kebersihan_keamanan'] }}</h6>
                            <p class="text-muted small mb-0">Jadwal ronda malam, pengelolaan sampah terpilah, dan portal keamanan.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Evaluasi & Rekomendasi RW -->
                <div class="p-4 rounded-4 bg-light border">
                    <h5 class="fw-bold text-dark mb-2">
                        <i data-lucide="edit-3" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Input Catatan Evaluasi & Rekomendasi Ketua RW
                    </h5>
                    <p class="text-muted small mb-3">Catatan evaluasi ini akan tersimpan dan terhubung ke dashboard monitoring koordinasi RW 013.</p>

                    <form method="POST" action="{{ route('monitoring-rt.evaluasi', $rtDetail['rt']) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Status Laporan Periode Ini</label>
                            <select name="status_laporan" class="form-select rounded-3">
                                <option value="Lengkap" {{ $rtDetail['status_laporan'] === 'Lengkap' ? 'selected' : '' }}>Lengkap (Disetujui)</option>
                                <option value="Perlu diperiksa" {{ $rtDetail['status_laporan'] === 'Perlu diperiksa' ? 'selected' : '' }}>Perlu diperiksa (Klarifikasi Berkas)</option>
                                <option value="Perlu tindak lanjut" {{ $rtDetail['status_laporan'] === 'Perlu tindak lanjut' ? 'selected' : '' }}>Perlu tindak lanjut (Terdapat Catatan Khusus)</option>
                                <option value="Belum dilaporkan" {{ $rtDetail['status_laporan'] === 'Belum dilaporkan' ? 'selected' : '' }}>Belum dilaporkan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Catatan Evaluasi RW</label>
                            <textarea name="evaluasi_rw" rows="3" class="form-control rounded-3" required>{{ $rtDetail['evaluasi_rw'] }}</textarea>
                            <small class="text-muted">Tinjauan umum terhadap kedisiplinan dan capaian program RT.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small">Rekomendasi Perbaikan RW</label>
                            <textarea name="rekomendasi" rows="3" class="form-control rounded-3" required>{{ $rtDetail['rekomendasi'] }}</textarea>
                            <small class="text-muted">Instruksi langkah perbaikan dan target tindak lanjut pengurus RT.</small>
                        </div>

                        <button type="submit" class="wm-btn-primary">
                            <i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Catatan Evaluasi RW
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
