<?php

namespace App\Filament\MOJO\Resources;

use DB;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\City;
use Filament\Tables;
use GuzzleHttp\Client;
use App\Models\Account;
use Filament\Forms\Get;
use App\Models\Barangay;
use App\Models\Username;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\JobOrderCode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\OnlineJobOrder;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Http;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\MOJO\Resources\OnlineJobOrderResource\Pages;
use App\Filament\MOJO\Resources\OnlineJobOrderResource\RelationManagers;

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
                    // ->placeholder(Carbon::now()->format('Ym') . '' . str_pad(OnlineJobOrder::withTrashed()->count() + 1, 7, '0', STR_PAD_LEFT))
                    // ->placeholder(Carbon::now()->format('Ym') . '' . str_pad((int)substr(OnlineJobOrder::latest()->value('jo_number'), -7) + 1, 7, '0', STR_PAD_LEFT))
                    ->placeholder(
                        fn () => Carbon::now()->format('Ym') .
                            str_pad(
                                (OnlineJobOrder::selectRaw("CAST(RIGHT(jo_number, 7) AS UNSIGNED) as number")
                                    ->orderByDesc(DB::raw("CAST(RIGHT(jo_number, 7) AS UNSIGNED)"))
                                    ->value('number') ?? 0) + 1,
                                7,
                                '0',
                                STR_PAD_LEFT
                            )
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
                                (OnlineJobOrder::selectRaw("CAST(RIGHT(jo_number, 7) AS UNSIGNED) as number")
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

                Forms\Components\Select::make('processed_by')
                    ->options(function () {
                        return Username::pluck('name', 'code')->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required(true),
                Forms\Components\Select::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'For Verification' => 'For Verification',
                        'For Forward' => 'For Forward',
                        'Forwarded' => 'Forwarded',
                        'For Dispatch' => 'Dispatched',
                        'Dispatched' => 'Dispatched',
                        'Accomplished' => 'Accomplished',
                        'Cancel' => 'Cancel',
                    ])
                    ->required(true),
                Forms\Components\Textarea::make('remarks')
                    ->required(true),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                OnlineJobOrder::query()->with('account')->orderBy('id', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('jo_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_requested')
                    ->dateTime('F d, Y'),
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
                Tables\Columns\TextColumn::make('account_number')
                    ->description(fn (OnlineJobOrder $record): string => $record->registered_name, position: 'below')
                    ->searchable()
                    ->wrap(),
                // Tables\Columns\TextColumn::make('registered_name')
                // ->searchable(),
                // Tables\Columns\TextColumn::make('meter_number')
                // ->searchable(),
                Tables\Columns\TextColumn::make('jocode.description')
                ->label('Type')
                ->searchable()
                ->wrap(),
                Tables\Columns\TextColumn::make('jocode.category.name')
                ->label('Category')
                ->searchable()
                ->wrap(),
                Tables\Columns\TextColumn::make('jocode.division.name')
                ->label('Division')
                ->searchable()
                ->wrap(),
                Tables\Columns\TextColumn::make('requested_by')
                ->searchable()
                ->wrap(),
                Tables\Columns\TextColumn::make('contact_number')
                ->searchable()
                ->wrap(),
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

                    // Only render the tooltip if the column content exceeds the length limit.
                    return $state;
                }),
                // ->lineClamp(2),
                // Tables\Columns\TextColumn::make('barangay')
                // ->getStateUsing(function (OnlineJobOrder $record) {
                //     return Barangay::where('id', $record->barangay)->value('name') ?? 'N/A';
                // })
                // ->searchable(),
                // Tables\Columns\TextColumn::make('town')
                // ->getStateUsing(function (OnlineJobOrder $record) {
                //     return DB::table('cities')->where('id', $record->town)->value('name') ?? 'N/A';
                // })
                // ->searchable(),

                // Tables\Columns\TextColumn::make('email')
                // ->searchable(),
                Tables\Columns\TextColumn::make('mode_received')
                ->searchable(),
                Tables\Columns\TextColumn::make('remarks')
                ->limit(30)
                ->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();

                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }

                    // Only render the tooltip if the column content exceeds the length limit.
                    return $state;
                }),
                Tables\Columns\TextColumn::make('processed_by')
                ->getStateUsing(function (OnlineJobOrder $record) {
                    return Username::where('code', $record->processed_by)->value('name') ?? '';
                })
                ->badge()
                ->color('success')
                ->searchable(),
                Tables\Columns\TextColumn::make('status')
                ->searchable(),
                Tables\Columns\TextColumn::make('date_accomplished')
                    ->dateTime('F d, Y'),
            ])
            ->filters([
                Filter::make('date_requested')
                    ->form([
                        DatePicker::make('from')
                        ->label('Date Requested - From')
                        ->displayFormat('F d, Y')
                        ->native(false),
                        DatePicker::make('to')
                        ->label('Date Requested - To')
                        ->displayFormat('F d, Y')
                        ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_requested', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_requested', '<=', $date),
                            );
                    }),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(function () {
                        return OnlineJobOrder::query()
                            ->distinct()
                            ->pluck('status', 'status')
                            ->toArray();
                    })
                    ->placeholder('All Statuses'),
                Filter::make('date_accomplished')
                ->label('test')
                    ->form([
                        DatePicker::make('from')
                        ->label('Date Accomplished - From')
                        ->displayFormat('F d, Y')
                        ->native(false),
                        DatePicker::make('to')
                        ->label('Date Accomplished - To')
                        ->displayFormat('F d, Y')
                        ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->where('status', 'Accomplished')->whereDate('date_accomplished', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->where('status', 'Accomplished')->whereDate('date_accomplished', '<=', $date),
                            );
                    }),
                // Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make()
                ->label('')
                ->color('info')
                ->size('xl'),
                Tables\Actions\EditAction::make()
                ->label('')
                ->color('success')
                ->size('xl'),

            ])
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

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {

    //     $prefix = match ($data['town']) {
    //         '21527' => 'SO',
    //         '21520' => 'PO',
    //         '21529' => 'TO',
    //         default => 'MOJO',
    //     };

    //     $suffix = str_pad(
    //         (OnlineJobOrder::selectRaw("CAST(RIGHT(jo_number, 7) AS UNSIGNED) as number")
    //             ->orderByDesc(DB::raw("CAST(RIGHT(jo_number, 7) AS UNSIGNED)"))
    //             ->value('number') ?? 0) + 1,
    //         7,
    //         '0',
    //         STR_PAD_LEFT
    //     );

    //     $data['jo_number'] = 'test'.$prefix . Carbon::now()->format('Ym') . $suffix;

    //     return $data;
    // }
}
