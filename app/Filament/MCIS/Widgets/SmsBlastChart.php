<?php

namespace App\Filament\MCIS\Widgets;

use Carbon\Carbon;
use App\Models\SmsReport;
use App\Models\Statement;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SmsBlastChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'smsBlastChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Monthly Chart';

    protected int | string | array $columnSpan = 'full';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $blast = Trend::query(
                SmsReport::query()
                    ->where('status', 'Sent')
                    ->where('account_number', '!=', '2FA/OTP')
            )
            ->dateColumn('created_at')
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();
        $otp = Trend::query(
                SmsReport::query()
                    ->where('status', 'Sent')
                    ->where('account_number', '2FA/OTP')
            )
            ->dateColumn('created_at')
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
                    'name' => 'SMS Blast Sent',
                    'data' => $blast->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                    'type' => 'bar',
                    'backgroundColor' => '#deb750',
                    'borderColor' => '#1061E7',
                ],

                [
                    'name' => 'OTP Sent',
                    'data' => $otp->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                    'type' => 'bar',
                    'backgroundColor' => '#deb750',
                    'borderColor' => '#1061E7',
                ],

            ],
            'xaxis' => [
                'categories' => $blast->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M-Y'))->toArray(),
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
