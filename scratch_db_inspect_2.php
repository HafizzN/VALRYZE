<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::where('name', 'like', '%Budi%')
    ->orWhere('name', 'like', '%Siti%')
    ->get();

foreach ($users as $u) {
    $roles = $u->getRoleNames()->implode(', ');
    echo "ID: {$u->id}, Name: {$u->name}, Roles: {$roles}, Raw Birthdate: " . var_export($u->getRawOriginal('birth_date'), true) . "\n";
}
