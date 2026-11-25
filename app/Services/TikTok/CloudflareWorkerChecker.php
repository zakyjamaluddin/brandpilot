<?php

namespace App\Services\TikTok;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CloudflareWorkerChecker
{
    /**
     * Ambil 1 video TikTok terbaru non-pinned via Cloudflare Worker edge function.
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
    public function getLatestNonPinnedPost(string $profileUrl): ?array
    {
        $workerUrl = config('services.cloudflare_worker.tiktok_url');

        if (empty($workerUrl)) {
            return null;
        }

        $cleanUsername = ltrim((new TikWmChecker)->extractUsername($profileUrl) ?? '', '@');

        if ($cleanUsername === '') {
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->get($workerUrl, [
                    'username' => $cleanUsername,
                ]);

            if (! $response->successful() || ! ($response->json('ok') ?? false)) {
                return null;
            }

            $data = $response->json('data') ?? $response->json();
            $postId = (string) ($data['post_id'] ?? $data['id'] ?? '');

            if ($postId === '') {
                return null;
            }

            $images = (array) ($data['images'] ?? []);
            $isCarousel = ! empty($data['is_carousel']) || ! empty($images);

            return [
                'post_id' => $postId,
                'caption' => (string) ($data['caption'] ?? $data['title'] ?? ''),
                'video_url' => $data['video_url'] ?? "https://www.tiktok.com/@{$cleanUsername}/video/{$postId}",
                'direct_download_url' => $data['direct_download_url'] ?? null,
                'cover_url' => $data['cover'] ?? ($data['cover_url'] ?? ($data['origin_cover'] ?? null)),
                'is_carousel' => $isCarousel,
                'images' => $images,
                'posted_at' => isset($data['create_time']) ? Carbon::parse($data['create_time']) : now(),
                'source' => 'cloudflare_worker',
            ];
        } catch (Throwable $e) {
            Log::notice('CloudflareWorkerChecker gagal: '.$e->getMessage());

            return null;
        }
    }
}
