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
                ->description('Key Composio dan Gemini dipakai BERSAMA oleh semua tenant — tidak perlu di-assign per tenant. Token Apify tidak lagi di sini: setiap tenant mengisi tokennya sendiri di menu Setting.')
                ->schema([
                    Radio::make('provider')
                        ->label('Layanan Provider')
                        ->options([
                            'composio' => 'Composio v3.1 (shared)',
                            'gemini'   => 'Google Gemini AI (shared, fallback antar-key)',
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
