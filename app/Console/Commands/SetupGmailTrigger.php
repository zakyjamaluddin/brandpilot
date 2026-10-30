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

        // Sertakan token webhook di URL egress (Composio memanggil URL ini apa adanya).
        $egressUrl = $webhookUrl;
        $secret = (string) config('services.composio.webhook_secret');

        if ($secret !== '' && ! str_contains($egressUrl, 'token=')) {
            $egressUrl .= (str_contains($egressUrl, '?') ? '&' : '?') . 'token=' . urlencode($secret);
        }

        $this->line('Target Webhook: <fg=cyan>' . $webhookUrl . '</>');
        $this->info("\nMendaftarkan trigger ke Composio API...");

        // v3: trigger dibuat lewat upsert per-slug, dan URL pengiriman memakai egress_url
        // (bukan webhook_url seperti di v1). v1 sudah tidak membuat trigger apa pun,
        // tetapi tetap membalas 200 HTML — itulah sebabnya dulu terlihat "berhasil".
        $endpoint = 'https://backend.composio.dev/api/v3/trigger_instances/GMAIL_NEW_GMAIL_MESSAGE/upsert';

        $payload = [
            'user_id'        => 'brandpilot_core_admin',
            'trigger_config' => [
                'query' => 'from:lynk.id', // Hanya dengarkan pesan dari Lynk.id
            ],
            'egress_url'     => $egressUrl,
        ];

        $response = Http::withHeaders([
            'x-api-key' => trim((string) $apiKey),
            'Accept'    => 'application/json',
        ])->post($endpoint, $payload);

        $triggerId = $response->json('trigger_id');

        if (! $response->successful() || ! $triggerId) {
            $this->error('Gagal mendaftarkan trigger: ' . mb_substr($response->body(), 0, 400));

            return self::FAILURE;
        }

        // Verifikasi: endpoint lama membalas 200 walau tidak membuat trigger apa pun,
        // jadi trigger baru dianggap berhasil kalau benar-benar muncul di daftar aktif.
        $active = Http::withHeaders([
            'x-api-key' => trim((string) $apiKey),
            'Accept'    => 'application/json',
        ])->get('https://backend.composio.dev/api/v3/trigger_instances/active', [
            'user_ids' => 'brandpilot_core_admin',
        ])->json('items', []);

        if (! collect($active)->firstWhere('id', $triggerId)) {
            $this->error("Trigger {$triggerId} tidak muncul di daftar aktif Composio. Cek dashboard.");

            return self::FAILURE;
        }

        $this->info("\n🎉 TRIGGER AKTIF DAN TERVERIFIKASI");
        $this->line('==================================================');
        $this->line('Trigger ID: ' . $triggerId);
        $this->line('Event     : GMAIL_NEW_GMAIL_MESSAGE');
        $this->line('Filter    : from:lynk.id');
        $this->line('Webhook   : ' . $webhookUrl);
        $this->line('==================================================');
        $this->info('Setiap email penjualan Lynk.id akan menembak webhook BrandPilot AI.');

        return self::SUCCESS;
    }
}