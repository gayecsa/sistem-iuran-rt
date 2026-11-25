@extends('layouts.app')

@push('styles')
<!-- Leaflet.js CSS untuk Peta Navigasi Interaktif -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    :root {
        --wm-primary: #1456A0;
        --wm-primary-dark: #0F3966;
        --wm-secondary: #2878D0;
        --wm-light-blue: #EAF3FF;
        --wm-bg: #F4F7FB;
        --wm-text-dark: #172B4D;
        --wm-text-muted: #64748B;
        --wm-border-card: #E2E8F0;
        --wm-success: #16865D;
    }

    /* Hero Banner Wisata & Fasilitas */
    .wisata-hero-header {
        background: linear-gradient(135deg, #0F3966 0%, #1456A0 55%, #2878D0 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #FFFFFF;
        box-shadow: 0 4px 14px rgba(20, 86, 160, 0.15);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .wisata-hero-header::after {
        content: "";
        position: absolute;
        top: -40px;
        right: -30px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }

    /* Standard Card Box */
    .wm-card-box {
        background: #FFFFFF;
        border: 1px solid var(--wm-border-card);
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        padding: 24px;
        margin-bottom: 24px;
    }

    /* Interactive Wisata Card */
    .wisata-card {
        background: #FFFFFF;
        border: 1px solid var(--wm-border-card);
        border-radius: 16px;
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
    }

    .wisata-card:hover {
        transform: translateY(-4px);
        border-color: var(--wm-secondary);
        box-shadow: 0 10px 24px rgba(20, 86, 160, 0.12) !important;
    }

    /* Dark Mode Overrides for Wisata */
    [data-theme="dark"] .wm-card-box,
    body.dark-mode .wm-card-box,
    [data-theme="dark"] .wisata-card,
    body.dark-mode .wisata-card {
        background: #131F35 !important;
        border-color: #24344F !important;
        color: #F8FAFC !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
    }

    [data-theme="dark"] .wisata-card:hover,
    body.dark-mode .wisata-card:hover {
        border-color: #3B82F6 !important;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.4) !important;
    }

    [data-theme="dark"] .wisata-card h5,
    body.dark-mode .wisata-card h5,
    [data-theme="dark"] .wisata-card .text-dark,
    body.dark-mode .wisata-card .text-dark {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .wisata-card p,
    body.dark-mode .wisata-card p,
    [data-theme="dark"] .wisata-card .text-muted,
    body.dark-mode .wisata-card .text-muted {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .wisata-card .bg-light,
    body.dark-mode .wisata-card .bg-light {
        background: #16243D !important;
        border-color: #24344F !important;
    }

    [data-theme="dark"] .wisata-card .bg-light span,
    body.dark-mode .wisata-card .bg-light span,
    [data-theme="dark"] .wisata-card .bg-light small,
    body.dark-mode .wisata-card .bg-light small {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .wisata-card [style*="border-top"],
    body.dark-mode .wisata-card [style*="border-top"] {
        border-top-color: #24344F !important;
    }

    [data-theme="dark"] #modalPetaWisata .modal-content,
    body.dark-mode #modalPetaWisata .modal-content {
        background: #131F35 !important;
        border: 1px solid #24344F !important;
        color: #F1F5F9 !important;
    }

    [data-theme="dark"] #modalPetaWisata .modal-title,
    body.dark-mode #modalPetaWisata .modal-title,
    [data-theme="dark"] #modalPetaWisata .text-dark,
    body.dark-mode #modalPetaWisata .text-dark {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] #modalPetaWisata .card-body.bg-light,
    body.dark-mode #modalPetaWisata .card-body.bg-light,
    [data-theme="dark"] #modalPetaWisata .bg-white,
    body.dark-mode #modalPetaWisata .bg-white {
        background: #16243D !important;
        border-color: #24344F !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] #modalPetaWisata .text-muted,
    body.dark-mode #modalPetaWisata .text-muted {
        color: #94A3B8 !important;
    }

    @media (max-width: 991.98px) {
        .wisata-hero-header {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    {{-- ======================================================================
         1. HERO BANNER WISATA & FASILITAS PUBLIK (WARKAS BLUE DESIGN)
         ====================================================================== --}}
    <div class="wisata-hero-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                    <i data-lucide="trees" style="width: 12px; height: 12px; vertical-align: middle;"></i>
                    PANDUAN REKREASI &amp; FASILITAS LINGKUNGAN
                </span>
                <span class="text-white-50 small">•</span>
                <span class="text-white small fw-medium">Kelurahan Sukamiskin, Arcamanik</span>
            </div>
            <h3 class="fw-bold mb-1 text-white">Wisata &amp; Fasilitas Terdekat</h3>
            <p class="mb-0 text-white-50 small">
                <i data-lucide="map-pin" style="width: 14px; height: 14px;" class="me-1"></i>
                Pusat olahraga, taman kota, rekreasi keluarga, dan wisata alam di sekitar wilayah RW 013 Sukamiskin Arcamanik.
            </p>
        </div>

        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('peta.index') }}" class="btn btn-outline-light rounded-pill px-3 fw-bold small d-inline-flex align-items-center gap-2">
                <i data-lucide="map" style="width: 16px; height: 16px;"></i>
                <span>Buka Peta Wilayah</span>
            </a>
        </div>
    </div>

    {{-- Banner Titik Acuan Domisili Warga --}}
    <div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3" style="background: var(--wm-light-blue); color: var(--wm-primary);">
        <div class="p-2 rounded-circle bg-white border shadow-xs" style="flex-shrink: 0;">
            <i data-lucide="home" style="width: 20px; height: 20px; color: var(--wm-primary);"></i>
        </div>
        <div>
            <span class="d-block small text-muted">Titik Acuan Rumah / Domisili Anda:</span>
            <strong class="d-block text-dark">{{ $userAddress }}</strong>
            <small class="text-muted">Klik salah satu kartu destinasi di bawah untuk melihat rincian harga tiket, jam buka, rute navigasi, dan estimasi waktu perjalanan.</small>
        </div>
    </div>

    {{-- ======================================================================
         2. GRID KARTU DESTINASI WISATA & FASILITAS
         ====================================================================== --}}
    <div class="row g-4 mb-4">
        @foreach($wisataList as $index => $wst)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="wisata-card p-4" 
                     data-bs-toggle="modal" 
                     data-bs-target="#modalPetaWisata"
                     data-index="{{ $index }}">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge {{ $wst['badge_color'] }} px-3 py-1 rounded-pill small fw-bold">
                                {{ $wst['kategori'] }}
                            </span>
                            <span class="badge bg-light text-secondary border px-2 py-1 small">
                                <i data-lucide="navigation" style="width: 11px; height: 11px; vertical-align: middle;"></i> {{ $wst['jarak'] }}
                            </span>
                        </div>

                        <h5 class="fw-bold mb-2" style="color: var(--wm-text-dark); line-height: 1.35;">
                            {{ $wst['nama'] }}
                        </h5>

                        <p class="text-muted small mb-2" style="font-size: 0.82rem; line-height: 1.4;">
                            <i data-lucide="map-pin" style="width: 12px; height: 12px;" class="text-danger me-1"></i>
                            {{ $wst['lokasi'] }}
                        </p>

                        <p class="text-muted small mb-3" style="font-size: 0.8rem;">
                            <i data-lucide="clock" style="width: 12px; height: 12px;" class="text-info me-1"></i>
                            Jam Operasional: <strong>{{ $wst['jam_buka'] }}</strong>
                        </p>

                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-semibold">Harga Tiket:</span>
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 fw-bold">{{ $wst['tiket'] }}</span>
                            </div>
                            <small class="text-muted d-block text-truncate" style="font-size: 0.78rem;">
                                <i data-lucide="check-circle" style="width: 12px; height: 12px;" class="text-warning me-1"></i>
                                {{ $wst['fasilitas'] }}
                            </small>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex align-items-center justify-content-between text-primary small fw-bold mt-auto" style="color: var(--wm-primary) !important;">
                        <span><i data-lucide="route" style="width: 14px; height: 14px;" class="me-1"></i> Rute &amp; Peta Navigasi</span>
                        <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ======================================================================
     MODAL PETA RUTE NAVIGASI INTERAKTIF (LEAFLET + GOOGLE MAPS)
     ====================================================================== --}}
<div class="modal fade" id="modalPetaWisata" tabindex="-1" aria-labelledby="modalPetaWisataLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <span class="badge bg-primary px-3 py-1 rounded-pill mb-2 small fw-bold" id="wstKategori">Kategori</span>
                    <h4 class="modal-title fw-bold text-dark mb-1" id="wstNama">Nama Wisata</h4>
                    <p class="text-muted small mb-0"><i data-lucide="map-pin" style="width: 12px; height: 12px;" class="text-danger me-1"></i> <span id="wstLokasi"></span></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body px-4 py-3">
                <!-- Info Jarak, Tiket, Jam Buka -->
                <div class="card card-body bg-light border-0 rounded-4 p-3 mb-3">
                    <div class="row g-3">
                        <div class="col-md-6 border-end">
                            <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">
                                <i data-lucide="home" style="width: 12px; height: 12px;" class="text-success me-1"></i> Keberangkatan Dari Rumah Anda
                            </small>
                            <strong class="text-dark small d-block mb-2">{{ $userAddress }}</strong>

                            <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">
                                <i data-lucide="navigation" style="width: 12px; height: 12px;" class="text-primary me-1"></i> Estimasi Jarak &amp; Waktu Tempuh
                            </small>
                            <strong class="text-primary small d-block">
                                <span id="wstJarak">-</span> &bull; <span id="wstWaktu">-</span>
                            </strong>
                        </div>

                        <div class="col-md-6 ps-md-3">
                            <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">
                                <i data-lucide="ticket" style="width: 12px; height: 12px;" class="text-warning me-1"></i> Tiket Masuk &amp; Jam Operasional
                            </small>
                            <span class="badge bg-success text-white px-2 py-1 mb-2 d-inline-block" id="wstTiket">-</span>
                            <span class="text-dark small d-block mb-2"><i data-lucide="clock" style="width: 12px; height: 12px;" class="text-info me-1"></i> <span id="wstJamBuka">-</span></span>

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
                <div class="p-3 bg-white border rounded-4 shadow-sm mb-2">
                    <h6 class="fw-bold text-dark small mb-2"><i data-lucide="info" style="width: 14px; height: 14px;" class="text-primary me-1"></i> Deskripsi &amp; Daya Tarik:</h6>
                    <p class="mb-0 text-muted small lh-lg" id="wstDeskripsi">-</p>
                </div>
            </div>

            <div class="modal-footer border-top-0 pt-0 px-4 pb-4 d-flex flex-column flex-sm-row gap-2">
                <a id="btnGoogleMapsWisata" href="#" target="_blank" class="btn btn-primary rounded-pill px-4 flex-grow-1 fw-bold shadow-sm" style="background: var(--wm-primary); border-color: var(--wm-primary);">
                    <i data-lucide="external-link" style="width: 14px; height: 14px;" class="me-1"></i> Buka Rute di Google Maps
                </a>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const wisataData = @json($wisataList);
        // Koordinat Acuan Sukamiskin, Arcamanik, Bandung (RW 013)
        const userLat = -6.915000;
        const userLng = 107.679000;

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
            badgeEl.className = 'badge px-3 py-1 rounded-pill mb-2 small fw-bold ' + wst.badge_color;
            badgeEl.textContent = wst.kategori;

            // Direct link Google Maps
            const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${wst.lat},${wst.lng}&travelmode=driving`;
            document.getElementById('btnGoogleMapsWisata').setAttribute('href', googleMapsUrl);

            // Inisialisasi atau Update Leaflet Map
            if (!leafletWisataMap) {
                leafletWisataMap = L.map('mapWisataRoute').setView([userLat, userLng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
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
                html: "<div style='background-color:#16865D; color:white; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3); font-size:14px;'><i class='fas fa-home'></i></div>",
                iconSize: [34, 34],
                iconAnchor: [17, 17]
            });

            const parkIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#1456A0; color:white; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3); font-size:14px;'><i class='fas fa-tree'></i></div>",
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });

            // Tambah Marker Rumah Warga
            userMarker = L.marker([userLat, userLng], { icon: homeIcon })
                .addTo(leafletWisataMap)
                .bindPopup('<b>Titik Acuan Rumah / Domisili RW 013</b>');

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
                color: '#1456A0',
                weight: 4,
                opacity: 0.8,
                dashArray: '6, 10'
            }).addTo(leafletWisataMap);

            // Fit Bounds agar kedua marker terlihat jelas di peta
            const group = new L.featureGroup([userMarker, wisataMarker]);
            leafletWisataMap.fitBounds(group.getBounds().pad(0.2));

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    });
</script>
@endpush
