<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\Team;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApifyService
{
    protected string $actor;

    public function __construct()
    {
        $this->actor = config('services.apify.actor', 'clockworks~tiktok-scraper');
    }

    /**
     * Token Apify: prioritas token milik tenant, lalu fallback ke token master di env / DB.
     */
    protected function tokenFor(?Team $team): string
    {
        $token = $team?->apifyToken()
            ?: (string) config('services.apify.token')
            ?: (string) ApiKey::where('provider', 'apify')->where('is_active', true)->value('api_key');

        if (trim($token) === '') {
            throw new \RuntimeException(
                'Apify Token belum tersedia di sistem. Masukkan APIFY_API_KEY di .env atau tabel ApiKey.'
            );
        }

        return trim($token);
    }

    /**
     * Ambil 1 video TikTok terbaru (skip pinned post).
     */
    public function getLatestNonPinnedVideo(string $tiktok_profile_url, ?Team $team = null): ?array
    {
        $token = $this->tokenFor($team);

        $items = $this->fetchItems($token, $tiktok_profile_url, 1);

        if ($items === []) {
            return null;
        }

        if ($this->isPinned($items[0])) {
            Log::info('Post teratas TikTok pinned, ambil beberapa hasil untuk melewatinya.');
            $items = $this->fetchItems($token, $tiktok_profile_url, 5);
        }

        foreach ($items as $item) {
            if ($this->isPinned($item)) {
                continue;
            }

            $id = (string) ($item['id'] ?? $item['videoId'] ?? '');

            if ($id === '') {
                continue;
            }

            return [
                'post_id' => $id,
                'caption' => $item['text'] ?? ($item['desc'] ?? ''),
                'video_url' => $item['videoUrl'] ?? ($item['webVideoUrl'] ?? ''),
                'posted_at' => $item['createTimeISO'] ?? now(),
            ];
        }

        return null;
    }

    protected function fetchItems(string $token, string $profileUrl, int $limit): array
    {
        $endpoint = "https://api.apify.com/v2/acts/{$this->actor}/run-sync-get-dataset-items?token={$token}";

        $response = Http::timeout(120)->post($endpoint, [
            'profiles' => [$profileUrl],
            'resultsPerPage' => $limit,
            'excludePinnedPosts' => true,
            'shouldDownloadVideos' => false,
        ]);

        if (! $response->successful()) {
            Log::error('Apify TikTok Scraping Failed: '.$response->body());

            return [];
        }

        $items = $response->json();

        return is_array($items) ? $items : [];
    }

    protected function isPinned(array $item): bool
    {
        return ! empty($item['isPinned'])
            || ! empty($item['pinned'])
            || ! empty($item['is_pinned'])
            || ! empty($item['is_top']);
    }
}
