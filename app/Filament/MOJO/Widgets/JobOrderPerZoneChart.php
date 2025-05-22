<?php

namespace App\Filament\MOJO\Widgets;

use Flowframe\Trend\Trend;
use App\Models\OnlineJobOrder;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use DB;

class JobOrderPerZoneChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'jobOrderPerZoneChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Job Orders Per Zone';

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = 2;

    protected function getOptions(): array
    {
        // Query to count OnlineJobOrders grouped by zone prefix (first 2 characters)
        $data = OnlineJobOrder::query()
            ->whereNotNull('account_number')
            ->where('account_number', '!=', '00-000000')
            ->whereNotIn('account_number', ['-', 'N', 'NA'])
            ->selectRaw('LEFT(account_number, 2) as zone, COUNT(*) as total')
            ->groupBy('zone')
            ->orderBy('zone')
            ->pluck('total', 'zone');

        return [
            'chart' => [
                'type' => 'area',
                'height' => 400,
                'toolbar' => [
                    'show' => true,
                ],
            ],

            'series' => [
                [
                    'name' => 'Job Orders',
                    'data' => $data->values(),
                     'type' => 'bar',
                    // 'backgroundColor' => '#10D0E7',
                    // 'borderColor' => '#1061E7',
                ],
            ],
            'xaxis' => [
                'categories' => $data->keys(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            // 'colors' => ['#0E1CF4','#04326C'],
            'colors' => ['#04326C', '#0E1CF4',],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,

                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                // 'position' => 'top',
                // 'fontSize' => '8px',
            ],
            'plotOptions' => [
                'bar' => [
                    'dataLabels' => [
                        'position' => 'middle',
                        // 'offsetX' => ,
                    ],
                ],
            ],
        ];
    }
}
