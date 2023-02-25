<?php

namespace App\Http\Requests;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

/**
 * @property-read int id
 * @property int transporter_id
 * @property int truck_type_id
 * @property int registration_number
 * @property int tonnage_id
 * @property int driver_id
 * @property string chassis_number
 * @property string maker
 * @property string model
 * @property int picture_id
 * @property int proof_of_ownership_id
 * @property int road_worthiness_id
 * @property int license_id
 * @property int insurance_id
 */
class TruckRequest extends FormRequest
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
    public function rules()
    {
        $user = request()->user();
        $fileExists = Rule::exists(File::class, 'id')
            ->where('type', File::TYPE_IMAGE)
            ->where('creator_id', $user->id);
        if (request()->method() == 'POST') {
            return [
                'chassis_number' => ['required', 'string', 'max:200', 'unique:trucks,chassis_number'],
                'model' => ['required', 'string', 'max:200'],
                'maker' => ['required', 'string', 'max:200'],
                'registration_number' => ['sometimes', 'string', 'max:200'],
                'plate_number' => ['required', 'string', 'unique:trucks,plate_number'],
                'picture_id' => ['sometimes', 'integer', $fileExists],
                'proof_of_ownership_id' => ['sometimes', 'integer', $fileExists],
                'road_worthiness_id' => ['sometimes', 'integer', $fileExists],
                'license_id' => ['sometimes', 'integer', $fileExists],
                'insurance_id' => ['sometimes', 'integer', $fileExists],
                "transporter_id" => [new RequiredIf($user->user_type === User::USER_TYPE_ADMIN), 'integer', 'exists:users,id'],
                "truck_type_id" => ['required', 'integer', 'exists:truck_types,id'],
                "tonnage_id" => ['required', 'integer', 'exists:tonnages,id'],
                "driver_id" => ['required', 'integer', 'exists:drivers,id'],
            ];
        }
        if (request()->method() == 'PATCH') {
            return [
                'chassis_number' => ['sometimes', 'string', 'max:200', Rule::unique('trucks', 'chassis_number')->ignore($this->truck)],
                'model' => ['sometimes', 'string', 'max:200'],
                'maker' => ['sometimes', 'string', 'max:200'],
                'registration_number' => ['sometimes', 'string', 'max:200'],
                'plate_number' => ['required', 'string', Rule::unique('trucks', 'plate_number')->ignore($this->truck)],
                'picture_id' => ['sometimes', 'integer', $fileExists],
                'proof_of_ownership_id' => ['sometimes', 'integer', $fileExists],
                'road_worthiness_id' => ['sometimes', 'integer', $fileExists],
                'license_id' => ['sometimes', 'integer', $fileExists],
                'insurance_id' => ['sometimes', 'integer', $fileExists],
                "transporter_id" => [new RequiredIf($user->user_type === User::USER_TYPE_ADMIN), 'integer', 'exists:users,id'],
                "truck_type_id" => ['sometimes', 'integer', 'exists:truck_types,id'],
                "tonnage_id" => ['sometimes', 'integer', 'exists:tonnages,id'],
            ];
        }
        return [];
    }
}
