<?php

namespace App\Filament\Resources\PassportClientResource\Pages;

use App\Filament\Resources\PassportClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPassportClient extends EditRecord
{
    protected static string $resource = PassportClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
