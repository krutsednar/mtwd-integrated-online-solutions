<?php

namespace App\Filament\MOJO\Resources;

use DB;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\City;
use App\Models\User;
use Filament\Tables;
use GuzzleHttp\Client;
use App\Models\Account;
use Filament\Forms\Get;
use App\Models\Barangay;
use App\Models\Username;
use Filament\Forms\Form;
use App\Models\JoDispatch;
use Filament\Tables\Table;
use App\Models\JobOrderCode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\OnlineJobOrder;
use Filament\Resources\Resource;
use App\Livewire\JobOrderHistory;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Http;
use Filament\Infolists\Components\Card;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\TextEntry;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\MOJO\Resources\OnlineJobOrderResource\Pages;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use App\Filament\MOJO\Resources\OnlineJobOrderResource\RelationManagers;
use App\Models\JoAccomplishment;

class OnlineJobOrderResource extends Resource
{
    protected static ?string $model = OnlineJobOrder::class;

    protected static ?string $navigationIcon = 'heroicon-c-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('jo_number')
                    ->label('JO Number')
                    ->required()
                    ->readOnly()
                    ->reactive()
                    ->placeholder(
                        fn () =>

                        Carbon::now()->format('Ym').str_pad(OnlineJobOrder::orderByDesc('created_at')
                                ->selectRaw("CAST(RIGHT(jo_number, 7) AS UNSIGNED) as number")
                                ->value('number') + 1, 7, '0', STR_PAD_LEFT)
                        )
                    ,
                Forms\Components\DateTimePicker::make('date_requested')
                    ->required(true)
                    ->default(Carbon::now()),
                Forms\Components\TextInput::make('account_number')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $data = DB::connection('kitdb')->table('accounts')
                            ->where('accmasterlist', $state)
                            ->first();

                        if ($data) {
                            $set('registered_name', $data->mastername ?? 'No Record');
                            $set('meter_number', $data->meter_number ?? 'No Record');
                        } else {
                            $set('registered_name', 'No Record');
                            $set('meter_number', 'No Record');
                        }

