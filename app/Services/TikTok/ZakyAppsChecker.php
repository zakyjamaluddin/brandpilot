<?php

namespace App\Services\TikTok;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ZakyAppsChecker
{
    /**
     * Endpoint Zaky Apps TikTok User Post Checker.
     */
    protected string $endpoint = 'https://tiktok-downloader.zakyapps.my.id/api/v1/user/latest';

    /**
     * Ambil 1 video TikTok terbaru non-pinned via microservice Zaky Apps.
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
        $cleanUsername = ltrim((new TikWmChecker)->extractUsername($profileUrl) ?? '', '@');

        if ($cleanUsername === '') {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->post($this->endpoint, [
                    'username' => $cleanUsername,
                    'url' => $profileUrl,
                    'count' => 5,
                ]);

            if (! $response->successful() || ! ($response->json('ok') ?? false)) {
                return null;
            }

            $json = $response->json();
            $data = $json['data'] ?? $json;

            // Mendukung respons berbentuk daftar video/post atau objek tunggal
            $items = $data['videos'] ?? ($data['posts'] ?? (isset($data[0]) ? $data : [$data]));

            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $isPinned = ! empty($item['is_pinned'])
                    || ! empty($item['isPinned'])
                    || ! empty($item['is_top'])
                    || ! empty($item['pinned']);

                if ($isPinned) {
                    continue;
                }

                $postId = (string) ($item['id'] ?? $item['post_id'] ?? $item['video_id'] ?? '');

                if ($postId === '') {
                    continue;
                }

                $isCarousel = ($item['type'] ?? '') === 'carousel' || ! empty($item['images']) || ! empty($item['slides']);

                return [
                    'post_id' => $postId,
                    'caption' => (string) ($item['caption'] ?? $item['title'] ?? ''),
                    'video_url' => $item['url'] ?? $item['video_url'] ?? "https://www.tiktok.com/@{$cleanUsername}/video/{$postId}",
                    'direct_download_url' => $item['download_url'] ?? $item['download']['url'] ?? null,
                    'is_carousel' => $isCarousel,
                    'images' => $item['slides'] ?? ($item['images'] ?? []),
                    'posted_at' => isset($item['create_time']) ? Carbon::parse($item['create_time']) : now(),
                    'source' => 'zakyapps',
                ];
            }

            return null;
        } catch (Throwable $e) {
            Log::notice('ZakyAppsChecker belum tersedia / error: '.$e->getMessage());

            return null;
        }
    }
}
