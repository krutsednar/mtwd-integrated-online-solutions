<?php

namespace App\Filament\MCIS\Resources\UserPaymentResource\Pages;

use App\Filament\MCIS\Resources\UserPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserPayment extends CreateRecord
{
    protected static string $resource = UserPaymentResource::class;
}
