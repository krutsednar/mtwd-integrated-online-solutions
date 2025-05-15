<?php

namespace App\Filament\Admin\Resources\OnlineJobOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Admin\Resources\OnlineJobOrderResource;

class CreateOnlineJobOrder extends CreateRecord
{
    // use HasPageShield;
    protected static string $resource = OnlineJobOrderResource::class;
}
