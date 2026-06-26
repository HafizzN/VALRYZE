<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule monthly leave accrual on the 1st of every month
Schedule::command('leave:accrue')->monthlyOn(1, '00:00');

// Schedule queue heartbeat job to run every minute
Schedule::job(new \App\Jobs\QueueHeartbeatJob)->everyMinute();
