<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$tables = \DB::connection()->getSchemaBuilder()->getTableListing();
echo "Tables in database:\n";
foreach ($tables as $table) {
    echo $table . "\n";
}

echo "\nUMKM count: " . \DB::table('umkm')->count() . "\n";
echo "KasRt count: " . \DB::table('kas_rt')->count() . "\n";
