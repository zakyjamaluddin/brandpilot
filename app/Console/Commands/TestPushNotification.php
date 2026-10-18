<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use App\Notifications\SubscriptionExpiryPushNotification;
use Illuminate\Console\Command;

class TestPushNotification extends Command
{
    protected $signature = 'brandpilot:test-push {hari=7 : Sisa hari uji coba (7, 3, 1, atau 0 untuk expired)}';
    protected $description = 'Uji coba pop-up Push Notification langsung ke layar desktop/HP Anda';

    public function handle()
    {
        // app/Console/Commands/TestPushNotification.php

        $days = (int) $this->argument('hari');
        
        // Ambil user yang sudah mendaftarkan perangkat notifikasi
        $user = User::has('pushSubscriptions')->first();

        if (! $user) {
            $this->error("X Belum ada perangkat yang terdaftar di tabel 'push_subscriptions'!");
            $this->line("Buka browser di http://127.0.0.1:8000/admin/fashion-glow lalu klik tombol '🔔 Aktifkan Notifikasi Push'.");
            return;
        }

        $team = $user->teams()->first() ?? Team::first();

        $this->info("Menembakkan Push Notification ({$days} hari) ke User: {$user->email}...");
        $user->notify(new SubscriptionExpiryPushNotification($team, $days, $days === 0));
        $this->info("✓ Notifikasi terkirim! Periksa pojok layar monitor/ponsel Anda!");
    }
}