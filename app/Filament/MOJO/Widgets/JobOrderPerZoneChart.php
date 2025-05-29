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

    protected static ?int $sort = 4;

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

        $ongoing =
        OnlineJobOrder::query()
            ->whereNotNull('account_number')
            ->where('status', '!=', 'Accomplished')
            ->where('account_number', '!=', '00-000000')
            ->whereNotIn('account_number', ['-', 'N', 'NA'])
            ->selectRaw('LEFT(account_number, 2) as zone, COUNT(*) as total')
            ->groupBy('zone')
            ->orderBy('zone')
            ->pluck('total', 'zone');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 400,
                'stacked' => true,
                'toolbar' => [
                    'show' => true,
                ],
            ],

            'series' => [
                [
                    'name' => 'Ongoing Job Orders - '.number_format(OnlineJobOrder::whereNotNull('account_number')->where('account_number', '!=', '00-000000')
                    ->whereNotIn('account_number', ['-', 'N', 'NA'])->where('status', '!=', 'Accomplished')->count()),
                    'data' => $ongoing->values(),
                     'type' => 'bar',
                    // 'backgroundColor' => '#10D0E7',
                    // 'borderColor' => '#1061E7',
                ],
                [
                    'name' => 'Total Job Orders - '.number_format(OnlineJobOrder::whereNotNull('account_number')->where('account_number', '!=', '00-000000')
                    ->whereNotIn('account_number', ['-', 'N', 'NA'])->count()),
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
            'colors' => ['#066e27', '#0E1CF4',],
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
                        'position' => 'top',
                        // 'font' => 'black',

                        // 'offsetX' => ,
                    ],
                ],
            ],
        ];

    //     return [
    //         'chart' => [
    //             'type' => 'bar',
    //             'height' => 400,
    //             'stacked' => true, // ✅ This enables overlapping bars
    //             'toolbar' => [
    //                 'show' => true,
    //             ],
    //         ],
    //         'series' => [
    //             [
    //                 'name' => 'Total Job Orders - ' . number_format(
    //                     OnlineJobOrder::whereNotNull('account_number')
    //                         ->where('account_number', '!=', '00-000000')
    //                         ->whereNotIn('account_number', ['-', 'N', 'NA'])
    //                         ->count()
    //                 ),
    //                 'data' => $data->values(),
    //             ],
    //             [
    //                 'name' => 'Ongoing Job Orders - ' . number_format(
    //                     OnlineJobOrder::whereNotNull('account_number')
    //                         ->where('account_number', '!=', '00-000000')
    //                         ->whereNotIn('account_number', ['-', 'N', 'NA'])
    //                         ->where('status', '!=', 'Accomplished')
    //                         ->count()
    //                 ),
    //                 'data' => $ongoing->values(),
    //             ],
    //         ],
    //         'xaxis' => [
    //             'categories' => $data->keys(),
    //             'labels' => [
    //                 'style' => [
    //                     'fontFamily' => 'inherit',
    //                 ],
    //             ],
    //         ],
    //         'yaxis' => [
    //             'labels' => [
    //                 'style' => [
    //                     'fontFamily' => 'inherit',
    //                 ],
    //             ],
    //         ],
    //         'colors' => ['#0E1CF4', '#04326C'],
    //         'stroke' => [
    //             'curve' => 'smooth',
    //         ],
    //         'legend' => [
    //             'labels' => [
    //                 'colors' => '#9ca3af',
    //                 'fontWeight' => 600,
    //             ],
    //         ],
    //         'dataLabels' => [
    //             'enabled' => true,

    //         ],
    //         'plotOptions' => [
    //             'bar' => [
    //                 'dataLabels' => [
    //                     'position' => 'middle',
    //                     'colors' => '#000000',
    //                 ],
    //             ],
    //         ],
    //     ];
    }
}
