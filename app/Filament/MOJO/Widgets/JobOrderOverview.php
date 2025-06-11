<?php

namespace App\Filament\MOJO\Widgets;

use Carbon\CarbonInterval;
use App\Models\OnlineJobOrder;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Actions\Modal\Actions\Action;

class JobOrderOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = 1;

    // protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $jo = OnlineJobOrder::get();
        $totalAccomplished = $jo->whereNotNull('date_accomplished')->count();
        $totalOngoing = $jo->where('date_accomplished', NULL)->count();
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

        $averageAccomplishedPerMonth = OnlineJobOrder::whereNotNull('date_accomplished')
        ->selectRaw('COUNT(*) / COUNT(DISTINCT DATE_FORMAT(date_accomplished, "%Y-%m")) as avg_per_month')
        ->value('avg_per_month');

        // New Stats: Today
        $today = now()->startOfDay();
        $receivedToday = OnlineJobOrder::whereDate('date_requested', $today)->count();
        $accomplishedToday = OnlineJobOrder::whereDate('date_accomplished', $today)->count();
        $ongoingToday = OnlineJobOrder::whereDate('date_requested', $today)->whereNull('date_accomplished')->count();

        return [

            // Stats TOtal
            Stat::make('Total Accomplished Job Orders', number_format($totalAccomplished).' ('.ceil($totalAccomplished/$jo->count()*100).'%)')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
            Stat::make('Total Ongoing Job Orders', number_format($totalOngoing).' ('.floor($totalOngoing/$jo->count()*100).'%)')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('warning'),
            Stat::make('Total Received Job Orders ', number_format($jo->count()))
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('info'),

            // Stats Monthly
            Stat::make('Monthly Accomplished Job Orders', number_format($averageAccomplishedPerMonth))
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
            Stat::make('Monthly Received Job Orders', number_format(floor($averagePerMonth)))
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('warning'),
             Stat::make('Turn Around Time', $averageTatFormatted)
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('info'),

             // Stats Today
            Stat::make('Accomplished Job Orders Today', number_format($accomplishedToday))
                ->chart([3, 5, 2, 8, 1, 9, 6])
                ->color('success'),

            Stat::make('Ongoing Job Orders Today', number_format($ongoingToday))
                ->chart([3, 5, 2, 8, 1, 9, 6])
                ->color('warning'),
            Stat::make('Received Job Orders Today', number_format($receivedToday))
                ->chart([3, 5, 2, 8, 1, 9, 6])
                ->color('info'),
        ];
    }

}
