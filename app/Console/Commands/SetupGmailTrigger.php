<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetupGmailTrigger extends Command
{
    protected $signature = 'brandpilot:setup-gmail-trigger {webhook_url? : URL Webhook publik Anda (misal ngrok atau domain asli)}';
    protected $description = 'Daftarkan trigger GMAIL_NEW_GMAIL_MESSAGE di Composio Core secara otomatis';

    public function handle()
    {
        $this->info("=== PENDAFTARAN TRIGGER GMAIL LYNK.ID DI COMPOSIO ===");

        $apiKey = config('services.composio.key');
        $webhookUrl = $this->argument('webhook_url');

        // Jika tidak diberikan argumen, tanyakan interaktif
        if (! $webhookUrl) {
            $defaultWebhook = url('/api/webhooks/composio/gmail-payment');
            $webhookUrl = $this->ask("Masukkan URL Webhook BrandPilot Anda (gunakan HTTPS Ngrok jika di localhost):", $defaultWebhook);
        }

        $this->line("Target Webhook: <fg=cyan>{$webhookUrl}</>");
        $this->info("\nMendaftarkan trigger ke Composio API...");

        // 1. Daftarkan Trigger GMAIL_NEW_GMAIL_MESSAGE
        $endpoints = [
            'https://backend.composio.dev/api/v1/triggers',
            'https://backend.composio.dev/api/v3.1/triggers',
        ];

        $payload = [
            'slug'           => 'GMAIL_NEW_GMAIL_MESSAGE',
            'user_id'        => 'brandpilot_core_admin',
            'webhook_url'    => $webhookUrl,
            'trigger_config' => [
                'query' => 'from:lynk.id', // Hanya dengarkan pesan yang berasal dari Lynk.id
            ],
        ];

        foreach ($endpoints as $ep) {
            $response = Http::withHeaders([
                'x-api-key'    => trim($apiKey),
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post($ep, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $this->info("\n🎉 TRIGGER BERHASIL DIAKTIFKAN!");
                $this->line("==================================================");
                $this->line("Trigger ID: " . ($data['trigger_id'] ?? ($data['id'] ?? 'Aktif')));
                $this->line("Event     : GMAIL_NEW_GMAIL_MESSAGE");
                $this->line("Filter    : from:lynk.id");
                $this->line("Webhook   : {$webhookUrl}");
                $this->line("==================================================");
                $this->info("Sekarang setiap ada email penjualan Lynk.id masuk ke Gmail Anda, Composio akan otomatis menembak webhook BrandPilot AI!");
                return;
            }
        }

        $this->error("Gagal mendaftarkan trigger: " . $response->body());
    }
}