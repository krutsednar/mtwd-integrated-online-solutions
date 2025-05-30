<?php

namespace App\Filament\Executive\Widgets;

use Carbon\Carbon;
use Filament\Tables;
use App\Models\JobOrderCode;
use Carbon\CarbonInterval;
use Filament\Tables\Table;
use App\Models\OnlineJobOrder;
use Filament\Widgets\TableWidget as BaseWidget;

class JobOrderPerType extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $totals = [
            'totalJO' => 0,
            'totalAccomplished' => 0,
            'totalOngoing' => 0,
            'totalReceivedToday' => 0,
            'totalAccomplishedToday' => 0,
            'totalOngoingToday' => 0,
            'avgTATSeconds' => 0,
            'avgTATReadable' => 'N/A',
        ];

        // Load all job orders once
        $jobOrders = OnlineJobOrder::whereNotNull('job_order_code')->get();

        // Group job orders by job_order_code
        $jobOrdersByCode = $jobOrders->groupBy('job_order_code');

        // Filter out codes where totalJO and totalAccomplished are both 0
        $jobOrdersByCode = $jobOrdersByCode->filter(function ($orders) {
            $totalJO = $orders->count();
            $totalAccomplished = $orders->whereNotNull('date_accomplished')->count();
            return !($totalJO === 0 && $totalAccomplished === 0);
        });

        // Get only the codes that have valid orders
        $filteredCodes = $jobOrdersByCode->keys();

        // Pre-calculate overall stats
        $totalSeconds = 0;
        $tatCount = 0;

        foreach ($jobOrders as $job) {
            if ($job->date_requested && $job->date_accomplished) {
                $totalSeconds += Carbon::parse($job->date_requested)
                    ->diffInSeconds(Carbon::parse($job->date_accomplished));
                $tatCount++;
            }
        }

        if ($tatCount > 0) {
            $avgSeconds = $totalSeconds / $tatCount;
            $totals['avgTATSeconds'] = round($avgSeconds);
            $totals['avgTATReadable'] = CarbonInterval::seconds($avgSeconds)
                ->cascade()
                ->forHumans(['parts' => 2, 'join' => true]);
        }

        $totals['totalJO'] = $jobOrders->count();
        $totals['totalAccomplished'] = $jobOrders->whereNotNull('date_accomplished')->count();
        $totals['totalOngoing'] = $jobOrders->whereNull('date_accomplished')->count();

        $totals['totalReceivedToday'] = $jobOrders->where('date_requested', '>=', Carbon::today())->count();
        $totals['totalAccomplishedToday'] = $jobOrders->where('date_accomplished', '>=', Carbon::today())->count();
        $totals['totalOngoingToday'] = $jobOrders
            ->where('date_requested', '>=', Carbon::today())
            ->whereNull('date_accomplished')
            ->count();

        return $table
            ->query(JobOrderCode::whereIn('code', $filteredCodes)->orderBy('description', 'asc'))
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label('Job Order Type')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('receivedToday')
                    ->label('Received Today')
                    ->getStateUsing(function (JobOrderCode $record) use ($jobOrdersByCode) {
                        return number_format(
                            $jobOrdersByCode->get($record->code, collect())
                                ->where('date_requested', '>=', Carbon::today())
                                ->count()
                        );
                    }),

                Tables\Columns\TextColumn::make('accomplishedToday')
                    ->label('Accomplished Today')
                    ->getStateUsing(function (JobOrderCode $record) use ($jobOrdersByCode) {
                        return number_format(
                            $jobOrdersByCode->get($record->code, collect())
                                ->where('date_accomplished', '>=', Carbon::today())
                                ->count()
                        );
                    }),

                Tables\Columns\TextColumn::make('ongoingToday')
                    ->label('Ongoing Today')
                    ->getStateUsing(function (JobOrderCode $record) use ($jobOrdersByCode) {
                        return number_format(
                            $jobOrdersByCode->get($record->code, collect())
                                ->where('date_requested', '>=', Carbon::today())
                                ->whereNull('date_accomplished')
                                ->count()
                        );
                    }),

                Tables\Columns\TextColumn::make('totalJO')
                    ->label('Total Job Orders')
                    ->getStateUsing(function (JobOrderCode $record) use ($jobOrdersByCode) {
                        return number_format(
                            $jobOrdersByCode->get($record->code, collect())->count()
                        );
                    }),

                Tables\Columns\TextColumn::make('totalAccomplished')
                    ->label('Total Accomplished')
                    ->getStateUsing(function (JobOrderCode $record) use ($jobOrdersByCode) {
                        $orders = $jobOrdersByCode->get($record->code, collect());
                        $accomplished = $orders->whereNotNull('date_accomplished')->count();
                        return number_format($accomplished);
                    })
                    ->description(function (JobOrderCode $record): string {
                        $total = OnlineJobOrder::where('job_order_code', $record->code)->count();

                        if ($total === 0) {
                            return '0%';
                        }

                        $accomplished = OnlineJobOrder::whereNotNull('date_accomplished')
                            ->where('job_order_code', $record->code)->count();

                        return round(($accomplished / $total) * 100, 2) . '%';
                    }, position: 'below'),

                Tables\Columns\TextColumn::make('totalOngoing')
                    ->label('Total Ongoing')
                    ->getStateUsing(function (JobOrderCode $record) use ($jobOrdersByCode) {
                        $orders = $jobOrdersByCode->get($record->code, collect());
                        $ongoing = $orders->whereNull('date_accomplished')->count();
                        return number_format($ongoing);
                    })
                    ->description(function (JobOrderCode $record): string {
                        $total = OnlineJobOrder::where('job_order_code', $record->code)->count();

                        if ($total === 0) {
                            return '0%';
                        }

                        $accomplished = OnlineJobOrder::whereNull('date_accomplished')
                            ->where('job_order_code', $record->code)->count();

                        return round(($accomplished / $total) * 100, 2) . '%';
                    }, position: 'below'),

                Tables\Columns\TextColumn::make('avgTAT')
                    ->label('Avg TAT')
                    ->wrap()
                    ->getStateUsing(function (JobOrderCode $record) use ($jobOrdersByCode) {
                        $orders = $jobOrdersByCode->get($record->code, collect());

                        $total = 0;
                        $count = 0;

                        foreach ($orders as $job) {
                            if ($job->date_requested && $job->date_accomplished) {
                                $start = Carbon::parse($job->date_requested);
                                $end = Carbon::parse($job->date_accomplished);
                                $total += $start->diffInSeconds($end);
                                $count++;
                            }
                        }

                        if ($count === 0) return 'N/A';

                        return CarbonInterval::seconds($total / $count)
                            ->cascade()
                            ->forHumans(['parts' => 2, 'join' => true]);
                    }),
            ])
            ->paginated(false)
            ->contentFooter(view('table.type-footer', $totals));
    }
}
