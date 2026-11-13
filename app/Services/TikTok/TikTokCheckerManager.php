<?php

namespace App\Services\TikTok;

use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TikTokCheckerManager
{
    public function __construct(
        protected TikWmChecker $tikWmChecker,
        protected ZakyAppsChecker $zakyAppsChecker,
        protected ApifyChecker $apifyChecker
    ) {}

    /**
     * Ambil 1 postingan TikTok terbaru non-pinned menggunakan strategi 3-Tier Fallback.
     *
     * Tier 1: TikWM API (Publik/Cepat, hemat resource)
     * Tier 2: ZakyApps Microservice (Server mandiri)
     * Tier 3: Apify Master Token (Garda terakhir dengan batasan harian Circuit Breaker)
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
        // 1. TIER 1: TikWM API
        try {
            $post = $this->tikWmChecker->getLatestNonPinnedPost($profileUrl);
            if ($post !== null && ! empty($post['post_id'])) {
                Log::info("TikTokChecker: Berhasil mendeteksi post [{$post['post_id']}] via TIER 1 (TikWM).");

                return $post;
            }
        } catch (\Throwable $e) {
            Log::notice('TikTokChecker: Tier 1 (TikWM) gagal: '.$e->getMessage());
        }

        // 2. TIER 2: ZakyApps Microservice
        try {
            $post = $this->zakyAppsChecker->getLatestNonPinnedPost($profileUrl);
            if ($post !== null && ! empty($post['post_id'])) {
                Log::info("TikTokChecker: Berhasil mendeteksi post [{$post['post_id']}] via TIER 2 (ZakyApps).");

                return $post;
            }
        } catch (\Throwable $e) {
            Log::notice('TikTokChecker: Tier 2 (ZakyApps) gagal: '.$e->getMessage());
        }

        // 3. TIER 3: Apify Master Token (Fallback Darurat dengan Circuit Breaker)
        try {
            $post = $this->apifyChecker->getLatestNonPinnedPost($profileUrl, $team);
            if ($post !== null && ! empty($post['post_id'])) {
                Log::info("TikTokChecker: Berhasil mendeteksi post [{$post['post_id']}] via TIER 3 (Apify Fallback).");

                return $post;
            }
        } catch (\Throwable $e) {
            Log::error('TikTokChecker: Tier 3 (Apify) gagal: '.$e->getMessage());
        }

        Log::warning("TikTokChecker: Semua tier pengecekan (TikWM, ZakyApps, Apify) gagal mendeteksi post untuk [{$profileUrl}].");

        return null;
    }
}
