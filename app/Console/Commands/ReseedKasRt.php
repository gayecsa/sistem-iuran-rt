<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KasRt;
use App\Models\User;
use Carbon\Carbon;

class ReseedKasRt extends Command
{
    protected $signature = 'reseed:kas-rt {--force : Skip confirmation}';
    protected $description = 'Reset KasRt table dan re-seed dengan data yang clean';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('⚠️  Ini akan MENGHAPUS semua data KasRt dan re-seed ulang. Lanjutkan?')) {
            $this->info('Dibatalkan.');
            return;
        }

        $this->info('Clearing KasRt table...');
        KasRt::truncate();

        $this->info('Re-seeding data...');

        // Ambil semua warga
        $warga = User::where('role', 'warga')->get();
        $this->info("Found {$warga->count()} warga");

        $list_bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei'
        ];

        // Seed pemasukan
        $bar = $this->output->createProgressBar($warga->count() * 5);
        $bar->start();

        foreach ($warga as $w) {
            for ($bulan = 1; $bulan <= 5; $bulan++) {
                // Iuran
                KasRt::create([
                    'nama_warga' => $w->name,
                    'no_hp' => $w->phone,
                    'pemasukan' => 100000,
                    'pengeluaran' => 0,
                    'kategori' => 'Iuran',
                    'keterangan' => 'Iuran Kas Bulan ' . $list_bulan[$bulan],
                    'tanggal_transaksi' => Carbon::create(2026, $bulan, rand(1, 25)),
                    'dibuat_oleh' => 'Bendahara RT 001',
                ]);

                // Sampah
                KasRt::create([
                    'nama_warga' => $w->name,
                    'no_hp' => $w->phone,
                    'pemasukan' => 50000,
                    'pengeluaran' => 0,
                    'kategori' => 'Sampah',
                    'keterangan' => 'Iuran Sampah Bulan ' . $list_bulan[$bulan],
                    'tanggal_transaksi' => Carbon::create(2026, $bulan, rand(1, 25)),
                    'dibuat_oleh' => 'Bendahara RT 001',
                ]);

                $bar->advance();
            }
        }
        $bar->finish();
        $this->newLine();

        // Seed pengeluaran
        $wargaNames = $warga->pluck('name')->toArray();
        shuffle($wargaNames);

        $kegiatan = [
            ['bulan' => 1, 'kategori' => 'Jenguk Sakit', 'jumlah' => 150000, 'ket' => 'Jenguk ibu @NAMA yang sakit di rumah sakit', 'hari' => 5],
            ['bulan' => 1, 'kategori' => 'Kebersihan', 'jumlah' => 300000, 'ket' => 'Membeli alat kebersihan & trash bag bulan Januari', 'hari' => 10],
            ['bulan' => 1, 'kategori' => 'Operasional', 'jumlah' => 100000, 'ket' => 'Konsumsi rapat RT bulan Januari', 'hari' => 15],
            
            ['bulan' => 2, 'kategori' => 'Khitanan', 'jumlah' => 200000, 'ket' => 'Khitanan anak Bapak @NAMA', 'hari' => 8],
            ['bulan' => 2, 'kategori' => 'Jenguk Sakit', 'jumlah' => 120000, 'ket' => 'Jenguk Pak @NAMA yang sedang sakit', 'hari' => 14],
            ['bulan' => 2, 'kategori' => 'Kebersihan', 'jumlah' => 250000, 'ket' => 'Upah petugas kebersihan minggu 1-4 Februari', 'hari' => 20],
            
            ['bulan' => 3, 'kategori' => 'Syukuran', 'jumlah' => 180000, 'ket' => 'Syukuran pernikahan anak Ibu @NAMA', 'hari' => 5],
            ['bulan' => 3, 'kategori' => 'Infrastruktur', 'jumlah' => 400000, 'ket' => 'Perbaikan jalan utama RT', 'hari' => 12],
            ['bulan' => 3, 'kategori' => 'Arisan', 'jumlah' => 80000, 'ket' => 'Dana arisan Bapak @NAMA yang menang', 'hari' => 18],
            ['bulan' => 3, 'kategori' => 'Kebersihan', 'jumlah' => 250000, 'ket' => 'Pengangkutan sampah ke TPA', 'hari' => 25],
            
            ['bulan' => 4, 'kategori' => 'Sosial', 'jumlah' => 200000, 'ket' => 'Bantuan sosial untuk keluarga Pak @NAMA yang sedang susah', 'hari' => 3],
            ['bulan' => 4, 'kategori' => 'Khitanan', 'jumlah' => 220000, 'ket' => 'Khitanan anak Ibu @NAMA', 'hari' => 10],
            ['bulan' => 4, 'kategori' => 'Operasional', 'jumlah' => 150000, 'ket' => 'Perbaikan pompa air warga & material', 'hari' => 16],
            ['bulan' => 4, 'kategori' => 'Kebersihan', 'jumlah' => 250000, 'ket' => 'Upah petugas kebersihan dan pembersihan saluran', 'hari' => 22],
            
            ['bulan' => 5, 'kategori' => 'Lingkungan', 'jumlah' => 300000, 'ket' => 'Kegiatan penghijauan RT dan pemeliharaan taman', 'hari' => 5],
            ['bulan' => 5, 'kategori' => 'Jenguk Sakit', 'jumlah' => 100000, 'ket' => 'Jenguk Mbak @NAMA yang melahirkan di klinik', 'hari' => 12],
            ['bulan' => 5, 'kategori' => 'Operasional', 'jumlah' => 80000, 'ket' => 'Fotokopi berkas & konsumsi rapat evaluasi RT', 'hari' => 18],
            ['bulan' => 5, 'kategori' => 'Utilitas', 'jumlah' => 200000, 'ket' => 'Tagihan listrik & air Balai RT Mei', 'hari' => 28],
        ];

        $nameIndex = 0;
        foreach ($kegiatan as $peng) {
            $keterangan = $peng['ket'];
            
            if (strpos($keterangan, '@NAMA') !== false) {
                if ($nameIndex >= count($wargaNames)) {
                    $nameIndex = 0;
                }
                $keterangan = str_replace('@NAMA', $wargaNames[$nameIndex], $keterangan);
                $nameIndex++;
            }
            
            KasRt::create([
                'nama_warga' => 'Kas RT Umum',
                'no_hp' => '-',
                'pemasukan' => 0,
                'pengeluaran' => $peng['jumlah'],
                'kategori' => $peng['kategori'],
                'keterangan' => $keterangan,
                'tanggal_transaksi' => Carbon::create(2026, $peng['bulan'], $peng['hari']),
                'dibuat_oleh' => 'Bendahara RT 001',
            ]);
        }

        $this->newLine();
        $kasData = KasRt::selectRaw('COALESCE(SUM(pemasukan), 0) as total_pemasukan, COALESCE(SUM(pengeluaran), 0) as total_pengeluaran')
            ->first();
        
        $saldo = ($kasData->total_pemasukan ?? 0) - ($kasData->total_pengeluaran ?? 0);
        
        $this->info("✅ Re-seed complete!");
        $this->line("📊 Final Balance:");
        $this->line("  Pemasukan: Rp " . number_format($kasData->total_pemasukan ?? 0, 0, ',', '.'));
        $this->line("  Pengeluaran: Rp " . number_format($kasData->total_pengeluaran ?? 0, 0, ',', '.'));
        $this->line("  Saldo: Rp " . number_format($saldo, 0, ',', '.'));
    }
}
