<?php

namespace App\Filament\Resources\InboxThreads\Tables;

use App\Filament\Resources\InboxThreads\Pages\ViewInboxThread;
use App\Models\InboxThread;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class InboxThreadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('platform')
                    ->colors([
                        'primary' => 'facebook',
                        'danger' => 'youtube',
                        'amber' => 'instagram',
                    ]),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                Tables\Columns\TextColumn::make('sender_name')
                    ->label('Customer')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('last_snippet')
                    ->label('Pesan Terakhir')
                    ->limit(50),

                Tables\Columns\TextColumn::make('last_interaction_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('last_interaction_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(fn (InboxThread $record): string => ViewInboxThread::getUrl([$record->id]));
    }
}
