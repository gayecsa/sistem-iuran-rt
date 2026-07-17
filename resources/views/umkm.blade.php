@extends('layouts.app')

@section('content')
<!-- Leaflet.js CSS & JS untuk Peta Navigasi Interaktif -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .umkm-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 16px !important;
    }
    .umkm-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.12) !important;
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
                        <p class="text-uppercase text-secondary mb-2 small fw-bold">UMKM & Usia Warga</p>
                        <h2 class="mb-2 fw-bold text-dark">UMKM & Tempat Makan RW 013</h2>
                        <p class="text-muted mb-0">Daftar tempat usaha, kuliner, dan jasa di sekitar RW 013 yang siap melayani warga.</p>
                        <p class="text-info small mt-2 mb-0"><i class="fas fa-hand-pointer me-1"></i> Klik kartu UMKM untuk melihat <strong>Daftar Menu & Peta Rute Navigasi</strong> dari rumah Anda.</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4 shadow-sm border">Kembali ke Dashboard</a>
                </div>
            </div>

            <div class="animate__animated animate__fadeInUp">
                <div class="row g-4">
                    @forelse($umkms as $umkm)
                        @php
                            $canEdit = auth()->user()->role === 'admin' || auth()->user()->name == $umkm->nama_pemilik;
                            $lat = $umkm->latitude ?? '-6.2320';
                            $lng = $umkm->longitude ?? '106.8190';
                        @endphp
                        
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0 umkm-card overflow-hidden" 
                                 data-bs-toggle="modal" 
                                 data-bs-target="#menuModal"
                                 data-id="{{ $umkm->id }}"
                                 data-nama="{{ $umkm->nama_umkm }}"
                                 data-jenis="{{ $umkm->jenis_usaha }}"
                                 data-alamat="{{ $umkm->alamat }}"
                                 data-lat="{{ $lat }}"
                                 data-lng="{{ $lng }}"
                                 data-canedit="{{ $canEdit ? 'true' : 'false' }}">
                                 
                                <div class="d-none raw-deskripsi">{{ $umkm->deskripsi }}</div>
                                
                                <div class="card-body d-flex gap-3 align-items-start p-4">
                                    
                                    <div class="flex-shrink-0">
                                        @if(isset($umkm->foto) && $umkm->foto)
                                            <img src="{{ asset('storage/' . $umkm->foto) }}" alt="Foto UMKM" class="rounded-4 shadow-sm object-fit-cover" style="width: 100px; height: 100px;">
                                        @else
                                            <div class="rounded-4 bg-primary-subtle text-primary d-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px;">
                                                <i class="fas fa-store fa-2x"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <div class="mb-2">
                                            <h5 class="card-title mb-1 text-truncate fw-bold text-dark">{{ $umkm->nama_umkm }}</h5>
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
                                                <i class="fas fa-clock text-info me-2"></i>Buka: {{ $umkm->jam_buka }} - {{ $umkm->jam_tutup }} WIB
                                            </p>
                                        @endif
                                    </div>
                                    
                                </div>

                                <div class="card-footer bg-light border-0 p-3 d-flex align-items-center justify-content-between text-primary small fw-semibold">
                                    <span><i class="fas fa-utensils me-1"></i> Lihat Menu & Peta Rute</span>
                                    <i class="fas fa-arrow-right"></i>
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

