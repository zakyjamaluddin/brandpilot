<?php

namespace App\Filament\Resources\InboxThreads;

use App\Filament\Resources\InboxThreads\Pages\CreateInboxThread;
use App\Filament\Resources\InboxThreads\Pages\EditInboxThread;
use App\Filament\Resources\InboxThreads\Pages\ListInboxThreads;
use App\Filament\Resources\InboxThreads\Pages\ViewInboxThread;
use App\Filament\Resources\InboxThreads\Schemas\InboxThreadForm;
use App\Filament\Resources\InboxThreads\Schemas\InboxThreadInfolist;
use App\Filament\Resources\InboxThreads\Tables\InboxThreadsTable;
use App\Models\InboxThread;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;


class InboxThreadResource extends Resource
{
    protected static ?string $model = InboxThread::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Inbox';
    protected static ?string $tenantOwnershipRelationshipName = 'team';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return InboxThreadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InboxThreadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InboxThreadsTable::configure($table);
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
            'index' => ListInboxThreads::route('/'),
            'create' => CreateInboxThread::route('/create'),
            'view' => ViewInboxThread::route('/{record}'),
            'edit' => EditInboxThread::route('/{record}/edit'),
        ];
    }
}
