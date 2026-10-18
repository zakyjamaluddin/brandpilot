<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestPaymentWebhook extends Command
{
    protected $signature = 'brandpilot:test-payment-webhook {email} {bulan=1}';
    protected $description = 'Simulasikan email notifikasi pembayaran Lynk.id masuk via Webhook';

    public function handle()
    {
        $email = $this->argument('email');
        $bulan = $this->argument('bulan');

        $this->info("Mensimulasikan email pembayaran Lynk.id...");
        $this->line("Email Pembeli: {$email}");
        $this->line("Paket: {$bulan} Bulan");

        $fakeEmailBody = "Halo Admin, Pembayaran Berhasil!\n" .
                         "Detail Transaksi:\n" .
                         "Produk: Subscription {$bulan} Bulan | Brandpilot - Autopilot Cross-Posting TikTok #1 di Indonesia\n" .
                         "Email Pembeli: {$email}\n" .
                         "Total: Rp 39.000\n" .
                         "Status: Lunas via Lynk.id";

        $url = url('/api/webhooks/composio/gmail-payment');
        $this->line("Menembak ke Webhook: {$url}");

        $response = Http::post($url, [
            'from'    => 'no-reply@lynk.id',
            'subject' => 'Pembayaran Berhasil via Lynk.id',
            'snippet' => $fakeEmailBody,
            'body'    => $fakeEmailBody,
        ]);

        $this->info("Status Response: HTTP " . $response->status());
        $this->line("Response Body: " . $response->body());
    }
}