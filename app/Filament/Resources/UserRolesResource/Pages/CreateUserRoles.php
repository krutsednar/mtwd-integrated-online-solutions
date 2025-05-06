<?php

namespace App\Filament\Resources\UserRolesResource\Pages;

use App\Filament\Resources\UserRolesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserRole extends CreateRecord
{
    protected static string $resource = UserRolesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->permissions()->sync($this->permissions);
    }
}

