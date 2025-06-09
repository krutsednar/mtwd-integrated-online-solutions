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
        // $this->jobOrders = OnlineJobOrder::with('jobOrderCode')
        //     ->select('lat', 'lng', 'jo_number', 'meter_number', 'registered_name', 'status','account_number')
        //     ->whereNotIn('status', ['Accomplished', 'For Verification', 'Verified'])
        //     ->whereNotNull('lat')
        //     ->whereNotNull('lng')
        //     ->get();

            $this->jobOrders = OnlineJobOrder::with('jobOrderCode.division')
            ->whereNotIn('status', ['Accomplished', 'For Verification', 'Verified'])
            ->get()
            ->map(function ($order) {
                return [
                    'lat' => $order->lat,
                    'lng' => $order->lng,
                    'date_requested' => Carbon::parse($order->date_requested)->format('F d, Y'),
                    'jo_number' => $order->jo_number,
                    'meter_number' => $order->meter_number,
                    'registered_name' => $order->registered_name,
                    'status' => $order->status,
                    'account_number' => $order->account_number,
                    'jobOrderCode' => $order->jobOrderCode,
                    'division' => $order->jobOrderCode->division,
                ];
            });
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

}
