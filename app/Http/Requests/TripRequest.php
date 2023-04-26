<?php

namespace App\Http\Requests;

use App\Models\Driver;
use App\Models\File;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TripRequest extends FormRequest
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
        $accountManagerUserExists = Rule::exists(User::class, 'id')->where('user_type', User::USER_TYPE_ACCOUNT_MANAGER);
        $adminUserExists = Rule::exists(User::class, 'id')->where('user_type', User::USER_TYPE_ACCOUNT_MANAGER);
        $driverExists = Rule::exists(Driver::class, 'id');
        $truckExists = Rule::exists(Truck::class, 'id');
        if (request()->method() == 'POST') {
            return [];
        }

        if (request()->method() == 'PATCH') {
            return [
                'way_bill_picture_id' => ['sometimes', 'integer', $fileExists],
                'total_payout' => ['sometimes', 'numeric'],
                'incidental_cost' => ['sometimes', 'numeric'],
                'remark' => ['sometimes', 'string'],
                'visibility_status' => ['sometimes', 'string'],
                'advance_payout' => ['sometimes', 'numeric'],
                'balance_payout' => ['sometimes', 'numeric'],
                'loading_date' => ['sometimes', 'date'],
                'delivery_date' => ['sometimes', 'date'],
                'way_bill_status_id' => ['sometimes', 'integer', 'exists:waybill_statuses,id'],
                'trip_status_id' => ['sometimes', 'integer', 'exists:trip_statuses,id'],
                'days_in_transit' => ['sometimes', 'integer'],
                'days_delivered' => ['sometimes', 'integer'],
                'payout_status' => ['sometimes', 'string'],
                'loading_tonnage_value' => ['sometimes', 'numeric'],
                'offloading_tonnage_value' => ['sometimes', 'numeric']
            ];
        }
        return [];
    }
}
