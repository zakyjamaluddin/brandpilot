<?php

namespace App\Filament\Resources\Teams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->weight('bold')->searchable(),
                TextColumn::make('slug'),
                TextColumn::make('subscription_expires_at')
                    ->label('Masa Aktif Hingga')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('composioKey.label')->label('Composio Key')->placeholder('Belum Diatur'),
                TextColumn::make('geminiKey.label')->label('Gemini Key')->placeholder('Belum Diatur'),
                TextColumn::make('apifyKey.label')->label('Apify Key')->placeholder('Belum Diatur'),

                BadgeColumn::make('subscription_status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'trial',
                        'danger'  => 'expired',
                    ]),
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
