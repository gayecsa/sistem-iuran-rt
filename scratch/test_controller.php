<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'admin')->first() ?: App\Models\User::where('role', 'rw')->first();
Illuminate\Support\Facades\Auth::login($user);
view()->share('errors', new \Illuminate\Support\ViewErrorBag);
echo "Testing as user: {$user->name} ({$user->role})\n";

// 1. Test DashboardController@monitoringRt for all 5 tabs
$dashCtrl = app(App\Http\Controllers\DashboardController::class);
foreach (['overview', 'laporan', 'keuangan', 'evaluasi', 'riwayat'] as $tab) {
    $req = new Illuminate\Http\Request(['tab' => $tab, 'periode' => 'November 2026']);
    $res = $dashCtrl->monitoringRt($req);
    echo "1. DashboardController@monitoringRt [tab={$tab}] returned view: " . $res->name() . "\n";
    $html = $res->render();
    echo "   Rendered HTML length: " . strlen($html) . " bytes\n";
}

// 2. Test DashboardController@laporanRt redirect
$resLap = $dashCtrl->laporanRt();
echo "2. DashboardController@laporanRt returned redirect to: " . $resLap->getTargetUrl() . "\n";

// 3. Test AuditController@index
$auditCtrl = app(App\Http\Controllers\AuditController::class);
$reqAudit = new Illuminate\Http\Request(['tab' => 'overview', 'tahun' => 2026]);
$resAudit = $auditCtrl->index($reqAudit);
echo "3. AuditController@index returned view: " . $resAudit->name() . "\n";
$htmlAudit = $resAudit->render();
echo "   Rendered HTML length: " . strlen($htmlAudit) . " bytes\n";

echo "\nSUCCESS! All unified monitoring tabs, redirects, and separate audit views rendered perfectly!\n";
