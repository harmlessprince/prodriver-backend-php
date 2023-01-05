<?php

namespace App\Http\Requests;

use App\Models\File;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read string first_name
 * @property-read string last_name
 * @property-read string middle_name
 * @property-read string phone_number
 * @property-read Carbon date_of_birth
 * @property-read int profile_image_id
 * @property-read int country_id
 * @property-read int state_id
 * @property-read string gender
 * @property-read string marital_status
 *
 * SPOUSE DETAILS
 * @property-read string spouse_name
 * @property-read string spouse_home_address
 * @property-read string spouse_phone_number
 * @property-read string spouse_occupation
 *
 * NEXT OF KIN
 * @property-read string next_of_kin_name
 * @property-read string next_of_kin_email_address
 * @property-read string next_of_kin_phone_number
 * @property-read string next_of_kin_relationship
 */
class UpdateProfileRequest extends FormRequest
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
        /**  @var User $user */
        $user = request()->user();
        $profileImageRule = Rule::exists(File::class, 'id')
            ->where('type', File::TYPE_IMAGE)
            ->where('creator_id', $user->id);
        $stateRule = Rule::exists('states', 'id')->where('country_id', $this->country_id);
        return [
            'first_name' => ['sometimes', 'string', 'max:200'],
            'middle_name' => ['sometimes', 'string', 'max:200'],
            'last_name' => ['sometimes', 'string', 'max:200'],
            'phone_number' => ['sometimes', 'string', 'max:11', Rule::unique('users', 'phone_number')->ignore($user->id)],
            'date_of_birth' => ['sometimes', 'date'],
            'profile_image_id' => ['sometimes', 'integer', $profileImageRule],
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
            'state_id' => ['sometimes', 'integer', $stateRule],
            'gender' => ['sometimes', 'string', Rule::in(User::GENDERS)],
            'marital_status' => ['sometimes', 'string', Rule::in(['single', 'married'])],
            'home_address' => ['sometimes', 'string', 'max:200'],
            'work_address' => ['sometimes', 'string', 'max:200'],
            'spouse_name' => ['sometimes', 'string', 'max:200'],
            'spouse_home_address' => ['sometimes', 'string', 'max:200'],
            'spouse_phone_number' => ['sometimes', 'string', 'max:200'],
            'spouse_occupation' => ['sometimes', 'string', 'max:200'],
            'next_of_kin_name' => ['sometimes', 'string', 'max:200'],
            'next_of_kin_email_address' => ['sometimes', 'string', 'max:200'],
            'next_of_kin_home_address' => ['sometimes', 'string', 'max:200'],
            'next_of_kin_phone_number' => ['sometimes', 'string', 'max:200'],
            'next_of_kin_relationship' => ['sometimes', 'string', 'max:200'],
            'next_of_kin_occupation' => ['sometimes', 'string', 'max:200'],
        ];
    }
}
