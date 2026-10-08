<?php

namespace App\Filament\Resources\Teams\Schemas;

use App\Models\ApiKey;
use App\Models\Team;
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
                ]),

            Section::make('Alokasi API Key Khusus Tenant Ini (1:1 Rule)')
                ->description('Pilih API Key yang belum digunakan oleh tenant lain.')
                ->schema([
                    // 1. Pilih Composio Key
                    Select::make('composio_api_key_id')
                        ->label('Composio API Key')
                        ->options(function (?Team $record) {
                            return ApiKey::where('provider', 'composio')
                                ->where('is_active', true)
                                ->where(function ($query) use ($record) {
                                    $query->whereDoesntHave('assignedTeam');
                                    if ($record && $record->composio_api_key_id) {
                                        $query->orWhere('id', $record->composio_api_key_id);
                                    }
                                })
                                ->pluck('label', 'id');
                        })
                        ->searchable()
                        ->placeholder('Pilih Key Composio yang tersedia'),

                    // 2. Pilih Gemini Key
                    Select::make('gemini_api_key_id')
                        ->label('Google Gemini API Key')
                        ->options(function (?Team $record) {
                            return ApiKey::where('provider', 'gemini')
                                ->where('is_active', true)
                                ->where(function ($query) use ($record) {
                                    $query->whereDoesntHave('assignedTeam');
                                    if ($record && $record->gemini_api_key_id) {
                                        $query->orWhere('id', $record->gemini_api_key_id);
                                    }
                                })
                                ->pluck('label', 'id');
                        })
                        ->searchable()
                        ->placeholder('Pilih Key Gemini yang tersedia'),

                    // 3. Pilih Apify Key
                    Select::make('apify_api_key_id')
                        ->label('Apify (TikTok Scraper) API Key')
                        ->options(function (?Team $record) {
                            return ApiKey::where('provider', 'apify')
                                ->where('is_active', true)
                                ->where(function ($query) use ($record) {
                                    $query->whereDoesntHave('assignedTeam');
                                    if ($record && $record->apify_api_key_id) {
                                        $query->orWhere('id', $record->apify_api_key_id);
                                    }
                                })
                                ->pluck('label', 'id');
                        })
                        ->searchable()
                        ->placeholder('Pilih Key Apify yang tersedia'),
                    ]),
            ]);
    }
}
