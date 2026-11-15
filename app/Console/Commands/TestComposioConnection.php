<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestComposioConnection extends Command
{
    protected $signature = 'brandpilot:test-composio {platform=instagram} {--key=}';

    protected $description = 'Diagnosa koneksi Composio API v3.1 dan pembuatan Auth Config ID untuk platform sosial media';

    public function handle(): int
    {
        $platform = strtolower($this->argument('platform'));
        $this->info("=== DIAGNOSA KONEKSI COMPOSIO UNTUK PLATFORM: {$platform} ===");

        // 1. Cek Ketersediaan API Key
        $apiKey = trim((string) $this->option('key'));
        $source = 'Opsi --key CLI';

        if ($apiKey === '') {
            $envKey = config('services.composio.key');
            if (! empty($envKey)) {
                $source = 'File .env (COMPOSIO_API_KEY)';
                $apiKey = trim((string) $envKey);
            }
        }

        if ($apiKey === '') {
            try {
                $dbKey = ApiKey::query()
                    ->where('provider', 'composio')
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->value('api_key');

                if (! empty($dbKey)) {
                    $source = 'Database (Tabel api_keys)';
                    $apiKey = trim((string) $dbKey);
                }
            } catch (\Throwable) {
                // Database mungkin offline atau belum disiapkan
            }
        }

        if ($apiKey === '') {
            $this->error('X GAGAL: Tidak ditemukan Composio API Key di Database maupun di file .env.');
            $this->line('  Solusi:');
            $this->line('  1. Masuk ke admin Brandpilot -> menu API Keys -> Tambah API Key provider: Composio');
            $this->line('  2. Atau isi COMPOSIO_API_KEY di file .env hosting Anda.');

            return 1;
        }

        $maskedKey = substr($apiKey, 0, 5).str_repeat('*', max(4, strlen($apiKey) - 9)).substr($apiKey, -4);
        $this->info("✓ Sumber API Key : {$source}");
        $this->info("✓ API Key Token  : {$maskedKey}");

        // 2. Tes Request GET /auth_configs ke backend Composio
        $baseUrl = 'https://backend.composio.dev/api/v3.1';
        $this->line("\n1. Menguji koneksi & memeriksa Auth Config untuk toolkit [{$platform}]...");

        try {
            $res = Http::withHeaders([
                'x-api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->baseUrl($baseUrl)->timeout(30)->get('/auth_configs', [
                'toolkit_slug' => $platform,
            ]);
        } catch (Exception $e) {
            $this->error("X GAGAL KONEKSI KE COMPOSIO: {$e->getMessage()}");
            $this->line('  Penyebab umum di hosting:');
            $this->line('  - Port outbound HTTPS / domain backend.composio.dev diblokir oleh firewall hosting.');
            $this->line('  - Masalah sertifikat SSL cURL di PHP hosting (cURL error 60).');
            $this->line('  - Batas waktu koneksi (timeout) terlampaui.');

            return 1;
        }

        if ($res->status() === 401 || $res->status() === 403) {
            $err = $res->json('error.message') ?? ($res->json('message') ?? $res->body());
            $this->error("X API KEY DITOLAK COMPOSIO (HTTP {$res->status()}): {$err}");
            $this->line('  Solusi:');
            $this->line('  - Buka dashboard Composio Anda di https://app.composio.dev');
            $this->line('  - Buat atau salin API Key yang masih aktif di Settings -> API Keys');
            $this->line('  - Perbarui nilai API Key tersebut di menu API Keys Brandpilot hosting.');

            return 1;
        }

        if (! $res->successful()) {
            $this->error("X RESPON COMPOSIO GAGAL (HTTP {$res->status()}): ".$res->body());

            return 1;
        }

        $items = $res->json('items') ?? ($res->json('data') ?? []);
        $authConfigId = null;

        if (! empty($items) && isset($items[0]['id'])) {
            $authConfigId = $items[0]['id'];
            $this->info("✓ Ditemukan Auth Config ID yang sudah ada: {$authConfigId}");
        } else {
            $this->line('  Auth config belum ada di Composio. Mencoba membuat baru (POST /auth_configs)...');

            try {
                $createRes = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->baseUrl($baseUrl)->timeout(30)->post('/auth_configs', [
                    'toolkit' => [
                        'slug' => $platform,
                    ],
                    'auth_config' => [
                        'type' => 'use_composio_managed_auth',
                        'name' => ucfirst($platform).' Managed Auth',
                    ],
                ]);
            } catch (Exception $e) {
                $this->error("X Gagal mengirim permintaan POST /auth_configs: {$e->getMessage()}");

                return 1;
            }

            if (! $createRes->successful()) {
                $createErr = $createRes->json('error.message') ?? ($createRes->json('message') ?? $createRes->body());
                $this->error("X Gagal membuat Auth Config (HTTP {$createRes->status()}): {$createErr}");

                return 1;
            }

            $created = $createRes->json();
            $authConfigId = $created['auth_config']['id'] ?? ($created['id'] ?? null);

            if (! $authConfigId) {
                $this->error("X Berhasil HTTP 201 namun 'id' tidak ditemukan di respon: ".$createRes->body());

                return 1;
            }

            $this->info("✓ Berhasil membuat Auth Config ID baru: {$authConfigId}");
        }

        // 3. Tes Pengambilan OAuth Link
        $this->line("\n2. Menguji pembuatan OAuth Redirect URL (/connected_accounts/link)...");
        $dummyUserId = 'brandpilot_test_diagnostic';

        try {
            $linkRes = Http::withHeaders([
                'x-api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->baseUrl($baseUrl)->timeout(30)->post('/connected_accounts/link', [
                'auth_config_id' => $authConfigId,
                'user_id' => $dummyUserId,
                'callback_url' => url('/'),
            ]);
        } catch (Exception $e) {
            $this->error("X Gagal request OAuth Link: {$e->getMessage()}");

            return 1;
        }

        if (! $linkRes->successful()) {
            $linkErr = $linkRes->json('error.message') ?? ($linkRes->json('message') ?? $linkRes->body());
            $this->error("X Gagal membuat OAuth Link (HTTP {$linkRes->status()}): {$linkErr}");

            return 1;
        }

        $oauthUrl = $linkRes->json('redirect_url') ?? ($linkRes->json('redirectUrl') ?? ($linkRes->json('url') ?? null));

        if ($oauthUrl) {
            $this->info('✓ OAuth Link berhasil dibuat!');
            $this->line("  URL: {$oauthUrl}");
            $this->info("\n=== SEMUA PENGECEKAN COMPOSIO BERHASIL (SEHAT) ===");

            return 0;
        }

        $this->warn('! Respon 200 tetapi field redirect_url tidak ditemukan: '.$linkRes->body());

        return 0;
    }
}
