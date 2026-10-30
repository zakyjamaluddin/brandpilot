<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Schedule::command('brandpilot:poll-inbox')->everyFiveMinutes();
// Polling TikTok — jendela hemat kuota Apify: 30 run/hari (WIB, app timezone Asia/Jakarta).
// Siang 08:00–19:30 tiap 30 menit = 24 run. Malam 20:00, 22:00, 00:00, 02:00, 04:00, 06:00 = 6 run.
Schedule::command('brandpilot:poll-tiktok')->cron('*/30 8-19 * * *')->withoutOverlapping();
Schedule::command('brandpilot:poll-tiktok')->cron('0 0,2,4,6,20,22 * * *')->withoutOverlapping();
Schedule::command('brandpilot:publish-scheduled')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('brandpilot:check-subscription-push')->dailyAt('09:00');