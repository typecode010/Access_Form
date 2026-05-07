<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    $conn = Illuminate\Support\Facades\DB::connection('mysql');
    $tables = $conn->select('SHOW TABLES');
    echo "MYSQL TABLES:\n";
    foreach ($tables as $t) {
        foreach ((array) $t as $val) {
            echo $val . PHP_EOL;
        }
    }
    echo "\nCOUNT responses:\n";
    $c = $conn->select('select count(*) as c from responses');
    var_export($c);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString();
}
