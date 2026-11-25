<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ZakyTikTokDownloaderService
{
    protected string $endpoint = 'https://tiktok-downloader.zakyapps.my.id/api/v1/fetch';

    /**
     * Unduh video atau carousel TikTok via Zaky Apps API dengan TikWM fallback.
     */
    public function downloadMedia(
        string $tiktokUrl,
        ?string $fallbackDownloadUrl = null,
        array $fallbackImages = [],
        ?string $fallbackCoverUrl = null
    ): array {
        Log::info("Memulai ekstraksi media TikTok (Video/Carousel) via Zaky Apps: {$tiktokUrl}");

        try {
            $response = Http::timeout(600)->post($this->endpoint, [
                'url' => $tiktokUrl,
                'download' => true,
                'upscale' => true,
            ]);

            if ($response->successful() && ($response->json('ok') ?? false)) {
                $data = $response->json();
                $mediaType = $data['type'] ?? 'video';
                $coverCandidate = $data['cover'] ?? ($data['thumbnail'] ?? ($data['origin_cover'] ?? $fallbackCoverUrl));
                $thumbResult = $this->downloadThumbnail($coverCandidate);

                // ==========================================
                // 1. JIKA KONTEN BERUPA CAROUSEL (FOTO / ZIP)
                // ==========================================
                if ($mediaType === 'carousel') {
                    $zipUrl = $data['zip'] ?? ($data['zip_info']['url'] ?? null);
                    $slides = $data['slides'] ?? ($data['images'] ?? []);

                    $folderName = 'carousel/'.Str::random(16);
                    $targetDir = Storage::disk('public')->path($folderName);
                    if (! file_exists($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }

                    // 1. Ekstrak ZIP secara lokal untuk kebutuhan Gemini AI
                    if ($zipUrl) {
                        Log::info("Zaky Apps: Mengunduh ZIP untuk Gemini AI: {$zipUrl}");
                        $tempZipPath = storage_path('app/temp_'.Str::random(10).'.zip');
                        file_put_contents($tempZipPath, Http::timeout(120)->get($zipUrl)->body());

                        $zip = new \ZipArchive;
                        if ($zip->open($tempZipPath) === true) {
                            $zip->extractTo($targetDir);
                            $zip->close();
                            @unlink($tempZipPath);
                        }
                    }

                    // Kumpulkan file lokal untuk Gemini
                    $localFiles = glob($targetDir.'/*.{jpg,jpeg,png}', GLOB_BRACE);
                    $localGallery = array_map(fn ($f) => $folderName.'/'.basename($f), $localFiles);

                    // 2. KUNCI SUKSES META: Kumpulkan URL Publik Asli dari Zaky Apps (Maksimal 10 foto)
                    $publicGallery = [];
                    foreach (array_slice($slides, 0, 10) as $s) {
                        if (! empty($s['url'])) {
                            $publicGallery[] = $s['url'];
                        }
                    }

                    if (empty($publicGallery)) {
                        $publicGallery = array_map(fn ($f) => asset('storage/'.$f), $localGallery);
                    }

                    Log::info('Zaky Apps: Terkumpul '.count($publicGallery).' URL publik siap kirim ke Meta.');

                    return [
                        'type' => 'carousel',
                        'title' => $data['title'] ?? '',
                        'id' => $data['id'] ?? null,
                        'local_gallery' => array_slice($localGallery, 0, 8),
                        'public_gallery' => array_slice($publicGallery, 0, 10),
                        'public_url' => $publicGallery[0] ?? '',
                        'local_path' => $localGallery[0] ?? '',
                        'thumbnail_local_path' => $thumbResult['local_path'] ?? null,
                        'thumbnail_public_url' => $thumbResult['public_url'] ?? ($publicGallery[0] ?? null),
                    ];
                }

                // ==========================================
                // 2. JIKA KONTEN BERUPA VIDEO (DEFAULT)
                // ==========================================
                $videoDownloadUrl = $data['video_1080p']['url'] ?? ($data['download']['url'] ?? ($data['video']['url'] ?? null));
                $is1080p = ! empty($data['video_1080p']['url']) || (($data['download']['quality'] ?? '') === '1080p');

                if ($videoDownloadUrl) {
                    Log::info('Zaky Apps: Mengunduh video '.($is1080p ? '1080p HD' : 'Original').": {$videoDownloadUrl}");
                    $fileContents = Http::timeout(300)->get($videoDownloadUrl)->body();
                    $filename = 'videos/'.Str::random(20).'.mp4';
                    Storage::disk('public')->put($filename, $fileContents);

                    return [
                        'type' => 'video',
                        'local_path' => $filename,
                        'public_url' => asset('storage/'.$filename),
                        'public_download_url' => $videoDownloadUrl,
                        'title' => $data['title'] ?? '',
                        'id' => $data['id'] ?? null,
                        'filesize' => $data['download']['bytes'] ?? strlen($fileContents),
                        'is_1080p' => $is1080p,
                        'thumbnail_local_path' => $thumbResult['local_path'] ?? null,
                        'thumbnail_public_url' => $thumbResult['public_url'] ?? null,
                    ];
                }
            }

            Log::warning('Zaky Apps Downloader gagal memberikan URL media, beralih ke fallback TikWM jika tersedia.');
        } catch (Throwable $e) {
            Log::warning('Zaky Apps Downloader Exception: '.$e->getMessage().'. Beralih ke fallback...');
        }

        // ==========================================
        // 3. FALLBACK: TIKWM DIRECT URL / SLIDES
        // ==========================================
        return $this->downloadFromFallback($tiktokUrl, $fallbackDownloadUrl, $fallbackImages, $fallbackCoverUrl);
    }

    /**
     * Jalankan proses unduh dari fallback metadata jika ZakyApps gagal.
     */
    protected function downloadFromFallback(
        string $tiktokUrl,
        ?string $fallbackDownloadUrl,
        array $fallbackImages,
        ?string $fallbackCoverUrl = null
    ): array {
        // 1. Fallback Carousel
        if (! empty($fallbackImages)) {
            Log::info('Downloader Fallback: Mengunduh foto carousel dari metadata TikWM...');
            $folderName = 'carousel/'.Str::random(16);
            $targetDir = Storage::disk('public')->path($folderName);
            if (! file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $localGallery = [];
            $publicGallery = [];
            foreach (array_slice($fallbackImages, 0, 10) as $index => $imgUrl) {
                if (empty($imgUrl)) {
                    continue;
                }
                $imgContents = Http::timeout(60)->get($imgUrl)->body();
                $imgFilename = $folderName.'/slide_'.($index + 1).'.jpg';
                Storage::disk('public')->put($imgFilename, $imgContents);
                $localGallery[] = $imgFilename;
                $publicGallery[] = asset('storage/'.$imgFilename);
            }

            if (! empty($localGallery)) {
                $thumbResult = $this->downloadThumbnail($fallbackCoverUrl);

                return [
                    'type' => 'carousel',
                    'title' => '',
                    'id' => null,
                    'local_gallery' => array_slice($localGallery, 0, 8),
                    'public_gallery' => array_slice($publicGallery, 0, 10),
                    'public_url' => $publicGallery[0] ?? '',
                    'local_path' => $localGallery[0] ?? '',
                    'thumbnail_local_path' => $thumbResult['local_path'] ?? null,
                    'thumbnail_public_url' => $thumbResult['public_url'] ?? ($publicGallery[0] ?? null),
                ];
            }
        }

        // 2. Fallback Video
        if (! empty($fallbackDownloadUrl)) {
            Log::info('Downloader Fallback: Mengunduh binary video dari TikWM direct URL: '.$fallbackDownloadUrl);
            $fileContents = Http::timeout(180)->get($fallbackDownloadUrl)->body();
            if (! empty($fileContents)) {
                $filename = 'videos/'.Str::random(20).'.mp4';
                Storage::disk('public')->put($filename, $fileContents);

                $thumbResult = $this->downloadThumbnail($fallbackCoverUrl);

                return [
                    'type' => 'video',
                    'local_path' => $filename,
                    'public_url' => asset('storage/'.$filename),
                    'public_download_url' => $fallbackDownloadUrl,
                    'title' => '',
                    'id' => null,
                    'filesize' => strlen($fileContents),
                    'thumbnail_local_path' => $thumbResult['local_path'] ?? null,
                    'thumbnail_public_url' => $thumbResult['public_url'] ?? null,
                ];
            }
        }

        throw new Exception("Semua sumber pengunduh (Zaky Apps dan fallback TikWM) gagal mengekstrak media: {$tiktokUrl}");
    }

    /**
     * Unduh dan konversi gambar cover/thumbnail menjadi JPEG standar di storage publik.
     *
     * @return array{local_path: string, relative_path: string, public_url: string}|null
     */
    public function downloadThumbnail(?string $coverUrl): ?array
    {
        if (empty($coverUrl)) {
            return null;
        }

        try {
            Log::info("Mengunduh thumbnail dari URL: {$coverUrl}");
            $res = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                    'Referer' => 'https://www.tiktok.com/',
                ])
                ->get($coverUrl);

            if (! $res->successful() || empty($res->body())) {
                Log::warning("Gagal mengunduh thumbnail dari [{$coverUrl}]: HTTP {$res->status()}");

                return null;
            }

            $rawBody = $res->body();
            $jpegData = null;

            // Normalisasi ke JPEG standar menggunakan GD jika tersedia (kompatibel Meta & YouTube)
            if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
                $img = @imagecreatefromstring($rawBody);
                if ($img !== false) {
                    ob_start();
                    imagejpeg($img, null, 90);
                    $jpegData = ob_get_clean();
                    imagedestroy($img);
                }
            }

            $finalData = $jpegData ?: $rawBody;
            $filename = 'thumbnails/'.Str::random(20).'.jpg';
            Storage::disk('public')->put($filename, $finalData);

            Log::info("Thumbnail berhasil disimpan: {$filename}");

            return [
                'local_path' => Storage::disk('public')->path($filename),
                'relative_path' => $filename,
                'public_url' => asset('storage/'.$filename),
            ];
        } catch (Throwable $e) {
            Log::warning('Gagal memproses thumbnail: '.$e->getMessage());

            return null;
        }
    }
}
