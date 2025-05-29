<?php

namespace App\Filament\MOJO\Widgets;

use App\Models\OnlineJobOrder;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class JoSummaryChart extends ApexChartWidget
{
    protected static ?int $sort = 2;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'joSummaryChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Job Order Summary';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $accomplished = OnlineJobOrder::query()
            ->whereNotNull('account_number')
            ->whereNotNull('date_accomplished')
            // ->where('account_number', '!=', '00-000000')
            // ->whereNotIn('account_number', ['-', 'N', 'NA'])
            ->count();

        $ongoing = OnlineJobOrder::query()
            ->whereNotNull('account_number')
            ->whereNull('date_accomplished')
            // ->where('account_number', '!=', '00-000000')
            // ->whereNotIn('account_number', ['-', 'N', 'NA'])
            ->count();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 400,
            ],
            'series' => [$accomplished, $ongoing],
            'labels' => ['Accomplished: '.$accomplished, 'Ongoing: '.$ongoing],
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                    'fontWeight' => 'inherit',
                ],
            ],
        ];
    }
}
