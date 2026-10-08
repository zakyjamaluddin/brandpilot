<?php

namespace App\Filament\Resources\ContentPosts\Schemas;

use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Metode Upload Konten')
                ->schema([
                    Radio::make('creation_mode')
                        ->label('Pilih Tipe Upload')
                        ->options([
                            'single' => 'Individual Upload (1 Video & Jadwalkan Mandiri)',
                            'bulk'   => 'Bulk Upload (Banyak Video Sekaligus - Jadwal Otomatis Mengisi Slot Jam Statis)',
                        ])
                        ->default('single')
                        ->live(),

                    // File upload untuk Single
                    FileUpload::make('single_video_file')
                        ->label('Pilih File Video')
                        ->disk('public')
                        ->directory('videos')
                        ->acceptedFileTypes(['video/mp4', 'video/quicktime'])
                        ->maxSize(102400) // 100MB
                        ->visible(fn ($get) => $get('creation_mode') === 'single')
                        ->required(fn ($get) => $get('creation_mode') === 'single'),

                    // File upload untuk Bulk
                    FileUpload::make('bulk_video_files')
                        ->label('Pilih Banyak File Video Sekaligus')
                        ->disk('public')
                        ->directory('videos')
                        ->multiple()
                        ->acceptedFileTypes(['video/mp4', 'video/quicktime'])
                        ->maxSize(102400)
                        ->visible(fn ($get) => $get('creation_mode') === 'bulk')
                        ->required(fn ($get) => $get('creation_mode') === 'bulk')
                        ->helperText('Seluruh video akan dijadwalkan otomatis mengisi jam posting statis yang diatur pada Setting.'),
                ]),

            Section::make('Pengaturan Caption')
                ->schema([
                    Radio::make('caption_type')
                        ->label('Metode Pembuatan Caption')
                        ->options([
                            'manual'    => 'Tulis Sendiri Secara Manual',
                            'gemini_ai' => 'Dibuatkan Otomatis oleh Gemini AI',
                        ])
                        ->default('manual')
                        ->live(),

                    Textarea::make('manual_caption')
                        ->label('Tuliskan Caption Anda')
                        ->rows(4)
                        ->visible(fn ($get) => $get('caption_type') === 'manual')
                        ->required(fn ($get) => $get('caption_type') === 'manual'),

                    TextInput::make('ai_topic_prompt')
                        ->label('Topik atau Kata Kunci untuk Gemini AI')
                        ->placeholder('Contoh: Diskon baju tidur wanita premium bahan sutra')
                        ->visible(fn ($get) => $get('caption_type') === 'gemini_ai')
                        ->required(fn ($get) => $get('caption_type') === 'gemini_ai')
                        ->helperText('Gemini AI akan meracik caption, hook, call-to-action, dan hashtag otomatis berdasarkan topik ini.'),
                ]),

            Section::make('Jadwal Tayang (Khusus Individual)')
                ->visible(fn ($get) => $get('creation_mode') === 'single')
                ->schema([
                    DateTimePicker::make('scheduled_at')
                        ->label('Jadwalkan Pada Waktu')
                        ->default(now()->addHour())
                        ->required(fn ($get) => $get('creation_mode') === 'single'),
                ]),
            ]);
    }
}
