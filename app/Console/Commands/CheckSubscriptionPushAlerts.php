<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Notifications\SubscriptionExpiryPushNotification;
use Illuminate\Console\Command;

class CheckSubscriptionPushAlerts extends Command
{
    protected $signature = 'brandpilot:check-subscription-push';
    protected $description = 'Periksa masa aktif tenant dan tembakkan Push Notification untuk 7, 3, 1 hari, dan expired';

    public function handle()
    {
        $this->info("Memeriksa masa aktif seluruh tenant untuk Push Notification...");

        $teams = Team::whereNotNull('subscription_expires_at')->get();

        foreach ($teams as $team) {
            $now = now()->startOfDay();
            $expiresAt = $team->subscription_expires_at->startOfDay();
            $daysLeft = (int) $now->diffInDays($expiresAt, false);

            $shouldNotify = false;
            $isExpired = false;

            // Trigger tepat di H-7, H-3, H-1, dan H-0 (Habis)
            if ($daysLeft === 7 || $daysLeft === 3 || $daysLeft === 1) {
                $shouldNotify = true;
            } elseif ($daysLeft <= 0 && $team->subscription_status === 'expired') {
                $shouldNotify = true;
                $isExpired = true;
            }

            if ($shouldNotify) {
                $this->info("Mengirim push alert ({$daysLeft} hari) ke Tenant: {$team->name}");
                foreach ($team->members as $member) {
                    $member->notify(new SubscriptionExpiryPushNotification($team, $daysLeft, $isExpired));
                }
            }
        }

        $this->info("Pemeriksaan selesai.");
    }
}