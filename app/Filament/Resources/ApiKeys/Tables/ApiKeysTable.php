<?php

namespace App\Filament\Resources\ApiKeys\Tables;

use App\Models\ApiKey;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApiKeysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                BadgeColumn::make('provider')
                    ->label('Provider')
                    ->colors([
                        'primary' => 'composio',
                        'amber'   => 'gemini',
                        'danger'  => 'apify',
                    ]),

                TextColumn::make('label')
                    ->label('Nama Label')
                    ->searchable()
                    ->weight('bold'),

                // PERBAIKAN DI KOLOM INI:
                TextColumn::make('assigned_tenant')
                    ->label('Digunakan Oleh Tenant')
                    ->state(function (ApiKey $record) {
                        return match ($record->provider) {
                            'composio' => \App\Models\Team::where('composio_api_key_id', $record->id)->value('name'),
                            'gemini'   => \App\Models\Team::where('gemini_api_key_id', $record->id)->value('name'),
                            'apify'    => \App\Models\Team::where('apify_api_key_id', $record->id)->value('name'),
                            default    => null,
                        };
                    })
                    ->placeholder('Tersedia (Belum Terpakai)')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
