<?php

namespace App\Jobs;

use App\Models\ContentPost;
use App\Models\SocialIntegration;
use App\Services\ComposioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ContentPost $post) {}

    public function handle(ComposioService $composio): void
    {
        // Kunci idempoten: hanya satu job yang boleh memproses post ini.
        // Mencegah duplikasi saat poll TikTok dan scheduler memicu di detik yang sama.
        $claimed = ContentPost::whereKey($this->post->id)
            ->where('status', 'pending')
            ->update(['status' => 'processing']);

        if (! $claimed) {
            Log::warning("Publish dilewati: ContentPost #{$this->post->id} sudah diproses job lain.");
            return;
        }

        $this->post->refresh();
        $team = $this->post->team;
        $finalCaption = $this->post->ai_generated_caption ?: $this->post->original_caption;

        // Segarkan status koneksi Composio dulu (akun EXPIRED tidak boleh dipakai)
        $composio->syncConnectedAccounts($team);

        // Ambil akun-akun yang terhubung
        $integrations = $team->hasMany(\App\Models\SocialIntegration::class)
            ->where('is_connected', true)
            ->get();

        if ($integrations->isEmpty()) {
            Log::warning("Tidak ada sosmed yang terhubung untuk memposting Tenant [{$team->name}]");
            $this->post->update(['status' => 'failed']);
            return;
        }

        $publishedPlatforms = [];
        // Ambil ID sosmed yang mungkin sudah tersimpan sebelumnya
        $platformPostIds = (array) ($this->post->platform_post_ids ?? []);

        // app/Jobs/PublishContentJob.php

        $postTitle        = $this->post->title ?: \Illuminate\Support\Str::limit($this->post->original_caption, 70);
        $finalDescription = $this->post->ai_generated_caption ?: $this->post->original_caption;
        $isCarousel = ($this->post->media_type === 'carousel');
        $gallery    = (array) ($this->post->media_gallery ?? []);

        $isCarousel = ($this->post->media_type === 'carousel');
        $gallery    = (array) ($this->post->media_gallery ?? []);

        foreach ($integrations as $integration) {
            // Jangan pernah memposting ulang platform yang sudah berhasil
            if (! empty($platformPostIds[$integration->platform])) {
                Log::info("Lewati {$integration->platform}: sudah terbit sebelumnya (" . $platformPostIds[$integration->platform] . ').');
                continue;
            }

            try {
                $connId = $integration->composio_connection_id;

                // ==========================================
                // 1. FACEBOOK: NATIVE VIDEO vs MULTI-PHOTO ALBUM
                // ==========================================
                if ($integration->platform === 'facebook') {
                    $pageId = $integration->metadata['page_id'] ?? null;

                    // Validasi: page_id wajib ada di daftar halaman koneksi ini.
                    // Mencegah posting lintas tenant bila koneksi menunjuk page brand lain.
                    $availablePages = $composio->facebookPages($team, $connId);
                    $availableIds = array_column($availablePages, 'id');

                    if ($pageId && ! in_array((string) $pageId, $availableIds, true)) {
                        Log::error('Facebook: page_id ' . $pageId . ' tidak lagi dapat diakses koneksi ' . $connId . '. Publikasi dibatalkan agar tidak salah halaman.', [
                            'available_pages' => $availablePages,
                        ]);

                        continue;
                    }

                    if (! $pageId) {
                        // Pilih halaman pertama yang belum dipakai tenant lain (cegah lintas brand)
                        $usedByOthers = SocialIntegration::where('platform', 'facebook')
                            ->where('team_id', '!=', $team->id)
                            ->get()
                            ->map(fn ($i) => (string) ($i->metadata['page_id'] ?? ''))
                            ->filter()
                            ->all();

                        foreach ($availablePages as $candidate) {
                            if (! in_array($candidate['id'], $usedByOthers, true)) {
                                $pageId = $candidate['id'];
                                $meta = (array) ($integration->metadata ?? []);
                                $meta['page_id'] = $pageId;
                                $meta['page_name'] = $candidate['name'];
                                $integration->update(['metadata' => $meta]);
                                Log::info('Facebook: page_id ditetapkan otomatis ke ' . $pageId . ' (' . $candidate['name'] . ').');
                                break;
                            }
                        }

                        if (! $pageId) {
                            Log::error('Facebook: semua halaman pada koneksi ' . $connId . ' sudah dipakai tenant lain. Hubungkan halaman khusus untuk tenant ini.');
                            continue;
                        }
                    }

                    if (! $pageId) {
                        Log::error('Facebook: page_id tidak ditemukan untuk koneksi ' . $connId . '. Halaman FB belum diberikan izin MANAGE/CREATE_CONTENT.');
                        continue;
                    }

                    if ($isCarousel && count($gallery) > 1) {
                        // UNGGAH SEMUA FOTO SEBAGAI MULTI-PHOTO POST DI FACEBOOK
                        Log::info("Facebook: Memulai publikasi multi-photo (" . count($gallery) . " foto)...");
                        $res = $composio->postFacebookCarouselAlbum(
                            $team,
                            $gallery, // Berisi URL publik Zaky Apps
                            $postTitle,
                            $finalDescription,
                            $pageId,
                            $connId
                        );
                    } elseif ($isCarousel && count($gallery) === 1) {
                        // Single Photo Post
                        $res = $composio->executeAction($team, 'FACEBOOK_CREATE_PHOTO_POST', [
                            'page_id'   => (string) $pageId,
                            'url'       => $gallery[0],
                            'message'   => "{$postTitle}\n\n{$finalDescription}",
                            'published' => true,
                        ], $connId);
                    } else {
                        // Native Video Post
                        $res = $composio->executeAction($team, 'FACEBOOK_CREATE_VIDEO_POST', [
                            'page_id'     => (string) $pageId,
                            'file_url'    => $this->post->video_url,
                            'title'       => $postTitle,
                            'description' => $finalDescription,
                            'published'   => true,
                        ], $connId);
                    }

                    if ($res['success']) {
                        $platformPostIds['facebook'] = (string) ($res['data']['data']['id'] ?? ($res['data']['id'] ?? 'fb_success'));
                        $publishedPlatforms['facebook'] = $platformPostIds['facebook'];
                        $this->post->update([
                            'status'            => 'published',
                            'published_at'      => now(),
                            'platform_post_ids' => $platformPostIds,
                        ]);
                        Log::info("✓ Facebook terbit dengan sukses!");
                    }
                }

                // ==========================================
                // 2. INSTAGRAM: REELS vs CAROUSEL (3-STEP)
                // ==========================================
                elseif ($integration->platform === 'instagram') {
                    if ($isCarousel && count($gallery) >= 2) {
                        // UNGGAH SEMUA FOTO SEBAGAI CAROUSEL DI INSTAGRAM
                        Log::info("Instagram: Memulai alur 3-langkah Instagram Carousel (" . count($gallery) . " foto)...");
                        $res = $composio->postInstagramCarousel(
                            $team,
                            $gallery, // Berisi URL publik Zaky Apps
                            $finalDescription,
                            $connId
                        );
                    } else {
                        // Native Reel Video Post
                        $res = $composio->postInstagramReel(
                            $team,
                            $this->post->video_url,
                            $finalDescription,
                            $connId
                        );
                    }

                    if ($res['success']) {
                        $platformPostIds['instagram'] = (string) ($res['data']['data']['id'] ?? ($res['data']['id'] ?? 'ig_success'));
                        $publishedPlatforms['instagram'] = $platformPostIds['instagram'];
                        $this->post->update([
                            'status'            => 'published',
                            'published_at'      => now(),
                            'platform_post_ids' => $platformPostIds,
                        ]);
                        Log::info("✓ Instagram Carousel terbit dengan sukses!");
                    }
                }


                                // ==========================================
                // 3. YOUTUBE SHORTS UPLOAD
                // ==========================================
                elseif ($integration->platform === 'youtube') {
                    if ($isCarousel) {
                        Log::info("YouTube: Dilewati untuk konten Carousel (YouTube Shorts hanya mendukung video).");
                        continue;
                    }

                    Log::info("YouTube: Mempersiapkan upload video Shorts...");

                    // Ekstrak tag otomatis dari caption atau gunakan default
                    preg_match_all('/#(\w+)/', $finalCaption, $matches);
                    $tags = ! empty($matches[1]) ? array_slice($matches[1], 0, 8) : ['Shorts', 'Viral', 'BrandPilot'];

                    // Auth dari Composio, byte video dikirim langsung ke YouTube (resumable upload)
                    $res = $composio->uploadYoutubeVideo($team, $this->post->video_url, [
                        'title'       => substr($postTitle, 0, 95),
                        'description' => $finalDescription,
                        'tags'          => $tags,
                        'categoryId'    => '22', // People & Blogs
                        'privacyStatus' => 'public',
                        'mimetype'      => 'video/mp4',
                    ], $connId);

                    if ($res['success']) {
                        $publishedPlatforms['youtube'] = (string) $res['video_id'];
                         $platformPostIds['youtube'] = (string) $res['video_id'];
                        $this->post->update([
                            'status'            => 'published',
                            'published_at'      => now(),
                            'platform_post_ids' => $platformPostIds,
                        ]);

                        // Pastikan YouTube tidak membuang videonya (transcode gagal)
                        dispatch(function () use ($composio, $team, $res, $connId) {
                            sleep(45);
                            $status = $composio->youtubeProcessingStatus($team, $res['video_id'], $connId);
                            Log::info("YouTube: status pemrosesan {$res['video_id']} = " . ($status ?? 'HILANG'));
                        })->delay(now()->addSeconds(30));


                    } else {
                        // Jika respon mengandung error kuota 429 RESOURCE_EXHAUSTED:
                        if (isset($response['error']['code']) && $response['error']['code'] == 429) {
                            \Illuminate\Support\Facades\Log::warning("YouTube Quota Harian Habis (Reset jam 15.00 WIB). Postingan dijadwalkan ulang.");

                            // Jadwalkan ulang postingan ke jam reset kuota berikutnya (jam 15:00 WIB)
                            $this->post->update([
                                'status'       => 'pending',
                                'scheduled_at' => now()->isAfter(now()->setTime(15, 0))
                                    ? now()->addDay()->setTime(15, 5)
                                    : now()->setTime(15, 5),
                            ]);

                            return;
                        }
                        Log::error('YouTube: upload gagal', ['error' => $res['error'] ?? 'unknown']);
                    }
                }

            } catch (\Throwable $e) {
                Log::error("Gagal publish ke {$integration->platform}: " . $e->getMessage());
            }
        }
        // Status akhir: 'published' hanya jika ada platform yang benar-benar terbit
        if (! empty($publishedPlatforms)) {
            $this->post->update(['status' => 'published', 'published_at' => now()]);
        } elseif (empty($platformPostIds)) {
            $this->post->update(['status' => 'failed']);
        } else {
            // Semua platform sudah terbit pada proses sebelumnya
            $this->post->update(['status' => 'published']);
        }

        if (! empty($publishedPlatforms)) {
            // Push hanya saat ada publikasi baru — jangan kirim "sukses" palsu
            foreach ($team->members as $member) {
                $member->notify(new \App\Notifications\ContentPublishedPushNotification($this->post));
            }
        }

        Log::info("Publish selesai untuk ContentPost #{$this->post->id}. Berhasil ke: " . implode(', ', $publishedPlatforms));
    }
}