<!-- Modal Detail Menu & Peta Rute UMKM -->
<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <span class="badge bg-warning text-dark mb-2 shadow-sm fs-6 px-3 py-1" id="modalJenisUmkm">Kategori</span>
                    <h3 class="modal-title fw-bold text-dark" id="modalNamaUmkm">Nama UMKM</h3>
                    <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt text-danger me-1"></i> <span id="modalAlamatUmkm"></span></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="align-self: flex-start;"></button>
            </div>
            
            <div class="modal-body px-4 py-3">
                
                <!-- Nav Tabs Modal -->
                <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded-pill" id="umkmTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill fw-bold" id="menu-tab" data-bs-toggle="tab" data-bs-target="#menu-tab-pane" type="button" role="tab" aria-controls="menu-tab-pane" aria-selected="true">
                            <i class="fas fa-utensils me-1"></i> Daftar Menu & Harga
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill fw-bold" id="peta-tab" data-bs-toggle="tab" data-bs-target="#peta-tab-pane" type="button" role="tab" aria-controls="peta-tab-pane" aria-selected="false">
                            <i class="fas fa-route me-1"></i> Peta Rute Lokasi
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="umkmTabContent">
                    <!-- Tab 1: Daftar Menu -->
                    <div class="tab-pane fade show active" id="menu-tab-pane" role="tabpanel" aria-labelledby="menu-tab" tabindex="0">
                        <div class="bg-white border p-3 rounded-4 shadow-sm" id="modalDeskripsiUmkm">
                            Tunggu sebentar...
                        </div>
                    </div>

                    <!-- Tab 2: Peta Rute Navigasi -->
                    <div class="tab-pane fade" id="peta-tab-pane" role="tabpanel" aria-labelledby="peta-tab" tabindex="0">
                        <div class="card card-body bg-light border-0 rounded-4 p-3 mb-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-6 border-end">
                                    <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-home text-success me-1"></i> Titik Keberangkatan (Rumah Anda)
                                    </small>
                                    <strong class="text-dark small d-block">{{ auth()->user()->address ?? 'Jl. Melati Utama No. 15, RW 013' }}</strong>
                                </div>
                                <div class="col-md-6 ps-md-3">
                                    <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-walking text-primary me-1"></i> Estimasi Jarak & Waktu Tempuh
                                    </small>
                                    <strong class="text-primary small d-block">
                                        ~ 250 Meter &bull; 3 Menit Jalan Kaki
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="position-relative mb-3">
                            <div id="mapUmkmRoute" style="height: 300px; width: 100%; border-radius: 16px; z-index: 1;" class="border shadow-sm"></div>
                        </div>

                        <a id="btnGoogleMapsUmkm" href="#" target="_blank" class="btn btn-primary rounded-pill w-100 py-2 shadow-sm">
                            <i class="fas fa-external-link-alt me-2"></i> Navigasi Langsung di Google Maps
                        </a>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <a href="#" id="modalBtnEdit" class="btn btn-outline-primary rounded-pill px-4 d-none">
                    <i class="fas fa-edit me-1"></i> Edit Menu UMKM
                </a>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuModal = document.getElementById('menuModal');
        const userLat = -6.229000; // Lat Rumah Warga RW 013
        const userLng = 106.815500; // Lng Rumah Warga RW 013

        let leafletUmkmMap = null;
        let userMarker = null;
        let umkmMarker = null;
        let polylineRoute = null;

        let currentUmkmLat = -6.230000;
        let currentUmkmLng = 106.817000;
        let currentUmkmNama = '';
        let currentUmkmAlamat = '';

        menuModal.addEventListener('show.bs.modal', function (event) {
            const card = event.relatedTarget;
            const id = card.getAttribute('data-id');
            const nama = card.getAttribute('data-nama');
            const jenis = card.getAttribute('data-jenis');
            const alamat = card.getAttribute('data-alamat');
            const canEdit = card.getAttribute('data-canedit');
            
            currentUmkmLat = parseFloat(card.getAttribute('data-lat')) || -6.230000;
            currentUmkmLng = parseFloat(card.getAttribute('data-lng')) || 106.817000;
            currentUmkmNama = nama;
            currentUmkmAlamat = alamat;

            const rawDescDiv = card.querySelector('.raw-deskripsi');
            const deskripsi = rawDescDiv ? rawDescDiv.innerText : '';
            
            document.getElementById('modalNamaUmkm').textContent = nama;
            document.getElementById('modalJenisUmkm').textContent = jenis;
            document.getElementById('modalAlamatUmkm').textContent = alamat;
            
            const deskContainer = document.getElementById('modalDeskripsiUmkm');

            if(!deskripsi || deskripsi.trim() === '') {
                deskContainer.innerHTML = '<div class="text-center text-muted my-4"><em>Daftar menu belum ditambahkan.</em></div>';
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
                                <span class="text-dark fw-medium">${namaMenu}</span>
                                <span class="text-success fw-bold" style="font-size: 1.05rem;">${harga}</span>
                            </div>
                        `;
                    } else {
                        formattedHtml += `<div class="fw-bold mt-3 mb-1 text-primary text-uppercase" style="font-size: 0.85rem;"><i class="fas fa-tag me-1"></i> ${cleanLine}</div>`;
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

            // Google Maps Direct Link
            const gmapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${currentUmkmLat},${currentUmkmLng}&travelmode=walking`;
            document.getElementById('btnGoogleMapsUmkm').setAttribute('href', gmapsUrl);
        });

        // Trigger Leaflet map invalidate size when tab changed
        const petaTabBtn = document.getElementById('peta-tab');
        petaTabBtn.addEventListener('shown.bs.tab', function () {
            if (!leafletUmkmMap) {
                leafletUmkmMap = L.map('mapUmkmRoute').setView([userLat, userLng], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(leafletUmkmMap);
            } else {
                leafletUmkmMap.invalidateSize();
            }

            if (userMarker) leafletUmkmMap.removeLayer(userMarker);
            if (umkmMarker) leafletUmkmMap.removeLayer(umkmMarker);
            if (polylineRoute) leafletUmkmMap.removeLayer(polylineRoute);

            const homeIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#198754; color:white; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3);'><i class='fas fa-home'></i></div>",
                iconSize: [34, 34],
                iconAnchor: [17, 17]
            });

            const storeIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#ffc107; color:black; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3);'><i class='fas fa-store'></i></div>",
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });

            userMarker = L.marker([userLat, userLng], { icon: homeIcon })
                .addTo(leafletUmkmMap)
                .bindPopup('<b>Rumah Anda</b>');

            umkmMarker = L.marker([currentUmkmLat, currentUmkmLng], { icon: storeIcon })
                .addTo(leafletUmkmMap)
                .bindPopup(`<b>${currentUmkmNama}</b><br>${currentUmkmAlamat}`)
                .openPopup();

            const latlngs = [
                [userLat, userLng],
                [(userLat + currentUmkmLat) / 2, (userLng + currentUmkmLng) / 2],
                [currentUmkmLat, currentUmkmLng]
            ];

            polylineRoute = L.polyline(latlngs, {
                color: '#ffc107',
                weight: 5,
                opacity: 0.9,
                dashArray: '8, 12'
            }).addTo(leafletUmkmMap);

            const group = new L.featureGroup([userMarker, umkmMarker]);
            leafletUmkmMap.fitBounds(group.getBounds().pad(0.2));
        });
    });
</script>
@endsection