<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['tb_employee_are', 'tb_employee_area', 'tb_area_master', 'tb_area_ap'];
$schemas = [];
foreach ($tables as $t) {
    try {
        $schemas[$t] = DB::connection('tms')->select("SHOW COLUMNS FROM db_ems.".$t);
    } catch (\Exception $e) {
        $schemas[$t] = $e->getMessage();
    }
}
echo json_encode($schemas, JSON_PRETTY_PRINT);
