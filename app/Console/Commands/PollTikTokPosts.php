<?php

namespace App\Console\Commands;

use App\Jobs\PublishContentJob;
use App\Models\ContentPost;
use App\Models\Team;
use App\Services\GeminiService;
use App\Services\TikTok\TikTokCheckerManager;
use App\Services\ZakyTikTokDownloaderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PollTikTokPosts extends Command
{
    protected $signature = 'brandpilot:poll-tiktok';

    protected $description = 'Polling TikTok (TikWM -> ZakyApps -> Apify Fallback) -> Download Media -> Multimodal Gemini AI -> Jadwalkan repost';

    public function handle(
        TikTokCheckerManager $checker,
        ZakyTikTokDownloaderService $downloader,
        GeminiService $gemini
    ): void {
        set_time_limit(0);

        $this->info('Menjalankan siklus multi-tier polling konten TikTok...');

        // 1. Tarik tenant aktif yang sudah menyetel URL TikTok dan hak kepemilikan
        $teams = Team::where('subscription_status', 'active')
            ->where(function ($query) {
                $query->whereNull('subscription_expires_at')
                    ->orWhere('subscription_expires_at', '>', now());
            })
            ->whereNotNull('tiktok_profile_url')
            ->where('tiktok_ownership_confirmed', true)
            ->get();

        $totalTeams = $teams->count();
        $this->line("Ditemukan {$totalTeams} tenant aktif untuk diperiksa.");

        foreach ($teams as $index => $team) {
            $this->info("Memeriksa akun TikTok untuk Tenant: {$team->name} ({$team->tiktok_profile_url})");

            // 2. Ambil postingan terbaru (melewati pinned post) via 3-Tier Checker
            try {
                $latestPost = $checker->getLatestNonPinnedPost($team->tiktok_profile_url, $team);
            } catch (\Throwable $e) {
                $this->warn("Lewati {$team->name}: ".$e->getMessage());

                continue;
            }

            if (! $latestPost || empty($latestPost['post_id'])) {
                $this->warn("Tidak ada postingan baru yang terdeteksi untuk {$team->name}");

                continue;
            }

            // 3. Cek apakah postingan ini UNIK (belum pernah diproses)
            if ($latestPost['post_id'] === $team->last_tiktok_post_id) {
                $this->line("Postingan ID [{$latestPost['post_id']}] sudah pernah disinkronkan. Lewati.");

                continue;
            }

            $sourceLabel = strtoupper($latestPost['source'] ?? 'CHECKER');
            $this->info("Postingan baru terdeteksi via [{$sourceLabel}]! ID: {$latestPost['post_id']}. Mengunduh media...");

            try {
                // 4. Ekstraksi Media via Zaky Apps (dengan TikWM fallback jika diperlukan)
                $mediaResult = $downloader->downloadMedia(
                    $latestPost['video_url'],
                    $latestPost['direct_download_url'] ?? null,
                    $latestPost['images'] ?? [],
                    $latestPost['cover_url'] ?? null
                );
                $mediaType = $mediaResult['type'] ?? 'video';

                $setting = $team->setting;
                $originalCaption = ! empty($latestPost['caption'])
                    ? $latestPost['caption']
                    : ($mediaResult['title'] ?? '');

                $finalTitle = Str::limit($originalCaption ?: 'Video Rekomendasi Terbaru', 70);
                $finalDescription = $originalCaption;

                // 5. Proses Multimodal Gemini AI
                if ($setting && $setting->auto_caption_enabled) {
                    if ($mediaType === 'carousel') {
                        $this->info('Gemini AI sedang mengamati seluruh slide foto Carousel...');
                        $aiResult = $gemini->generateCaptionFromCarousel(
                            $mediaResult['local_gallery'],
                            $originalCaption,
                            $setting->custom_caption_prompt,
                            $team
                        );
                    } else {
                        $this->info('Gemini AI sedang menonton video...');
                        $aiResult = $gemini->generateCaptionFromVideo(
                            $mediaResult['local_path'],
                            $originalCaption,
                            $setting->custom_caption_prompt,
                            $team
                        );
                    }

                    $finalTitle = $aiResult['title'];
                    $finalDescription = $aiResult['description'];
                }

                // Prioritaskan URL berkas permanen di storage publik BrandPilot agar tidak pernah kedaluwarsa (404)
                $mediaUrlForSocialMedia = ! empty($mediaResult['public_url'])
                    ? $mediaResult['public_url']
                    : ($mediaResult['public_download_url'] ?? '');

                $scheduledAt = now();

                // 6. Simpan Record ContentPost
                $post = ContentPost::create([
                    'team_id' => $team->id,
                    'tiktok_post_id' => $latestPost['post_id'],
                    'title' => $finalTitle,
                    'media_type' => $mediaType,
                    'original_caption' => $originalCaption,
                    'ai_generated_caption' => $finalDescription,
                    'video_url' => $mediaUrlForSocialMedia,
                    'thumbnail_url' => $mediaResult['thumbnail_public_url'] ?? null,
                    'media_gallery' => $mediaResult['public_gallery'] ?? null,
                    'caption_type' => ($setting?->auto_caption_enabled ? 'gemini_ai' : 'manual'),
                    'status' => 'pending',
                    'scheduled_at' => $scheduledAt,
                ]);

                // 7. Kunci ID postingan terakhir agar tidak terproses ganda
                $team->update(['last_tiktok_post_id' => $latestPost['post_id']]);

                // 8. Eksekusi Posting Jika Jadwalnya Sekarang
                if ($scheduledAt->isPast() || $scheduledAt->diffInMinutes(now()) <= 1) {
                    $this->info('Mendistribusikan postingan ke FB, IG, dan YouTube...');
                    dispatch_sync(new PublishContentJob($post));
                } else {
                    $this->info("Postingan dijadwalkan tayang pada: {$scheduledAt->toDateTimeString()}");
                }

            } catch (\Throwable $e) {
                Log::error("Gagal memproses sinkronisasi TikTok [{$latestPost['post_id']}]: ".$e->getMessage());
                $this->error('Terjadi galat: '.$e->getMessage());
            }

            // Jeda 2 detik antar tenant agar tidak membuat burst spike ke server
            if ($index < $totalTeams - 1) {
                sleep(2);
            }
        }

        $this->info('Siklus multi-tier polling TikTok selesai.');
    }
}
