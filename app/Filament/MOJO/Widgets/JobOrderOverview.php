<?php

namespace App\Filament\MOJO\Widgets;

use Carbon\CarbonInterval;
use App\Models\OnlineJobOrder;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class JobOrderOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $jo = OnlineJobOrder::get();
        $totalAccomplished = $jo->where('status', 'Accomplished')->count();
        $totalOngoing = $jo->where('status', '!=', 'Accomplished')->count();
        $averagePerMonth = OnlineJobOrder::selectRaw('COUNT(*) / COUNT(DISTINCT DATE_FORMAT(date_requested, "%Y-%m")) as avg_per_month')
            ->value('avg_per_month');
        $averageTatInSeconds = OnlineJobOrder::whereNotNull('date_requested')
    ->whereNotNull('date_accomplished')
    ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, date_requested, date_accomplished)) as avg_tat')
    ->value('avg_tat');

    // Convert to CarbonInterval and format
    $averageTatFormatted = CarbonInterval::seconds($averageTatInSeconds)->cascade()->forHumans([
        'join' => true,
        'parts' => 2,
    ]);
        return [
            Stat::make('Total Accomplished Job Orders', number_format($totalAccomplished))
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
            Stat::make('Total Ongoing Job Orders', number_format($totalOngoing))
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('warning'),
            Stat::make('Average Monthly Job Orders', number_format(floor($averagePerMonth)))
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('info'),
            Stat::make('Average Turn Around Time', $averageTatFormatted)
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('danger'),
        ];
    }
}
