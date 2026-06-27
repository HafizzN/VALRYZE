<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Shift;

echo "SHIFTS:\n";
foreach (Shift::all() as $s) {
    echo "ID: {$s->id}, Name: {$s->name}, Start: {$s->start_time}, End: {$s->end_time}, Active: {$s->is_active}, Tolerance: {$s->late_tolerance_minutes}\n";
}
