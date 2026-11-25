<?php

namespace App\Filament\Resources\Teams\Tables;

use App\Models\Team;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
                        'danger' => 'expired',
                    ]),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('invitation')
                    ->label('Tautan Kelola')
                    ->icon('heroicon-o-link')
                    ->color('success')
                    ->tooltip('Lihat & salin tautan undangan mengelola tim')
                    ->modalHeading(fn (Team $record) => "Tautan Undangan Kelola: {$record->name}")
                    ->modalDescription('Siapa pun yang membuka tautan ini dapat login/mendaftar dengan Google dan otomatis dapat mengelola workspace ini.')
                    ->form([
                        TextInput::make('invitation_url')
                            ->label('URL Tautan Undangan')
                            ->default(fn (Team $record) => $record->invitation_code ? url('/invitations/'.$record->invitation_code) : null)
                            ->readOnly()
                            ->copyable()
                            ->helperText('Klik ikon salin untuk menyalin tautan ke clipboard Anda.'),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->extraModalFooterActions([
                        Action::make('regenerate')
                            ->label('Buat Ulang Tautan Baru')
                            ->color('danger')
                            ->icon('heroicon-o-arrow-path')
                            ->requiresConfirmation()
                            ->modalHeading('Konfirmasi Buat Ulang Tautan')
                            ->modalDescription('Tautan undangan lama akan hangus dan tidak dapat digunakan lagi.')
                            ->action(function (Team $record) {
                                $record->regenerateInvitationCode();
                                Notification::make()
                                    ->title('Tautan Undangan Berhasil Diperbarui')
                                    ->success()
                                    ->send();
                            }),
                    ]),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
