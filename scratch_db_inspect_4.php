<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Shift;

echo "SHIFTS OVERNIGHT STATUS:\n";
foreach (Shift::all() as $s) {
    echo "ID: {$s->id}, Name: {$s->name}, Overnight: " . var_export($s->is_overnight, true) . "\n";
}
