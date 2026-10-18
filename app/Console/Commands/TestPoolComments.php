<?php

namespace App\Console\Commands;

use App\Jobs\PublishContentJob;
use App\Models\ContentPost;
use App\Models\InboxMessage;
use App\Models\InboxThread;
use App\Models\SocialIntegration;
use App\Models\Team;
use App\Services\ComposioService;
use App\Services\GeminiService;
use Illuminate\Console\Command;

class TestPoolComments extends Command
{
    protected $signature = 'brandpilot:test-pool-comments {--reply : Sekaligus kirim balasan otomatis ke sosmed}';
    protected $description = 'Wajib mengambil postingan terakhir, deteksi komentar sendiri via User ID, anti-double reply, dan kirim balasan';

    public function handle(ComposioService $composio, GeminiService $gemini)
    {
        $this->info("=== UJI COBA POOLING KOMENTAR SOSIAL MEDIA ===");

        $team = Team::first();
        if (! $team) {
            $this->error("X Belum ada Tenant/Team di database!");
            return;
        }

        // 1. ATURAN BAKU: WAJIB AMBIL POSTINGAN PALING TERAKHIR DI DATABASE
        $post = ContentPost::where('team_id', $team->id)->latest()->first();

        if (! $post) {
            $this->error("X Belum ada satupun ContentPost di database.");
            return;
        }

        $this->info("1. Memeriksa Postingan Terakhir (Post ID #{$post->id})");
        $this->line("   Judul: \"{$post->title}\"");
        $this->line("   Status Saat Ini: <fg=yellow>{$post->status}</>");
        $this->line("   ID Sosmed: " . json_encode($post->platform_post_ids));

        $platformIds = (array) ($post->platform_post_ids ?? []);

        // 2. JIKA POSTINGAN TERAKHIR BELUM TERBIT ATAU ID KOSONG, TAWARKAN AUTO-PUBLISH
        if (empty($platformIds) || $post->status !== 'published') {
            $this->warn("\n⚠️ Postingan terakhir (#{$post->id}) belum terbit ke sosmed sehingga belum memiliki ID.");

            if ($this->confirm("Apakah Anda ingin menerbitkan Post #{$post->id} ke Facebook, Instagram & YouTube sekarang?", true)) {
                $this->info("Menerbitkan Post #{$post->id}...");

                try {
                    dispatch_sync(new PublishContentJob($post));
                    $post->refresh();
                    $platformIds = (array) ($post->platform_post_ids ?? []);
                    $this->info("✓ Publikasi selesai! ID Baru: " . json_encode($platformIds));
                } catch (\Throwable $e) {
                    $this->error("Gagal publish: " . $e->getMessage());
                    return;
                }
            } else {
                $this->line("Dibatalkan.");
                return;
            }
        }

        // 3. MULAI POOLING KOMENTAR UNTUK POSTINGAN TERAKHIR INI
        $integrations = SocialIntegration::where('team_id', $team->id)->where('is_connected', true)->get();

        foreach ($integrations as $integration) {
            $platform = $integration->platform;
            $objectId = $platformIds[$platform] ?? null;

            $this->info("\n--------------------------------------------------");
            $this->info("2. Platform: " . strtoupper($platform));

            if (! $objectId) {
                $this->warn("   Postingan #{$post->id} tidak memiliki ID untuk platform {$platform}. Melewati.");
                continue;
            }

            $this->line("   Target Object ID: {$objectId}");
            $connId = $integration->composio_connection_id;

            $comments = [];

            // Identifikasi ID Akun Milik Kita Sendiri (Anti-Self by ID)
            $myPageId   = (string) ($integration->metadata['page_id'] ?? '422099137659003');
            $myIgUserId = (string) ($integration->metadata['ig_user_id'] ?? '38448916064752527');
            $myIgUsername = strtolower($integration->metadata['username'] ?? ($integration->account_name ?? 'kenbifarm'));

            // ==========================================
            // A. TARIK KOMENTAR FACEBOOK
            // ==========================================
            if ($platform === 'facebook') {
                $this->line("   Memanggil tool: FACEBOOK_GET_COMMENTS...");
                $res = $composio->executeAction($team, 'FACEBOOK_GET_COMMENTS', [
                    'object_id' => (string) $objectId,
                ], $connId);

                $raw = $res['data']['data']['data'] ?? ($res['data']['data'] ?? ($res['data']['comments']['data'] ?? []));
                if (is_array($raw)) {
                    foreach ($raw as $c) {
                        $commentId = $c['id'] ?? null;
                        if (empty($commentId)) continue;

                        $comments[] = [
                            'id'        => (string) $commentId,
                            'author_id' => (string) ($c['from']['id'] ?? ''), // ID User Facebook Pengirim
                            'author'    => (string) ($c['from']['name'] ?? 'Facebook User'),
                            'text'      => (string) ($c['message'] ?? ''),
                        ];
                    }
                }
            }

            // ==========================================
            // B. TARIK KOMENTAR INSTAGRAM
            // ==========================================
            elseif ($platform === 'instagram') {
                $this->line("   Memanggil tool: INSTAGRAM_GET_IG_MEDIA_COMMENTS...");
                $res = $composio->executeAction($team, 'INSTAGRAM_GET_IG_MEDIA_COMMENTS', [
                    'ig_media_id' => (string) $objectId,
                ], $connId);

                $raw = $res['data']['data']['data']
                    ?? ($res['data']['data']
                    ?? ($res['data']['response_data']['data'] ?? []));

                if (isset($raw['data']) && is_array($raw['data'])) {
                    $raw = $raw['data'];
                }

                if (is_array($raw)) {
                    foreach ($raw as $c) {
                        $c = (array) $c;
                        $commentId = $c['id'] ?? null;
                        if (empty($commentId)) continue;

                        $comments[] = [
                            'id'        => (string) $commentId,
                            'author_id' => (string) ($c['from']['id'] ?? ($c['user']['id'] ?? '')), // ID User IG Pengirim
                            'author'    => (string) ($c['username'] ?? ($c['from']['username'] ?? 'Instagram User')),
                            'text'      => (string) ($c['text'] ?? ($c['message'] ?? '')),
                        ];
                    }
                }
            }

            // ==========================================
            // C. TARIK KOMENTAR YOUTUBE
            // ==========================================
            elseif ($platform === 'youtube') {
                $this->line("   Memanggil tool: YOUTUBE_LIST_COMMENT_THREADS...");
                $res = $composio->executeAction($team, 'YOUTUBE_LIST_COMMENT_THREADS2', [
                    'videoId' => (string) $objectId,
                ], $connId);

                $items = $res['data']['items'] ?? [];
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $snippet = $item['snippet']['topLevelComment']['snippet'] ?? null;
                        if ($snippet && !empty($item['id'])) {
                            $comments[] = [
                                'id'        => (string) $item['id'],
                                'author_id' => (string) ($snippet['authorChannelId']['value'] ?? ''),
                                'author'    => (string) ($snippet['authorDisplayName'] ?? 'YouTube User'),
                                'text'      => (string) ($snippet['textDisplay'] ?? ''),
                            ];
                        }
                    }
                }
            }

