<?php

namespace App\Filament\MOJO\Resources\OnlineJobOrderResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use GuzzleHttp\Client;
use App\Models\Account;
use App\Models\Division;
use App\Models\JobOrderCode;
use App\Models\OnlineJobOrder;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\ListRecords;
use App\Filament\MOJO\Resources\OnlineJobOrderResource;

class ListOnlineJobOrders extends ListRecords
{
    protected static string $resource = OnlineJobOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Create Job Order')
            ->color('info')
            ->mutateFormDataUsing(function (array $data): array {
                $prefix = match ($data['town']) {
            '21527' => 'SO',
            '21520' => 'PO',
            '21529' => 'TO',
            default => 'MOJO',
        };

        $suffix = str_pad(
            (OnlineJobOrder::selectRaw("CAST(RIGHT(jo_number, 7) AS UNSIGNED) as number")
                ->orderByDesc(DB::raw("CAST(RIGHT(jo_number, 7) AS UNSIGNED)"))
                ->value('number') ?? 0) + 1,
            7,
            '0',
            STR_PAD_LEFT
        );

        $data['jo_number'] = $prefix . Carbon::now()->format('Ym') . $suffix;

        return $data;
            })
            ,
        ];
    }
}
