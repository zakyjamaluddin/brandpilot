<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal Polling TikTok Cerdas (Dual-Tier Scraper):
// 1. Pukul 08:00 – 16:59 WIB: Tiap 15 menit (jam kerja paling aktif)
Schedule::command('brandpilot:poll-tiktok')->cron('*/15 8-16 * * *')->withoutOverlapping();

// 2. Pukul 17:00 – 20:59 WIB: Tiap 30 menit (jam sore sampai malam)
Schedule::command('brandpilot:poll-tiktok')->cron('*/30 17-20 * * *')->withoutOverlapping();

// 3. Pukul 21:00 – 07:59 WIB: Tiap 60 menit (jam istirahat malam)
Schedule::command('brandpilot:poll-tiktok')->cron('0 21-23,0-7 * * *')->withoutOverlapping();

Schedule::command('brandpilot:publish-scheduled')->everyMinute()->withoutOverlapping()->runInBackground();
Schedule::command('brandpilot:check-subscription-push')->dailyAt('09:00');
