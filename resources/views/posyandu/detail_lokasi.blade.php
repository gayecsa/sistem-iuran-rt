@extends('layouts.app')

@section('content')
<!-- Leaflet.js CSS & JS untuk Peta Navigasi Interaktif -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="dashboard-shell container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 animate__animated animate__fadeInUp border-0 shadow-sm" style="border-radius: 20px;">
                
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <p class="text-uppercase text-secondary mb-2 small fw-bold">Informasi Posyandu</p>
                        <h2 class="mb-2 fw-bold text-dark">Daftar Lokasi Posyandu Wilayah RW 013</h2>
                        <p class="text-muted small mb-0"><i class="fas fa-home text-primary me-1"></i> Rumah Anda: <strong>{{ $userAddress }}</strong></p>
                    </div>
                    <a href="{{ route('posyandu') }}" class="btn btn-soft rounded-pill px-4">Kembali</a>
                </div>

                <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4">
                    <i class="fas fa-map-marked-alt me-2"></i>Berikut adalah 8 lokasi pelayanan Posyandu yang tersebar di wilayah RW 013. <strong>Klik salah satu lokasi Posyandu</strong> untuk membuka peta navigasi rute perjalanan dari rumah Anda!
                </div>

                <div class="row g-4 mt-1">
                    @foreach($posyanduList as $index => $pos)
                        <div class="col-md-6">
                            <div class="card bg-white border-0 shadow-sm posyandu-lokasi-card h-100 p-4"
                                 style="border-radius: 18px; cursor: pointer; transition: all 0.3s ease; border-left: 5px solid #0d6efd !important;"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#modalPetaPosyandu"
                                 data-index="{{ $index }}">
                                
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill small fw-bold">
                                        <i class="fas fa-building me-1"></i> {{ $pos['nama'] }}
                                    </span>
                                    <small class="badge bg-success-subtle text-success border px-2 py-1">
                                        <i class="fas fa-walking me-1"></i> {{ $pos['jarak'] }}
                                    </small>
                                </div>

                                <h5 class="fw-bold text-dark mb-2">{{ $pos['nama'] }}</h5>
                                <p class="mb-2 small text-secondary">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i><strong>Alamat:</strong> {{ $pos['lokasi'] }}
                                </p>
                                <p class="mb-3 small text-muted">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i><strong>Jadwal:</strong> {{ $pos['jadwal'] }}
                                </p>

                                <div class="pt-3 border-top d-flex align-items-center justify-content-between text-primary small fw-semibold mt-auto">
                                    <span><i class="fas fa-route me-1"></i> Klik untuk Buka Peta Rute</span>
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Peta Rute Navigasi Ke Posyandu -->
<div class="modal fade" id="modalPetaPosyandu" tabindex="-1" aria-labelledby="modalPetaPosyanduLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <span class="badge bg-primary px-3 py-2 rounded-pill mb-2 shadow-sm fs-6">Peta Navigasi Warga RW 013</span>
                    <h3 class="modal-title fw-bold text-dark" id="mPosyanduTitle">Posyandu</h3>
                    <p class="text-muted small mb-0"><i class="fas fa-map-pin text-danger me-1"></i> <span id="mPosyanduAlamat"></span></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="align-self: flex-start;"></button>
            </div>
            
            <div class="modal-body px-4 py-3">
                
                <!-- Info Jarak & Waktu Tempuh -->
                <div class="card card-body bg-light border-0 rounded-4 p-3 mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6 border-end">
                            <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">
                                <i class="fas fa-home text-success me-1"></i> Titik Keberangkatan (Rumah Anda)
                            </small>
                            <strong class="text-dark small d-block">{{ $userAddress }}</strong>
                        </div>
                        <div class="col-md-6 ps-md-3">
                            <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">
                                <i class="fas fa-walking text-primary me-1"></i> Estimasi Jarak & Waktu Tempuh
                            </small>
                            <strong class="text-primary small d-block">
                                <span id="mJarak">-</span> &bull; <span id="mWaktu">-</span>
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Container Peta Interaktif Leaflet -->
                <div class="position-relative mb-3">
                    <div id="mapPosyanduRoute" style="height: 320px; width: 100%; border-radius: 16px; z-index: 1;" class="border shadow-sm"></div>
                </div>

                <!-- Panduan Rute Jalan -->
                <div class="p-3 bg-white border rounded-4 shadow-sm mb-3">
                    <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-directions text-primary me-1"></i> Panduan Petunjuk Rute Jalan:</h6>
                    <p class="mb-0 text-muted small lh-lg" id="mRutePetunjuk">-</p>
                </div>

            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4 d-flex flex-column flex-sm-row gap-2">
                <a id="btnGoogleMapsNav" href="#" target="_blank" class="btn btn-primary rounded-pill px-4 flex-grow-1 shadow-sm">
                    <i class="fas fa-external-link-alt me-2"></i> Navigasi Langsung di Google Maps
                </a>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .posyandu-lokasi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13, 110, 253, 0.12) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const posyanduData = @json($posyanduList);
        const userLat = -6.229000; // Lat Rumah Warga RW 013
        const userLng = 106.815500; // Lng Rumah Warga RW 013

        let leafletMap = null;
        let userMarker = null;
        let posyanduMarker = null;
        let polylineRoute = null;

        const modalElement = document.getElementById('modalPetaPosyandu');
        
        modalElement.addEventListener('shown.bs.modal', function (event) {
            const card = event.relatedTarget;
            const index = card.getAttribute('data-index');
            const pos = posyanduData[index];

            if (!pos) return;

            document.getElementById('mPosyanduTitle').textContent = pos.nama;
            document.getElementById('mPosyanduAlamat').textContent = pos.lokasi;
            document.getElementById('mJarak').textContent = pos.jarak;
            document.getElementById('mWaktu').textContent = pos.waktu;
            document.getElementById('mRutePetunjuk').textContent = pos.rute_petunjuk;

            // Direct link Google Maps
            const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${pos.lat},${pos.lng}&travelmode=walking`;
            document.getElementById('btnGoogleMapsNav').setAttribute('href', googleMapsUrl);

            // Inisialisasi atau Update Leaflet Map
            if (!leafletMap) {
                leafletMap = L.map('mapPosyanduRoute').setView([userLat, userLng], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(leafletMap);
            } else {
                leafletMap.invalidateSize();
            }

            // Hapus marker & route sebelumnya
            if (userMarker) leafletMap.removeLayer(userMarker);
            if (posyanduMarker) leafletMap.removeLayer(posyanduMarker);
            if (polylineRoute) leafletMap.removeLayer(polylineRoute);

            // Custom Leaflet Icons
            const homeIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#198754; color:white; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3);'><i class='fas fa-home'></i></div>",
                iconSize: [34, 34],
                iconAnchor: [17, 17]
            });

            const posIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#dc3545; color:white; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3);'><i class='fas fa-clinic-medical'></i></div>",
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });

            // Tambah Marker Rumah Warga
            userMarker = L.marker([userLat, userLng], { icon: homeIcon })
                .addTo(leafletMap)
                .bindPopup('<b>Rumah Anda</b><br>Titik Keberangkatan');

            // Tambah Marker Posyandu
            posyanduMarker = L.marker([pos.lat, pos.lng], { icon: posIcon })
                .addTo(leafletMap)
                .bindPopup(`<b>${pos.nama}</b><br>${pos.lokasi}`)
                .openPopup();

            // Gambar Garis Rute Perjalanan (Dotted Polyline)
            const latlngs = [
                [userLat, userLng],
                [(userLat + pos.lat) / 2, (userLng + pos.lng) / 2],
                [pos.lat, pos.lng]
            ];

            polylineRoute = L.polyline(latlngs, {
                color: '#0d6efd',
                weight: 5,
                opacity: 0.8,
                dashArray: '8, 12'
            }).addTo(leafletMap);

            // Fit Bounds agar kedua marker terlihat jelas di peta
            const group = new L.featureGroup([userMarker, posyanduMarker]);
            leafletMap.fitBounds(group.getBounds().pad(0.2));
        });
    });
</script>
@endsection