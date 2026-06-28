<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule monthly leave accrual on the 1st of every month
Schedule::command('leave:accrue')->monthlyOn(1, '00:00');

// Schedule daily birthday notifications check at 08:00 AM (Asia/Jakarta timezone)
Schedule::command('birthday:notify')->dailyAt('08:00')->timezone('Asia/Jakarta');

// Schedule queue heartbeat job to run every minute
Schedule::job(new \App\Jobs\QueueHeartbeatJob)->everyMinute();
