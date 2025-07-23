<?php

namespace App\Filament\MCIS\Resources;

use App\Filament\MCIS\Resources\UserPaymentResource\Pages;
use App\Filament\MCIS\Resources\UserPaymentResource\RelationManagers;
use App\Models\UserPayment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserPaymentResource extends Resource
{
    protected static ?string $model = UserPayment::class;

    protected static ?string $navigationIcon = 'fas-receipt';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('AccountNumber')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('MerchantCode')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('MerchantRefNo')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('Particulars')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('Amount')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('PayorName')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('PayorEmail')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('Status')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('EppRefNo')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('PaymentOption')
                    ->maxLength(191)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return UserPayment::query()
                    ->orderBy('created_at', 'desc')
                    ->where(function ($query) {
                        $query->whereNotNull('AccountNumber')
                            ->orWhere('AccountNumber', '!=', '');
                    });
            })
            ->columns([
                Tables\Columns\TextColumn::make('AccountNumber')
                    ->searchable(),
                Tables\Columns\TextColumn::make('MerchantCode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('MerchantRefNo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Particulars')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('Amount')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('PayorName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PayorEmail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '00' => 'success',
                        default => 'warning',
                        // 'published' => 'success',
                        // 'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('EppRefNo')
                    // ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PaymentOption')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListUserPayments::route('/'),
            // 'create' => Pages\CreateUserPayment::route('/create'),
            // 'edit' => Pages\EditUserPayment::route('/{record}/edit'),
        ];
    }
}
