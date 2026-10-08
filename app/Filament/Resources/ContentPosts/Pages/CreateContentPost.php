<?php

namespace App\Filament\Resources\ContentPosts\Pages;

use App\Filament\Resources\ContentPosts\ContentPostResource;
use App\Models\ContentPost;
use App\Services\GeminiService;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateContentPost extends CreateRecord
{
    protected static string $resource = ContentPostResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $team = Filament::getTenant();
        $setting = $team->setting;
        $gemini = app(GeminiService::class);

        // MODE 1: BULK UPLOAD
        if (($data['creation_mode'] ?? 'single') === 'bulk') {
            $videoFiles = $data['bulk_video_files'] ?? [];
            $hours = $setting?->static_posting_hours ?? ['09:00', '17:00'];
            if (empty($hours)) {
                $hours = ['09:00', '17:00'];
            }

            $currentDate = Carbon::today();
            $hourIndex = 0;
            $firstCreatedRecord = null;

            foreach ($videoFiles as $filePath) {
                // Tentukan waktu jadwal berdasarkan jam statis
                $scheduledTime = $currentDate->copy()->setTimeFromTimeString($hours[$hourIndex]);
                
                // Jika jam hari ini sudah lewat, jadwalkan besoknya
                if ($scheduledTime->isPast()) {
                    $scheduledTime->addDay();
                }

                // Buat caption (AI vs Manual)
                $caption = $data['manual_caption'] ?? '';
                if (($data['caption_type'] ?? 'manual') === 'gemini_ai') {
                    $caption = $gemini->generateEnhancedCaption(
                        $data['ai_topic_prompt'] ?? 'Video promosi produk terbaru',
                        $setting?->custom_caption_prompt
                    );
                }

                $record = ContentPost::create([
                    'team_id'              => $team->id,
                    'video_url'            => asset('storage/' . $filePath),
                    'caption_type'         => $data['caption_type'] ?? 'manual',
                    'original_caption'     => $caption,
                    'ai_generated_caption' => ($data['caption_type'] === 'gemini_ai') ? $caption : null,
                    'status'               => 'pending',
                    'scheduled_at'         => $scheduledTime,
                ]);

                if (! $firstCreatedRecord) {
                    $firstCreatedRecord = $record;
                }

                // Geser ke slot jam berikutnya
                $hourIndex++;
                if ($hourIndex >= count($hours)) {
                    $hourIndex = 0;
                    $currentDate->addDay();
                }
            }

            return $firstCreatedRecord ?? new ContentPost();
        }

        // MODE 2: INDIVIDUAL UPLOAD
        $caption = $data['manual_caption'] ?? '';
        if (($data['caption_type'] ?? 'manual') === 'gemini_ai') {
            $caption = $gemini->generateEnhancedCaption(
                $data['ai_topic_prompt'] ?? 'Video produk',
                $setting?->custom_caption_prompt
            );
        }

        $videoPath = $data['single_video_file'] ?? '';

        return ContentPost::create([
            'team_id'              => $team->id,
            'video_url'            => asset('storage/' . $videoPath),
            'caption_type'         => $data['caption_type'] ?? 'manual',
            'original_caption'     => $caption,
            'ai_generated_caption' => ($data['caption_type'] === 'gemini_ai') ? $caption : null,
            'status'               => 'pending',
            'scheduled_at'         => $data['scheduled_at'] ?? now(),
        ]);
    }
}