<?php

namespace App\Filament\MOJO\Resources;

use App\Filament\MOJO\Resources\AccountResource\Pages;
use App\Filament\MOJO\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('accmasterlist')
                    ->label('Account Number')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('mastername')
                    ->label('Account Name')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('mobile')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('meter_number')
                    ->maxLength(255)
                    ->default(null),
                // Forms\Components\TextInput::make('latitude')
                //     ->maxLength(255)
                //     ->default(null),
                // Forms\Components\TextInput::make('longtitude')
                //     ->maxLength(255)
                //     ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('accmasterlist')
                    ->searchable()
                    ->label('Account Number'),
                Tables\Columns\TextColumn::make('mastername')
                    ->searchable()
                    ->label('Account Name'),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meter_number')
                    ->searchable(),
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
                // Tables\Columns\TextColumn::make('latitude')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('longtitude')
                //     ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListAccounts::route('/'),
            // 'create' => Pages\CreateAccount::route('/create'),
            // 'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
