<?php

namespace App\Filament\MOJO\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Username;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Imports\UsernameImporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\MOJO\Resources\UsernameResource\Pages;
use App\Filament\MOJO\Resources\UsernameResource\RelationManagers;

class UsernameResource extends Resource
{
    protected static ?string $model = Username::class;

    protected static ?string $navigationIcon = 'heroicon-m-user-group';

    protected static ?string $navigationGroup = 'JO Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required(true),
                Forms\Components\TextInput::make('name')
                    ->required(true)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    // ->importer(UsernameImporter::class)
                    // ->color('success')
                    // ->label('Import CSV')
                    // ->icon('heroicon-o-document-arrow-down')
            ])
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('name')
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsernames::route('/'),
            'create' => Pages\CreateUsername::route('/create'),
            'edit' => Pages\EditUsername::route('/{record}/edit'),
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
