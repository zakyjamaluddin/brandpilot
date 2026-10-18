<?php

namespace App\Filament\Resources\ContentPosts;

use App\Filament\Resources\ContentPosts\Pages\CreateContentPost;
use App\Filament\Resources\ContentPosts\Pages\EditContentPost;
use App\Filament\Resources\ContentPosts\Pages\ListContentPosts;
use App\Filament\Resources\ContentPosts\Schemas\ContentPostForm;
use App\Filament\Resources\ContentPosts\Tables\ContentPostsTable;
use App\Models\ContentPost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentPostResource extends Resource
{
    protected static ?string $model = ContentPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return ContentPostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentPostsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentPosts::route('/'),
            'create' => CreateContentPost::route('/create'),
            'edit' => EditContentPost::route('/{record}/edit'),
        ];
    }
}
