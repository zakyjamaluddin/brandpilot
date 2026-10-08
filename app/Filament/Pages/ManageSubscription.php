<?php

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use BackedEnum;

class ManageSubscription extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Subscription';
    protected static ?string $title = 'Paket & Langganan BrandPilot';
    protected  string $view = 'filament.pages.manage-subscription';

    public function getTeamProperty()
    {
        return Filament::getTenant();
    }
}