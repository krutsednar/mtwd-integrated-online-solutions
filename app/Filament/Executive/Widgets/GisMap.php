<?php

namespace App\Filament\Executive\Widgets;

use Filament\Widgets\Widget;

class GisMap extends Widget
{
    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.executive.widgets.gis-map';
}
