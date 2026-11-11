@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('bank-sampah') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3 fw-semibold">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px; vertical-align: middle;"></i> Kembali
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Detail Setoran Sampah Terpadu</h4>
                <small class="text-muted">Kode Transaksi: <strong class="text-dark">{{ $transaksi->kode_transaksi }}</strong></small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($transaksi->status === 'selesai')
                <a href="{{ route('bank-sampah.cetak', $transaksi->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill btn-sm px-3 fw-bold">
                    <i data-lucide="printer" style="width: 14px; height: 14px; vertical-align: middle;"></i> Cetak Slip
                </a>
            @endif
            <span class="badge {{ $transaksi->status_badge['bg'] }} border px-3 py-2 rounded-pill fs-6 fw-bold">
                <i data-lucide="{{ $transaksi->status_badge['icon'] }}" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                {{ $transaksi->status_label }}
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

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 p-3">
            <div class="rounded-circle bg-danger text-white p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="text-danger d-block">Gagal!</strong>
                <span class="small text-dark">{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stepper Status Alur Transaksi -->
    <div class="wm-card mb-4 bg-white border rounded-4 shadow-sm p-4">
        <h6 class="fw-bold mb-3 text-muted text-uppercase small" style="letter-spacing: 0.5px;">Progress Penyetoran & Penimbangan</h6>
        
        @php
            $statuses = [
                'menunggu' => '1. Pengajuan Warga',
                'dijadwalkan' => '2. Diterima / Dijadwalkan',
                'sedang_ditimbang' => '3. Ditimbang di Drop Point',
                'menunggu_konfirmasi' => '4. Verifikasi Nilai',
                'selesai' => '5. Selesai (Saldo Masuk)'
            ];
            $statusKeys = array_keys($statuses);
            $currentIndex = array_search($transaksi->status, $statusKeys);
            if ($transaksi->status === 'ditolak' || $transaksi->status === 'dibatalkan') {
                $currentIndex = -1;
            }
        @endphp

        @if($transaksi->status === 'ditolak')
            <div class="p-3 rounded-3 bg-danger-subtle border border-danger-subtle d-flex align-items-center gap-3">
                <i data-lucide="x-circle" class="text-danger" style="width: 24px; height: 24px;"></i>
                <div>
                    <strong class="text-danger">Pengajuan Setoran Telah Ditolak Petugas</strong>
                    <p class="mb-0 small text-dark">Alasan penolakan: <em>"{{ $transaksi->alasan_penolakan }}"</em></p>
                </div>
            </div>
        @elseif($transaksi->status === 'dibatalkan')
            <div class="p-3 rounded-3 bg-secondary-subtle border border-secondary-subtle d-flex align-items-center gap-3">
                <i data-lucide="slash" class="text-secondary" style="width: 24px; height: 24px;"></i>
                <div>
                    <strong class="text-secondary">Pengajuan Telah Dibatalkan oleh Warga</strong>
                    <p class="mb-0 small text-muted">Pengajuan ini tidak diproses lebih lanjut.</p>
                </div>
            </div>
        @else
            <div class="row g-2 text-center">
                @foreach($statuses as $k => $label)
                    @php
                        $stepIdx = array_search($k, $statusKeys);
                        $isPassed = ($currentIndex !== false && $stepIdx <= $currentIndex);
                        $isCurrent = ($k === $transaksi->status);
                    @endphp
                    <div class="col">
                        <div class="p-2 rounded-3 border {{ $isCurrent ? 'bg-success text-white border-success shadow-sm' : ($isPassed ? 'bg-emerald-50 text-success border-emerald-300' : 'bg-light text-muted border') }}">
                            <div class="fw-bold small">{{ $label }}</div>
                            @if($isPassed && !$isCurrent)
                                <small class="text-success" style="font-size: 10px;">&#10003; Selesai</small>
                            @elseif($isCurrent)
                                <small class="text-white" style="font-size: 10px;">&bull; Aktif</small>
                            @else
                                <small class="text-muted" style="font-size: 10px;">Menunggu</small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Rincian Nota Digital & Hasil Timbang -->
        <div class="col-lg-8">
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i data-lucide="file-text" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Rincian Penimbangan Sampah Terpilah
                    </h5>
                    @can('manageTransaksi', $transaksi)
                        @if(!in_array($transaksi->status, ['selesai', 'ditolak', 'dibatalkan']))
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTimbangSampah">
                                <i data-lucide="scale" style="width: 14px; height: 14px; vertical-align: middle;"></i> Input / Perbarui Timbangan
                            </button>
                        @endif
                    @endcan
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Jenis Sampah</th>
                                <th class="text-end">Berat / Volume</th>
                                <th class="text-end">Tarif per Satuan</th>
                                <th class="text-end">Subtotal Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksi->items as $idx => $item)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <strong class="text-dark">{{ $item->kategori->nama_kategori ?? '-' }}</strong>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        {{ number_format($item->berat_satuan, 2, ',', '.') }} {{ $item->satuan }}
                                    </td>
                                    <td class="text-end text-muted small">
                                        Rp {{ number_format($item->harga_per_satuan, 0, ',', '.') }} / {{ $item->satuan }}
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        {{ $item->subtotal_formatted }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <em>Belum ada rincian penimbangan sampah. Petugas Bank Sampah akan menginput hasil timbang fisik.</em>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="2" class="text-end fw-bold text-dark">TOTAL BERAT:</th>
                                <th class="text-end fw-bold text-dark fs-6">{{ $transaksi->total_berat_formatted }}</th>
                                <th class="text-end fw-bold text-dark">TOTAL NILAI TABUNGAN:</th>
                                <th class="text-end fw-bold text-success fs-5">{{ $transaksi->total_rupiah_formatted }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($transaksi->catatan_warga)
                    <div class="p-3 bg-light rounded-3 mb-2 border">
                        <small class="text-muted d-block fw-bold">Catatan dari Warga:</small>
                        <span class="text-dark small">{{ $transaksi->catatan_warga }}</span>
                    </div>
                @endif

                @if($transaksi->catatan_petugas)
                    <div class="p-3 bg-emerald-50 rounded-3 border border-emerald-200" style="background: #E8F5E9;">
                        <small class="text-success d-block fw-bold">Catatan / Instruksi Petugas:</small>
                        <span class="text-dark small">{{ $transaksi->catatan_petugas }}</span>
                    </div>
                @endif
            </div>

            <!-- Panel Aksi Operasional Khusus Petugas RT / Ketua RT / Admin RW -->
            @can('manageTransaksi', $transaksi)
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i data-lucide="shield" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                        Tindakan Operasional Petugas (RT {{ str_pad($transaksi->rt_number, 3, '0', STR_PAD_LEFT) }})
                    </h5>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if($transaksi->status === 'menunggu')
                            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTerimaTransaksi">
                                <i data-lucide="check" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Terima & Jadwalkan
                            </button>
                            <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTolakTransaksi">
                                <i data-lucide="x" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Tolak Pengajuan
                            </button>
                        @elseif(in_array($transaksi->status, ['dijadwalkan', 'sedang_ditimbang']))
                            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTimbangSampah">
                                <i data-lucide="scale" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Input Timbangan Aktual
                            </button>
                        @elseif($transaksi->status === 'menunggu_konfirmasi')
                            <form action="{{ route('bank-sampah.konfirmasi', $transaksi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan transaksi ini? Saldo sebesar {{ $transaksi->total_rupiah_formatted }} akan langsung masuk ke rekening tabungan warga.');">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                    <i data-lucide="check-circle" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Konfirmasi Selesai & Cairkan Saldo
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTimbangSampah">
                                Koreksi Timbangan
                            </button>
                        @elseif($transaksi->status === 'selesai')
                            <div class="alert alert-success d-flex align-items-center gap-2 mb-0 py-2 px-3 rounded-pill w-100">
                                <i data-lucide="check-circle" class="text-success" style="width: 18px; height: 18px;"></i>
                                <span class="small fw-semibold">Transaksi telah berstatus SELESAI. Saldo telah masuk ke rekening tabungan warga secara aman.</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endcan

            <!-- Opsi Batalkan bagi Warga Pemilik -->
            @can('cancelTransaksi', $transaksi)
                <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="text-dark">Batalkan Pengajuan</strong>
                            <p class="mb-0 text-muted small">Anda dapat membatalkan pengajuan ini selama petugas belum memprosesnya.</p>
                        </div>
                        <form action="{{ route('bank-sampah.batal', $transaksi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pengajuan setoran ini?');">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary rounded-pill px-3 fw-bold btn-sm">
                                Batalkan Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>

        <!-- Kolom Kanan: Info Warga, Unit, Petugas & Jadwal -->
        <div class="col-lg-4">
            <!-- Kartu Info Warga -->
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
                <h6 class="fw-bold mb-3 text-muted text-uppercase small" style="letter-spacing: 0.5px;">Warga Penyetor</h6>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $transaksi->user->avatar_url ?? asset('images/avatar-default.svg') }}" class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover;">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">{{ $transaksi->user->name ?? 'Warga' }}</h6>
                        <small class="text-muted d-block">{{ $transaksi->user->phone ?? 'Tanpa Nomor HP' }}</small>
                        <span class="badge bg-secondary-subtle text-secondary small">RT {{ str_pad($transaksi->user->rt_number, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
                <div class="p-3 bg-light rounded-3 small">
                    <div class="mb-1"><strong>Alamat:</strong> {{ $transaksi->user->address ?? 'RT ' . $transaksi->rt_number . ' RW 013 Sukamiskin' }}</div>
                    <div><strong>Metode:</strong> {{ $transaksi->metode_penyerahan === 'drop_point' ? 'Antar ke Drop Point' : 'Jemput ke Rumah' }}</div>
                </div>
            </div>

            <!-- Kartu Info Unit & Petugas Pengelola -->
            <div class="wm-card bg-white border rounded-4 shadow-sm p-4 mb-4">
                <h6 class="fw-bold mb-3 text-muted text-uppercase small" style="letter-spacing: 0.5px;">Unit & Petugas Bank Sampah</h6>
                <strong class="text-dark d-block mb-1">{{ $transaksi->bankSampah->nama_unit ?? 'Unit RT ' . $transaksi->rt_number }}</strong>
                <p class="text-muted small mb-3">
                    <i data-lucide="map-pin" style="width: 14px; height: 14px; vertical-align: middle;" class="text-success"></i>
                    {{ $transaksi->bankSampah->lokasi ?? '-' }}
                </p>

                <div class="p-3 bg-light rounded-3 small mb-3">
                    <div class="mb-1"><strong>Jadwal Operasional:</strong> {{ $transaksi->bankSampah->jadwal ?? 'Setiap Hari Rabu' }}</div>
                    <div class="mb-1"><strong>Petugas Penimbang:</strong> {{ $transaksi->petugas->name ?? 'Petugas RT ' . $transaksi->rt_number }}</div>
                    <div><strong>Waktu Diajukan:</strong> {{ $transaksi->tanggal_pengajuan->format('d/m/Y H:i') }}</div>
                    @if($transaksi->tanggal_selesai)
                        <div class="mt-1 text-success"><strong>Waktu Selesai:</strong> {{ $transaksi->tanggal_selesai->format('d/m/Y H:i') }}</div>
                    @endif
                </div>

                @if($transaksi->user->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $transaksi->user->phone) }}?text={{ urlencode('Halo ' . $transaksi->user->name . ', terkait pengajuan setoran Bank Sampah Terpadu RW 013 dengan kode: ' . $transaksi->kode_transaksi) }}" target="_blank" class="btn btn-outline-success w-100 rounded-pill fw-bold btn-sm">
                        <i data-lucide="message-circle" style="width: 16px; height: 16px; vertical-align: middle;"></i> Hubungi Warga via WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- MODAL: TERIMA & JADWALKAN -->
<div class="modal fade" id="modalTerimaTransaksi" tabindex="-1" aria-labelledby="modalTerimaTransaksiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalTerimaTransaksiLabel">
                    <i data-lucide="check" class="me-2 text-primary" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Terima & Jadwalkan Pengajuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bank-sampah.terima', $transaksi->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Tanggal / Waktu Penimbangan Disepakati</label>
                        <input type="datetime-local" name="tanggal_dijadwalkan" class="form-control rounded-3" value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Catatan Petugas untuk Warga</label>
                        <textarea name="catatan_petugas" class="form-control rounded-3" rows="3" placeholder="Contoh: Sampah siap dijemput hari Rabu pukul 10.30 WIB atau silakan bawa ke drop point."></textarea>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="langsung_timbang" id="checkLangsungTimbang" value="1">
                        <label class="form-check-label fw-semibold small" for="checkLangsungTimbang">
                            Sampah sudah berada di lokasi drop point (langsung proses timbang)
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Konfirmasi Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: INPUT PENIMBANGAN AKTUAL -->
<div class="modal fade" id="modalTimbangSampah" tabindex="-1" aria-labelledby="modalTimbangSampahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalTimbangSampahLabel">
                    <i data-lucide="scale" class="me-2 text-success" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Formulir Input Penimbangan Aktual Sampah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bank-sampah.timbang', $transaksi->id) }}" method="POST" id="formPenimbangan">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Pilih jenis sampah terpilah dan masukkan berat kilogram aktual dari timbangan. Sistem otomatis mengalikan tarif resmi RW 013.
                    </p>

                    <div id="wrapperItemTimbang">
                        <!-- Baris Item Pertama Default -->
                        <div class="row g-2 align-items-center mb-3 item-timbang-row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark mb-1">Jenis Sampah</label>
                                <select name="items[0][kategori_id]" class="form-select select-kategori rounded-3" required>
                                    <option value="" disabled selected>-- Pilih Kategori Sampah --</option>
                                    @foreach($kategoris as $k)
                                        <option value="{{ $k->id }}" data-harga="{{ $k->harga_per_satuan }}" data-satuan="{{ $k->satuan }}">
                                            {{ $k->nama_kategori }} (Rp {{ number_format($k->harga_per_satuan, 0, ',', '.') }}/{{ $k->satuan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark mb-1">Berat Aktual (Kg / Liter)</label>
                                <input type="number" step="0.01" min="0.01" max="5000" name="items[0][berat]" class="form-control input-berat rounded-3" placeholder="Contoh: 4.5" required>
                            </div>
                            <div class="col-md-2 text-end">
                                <label class="form-label d-block small text-muted mb-1">&nbsp;</label>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill w-100 btn-remove-row" disabled>
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold mb-3" id="btnAddRow">
                        <i data-lucide="plus" style="width: 14px; height: 14px; vertical-align: middle;"></i> Tambah Jenis Sampah Lain
                    </button>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Catatan Petugas (Opsional)</label>
                        <textarea name="catatan_petugas" class="form-control rounded-3" rows="2" placeholder="Kondisi sampah bersih, kering, dsb...">{{ $transaksi->catatan_petugas }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i data-lucide="save" class="me-1" style="width: 16px; height: 16px; vertical-align: middle;"></i> Simpan & Lanjut ke Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: TOLAK TRANSAKSI -->
<div class="modal fade" id="modalTolakTransaksi" tabindex="-1" aria-labelledby="modalTolakTransaksiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom bg-danger text-white">
                <h5 class="modal-title fw-bold text-white" id="modalTolakTransaksiLabel">
                    <i data-lucide="alert-octagon" class="me-2" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                    Tolak Pengajuan Setoran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bank-sampah.tolak', $transaksi->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_penolakan" class="form-control rounded-3" rows="3" required placeholder="Contoh: Sampah basah/terkontaminasi limbah berbahaya, atau di luar jangkauan wilayah RT..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Tolak Pengajuan</button>
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

        var rowCounter = 1;
        var btnAddRow = document.getElementById('btnAddRow');
        var wrapper = document.getElementById('wrapperItemTimbang');

        if (btnAddRow && wrapper) {
            btnAddRow.addEventListener('click', function () {
                var firstRow = wrapper.querySelector('.item-timbang-row');
                var newRow = firstRow.cloneNode(true);

                // Update input names
                var select = newRow.querySelector('.select-kategori');
                var input = newRow.querySelector('.input-berat');
                var btnRemove = newRow.querySelector('.btn-remove-row');

                select.name = 'items[' + rowCounter + '][kategori_id]';
                select.value = '';
                input.name = 'items[' + rowCounter + '][berat]';
                input.value = '';
                btnRemove.disabled = false;

                btnRemove.addEventListener('click', function () {
                    newRow.remove();
                });

                wrapper.appendChild(newRow);
                rowCounter++;

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        }
    });
</script>
@endsection
