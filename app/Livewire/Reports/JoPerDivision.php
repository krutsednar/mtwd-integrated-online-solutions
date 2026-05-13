<?php

namespace App\Livewire\Reports;

use App\Models\Division;
use App\Models\OnlineJobOrder;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Livewire\Component;

class JoPerDivision extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    /**
     * Build per-division stats in 2 DB queries.
     * Query 1: divisions + jocodes eager load.
     * Query 2: one aggregated GROUP BY across all JO codes.
     */
    private function buildStats(): Collection
    {
        $codes = config('mtwd.widget_division_codes');

        $divisions = Division::whereIn('code', $codes)
            ->with('jocodes')
            ->get();

        $divisionJoCodes = $divisions->mapWithKeys(
            fn (Division $d) => [$d->id => $d->jocodes->pluck('code')]
        );

        $allJoCodes = $divisionJoCodes->flatten()->unique()->values();

        if ($allJoCodes->isEmpty()) {
            return $divisions->map(fn (Division $d) => $this->emptyRow($d));
        }

        $today = Carbon::today()->toDateString();

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

        return $divisions->map(function (Division $division) use ($divisionJoCodes, $rawStats) {
            $joCodes = $divisionJoCodes[$division->id] ?? collect();

            $total = $accomplished = $ongoing = 0;
            $receivedToday = $accomplishedToday = $ongoingToday = 0;
            $totalSeconds = $tatCount = 0;

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

            return (object) compact(
                'division', 'total', 'accomplished', 'ongoing',
                'receivedToday', 'accomplishedToday', 'ongoingToday',
                'totalSeconds', 'tatCount', 'avgTat'
            );
        });
    }

    private function emptyRow(Division $division): object
    {
        return (object) [
            'division' => $division, 'total' => 0, 'accomplished' => 0, 'ongoing' => 0,
            'receivedToday' => 0, 'accomplishedToday' => 0, 'ongoingToday' => 0,
            'totalSeconds' => 0, 'tatCount' => 0, 'avgTat' => 'N/A',
        ];
    }

    public function table(Table $table): Table
    {
        $stats = $this->buildStats();

        $totals = [
            'totalJO'                => $stats->sum('total'),
            'totalAccomplished'      => $stats->sum('accomplished'),
            'totalOngoing'           => $stats->sum('ongoing'),
            'totalReceivedToday'     => $stats->sum('receivedToday'),
            'totalAccomplishedToday' => $stats->sum('accomplishedToday'),
            'totalOngoingToday'      => $stats->sum('ongoingToday'),
        ];

        $globalTatCount   = $stats->sum('tatCount');
        $globalTatSeconds = $stats->sum('totalSeconds');
        $totals['avgTATReadable'] = $globalTatCount > 0
            ? CarbonInterval::seconds($globalTatSeconds / $globalTatCount)->cascade()->forHumans(['parts' => 2, 'join' => true])
            : 'N/A';

        $statsByDivisionId = $stats->keyBy(fn ($s) => $s->division->id);

        return $table
            ->query(Division::whereIn('code', config('mtwd.widget_division_codes')))
            ->columns([
                TextColumn::make('name')
                    ->label('Division Name')
                    ->wrap(),

                TextColumn::make('receivedToday')
                    ->label('Received Today')
                    ->getStateUsing(fn (Division $record) =>
                        $statsByDivisionId[$record->id]?->receivedToday ?? 0
                    ),

                TextColumn::make('accomplishedToday')
                    ->label('Accomplished Today')
                    ->getStateUsing(fn (Division $record) =>
                        $statsByDivisionId[$record->id]?->accomplishedToday ?? 0
                    ),

                TextColumn::make('ongoingToday')
                    ->label('Ongoing Today')
                    ->getStateUsing(fn (Division $record) =>
                        $statsByDivisionId[$record->id]?->ongoingToday ?? 0
                    ),

                TextColumn::make('totalJO')
                    ->label('Total Received')
                    ->getStateUsing(fn (Division $record) =>
                        number_format($statsByDivisionId[$record->id]?->total ?? 0)
                    ),

                TextColumn::make('totalAccomplished')
                    ->label('Total Accomplished')
                    ->getStateUsing(function (Division $record) use ($statsByDivisionId) {
                        return number_format($statsByDivisionId[$record->id]?->accomplished ?? 0);
                    })
                    ->description(function (Division $record) use ($statsByDivisionId): string {
                        $row = $statsByDivisionId[$record->id] ?? null;
                        if (! $row || $row->total === 0) return '0%';
                        return round(($row->accomplished / $row->total) * 100, 2) . '%';
                    }, position: 'below'),

                TextColumn::make('totalOngoing')
                    ->label('Total Ongoing')
                    ->getStateUsing(function (Division $record) use ($statsByDivisionId) {
                        return number_format($statsByDivisionId[$record->id]?->ongoing ?? 0);
                    })
                    ->description(function (Division $record) use ($statsByDivisionId): string {
                        $row = $statsByDivisionId[$record->id] ?? null;
                        if (! $row || $row->total === 0) return '0%';
                        return round(($row->ongoing / $row->total) * 100, 2) . '%';
                    }, position: 'below'),

                TextColumn::make('avgTAT')
                    ->label('Avg TAT')
                    ->wrap()
                    ->getStateUsing(fn (Division $record) =>
                        $statsByDivisionId[$record->id]?->avgTat ?? 'N/A'
                    ),
            ])
            ->paginated(false)
            ->contentFooter(view('table.footer', ['totals' => $totals]));
    }

    public function render()
    {
        return view('livewire.reports.jo-per-division');
    }
}