            // Evaluasi Komentar yang Tertangkap
            if (empty($comments)) {
                $this->line("   <fg=yellow>○ Belum ada komentar baru yang masuk di {$platform}.</>");
                continue;
            }

            $this->info("   ✓ Ditemukan " . count($comments) . " komentar:");

            foreach ($comments as $cmt) {
                $this->line("   ----------------------------------------");
                $this->line("   👤 Dari: <fg=cyan>{$cmt['author']}</> [User ID: {$cmt['author_id']}]");
                $this->line("   💬 Pesan: \"{$cmt['text']}\"");

                // ==============================================================
                // TIGA LAPIS PENGAMAN ANTI-SELF (ANTI-BOT-LOOP)
                // ==============================================================
                $isOwnComment = false;
                $trimmedText  = trim($cmt['text']);

                // LAPISAN 1: Cek apakah teks ini pernah diproduksi oleh AI kita di database!
                if (InboxMessage::where('sender_type', 'ai')->where('message', $trimmedText)->exists()) {
                    $isOwnComment = true;
                    $reason = "Teks komentar identik dengan balasan AI di database";
                }

                // LAPISAN 2: Cek apakah Username adalah username Instagram kita sendiri (kenbifarm)
                elseif ($platform === 'instagram' && !empty($myIgUsername) && str_contains(strtolower($cmt['author']), strtolower($myIgUsername))) {
                    $isOwnComment = true;
                    $reason = "Username pengirim cocok dengan akun Instagram kita (@{$myIgUsername})";
                }

                // LAPISAN 3: Cek User ID / Page ID
                elseif ($platform === 'facebook' && $cmt['author_id'] === $myPageId) {
                    $isOwnComment = true;
                    $reason = "Author ID cocok dengan Facebook Page ID";
                } elseif ($platform === 'instagram' && $cmt['author_id'] === $myIgUserId) {
                    $isOwnComment = true;
                    $reason = "Author ID cocok dengan Instagram User ID";
                }

                // JIKA TERBUKTI KOMENTAR DARI KITA SENDIRI, LANGSUNG LEWATI!
                if ($isOwnComment) {
                    $this->line("   <fg=yellow>⏩ [SKIP ANTI-SELF] Dilewati: {$reason}.</>");
                    continue;
                }

                // ==============================================================
                // ATURAN 2: CEK DATABASE APAKAH SUDAH PERNAH DIBALAS (ANTI-SPAM)
                // ==============================================================
                $thread = InboxThread::firstOrCreate(
                    [
                        'team_id'     => $team->id,
                        'platform'    => $platform,
                        'external_id' => $cmt['id'],
                    ],
                    [
                        'type'                => 'comment',
                        'sender_name'         => $cmt['author'],
                        'last_snippet'        => $cmt['text'],
                        'last_interaction_at' => now(),
                        'is_replied'          => false,
                    ]
                );

                InboxMessage::firstOrCreate(
                    [
                        'inbox_thread_id' => $thread->id,
                        'message'         => $cmt['text'],
                    ],
                    [
                        'sender_type' => 'customer',
                        'sent_at'     => now(),
                    ]
                );

                if ($thread->is_replied || $thread->messages()->whereIn('sender_type', ['ai', 'user'])->exists()) {
                    $this->line("   <fg=yellow>⏩ [SKIP SUDAH DIBALAS] Komentar ini sudah pernah dibalas sebelumnya di database.</>");
                    continue;
                }

                // ==============================================================
                // ATURAN 3: RACIK BALASAN DENGAN GEMINI AI 3.1 FLASH LITE
                // ==============================================================
                $this->line("   🤖 Meracik balasan dengan Gemini AI...");
                $aiReply = $gemini->generateSmartReply(
                    $cmt['text'],
                    $team->setting?->brand_knowledge_base,
                    $team
                );

                $this->line("   ✨ Saran Balasan Gemini: <fg=green>\"{$aiReply}\"</>");

                // ==============================================================
                // ATURAN 4: EKSEKUSI PENGIRIMAN BALASAN KE SOSIAL MEDIA
                // ==============================================================
                if ($this->option('reply')) {
                    $this->line("   🚀 Mengirim balasan ke {$platform}...");

                    // A. FACEBOOK: Mengirim dengan page_id resmi
                    if ($platform === 'facebook') {
                        $res = $composio->executeAction($team, 'FACEBOOK_CREATE_COMMENT', [
                            'page_id'   => (string) $myPageId,  // Page ID resmi kita
                            'object_id' => (string) $cmt['id'], // Comment ID yang dibalas
                            'message'   => $aiReply,
                        ], $connId);

                        if ($res['success']) {
                            $this->info("   ✓ Berhasil membalas komentar Facebook!");
                        } else {
                            $this->error("   X Gagal membalas Facebook: " . json_encode($res));
                        }
                    }

                    // B. INSTAGRAM: Menggunakan tool resmi INSTAGRAM_POST_IG_COMMENT_REPLIES
                    elseif ($platform === 'instagram') {
                        $res = $composio->executeAction($team, 'INSTAGRAM_POST_IG_COMMENT_REPLIES', [
                            'ig_comment_id' => (string) $cmt['id'],
                            'message'    => $aiReply,
                        ], $connId);

                        if ($res['success']) {
                            $this->info("   ✓ Berhasil membalas komentar Instagram!");
                        } else {
                            $this->error("   X Gagal membalas Instagram: " . json_encode($res));
                        }
                    }

                    // SIMPAN STATUS BALASAN DI DATABASE
                    InboxMessage::create([
                        'inbox_thread_id' => $thread->id,
                        'sender_type'     => 'ai',
                        'message'         => $aiReply,
                        'sent_at'         => now(),
                    ]);

                    $thread->update([
                        'is_replied'          => true,
                        'replied_at'          => now(),
                        'last_snippet'        => "AI: {$aiReply}",
                        'last_interaction_at' => now(),
                    ]);
                }
            }
        }

        $this->info("\n=== PENGUJIAN SELESAI ===");
    }
}
