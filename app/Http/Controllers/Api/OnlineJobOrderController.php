<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OnlineJobOrder;

class OnlineJobOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return OnlineJobOrder::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'jo_number' => 'required|string',
            'date_requested' => 'required|date',
            'account_number' => 'required|string',
            'registered_name' => 'required|string',
            'meter_number' => 'required|string',
            'job_order_code' => 'required|string',
            'address' => 'required|string',
            'town' => 'required|string',
            'barangay' => 'required|string',
            'requested_by' => 'required|string',
            'contact_number' => 'required|string',
            'email' => 'nullable|email',
            'mode_received' => 'nullable|string',
            'remarks' => 'nullable|string',
            'processed_by' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        return OnlineJobOrder::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(OnlineJobOrder $onlineJobOrder)
    {
        return $onlineJobOrder;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OnlineJobOrder $onlineJobOrder)
    {
        $onlineJobOrder->update($request->all());
        return $onlineJobOrder;
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {

    // }
}
