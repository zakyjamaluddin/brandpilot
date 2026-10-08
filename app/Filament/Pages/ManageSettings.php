<?php

namespace App\Filament\Pages;

use App\Models\TenantSetting;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Setting';
    protected static ?string $title = 'Workspace & AI Settings';
    protected  string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $team = Filament::getTenant();
        $setting = TenantSetting::firstOrCreate(['team_id' => $team->id]);

        $this->form->fill([
            // Data dari Team (TikTok)
            'tiktok_profile_url' => $team->tiktok_profile_url,
            'tiktok_ownership_confirmed' => (bool) $team->tiktok_ownership_confirmed,

            // Data dari TenantSetting
            'schedule_mode' => $setting->schedule_mode,
            'static_posting_hours' => $setting->static_posting_hours,
            'auto_caption_enabled' => (bool) $setting->auto_caption_enabled,
            'custom_caption_prompt' => $setting->custom_caption_prompt,
            'auto_reply_enabled' => (bool) $setting->auto_reply_enabled,
            'brand_knowledge_base' => $setting->brand_knowledge_base,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Sumber Konten: TikTok Profile')
                    ->description('Masukkan profil TikTok yang akan dipantau oleh mesin scraping BrandPilot.')
                    ->schema([
                        TextInput::make('tiktok_profile_url')
                            ->label('URL Profil TikTok')
                            ->placeholder('https://www.tiktok.com/@brandkamu')
                            ->url()
                            ->required(),

                        Checkbox::make('tiktok_ownership_confirmed')
                            ->label('Saya menyatakan bahwa akun TikTok ini adalah milik saya/bisnis saya sendiri dan saya memiliki hak penuh atas video yang diposting.')
                            ->required()
                            ->accepted(),
                    ]),

                Section::make('Mode Distribusi Postingan')
                    ->description('Sistem otomatis memeriksa video baru setiap 30 menit.')
                    ->schema([
                        Radio::make('schedule_mode')
                            ->label('Waktu Posting ke FB, IG, dan YouTube')
                            ->options([
                                'dynamic_delay' => 'Segera Diposting (Begitu terdeteksi pada siklus pooling)',
                                'static_fixed'  => 'Jadwal Jam Tertentu (Statis)',
                            ])
                            ->live()
                            ->required(),

                        TagsInput::make('static_posting_hours')
                            ->label('Jam Posting Statis')
                            ->placeholder('Contoh: 07:00, 16:00')
                            ->visible(fn ($get) => $get('schedule_mode') === 'static_fixed')
                            ->helperText('Ketik jam dan tekan Enter. Contoh: 07:00, 16:00.'),
                    ]),

                Section::make('Gemini AI: Auto Caption')
                    ->schema([
                        Toggle::make('auto_caption_enabled')
                            ->label('Aktifkan Auto Caption Gemini AI')
                            ->helperText('Jika dinonaktifkan, menggunakan caption asli dari TikTok.')
                            ->live(),

                        Textarea::make('custom_caption_prompt')
                            ->label('Instruksi Tambahan (Prompt Khusus)')
                            ->rows(3)
                            ->visible(fn ($get) => $get('auto_caption_enabled')),
                    ]),

                Section::make('Gemini AI: Smart Auto-Reply')
                    ->schema([
                        Toggle::make('auto_reply_enabled')
                            ->label('Aktifkan Auto Reply AI (Polling 5 Menit)')
                            ->live(),

                        Textarea::make('brand_knowledge_base')
                            ->label('Brand Knowledge Base (Konteks Bisnis)')
                            ->rows(4)
                            ->visible(fn ($get) => $get('auto_reply_enabled')),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $team = Filament::getTenant();
        $state = $this->form->getState();

        // Simpan data TikTok ke Team
        $team->update([
            'tiktok_profile_url' => $state['tiktok_profile_url'] ?? null,
            'tiktok_ownership_confirmed' => (bool) ($state['tiktok_ownership_confirmed'] ?? false),
        ]);

        // Simpan sisa setting ke TenantSetting
        $setting = TenantSetting::where('team_id', $team->id)->first();
        $setting->update([
            'schedule_mode' => $state['schedule_mode'],
            'static_posting_hours' => $state['static_posting_hours'] ?? null,
            'auto_caption_enabled' => (bool) ($state['auto_caption_enabled'] ?? false),
            'custom_caption_prompt' => $state['custom_caption_prompt'] ?? null,
            'auto_reply_enabled' => (bool) ($state['auto_reply_enabled'] ?? false),
            'brand_knowledge_base' => $state['brand_knowledge_base'] ?? null,
        ]);

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan!')
            ->success()
            ->send();
    }
}