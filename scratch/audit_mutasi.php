<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
$dbName = config('database.connections.mysql.database');
$keyName = "Tables_in_" . $dbName;
$mutasiTables = [];
foreach ($tables as $t) {
    $tName = $t->$keyName;
    if (str_contains($tName, 'mutasi') || str_contains($tName, 'warga') || str_contains($tName, 'kependudukan')) {
        $mutasiTables[] = $tName;
    }
}
echo "Tabel Terkait: " . implode(', ', $mutasiTables) . PHP_EOL;

if (Schema::hasTable('warga_status_histories')) {
    echo "Columns of warga_status_histories:\n";
    foreach (Schema::getColumnListing('warga_status_histories') as $c) {
        echo "- $c\n";
    }
}
