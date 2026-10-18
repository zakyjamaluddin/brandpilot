<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class SubscriptionExpiryPushNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Team $team,
        public int $daysLeft,
        public bool $isExpired = false
    ) {}

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        if ($this->isExpired) {
            $title = "🚨 Masa Langganan Habis!";
            $body = "Masa aktif BrandPilot AI Anda telah berakhir. Sistem autopilot dinonaktifkan sementara.";
        } elseif ($this->daysLeft === 1) {
            $title = "⚠️ Besok Hari Terakhir!";
            $body = "Masa aktif BrandPilot Anda berakhir besok. Perpanjang agar jadwal posting tidak berhenti.";
        } else {
            $title = "⚠️ Pengingat Langganan ({$this->daysLeft} Hari Lagi)";
            $body = "Masa aktif BrandPilot Anda tersisa {$this->daysLeft} hari lagi. Perpanjang sekarang via Lynk.id.";
        }

        return (new WebPushMessage)
            ->title($title)
            ->body($body)
            ->data(['url' => route('filament.admin.pages.manage-subscription', ['tenant' => $this->team->slug])]);
    }
}