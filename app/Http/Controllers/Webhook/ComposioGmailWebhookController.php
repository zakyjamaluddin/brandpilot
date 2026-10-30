<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Mail\SubscriptionRenewedMail;
use App\Mail\WelcomeNewSubscriberMail;
use App\Models\Team;
use App\Models\TenantSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ComposioGmailWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Webhook ini bisa memperpanjang langganan, jadi wajib bertoken.
        // Token dikirim lewat header X-Webhook-Token atau query ?token=.
        $expected = (string) config('services.composio.webhook_secret');

        if ($expected !== '') {
            $given = (string) ($request->header('X-Webhook-Token') ?: $request->query('token', ''));

            if (! hash_equals($expected, $given)) {
                abort(403, 'Token webhook tidak valid.');
            }
        }

        $payload = $request->all();
        Log::info('Composio Core Gmail Webhook diterima:', $payload);

        // Bentuk event Composio bisa berbeda antar versi: isi email ada di `data.*`
        // (message_text / payload.body.data base64 / preview.body) atau `payload.*`.
        $event = $payload['data'] ?? ($payload['payload'] ?? $payload);

        $subject = (string) ($event['subject'] ?? ($event['preview']['subject'] ?? ($payload['subject'] ?? '')));
        $sender  = (string) ($event['sender'] ?? ($event['from'] ?? ($payload['from'] ?? '')));

        $rawBody = $event['message_text']
            ?? ($event['payload']['body']['data'] ?? null)
            ?? ($event['body']['data'] ?? null)
            ?? ($event['body'] ?? null);

        if (is_string($rawBody) && $rawBody !== '' && ! str_contains($rawBody, '<')) {
            $decoded = base64_decode($rawBody, true);

            if ($decoded !== false) {
                $rawBody = $decoded;
            }
        }

        $body = is_string($rawBody) ? strip_tags(html_entity_decode($rawBody)) : '';
        $body = trim((string) preg_replace('/\s+/', ' ', $body));

        if ($body === '') {
            $body = trim((string) ($event['preview']['body'] ?? ($event['snippet'] ?? '')));
        }

        // 1. Validasi: Pastikan email berkaitan dengan pembelian Lynk.id
        $isLynkEmail = str_contains(strtolower($sender), 'lynk.id') || 
                       str_contains(strtolower($subject), 'berhasil') || 
                       str_contains(strtolower($body), 'brandpilot') ||
                       str_contains(strtolower($body), 'pembayaran');

        if (! $isLynkEmail) {
            return response()->json(['status' => 'ignored', 'message' => 'Bukan email transaksi Lynk.id.'], 200);
        }

        // 1b. Hanya produk BrandPilot yang membuka akses — produk lain di Lynk.id diabaikan.
        if (! str_contains(strtolower($subject . ' ' . $body), 'brandpilot')) {
            Log::info('Email Lynk.id diabaikan: produk bukan BrandPilot.', ['subject' => $subject]);

            return response()->json(['status' => 'ignored', 'message' => 'Produk bukan BrandPilot.'], 200);
        }

        // 2. Ekstrak Email Pembeli dari Isi Email
        $buyerEmail = $this->extractBuyerEmail($body);

        if (! $buyerEmail) {
            // Email "Receipt for your purchase" (salinan untuk pembeli) tidak memuat email pembeli.
            // Itu bukan notifikasi penjualan, jadi cukup diabaikan — bukan error.
            Log::info('Email Lynk.id tanpa email pembeli, dilewati.', ['subject' => $subject]);

            return response()->json(['status' => 'ignored', 'message' => 'Email tanpa email pembeli.'], 200);
        }

        // 3. Tentukan Durasi Paket (1, 6, atau 12 Bulan)
        $durationMonths = $this->detectDurationMonths($subject . ' ' . $body);
        Log::info("Transaksi Lynk.id valid: Email [{$buyerEmail}], Durasi [{$durationMonths} Bulan]");

        // Idempotensi: klaim message_id setelah email lolos validasi, supaya satu email
        // hanya diproses sekali (retry Composio tidak menambah masa aktif lagi).
        $messageId = (string) ($payload['data']['message_id'] ?? ($payload['id'] ?? ''));

        if ($messageId !== '' && ! Cache::add('composio:email:' . $messageId, true, now()->addDays(30))) {
            Log::info("Email {$messageId} sudah pernah diproses, dilewati.");

            return response()->json(['status' => 'duplicate', 'message' => 'Email ini sudah diproses.'], 200);
        }

        // 4. EKSEKUSI: USER BARU vs USER LAMA
        $user = User::where('email', $buyerEmail)->first();

        // ==============================================================
        // SKENARIO A: USER BARU (Otomatis Daftar & Buat Workspace)
        // ==============================================================
        if (! $user) {
            Log::info("Mendaftarkan User & Tenant Baru untuk [{$buyerEmail}]...");

            // A. Buat Password Acak
            $plainPassword = Str::password(10, letters: true, numbers: true, symbols: false);

            $cleanName = ucfirst(explode('@', $buyerEmail)[0]);

            // B. Buat Record User
            $user = User::create([
                'name'     => $cleanName,
                'email'    => $buyerEmail,
                'password' => Hash::make($plainPassword),
            ]);

            // C. Buat Record Team (Tenant)
            $teamSlug = Str::slug($cleanName) . '-' . Str::lower(Str::random(4));
            $team = Team::create([
                'name'                    => $cleanName . ' Brand',
                'slug'                    => $teamSlug,
                'customer_email'          => $buyerEmail,
                'subscription_status'     => 'active',
                'subscription_expires_at' => now()->addMonths($durationMonths),
            ]);

            // D. Relasikan User dengan Team (1 User = 1 Tenant)
            $user->teams()->attach($team);

            // E. Buat Default Setting untuk Tenant
            TenantSetting::create([
                'team_id'              => $team->id,
                'schedule_mode'        => 'dynamic_delay',
                'auto_caption_enabled' => true,
                'auto_reply_enabled'   => true,
            ]);

            // F. Pasang Role Super Admin (Shield)
            try {
                setPermissionsTeamId($team->id);
                $role = Role::firstOrCreate([
                    'name'       => 'super_admin',
                    'guard_name' => 'web',
                    'team_id'    => $team->id,
                ]);
                $user->assignRole($role);
            } catch (\Throwable $e) {
                Log::warning("Shield role assign warning: " . $e->getMessage());
            }

            // G. Kirim Email Selamat Datang & Kredensial Login
            try {
                Mail::to($buyerEmail)->send(new WelcomeNewSubscriberMail($user, $team, $plainPassword, $durationMonths));
                Log::info("Email selamat datang & password terkirim ke [{$buyerEmail}]");
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim email welcome: " . $e->getMessage());
            }

            return response()->json([
                'status'  => 'success',
                'type'    => 'new_user_created',
                'user_id' => $user->id,
                'team_id' => $team->id,
                'expires' => $team->subscription_expires_at->toDateTimeString(),
            ], 200);
        }

        // ==============================================================
        // SKENARIO B: USER LAMA (Perpanjangan Masa Aktif)
        // ==============================================================
        Log::info("User lama terdeteksi [{$buyerEmail}]. Memperpanjang langganan...");
        $team = $user->teams()->first();

        if (! $team) {
            $team = Team::where('customer_email', $buyerEmail)->first();
        }

        if ($team) {
            // Hitung tanggal dasar: jika masih aktif, tambahkan dari tanggal expired lama
            $baseDate = ($team->subscription_expires_at && $team->subscription_expires_at->isFuture())
                ? $team->subscription_expires_at
                : now();

            $newExpiresAt = (clone $baseDate)->addMonths($durationMonths);

            $team->update([
                'subscription_status'     => 'active',
                'subscription_expires_at' => $newExpiresAt,
            ]);

            // Kirim Email Konfirmasi Perpanjangan
            try {
                Mail::to($buyerEmail)->send(new SubscriptionRenewedMail($user, $team, $durationMonths));
                Log::info("Email perpanjangan terkirim ke [{$buyerEmail}]");
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim email perpanjangan: " . $e->getMessage());
            }

            return response()->json([
                'status'  => 'success',
                'type'    => 'subscription_renewed',
                'team_id' => $team->id,
                'expires' => $newExpiresAt->toDateTimeString(),
            ], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'Tenant tidak ditemukan.'], 404);
    }

    /**
     * Helper Regex untuk mengekstrak email pembeli
     */
    protected function extractBuyerEmail(string $text): ?string
    {
        // Cari format email yang bukan no-reply@lynk.id
        preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches);
        $emails = $matches[0] ?? [];

        foreach ($emails as $email) {
            $lower = strtolower($email);
            if (! str_contains($lower, 'lynk.id') && ! str_contains($lower, 'composio') && ! str_contains($lower, 'google')) {
                return $email;
            }
        }

        return null;
    }

    /**
     * Helper mendeteksi paket berdasarkan teks email
     */
    protected function detectDurationMonths(string $content): int
    {
        $lower = strtolower($content);

        if (str_contains($lower, '12 bulan') || str_contains($lower, '1 tahun') || str_contains($lower, 'jokgvgpvlq9x')) {
            return 12;
        }

        if (str_contains($lower, '6 bulan') || str_contains($lower, 'xj9nkn994yeo')) {
            return 6;
        }

        // Default 1 Bulan
        return 1;
    }
}