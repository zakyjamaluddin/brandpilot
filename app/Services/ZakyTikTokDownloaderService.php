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
        array $fallbackImages = []
    ): array {
        Log::info("Memulai ekstraksi media TikTok (Video/Carousel) via Zaky Apps: {$tiktokUrl}");

        try {
            $response = Http::timeout(120)->post($this->endpoint, [
                'url' => $tiktokUrl,
                'download' => true,
            ]);

            if ($response->successful() && ($response->json('ok') ?? false)) {
                $data = $response->json();
                $mediaType = $data['type'] ?? 'video';

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
                    ];
                }

                // ==========================================
                // 2. JIKA KONTEN BERUPA VIDEO (DEFAULT)
                // ==========================================
                $videoDownloadUrl = $data['download']['url'] ?? ($data['video']['url'] ?? null);

                if ($videoDownloadUrl) {
                    $fileContents = Http::timeout(180)->get($videoDownloadUrl)->body();
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
        return $this->downloadFromFallback($tiktokUrl, $fallbackDownloadUrl, $fallbackImages);
    }

    /**
     * Jalankan proses unduh dari fallback metadata jika ZakyApps gagal.
     */
    protected function downloadFromFallback(string $tiktokUrl, ?string $fallbackDownloadUrl, array $fallbackImages): array
    {
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
                return [
                    'type' => 'carousel',
                    'title' => '',
                    'id' => null,
                    'local_gallery' => array_slice($localGallery, 0, 8),
                    'public_gallery' => array_slice($publicGallery, 0, 10),
                    'public_url' => $publicGallery[0] ?? '',
                    'local_path' => $localGallery[0] ?? '',
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

                return [
                    'type' => 'video',
                    'local_path' => $filename,
                    'public_url' => asset('storage/'.$filename),
                    'public_download_url' => $fallbackDownloadUrl,
                    'title' => '',
                    'id' => null,
                    'filesize' => strlen($fileContents),
                ];
            }
        }

        throw new Exception("Semua sumber pengunduh (Zaky Apps dan fallback TikWM) gagal mengekstrak media: {$tiktokUrl}");
    }
}
