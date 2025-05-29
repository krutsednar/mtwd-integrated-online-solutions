<?php

namespace App\Filament\Executive\Widgets;

use Filament\Tables;
use App\Models\Division;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Tables\Table;
use App\Models\OnlineJobOrder;
use Filament\Widgets\TableWidget as BaseWidget;

class JobOrderPerDivision extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $totals = [
            'totalJO' => 0,
            'totalAccomplished' => 0,
            'totalOngoing' => 0,
            'avgTATSeconds' => 0,
            'avgTATReadable' => 'N/A',
        ];

        $divisions = Division::whereNotIn('code', ['2023', '2022'])->get();

        $totalSeconds = 0;
        $count = 0;

        foreach ($divisions as $division) {
            $jobOrderCodes = $division->jocodes()->pluck('code');

            $joCount = OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)->count();
            $accomplished = OnlineJobOrder::whereNotNull('date_accomplished')
                ->whereIn('job_order_code', $jobOrderCodes)->count();
            $ongoing = OnlineJobOrder::whereNull('date_accomplished')
                ->whereIn('job_order_code', $jobOrderCodes)->count();

            $totals['totalJO'] += $joCount;
            $totals['totalAccomplished'] += $accomplished;
            $totals['totalOngoing'] += $ongoing;

            // Calculate TAT per job and sum seconds
            $tatJobs = OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)
                ->whereNotNull('date_requested')
                ->whereNotNull('date_accomplished')
                ->get(['date_requested', 'date_accomplished']);

            foreach ($tatJobs as $job) {
                $start = Carbon::parse($job->date_requested);
                $end = Carbon::parse($job->date_accomplished);
                $diff = $start->diffInSeconds($end);
                $totalSeconds += $diff;
                $count++;
            }
        }

        if ($count > 0) {
            $avgSeconds = $totalSeconds / $count;
            $totals['avgTATSeconds'] = round($avgSeconds);
            $totals['avgTATReadable'] = CarbonInterval::seconds($avgSeconds)
                ->cascade()
                ->forHumans(['parts' => 2, 'join' => true]);
        }

        return $table
            ->query(
                Division::whereNotIn('code', ['2023', '2022'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label('Division Name'),
                Tables\Columns\TextColumn::make('totalJO')
                    ->label('Job Orders')
                    ->getStateUsing(function (Division $record) {
                        $jobOrderCodes = $record->jocodes()->pluck('code');
                        return number_format(OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)->count());
                    }),
                Tables\Columns\TextColumn::make('totalAccomplished')
                    ->label('Accomplished')
                    ->getStateUsing(function (Division $record) {
                        $jobOrderCodes = $record->jocodes()->pluck('code');
                        $total = OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)->count();
                        $accomplished = OnlineJobOrder::whereNotNull('date_accomplished')
                            ->whereIn('job_order_code', $jobOrderCodes)->count();

                        if ($total === 0) {
                            return '0 (0%)';
                        }

                        $percentage = round(($accomplished / $total) * 100, 2);
                        return number_format($accomplished) . " ({$percentage}%)";
                    }),
                Tables\Columns\TextColumn::make('totalOngoing')
                    ->label('Ongoing')
                    ->getStateUsing(function (Division $record) {
                        $jobOrderCodes = $record->jocodes()->pluck('code');
                        $total = OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)->count();
                        $ongoing = OnlineJobOrder::whereNull('date_accomplished')
                            ->whereIn('job_order_code', $jobOrderCodes)->count();

                        if ($total === 0) {
                            return '0 (0%)';
                        }

                        $percentage = round(($ongoing / $total) * 100, 2);
                        return number_format($ongoing) . " ({$percentage}%)";
                    }),
                Tables\Columns\TextColumn::make('avgTAT')
                    ->label('Avg TAT')
                    ->getStateUsing(function (Division $record) {
                        $jobOrderCodes = $record->jocodes()->pluck('code');

                        $jobs = OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)
                            ->whereNotNull('date_requested')
                            ->whereNotNull('date_accomplished')
                            ->get(['date_requested', 'date_accomplished']);

                        $total = 0;
                        $count = 0;

                        foreach ($jobs as $job) {
                            $start = Carbon::parse($job->date_requested);
                            $end = Carbon::parse($job->date_accomplished);
                            $total += $start->diffInSeconds($end);
                            $count++;
                        }

                        if ($count === 0) {
                            return 'N/A';
                        }

                        $avg = $total / $count;

                        return CarbonInterval::seconds($avg)
                            ->cascade()
                            ->forHumans(['parts' => 2, 'join' => true]);
                    }),
            ])
            ->paginated(false)
            ->contentFooter(view('table.footer', $totals));
    }
}
