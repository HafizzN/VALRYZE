<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('email', 'hrd@smarthr.com')->first();
Auth::login($user);

$errors = new \Illuminate\Support\ViewErrorBag();
$html = view('profile.edit', ['user' => $user])->with('errors', $errors)->render();

// Print the lines around profile-preview-initials
$lines = explode("\n", $html);
foreach ($lines as $i => $line) {
    if (strpos($line, 'profile-preview-initials') !== false || strpos($line, 'profile-preview-img') !== false) {
        for ($j = max(0, $i - 2); $j <= min(count($lines) - 1, $i + 5); $j++) {
            echo "Line " . ($j + 1) . ": " . trim($lines[$j]) . "\n";
        }
        break;
    }
}

echo "\n--- TOPBAR AVATAR SECTION ---\n";
// Find the topbar avatar button
foreach ($lines as $i => $line) {
    if (strpos($line, 'class="avatar"') !== false) {
        for ($j = max(0, $i - 2); $j <= min(count($lines) - 1, $i + 5); $j++) {
            echo "Line " . ($j + 1) . ": " . trim($lines[$j]) . "\n";
        }
    }
}
