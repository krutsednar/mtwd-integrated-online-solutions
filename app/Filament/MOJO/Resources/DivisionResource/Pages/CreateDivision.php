<?php

namespace App\Filament\MOJO\Resources\DivisionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\MOJO\Resources\DivisionResource;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class CreateDivision extends CreateRecord
{

    protected static string $resource = DivisionResource::class;
}
