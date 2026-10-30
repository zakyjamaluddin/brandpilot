<?php

namespace App\Services;

use App\Models\Team;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeminiService
{
    // Prioritas model: Flash Lite terbaru (murah, cepat, multimodal)
    protected array $candidateModels = [
        'gemini-2.0-flash-lite',
        'gemini-2.0-flash-lite-preview-02-05',
        'gemini-1.5-flash-8b',
        'gemini-2.0-flash',
        'gemini-1.5-flash-latest',
    ];

    /**
     * Daftar API key Gemini yang dipakai BERSAMA oleh semua tenant (bukan assign per tenant).
     * Sumber: menu API Keys (provider = gemini, aktif) → fallback config/env.
     */
    public function keys(): array
    {
        $keys = \App\Models\ApiKey::query()
            ->where('provider', 'gemini')
            ->where('is_active', true)
            ->orderBy('id')
            ->pluck('api_key')
            ->map(fn ($k) => trim((string) $k))
            ->filter()
            ->values()
            ->all();

        if ($keys === []) {
            $env = trim((string) config('services.gemini.api_key', ''));
            if ($env !== '') {
                $keys = [$env];
            }
        }

        return $keys;
    }

    public function getApiKey(?Team $team = null): string
    {
        $keys = $this->keys();

        if ($keys === []) {
            throw new \RuntimeException('Gemini API Key belum tersedia. Tambahkan di menu API Keys (provider: Gemini).');
        }

        return $keys[0];
    }

    protected function keyIsDead(string $key): bool
    {
        return \Illuminate\Support\Facades\Cache::has('gemini:dead:' . md5($key));
    }

    protected function markKeyDead(string $key, int $minutes = 30): void
    {
        \Illuminate\Support\Facades\Cache::put('gemini:dead:' . md5($key), true, now()->addMinutes($minutes));
    }

    /**
     * Kirim generateContent dengan fallback antar-key:
     * 401/403 → key ditandai mati 30 menit; 429 → dilewati 10 menit; 5xx → coba key berikutnya.
     */
    protected function postGenerate(array $payload, int $timeout = 120): \Illuminate\Http\Client\Response
    {
        $keys = $this->keys();

        if ($keys === []) {
            throw new \RuntimeException('Gemini API Key belum tersedia. Tambahkan di menu API Keys (provider: Gemini).');
        }

        $lastResponse = null;

        foreach ($keys as $key) {
            if ($this->keyIsDead($key)) {
                continue;
            }

            $endpoint = 'https://generativelanguage.googleapis.com/v1/models/gemini-3.1-flash-lite:generateContent?key=' . $key;
            $response = Http::timeout($timeout)->post($endpoint, $payload);
            $lastResponse = $response;

            if ($response->successful()) {
                return $response;
            }

            $status = $response->status();

            if (in_array($status, [401, 403])) {
                $this->markKeyDead($key, 30);
                Log::warning("Gemini: key ditandai mati (HTTP {$status}).");
            } elseif ($status === 429) {
                $this->markKeyDead($key, 10);
                Log::warning('Gemini: key kena limit 429, coba key berikutnya.');
            } else {
                Log::warning("Gemini: HTTP {$status} pada salah satu key, coba key berikutnya.");
            }
        }

        return $lastResponse ?? Http::response(['error' => ['message' => 'Semua Gemini API key tidak tersedia.']], 503);
    }

    // app/Services/GeminiService.php

    /**
     * Helper untuk mengambil konten biner MP4 asli dari disk lokal atau URL
     */
    protected function getVideoBinary(string $videoPathOrUrl): string
    {
        // 1. Cek jika path lokal langsung diberikan
        if (file_exists($videoPathOrUrl) && is_file($videoPathOrUrl)) {
            return file_get_contents($videoPathOrUrl);
        }

        // 2. Cek di Storage disk public Laravel
        if (Storage::disk('public')->exists($videoPathOrUrl)) {
            return Storage::disk('public')->get($videoPathOrUrl);
        }

        // 3. Ekstrak nama file dari URL untuk mencari salinan di storage/app/public/videos/
        $filename = basename(parse_url($videoPathOrUrl, PHP_URL_PATH));
        if ($filename && Storage::disk('public')->exists('videos/' . $filename)) {
            Log::info("Gemini: Menggunakan salinan video lokal: videos/{$filename}");
            return Storage::disk('public')->get('videos/' . $filename);
        }

        // 4. Fallback jika URL masih aktif
        if (filter_var($videoPathOrUrl, FILTER_VALIDATE_URL)) {
            $response = Http::timeout(180)->get($videoPathOrUrl);
            if ($response->successful() && strlen($response->body()) > 10000) {
                return $response->body();
            }
        }

        // 5. Fallback terakhir: Ambil file video mp4 paling baru yang ada di folder storage lokal
        $files = Storage::disk('public')->files('videos');
        if (!empty($files)) {
            $latestLocalFile = end($files);
            Log::info("Gemini: Menggunakan video lokal terbaru di disk: {$latestLocalFile}");
            return Storage::disk('public')->get($latestLocalFile);
        }

        throw new Exception("Berkas video tidak ditemukan di disk lokal maupun URL.");
    }

    /**
     * 1. Unggah Berkas Video Asli ke Gemini Files API (Resumable Upload)
     */
    public function uploadVideoToGemini(string $videoPathOrUrl, ?Team $team = null): array
    {
        $apiKey = $this->getApiKey($team);
        if (empty($apiKey)) {
            throw new Exception("Gemini API Key tidak ditemukan.");
        }

        // Ambil biner video asli
        $contents = $this->getVideoBinary($videoPathOrUrl);
        $numBytes = strlen($contents);

        // PENGAMAN: Jika file < 10KB, dipastikan bukan file video
        if ($numBytes < 10000) {
            throw new Exception("Berkas yang didapat BUKAN video valid (hanya {$numBytes} bytes). Konten terbaca: " . substr($contents, 0, 100));
        }

        Log::info("Gemini Files API: Mengunggah video asli (" . number_format($numBytes / 1024 / 1024, 2) . " MB)...");

        // Langkah A: Inisialisasi Resumable Upload
        $initRes = Http::withHeaders([
            'X-Goog-Upload-Protocol'              => 'resumable',
            'X-Goog-Upload-Command'               => 'start',
            'X-Goog-Upload-Header-Content-Length' => $numBytes,
            'X-Goog-Upload-Header-Content-Type'   => 'video/mp4',
            'Content-Type'                        => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/upload/v1beta/files?key={$apiKey}", [
            'file' => [
                'display_name' => 'tiktok_video_' . time(),
            ],
        ]);

        $uploadUrl = $initRes->header('x-goog-upload-url') ?? ($initRes->header('X-Goog-Upload-URL') ?? null);
        if (! $uploadUrl) {
            throw new Exception("Gagal inisialisasi upload ke Gemini Files API: " . $initRes->body());
        }

        // Langkah B: Kirim Data Biner Video
        $uploadRes = Http::timeout(300)->withHeaders([
            'Content-Length'        => $numBytes,
            'X-Goog-Upload-Offset'  => 0,
            'X-Goog-Upload-Command' => 'upload, finalize',
        ])->withBody($contents, 'video/mp4')->post($uploadUrl);

        $fileData = $uploadRes->json('file');
        if (! $fileData || empty($fileData['uri'])) {
            throw new Exception("Gagal menyelesaikan upload video ke Gemini: " . $uploadRes->body());
        }

        Log::info("Gemini Files API: Video asli berhasil diunggah. URI: {$fileData['uri']}. Menunggu state ACTIVE...");

        // Langkah C: Tunggu hingga state video menjadi ACTIVE (biasanya 4-10 detik)
        $fileUri  = $fileData['uri'];
        $fileName = $fileData['name'];

        for ($i = 0; $i < 12; $i++) {
            sleep(4);
            $checkRes = Http::get("https://generativelanguage.googleapis.com/v1beta/{$fileName}?key={$apiKey}");
            $state = $checkRes->json('state');

            if ($state === 'ACTIVE') {
                Log::info("Gemini Files API: Video ACTIVE dan siap diproses AI!");
                return [
                    'uri'  => $fileUri,
                    'name' => $fileName,
                ];
            }

            if ($state === 'FAILED') {
                throw new Exception("Google gagal memproses video: " . $checkRes->body());
            }
        }

        return [
            'uri'  => $fileUri,
            'name' => $fileName,
        ];
    }

    // app/Services/GeminiService.php

    /**
     * 2. Multimodal Generation: Gemini "Menonton" Video & Meracik [Judul + Deskripsi] Terpisah
     * Mengembalikan array: ['title' => string, 'description' => string]
     */
    public function generateCaptionFromVideo(
        string $videoPathOrUrl,
        ?string $originalCaption = null,
        ?string $customPrompt = null,
        ?Team $team = null
    ): array {
        $apiKey = $this->getApiKey($team);

        // Nilai fallback bawaan jika terjadi kendala
        $fallbackTitle = \Illuminate\Support\Str::limit($originalCaption ?: 'Video Rekomendasi Hari Ini', 70);
        $fallbackDescription = $originalCaption ?: 'Tonton video selengkapnya sampai habis! Jangan lupa like dan follow ✨';

        try {
            // 1. Upload video ke Gemini Files API
            $file = $this->uploadVideoToGemini($videoPathOrUrl, $team);

            // 2. Susun prompt khusus agar AI mengeluarkan Judul & Deskripsi terpisah
            $promptText = "Tonton dan pahami video ini secara menyeluruh dari awal sampai akhir.\n" .
                "Sebagai social media strategist papan atas untuk BrandPilot AI, tugasmu adalah meracik 2 hal:\n" .
                "1. 'title': Judul video yang sangat memikat, click-worthy, dan membuat penasaran untuk YouTube & Facebook (maksimal 70-80 karakter, TANPA hashtag).\n" .
                "2. 'description': Deskripsi lengkap yang memiliki hook pembuka visual, penjelasan konten menarik, Call-to-Action (CTA) rapi, dan hashtag relevan untuk Instagram, YouTube, dan Facebook.\n\n" .
                ($originalCaption ? "Konteks caption asli kreator: \"{$originalCaption}\"\n" : "") .
                ($customPrompt ? "Instruksi khusus brand: {$customPrompt}\n" : "") .
                "Keluarkan HANYA format JSON valid dengan struktur: {\"title\": \"...\", \"description\": \"...\"}";

            Log::info("Gemini Files API: Selesai upload. Mengirim prompt pemisahan Judul & Deskripsi...");

            // 3. Request ke Gemini dengan konfigurasi JSON output (otomatis fallback antar-key)
            $response = $this->postGenerate([
                'contents' => [
                    [
                        'parts' => [
                            [
                                'file_data' => [
                                    'mime_type' => 'video/mp4',
                                    'file_uri'  => $file['uri'],
                                ],
                            ],
                            [
                                'text' => $promptText,
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json', // Memaksa AI mengembalikan JSON murni
                ],
            ]);

            Log::info("Gemini AI meracik Judul & Deskripsi. Status HTTP: " . $response->status());

            if ($response->successful()) {
                $rawJsonText = trim($response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');

                // Bersihkan kemungkinan markdown ```json ... ```
                $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', $rawJsonText);
                $result = json_decode($cleanJson, true);

                if (is_array($result) && !empty($result['title']) && !empty($result['description'])) {
                    Log::info("Gemini AI: Berhasil mengekstrak Judul & Deskripsi secara terpisah!", [
                        'title' => $result['title'],
                    ]);

                    return [
                        'title'       => trim($result['title']),
                        'description' => trim($result['description']),
                    ];
                }
            }

            Log::warning("Gemini AI tidak mengembalikan JSON valid, menggunakan fallback: " . $response->body());

        } catch (\Throwable $e) {
            Log::error("Gemini Video Understanding Gagal: " . $e->getMessage());
        }

        return [
            'title'       => $fallbackTitle,
            'description' => $fallbackDescription,
        ];
    }

    /**
     * Balas pesan / DM menggunakan teks
     */
    // app/Services/GeminiService.php

    /**
     * 3. Balas komentar / DM pelanggan secara cerdas dan kontekstual menggunakan Gemini 3.1 Flash Lite
     */
    public function generateSmartReply(string $incomingMessage, ?string $knowledgeBase = null, ?Team $team = null): string
    {
        $apiKey = $this->getApiKey($team);

        if (empty($apiKey)) {
            Log::error("Gemini Smart Reply Gagal: API Key tidak ditemukan!");
            return "Terima kasih telah menghubungi kami. Kami akan segera merespons Anda.";
        }

        // Susun prompt ramah, sopan, dan berorientasi penjualan (sales/support)
        $prompt = "Kamu adalah customer support profesional, ramah, dan solutif untuk brand/toko ini.\n" .
            "Tugasmu: Balas komentar atau pesan masuk dari calon pembeli berikut secara singkat, padat, sopan, dan hangat (maksimal 2-3 kalimat).\n\n" .
            "Konteks / Informasi Toko (Knowledge Base):\n" .
            ($knowledgeBase ?: "Kami siap melayani kebutuhan Anda dengan produk berkualitas dan pengiriman cepat ke seluruh Indonesia.") . "\n\n" .
            "Pesan Masuk dari Customer: \"{$incomingMessage}\"\n\n" .
            "Aturan Wajib:\n" .
            "- Jawab langsung ke inti pertanyaan customer.\n" .
            "- Gunakan nada bicara yang ramah dan bersahabat (misal: panggil 'Kak' atau 'Bunda').\n" .
            "- Keluarkan HANYA teks balasan siap kirim tanpa tanda petik pembuka/penutup atau teks pengantar 'Balasan:'.";

        // Gunakan endpoint v1 dan model gemini-3.1-flash-lite yang sudah terbukti bekerja
        try {
            Log::info("Gemini Smart Reply: Mengirim pesan ke AI Gemini...", ['pesan' => $incomingMessage]);

            $response = $this->postGenerate([
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ], 30);

            Log::info("Gemini Smart Reply HTTP Status: " . $response->status());

            if ($response->successful()) {
                $reply = trim($response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');

                if (!empty($reply)) {
                    Log::info("Gemini Smart Reply BERHASIL meracik jawaban: \"{$reply}\"");
                    return $reply;
                }
            }

            Log::error("Gemini Smart Reply Gagal dari Google (HTTP {$response->status()}): " . $response->body());

        } catch (\Throwable $e) {
            Log::error("Exception Gemini Smart Reply: " . $e->getMessage());
        }

        // Fallback jika API benar-benar terputus
        return "Halo Kak! Terima kasih banyak sudah bertanya. Pesan Kakak sudah kami terima dan akan segera kami bantu lebih lanjut ya! ✨";
    }


    // app/Services/GeminiService.php

    /**
     * Multimodal Generation: Gemini "Melihat" Seluruh Foto Carousel & Meracik [Judul + Deskripsi]
     * Mengembalikan array: ['title' => string, 'description' => string]
     */
    public function generateCaptionFromCarousel(
        array $imagePathsOrUrls,
        ?string $originalCaption = null,
        ?string $customPrompt = null,
        ?Team $team = null
    ): array {
        $apiKey = $this->getApiKey($team);

        $fallbackTitle = \Illuminate\Support\Str::limit($originalCaption ?: 'Koleksi Foto Terbaru', 70);
        $fallbackDescription = $originalCaption ?: 'Geser untuk melihat koleksi lengkapnya! ✨ #KoleksiTerbaru';

        try {
            $imageParts = [];

            // 1. Ubah setiap slide foto menjadi inline_data base64 (maksimal 6-8 slide agar efisien)
            foreach (array_slice($imagePathsOrUrls, 0, 8) as $img) {
                if (Storage::disk('public')->exists($img)) {
                    $binary = Storage::disk('public')->get($img);
                } elseif (file_exists($img)) {
                    $binary = file_get_contents($img);
                } else {
                    $binary = Http::timeout(30)->get($img)->body();
                }

                if (strlen($binary) > 1000) {
                    $imageParts[] = [
                        'inline_data' => [
                            'mime_type' => 'image/jpeg',
                            'data'      => base64_encode($binary),
                        ]
                    ];
                }
            }

            if (empty($imageParts)) {
                return ['title' => $fallbackTitle, 'description' => $fallbackDescription];
            }

            // 2. Susun prompt pemahaman carousel
            $promptText = "Perhatikan dan amati seluruh rangkaian foto carousel ini dari slide awal sampai slide akhir secara berurutan.\n" .
                "Sebagai social media strategist profesional untuk BrandPilot AI, tugasmu adalah meracik:\n" .
                "1. 'title': Judul postingan carousel yang memancing rasa penasaran untuk swipe/geser foto (maksimal 70 karakter, TANPA hashtag).\n" .
                "2. 'description': Deskripsi/caption lengkap yang merangkum cerita dari foto-foto ini, ajakan untuk swipe/cek bio (CTA), dan hashtag viral yang relevan.\n\n" .
                ($originalCaption ? "Konteks caption asli kreator: \"{$originalCaption}\"\n" : "") .
                ($customPrompt ? "Instruksi khusus brand: {$customPrompt}\n" : "") .
                "Keluarkan HANYA format JSON valid: {\"title\": \"...\", \"description\": \"...\"}";

            // Masukkan prompt teks ke bagian akhir array parts
            $imageParts[] = ['text' => $promptText];

            Log::info("Gemini AI: Mengirim " . (count($imageParts) - 1) . " slide foto carousel ke Gemini Flash Lite...");

            $response = $this->postGenerate([
                'contents' => [
                    [
                        'parts' => $imageParts,
                    ],
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ],
            ], 90);

            if ($response->successful()) {
                $rawJsonText = trim($response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');
                $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', $rawJsonText);
                $result = json_decode($cleanJson, true);

                if (is_array($result) && !empty($result['title']) && !empty($result['description'])) {
                    Log::info("Gemini AI: Berhasil memahami gambar carousel & meracik caption!", [
                        'title' => $result['title'],
                    ]);

                    return [
                        'title'       => trim($result['title']),
                        'description' => trim($result['description']),
                    ];
                }
            }

        } catch (\Throwable $e) {
            Log::error("Gemini Carousel Understanding Gagal: " . $e->getMessage());
        }

        return [
            'title'       => $fallbackTitle,
            'description' => $fallbackDescription,
        ];
    }
}
