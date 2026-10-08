<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApifyService
{
    protected string $token;
    protected string $actor;

    public function __construct()
    {
        $this->token = config('services.apify.token');
        $this->actor = config('services.apify.actor');
    }

    /**
     * Ambil 1 video TikTok terbaru (skip pinned post)
     */
    public function getLatestNonPinnedVideo(string $tiktokProfileUrl, ?Team $team = null): ?array
    {
        // Jalankan Actor Clockworks secara synchronous dan ambil hasilnya langsung
        $token = $team?->apifyKey?->api_key ?: config('services.apify.token', '');
        $endpoint = "https://api.apify.com/v2/acts/{$this->actor}/run-sync-get-dataset-items?token={$token}";

        $response = Http::timeout(120)->post($endpoint, [
            'profiles' => [$tiktokProfileUrl],
            'resultsPerPage' => 5, // Ambil 5 teratas untuk mengantisipasi jika ada 1-3 pinned post
            'shouldDownloadVideos' => false,
        ]);

        if (! $response->successful()) {
            Log::error('Apify TikTok Scraping Failed: ' . $response->body());
            return null;
        }

        $items = $response->json();

        if (empty($items) || ! is_array($items)) {
            return null;
        }

        // Loop untuk mencari video pertama yang BUKAN pinned post
        foreach ($items as $item) {
            $isPinned = $item['isPinned'] ?? ($item['pinned'] ?? false);

            if (! $isPinned) {
                return [
                    'post_id'    => (string) ($item['id'] ?? $item['videoId'] ?? ''),
                    'caption'    => $item['text'] ?? ($item['desc'] ?? ''),
                    'video_url'  => $item['videoUrl'] ?? ($item['webVideoUrl'] ?? ''),
                    'posted_at'  => $item['createTimeISO'] ?? now(),
                ];
            }
        }

        return null;
    }
}
