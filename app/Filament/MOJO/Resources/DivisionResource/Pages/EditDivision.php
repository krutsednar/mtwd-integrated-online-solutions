<?php

namespace App\Filament\MOJO\Resources\DivisionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\MOJO\Resources\DivisionResource;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class EditDivision extends EditRecord
{
    use HasPageShield;

    protected static string $resource = DivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
