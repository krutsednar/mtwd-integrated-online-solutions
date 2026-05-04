<?php

namespace App\Filament\Executive\Widgets;

use App\Models\Category;
use App\Models\OnlineJobOrder;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;

class JobOrderPerCategory extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    private function buildStats(): Collection
    {
        $excluded = config('mtwd.excluded_category_codes');

        $categories = Category::whereNotIn('code', $excluded)
            ->with('jocodes')
            ->get();

        $categoryJoCodes = $categories->mapWithKeys(
            fn (Category $c) => [$c->id => $c->jocodes->pluck('code')]
        );

        $allJoCodes = $categoryJoCodes->flatten()->unique()->values();

        if ($allJoCodes->isEmpty()) {
            return $categories->map(fn (Category $c) => $this->emptyRow($c));
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

        return $categories->map(function (Category $category) use ($categoryJoCodes, $rawStats) {
            $joCodes = $categoryJoCodes[$category->id] ?? collect();

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
                'category', 'total', 'accomplished', 'ongoing',
                'receivedToday', 'accomplishedToday', 'ongoingToday',
                'totalSeconds', 'tatCount', 'avgTat'
            );
        });
    }

    private function emptyRow(Category $category): object
    {
        return (object) [
            'category' => $category, 'total' => 0, 'accomplished' => 0, 'ongoing' => 0,
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

        $statsByCategoryId = $stats->keyBy(fn ($s) => $s->category->id);

        $excluded = config('mtwd.excluded_category_codes');

        return $table
            ->query(Category::query()->whereNotIn('code', $excluded))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Job Order Category')
                    ->wrap(),

                Tables\Columns\TextColumn::make('receivedToday')
                    ->label('Received Today')
                    ->getStateUsing(fn (Category $record) =>
                        number_format($statsByCategoryId[$record->id]?->receivedToday ?? 0)
                    ),

                Tables\Columns\TextColumn::make('accomplishedToday')
                    ->label('Accomplished Today')
                    ->getStateUsing(fn (Category $record) =>
                        number_format($statsByCategoryId[$record->id]?->accomplishedToday ?? 0)
                    ),

                Tables\Columns\TextColumn::make('ongoingToday')
                    ->label('Ongoing Today')
                    ->getStateUsing(fn (Category $record) =>
                        number_format($statsByCategoryId[$record->id]?->ongoingToday ?? 0)
                    ),

                Tables\Columns\TextColumn::make('totalJO')
                    ->label('Total Received')
                    ->getStateUsing(fn (Category $record) =>
                        number_format($statsByCategoryId[$record->id]?->total ?? 0)
                    ),

                Tables\Columns\TextColumn::make('totalAccomplished')
                    ->label('Total Accomplished')
                    ->getStateUsing(function (Category $record) use ($statsByCategoryId) {
                        return number_format($statsByCategoryId[$record->id]?->accomplished ?? 0);
                    })
                    ->description(function (Category $record) use ($statsByCategoryId): string {
                        $row = $statsByCategoryId[$record->id] ?? null;
                        if (! $row || $row->total === 0) return '0%';
                        return round(($row->accomplished / $row->total) * 100, 2) . '%';
                    }, position: 'below'),

                Tables\Columns\TextColumn::make('totalOngoing')
                    ->label('Total Ongoing')
                    ->getStateUsing(function (Category $record) use ($statsByCategoryId) {
                        return number_format($statsByCategoryId[$record->id]?->ongoing ?? 0);
                    })
                    ->description(function (Category $record) use ($statsByCategoryId): string {
                        $row = $statsByCategoryId[$record->id] ?? null;
                        if (! $row || $row->total === 0) return '0%';
                        return round(($row->ongoing / $row->total) * 100, 2) . '%';
                    }, position: 'below'),

                Tables\Columns\TextColumn::make('avgTAT')
                    ->label('Avg TAT')
                    ->wrap()
                    ->getStateUsing(fn (Category $record) =>
                        $statsByCategoryId[$record->id]?->avgTat ?? 'N/A'
                    ),
            ])
            ->paginated(false)
            ->contentFooter(view('table.category-footer', ['totals' => $totals]));
    }
}
