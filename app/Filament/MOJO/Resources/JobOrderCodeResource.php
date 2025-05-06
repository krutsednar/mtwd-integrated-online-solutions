<?php

namespace App\Filament\MOJO\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\JobOrderCode;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Imports\JobOrderCodeImporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\MOJO\Resources\JobOrderCodeResource\Pages;
use App\Filament\MOJO\Resources\JobOrderCodeResource\RelationManagers;

class JobOrderCodeResource extends Resource
{
    protected static ?string $model = JobOrderCode::class;

    protected static ?string $navigationIcon = 'heroicon-c-cog-6-tooth';

    protected static ?string $navigationGroup = 'JO Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required(true),
                Forms\Components\TextInput::make('description')
                    ->required(true),
                Forms\Components\TextInput::make('category_code')
                    ->required(true),
                Forms\Components\TextInput::make('division_code')
                    ->required(true)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(JobOrderCodeImporter::class)
                    ->color('success')
                    ->label('Import CSV')
                    ->icon('heroicon-o-document-arrow-down')
            ])
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('category_code'),
                Tables\Columns\TextColumn::make('division_code')
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
            'index' => Pages\ListJobOrderCodes::route('/'),
            'create' => Pages\CreateJobOrderCode::route('/create'),
            'edit' => Pages\EditJobOrderCode::route('/{record}/edit'),
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
