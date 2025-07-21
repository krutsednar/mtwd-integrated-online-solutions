<?php

namespace App\Filament\MCIS\Resources\StatementResource\Pages;

use Log;
use Filament\Actions;
// use Filament\Tables\Actions\Action;
use Filament\Actions\Action;
use App\Imports\StatementsImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use EightyNine\ExcelImport\ExcelImportAction;
use App\Filament\MCIS\Resources\StatementResource;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListStatements extends ListRecords
{
    protected static string $resource = StatementResource::class;

       protected function getHeaderActions(): array
    {
        return [
            ExcelImportAction::make()
            ->label('Import Statement')
            ->color("primary")
            ->use(StatementsImport::class),
            // Actions\CreateAction::make(),
            // Action::make('importMultiple')
            //     ->label('Import Multiple CSVs')
            //     ->icon('heroicon-o-arrow-down-tray')
            //     ->color('primary')
            //     ->form([
            //         FileUpload::make('files')
            //             ->label('Upload CSV Files')
            //             ->multiple()
            //             ->acceptedFileTypes(['text/csv', 'text/plain'])
            //             ->required(),
            //     ])
            //     ->action(function (array $data): void {
            //         $importedCount = 0;
            //         $failedImports = [];

            //         foreach ($data['files'] as $uploadedFile) {
            //             if ($uploadedFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            //                 $path = $uploadedFile->getRealPath(); // Get temp path
            //                 $originalName = $uploadedFile->getClientOriginalName();

            //                 try {
            //                     if (file_exists($path)) {
            //                         \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\StatementsImport, $path);
            //                         $importedCount++;
            //                     } else {
            //                         $failedImports[] = "{$originalName} (file not found)";
            //                         \Log::error("File not found at path: {$path}");
            //                     }
            //                 } catch (\Throwable $e) {
            //                     $failedImports[] = "{$originalName} ({$e->getMessage()})";
            //                     \Log::error("Error importing {$originalName}: {$e->getMessage()}");
            //                 }
            //             } else {
            //                 $failedImports[] = "Unknown file (invalid upload object)";
            //                 \Log::error("Invalid upload object: " . print_r($uploadedFile, true));
            //             }
            //         }

            //         if ($importedCount > 0) {
            //             \Filament\Notifications\Notification::make()
            //                 ->title('Import Complete')
            //                 ->body("Successfully imported {$importedCount} file(s).")
            //                 ->success()
            //                 ->send();
            //         }

            //         if (!empty($failedImports)) {
            //             \Filament\Notifications\Notification::make()
            //                 ->title('Import Errors')
            //                 ->body("Failed to import: " . implode(', ', $failedImports))
            //                 ->danger()
            //                 ->send();
            //         }
            //     })
        ];
    }
}
