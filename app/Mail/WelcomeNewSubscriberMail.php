<?php

namespace App\Mail;

use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeNewSubscriberMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Team $team,
        public string $plainPassword,
        public int $durationMonths
    ) {}

    public function build()
    {
        return $this->subject('🎉 Selamat Datang di BrandPilot AI! Akses Akun Anda')
            ->view('emails.welcome-subscriber');
    }
}