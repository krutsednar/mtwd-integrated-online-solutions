<?php

namespace App\Filament\MOJO\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Division;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Imports\DivisionImporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\MOJO\Resources\DivisionResource\Pages;
use App\Filament\MOJO\Resources\DivisionResource\RelationManagers;
Use Filament\Tables\Actions\ImportAction;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static ?string $navigationIcon = 'heroicon-c-cog-6-tooth';

    protected static ?string $navigationGroup = 'JO Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required(true),
                Forms\Components\TextInput::make('name')
                    ->required(true),
                Forms\Components\TextInput::make('contact_number')
                    ->required(true)
                    ->prefix('+63')
                    ->maxLength(10),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    // ->importer(DivisionImporter::class)
                    // ->color('success')
                    // ->label('Import CSV')
                    // ->icon('heroicon-o-document-arrow-down')
            ])
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('contact_number')
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
            'index' => Pages\ListDivisions::route('/'),
            'create' => Pages\CreateDivision::route('/create'),
            'edit' => Pages\EditDivision::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_any_division'); // Replace with your actual permission
    }

    // public static function navigationItems(): array
    // {
    //     return [
    //         NavigationItem::make()
    //             ->label('Division')
    //             ->url(route('filament.MOJO.resources.divisions.index'))
    //             ->icon('heroicon-o-document-text')
    //             ->canSee(fn () => auth()->user()?->can('view_any_division')),
    //     ];
    // }
}
