<?php

namespace App\Filament\Admin\Resources\OnlineJobOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Imports\OnlineJobOrderImporter;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Admin\Resources\OnlineJobOrderResource;

class ListOnlineJobOrders extends ListRecords
{
    // use HasPageShield;
    protected static string $resource = OnlineJobOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(OnlineJobOrderImporter::class)
                ->icon('fas-file-import')
                ->color('warning')
                ->label('Import CSV'),
            Actions\CreateAction::make(),
        ];
    }
}
