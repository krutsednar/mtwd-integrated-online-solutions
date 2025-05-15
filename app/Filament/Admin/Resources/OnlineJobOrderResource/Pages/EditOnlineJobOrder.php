<?php

namespace App\Filament\Admin\Resources\OnlineJobOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Admin\Resources\OnlineJobOrderResource;

class EditOnlineJobOrder extends EditRecord
{
    // use HasPageShield;
    protected static string $resource = OnlineJobOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
