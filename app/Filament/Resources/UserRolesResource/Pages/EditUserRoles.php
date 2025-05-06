<?php

namespace App\Filament\Resources\UserRolesResource\Pages;

use App\Filament\Resources\UserRolesResource;
use Filament\Resources\Pages\EditRecord;

class EditUserRole extends EditRecord
{
    protected static string $resource = UserRolesResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->permissions()->sync($this->permissions);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = $this->record->permissions->pluck('id')->toArray();
        return $data;
    }
}

