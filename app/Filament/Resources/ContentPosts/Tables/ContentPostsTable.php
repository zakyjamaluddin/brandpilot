<?php

namespace App\Filament\Resources\ContentPosts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;

class ContentPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Buat')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('original_caption')
                    ->label('Caption')
                    ->limit(40),

                Tables\Columns\BadgeColumn::make('caption_type')
                    ->label('Tipe Caption')
                    ->colors([
                        'amber' => 'gemini_ai',
                        'gray'  => 'manual',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => 'published',
                        'danger'  => 'failed',
                    ]),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Jadwal Tayang')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_views')
                    ->label('Views')
                    ->badge()
                    ->color('amber'),

                Tables\Columns\TextColumn::make('total_comments')
                    ->label('Comments')
                    ->badge()
                    ->color('primary'),
            ])
            ->defaultSort('created_at', 'desc')
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
