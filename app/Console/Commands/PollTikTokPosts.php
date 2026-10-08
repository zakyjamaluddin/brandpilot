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

class PollTikTokPosts extends Command
{
    protected $signature = 'brandpilot:poll-tiktok';
    protected $description = 'Scrape TikTok tiap 30 menit (Apify) -> Download via Zaky Apps -> Jadwalkan repost';

    public function handle(
        ApifyService $apify,
        ZakyTikTokDownloaderService $downloader,
        GeminiService $gemini
    ) {
        $this->info("Menjalankan siklus polling konten TikTok...");

        // Tarik tenant aktif yang sudah menyetel URL TikTok dan hak kepemilikan
        $teams = Team::where('subscription_status', 'active')
            ->whereNotNull('tiktok_profile_url')
            ->where('tiktok_ownership_confirmed', true)
            ->get();

        foreach ($teams as $team) {
            $this->info("Memeriksa akun TikTok untuk Tenant: {$team->name}");

            // 1. Ambil video terbaru (bukan pinned) via Apify
            $latestVideo = $apify->getLatestNonPinnedVideo($team->tiktok_profile_url, $team);

            if (! $latestVideo || empty($latestVideo['post_id'])) {
                $this->warn("Tidak ada video yang ditemukan atau gagal scrape untuk {$team->name}");
                continue;
            }

            // 2. Cek apakah video ini UNIK (belum pernah diproses)
            if ($latestVideo['post_id'] === $team->last_tiktok_post_id) {
                $this->line("Video ID [{$latestVideo['post_id']}] sudah pernah disinkronkan. Lewati.");
                continue;
            }

            $this->info("Video baru ditemukan! ID: {$latestVideo['post_id']}. Mengunduh via Zaky Apps...");

            try {
                // 3. Unduh video asli ke server lokal via Zaky Apps Downloader
                $downloadResult = $downloader->downloadVideo($latestVideo['video_url']);

                $setting = $team->setting;
                $originalCaption = !empty($latestVideo['caption']) ? $latestVideo['caption'] : $downloadResult['title'];

                // 4. Proses Auto Caption Gemini AI jika diaktifkan
                $finalCaption = $originalCaption;
                $aiCaption = null;

                if ($setting && $setting->auto_caption_enabled) {
                    $this->info("Men-generate caption baru menggunakan Gemini AI...");
                    $aiCaption = $gemini->generateEnhancedCaption(
                        $originalCaption,
                        $setting->custom_caption_prompt,
                        $team
                    );
                    $finalCaption = $aiCaption;
                }

                // 5. Tentukan jadwal tayang (Dynamic Delay vs Jam Statis)
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

                $videoUrlForSocialMedia = !empty($downloadResult['public_download_url'])
                    ? $downloadResult['public_download_url']
                    : $downloadResult['public_url'];
                // 6. Buat Record ContentPost di database
                $post = ContentPost::create([
                    'team_id'              => $team->id,
                    'tiktok_post_id'       => $latestVideo['post_id'],
                    'original_caption'     => $originalCaption,
                    'ai_generated_caption' => $aiCaption,
                    'video_url'            => $videoUrlForSocialMedia,
                    'caption_type'         => ($aiCaption ? 'gemini_ai' : 'manual'),
                    'status'               => 'pending',
                    'scheduled_at'         => $scheduledAt,
                ]);

                // 7. Update status ID terakhir pada team agar tidak diproses berulang
                $team->update(['last_tiktok_post_id' => $latestVideo['post_id']]);

                // 8. Eksekusi posting jika jadwalnya adalah sekarang
                if ($scheduledAt->isPast() || $scheduledAt->diffInMinutes(now()) <= 1) {
                    $this->info("Mendistribusikan postingan ke FB, IG, dan YouTube...");
                    PublishContentJob::dispatch($post);
                } else {
                    $this->info("Postingan dijadwalkan pada: {$scheduledAt->toDateTimeString()}");
                }

            } catch (\Throwable $e) {
                Log::error("Gagal memproses sinkronisasi video TikTok [{$latestVideo['post_id']}]: " . $e->getMessage());
                $this->error("Terjadi galat: " . $e->getMessage());
            }
        }

        $this->info("Siklus polling TikTok selesai.");
    }
}
