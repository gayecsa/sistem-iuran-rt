<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KasRt;
use Illuminate\Support\Facades\DB;

class KeuanganController extends Controller
{
    public function detail()
    {
        // 1. Hitung Total Pemasukan & Pengeluaran Nyata dari Database
        $total_pemasukan = KasRt::sum('pemasukan') ?? 0;
        $total_pengeluaran = KasRt::sum('pengeluaran') ?? 0;

        // 2. Ambil data bulanan untuk grafik berdasarkan kolom tanggal_transaksi di database kamu
        $pemasukan_per_bulan = KasRt::whereYear('tanggal_transaksi', date('Y'))
            ->select(DB::raw('MONTH(tanggal_transaksi) as bulan'), DB::raw('SUM(pemasukan) as total'))
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $pengeluaran_per_bulan = KasRt::whereYear('tanggal_transaksi', date('Y'))
            ->select(DB::raw('MONTH(tanggal_transaksi) as bulan'), DB::raw('SUM(pengeluaran) as total'))
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // Menyusun 12 Bulan agar grafik urut dari Januari - Desember
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chart_pemasukan = [];
        $chart_pengeluaran = [];

        for ($i = 1; $i <= 12; $i++) {
            $chart_pemasukan[] = $pemasukan_per_bulan[$i] ?? 0;
            $chart_pengeluaran[] = $pengeluaran_per_bulan[$i] ?? 0;
        }

        // 3. Lempar semua data asli ke View Blade
        $data = [
            'total_pemasukan' => $total_pemasukan,
            'total_pengeluaran' => $total_pengeluaran,
            'chart_labels' => $months,
            'chart_pemasukan' => $chart_pemasukan,
            'chart_pengeluaran' => $chart_pengeluaran,
        ];

        return view('keuangan.detail', $data);
    }
}