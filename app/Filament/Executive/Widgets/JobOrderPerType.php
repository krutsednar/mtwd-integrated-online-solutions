<?php

namespace App\Filament\Executive\Widgets;

use App\Models\JobOrderCode;
use App\Models\OnlineJobOrder;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class JobOrderPerType extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $today = Carbon::today()->toDateString();

        $rawStats = OnlineJobOrder::whereNotNull('job_order_code')
            ->selectRaw("
                job_order_code,
                COUNT(*) AS total,
                SUM(date_accomplished IS NOT NULL) AS accomplished,
                SUM(date_accomplished IS NULL) AS ongoing,
                SUM(DATE(date_requested) = ?) AS received_today,
                SUM(DATE(date_accomplished) = ?) AS accomplished_today,
                SUM(date_accomplished IS NULL AND DATE(date_requested) = ?) AS ongoing_today,
                SUM(TIMESTAMPDIFF(SECOND, date_requested, date_accomplished)) AS total_seconds,
                SUM(date_accomplished IS NOT NULL AND date_requested IS NOT NULL) AS tat_count
            ", [$today, $today, $today])
            ->groupBy('job_order_code')
            ->get()
            ->keyBy('job_order_code');

        $totals = [
            'totalJO'                => (int) $rawStats->sum('total'),
            'totalAccomplished'      => (int) $rawStats->sum('accomplished'),
            'totalOngoing'           => (int) $rawStats->sum('ongoing'),
            'totalReceivedToday'     => (int) $rawStats->sum('received_today'),
            'totalAccomplishedToday' => (int) $rawStats->sum('accomplished_today'),
            'totalOngoingToday'      => (int) $rawStats->sum('ongoing_today'),
        ];

        $globalTatCount   = (int) $rawStats->sum('tat_count');
        $globalTatSeconds = (int) $rawStats->sum('total_seconds');
        $totals['avgTATReadable'] = $globalTatCount > 0
            ? CarbonInterval::seconds($globalTatSeconds / $globalTatCount)->cascade()->forHumans(['parts' => 2, 'join' => true])
            : 'N/A';

        $activeCodes = $rawStats->keys();

        return $table
            ->query(JobOrderCode::whereIn('code', $activeCodes)->orderBy('description', 'asc'))
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label('Job Order Type')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('receivedToday')
                    ->label('Received Today')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->received_today ?? 0));
                    }),

                Tables\Columns\TextColumn::make('accomplishedToday')
                    ->label('Accomplished Today')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->accomplished_today ?? 0));
                    }),

                Tables\Columns\TextColumn::make('ongoingToday')
                    ->label('Ongoing Today')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->ongoing_today ?? 0));
                    }),

                Tables\Columns\TextColumn::make('totalJO')
                    ->label('Total Job Orders')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->total ?? 0));
                    }),

                Tables\Columns\TextColumn::make('totalAccomplished')
                    ->label('Total Accomplished')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->accomplished ?? 0));
                    })
                    ->description(function (JobOrderCode $record) use ($rawStats): string {
                        $row = $rawStats->get($record->code);
                        $total = (int) ($row?->total ?? 0);
                        if ($total === 0) return '0%';
                        $accomplished = (int) ($row?->accomplished ?? 0);
                        return round(($accomplished / $total) * 100, 2) . '%';
                    }, position: 'below'),

                Tables\Columns\TextColumn::make('totalOngoing')
                    ->label('Total Ongoing')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->ongoing ?? 0));
                    })
                    ->description(function (JobOrderCode $record) use ($rawStats): string {
                        $row = $rawStats->get($record->code);
                        $total = (int) ($row?->total ?? 0);
                        if ($total === 0) return '0%';
                        $ongoing = (int) ($row?->ongoing ?? 0);
                        return round(($ongoing / $total) * 100, 2) . '%';
                    }, position: 'below'),

                Tables\Columns\TextColumn::make('avgTAT')
                    ->label('Avg TAT')
                    ->wrap()
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        $row = $rawStats->get($record->code);
                        $tatCount   = (int) ($row?->tat_count ?? 0);
                        $tatSeconds = (int) ($row?->total_seconds ?? 0);

                        if ($tatCount === 0) return 'N/A';

                        return CarbonInterval::seconds($tatSeconds / $tatCount)
                            ->cascade()
                            ->forHumans(['parts' => 2, 'join' => true]);
                    }),
            ])
            ->paginated(false)
            ->contentFooter(view('table.type-footer', $totals));
    }
}
