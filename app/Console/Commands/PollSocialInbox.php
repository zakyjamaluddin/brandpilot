<?php

namespace App\Console\Commands;

use App\Models\InboxMessage;
use App\Models\InboxThread;
use App\Models\Team;
use App\Services\ComposioService;
use App\Services\GeminiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\ContentPost;


class PollSocialInbox extends Command
{
    protected $signature = 'brandpilot:poll-inbox';
    protected $description = 'Polling DM/Komentar FB, IG, YT via Composio -> Cek Saklar -> Eksekusi Gemini Reply';

    public function handle(ComposioService $composio, GeminiService $gemini)
    {
        $this->info("Menjalankan polling Inbox Sosial Media tiap 5 menit...");

        $teams = Team::where('subscription_status', 'active')->get();

        foreach ($teams as $team) {
            $setting = $team->setting;
            $integrations = $team->hasMany(\App\Models\SocialIntegration::class)
                ->where('is_connected', true)
                ->get();

            foreach ($integrations as $integration) {
                $this->info("Memeriksa interaksi baru di {$integration->platform} untuk [{$team->name}]...");

                try {
                    $newMessages = $this->fetchIncomingMessages($composio, $team, $integration);

                    foreach ($newMessages as $item) {
                        // 1. Simpan atau cari thread percakapan
                        $thread = InboxThread::firstOrCreate(
                            [
                                'team_id'     => $team->id,
                                'platform'    => $integration->platform,
                                'external_id' => $item['external_id'],
                            ],
                            [
                                'type'                => $item['type'],
                                'sender_name'         => $item['sender_name'],
                                'last_snippet'        => $item['text'],
                                'last_interaction_at' => now(),
                            ]
                        );

                        // 2. Simpan pesan dari customer
                        $msg = InboxMessage::create([
                            'inbox_thread_id' => $thread->id,
                            'sender_type'     => 'customer',
                            'message'         => $item['text'],
                            'sent_at'         => now(),
                        ]);

                        $thread->update([
                            'last_snippet'        => $item['text'],
                            'last_interaction_at' => now(),
                        ]);

                        // 3. POIN 4: Cek apakah fitur Auto-Reply DIAKTIFKAN?
                        if ($setting && $setting->auto_reply_enabled) {
                            $this->info("Auto-Reply AKTIF: Meracik jawaban dengan Gemini AI...");

                            // POIN 3: Generate balasan dengan konteks Knowledge Base
                            $aiReply = $gemini->generateSmartReply(
                                $item['text'],
                                $setting->brand_knowledge_base,
                                $team
                            );

                            // Kirim balasan ke platform tujuan via Composio
                            $this->sendReplyViaPlatform($composio, $team, $integration, $item, $aiReply);

                            // Simpan balasan AI ke database
                            InboxMessage::create([
                                'inbox_thread_id' => $thread->id,
                                'sender_type'     => 'ai',
                                'message'         => $aiReply,
                                'sent_at'         => now(),
                            ]);

                            $thread->update(['last_snippet' => "AI: {$aiReply}"]);
                            $this->info("Balasan Gemini berhasil dikirim!");
                        } else {
                            $this->line("Auto-Reply NONAKTIF untuk tenant ini. Pesan disimpan tanpa balasan otomatis.");
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error("Error polling inbox pada {$integration->platform}: " . $e->getMessage());
                }
            }
        }

        $this->info("Siklus polling inbox selesai.");
    }

    /**
     * Mengambil komentar atau DM dari Composio v3.1
     */
    // app/Console/Commands/PollSocialInbox.php


    protected function fetchIncomingMessages(ComposioService $composio, Team $team, $integration): array
    {
        $messages = [];
        $connId = $integration->composio_connection_id;

        // Ambil postingan-postingan terakhir yang sudah terbit di tenant ini
        $recentPosts = ContentPost::where('team_id', $team->id)
            ->where('status', 'published')
            ->whereNotNull('platform_post_ids')
            ->latest()
            ->take(5)
            ->get();

        foreach ($recentPosts as $post) {
            $platformIds = (array) $post->platform_post_ids;
            $objectId = $platformIds[$integration->platform] ?? null;

            if (! $objectId) {
                continue;
            }

            // ==========================================
            // 1. POLLING KOMENTAR FACEBOOK
            // ==========================================
            if ($integration->platform === 'facebook') {
                $res = $composio->executeAction($team, 'FACEBOOK_GET_COMMENTS', [
                    'object_id' => (string) $objectId, // Ini parameter wajib yang tadi kurang!
                ], $connId);

                $comments = $res['data']['data']['data']
                    ?? ($res['data']['data']
                    ?? ($res['data']['comments']['data'] ?? []));

                if (is_array($comments)) {
                    foreach ($comments as $cmt) {
                        $commentId = $cmt['id'] ?? null;
                        $commentText = $cmt['message'] ?? '';

                        if ($commentId && !empty($commentText)) {
                            // Cegah duplikasi jika thread komentar sudah pernah disimpan
                            if (! \App\Models\InboxThread::where('external_id', $commentId)->exists()) {
                                $messages[] = [
                                    'external_id' => (string) $commentId,
                                    'type'        => 'comment',
                                    'sender_name' => $cmt['from']['name'] ?? 'Facebook User',
                                    'text'        => $commentText,
                                ];
                            }
                        }
                    }
                }
            }

            // ==========================================
            // 2. POLLING KOMENTAR INSTAGRAM
            // ==========================================
            elseif ($integration->platform === 'instagram') {
                $res = $composio->executeAction($team, 'INSTAGRAM_GET_MEDIA_COMMENTS', [
                    'media_id' => (string) $objectId,
                ], $connId);

                $comments = $res['data']['data']['data'] ?? ($res['data']['data'] ?? []);
                if (is_array($comments)) {
                    foreach ($comments as $cmt) {
                        $commentId = $cmt['id'] ?? null;
                        $commentText = $cmt['text'] ?? '';

                        if ($commentId && !empty($commentText)) {
                            if (! \App\Models\InboxThread::where('external_id', $commentId)->exists()) {
                                $messages[] = [
                                    'external_id' => (string) $commentId,
                                    'type'        => 'comment',
                                    'sender_name' => $cmt['username'] ?? 'Instagram User',
                                    'text'        => $commentText,
                                ];
                            }
                        }
                    }
                }
            }

            // ==========================================
            // 3. POLLING KOMENTAR YOUTUBE
            // ==========================================
            elseif ($integration->platform === 'youtube') {
                $res = $composio->executeAction($team, 'YOUTUBE_LIST_COMMENT_THREADS', [
                    'video_id' => (string) $objectId,
                ], $connId);

                $items = $res['data']['items'] ?? [];
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $snippet = $item['snippet']['topLevelComment']['snippet'] ?? null;
                        if ($snippet) {
                            $commentId = $item['id'] ?? null;
                            $commentText = $snippet['textDisplay'] ?? '';

                            if ($commentId && !empty($commentText)) {
                                if (! \App\Models\InboxThread::where('external_id', $commentId)->exists()) {
                                    $messages[] = [
                                        'external_id' => (string) $commentId,
                                        'type'        => 'comment',
                                        'sender_name' => $snippet['authorDisplayName'] ?? 'YouTube User',
                                        'text'        => $commentText,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $messages;
    }

    /**
     * Mengirimkan balasan kembali ke sosmed
     */
    protected function sendReplyViaPlatform(ComposioService $composio, Team $team, $integration, array $item, string $reply): void
    {
        $connId = $integration->composio_connection_id;

        if ($integration->platform === 'facebook') {
            $composio->executeAction($team, 'FACEBOOK_CREATE_COMMENT', [
                'object_id' => $item['external_id'],
                'message'   => $reply,
            ], $connId);
        } elseif ($integration->platform === 'instagram') {
            $composio->executeAction($team, 'INSTAGRAM_CREATE_MEDIA_COMMENT_REPLY', [
                'comment_id' => $item['external_id'],
                'message'    => $reply,
            ], $connId);
        }
    }
}
