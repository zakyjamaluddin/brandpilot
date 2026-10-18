<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Profile extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Profil Saya';
    protected static ?string $title = 'Pengaturan Akun & Brand';
    protected string $view = 'filament.pages.profile';

    // Sembunyikan dari sidebar agar menu samping tetap rapi
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $team = Filament::getTenant();

        $this->form->fill([
            // Data Brand / Tenant
            'brand_name'    => $team?->name,
            'tiktok_handle' => $team?->tiktok_handle,

            // Data User
            'name'          => $user->name,
            'email'         => $user->email,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Identitas Brand / Workspace')
                    ->description('Ubah nama brand atau toko Anda yang tampil di dashboard BrandPilot.')
                    ->schema([
                        TextInput::make('brand_name')
                            ->label('Nama Brand / Toko')
                            ->placeholder('Contoh: Fashion Glow Official')
                            ->required(),

                        TextInput::make('tiktok_handle')
                            ->label('Username TikTok (@username)')
                            ->placeholder('fashionglow.id')
                            ->helperText('Username TikTok akun bisnis Anda.'),
                    ]),

                Section::make('Informasi Pengguna')
                    ->description('Ubah nama panggilan Anda.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap / Panggilan')
                            ->required(),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->disabled()
                            ->helperText('Email terhubung dengan verifikasi pembayaran Lynk.id.'),
                    ]),

                Section::make('Keamanan & Ganti Password')
                    ->description('Ganti password sementara Anda dengan password rahasia baru.')
                    ->schema([
                        TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->rule(Password::default()),

                        TextInput::make('new_password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->revealable()
                            ->same('new_password')
                            ->requiredWith('new_password'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $user = auth()->user();
        $team = Filament::getTenant();

        // 1. Simpan Nama Brand & TikTok Handle
        if ($team) {
            $team->update([
                'name'          => $state['brand_name'],
                'tiktok_handle' => $state['tiktok_handle'],
            ]);
        }

        // 2. Simpan Nama Panggilan User
        $user->name = $state['name'];

        // 3. Simpan Password Baru (Jika diisi)
        if (! empty($state['new_password'])) {
            $user->password = Hash::make($state['new_password']);
        }

        $user->save();

        // Refresh data form
        $this->form->fill([
            'brand_name'    => $team?->name,
            'tiktok_handle' => $team?->tiktok_handle,
            'name'          => $user->name,
            'email'         => $user->email,
        ]);

        Notification::make()
            ->title('Data Brand & Password Berhasil Disimpan!')
            ->success()
            ->send();
    }
}