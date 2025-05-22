<?php

namespace App\Filament\MOJO\Resources;

use DB;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use GuzzleHttp\Client;
use App\Models\Account;
use Filament\Forms\Get;
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
use Filament\Tables\Columns\ViewColumn;
use Filament\Forms\Components\DatePicker;
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
                    ->placeholder(Carbon::now()->format('Ym') . '' . str_pad(OnlineJobOrder::withTrashed()->count() + 1, 7, '0', STR_PAD_LEFT)),
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
                Forms\Components\TextInput::make('address')
                    ->label('House No./Blk/Unit/Zone/Purok/Street')
                    ->required(true),
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

                        $count = \App\Models\OnlineJobOrder::withTrashed()->count() + 1;
                        $suffix = str_pad($count, 7, '0', STR_PAD_LEFT);
                        $joNumber = $prefix . Carbon::now()->format('Ym') . $suffix;

                        $set('jo_number', $joNumber);
                    }),
                Forms\Components\Select::make('barangay')
                    ->required(true)
                    ->options(fn (Get $get): Collection => DB::connection('kitdb')->table('barangays')
                    ->where('city_id', $get('town'))
                    ->pluck('name', 'id'))
                    ->searchable(),
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
                OnlineJobOrder::query()->with('account')->orderBy('date_requested', 'desc')
            )
            ->columns([
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


                Tables\Columns\TextColumn::make('registered_name')
                ->searchable(),
                Tables\Columns\TextColumn::make('meter_number')
                ->searchable(),
                Tables\Columns\TextColumn::make('jocode.description')
                ->label('JO Code')
                ->searchable(),
                Tables\Columns\TextColumn::make('address')
                ->searchable(),
                Tables\Columns\TextColumn::make('barangay')
                ->getStateUsing(function (OnlineJobOrder $record) {
                    return DB::connection('kitdb')->table('barangays')->where('id', $record->barangay)->value('name') ?? 'N/A';
                })
                ->searchable(),
                Tables\Columns\TextColumn::make('town')
                ->getStateUsing(function (OnlineJobOrder $record) {
                    return DB::connection('kitdb')->table('cities')->where('id', $record->town)->value('name') ?? 'N/A';
                })
                ->searchable(),
                Tables\Columns\TextColumn::make('requested_by')
                ->searchable(),
                Tables\Columns\TextColumn::make('contact_number')
                ->searchable(),
                Tables\Columns\TextColumn::make('email')
                ->searchable(),
                Tables\Columns\TextColumn::make('mode_received')
                ->searchable(),
                Tables\Columns\TextColumn::make('remarks'),
                Tables\Columns\TextColumn::make('processed_by')
                ->getStateUsing(function (OnlineJobOrder $record) {
                    return Username::where('code', $record->processed_by)->value('name') ?? '';
                })
                ->badge()
                ->color('success')
                ->searchable(),
                Tables\Columns\TextColumn::make('status')
                ->searchable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('date_requested'),
                    ]),
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
            'create' => Pages\CreateOnlineJobOrder::route('/create'),
            'edit' => Pages\EditOnlineJobOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
