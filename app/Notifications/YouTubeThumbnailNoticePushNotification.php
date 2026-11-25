<?php

namespace App\Notifications;

use App\Models\ContentPost;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class YouTubeThumbnailNoticePushNotification extends Notification
{
    use Queueable;

    public function __construct(public ContentPost $post) {}

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('📹 Video YouTube Berhasil Tayang')
            ->body("Video \"{$this->post->title}\" berhasil terupload ke YouTube, namun pemasangan gambar thumbnail belum berhasil. Silakan verifikasi nomor HP pada channel YouTube Anda untuk mengaktifkan fitur thumbnail custom.")
            ->data(['url' => url('/admin/'.$this->post->team->slug)]);
    }
}
