<?php

namespace App\Filament\Imports;

use App\Models\OnlineJobOrder;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Carbon\Carbon;

class OnlineJobOrderImporter extends Importer
{
    protected static ?string $model = OnlineJobOrder::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('jo_number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('date_requested')
                ->requiredMapping()
                ->rules(['required', 'date'])
                ->castStateUsing(function ($state){
                    $state = Carbon::parse($state)->format('Y-m-d H:i:s');
                    return $state;
                    })
                    ,
            ImportColumn::make('account_number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('registered_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('meter_number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('job_order_code')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('address')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('town')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('barangay')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('requested_by')
                ->rules(['max:255']),
            ImportColumn::make('contact_number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->rules(['email', 'max:255']),
            ImportColumn::make('mode_received')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('remarks')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('processed_by')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('is_online')
                ->boolean()
                ->rules(['boolean']),
        ];
    }

    public function resolveRecord(): ?OnlineJobOrder
    {
        return OnlineJobOrder::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'jo_number' => $this->data['jo_number'],
        ]);

        return new OnlineJobOrder();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your Job Order import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
