<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ZakyTikTokDownloaderService
{
    protected string $endpoint = 'https://tiktok-downloader.zakyapps.my.id/api/v1/fetch';

    /**
     * Unduh video dari TikTok via Zaky Apps dan simpan ke Storage lokal aplikasi.
     * Mengembalikan path berkas lokal dan informasi media.
     */
    // app/Services/ZakyTikTokDownloaderService.php

    /**
     * Unduh video atau carousel TikTok via Zaky Apps API
     */
    public function downloadMedia(string $tiktokUrl): array
    {
        Log::info("Memulai ekstraksi media TikTok (Video/Carousel) via Zaky Apps: {$tiktokUrl}");

        $response = Http::timeout(120)->post($this->endpoint, [
            'url'      => $tiktokUrl,
            'download' => true,
        ]);

        if (! $response->successful() || ! ($response->json('ok') ?? false)) {
            $errorMsg = $response->json('error.message') ?? 'Gagal mengekstrak media TikTok.';
            Log::error("Zaky Apps Downloader Error: {$errorMsg}");
            throw new Exception("Zaky Apps Downloader: {$errorMsg}");
        }

        $data = $response->json();
        $mediaType = $data['type'] ?? 'video'; // 'video' atau 'carousel'

        // app/Services/ZakyTikTokDownloaderService.php

        // ==========================================
        // 1. JIKA KONTEN BERUPA CAROUSEL (FOTO / ZIP)
        // ==========================================
        // app/Services/ZakyTikTokDownloaderService.php

        if ($mediaType === 'carousel') {
            $zipUrl = $data['zip'] ?? ($data['zip_info']['url'] ?? null);
            $slides = $data['slides'] ?? ($data['images'] ?? []);

            $folderName = 'carousel/' . Str::random(16);
            $targetDir = Storage::disk('public')->path($folderName);
            if (! file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // 1. Ekstrak ZIP secara lokal untuk kebutuhan Gemini AI
            if ($zipUrl) {
                Log::info("Zaky Apps: Mengunduh ZIP untuk Gemini AI: {$zipUrl}");
                $tempZipPath = storage_path('app/temp_' . Str::random(10) . '.zip');
                file_put_contents($tempZipPath, Http::timeout(120)->get($zipUrl)->body());

                $zip = new \ZipArchive();
                if ($zip->open($tempZipPath) === true) {
                    $zip->extractTo($targetDir);
                    $zip->close();
                    @unlink($tempZipPath);
                }
            }

            // Kumpulkan file lokal untuk Gemini
            $localFiles = glob($targetDir . '/*.{jpg,jpeg,png}', GLOB_BRACE);
            $localGallery = array_map(fn($f) => $folderName . '/' . basename($f), $localFiles);

            // 2. KUNCI SUKSES META: Kumpulkan URL Publik Asli dari Zaky Apps (Maksimal 10 foto)
            $publicGallery = [];
            foreach (array_slice($slides, 0, 10) as $s) {
                if (!empty($s['url'])) {
                    $publicGallery[] = $s['url']; // URL https://tiktok-downloader.zakyapps.my.id/dl/...
                }
            }

            // Fallback jika slides kosong
            if (empty($publicGallery)) {
                $publicGallery = array_map(fn($f) => asset('storage/' . $f), $localGallery);
            }

            Log::info("Zaky Apps: Terkumpul " . count($publicGallery) . " URL publik siap kirim ke Meta.");

            return [
                'type'           => 'carousel',
                'title'          => $data['title'] ?? '',
                'id'             => $data['id'] ?? null,
                'local_gallery'  => array_slice($localGallery, 0, 8), // Untuk Gemini
                'public_gallery' => array_slice($publicGallery, 0, 10), // Untuk Meta (Maks 10 foto)
                'public_url'     => $publicGallery[0] ?? '',
                'local_path'     => $localGallery[0] ?? '',
            ];
        }

        // ==========================================
        // 2. JIKA KONTEN BERUPA VIDEO (DEFAULT)
        // ==========================================
        $videoDownloadUrl = $data['download']['url'] ?? ($data['video']['url'] ?? null);

        if (! $videoDownloadUrl) {
            throw new Exception("URL unduhan video tidak ditemukan di respon Zaky Apps.");
        }

        $fileContents = Http::timeout(180)->get($videoDownloadUrl)->body();
        $filename = 'videos/' . Str::random(20) . '.mp4';
        Storage::disk('public')->put($filename, $fileContents);

        return [
            'type'                => 'video',
            'local_path'          => $filename,
            'public_url'          => asset('storage/' . $filename),
            'public_download_url' => $videoDownloadUrl,
            'title'               => $data['title'] ?? '',
            'id'                  => $data['id'] ?? null,
            'filesize'            => $data['download']['bytes'] ?? strlen($fileContents),
        ];
    }
}
