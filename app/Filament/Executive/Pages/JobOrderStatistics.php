<?php

namespace App\Filament\Executive\Pages;

use Filament\Pages\Page;
use App\Filament\MOJO\Widgets\JoPerMonth;
use App\Filament\MOJO\Widgets\JoSummaryChart;
use App\Filament\MOJO\Widgets\JobOrderOverview;
use App\Filament\Executive\Widgets\JobOrderPerType;
use App\Filament\MOJO\Widgets\JobOrderPerZoneChart;
use App\Filament\Executive\Widgets\JobOrderPerCategory;
use App\Filament\Executive\Widgets\JobOrderPerDivision;

class JobOrderStatistics extends Page
{
    protected static ?string $navigationIcon = 'fas-chart-line';

    protected static ?int $sort = 1;

    protected static string $view = 'filament.executive.pages.job-order-statistics';

    protected static ?string $navigationGroup = 'MOJO Reports';

    protected function getHeaderWidgets(): array
    {
        return [
            JobOrderOverview::class,
            // JobOrderPerDivision::class,
            // JobOrderPerCategory::class,
            // JobOrderPerType::class,
        ];
    }
}
