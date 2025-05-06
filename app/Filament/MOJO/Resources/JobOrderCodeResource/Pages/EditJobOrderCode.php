<?php

namespace App\Filament\MOJO\Resources\JobOrderCodeResource\Pages;

use App\Filament\MOJO\Resources\JobOrderCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobOrderCode extends EditRecord
{
    protected static string $resource = JobOrderCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
