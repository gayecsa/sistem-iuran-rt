<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PortalIotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('iot/portal')->group(function () {
    Route::post('/tap', [PortalIotController::class, 'tap'])->name('api.iot.portal.tap');
    Route::get('/live-status', [PortalIotController::class, 'liveStatus'])->name('api.iot.portal.live_status');
    Route::get('/live-status-alt', [PortalIotController::class, 'liveStatus'])->name('api.iot.portal.live-status');
});

