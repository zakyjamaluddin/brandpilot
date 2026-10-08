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
    public function downloadVideo(string $tiktokUrl): array
    {
        Log::info("Memulai ekstraksi video TikTok via Zaky Apps API: {$tiktokUrl}");

        // 1. Kirim permintaan ke API Zaky Apps
        $response = Http::timeout(120)->post($this->endpoint, [
            'url'      => $tiktokUrl,
            'download' => true,
        ]);

        if (! $response->successful() || ! ($response->json('ok') ?? false)) {
            $errorMsg = $response->json('error.message') ?? 'Gagal mengekstrak video TikTok.';
            Log::error("Zaky Apps Downloader Error: {$errorMsg}");
            throw new Exception("Zaky Apps Downloader: {$errorMsg}");
        }

        $data = $response->json();
        $videoDownloadUrl = $data['download']['url'] ?? ($data['video']['url'] ?? null);

        if (! $videoDownloadUrl) {
            throw new Exception("URL unduhan video tidak ditemukan di respon Zaky Apps.");
        }

        // 2. Unduh konten binary MP4 dan simpan ke disk lokal
        $fileContents = Http::timeout(180)->get($videoDownloadUrl)->body();
        if (empty($fileContents)) {
            throw new Exception("Gagal mengunduh isi binary video dari server Zaky Apps.");
        }

        $filename = 'videos/' . Str::random(20) . '.mp4';
        Storage::disk('public')->put($filename, $fileContents);

        Log::info("Video TikTok berhasil diunduh dan disimpan ke disk: {$filename}");

        return [
            'local_path' => $filename,
            'public_url' => asset('storage/' . $filename),
            'public_download_url'  => $videoDownloadUrl, // URL publik milik domain Zaky Apps
            'title'      => $data['title'] ?? '',
            'id'         => $data['id'] ?? null,
            'filesize'   => $data['download']['bytes'] ?? strlen($fileContents),
        ];
    }
}
