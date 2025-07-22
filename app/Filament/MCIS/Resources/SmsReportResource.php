<?php

namespace App\Filament\MCIS\Resources;

use App\Filament\MCIS\Resources\SmsReportResource\Pages;
use App\Filament\MCIS\Resources\SmsReportResource\RelationManagers;
use App\Models\SmsReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SmsReportResource extends Resource
{
    protected static ?string $model = SmsReport::class;

    protected static ?string $navigationIcon = 'fas-sms';

    protected static ?string $navigationLabel = 'SMS Blast & OTP';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('account_number')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('mobile')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('amount_before_due')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('due_date')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return SmsReport::query()
                    ->orderBy('created_at', 'desc')
                    ->where(function ($query) {
                        $query->whereNotNull('account_number')
                            ->where('account_number', '!=', '');
                    });
            })
            ->columns([
                Tables\Columns\TextColumn::make('account_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount_before_due')
                    ->searchable()
                    ->money('PHP'),
                Tables\Columns\TextColumn::make('due_date')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sent' => 'success',
                        default => 'danger',
                        // 'published' => 'success',
                        // 'rejected' => 'danger',
                    }),
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
            'index' => Pages\ListSmsReports::route('/'),
            // 'create' => Pages\CreateSmsReport::route('/create'),
            // 'edit' => Pages\EditSmsReport::route('/{record}/edit'),
        ];
    }
}
