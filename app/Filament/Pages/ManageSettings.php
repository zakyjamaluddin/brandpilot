<?php

namespace App\Filament\Pages;

use App\Models\TenantSetting;
use App\Services\TikTok\TikWmChecker;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ManageSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Setting';

    protected static ?string $title = 'Workspace & AI Settings';

    protected string $view = 'filament.pages.manage-settings';

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
                    ->description('Masukkan profil TikTok yang akan dipantau. Setiap video atau carousel baru otomatis terdeteksi dan tayang pada pemeriksaan berikutnya — berjalan otomatis setiap 15 menit pada 08.00–22.00 WIB, dan setiap 1 jam pada 22.00–08.00 WIB.')
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

                        Placeholder::make('tos_note')
                            ->label('')
                            ->content(new HtmlString(
                                '<div style="font-size:12.5px;color:#78716c;line-height:1.6;">'
                                .'Distribusi berjalan sebagai antrean otomatis. Dengan menyimpan pengaturan ini, kamu menyetujui '
                                .'<a href="/syarat-ketentuan" target="_blank" rel="noopener" style="color:#7c2a41;text-decoration:underline;">ketentuan distribusi otomatis</a>'
                                .' — termasuk batas satu video terbaru per jendela pemeriksaan.'
                                .'</div>'
                            )),
                    ]),

                Section::make('Gemini AI: Auto Caption')
                    ->description('Biarkan kecerdasan buatan Google Gemini menonton video atau membaca carousel Anda, lalu menulis caption yang persuasif dan sesuai gaya brand Anda.')
                    ->schema([
                        Toggle::make('auto_caption_enabled')
                            ->label('Aktifkan Auto Caption Gemini AI')
                            ->helperText('Jika dinonaktifkan, sistem akan menggunakan caption asli dari postingan TikTok.')
                            ->live(),

                        Textarea::make('custom_caption_prompt')
                            ->label('Instruksi Tambahan (Prompt Khusus)')
                            ->placeholder('Contoh: Gunakan bahasa yang santai, selipkan hashtag #bisnisonline, dan sertakan Call to Action untuk cek link di bio.')
                            ->rows(3)
                            ->visible(fn ($get) => $get('auto_caption_enabled')),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $team = Filament::getTenant();
        $state = $this->form->getState();

        $rawUrl = trim($state['tiktok_profile_url'] ?? '');
        $extractedHandle = (new TikWmChecker)->extractUsername($rawUrl);

        // Simpan data TikTok ke Team
        $team->update([
            'tiktok_profile_url' => $rawUrl ?: null,
            'tiktok_handle' => $extractedHandle ?: $team->tiktok_handle,
            'tiktok_ownership_confirmed' => (bool) ($state['tiktok_ownership_confirmed'] ?? false),
        ]);

        // Simpan sisa setting ke TenantSetting
        $setting = TenantSetting::where('team_id', $team->id)->first();
        if ($setting) {
            $setting->update([
                'auto_caption_enabled' => (bool) ($state['auto_caption_enabled'] ?? false),
                'custom_caption_prompt' => $state['custom_caption_prompt'] ?? null,
                'auto_reply_enabled' => (bool) ($state['auto_reply_enabled'] ?? false),
                'brand_knowledge_base' => $state['brand_knowledge_base'] ?? null,
            ]);
        }

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan!')
            ->body('Akun TikTok Anda kini aktif dipantau untuk sindikasi otomatis ke seluruh sosial media.')
            ->success()
            ->send();
    }
}
