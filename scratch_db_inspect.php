<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Shift;
use App\Models\Attendance;
use App\Models\User;

echo "SHIFTS:\n";
foreach (Shift::all() as $s) {
    echo "ID: {$s->id}, Name: {$s->name}, Start: {$s->start_time}, End: {$s->end_time}, Active: {$s->is_active}\n";
}

echo "\nUSERS:\n";
foreach (User::all() as $u) {
    $roles = $u->getRoleNames()->implode(', ');
    echo "ID: {$u->id}, Name: {$u->name}, Roles: {$roles}, Birthdate: {$u->birth_date?->format('Y-m-d')}\n";
}

echo "\nATTENDANCE TODAY:\n";
foreach (Attendance::whereDate('date', \Carbon\Carbon::today('Asia/Jakarta'))->get() as $a) {
    echo "User: {$a->user_id}, Date: {$a->date}, Shift: {$a->shift_id}, Status: {$a->status}, CheckIn: {$a->check_in_time}, CheckOut: {$a->check_out_time}\n";
}
