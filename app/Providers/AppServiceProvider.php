<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Umkm;
use App\Policies\UmkmPolicy;
use App\Models\BankSampahTransaksi;
use App\Models\BankSampah;
use App\Policies\BankSampahPolicy;

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
    }
}
