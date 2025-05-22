<?php

namespace App\Filament\Executive\Pages;

use Filament\Pages\Page;
use Filament\Widgets\Widget;
use App\Filament\Widgets\OnlineJobOrderMap;

class MojoReports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.executive.pages.mojo-reports';

    // protected static ?string $navigationGroup = 'MOJOReports'; // optional group
    // protected static ?int $navigationSort = 100; // sort order

    // Optional: Customize navigation label
    protected static ?string $navigationLabel = 'MOJO Reports';

    // Optional: Set custom slug
    // protected static ?string $slug = 'mojo-reports';

    // Define widgets to show
    protected function getHeaderWidgets(): array
    {
        return [
            OnlineJobOrderMap::class,
            // \App\Filament\Widgets\MonthlyEarnings::class,
        ];
    }

}
