<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int tonnage_id
 * @property int truck_type_id
 * @property float amount_willing_to_pay
 * @property boolean display_amount_willing_to_pay
 * @property string description
 * @property string pickup_address
 * @property string destination_address
 * @property Carbon date_needed
 * @property string financial_status
 * @property string status
 */
class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if (request()->method() == 'POST') {
            return [
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'tonnage_id' => ['required', 'integer', 'exists:tonnages,id'],
                'truck_type_ids' => ['required', 'array'],
                'tuck_type_ids.*' => ['integer', 'exists:truck_types,id'],
                'amount_willing_to_pay' => ['required', 'numeric', 'min:1'],
                'display_amount_willing_to_pay' => ['required', 'boolean'],
                'description' => ['required', 'string'],
                'pickup_address' => ['required', 'string'],
                'destination_address' => ['required', 'string'],
                'date_needed' => ['required', 'date', 'after:'. Carbon::now()],
            ];
        }

        if (request()->method() == 'PATCH') {
            return [
                'tonnage_id' => ['sometimes', 'integer', 'exists:tonnages,id'],
                'truck_type_ids' => ['sometimes', 'array'],
                'tuck_type_ids.*' => ['integer', 'exists:truck_types,id'],
                'amount_willing_to_pay' => ['sometimes', 'numeric', 'min:1'],
                'display_amount_willing_to_pay' => ['sometimes', 'boolean'],
                'description' => ['sometimes', 'string'],
                'pickup_address' => ['sometimes', 'string'],
                'destination_address' => ['sometimes', 'string'],
                'date_needed' => ['sometimes', 'date'],
            ];
        }
        return [];
    }
}
