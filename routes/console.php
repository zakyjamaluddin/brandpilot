<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Polling TikTok 3-Tier Fallback:
// 1. Prime time (08:00–21:59 WIB): Tiap 15 menit untuk deteksi cepat konten baru.
// 2. Off-peak malam (22:00–07:59 WIB): Tiap 60 menit untuk efisiensi server.
Schedule::command('brandpilot:poll-tiktok')->cron('*/15 8-21 * * *')->withoutOverlapping();
Schedule::command('brandpilot:poll-tiktok')->cron('0 22-23,0-7 * * *')->withoutOverlapping();

Schedule::command('brandpilot:publish-scheduled')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('brandpilot:check-subscription-push')->dailyAt('09:00');
