<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApifyService
{
    protected string $actor;

    public function __construct()
    {
        $this->actor = config('services.apify.actor');
    }

    /**
     * Token Apify milik tenant. Core token di .env TIDAK dipakai sebagai fallback.
     */
    protected function tokenFor(?Team $team): string
    {
        $token = $team?->apifyToken() ?? '';

        if ($token === '') {
            throw new \RuntimeException(
                $team
                    ? "Workspace [{$team->name}] belum memiliki Apify Token. Isi di menu Setting → Apify Token."
                    : 'Apify Token belum tersedia: operasi ini membutuhkan tenant.'
            );
        }

        return $token;
    }

    /**
     * Ambil 1 video TikTok terbaru (skip pinned post).
     *
     * Hemat kuota Apify: normalnya hanya 1 hasil per run ($3,70/1.000 hasil).
     * Hanya jika hasil teratas ternyata pinned, kita ambil 5 hasil sekali lagi —
     * kejadian langka, dan pinned post biasanya tidak berubah-ubah.
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
                'post_id'   => $id,
                'caption'   => $item['text'] ?? ($item['desc'] ?? ''),
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
            'excludePinnedPosts' => true, // hemat kuota: pinned tidak ikut dihitung/dikembalikan
            'shouldDownloadVideos' => false,
        ]);

        if (! $response->successful()) {
            Log::error('Apify TikTok Scraping Failed: ' . $response->body());

            return [];
        }

        $items = $response->json();

        return is_array($items) ? $items : [];
    }

    protected function isPinned(array $item): bool
    {
        return (bool) ($item['isPinned'] ?? ($item['pinned'] ?? false));
    }
}
