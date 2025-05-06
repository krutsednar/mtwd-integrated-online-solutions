<?php

namespace App\Filament\MOJO\Resources\CategoryResource\Pages;

use App\Filament\MOJO\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
