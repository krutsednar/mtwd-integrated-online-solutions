<?php

namespace App\Filament\Admin\Resources\OnlineJobOrderResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOnlineJobOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create_online::job::order');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jo_number'      => 'required|string|unique:online_job_orders,jo_number',
            'date_requested' => 'required|date',
            'account_number' => 'required|string',
            'registered_name'=> 'required|string|max:255',
            'meter_number'   => 'required|string',
            'job_order_code' => 'required|string|exists:job_order_codes,code',
            'address'        => 'required|string',
            'town'           => 'required',
            'barangay'       => 'required',
            'contact_number' => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'mode_received'  => 'required|string',
            'remarks'        => 'required|string',
            'processed_by'   => 'required|string',
        ];
    }
}
