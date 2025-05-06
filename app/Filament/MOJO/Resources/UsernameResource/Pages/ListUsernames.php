<?php

namespace App\Filament\MOJO\Resources\UsernameResource\Pages;

use App\Filament\MOJO\Resources\UsernameResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsernames extends ListRecords
{
    protected static string $resource = UsernameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->color('info'),
        ];
    }
}
