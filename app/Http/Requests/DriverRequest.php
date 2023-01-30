<?php

namespace App\Http\Requests;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;


/**
 * @property int user_id
 * @property int company_id
 * @property  string first_name
 * @property string last_name
 * @property string phone_number
 * @property string license_number
 * @property int picture_id
 * @property int license_picture_id
 */
class DriverRequest extends FormRequest
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
                'first_name' => ['required', 'string', 'max:200'],
                'last_name' => ['required', 'string', 'max:200'],
                'phone_number' => ['required', 'string', 'min:11', 'max:200', Rule::unique('drivers', 'phone_number')],
                'license_number' => ['sometimes', 'string', 'max:200',  Rule::unique('drivers', 'license_number')],
                'picture_id' => ['sometimes', 'integer', $fileExists],
                'license_picture_id' => ['sometimes', 'integer', $fileExists],
                "user_id" => [new RequiredIf($user->user_type === User::USER_TYPE_ADMIN), 'integer', 'exists:users,id'],
                // 'company_id' => [new RequiredIf($user->user_type === User::USER_TYPE_ADMIN), 'integer', 'exists:companies,id'],
            ];
        }
        if (request()->method() == 'PATCH') {
            return [
                'first_name' => ['sometimes', 'string', 'max:200'],
                'last_name' => ['sometimes', 'string', 'max:200'],
                'phone_number' => ['sometimes', 'string', 'min:11', 'max:200', Rule::unique('drivers', 'phone_number')->ignore($this->driver)],
                'license_number' => ['sometimes', 'string', 'max:200',  Rule::unique('drivers', 'license_number')->ignore($this->driver)],
                'picture_id' => ['sometimes', 'integer', $fileExists],
                'license_picture_id' => ['sometimes', 'integer', $fileExists],
            ];
        }
        return [];
    }
}
