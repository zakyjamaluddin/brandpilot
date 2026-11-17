<?php

namespace App\Services\TikTok;

use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TikTokCheckerManager
{
    public function __construct(
        protected ZakyAppsChecker $zakyAppsChecker,
        protected ApifyChecker $apifyChecker
    ) {}

    /**
     * Ambil 1 postingan TikTok terbaru non-pinned menggunakan strategi Dual-Tier Fallback.
     *
     * Tier 1: ZakyApps Microservice (Direct dengan Rotating Webshare Proxy, 100% Free, Anti-Ban)
     * Tier 2: Apify Master Token (Garda terakhir dengan Circuit Breaker hemat kuota)
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
        // 1. TIER 1: ZakyApps Microservice (Rotating Webshare Proxy yt-dlp)
        try {
            $post = $this->zakyAppsChecker->getLatestNonPinnedPost($profileUrl);
            if ($post !== null && ! empty($post['post_id'])) {
                Log::info("TikTokChecker: Berhasil mendeteksi post [{$post['post_id']}] via TIER 1 (ZakyApps Proxy).");

                return $post;
            }
        } catch (\Throwable $e) {
            Log::notice('TikTokChecker: Tier 1 (ZakyApps) gagal: '.$e->getMessage());
        }

        // 2. TIER 2: Apify Master Token (Fallback Cadangan dengan Circuit Breaker)
        try {
            $post = $this->apifyChecker->getLatestNonPinnedPost($profileUrl, $team);
            if ($post !== null && ! empty($post['post_id'])) {
                Log::info("TikTokChecker: Berhasil mendeteksi post [{$post['post_id']}] via TIER 2 (Apify Fallback).");

                return $post;
            }
        } catch (\Throwable $e) {
            Log::error('TikTokChecker: Tier 2 (Apify) gagal: '.$e->getMessage());
        }

        Log::warning("TikTokChecker: Semua tier pengecekan (ZakyApps & Apify) gagal mendeteksi post untuk [{$profileUrl}].");

        return null;
    }
}
