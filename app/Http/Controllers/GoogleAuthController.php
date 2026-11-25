<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user ke Google OAuth consent screen.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Menangani callback autentikasi dari Google.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            Log::error('Google OAuth Error: '.$e->getMessage());

            $pendingCode = session('pending_team_invitation');
            if ($pendingCode) {
                return redirect()->route('teams.invitation', ['code' => $pendingCode])
                    ->with('error', 'Gagal masuk dengan Google. Silakan coba kembali.');
            }

            return redirect('/admin/login')
                ->with('error', 'Autentikasi Google dibatalkan atau mengalami kegagalan.');
        }

        $email = $googleUser->getEmail();

        if (empty($email)) {
            return redirect('/admin/login')
                ->with('error', 'Akun Google Anda tidak memiliki alamat email publik.');
        }

        // Cari atau buat User baru
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: explode('@', $email)[0],
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => bcrypt(Str::random(32)),
            ]);
        } else {
            // Update google_id atau avatar jika belum ada
            $user->update([
                'google_id' => $user->google_id ?: $googleUser->getId(),
                'avatar' => $googleUser->getAvatar() ?: $user->avatar,
            ]);
        }

        Auth::login($user, remember: true);

        // Periksa apakah login ini dipicu dari tautan undangan tim
        $pendingInvitationCode = session('pending_team_invitation');

        if ($pendingInvitationCode) {
            $team = Team::where('invitation_code', $pendingInvitationCode)->first();

            if ($team) {
                // Hubungkan user ke workspace ini
                $team->members()->syncWithoutDetaching([$user->id]);
                session()->forget('pending_team_invitation');

                return redirect("/admin/{$team->slug}")
                    ->with('status', "Selamat datang! Anda berhasil bergabung dan sekarang dapat mengelola workspace {$team->name}.");
            }
        }

        // Jika tidak ada undangan, arahkan ke team pertama milik user atau default admin
        $firstTeam = $user->teams()->first();
        if ($firstTeam) {
            return redirect("/admin/{$firstTeam->slug}");
        }

        return redirect('/admin');
    }
}
