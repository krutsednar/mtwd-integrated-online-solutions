<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use Filament\Actions;
use App\Imports\UsersImport;
// use App\Filament\Imports\UserImporter;
use Filament\Resources\Pages\ListRecords;
use EightyNine\ExcelImport\ExcelImportAction;
use App\Filament\Admin\Resources\UserResource;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }

       protected function getHeaderActions(): array
    {
        return [
            ExcelImportAction::make()
            ->slideOver()
            ->label('Import Users')
            ->color("primary")
            ->use(UsersImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
