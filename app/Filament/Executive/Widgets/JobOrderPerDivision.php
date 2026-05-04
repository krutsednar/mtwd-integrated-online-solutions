<?php

namespace App\Filament\Executive\Widgets;

use App\Models\Division;
use App\Models\OnlineJobOrder;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;

class JobOrderPerDivision extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * Load all job-order stats in 2 queries (divisions+jocodes eager load,
     * then one aggregated SQL), then pass the result collection into the
     * table as cached view data so no per-row queries are fired.
     */
    private function buildStats(): Collection
    {
        $codes = config('mtwd.widget_division_codes');

        // Query 1: divisions with their job-order codes (eager loaded)
        $divisions = Division::whereIn('code', $codes)
            ->with('jocodes')
            ->get();

        // Map division → its JO codes
        $divisionJoCodes = $divisions->mapWithKeys(
            fn (Division $d) => [$d->id => $d->jocodes->pluck('code')]
        );

        $allJoCodes = $divisionJoCodes->flatten()->unique()->values();

        if ($allJoCodes->isEmpty()) {
            return $divisions->map(fn (Division $d) => $this->emptyRow($d));
        }

        $today = Carbon::today()->toDateString();

        // Query 2: single aggregated query covering every stat for every JO code
        $rawStats = OnlineJobOrder::whereIn('job_order_code', $allJoCodes)
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

        // Aggregate per-division from the already-loaded in-memory result
        return $divisions->map(function (Division $division) use ($divisionJoCodes, $rawStats) {
            $joCodes = $divisionJoCodes[$division->id] ?? collect();

            $total           = 0;
            $accomplished    = 0;
            $ongoing         = 0;
            $receivedToday   = 0;
            $accomplishedToday = 0;
            $ongoingToday    = 0;
            $totalSeconds    = 0;
            $tatCount        = 0;

            foreach ($joCodes as $code) {
                $row = $rawStats->get($code);
                if (! $row) continue;

                $total             += (int) $row->total;
                $accomplished      += (int) $row->accomplished;
                $ongoing           += (int) $row->ongoing;
                $receivedToday     += (int) $row->received_today;
                $accomplishedToday += (int) $row->accomplished_today;
                $ongoingToday      += (int) $row->ongoing_today;
                $totalSeconds      += (int) $row->total_seconds;
                $tatCount          += (int) $row->tat_count;
            }

            $avgTat = $tatCount > 0
                ? CarbonInterval::seconds($totalSeconds / $tatCount)->cascade()->forHumans(['parts' => 2, 'join' => true])
                : 'N/A';

            return (object) [
                'division'           => $division,
                'total'              => $total,
                'accomplished'       => $accomplished,
                'ongoing'            => $ongoing,
                'receivedToday'      => $receivedToday,
                'accomplishedToday'  => $accomplishedToday,
                'ongoingToday'       => $ongoingToday,
                'totalSeconds'       => $totalSeconds,
                'tatCount'           => $tatCount,
                'avgTat'             => $avgTat,
            ];
        });
    }

    private function emptyRow(Division $division): object
    {
        return (object) [
            'division'           => $division,
            'total'              => 0,
            'accomplished'       => 0,
            'ongoing'            => 0,
            'receivedToday'      => 0,
            'accomplishedToday'  => 0,
            'ongoingToday'       => 0,
            'totalSeconds'       => 0,
            'tatCount'           => 0,
            'avgTat'             => 'N/A',
        ];
    }

    public function table(Table $table): Table
    {
        $stats = $this->buildStats();

        // Footer totals — computed from already-loaded PHP objects (no extra queries)
        $totals = [
            'totalJO'               => $stats->sum('total'),
            'totalAccomplished'     => $stats->sum('accomplished'),
            'totalOngoing'          => $stats->sum('ongoing'),
            'totalReceivedToday'    => $stats->sum('receivedToday'),
            'totalAccomplishedToday'=> $stats->sum('accomplishedToday'),
            'totalOngoingToday'     => $stats->sum('ongoingToday'),
        ];

        $globalTatCount   = $stats->sum('tatCount');
        $globalTatSeconds = $stats->sum('totalSeconds');
        $totals['avgTATReadable'] = $globalTatCount > 0
            ? CarbonInterval::seconds($globalTatSeconds / $globalTatCount)->cascade()->forHumans(['parts' => 2, 'join' => true])
            : 'N/A';

        // Index stats by division id for O(1) lookup in getStateUsing
        $statsByDivisionId = $stats->keyBy(fn ($s) => $s->division->id);

        return $table
            ->query(Division::whereIn('code', config('mtwd.widget_division_codes')))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Division Name')
                    ->wrap(),

                Tables\Columns\TextColumn::make('receivedToday')
                    ->label('Received Today')
                    ->getStateUsing(fn (Division $record) =>
                        $statsByDivisionId[$record->id]?->receivedToday ?? 0
                    ),

                Tables\Columns\TextColumn::make('accomplishedToday')
                    ->label('Accomplished Today')
                    ->getStateUsing(fn (Division $record) =>
                        $statsByDivisionId[$record->id]?->accomplishedToday ?? 0
                    ),

                Tables\Columns\TextColumn::make('ongoingToday')
                    ->label('Ongoing Today')
                    ->getStateUsing(fn (Division $record) =>
                        $statsByDivisionId[$record->id]?->ongoingToday ?? 0
                    ),

                Tables\Columns\TextColumn::make('totalJO')
                    ->label('Total Received')
                    ->getStateUsing(fn (Division $record) =>
                        number_format($statsByDivisionId[$record->id]?->total ?? 0)
                    ),

                Tables\Columns\TextColumn::make('totalAccomplished')
                    ->label('Total Accomplished')
                    ->getStateUsing(function (Division $record) use ($statsByDivisionId) {
                        $row = $statsByDivisionId[$record->id] ?? null;
                        return number_format($row?->accomplished ?? 0);
                    })
                    ->description(function (Division $record) use ($statsByDivisionId): string {
                        $row = $statsByDivisionId[$record->id] ?? null;
                        if (! $row || $row->total === 0) return '0%';
                        return round(($row->accomplished / $row->total) * 100, 2) . '%';
                    }, position: 'below'),

                Tables\Columns\TextColumn::make('totalOngoing')
                    ->label('Total Ongoing')
                    ->getStateUsing(function (Division $record) use ($statsByDivisionId) {
                        $row = $statsByDivisionId[$record->id] ?? null;
                        return number_format($row?->ongoing ?? 0);
                    })
                    ->description(function (Division $record) use ($statsByDivisionId): string {
                        $row = $statsByDivisionId[$record->id] ?? null;
                        if (! $row || $row->total === 0) return '0%';
                        return round(($row->ongoing / $row->total) * 100, 2) . '%';
                    }, position: 'below'),

                Tables\Columns\TextColumn::make('avgTAT')
                    ->label('Avg TAT')
                    ->wrap()
                    ->getStateUsing(fn (Division $record) =>
                        $statsByDivisionId[$record->id]?->avgTat ?? 'N/A'
                    ),
            ])
            ->paginated(false)
            ->contentFooter(view('table.footer', ['totals' => $totals]));
    }
}
