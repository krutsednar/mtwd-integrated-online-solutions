<?php

namespace App\Filament\MOJO\Resources\JobOrderCodeResource\Pages;

use App\Filament\MOJO\Resources\JobOrderCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobOrderCodes extends ListRecords
{
    protected static string $resource = JobOrderCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->color('info'),
        ];
    }
}
