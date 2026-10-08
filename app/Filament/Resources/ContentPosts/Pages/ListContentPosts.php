<?php

namespace App\Filament\Resources\ContentPosts\Pages;

use App\Filament\Resources\ContentPosts\ContentPostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentPosts extends ListRecords
{
    protected static string $resource = ContentPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
