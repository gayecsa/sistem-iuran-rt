@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('bank-sampah') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3 fw-semibold">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px; vertical-align: middle;"></i> Portal
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Manajemen Tarif Sampah Resmi RW 013</h4>
                <small class="text-muted">Kelola harga beli per kilogram/liter dan riwayat perubahan tarif.</small>
            </div>
        </div>
        <div>
            <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold">
                <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;" class="me-1"></i>
                Otoritas Khusus Pengurus RW
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

    <!-- Daftar Kategori & Tarif Saat Ini -->
    <div class="wm-card bg-white border rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="p-4 border-bottom bg-light">
            <h5 class="fw-bold mb-0 text-dark">
                <i data-lucide="tag" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                Tarif Aktif Saat Ini
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Kategori Sampah</th>
                        <th>Satuan</th>
                        <th>Tarif Beli Saat Ini</th>
                        <th>Deskripsi & Pemilahan</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kategoris as $idx => $kat)
                        <tr>
                            <td class="ps-4">{{ $idx + 1 }}</td>
                            <td>
                                <strong class="text-dark">{{ $kat->nama_kategori }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">{{ $kat->satuan }}</span>
                            </td>
                            <td>
                                <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 fs-6 fw-bold px-3 py-1" style="background: #E8F5E9; color: #1B5E20;">
                                    {{ $kat->harga_formatted }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted d-block">{{ Str::limit($kat->deskripsi, 40) }}</small>
                            </td>
                            <td>
                                @if($kat->is_aktif)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border fw-semibold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold btn-edit-tarif"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditTarif"
                                    data-id="{{ $kat->id }}"
                                    data-nama="{{ $kat->nama_kategori }}"
                                    data-satuan="{{ $kat->satuan }}"
                                    data-harga="{{ $kat->harga_per_satuan }}"
                                    data-aktif="{{ $kat->is_aktif ? '1' : '0' }}">
                                    <i data-lucide="edit" style="width: 14px; height: 14px; vertical-align: middle;"></i> Ubah Tarif
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Perubahan Tarif (Audit Trail) -->
    <div class="wm-card bg-white border rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="p-4 border-bottom bg-light">
            <h5 class="fw-bold mb-0 text-dark">
                <i data-lucide="history" class="me-2 text-warning" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                Log Audit Riwayat Perubahan Tarif
            </h5>
            <small class="text-muted">Seluruh penyesuaian harga tercatat otomatis dengan identitas pengurus dan alasan tertulis.</small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Waktu Perubahan</th>
                        <th>Kategori</th>
                        <th>Tarif Lama</th>
                        <th>Tarif Baru</th>
                        <th>Diubah Oleh</th>
                        <th class="pe-4">Alasan Penyesuaian Tarif</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $histories = \App\Models\BankSampahTarifHistory::with(['kategori', 'diubahOleh'])->orderBy('created_at', 'desc')->limit(20)->get();
                    @endphp
                    @forelse($histories as $h)
                        <tr>
                            <td class="ps-4">
                                <small class="text-dark fw-semibold d-block">{{ $h->created_at->format('d/m/Y') }}</small>
                                <small class="text-muted" style="font-size: 11px;">{{ $h->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $h->kategori->nama_kategori ?? '-' }}</strong>
                            </td>
                            <td>
                                <span class="text-muted">Rp {{ number_format($h->harga_lama, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <strong class="text-success">Rp {{ number_format($h->harga_baru, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                <small class="fw-semibold text-dark">{{ $h->diubahOleh->name ?? 'Admin RW' }}</small>
                            </td>
                            <td class="pe-4">
                                <small class="text-secondary">{{ $h->alasan_perubahan }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat penyesuaian tarif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: UBAH TARIF -->
<div class="modal fade" id="modalEditTarif" tabindex="-1" aria-labelledby="modalEditTarifLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalEditTarifLabel">
                    <i data-lucide="edit" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Penyesuaian Tarif: <span id="modalKategoriNama" class="text-primary">-</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditTarif" action="" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Tarif Baru (Rupiah per <span id="modalSatuanLabel">kg</span>) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" step="100" min="0" name="harga_per_satuan" id="inputHargaBaru" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Alasan Perubahan Tarif <span class="text-danger">*</span></label>
                        <textarea name="alasan_perubahan" class="form-control rounded-3" rows="3" required placeholder="Contoh: Mengikuti penyesuaian harga pasar pengepul daur ulang Kota Bandung periode November 2026..."></textarea>
                        <small class="text-muted">Wajib diisi sebagai bagian dari akuntabilitas & audit log RW 013.</small>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_aktif" id="checkIsAktif" value="1">
                        <label class="form-check-label fw-semibold small" for="checkIsAktif">
                            Status Kategori Aktif Diterima di Seluruh RT
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Penyesuaian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        document.querySelectorAll('.btn-edit-tarif').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = this.getAttribute('data-id');
                var nama = this.getAttribute('data-nama');
                var satuan = this.getAttribute('data-satuan');
                var harga = this.getAttribute('data-harga');
                var aktif = this.getAttribute('data-aktif');

                document.getElementById('modalKategoriNama').textContent = nama;
                document.getElementById('modalSatuanLabel').textContent = satuan;
                document.getElementById('inputHargaBaru').value = harga;
                document.getElementById('checkIsAktif').checked = (aktif === '1');

                document.getElementById('formEditTarif').action = '/bank-sampah/tarif/' + id;
            });
        });
    });
</script>
@endsection
