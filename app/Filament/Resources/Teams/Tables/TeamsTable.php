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

                TextColumn::make('apify_token')
                    ->label('Apify Token')
                    ->formatStateUsing(fn ($state) => filled($state) ? 'Terisi' : 'Belum')
                    ->badge()
                    ->color(fn ($state) => filled($state) ? 'success' : 'danger'),

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
