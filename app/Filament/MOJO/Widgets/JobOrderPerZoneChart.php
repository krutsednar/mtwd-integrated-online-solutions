<?php

namespace App\Filament\MOJO\Widgets;

use App\Models\OnlineJobOrder;
use Illuminate\Support\Facades\Cache;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class JobOrderPerZoneChart extends ApexChartWidget
{
    protected static ?string $chartId = 'jobOrderPerZoneChart';

    protected static ?string $heading = 'Job Orders Per Zone';

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = 4;

    private function getChartData(): array
    {
        return Cache::remember('jo_zone_chart_data', 8, function () {
            $data = OnlineJobOrder::query()
                ->whereNotNull('account_number')
                ->where('account_number', '!=', '00-000000')
                ->whereNotIn('account_number', ['-', 'N', 'NA'])
                ->selectRaw('LEFT(account_number, 2) as zone, COUNT(*) as total')
                ->groupBy('zone')
                ->orderBy('zone')
                ->pluck('total', 'zone');

            $ongoing = OnlineJobOrder::query()
                ->whereNotNull('account_number')
                ->where('status', '!=', 'Accomplished')
                ->where('account_number', '!=', '00-000000')
                ->whereNotIn('account_number', ['-', 'N', 'NA'])
                ->selectRaw('LEFT(account_number, 2) as zone, COUNT(*) as total')
                ->groupBy('zone')
                ->orderBy('zone')
                ->pluck('total', 'zone');

            return [
                'total'        => $data->sum(),
                'ongoing'      => $ongoing->sum(),
                'data'         => $data,
                'ongoing_data' => $ongoing,
            ];
        });
    }

    protected function getOptions(): array
    {
        $chartData = $this->getChartData();

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
                    'name' => 'Ongoing Job Orders - ' . number_format($chartData['ongoing']),
                    'data' => $chartData['ongoing_data']->values(),
                    'type' => 'bar',
                ],
                [
                    'name' => 'Total Job Orders - ' . number_format($chartData['total']),
                    'data' => $chartData['data']->values(),
                    'type' => 'bar',
                ],
            ],
            'xaxis' => [
                'categories' => $chartData['data']->keys(),
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
            'colors' => ['#066e27', '#0E1CF4'],
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
            ],
            'plotOptions' => [
                'bar' => [
                    'dataLabels' => [
                        'position' => 'top',
                    ],
                ],
            ],
        ];
    }
}
