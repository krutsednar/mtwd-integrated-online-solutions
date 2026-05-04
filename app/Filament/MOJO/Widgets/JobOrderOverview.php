<?php

namespace App\Filament\MOJO\Widgets;

use Carbon\CarbonInterval;
use App\Models\OnlineJobOrder;
use Illuminate\Support\Facades\Cache;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class JobOrderOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return Cache::remember('jo_overview_stats', 8, function () {
            $today = now()->toDateString();

            $stats = OnlineJobOrder::selectRaw("
                COUNT(*) AS total,
                SUM(date_accomplished IS NOT NULL) AS accomplished,
                SUM(date_accomplished IS NULL) AS ongoing,
                SUM(DATE(date_requested) = ?) AS received_today,
                SUM(DATE(date_accomplished) = ?) AS accomplished_today,
                SUM(date_accomplished IS NULL AND DATE(date_requested) = ?) AS ongoing_today
            ", [$today, $today, $today])->first();

            $totalAccomplished = (int) $stats->accomplished;
            $totalOngoing      = (int) $stats->ongoing;
            $total             = (int) $stats->total;
            $receivedToday     = (int) $stats->received_today;
            $accomplishedToday = (int) $stats->accomplished_today;
            $ongoingToday      = (int) $stats->ongoing_today;

            $averagePerMonth = OnlineJobOrder::selectRaw('COUNT(*) / COUNT(DISTINCT DATE_FORMAT(date_requested, "%Y-%m")) as avg_per_month')
                ->value('avg_per_month');
            $averageTatInSeconds = OnlineJobOrder::whereNotNull('date_requested')
                ->whereNotNull('date_accomplished')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, date_requested, date_accomplished)) as avg_tat')
                ->value('avg_tat');

            $averageTatFormatted = CarbonInterval::seconds($averageTatInSeconds)->cascade()->forHumans([
                'join' => true,
                'parts' => 2,
            ]);

            $averageAccomplishedPerMonth = OnlineJobOrder::whereNotNull('date_accomplished')
                ->selectRaw('COUNT(*) / COUNT(DISTINCT DATE_FORMAT(date_accomplished, "%Y-%m")) as avg_per_month')
                ->value('avg_per_month');

            $sparkline = OnlineJobOrder::selectRaw("DATE(date_requested) as day, COUNT(*) as cnt")
                ->where('date_requested', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('cnt', 'day')
                ->values()
                ->toArray();

            return [
                // Stats Total
                Stat::make('Total Accomplished Job Orders', number_format($totalAccomplished).' ('.($total > 0 ? ceil($totalAccomplished / $total * 100) : 0).'%)')
                ->chart($sparkline)
                ->color('success'),
                Stat::make('Total Ongoing Job Orders', number_format($totalOngoing).' ('.($total > 0 ? floor($totalOngoing / $total * 100) : 0).'%)')
                ->chart($sparkline)
                ->color('warning'),
                Stat::make('Total Received Job Orders ', number_format($total))
                ->chart($sparkline)
                ->color('info'),

                // Stats Monthly
                Stat::make('Monthly Accomplished Job Orders', number_format($averageAccomplishedPerMonth))
                ->chart($sparkline)
                ->color('success'),
                Stat::make('Monthly Received Job Orders', number_format(floor($averagePerMonth)))
                ->chart($sparkline)
                ->color('warning'),
                Stat::make('Turn Around Time', $averageTatFormatted)
                ->chart($sparkline)
                ->color('info'),

                // Stats Today
                Stat::make('Accomplished Job Orders Today', number_format($accomplishedToday))
                    ->chart($sparkline)
                    ->color('success'),

                Stat::make('Ongoing Job Orders Today', number_format($ongoingToday))
                    ->chart($sparkline)
                    ->color('warning'),
                Stat::make('Received Job Orders Today', number_format($receivedToday))
                    ->chart($sparkline)
                    ->color('info'),
            ];
        });
    }
}
