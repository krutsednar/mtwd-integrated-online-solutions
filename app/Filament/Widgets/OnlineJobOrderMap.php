<?php

namespace App\Filament\Widgets;

use DB;
use Filament\Tables;
// use Geocoder\Location;
use App\Models\Username;
use Filament\Actions\Action;
use App\Models\OnlineJobOrder;
use Filament\Tables\Filters\Filter;
use Filament\Infolists\Components\Card;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
// use Cheesegrits\FilamentGoogleMaps\Tests\Models\Location;
use Filament\Infolists\Components\TextEntry;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;
use Cheesegrits\FilamentGoogleMaps\Actions\GoToAction;
use Cheesegrits\FilamentGoogleMaps\Filters\MapIsFilter;
use Cheesegrits\FilamentGoogleMaps\Filters\RadiusFilter;
use Cheesegrits\FilamentGoogleMaps\Tests\Models\Location;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapTableWidget;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class OnlineJobOrderMap extends MapTableWidget
{
    // use HasWidgetShield;

	protected static ?string $heading = 'Online Job Order Map';

	protected static ?int $sort = 999;

	protected static ?string $pollingInterval = null;

	protected static ?bool $clustering = true;

	protected static ?string $mapId = 'incidents';

    protected int | string | array $columnSpan = 'full';

    protected static ?string $markerAction = 'markerAction';


	protected function getTableQuery(): Builder
	{
		return \App\Models\OnlineJobOrder::whereNotNull('lat')->where('status', '!=', 'Accomplished')->latest();
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
                IconColumn::make('account.latitude')
                ->label('Map View')
                ->icon('fas-map-marked-alt')
                ->color('info')
                ->size(IconColumn\IconColumnSize::Large)
                ->url(function ($record) {
                    if (!$record || !$record->account || !$record->account->latitude || !$record->account->longtitude) {
                        return null;
                    }

                    return 'https://www.google.com/maps/dir/17.6223543,121.7214678/' .
                        $record->account->latitude . ',' . $record->account->longtitude .
                        '/@' . $record->account->latitude . ',' . $record->account->longtitude . ',20z';
                }, shouldOpenInNewTab: true)
                ->tooltip('Go to map view')
                ->alignCenter(),

                // ViewColumn::make('id')
                // ->view('filament.tables.columns.map-view')
                // ,


                // Tables\Columns\TextColumn::make('registered_name')
                // ->searchable(),
                // Tables\Columns\TextColumn::make('meter_number')
                // ->searchable(),
                Tables\Columns\TextColumn::make('jocode.description')
                ->label('Description')
                ->searchable(),
                Tables\Columns\TextColumn::make('address')
                ->searchable(),
                // Tables\Columns\TextColumn::make('barangay')
                // ->getStateUsing(function (OnlineJobOrder $record) {
                //     return DB::connection('kitdb')->table('barangays')->where('id', $record->barangay)->value('name') ?? 'N/A';
                // })
                // ->searchable(),
                // Tables\Columns\TextColumn::make('town')
                // ->getStateUsing(function (OnlineJobOrder $record) {
                //     return DB::connection('kitdb')->table('cities')->where('id', $record->town)->value('name') ?? 'N/A';
                // })
                // ->searchable(),
                Tables\Columns\TextColumn::make('requested_by')
                ->searchable(),
                Tables\Columns\TextColumn::make('contact_number')
                ->searchable(),
                // Tables\Columns\TextColumn::make('email')
                // ->searchable(),
                // Tables\Columns\TextColumn::make('mode_received')
                // ->searchable(),
                // Tables\Columns\TextColumn::make('remarks'),
                // Tables\Columns\TextColumn::make('processed_by')
                // ->getStateUsing(function (OnlineJobOrder $record) {
                //     return Username::where('code', $record->processed_by)->value('name') ?? '';
                // })
                // ->badge()
                // ->color('success')
                // ->searchable(),
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
            SelectFilter::make('status')
            ->label('Status')
            ->options(fn () => OnlineJobOrder::query()
                ->distinct()
                ->pluck('status', 'status')
                ->reject(fn ($status) => $status === 'Accomplished')
                ->toArray())
            ->placeholder('All Statuses')
            ->searchable(),
            Filter::make('date_requested')
                ->form([
                    DatePicker::make('start_date')
                    ->native(false)
                    ->default(now()->subMonths(1))
                    ->displayFormat('F d, Y'),
                    DatePicker::make('end_date')
                    ->native(false)
                    ->default(now())
                    ->displayFormat('F d, Y'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['start_date'],
                            fn(Builder $query, $date): Builder => $query->whereDate('date_requested', '>=', $date),
                        )
                        ->when(
                            $data['end_date'],
                            fn(Builder $query, $date): Builder => $query->whereDate('date_requested', '<=', $date),
                        );
                }),

		];
	}

	protected function getTableActions(): array
	{
		return [
			Tables\Actions\ViewAction::make(),
			Tables\Actions\EditAction::make(),
            // GoToAction::make()
            //     ->zoom(14),
		];
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

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10000, 25, 50, 100];
    }

}
