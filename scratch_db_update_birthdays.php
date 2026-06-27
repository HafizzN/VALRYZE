<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// 1. Update Siti Rahayu (ID 2) -> Birthday today!
$siti = User::find(2);
if ($siti) {
    $siti->update(['birth_date' => '1995-06-27']);
    echo "Updated Siti Rahayu (ID 2) birthday to 1995-06-27\n";
}

// 2. Update Budi Santoso manager (ID 3) -> Birthday not today!
$budiManager = User::find(3);
if ($budiManager) {
    $budiManager->update(['birth_date' => '1990-12-15']);
    echo "Updated Budi Santoso Manager (ID 3) birthday to 1990-12-15\n";
}

// 3. Update Budi Santoso karyawan (ID 6) -> Birthday not today!
$budiKaryawan = User::find(6);
if ($budiKaryawan) {
    $budiKaryawan->update(['birth_date' => '1996-10-10']);
    echo "Updated Budi Santoso Karyawan (ID 6) birthday to 1996-10-10\n";
}

// Clear today's birthday notifications so they regenerate for Siti Rahayu
\App\Models\Notification::where('title', '🎉 Hari Spesial Karyawan!')->delete();
\App\Models\Notification::where('title', '⏰ Pengingat Ulang Tahun Besok')->delete();
echo "Cleared old birthday notifications to force recalculation.\n";

// Reset the seed cache so it runs once with the new data
\Illuminate\Support\Facades\Cache::forget('birthday_seed_done_' . now()->format('Y-m-d'));
echo "Cleared cache birthday_seed_done\n";
