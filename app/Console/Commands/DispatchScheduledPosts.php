<?php

namespace App\Console\Commands;

use App\Jobs\PublishContentJob;
use App\Models\ContentPost;
use Illuminate\Console\Command;

class DispatchScheduledPosts extends Command
{
    protected $signature = 'brandpilot:publish-scheduled';
    protected $description = 'Periksa dan eksekusi postingan video yang jadwalnya sudah tiba atau terlewati';

    public function handle()
    {
        $this->info('Memeriksa antrean postingan yang siap dipublikasikan...');

        // Pulihkan post yang tertahan di 'processing' (mis. worker mati di tengah jalan)
        $stuck = ContentPost::where('status', 'processing')
            ->where('updated_at', '<', now()->subMinutes(20))
            ->update(['status' => 'pending']);

        if ($stuck > 0) {
            $this->warn("{$stuck} postingan macet dikembalikan ke antrean.");
        }

        // Cari semua postingan pending yang jadwalnya sudah lewat atau tepat saat ini
        $duePosts = ContentPost::where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            })
            ->get();

        if ($duePosts->isEmpty()) {
            $this->line('Tidak ada postingan yang menunggu jadwal saat ini.');
            return;
        }

        $this->info("Ditemukan {$duePosts->count()} postingan siap diposting!");

        foreach ($duePosts as $post) {
            $this->info("Memproses Post ID #{$post->id} untuk Tenant: {$post->team->name}...");

            // Status 'processing' diklaim di dalam job (atomic), supaya tidak ada
            // dua jalur yang sama-sama mengubah status lalu memposting dua kali.
            try {
                dispatch_sync(new PublishContentJob($post));
                $this->info("✓ Post ID #{$post->id} selesai diproses.");
            } catch (\Throwable $e) {
                $post->update(['status' => 'failed']);
                $this->error("✗ Gagal memposting Post ID #{$post->id}: " . $e->getMessage());
            }
        }

        $this->info('Semua antrean terjadwal selesai dieksekusi.');
    }
}
