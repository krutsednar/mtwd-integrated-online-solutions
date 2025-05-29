<?php

namespace App\Filament\MOJO\Widgets;

use Carbon\Carbon;
use Flowframe\Trend\Trend;
use App\Models\OnlineJobOrder;
use Flowframe\Trend\TrendValue;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class JoPerMonth extends ApexChartWidget
{
    protected static ?int $sort = 2;
    // protected int | string | array $columnSpan = 'full';
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
            ->dateColumn('date_requested')
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 400,
                'toolbar' => [
                    'show' => true,
                ],
            ],
            'series' => [
                [
                    'name' => 'Job Orders',
                    'data' => $trend->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                    'type' => 'bar',
                    'backgroundColor' => '#deb750',
                    'borderColor' => '#1061E7',
                ],
            ],
            'xaxis' => [
                'categories' => $trend->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M-Y'))->toArray(),
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
                        'position' => 'top',
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
