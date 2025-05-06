<?php

namespace App\Filament\MOJO\Resources\DivisionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\MOJO\Resources\DivisionResource;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ListDivisions extends ListRecords
{
    // use HasPageShield;

    protected static string $resource = DivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->color('info'),
        ];
    }
}
