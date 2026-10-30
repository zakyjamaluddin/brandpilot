<?php

namespace App\Services;

use App\Models\SocialIntegration;
use App\Models\Team;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $userId = "brandpilot_tenant_{$team->id}_{$team->slug}";
        $platforms = ['facebook', 'instagram', 'youtube'];

        try {
            // WAJIB paginasi + limit besar: tanpa ini sebagian koneksi (mis. Facebook)
            // jatuh di luar halaman pertama dan platform ikut ditandai terputus.
            $items = [];
            $cursor = null;

            do {
                $params = ['user_id' => $userId, 'limit' => 100];
                if ($cursor) {
                    $params['cursor'] = $cursor;
                }

                $res = $this->client($team)->get('/connected_accounts', $params);
                $page = $res->json('items') ?? ($res->json('data') ?? []);

                if (is_array($page)) {
                    $items = array_merge($items, $page);
                }

                $cursor = $res->json('next_cursor') ?? $res->json('nextCursor');
            } while ($cursor && count($items) < 1000);
        } catch (Throwable $e) {
            Log::error("Exception syncConnectedAccounts untuk Tenant [{$team->slug}]: " . $e->getMessage());

            return;
        }

        $active = [];

        foreach ($items as $item) {
            // Pastikan akun benar-benar milik tenant ini (jangan percaya filter server saja).
            if (($item['user_id'] ?? null) !== $userId) {
                continue;
            }

            $slug = strtolower($item['toolkit']['slug'] ?? ($item['appName'] ?? ''));
            $status = strtoupper($item['status'] ?? ($item['data']['status'] ?? ''));

            if (! in_array($slug, $platforms, true) || $status !== 'ACTIVE') {
                continue;
            }

            $active[$slug][] = $item;
        }

        // Urutkan tiap platform: koneksi terbaru lebih dulu (cadangan bila belum ada pilihan tersimpan)
        foreach ($active as $slug => $list) {
            usort($list, fn ($a, $b) => strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? '')));
            $active[$slug] = $list;
        }

        foreach ($platforms as $platform) {
            if (! empty($active[$platform])) {
                // Pertahankan koneksi yang sudah tersimpan selama masih ACTIVE,
                // supaya tidak berpindah akun saat tenant punya beberapa koneksi.
                $current = SocialIntegration::where('team_id', $team->id)
                    ->where('platform', $platform)
                    ->first();

                $item = null;
                if ($current?->composio_connection_id) {
                    foreach ($active[$platform] as $candidate) {
                        if (($candidate['id'] ?? null) === $current->composio_connection_id) {
                            $item = $candidate;
                            break;
                        }
                    }
                }

                $item ??= $active[$platform][0];

                SocialIntegration::updateOrCreate(
                    [
                        'team_id'  => $team->id,
                        'platform' => $platform,
                    ],
                    [
                        'composio_connection_id' => $item['id'],
                        'account_name'           => ucfirst(str_replace('_', ' ', (string) ($item['word_id'] ?? $item['alias'] ?? $platform))),
                        'is_connected'           => true,
                    ]
                );

                continue;
            }

            // Tidak ada koneksi ACTIVE di Composio: status wajib ikut terputus,
            // supaya tidak ada lagi "aktif" palsu dari data lama.
            SocialIntegration::where('team_id', $team->id)
                ->where('platform', $platform)
                ->update([
                    'is_connected'           => false,
                    'composio_connection_id' => null,
                    'account_name'           => null,
                ]);
        }
    }

    /**
     * API key Composio dipakai BERSAMA oleh semua tenant (satu aplikasi, banyak entity).
     * Connected account tiap tenant tetap terpisah lewat user_id brandpilot_tenant_{id}_{slug}.
     */
    protected function apiKeyFor(Team $team): string
    {
        $key = trim((string) \App\Models\ApiKey::query()
            ->where('provider', 'composio')
            ->where('is_active', true)
            ->orderBy('id')
            ->value('api_key'));

        if ($key === '') {
            throw new Exception('Composio API Key belum tersedia. Tambahkan di menu API Keys (provider: Composio).');
        }

        return $key;
    }

    protected function client(Team $team)
    {
        $apiKey = $this->apiKeyFor($team);

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


    /**
     * Mengunggah berkas video lokal ke Composio S3 Storage untuk mendapatkan s3key (FileUploadable)
     */
    // app/Services/ComposioService.php

    /**
     * Mengunggah berkas video lokal ke Composio S3 Storage (/files/upload) untuk mendapatkan S3Key
     */
    public function uploadFile(Team $team, string $fileContentOrPath, string $filename = 'video.mp4', string $mimeType = 'video/mp4'): array
    {
        $apiKey = $this->apiKeyFor($team);

        if (file_exists($fileContentOrPath)) {
            $contents = file_get_contents($fileContentOrPath);
        } elseif (Storage::disk('public')->exists($fileContentOrPath)) {
            $contents = Storage::disk('public')->get($fileContentOrPath);
        } else {
            $contents = Http::timeout(180)->get($fileContentOrPath)->body();
        }

        // 1) minta presigned URL + key
        $res = Http::withHeaders(['x-api-key' => $apiKey])->timeout(60)
            ->post('https://backend.composio.dev/api/v3/files/upload/request', [
                'toolkit_slug' => 'youtube',
                'tool_slug'    => 'YOUTUBE_UPLOAD_VIDEO',
                'filename'     => $filename,
                'mimetype'     => $mimeType,
                'md5'          => md5($contents),
            ]);

        if (! $res->successful()) {
            throw new Exception('Composio upload/request gagal: ' . $res->body());
        }

        $json    = $res->json();
        $key     = $json['key'];                                     // <-- key, bukan s3key
        $url     = $json['new_presigned_url'] ?? $json['newPresignedUrl'] ?? null;
        $backend = $json['metadata']['storage_backend'] ?? 's3';

        // 2) PUT isi file (lewati kalau dedupe bilang sudah ada)
        if (($json['type'] ?? 'new') !== 'existing' && $url) {
            $headers = ['Content-Type' => $mimeType];
            if ($backend === 'azure_blob_storage') {
                $headers['x-ms-blob-type'] = 'BlockBlob';
            }
            $put = Http::withHeaders($headers)->timeout(900)->withBody($contents, $mimeType)->put($url);
            if (! $put->successful()) {
                throw new Exception("PUT ke storage gagal ({$put->status()}): " . $put->body());
            }
        }

        Log::info("Composio staging OK, s3key=[{$key}] backend={$backend}");

        // 3) objek inilah yang dipakai sebagai videoFilePath
        return ['name' => $filename, 'mimetype' => $mimeType, 's3key' => $key];
    }


        /**
     * Unggah video ke YouTube: auth dari Composio (proxy), byte dikirim langsung ke
     * YouTube lewat resumable upload. Aksi YOUTUBE_UPLOAD_VIDEO tidak dipakai karena
     * relay berkasnya merusak stream (video terbit lalu dibuang YouTube).
     */
    public function uploadYoutubeVideo(Team $team, string $videoSource, array $meta, ?string $connectedAccountId = null): array
    {
        $apiKey  = $this->apiKeyFor($team);
        $base    = rtrim(config('services.composio.base_url', 'https://backend.composio.dev/api/v3'), '/');
        $mime    = $meta['mimetype'] ?? 'video/mp4';
        $account = $connectedAccountId ?: $this->activeYoutubeAccountId($team);

        if (! $account) {
            return ['success' => false, 'error' => 'Tidak ada connected account YouTube berstatus ACTIVE.'];
        }

        // 1) berkas lokal (ukuran pasti + bisa di-stream, tidak dimuat ke memori)
        $tmp  = null;
        $path = $videoSource;
        Log::info("YouTube: mempersiapkan unggah video dari sumber: {$videoSource}");
        if (! is_file($path)) {
            $tmp = tempnam(sys_get_temp_dir(), 'ytshort_') . '.mp4';
            $dl  = Http::timeout(600)->sink($tmp)->get($videoSource);
            if (! $dl->successful() || ! is_file($tmp) || filesize($tmp) === 0) {
                @unlink($tmp);
                return ['success' => false, 'error' => 'Gagal mengambil sumber video: ' . $videoSource];
            }
            $path = $tmp;
        }
        $size = filesize($path);

        // 2) minta sesi resumable ke YouTube (Composio menyuntikkan auth-nya)
        $init = Http::withHeaders(['x-api-key' => $apiKey])->timeout(120)
            ->post($base . '/tools/execute/proxy', [
                'connected_account_id' => $account,
                'endpoint'             => 'https://www.googleapis.com/upload/youtube/v3/videos?uploadType=resumable&part=snippet,status',
                'method'               => 'POST',
                'body'                 => [
                    'snippet' => [
                        'title'       => mb_substr((string) ($meta['title'] ?? ''), 0, 95),
                        'description' => (string) ($meta['description'] ?? ''),
                        'tags'        => array_slice($meta['tags'] ?? [], 0, 15),
                        'categoryId'  => (string) ($meta['categoryId'] ?? '22'),
                    ],
                    'status' => [
                        'privacyStatus'           => $meta['privacyStatus'] ?? 'private',
                        'selfDeclaredMadeForKids' => false,
                    ],
                ],
                'parameters' => [
                    ['name' => 'X-Upload-Content-Type',   'value' => $mime,          'type' => 'header'],
                    ['name' => 'X-Upload-Content-Length', 'value' => (string) $size, 'type' => 'header'],
                ],
            ]);

        $location = $init->json('headers.location') ?? $init->json('headers.Location');
        if (! $location) {
            @unlink($tmp);
            Log::error('YouTube: gagal membuat sesi resumable', ['http' => $init->status(), 'body' => $init->body()]);
return ['success' => false, 'error' => 'Gagal membuat sesi upload YouTube: ' . $init->body()];
        }

        // 3) kirim byte langsung ke YouTube — timeout panjang, body berupa stream
        $handle = fopen($path, 'rb');
        try {
            $put = Http::timeout(1800)
                ->withHeaders(['Content-Type' => $mime, 'Content-Length' => (string) $size])
                ->withBody($handle, $mime)
                ->put($location);
        } finally {
            if (is_resource($handle)) fclose($handle);
            if ($tmp) @unlink($tmp);
        }

        $videoId = $put->json('id');
        if (! $put->successful() || ! $videoId) {
            Log::error('YouTube: PUT resumable gagal', ['http' => $put->status(), 'body' => $put->body()]);
            return ['success' => false, 'error' => 'Unggah ke YouTube gagal: ' . $put->body()];
        }

        Log::info("YouTube: video terunggah ({$size} bytes) videoId={$videoId}");
        return ['success' => true, 'video_id' => (string) $videoId, 'size' => $size];
    }

    /** 'succeeded' | 'processing' | 'failed' | null (null = video sudah dibuang YouTube) */
    public function youtubeProcessingStatus(Team $team, string $videoId, ?string $connectedAccountId = null): ?string
    {
        $res   = $this->executeAction($team, 'YOUTUBE_VIDEO_DETAILS', [
            'id' => $videoId, 'part' => 'status,processingDetails',
        ], $connectedAccountId);

        // Tool ini bisa hilang dari katalog Composio (404 ToolNotFound).
        // Jangan sampai kegagalan verifikasi dilaporkan sebagai "video hilang".
        if (! ($res['success'] ?? false)) {
            Log::warning('YouTube: verifikasi status tidak tersedia', [
                'video' => $videoId,
                'error' => $res['error'] ?? null,
            ]);

            return 'processing';
        }

        $items = $res['data']['data']['response_data']['items'] ?? [];
        if (! $items) {
            return null;
        }
        return $items[0]['processingDetails']['processingStatus'] ?? ($items[0]['status']['uploadStatus'] ?? null);
    }

    /** ID connected account YouTube yang ACTIVE (hindari akun EXPIRED). */
    public function activeYoutubeAccountId(Team $team): ?string
    {
        $apiKey = $this->apiKeyFor($team);
        $base   = rtrim(config('services.composio.base_url', 'https://backend.composio.dev/api/v3'), '/');

        $resp = Http::withHeaders(['x-api-key' => $apiKey])->timeout(60)
            ->get($base . '/connected_accounts', [
                'toolkit_slugs' => 'youtube',
                'user_ids'      => $this->getUserId($team),
                'statuses'      => 'ACTIVE',
                'limit'         => 20,
            ]);

        foreach (($resp->json('items') ?? []) as $item) {
            if (($item['status'] ?? null) === 'ACTIVE' && (($item['toolkit']['slug'] ?? null) === 'youtube')) {
                return $item['id'];
            }
        }
        return null;
    }

    

    /**
     * 2. Instagram Carousel (Katalog Resmi: MEDIA_CONTAINER -> CAROUSEL_CONTAINER -> CREATE_POST)
     */
    public function postInstagramCarousel(Team $team, array $publicImageUrls, string $caption, ?string $connectedAccountId = null): array
    {
        try {
            // Ambil ig_user_id
            $profileRes = $this->executeAction($team, 'INSTAGRAM_GET_USER_INFO', [], $connectedAccountId);
            $igUserId = $profileRes['data']['data']['id'] ?? ($profileRes['data']['id'] ?? null);

            if (! $igUserId) {
                throw new Exception("Gagal mendapatkan ig_user_id.");
            }

            $childContainerIds = [];
            // Batasi maksimal 10 foto sesuai aturan baku Meta
            $validPhotos = array_slice($publicImageUrls, 0, 10);

            // LANGKAH 1: Buat Item Container per foto (is_carousel_item = true)
            Log::info("Instagram Carousel: Langkah 1 -> Membuat " . count($validPhotos) . " item containers...");
            foreach ($validPhotos as $idx => $imgUrl) {
                $itemRes = $this->executeAction($team, 'INSTAGRAM_CREATE_MEDIA_CONTAINER', [
                    'ig_user_id'       => (string) $igUserId,
                    'image_url'        => $imgUrl, // URL publik Zaky Apps (bukan localhost!)
                    'is_carousel_item' => true,
                ], $connectedAccountId);

                $childId = $itemRes['data']['data']['id'] ?? ($itemRes['data']['id'] ?? null);
                if ($childId) {
                    $childContainerIds[] = (string) $childId;
                } else {
                    Log::warning("Gagal membuat item container untuk foto ke-" . ($idx + 1) . ": " . json_encode($itemRes));
                }
            }

            if (count($childContainerIds) < 2) {
                throw new Exception("Instagram Carousel membutuhkan minimal 2 foto valid. Berhasil dibuat: " . count($childContainerIds));
            }

            // LANGKAH 2: Satukan ke Wadah Induk Carousel (INSTAGRAM_CREATE_CAROUSEL_CONTAINER)
            Log::info("Instagram Carousel: Langkah 2 -> Menggabungkan " . count($childContainerIds) . " container ke INSTAGRAM_CREATE_CAROUSEL_CONTAINER...");
            $parentRes = $this->executeAction($team, 'INSTAGRAM_CREATE_CAROUSEL_CONTAINER', [
                'ig_user_id' => (string) $igUserId,
                'children'   => $childContainerIds,
                'caption'    => Str::limit($caption, 2100),
            ], $connectedAccountId);

            $carouselCreationId = $parentRes['data']['data']['id'] ?? ($parentRes['data']['id'] ?? null);

            if (! $carouselCreationId) {
                throw new Exception("Gagal membuat Carousel Container: " . json_encode($parentRes));
            }

            Log::info("Instagram Carousel: Menunggu rendering container Meta (10 detik)...");
            sleep(10);

            // LANGKAH 3: Terbitkan ke Feed (INSTAGRAM_CREATE_POST)
            Log::info("Instagram Carousel: Langkah 3 -> Menerbitkan Carousel ID [{$carouselCreationId}]...");
            $publishRes = $this->executeAction($team, 'INSTAGRAM_CREATE_POST', [
                'ig_user_id'  => (string) $igUserId,
                'creation_id' => (string) $carouselCreationId,
            ], $connectedAccountId);

            return $publishRes;

        } catch (\Throwable $e) {
            Log::error("Gagal Menerbitkan Instagram Carousel: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // app/Services/ComposioService.php

    /**
     * Mengambil Page Access Token resmi milik Halaman Facebook
     */
    /**
     * Ambil page_id Facebook pertama yang bisa dipakai untuk posting.
     * Dipakai saat metadata integrasi belum menyimpan page_id.
     */
    public function facebookPageId(Team $team, ?string $connectedAccountId = null): ?string
    {
        $pages = $this->facebookPages($team, $connectedAccountId);

        return $pages[0]['id'] ?? null;
    }

    /**
     * Daftar halaman Facebook yang benar-benar bisa diakses oleh koneksi ini.
     * Dipakai untuk memvalidasi page_id tersimpan agar tidak pernah memposting
     * ke halaman milik tenant/brand lain.
     *
     * @return array<int, array{id: string, name: string}>
     */
    public function facebookPages(Team $team, ?string $connectedAccountId = null): array
    {
        $res = $this->executeAction($team, 'FACEBOOK_GET_USER_PAGES', [], $connectedAccountId);

        if (! ($res['success'] ?? false)) {
            Log::warning('Facebook: gagal mengambil daftar halaman', ['conn' => $connectedAccountId]);

            return [];
        }

        $payload = $res['data']['data'] ?? ($res['data'] ?? []);
        $pages = $payload['response_data']['data']
            ?? ($payload['data']['data'] ?? ($payload['data'] ?? []));

        if (! is_array($pages)) {
            return [];
        }

        $out = [];
        foreach ($pages as $page) {
            if (! empty($page['id'])) {
                $out[] = ['id' => (string) $page['id'], 'name' => (string) ($page['name'] ?? '')];
            }
        }

        return $out;
    }

    public function getPageAccessToken(Team $team, string $pageId, ?string $connectedAccountId = null): ?string
    {
        try {
            // 1. Cek apakah token sudah tersimpan di metadata database
            $integration = SocialIntegration::where('team_id', $team->id)->where('platform', 'facebook')->first();
            if (!empty($integration->metadata['page_access_token'])) {
                return $integration->metadata['page_access_token'];
            }

            // 2. Ambil dari tool FACEBOOK_LIST_MANAGED_PAGES atau FACEBOOK_GET_USER_PAGES
            $pagesRes = $this->executeAction($team, 'FACEBOOK_LIST_MANAGED_PAGES', [], $connectedAccountId);
            $pages = $pagesRes['data']['data']['data']
                ?? ($pagesRes['data']['data']
                ?? ($pagesRes['data']['response_data'] ?? []));

            if (empty($pages)) {
                $userPagesRes = $this->executeAction($team, 'FACEBOOK_GET_USER_PAGES', [], $connectedAccountId);
                $pages = $userPagesRes['data']['data']['data'] ?? ($userPagesRes['data']['data'] ?? []);
            }

            foreach ($pages as $p) {
                if ((string) ($p['id'] ?? '') === (string) $pageId) {
                    $token = $p['access_token'] ?? null;
                    if ($token && $integration) {
                        $meta = $integration->metadata ?? [];
                        $meta['page_access_token'] = $token;
                        $integration->update(['metadata' => $meta]);
                    }
                    return $token;
                }
            }

            return null;
        } catch (\Throwable $e) {
            Log::error("Gagal mendapatkan Page Access Token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * RESEP TERBUKTI: Facebook Multi-Photo Album (1 Postingan Grid Berisi Semua Foto)
     */
    public function postFacebookCarouselAlbum(
        Team $team,
        array $publicImageUrls,
        string $title,
        string $description,
        string $pageId,
        ?string $connectedAccountId = null
    ): array {
        try {
            // 1. Ambil Page Access Token (Kunci Penentu Meta!)
            $pageToken = $this->getPageAccessToken($team, $pageId, $connectedAccountId);
            if (! $pageToken) {
                throw new Exception("Gagal mendapatkan Page Access Token untuk Page ID [{$pageId}].");
            }

            $photoUrls = array_slice($publicImageUrls, 0, 10);
            $attachedMedia = [];

            Log::info("Facebook Multi-Photo Album: Mengunggah " . count($photoUrls) . " foto sebagai unpublished dengan Page Token...");

            // 2. Per foto: POST /{page}/photos?published=false&url=<url>&access_token=*** -> dapat media_fbid
            foreach ($photoUrls as $idx => $imgUrl) {
                $res = Http::timeout(60)->post("https://graph.facebook.com/v21.0/{$pageId}/photos", [
                    'url'          => $imgUrl,
                    'published'    => 'false', // Unshared/staging
                    'access_token' => $pageToken,
                ]);

                $data = $res->json();
                $photoId = $data['id'] ?? null;

                if ($photoId) {
                    $attachedMedia[] = ['media_fbid' => (string) $photoId];
                } else {
                    Log::warning("Gagal upload unpublished foto ke-" . ($idx + 1) . ": " . $res->body());
                }
            }

            if (empty($attachedMedia)) {
                throw new Exception("Tidak ada foto yang berhasil diunggah ke staging Facebook.");
            }

            Log::info("Facebook Multi-Photo Album: Menggabungkan " . count($attachedMedia) . " foto ke 1 postingan feed...");

            // 3. Sekali saja: POST /{page}/feed dengan attached_media -> 1 POSTINGAN GRID UTUH!
            $publishRes = Http::timeout(60)->post("https://graph.facebook.com/v21.0/{$pageId}/feed", [
                'message'        => "{$title}\n\n{$description}",
                'attached_media' => $attachedMedia,
                'access_token'   => $pageToken,
            ]);

            $result = $publishRes->json();

            if ($publishRes->successful() && !empty($result['id'])) {
                Log::info("✓ SUKSES BESAR! 1 Postingan Facebook Album terbit dengan ID: {$result['id']}");
                return [
                    'success' => true,
                    'data'    => ['data' => ['id' => $result['id']]],
                ];
            }

            Log::error("Gagal menerbitkan Facebook feed album: " . $publishRes->body());
            return [
                'success' => false,
                'error'   => $result['error']['message'] ?? 'Gagal membuat multi-photo feed post di Facebook.',
            ];

        } catch (\Throwable $e) {
            Log::error("Exception postFacebookCarouselAlbum: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


}
