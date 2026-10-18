<?php

namespace App\Console\Commands;

use App\Jobs\PublishContentJob;
use App\Models\ContentPost;
use App\Models\Team;
use App\Services\ApifyService;
use App\Services\GeminiService;
use App\Services\ZakyTikTokDownloaderService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PollTikTokPosts extends Command
{
    protected $signature = 'brandpilot:poll-tiktok';
    protected $description = 'Scrape TikTok tiap 30 menit (Apify) -> Download via Zaky Apps -> Multimodal Gemini -> Jadwalkan repost';

    public function handle(
        ApifyService $apify,
        ZakyTikTokDownloaderService $downloader,
        GeminiService $gemini
    ) {
        $this->info("Menjalankan siklus polling konten TikTok...");

        // 1. Tarik tenant aktif yang sudah menyetel URL TikTok dan hak kepemilikan
        // $teams = Team::where('subscription_status', 'active')
        //     ->whereNotNull('tiktok_profile_url')
        //     ->where('tiktok_ownership_confirmed', true)
        //     ->get();
        $teams = Team::where('subscription_status', 'active')
            ->where(function ($query) {
                $query->whereNull('subscription_expires_at')
                      ->orWhere('subscription_expires_at', '>', now());
            })
            ->whereNotNull('tiktok_profile_url')
            ->where('tiktok_ownership_confirmed', true)
            ->get();

        foreach ($teams as $team) {
            $this->info("Memeriksa akun TikTok untuk Tenant: {$team->name}");

            // 2. Ambil video/carousel terbaru (melewati pinned post) via Apify
            $latestVideo = $apify->getLatestNonPinnedVideo($team->tiktok_profile_url, $team);

            if (! $latestVideo || empty($latestVideo['post_id'])) {
                $this->warn("Tidak ada postingan baru yang ditemukan atau gagal scrape untuk {$team->name}");
                continue;
            }

            // 3. Cek apakah postingan ini UNIK (belum pernah diproses)
            if ($latestVideo['post_id'] === $team->last_tiktok_post_id) {
                $this->line("Postingan ID [{$latestVideo['post_id']}] sudah pernah disinkronkan. Lewati.");
                continue;
            }

            $this->info("Postingan baru terdeteksi! ID: {$latestVideo['post_id']}. Mengunduh via Zaky Apps...");

            try {
                // 4. Ekstraksi via Zaky Apps (Otomatis mendeteksi Video atau Carousel)
                $mediaResult = $downloader->downloadMedia($latestVideo['video_url']);
                $mediaType   = $mediaResult['type'] ?? 'video';

                $setting = $team->setting;
                $originalCaption = !empty($latestVideo['caption']) ? $latestVideo['caption'] : ($mediaResult['title'] ?? '');

                $finalTitle       = Str::limit($originalCaption ?: 'Video Rekomendasi Terbaru', 70);
                $finalDescription = $originalCaption;

                // 5. Proses Multimodal Gemini AI (Membaca Gambar Carousel atau Menonton Video)
                if ($setting && $setting->auto_caption_enabled) {
                    if ($mediaType === 'carousel') {
                        $this->info("Gemini AI sedang mengamati seluruh slide foto Carousel...");
                        $aiResult = $gemini->generateCaptionFromCarousel(
                            $mediaResult['local_gallery'],
                            $originalCaption,
                            $setting->custom_caption_prompt,
                            $team
                        );
                    } else {
                        $this->info("Gemini AI sedang menonton video...");
                        $aiResult = $gemini->generateCaptionFromVideo(
                            $mediaResult['local_path'],
                            $originalCaption,
                            $setting->custom_caption_prompt,
                            $team
                        );
                    }

                    $finalTitle       = $aiResult['title'];
                    $finalDescription = $aiResult['description'];
                }

                // URL yang akan dikirim ke media sosial
                $mediaUrlForSocialMedia = !empty($mediaResult['public_download_url']) 
                    ? $mediaResult['public_download_url'] 
                    : ($mediaResult['public_url'] ?? '');

                // 6. Tentukan Jadwal Tayang (Dynamic Delay vs Jam Statis)
                $scheduledAt = now();
                if ($setting && $setting->schedule_mode === 'static_fixed' && !empty($setting->static_posting_hours)) {
                    $hours = $setting->static_posting_hours;
                    $nextSlot = Carbon::today()->setTimeFromTimeString($hours[0]);
                    
                    if ($nextSlot->isPast()) {
                        $nextSlot = (count($hours) > 1 && Carbon::today()->setTimeFromTimeString($hours[1])->isFuture())
                            ? Carbon::today()->setTimeFromTimeString($hours[1])
                            : Carbon::tomorrow()->setTimeFromTimeString($hours[0]);
                    }
                    $scheduledAt = $nextSlot;
                }

                // 7. Simpan Record ContentPost
                $post = ContentPost::create([
                    'team_id'              => $team->id,
                    'tiktok_post_id'       => $latestVideo['post_id'],
                    'title'                => $finalTitle,
                    'media_type'           => $mediaType,
                    'original_caption'     => $originalCaption,
                    'ai_generated_caption' => $finalDescription,
                    'video_url'            => $mediaUrlForSocialMedia,
                    'media_gallery'        => $mediaResult['public_gallery'] ?? null,
                    'caption_type'         => ($setting?->auto_caption_enabled ? 'gemini_ai' : 'manual'),
                    'status'               => 'pending',
                    'scheduled_at'         => $scheduledAt,
                ]);

                // 8. Kunci ID postingan terakhir agar tidak terproses ganda
                $team->update(['last_tiktok_post_id' => $latestVideo['post_id']]);

                // 9. Eksekusi Posting Jika Jadwalnya Sekarang
                if ($scheduledAt->isPast() || $scheduledAt->diffInMinutes(now()) <= 1) {
                    $this->info("Mendistribusikan postingan ke FB, IG, dan YouTube...");
                    PublishContentJob::dispatch($post);
                } else {
                    $this->info("Postingan dijadwalkan tayang pada: {$scheduledAt->toDateTimeString()}");
                }

            } catch (\Throwable $e) {
                Log::error("Gagal memproses sinkronisasi TikTok [{$latestVideo['post_id']}]: " . $e->getMessage());
                $this->error("Terjadi galat: " . $e->getMessage());
            }
        }

        $this->info("Siklus polling TikTok selesai.");
    }
}