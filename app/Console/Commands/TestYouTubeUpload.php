<?php

namespace App\Console\Commands;

use App\Models\ContentPost;
use App\Models\SocialIntegration;
use App\Models\Team;
use App\Services\ComposioService;
use Illuminate\Console\Command;

class TestYouTubeUpload extends Command
{
    protected $signature = 'brandpilot:test-youtube
                            {--post= : ID ContentPost yang diuji (default: terbaru)}
                            {--privacy=private : public|private|unlisted}';

    protected $description = 'Tes upload video ke YouTube (auth Composio + resumable upload langsung ke YouTube)';

    public function handle(ComposioService $composio): int
    {
        $team = Team::first();
        if (! $team) {
            $this->error('Belum ada Team di database.');
            return self::FAILURE;
        }

        $ytIntegration = SocialIntegration::where('team_id', $team->id)
            ->where('platform', 'youtube')
            ->first();

        if (! $ytIntegration) {
            $this->error('Integrasi YouTube belum terhubung untuk team ini!');
            return self::FAILURE;
        }

        $post = $this->option('post')
            ? ContentPost::find($this->option('post'))
            : ContentPost::latest()->first();

        if (! $post) {
            $this->error('Belum ada ContentPost di database.');
            return self::FAILURE;
        }

        $caption = $post->ai_generated_caption ?: $post->original_caption;
        $privacy = $this->option('privacy');

        $this->info("1. Menyiapkan sumber video Post #{$post->id}: {$post->video_url}");
        $this->line("   Judul    : " . \Illuminate\Support\Str::limit($caption, 95));
        $this->line("   Privasi  : {$privacy}");

        try {
            // Auth dari Composio (proxy) + byte dikirim langsung ke YouTube (resumable upload).
            // Aksi YOUTUBE_UPLOAD_VIDEO tidak dipakai: relay berkasnya merusak stream sehingga
            // YouTube menerima video lalu membuangnya ("tidak dapat memproses file").
            $res = $composio->uploadYoutubeVideo($team, $post->video_url, [
                'title'         => \Illuminate\Support\Str::limit($caption, 95),
                'description'   => $caption,
                'tags'          => ['Shorts', 'Viral', 'BrandPilot'],
                'categoryId'    => '22',
                'privacyStatus' => $privacy,
                'mimetype'      => 'video/mp4',
            ], $ytIntegration->composio_connection_id);
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (! ($res['success'] ?? false)) {
            $this->error('Unggah gagal: ' . ($res['error'] ?? 'unknown'));
            return self::FAILURE;
        }

        $videoId = $res['video_id'];
        $this->info("✓ Terunggah: {$res['size']} bytes, videoId={$videoId}");
        $this->line("   https://youtu.be/{$videoId}");

        // Pemrosesan YouTube berjalan asinkron: video bisa terbit lalu dibuang kalau transcode gagal.
        $this->info('2. Menunggu pemrosesan YouTube...');

        for ($i = 1; $i <= 12; $i++) {
            sleep(15);
            $status = $composio->youtubeProcessingStatus($team, $videoId, $ytIntegration->composio_connection_id);
            $this->line("   [{$i}] status: " . ($status ?? 'HILANG (dibuang YouTube)'));

            if ($status === 'succeeded') {
                $this->info("🎉 YOUTUBE SHORTS BERHASIL DIPROSES: https://youtu.be/{$videoId}");
                return self::SUCCESS;
            }

            if ($status === null || $status === 'failed') {
                $this->error('Video dibuang YouTube — jalur upload bermasalah.');
                return self::FAILURE;
            }
        }

        $this->warn("Masih 'processing' setelah ~3 menit. Cek YouTube Studio untuk {$videoId}.");
        return self::SUCCESS;
    }
}
