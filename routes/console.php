<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Schedule::command('brandpilot:poll-inbox')->everyFiveMinutes();
Schedule::command('brandpilot:poll-tiktok')->everyThirtyMinutes();
Schedule::command('brandpilot:publish-scheduled')->everyMinute();
