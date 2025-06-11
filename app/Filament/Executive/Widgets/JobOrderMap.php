<?php

namespace App\Filament\Executive\Widgets;

use DB;
use App\Models\City;
use Filament\Tables;
use App\Models\Barangay;
use App\Models\Category;
use App\Models\Username;
use Filament\Actions\Action;
use App\Models\OnlineJobOrder;
use Filament\Tables\Filters\Filter;
use Filament\Infolists\Components\Card;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;
use Cheesegrits\FilamentGoogleMaps\Actions\GoToAction;
use Cheesegrits\FilamentGoogleMaps\Filters\MapIsFilter;
use Cheesegrits\FilamentGoogleMaps\Filters\RadiusFilter;
use Cheesegrits\FilamentGoogleMaps\Tests\Models\Location;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapTableWidget;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;


class JobOrderMap extends MapTableWidget
{
    protected static ?string $heading = 'Online Job Order Map';

    protected static ?int $sort = 3;

    protected static ?string $pollingInterval = null;

	protected static ?bool $clustering = true;

	protected static ?string $mapId = 'jobOrders';

    protected int | string | array $columnSpan = 'full';

    protected static ?string $markerAction = 'markerAction';

    protected static bool $isDiscovered = false;

    protected function getTableQuery(): Builder
	{
		return \App\Models\OnlineJobOrder::with('jocode')->whereNotNull('lat')->whereNull('date_accomplished')->latest();
	}

