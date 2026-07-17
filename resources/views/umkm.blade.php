@extends('layouts.app')

@section('content')
<style>
    .umkm-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 16px !important; /* Membuat sudut lebih melengkung seperti bubble */
    }
    .umkm-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.1) !important; /* Bayangan lebih halus saat di-hover */
    }
    .header-card {
        border-radius: 16px !important;
    }
</style>

<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card p-4 border-0 shadow-sm mb-4 header-card animate__animated animate__fadeInUp">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small fw-bold">UMKM</p>
                        <h2 class="mb-2 fw-bold text-dark">UMKM & Tempat Makan Terdekat</h2>
                        <p class="text-muted mb-0">Daftar tempat usaha dan warung makan di sekitar RT 001 yang bisa mendukung kebutuhan warga.</p>
                        <p class="text-info small mt-2 mb-0"><i class="fas fa-hand-pointer me-1"></i> Klik kartu UMKM untuk melihat menu/detailnya.</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4 shadow-sm border">Kembali ke Dashboard</a>
                </div>
            </div>

            <div class="animate__animated animate__fadeInUp">
                <div class="row g-4">
                    @forelse($umkms as $umkm)
                        @php
                            $canEdit = auth()->user()->role === 'admin' || auth()->user()->name == $umkm->nama_pemilik;
                        @endphp
                        
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0 umkm-card" 
                                 data-bs-toggle="modal" 
                                 data-bs-target="#menuModal"
                                 data-id="{{ $umkm->id }}"
                                 data-nama="{{ $umkm->nama_umkm }}"
                                 data-jenis="{{ $umkm->jenis_usaha }}"
                                 data-canedit="{{ $canEdit ? 'true' : 'false' }}">
                                 
                                <div class="d-none raw-deskripsi">{{ $umkm->deskripsi }}</div>
                                
                                <div class="card-body d-flex gap-3 align-items-start p-4">
                                    
                                    <div class="flex-shrink-0">
                                        @if(isset($umkm->foto) && $umkm->foto)
                                            <img src="{{ asset('storage/' . $umkm->foto) }}" alt="Foto UMKM" class="rounded-4 shadow-sm object-fit-cover" style="width: 100px; height: 100px;">
                                        @else
                                            <div class="rounded-4 bg-light text-secondary d-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px;">
                                                <i class="fas fa-store fa-2x"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <div class="mb-2">
                                            <h5 class="card-title mb-1 text-truncate fw-bold">{{ $umkm->nama_umkm }}</h5>
                                            <span class="badge bg-warning text-dark">{{ $umkm->jenis_usaha }}</span>
                                        </div>
                                        
                                        <p class="card-text small mb-1 text-truncate">
                                            <i class="fas fa-map-marker-alt text-danger me-2"></i>{{ $umkm->alamat }}
                                        </p>

                                        @if($umkm->nama_pemilik)
                                            <p class="small mb-1 text-truncate">
                                                <i class="fas fa-user text-primary me-2"></i>{{ $umkm->nama_pemilik }}
                                            </p>
                                        @endif

                                        @if($umkm->no_hp)
                                            <p class="small mb-1 text-truncate">
                                                <i class="fas fa-phone text-success me-2"></i><a href="tel:{{ $umkm->no_hp }}" onclick="event.stopPropagation();" class="text-decoration-none">{{ $umkm->no_hp }}</a>
                                            </p>
                                        @endif

                                        @if($umkm->jam_buka && $umkm->jam_tutup)
                                            <p class="small mb-0 text-truncate">
                                                <i class="fas fa-clock text-info me-2"></i>{{ $umkm->jam_buka }} - {{ $umkm->jam_tutup }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info border-0 shadow-sm rounded-4" role="alert">
                                <i class="fas fa-info-circle me-2"></i>Belum ada data UMKM.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            
        </div>
    </div>
</div>

<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0" style="background-color: #fef08a; border-radius: 20px 20px 0 0;">
                <div>
                    <span class="badge bg-white text-dark mb-2 shadow-sm" id="modalJenisUmkm">Kategori</span>
                    <h4 class="modal-title fw-bold text-dark" id="modalNamaUmkm">Nama UMKM</h4>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-utensils text-warning me-2"></i>Daftar Menu & Detail</h6>
                <div class="bg-white border p-3 rounded-4 shadow-sm" id="modalDeskripsiUmkm">
                    Tunggu sebentar...
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <a href="#" id="modalBtnEdit" class="btn btn-outline-primary rounded-pill px-4 d-none">
                    <i class="fas fa-edit me-1"></i> Edit Menu
                </a>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuModal = document.getElementById('menuModal');
        
        menuModal.addEventListener('show.bs.modal', function (event) {
            const card = event.relatedTarget;
            const id = card.getAttribute('data-id');
            const nama = card.getAttribute('data-nama');
            const jenis = card.getAttribute('data-jenis');
            const canEdit = card.getAttribute('data-canedit');
            
            const rawDescDiv = card.querySelector('.raw-deskripsi');
            const deskripsi = rawDescDiv ? rawDescDiv.innerText : '';
            
            document.getElementById('modalNamaUmkm').textContent = nama;
            document.getElementById('modalJenisUmkm').textContent = jenis;
            
            const deskContainer = document.getElementById('modalDeskripsiUmkm');

            if(!deskripsi || deskripsi.trim() === '') {
                deskContainer.innerHTML = '<div class="text-center text-muted my-4"><em>Menu belum ditambahkan.</em></div>';
            } else {
                let formattedHtml = '';
                const lines = deskripsi.split(/\r?\n/);

                lines.forEach(line => {
                    const cleanLine = line.trim();
                    if (cleanLine === '') return; 

                    const rpIndex = cleanLine.search(/rp\.?\s*/i);

                    if (rpIndex !== -1) {
                        let namaMenu = cleanLine.substring(0, rpIndex).trim();
                        let harga = cleanLine.substring(rpIndex).trim();

                        formattedHtml += `
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                                <span class="text-dark">${namaMenu}</span>
                                <span class="text-success fw-bold" style="font-size: 1.05rem;">${harga}</span>
                            </div>
                        `;
                    } else {
                        formattedHtml += `<div class="fw-bold mt-3 mb-1 text-secondary text-uppercase" style="font-size: 0.85rem;">${cleanLine}</div>`;
                    }
                });

                deskContainer.innerHTML = formattedHtml;
            }

            const btnEdit = document.getElementById('modalBtnEdit');
            if(canEdit === 'true') {
                btnEdit.classList.remove('d-none');
                btnEdit.href = `/umkm/${id}/edit`; 
            } else {
                btnEdit.classList.add('d-none');
            }
        });
    });
</script>
@endsection