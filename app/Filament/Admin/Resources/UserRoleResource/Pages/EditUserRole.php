<?php

namespace App\Filament\Admin\Resources\UserRoleResource\Pages;

use App\Filament\Admin\Resources\UserRoleResource;
use Filament\Resources\Pages\EditRecord;

class EditUserRole extends EditRecord
{
    protected static string $resource = UserRoleResource::class;

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

