<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KasRt;
use Illuminate\Support\Facades\DB;

class CleanupKasRtDuplicates extends Command
{
    protected $signature = 'cleanup:kas-duplicates';
    protected $description = 'Cleanup duplicate KasRt entries to prevent inflated balance';

    public function handle()
    {
        $this->info('Checking for duplicate KasRt entries...');
        
        // Cek duplikasi berdasarkan nama_warga, kategori, keterangan, tanggal_transaksi
        $duplicates = KasRt::selectRaw('nama_warga, kategori, keterangan, tanggal_transaksi, COUNT(*) as count')
            ->groupBy('nama_warga', 'kategori', 'keterangan', 'tanggal_transaksi')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicates found!');
            return;
        }

        $this->warn('⚠️ Found ' . $duplicates->count() . ' duplicate groups:');
        
        $totalDeleted = 0;
        foreach ($duplicates as $dup) {
            // Ambil semua records yang duplikat
            $records = KasRt::where('nama_warga', $dup->nama_warga)
                ->where('kategori', $dup->kategori)
                ->where('keterangan', $dup->keterangan)
                ->where('tanggal_transaksi', $dup->tanggal_transaksi)
                ->orderBy('created_at', 'asc')
                ->get();

            // Hapus semua kecuali yang paling baru
            $keep = $records->last();
            $toDelete = $records->slice(0, -1);
            
            $deleteCount = $toDelete->count();
            KasRt::whereIn('id', $toDelete->pluck('id'))->delete();
            
            $totalDeleted += $deleteCount;
            $this->line("  • {$dup->keterangan}: Deleted {$deleteCount} duplicate(s)");
        }

        $this->info("✅ Cleanup complete! Deleted {$totalDeleted} duplicate entries.");
        
        // Show current balance
        $kasData = KasRt::selectRaw('COALESCE(SUM(pemasukan), 0) as total_pemasukan, COALESCE(SUM(pengeluaran), 0) as total_pengeluaran')
            ->first();
        
        $saldo = ($kasData->total_pemasukan ?? 0) - ($kasData->total_pengeluaran ?? 0);
        $this->info("\n📊 Current Balance:");
        $this->line("  Pemasukan: Rp " . number_format($kasData->total_pemasukan ?? 0, 0, ',', '.'));
        $this->line("  Pengeluaran: Rp " . number_format($kasData->total_pengeluaran ?? 0, 0, ',', '.'));
        $this->line("  Saldo: Rp " . number_format($saldo, 0, ',', '.'));
    }
}
