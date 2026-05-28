@extends('layouts.app')

@section('content')
<div class="dashboard-shell container mt-4">
    <div class="max-w-4xl mx-auto bg-white p-4 rounded-3xl shadow-sm animate__animated animate__fadeInUp">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Detail Saldo Iuran 📊</h2>
                <p class="text-muted">Rincian pemasukan dan pengeluaran kas RT secara real-time</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>

        <!-- Ringkasan Singkat -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="p-4 rounded-4" style="background-color: #f0fdf4; border: 1px solid #dcfce7;">
                    <p class="text-muted mb-1">Total Pemasukan</p>
                    <h3 class="text-success mb-0">Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 rounded-4" style="background-color: #fef2f2; border: 1px solid #fee2e2;">
                    <p class="text-muted mb-1">Total Pengeluaran</p>
                    <h3 class="text-danger mb-0">Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- GRAFIK 1: Grafik Batang (Arus Kas Bulanan) -->
        <div class="card p-4 shadow-sm border-0 rounded-4 mb-4">
            <h5 class="mb-4">Grafik Batang Perbandingan Per Bulan</h5>
            <div style="position: relative; height:320px; width:100%">
                <canvas id="kasChart"></canvas>
            </div>
        </div>

        <!-- GRAFIK 2: GRAFIK GARIS BARU (Tren Keuangan) -->
        <div class="card p-4 shadow-sm border-0 rounded-4">
            <h5 class="mb-4">Grafik Garis Tren Keuangan (Line Chart)</h5>
            <div style="position: relative; height:320px; width:100%">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil data langsung dari controller laravel
        const labelsData = @json($chart_labels);
        const pemasukanData = @json($chart_pemasukan);
        const pengeluaranData = @json($chart_pengeluaran);

        // --- KONFIGURASI GRAFIK BATANG ---
        const ctxBar = document.getElementById('kasChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labelsData,
                datasets: [
                    {
                        label: 'Pemasukan (Rp)',
                        data: pemasukanData,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Pengeluaran (Rp)',
                        data: pengeluaranData,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        // --- KONFIGURASI GRAFIK GARIS (NEW) ---
        const ctxLine = document.getElementById('lineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line', // Berubah jadi line chart
            data: {
                labels: labelsData,
                datasets: [
                    {
                        label: 'Tren Pemasukan (Rp)',
                        data: pemasukanData,
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 3,
                        tension: 0.3, // Membuat garis jadi sedikit melengkung halus
                        fill: true // Memberikan efek bayangan di bawah garis
                    },
                    {
                        label: 'Tren Pengeluaran (Rp)',
                        data: pengeluaranData,
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    });
</script>
@endsection