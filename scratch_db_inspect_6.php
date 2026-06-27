<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OfficeLocation;

echo "OFFICE LOCATIONS:\n";
foreach (OfficeLocation::all() as $o) {
    echo "ID: {$o->id}, Name: {$o->name}, Lat: {$o->latitude}, Lng: {$o->longitude}, Radius: {$o->radius_meters}, Active: " . var_export($o->is_active, true) . "\n";
}
