<?php

namespace App\Livewire\Reports;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Category;
use App\Models\Division;
use Carbon\CarbonInterval;
use Filament\Tables\Table;
use App\Models\OnlineJobOrder;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class JoPerCategory extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        $totals = [
            'totalJO' => 0,
            'totalAccomplished' => 0,
            'totalOngoing' => 0,
            'avgTATSeconds' => 0,
            'avgTATReadable' => 'N/A',
            'totalReceivedToday' => 0,
            'totalAccomplishedToday' => 0,
            'totalOngoingToday' => 0,
        ];

        $categories = Category::whereNotIn('code', ['2023', '2022'])->get();

        $totalSeconds = 0;
        $count = 0;

        foreach ($categories as $category) {
            $jobOrderCodes = $category->jocodes()->pluck('code');

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

            $receivedToday = OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)
                ->whereDate('date_requested', Carbon::today())
                ->count();

            $accomplishedToday = OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)
                ->whereDate('date_accomplished', Carbon::today())
                ->count();

            $ongoingToday = OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)
                ->whereNull('date_accomplished')
                ->whereDate('date_requested', Carbon::today())
                ->count();

            $totals['totalReceivedToday'] += $receivedToday;
            $totals['totalAccomplishedToday'] += $accomplishedToday;
            $totals['totalOngoingToday'] += $ongoingToday;
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
                Category::query()
            )
            ->columns([
                TextColumn::make('name')
                ->label('Job Order Category')
                ->wrap(),
                TextColumn::make('receivedToday')
                    ->label('Received Today')
                    ->getStateUsing(function (Category $record) {
                        $jobOrderCodes = $record->jocodes()->pluck('code');
                        return number_format(OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)
                            ->whereDate('date_requested', Carbon::today())
                            ->count());
                    }),

                TextColumn::make('accomplishedToday')
                    ->label('Accomplished Today')
                    ->getStateUsing(function (Category $record) {
                        $jobOrderCodes = $record->jocodes()->pluck('code');
                        return number_format(OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)
                            ->whereDate('date_accomplished', Carbon::today())
                            ->count());
                    }),

                TextColumn::make('ongoingToday')
                    ->label('Ongoing Today')
                    ->getStateUsing(function (Category $record) {
                        $jobOrderCodes = $record->jocodes()->pluck('code');
                        return number_format(OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)
                            ->whereNull('date_accomplished')
                            ->whereDate('date_requested', Carbon::today())
                            ->count());
                    }),
                TextColumn::make('totalJO')
                    ->label('Total Received')
                    ->getStateUsing(function (Category $record) {
                        $jobOrderCodes = $record->jocodes()->pluck('code');
                        return number_format(OnlineJobOrder::whereIn('job_order_code', $jobOrderCodes)->count());
                    }),
                TextColumn::make('totalAccomplished')
                    ->label('Total Accomplished')
                    ->getStateUsing(function (Category $record) {
                        $jobOrderCodes = $record->jocodes()->pluck('code');
                        $accomplished = OnlineJobOrder::whereNotNull('date_accomplished')
                            ->whereIn('job_order_code', $jobOrderCodes)->count();

                        return number_format($accomplished);
                    })
                    ->description(function (Category $record): string {
                        $total = OnlineJobOrder::whereIn('job_order_code', $record->jocodes()->pluck('code'))->count();

                        if ($total === 0) {
                            return '0%';
                        }

                        $accomplished = OnlineJobOrder::whereNotNull('date_accomplished')
                            ->whereIn('job_order_code', $record->jocodes()->pluck('code'))->count();

                        return round(($accomplished / $total) * 100, 2) . '%';
                    }, position: 'below'),
                TextColumn::make('totalOngoing')
                    ->label('Total Ongoing')
                    ->getStateUsing(function (Category $record) {
                        $jobOrderCodes = $record->jocodes()->pluck('code');
                        $ongoing = OnlineJobOrder::whereNull('date_accomplished')
                            ->whereIn('job_order_code', $jobOrderCodes)->count();

                        return number_format($ongoing);
                    })
                    ->description(function (Category $record): string {
                        $total = OnlineJobOrder::whereIn('job_order_code', $record->jocodes()->pluck('code'))->count();

                        if ($total === 0) {
                            return '0%';
                        }

                        $accomplished = OnlineJobOrder::whereNull('date_accomplished')
                            ->whereIn('job_order_code', $record->jocodes()->pluck('code'))->count();

                        return round(($accomplished / $total) * 100, 2) . '%';
                    }, position: 'below'),
                TextColumn::make('avgTAT')
                    ->label('Avg TAT')
                    ->getStateUsing(function (Category $record) {
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
                    })
                    ->wrap(),
            ])
            ->paginated(false)
            ->contentFooter(view('table.category-footer', ['totals' => $totals]));

    }

    public function render()
    {
        return view('livewire.reports.jo-per-category');
    }
}
