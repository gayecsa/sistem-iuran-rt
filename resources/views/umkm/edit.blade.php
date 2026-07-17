@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4 shadow-sm animate__animated animate__fadeInUp border-0" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <div>
                        <h4 class="mb-0 fw-bold">Edit Menu & Data UMKM</h4>
                        <p class="text-muted small mb-0 mt-1">Perbarui daftar menu atau informasi UMKM Anda di sini.</p>
                    </div>
                    <a href="{{ route('umkm.index') }}" class="btn btn-light rounded-pill px-3">Batal</a>
                </div>

                <form id="formEditUmkm" action="{{ route('umkm.update', $umkm->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') 

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small text-uppercase fw-bold">Nama UMKM</label>
                            <input type="text" name="nama_umkm" class="form-control bg-light" value="{{ $umkm->nama_umkm }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small text-uppercase fw-bold">Kategori Usaha</label>
                            <input type="text" name="jenis_usaha" class="form-control bg-light" value="{{ $umkm->jenis_usaha }}" required>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <label class="form-label text-secondary small text-uppercase fw-bold">Foto UMKM (Opsional)</label>
                            <input type="file" name="foto" class="form-control bg-light" accept="image/*">
                            @if(isset($umkm->foto) && $umkm->foto)
                                <div class="mt-2">
                                    <small class="text-muted d-block mb-1">Foto saat ini:</small>
                                    <img src="{{ asset('storage/' . $umkm->foto) }}" alt="Foto UMKM" class="rounded shadow-sm object-fit-cover" style="width: 100px; height: 100px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4 mt-4">
                        <label class="form-label text-primary small text-uppercase fw-bold">
                            <i class="fas fa-utensils me-2"></i>Daftar Menu & Harga
                        </label>
                        
                        <table class="table table-bordered mt-2">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Menu</th>
                                    <th width="35%">Harga</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="menuBody"></tbody>
                        </table>

                        <button type="button" class="btn btn-sm btn-outline-success" onclick="tambahBaris()">
                            <i class="fas fa-plus me-1"></i> Tambah Baris Menu
                        </button>
                        <input type="hidden" name="deskripsi" id="deskripsiOutput">
                    </div>

                    <div class="text-end pt-2 border-top pt-4">
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const existingData = {!! json_encode($umkm->deskripsi ?? '') !!};
        const lines = existingData.split(/\r?\n/);
        let hasData = false;
        
        lines.forEach(line => {
            const cleanLine = line.trim();
            if(cleanLine !== '') {
                const rpMatch = cleanLine.match(/rp\.?\s*/i);
                if(rpMatch) {
                    let nama = cleanLine.substring(0, rpMatch.index).trim();
                    let harga = cleanLine.substring(rpMatch.index + rpMatch[0].length).trim();
                    nama = nama.replace(/^[-*•]\s*/, '').trim();
                    tambahBaris(nama, harga);
                    hasData = true;
                } else {
                    tambahBaris(cleanLine, '');
                    hasData = true;
                }
            }
        });

        if(!hasData) tambahBaris();
    });

    function tambahBaris(nama = '', harga = '') {
        const tbody = document.getElementById('menuBody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" class="form-control input-nama border-0 bg-light" value="${nama}" placeholder="Contoh: Jus Apel"></td>
            <td>
                <div class="input-group">
                    <span class="input-group-text border-0 bg-light text-muted">Rp.</span>
                    <input type="number" class="form-control input-harga border-0 bg-light" value="${harga.replace(/[^0-9]/g, '')}" placeholder="12000">
                </div>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="this.closest('tr').remove()" title="Hapus Baris"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    document.getElementById('formEditUmkm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#menuBody tr');
        let deskripsiFinal = '';

        rows.forEach(row => {
            const nama = row.querySelector('.input-nama').value.trim();
            let harga = row.querySelector('.input-harga').value.trim();

            if(nama !== '') {
                if(harga !== '') {
                    harga = Number(harga).toLocaleString('id-ID'); 
                    deskripsiFinal += nama + ' Rp. ' + harga + '\n';
                } else {
                    deskripsiFinal += nama + '\n';
                }
            }
        });
        document.getElementById('deskripsiOutput').value = deskripsiFinal.trim();
    });
</script>
@endsection