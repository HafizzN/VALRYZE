<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$usersWithToken = User::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->get();
if ($usersWithToken->isEmpty()) {
    echo "No users have an FCM token registered yet.\n";
} else {
    foreach ($usersWithToken as $user) {
        echo "ID: {$user->id}, Name: '{$user->name}', Email: '{$user->email}', FCM Token: '" . substr($user->fcm_token, 0, 15) . "...'\n";
    }
}
