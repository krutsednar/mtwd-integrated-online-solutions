<?php

namespace App\Filament\MCIS\Resources\UserPaymentResource\Pages;

use App\Filament\MCIS\Resources\UserPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserPayment extends EditRecord
{
    protected static string $resource = UserPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
