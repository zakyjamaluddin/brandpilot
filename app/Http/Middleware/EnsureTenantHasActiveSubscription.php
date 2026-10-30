<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantHasActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        // Super admin tidak boleh terkunci oleh status langganan tenant mana pun.
        $user = Filament::auth()->user();

        if ($user instanceof \App\Models\User && $user->isSuperAdmin()) {
            return $next($request);
        }

        $team = Filament::getTenant();

        if (! $team) {
            return $next($request);
        }

        // Cek apakah masa aktif sudah lewat
        $isExpired = false;

        if ($team->subscription_status === 'expired') {
            $isExpired = true;
        } elseif ($team->subscription_expires_at && $team->subscription_expires_at->isPast()) {
            $isExpired = true;
            // Sinkronkan status di database jika belum 'expired'
            if ($team->subscription_status !== 'expired') {
                $team->update(['subscription_status' => 'expired']);
            }
        }

        // Jika langganan habis:
        if ($isExpired) {
            // Rute yang TETAP BOLEH diakses: Dashboard, Subscription, Documentation, dan Logout
            $allowedRoutes = [
                'filament.admin.pages.dashboard',
                'filament.admin.pages.manage-subscription',
                'filament.admin.pages.documentation-page',
                'filament.admin.auth.logout',
            ];

            $currentRoute = $request->route()?->getName();

            // Jika mencoba membuka menu lain (Integrations, Setting, dll), REDIRECT ke Subscription!
            if (! in_array($currentRoute, $allowedRoutes)) {
                Notification::make()
                    ->title('Masa Langganan Habis')
                    ->body('Fitur ini terkunci. Silakan perpanjang langganan untuk melanjutkan.')
                    ->danger()
                    ->send();

                return redirect()->route('filament.admin.pages.manage-subscription', ['tenant' => $team->slug]);
            }
        }

        return $next($request);
    }
}