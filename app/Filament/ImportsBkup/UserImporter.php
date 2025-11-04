<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('employee_number')
                ->rules(['max:255']),
            ImportColumn::make('name')
                ->rules(['max:255']),
            ImportColumn::make('first_name')
                ->rules(['max:255']),
            ImportColumn::make('middle_name')
                ->rules(['max:255']),
            ImportColumn::make('last_name')
                ->rules(['max:255']),
            ImportColumn::make('suffix')
                ->rules(['max:255']),
            ImportColumn::make('birthday')
                ->rules(['date']),
            // ImportColumn::make('division')
            //     ->rules(['max:255']),
            ImportColumn::make('email')
                ->rules(['email', 'max:255']),
            ImportColumn::make('mobile_number')
                ->rules(['max:255']),
            ImportColumn::make('address')
                ->rules(['max:255']),
            // ImportColumn::make('avatar')
            //     ->rules(['max:255']),
            // ImportColumn::make('locale')
            //     ->rules(['max:255']),
            ImportColumn::make('is_approved')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('email_verified_at')
                ->rules(['email', 'datetime']),
            ImportColumn::make('password')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            // ImportColumn::make('jo_id')
            //     ->rules(['max:255']),
            // ImportColumn::make('prod_id')
            //     ->rules(['max:255']),
            // ImportColumn::make('division_id')
            //     ->rules(['max:255']),
        ];
    }

    protected function mutateRecordData(array $data): array
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }

    public function resolveRecord(): ?User
    {
        // return User::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new User();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
