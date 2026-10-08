<?php

namespace App\Filament\Resources\ContentPosts\Pages;

use App\Filament\Resources\ContentPosts\ContentPostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentPost extends EditRecord
{
    protected static string $resource = ContentPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
