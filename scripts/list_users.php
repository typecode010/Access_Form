<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\User;
try {
    $users = User::query()->limit(10)->get();
    if ($users->isEmpty()){
        echo "No users found\n";
        exit;
    }
    foreach ($users as $u){
        echo "ID: {$u->id} | Email: {$u->email} | Name: {$u->name} | PasswordHash: ".substr($u->password,0,20)."...\n";
        if (method_exists($u,'getRoleNames')){
            echo " Roles: ".implode(',', $u->getRoleNames()->toArray())."\n";
        }
    }
} catch (Throwable $e){
    echo "ERROR: ".$e->getMessage()."\n";
}
