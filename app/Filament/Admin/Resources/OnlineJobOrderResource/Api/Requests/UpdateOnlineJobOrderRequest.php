<?php

namespace App\Filament\Admin\Resources\OnlineJobOrderResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOnlineJobOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update_online::job::order');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jo_number'      => 'sometimes|string|unique:online_job_orders,jo_number,' . $this->route('jo_number') . ',jo_number',
            'date_requested' => 'sometimes|date',
            'account_number' => 'sometimes|string',
            'registered_name'=> 'sometimes|string|max:255',
            'meter_number'   => 'sometimes|string',
            'job_order_code' => 'sometimes|string|exists:job_order_codes,code',
            'address'        => 'sometimes|string',
            'town'           => 'sometimes',
            'barangay'       => 'sometimes',
            'contact_number' => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'mode_received'  => 'sometimes|string',
            'remarks'        => 'sometimes|string',
            'processed_by'   => 'sometimes|string',
        ];
    }
}
