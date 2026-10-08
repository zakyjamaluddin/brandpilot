<?php

namespace App\Filament\Resources\ApiKeys\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApiKeyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kredensial API Eksternal')
                ->description('Simpan dan alokasikan API key untuk tenant BrandPilot.')
                ->schema([
                    Radio::make('provider')
                        ->label('Layanan Provider')
                        ->options([
                            'composio' => 'Composio v3.1',
                            'gemini'   => 'Google Gemini AI',
                            'apify'    => 'Apify (TikTok Scraper)',
                        ])
                        ->required()
                        ->inline(),

                    TextInput::make('label')
                        ->label('Label / Nama Pengenal')
                        ->placeholder('Misal: Gemini Production Akun #1')
                        ->required(),

                    TextInput::make('api_key')
                        ->label('API Key Token')
                        ->password()
                        ->revealable()
                        ->required(),

                    Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->default(true),
                ]),
            ]);
    }
}
