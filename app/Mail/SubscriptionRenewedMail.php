<?php

namespace App\Mail;

use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Team $team,
        public int $durationMonths
    ) {}

    public function build()
    {
        return $this->subject('⚡ Langganan BrandPilot AI Anda Berhasil Diperpanjang!')
            ->view('emails.subscription-renewed');
    }
}