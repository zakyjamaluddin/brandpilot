<?php

namespace App\Jobs;

use App\Models\ContentPost;
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
        $team = $this->post->team;
        $finalCaption = $this->post->ai_generated_caption ?: $this->post->original_caption;

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

        // app/Jobs/PublishContentJob.php

        foreach ($integrations as $integration) {
            try {
                $connId = $integration->composio_connection_id;

                // ==========================================
                // 1. FACEBOOK (Auto-Detect & Simpan page_id)
                // ==========================================
                if ($integration->platform === 'facebook') {
                    $pageId = $integration->metadata['page_id'] ?? null;

                    // Jika belum ada page_id di database, tarik otomatis dari Facebook
                    if (! $pageId) {
                        $pagesRes = $composio->executeAction($team, 'FACEBOOK_LIST_MANAGED_PAGES', [], $connId);
                        $pages = $pagesRes['data']['data']['data']
                            ?? ($pagesRes['data']['data']
                            ?? ($pagesRes['data']['response_data'] ?? []));

                        $pageId = $pages[0]['id'] ?? null;

                        if ($pageId) {
                            $meta = $integration->metadata ?? [];
                            $meta['page_id'] = (string) $pageId;
                            $integration->update(['metadata' => $meta]);
                        }
                    }

                    if (! $pageId) {
                        throw new \Exception("Tidak dapat menemukan Facebook Page ID untuk akun ini.");
                    }

                    // Eksekusi posting dengan menyertakan page_id
                    $res = $composio->executeAction($team, 'FACEBOOK_CREATE_POST', [
                        'page_id' => (string) $pageId,
                        'message' => $finalCaption,
                        'link'    => $this->post->video_url,
                    ], $connId);

                    $fbPostId = $res['data']['data']['id'] ?? ($res['data']['id'] ?? null);
                    if ($res['success']) {
                        $publishedPlatforms['facebook'] = (string) ($fbPostId ?: 'fb_success');
                    }
                }

                // ==========================================
                // 2. INSTAGRAM REEL
                // ==========================================
                elseif ($integration->platform === 'instagram') {
                    $res = $composio->postInstagramReel(
                        $team,
                        $this->post->video_url,
                        $finalCaption,
                        $connId
                    );

                    if ($res['success']) {
                        $igId = $res['data']['data']['id'] ?? ($res['data']['id'] ?? 'ig_success');
                        $publishedPlatforms['instagram'] = (string) $igId;
                    }
                }

                // ==========================================
                // 3. YOUTUBE SHORTS
                // ==========================================
                elseif ($integration->platform === 'youtube') {
                    $res = $composio->executeAction($team, 'YOUTUBE_UPLOAD_VIDEO', [
                        'title'       => substr($finalCaption, 0, 95),
                        'description' => $finalCaption,
                        'video_url'   => $this->post->video_url,
                    ], $connId);

                    if ($res['success']) {
                        $ytId = $res['data']['data']['id'] ?? ($res['data']['id'] ?? 'yt_success');
                        $publishedPlatforms['youtube'] = (string) $ytId;
                    }
                }
            } catch (\Throwable $e) {
                Log::error("Gagal publish ke {$integration->platform}: " . $e->getMessage());
            }
        }

        Log::info("Publish selesai untuk ContentPost #{$this->post->id}. Berhasil ke: " . implode(', ', $publishedPlatforms));
    }
}
