<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Profile;
use App\Filament\Pages\Tenancy\EditTeamProfile;
use App\Filament\Widgets\RecentContentPostsWidget;
use App\Models\Team;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Gate;
use App\Http\Middleware\EnsureTenantHasActiveSubscription;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->favicon(asset('icons/icon-192.png'))
            ->login(\App\Filament\Pages\Auth\Login::class)

            // 1. Multi-Tenancy
            ->tenant(Team::class, slugAttribute: 'slug')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.custom-ambient-theme')
            )
            // 2. METADATA PWA & SERVICE WORKER
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.pwa-head')
            )

            // 3. TOMBOL STICKY INSTALL PWA DI DASAR SIDEBAR:
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn () => view('filament.pwa-sidebar-button')
            )

            ->renderHook(
                PanelsRenderHook::PAGE_START,
                fn () => view('filament.subscription-banner')
            )

            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Profil & Password')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn (): string => Profile::getUrl()),
            ])


            // 2. Tema Elegan: Burgundy (Deep Wine) & Rose Gold
            ->colors([
                'primary' => [
                    50 => '#fbf2f4',
                    100 => '#f6e4e8',
                    200 => '#eccdd4',
                    300 => '#dda6b4',
                    400 => '#ca768d',
                    500 => '#b24c68',
                    600 => '#963650',
                    700 => '#7c2a41',
                    800 => '#5b0e2d', // Burgundy / Deep Wine
                    900 => '#420b22',
                    950 => '#2b0414',
                ],
                'amber' => [ // Rose Gold / Antique Copper
                    50 => '#faf6f3',
                    100 => '#f4ece5',
                    200 => '#e7d6c8',
                    300 => '#d5b9a4',
                    400 => '#c0987e',
                    500 => '#b38165',
                    600 => '#a06e54',
                    700 => '#855844',
                    800 => '#6d4839',
                    900 => '#593c31',
                    950 => '#301e18',
                ],
                'gray' => Color::Slate,
            ])
            ->font('Plus Jakarta Sans')

            // 3. Plugin Shield
            ->plugins([
                FilamentShieldPlugin::make()
                    ->globallySearchable(false)
                    ->scopeToTenant(true)
                    ->tenantOwnershipRelationshipName('team'),
            ])

            // 4. Resource & Pages
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                RecentContentPostsWidget::class,
            ])

            // 5. Middleware Wajib (Ini yang sebelumnya hilang)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->tenantMiddleware([
                EnsureTenantHasActiveSubscription::class,
            ])
            ->passwordReset()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        // 2. KUNCI SHIELD: Super Admin otomatis diizinkan membuka semua menu
        Gate::before(function ($user, $ability) {
            if ($user->id === 1 || $user->roles()->where('name', 'super_admin')->exists()) {
                return true;
            }

            return null;
        });
    }
}
