<?php

namespace App\Filament\Imports;

use App\Models\OnlineJobOrder;
use Carbon\Carbon;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\Rule;

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
                ->castStateUsing(function ($state) {
                    return Carbon::parse($state)->format('Y-m-d H:i:s');
                }),
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
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('contact_number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('mode_received')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('remarks')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('processed_by')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('forwarded_by')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('received_by')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('dispatched_by')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('verified_by')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('accomplishment_processed_by')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('pad_received_by')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required', Rule::in(['For Forward', 'Forwarded', 'For Dispatch', 'Dispatched', 'Accomplished', 'For Verification', 'Verified', 'Cancel'])]),
            ImportColumn::make('is_online')
                ->boolean()
                ->rules(['boolean']),
        ];
    }

    public function resolveRecord(): ?OnlineJobOrder
    {
        return OnlineJobOrder::firstOrNew(['jo_number' => $this->data['jo_number']]);
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
