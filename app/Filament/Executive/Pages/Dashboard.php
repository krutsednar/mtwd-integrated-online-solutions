<?php

namespace App\Filament\Executive\Pages;

use Carbon\Carbon;
use Filament\Pages\Page;
use App\Models\OnlineJobOrder;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BasePage;
use App\Filament\Executive\Widgets\GisMap;
use Filament\Support\Enums\MaxWidth;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'fas-home';

    protected static string $view = 'filament.executive.pages.dashboard';

    public $jobOrders;

    public function mount()
    {

            $this->jobOrders = OnlineJobOrder::with('jobOrderCode.division')
            ->whereNotIn('status', ['Accomplished', 'For Verification', 'Verified'])
            ->get()
            ->map(function ($order) {
                $total = 1;
                $previousDescriptions = [];

                if ($order->account_number) {
                    $allOrders = OnlineJobOrder::where('account_number', $order->account_number)
                        ->with('jobOrderCode')
                        ->where('id', '!=', $order->id)
                        ->get();

                    $total = $allOrders->count() + 1;

                    $previousDescriptions = $allOrders
                        ->pluck('jobOrderCode.description')
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();
                }

                return [
                    'lat' => $order->lat,
                    'lng' => $order->lng,
                    'date_requested' => Carbon::parse($order->date_requested)->format('F d, Y'),
                    'jo_number' => $order->jo_number,
                    'meter_number' => $order->meter_number,
                    'registered_name' => $order->registered_name,
                    'address' => $order->address,
                    'status' => $order->status,
                    'account_number' => $order->account_number,
                    'jobOrderCode' => $order->jobOrderCode,
                    'division' => $order->jobOrderCode->division,
                    'total' => $total,
                    'previous_descriptions' => $previousDescriptions,
                ];
            });
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

}
