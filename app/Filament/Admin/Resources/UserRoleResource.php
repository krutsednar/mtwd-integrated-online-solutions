<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Admin\Resources\UserRoleResource\Pages;
use Filament\Forms\Components\Actions\Action;

class UserRoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?string $slug = 'user-role';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Select::make('guard_name')
                    ->default('web')
                    ->required()
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ])
                    ->label('Guard'),
                    CheckboxList::make('permissions')
                    ->label('Permissions')
                    ->options(function () {
                        return Permission::all()->sortBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->columns(2)
                    ,

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('guard_name')->badge(),
                Tables\Columns\TextColumn::make('permissions_count')->counts('permissions')->label('Permissions'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserRoles::route('/'),
            'create' => Pages\CreateUserRole::route('/create'),
            'edit' => Pages\EditUserRole::route('/{record}/edit'),
        ];
    }
}
