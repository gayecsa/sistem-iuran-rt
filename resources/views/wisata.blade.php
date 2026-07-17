@extends('layouts.app')

@section('content')
<!-- Leaflet.js CSS & JS untuk Peta Navigasi Interaktif -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 animate__animated animate__fadeInUp border-0 shadow-sm mb-4" style="border-radius: 20px;">
                
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small fw-bold"><i class="fas fa-compass text-primary me-1"></i> Destinasi Rekreasi</p>
                        <h2 class="mb-2 fw-bold text-dark">Peta Lokasi Wisata Terdekat</h2>
                        <p class="text-muted small mb-0"><i class="fas fa-home text-primary me-1"></i> Titik Rumah Anda: <strong>{{ $userAddress }}</strong></p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4 shadow-sm border">Kembali ke Dashboard</a>
                </div>

                <div class="alert alert-info border-0 rounded-4 shadow-sm mb-0">
                    <i class="fas fa-tree me-2"></i>Daftar obyek wisata, taman kota, dan tempat rekreasi keluarga di sekitar wilayah RW 013. <strong>Klik salah satu kartu wisata</strong> untuk melihat rincian harga tiket, jam buka, dan peta rute perjalanan dari rumah Anda!
                </div>

            </div>

            <!-- Grid Kartu Wisata Terdekat -->
            <div class="row g-4">
                @foreach($wisataList as $index => $wst)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm wisata-card overflow-hidden" 
                             style="border-radius: 18px; cursor: pointer; transition: all 0.3s ease;"
                             data-bs-toggle="modal" 
                             data-bs-target="#modalPetaWisata"
                             data-index="{{ $index }}">
                            
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <span class="badge {{ $wst['badge_color'] }} px-3 py-2 rounded-pill shadow-sm small">{{ $wst['kategori'] }}</span>
                                        <small class="badge bg-light text-dark border"><i class="fas fa-walking me-1 text-primary"></i> {{ $wst['jarak'] }}</small>
                                    </div>

                                    <h4 class="fw-bold text-dark mb-2">{{ $wst['nama'] }}</h4>

                                    <p class="text-secondary small mb-2">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>{{ $wst['lokasi'] }}
                                    </p>

                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-clock text-info me-2"></i>Jam Buka: {{ $wst['jam_buka'] }}
                                    </p>

                                    <div class="p-3 bg-light rounded-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted small fw-semibold">Harga Tiket:</span>
                                            <span class="badge bg-success-subtle text-success border fw-bold">{{ $wst['tiket'] }}</span>
                                        </div>
                                        <small class="text-muted d-block text-truncate"><i class="fas fa-concierge-bell text-warning me-1"></i> {{ $wst['fasilitas'] }}</small>
                                    </div>
                                </div>

                                <div class="pt-3 border-top d-flex align-items-center justify-content-between text-primary small fw-semibold mt-auto">
                                    <span><i class="fas fa-route me-1"></i> Lihat Rincian & Peta Rute</span>
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

