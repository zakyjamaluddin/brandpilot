<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ConnectAdminGmail extends Command
{
    protected $signature = 'brandpilot:connect-admin-gmail';
    protected $description = 'Ambil link login OAuth untuk menghubungkan Gmail Admin ke Composio Core';

    public function handle()
    {
        $this->info("Menghubungkan Gmail Admin ke Composio Core Project...");

        $apiKey = config('services.composio.key');
        $baseUrl = 'https://backend.composio.dev/api/v3.1';

        // 1. Ambil atau Buat auth_config untuk Gmail
        $client = Http::withHeaders([
            'x-api-key'    => trim($apiKey),
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->baseUrl($baseUrl);

        $authRes = $client->get('/auth_configs', ['toolkit_slug' => 'gmail']);
        $items = $authRes->json('items') ?? ($authRes->json('data') ?? []);
        $authConfigId = $items[0]['id'] ?? null;

        if (! $authConfigId) {
            $createAuth = $client->post('/auth_configs', [
                'toolkit'     => ['slug' => 'gmail'],
                'auth_config' => ['type' => 'use_composio_managed_auth'],
            ]);
            $authConfigId = $createAuth->json('auth_config.id') ?? ($createAuth->json('id') ?? null);
        }

        if (! $authConfigId) {
            $this->error("Gagal mendapatkan Auth Config ID Gmail: " . $authRes->body());
            return;
        }

        // 2. Minta URL Login OAuth untuk Admin Core
        $linkRes = $client->post('/connected_accounts/link', [
            'auth_config_id' => $authConfigId,
            'user_id'        => 'brandpilot_core_admin',
            'callback_url'   => url('/admin'),
        ]);

        $url = $linkRes->json('redirect_url') ?? ($linkRes->json('redirectUrl') ?? ($linkRes->json('url') ?? null));

        if ($url) {
            $this->info("\n✓ BERHASIL MENDAPATKAN LINK KONEKSI GMAIL ADMIN!");
            $this->line("==================================================");
            $this->line("Silakan salin dan buka tautan ini di browser Anda:");
            $this->line("<fg=cyan;options=bold>{$url}</>");
            $this->line("==================================================");
            $this->line("Login dengan akun Gmail Anda yang menerima notifikasi penjualan Lynk.id.");
        } else {
            $this->error("Gagal membuat sesi login: " . $linkRes->body());
        }
    }
}