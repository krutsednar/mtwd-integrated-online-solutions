<?php

namespace App\Filament\MCIS\Resources\AccountResource\Pages;

use App\Filament\MCIS\Resources\AccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Create Account Record')
            ->icon('heroicon-m-plus-circle')
            ->color('info')
            ->modalWidth('7xl')
            ->closeModalByClickingAway(false),

        ];
    }
}
