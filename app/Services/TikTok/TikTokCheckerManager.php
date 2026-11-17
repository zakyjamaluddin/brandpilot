<?php

namespace App\Services\TikTok;

use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TikTokCheckerManager
{
    public function __construct(
        protected CloudflareWorkerChecker $cloudflareWorkerChecker,
        protected TikWmChecker $tikWmChecker,
        protected ZakyAppsChecker $zakyAppsChecker,
        protected ApifyChecker $apifyChecker
    ) {}

    /**
     * Ambil 1 postingan TikTok terbaru non-pinned menggunakan strategi 4-Tier Fallback.
     *
     * Tier 1: ZakyApps Microservice (Direct with Rotating Webshare Proxy, 100% Free, Anti-Ban)
     * Tier 2: TikWM API (Publik/Cepat, hemat resource)
     * Tier 3: Cloudflare Worker Edge Scraper (100% Free edge proxy)
     * Tier 4: Apify Master Token (Garda terakhir dengan batasan harian Circuit Breaker)
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
        // 1. TIER 1: ZakyApps Microservice (Rotating Proxy yt-dlp)
        try {
            $post = $this->zakyAppsChecker->getLatestNonPinnedPost($profileUrl);
            if ($post !== null && ! empty($post['post_id'])) {
                Log::info("TikTokChecker: Berhasil mendeteksi post [{$post['post_id']}] via TIER 1 (ZakyApps Proxy).");

                return $post;
            }
        } catch (\Throwable $e) {
            Log::notice('TikTokChecker: Tier 1 (ZakyApps) gagal: '.$e->getMessage());
        }

        // 2. TIER 2: TikWM API
        try {
            $post = $this->tikWmChecker->getLatestNonPinnedPost($profileUrl);
            if ($post !== null && ! empty($post['post_id'])) {
                Log::info("TikTokChecker: Berhasil mendeteksi post [{$post['post_id']}] via TIER 2 (TikWM).");

                return $post;
            }
        } catch (\Throwable $e) {
            Log::notice('TikTokChecker: Tier 2 (TikWM) gagal: '.$e->getMessage());
        }

        // 3. TIER 3: Cloudflare Worker Edge Function
        try {
            $post = $this->cloudflareWorkerChecker->getLatestNonPinnedPost($profileUrl);
            if ($post !== null && ! empty($post['post_id'])) {
                Log::info("TikTokChecker: Berhasil mendeteksi post [{$post['post_id']}] via TIER 3 (Cloudflare Worker).");

                return $post;
            }
        } catch (\Throwable $e) {
            Log::notice('TikTokChecker: Tier 3 (Cloudflare Worker) gagal: '.$e->getMessage());
        }

        // 4. TIER 4: Apify Master Token (Fallback Darurat dengan Circuit Breaker)
        try {
            $post = $this->apifyChecker->getLatestNonPinnedPost($profileUrl, $team);
            if ($post !== null && ! empty($post['post_id'])) {
                Log::info("TikTokChecker: Berhasil mendeteksi post [{$post['post_id']}] via TIER 4 (Apify Fallback).");

                return $post;
            }
        } catch (\Throwable $e) {
            Log::error('TikTokChecker: Tier 4 (Apify) gagal: '.$e->getMessage());
        }

        Log::warning("TikTokChecker: Semua tier pengecekan (ZakyApps, TikWM, Cloudflare Worker, Apify) gagal mendeteksi post untuk [{$profileUrl}].");

        return null;
    }
}
