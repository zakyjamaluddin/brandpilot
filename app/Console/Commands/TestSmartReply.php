<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Services\GeminiService;
use Illuminate\Console\Command;

class TestSmartReply extends Command
{
    protected $signature = 'brandpilot:test-reply {pesan? : Contoh pesan dari customer}';
    protected $description = 'Uji coba terisolasi Gemini AI Smart Reply';

    public function handle(GeminiService $gemini)
    {
        $this->info("=== UJI COBA GEMINI SMART REPLY (ISOLATED TEST) ===");

        $team = Team::first();
        if (! $team) {
            $this->error("X Tidak ada Tenant/Team di database!");
            return;
        }

        $incomingMessage = $this->argument('pesan') ?? "Halo kak, ayam kampungnya masih ada stock kah? Pengiriman bisa ke Surabaya?";

        $this->info("1. Tenant: {$team->name}");
        $this->info("2. Knowledge Base Brand Saat Ini:");
        $this->line("   \"" . ($team->setting?->brand_knowledge_base ?: 'Default') . "\"");

        $this->info("\n3. Pesan Uji Coba dari Customer:");
        $this->line("   <fg=cyan>\"{$incomingMessage}\"</>");

        $this->info("\n4. Mengirim ke Gemini 3.1 Flash Lite...");

        $reply = $gemini->generateSmartReply(
            $incomingMessage,
            $team->setting?->brand_knowledge_base,
            $team
        );

        $this->info("\n🎉 HASIL BALASAN GEMINI AI:");
        $this->line("==================================================");
        $this->line("<fg=green;options=bold>{$reply}</>");
        $this->line("==================================================");
    }
}
