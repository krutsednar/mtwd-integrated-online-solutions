<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\UserRolesResource\Pages;
use Filament\Forms\Components\Actions\Action;

class UserRolesResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?string $slug = 'user-roles';

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

                    // Forms\Components\Select::make('permissions')
                    // ->label('Permissions')
                    // ->multiple()
                    // ->options(function () {
                    //     return Permission::all()
                    //         ->sortBy('name')
                    //         ->groupBy(function ($permission) {
                    //             return explode('.', $permission->name)[0] ?? 'General';
                    //         })
                    //         ->map(function ($permissions, $group) {
                    //             return $permissions->mapWithKeys(function ($permission) {
                    //                 return [$permission->id => $permission->name];
                    //             })->toArray();
                    //         })
                    //         ->toArray()
                    //         ;
                    // })
                    // ->columns(2)
                    // ->searchable(),

                    // CheckboxList::make('permissions')
                    // ->label('Permissions')
                    // ->relationship('permissions', 'name') // Assuming you're using Spatie's hasMany relationship
                    // // ->options(function () {
                    // //     return Permission::all()
                    // //         ->sortBy('name')
                    // //         ->groupBy(function ($permission) {
                    // //             return ucfirst(str_replace('_', ' ', explode('.', $permission->name)[0] ?? 'General'));
                    // //         })
                    // //         ->map(function ($groupedPermissions) {
                    // //             return $groupedPermissions->mapWithKeys(function ($permission) {
                    // //                 return [$permission->id => ucfirst(str_replace('_', ' ', $permission->name))];
                    // //             })->toArray();
                    // //         })
                    // //         ->toArray();
                    // // })
                    // ->columns(2)
                    // ->selectAllAction(
                    //     fn (Action $action) => $action->label('Select all technologies'),
                    // )
                    // ,

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
