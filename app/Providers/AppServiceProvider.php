<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\Umkm;
use App\Policies\UmkmPolicy;
use App\Models\BankSampahTransaksi;
use App\Models\BankSampah;
use App\Policies\BankSampahPolicy;
use App\Models\Pengaduan;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Umkm::class, UmkmPolicy::class);
        Gate::policy(BankSampahTransaksi::class, BankSampahPolicy::class);
        Gate::policy(BankSampah::class, BankSampahPolicy::class);

        // View Composer untuk indikator status keamanan RW 013 di topbar global (fail-safe)
        View::composer('layouts.app', function ($view) {
            try {
                $activeDaruratCount = Pengaduan::where('kategori', 'Keamanan & Ketertiban')
                    ->whereNotIn('status', ['Selesai', 'Ditolak'])
                    ->where('deskripsi', 'LIKE', '%[Tingkat Urgensi: Darurat]%')
                    ->count();

                $activeSegeraCount = Pengaduan::where('kategori', 'Keamanan & Ketertiban')
                    ->whereNotIn('status', ['Selesai', 'Ditolak'])
                    ->where('deskripsi', 'LIKE', '%[Tingkat Urgensi: Segera]%')
                    ->count();

                $level = 'aman';
                if ($activeDaruratCount > 0) {
                    $level = 'darurat';
                } elseif ($activeSegeraCount > 0) {
                    $level = 'segera';
                }

                $view->with('securityStatus', [
                    'darurat_count' => $activeDaruratCount,
                    'segera_count' => $activeSegeraCount,
                    'total_urgent' => $activeDaruratCount + $activeSegeraCount,
                    'level' => $level,
                ]);
            } catch (\Throwable $e) {
                $view->with('securityStatus', [
                    'darurat_count' => 0,
                    'segera_count' => 0,
                    'total_urgent' => 0,
                    'level' => 'aman',
                ]);
            }
        });
    }
}
