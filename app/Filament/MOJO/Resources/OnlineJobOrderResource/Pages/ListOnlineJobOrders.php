<?php

namespace App\Filament\MOJO\Resources\OnlineJobOrderResource\Pages;

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
            // ->after(function (OnlineJobOrder $record) {
            //     $account = Account::where('accmasterlist', $record->account_number)
            //         ->whereNotNull('latitude')
            //         ->first();

            //     if ($account) {
            //         $record->lat = $account->latitude;
            //         $record->lng = $account->longtitude;
            //         $record->save();
            //     }
            // })
            ,
            // ->after(function (OnlineJobOrder $record) {
            //     $jocode  = JobOrderCode::where('code', $record->job_order_code)->get();
            //     $divcode = Division::where('code', $jocode->value('division_code'));
            //     $contact = $divcode->value('contact_number');
            //     $post = new Client();
            //         $response = $post->request('POST', 'https://messagingsuite.smart.com.ph/cgphttp/servlet/sendmsg', [
            //         'headers' =>[
            //             'Authorization' => ['Basic '.base64_encode('ict@mtwd.gov.ph:M!ST2o24')],
            //             'Content-Type' => 'application/x-www-form-urlencoded'
            //         ],
            //         'form_params' => [
            //             'destination' => $contact,9178743635,
            //         'text' => 'MOJO Request
            //         '.$record->account_number.'
            //         '.$record->registered_name.'
            //         '.$record->meter_number.'
            //         '.JobOrderCode::where('code', $record->job_order_code)->value('description').'
            //         '.$record->address.', '.DB::connection('kitdb')->table('barangays')->where('id', $record->barangay)->value('name').'
            //         '.$record->contact_number

            //         ],
            //     ]);
            // }),
        ];
    }
}
