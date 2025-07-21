<?php

namespace App\Filament\MCIS\Resources\SmsReportResource\Pages;

use App\Filament\MCIS\Resources\SmsReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSmsReports extends ListRecords
{
    protected static string $resource = SmsReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
