<?php

namespace App\Filament\Resources\InboxThreads\Pages;

use App\Filament\Resources\InboxThreads\InboxThreadResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInboxThread extends EditRecord
{
    protected static string $resource = InboxThreadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
