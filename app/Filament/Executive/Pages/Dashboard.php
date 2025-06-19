<?php

namespace App\Filament\Executive\Pages;

use Carbon\Carbon;
use App\Models\OnlineJobOrder;
use Filament\Facades\Filament;
use Filament\Support\Enums\MaxWidth;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends \Filament\Pages\Dashboard
{

    protected static ?string $navigationIcon = 'fas-home';

    protected static string $view = 'filament.executive.pages.dashboard';

    public $jobOrders;

    public function mount()
    {

        $this->jobOrders = OnlineJobOrder::select('id', 'lat', 'lng')
        ->whereNotIn('status', ['Accomplished', 'For Verification', 'Verified'])
        ->get();

    }

    // public function getMaxContentWidth(): MaxWidth
    // {
    //     return MaxWidth::Full;
    // }

}
