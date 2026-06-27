<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

foreach (User::all() as $user) {
    echo "ID: {$user->id}, Name: '{$user->name}', Email: '{$user->email}', Photo: '{$user->photo}', Initials: '{$user->initials}'\n";
}
