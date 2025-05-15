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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'jo_number' => 'required',
			'date_requested' => 'required',
			'account_number' => 'required',
			'registered_name' => 'required',
			'meter_number' => 'required',
			'job_order_code' => 'required',
			'address' => 'required',
			'town' => 'required',
			'barangay' => 'required',
			'contact_number' => 'required',
			'email' => 'required',
			'mode_received' => 'required',
			'remarks' => 'required|string',
			'processed_by' => 'required',
			'deleted_at' => 'required'
		];
    }
}
