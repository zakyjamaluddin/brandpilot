<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class DocumentationPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Documentation';

    protected static ?string $title = 'Panduan & Dokumentasi BrandPilot AI';

    protected string $view = 'filament.pages.documentation-page';
}
