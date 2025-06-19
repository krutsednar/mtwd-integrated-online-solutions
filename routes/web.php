<?php

use Carbon\Carbon;
use App\Models\OnlineJobOrder;
use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome');
Route::redirect('/', url('home/login'));
Route::redirect('/login', url('home/login'));
// Route::get('/manifest.json', [\TomatoPHP\FilamentPWA\Http\Controllers\PWAController::class, 'index'])->name('manifest');
// Route::get('/offline', [\TomatoPHP\FilamentPWA\Http\Controllers\PWAController::class, 'offline'])->name('offline');
// Route::get('/offline', function () {
//     return view('vendor.laravelpwa.offline');
// });

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');



Route::get('/executive/job-order/{id}', function ($id) {
    $order = OnlineJobOrder::with('jobOrderCode.division')
        ->findOrFail($id);

    $total = 1;
    $previousDescriptions = [];

    if ($order->account_number) {
        $allOrders = OnlineJobOrder::where('account_number', $order->account_number)
            ->where('id', '!=', $order->id)
            ->with('jobOrderCode')
            ->get();

        $total = $allOrders->count() + 1;
        $previousDescriptions = $allOrders
            ->pluck('jobOrderCode.description')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    return response()->json([
        'id' => $order->id,
        'lat' => $order->lat,
        'lng' => $order->lng,
        'date_requested' => Carbon::parse($order->date_requested)->format('F d, Y'),
        'jo_number' => $order->jo_number,
        'requested_by' => $order->requested_by,
        'meter_number' => $order->meter_number,
        'registered_name' => $order->registered_name,
        'address' => $order->address,
        'status' => $order->status,
        'account_number' => $order->account_number,
        'jobOrderCode' => $order->jobOrderCode,
        'division' => $order->jobOrderCode->division,
        'total' => $total,
        'previous_descriptions' => $previousDescriptions,
    ]);
})
->middleware(['auth']);
require __DIR__.'/auth.php';