                        if ($data && !is_null($data->latitude)) {
                            $set('lat', $data->latitude ?? null);
                            $set('lng', $data->longtitude ?? null);
                        } else {
                            $set('lat', null);
                            $set('lng', null);
                        }

                    }),
                Forms\Components\TextInput::make('lat')
                    ->readOnly()
                    ->hidden(),
                Forms\Components\TextInput::make('lng')
                    ->readOnly()
                    ->hidden(),
                Forms\Components\TextInput::make('registered_name')
                    ->readOnly(),
                Forms\Components\TextInput::make('meter_number')
                    ->readOnly(),
                Forms\Components\Select::make('job_order_code')
                    ->required(true)
                    ->options(function () {
                        return JobOrderCode::pluck('description', 'code')->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->reactive(),

                Forms\Components\Select::make('town')
                    ->required(true)->options(function () {
                        return DB::connection('kitdb')->table('cities')->whereIn('id', ['21529', '21527', '21520'])->pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->reactive()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Determine prefix based on selected town
                        $prefix = match ($state) {
                            '21527' => 'SO',
                            '21520' => 'PO',
                            '21529' => 'TO',
                            default => 'MOJO',
                        };

                        // $count = \App\Models\OnlineJobOrder::withTrashed()->count() + 1;
                        $suffix = str_pad(
                                (OnlineJobOrder::latest()->selectRaw("CAST(RIGHT(jo_number, 7) AS UNSIGNED) as number")
                                    ->orderByDesc(DB::raw("CAST(RIGHT(jo_number, 7) AS UNSIGNED)"))
                                    ->value('number') ?? 0) + 1,
                                7,
                                '0',
                                STR_PAD_LEFT
                            );
                        $joNumber = $prefix . Carbon::now()->format('Ym') . $suffix;

                        $set('jo_number', $joNumber);
                    }),
                Forms\Components\Select::make('barangay')
                    ->required(true)
                    ->options(fn (Get $get): Collection => DB::table('barangays')
                    ->where('city_id', $get('town'))
                    ->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\TextInput::make('address')
                    ->label('House No./Blk/Unit/Zone/Purok/Street')
                    ->required(true),
                Forms\Components\TextInput::make('requested_by')
                    ->required(true),
                Forms\Components\TextInput::make('contact_number')
                    ->required(true)
                    ->prefix('+63')
                    ->maxLength(10),
                Forms\Components\TextInput::make('email')
                    ->email(true),
                Forms\Components\Select::make('mode_received')
                    ->options([
                        'Text' => 'Text',
                        'Personal' => 'Personal',
                        'Phone' => 'Phone',
                        'Social Media' => 'Social Media',
                    ])
                    ->required(true),

                // Forms\Components\Select::make('processed_by')
                //     ->options(function () {
                //         return Username::pluck('name', 'code')->toArray();
                //     })
                //     ->searchable()
                //     ->preload()
                //     ->required(true),
                Forms\Components\TextInput::make('processed_by')
                    ->label('Processed By')
                    ->default(auth()->user()->name) // display name
                    ->readOnly() // make it non-editable
                    ->required()
                    ->afterStateHydrated(function ($component) {
                        $component->state(auth()->user()->name);
                    })
                     ->dehydrateStateUsing(function ($state) {
                    $user = auth()->user();
                    $value = $user->jo_id ?? $user->employee_number;
                    return str_replace('-', '', $value);
                })
                    ->dehydrated(true),
                Forms\Components\TextInput::make('status')
                    ->default('For Forward')
                    ->required(true)
                    ->readOnly(),
                Forms\Components\Textarea::make('remarks')
                    ->required(true),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = auth()->user();

                if (in_array($user->division_id, [2022, 2023, 7, 2, 3, 4, 5])) {
                    return OnlineJobOrder::query()
                        ->with('account')
                        ->orderBy('id', 'desc');
                }

                return OnlineJobOrder::query()
                    ->whereHas('jocode.division', function ($query) use ($user) {
                        $query->where('code', $user->division_id);
                    })
                    ->orWhere('processed_by', $user->jo_id)
                    ->orWhere('processed_by', str_replace('-', '', $user->employee_number))
                    ->with('account')
                    ->orderBy('id', 'desc');
            })
            ->headerActions([
                ExportAction::make()->exports([
                    ExcelExport::make('table')->fromTable()->withFilename('MTWD Online Job Orders - '.date('F d, Y')),
                ])->color('success'),

            ])
            // ->recordAction(Tables\Actions\ViewAction::class)
            ->columns([

                Tables\Columns\TextColumn::make('jo_number')
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('lat')
                    ->label('Map View')
                    ->icon('fas-map-marked-alt')
                    ->color('info')
                    ->size(IconColumn\IconColumnSize::Large)
                    ->disabled(fn ($record) => !$record->lat || !$record->lng)
                    ->url(function ($record) {
                        if (!$record || !$record->lat || !$record->lng ) {
                            return null;
                        }
                        return 'https://www.google.com/maps/dir/17.6223543,121.7214678/' .
                            $record->lat  . ',' . $record->lng .
                            '/@' . $record->lat . ',' . $record->lng . ',20z';
                    }, shouldOpenInNewTab: true)
                    ->tooltip('Go to map view')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('date_requested')
                    ->dateTime('F d, Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->description(fn (OnlineJobOrder $record): string => $record->registered_name ?? 'No Record', position: 'below')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('jocode.description')
                ->label('JO Type')
                ->searchable()
                ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('jocode.category.name')
                ->label('JO Category')
                ->searchable()
                ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('jocode.division.name')
                ->label('Division Concerned')
                ->searchable()
                ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('requested_by')
                ->searchable()
                ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('contact_number')
                ->searchable()
                ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('address')
                ->searchable()
                ->getStateUsing(function (OnlineJobOrder $record) {
                    return $record->address.', '.Barangay::where('id', $record->barangay)->value('name').', '.City::where('id', $record->town)->value('name');
                })
                // ->wrap()
                ->limit(30)
                ->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();

                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }
                    return $state;
                })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('mode_received')
                ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('remarks')
                ->limit(30)
                ->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();

                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }

                    return $state;
                })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('processed_by')
                 ->getStateUsing(function (OnlineJobOrder $record) {
                    $user = User::where('jo_id', $record->processed_by)->first();

                    if (! $user) {
                        $user = User::where('employee_number', substr_replace($record->processed_by, '-', 2, 0))->first();
                    }

                    return $user ? "{$user->first_name} {$user->last_name}" : '';
                })
                ->badge()
                ->color('success')
                ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('forwarded_by')
                 ->getStateUsing(function (OnlineJobOrder $record) {
                    $user = User::where('jo_id', $record->forwarded_by)->first();

                    if (! $user) {
                        $user = User::where('employee_number', substr_replace($record->forwarded_by, '-', 2, 0))->first();
                    }

                    if($record->forwarded_by){
                        return $user ? "{$user->first_name} {$user->last_name}" : '';
                    } else {
                        return '';
                    }

                })
                ->badge()
                ->color('success')
                ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_forwarded')
                    ->dateTime('F d, Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('received_by')
                 ->getStateUsing(function (OnlineJobOrder $record) {
                    $user = User::where('jo_id', $record->received_by)->first();

                    if (! $user) {
                        $user = User::where('employee_number', substr_replace($record->received_by, '-', 2, 0))->first();
                    }

                    if($record->received_by){
                        return $user ? "{$user->first_name} {$user->last_name}" : '';
                    } else {
                        return '';
                    }
                })
                ->badge()
                ->color('success')
                ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_received')
                    ->dateTime('F d, Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('dispatched_by')
                 ->getStateUsing(function (OnlineJobOrder $record) {
                    $user = User::where('jo_id', $record->dispatched_by)->first();

                    if (! $user) {
                        $user = User::where('employee_number', substr_replace($record->dispatched_by, '-', 2, 0))->first();
                    }

                    if($record->dispatched_by){
                        return $user ? "{$user->first_name} {$user->last_name}" : '';
                    } else {
                        return '';
                    }
                })
                ->badge()
                ->color('success')
                ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_dispatched')
                    ->dateTime('F d, Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('accomplishment_processed_by')
                 ->getStateUsing(function (OnlineJobOrder $record) {
                    $user = User::where('jo_id', $record->accomplishment_processed_by)->first();

                    if (! $user) {
                        $user = User::where('employee_number', substr_replace($record->accomplishment_processed_by, '-', 2, 0))->first();
                    }

                    if($record->accomplishment_processed_by){
                        return $user ? "{$user->first_name} {$user->last_name}" : '';
                    } else {
                        return '';
                    }
                })
                ->badge()
                ->color('success')
                ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_accomplished')
                    ->dateTime('F d, Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('actions_taken')
                    ->searchable()
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('recommendations')
                    ->searchable()
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('field_findings')
                    ->searchable()
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('acknowledge_by')
                    ->label('Acknowledged By')
                    ->searchable()
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pad_received_by')
                 ->getStateUsing(function (OnlineJobOrder $record) {
                    $user = User::where('jo_id', $record->pad_received_by)->first();

                    if (! $user) {
                        $user = User::where('employee_number', substr_replace($record->pad_received_by, '-', 2, 0))->first();
                    }

                    if($record->pad_received_by){
                        return $user ? "{$user->first_name} {$user->last_name}" : '';
                    } else {
                        return '';
                    }
                })
                ->badge()
                ->color('success')
                ->searchable()
                ->toggleable(),
                Tables\Columns\TextColumn::make('date_returned')
                    ->dateTime('F d, Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('verified_by')
                 ->getStateUsing(function (OnlineJobOrder $record) {
                    $user = User::where('jo_id', $record->verified_by)->first();

                    if (! $user) {
                        $user = User::where('employee_number', substr_replace($record->verified_by, '-', 2, 0))->first();
                    }

                    if($record->verified_by){
                        return $user ? "{$user->first_name} {$user->last_name}" : '';
                    } else {
                        return '';
                    }
                })
                ->badge()
                ->color('success')
                ->searchable()
                ->toggleable(),
                Tables\Columns\TextColumn::make('date_verified')
                    ->dateTime('F d, Y')
                    ->toggleable(),

            ])
            ->filters([
                DateRangeFilter::make('date_requested')
                ->withIndicator(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(function () {
                        return OnlineJobOrder::query()
                            ->distinct()
                            ->pluck('status', 'status')
                            ->toArray();
                    })
                    ->placeholder('All Statuses'),
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
            ])
            ->actions([

                Tables\Actions\Action::make('tag')
                ->label('')
                ->icon('fas-map-marker-alt')
                ->tooltip('Set Location')
                ->color('info')
                ->size('xl')
                ->modalWidth(MaxWidth::FiveExtraLarge)
                ->closeModalByClickingAway(false)
                ->visible(fn ($record) =>
                    is_null($record->lat) && is_null($record->lng)
                )
                ->form([

                    Map::make('location')
                    ->defaultLocation(fn () => [17.6223543, 121.7214678])
                    ->defaultZoom(17)
                    ->live()
                    ->reactive()
                    ->extraAttributes([
                        'x-init' => <<<'JS'
                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(function(position) {
                                    $wire.set('location', {
                                        lat: position.coords.latitude,
                                        lng: position.coords.longitude
                                    });
                                });
                            }
                        JS,
                    ])

                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('lat', $state['lat']);
                        $set('lng', $state['lng']);
                    }),
                    Forms\Components\Hidden::make('lat'),
                    Forms\Components\Hidden::make('lng'),
                    ])
                ->action(function (array $data, $record) {
                    $record->update([
                        'lat' => $data['lat'],
                        'lng' => $data['lng'],
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Successfully set location of Job Order')
                        ->success()
                        ->send();
                })
                ->modalHeading('Set Location of Job Order')
                ->requiresConfirmation(),

                Action::make('jo_details')
                ->label('')
                ->tooltip('View Job Order')
                // ->color('info')
                ->color(fn ($record) => in_array($record->status, ['Accomplished', 'For Verification', 'Verified']) ? 'success' : 'info')
                ->icon('fas-eye')
                ->size('xl')
                ->slideOver()
                ->infolist([
                    Section::make('Job Order Details')
                    ->schema([
                        TextEntry::make('jo_number')
                        ->columnSpanFull(),
                        TextEntry::make('date_requested')
                        ->dateTime('F d, Y'),
                        TextEntry::make('jocode.description')
                        ->label('JO Type'),
                        TextEntry::make('status'),
                        TextEntry::make('requested_by'),
                        TextEntry::make('contact_number'),
                        TextEntry::make('account_number'),
                        TextEntry::make('registered_name'),
                        TextEntry::make('address')
                        ->formatStateUsing(function (OnlineJobOrder $record) {
                            return $record->address.', '.Barangay::where('id', $record->barangay)->value('name').', '.City::where('id', $record->town)->value('name');
                            }),
                        TextEntry::make('remarks'),
                        TextEntry::make('actions_taken'),
                        TextEntry::make('field_findings'),
                        TextEntry::make('acknowledge_by'),
                        TextEntry::make('recommendations'),

                        Livewire::make(JobOrderHistory::class)
                        ->columnSpanFull(),
                    ])->columns(3)
                ])
                ->record(function (array $arguments) {
                    return array_key_exists('model_id', $arguments) ? OnlineJobOrder::find($arguments['model_id']) : null;
                })
                ->modalSubmitAction(false),
            Tables\Actions\EditAction::make('edit')
                ->label('')
                ->color('success')
                ->size('xl')
                ->visible(fn ($record) =>
                        $record->status === 'For Forward'
                    )
                ->form([
                    Forms\Components\Grid::make(2)
                            ->schema([
                    Forms\Components\TextInput::make('jo_number')
                    ->label('JO Number')
                    ->required()
                    ->readOnly()
                    ->reactive()
                    ->placeholder(
                        fn () =>
                        Carbon::now()->format('Ym').str_pad(OnlineJobOrder::latest()->selectRaw("CAST(RIGHT(jo_number, 7) AS UNSIGNED) as number")
                        ->orderByDesc(DB::raw("CAST(RIGHT(jo_number, 7) AS UNSIGNED)"))
                        ->value('number') + 1, 7, '0', STR_PAD_LEFT)
                        )
                    ,
                    Forms\Components\DateTimePicker::make('date_requested')
                        ->required(true)
                        ->default(Carbon::now()),
                    Forms\Components\TextInput::make('account_number')
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $data = DB::connection('kitdb')->table('accounts')
                                ->where('accmasterlist', $state)
                                ->first();

                            if ($data) {
                                $set('registered_name', $data->mastername ?? 'No Record');
                                $set('meter_number', $data->meter_number ?? 'No Record');
                            } else {
                                $set('registered_name', 'No Record');
                                $set('meter_number', 'No Record');
                            }

                            if ($data && !is_null($data->latitude)) {
                                $set('lat', $data->latitude ?? null);
                                $set('lng', $data->longtitude ?? null);
                            } else {
                                $set('lat', null);
                                $set('lng', null);
                            }

                        }),
                    Forms\Components\TextInput::make('lat')
                        ->readOnly()
                        ->hidden(),
                    Forms\Components\TextInput::make('lng')
                        ->readOnly()
                        ->hidden(),
                    Forms\Components\TextInput::make('registered_name')
                        ->readOnly(),
                    Forms\Components\TextInput::make('meter_number')
                        ->readOnly(),
                    Forms\Components\Select::make('job_order_code')
                        ->required(true)
                        ->options(function () {
                            return JobOrderCode::pluck('description', 'code')->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->reactive(),

                    Forms\Components\Select::make('town')
                        ->required(true)->options(function () {
                            return DB::connection('kitdb')->table('cities')->whereIn('id', ['21529', '21527', '21520'])->pluck('name', 'id')->toArray();
                        })
                        ->searchable()
                        ->reactive()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            // Determine prefix based on selected town
                            $prefix = match ($state) {
                                '21527' => 'SO',
                                '21520' => 'PO',
                                '21529' => 'TO',
                                default => 'MOJO',
                            };

                            // $suffix = str_pad(
                            //         (OnlineJobOrder::latest()->selectRaw("CAST(RIGHT(jo_number, 7) AS UNSIGNED) as number")
                            //             ->orderByDesc(DB::raw("CAST(RIGHT(jo_number, 7) AS UNSIGNED)"))
                            //             ->value('number') ?? 0) + 1,
                            //         7,
                            //         '0',
                            //         STR_PAD_LEFT
                            //     );
                            // $joNumber = $prefix . Carbon::now()->format('Ym') . $suffix;

                            $latestNumber = \App\Models\OnlineJobOrder::orderByDesc('created_at')
                                ->selectRaw("CAST(RIGHT(jo_number, 7) AS UNSIGNED) as number")
                                ->value('number') ?? 0;

                            $suffix = str_pad($latestNumber + 1, 7, '0', STR_PAD_LEFT);

                            $joNumber = $prefix . Carbon::now()->format('Ym') . $suffix;

                            $set('jo_number', $joNumber);
                        }),
                    Forms\Components\Select::make('barangay')
                        ->required(true)
                        ->options(fn (Get $get): Collection => DB::table('barangays')
                        ->where('city_id', $get('town'))
                        ->pluck('name', 'id'))
                        ->searchable(),
                    Forms\Components\TextInput::make('address')
                        ->label('House No./Blk/Unit/Zone/Purok/Street')
                        ->required(true),
                    Forms\Components\TextInput::make('requested_by')
                        ->required(true),
                    Forms\Components\TextInput::make('contact_number')
                        ->required(true)
                        ->prefix('+63')
                        ->maxLength(10),
                    Forms\Components\TextInput::make('email')
                        ->email(true),

                    Forms\Components\TextInput::make('processed_by')
                        ->afterStateHydrated(function ($component, OnlineJobOrder $record) {
                            $user = User::where('jo_id', $record->processed_by)->first();

                            if (! $user) {
                                $user = User::where('employee_number', $record->processed_by)->first();
                            }

                            $displayName = $user ? "{$user->first_name} {$user->last_name}" : 'Unknown User';
                            $component->state($displayName);
                        })
                        ->dehydrateStateUsing(function ($record) {
                            return auth()->user()->jo_id ?? str_replace('-', '', auth()->user()->employee_number);
                        })
                        ->readOnly(),
                        Forms\Components\TextInput::make('is_synced')
                            ->default(0)
                            ->hidden(),

                    Forms\Components\Select::make('status')
                        ->options([
                            // 'Pending' => 'Pending',
                            // 'For Verification' => 'For Verification',
                            'For Forward' => 'For Forward',
                            // 'Forwarded' => 'Forwarded',
                            // 'For Dispatch' => 'For Dispatch',
                            // 'Dispatched' => 'Dispatched',
                            // 'Accomplished' => 'Accomplished',
                            // 'Cancel' => 'Cancel',
                        ])
                        ->required(true),
                    Forms\Components\Textarea::make('remarks')
                        ->required(true),
                    ])
                ]),
                Tables\Actions\Action::make('Forward')
                    ->label('')
                    ->icon('fas-file-export')
                    ->tooltip('Forward Job order')
                    ->color('warning')
                    ->size('xl')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['Mojo User', 'Executive', 'Mojo Admin', 'Super Admin']) &&

                        $record->status === 'For Forward' && is_null($record->date_forwarded)
                    )
                    ->action(function ($record) {
                        $record->update([
                            'date_forwarded' => now(),
                            'status' => 'Forwarded',
                            'forwarded_by' =>auth()->user()->jo_id ?? str_replace('-', '', auth()->user()->employee_number),
                            'is_synced' => 0,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Job Order Forwarded Successfully')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Forward Job Order?')
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('Receive')
                    ->label('')
                    ->icon('fas-file-import')
                    ->tooltip('Receive Job order')
                    ->modalHeading('Receive Job Order?')
                    ->requiresConfirmation()
                    ->color('warning')
                    ->size('xl')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['Mojo User', 'Executive', 'Mojo Admin', 'Super Admin']) &&
                        $record->status === 'Forwarded' && !is_null($record->date_forwarded) && is_null($record->date_received)
                    )
                    ->action(function ($record) {
                        $record->update([
                            'date_received' => now(),
                            'status' => 'For Dispatch',
                            'received_by' =>auth()->user()->jo_id ?? str_replace('-', '', auth()->user()->employee_number),
                            'is_synced' => 0,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Job Order Received')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make('Dispatch')
                    ->label('')
                    ->icon('fas-user-clock')
                    ->size('xl')
                    ->tooltip('Dispatch Job Order')
                    ->modalHeading('Dispatch Job Order')
                    ->modalDescription('Fill out the form to dispatch a job order.')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['Mojo User', 'Executive', 'Mojo Admin', 'Super Admin']) &&
                        $record->status === 'For Dispatch' && !is_null($record->date_received) && is_null($record->date_dispatched)
                    )
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([

                                Forms\Components\TextInput::make('jo_number')
                                    ->label('Job Order Code')

                                    // ->preload()
                                    // ->autocomplete()
                                    ->required()
                                    ->readOnly(),
                                Forms\Components\DateTimePicker::make('date_requested')
                                    ->required()
                                    ->native(false)
                                    ->format('F d, Y H:i:s')
                                    ->disabled(),
                                Forms\Components\Select::make('job_order_code')
                                    ->required(true)
                                    ->options(function () {
                                        return JobOrderCode::pluck('description', 'code')->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->disabled(),
                                Forms\Components\TextInput::make('requested_by')
                                    ->required(true)
                                    ->disabled(),
                                Forms\Components\TextInput::make('contact_number')
                                    ->required(true)
                                    ->prefix('+63')
                                    // ->maxLength(10)
                                    ->disabled(),
                                Forms\Components\TextInput::make('account_number')
                                    ->label('Account Number')
                                    ->required()
                                    ->disabled(),
                                Forms\Components\TextInput::make('registered_name')
                                     ->disabled(),
                                Forms\Components\Select::make('town')
                                    ->required(true)->options(function () {
                                        return DB::connection('kitdb')->table('cities')->whereIn('id', ['21529', '21527', '21520'])->pluck('name', 'id')->toArray();
                                    })
                                    ->searchable()
                                    ->reactive()
                                    ->disabled()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // Determine prefix based on selected town
                                        $prefix = match ($state) {
                                            '21527' => 'SO',
                                            '21520' => 'PO',
                                            '21529' => 'TO',
                                            default => 'MOJO',
                                        };

                                        // $count = \App\Models\OnlineJobOrder::withTrashed()->count() + 1;
                                        $suffix = str_pad(
                                                (OnlineJobOrder::latest()->selectRaw("CAST(RIGHT(jo_number, 7) AS UNSIGNED) as number")
                                                    ->orderByDesc(DB::raw("CAST(RIGHT(jo_number, 7) AS UNSIGNED)"))
                                                    ->value('number') ?? 0) + 1,
                                                7,
                                                '0',
                                                STR_PAD_LEFT
                                            );
                                        $joNumber = $prefix . Carbon::now()->format('Ym') . $suffix;

                                        $set('jo_number', $joNumber);
                                    }),
                                Forms\Components\Select::make('barangay')
                                    ->required(true)
                                    ->options(fn (Get $get): Collection => DB::table('barangays')
                                    ->where('city_id', $get('town'))
                                    ->pluck('name', 'id'))
                                    ->searchable()
                                    ->disabled(),
                                Forms\Components\TextInput::make('address')
                                    ->label('House No./Blk/Unit/Zone/Purok/Street')
                                    ->required(true)
                                    ->disabled(),
                                    Forms\Components\TextInput::make('is_synced')
                                    ->default(0)
                                    ->hidden(),
                                Forms\Components\Select::make('dispatched')
                                    ->label('Dispatch To')
                                    ->multiple()
                                    ->options(function (callable $get) {
                                        $joNumber = $get('jo_number');
                                        $onlineJobOrder = OnlineJobOrder::with('jocode.division')->where('jo_number', $joNumber)->first();

                                        if (! $onlineJobOrder || ! $onlineJobOrder->jocode || ! $onlineJobOrder->jocode->division) {
                                            return [];
                                        }

                                        $divisionCode = $onlineJobOrder->jocode->division->code;

                                        return User::where('division_id', $divisionCode)
                                            ->get()
                                            ->pluck('full_name', 'employee_number')
                                            ->toArray();
                                    })
                                    ->afterStateHydrated(function (callable $set, callable $get) {
                                        $joNumber = $get('jo_number');
                                        $default = \App\Models\JoDispatch::where('jo_number', $joNumber)->pluck('jo_user')->toArray();
                                        $set('dispatched', $default);
                                        $set('is_synced', 0);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),


                            ]),
                    ])
                    ->modalButton('Confirm Dispatch')
                    ->color('info')
                    ->action(function (array $data) {
                        // Save each dispatched user
                        foreach ($data['dispatched'] as $joUser) {
                            \App\Models\JoDispatch::create([
                                'jo_number' => $data['jo_number'],
                                'jo_user' => $joUser,
                            ]);
                        }

                        // Update the date_dispatched in OnlineJobOrder
                        \App\Models\OnlineJobOrder::where('jo_number', $data['jo_number'])
                            ->update([
                                'date_dispatched' => now(),
                                'dispatched_by' => auth()->user()->jo_id ?? str_replace('-', '', auth()->user()->employee_number),
                                'status' => 'Dispatched',
                                'is_synced' => 0,
                        ]);

                        // Optional: Show success notification
                        \Filament\Notifications\Notification::make()
                            ->title('Dispatched Successfully')
                            ->success()
                            ->send();
                    })
                    ->color('warning'),

                    Tables\Actions\Action::make('cancelDispatch')
                    ->label('')
                    ->icon('fas-person-circle-xmark')
                    ->tooltip('Cancel Dispatch')
                    ->modalHeading('Cancel Job Order Dispatch?')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->size('xl')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['Mojo User', 'Executive', 'Mojo Admin', 'Super Admin']) &&
                        $record->status === 'Dispatched' && !is_null($record->date_dispatched) && is_null($record->date_accomplished)
                    )
                    ->action(function ($record) {
                        $record->update([
                            'date_dispatched' => null,
                            'status' => 'For Dispatch',
                            'dispatched_by' => '',
                            'is_synced' => 0,
                        ]);

                        JoDispatch::where('jo_number', $record->jo_number)->delete();

                        \Filament\Notifications\Notification::make()
                            ->title('Cancelled Job Order Dispatch')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                    Tables\Actions\EditAction::make('accomplish')
                    ->label('')
                    ->icon('fas-check-circle')
                    ->size('xl')
                    ->tooltip('Accomplish Job Order')
                    ->modalHeading('Accomplish Job Order')
                    ->modalDescription('Fill out the form to accomplish a job order.')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['Mojo User', 'Executive', 'Mojo Admin', 'Super Admin']) &&
                        $record->status === 'Dispatched' && !is_null($record->date_dispatched) && is_null($record->date_accomplished)
                    )
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([

                                Forms\Components\TextInput::make('jo_number')
                                    ->label('Job Order Code')

                                    // ->preload()
                                    // ->autocomplete()
                                    ->required()
                                    ->readOnly(),
                                Forms\Components\DateTimePicker::make('date_requested')
                                    ->required()
                                    ->native(false)
                                    ->format('F d, Y H:i:s')
                                    ->disabled(),
                                Forms\Components\Select::make('job_order_code')
                                    ->required(true)
                                    ->options(function () {
                                        return JobOrderCode::pluck('description', 'code')->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->disabled(),
                                Forms\Components\TextInput::make('requested_by')
                                    ->required(true)
                                    ->disabled(),
                                Forms\Components\TextInput::make('contact_number')
                                    ->required(true)
                                    ->prefix('+63')
                                    ->disabled(),
                                Forms\Components\TextInput::make('address')
                                    ->required(true)
                                    ->formatStateUsing(function (OnlineJobOrder $record) {
                                        return $record->address.', '.Barangay::where('id', $record->barangay)->value('name').', '.City::where('id', $record->town)->value('name');
                                    })
                                    ->disabled(),

                                TextInput::make('dispatched_names')
                                ->label('Dispatched Users')
                                ->disabled()
                                ->formatStateUsing(function (OnlineJobOrder $record) {
                                    $joNumber = $record->jo_number;
                                    $test = User::whereIn('employee_number', \App\Models\JoDispatch::where('jo_number', $joNumber)->pluck('jo_user'))->get()
                                            ->pluck('full_name')
                                            ->toArray();

                                    return implode(', ', $test);
                                })
                                ->columnSpanFull(),
                                Forms\Components\TextInput::make('actions_taken'),
                                Forms\Components\TextInput::make('field_findings'),
                                Forms\Components\Select::make('recommendations')
                                ->options([
                                    'No Further Action' => 'No Further Action',
                                    'For Further Action' => 'For Further Action',
                                    'Wrong Complaint/Request' => 'Wrong Complaint/Request',
                                ])
                                ->required(),
                                TextInput::make('is_synced')
                                ->default(0)
                                ->hidden(),
                                Forms\Components\TextInput::make('acknowledge_by'),
                                Forms\Components\Select::make('accomplished')
                                    ->label('Accomplished By')
                                    ->multiple()
                                    ->options(function (callable $get) {
                                        $joNumber = $get('jo_number');
                                        $onlineJobOrder = OnlineJobOrder::with('jocode.division')->where('jo_number', $joNumber)->first();

                                        if (! $onlineJobOrder || ! $onlineJobOrder->jocode || ! $onlineJobOrder->jocode->division) {
                                            return [];
                                        }

                                        $divisionCode = $onlineJobOrder->jocode->division->code;

                                        return User::where('division_id', $divisionCode)
                                            ->get()
                                            ->pluck('full_name', 'employee_number')
                                            ->toArray();
                                    })
                                    ->afterStateHydrated(function (callable $set, callable $get) {
                                        $joNumber = $get('jo_number');
                                        $default = \App\Models\JoAccomplishment::where('jo_number', $joNumber)->pluck('jo_user')->toArray();
                                        $set('dispatched', $default);
                                        $set('is_synced', 0);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->modalButton('Accomplish Job Order')
                    ->color('info')
                    ->action(function (array $data) {
                        // Save each dispatched user
                        foreach ($data['accomplished'] as $joUser) {
                            \App\Models\JoAccomplishment::create([
                                'jo_number' => $data['jo_number'],
                                'jo_user' => $joUser,
                            ]);
                        }

                        // Update the date_dispatched in OnlineJobOrder
                        \App\Models\OnlineJobOrder::where('jo_number', $data['jo_number'])
                            ->update([
                                'date_accomplished' => now(),
                                'accomplishment_processed_by' => auth()->user()->jo_id ?? str_replace('-', '', auth()->user()->employee_number),
                                'status' => 'Accomplished',
                                'recommendations' => $data['recommendations'],
                                'actions_taken' => $data['actions_taken'] ?? null,
                                'field_findings' => $data['field_findings'] ?? null,
                                'acknowledge_by' => $data['acknowledge_by'] ?? null,
                                'is_synced' => 0,
                        ]);

                        // Optional: Show success notification
                        \Filament\Notifications\Notification::make()
                            ->title('Job Order Accomplished Successfully')
                            ->success()
                            ->send();
                    })
                    ->color('warning'),

                Tables\Actions\Action::make('cancelAccomplish')
                    ->label('')
                    ->icon('fas-circle-xmark')
                    ->tooltip('Cancel Accomplishment')
                    ->modalHeading('Cancel Job Order Accomplishment?')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->size('xl')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['Mojo User', 'Executive', 'Mojo Admin', 'Super Admin']) &&
                        $record->status === 'Accomplished' && !is_null($record->date_accomplished) && is_null($record->date_returned)
                    )
                    ->action(function ($record) {
                        $record->update([
                            'date_accomplished' => null,
                            'status' => 'Dispatched',
                            'accomplishment_processed_by' => null,
                            'field_findings' => null,
                            'actions_taken' => null,
                            'acknowledge_by' => null,
                            'recommendations' => null,
                            'is_synced' => 0,
                        ]);

                        JoAccomplishment::where('jo_number', $record->jo_number)->delete();

                        \Filament\Notifications\Notification::make()
                            ->title('Cancelled Job Order Accomplishment')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('Return')
                    ->label('')
                    ->icon('fas-file-export')
                    ->tooltip('Forward Job Order to PACD')
                    ->modalHeading('Forward Job Order to PACD?')
                    ->requiresConfirmation()
                    ->color('info')
                    ->size('xl')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['Mojo User', 'Executive', 'Mojo Admin', 'Super Admin']) &&
                        $record->status === 'Accomplished' && !is_null($record->date_accomplished) && is_null($record->date_returned)
                    )
                    ->action(function ($record) {
                        $record->update([
                            'date_returned' => now(),
                            'status' => 'For Verification',
                            'is_synced' => 0,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Job Order Forwarded to PACD')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('Verify')
                    ->label('')
                    ->icon('fas-list-check')
                    ->tooltip('Verify Job Order')
                    ->modalHeading('Verify Job Order?')
                    ->requiresConfirmation()
                    ->color('success')
                    ->size('xl')
                    ->visible(fn ($record) =>
                        auth()->user()->hasAnyRole(['Mojo User', 'Executive', 'Mojo Admin', 'Super Admin']) &&
                        $record->status === 'For Verification' && !is_null($record->date_returned) && is_null($record->date_verified)
                    )
                    ->action(function ($record) {
                        $record->update([
                            'date_verified' => now(),
                            'status' => 'Verified',
                            'is_synced' => 0,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Job Order Verified Successfully')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOnlineJobOrders::route('/'),
            // 'create' => Pages\CreateOnlineJobOrder::route('/create'),
            // 'edit' => Pages\EditOnlineJobOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
