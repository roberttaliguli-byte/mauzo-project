<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Clean old login histories daily (keep last 90 days)
Schedule::command('clean:login-histories --days=90')->daily();

// Send package expiry email notifications
Schedule::command('emails:package-expiry')  // This matches your command signature
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/package-expiry-emails.log'));

// Also check every 6 hours for urgent expiries (<=5 days)
Schedule::command('emails:package-expiry')
    ->everySixHours()
    ->between('6:00', '22:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/package-expiry-emails.log'));