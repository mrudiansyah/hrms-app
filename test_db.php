<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\DB::connection('tms')->getPdo();
    echo "Connection to 'tms' (192.168.1.4) SUCCESSFUL! Database 'db_ems_tms' is accessible.";
} catch (\Exception $e) {
    echo "Connection ERROR: " . $e->getMessage();
}
