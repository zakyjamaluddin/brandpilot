<?php

namespace App\Services;

use App\Models\SocialIntegration;
use App\Models\Team;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ComposioService
{
    /**
     * Base URL resmi Composio REST API v3.1
     */
    protected string $baseUrl = 'https://backend.composio.dev/api/v3.1';

    
    /**
     * 1. Mengambil atau Membuat Auth Config ID untuk Toolkit (facebook, instagram, youtube)
     */
    public function getAuthConfigId(Team $team, string $toolkitSlug): ?string
    {
        try {
            $slug = strtolower($toolkitSlug);
            $client = $this->client($team);

            // Cek apakah auth_config sudah pernah dibuat sebelumnya
            $response = $client->get('/auth_configs', [
                'toolkit_slug' => $slug,
            ]);

            if ($response->successful()) {
                $items = $response->json('items') ?? ($response->json('data') ?? []);
                if (!empty($items) && isset($items[0]['id'])) {
                    return $items[0]['id'];
                }
            }

            // Jika belum ada, buat baru menggunakan managed auth Composio
            $createRes = $client->post('/auth_configs', [
                'toolkit' => [
                    'slug' => $slug,
                ],
                'auth_config' => [
                    'type' => 'use_composio_managed_auth',
                ],
            ]);

            if ($createRes->successful()) {
                $created = $createRes->json();
                return $created['auth_config']['id'] ?? ($created['id'] ?? null);
            }

            Log::error("Gagal membuat auth_config Composio [{$slug}]: " . $createRes->body());
            return null;
        } catch (Throwable $e) {
            Log::error("Exception getAuthConfigId ({$toolkitSlug}) untuk Tenant [{$team->slug}]: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 2. Inisiasi Link Sesi OAuth per Tenant (Facebook, Instagram, YouTube)
     */
    public function initiateConnection(Team $team, string $appName): string
    {
        $toolkit = strtolower($appName);
        $userId  = "brandpilot_tenant_{$team->id}_{$team->slug}";

        $authConfigId = $this->getAuthConfigId($team, $toolkit);
        if (!$authConfigId) {
            throw new Exception("Gagal mendapatkan Auth Config ID dari Composio untuk platform {$appName}.");
        }

        // URL tujuan callback setelah user menyelesaikan otorisasi di Composio
        $callbackUrl = route('composio.callback', ['team' => $team->id, 'platform' => $appName]);

        $response = $this->client($team)->post('/connected_accounts/link', [
            'auth_config_id' => $authConfigId,
            'user_id'        => $userId,
            'callback_url'   => $callbackUrl,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $url = $data['redirect_url'] ?? ($data['redirectUrl'] ?? ($data['url'] ?? null));

            if (!empty($url)) {
                return $url;
            }
        }

        Log::error("Composio /connected_accounts/link gagal untuk [{$appName}]: " . $response->body());
        throw new Exception("Gagal mengambil link OAuth dari Composio: " . $response->body());
    }

    /**
     * 3. Sinkronisasi Akun Terkoneksi dari Composio ke Database SocialIntegration
     */
    public function syncConnectedAccounts(Team $team): void
    {
        try {
            $userId = "brandpilot_tenant_{$team->id}_{$team->slug}";

            $res = $this->client($team)->get('/connected_accounts', [
                'user_id' => $userId,
            ]);

            $items = $res->json('items') ?? ($res->json('data') ?? []);
            if (empty($items)) {
                return;
            }

            foreach ($items as $item) {
                $slug = strtolower($item['toolkit']['slug'] ?? ($item['appName'] ?? ''));
                $status = strtoupper($item['status'] ?? ($item['data']['status'] ?? ''));

                if (in_array($slug, ['facebook', 'instagram', 'youtube']) && in_array($status, ['ACTIVE', 'CONNECTED', 'SUCCESS'])) {
                    SocialIntegration::updateOrCreate(
                        [
                            'team_id'  => $team->id,
                            'platform' => $slug,
                        ],
                        [
                            'composio_connection_id' => $item['id'],
                            'account_name'           => ucfirst($slug) . ' Connected',
                            'is_connected'           => true,
                        ]
                    );
                }
            }
        } catch (Throwable $e) {
            Log::error("Exception syncConnectedAccounts untuk Tenant [{$team->slug}]: " . $e->getMessage());
        }
    }

    protected function client(Team $team)
    {
        $apiKey = $team->composioKey?->api_key ?: config('services.composio.key');

        if (empty($apiKey)) {
            throw new Exception("Workspace [{$team->name}] belum memiliki Composio API Key yang aktif.");
        }

        return Http::withHeaders([
            'x-api-key'    => trim($apiKey),
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->baseUrl($this->baseUrl)->timeout(120); // Naikkan ke 120 detik
    }

    /**
     * User ID resmi yang terbukti sukses di akun Anda
     */
    protected function getUserId(Team $team): string
    {
        return "brandpilot_tenant_{$team->id}_{$team->slug}";
    }

    /**
     * Eksekutor Aksi dengan Penanganan Response yang Benar
     */
    public function executeAction(Team $team, string $toolSlug, array $params = [], ?string $connectedAccountId = null): array
    {
        $userId    = $this->getUserId($team);
        $arguments = empty($params) ? (object) [] : $params;

        $payload = [
            'user_id'   => $userId,
            'arguments' => $arguments,
            'version'   => 'latest',
        ];

        if ($connectedAccountId) {
            $payload['connected_account_id'] = $connectedAccountId;
        }

        $response = $this->client($team)->post("/tools/execute/{$toolSlug}", $payload);
        $json = $response->json();

        // Auto-heal jika ada mismatch ID
        $errorCode = $json['error']['code'] ?? null;
        if (in_array($errorCode, [1810, 1812]) && isset($payload['connected_account_id'])) {
            Log::warning("Composio: Melepas connected_account_id untuk auto-resolve entity...");
            unset($payload['connected_account_id']);

            $response = $this->client($team)->post("/tools/execute/{$toolSlug}", $payload);
            $json = $response->json();
        }

        // Cek keberhasilan dari respon aktif
        if ($response->successful() && ($json['successful'] ?? true) && empty($json['error'])) {
            return [
                'success' => true,
                'data'    => $json,
            ];
        }

        Log::error("Composio Action Gagal [{$toolSlug}]:", [
            'team'     => $team->slug,
            'response' => $json,
        ]);

        return [
            'success' => false,
            'error'   => $json['error'] ?? ($json['message'] ?? 'Gagal mengeksekusi aksi Composio.'),
            'data'    => $json,
        ];
    }

    /**
     * Helper Instagram Reel dengan waktu tunggu rendering yang aman
     */
    public function postInstagramReel(Team $team, string $videoUrl, string $caption, ?string $connectedAccountId = null): array
    {
        try {
            // 1. Ambil ig_user_id
            $profileRes = $this->executeAction($team, 'INSTAGRAM_GET_USER_INFO', [], $connectedAccountId);
            $igUserId = $profileRes['data']['data']['id'] ?? ($profileRes['data']['id'] ?? null);

            if (! $igUserId && $connectedAccountId) {
                $accRes = $this->client($team)->get("/connected_accounts/{$connectedAccountId}")->json();
                $igUserId = $accRes['data']['id'] ?? ($accRes['data']['user_id'] ?? null);
            }

            if (! $igUserId) {
                throw new Exception("Gagal mendapatkan ig_user_id dari akun Instagram.");
            }

            Log::info("Instagram: Menggunakan ig_user_id [{$igUserId}]");

            // 2. Langkah 1: Buat Media Container
            Log::info("Instagram: Membuat Media Container Reel...");
            $containerRes = $this->executeAction($team, 'INSTAGRAM_CREATE_MEDIA_CONTAINER', [
                'ig_user_id' => (string) $igUserId,
                'media_type' => 'REELS',
                'video_url'  => $videoUrl,
                'caption'    => $caption,
            ], $connectedAccountId);

            $creationId = $containerRes['data']['data']['id']
                ?? ($containerRes['data']['id'] ?? null);

            if (! $creationId) {
                throw new Exception("Gagal membuat Media Container: " . json_encode($containerRes));
            }

            // Beri jeda 12 detik agar server Meta selesai memproses dan mengompres video
            Log::info("Instagram: Menunggu rendering video di server Meta (12 detik)...");
            sleep(12);

            // 3. Langkah 2: Terbitkan Post Reel
            Log::info("Instagram: Menerbitkan Container ID: {$creationId}...");
            $publishRes = $this->executeAction($team, 'INSTAGRAM_CREATE_POST', [
                'ig_user_id'  => (string) $igUserId,
                'creation_id' => (string) $creationId,
            ], $connectedAccountId);

            return $publishRes;

        } catch (\Throwable $e) {
            Log::error("Gagal Posting Instagram Reel: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * 5. Wrapper publik untuk memposting konten (Facebook, Instagram, YouTube)
     */
    public function publishPost(Team $team, string $actionName, array $params, ?string $connectedAccountId = null): array
    {
        return $this->executeAction($team, $actionName, $params, $connectedAccountId);
    }
}
