<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

foreach (User::whereIn('id', [2, 3])->get() as $u) {
    echo "ID: {$u->id}, Name: {$u->name}, Shift ID: " . var_export($u->shift_id, true) . "\n";
}
