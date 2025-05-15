<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OnlineJobOrderResource\Pages;
use App\Filament\Admin\Resources\OnlineJobOrderResource\RelationManagers;
use App\Models\OnlineJobOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OnlineJobOrderResource extends Resource
{
    protected static ?string $model = OnlineJobOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('jo_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('date_requested')
                    ->required(),
                Forms\Components\TextInput::make('account_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('registered_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('meter_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('job_order_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('town')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('barangay')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mode_received')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('remarks')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('processed_by')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jo_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_requested')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registered_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meter_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_order_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('town')
                    ->searchable(),
                Tables\Columns\TextColumn::make('barangay')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mode_received')
                    ->searchable(),
                Tables\Columns\TextColumn::make('processed_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
}
