<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeamInvitationController extends Controller
{
    /**
     * Menangani tautan undangan pengelolaan team.
     */
    public function show(Request $request, string $code): View|RedirectResponse
    {
        $team = Team::where('invitation_code', $code)->firstOrFail();

        // Simpan kode undangan di session agar tetap terbawa saat alur Google OAuth
        session(['pending_team_invitation' => $code]);

        // Jika user SUDAH login di BrandPilot:
        if (Auth::check()) {
            $user = Auth::user();

            // Hubungkan user ke workspace / team ini
            $team->members()->syncWithoutDetaching([$user->id]);

            session()->forget('pending_team_invitation');

            // Redirect langsung ke dashboard workspace di Filament
            return redirect("/admin/{$team->slug}")
                ->with('status', "Berhasil! Anda sekarang dapat mengelola workspace {$team->name}.");
        }

        // Jika belum login / belum punya akun: Tampilkan halaman khusus Sign In by Google
        return view('auth.team-invitation', [
            'team' => $team,
            'code' => $code,
        ]);
    }
}
