<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected string $apiKey;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        // Gemini 1.5 Flash: cepat, cerdas, dan efisien untuk kuota
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$this->apiKey}";
    }

    public function getApiKey(?Team $team = null): string
    {
        return $team?->geminiKey?->api_key ?: config('services.gemini.key', '');
    }


    public function generateEnhancedCaption(string $originalCaption, ?string $customPrompt = null, ?Team $team = null): string
    {
        $prompt = "Kamu adalah social media strategist profesional untuk BrandPilot AI. " .
                  "Ubah caption TikTok berikut ini menjadi caption yang memikat, elegan, memiliki call-to-action (CTA) rapi, dan hashtag relevan untuk diposting silang ke Facebook, Instagram Reels, dan YouTube Shorts.\n\n" .
                  "Caption asli: \"{$originalCaption}\"\n" .
                  ($customPrompt ? "Instruksi tambahan: {$customPrompt}\n" : "") .
                  "Keluarkan HANYA teks caption hasil akhirnya saja tanpa pembuka/penutup basa-basi.";

        $key = $this->getApiKey($team);
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$key}";

        $response = Http::post($endpoint, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            return trim($response->json()['candidates'][0]['content']['parts'][0]['text'] ?? $originalCaption);
        }

        return $originalCaption; // Fallback ke caption asli jika API ada kendala
    }

    /**
     * Membalas komentar / DM customer secara ramah berdasarkan informasi bisnis
     */
    public function generateSmartReply(string $incomingMessage, ?string $knowledgeBase = null, ?Team $team = null): string
    {
        $prompt = "Kamu adalah customer care profesional dan ramah untuk BrandPilot AI. " .
                  "Balas pesan atau komentar dari calon customer berikut secara sopan, solutif, ringkas, dan elegan. " .
                  "Gunakan konteks bisnis ini jika relevan: " . ($knowledgeBase ?: "Kami siap melayani kebutuhan Anda dengan senang hati.") . "\n\n" .
                  "Pesan Masuk: \"{$incomingMessage}\"\n" .
                  "Aturan: Berikan HANYA teks balasan langsung tanpa format pembuka seperti 'Balasan:' atau tanda petik.";

        $key = $this->getApiKey($team);
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$key}";
        $response = \Illuminate\Support\Facades\Http::post($endpoint, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            return trim($response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Terima kasih atas pesannya, kami akan segera merespons Anda!');
        }

        return 'Terima kasih telah menghubungi kami! Pesan Anda telah kami terima.';
    }
}
