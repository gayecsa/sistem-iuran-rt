@extends('layouts.app')

@section('content')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    #mapRw {
        height: 580px;
        width: 100%;
        border-radius: 16px;
        z-index: 1;
    }
    .filter-btn.active {
        background-color: #1456A0 !important;
        color: #ffffff !important;
        border-color: #1456A0 !important;
    }
    .custom-leaflet-icon {
        background: transparent;
        border: none;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        font-family: inherit;
    }
    .leaflet-popup-content {
        margin: 14px;
        line-height: 1.4;
    }
</style>

<div class="container-fluid p-0">
    <!-- Header Peta Wilayah Bandung -->
    <div class="wm-card mb-4" style="background: linear-gradient(135deg, #123766 0%, #1456A0 100%); color: #fff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-warning text-dark mb-2 fw-bold px-3 py-1 rounded-pill">SISTEM INFORMASI GEOGRAFIS LINGKUNGAN</span>
                <h3 class="fw-bold mb-1 text-white">Peta Wilayah & Titik Layanan RW 013</h3>
                <p class="mb-0 text-white opacity-75 small">
                    <i data-lucide="map-pin" style="width: 14px; height: 14px;" class="me-1"></i>
                    Kelurahan Sukamiskin, Kecamatan Arcamanik, Kota Bandung 40293 &bull; Peta interaktif koordinat Pos Ronda, Posyandu, Bank Sampah, UMKM, dan Pengaduan.
                </p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-light rounded-pill px-3 fw-bold small text-primary" onclick="resetMapView()">
                    <i data-lucide="crosshair" style="width: 16px; height: 16px;"></i> Pusatkan Wilayah RW
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Kategori Layer Peta -->
    <div class="wm-card mb-3 p-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-secondary fw-bold small me-2"><i data-lucide="layers" style="width: 16px; height: 16px;"></i> Filter Layer:</span>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 filter-btn active" data-filter="all" onclick="filterMarkers('all', this)">
                Semua Titik ({{ count($markers['rt']) + count($markers['ronda']) + count($markers['posyandu']) + count($markers['bank_sampah']) + count($markers['umkm']) + count($markers['pengaduan']) + 1 }})
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 filter-btn" data-filter="rw" onclick="filterMarkers('rw', this)">
                <i data-lucide="landmark" style="width: 13px; height: 13px;"></i> Balai RW 013
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 filter-btn" data-filter="ronda" onclick="filterMarkers('ronda', this)">
                <i data-lucide="shield" style="width: 13px; height: 13px;"></i> Pos Ronda ({{ count($markers['ronda']) }})
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 filter-btn" data-filter="posyandu" onclick="filterMarkers('posyandu', this)">
                <i data-lucide="heart-pulse" style="width: 13px; height: 13px;"></i> Posyandu ({{ count($markers['posyandu']) }})
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 filter-btn" data-filter="bank_sampah" onclick="filterMarkers('bank_sampah', this)">
                <i data-lucide="recycle" style="width: 13px; height: 13px;"></i> Bank Sampah ({{ count($markers['bank_sampah']) }})
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 filter-btn" data-filter="umkm" onclick="filterMarkers('umkm', this)">
                <i data-lucide="shopping-bag" style="width: 13px; height: 13px;"></i> UMKM Warga ({{ count($markers['umkm']) }})
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 filter-btn" data-filter="pengaduan" onclick="filterMarkers('pengaduan', this)">
                <i data-lucide="alert-circle" style="width: 13px; height: 13px;"></i> Titik Pengaduan ({{ count($markers['pengaduan']) }})
            </button>
        </div>
    </div>

    <!-- Canvas Peta Interaktif Leaflet -->
    <div class="row g-4 mb-4">
        <div class="col-lg-9">
            <div class="wm-card p-2 shadow-sm border position-relative">
                <div id="mapRw"></div>
            </div>
        </div>

        <!-- Sidebar Ringkasan Wilayah Sukamiskin -->
        <div class="col-lg-3">
            <div class="wm-card h-100 p-3">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i data-lucide="compass" class="text-primary me-1" style="width: 18px; height: 18px;"></i>
                    Identitas Wilayah RW
                </h6>
                <div class="small mb-3">
                    <strong class="d-block text-secondary text-uppercase" style="font-size: 0.7rem;">Wilayah Administrasi:</strong>
                    <span class="text-dark fw-bold d-block">RW 013 (RT 001 - RT 008)</span>
                    <span class="text-muted d-block">Kel. Sukamiskin, Kec. Arcamanik</span>
                    <span class="text-muted d-block">Kota Bandung, Jawa Barat 40293</span>
                </div>

                <div class="p-2 rounded-3 bg-light border small mb-3">
                    <span class="text-dark fw-semibold d-block mb-1"><i data-lucide="map" class="text-primary me-1" style="width: 14px; height: 14px;"></i> Titik Koordinat Pusat:</span>
                    <code class="text-primary fw-bold">-6.915000, 107.679000</code>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-2 mt-4" style="font-size: 0.85rem;">Legenda Marker</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-2 bg-primary"><i data-lucide="landmark" style="width: 12px; height: 12px;"></i></span>
                        <span>Balai RW 013</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-2 bg-danger"><i data-lucide="shield" style="width: 12px; height: 12px;"></i></span>
                        <span>Pos Ronda & Keamanan</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-2 bg-info text-white"><i data-lucide="heart-pulse" style="width: 12px; height: 12px;"></i></span>
                        <span>Posyandu & Kesehatan</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-2 bg-success"><i data-lucide="recycle" style="width: 12px; height: 12px;"></i></span>
                        <span>Unit Bank Sampah</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-2 bg-warning text-dark"><i data-lucide="shopping-bag" style="width: 12px; height: 12px;"></i></span>
                        <span>Sentra UMKM Warga</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-2 bg-secondary text-white"><i data-lucide="alert-triangle" style="width: 12px; height: 12px;"></i></span>
                        <span>Laporan Pengaduan</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    const centerBandung = [-6.916000, 107.679000];
    const map = L.map('mapRw', {
        center: centerBandung,
        zoom: 16,
        scrollWheelZoom: true
    });

    // Tile Layer OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | Sukamiskin Arcamanik Bandung'
    }).addTo(map);

    // Marker Groups
    const markerLayers = {
        rw: L.layerGroup().addTo(map),
        ronda: L.layerGroup().addTo(map),
        posyandu: L.layerGroup().addTo(map),
        bank_sampah: L.layerGroup().addTo(map),
        umkm: L.layerGroup().addTo(map),
        pengaduan: L.layerGroup().addTo(map),
    };

    const markersData = @json($markers);

    // Helper membuat ikon pin SVG berwarna
    function createCustomPin(color, iconHtml) {
        return L.divIcon({
            className: 'custom-leaflet-icon',
            html: `<div style="background-color: ${color}; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; border: 2.5px solid #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.3); font-size: 14px;">${iconHtml}</div>`,
            iconSize: [34, 34],
            iconAnchor: [17, 17],
            popupAnchor: [0, -17]
        });
    }

    // 1. Marker Balai RW
    if (markersData.rw) {
        const rw = markersData.rw;
        const icon = createCustomPin('#123766', '<i class="fas fa-landmark"></i>');
        const popup = `
            <div style="min-width: 220px;">
                <span class="badge bg-primary text-white mb-1">Pusat Pemerintahan RW</span>
                <h6 class="fw-bold mb-1 text-dark">${rw.nama}</h6>
                <p class="small text-muted mb-2">${rw.alamat}</p>
                <p class="small text-secondary mb-0">${rw.keterangan || ''}</p>
            </div>
        `;
        L.marker([rw.lat, rw.lng], { icon }).bindPopup(popup).addTo(markerLayers.rw);
    }

    // 2. Marker Ronda & Keamanan
    if (markersData.ronda) {
        markersData.ronda.forEach(r => {
            const icon = createCustomPin('#dc3545', '<i class="fas fa-shield-alt"></i>');
            const popup = `
                <div style="min-width: 230px;">
                    <span class="badge bg-danger text-white mb-1">Pos Ronda & Siskamling</span>
                    <h6 class="fw-bold mb-1 text-dark">${r.nama}</h6>
                    <p class="small text-muted mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i>${r.alamat}</p>
                    <div class="p-2 rounded bg-light border small mt-2">
                        <strong>Koordinator:</strong> ${r.koordinator}<br>
                        <strong>Jadwal Tugas:</strong> ${r.jam}<br>
                        <span class="badge bg-success-subtle text-success border mt-1">Status: ${r.status}</span>
                    </div>
                </div>
            `;
            L.marker([r.lat, r.lng], { icon }).bindPopup(popup).addTo(markerLayers.ronda);
        });
    }

    // 3. Marker Posyandu
    if (markersData.posyandu) {
        markersData.posyandu.forEach(p => {
            const icon = createCustomPin('#0ea5e9', '<i class="fas fa-heartbeat"></i>');
            const popup = `
                <div style="min-width: 230px;">
                    <span class="badge bg-info text-white mb-1">Posyandu & Kesehatan</span>
                    <h6 class="fw-bold mb-1 text-dark">${p.nama}</h6>
                    <p class="small text-muted mb-1"><i class="fas fa-map-marker-alt text-info me-1"></i>${p.alamat}</p>
                    <div class="p-2 rounded bg-light border small mt-2">
                        <strong>Kader Utama:</strong> ${p.kader}<br>
                        <strong>Jadwal:</strong> ${p.jadwal}<br>
                        <small class="text-secondary d-block mt-1">${p.layanan}</small>
                    </div>
                </div>
            `;
            L.marker([p.lat, p.lng], { icon }).bindPopup(popup).addTo(markerLayers.posyandu);
        });
    }

    // 4. Marker Bank Sampah
    if (markersData.bank_sampah) {
        markersData.bank_sampah.forEach(b => {
            const icon = createCustomPin('#16865D', '<i class="fas fa-recycle"></i>');
            const popup = `
                <div style="min-width: 230px;">
                    <span class="badge bg-success text-white mb-1">Bank Sampah Lingkungan</span>
                    <h6 class="fw-bold mb-1 text-dark">${b.nama}</h6>
                    <p class="small text-muted mb-1"><i class="fas fa-map-marker-alt text-success me-1"></i>${b.alamat}</p>
                    <div class="p-2 rounded bg-light border small mt-2">
                        <strong>Petugas:</strong> ${b.petugas}<br>
                        <strong>Total Koleksi:</strong> ${b.total_kg}<br>
                        <strong>Jadwal Setor:</strong> ${b.jadwal}
                    </div>
                </div>
            `;
            L.marker([b.lat, b.lng], { icon }).bindPopup(popup).addTo(markerLayers.bank_sampah);
        });
    }

    // 5. Marker UMKM Warga
    if (markersData.umkm) {
        markersData.umkm.forEach(u => {
            const icon = createCustomPin('#d97706', '<i class="fas fa-store"></i>');
            const popup = `
                <div style="min-width: 240px;">
                    <span class="badge bg-warning text-dark mb-1">UMKM Khas Bandung</span>
                    <h6 class="fw-bold mb-1 text-dark">${u.nama}</h6>
                    <p class="small text-muted mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i>${u.alamat}</p>
                    <div class="p-2 rounded bg-light border small mt-2">
                        <strong>Pemilik:</strong> ${u.pemilik}<br>
                        <strong>Kategori:</strong> ${u.jenis}<br>
                        <strong>Jam Operasional:</strong> ${u.jam}
                    </div>
                </div>
            `;
            L.marker([u.lat, u.lng], { icon }).bindPopup(popup).addTo(markerLayers.umkm);
        });
    }

    // 6. Marker Pengaduan
    if (markersData.pengaduan) {
        markersData.pengaduan.forEach(ad => {
            const icon = createCustomPin('#64748b', '<i class="fas fa-exclamation-circle"></i>');
            const popup = `
                <div style="min-width: 230px;">
                    <span class="badge bg-secondary text-white mb-1">Aspirasi & Pengaduan</span>
                    <h6 class="fw-bold mb-1 text-dark">${ad.nama}</h6>
                    <p class="small text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i>${ad.alamat}</p>
                    <p class="small text-secondary mb-1">"${ad.deskripsi}"</p>
                    <small class="text-muted">Pelapor: <strong>${ad.pelapor}</strong></small>
                </div>
            `;
            L.marker([ad.lat, ad.lng], { icon }).bindPopup(popup).addTo(markerLayers.pengaduan);
        });
    }

    // Filter Layers
    function filterMarkers(category, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        if (category === 'all') {
            Object.values(markerLayers).forEach(layer => map.addLayer(layer));
        } else {
            Object.entries(markerLayers).forEach(([cat, layer]) => {
                if (cat === category) {
                    map.addLayer(layer);
                } else {
                    map.removeLayer(layer);
                }
            });
        }
    }

    function resetMapView() {
        map.setView(centerBandung, 16);
    }
</script>
@endsection
