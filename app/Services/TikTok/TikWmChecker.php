<?php

namespace App\Services\TikTok;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TikWmChecker
{
    /**
     * Endpoint API TikWM untuk mengambil daftar post pengguna.
     */
    protected array $endpoints = [
        'https://www.tikwm.com/api/user/posts',
        'https://tikwm.com/api/user/posts',
    ];

    /**
     * Ambil 1 video TikTok terbaru yang tidak disematkan (non-pinned).
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
        $username = $this->extractUsername($profileUrl);

        if (! $username) {
            Log::warning("TikWmChecker: Gagal mengekstrak username dari URL [{$profileUrl}]");

            return null;
        }

        foreach ($this->endpoints as $endpoint) {
            try {
                $response = Http::timeout(12)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                        'Accept' => 'application/json, text/plain, */*',
                    ])
                    ->asForm()
                    ->post($endpoint, [
                        'unique_id' => '@'.ltrim($username, '@'),
                        'count' => 6,
                        'cursor' => 0,
                    ]);

                if (! $response->successful()) {
                    continue;
                }

                $json = $response->json();

                if (! is_array($json) || ($json['code'] ?? -1) !== 0) {
                    continue;
                }

                $videos = $json['data']['videos'] ?? [];

                if (empty($videos) || ! is_array($videos)) {
                    continue;
                }

                // Cari video pertama yang bukan pinned (is_top != 1, pinned != true)
                foreach ($videos as $video) {
                    $isPinned = ! empty($video['is_top'])
                        || ! empty($video['pinned'])
                        || ! empty($video['is_pinned'])
                        || ! empty($video['isPinned']);

                    if ($isPinned) {
                        continue;
                    }

                    $videoId = (string) ($video['video_id'] ?? $video['id'] ?? '');

                    if ($videoId === '') {
                        continue;
                    }

                    $cleanHandle = ltrim($username, '@');
                    $images = $video['images'] ?? [];
                    $isCarousel = ! empty($images);

                    return [
                        'post_id' => $videoId,
                        'caption' => (string) ($video['title'] ?? ''),
                        'video_url' => "https://www.tiktok.com/@{$cleanHandle}/video/{$videoId}",
                        'direct_download_url' => $video['play'] ?? ($video['wmplay'] ?? null),
                        'is_carousel' => $isCarousel,
                        'images' => is_array($images) ? $images : [],
                        'posted_at' => isset($video['create_time'])
                            ? Carbon::createFromTimestamp((int) $video['create_time'])
                            : now(),
                        'source' => 'tikwm',
                    ];
                }
            } catch (Throwable $e) {
                Log::notice("TikWmChecker error pada endpoint [{$endpoint}]: ".$e->getMessage());
            }
        }

        return null;
    }

    /**
     * Ekstrak username TikTok dari URL atau handle string.
     */
    public function extractUsername(string $input): ?string
    {
        $trimmed = trim($input);

        if ($trimmed === '') {
            return null;
        }

        // Jika user hanya memasukkan @username atau username langsung
        if (! str_contains($trimmed, 'tiktok.com')) {
            return ltrim($trimmed, '@');
        }

        // Format: https://www.tiktok.com/@username atau https://tiktok.com/@username/live
        if (preg_match('/@([a-zA-Z0-9_\.\-]+)/', $trimmed, $matches)) {
            return $matches[1];
        }

        // Format tanpa @: https://www.tiktok.com/username
        $path = parse_url($trimmed, PHP_URL_PATH);
        if ($path) {
            $segments = array_values(array_filter(explode('/', $path)));
            if (! empty($segments[0])) {
                return ltrim($segments[0], '@');
            }
        }

        return null;
    }
}
