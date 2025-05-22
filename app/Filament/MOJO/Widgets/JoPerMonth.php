<?php

namespace App\Filament\MOJO\Widgets;

use App\Models\OnlineJobOrder;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class JoPerMonth extends ApexChartWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'joPerMonth';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Monthly Job Orders Received';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $trend = Trend::model(OnlineJobOrder::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

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
                    'data' => $trend->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                    'type' => 'area',
                    'backgroundColor' => '#deb750',
                    'borderColor' => '#1061E7',
                ],
            ],
            'xaxis' => [
                'categories' => $trend->map(fn (TrendValue $value) => $value->date)->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#0b0d95', '#0E1CF4',],
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
            'markers' => [
                'size' => 4,
            ],
        ];
    }
}
