<?php

namespace App\Filament\Pages;

use App\Models\SocialIntegration;
use App\Services\ComposioService;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;

class ManageIntegrations extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationLabel = 'Integrations';
    protected static ?string $title = 'Social Media Integrations';
    protected  string $view = 'filament.pages.manage-integrations';

    public array $integrations = [];

    public function mount(ComposioService $composio): void
    {
        $team = Filament::getTenant();

        // 1. Sinkronkan secara otomatis akun aktif dari Composio
        $composio->syncConnectedAccounts($team);

        // 2. Tampilkan status terbaru ke layar
        $this->loadIntegrations();
    }

    public function loadIntegrations(): void
    {
        $team = Filament::getTenant();
        $saved = SocialIntegration::where('team_id', $team->id)->get()->keyBy('platform');

        $platforms = ['facebook', 'instagram', 'youtube'];
        $this->integrations = [];

        foreach ($platforms as $p) {
            $this->integrations[$p] = [
                'is_connected' => $saved[$p]->is_connected ?? false,
                'account_name' => $saved[$p]->account_name ?? 'Belum Terhubung',
            ];
        }
    }

    public function connectPlatform(string $platform, ComposioService $composio)
    {
        $team = Filament::getTenant();

        try {
            // Ambil link OAuth Composio v3.1
            $oauthUrl = $composio->initiateConnection($team, $platform);

            // Buka halaman otentikasi login sosmed
            return $this->js("window.location.href = '{$oauthUrl}';");

        } catch (\Throwable $e) {
            Log::error("Gagal Connect Platform [{$platform}]: " . $e->getMessage());

            Notification::make()
                ->title("Gagal Menghubungkan " . ucfirst($platform))
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function disconnectPlatform(string $platform)
    {
        $team = Filament::getTenant();
        SocialIntegration::where('team_id', $team->id)->where('platform', $platform)->delete();
        $this->loadIntegrations();

        Notification::make()
            ->title('Berhasil Terputus')
            ->body("Koneksi {$platform} telah dicabut.")
            ->success()
            ->send();
    }
}
