<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Shared-hosting queue pattern: a single Hostinger cron entry runs `schedule:run`
// every minute; this drains the database queue without a long-lived worker.
Schedule::command('queue:work --stop-when-empty --max-time=50')
    ->everyMinute()
    ->withoutOverlapping();

// Nightly DB + media backup (spatie/laravel-backup), cleaned per retention policy.
Schedule::command('backup:clean')->dailyAt('01:30');
Schedule::command('backup:run')->dailyAt('02:00');
