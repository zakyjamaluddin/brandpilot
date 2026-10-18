<?php

namespace App\Console\Commands;

use App\Models\ContentPost;
use App\Models\Team;
use App\Services\GeminiService;
use Illuminate\Console\Command;

class TestGeminiCaption extends Command
{
    protected $signature = 'brandpilot:test-gemini';
    protected $description = 'Uji coba Gemini Files API (Video Understanding) dengan Flash Lite';

    public function handle(GeminiService $gemini)
    {
        $this->info("=== UJI COBA GEMINI FILES API (VIDEO UNDERSTANDING) ===");

        $team = Team::first();
        $post = ContentPost::latest()->first();

        if (! $post) {
            $this->error("Belum ada ContentPost di database untuk dites videonya.");
            return;
        }

        $this->info("1. Mengambil video dari Post #{$post->id}...");
        $this->line("   URL: {$post->video_url}");
        $this->line("   Caption Asli: \"{$post->original_caption}\"");

        $this->info("\n2. Mengunggah video ke Gemini Files API & menunggu state ACTIVE...");

        try {
            $result = $gemini->generateCaptionFromVideo(
                $post->video_url,
                $post->original_caption,
                $team->setting?->custom_caption_prompt,
                $team
            );

            $this->info("\n🎉 GEMINI FLASH LITE SELESAI MENONTON VIDEO!");
            $this->line("==================================================");
            $this->line("<fg=yellow;options=bold>JUDUL VIDEO (Untuk YouTube & Facebook):</>");
            $this->line($result['title']);
            $this->line("\n<fg=cyan;options=bold>DESKRIPSI LENGKAP (Untuk IG Reels, YT, & FB):</>");
            $this->line($result['description']);
            $this->line("==================================================");

        } catch (\Throwable $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
