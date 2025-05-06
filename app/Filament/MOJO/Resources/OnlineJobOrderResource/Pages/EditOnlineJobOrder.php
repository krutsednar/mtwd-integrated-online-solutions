<?php

namespace App\Filament\MOJO\Resources\OnlineJobOrderResource\Pages;

use App\Filament\MOJO\Resources\OnlineJobOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOnlineJobOrder extends EditRecord
{
    protected static string $resource = OnlineJobOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
