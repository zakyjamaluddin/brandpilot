<?php

namespace App\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class GreetingWidget extends Widget
{
    protected string $view = 'filament.widgets.greeting-widget';

    // Membuat widget memanjang penuh (1 kolom penuh)
    protected int | string | array $columnSpan = 'full';

    // Mengambil data tenant yang sedang aktif
    public function getActiveTeam()
    {
        return Filament::getTenant();
    }
}
