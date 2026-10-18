<?php

namespace App\Filament\Widgets;

use App\Models\ContentPost;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentContentPostsWidget extends BaseWidget
{
    protected static ?string $heading = '5 Konten Terakhir';
    
    // Tampilkan selebar layar (full width di bawah Greeting Widget)
    protected int | string | array $columnSpan = 'full';

    // Urutan posisi widget di dashboard (Greeting widget = 1, tabel ini = 2)
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $tenantId = Filament::getTenant()?->id;

        return $table
            ->query(
                ContentPost::query()
                    ->where('team_id', $tenantId)
                    ->latest()
                    ->limit(5)
            )
            ->paginated(false) // Cukup tampilkan 5 baris tanpa nomor halaman
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul / Konten')
                    ->description(fn (ContentPost $record) => \Illuminate\Support\Str::limit($record->original_caption, 50))
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('media_type')
                    ->label('Format')
                    ->formatStateUsing(fn ($state) => strtoupper($state ?? 'VIDEO'))
                    ->colors([
                        'amber'   => 'carousel',
                        'primary' => 'video',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'published',
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'danger'  => 'failed',
                    ]),

                

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
            ]);
    }
}