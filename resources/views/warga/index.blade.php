@extends('layouts.app')

@section('content')
<div class="container mt-4">
    
    <!-- Notifikasi Sukses (Hanya 1 dan desain lebih rapi) -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 d-flex align-items-center" role="alert" style="background-color: #d1fae5; color: #047857; border-color: #a7f3d0; border-radius: 8px;">
            <i class="fas fa-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bagian Header: Judul, Pencarian, & Tombol -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <!-- Judul -->
        <div>
            <h3 class="mb-1" style="color: #334155;">Daftar Warga RW 013</h3>
            <p class="text-muted mb-0">Menampilkan identitas kependudukan, kontak, dan status warga.</p>
        </div>

        <!-- Aksi: Pencarian & Tambah Warga -->
        <div class="d-flex align-items-center gap-2">
            <!-- Form Pencarian -->
            <form action="{{ route('warga.index') }}" method="GET" class="d-flex mb-0">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau NIK..." value="{{ request('search') }}" style="border-radius: 20px 0 0 20px;">
                    <button class="btn border" type="submit" style="border-radius: 0 20px 20px 0; background-color: #f8fafc; border-color: #dee2e6;">
                        <i class="fas fa-search text-muted"></i>
                    </button>
                </div>
            </form>

            <!-- Tombol Tambah Warga (Hanya untuk admin) -->
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('warga.create') }}" class="btn btn-primary px-4 py-2" style="border-radius: 20px;">
                    <i class="fas fa-plus me-1"></i> Tambah Warga
                </a>
            @endif
        </div>
    </div>

    <!-- Tabel Warga -->
    <div class="card p-3 shadow-sm">
        <div class="mb-3">
            <span class="badge bg-info text-white">Total Warga: {{ $warga->total() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>RT / RW</th>
                        <th>No. KK</th>
                        <th>NIK</th>
                        <th>Gender</th>
                        <th>Tanggal Lahir</th>
                        <th>Alamat Rumah</th>
                        <th>Status</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($warga as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($warga->currentPage() - 1) * $warga->perPage() }}</td>
                            <td>
                                <a href="javascript:void(0);" class="warga-name-link fw-bold text-decoration-none text-primary" data-no-kk="{{ $item->no_kk }}" title="Klik untuk melihat anggota keluarga">
                                    {{ $item->name }}
                                    <i class="fas fa-users ms-1 text-secondary small"></i>
                                </a>
                            </td>
                            <td>{{ $item->email }}</td>
                            
                            <td>
                                @if($item->phone)
                                    <span class="text-dark">{{ $item->phone }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-secondary-subtle text-dark border fw-bold px-2 py-1">
                                    {{ $item->rt_number ? (str_contains($item->rt_number, 'RT') ? $item->rt_number : 'RT ' . str_pad($item->rt_number, 3, '0', STR_PAD_LEFT)) : 'RT 001' }} / 
                                    {{ $item->rw_number ? (str_contains($item->rw_number, 'RW') ? $item->rw_number : 'RW ' . str_pad($item->rw_number, 3, '0', STR_PAD_LEFT)) : 'RW 013' }}
                                </span>
                            </td>

                            <td class="font-monospace text-secondary" title="No. KK: {{ $item->no_kk }}">{{ $item->no_kk ?? '-' }}</td>
                            <td class="font-monospace text-secondary" title="NIK: {{ $item->nik }}">{{ $item->nik ?? '-' }}</td>
                            <td>
                                @if($item->gender === 'Laki-laki')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Laki-laki</span>
                                @elseif($item->gender === 'Perempuan')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Perempuan</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                @if($item->tanggal_lahir)
                                    <span class="text-dark small"><i class="far fa-calendar-alt me-1 text-muted"></i>{{ \Carbon\Carbon::parse($item->tanggal_lahir)->translatedFormat('d M Y') }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td>{{ $item->address }}</td>
                            
                            <td>
                                <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            @if(auth()->user()->role === 'admin')
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <a href="{{ route('warga.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit Warga">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('warga.destroy', $item) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus warga ini?');" title="Hapus Warga">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('warga.toggleActive', $item) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Ubah Status Aktif">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            {{-- SINKRONISASI: Kolom colspan disesuaikan menjadi 10 untuk Admin dan 9 untuk Umum --}}
                            <td colspan="{{ auth()->user()->role === 'admin' ? 10 : 9 }}" class="text-center text-muted">Belum ada data warga.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $warga->links() }}
        </div>
    </div>
</div>

<!-- Modal Detail Keluarga & Tambah Anggota -->
<div class="modal fade" id="detailKeluargaModal" tabindex="-1" aria-labelledby="detailKeluargaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <span class="badge bg-primary mb-2 shadow-sm"><i class="fas fa-id-card me-1"></i> Kartu Keluarga</span>
                    <h4 class="modal-title fw-bold text-dark" id="modalNoKkTitle">No. KK: -</h4>
                    <p class="text-muted small mb-0" id="modalAddressSub">-</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="align-self: flex-start;"></button>
            </div>
            
            <div class="modal-body px-4 py-4">
                
                <!-- Detail Alamat & Rumah -->
                <div class="card card-body bg-light border-0 rounded-4 p-3 mb-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Alamat Rumah</span>
                            <strong class="text-dark small" id="modalAlamatLengkap">-</strong>
                        </div>
                        <div class="col-sm-3">
                            <span class="text-muted small d-block">Status Rumah</span>
                            <span class="badge bg-info text-white" id="modalStatusRumah">-</span>
                        </div>
                        <div class="col-sm-3">
                            <span class="text-muted small d-block">Total Anggota</span>
                            <strong class="text-primary fs-5" id="modalTotalAnggota">0 Orgs</strong>
                        </div>
                    </div>
                </div>

                @if(in_array(strtolower(auth()->user()->role), ['admin', 'bendahara']))
                <!-- Form Tambah Anggota Keluarga Collapse -->
                <div class="mb-4">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formAnggotaCollapse" aria-expanded="false" aria-controls="formAnggotaCollapse">
                        <i class="fas fa-user-plus me-1"></i> Tambah Anggota Keluarga Ini
                    </button>

                    <div class="collapse mt-3" id="formAnggotaCollapse">
                        <div class="card card-body border-0 bg-light rounded-4 shadow-sm p-3">
                            <h6 class="fw-bold mb-3 text-dark small"><i class="fas fa-user-plus text-primary me-1"></i> Form Tambah Anggota Keluarga</h6>
                            <form id="formTambahAnggota">
                                @csrf
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Nama Lengkap *</label>
                                        <input type="text" class="form-control form-control-sm rounded-3" name="name" placeholder="Contoh: Alesha Zahra" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Jenis Kelamin *</label>
                                        <select class="form-select form-select-sm rounded-3" name="gender" required>
                                            <option value="Laki-laki">Laki-laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Tanggal Lahir *</label>
                                        <input type="date" class="form-control form-control-sm rounded-3" name="tanggal_lahir" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">NIK (Opsional)</label>
                                        <input type="text" class="form-control form-control-sm rounded-3" name="nik" placeholder="16 Digit NIK">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">No. HP (Opsional)</label>
                                        <input type="text" class="form-control form-control-sm rounded-3" name="phone" placeholder="08xxx">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Email (Opsional)</label>
                                        <input type="email" class="form-control form-control-sm rounded-3" name="email" placeholder="Kosongkan jika otomatis">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" id="btnSubmitAnggota">
                                    <i class="fas fa-save me-1"></i> Simpan Anggota Baru
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Tabel Daftar Anggota Keluarga -->
                <h6 class="fw-bold mb-3 text-secondary small text-uppercase"><i class="fas fa-users text-primary me-1"></i> Daftar Anggota Keluarga</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle border rounded-3 overflow-hidden small">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Gender</th>
                                <th>Tgl Lahir / Usia</th>
                                <th>Peran / Status</th>
                                <th>NIK</th>
                            </tr>
                        </thead>
                        <tbody id="modalMemberList">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const links = document.querySelectorAll('.warga-name-link');
        const modalElement = document.getElementById('detailKeluargaModal');
        const modal = new bootstrap.Modal(modalElement);
        let currentNoKk = null;

        function loadKeluarga(noKk) {
            currentNoKk = noKk;
            document.getElementById('modalNoKkTitle').textContent = 'Memuat data KK: ' + noKk + '...';
            document.getElementById('modalMemberList').innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Memuat anggota keluarga...</td></tr>';

            fetch(`/warga/keluarga/${noKk}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalNoKkTitle').textContent = 'No. KK: ' + data.no_kk;
                    document.getElementById('modalAddressSub').textContent = data.address;
                    document.getElementById('modalAlamatLengkap').textContent = data.address + ' (' + data.rt_rw + ' - No. ' + data.house_number + ')';
                    document.getElementById('modalStatusRumah').textContent = data.status_rumah;
                    document.getElementById('modalTotalAnggota').textContent = data.total_anggota + ' Orang';

                    const memberList = document.getElementById('modalMemberList');
                    let html = '';

                    data.members.forEach((m, index) => {
                        let genderBadge = m.gender === 'Laki-laki' 
                            ? '<span class="badge bg-primary-subtle text-primary border">Laki-laki</span>' 
                            : '<span class="badge bg-danger-subtle text-danger border">Perempuan</span>';

                        let peranBadge = '';
                        if (m.peran.includes('Kepala')) {
                            peranBadge = '<span class="badge bg-primary text-white"><i class="fas fa-user-shield me-1"></i> ' + m.peran + '</span>';
                        } else if (m.peran.includes('Ibu')) {
                            peranBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-heart me-1"></i> ' + m.peran + '</span>';
                        } else if (m.peran.includes('Balita')) {
                            peranBadge = '<span class="badge bg-pink text-white" style="background-color:#e83e8c;"><i class="fas fa-baby me-1"></i> ' + m.peran + '</span>';
                        } else {
                            peranBadge = '<span class="badge bg-secondary"><i class="fas fa-user me-1"></i> ' + m.peran + '</span>';
                        }

                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td class="fw-bold text-dark">${m.name}</td>
                                <td>${genderBadge}</td>
                                <td>${m.tanggal_lahir} <br><small class="text-muted">(${m.usia})</small></td>
                                <td>${peranBadge}</td>
                                <td class="font-monospace text-secondary">${m.nik}</td>
                            </tr>
                        `;
                    });

                    memberList.innerHTML = html;
                    modal.show();
                })
                .catch(err => {
                    console.error('Error fetching keluarga:', err);
                    alert('Gagal mengambil data keluarga.');
                });
        }

        links.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const noKk = this.getAttribute('data-no-kk');
                if (!noKk || noKk === '-') {
                    alert('Nomor KK warga ini belum diatur.');
                    return;
                }

                // Reset collapse form
                const collapseEl = document.getElementById('formAnggotaCollapse');
                if (collapseEl && collapseEl.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                    if (bsCollapse) bsCollapse.hide();
                }

                loadKeluarga(noKk);
            });
        });

        // Form Submit Tambah Anggota
        const formTambah = document.getElementById('formTambahAnggota');
        if (formTambah) {
            formTambah.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!currentNoKk) return;

                const btnSubmit = document.getElementById('btnSubmitAnggota');
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

                const formData = new FormData(formTambah);

                fetch(`/warga/keluarga/${currentNoKk}/anggota`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Anggota Baru';

                    if (res.success) {
                        alert(res.message);
                        formTambah.reset();

                        // Hide collapse
                        const collapseEl = document.getElementById('formAnggotaCollapse');
                        if (collapseEl) {
                            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                            if (bsCollapse) bsCollapse.hide();
                        }

                        // Reload modal content
                        loadKeluarga(currentNoKk);
                    } else {
                        alert(res.message || 'Terjadi kesalahan.');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Anggota Baru';
                    alert('Gagal menambah anggota keluarga.');
                });
            });
        }
    });
</script>
@endsection