<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComposioGmailWebhookController extends Controller
{
    /**
     * Menangani trigger Gmail masuk dari Composio saat ada email penjualan Lynk.id
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('Composio Gmail Webhook Payload Diterima:', $payload);

        // Ambil data subjek atau snippet email
        $subject = $payload['subject'] ?? '';
        $body = $payload['body'] ?? ($payload['snippet'] ?? '');
        $sender = $payload['from'] ?? '';

        // Pastikan email berasal dari Lynk.id atau berkaitan dengan transaksi
        if (str_contains(strtolower($sender), 'lynk.id') || str_contains(strtolower($subject), 'berhasil')) {
            
            // Ekstrak email pembeli (Regex mencari format email di body/snippet)
            preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $body, $matches);
            $buyerEmail = $matches[0] ?? null;

            if ($buyerEmail) {
                // Cocokkan dengan tenant yang memiliki customer_email tersebut
                $team = Team::where('customer_email', $buyerEmail)->first();

                if ($team) {
                    // Perpanjang 30 hari dari tanggal sekarang atau tanggal expiry terakhir
                    $currentExpiry = ($team->subscription_expires_at && $team->subscription_expires_at->isFuture())
                        ? $team->subscription_expires_at
                        : now();

                    $team->update([
                        'subscription_status' => 'active',
                        'subscription_expires_at' => $currentExpiry->addDays(30),
                    ]);

                    Log::info("Langganan berhasil diaktifkan untuk Tenant ID: {$team->id} ({$team->name})");

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Subscription activated successfully.',
                        'team_id' => $team->id,
                    ], 200);
                }
            }
        }

        return response()->json(['status' => 'ignored', 'message' => 'Email bukan transaksi valid atau user tidak ditemukan.'], 200);
    }
}