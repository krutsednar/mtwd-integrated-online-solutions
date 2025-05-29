<?php

namespace App\Filament\Executive\Pages;

use Filament\Pages\Page;
use App\Filament\MOJO\Widgets\JoPerMonth;
use App\Filament\MOJO\Widgets\JoSummaryChart;
use App\Filament\MOJO\Widgets\JobOrderPerZoneChart;

class JobOrderCharts extends Page
{
    protected static ?string $navigationIcon = 'fas-chart-area';

    protected static string $view = 'filament.executive.pages.job-order-charts';

    protected static ?string $navigationGroup = 'MOJO Reports';

     protected static ?int $sort = 4;

    protected function getHeaderWidgets(): array
    {
        return [
            // JobOrderOverview::class,
            JoSummaryChart::class,
            JoPerMonth::class,
            JobOrderPerZoneChart::class,
        ];
    }

}
