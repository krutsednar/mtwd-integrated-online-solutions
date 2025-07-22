<?php

namespace App\Filament\MCIS\Resources;

use App\Filament\MCIS\Resources\StatementResource\Pages;
use App\Filament\MCIS\Resources\StatementResource\RelationManagers;
use App\Models\Statement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StatementResource extends Resource
{
    protected static ?string $model = Statement::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'fas-file-invoice';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('account_number')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('account_name')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('address')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('classification')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\DatePicker::make('reading_date'),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\TextInput::make('previous_reading_cum')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('present_reading_cum')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('consumption_cum')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('current_bill')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('maintenance_fee')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('franchise_tax')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('arrears')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('other_charges')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('advance_payment')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('senior_citizen_discount')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('amount_before_due_date')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('penalty')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('amount_after_due_date')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('months_in_arrears')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('paid')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('transmitted')
                    ->maxLength(5)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Statement::query()
                    ->orderBy('updated_at', 'desc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('account_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('classification')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reading_date')
                    ->date('F d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date('F d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('previous_reading_cum')
                    ->label('Previous Reading')
                    ->numeric()
                    ->suffix(' m³')
                    ->sortable(),
                Tables\Columns\TextColumn::make('present_reading_cum')
                    ->label('Present Reading')
                    ->numeric()
                    ->suffix(' m³')
                    ->sortable(),
                Tables\Columns\TextColumn::make('consumption_cum')
                    ->label('Consumption')
                    ->numeric()
                    ->suffix(' m³')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_bill')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('maintenance_fee')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('franchise_tax')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrears')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('other_charges')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('advance_payment')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('senior_citizen_discount')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_before_due_date')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('penalty')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_after_due_date')
                    ->money('PHP')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('status')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('months_in_arrears')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('paid')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('transmitted')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListStatements::route('/'),
            // 'create' => Pages\CreateStatement::route('/create'),
            // 'edit' => Pages\EditStatement::route('/{record}/edit'),
        ];
    }
}
