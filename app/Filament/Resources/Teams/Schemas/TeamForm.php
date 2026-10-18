<?php

namespace App\Filament\Resources\Teams\Schemas;

use App\Models\ApiKey;
use App\Models\Team;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Workspace')
                ->schema([
                    TextInput::make('name')->required()->label('Nama Brand / Workspace'),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    TextInput::make('customer_email')->email()->label('Email Pelanggan'),
                    DateTimePicker::make('subscription_expires_at')
                        ->label('Masa Aktif Hingga')
                        ->helperText('Tentukan batas akhir masa aktif tenant ini.'),

                    Select::make('subscription_status')
                        ->label('Status Langganan')
                        ->options([
                            'active'  => 'Active',
                            'trial'   => 'Trial',
                            'expired' => 'Expired',
                        ])
                        ->default('active')
                        ->required(),
                ]),

            // app/Filament/Resources/TeamResource.php

            Section::make('Alokasi API Key Khusus Tenant Ini (1:1 Rule)')
                ->description('Pilih API Key yang belum digunakan oleh tenant lain.')
                ->schema([
                    // 1. COMPOSIO API KEY
                    Select::make('composio_api_key_id')
                        ->label('Composio API Key')
                        ->options(function (?Team $record) {
                            $query = ApiKey::where('provider', 'composio')->where('is_active', true);

                            // Ambil semua ID key Composio yang sudah dipakai oleh tenant lain
                            $usedKeyIds = Team::query()
                                ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                ->whereNotNull('composio_api_key_id')
                                ->pluck('composio_api_key_id')
                                ->toArray();

                            // Tampilkan HANYA key yang belum pernah terpakai
                            return $query->whereNotIn('id', $usedKeyIds)->pluck('label', 'id');
                        })
                        ->searchable()
                        ->placeholder('Pilih Key Composio yang tersedia'),

                    // 2. GEMINI API KEY
                    Select::make('gemini_api_key_id')
                        ->label('Google Gemini API Key')
                        ->options(function (?Team $record) {
                            $query = ApiKey::where('provider', 'gemini')->where('is_active', true);

                            // Ambil semua ID key Gemini yang sudah dipakai oleh tenant lain
                            $usedKeyIds = Team::query()
                                ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                ->whereNotNull('gemini_api_key_id')
                                ->pluck('gemini_api_key_id')
                                ->toArray();

                            // Tampilkan HANYA key yang belum pernah terpakai
                            return $query->whereNotIn('id', $usedKeyIds)->pluck('label', 'id');
                        })
                        ->searchable()
                        ->placeholder('Pilih Key Gemini yang tersedia'),

                    // 3. APIFY API KEY
                    Select::make('apify_api_key_id')
                        ->label('Apify (TikTok Scraper) API Key')
                        ->options(function (?Team $record) {
                            $query = ApiKey::where('provider', 'apify')->where('is_active', true);

                            // Ambil semua ID key Apify yang sudah dipakai oleh tenant lain
                            $usedKeyIds = Team::query()
                                ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                ->whereNotNull('apify_api_key_id')
                                ->pluck('apify_api_key_id')
                                ->toArray();

                            // Tampilkan HANYA key yang belum pernah terpakai
                            return $query->whereNotIn('id', $usedKeyIds)->pluck('label', 'id');
                        })
                        ->searchable()
                        ->placeholder('Pilih Key Apify yang tersedia'),
                ]),
            ]);
    }
}
