<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\User;
use Illuminate\Support\Facades\Hash;
try {
    $admin = User::where('email','admin@accessform.local')->first();
    echo $admin ? "Admin id={$admin->id}\n" : "Admin not found\n";
    echo 'Admin password matches env? ' . (Hash::check(env('SEED_ADMIN_PASSWORD'), $admin->password) ? 'yes' : 'no') . PHP_EOL;
    $creator = User::where('email','creator@accessform.local')->first();
    echo $creator ? "Creator id={$creator->id}\n" : "Creator not found\n";
    echo 'Creator password matches env? ' . (Hash::check(env('SEED_FORMCREATOR_PASSWORD'), $creator->password) ? 'yes' : 'no') . PHP_EOL;
    $resp = User::where('email','respondent@accessform.local')->first();
    echo $resp ? "Respondent id={$resp->id}\n" : "Respondent not found\n";
    echo 'Respondent password matches env? ' . (Hash::check(env('SEED_RESPONDENT_PASSWORD'), $resp->password) ? 'yes' : 'no') . PHP_EOL;
} catch (Throwable $e){
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
