<?php

namespace App\Filament\MCIS\Resources\SmsReportResource\Pages;

use App\Filament\MCIS\Resources\SmsReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSmsReport extends EditRecord
{
    protected static string $resource = SmsReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
