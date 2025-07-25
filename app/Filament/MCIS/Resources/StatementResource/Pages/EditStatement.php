<?php

namespace App\Filament\MCIS\Resources\StatementResource\Pages;

use App\Filament\MCIS\Resources\StatementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatement extends EditRecord
{
    protected static string $resource = StatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
