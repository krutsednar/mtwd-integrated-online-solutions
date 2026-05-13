<?php

namespace App\Filament\Executive\Pages;

use Filament\Pages\Page;
use App\Filament\Executive\Widgets\JobOrderMap;

class JobOrderMapMonitoring extends Page
{
    protected static ?string $navigationIcon = 'fas-map-marked-alt';

    protected static ?int $sort = 2;

    protected static string $view = 'filament.executive.pages.job-order-map-monitoring';

    protected static ?string $navigationGroup = 'MOJO Reports';

    protected function getHeaderWidgets(): array
    {
        return [
            JobOrderMap::class,
        ];
    }

}
