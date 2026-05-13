<?php

namespace App\Livewire\Reports;

use App\Models\JobOrderCode;
use App\Models\OnlineJobOrder;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class JoPerType extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        $today = Carbon::today()->toDateString();

        // W-C fix: replace full OnlineJobOrder::get() with a single aggregated query.
        // Previously loaded every row of the table into PHP memory.
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

        // Footer totals — summed from already-loaded aggregation (no extra queries)
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

        // Only show job order codes that have at least one entry
        $activeCodes = $rawStats->keys();

        return $table
            ->query(JobOrderCode::whereIn('code', $activeCodes)->orderBy('description', 'asc'))
            ->columns([
                TextColumn::make('description')
                    ->label('Job Order Type')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('receivedToday')
                    ->label('Received Today')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->received_today ?? 0));
                    }),

                TextColumn::make('accomplishedToday')
                    ->label('Accomplished Today')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->accomplished_today ?? 0));
                    }),

                TextColumn::make('ongoingToday')
                    ->label('Ongoing Today')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->ongoing_today ?? 0));
                    }),

                TextColumn::make('totalJO')
                    ->label('Total Job Orders')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->total ?? 0));
                    }),

                TextColumn::make('totalAccomplished')
                    ->label('Total Accomplished')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->accomplished ?? 0));
                    })
                    // W-B fix: was firing 2 DB queries per row — now reads from in-memory collection
                    ->description(function (JobOrderCode $record) use ($rawStats): string {
                        $row = $rawStats->get($record->code);
                        $total = (int) ($row?->total ?? 0);
                        if ($total === 0) return '0%';
                        $accomplished = (int) ($row?->accomplished ?? 0);
                        return round(($accomplished / $total) * 100, 2) . '%';
                    }, position: 'below'),

                TextColumn::make('totalOngoing')
                    ->label('Total Ongoing')
                    ->getStateUsing(function (JobOrderCode $record) use ($rawStats) {
                        return number_format((int) ($rawStats->get($record->code)?->ongoing ?? 0));
                    })
                    // W-B fix: was firing 2 DB queries per row — now reads from in-memory collection
                    ->description(function (JobOrderCode $record) use ($rawStats): string {
                        $row = $rawStats->get($record->code);
                        $total = (int) ($row?->total ?? 0);
                        if ($total === 0) return '0%';
                        $ongoing = (int) ($row?->ongoing ?? 0);
                        return round(($ongoing / $total) * 100, 2) . '%';
                    }, position: 'below'),

                TextColumn::make('avgTAT')
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

    public function render()
    {
        return view('livewire.reports.jo-per-type');
    }
}