<!-- Modal Peta Rute Navigasi Ke Wisata -->
<div class="modal fade" id="modalPetaWisata" tabindex="-1" aria-labelledby="modalPetaWisataLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <span class="badge bg-primary px-3 py-2 rounded-pill mb-2 shadow-sm fs-6" id="wstKategori">Kategori</span>
                    <h3 class="modal-title fw-bold text-dark" id="wstNama">Nama Wisata</h3>
                    <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt text-danger me-1"></i> <span id="wstLokasi"></span></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="align-self: flex-start;"></button>
            </div>
            
            <div class="modal-body px-4 py-3">
                
                <!-- Info Jarak, Tiket, Jam Buka -->
                <div class="card card-body bg-light border-0 rounded-4 p-3 mb-3">
                    <div class="row g-3">
                        <div class="col-md-6 border-end">
                            <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">
                                <i class="fas fa-home text-success me-1"></i> Keberangkatan Dari Rumah Anda
                            </small>
                            <strong class="text-dark small d-block mb-2">{{ $userAddress }}</strong>

                            <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">
                                <i class="fas fa-walking text-primary me-1"></i> Estimasi Jarak & Waktu Tempuh
                            </small>
                            <strong class="text-primary small d-block">
                                <span id="wstJarak">-</span> &bull; <span id="wstWaktu">-</span>
                            </strong>
                        </div>

                        <div class="col-md-6 ps-md-3">
                            <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">
                                <i class="fas fa-ticket-alt text-warning me-1"></i> Tiket Masuk & Jam Operasional
                            </small>
                            <span class="badge bg-success text-white px-2 py-1 mb-2 d-inline-block" id="wstTiket">-</span>
                            <span class="text-dark small d-block mb-2"><i class="fas fa-clock me-1 text-info"></i> <span id="wstJamBuka">-</span></span>

                            <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">Fasilitas</small>
                            <span class="text-secondary small d-block" id="wstFasilitas">-</span>
                        </div>
                    </div>
                </div>

                <!-- Container Peta Interaktif Leaflet -->
                <div class="position-relative mb-3">
                    <div id="mapWisataRoute" style="height: 300px; width: 100%; border-radius: 16px; z-index: 1;" class="border shadow-sm"></div>
                </div>

                <!-- Ringkasan Deskripsi Wisata -->
                <div class="p-3 bg-white border rounded-4 shadow-sm mb-3">
                    <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-info-circle text-primary me-1"></i> Deskripsi & Daya Tarik:</h6>
                    <p class="mb-0 text-muted small lh-lg" id="wstDeskripsi">-</p>
                </div>

            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4 d-flex flex-column flex-sm-row gap-2">
                <a id="btnGoogleMapsWisata" href="#" target="_blank" class="btn btn-primary rounded-pill px-4 flex-grow-1 shadow-sm">
                    <i class="fas fa-external-link-alt me-2"></i> Navigasi Langsung di Google Maps
                </a>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .wisata-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13, 110, 253, 0.12) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wisataData = @json($wisataList);
        const userLat = -6.229000; // Lat Rumah Warga RW 013
        const userLng = 106.815500; // Lng Rumah Warga RW 013

        let leafletWisataMap = null;
        let userMarker = null;
        let wisataMarker = null;
        let polylineRoute = null;

        const modalElement = document.getElementById('modalPetaWisata');
        
        modalElement.addEventListener('shown.bs.modal', function (event) {
            const card = event.relatedTarget;
            const index = card.getAttribute('data-index');
            const wst = wisataData[index];

            if (!wst) return;

            document.getElementById('wstNama').textContent = wst.nama;
            document.getElementById('wstLokasi').textContent = wst.lokasi;
            document.getElementById('wstJarak').textContent = wst.jarak;
            document.getElementById('wstWaktu').textContent = wst.waktu;
            document.getElementById('wstJamBuka').textContent = wst.jam_buka;
            document.getElementById('wstTiket').textContent = wst.tiket;
            document.getElementById('wstFasilitas').textContent = wst.fasilitas;
            document.getElementById('wstDeskripsi').textContent = wst.deskripsi;

            const badgeEl = document.getElementById('wstKategori');
            badgeEl.className = 'badge px-3 py-2 rounded-pill mb-2 shadow-sm fs-6 ' + wst.badge_color;
            badgeEl.textContent = wst.kategori;

            // Direct link Google Maps
            const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${wst.lat},${wst.lng}&travelmode=walking`;
            document.getElementById('btnGoogleMapsWisata').setAttribute('href', googleMapsUrl);

            // Inisialisasi atau Update Leaflet Map
            if (!leafletWisataMap) {
                leafletWisataMap = L.map('mapWisataRoute').setView([userLat, userLng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(leafletWisataMap);
            } else {
                leafletWisataMap.invalidateSize();
            }

            // Hapus marker & route sebelumnya
            if (userMarker) leafletWisataMap.removeLayer(userMarker);
            if (wisataMarker) leafletWisataMap.removeLayer(wisataMarker);
            if (polylineRoute) leafletWisataMap.removeLayer(polylineRoute);

            // Custom Leaflet Icons
            const homeIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#198754; color:white; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3);'><i class='fas fa-home'></i></div>",
                iconSize: [34, 34],
                iconAnchor: [17, 17]
            });

            const parkIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#0d6efd; color:white; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3);'><i class='fas fa-umbrella-beach'></i></div>",
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });

            // Tambah Marker Rumah Warga
            userMarker = L.marker([userLat, userLng], { icon: homeIcon })
                .addTo(leafletWisataMap)
                .bindPopup('<b>Rumah Anda</b>');

            // Tambah Marker Wisata
            wisataMarker = L.marker([wst.lat, wst.lng], { icon: parkIcon })
                .addTo(leafletWisataMap)
                .bindPopup(`<b>${wst.nama}</b><br>${wst.lokasi}`)
                .openPopup();

            // Gambar Garis Rute Perjalanan
            const latlngs = [
                [userLat, userLng],
                [(userLat + wst.lat) / 2, (userLng + wst.lng) / 2],
                [wst.lat, wst.lng]
            ];

            polylineRoute = L.polyline(latlngs, {
                color: '#0d6efd',
                weight: 5,
                opacity: 0.8,
                dashArray: '8, 12'
            }).addTo(leafletWisataMap);

            // Fit Bounds agar kedua marker terlihat jelas di peta
            const group = new L.featureGroup([userMarker, wisataMarker]);
            leafletWisataMap.fitBounds(group.getBounds().pad(0.2));
        });
    });
</script>
@endsection
