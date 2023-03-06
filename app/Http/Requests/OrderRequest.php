<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

/**
 * @property int tonnage_id
 * @property int truck_type_id
 * @property float amount_willing_to_pay
 * @property int number_trucks
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
    public function authorize(): bool
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
        $user = request()->user();
        $fileExists = Rule::exists(File::class, 'id')
            ->where('type', File::TYPE_IMAGE)
            ->where('creator_id', $user->id);
        if (request()->method() == 'POST') {
            return [
                "cargo_owner_id" => [new RequiredIf($user->user_type === User::USER_TYPE_ADMIN), 'integer', 'exists:users,id'],
                'tonnage_id' => ['required', 'integer', 'exists:tonnages,id'],
                'truck_type_ids' => ['required', 'array'],
                'tuck_type_ids.*' => ['integer', 'exists:truck_types,id'],
                'amount_willing_to_pay' => ['required', 'numeric', 'min:1'],
                'display_amount_willing_to_pay' => ['required', 'boolean'],
                'description' => ['required', 'string'],
                'pickup_address' => ['required', 'string'],
                'destination_address' => ['required', 'string'],
                'number_trucks' => ['sometimes', 'integer', 'min:1'],
                'date_needed' => ['required', 'date', 'after:' . Carbon::now()],
                'product_pictures' => ['nullable', 'array', 'min:1', 'max:4'],
                'product_pictures.*' => ['integer', $fileExists],
            ];
        }

        if (request()->method() == 'PATCH') {
            return [
                'tonnage_id' => ['required', 'integer', 'exists:tonnages,id'],
                'truck_type_ids' => ['sometimes', 'array'],
                'tuck_type_ids.*' => ['integer', 'exists:truck_types,id'],
                'amount_willing_to_pay' => ['sometimes', 'numeric', 'min:1'],
                'display_amount_willing_to_pay' => ['sometimes', 'boolean'],
                'description' => ['sometimes', 'string'],
                'pickup_address' => ['sometimes', 'string'],
                'destination_address' => ['sometimes', 'string'],
                'number_trucks' => ['sometimes', 'integer', 'min:1'],
                'date_needed' => ['sometimes', 'date', 'after:' . Carbon::now()],
                'financial_status' => ['sometimes', 'string']
            ];
        }
        return [];
    }
}
