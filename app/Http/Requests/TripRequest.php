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
                'advance_payout' => ['sometimes', 'numeric'],
                'loading_date' => ['sometimes', 'date'],
                'delivery_date' => ['sometimes', 'date'],
            ];
        }
        return [];
    }
}
