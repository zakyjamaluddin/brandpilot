<?php

namespace App\Notifications;

use App\Models\ContentPost;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ContentPublishedPushNotification extends Notification
{
    use Queueable;

    public function __construct(public ContentPost $post) {}

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $platforms = implode(', ', array_keys((array) $this->post->platform_post_ids));

        return (new WebPushMessage)
            ->title('🚀 Konten Berhasil Dipublikasikan!')
            ->body("Konten: \"{$this->post->title}\" telah berhasil tayang di {$platforms}.")
            ->data(['url' => url('/admin/' . $this->post->team->slug)]);
    }
}