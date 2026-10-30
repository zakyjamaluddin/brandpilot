<?php

namespace App\Filament\Pages;

use App\Models\TenantSetting;
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
                    ->description('Masukkan profil TikTok yang akan dipantau. Setiap video baru otomatis masuk antrean dan tayang pada pemeriksaan berikutnya — paling lambat sekitar 30 menit pada 08.00–20.00 WIB, dan sekitar 2 jam pada 20.00–08.00 WIB.')
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
                                . 'Distribusi berjalan sebagai antrean otomatis. Dengan menyimpan pengaturan ini, kamu menyetujui '
                                . '<a href="/syarat-ketentuan" target="_blank" rel="noopener" style="color:#7c2a41;text-decoration:underline;">ketentuan distribusi otomatis</a>'
                                . ' — termasuk batas satu video terbaru per jendela pemeriksaan.'
                                . '</div>'
                            )),
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

                // Section::make('Gemini AI: Smart Auto-Reply')
                //     ->schema([
                //         Toggle::make('auto_reply_enabled')
                //             ->label('Aktifkan Auto Reply AI (Polling 5 Menit)')
                //             ->live(),

                //         Textarea::make('brand_knowledge_base')
                //             ->label('Brand Knowledge Base (Konteks Bisnis)')
                //             ->rows(4)
                //             ->visible(fn ($get) => $get('auto_reply_enabled')),
                //     ]),

                Section::make('Apify Token — Wajib untuk Auto-Posting')
                    ->description('BrandPilot memakai akun Apify milik Anda sendiri. Kuota dan batasannya ada di akun Anda, jadi tidak dibatasi oleh BrandPilot. Token disimpan terenkripsi dan hanya dipakai untuk mendeteksi video baru di profil TikTok Anda.')
                    ->schema([
                        Placeholder::make('apify_status')
                            ->label('')
                            ->content(fn () => new HtmlString(
                                Filament::getTenant()?->hasApifyToken()
                                    ? '<div style="font-size:13px;line-height:1.6;padding:10px 12px;border-radius:8px;background:#ecfdf5;border:1px solid #10b981;color:#065f46;"><strong>Token tersimpan.</strong> Auto-posting aktif — video baru akan diperiksa sesuai jadwal.</div>'
                                    : '<div style="font-size:13px;line-height:1.6;padding:10px 12px;border-radius:8px;background:#fef3c7;border:1px solid #f59e0b;color:#78350f;"><strong>Auto-posting belum aktif.</strong> Isi Apify Token di bawah supaya video baru bisa terdeteksi.</div>'
                            )),

                        TextInput::make('apify_token')
                            ->label('Apify Token')
                            ->password()
                            ->columnSpanFull()
                            ->placeholder(fn () => Filament::getTenant()?->hasApifyToken()
                                ? 'Tersimpan — kosongkan jika tidak ingin mengubah'
                                : 'apify_api_xxxxxxxxxxxxxxxxxxxxx')
                            ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                                if (blank($value)) {
                                    return;
                                }

                                try {
                                    $res = \Illuminate\Support\Facades\Http::timeout(20)
                                        ->withToken(trim($value))
                                        ->get('https://api.apify.com/v2/users/me');
                                } catch (\Throwable $e) {
                                    $fail('Tidak bisa menghubungi Apify: ' . $e->getMessage());

                                    return;
                                }

                                if (! $res->successful()) {
                                    $fail('Token Apify tidak valid (HTTP ' . $res->status() . '). Periksa lagi di console.apify.com → Integrations → API.');
                                }
                            }),

                        Placeholder::make('apify_tutorial_link')
                            ->label('')
                            ->content(new HtmlString(
                                '<div style="font-size:13px;">'
                                . '<a href="#" @click.prevent="$dispatch(\'open-modal\', { id: \'apify-tutorial\' })" '
                                . 'style="display:inline-block;padding:9px 16px;border-radius:9px;background:linear-gradient(135deg,#7c2a41 0%,#5b0e2d 100%);color:#fff;font-weight:700;text-decoration:none;border:1px solid #B76E79;">'
                                . 'Cara mendapatkan Apify Token'
                                . '</a>'
                                . '<span style="margin-left:10px;color:#78716c;">Gratis, tanpa kartu kredit.</span>'
                                . '</div>'
                            )),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $team = Filament::getTenant();
        $state = $this->form->getState();

        // Simpan data TikTok ke Team
        $teamData = [
            'tiktok_profile_url' => $state['tiktok_profile_url'] ?? null,
            'tiktok_ownership_confirmed' => (bool) ($state['tiktok_ownership_confirmed'] ?? false),
        ];

        // Token Apify hanya ditimpa kalau field diisi (dienkripsi oleh cast model).
        if (filled($state['apify_token'] ?? null)) {
            $teamData['apify_token'] = trim($state['apify_token']);
        }

        $team->update($teamData);

        // Simpan sisa setting ke TenantSetting
        $setting = TenantSetting::where('team_id', $team->id)->first();
        $setting->update([
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