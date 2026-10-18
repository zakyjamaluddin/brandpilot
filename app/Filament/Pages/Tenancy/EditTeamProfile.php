<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;

class EditTeamProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Pengaturan Brand / Workspace';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Identitas Brand / Bisnis')
                    ->description('Ubah nama workspace dan username TikTok toko Anda.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Brand / Workspace')
                            ->placeholder('Contoh: Fashion Glow Official')
                            ->required(),

                        TextInput::make('tiktok_handle')
                            ->label('Username TikTok (@username)')
                            ->placeholder('fashionglow.id')
                            ->helperText('Username TikTok utama untuk brand ini.'),

                        TextInput::make('customer_email')
                            ->label('Email Penagihan (Billing)')
                            ->disabled()
                            ->helperText('Email yang terhubung dengan transaksi Lynk.id.'),
                    ]),
            ]);
    }
}