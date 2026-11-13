<?php

namespace App\Services\TikTok;

use App\Models\ApiKey;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApifyChecker
{
    /**
     * Batas maksimal pemanggilan Apify per hari agar kuota gratis 1.200/bulan tidak habis.
     */
    public const DAILY_CALL_LIMIT = 40;

    protected string $actor;

    public function __construct()
    {
        $this->actor = config('services.apify.actor', 'clockworks~tiktok-scraper');
    }

    /**
     * Ambil token Apify (prioritas: token tenant -> token master di config/env -> token di tabel ApiKey).
     */
    public function resolveToken(?Team $team = null): ?string
    {
        $tenantToken = $team?->apifyToken();
        if (! empty($tenantToken)) {
            return $tenantToken;
        }

        $masterToken = config('services.apify.token');
        if (! empty($masterToken)) {
            return trim((string) $masterToken);
        }

        $dbToken = ApiKey::where('provider', 'apify')->where('is_active', true)->value('api_key');
        if (! empty($dbToken)) {
            return trim((string) $dbToken);
        }

        return null;
    }

    /**
     * Cek apakah pemanggilan Apify hari ini masih di bawah batas harian (Circuit Breaker).
     */
    public function canMakeCall(): bool
    {
        $cacheKey = 'apify_daily_calls_'.now()->format('Y_m_d');
        $callsToday = (int) Cache::get($cacheKey, 0);

        return $callsToday < self::DAILY_CALL_LIMIT;
    }

    public function incrementCallCount(): void
    {
        $cacheKey = 'apify_daily_calls_'.now()->format('Y_m_d');
        Cache::increment($cacheKey);
    }

    /**
     * Ambil 1 video TikTok terbaru non-pinned via Apify sebagai fallback darurat.
     *
     * @return array{
     *     post_id: string,
     *     caption: string,
     *     video_url: string,
     *     direct_download_url: ?string,
     *     is_carousel: bool,
     *     images: array,
     *     posted_at: Carbon,
     *     source: string
     * }|null
     */
    public function getLatestNonPinnedPost(string $profileUrl, ?Team $team = null): ?array
    {
        $token = $this->resolveToken($team);

        if (! $token) {
            Log::warning('ApifyChecker: Tidak ada Apify Token yang tersedia.');

            return null;
        }

        if (! $this->canMakeCall()) {
            Log::warning('ApifyChecker: Circuit breaker aktif. Batas harian ('.self::DAILY_CALL_LIMIT.' call) telah tercapai untuk mengamankan kuota gratis bulanan.');

            return null;
        }

        try {
            $items = $this->fetchItems($token, $profileUrl, 1);
            $this->incrementCallCount();

            if (empty($items)) {
                return null;
            }

            if ($this->isPinned($items[0])) {
                Log::info('ApifyChecker: Post teratas pinned, mengambil 5 item berikutnya.');
                $items = $this->fetchItems($token, $profileUrl, 5);
                $this->incrementCallCount();
            }

            foreach ($items as $item) {
                if ($this->isPinned($item)) {
                    continue;
                }

                $id = (string) ($item['id'] ?? $item['videoId'] ?? '');
                if ($id === '') {
                    continue;
                }

                $videoUrl = $item['videoUrl'] ?? ($item['webVideoUrl'] ?? '');
                $caption = $item['text'] ?? ($item['desc'] ?? '');

                return [
                    'post_id' => $id,
                    'caption' => (string) $caption,
                    'video_url' => (string) $videoUrl,
                    'direct_download_url' => $item['videoDownloadUrl'] ?? null,
                    'is_carousel' => ! empty($item['images']) || ($item['mediaType'] ?? '') === 'image',
                    'images' => $item['images'] ?? [],
                    'posted_at' => isset($item['createTimeISO']) ? Carbon::parse($item['createTimeISO']) : now(),
                    'source' => 'apify',
                ];
            }
        } catch (Throwable $e) {
            Log::error('ApifyChecker Exception: '.$e->getMessage());
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
            Log::error('Apify scraping gagal: '.$response->body());

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
