<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'admin')->first() ?: App\Models\User::where('role', 'rw')->first();
if (!$user) {
    echo "No admin/rw user found!\n";
    exit(1);
}

function testUrl($url, $user, $kernel) {
    $request = Illuminate\Http\Request::create($url, 'GET');
    app()->instance('request', $request);
    Illuminate\Support\Facades\Auth::setUser($user);
    $response = $kernel->handle($request);
    echo "GET {$url} => Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() >= 400) {
        echo "Error output:\n" . substr(strip_tags($response->getContent()), 0, 500) . "\n";
    }
}

echo "Testing with user: {$user->name} ({$user->role})\n";

testUrl('/rw/monitoring-keuangan', $user, $kernel);
testUrl('/rw/audit', $user, $kernel);
testUrl('/rw/audit?tab=keuangan', $user, $kernel);
testUrl('/rw/audit?tab=kegiatan', $user, $kernel);
testUrl('/rw/audit?tab=temuan', $user, $kernel);
testUrl('/rw/audit?tab=evaluasi', $user, $kernel);
testUrl('/rw/audit/cetak-laporan', $user, $kernel);
testUrl('/monitoring-rt', $user, $kernel);

echo "\n--- ALL TESTS COMPLETED ---\n";
