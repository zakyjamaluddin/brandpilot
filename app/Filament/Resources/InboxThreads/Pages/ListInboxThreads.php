<?php

namespace App\Filament\Resources\InboxThreads\Pages;

use App\Filament\Resources\InboxThreads\InboxThreadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInboxThreads extends ListRecords
{
    protected static string $resource = InboxThreadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
