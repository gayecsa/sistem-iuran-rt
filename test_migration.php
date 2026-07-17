<?php
file_put_contents(__DIR__ . '/migration_test.log', "Starting migration test at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

try {
    require 'vendor/autoload.php';
    file_put_contents(__DIR__ . '/migration_test.log', "Autoload successful\n", FILE_APPEND);
    
    $app = require_once 'bootstrap/app.php';
    file_put_contents(__DIR__ . '/migration_test.log', "App bootstrapped\n", FILE_APPEND);
    
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    file_put_contents(__DIR__ . '/migration_test.log', "Kernel created\n", FILE_APPEND);
    
    $kernel->bootstrap();
    file_put_contents(__DIR__ . '/migration_test.log', "Kernel bootstrapped\n", FILE_APPEND);
    
    $tables = \DB::connection()->getSchemaBuilder()->getTableListing();
    file_put_contents(__DIR__ . '/migration_test.log', "Tables: " . implode(', ', $tables) . "\n", FILE_APPEND);
    
    $umkmCount = \DB::table('umkm')->count();
    $kasRtCount = \DB::table('kas_rt')->count();
    
    file_put_contents(__DIR__ . '/migration_test.log', "UMKM count: {$umkmCount}, KasRt count: {$kasRtCount}\n", FILE_APPEND);
    
} catch (\Exception $e) {
    file_put_contents(__DIR__ . '/migration_test.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
}

echo "Test completed. Check migration_test.log for details.\n";
