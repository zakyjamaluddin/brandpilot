<?php

namespace App\Filament\Resources\Teams\Schemas;

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
                        TextInput::make('name')
                            ->required()
                            ->label('Nama Brand / Workspace'),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('customer_email')
                            ->email()
                            ->label('Email Pelanggan'),

                        DateTimePicker::make('subscription_expires_at')
                            ->label('Masa Aktif Hingga')
                            ->helperText('Tentukan batas akhir masa aktif tenant ini.'),

                        Select::make('subscription_status')
                            ->label('Status Langganan')
                            ->options([
                                'active' => 'Active',
                                'trial' => 'Trial',
                                'expired' => 'Expired',
                            ])
                            ->default('active')
                            ->required(),

                        TextInput::make('invitation_url')
                            ->label('Tautan Undangan Kelola (Invitation Link)')
                            ->helperText('Bagikan tautan ini kepada anggota atau pengelola untuk mengelola workspace ini.')
                            ->visible(fn (?Team $record) => $record && filled($record->invitation_code))
                            ->formatStateUsing(fn (?Team $record) => $record ? url('/invitations/'.$record->invitation_code) : null)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