	protected function getTableColumns(): array
	{
		return [
                // Tables\Columns\TextColumn::make('lat')
                // ->searchable(),
                // Tables\Columns\TextColumn::make('lng')
                // ->searchable(),
			    Tables\Columns\TextColumn::make('jo_number')
                ->searchable(),
                Tables\Columns\TextColumn::make('date_requested')
                    ->dateTime('F d, Y'),
                Tables\Columns\TextColumn::make('account_number')
                ->searchable(),
                // IconColumn::make('account.latitude')
                // ->label('Map View')
                // ->icon('fas-map-marked-alt')
                // ->color('info')
                // ->size(IconColumn\IconColumnSize::Large)
                // ->url(function ($record) {
                //     if (!$record || !$record->account || !$record->account->latitude || !$record->account->longtitude) {
                //         return null;
                //     }

                //     return 'https://www.google.com/maps/dir/17.6223543,121.7214678/' .
                //         $record->account->latitude . ',' . $record->account->longtitude .
                //         '/@' . $record->account->latitude . ',' . $record->account->longtitude . ',20z';
                // }, shouldOpenInNewTab: true)
                // ->tooltip('Go to map view')
                // ->alignCenter(),

                // ViewColumn::make('id')
                // ->view('filament.tables.columns.map-view')
                // ,


                // Tables\Columns\TextColumn::make('registered_name')
                // ->searchable(),
                // Tables\Columns\TextColumn::make('meter_number')
                // ->searchable(),
                // Tables\Columns\TextColumn::make('jocode.description')
                // ->label('JO Type')
                // ->searchable(),
                Tables\Columns\TextColumn::make('jocode.category.name')
                ->label('Category')
                ->description(function (OnlineJobOrder $record) {
                    return $record->jocode->description;
                })
                ->wrap()
                 ->searchable(query: function ($query, $search) {
                    $query->orWhereHas('jocode', function ($subQuery) use ($search) {
                        $subQuery->where('description', 'like', "%{$search}%");
                    });
                }),
                // Tables\Columns\TextColumn::make('jocode.description')
                // ->label('Type')
                // ->hidden()
                // ->searchable(),
                 Tables\Columns\TextColumn::make('jocode.division.name')
                ->label('Division')
                ->searchable()
                ->wrap(),
                Tables\Columns\TextColumn::make('address')
                ->searchable()
                ->getStateUsing(function (OnlineJobOrder $record) {
                    return $record->address.', '.Barangay::where('id', $record->barangay)->value('name').', '.City::where('id', $record->town)->value('name');
                })
                // ->wrap()
                ->limit(20)
                ->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();

                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }

                    // Only render the tooltip if the column content exceeds the length limit.
                    return $state;
                }),
                Tables\Columns\TextColumn::make('requested_by')
                ->searchable()
                ->wrap(),
                Tables\Columns\TextColumn::make('status')
                ->searchable(),
		];
	}

	protected function getTableFilters(): array
	{
		return [
			// RadiusFilter::make('location')
			// 	->section('Radius Filter')
			// 	->selectUnit(),
            MapIsFilter::make('map'),
            DateRangeFilter::make('date_requested')
            ->withIndicator(),
            SelectFilter::make('status')
            ->label('Status')
            ->options(fn () => OnlineJobOrder::whereNull('date_accomplished')
                ->distinct()
                ->pluck('status', 'status')
                ->toArray())
            ->placeholder('All Statuses')
            ->searchable(),
            SelectFilter::make('division')
            ->label('Division')
            ->options(
                fn () => \App\Models\Division::pluck('name', 'id')->toArray()
            )
            ->searchable()
            ->placeholder('All Divisions')
            ->query(function ($query, array $data) {
                if (filled($data['value'])) {
                    $query->whereHas('jocode.division', function ($q) use ($data) {
                        $q->where('id', $data['value']);
                    });
                }
            }),
            SelectFilter::make('category')
            ->label('Category')
            ->options(
                fn () => \App\Models\Category::pluck('name', 'id')->toArray()
            )
            ->searchable()
            ->placeholder('All Categories')
            ->query(function ($query, array $data) {
                if (filled($data['value'])) {
                    $query->whereHas('jocode.category', function ($q) use ($data) {
                        $q->where('id', $data['value']);
                    });
                }
            }),
		];
	}

	protected function getTableActions(): array
	{
		return [
			Tables\Actions\ViewAction::make()
            ->slideOver()
            ->infolist([
                Section::make()
    ->columns([
        'sm' => 2,
        // 'xl' => 6,
        // '2xl' => 8,
    ])
    ->schema([
            TextEntry::make('jo_number')->label('JO Number'),
                // TextEntry::make('jocode.description')->label('Ty[e'),
                TextEntry::make('date_requested')
                ->dateTime(),
                TextEntry::make('jocode.description')->label('JO Type'),
                TextEntry::make('jocode.category.name')->label('Category'),
                TextEntry::make('jocode.division.name')->label('Division'),
                TextEntry::make('requested_by'),
                TextEntry::make('contact_number'),
                TextEntry::make('account_number'),
                TextEntry::make('registered_name'),
                TextEntry::make('meter_number'),
                TextEntry::make('address')
                ->getStateUsing(function (OnlineJobOrder $record) {
                    return $record->address.', '.Barangay::where('id', $record->barangay)->value('name').', '.City::where('id', $record->town)->value('name');
                }),
                // TextEntry::make('barangay')
                //     ->getStateUsing(fn (OnlineJobOrder $record) =>
                //         DB::connection('kitdb')->table('barangays')->where('id', $record->barangay)->value('name') ?? 'N/A'),

                // TextEntry::make('contact_number'),
                TextEntry::make('processed_by')
                    ->getStateUsing(fn (OnlineJobOrder $record) =>
                        Username::where('code', $record->processed_by)->value('name') ?? ''),
                TextEntry::make('status'),
        // ...
    ])


            ])
            ->recordTitle(fn (OnlineJobOrder $record) => 'JO #' . $record->jo_number),
			// Tables\Actions\EditAction::make(),
            // GoToAction::make()
            //     ->zoom(14),
		];
	}

    protected function getTableRecordAction(): ?string
    {
        return 'view';
    }

	protected function getData(): array
	{
		$locations = $this->getRecords();

		$data = [];

		foreach ($locations as $location)
		{
			$data[] = [
				'location' => [
					'lat' => $location->lat ? round(floatval($location->lat), static::$precision) : 0,
                    'lng' => $location->lng ? round(floatval($location->lng), static::$precision) : 0,
				],
                'id'      => $location->id,
                'icon' => [
                        'url' => url('images/wrench.svg'),
                        'type' => 'svg',
                        'color' => 'red',
                        'scale' => [26,26],
                    ],
                'label'     => view(
                        'widgets.job-order-label',
                        [
                            'jobOrderDescription'   => $location->jocode?->description,
                            'jobOrderAccount' => $location->account_number,
                            // 'jobOrderIcon' => $location->icon,
                            'jobOrderStatus' => $location->status,
                            'jobOrderCode' => $location->jo_number,
                        ]
                    )->render(),
			];
		}

		return $data;
	}



	public function markerAction(): Action
	{
		return Action::make('markerAction')
			->label('Details')
			->infolist([
				Card::make([
                    TextEntry::make('jocode.description')
                        ->label('Description'),
                    TextEntry::make('date_requested'),
					TextEntry::make('account_number'),
                    TextEntry::make('address'),
                    TextEntry::make('barangay')
                        ->getStateUsing(function (OnlineJobOrder $record) {
                        return DB::connection('kitdb')->table('barangays')->where('id', $record->barangay)->value('name') ?? 'N/A';
                        }),
                    TextEntry::make('requested_by'),

					TextEntry::make('contact_number'),
                    TextEntry::make('processed_by')
                        ->getStateUsing(function (OnlineJobOrder $record) {
                            return Username::where('code', $record->processed_by)->value('name') ?? '';
                        }),
					TextEntry::make('status'),

				])
				->columns(3)
			])
			->record(function (array $arguments) {
				return array_key_exists('model_id', $arguments) ? OnlineJobOrder::find($arguments['model_id']) : null;
			})
			->modalSubmitAction(false);
	}

    // protected function getTableRecordsPerPageSelectOptions(): array
    // {
    //     return [5000, 500, 50];
    // }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5000];
    }

    // protected function getTableRecordsPerPage(): int
    // {
    //     return -1;
    // }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
    protected function getScripts(): array
    {
        return [
            asset('js/job-order-map.js'),
        ];
    }
}
